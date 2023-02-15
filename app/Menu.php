<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use DB;
use Exception;
use Log;
use NumberFormatter;

class Menu extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'name',
		'price',
		'duration'
	];
	
	protected $casts = [
		'created_at' => 'datetime: M d, Y h:i A',
		'updated_at' => 'datetime: M d, Y h:i A',
		'deleted_at' => 'datetime: M d, Y h:i A',
		'duration' => 'datetime: H:i'
	];

	// Accessor
	public function getDurationAttribute($value) {
		return Carbon::createFromFormat('H:i:s', $value)->format("H:i");
	}

	// Relationships
	public function items() { return $this->belongsToMany('App\Inventory', 'menu_items', 'menu_id', 'inventory_id'); }
	public function menuItems() { return $this->hasMany('App\MenuItem', 'menu_id', 'id'); }
	public function bookings() { return $this->morphedByMany('App\Booking', 'boooking_menus'); }
	public function additionalOrders() { return $this->morphedByMany('App\AdditionalOrder', 'boooking_menus'); }

	// Custom Functions
	public function getPrice() {
		$locale = app()->currentLocale();
		return (new NumberFormatter("{$locale}@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . number_format($this->price, 2);
	}

	public function getFromDuration($format = "H:i") {
		return Carbon::parse($this->duration)->format($format);
	}

	public function reduceInventory() {
		$notReduced = [];
		$response = json_decode(json_encode([
			"success" => true,
			"message" => "Successfully reduced {$this->name}'s menu items",
			"notReduced" => []
		]));

		try {
			DB::beginTransaction();

			foreach($this->items as $i) {
				if ($i->trashed()) {
					array_push($notReduced, $i->item_name);
					continue;
				}

				$i->quantity = $i->quantity - $this->menuItems()->where('inventory_id', '=', $i->id)->first()->amount;
				$i->save();
			}
			
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

	public function returnInventory() {
		$notReturned = [];
		$response = json_decode(json_encode([
			"success" => true,
			"message" => "Successfully returned {$this->name}'s menu items",
			"notReturned" => []
		]));

		try {
			DB::beginTransaction();

			foreach($this->items as $i) {
				if ($i->trashed()) {
					array_push($notReturned, $i->item_name);
					continue;
				}

				$i->quantity = $i->quantity + $this->menuItems()->where('inventory_id', '=', $i->id)->first()->amount;
				$i->save();
			}
			
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
}