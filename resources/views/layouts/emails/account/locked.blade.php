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

@php($ip = $user->locked_by == '::1' ? '192.168.0.1' : $user->locked_by)
@php ($ipData = json_decode(file_get_contents("https://ip-api.io/json/{$ip}")))

<label for="data">Your account is being accessed:</label>
<div style="background-color: lightgray; padding: 1rem;">
	<code id="data">
		IP: {{ $ipData ? $ipData->ip : 'IP Not Valid' }}<br>
		Country: {{ $ipData ? $ipData->country_name : 'IP Not Valid' }}<br>
		Region: {{ $ipData ? $ipData->region_name : 'IP Not Valid' }}<br>
		City: {{ $ipData ? $ipData->city : 'IP Not Valid' }}<br>
		ISP: {{ $ipData ? $ipData->organisation : 'IP Not Valid' }}<br>
	</code>
</div>
@endsection