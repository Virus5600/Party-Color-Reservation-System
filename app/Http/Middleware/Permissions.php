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
		if (!Auth::check()) 
			return redirect()->intended();

		$user = Auth::user();
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