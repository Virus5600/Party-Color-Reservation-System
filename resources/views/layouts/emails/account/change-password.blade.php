@extends('layouts.emails.layout')

@section('title')
You've requested for a password change!
@endsection

@section('content')
<p style="text-indent: 1rem;">You've requested to change the password of your account and we've received it.</p>
<p>Click this link or copy and open it on another tab to change your password.</p>

<p>
	<a href="{{ route("change-password.edit", [$args['token']]) }}">{{ route("change-password.edit", [$args['token']]) }}</a>
</p>

<p>If you did not request for a password change, please disregard this email.</p>
@endsection