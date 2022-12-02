<html>
	<body style="font-family: 'Arial'; margin: 0; max-width: 100%; width: 100%">
		<div style="height: 5rem; background-color: #35408F; padding: 5px; display: flex;">
			<img src="{{ App\Settings::getFile('web-logo') }}" style="width: auto; height: 100%; margin-left: auto; margin-right: 1rem;">
			<h1 style="color: white; margin-top: auto; margin-bottom: auto; margin-right: auto;">{{ App\Settings::getValue('web-name') }}</h1>
		</div>

		<div style="display: flex; flex-direction: column;">
			<div style="width: 75%; margin-left: auto; margin-right: auto;">
				<h1>@yield('title')</h1>

				@yield('content')
			</div>
		</div><br>

		<div style="height: 2.5rem; background-color: #35408F; text-align: center; padding: 5px;">
			<p style="color: #cccccc; font-size: smaller; text-align: center; width: 100%;">
				This is a system-generated email. Please do not reply.
			</p>
		</div>
	</body>
</html>