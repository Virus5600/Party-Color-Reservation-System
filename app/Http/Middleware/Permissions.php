<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Input;

use Auth;
use Closure;
use Log;

class Permissions
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $permissions)
	{
		if (!auth()->check()) 
			return redirect()->intended();
		
		$user = auth()->user();
		
		if ($permissions == 'sanctum') {
			if ($user->tokens()->count() <= 0) {
				auth()->guard('web')->logout();
				session()->flush();

				activity('middleware')
					->byAnonymous()
					->on($user)
					->event('logged-out')
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
					->log("User {$user->email} was logged out due to missing PAT");

				return redirect()->route("login");
			}
			else {
				return $next($request);
			}
		}

		if ($user->tokens()->count() <= 0) {
			auth()->guard('web')->logout();
			session()->flush();

			activity('middleware')
				->by($user)
				->on($user)
				->event('logged-out')
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
				->log("User {$user->email} was logged out due to missing PAT");

			return redirect()->route("login");
		}
		
		if ($user->hasPermission($permissions)) {
			return $next($request);
		}
		else {
			activity('middleware')
				->by($user)
				->on($user)
				->event('logged-out')
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
				->log("User {$user->email} attempted to access <a href='{$request->fullUrl()}'>{$request->getRequestUri()}</a>");

			return redirect()
				->route('admin.dashboard')
				->with('flash_info', 'Access Denied')
				->with('has_icon', 'true')
				->with('message', 'Redirected back to previous page.')
				->with('has_timer')
				->with('duration', '5000');
		}
	}
}