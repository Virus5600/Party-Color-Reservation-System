<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Enum\ApprovalStatus;

use Spatie\Activitylog\Models\Activity;

use App\AdditionalOrder;
use App\Announcement;
use App\Booking;
use App\Inventory;
use App\Menu;
use App\MenuVariation;
use App\Permission;
use App\Type;
use App\User;

use DB;
use Exception;
use Hash;
use File;
use Log;
use Route;
use Validator;

class ApiController extends Controller
{
	protected function generalSearch(Request $req) {
		$content = '<h2>' . $req->search . '</h2>';

		return response()->json([
			'content' => $content
		]);
	}

	protected function adminSearch(Request $req, $id) {
		$toSearch = '%'.$req->search.'%';

		$user = User::find($id);

		if ($user == null)
			throw new Exception('Unauthorize Access');

		$fn = ucfirst($req->type);
		$fn = "generate{$fn}Content";

		$content = $this->$fn($toSearch, $user, $req->etcInput ?? array());

		return response()->json([
			'type' => $req->type,
			'content' => $content
		]);
	}

	protected function removeImage(Request $req) {
		$validator = Validator::make($req->all(), [
			'type' => 'required|string',
			'id' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()
				->json([
					'type' => 'validation_error',
					'errors' => $validator->errors()
				]);
		}

		$emptyResponse = true;

		if ($req->type == 'user') {
			try {
				DB::beginTransaction();

				$user = User::find($req->id);

				$oldAvatar = $user->avatar;
				$user->avatar = 'default.png';
				$user->save();

				File::delete(public_path() . '/uploads/users/' . $oldAvatar);

				$fallback = asset('uploads/users/default.png');
				$emptyResponse = false;

				DB::commit();
			} catch (Exception $e) {
				DB::rollback();
				Log::error($e);

				return response()
					->json([
						'type' => 'error',
						'error' => $e
					]);
			}
		}

		if (!$emptyResponse) {
			activity('api')
				->by(auth()->user())
				->on($user)
				->event('update')
				->withProperties([
					'first_name' => $user->first_name,
					'middle_name' => $user->middle_name,
					'last_name' => $user->last_name,
					'suffix' => $user->suffix,
					'is_avatar_link' => $user->is_avatar_link,
					'avatar' => $user->avatar,
					'email' => $user->email,
					'type_id' => $user->type
				])
				->log("{$user->getName()} removed avatar image ('{$req->type}').");

			return response()
				->json([
					'type' => 'success',
					'message' => 'Successfully removed image of ' . $req->type,
					'fallback' => $fallback
				]);
		}

		return response()->json([
			'type' => 'empty',
			'message' => 'Unknown category: ' . $req->type
		]);
	}

	protected function fetchBookingEvent(Request $req, $id) {
		$booking = Booking::with("menus")->find($id);

		if ($booking == null) {
			return response()
				->json([
					'success' => false,
					'message' => 'The booking either does not exists or is already deleted'
				]);
		}

		return response()
			->json([
				'success' => true,
				'message' => $booking,
				'props' => [
					'statusColorCode' => $booking->getStatusColorCode($booking->getOverallStatus())
				]
			]);
	}

	protected function fetchBookingFromRange(Request $req, $monthYear = null) {
		if ($monthYear == null)
			$monthYear = now()->format("F") . ' ' . now()->format("Y");

		$monthYear = explode(" ", $monthYear);
		$month = $monthYear[0];
		$year = $monthYear[1];

		$bookings = Booking::with('contactInformation', 'menus', 'menus.menu')
			->where('created_at', '>=', Carbon::parse("$month 01, $year"))
			->where('created_at', '<=', Carbon::parse("$month $year")->endOfMonth())
			->where('status', '=', ApprovalStatus::Approved->value)
			->get();

		return response()
			->json([
				'success' => true,
				'bookings' => $bookings
			]);
	}

	// CONFIRM PASSWORD MIDDLEWARE
	protected function confirmPassword(Request $req) {
		$controller = collect(Route::getRoutes())
			->first(
				function($route) {
					return $route->matches(
						request()->create(
							session()->get("url")["intended"]
						)
					);
				})
			->action["uses"];

		$controller = explode("@", $controller);
		$msg = "";
		
		if (class_exists($controller[0]))
			if (defined("{$controller[0]}::CONFIRM_PASS_MSG"))
				$msg = $controller[0]::CONFIRM_PASS_MSG[$controller[1]];

		activity('api')
			->by(auth()->user())
			->event('auth-confirm')
			->log("Password confirmation requested for action authenticity");

		return view('middleware.confirm-password', [
			"message" => $msg
		]);
	}

	protected function checkPassword(Request $req) {
		if (!Hash::check($req->password, auth()->user()->password)) {
			activity('api')
				->by(auth()->user())
				->event('auth-confirm')
				->log("Password confirmation rejected.");

			return back()
				->with('flash_error', 'Incorrect password');
		}

		$req->session()->passwordConfirmed();

		activity('api')
			->by(auth()->user())
			->event('auth-confirm')
			->log("Password confirmation accepted. Valid for five (5) minutes");

		session()->forget("confirmPassPrev");
		return redirect()
			->intended();
	}

	// PRIVATE FUNCTIONS
	/**
	 * Extracts the items of the `etc` array to a more "extractable" array with keys for later extraction with `extract()`
	 */
	private function extractParams($etc = array()): array {
		$arr = array();

		foreach ($etc as $v) {
			$splice = preg_split("/\s*=>\s*/", $v);

			$arr["{$splice[0]}"] = $splice[1] ?? null;
		}
		
		return $arr;
	}

	// ADMIN SEARCH CONTENT GENERATOR
	private function generateActivityContent($search, $user, $etc = array()) {
		$editAllow = $user->hasPermission('menu_tab_edit');
		$deleteAllow = $user->hasPermission('menu_tab_delete');

		extract($this->extractParams($etc));
		
		$items = Activity::query();

		$items = $items->where('log_name', 'LIKE', $search)
			->orWhere('description', 'LIKE', $search)
			->orWhere('event', 'LIKE', $search)
			->orWhere('subject_type', 'LIKE', $search)
			->orWhere('causer_type', 'LIKE', $search)
			->orWhere('ip_address', 'LIKE', $search)
			->orWhere('reason', 'LIKE', $search)
			->paginate(10);

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover" . ($i->is_marked == 1 ? "bg-warning text-dark" : "") . "\" id=\"tr-{$i->id}\">
						<td class=\"text-center align-middle mx-auto\">";
				
				if ($i->causer != null)
					$table .= "{$i->causer->email}@{$i->ip_address}";
				else
					$table .= $i->ip_address;

				$table .= "
						</td>
						
						<td class=\"text-center align-middle mx-auto\">
							{$i->description}";
				if ($i->is_marked == 1)
					$table .= "<span class=\"badge badge-danger\">Marked as Suspicious</span>";
				
				$table .="
						</td>
						
						<td class=\"text-center align-middle mx-auto\">
							{$i->created_at->format("M d, Y h:i A")}
						</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<div class=\"dropdown\">
								<button class=\"btn btn-" . ($i->is_marked == 1 ? "danger" : "primary") . " dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"dropdown{$i->id}\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Action
								</button>

								<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"dropdown{$i->id}\">
									<a href=\"" . route('admin.activity-log.show', [$i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-eye mr-2\"></i>View</a>";

					if ($i->causer != null)
						$href = route('admin.users.show', [$i->causer->id]);
					else
						$href = "javascript:SwalFlash.info(`Cannot Find User`, `User account may already be deleted or an anonymous user.`, true, false, `center`, false);";

					$table .= 		"<a href=\"{$href}\" class=\"dropdown-item\">
										<i class=\"fas fa-magnifying-glass-location mr-2\"></i>Trace User
									</a>
								</div>
							</div>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"4\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.activity-log.index'))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}

	private function generateAdditionalOrderContent($search, $user, $etc = array()) {
		extract($this->extractParams($etc));
		
		$booking = Booking::withTrashed()->find($bid);

		if ($booking == null) {
			session()->flash('flash_error', "The booking either does not exists or is already deleted.");

			return response()
				->json([
					'missing' => true,
					'redirect' => route('admin.bookings.index')
				]);
		}

		$items = $booking->additionalOrders()
			->where('extension', 'LIKE', $search)
			->orWhere('price', 'LIKE', $search)
			->withTrashed()
			->paginate(10);

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover\">
						<td class=\"text-center align-middle mx-auto font-weight-bold\">{$i->name}</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<i class=\"fas fa-circle " . ($i->trashed() ? 'text-danger' : 'text-success') . " mr-2\"></i>" . ($i->trashed() ? 'Inactive' : 'Active') . "
						</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<div class=\"dropdown\">
								<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"dropdown{$i->id}\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Action
								</button>

								<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"dropdown{$i->id}\">
									<a href=\"" . route('admin.menu.variation.show', [$menu->id, $i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-eye mr-2\"></i>View</a>";

				if ($editAllow)
					$table .= 		"<a href=\"" . route('admin.menu.variation.edit', [$menu->id, $i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-pencil-alt mr-2\"></i>Edit</a>";
				
				if ($deleteAllow) {
					if ($i->trashed())
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.menu.variation.restore', [$menu->id, $i->id]) . "', undefined, 'Are you sure you want to activate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-on mr-2\"></i>Set Active</a>";
					else
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.menu.variation.delete', [$menu->id, $i->id]) . "', undefined, 'Are you sure you want to deactivate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-off mr-2\"></i>Set Inactive</a>";
				}
				
				$table .= "
								</div>
							</div>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"4\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.bookings.additional-orders.index', [$bid]))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}

	private function generateAnnouncementContent($search, $user, $etc = array()) {
		$editAllow = $user->hasPermission('announcements_tab_edit');
		$publishViable = $user->hasSomePermission('announcements_tab_publish', 'announcements_tab_unpublish');
		$publishAllow = $user->hasPermission('announcements_tab_publish');
		$unpublishAllow = $user->hasPermission('announcements_tab_unpublish');
		$deleteAllow = $user->hasPermission('announcements_tab_delete');
		$permaDeleteAllow = $user->hasPermission('announcements_tab_perma_delete');

		extract($this->extractParams($etc));

		// d => Drafts | sd => Soft Deletes
		if ((isset($sd) && $sd == 1) && (isset($d) && $d == 1))
			$items = Announcement::withTrashed();
		else if (isset($sd) && $sd == 1)
			$items = Announcement::onlyTrashed();
		else if (isset($d) && $d == 1)
			$items = Announcement::where('is_draft', '=', '1');
		else
			$items = Announcement::where('is_draft', '=', '0');

		$items = $items->where('title', 'LIKE', $search)
			->orWhere('slug', 'LIKE', $search)
			->orWhere('summary', 'LIKE', $search)
			->orWhere('content', 'LIKE', $search)
			->with(['user:id,first_name,middle_name,last_name,suffix'])
			->paginate(10);

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover\">
						<td class=\"text-center align-middle mx-auto font-weight-bold\">
							<img src=\"{$i->getPoster()}\" alt=\"{$i->title}\" class=\"img img-fluid user-icon mx-auto rounded\" data-fallback-image=\"" . asset('uploads/announcements/default.png') . "\">
						</td>

						<td class=\"text-center align-middle mx-auto\">
							<i class=\"fas fa-circle " . ($i->trashed() ? 'text-danger' : ($i->is_draft ? 'text-info' : 'text-success')) . " mr-2\"></i>{$i->title}
						</td>
						
						<td class=\"text-center align-middle mx-auto\">{$i->created_at->locale('en_US')->translatedFormat('M d, Y')}</td>
						<td class=\"text-center align-middle mx-auto\">{$i->user->getName()}</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<div class=\"dropdown\">
								<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"dropdown{$i->id}\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Action
								</button>

								<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"dropdown{$i->id}\">
									<a href=\"" . route('admin.announcements.show', [$i->id ,'d' => ($d ? 1 : 0), 'sd' => ($sd ? 1 : 0)]) . "\" class=\"dropdown-item\"><i class=\"fas fa-eye mr-2\"></i>View</a>";

				if ($editAllow)
					$table .= 		"<a href=\"" . route('admin.announcements.edit', [$i->id ,'d' => ($d ? 1 : 0), 'sd' => ($sd ? 1 : 0)]) . "\" class=\"dropdown-item\"><i class=\"fas fa-pencil-alt mr-2\"></i>Edit</a>";
				
				if ($publishViable) {
					if ($i->is_draft && $publishAllow)
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.announcements.publish', [$i->id ,'d' => ($d ? 1 : 0), 'sd' => ($sd ? 1 : 0)]) . "', undefined, 'Publish this announcement?');\" class=\"dropdown-item\"><i class=\"fas fa-upload mr-2\"></i>Publish</a>";
					else if (!$i->is_draft && $unpublishAllow)
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.announcements.unpublish', [$i->id ,'d' => ($d ? 1 : 0), 'sd' => ($sd ? 1 : 0)]) . "', undefined, 'Unpublish this announcement');\" class=\"dropdown-item\"><i class=\"fas fa-pencil-ruler mr-2\"></i>Draft</a>";
				}

				if ($deleteAllow) {
					if ($i->trashed())
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.announcements.restore', [$i->id ,'d' => ($d ? 1 : 0), 'sd' => ($sd ? 1 : 0)]) . "', undefined, 'Are you sure you want to restore this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-on mr-2\"></i>Restore</a>";
					else
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.announcements.delete', [$i->id ,'d' => ($d ? 1 : 0), 'sd' => ($sd ? 1 : 0)]) . "', undefined, 'Are you sure you want to trash this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-off mr-2\"></i>Trash</a>";
				}

				if ($permaDeleteAllow)
					$table .= "<a onclick=\"confirmLeave('" . route('admin.announcements.permaDelete', [$i->id ,'d' => ($d ? 1 : 0), 'sd' => ($sd ? 1 : 0)]) . ", undefined, 'Are you sure you want to permanently delete this?')\" class=\"dropdown-item\"><i class=\"fas fa-fire-alt mr-2\"></i>Delete</a>";
				
				$table .= "
								</div>
							</div>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"5\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.announcements.index'))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}

	private function generateInventoryContent($search, $user, $etc = array()) {
		$editAllow = $user->hasPermission('inventory_tab_edit');
		$deleteAllow = $user->hasPermission('inventory_tab_delete');

		extract($this->extractParams($etc));
		
		$items = Inventory::withTrashed()
			->where('item_name', 'LIKE', $search)
			->orWhere('measurement_unit', 'LIKE', $search)
			->paginate(10);

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover\">
						<td class=\"text-center align-middle mx-auto font-weight-bold\">{$i->item_name}</td>
						<td class=\"text-center align-middle mx-auto\">{$i->getInStock()}</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<i class=\"fas fa-circle " . ($i->trashed() ? 'text-danger' : ($i->quantity > $i->critical_level ? 'text-success' : 'text-warning')) . " mr-2\"></i>" . ($i->trashed() ? 'Inactive' : ($i->quantity > $i->critical_level ? 'Active' : 'Critical')) . "
						</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<div class=\"dropdown\">
								<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"dropdown{$i->id}\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Action
								</button>

								<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"dropdown{$i->id}\">
									<a href=\"" . route('admin.inventory.show', [$i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-eye mr-2\"></i>View</a>";

				if ($editAllow)
					$table .= 		"<a href=\"" . route('admin.inventory.edit', [$i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-pencil-alt mr-2\"></i>Edit</a>";
				
				if ($deleteAllow) {
					if ($i->trashed())
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.inventory.restore', [$i->id]) . "', undefined, 'Are you sure you want to activate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-on mr-2\"></i>Set Active</a>";
					else
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.inventory.delete', [$i->id]) . "', undefined, 'Are you sure you want to deactivate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-off mr-2\"></i>Set Inactive</a>";
				}
				
				$table .= "
								</div>
							</div>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"4\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.inventory.index'))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}

	private function generateMenuContent($search, $user, $etc = array()) {
		$editAllow = $user->hasPermission('menu_tab_edit');
		$deleteAllow = $user->hasPermission('menu_tab_delete');

		extract($this->extractParams($etc));
		
		$items = Menu::withTrashed();

		$items = $items->where('name', 'LIKE', $search)
			->without(['menuVariations'])
			->withCount(['menuVariations'])
			->paginate(10);

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover\">
						<td class=\"text-center align-middle mx-auto font-weight-bold\">{$i->name}</td>
						<td class=\"text-center align-middle mx-auto\">{$i->menu_variations_count}</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<i class=\"fas fa-circle " . ($i->trashed() ? 'text-danger' : 'text-success') . " mr-2\"></i>" . ($i->trashed() ? 'Inactive' : 'Active') . "
						</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<div class=\"dropdown\">
								<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"dropdown{$i->id}\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Action
								</button>

								<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"dropdown{$i->id}\">
									<a href=\"" . route('admin.menu.variation.index', [$i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-eye mr-2\"></i>View</a>";

				if ($editAllow)
					$table .= 		"<a href=\"javascript:void(0);\" class=\"dropdown-item\"
										data-scf=\"Menu Name\"
										data-scf-name=\"menu_name\"
										data-scf-target-uri=\"" . route('admin.menu.update', [$i->id]) . "\"
										data-scf-reload=\"true\">
										<i class=\"fas fa-pen-to-square mr-2\"></i>Change Name
									</a>";
				
				if ($deleteAllow) {
					if ($i->trashed())
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.menu.restore', [$i->id]) . "', undefined, 'Are you sure you want to activate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-on mr-2\"></i>Set Active</a>";
					else
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.menu.delete', [$i->id]) . "', undefined, 'Are you sure you want to deactivate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-off mr-2\"></i>Set Inactive</a>";
				}
				
				$table .= "
								</div>
							</div>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"4\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.menu.index'))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}

	private function generateMenuVariationContent($search, $user, $etc = array()) {
		$editAllow = $user->hasPermission('menu_var_tab_edit');
		$deleteAllow = $user->hasPermission('menu_var_tab_delete');

		extract($this->extractParams($etc));
		
		$menu = Menu::withTrashed()->find($mid);

		if ($menu == null) {
			session()->flash('flash_error', "The menu either does not exists or is already deleted.");

			return response()
				->json([
					'missing' => true,
					'redirect' => route('admin.menu.index')
				]);
		}

		$items = $menu->menuVariations()
			->where('name', 'LIKE', $search)
			->withTrashed()
			->paginate(10);

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover\">
						<td class=\"text-center align-middle mx-auto font-weight-bold\">{$i->name}</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<i class=\"fas fa-circle " . ($i->trashed() ? 'text-danger' : 'text-success') . " mr-2\"></i>" . ($i->trashed() ? 'Inactive' : 'Active') . "
						</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<div class=\"dropdown\">
								<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"dropdown{$i->id}\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Action
								</button>

								<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"dropdown{$i->id}\">
									<a href=\"" . route('admin.menu.variation.show', [$menu->id, $i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-eye mr-2\"></i>View</a>";

				if ($editAllow)
					$table .= 		"<a href=\"" . route('admin.menu.variation.edit', [$menu->id, $i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-pencil-alt mr-2\"></i>Edit</a>";
				
				if ($deleteAllow) {
					if ($i->trashed())
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.menu.variation.restore', [$menu->id, $i->id]) . "', undefined, 'Are you sure you want to activate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-on mr-2\"></i>Set Active</a>";
					else
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.menu.variation.delete', [$menu->id, $i->id]) . "', undefined, 'Are you sure you want to deactivate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-off mr-2\"></i>Set Inactive</a>";
				}
				
				$table .= "
								</div>
							</div>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"3\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.menu.variation.index', [$mid]))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}

	private function generatePermissionContent($search, $user, $etc = array()) {
		extract($this->extractParams($etc));
		
		$items = Permission::query();

		$items = $items->where('name', 'LIKE', $search)
			->paginate(10);

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover\">
						<td class=\"text-center align-middle mx-auto font-weight-bold\">{$i->name}</td>
						<td class=\"text-center align-middle mx-auto\">" . $i->allUsers()->count() . "</td>
						
						<td class=\"align-middle\">
							<a href=\"" . route('admin.permissions.show', [$i->slug]) . "\" class=\"btn btn-primary\"><i class=\"fas fa-eye mr-2\"></i>View</a>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"3\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.permissions.index'))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}

	private function generateTypeContent($search, $user, $etc = array()) {
		$editAllow = $user->hasPermission('types_tab_edit');
		$permissionAllow = $user->hasPermission('types_tab_permissions');
		$deleteAllow = $user->hasPermission('types_tab_delete');
		$permaDeleteAllow = $user->hasPermission('users_tab_perma_delete');

		extract($this->extractParams($etc));
		
		$items = Type::withTrashed();

		$items = $items->where('name', 'LIKE', $search)
			->withCount(['users', 'permissions'])
			->paginate(10);

		$totalPerms = Permission::count();

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover\" id=\"tr-{$i->id}\">
						<td class=\"text-center align-middle mx-auto font-weight-bold\">{$i->name}</td>
						<td class=\"text-center align-middle mx-auto font-weight-bold\">{$i->users_count}</td>
						<td class=\"text-center align-middle mx-auto font-weight-bold\">{$i->permissions_count}/{$totalPerms} (" . number_format(($i->permissions_count/$totalPerms) * 100, 2) . "%)</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<i class=\"fas fa-circle " . ($i->trashed() ? 'text-danger' : 'text-success') . " mr-2\"></i>" . ($i->trashed() ? 'Inactive' : 'Active') . "
						</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<div class=\"dropdown\">
								<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"dropdown{$i->id}\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Action
								</button>

								<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"dropdown{$i->id}\">
									<a href=\"" . route('admin.menu.variation.index', [$i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-eye mr-2\"></i>View</a>";

				if ($editAllow)
					$table .= 		"<a href=\"javascript:void(0);\" class=\"dropdown-item\"
										data-scf=\"Menu Name\"
										data-scf-name=\"menu_name\"
										data-scf-target-uri=\"" . route('admin.menu.update', [$i->id]) . "\"
										data-scf-reload=\"true\">
										<i class=\"fas fa-pen-to-square mr-2\"></i>Change Name
									</a>";
				
				if ($deleteAllow) {
					if ($i->trashed())
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.menu.restore', [$i->id]) . "', undefined, 'Are you sure you want to activate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-on mr-2\"></i>Set Active</a>";
					else
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.menu.delete', [$i->id]) . "', undefined, 'Are you sure you want to deactivate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-off mr-2\"></i>Set Inactive</a>";
				}
				
				$table .= "
								</div>
							</div>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"5\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.types.index'))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}

	private function generateUserContent($search, $user, $etc = array()) {
		$deleteViable = $user->hasSomePermission('users_tab_delete', 'users_tab_perma_delete');
		$editAllow = $user->hasPermission('users_tab_edit');
		$permissionAllow = $user->hasPermission('users_tab_permissions');
		$deleteAllow = $user->hasPermission('users_tab_delete');
		$permaDeleteAllow = $user->hasPermission('users_tab_perma_delete');

		extract($this->extractParams($etc));
		
		$items = User::withTrashed();

		$items = $items->leftJoin('types', 'users.type_id', '=', 'types.id')
			->where('first_name', 'LIKE', $search)
			->orWhere('middle_name', 'LIKE', $search)
			->orWhere('last_name', 'LIKE', $search)
			->orWhere('email', 'LIKE', $search)
			->orWhere('types.name', 'LIKE', $search)
			->paginate(10);

		$table = "";

		if (count($items) > 0) {
			foreach ($items as $i) {
				$table .= "
					<tr class=\"enlarge-on-hover\" id=\"tr-{$i->id}\">
						<td class=\"text-center\">
							<img src=\"{$i->getAvatar()}\" alt=\"{{ $i->first_name }}'s Avatar\" class=\"img img-fluid user-icon mx-auto rounded\">
						</td>

						<td class=\"text-center align-middle mx-auto font-weight-bold\">";

				if ($deleteViable) {
					$table .= "
							<span class=\"" . ($i->deleted_at ? 'text-danger' : 'text-success') . "\">
								<i class=\"fas fa-circle small\"></i>
							</span>
							";
				}

				
				$table .= "
						</td>

						<td class=\"text-center align-middle mx-auto\">{$i->getName()}</td>
						<td class=\"text-center align-middle mx-auto\">{$i->type->name}</td>
						<td class=\"text-center align-middle mx-auto\">{$i->email}</td>
						
						<td class=\"text-center align-middle mx-auto\">
							<div class=\"dropdown\">
								<button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"dropdown{$i->id}\" aria-haspopup=\"true\" aria-expanded=\"false\">
									Action
								</button>

								<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"dropdown{$i->id}\">
									<a href=\"" . route('admin.users.show', [$i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-eye mr-2\"></i>View</a>";

				if ($editAllow)
					$table .= 		"<a href=\"" . route('admin.users.edit', [$i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-pencil-alt mr-2\"></i>Edit</a>";

				if ($permissionAllow)
					$table .=		"<a href=\"" . route('admin.users.manage-permissions', [$i->id]) . "\" class=\"dropdown-item\"><i class=\"fas fa-user-lock mr-2\"></i>Manage Permissions</a>";
				
				if ($editAllow || $user->id == $i->id) {
					$table .=		"<a href=\"javascript:void(0);\" class=\"dropdown-item change-password\" id=\"scp-{$i->id}\">
										<i class=\"fas fa-lock mr-2\"></i>Change Password
										<script type=\"text/javascript\">
											$(document).ready(() => {
												let data = `{
													\"preventDefault\": true,
													\"name\": \"{$i->getName()}\",
													\"targetURI\": \"" . route('admin.users.change-password', [$i->id]) . "\",
													\"notify\": true,
													\"for\": \"#tr-{$i->id}\"
												}`;
												$('#scp-{$i->id}').attr(\"data-scp\", data);
												$('#scp-{$i->id}').find('script').remove();
											});
										</script>
									</a>";
				}

				if ($deleteAllow) {
					if ($i->trashed())
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.users.restore', [$i->id]) . "', undefined, 'Are you sure you want to activate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-on mr-2\"></i>Set Active</a>";
					else
						$table .=	"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.users.delete', [$i->id]) . "', undefined, 'Are you sure you want to deactivate this?');\" class=\"dropdown-item\"><i class=\"fas fa-toggle-off mr-2\"></i>Set Inactive</a>";
				}

				if ($permaDeleteAllow)
					$table .=		"<a href=\"javascript:void(0);\" onclick=\"confirmLeave('" . route('admin.users.permaDelete', [$i->id]) . "', undefined, 'Are you sure you want to delete this?')\" class=\"dropdown-item\"><i class=\"fas fa-trash mr-2\"></i>Delete</a>";
				
				$table .= "
								</div>
							</div>
						</td>
					</tr>
				";
			}
		}
		else {
			$table = "
				<tr>
					<td class=\"text-center\" colspan=\"5\">Nothing to show~</td>
				</tr>
			";
		}

		$items->setPath(route('admin.users.index'))
			->appends("search", substr($search, 1, strlen($search)-2));

		return array(
			'items' => $table,
			'paginate' => "{$items->onEachSide(5)->links()}"
		);
	}
}