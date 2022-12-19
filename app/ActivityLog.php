<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'email',
		'address',
		'action',
		'is_marked',
		'reason',
	];

	// Relationship
	protected function user() { return $this->belongsTo('App\User'); }

	// Custom Functions
	public function userF() {
		if ($this->isValidUser())
			return $this->user;
		else
			return null;
	}

	public static function log($action, $user_id = null) {
		if ($user_id == null && Auth::check())
			$user_id = Auth::user()->id;
		else if ($user_id == null && !Auth::check())
			$user_id = 0;

		ActivityLog::create([
			'user_id' => $user_id,
			'address' => Request::ip(),
			'action' => $action
		]);
	}

	public static function userDeleted($id, $lastEmail) {
		$logs = ActivityLog::where('user_id', '=', $id)->get();
		if ($logs->count() > 0) {
			try {
				DB::beginTransaction();

				foreach ($logs as $l) {
					$l->user_id = 0;
					$l->email = $lastEmail;
					$l->save();
				}

				DB::commit();
			} catch (Exception $e) {
				DB::rollback();
				Log::error($e);
			}
		}
	}

	public function getUser() {
		if ($this->user_id == 0 && $this->email == null) {
			return 'guest';
		}
		else {
			if ($this->userF() != null) {
				return $this->userF()->email;
			}
			else {
				return $this->email;
			}
		}
	}

	public function isValidUser() {
		if ($this->user_id > 0)
			return true;
		return false;
	}
}