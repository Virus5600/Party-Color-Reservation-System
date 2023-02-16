<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Jobs\AccountNotification;

use App\PasswordReset;
use App\Permission;
use App\Type;
use App\TypePermission;
use App\User;
use App\UserPermission;
use App\ActivityLog;

use Auth;
use DB;
use Exception;
use File;
use Hash;
use Location;
use Log;
use Mail;
use Session;
use Validator;

class UserController extends Controller
{
	// LOGIN AND AUTHENTICATION RELATED START
	protected function redirectLogin() {
		return redirect()->route('login');
	}

	protected function login() {
		if (!Auth::check())
			return view('admin.login');
		else
			return redirect()->route('admin.dashboard');
	}

	protected function authenticate(Request $req) {
		$user = User::where('email', '=', $req->email)->first();

		if ($user == null)
			return redirect()
				->back()
				->with('flash_error', 'Wrong email/password!')
				->withInput(array('email' => $req->email));

		$authenticated = false;
		if (!$user->locked) {
			$authenticated = Auth::attempt([
				'email' => $req->email,
				'password' => $req->password
			]);
		}
		
		if ($authenticated) {
			if ($user) {
				try {
					DB::beginTransaction();
			
					$user->login_attempts = 0;
					$user->last_auth = Carbon::now()->timezone('Asia/Manila');
					$user->save();
			
					DB::commit();
				} catch (Exception $e) {
					DB::rollback();
					Log::error($e);
				}
			}

			return redirect()
				->intended(route('admin.dashboard'))
				->with('flash_success', 'Logged In!');
		}
		else {
			$msg = "";
			if ($user) {
				try {
					DB::beginTransaction();

					if ($user->login_attempts < 5) {
						$user->login_attempts = $user->login_attempts + 1;
						$msg = 'Wrong email/password!';
					}
					else {
						if ($user->locked == 0) {
							// Generates a password reset link if there are no other instances of this email in this table.
							if (PasswordReset::where('email', '=', $user->email)->get()->count() <= 0) {
								PasswordReset::insert([
									'email' => $user->email,
									'created_at' => now()->timezone('Asia/Manila')
								]);
								$pr = PasswordReset::where('email', '=', $user->email)->first();
								$pr->generateToken()->generateExpiration();

								ActivityLog::log(
									"Locked account of {$user->email} after 5 incorrect attempts",
									$user->id,
									"User",
									null,
									true
								);
							}
							// Otherwise, fetch the row to use the already generated data
							else {
								$pr = PasswordReset::where('email', '=', $user->email)->first();

								ActivityLog::log(
									"Login attempt to {$user->email} after lockout",
									$user->id,
									"User",
									null,
									true
								);
							}
							
							$args = [
								"subject" => "Your account has been locked!",
								"token" => $pr->token,
								"recipients" => [$user->email]
							];
							AccountNotification::dispatch($user, "locked", $args);
						}

						$user->locked = 1;
						$user->locked_by = User::getIP();
						$msg = 'Exceeded 5 tries, account locked';
					}
					$user->save();

					ActivityLog::log(
						"Login attempted for {$user->email}.",
						$user->id,
						"User"
					);
					
					DB::commit();
				} catch (Exception $e) {
					DB::rollback();
					Log::error($e);
				}
			}

			auth()->logout();
			return redirect()
				->back()
				->with('flash_error', $msg)
				->withInput(array('email' => $req->email));
		}
	}

	protected function logout() {
		if (Auth::check()) {
			$auth = Auth::user();
			auth()->logout();
			Session::flush();

			ActivityLog::log(
				"User {$auth->email} logout",
				$auth->id,
				"User",
				$auth->id,
				false
			);

			return redirect(route('home'))->with('flash_success', 'Logged out!');
		}
		return redirect()->route('admin.dashboard')->with('flash_error', 'Something went wrong, please try again.');
	}

	// PAGES
	protected function index(Request $req) {
		$users = User::withTrashed()->get();

		return view('admin.users.index', [
			'users' => $users
		]);
	}

	protected function create() {
		$types = Type::get();
		$password = str_shuffle(Str::random(25) . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT));

