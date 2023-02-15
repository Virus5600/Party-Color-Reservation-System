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

				if ($inventory->quantity <= 0)
					$inventory->delete();

				DB::commit();
			} catch (Exception $e) {
				DB::rollback();
				Log::error($e);
			}
		});
	}

	// Relationships
	public function menus() { return $this->belongsToMany('App\Menu', 'menu_items', 'inventory_id', 'menu_id'); }
	public function menuItem() { return $this->belongsTo('App\MenuItem', 'id', 'inventory_id'); }

	// Custom Functions
	public function getInStock() {
		return number_format($this->quantity, 0, ',', ', ') . " {$this->measurement_unit}";
	}

	public function deletePermanently() {
		try {
			DB::beginTransaction();

			ActivityLog::log(
				"Item '{$this->item_name}' permanently deleted.",
				null,
				"Inventory",
				null,
				true
			);

			$this->forceDelete();
			$this->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);
		}
	}

	public static function getForDeletion() {
		return Inventory::withTrashed()->whereDate('updated_at', '<', now()->subYears(5))->get();
	}
}
