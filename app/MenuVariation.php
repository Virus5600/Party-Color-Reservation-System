<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use Carbon\Carbon;

use DB;
use Log;
use NumberFormatter;
use Validator;

class MenuVariation extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'menu_id',
		'name',
		'price',
		'duration'
	];

	protected $casts = [
		'deleted_at' => 'datetime: M d, Y h:i A',
		'duration' => 'datetime: H:i'
	];

	// Accessor
	public function getDurationAttribute($value) {
		return Carbon::createFromFormat('H:i:s', $value)->format("H:i");
	}

	// Relationships
	public function menu() { return $this->belongsTo('App\Menu', 'menu_id', 'id'); }
	public function items() { return $this->belongsToMany('App\Inventory', 'menu_variation_items', 'menu_variation_id', 'inventory_id')->withPivot('amount', 'is_unlimited'); }
	public function variationItems() { return $this->hasMany('App\MenuVariationItem', 'menu_variation_id', 'id'); }
	public function bookings() { return $this->morphedByMany('App\Booking', 'orderables'); }
	public function additionalOrders() { return $this->morphedByMany('App\AdditionalOrder', 'orderables'); }

	// Custom Functions
	public function getPrice() {
		$locale = app()->currentLocale();
		return (new NumberFormatter("{$locale}@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . number_format($this->price, 2);
	}

	public function getFromDuration($format = "H:i") {
		return Carbon::parse($this->duration)->format($format);
	}

	public function reduceInventory($count = 1, Booking $booking = null) {
		$notReduced = [];
		$response = json_decode(json_encode([
			"success" => true,
			"message" => "Successfully reduced {$this->menu->name} ({$this->name})'s menu items",
			"notReduced" => []
		]));

		try {
			DB::beginTransaction();

			foreach($this->items()->withTrashed()->get() as $i) {
				$variationItem = $this->variationItems()->where('inventory_id', '=', $i->id)->first();

				$i->quantity = $i->quantity - ($variationItem->amount * $count);
				$i->save();
			}

			// LOGGER
			activity('menu-variation')
				->by($booking)
				->on($this)
				->event('update')
				->withProperties([
					'menu_id' => $this->menu_id,
					'name' => $this->name,
					'price' => $this->price,
					'duration' => $this->duration
				])
				->log("Menu Variaton {$this->name} from {$this->menu->name} reduced its items.");
			
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			$response->success = false;
			$response->message = $e;
			return $response;
		}

		$response->notReduced = $notReduced;
		return $response;
	}

	public function returnInventory($count = 1, Booking $booking = null) {
		$notReturned = [];
		$response = json_decode(json_encode([
			"success" => true,
			"message" => "Successfully returned {$this->menu->name} ({$this->name})'s menu items",
			"notReturned" => []
		]));

		try {
			DB::beginTransaction();

			foreach($this->items()->withTrashed()->get() as $i) {
				$variationItem = $this->variationItems()->where('inventory_id', '=', $i->id)->first();

				$i->quantity = $i->quantity + ($variationItem->amount * $count);
				$i->save();

				if ($i->quantity > 0) {
					$i->restore();

					// LOGGER
					activity('inventory')
						->byAnonymous()
						->on($i)
						->event('update')
						->withProperties([
							'item_name' => $i->item_name,
							'quantity' => $i->quantity,
							'measurement_unit' => $i->measurement_unit,
							'critical_level' => $i->critical_level,
						])
						->log("Item {$i->item_name} set to active after stock has been returned.");
				}
			}

			// LOGGER
			activity('menu-variation')
				->by($booking)
				->on($this)
				->event('update')
				->withProperties([
					'menu_id' => $this->menu_id,
					'name' => $this->name,
					'price' => $this->price,
					'duration' => $this->duration
				])
				->log("Menu Variaton {$this->name} from {$this->menu->name} returned its items.");
			
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			$response->success = false;
			$response->message = $e;
			return $response;
		}

		$response->notReturned = $notReturned;
		return $response;
	}

	// STATIC FUNCTIONS
	// VALIDATION
	public static function validate(Request $req, $vid = null) {
		$variationItemValidation = [];
		$amountValidation = [];

		foreach ($req->variation_item as $k => $v) {
			$variationItemValidation["variation_item.{$k}"] = "required_unless:amount.{$k},null|numeric|exists:inventories,id";
			$amountValidation["amount.{$k}"] = "required_unless:variation_item.{$k},null|numeric" . ( $req->is_unlimited == 0 ? "|min:1" : "") . "|max:4294967295";
		}
		
		$uniqueRule = Rule::unique("menu_variations", "name");
		$validator = Validator::make($req->all(), array_merge([
			'variation_name' => ["required", "string", ($vid == null ? $uniqueRule : $uniqueRule->ignore($vid)), "max:255"],
			'variation_item' => 'array|nullable',
			'price' => 'required|numeric|min:0|max:4294967295',
			'amount' => 'array|nullable',
			'duration' => 'required|date_format:H:i|before_or_equal:12:00|after:00:00'
		], $variationItemValidation, $amountValidation), [
			'variation_name.required' => 'A variation name is required',
			'variation_name.string' => 'The variation name should be a string',
			'variation_name.string' => "\"{$req->variation_name}\" already exists",
			'variation_name.max' => 'Variation name should not exceed 255 characters',
			'variation_item.array' => 'Please refrain from modifying the page',
			'variation_item.nullable' => 'Please refrain from modifying the page',
			'price.required' => 'A price is required',
			'price.numeric' => 'The price should consists of numbers only',
			'price.min' => 'The minimum allowed price is ' . (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . '0.00 (Free)',
			'price.max' => 'The maximum allowed price is ' . (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . number_format(4294967295, 2),
			'amount.array' => 'Please refrain from modifying the page',
			'amount.nullable' => 'Please refrain from modifying the page',
			'duration.required' => 'Please set a duration for this menu',
			'duration.date_format' => 'Please refrain from modifying the page',
			'duration.before_or_equal' => 'Duration is capped at 12 hours',
			'duration.after' => 'Minimum duration is 1 minute',
			'variation_item.*.required_unless' => 'An item for this is required',
			'variation_item.*.numeric' => 'Please provide a proper item',
			'variation_item.*.exists' => 'The selected item does not exists',
			'amount.*.required_unless' => 'An amount of this item is required',
			'amount.*.numeric' => 'Please provide a proper amount',
			'amount.*.min' => 'Minimum amount is capped to 1',
			'amount.*.' => 'Maximum amount is capped to ' . number_format(4294967295),
		]);

		// Comporess the arrays
		$newIndex = [];
		$variationItem = [];
		$amount = [];
		$isUnlimited = [];
		for ($i = 0; $i < count($req->variation_item); $i++) {
			if ($req->variation_item[$i] || $req->amount[$i] || ($req->amount[$i] > 0) || $req->is_unlimited[$i]) {
				array_push($newIndex, $i);
				array_push($variationItem, $req->variation_item[$i]);
				array_push($amount, $req->amount[$i]);
				array_push($isUnlimited, $req->is_unlimited[$i]);
			}
		}

		// Replaces the old arrays with the new compressed one, adding their new indexes in the process as well to properly display validation messages
		$req->merge([
			'new_index' => $newIndex,
			'variation_item' => $variationItem,
			'amount' => $amount
		]);

		return [
			"validator" => $validator,
			"variationItem" => $variationItem,
			"amount" => $amount,
			"isUnlimited" => $isUnlimited
		];
	}

	public static function showRoute($vid) {
		return route('admin.menu.variation.show', [MenuVariation::find($vid)->menu_id, $vid]);
	}
}