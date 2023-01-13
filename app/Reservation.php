<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

use Log;
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

	public function getApprovalStatus() {
		$approved = $this->approved;

		if ($this->cancelled == 1)
			return Status::Cancelled;

		if ($approved == -1)
			return ApprovalStatus::Rejected;
		else if ($approved == 1)
			return ApprovalStatus::Approved;
		else
			return ApprovalStatus::Pending;
	}

	public function getOverallStatus() {
		$approvalStatus = $this->getApprovalStatus();
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
			'phone_numbers' => "required|array|min:1",
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

		return [
			'validator' => $validator,
			'newContactIndex' => $newContactIndex
		];
	}
}

// ENUMS
abstract class Status {
	const Coming = 0;
	const Happening = 1;
	const Done = 2;
	const Cancelled = 3;
	const Ghosted = 4;
	const NonExistent = 5;
}

abstract class ApprovalStatus {
	const Pending = 10;
	const Approved = 11;
	const Rejected = 12;
}