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
	protected function users() { return $this->hasMany('App\User'); }
	protected function permissions() { return $this->belongsToMany('App\Permission', 'type_permissions'); }

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
}