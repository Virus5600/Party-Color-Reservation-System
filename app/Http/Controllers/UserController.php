<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
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

use DB;
use Exception;
use File;
use Hash;
use Location;
use Log;
use Mail;
use Validator;

class UserController extends Controller
{
	// LOGIN AND AUTHENTICATION RELATED START
	protected function redirectLogin() {
		return redirect()->route('login');
	}

	protected function login() {
		if (!auth()->check())
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
			$authenticated = auth()->attempt([
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

					activity('user')
						->byAnonymous()
						->on($user)
						->event('login-success')
						->withProperties([
							'first_name' => $user->first_name,
							'middle_name' => $user->middle_name,
							'last_name' => $user->last_name,
							'suffix' => $user->suffix,
							'is_avatar_link' => $user->is_avatar_link,
							'avatar' => $user->avatar,
							'email' => $user->email,
							'type_id' => $user->type,
							'last_auth' => $user->last_auth
						])
						->log("User {$user->email} successfully logged in.");
			
					DB::commit();
				} catch (Exception $e) {
					DB::rollback();
					Log::error($e);
				}
			}
			$user->tokens()->delete();
			$token = $user->createToken('authenticated');
			session(["bearer" => $token->plainTextToken]);

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

								activity('user')
									->byAnonymous()
									->on($user)
									->event('login-attempt')
									->withProperties([
										'first_name' => $user->first_name,
										'middle_name' => $user->middle_name,
										'last_name' => $user->last_name,
										'suffix' => $user->suffix,
										'is_avatar_link' => $user->is_avatar_link,
										'avatar' => $user->avatar,
										'email' => $user->email,
										'type_id' => $user->type,
										'last_auth' => $user->last_auth
									])
									->log("Locked account of {$user->email} after 5 incorrect attempts");
							}
							// Otherwise, fetch the row to use the already generated data
							else {
								$pr = PasswordReset::where('email', '=', $user->email)->first();

								activity('user')
									->byAnonymous()
									->on($booking)
									->event('login-attempt')
									->withProperties([
										'first_name' => $user->first_name,
										'middle_name' => $user->middle_name,
										'last_name' => $user->last_name,
										'suffix' => $user->suffix,
										'is_avatar_link' => $user->is_avatar_link,
										'avatar' => $user->avatar,
										'email' => $user->email,
										'type_id' => $user->type,
										'last_auth' => $user->last_auth
									])
									->log("Login attempt to {$user->email} after lockout");
							}
							
