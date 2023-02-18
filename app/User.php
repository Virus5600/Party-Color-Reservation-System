<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use Notifiable, SoftDeletes, HasApiTokens;

	protected $fillable = [
		'first_name',
		'middle_name',
		'last_name',
		'suffix',
		'email',
		'avatar',
		'type_id',
		'login_attempts',
		'locked',
		'locked_by',
		'password',
		'last_auth',
	];

	protected $hidden = [
		'password',
		'remember_token',
	];

	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'deleted_at' => 'datetime',
		'last_auth' => 'datetime',
	];

	// Relationships
	protected function announcements() { return $this->hasMany('App\Announcement'); }
	protected function type() { return $this->belongsTo('App\Type'); }
	protected function passwordReset() { return $this->belongsTo('App\PasswordReset', 'email', 'email'); }

	// Custom Function
	public function permissions() {
		$perms = UserPermission::where('user_id', '=', $this->id)->get();
		if ($perms->count() <= 0)
			$perms = $this->type->permissions;

		return $perms;
	}

	public function isUsingTypePermissions() {
		return UserPermission::where('user_id', '=', $this->id)->get()->count() <= 0;
	}

	public function hasPermission(...$permissions) {
		$matches = 0;
		$usingTypePermissions = $this->isUsingTypePermissions();
		$perms = $this->permissions();

		foreach ($perms as $p) {
			if ($usingTypePermissions) {
				if (in_array($p->slug, $permissions)) {
					$matches += 1;
				}
			}
			else {
				if (in_array($p->permission->slug, $permissions)) {
					$matches += 1;
				}
			}
		}

		return $matches == count($permissions);
	}

	public function hasSomePermission(...$permissions) {
		$usingTypePermissions = $this->isUsingTypePermissions();
		$perms = $this->permissions();

		foreach ($perms as $p) {
			if ($usingTypePermissions) {
				if (in_array($p->slug, $permissions)) {
					return true;
				}
			}
			else {
				if (in_array($p->permission->slug, $permissions)) {
					return true;
				}
			}
		}
	}

	public function getAvatar($useDefault=false, $getFull=true) {
		$avatarF = $this->avatar;
		$avatarU = asset('/uploads/users/'.$this->avatar);
		$avatarD = asset('/uploads/users/default.png');
		$toRet = null;

		if ($useDefault) {
			if ($getFull)
				return $avatarD;
			else
				return 'default.png';
		}
		else {
			if ($getFull) {
				$toRet = $avatarU;
			}
			else {
				$toRet = $avatarF;
			}
		}

		return $toRet;
	}

	public function getName($include_middle = false) {
		return $this->first_name . ($include_middle ? (' ' . $this->middle_name . ' ') : ' ') . $this->last_name;
	}

	// Static Functions
	public static function getIP() {
		$ip = request()->ip();

		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$ip = $_SERVER['REMOTE_ADDR'];

		return $ip;
	}
}