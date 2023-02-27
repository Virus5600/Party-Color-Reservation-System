<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use Carbon\Carbon;

use App\Enum\ApprovalStatus;
use App\Enum\Status;

use Exception;
use Log;
use NumberFormatter;
use Validator;

class Booking extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'control_no',
		'booking_type',
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
		'special_request',
		'cancel_requested',
		'cancel_request_reason'
	];

	protected $casts = [
		'created_at' => 'datetime:Y-m-d h:i A',
		'updated_at' => 'datetime:H:i',
		'reserved_at' => 'date:Y-m-d',
		'deleted_at' => 'datetime',
	];

	// Public static variables
	public static $bookingTypes = ['reservation', 'walk-ins'];

	// Accessor
	protected function getStartAtAttribute($value) {
		try {
			return Carbon::createFromFormat('H:i:s', $value)->format('h:i A');
		} catch (Exception $e) {
			return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('h:i A');
		}
	}

	protected function getEndAtAttribute($value) {
		try {
			return Carbon::createFromFormat('H:i:s', $value)->format('h:i A');
		} catch (Exception $e) {
			return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('h:i A');
		}
	}

	protected function getReservedAtAttribute($value) {
		try {
			return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('h:i A');
		} catch (Exception $e) {
			return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
		}
	}

	// Relationships
	public function additionalOrders() { return $this->hasMany('App\AdditionalOrder'); }
	public function menus() { return $this->morphToMany('App\MenuVariation', 'orderable', 'orderables')->withPivot('count'); }
	public function contactInformation() { return $this->hasMany('App\ContactInformation'); }

	// Conditional Relationships
	public function primaryContactInformation() { return $this->hasOne('App\ContactInformation', 'booking_id')->oldest(); }

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
			Log::info("Booking does not match the three status; returning \"NonExistent\" as value.", ["Booking" => $this]);
			return Status::NonExistent;
		}
	}

	public function getOverallStatus($asValue = false) {
		if ($this->trashed())
			return $asValue ? Status::Cancelled->asValue : Status::Cancelled;

		$status = $this->status;

		$approvalStatus = Status::tryFrom($status) ?? (ApprovalStatus::tryFrom($status) ?? $status);
		$bookingStatus = $this->getStatus();

		if ($this->cancel_requested == 1) {
			return $asValue ? Status::CancelRequest->asValue : Status::CancelRequest;
		}
		else if ($approvalStatus == ApprovalStatus::Approved) {
			return $asValue ? $bookingStatus->value : $bookingStatus;
		}
		else {
			if ($approvalStatus == ApprovalStatus::Pending && ($bookingStatus == Status::Happening || $bookingStatus == Status::Done))
				return $asValue ? Status::Ghosted->value : Status::Ghosted;
			return $status;
		}
	}

	public function getStatusColorCode($status) {

		if ($this->trashed())
			return "#1e2b37";

		if (is_numeric($status))
			$status = Status::tryFrom($status) ?? (ApprovalStatus::tryFrom($status) ?? $status);

		switch ($status) {
			case Status::CancelRequest:
				return "#fd7e14";

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
		
		if (is_numeric($status))
			$status = Status::tryFrom($status) ?? (ApprovalStatus::tryFrom($status) ?? $status);

		switch ($status) {
			case Status::CancelRequest:
				return "Cancellation Requested";

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

	public function bookingFor() {
		return $this->contactInformation()->first()->contact_name;
	}

	public function fetchPrice() {
		return (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . " $this->price";
	}

	// STATIC FUNCTIONS
	// Control Number Generator
	public static function createControlNumber() {
		$controlNumber = now()->format("ymd") . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
		$validator = Validator::make(['control_no' => $controlNumber], ['control_no' => 'unique:bookings,control_no']);

		if (count($validator->errors()->get('control_no')) > 0)
			$controlNumber = createControlNumber();

		return $controlNumber;
	}

	public static function showRoute($id) {
		return route('admin.bookings.index', ['cn' => Booking::select(['id', 'control_no'])->withTrashed()->find($id)->control_no]);
	}

	// Validation
	public static function validate($req, $id = null) {
		$datetime = Carbon::now()->timezone('Asia/Tokyo');
		$openingTime = Settings::getValue('opening');
		$closingTime = Settings::getValue('closing');
		$opening = Carbon::parse("{$req->booking_date} {$openingTime}");
		$closing = Carbon::parse("{$req->booking_date} {$closingTime}");
		$isClosing = $datetime->gt($closing);
		$validDate = $isClosing ? $datetime->addDay()->format("Y-m-d") : $datetime->format("Y-m-d");
		$validTime = ($isClosing ? $opening->format('H') : ($datetime->format('H') < $opening->format('H') ? $opening->format('H') : $datetime->format('H'))) . ":00";
		$validDatetime = Carbon::parse("{$validDate} {$validTime}")->subHours(9)->timezone("Asia/Tokyo");
		$storeCap = Settings::getValue('capacity');

		$validationRules = [
			'booking_date' => array("required", "date", "after_or_equal:{$validDatetime->format('M d, Y h:i A')}"),
			'pax' => "required|numeric|between:1,{$storeCap}",
			'price' => "required|numeric|min:0",
			'time_hour' => "required|numeric|between:{$opening->format('H')},{$closing->format('H')}",
			'time_min' => "required|numeric|between:0,59",
			'booking_time' => "required",
			'extension' => "nullable|numeric|between:0,5",
			'menu' => "required|array|min:1",
			'amount' => "required|array|min:1",
			'phone_numbers' => "required|array|min:1",
			'contact_name' => "required_unless:contact_email,null|array|min:1",
			'contact_email' => "required_unless:contact_name,null|array|min:1",
			'contact_email.*' => "distinct:ignore_case",
			'special_request' => "nullable|string|max:1000",
		];

		$validationMsg = [
			"booking_date.required" => "Booking date is required",
			"booking_date.date" => "Booking date should be a date",
			"booking_date.after_or_equal" => "Booking date should be on or after {$validDatetime->format('M d, Y h:i A')}",
			"pax.required" => "Amount of people is required",
			"pax.numeric" => "Amount of people should be a number",
			"pax.between" => "Amount of people should be between 1 and {$storeCap}",
			"price.required" => "Price is required",
			"price.numeric" => "Price should be a number",
			"price.min" => "Price should be at least ¥0.00",
			"time_hour.required" => "Hour is required",
			"time_hour.numeric" => "Hour should be a number",
			"time_hour.between" => "Hour should be between {$opening->format('H')} and {$closing->format('H')}",
			"time_min.required" => "Minute is required",
			"time_min.numeric" => "Minute should be a number",
			"time_min.between" => "Minute should be between 0 and 59",
			"booking_time.requried" => "Booking Time is required",
			"extension.numeric" => "Extension should be a number",
			"extension.between" => "Extension should be between 0 and 5",
			"menu.required" => "A menu is required",
			"menu.array" => "Malformed menu data, please resubmit",
			"menu.min" => "At least 1 menu should be selected along with an amount",
			"amount.required" => "An amount is required",
			"amount.array" => "Malformed amount data, please resubmit",
			"amount.min" => "At least 1 amount should be provided along with a menu",
			"menu.*.required_unless" => "Menu is required",
			"menu.*.numeric" => "Please refrain from modifying the form",
			"menu.*.exists" => "Please refrain from modifying the form",
			"amount.*.required_unless" => "An amount is needed",
			"amount.*.numeric" => "An amount should be a number",
			"amount.*.between" => "Amount should be from 1 to {$storeCap}",
			"phone_numbers.required" => "A phone number is required",
			"phone_numbers.array" => "Malformed contact data, please resubmit",
			"phone_numbers.min" => "At least 1 phone number is required",
			"contact_name.required_unless" => "Contact name is required",
			"contact_name.array" => "Malformed contact name, please resubmit",
			"contact_name.min" => "At least 1 contact is required",
			"contact_name.*.required_unless" => "Contact name is required",
			"contact_name.*.string" => "Contact name should be a string",
			"contact_name.*.max" => "Contact name is capped at 255",
			"contact_email.required_unless" => "Contact email is required",
			"contact_email.array" => "Malformed contact email, please resubmit",
			"contact_email.min" => "At least 1 contact is required",
			"contact_email.*.distinct" => "Contact email already provided",
			"contact_email.*.required_unless" => "Contact email is required",
			"contact_email.*.email" => "Contact email should be a valid email",
			"contact_email.*.distinct" => "Contact email already provided",
			"contact_email.*.max" => "Contact email is capped at 255",
			"special_request..nullable" => "",
			"special_request.string" => "Malformed content...",
			"special_request.max" => "A maximum of 1000 characters is the allowed limit"
		];

		$req->merge(['phone_numbers' => explode(",", $req->phone_numbers)]);

		// Balancer
		$balancer = [];
		$menuGroupNull = false;
		if ($req->menu == null && $req->amount != null) {
			for ($i = 0; $i < count($req->amount); $i++)
				array_push($balancer, '');
			$req->merge(['menu' => $balancer]);
			$menuGroupNull = true;
		}
		else if ($req->menu != null && $req->amount == null) {
			for ($i = 0; $i < count($req->menu); $i++)
				array_push($balancer, '');
			$req->merge(['amount' => $balancer]);
			$menuGroupNull = true;
		}
		
		$balancer = [];
		$contactGroupNull = false;
		if ($req->contact_name == null && $req->contact_email != null) {
			for ($i = 0; $i < count($req->contact_email); $i++)
				array_push($balancer, '');
			$req->merge(['contact_name' => $balancer]);
			$contactGroupNull = true;
		}
		else if ($req->contact_name != null && $req->contact_email == null) {
			for ($i = 0; $i < count($req->contact_name); $i++)
				array_push($balancer, '');
			$req->merge(['contact_email' => $balancer]);
			$contactGroupNull = true;
		}

		// Validation for phone number
		{
			$validationRules["phone_numbers.*"] = array("required", "regex:/^\+*(?=.{7,14})[\d\s-]{7,15}$/", "distinct", "max:15"); // Identifies a phone number with atleast 7 digits

			$validationMsg["phone_numbers.*.required"] = "The phone number is required";
			$validationMsg["phone_numbers.*.regex"] = "Please put a proper phone number";
			$validationMsg["phone_numbers.*.distinct"] = "This number is already delcared";
			$validationMsg["phone_numbers.*.max"] = "Phone numbers is capped at 15 characters only";
		}

		$iterations = max(count($req->menu), count($req->amount));
		for ($i = 0; $i < $iterations; $i++) {
			$validationRules["menu.{$i}"] = "required_unless:amount.{$i},null|numeric|exists:menu_variations,id";
			$validationRules["amount.{$i}"] = "required_unless:menu.{$i},null|numeric|between:1,{$storeCap}";
		}

		$iterations = max(count($req->contact_name), count($req->contact_email));
		for ($i = 0; $i < $iterations; $i++) {
			$validationRules["contact_name.{$i}"] = "required_unless:contact_email.{$i},null|nullable|string|max:255";
			$validationRules["contact_email.{$i}"] = "required_unless:contact_name.{$i},null|nullable|email|distinct:ignore_case|max:255";
		}

		$newMenu = [];
		$newAmount = [];
		$newMenuIndex = [];
		for ($i = 0; $i < max(count($req->menu), count($req->amount)); $i++) {
			if ($req->menu[$i] || $req->amount[$i]) {
				array_push($newMenu, $req->menu[$i]);
				array_push($newAmount, $req->amount[$i]);
				array_push($newMenuIndex, $i);
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

		$validator = Validator::make($req->all(), $validationRules, $validationMsg);

		// Calculates how many minutes will be added to the booking (will be used using the addMinutes)
		$start_at = null;
		$end_at = null;
		if (!$menuGroupNull) {
			$menu = [];
			$hoursToAdd = 0;
			$minutesToAdd = 0;
			foreach ($req->menu as $mi) {
				$menu["{$mi}"] = MenuVariation::find($mi);
				
				// Compares what hour has the highest among the menus selected
				$hoursComparisonVal = (int) Carbon::parse($menu["{$mi}"]->duration)->format("H");
				$hoursToAdd = max(Carbon::parse($menu["{$mi}"]->duration)->format("H"), $hoursToAdd);

				// Compares what minute has the highest among the menus selected
				$minutesComparisonVal = (int) Carbon::parse($menu["{$mi}"]->duration)->format("i");
				$minutesToAdd = max($minutesComparisonVal, $minutesToAdd);
			}
			// Adds the extension as minutes
			$minutesToAdd += ($req->extension * 60);

			// Calculates the duration of the booking
			$closing = Carbon::parse("{$req->booking_date} 22:00:00");
			$start_at = Carbon::parse("{$req->booking_date} {$req->time_hour}:{$req->time_min}");
			$end_at = Carbon::parse("{$req->booking_date} {$req->time_hour}:{$req->time_min}")
				->addHours($hoursToAdd)->addMinutes($minutesToAdd);
			
			$validator->after(function ($validator) use ($req, $storeCap, $start_at, $end_at, $closing, $id) {
				// Checks whether the booking exceeded the closing time
				if ($end_at->gt($closing)) {
					$toSubtract = $end_at->diffInMinutes($closing) / 60;

					$validator->errors()->add(
						"extension",
						"Extension made the booking exceed closing time. Remove {$toSubtract} " . Str::plural('hour', $toSubtract)
					);
				}

				// Checks if the booking can still be accomodated (store capacity related)
				{
					$paxAccomodated = Booking::whereDate('reserved_at', '=', $req->booking_date)
						->where('id', '<>', $id)
						->where('status', '=', ApprovalStatus::Approved->value)
						->whereTime('start_at', '<', $end_at)
						->whereTime('end_at', '>', $start_at)
						->sum('pax');

					if ($paxAccomodated >= $storeCap) {
						$end = $end_at->gt($closing) ? $closing : $end_at;
						$validator->errors()->add(
							"general",
							"Sorry but booking cannot be accomodated. The restaurant is at full capacity for this time range <b>({$start_at->format('H:i')} - {$end->format('H:i')})</b>."
						);
					}

					$totalPax = $paxAccomodated + $req->pax;
					if ($totalPax > $storeCap) {
						$validator->errors()->add(
							"general",
							"Sorry but booking cannot be accomodated. Current bookings is already at restaurant's capacity which is at <b>{$storeCap}</b> people. Adding the current reservaton will result to a total of <b>{$totalPax}</b> people reserved at the same time..."
						);
					}
				}
			});
		}

		return [
			'validator' => $validator,
			'newContactIndex' => count($newContactIndex) > 0 ? $newContactIndex : null,
			'newMenuIndex' => count($newMenuIndex) > 0 ? $newMenuIndex : null,
			'start_at' => $start_at,
			'end_at' => $end_at
		];
	}
}