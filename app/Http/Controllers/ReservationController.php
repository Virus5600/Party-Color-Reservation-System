<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
		$datetime = Carbon::now()->timezone('Asia/Tokyo');
		$isEightPM = $datetime->gt('08:00 PM');
		$validDate = $isEightPM ? $datetime->addDay()->format("Y-m-d") : $datetime->format("Y-m-d");
		$validTime = ($isEightPM ? '17' : ($datetime->format('H') < 17 ? '17' : $datetime->format('H'))) . ":00";
		$validDatetime = Carbon::parse("{$validDate} {$validTime}")->subHours(9)->timezone("Asia/Tokyo");
		$storeCap = Settings::getValue('capacity');

		$validator = Validator::make($req->all(), [
			'reservation_date' => array("required", "date", "after_or_equal:{$validDatetime->format('M d, Y h:i A')}"),
			'pax' => "required|numeric|between:0,{$storeCap}",
			'price' => "required|numeric|min:0",
			'time_hour' => "required|numeric|between:17,19",
			'time_min' => "required|numeric|between:0,59",
			'reservation_time' => "required",
			'extension' => "nullable|numeric|between:0,5",
			'menu' => "required|array|min:1",
			'menu.*' => "required|numeric|exists:menus,id",
			'phone_numbers' => "required|array|min:1",
			'contact_name' => "required_unless:contact_email,null|array|min:1",
			'contact_email' => "required_unless:contact_name,null|array|min:1",
			'contact_email.*' => "distinct:ignore_case",
		], [
			"reservation_date.required" => "Reservation date is required",
			"reservation_date.date" => "Reservation date should be a date",
			"reservation_date.after_or_equal" => "Reservation date should be on or after {$validDatetime->format('M d, Y h:i A')}",
			"pax.required" => "Amount of people is required",
			"pax.numeric" => "Amount of people should be a number",
			"pax.between" => "Amount of people should be between 1 and {$storeCap}",
			"price.required" => "Price is required",
			"price.numeric" => "Price should be a number",
			"price.min" => "Price should be at least ¥0.00",
			"time_hour.required" => "Hour is required",
			"time_hour.numeric" => "Hour should be a number",
			"time_hour.between" => "Hour should be between 17 and 19",
			"time_min.required" => "Minute is required",
			"time_min.numeric" => "Minute should be a number",
			"time_min.between" => "Minute should be between 0 and 59",
			"reservation_time.requried" => "Reservation Time is required",
			"extension.numeric" => "Extension should be a number",
			"extension.between" => "Extension should be between 0 and 5",
			"menu.required" => "A menu is required",
			"menu.array" => "Malformed menu data, please resubmit",
			"menu.min" => "At least 1 menu should be selected",
			"phone_numbers.required" => "A phone number is required",
			"phone_numbers.array" => "Malformed contact data, please resubmit",
			"phone_numbers.min" => "At least 1 phone number is required",
			"contact_name.required_unless" => "Contact name is required",
			"contact_name.array" => "Malformed contact name, please resubmit",
			"contact_name.min" => "At least 1 contact is required",
			"contact_email.required_unless" => "Contact email is required",
			"contact_email.array" => "Malformed contact email, please resubmit",
			"contact_email.min" => "At least 1 contact is required",
			"contact_email.*.distinct" => "Contact email already provided",
		]);
		$validatorInvalid = $validator->fails();

		$iterations = $req->phone_numbers ? count($req->phone_numbers) : 0;
		$phoneInvalid = false;
		for ($i = 0; $i < $iterations; $i++) {
			$phoneValidator = Validator::make($req->only(["phone_numbers"]), [
				"phone_numbers.{$i}" => array("required", "regex:/^\+*(?=.{7,14})[\d\s-]{7,15}$/", "distinct", "max:15"),
			], [
				"phone_numbers.{$i}.required" => "The phone number is required",
				"phone_numbers.{$i}.regex" => "Please put a proper phone number",
				"phone_numbers.{$i}.distinct" => "This number is already delcared",
				"phone_numbers.{$i}.max" => "Phone numbers is capped at 15 characters only",
			]);

			if ($phoneValidator->fails()) {
				$phoneInvalid = true;
				$validator->messages()->merge($phoneValidator->messages());
			}
		}

		$iterations = max(count($req->contact_name), count($req->contact_email));
		$contactInvalid = false;
		for ($i = 0; $i < $iterations; $i++) {
			$contactValidator = Validator::make($req->only(['contact_name', 'contact_email']), [
				"contact_name.{$i}" => "required_unless:contact_email.{$i},null|nullable|string|max:255",
				"contact_email.{$i}" => "required_unless:contact_name.{$i},null|nullable|email|distinct:ignore_case|max:255"
			], [
				"contact_name.{$i}.required_unless" => "Contact name is required",
				"contact_name.{$i}.string" => "Contact name should be a string",
				"contact_name.{$i}.max" => "Contact name is capped at 255",
				"contact_email.{$i}.required_unless" => "Contact email is required",
				"contact_email.{$i}.email" => "Contact email should be a valid email",
				"contact_email.{$i}.distinct" => "Contact email already provided",
				"contact_email.{$i}.max" => "Contact email is capped at 255",
			]);

			if ($contactValidator->fails()) {
				$contactInvalid = true;
				$validator->messages()->merge($contactValidator->messages());
			}
		}

		$contactNames = [];
		$contactEmails = [];
		$newContactIndex = [];
		for ($i = 0; $i < max(count($req->contact_name), count($req->contact_email)); $i++) {
			if ($req->contact_email[$i] || $req->contact_name[$i]) {
				array_push($contactEmails, $req->contact_email[$i]);
				array_push($contactNames, $req->contact_name[$i]);
				array_push($newContactIndex, $i);
			}
		}

		$req->merge([
			'contact_name' => $contactNames,
			'contact_email' => $contactEmails
		]);

		if ($validatorInvalid || $contactInvalid || $phoneInvalid)
			return redirect()
				->back()
				->withErrors($validator->messages()->merge($validator->messages()))
				->withInput()
				->with("new_contact_index",  $newContactIndex);

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

			$start_at = Carbon::parse("{$req->reservation_date} {$req->time_hour}:{$req->time_min}");
			$end_at = Carbon::parse("{$req->reservation_date} {$req->time_hour}:{$req->time_min}")
				->addHours($hoursToAdd)->addMinutes($minutesToAdd);
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
		$colorCode = $reservation->getStatusColorCode($reservation->getOverallStatus());
		$status = $reservation->getStatusText($reservation->getOverallStatus());

		if ($reservation == null) {
			return response()
				->json([
					'success' => false,
					'message' => 'The reservation either does not exists or is already deleted'
				]);
		}

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
			return redirect()
				->route('admin.reservations.index')
				->with('flash_error', 'The reservations either does not exists or is already deleted.');
		}

		// TODO: Finish reservation pages
	}

	protected function update(Request $req, $id) {
	}

	protected function delete($id) {
	}
}