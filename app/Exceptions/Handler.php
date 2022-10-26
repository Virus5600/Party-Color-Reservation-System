<?php

namespace App\Exceptions;

use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Throwable;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Report or log an exception.
	 *
	 * @param  \Throwable  $exception
	 * @return void
	 *
	 * @throws \Throwable
	 */
	public function report(Throwable $exception)
	{
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Throwable  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Throwable
	 */
	public function render($request, Throwable $exception)
	{
		if ($exception instanceof TokenMismatchException) {
			return redirect()
				->back()
				->withInput($request->except('_token'))
				->with('flash_message', "Oops! Seems you couldn't submit the form for a long time. Please try again.")
				->with('has_icon', 'true');
		}

		return parent::render($request, $exception);
	}
}