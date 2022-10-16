<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
	protected $fillable = [
		'user_id',
		'permission_id'
	];

	// Relationship Functions
	public function user() { return $this->belongsTo('App\User'); }
	public function permission() { return $this->belongsTo('App\Permission'); }

	// Custom Functions
	public function isDuplicate($permission =  null) {
		if ($permission === null)
			$permission = $this->id;

		return UserPermission::isDuplicatePermission($permission, $this->id);
	}

	public static function isDuplicatePermission($permission, $user) {
		// Permission checking
		if ($permission instanceof Permission) {
			$permission = $permission->id;
		}
		else if (gettype($permission) != 'integer') {
			\Log::warning('Inserted $permission not an ID nor an instance of Permission...');
			return false;
		}

		// User checking
		if ($user instanceof User) {
			$user = $user->id;
		}
		else if (gettype($user) != 'integer') {
			\Log::warning('Inserted $user not an ID nor an instance of Permission...');
			return false;
		}

		return UserPermission::where('user_id', '=', $user)
			->where('permission_id', '=', $permission)
			->first() != null;
	}
}