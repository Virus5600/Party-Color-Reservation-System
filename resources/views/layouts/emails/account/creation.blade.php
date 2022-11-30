<html>
	<body style="font-family: 'Arial'; margin: 0; max-width: 100%; width: 100%">
		<div style="height: 5rem; background-color: #35408F; padding: 5px; display: flex;">
			<img src="{{ App\Settings::getFile('web-logo') }}" style="width: auto; height: 100%; margin-left: auto; margin-right: 1rem;">
			<h1 style="color: white; margin-top: auto; margin-bottom: auto; margin-right: auto;">{{ App\Settings::getValue('web-name') }}</h1>
		</div>

		@php ($req = $args['req'])
		<div style="display: flex; flex-direction: column;">
			<div style="width: 75%; margin-left: auto; margin-right: auto;">
				<h1>Hello!</h1>
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
			</div>
		</div><br>

		<div style="height: 2.5rem; background-color: #35408F; text-align: center; padding: 5px;">
			<p style="color: #cccccc; font-size: smaller; text-align: center; width: 100%;">
				Please do not reply to this e-mail directly as this is just an automatic email and we will not receive your letter.
			</p>
		</div>
	</body>
</html>