							$args = [
								"subject" => "Your account has been locked!",
								"token" => $pr->token,
								"email" => $user->email,
								"recipients" => [$user->email]
							];
							AccountNotification::dispatch($user, "locked", $args);
						}

						$user->locked = 1;
						$user->locked_by = User::getIP();
						$msg = 'Exceeded 5 tries, account locked';
					}
					$user->save();

					activity('user')
						->byAnonymous()
						->on($user)
						->event('login-attempt')
						->withProperties([
							'first_name' => $user->first_name,
							'middle_name' => $user->middle_name,
							'last_name' => $user->last_name,
							'suffix' => $user->suffix,
							'is_avatar_link' => $user->is_avatar_link,
							'avatar' => $user->avatar,
							'email' => $user->email,
							'type_id' => $user->type,
							'last_auth' => $user->last_auth
						])
						->log("Login attempted for {$user->email}.");
					
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
		if (auth()->check()) {
			$auth = auth()->user();
			$auth->tokens()->delete();
			
			auth()->logout();
			session()->flush();

			// LOGGER
			activity('user')
				->by($auth)
				->on($auth)
				->event('logout')
				->withProperties([
					'first_name' => $auth->first_name,
					'middle_name' => $auth->middle_name,
					'last_name' => $auth->last_name,
					'suffix' => $auth->suffix,
					'is_avatar_link' => $auth->is_avatar_link,
					'avatar' => $auth->avatar,
					'email' => $auth->email,
					'type_id' => $auth->type,
					'last_auth' => $auth->last_auth
				])
				->log("User {$auth->email} logout");

			return redirect(route('home'))->with('flash_success', 'Logged out!');
		}
		return redirect()->route('admin.dashboard')->with('flash_error', 'Something went wrong, please try again.');
	}

	// PAGES
	protected function index(Request $req) {
		$search = "%" . request('search') . "%";

		$users = User::withTrashed()
			->leftJoin('types', 'users.type_id', '=', 'types.id')
			->where('first_name', 'LIKE', $search)
			->orWhere('middle_name', 'LIKE', $search)
			->orWhere('last_name', 'LIKE', $search)
			->orWhere('email', 'LIKE', $search)
			->orWhere('types.name', 'LIKE', $search)
			->select(['users.*'])
			->paginate(10);

		return view('admin.users.index', [
			'users' => $users
		]);
	}

	protected function create(Request $req) {
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
			'email' => 'required|email|string|max:255|unique:users,email',
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
			'email.unique' => 'Email already registered',
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
				'recipients' => [$req->email, auth()->user()->email]
			];
			AccountNotification::dispatch($user, "creation", $args)->afterCommit();

			// LOGGER
			activity('user')
				->by(auth()->user())
				->on($user)
				->event('create')
				->withProperties([
					'first_name' => $user->first_name,
					'middle_name' => $user->middle_name,
					'last_name' => $user->last_name,
					'suffix' => $user->suffix,
					'is_avatar_link' => $user->is_avatar_link,
					'avatar' => $user->avatar,
					'email' => $user->email,
					'type_id' => $user->type,
					'last_auth' => $user->last_auth
				])
				->log("User '" . trim($user->getName()) . "' created under the email of '{$user->email}' as {$user->type->name}.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.users.index')
			->with('flash_success', 'Successfully added "' . trim($user->getName()) . '"');
	}

	protected function show(Request $req, $id) {
		$user = User::withTrashed()->find($id);

		if ($user == null) {
			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'The account either does not exists or is already deleted.');
		}

		$format = [
			'first_name',
			'middle_name',
			'last_name',
			'suffix',
			'email',
			'last_auth',
		];

		return view('admin.users.show', [
			'user' => $user,
			'format' => $format
		]);
	}

	protected function edit(Request $req, $id) {
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
			'email' => array('required', 'email', 'string', 'max:255', Rule::unique('users', 'email')->ignore($user->id)),
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
			'email.unique' => 'Email already registered',
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

			// LOGGER
			activity('user')
				->by(auth()->user())
				->on($user)
				->event('update')
				->withProperties([
					'first_name' => $user->first_name,
					'middle_name' => $user->middle_name,
					'last_name' => $user->last_name,
					'suffix' => $user->suffix,
					'is_avatar_link' => $user->is_avatar_link,
					'avatar' => $user->avatar,
					'email' => $user->email,
					'type_id' => $user->type,
					'last_auth' => $user->last_auth
				])
				->log("User '{$user->email}' updated.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

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

			// LOGGER
			activity('user')
				->by(auth()->user())
				->on($user)
				->event('update')
				->withProperties([
					'first_name' => $user->first_name,
					'middle_name' => $user->middle_name,
					'last_name' => $user->last_name,
					'suffix' => $user->suffix,
					'is_avatar_link' => $user->is_avatar_link,
					'avatar' => $user->avatar,
					'email' => $user->email,
					'type_id' => $user->type,
					'last_auth' => $user->last_auth
				])
				->log("User '{$user->email}' changed password.");

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

			// LOGGER
			activity('user')
				->by(auth()->user())
				->on($user)
				->event('update')
				->withProperties([
					'first_name' => $user->first_name,
					'middle_name' => $user->middle_name,
					'last_name' => $user->last_name,
					'suffix' => $user->suffix,
					'is_avatar_link' => $user->is_avatar_link,
					'avatar' => $user->avatar,
					'email' => $user->email,
					'type_id' => $user->type,
					'last_auth' => $user->last_auth
				])
				->log("User '{$user->email}' reverted back to using their type permissions ({$user->type->name}).");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.manage-permissions', [$user->id, 'from' => $from ? $from : null])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.users.manage-permissions', [$user->id, 'from' => $from ? $from : null])
			->with('flash_success', 'Successfully reverted back to type permissions');
	}

	protected function updatePermissions(Request $req, $id) {
		$user = User::withTrashed()
			->with(['userPerm'])
			->find($id);
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

			$userPerms = ($user->userPerms == null ? array() : $user->userPerms->pluck(['id'])->toArray());
			$typePerms = ($user->type->permissions == null ? array() : $user->type->permissions->pluck(['id'])->toArray());

			sort($userPerms);
			sort($typePerms);

			if ($userPerms == $typePerms)
				$user->userPerms()->detach();
			else
				$user->userPerms()->sync($req->permissions);

			// LOGGER
			activity('user')
				->by(auth()->user())
				->on($user)
				->event('update')
				->withProperties([
					'first_name' => $user->first_name,
					'middle_name' => $user->middle_name,
					'last_name' => $user->last_name,
					'suffix' => $user->suffix,
					'is_avatar_link' => $user->is_avatar_link,
					'avatar' => $user->avatar,
					'email' => $user->email,
					'type_id' => $user->type,
					'last_auth' => $user->last_auth
				])
				->log("User '{$user->email}' permissions' updated.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->to($from)
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->to($from)
			->with('flash_success', 'Successfully updated ' . trim($user->getName()) . '\'s permissions');
	}

	protected function delete(Request $req, $id) {
		$user = User::find($id);

		if ($user == null) {
			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'The user either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();

			$user->delete();

			// LOGGER
			activity('user')
				->by(auth()->user())
				->on($user)
				->event('deactivate')
				->withProperties([
					'first_name' => $user->first_name,
					'middle_name' => $user->middle_name,
					'last_name' => $user->last_name,
					'suffix' => $user->suffix,
					'is_avatar_link' => $user->is_avatar_link,
					'avatar' => $user->avatar,
					'email' => $user->email,
					'type_id' => $user->type,
					'last_auth' => $user->last_auth
				])
				->log("User '{$user->email}' deactivated account.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.users.index')
			->with('flash_success', 'Successfully deactivated account.');
	}

	protected function restore(Request $req, $id) {
		$user = User::withTrashed()->find($id);

		if ($user == null) {
			return redirect()
				->route('admin.users.index')
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

			// LOGGER
			activity('user')
				->by(auth()->user())
				->on($user)
				->event('activate')
				->withProperties([
					'first_name' => $user->first_name,
					'middle_name' => $user->middle_name,
					'last_name' => $user->last_name,
					'suffix' => $user->suffix,
					'is_avatar_link' => $user->is_avatar_link,
					'avatar' => $user->avatar,
					'email' => $user->email,
					'type_id' => $user->type,
					'last_auth' => $user->last_auth
				])
				->log("User '{$user->getName()}' reactivated account.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.users.index')
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
			
			$first_name = $user->first_name;
			$middle_name = $user->middle_name;
			$last_name = $user->last_name;
			$suffix = $user->suffix;
			$is_avatar_link = $user->is_avatar_link;
			$avatar = $user->avatar;
			$email = $user->email;
			$type = $user->type;
			$last_auth = $user->last_auth;

			$user->forceDelete();

			activity('user')
				->by(auth()->user())
				->on($user)
				->event('logout')
				->withProperties([
					'first_name' => $first_name,
					'middle_name' => $middle_name,
					'last_name' => $last_name,
					'suffix' => $suffix,
					'is_avatar_link' => $is_avatar_link,
					'avatar' => $avatar,
					'email' => $email,
					'type_id' => $type,
					'last_auth' => $last_auth
				])
				->log("User '{$email}' permanently deleted.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.users.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.users.index')
			->with('flash_success', 'Successfully removed the user permanently');
	}
}