<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	protected $fillable = [
		'parent_permission',
		'name',
		'slug'
	];

	// Relationships
	public function types() { return $this->belongsToMany('App\Type', 'type_permissions'); }
	public function users() { return $this->belongsToMany('App\User', 'user_permissions'); }

	// Custom Functions
	public function childPermissions() {
		return Permission::where('parent_permission', '=', $this->id)->get();
	}

	public function parentPermission() {
		return Permission::where('id', '=', $this->parent_permission)->first();
	}

	public function allUsers() {
		$typeIds = array();
		foreach ($this->types as $d)
			array_push($typeIds, $d->id);

		$userIds = array();
		foreach ($this->users as $u)
			array_push($userIds, $u->id);

		$users = User::whereIn('type_id', $typeIds)
			->whereIn('id', $userIds, 'or');

		return $users->get();
	}

	// STATIC FUNCTIONS
	public static function showRoute($id) {
		return route('admin.permissions.show', [Permission::find($id)->slug]);
	}
}