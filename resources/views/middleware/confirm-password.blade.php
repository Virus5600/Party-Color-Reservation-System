<!DOCTYPE html>
<html lang="en-US">
	<head>
		@php
		$webName = App\Settings::getValue('web-name');
		$webDesc = App\Settings::getValue('web-name');
		$webLogoInstance = App\Settings::getInstance('web-logo');
		$webLogo = $webLogoInstance->getImage(!$webLogoInstance->is_file);
		@endphp

		{{-- META DATA --}}
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="Content-Language" content="en-US" />

		{{-- SITE META --}}
		<meta name="type" content="website">
		<meta name="title" content="{{ $webName }}">
		<meta name="description" content="{{ $webDesc }}">
		<meta name="image" content="{{ asset('images/meta-banner.jpg') }}">
		<meta name="keywords" content="{{ env('APP_KEYW') }}">
		<meta name="application-name" content="{{ $webName }}">

		{{-- TWITTER META --}}
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:title" content="{{ $webName }}">
		<meta name="twitter:description" content="{{ $webDesc }}">
		<meta name="twitter:image" content="{{asset('/images/meta-banner.jpg')}}">

		{{-- OG META --}}
		<meta name="og:url" content="{{Request::url()}}">
		<meta name="og:type" content="website">
		<meta name="og:title" content="{{ $webName }}">
		<meta name="og:description" content="{{ $webDesc }}">
		<meta name="og:image" content="{{asset('/images/meta-banner.jpg')}}">

		{{-- CSS --}}
		<link href="{{ asset('css/lib-styles.css') }}" rel="stylesheet">
		<link href="{{ asset('css/style.css') }}" rel="stylesheet">
		<link href="{{ asset('css/login.css') }}" rel="stylesheet">

		{{-- JQUERY / SWEETALERT 2 / SLICK CAROUSEL --}}
		<script type="text/javascript" src="{{ asset('js/lib-scripts.js') }}"></script>

		{{-- Removes the code that shows up when script is disabled/not allowed/blocked --}}
		<script type="text/javascript" id="for-js-disabled-js">$('head').append('<style id="for-js-disabled">#js-disabled { display: none; }</style>');$(document).ready(function() {$('#js-disabled').remove();$('#for-js-disabled').remove();$('#for-js-disabled-js').remove();});</script>

		{{-- FAVICON --}}
		<link rel="icon" href="{{ $webLogo }}">
		<link rel="shortcut icon" href="{{ $webLogo }}">
		<link rel="apple-touch-icon" href="{{ $webLogo }}">
		<link rel="mask-icon" href="{{ $webLogo }}">

		{{-- TITLE --}}
		<title>Confirm Password - Party Color</title>
	</head>

	<body>
		{{-- SHOWS THIS INSTEAD WHEN JAVASCRIPT IS DISABLED --}}
		<div style="position: absolute; height: 100vh; width: 100vw; background-color: #ccc;" id="js-disabled">
			<style type="text/css">
				/* Make the element disappear if JavaScript isn't allowed */
				.js-only {
					display: none!important;
				}
			</style>
			<div class="row h-100">
				<div class="col-12 col-md-4 offset-md-4 py-5 my-auto">
					<div class="card shadow my-auto">
						<h4 class="card-header card-title text-center">Javascript is Disabled</h4>

						<div class="card-body">
							<p class="card-text">This website required <b>JavaScript</b> to run. Please allow/enable JavaScript and refresh the page.</p>
							<p class="card-text">If the JavaScript is enabled or allowed, please check your firewall as they might be the one disabling JavaScript.</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="d-flex flex-column min-vh-100 js-only">
			<main class="content d-flex flex-column flex-grow-1 my-3 my-lg-5" id="content">
				<div class="container-fluid d-flex flex-column flex-grow-1">
					
					{{-- CONFIRM PASSWORD FORM START --}}
					<div class="card w-100 w-sm-75 w-md-50 w-lg-25 m-auto">
						<h4 class="card-header text-center">CONFIRM PASSWORD</h4>

						<form action="{{ route('password.confirm.check') }}" method="POST" class="card-body" id="form">
							{{ csrf_field() }}

							<p class="card-text text-center text-muted">{{ $message }}</p>
							
							<div class="form-group mb-0">
								<label class="form-label" for="password">Password</label>
								<div class="input-group">
									<input class="form-control border-secondary border-right-0" type="password" name="password" id="password" aria-label="Password" aria-describedby="toggle-show-password" placeholder="Password" />
									<div class="input-group-append">
										<button type="button" class="btn bg-white border-secondary border-left-0" id="toggle-show-password" aria-label="Show Password" data-target="#password">
											<i class="fas fa-eye d-none" id="show"></i>
											<i class="fas fa-eye-slash" id="hide"></i>
										</button>
									</div>
								</div>
							</div>

							<div class="form-group text-center">
								<a href="{{ route('forgot-password.index') }}" class="text-primary small" id="forgotPassword">Forgot Password?</a>
							</div>

							<div class="form-group text-center">
								<button type="submit" class="btn btn-primary" data-action="submit">Submit</button>
								<a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
							</div>
						</form>
					</div>
					{{-- LOGIN FORM END --}}

				</div>
			</main>

			<!-- SCRIPTS -->
			<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
			<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
			<script type="text/javascript" src="{{ asset('js/login.js') }}"></script>
			<script type="text/javascript">
				@if (Session::has('flash_error'))
				Swal.fire({
					{!!Session::has('has_icon') ? "icon: `error`," : ""!!}
					title: `{{Session::get('flash_error')}}`,
					{!!Session::has('message') ? 'html: `' . Session::get('message') . '`,' : ''!!}
					position: {!!Session::has('position') ? '`' . Session::get('position') . '`' : '`top`'!!},
					showConfirmButton: false,
					toast: {!!Session::has('is_toast') ? Session::get('is_toast') : true!!},
					{!!Session::has('has_timer') ? (Session::get('has_timer') ? (Session::has('duration') ? ('timer: ' . Session::get('duration')) . ',' : `timer: 10000,`) : '') : `timer: 10000,`!!}
					background: `#dc3545`,
					customClass: {
						title: `text-white`,
						content: `text-white`,
						popup: `px-3`
					},
				});
				@elseif (Session::has('flash_info'))
				Swal.fire({
					{!!Session::has('has_icon') ? "icon: `info`," : ""!!}
					title: `{{Session::get('flash_info')}}`,
					{!!Session::has('message') ? 'html: `' . Session::get('message') . '`,' : ''!!}
					position: {!!Session::has('position') ? '`' . Session::get('position') . '`' : '`top`'!!},
					showConfirmButton: false,
					toast: {!!Session::has('is_toast') ? Session::get('is_toast') : true!!},
					{!!Session::has('has_timer') ? (Session::get('has_timer') ? (Session::has('duration') ? ('timer: ' . Session::get('duration')) . ',' : `timer: 10000,`) : '') : `timer: 10000,`!!}
					background: `#17a2b8`,
					customClass: {
						title: `text-white`,
						content: `text-white`,
						popup: `px-3`
					},
				});
				@elseif (Session::has('flash_success'))
				Swal.fire({
					{!!Session::has('has_icon') ? "icon: `success`," : ""!!}
					title: `{{Session::get('flash_success')}}`,
					{!!Session::has('message') ? 'html: `' . Session::get('message') . '`,' : ''!!}
					position: {!!Session::has('position') ? '`' . Session::get('position') . '`' : '`top`'!!},
					showConfirmButton: false,
					toast: {!!Session::has('is_toast') ? Session::get('is_toast') : true!!},
					{!!Session::has('has_timer') ? (Session::get('has_timer') ? (Session::has('duration') ? ('timer: ' . Session::get('duration')) . ',' : `timer: 10000,`) : '') : `timer: 10000,`!!}
					background: `#28a745`,
					customClass: {
						title: `text-white`,
						content: `text-white`,
						popup: `px-3`
					},
				});
				@endif
			</script>
		</div>
	</body>
</html>