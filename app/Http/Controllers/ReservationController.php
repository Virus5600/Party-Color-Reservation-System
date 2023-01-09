<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use Carbon\Carbon;

use App\ContactInformation;
use App\Inventory;
use App\Menu;
use App\Reservation;
use App\Settings;

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

			$menu = [];
			$hoursToAdd = 0;
			$minutesToAdd = ($req->extension * 60);
			foreach ($req->menu as $mi) {
				$menu["{$mi}"] = Menu::find($mi);
				
				$hoursComparisonVal = (int) Carbon::parse($menu["{$mi}"]->duration)->format("H");
				$hoursToAdd = max(Carbon::parse($menu["{$mi}"]->duration)->format("H"), $hoursToAdd);

				$minutesComparisonVal = (int) Carbon::parse($menu["{$mi}"]->duration)->format("i");
				$minutesToAdd = max($minutesComparisonVal, $minutesToAdd);
			}

			$closing = Carbon::parse("{$req->reservation_date} 22:00:00");
			$start_at = Carbon::parse("{$req->reservation_date} {$req->time_hour}:{$req->time_min}");
			$end_at = Carbon::parse("{$req->reservation_date} {$req->time_hour}:{$req->time_min}")
				->addHours($hoursToAdd)->addMinutes($minutesToAdd);

			if ($end_at->gt($closing)) {
				$toSubtract = $end_at->diffInMinutes($closing) / 60;

				$customMessage = new MessageBag(["extension" => "Extension made the reservation exceed closing time. Remove {$toSubtract} hours."]);
				
				return redirect()
					->back()
					->withErrors($validator->messages()->merge($customMessage))
					->withInput()
					->with("new_contact_index",  $newContactIndex);
			}

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

			// Menu calculation
			$menu = [];
			$hoursToAdd = 0;
			$minutesToAdd = ($req->extension * 60);
			foreach ($req->menu as $mi) {
				$menu["{$mi}"] = Menu::find($mi);
				
				$hoursComparisonVal = (int) Carbon::parse($menu["{$mi}"]->duration)->format("H");
				$hoursToAdd = max(Carbon::parse($menu["{$mi}"]->duration)->format("H"), $hoursToAdd);

				$minutesComparisonVal = (int) Carbon::parse($menu["{$mi}"]->duration)->format("i");
				$minutesToAdd = max($minutesComparisonVal, $minutesToAdd);
			}

			// Date calculation
			$start_at = Carbon::parse("{$req->reservation_date} {$req->time_hour}:{$req->time_min}");
			$end_at = Carbon::parse("{$req->reservation_date} {$req->time_hour}:{$req->time_min}")
				->addHours($hoursToAdd)->addMinutes($minutesToAdd);
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
			Log::debug($reservation->contactInformation()->toSql());
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

		return redirect()
			->route('admin.reservations.index')
			->with('flash_success', 'Successfully updated reservation');
	}

	protected function delete($id) {
		$reservation = Reservation::with(['menus', 'contactInformation'])->find($id);

		if ($reservation == null) {
			Log::info("No such reservation.", ["id" => $id, "reservation" => $reservation]);
			return redirect()
				->route('admin.reservations.index')
				->with('flash_error', 'The reservations either does not exists or is already deleted.');
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

			if ($reservation->approved == -1) {
				// Reduce the inventoy for realtime update
				foreach ($reservation->menus as $m) {
					$response = $m->reduceInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}
			}

			$reservation->approved = 1;
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

			if ($reservation->approved > -1) {
				// Return the inventory for realtime update
				foreach ($reservation->menus as $m) {
					$response = $m->returnInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}
			}

			$reservation->reason = $req->reason;
			$reservation->approved = -1;
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

			if ($reservation->approved == -1) {
				// Reduce the inventoy for realtime update
				foreach ($reservation->menus as $m) {
					$response = $m->reduceInventory();

					if (!$response->success) {
						throw new Exception($response->message);
					}
				}
			}

			$reservation->approved = 0;
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

		return response()
			->json([
				'success' => true,
				'title' => 'Successfully moved reservation request to pending',
				'message' => ''
			]);
	}
}