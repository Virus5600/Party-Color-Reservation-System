<!DOCTYPE html>
<html>
	<head>
		@php
		$webName = App\Settings::getValue('web-name');
		$webDesc = App\Settings::getValue('web-desc');
		$webLogoInstance = App\Settings::getInstance('web-logo');
		$webLogo = $webLogoInstance->getImage(!$webLogoInstance->is_file);
		@endphp

		{{-- META DATA --}}
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="Content-Language" content="ja-JP" />
		<meta name="csrf-token" content="{{ csrf_token() }}">

		{{-- SITE META --}}
		<meta name="type" content="website">
		<meta name="title" content="{{ $webName }}">
		<meta name="description" content="{{ $webDesc }}">
		<meta name="image" content="{{ asset('uploads/settings/meta-banner.png') }}">
		<meta name="keywords" content="{{ env('APP_KEYW') }}">
		<meta name="application-name" content="{{ $webName }}">

		{{-- TWITTER META --}}
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:title" content="{{ $webName }}">
		<meta name="twitter:description" content="{{ $webDesc }}">
		<meta name="twitter:image" content="{{ asset('uploads/settings/meta-banner.png') }}">

		{{-- OG META --}}
		<meta name="og:url" content="{{Request::url()}}">
		<meta name="og:type" content="website">
		<meta name="og:title" content="{{ $webName }}">
		<meta name="og:description" content="{{ $webDesc }}">
		<meta name="og:image" content="{{ asset('uploads/settings/meta-banner.png') }}">

		{{-- FAVICON --}}
		<link rel="icon" href="{{ $webLogo }}">
		<link rel="shortcut icon" href="{{ $webLogo }}">
		<link rel="apple-touch-icon" href="{{ $webLogo }}">
		<link rel="mask-icon" href="{{ $webLogo }}">

		<title>{{ $webName }}</title>
	</head>

	<body>
		<div id="app"></div>
		
		<script id="forRemoval">const loadedPage = "{{ $loadedPage == "/" ? "" : $loadedPage }}"; document.getElementById("forRemoval").remove();</script>
		<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/util/swal-flash.js') }}"></script>
	</body>
</html>