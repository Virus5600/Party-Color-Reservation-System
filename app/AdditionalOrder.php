<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Http\Request;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use Carbon\Carbon;

use Log;
use NumberFormatter;
use Validator;

class AdditionalOrder extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'booking_id',
		'extension',
		'price',
	];

	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime'
	];

	protected $with = [
		'menus'
	];

	// Relationships
	public function booking() { return $this->belongsTo('App\Booking'); }
	public function menus() { return $this->morphToMany('App\MenuVariation', 'orderable', 'orderables')->withPivot('count'); }
	public function orderable() { return $this->morphMany('App\Orderable', 'orderable'); }

	// Custom Functions
	public function fetchPrice($orderOnly = false) {
		$price = $this->price;

		if (!$orderOnly)
			$price -= ($this->extension * Settings::getValue('extension_fee'));

		return (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . " $price";
	}

	// STATIC FUNCTIONS
	// Validation
	public static function validate(Request $req, Booking $booking) {
		$openingTime = Settings::getValue('opening');
		$closingTime = Settings::getValue('closing');
		$storeCap = Settings::getValue('capacity');

		$validationRules = [
			"price" => "required|numeric|min:0",
			'extension' => "nullable|numeric|between:0,4",
			'menu' => "required|array|min:1",
			'menu.*' => "required|numeric|exists:menu_variations,id",
			"count" => "required|array|min:1",
			"count.*" => "required|numeric|between:1,{$storeCap}"
		];
		$validationMsg = [
			"price.required" => "Price is required",
			"price.numeric" => "Price should be a number",
			"price.min" => "Price should be at least ¥0.00",
			"extension.numeric" => "Extension should be a number",
			"extension.between" => "Extension should be between 0 and 4",
			"menu.required" => "A menu is required",
			"menu.array" => "Malformed menu data, please resubmit",
			"menu.min" => "At least 1 menu should be selected",
			"menu.*.required" => "Menu is required",
			"menu.*.numeric" => "Please refrain from modifying the form",
			"menu.*.exists" => "Please refrain from modifying the form",
			"count.required" => "An order count for the menu is required",
			"count.array" => "Malformed order count data, please resubmit",
			"count.min" => "At least 1 order count should be provided",
			"count.*.required" => "Amount of order is required",
			"count.*.numeric" => "Amount of order should be a number",
			"count.*.between" => "Amount of order should be between 1 and {$storeCap}"
		];

		$validator = Validator::make($req->all(), $validationRules, $validationMsg);

		// Calculates how many minutes will be added to the booking (will be used using the addMinutes)
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
		$closing = Carbon::parse("{$booking->reserved_at} {$closingTime}");
		$start_at = Carbon::parse("{$booking->reserved_at} {$booking->start_at}");
		$end_at = Carbon::parse("{$booking->reserved_at} {$booking->end_at}")
			->addHours($hoursToAdd)->addMinutes($minutesToAdd);

		// If the additional time from the menu is greater than the closing, then set the end time to the closing.
		if ($end_at->gt($closing))
			$end_at = $closing;

		$validator->after(function ($validator) use ($req, $storeCap, $start_at, $end_at, $closing) {
			// Checks whether the booking exceeded the closing time
			if ($end_at->gt($closing)) {
				$toSubtract = $end_at->diffInMinutes($closing) / 60;

				$validator->errors()->add(
					"extension",
					"Extension made the booking exceed closing time. Remove {$toSubtract} " . Str::plural('hour', $toSubtract)
				);
			}
		});

		return [
			'validator' => $validator
		];
	}

	public static function showRoute($id) {
		$additionalOrder = AdditionalOrder::withTrashed()->find($id);

		if ($additionalOrder == null)
			return "javascript:SwalFlash.info(`Cannot Find Item`, `Item may already be deleted or an anonymous user.`, true, false, `center`, false);";
		return route('admin.bookings.additional-orders.show', [$additionalOrder->booking_id, $id]);
	}
}