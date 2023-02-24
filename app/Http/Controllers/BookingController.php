<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Carbon\Carbon;

use App\Enum\ApprovalStatus;
use App\Enum\Status;

use App\Booking;
use App\ContactInformation;
use App\Inventory;
use App\Menu;
use App\MenuVariation;
use App\Settings;

use DB;
use Exception;
use Log;
use Session;
use Validator;

class BookingController extends Controller
{
	protected function index(Request $req) {
		$bookings = Booking::with("menus")->get();

		$targetBooking = Booking::where('control_no', '=', $req->cn)->first();
		$isShow = $targetBooking != null;
		$control_no = $isShow ? $targetBooking->control_no : null;
		$reserved_at = $isShow ? $targetBooking->reserved_at : null;

		return view('admin.bookings.index', [
			'bookings' => $bookings,
			'isShow' => $isShow,
			'control_no' => $control_no,
			'reserved_at' => $reserved_at
		]);
	}

	protected function create(Request $req) {
		$menus = Menu::get();

		return view('admin.bookings.create', [
			'menus' => $menus,
			'booking_type' => $req->t
		]);
	}

	protected function store(Request $req) {
		extract(Booking::validate($req));

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput()
				->with("new_contact_index",  $newContactIndex);
		}

		try {
			DB::beginTransaction();

			$price = 0;

			foreach ($req->menu as $k => $v)
				$price += (MenuVariation::find($v)->price * $req->amount[$k]);
			$price += ($req->extension * Settings::getValue("extension_fee"));

			$booking = Booking::create([
				'control_no' => Booking::createControlNumber(),
				'booking_type' => $req->booking_type,
				'start_at' => $start_at,
				'end_at' => $end_at,
				'reserved_at' => $req->booking_date,
				'extension' => $req->extension,
				'price' => $price,
				'pax' => $req->pax,
				'phone_numbers' => implode("|", $req->phone_numbers),
				'special_request' => $req->special_request
			]);

			foreach ($req->menu as $k => $v)
				$booking->menus()
					->attach([
						$v => [
							'count' => $req->amount[$k]
						]
					]);

			$iterations = max(count($req->contact_name), count($req->contact_email));
			for ($i = 0; $i < $iterations; $i++) {
				ContactInformation::create([
					'contact_name' => $req->contact_name["{$i}"],
					'email' => $req->contact_email["{$i}"],
					'booking_id' => $booking->id
				]);
			}

			// Logger
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('create')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Booking #{$booking->control_no} created.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.bookings.index')
			->with('flash_success', 'Successfully added a new booking');
	}

	protected function show($id) {
		$booking = Booking::with([
				'menus',
				'menus.menu:id,name',
				'contactInformation',
				'additionalOrders' => function($query) {return $query->withTrashed();},
				'additionalOrders.orderable',
				'additionalOrders.orderable.menuVariation',
				'additionalOrders.orderable.menuVariation.menu:id,name'
			])
			->find($id);

		if ($booking == null) {
			return response()
				->json([
					'success' => false,
					'message' => 'The booking either does not exists or is already deleted'
				]);
		}

		$colorCode = $booking->getStatusColorCode($booking->getOverallStatus());
		$status = $booking->getStatusText($booking->getOverallStatus());

		// Check if this booking can be accomodated or not
		$canAccomodate = true;
		$relatedInventory = [];
		$relatedInventoryName = [];
		// Iterate through the booking's initial order
		foreach ($booking->menus as $m) {
			foreach ($m->items as $i) {
				// If the item is marked as unlimited
				if ($i->is_unlimited)
					continue;

				// Proceed to check if stock is viable still if it is not unlimited
				$item = $i->variationItem()->first();
				$key = Str::snake($item->item_name);
				if (array_key_exists($key, $relatedInventory)) {
					$relatedInventory["$key"] += $i->amount * $m->pivot->count;
				}
				else {
					array_push($relatedInventoryName, $item->item_name);
					$relatedInventory["$key"] = $i->amount * $m->pivot->count;
				}
			}
		}

		// Iterate through the additional orders made, if there are any
		if ($booking->additionalOrders != null) {
			foreach ($booking->additionalOrders as $o) {
				foreach ($o->menus as $m) {
					foreach ($m->items as $i) {
						// If the item is marked as unlimited
						if ($i->is_unlimited)
							continue;

						// Proceed to check if stock is viable still if it is not unlimited
						$item = $i->variationItem()->first();
						$key = Str::snake($item->item_name);
						if (array_key_exists($key, $relatedInventory)) {
							$relatedInventory["$key"] += $i->amount * $m->pivot->count;
						}
						else {
							array_push($relatedInventoryName, $item->item_name);
							$relatedInventory["$key"] = $i->amount * $m->pivot->count;
						}
					}
				}
			}
		}

		$inventory = Inventory::whereIn('item_name', $relatedInventoryName)->get();
		$lackingInventory = [];
		foreach ($inventory as $i) {
			if ($i->quantity < $relatedInventory[Str::snake("$i->item_name")]) {
				$canAccomodate = false;
				$lackingInventory["{$i->item_name}"] = ($relatedInventory[Str::snake("$i->item_name")] - $i->quantity) . $i->measurement_unit;
			}
		}

		return response()
			->json([
				'success' => true,
				'message' => 'Booking found',
				'booking' => $booking,
				'colorCode' => $colorCode,
				'status' => $status,
				'canAccomodate' => $canAccomodate,
				'lackingInventory' => $lackingInventory
			]);
	}

