<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use DB;
use Exception;
use Log;

class Menu extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'name',
	];
	
	protected $casts = [
		'created_at' => 'datetime: M d, Y h:i A',
		'updated_at' => 'datetime: M d, Y h:i A',
		'deleted_at' => 'datetime: M d, Y h:i A',
	];

	protected $with = [
		'menuVariations'
	];

	// Booted
	protected static function booted() {
		static::retrieved(function($menu) {
			try {
				DB::beginTransaction();

				// Deletes the menu only if its inactive for a year or more, and if all its variation is inactive.
				if (now()->gte($menu->deleted_at) && count($menu->menuVariations) <= 0) {

					activity('menu')
						->byAnonymous()
						->on($menu)
						->event('delete')
						->withProperties([
							'name' => $menu->name
						])
						->log("Menu {$menu->name} removed permanently after being inactive for more than an entire year.");
					
					$menu->forceDelete();
				}

				DB::commit();
			} catch (Exception $e) {
				DB::rollback();
				Log::error($e);
			}
		});
	}

	// Relationships
	public function menuVariations() { return $this->hasMany('App\MenuVariation', 'menu_id', 'id'); }

	// STATIC FUNCTIONS
	public static function showRoute($id) {
		return route('admin.menu.variation.index', [$id]);
	}
}