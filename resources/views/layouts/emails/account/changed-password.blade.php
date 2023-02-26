@extends('layouts.emails.layout')

@section('title')
You're password has been updated!
@endsection

@section('content')
<p style="text-indent: 1rem;">
	You're password has been updated just a couple of minutes ago{{ (isset($args['type']) && $args['type'] == 'admin-change') ? 'by a system admin.' : '.' }}
</p>

<p>If this was not you or did not request for a password reset, please notify your system admin to change your password or to temporarily deactivate your account.</p>

<hr>

<p>To access your account, go to the <a href="{{ route('login') }}">login</a> page and enter the credentials below:</p>

<code style="font-size: large;">
	<span style="font-family: Arial;">Email:</span> {{ $args['email'] }}<br>
	<span style="font-family: Arial;">Password:</span> {{ $args['password'] }}
</code>
@endsection