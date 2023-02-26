@extends('layouts.emails.layout')

@section('title')
Hello!
@endsection

@section('content')
@php ($req = $args['req'])
<p>
	@if ($recipient == $args['email'])
	Your account has been created just recently and you're now ready to access it!
	@else
	An account for <code>{{ $args['email'] }}</code> has been created just recently. Attached below are the credentials for the profile. This serves as a notification for the creation of the account.
	@endif
</p>

@if ($req['email'] == $args['email'])
<p>To access your account, go to the <a href="{{ route('login') }}">login</a> page and enter the credentials below:</p>
@endif

<code style="font-size: large;">
	<span style="font-family: Arial;">Email:</span> {{ $req['email'] }}<br>
	<span style="font-family: Arial;">Password:</span> {{ $req['password'] }}
</code>
@endsection