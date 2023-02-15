<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Auth;
use Request;

class ActivityLog extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'email',
		'address',
		'action',
		'is_automated',
		'is_marked',
		'reason',
		'model_type',
		'model_id'
	];

	// Relationship
	private function user() { return $this->belongsTo('App\User'); }

	// Morpher
	private function model() { return $this->belongsTo("App\{$this->model_type}", 'model_type', 'id'); }

	// Custom Functions
	public function userF() {
		if ($this->isValidUser())
			return $this->user;
		else
			return $this->email;
	}

	public function item(bool $asQuery = false) {
		$modelType = $this->model_type;
		$modelId = $this->model_id;

		if ($modelType == null || $modelId == null)
			return [];
		else
			return $asQuery ? $this->model() : $this->model;
	}

	public static function log($action, $model_id = null, $model_type = null, $user_id = null, $is_automated = false) {
		$email = null;
		if ($user_id == null && Auth::check()) {
			$user_id = Auth::user()->id;
			$email = Auth::user()->email;
		}
		else if ($user_id == null && !Auth::check()) {
			$user_id = 0;
		}
		else if ($user_id != null) {
			$user = User::find($user_id);
			$email = $user->email;
		}

		ActivityLog::create([
			'user_id' => $user_id,
			'address' => Request::ip(),
			'action' => $action,
			'email' => $email,
			'is_automated' => $is_automated ? 1 : 0,
			'model_id' => $model_id,
			'model_type' => $model_type
		]);
	}

	public static function itemDeleted($model_id) {
		$logs = ActivityLog::where('model_id', '=', $model_id)->get();

		if ($logs) {
			try {
				DB::beginTransaction();

				foreach ($logs as $log) {
					$log->model_id = null;
					$log->save();
				}

				DB::commit();
			} catch (Exception $e) {
				DB::rollback();
			}
		}
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