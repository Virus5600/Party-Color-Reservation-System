<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Enum\ApprovalStatus;
use App\Enum\Status;

use App\ContactInformation;
use App\Inventory;
use App\Menu;
use App\Reservation;
use App\Settings;
use App\ActivityLog;

use DB;
use Exception;
use Log;
use Validator;

class ReservationController extends Controller
{
	protected function index(Request $req) {
		$reservations = Reservation::with("menus")->get();

		return view('admin.reservations.index', [
			'reservations' => $reservations
		]);
	}

	protected function create() {
		$menus = Menu::get();

		return view('admin.reservations.create', [
			'menus' => $menus
		]);
	}

	protected function store(Request $req) {
		extract(Reservation::validate($req));

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

			$reservation = Reservation::create([
				'start_at' => $start_at,
				'end_at' => $end_at,
				'reserved_at' => $req->reservation_date,
				'extension' => $req->extension,
				'price' => $price,
				'pax' => $req->pax,
				'phone_numbers' => implode("|", $req->phone_numbers)
			]);

			$reservation->menus()->attach($req->menu);

			$iterations = max(count($req->contact_name), count($req->contact_email));
			for ($i = 0; $i < $iterations; $i++) {
				$ci = ContactInformation::create([
					'contact_name' => $req->contact_name["{$i}"],
					'email' => $req->contact_email["{$i}"],
					'reservation_id' => $reservation->id
				]);
			}

			// Reduce the inventoy for realtime update
			foreach ($reservation->menus as $m) {
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
				->route('admin.reservations.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Reservation created.",
			null,
			true
		);

		return redirect()
			->route('admin.reservations.index')
			->with('flash_success', 'Successfully added a new reservation');
	}

	protected function show($id) {
		$reservation = Reservation::with(['menus', 'contactInformation'])->find($id);

		if ($reservation == null) {
			return response()
				->json([
					'success' => false,
					'message' => 'The reservation either does not exists or is already deleted'
				]);
		}

		$colorCode = $reservation->getStatusColorCode($reservation->getOverallStatus());
		$status = $reservation->getStatusText($reservation->getOverallStatus());

		return response()
			->json([
				'success' => true,
				'message' => 'Reservation found',
				'reservation' => $reservation,
				'colorCode' => $colorCode,
				'status' => $status
			]);
	}

	protected function edit($id) {
		$reservation = Reservation::with(['menus', 'contactInformation'])->find($id);

		if ($reservation == null) {
			Log::info("No such reservation.", ["id" => $id, "reservation" => $reservation]);
			return redirect()
				->route('admin.reservations.index')
				->with('flash_error', 'The reservations either does not exists or is already deleted.');
		}

		$menus = Menu::get();
		$newContactIndex = [];

		for ($i = 0; $i < $reservation->contactInformation->count(); $i++)
			array_push($newContactIndex, "{$i}");

		\Session::put("new_contact_index", $newContactIndex);

		return view('admin.reservations.edit', [
			'reservation' => $reservation,
			'menus' => $menus
		]);
	}

	protected function update(Request $req, $id) {
		$reservation = Reservation::find($id);

		if ($reservation == null) {
			Log::info("No such reservation.", ["id" => $id, "reservation" => $reservation]);
			return redirect()
				->route('admin.reservations.index')
				->with('flash_error', 'The reservations either does not exists or is already deleted.');
		}

		extract(Reservation::validate($req));

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

			$reservation->start_at = $start_at;
			$reservation->end_at = $end_at;
			$reservation->reserved_at = $req->reservation_date;
			$reservation->extension = $req->extension;
			$reservation->price = $price;
			$reservation->pax = $req->pax;
			$reservation->phone_numbers = implode("|", $req->phone_numbers);
			$reservation->save();

			// Return the inventory for realtime update
			foreach ($reservation->menus as $m) {
				$response = $m->returnInventory();

				if (!$response->success) {
					throw new Exception($response->message);
				}
			}

			$reservation->menus()->sync($req->menu);
			$reservation->contactInformation()->delete();


			$iterations = max(count($req->contact_name), count($req->contact_email));
			for ($i = 0; $i < $iterations; $i++) {
				$ci = ContactInformation::create([
					'contact_name' => $req->contact_name["{$i}"],
					'email' => $req->contact_email["{$i}"],
					'reservation_id' => $reservation->id
				]);
			}

			// Reduce the inventoy for realtime update
			foreach ($reservation->menus as $m) {
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
				->route('admin.reservations.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Reservation {$id} updated.",
			null,
			true
		);

		return redirect()
			->route('admin.reservations.index')
			->with('flash_success', 'Successfully updated reservation');
	}

	protected function delete($id) {
		$reservation = Reservation::withTrashed()->find($id);

		if ($reservation == null) {
			Log::info("No such reservation.", ["id" => $id, "reservation" => $reservation]);
			return redirect()
				->route('admin.reservations.index')
				->with('flash_error', 'The reservations either does not exists or is already deleted');
		}

		try {
			DB::beginTransaction();

			// Return the inventory for realtime update
			foreach ($reservation->menus as $m) {
				$response = $m->returnInventory();

				if (!$response->success) {
					throw new Exception($response->message);
				}
			}

			$reservation->forceDelete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();

			return redirect()
				->route('admin.reservations.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Reservation {$id} deleted.",
			null,
			true
		);

		return redirect()
			->route('admin.reservations.index')
			->with('flash_success', 'Successfully removed reservation request');
	}

	protected function archive($id) {
		$reservation = Reservation::find($id);

		if ($reservation == null) {
			Log::info("No such reservation.", ["id" => $id, "reservation" => $reservation]);
			return redirect()
				->route('admin.reservations.index')
				->with('flash_error', 'The reservations either does not exists or is already deleted');
		}

		try {
			DB::beginTransaction();

			// Return the inventory for realtime update
			foreach ($reservation->menus as $m) {
				$response = $m->returnInventory();

				if (!$response->success) {
					throw new Exception($response->message);
				}
			}

			$reservation->delete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();

			return redirect()
				->route('admin.reservations.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}
	}

	protected function accept($id) {
		$reservation = Reservation::find($id);

		if ($reservation == null) {
			Log::info("No such reservation.", ["id" => $id, "reservation" => $reservation]);
			return response()
				->json([
					'success' => false,
					'title' => 'The reservations either does not exists or is already deleted',
					'message' => ''
				]);
		}

		try {
			DB::beginTransaction();

			if ($reservation->items_returned == 1) {
				// Reduce the inventoy for realtime update
				foreach ($reservation->menus as $m) {
					$response = $m->reduceInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				$reservation->items_returned = 0;
			}

			$reservation->status = ApprovalStatus::Approved;
			$reservation->reason = null;
			$reservation->save();

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
			"Reservation {$id} accepted.",
			null,
			true
		);

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully accepted reservation request',
				'message' => ''
			]);
	}

	protected function reject(Request $req, $id) {
		$reservation = Reservation::find($id);

		if ($reservation == null) {
			return response()
				->json([
					'success' => false,
					'title' => 'Reservation not found',
					'message' => 'The reservation either does not exists or is already deleted'
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

			if ($reservation->items_returned == 0) {
				// Return the inventory for realtime update
				foreach ($reservation->menus as $m) {
					$response = $m->returnInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				$reservation->items_returned = 1;
			}

			$reservation->reason = $req->reason;
			$reservation->status = ApprovalStatus::Rejected;
			$reservation->save();

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
			"Reservation {$id} rejected.",
			null,
			true
		);

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully rejected reservation request',
				'message' => ''
			]);
	}

	protected function pending($id) {
		$reservation = Reservation::find($id);

		if ($reservation == null) {
			Log::info("No such reservation.", ["id" => $id, "reservation" => $reservation]);
			return response()
				->json([
					'success' => false,
					'title' => 'The reservations either does not exists or is already deleted',
					'message' => ''
				]);
		}

		try {
			DB::beginTransaction();

			if ($reservation->items_returned == 1) {
				// Reduce the inventoy for realtime update
				foreach ($reservation->menus as $m) {
					$response = $m->reduceInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}

				$reservation->items_returned = 0;
			}

			$reservation->status = ApprovalStatus::Pending;
			$reservation->reason = null;
			$reservation->save();

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
			"Reservation {$id} moved to pending.",
			null,
			true
		);

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully moved reservation request to pending',
				'message' => ''
			]);
	}
}