<html>
	<body style="font-family: 'Arial'; margin: 0; max-width: 100%; width: 100%">
		<div style="height: 5rem; background-color: #35408F; padding: 5px; display: flex;">
			<img src="{{ App\Settings::getFile('web-logo') }}" style="width: auto; height: 100%; margin-left: auto; margin-right: 1rem;">
			<h1 style="color: white; margin-top: auto; margin-bottom: auto; margin-right: auto;">{{ App\Settings::getValue('web-name') }}</h1>
		</div>

		<div style="display: flex; flex-direction: column;">
			<div style="width: 75%; margin-left: auto; margin-right: auto;">
				<h1>An attempt to access you account has been made!</h1>
				<p style="text-indent: 1rem;">Your account was locked after 5 failed attempts of logging in. Please create a new password for your account.</p>
				<p>Click this link or copy and open it on another tab to change your password.</p>
				
				<p>
					<a href="{{ route("user.change-password", [$args['token']]) }}">{{ route("user.change-password", [$args['token']]) }}</a>
				</p>

				<label for="data">Your account is being accessed:</label>
				<div style="background-color: lightgray; padding: 1rem;">
					<code id="data">
						@php($ip = $user->locked_by == '::1' ? '110.54.173.39' : $user->locked)
						IP: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->ip }}<br>
						Country: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->country_name }}<br>
						Region: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->region_name }}<br>
						City: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->city }}<br>
						ISP: {{ json_decode(file_get_contents("https://ip-api.io/json/{$ip}"))->organisation }}<br>
					</code>
				</div>
			</div>
		</div><br>

		<div style="height: 2.5rem; background-color: #35408F; text-align: center; padding: 5px;">
			<p style="color: #cccccc; font-size: smaller; text-align: center; width: 100%;">
				Please do not reply to this e-mail directly as this is just an automatic email and we will not receive your letter.
			</p>
		</div>
	</body>
</html>
{{-- Resume fixing locked mailing --}}