<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Enum\ApprovalStatus;
use App\Enum\Status;

use App\ActivityLog;
use App\Booking;
use App\ContactInformation;
use App\Inventory;
use App\Menu;
use App\Settings;

use Auth;
use DB;
use Exception;
use Log;
use Session;
use Validator;

class BookingController extends Controller
{
	protected function index(Request $req) {
		$bookings = Booking::with("menus")->get();

		return view('admin.bookings.index', [
			'bookings' => $bookings
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
				->withErrors($validator->messages())
				->withInput()
				->with("new_contact_index",  $newContactIndex);
		}

		try {
			DB::beginTransaction();

			$price = 0;

			foreach ($menu as $v)
				$price += $v->price;
			$price *= $req->pax;
			$price += ($req->extension * 500);

			$booking = Booking::create([
				'control_no' => Booking::createControlNumber(),
				'booking_type' => $req->booking_type,
				'start_at' => $start_at,
				'end_at' => $end_at,
				'reserved_at' => $req->booking_date,
				'extension' => $req->extension,
				'price' => $price,
				'pax' => $req->pax,
				'phone_numbers' => implode("|", $req->phone_numbers)
			]);

			foreach ($req->menu as $v)
				$booking->menus()
					->attach([
						$v => [
							'count' => $req->pax
						]
					]);

			$iterations = max(count($req->contact_name), count($req->contact_email));
			for ($i = 0; $i < $iterations; $i++) {
				$ci = ContactInformation::create([
					'contact_name' => $req->contact_name["{$i}"],
					'email' => $req->contact_email["{$i}"],
					'booking_id' => $booking->id
				]);
			}

			// Reduce the inventoy for realtime update
			foreach ($booking->menus as $m) {
				$response = $m->reduceInventory();

				if (!$response->success) {
					throw new Exception($response->message);
				}
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Booking #{$booking->control_no} created.",
			$booking->id,
			"Booking",
			Auth::user()->id
		);

		return redirect()
			->route('admin.bookings.index')
			->with('flash_success', 'Successfully added a new booking');
	}

	protected function show($id) {
		$booking = Booking::with([
				'menus',
				'contactInformation',
				'additionalOrders' => function($query) {return $query->withTrashed();},
				'additionalOrders.bookingMenus',
				'additionalOrders.bookingMenus.menu'
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

		return response()
			->json([
				'success' => true,
				'message' => 'Booking found',
				'booking' => $booking,
				'colorCode' => $colorCode,
				'status' => $status
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
				->withErrors($validator->messages()->merge($validator->messages()))
				->withInput()
				->with("new_contact_index",  $newContactIndex);
		}

		try {
			DB::beginTransaction();

			$price = 0;

			foreach ($menu as $v)
				$price += $v->price;
			$price *= $req->pax;
			$price += ($req->extension * 500);

			$booking->start_at = $start_at;
			$booking->end_at = $end_at;
			$booking->reserved_at = $req->booking_date;
			$booking->extension = $req->extension;
			$booking->price = $price;
			$booking->pax = $req->pax;
			$booking->phone_numbers = implode("|", $req->phone_numbers);
			$booking->save();

			// Return the inventory for realtime update
			foreach ($booking->menus as $m) {
				$response = $m->returnInventory();

				if (!$response->success) {
					throw new Exception($response->message);
				}
			}

			$booking->menus()->sync($req->menu);
			$booking->contactInformation()->delete();

			$iterations = max(count($req->contact_name), count($req->contact_email));
			for ($i = 0; $i < $iterations; $i++) {
				$ci = ContactInformation::create([
					'contact_name' => $req->contact_name["{$i}"],
					'email' => $req->contact_email["{$i}"],
					'booking_id' => $booking->id
				]);
			}

			// Reduce the inventoy for realtime update
			foreach ($booking->menus as $m) {
				$response = $m->reduceInventory();

				if (!$response->success) {
					throw new Exception($response->message);
				}
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Booking #{$booking->control_no} updated.",
			$booking->id,
			"Booking",
			Auth::user()->id
		);

		return redirect()
			->route('admin.bookings.index')
			->with('flash_success', 'Successfully updated booking');
	}

	protected function delete($id) {
		$booking = Booking::withTrashed()->find($id);

		if ($booking == null) {
			Log::info("No such booking.", ["id" => $id, "booking" => $booking]);
			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'The bookings either does not exists or is already deleted');
		}

		try {
			DB::beginTransaction();

			// Return the inventory for realtime update
			foreach ($booking->menus as $m) {
				$response = $m->returnInventory();

				if (!$response->success) {
					throw new Exception($response->message);
				}
			}

			$control_no = $booking->control_no;
			$booking->forceDelete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Booking #{$control_no} deleted.",
			$booking->id,
			"Booking",
			Auth::user()->id
		);

		ActivityLog::itemDeleted($id);

		return redirect()
			->route('admin.bookings.index')
			->with('flash_success', 'Successfully removed booking request');
	}

	protected function archive($id) {
		$booking = Booking::find($id);

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
				foreach ($booking->menus as $m) {
					$response = $m->returnInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}
			}

			$booking->delete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Booking #{$booking->control_no} archived.",
			$booking->id,
			"Booking",
			Auth::user()->id
		);
	}

	protected function accept($id) {
		$booking = Booking::find($id);

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
					$response = $m->reduceInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				$booking->items_returned = 0;
			}

			$booking->status = ApprovalStatus::Approved;
			$booking->reason = null;
			$booking->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();

			return response()
				->json([
					'success' => false,
					'title' => 'Something went wrong, please try again later',
					'message' => ''
				]);
		}

		ActivityLog::log(
			"Booking #{$booking->control_no} accepted.",
			$booking->id,
			"Booking",
			Auth::user()->id
		);

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully accepted booking request',
				'message' => ''
			]);
	}

	protected function reject(Request $req, $id) {
		$booking = Booking::find($id);

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
					$response = $m->returnInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				$booking->items_returned = 1;
			}

			$booking->reason = $req->reason;
			$booking->status = ApprovalStatus::Rejected;
			$booking->save();

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

		ActivityLog::log(
			"Booking #{$booking->control_no} rejected.",
			$booking->id,
			"Booking",
			Auth::user()->id
		);

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully rejected booking request',
				'message' => ''
			]);
	}

	protected function pending($id) {
		$booking = Booking::find($id);

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
					$response = $m->reduceInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				$booking->items_returned = 0;
			}

			$booking->status = ApprovalStatus::Pending;
			$booking->reason = null;
			$booking->save();

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

		ActivityLog::log(
			"Booking {$booking->control_no} moved to pending.",
			$booking->id,
			"Booking",
			Auth::user()->id
		);

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully moved booking request to pending',
				'message' => ''
			]);
	}
}