<!DOCTYPE html>
<html>
	<head>
		{{-- META DATA --}}
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="Content-Language" content="ja-JP" />
		<meta name="csrf-token" content="{{ csrf_token() }}">

		{{-- SITE META --}}
		<meta name="type" content="website">
		<meta name="title" content="{{ App\Settings::getValue('web-name') }}">
		<meta name="description" content="{{ App\Settings::getValue('web-desc') }}">
		<meta name="image" content="{{ asset('images/meta-banner.jpg') }}">
		<meta name="keywords" content="{{ env('APP_KEYW') }}">
		<meta name="application-name" content="{{ App\Settings::getValue('web-name') }}">

		{{-- TWITTER META --}}
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:title" content="{{ App\Settings::getValue('web-name') }}">
		<meta name="twitter:description" content="{{ App\Settings::getValue('web-desc') }}">
		<meta name="twitter:image" content="{{asset('/images/meta-banner.jpg')}}">

		{{-- OG META --}}
		<meta name="og:url" content="{{Request::url()}}">
		<meta name="og:type" content="website">
		<meta name="og:title" content="{{ App\Settings::getValue('web-name') }}">
		<meta name="og:description" content="{{ App\Settings::getValue('web-desc') }}">
		<meta name="og:image" content="{{asset('/images/meta-banner.jpg')}}">

		{{-- FAVICON --}}
		<link rel="icon" href="{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file) }}">
		<link rel="shortcut icon" href="{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file) }}">
		<link rel="apple-touch-icon" href="{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file) }}">
		<link rel="mask-icon" href="{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file) }}">

		<title>Party Color</title>
	</head>

	<body>
		<div id="example"></div>
		
		<script type="text/javascript" src="{{ asset('js/app.js') }}" onerror="this.remove();"></script>
		<script type="text/javascript" src="{{ secure_asset('js/app.js') }}" onerror="this.remove();"></script>
	</body>
</html>