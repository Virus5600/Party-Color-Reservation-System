<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Permission;
use App\Type;
use App\TypePermission;
use App\User;
use App\UserPermission;

use Auth;
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
		if (!Auth::check())
			return view('admin.login');
		else
			return redirect()->route('admin.dashboard');
	}

	protected function authenticate(Request $req) {
		$user = User::where('email', '=', $req->email)->first();

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
			if ($user) {
				try {
					DB::beginTransaction();

					if ($user->login_attempts < 5) {
						$user->login_attempts = $user->login_attempts + 1;
						$msg = 'Wrong email/password!';
					}
					else {
						if ($user->locked == 0) {
							// DO THE MAILING HERE. THIS IS TO SEND AN EMAIL ONLY ONCE
						}

						$user->locked = 1;
						$user->locked_by = User::getIP();
						$msg = 'Exceeded 5 tries, account locked';
					}
					$user->save();
					
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
			auth()->logout();
			return redirect(route('home'))->with('flash_success', 'Logged out!');
		}
		return redirect()->route('admin.dashboard')->with('flash_error', 'Something went wrong, please try again.');
	}

	// PAGES
	protected function index(Request $req) {
		$users = User::get();

		if ($req->has('sd') && $req->sd == 1)
			$users = User::withTrashed()->get();

		return view('admin.users.index', [
			'users' => $users,
			'show_softdeletes' => ($req->has('sd') && $req->sd == 1 ? true : false)
		]);
	}

	protected function create() {
		$types = Type::get();
		$password = str_random(25);

		// return view('admin.users.create', [
		// 	'departments' => $types,
		// 	'password' => $password
		// ]);
	}

	// protected function store(Request $req) {
	// 	// If the isAvatarLink is not checked, set it to 0 since php returns nothing if a boolean is god damn false...
	// 	if (!$req->has('is_avatar_link'))
	// 		$req->request->set('is_avatar_link', '0');

	// 	if (!$req->has('is_departamental_account'))
	// 		$req->request->set('is_departamental_account', '0');

	// 	$hasErrors = false;
	// 	$validator = Validator::make($req->all(), [
	// 		'first_name' => array('required', 'regex:/([a-zA-Z])|(\s)/', 'max:255'),
	// 		'middle_name' => array('regex:/([a-zA-Z])|(\s)/', 'max:255', 'nullable'),
	// 		'last_name' => array('required', 'regex:/([a-zA-Z])|(\s)/', 'max:255'),
	// 		'email' => 'required|email|string|max:255',
	// 		'department' => 'required|numeric|exists:departments,id',
	// 		'password' => 'required|string|min:8|max:255'
	// 	], [
	// 		'first_name.required' => 'The first or given name is required',
	// 		'first_name.regex' => 'First names are only composed of alphabets only',
	// 		'first_name.max' => 'First names must not exceed 255 characters',
	// 		'middle_name.regex' => 'Middle names are only composed of alphabets only',
	// 		'middle_name.max' => 'Middle names must not exceed 255 characters',
	// 		'last_name.required' => 'The last name is required',
	// 		'last_name.regex' => 'Last names are only composed of alphabets only',
	// 		'last_name.max' => 'Last names must not exceed 255 characters',
	// 		'email.required' => 'The email for this user is required',
	// 		'email.email' => 'Invalid email address',
	// 		'email.string' => 'Inavlid email address',
	// 		'email.max' => 'Emails must not exceed 255 characters',
	// 		'department.required' => 'Please select the department where the user works under',
	// 		'department.numeric' => 'Please refrain from modifying the form',
	// 		'department.exists' => 'Unknown department',
	// 		'password.required' => 'Password is required',
	// 		'password.string' => 'Password should be a string of characters',
	// 		'password.min' => 'A minimum of 8 characters is the allowed limit for passwords',
	// 		'password.max' => 'A maximum of 255 characters is the allowed limit for passwords'
	// 	]);
	// 	$hasErrors = $validator->fails();

	// 	if ($req->is_avatar_link) {
	// 		$imgValidator = Validator::make($req->all(), [
	// 			'avatar' => 'max:255|nullable',
	// 		], [
	// 			'avatar.max' => 'URL length must not exceed 255 characters',
	// 			'avatar.url' => 'Invlid URL',
	// 		]);

	// 		if ($imgValidator->fails()) {
	// 			$hasErrors = true;
	// 			$validator->messages()->merge($imgValidator->messages());
	// 		}
	// 	}
	// 	else {
	// 		$imgValidator = Validator::make($req->all(), [
	// 			'avatar' => 'max:5120|mimes:jpeg,jpg,png,webp|nullable',
	// 		], [
	// 			'avatar.max' => 'Image should be below 5MB',
	// 			'avatar.mimes' => 'Selected file doesn\'t match the allowed image formats',
	// 		]);

	// 		if ($imgValidator->fails()) {
	// 			$hasErrors = true;
	// 			$validator->messages()->merge($imgValidator->messages());
	// 		}
	// 	}

	// 	if ($hasErrors) {
	// 		return redirect()
	// 			->back()
	// 			->withErrors($validator)
	// 			->withInput();
	// 	}

	// 	try {
	// 		DB::beginTransaction();

	// 		$avatar = 'default.png';
	// 		// FILE HANDLING
	// 		// If a new avatar is coming
	// 		if ($req->exists('avatar')) {
	// 			// If the new avatar is a file
	// 			if (!$req->is_avatar_link) {
	// 				// Proceed with the usual storing of files
	// 				$destination = 'uploads/users';
	// 				$fileType = $req->file('avatar')->getClientOriginalExtension();
	// 				$avatar = $req->first_name . '-' . $req->last_name . "-DP." . $fileType;
	// 				$req->file('avatar')->move($destination, $avatar);

	// 				// Save the file name to the table
	// 				$avatar = $avatar;
	// 			}
	// 			// If the new avatar is a link, just slap that link to the table
	// 			else {
	// 				$avatar = $req->avatar;
	// 			}
	// 		}

	// 		$user = User::create([
	// 			'first_name' => $req->first_name,
	// 			'middle_name' => $req->middle_name,
	// 			'last_name' => $req->last_name,
	// 			'suffix' => $req->suffix,
	// 			'is_avatar_link' => $req->is_avatar_link ? 1 : 0,
	// 			'avatar' => $avatar,
	// 			'email' => $req->email,
	// 			'department_id' => $req->department,
	// 			'is_departamental_account' => $req->is_departamental_account ? 1 : 0,
	// 			'password' => Hash::make($req->password)
	// 		]);

	// 		$recipients = array(Auth::user()->email, $user->email);

	// 		foreach ($recipients as $r) {
	// 			Mail::send(
	// 				'templates.emails.account_creation',
	// 				['email' => $r, 'req' => $req],
	// 				function ($m) use ($r) {
	// 					$m->to($r, $r)
	// 						->from('mis@taytayrizal.gov.ph')
	// 						->subject('Account Creation');
	// 				}
	// 			);
	// 		}

	// 		DB::commit();
	// 	} catch (Exception $e) {
	// 		DB::rollback();
	// 		Log::error($e);

	// 		return redirect()
	// 			->route('admin.users.index')
	// 			->with('flash_error', 'Something went wrong, please try again later');
	// 	}

	// 	return redirect()
	// 		->route('admin.users.index')
	// 		->with('flash_success', 'Successfully added "' . trim($user->getName()) . '"');
	// }
}