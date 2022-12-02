@extends('layouts.emails.account.layout')

@section('title')
An attempt to access you account has been made!
@endsection

@section('content')
<p style="text-indent: 1rem;">Your account was locked after 5 failed attempts of logging in. Please create a new password for your account.</p>
<p>Click this link or copy and open it on another tab to <span title="This may be a good time to update your password to a stronger one since someone is trying to access it.">change your password.</span></p>

<p>
	<a href="{{ route("change-password.edit", [$args['token']]) }}">{{ route("change-password.edit", [$args['token']]) }}</a>
</p>

<label for="data">Your account is being accessed:</label>
<div style="background-color: lightgray; padding: 1rem;">
	<code id="data">
		@php($ip = $user->locked_by == '::1' ? '103.5.2.102' : $user->locked)
		IP: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->ip }}<br>
		Country: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->country_name }}<br>
		Region: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->region_name }}<br>
		City: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->city }}<br>
		ISP: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->organisation }}<br>
	</code>
</div>
@endsection