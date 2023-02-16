<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Jobs\AccountNotification;

use App\PasswordReset;
use App\User;
use App\ActivityLog;

use DB;
use Exception;
use Hash;
use Log;
use Validator;

class PasswordResetController extends Controller
{
	protected function index(Request $req) {
		return view('change-password.index', [
			'email' => $req->e
		]);
	}

	protected function submit(Request $req) {
		$validator = Validator::make($req->all(), [
			'email' => 'required|email|exists:users,email|max:255'
		], [
			'email.required' => 'Please provide the registered email',
			'email.email' => 'Please provide a proper email address',
			'email.exists' => 'Account does not exists',
			'email.max' => 'Please provide a proper email address',
		]);

		if ($validator->fails())
			return redirect()
				->back()
				->withErrors($validator)
				->withInput($req);

		try {
			DB::beginTransaction();

			$user = User::where('email', '=', $req->email)->first();
			$pr = PasswordReset::where('email', '=', $user->email)->first();

			if ($pr == null) {
				PasswordReset::insert([
					'email' => $user->email,
					'created_at' => now()->timezone('Asia/Manila')
				]);

				$pr = PasswordReset::where('email', '=', $user->email)->first();
				$pr->generateToken()->generateExpiration();
			}

			$args = [
				'subject' => 'Password Reset Request',
				'recipients' => [$user->email],
				'token' => $pr->token
			];

			AccountNotification::dispatch($user, "change-password", $args);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->back()
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Password for '{$req->email}' reset requested.",
			null,
			"PasswordReset"
		);

		return redirect()
			->route('login')
			->with('flash_success', 'Successfully requested a password change')
			->with('timer', true)
			->with('duration', '10000');
	}

	protected function edit(Request $req, $token) {
		$pr = PasswordReset::where('token', '=', $token)->first();

		if ($pr == null)
			return redirect()
				->route('login')
				->with('flash_info', 'No request has been put for this token')
				->with('has_timer', true)
				->with('duration', '5000');

		if ($pr->isExpired())
			return redirect()
				->route('login')
				->with('flash_info', 'Request already expired, please renew request via the "Forgot Password" below')
				->with('has_timer', true)
				->with('duration', '10000');

		return view('change-password.edit', [
			'token' => $token
		]);
	}

	protected function update(Request $req) {
		$pr = PasswordReset::where('token', '=', $req->token)->first();
		$user = $pr->user;

		if ($user == null) {
			return redirect()
				->route('login')
				->with('flash_error', 'User either does not exists or is already deleted');
		}

		$validator = Validator::make($req->all(), [
			'password' => array('required', 'regex:/([a-z]*)([0-9])*/i', 'min:8', 'confirmed'),
			'password_confirmation' => 'required'
		], [
			'password.required' => 'The new password is required',
			'password.regex' => 'Password must contain at least 1 letter and 1 number',
			'password.min' => 'Password should be at least 8 characters',
			'password.confirmed' => 'You must confirm your password first',
			'password_confirmation.required' => 'You must confirm your password first'
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator);
		}

		try {
			DB::beginTransaction();

			$user->password = Hash::make($req->password);
			$user->login_attempts = 0;
			$user->locked = 0;
			$user->locked_by = null;

			$args = [
				'subject' => 'Password Changed',
				'recipients' => [$user->email],
				'email' => $user->email,
				'password' => $req->password
			];

			// Uses past-tense due to password is now changed
			AccountNotification::dispatch($user, "changed-password", $args);

			$user->save();
			$pr->delete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->back()
				->with('flash_error', 'Something went wrong, please try again later.');
		}

		ActivityLog::log(
			"Password for '{$user->email}' updated.",
			null,
			"PasswordReset"
		);

		return redirect()
			->route('login')
			->with('flash_success', "Succesfully updated password");
	}
}