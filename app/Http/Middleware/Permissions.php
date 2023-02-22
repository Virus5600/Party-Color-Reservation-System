<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Input;

use App\ActivityLog;

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

		if ($user->tokens()->count() <= 0) {
			auth()->guard('web')->logout();
			session()->flush();

			ActivityLog::log(
				"User {$user->email} was logged out due to missing PAT",
				$user->id,
				"User",
				$user->id,
				true
			);

			return redirect()->route("login");
		}

		if ($user->hasPermission($permissions)) {
			return $next($request);
		}
		else {
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