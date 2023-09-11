<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use DB;
use Exception;
use Log;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

	protected $fillable = [
		'item_name',
		'quantity',
		'measurement_unit',
		'critical_level',
	];

    protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'deleted_at' => 'datetime',
	];

	// Booted
	protected static function booted() {
		static::retrieved(function($inventory) {
			try {
				DB::beginTransaction();

				if ($inventory->quantity <= 0) {
					$id = $inventory->id;
					$name = $inventory->name;
					$mu = $inventory->measurement_unit;

					$inventory->delete();

					activity('inventory')
						->byAnonymous()
						->on($inventory)
						->event('inactive')
						->withProperties([
							'item_name' => $inventory->item_name,
							'quantity' => $inventory->quantity,
							'measurement_unit' => $inventory->measurement_unit,
							'critical_level' => $inventory->critical_level
						])
						->log("Item {$name} set to inactive after stock has reached less than or equals to 0{$mu}.");
				}

				DB::commit();
			} catch (Exception $e) {
				DB::rollback();
				Log::error($e);
			}
		});
	}

	// Relationships
	public function menus() { return $this->belongsToMany('App\MenuVariation', 'menu_variation_items', 'inventory_id', 'menu_variation_id'); }
	public function variationItem() { return $this->belongsTo('App\MenuVariationItem', 'id', 'inventory_id'); }

	// Custom Functions
	public function getInStock() {
		return number_format($this->quantity, 0, ',', ', ') . " {$this->measurement_unit}";
	}

	public function deletePermanently() {
		try {
			DB::beginTransaction();

			activity('inventory')
				->byAnonymous()
				->on($this)
				->event('deleted')
				->withProperties([
					'item_name' => $this->item_name,
					'quantity' => $this->quantity,
					'measurement_unit' => $this->measurement_unit,
					'critical_level' => $this->critical_level
				])
				->log("Item '{$this->item_name}' permanently deleted.");

			$this->forceDelete();
			$this->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);
		}
	}

	// STATIC FUNCTIONS
	public static function getForDeletion() {
		return Inventory::onlyTrashed()->whereDate('updated_at', '<', now()->subYears(5))->get();
	}

	public static function showRoute($id) {
		$inventory = Inventory::withTrashed()->find($id);
		
		if ($inventory == null)
			return "javascript:SwalFlash.info(`Cannot Find Item`, `Item may already be deleted or an anonymous user.`, true, false, `center`, false);";
		return route('admin.inventory.show', [$id]);
	}
}
