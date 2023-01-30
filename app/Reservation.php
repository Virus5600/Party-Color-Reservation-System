<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use Carbon\Carbon;

use App\Enum\ApprovalStatus;
use App\Enum\Status;

use Log;
use NumberFormatter;
use Validator;

class Reservation extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'start_at',
		'end_at',
		'reserved_at',
		'extension',
		'price',
		'pax',
		'phone_numbers',
		'archived',
		'status',
		'reason',
		'items_returned',
	];

	protected $casts = [
		'reserved_at' => 'datetime',
		'deleted_at' => 'datetime',
	];

	// Accessor
	protected function getStartAtAttribute($value) {
		return Carbon::createFromFormat('H:i:s', $value)->format('H:i');
	}

	protected function getEndAtAttribute($value) {
		return Carbon::createFromFormat('H:i:s', $value)->format('H:i');
	}

	protected function getReservedAtAttribute($value) {
		return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
	}

	// Relationships
	public function menus() { return $this->belongsToMany('App\Menu', 'reservation_menus'); }
	public function contactInformation() { return $this->hasMany('App\ContactInformation'); }

	// Public Function
	public function getStatus() {
		$start = Carbon::parse("{$this->reserved_at} {$this->start_at}");
		$end = Carbon::parse("{$this->reserved_at} {$this->end_at}");
		$now = Carbon::parse(now()->timezone("Asia/Manila")->format("Y-m-d H:i:s"));
		
		$toCome = $now->lt($start);
		$between = $now->between($start, $end);
		$done = $now->gt($end);

		if ($this->cancelled == 1)
			return Status::Cancelled;

		if ($toCome)
			return Status::Coming;
		else if ($between)
			return Status::Happening;
		else if ($done)
			return Status::Done;
		else {
			Log::info("Reservation does not match the three status; returning \"NonExistent\" as value.", ["Reservation" => $this]);
			return Status::NonExistent;
		}
	}

	public function getOverallStatus() {
		if ($this->trashed())
			return "#1e2b37";

		$approvalStatus = $this->status;
		$reservationStatus = $this->getStatus();

		if ($approvalStatus == ApprovalStatus::Approved) {
			return $reservationStatus;
		}
		else {
			if ($approvalStatus == ApprovalStatus::Pending && ($reservationStatus == Status::Happening || $reservationStatus == Status::Done))
				return Status::Ghosted;
			return $approvalStatus;
		}
	}

	public function getStatusColorCode($status) {

		if ($this->trashed())
			return "#1e2b37";

		switch ($status) {
			case Status::Coming:
				return "#17a2b8";
			
			case Status::Happening:
				return "#007bff";

			case Status::Done:
				return "#6c757d";

			case ApprovalStatus::Pending:
				return "#ffc107";

			case ApprovalStatus::Approved:
				return "#28a745";

			case ApprovalStatus::Rejected:
			case Status::Cancelled:
				return "#dc3545";

			default:
				return "#1e2b37";
		}
	}

	public function getStatusText($status) {
		if ($this->trashed())
			return "Archived";

		switch ($status) {
			case Status::Coming:
				return "Coming";
			
			case Status::Happening:
				return "Happening";

			case Status::Done:
				return "Done";

			case Status::Ghosted:
				return "Ghosted";

			case Status::Cancelled:
				return "Cancelled";

			case ApprovalStatus::Pending:
				return "Pending";

			case ApprovalStatus::Approved:
				return "Approved";

			case ApprovalStatus::Rejected:
				return "Rejected";

			default:
				return "Unknown";
		}
	}

	public function reservationFor() {
		return $this->contactInformation()->first()->contact_name;
	}

	public function fetchPrice() {
		return (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . " $this->price";
	}

	// STATIC FUNCTIONS
	// Validation
	public static function validate($req) {
		$datetime = Carbon::now()->timezone('Asia/Tokyo');
		$isEightPM = $datetime->gt('08:00 PM');
		$validDate = $isEightPM ? $datetime->addDay()->format("Y-m-d") : $datetime->format("Y-m-d");
		$validTime = ($isEightPM ? '17' : ($datetime->format('H') < 17 ? '17' : $datetime->format('H'))) . ":00";
		$validDatetime = Carbon::parse("{$validDate} {$validTime}")->subHours(9)->timezone("Asia/Tokyo");
		$storeCap = Settings::getValue('capacity');

		$validationRules = [
			'reservation_date' => array("required", "date", "after_or_equal:{$validDatetime->format('M d, Y h:i A')}"),
			'pax' => "required|numeric|between:0,{$storeCap}",
			'price' => "required|numeric|min:0",
			'time_hour' => "required|numeric|between:17,19",
			'time_min' => "required|numeric|between:0,59",
			'reservation_time' => "required",
			'extension' => "nullable|numeric|between:0,5",
			'menu' => "required|array|min:1",
			'menu.*' => "required|numeric|exists:menus,id",
			'phone_numbers' => "required|array|min:1", // this is not worked and it works when array format is not used!!
			'contact_name' => "required_unless:contact_email,null|array|min:1",
			'contact_email' => "required_unless:contact_name,null|array|min:1",
			'contact_email.*' => "distinct:ignore_case",
		];

		$validationMsg = [
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
		];

		$req->merge([
			'phone_numbers' => explode(",", $req->phone_numbers)
		]);

		$iterations = $req->phone_numbers ? count($req->phone_numbers) : 0;
		for ($i = 0; $i < $iterations; $i++) {
			$validationRules["phone_numbers.{$i}"] = array("required", "regex:/^\+*(?=.{7,14})[\d\s-]{7,15}$/", "distinct", "max:15"); // Identifies a phone number with atleast 7 digits

			$validationMsg["phone_numbers.{$i}.required"] = "The phone number is required";
			$validationMsg["phone_numbers.{$i}.regex"] = "Please put a proper phone number";
			$validationMsg["phone_numbers.{$i}.distinct"] = "This number is already delcared";
			$validationMsg["phone_numbers.{$i}.max"] = "Phone numbers is capped at 15 characters only";
		}

		$iterations = max(count($req->contact_name), count($req->contact_email));
		for ($i = 0; $i < $iterations; $i++) {
			$validationRules["contact_name.{$i}"] = "required_unless:contact_email.{$i},null|nullable|string|max:255";
			$validationRules["contact_email.{$i}"] = "required_unless:contact_name.{$i},null|nullable|email|distinct:ignore_case|max:255";
			
			$validationMsg["contact_name.{$i}.required_unless"] = "Contact name is required";
			$validationMsg["contact_name.{$i}.string"] = "Contact name should be a string";
			$validationMsg["contact_name.{$i}.max"] = "Contact name is capped at 255";
			$validationMsg["contact_email.{$i}.required_unless"] = "Contact email is required";
			$validationMsg["contact_email.{$i}.email"] = "Contact email should be a valid email";
			$validationMsg["contact_email.{$i}.distinct"] = "Contact email already provided";
			$validationMsg["contact_email.{$i}.max"] = "Contact email is capped at 255";
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

		$validator = Validator::make($req->all(), $validationRules, $validationMsg);

		// Calculates how many minutes will be added to the reservation (will be used using the addMinutes)
		$menu = [];
		$hoursToAdd = 0;
		$minutesToAdd = 0;
		foreach ($req->menu as $mi) {
			$menu["{$mi}"] = Menu::find($mi);
			
			// Compares what hour has the highest among the menus selected
			$hoursComparisonVal = (int) Carbon::parse($menu["{$mi}"]->duration)->format("H");
			$hoursToAdd = max(Carbon::parse($menu["{$mi}"]->duration)->format("H"), $hoursToAdd);

			// Compares what minute has the highest among the menus selected
			$minutesComparisonVal = (int) Carbon::parse($menu["{$mi}"]->duration)->format("i");
			$minutesToAdd = max($minutesComparisonVal, $minutesToAdd);
		}
		// Adds the extension as minutes
		$minutesToAdd += ($req->extension * 60);

		// Calculates the duration of the reservation
		$closing = Carbon::parse("{$req->reservation_date} 22:00:00");
		$start_at = Carbon::parse("{$req->reservation_date} {$req->time_hour}:{$req->time_min}");
		$end_at = Carbon::parse("{$req->reservation_date} {$req->time_hour}:{$req->time_min}")
			->addHours($hoursToAdd)->addMinutes($minutesToAdd);

		$validator->after(function ($validator) use ($req, $storeCap, $start_at, $end_at, $closing) {
			// Checks whether the reservation exceeded the closing time
			if ($end_at->gt($closing)) {
				$toSubtract = $end_at->diffInMinutes($closing) / 60;

				$validator->errors()->add(
					"extension",
					"Extension made the reservation exceed closing time. Remove {$toSubtract} " . Str::plural('hour', $toSubtract)
				);
			}

			// Checks if the reservation can still be accomodated (store capacity related)
			{
				$paxAccomodated = Reservation::whereDate('reserved_at', '=', $req->reservation_date)
					->whereTime('start_at', '<', $end_at)
					->whereTime('end_at', '>', $start_at)
					->sum('pax');

				if ($paxAccomodated >= $storeCap) {
					$end = $end_at->gt($closing) ? $closing : $end_at;
					$validator->errors()->add(
						"general",
						"Sorry but reservation cannot be accomodated. The restaurant is at full capacity for this time range <b>({$start_at->format('H:i')} - {$end->format('H:i')})</b>."
					);
				}

				$totalPax = $paxAccomodated + $req->pax;
				if ($totalPax > $storeCap) {
					$validator->errors()->add(
						"general",
						"Sorry but reservation cannot be accomodated. Current reservations is already at restaurant's capacity which is at <b>{$storeCap}</b> people. Adding the current reservaton will result to a total of <b>{$totalPax}</b> people reserved at the same time..."
					);
				}
			}
		});

		return [
			'validator' => $validator,
			'newContactIndex' => $newContactIndex,
			'menu' => $menu,
			'start_at' => $start_at,
			'end_at' => $end_at
		];
	}
}