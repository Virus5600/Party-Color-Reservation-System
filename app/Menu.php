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

	// Relationships
	public function menuVariations() { return $this->hasMany('App\MenuVariation', 'menu_id', 'id'); }

	// STATIC FUNCTIONS
	public static function getForDeletion() {
		return Menu::onlyTrashed()->whereDate('updated_at', '<', now()->subYears(5))->get();
	}

	public static function showRoute($id) {
		$menu = Menu::withTrashed()->find($id);

		if ($menu == null)
			return "javascript:SwalFlash.info(`Cannot Find Item`, `Item may already be deleted or an anonymous user.`, true, false, `center`, false);";
		return route('admin.menu.variation.index', [$id]);
	}
}