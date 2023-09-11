<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'name'
	];

	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'deleted_at' => 'datetime',
	];

	// Relationships
	public function users() { return $this->hasMany('App\User'); }
	public function permissions() { return $this->belongsToMany('App\Permission', 'type_permissions'); }

	// Custom Functions
	public function hasPermission(...$permissions) {
		$matches = 0;

		foreach ($permissions as $t) {
			foreach ($this->permissions as $h) {
				if ($t == $h->slug) {
					$matches += 1;
				}
			}
		}

		return $matches == count($permissions);
	}

	// STATIC FUNCTIONS
	public static function showRoute($id) {
		$type = Type::withTrashed()->find($id);

		if ($type == null)
			return "javascript:SwalFlash.info(`Cannot Find Item`, `Item may already be deleted or an anonymous user.`, true, false, `center`, false);";
		return route('admin.types.show', [$id]);
	}
}