		return view('admin.users.create', [
			'types' => $types,
			'password' => $password
		]);
	}

	protected function store(Request $req) {
		$validator = Validator::make($req->all(), [
			'first_name' => array('required', 'string', 'max:255'),
			'middle_name' => array('string', 'max:255', 'nullable'),
			'last_name' => array('required', 'string', 'max:255'),
			'email' => 'required|email|string|max:255',
			'type' => 'required|numeric|exists:types,id',
			'password' => array('required', 'string', 'min:8', 'max:255', 'regex:/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]*$/'),
			'avatar' => 'max:5120|mimes:jpeg,jpg,png,webp|nullable',
		], [
			'first_name.required' => 'The first or given name is required',
			'first_name.string' => 'First names are only composed of string',
			'first_name.max' => 'First names must not exceed 255 characters',
			'middle_name.string' => 'Middle names are only composed of string',
			'middle_name.max' => 'Middle names must not exceed 255 characters',
			'last_name.required' => 'The last name is required',
			'last_name.string' => 'Last names are only composed of string',
			'last_name.max' => 'Last names must not exceed 255 characters',
			'email.required' => 'The email for this user is required',
			'email.email' => 'Invalid email address',
			'email.string' => 'Inavlid email address',
			'email.max' => 'Emails must not exceed 255 characters',
			'type.required' => 'Please select the department where the user works under',
			'type.numeric' => 'Please refrain from modifying the form',
			'type.exists' => 'Unknown department',
			'password.required' => 'Password is required',
			'password.string' => 'Password should be a string of characters',
			'password.min' => 'A minimum of 8 characters is the allowed limit for passwords',
			'password.max' => 'A maximum of 255 characters is the allowed limit for passwords',
			'avatar.max' => 'Image should be below 5MB',
			'avatar.mimes' => 'Selected file doesn\'t match the allowed image formats',
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			// FILE HANDLING
			// If a new avatar is coming
			$avatar = 'default.png';
			if ($req->exists('avatar')) {
				// Store the image
				$destination = 'uploads/users';
				$fileType = $req->file('avatar')->getClientOriginalExtension();
				$avatar = $req->first_name . '-' . $req->last_name . "-DP-" . uniqid() . "." . $fileType;
				$req->file('avatar')->move($destination, $avatar);

				// Save the file name to the table
				$avatar = $avatar;
			}

			$user = User::create([
				'first_name' => $req->first_name,
				'middle_name' => $req->middle_name,
				'last_name' => $req->last_name,
				'suffix' => $req->suffix,
				'is_avatar_link' => $req->is_avatar_link ? 1 : 0,
				'avatar' => $avatar,
				'email' => $req->email,
				'password' => Hash::make($req->password),
				'type_id' => $req->type
			]);

			// MAILING
			$reqArgs = $req->except('avatar');
			$args = [
				'subject' => 'Account Created',
				'req' => $reqArgs,
				'email' => $req->email,
				'recipients' => [$req->email, Auth::user()->email]
			];
			AccountNotification::dispatch($user, "creation", $args)->afterCommit();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"User '" . trim($user->getName()) . "' created under the email of '{$user->email}' as {$user->type->name}.",
			$user->id,
			"User",
			Auth::user()->id,
			false
		);

		return redirect()
			->route('admin.users.index')
			->with('flash_success', 'Successfully added "' . trim($user->getName()) . '"');
	}

	protected function edit($id) {
		$user = User::withTrashed()->find($id);
		$types = Type::get();

		if ($user == null) {
			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'The account either does not exists or is already deleted.');
		}

		return view('admin.users.edit', [
			'user' => $user,
			'types' => $types
		]);
	}

	protected function update(Request $req, $id) {
		$user = User::withTrashed()->find($id);

		if ($user == null) {
			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'User either does not exists or is already deleted.');
		}

		$validator = Validator::make($req->all(), [
			'first_name' => array('required', 'string', 'max:255'),
			'middle_name' => array('string', 'max:255', 'nullable'),
			'last_name' => array('required', 'string', 'max:255'),
			'email' => 'required|email|string|max:255',
			'type' => 'required|numeric|exists:types,id',
			'avatar' => 'max:5120|mimes:jpeg,jpg,png,webp|nullable',
		], [
			'first_name.required' => 'The first or given name is required',
			'first_name.string' => 'First names are only composed of string',
			'first_name.max' => 'First names must not exceed 255 characters',
			'middle_name.string' => 'Middle names are only composed of string',
			'middle_name.max' => 'Middle names must not exceed 255 characters',
			'last_name.required' => 'The last name is required',
			'last_name.string' => 'Last names are only composed of string',
			'last_name.max' => 'Last names must not exceed 255 characters',
			'email.required' => 'The email for this user is required',
			'email.email' => 'Invalid email address',
			'email.string' => 'Inavlid email address',
			'email.max' => 'Emails must not exceed 255 characters',
			'type.required' => 'Please select the department where the user works under',
			'type.numeric' => 'Please refrain from modifying the form',
			'type.exists' => 'Unknown department',
			'avatar.max' => 'Image should be below 5MB',
			'avatar.mimes' => 'Selected file doesn\'t match the allowed image formats',
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			// FILE HANDLING
			// If a new avatar is coming
			if ($req->exists('avatar')) {
				// If the user uses a custom avatar
				if ($user->avatar != 'default.png')
					File::delete(public_path() . '/uploads/users/' . $user->avatar);

				// If the new avatar is a file, proceed with the usual storing of files
				$avatar = null;
				$destination = 'uploads/users';
				$fileType = $req->file('avatar')->getClientOriginalExtension();
				$avatar = $req->first_name . '-' . $req->last_name . "-DP-" . uniqid() . "." . $fileType;
				$req->file('avatar')->move($destination, $avatar);

				// Save the file name to the table
				$user->avatar = $avatar;
			}

			$user->first_name = $req->first_name;
			$user->middle_name = $req->middle_name;
			$user->last_name = $req->last_name;
			$user->suffix = $req->suffix;
			$user->email = $req->email;
			$user->type_id = $req->type;
			$user->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"User '{$user->email}' updated.",
			$user->id,
			"User",
			Auth::user()->id
		);

		return redirect()
			->route('admin.users.index')
			->with('flash_success', 'Successfully updated "' . trim($user->getName()) . '"');
	}

	protected function changePassword(Request $req, $id) {
		$user = User::withTrashed()->find($id);

		if ($user == null) {
			return response()
				->json([
					'type' => 'missing',
					'message' => 'User either does not exists or is already deleted.'
				]);
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
			return response()
				->json([
					'type' => 'validation_error',
					'message' => $validator->messages()->first()
				]);
		}

		try {
			DB::beginTransaction();

			$user->password = Hash::make($req->password);

			$args = [
				'subject' => 'Password Changed',
				'recipients' => [$user->email],
				'email' => $user->email,
				'password' => $req->password,
				'type' => 'admin-change'
			];

			// Uses past-tense due to password is now changed
			AccountNotification::dispatch($user, "changed-password", $args);

			$user->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'type' => 'error',
					'message' => 'Something went wrong, please try again later.',
				]);
		}

		ActivityLog::log(
			"User '{$user->email}' changed password.",
			$user->id,
			"User",
			Auth::user()->id
		);

		return response()
			->json([
				'type' => 'success',
				'message' => 'Succesfully updated ' . $user->getName()
			]);
	}

	protected function managePermissions(Request $req, $id) {
		$user = User::withTrashed()->find($id);
		$permissions = Permission::get();
		$from = $req->from ? $req->from : route('admin.users.index');

		if ($user == null) {
			return redirect()
				->to($from)
				->with('flash_error', 'The account either does not exists or is already deleted.');
		}

		return view('admin.users.manage-permissions', [
			'user' => $user,
			'permissions' => $permissions,
			'from' => $from
		]);
	}

	protected function revertPermissions(Request $req, $id) {
		$user = User::withTrashed()->find($id);
		$permissions = Permission::get();
		$from = $req->from ? $req->from : false;

		if ($user == null) {
			return redirect()
				->to($from)
				->with('flash_error', 'The account either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();

			DB::table('user_permissions')
				->where('user_id', '=', $user->id)
				->delete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.manage-permissions', [$user->id, 'from' => $from ? $from : null])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"User '{$user->email}' reverted back to type permissions.",
			$user->id,
			"User",
			Auth::user()->id
		);

		return redirect()
			->route('admin.users.manage-permissions', [$user->id, 'from' => $from ? $from : null])
			->with('flash_success', 'Successfully reverted back to type permissions');
	}

	protected function updatePermissions(Request $req, $id) {
		$user = User::withTrashed()->find($id);
		$from = $req->from ? $req->from : route('admin.users.index');

		if ($user == null) {
			return redirect()
				->to($from)
				->with('flash_error', 'The account either does not exists or is already deleted.');
		}

		$validator = Validator::make($req->all(), [
			'permissions' => 'array',
			'permissions.*' => 'exists:permissions,slug|nullable'
		], [
			'permissions.array' => 'Please refrain from modifying the form',
			'permissions.*.exists' => 'The permission does not exists '
		]);

		try {
			DB::beginTransaction();

			$userPerms = UserPermission::where('user_id', '=', $user->id)->get();
			$typePerms = $user->type->permissions;
			$userPermsID = array();

			foreach ($userPerms as $up)
				array_push($userPermsID, $up->permission_id);
			$userPerms = Permission::whereIn('id', $userPermsID)->get();
			
			// If there are are still permissions...
			if ($req->permissions != null) {
				// Store the list of permissions from the request and all department permissions.
				$selectedPerms = array();
				$userPermis = array();
				$typesPerms = array();

				foreach ($req->permissions as $sp)
					array_push($selectedPerms, $sp);

				foreach ($userPerms as $up)
					array_push($userPermis, $up->slug);

				foreach ($typePerms as $dp)
					array_push($typesPerms, $dp->slug);

				// Sort them...
				sort($selectedPerms);
				sort($userPermis);
				sort($typesPerms);

				// If the permission from the request is exactly the same as the department permissions...
				if ($selectedPerms === $typesPerms) {
					// Remove all user permission so that the default (department permissions) will be used.
					DB::table('user_permissions')
						->where('user_id', '=', $user->id)
						->delete();
				}
				// Otherwise...
				else {
					// Remove all permissions that are in the use permission but not in the request...
					foreach ($userPerms as $up) {
						if (!in_array($up->slug, $selectedPerms)) {
							DB::table('user_permissions')
								->where('user_id', '=', $user->id)
								->where('permission_id', '=', $up->id)
								->delete();
						}
					}

					// ...Then add all those that aren't in the user permission yet
					foreach ($selectedPerms as $sp) {
						if (!in_array($sp, $userPermis) && !UserPermission::isDuplicatePermission(Permission::where('slug', '=', $sp)->first(), $user->id)) {
							UserPermission::insert([
								'user_id' => $user->id,
								'permission_id' => Permission::where('slug', '=', $sp)->first()->id
							]);
						}
					}
				}
			}
			// If all user permissions is remove...
			else {
				// Remove all instances of user permission for this user
				DB::table('user_permissions')
					->where('user_id', '=', $user->id)
					->delete();
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->to($from)
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"User '{$user->email}' updated permissions.",
			$user->id,
			"User",
			Auth::user()->id
		);

		return redirect()
			->to($from)
			->with('flash_success', 'Successfully updated ' . trim($user->getName()) . '\'s permissions');
	}

	protected function delete(Request $req, $id) {
		$user = User::find($id);

		if ($user == null) {
			return redirect()
				->route('admin.user.index')
				->with('flash_error', 'The user either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();			
			$user->delete();
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.user.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"User '{$user->email}' deactivated account.",
			$user->id,
			"User",
			Auth::user()->id
		);

		return redirect()
			->back()
			->with('flash_success', 'Successfully deactivated account.');
	}

	protected function restore(Request $req, $id) {
		$user = User::withTrashed()->find($id);

		if ($user == null) {
			return redirect()
				->route('admin.user.index')
				->with('flash_error', 'The account either does not exists or is already deleted permanently.');
		}
		else if (!$user->trashed()) {
			return redirect()
				->back()
				->with('flash_error', 'The account is already activated.');
		}

		try {
			DB::beginTransaction();
			$user->restore();
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.user.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"User '{$user->email()}' reactivated account.",
			$user->id,
			"User",
			Auth::user()->id
		);

		return redirect()
			->back()
			->with('flash_success', 'Successfully re-activated account.');
	}

	protected function permaDelete(Request $req, $id) {
		$user = User::withTrashed()->find($id);

		if ($user == null) {
			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'The account either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();

			if ($user->avatar != 'default.png')
					File::delete(public_path() . '/uploads/users/' . $user->avatar);
			
			$email = $user->email;

			$user->forceDelete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"User '{$user->email}' permanently deleted.",
			null,
			"User",
			Auth::user()->id,
			false
		);

		ActivityLog::itemDeleted($id);

		return redirect()
			->route('admin.users.index')
			->with('flash_success', 'Successfully removed the user permanently');
	}
}