	protected function edit($id) {
		$booking = Booking::with(['menus', 'contactInformation'])->find($id);

		if ($booking == null) {
			Log::info("No such booking.", ["id" => $id, "booking" => $booking]);
			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'The bookings either does not exists or is already deleted.');
		}

		$menus = Menu::get();
		$newContactIndex = [];

		for ($i = 0; $i < $booking->contactInformation->count(); $i++)
			array_push($newContactIndex, "{$i}");

		Session::put("new_contact_index", $newContactIndex);

		return view('admin.bookings.edit', [
			'booking' => $booking,
			'menus' => $menus
		]);
	}

	protected function update(Request $req, $id) {
		$booking = Booking::find($id);

		if ($booking == null) {
			Log::info("No such booking.", ["id" => $id, "booking" => $booking]);
			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'The bookings either does not exists or is already deleted.');
		}

		extract(Booking::validate($req));

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput()
				->with("new_contact_index",  $newContactIndex);
		}

		try {
			DB::beginTransaction();

			$price = 0;

			foreach ($req->menu as $k => $v)
				$price += (MenuVariation::find($v)->price * $req->amount[$k]);
			$price += ($req->extension * Settings::getValue("extension_fee"));

			$booking->booking_type = $req->booking_type;
			$booking->start_at = $start_at;
			$booking->end_at = $end_at;
			$booking->reserved_at = $req->booking_date;
			$booking->extension = $req->extension;
			$booking->price = $price;
			$booking->pax = $req->pax;
			$booking->phone_numbers = implode("|", $req->phone_numbers);
			$booking->save();

			$this->pending($booking->id, true);

			$booking->menus()->detach();
			foreach ($req->menu as $k => $v)
				$booking->menus()
					->attach([
						$v => [
							'count' => $req->amount[$k]
						]
					]);

			$booking->contactInformation()->delete();

			$iterations = max(count($req->contact_name), count($req->contact_email));
			for ($i = 0; $i < $iterations; $i++) {
				$ci = ContactInformation::create([
					'contact_name' => $req->contact_name["{$i}"],
					'email' => $req->contact_email["{$i}"],
					'booking_id' => $booking->id
				]);
			}

			// Logger
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('update')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Booking #{$booking->control_no} updated.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.bookings.index')
			->with('flash_success', "Successfully updated booking.")
			->with('has_icon', "true")
			->with('message', "Please re-evaluate the booking if it should still be accepted or not.")
			->with('has_timer', "false");
	}

	protected function delete($id) {
		$booking = Booking::withTrashed()->with(['menus'])->find($id);

		if ($booking == null) {
			Log::info("No such booking.", ["id" => $id, "booking" => $booking]);
			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'The bookings either does not exists or is already deleted');
		}

		try {
			DB::beginTransaction();

			// Return the inventory for realtime update
			if ($booking->items_returned == 0) {
				foreach ($booking->menus as $m) {
					$response = $m->returnInventory($m->pivot->count);

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				foreach ($booking->additionalOrders as $o) {
					foreach ($o->menus as $m) {
						$response = $m->returnInventory($m->pivot->count);

						if (!$response->success)
							throw new Exception($response->message);
					}
				}
			}

			$control_no = $booking->control_no;
			$booking_type = $booking->booking_type;
			$start_at = $booking->start_at;
			$end_at = $booking->end_at;
			$reserved_at = $booking->reserved_at;
			$extension = $booking->extension;
			$price = $booking->price;
			$pax = $booking->pax;
			$phone_numbers = $booking->phone_numbers;
			$special_request = $booking->special_request;

			$booking->forceDelete();

			// Logger
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('delete')
				->withProperties([
					'control_no' => $control_no,
					'booking_type' => $booking_type,
					'start_at' => $start_at,
					'end_at' => $end_at,
					'reserved_at' => $reserved_at,
					'extension' => $extension,
					'price' => $price,
					'pax' => $pax,
					'phone_numbers' => $phone_numbers,
					'special_request' => $special_request
				])
				->log("Booking #{$control_no} deleted.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.bookings.index')
			->with('flash_success', 'Successfully removed booking request');
	}

	protected function archive($id) {
		$booking = Booking::with(['menus'])->find($id);

		if ($booking == null) {
			Log::info("No such booking.", ["id" => $id, "booking" => $booking]);
			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'The bookings either does not exists or is already deleted');
		}

		try {
			DB::beginTransaction();

			// Return the inventory for realtime update
			if (!($booking->getOverallStatus() == Status::Happening || $booking->getOverallStatus() == Status::Done)) {
				if ($booking->items_returned == 0) {
					foreach ($booking->menus as $m) {
						$response = $m->returnInventory($m->pivot->count);

						if (!$response->success) {
							throw new Exception($response->message);
						}
					}

					foreach ($booking->additionalOrders as $o) {
						foreach ($o->menus as $m) {
							$response = $m->returnInventory($m->pivot->count);

							if (!$response->success)
								throw new Exception($response->message);
						}
					}

					$booking->items_returned = 1;
					$booking->save();
				}
			}

			$booking->delete();

			// Logger
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('archive')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Booking #{$booking->control_no} archived.");

			ActivityLog::log(
				"Booking #{$booking->control_no} archived.",
				$booking->id,
				"Booking",
				auth()->user()->id
			);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}
	}

	protected function accept($id) {
		$booking = Booking::with(['menus'])->find($id);

		if ($booking == null) {
			Log::info("No such booking.", ["id" => $id, "booking" => $booking]);
			return response()
				->json([
					'success' => false,
					'title' => 'The bookings either does not exists or is already deleted',
					'message' => ''
				]);
		}

		try {
			DB::beginTransaction();

			if ($booking->items_returned == 1) {
				// Reduce the inventoy for realtime update
				foreach ($booking->menus as $m) {
					$response = $m->reduceInventory($m->pivot->count);

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				foreach ($booking->additionalOrders as $o) {
					foreach ($o->menus as $m) {
						$response = $m->reduceInventory($m->pivot->count);

						if (!$response->success)
							throw new Exception($response->message);
					}
				}

				$booking->items_returned = 0;
			}

			$booking->status = ApprovalStatus::Approved;
			$booking->reason = null;
			$booking->save();

			// Logger
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('accept')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Booking #{$booking->control_no} accepted.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'title' => 'Something went wrong, please try again later',
					'message' => ''
				]);
		}

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully accepted booking request',
				'message' => ''
			]);
	}

	protected function reject(Request $req, $id) {
		$booking = Booking::with(['menus'])->find($id);

		if ($booking == null) {
			return response()
				->json([
					'success' => false,
					'title' => 'Booking not found',
					'message' => 'The booking either does not exists or is already deleted'
				]);
		}

		$validator = Validator::make($req->all(), [
			'reason' => 'required|string|max:255'
		], [
			'reason.required' => 'A reason is required',
			'reason.string' => 'Malformed data',
			'reason.max' => 'Character limit reached (255)',
		]);

		if ($validator->fails()) {
			return response()
				->json([
					'success' => false,
					'title' => 'Validation error',
					'message' => $validator->messages()->first()
				]);
		}

		try {
			DB::beginTransaction();

			if ($booking->items_returned == 0) {
				// Return the inventory for realtime update
				foreach ($booking->menus as $m) {
					$response = $m->returnInventory($m->pivot->count);

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				foreach ($booking->additionalOrders as $o) {
					foreach ($o->menus as $m) {
						$response = $m->returnInventory($m->pivot->count);

						if (!$response->success)
							throw new Exception($response->message);
					}
				}

				$booking->items_returned = 1;
			}

			$booking->reason = $req->reason;
			$booking->status = ApprovalStatus::Rejected;
			$booking->save();

			// Logger
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('reject')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Booking #{$booking->control_no} rejected.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'title' => 'Something went wrong, please try again later',
					'message' => ''
				]);
		}

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully rejected booking request',
				'message' => ''
			]);
	}

	public function pending($id, $automated = false) {
		$booking = Booking::with(['menus'])->find($id);

		if ($booking == null) {
			Log::info("No such booking.", ["id" => $id, "booking" => $booking]);
			return response()
				->json([
					'success' => false,
					'title' => 'The bookings either does not exists or is already deleted',
					'message' => ''
				]);
		}

		try {
			DB::beginTransaction();

			if ($booking->items_returned == 0) {
				// Reduce the inventoy for realtime update
				foreach ($booking->menus as $m) {
					$response = $m->returnInventory($m->pivot->count);

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				foreach ($booking->additionalOrders as $o) {
					foreach ($o->menus as $m) {
						$response = $m->returnInventory($m->pivot->count);

						if (!$response->success)
							throw new Exception($response->message);
					}
				}

				$booking->items_returned = 1;
			}

			$booking->status = ApprovalStatus::Pending;
			$booking->reason = null;
			$booking->save();

			// Logger
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('create')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log(trim("Booking {$booking->control_no} moved to pending. " . ($automated ? "This is an automated action after #{$booking->control_no} is updated." : "")));

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'title' => 'Something went wrong, please try again later',
					'message' => ''
				]);
		}

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully moved booking request to pending',
				'message' => ''
			]);
	}

	public function acceptCancel(Request $req, $id) {
		$booking = Booking::with(['menus'])->find($id);

		if ($booking == null) {
			return response()
				->json([
					'success' => false,
					'title' => 'Booking not found',
					'message' => 'The booking either does not exists or is already deleted'
				]);
		}

		$validator = Validator::make($req->all(), [
			'reason' => 'required|string|max:255'
		], [
			'reason.required' => 'A reason is required',
			'reason.string' => 'Malformed data',
			'reason.max' => 'Character limit reached (255)',
		]);

		if ($validator->fails()) {
			return response()
				->json([
					'success' => false,
					'title' => 'Validation error',
					'message' => $validator->messages()->first()
				]);
		}

		try {
			DB::beginTransaction();

			if ($booking->items_returned == 0) {
				// Return the inventory for realtime update
				foreach ($booking->menus as $m) {
					$response = $m->returnInventory($m->pivot->count);

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				foreach ($booking->additionalOrders as $o) {
					foreach ($o->menus as $m) {
						$response = $m->returnInventory($m->pivot->count);

						if (!$response->success)
							throw new Exception($response->message);
					}
				}

				$booking->items_returned = 1;
			}

			$booking->cancel_requested = 0;
			$booking->status = Status::Cancelled;
			$booking->reason = $req->reason;
			$booking->save();

			// Mailer to customer that his cancellation is approved

			// Logger
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('approve-cancel')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Approval for the cancellation of booking #{$booking->control_no}.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'title' => 'Something went wrong, please try again later',
					'message' => ''
				]);
		}

		return response()
			->json([
				'success' => true,
				'title' => 'Accepted cancellation of booking request',
				'message' => ''
			]);
	}
	
	public function rejectCancel(Request $req, $id) {
		$booking = Booking::with(['menus'])->find($id);

		if ($booking == null) {
			return response()
				->json([
					'success' => false,
					'title' => 'Booking not found',
					'message' => 'The booking either does not exists or is already deleted'
				]);
		}

		$validator = Validator::make($req->all(), [
			'reason' => 'required|string|max:255'
		], [
			'reason.required' => 'A reason is required',
			'reason.string' => 'Malformed data',
			'reason.max' => 'Character limit reached (255)',
		]);

		if ($validator->fails()) {
			return response()
				->json([
					'success' => false,
					'title' => 'Validation error',
					'message' => $validator->messages()->first()
				]);
		}

		try {
			DB::beginTransaction();

			$booking->cancel_requested = 0;
			$booking->reason = $req->reason;
			$booking->save();

			// Mailer to customer that his cancellation is approved

			// LOGGER
			activity('booking')
				->by(auth()->user())
				->on($booking)
				->event('reject-cancel')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Rejected cancellation request for booking #{$booking->control_no}.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'title' => 'Something went wrong, please try again later',
					'message' => ''
				]);
		}

		return response()
			->json([
				'success' => true,
				'title' => 'Rejected cancellation of booking request',
				'message' => ''
			]);
	}
}