@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 col-md-4 col-lg my-3">
			<div class="row">
				{{-- HEADER --}}
				<div class="col-12 text-center text-md-left">
					<h1>Settings</h1>
				</div>
			</div>
		</div>
	</div>

	<div class="card dark-shadow flex-fill py-2 px-3 mb-3" id="inner-content">
		@if (Auth::user()->hasPermission('settings_tab_edit'))
		<form method="POST" action="{{ route('admin.settings.update') }}" class="form" enctype="multipart/form-data">
		@else
		<form class="form" readonly>
		@endif
			{{ csrf_field() }}

			<h3 class="text-center font-weight-bold mb-5">Website Related</h3>

			<div class="row">
				{{-- WEB LOGO --}}
				<div class="col-12 col-lg-6">
					{{-- IMAGE INPUT --}}
					<div class="image-input-scope h-100" id="web-logo-scope" data-settings="#image-input-settings" data-fallback-img="{{ asset('uploads/settings/default.png') }}">
						{{-- FILE IMAGE --}}
						<div class="h-100 pb-3 text-center image-input collapse show avatar_holder" id="web-logo-image-input-wrapper">
							<div class="h-100 row border rounded border-secondary-light py-2 mx-1">
								<div class="col-12 col-md-6 text-md-right">
									<div class="hover-cam mx-auto avatar rounded overflow-hidden">
										<img src="{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file) }}" class="hover-zoom img-fluid avatar" id="web-logo-container" alt="Website Logo" data-default-src="{{ asset('uploads/settings/default.png') }}">
										<span class="icon text-center image-input-float" id="web-logo" tabindex="0">
											<i class="fas fa-camera text-white hover-icon-2x"></i>
										</span>
									</div>
									<input type="file" name="web-logo" class="d-none" accept=".jpg,.jpeg,.png,.webp" data-role="image-input" data-target-image-container="#web-logo-container" data-target-name-container="#web-logo-name" >
									<h6 id="web-logo-name" class="text-truncate w-50 mx-auto text-center" data-default-name="{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file, false) }}">{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file, false) }}</h6>
								</div>

								<div class="col-12 col-md-6 text-md-left">
									<label class="form-label font-weight-bold" for="web-logo">Website Logo</label><br>
									<small class="text-muted pb-0 mb-0">
										<b>FORMATS ALLOWED:</b>
										<br>JPEG, JPG, PNG, WEBP
									</small><br>
									<small class="text-muted pt-0 mt-0"><b>MAX SIZE:</b> 5MB</small><br>
								</div>
							</div>
						</div>
					</div>

					{{-- LOGO ERROR --}}
					<div class="text-center">
						<span class="text-danger small">{{$errors->first('web-logo')}}</span>
					</div>
				</div>

				<div class="col-12 col-lg-6">
					{{-- APP NAME --}}
					<div class="form-group">
						<label class="form-label">Website Name</label>
						<input type="text" name="web-name" class="form-control" value="{{ App\Settings::getValue('web-name') == null ? 'Party Color' : App\Settings::getValue('web-name') }}" required />
						<span class="text-danger small">{{$errors->first('web-name')}}</span>
					</div>

					{{-- APP DESCRIPTION --}}
					<div class="form-group text-counter-parent">
						<label for="web-desc" class="form-label">Website Description</label>
						<textarea name="web-desc" id="web-desc" class="form-control not-resizable text-counter-input" rows="3" data-max="255" required>{{ App\Settings::getValue('web-desc') == null ? 'The official website of Taytay Municipal' : App\Settings::getValue('web-desc') }}</textarea>
						<span class="text-counter small">255</span>
						<span class="text-danger small">{{$errors->first('web-desc')}}</span>
					</div>
				</div>

				{{-- STORE CAPACITY --}}
				<div class="col-6 col-lg-4 mx-auto">
					<div class="form-group">
						<label for="capacity" class="form-label">Store Capacity</label>
						<input type="number" class="form-control w-100" min="1" max="2147483647" name="capacity" id="capacity" value="{{ App\Settings::getValue('capacity') == null ? '50' : App\Settings::getValue('capacity') }}" required>
						<span class="text-danger small">{{$errors->first('capacity')}}</span>
					</div>
				</div>

				{{-- EXTENSION FEE --}}
				<div class="col-6 col-lg-4 mx-auto">
					<div class="form-group">
						<label for="extension_fee" class="form-label">Extension Fee</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) }}</span>
							</div>
							<input type="number" class="form-control w-auto" min="1" max="2147483647" name="extension_fee" id="extension_fee" value="{{ App\Settings::getValue('extension_fee') == null ? '500' : App\Settings::getValue('extension_fee') }}" required>
							<div class="input-group-append">
								<span class="input-group-text">/hr</span>
							</div>
						</div>
						<span class="text-danger small">{{$errors->first('extension_fee')}}</span>
					</div>
				</div>

				{{-- DAY SCHEDULE --}}
				<div class="col-6 col-lg-4 mx-auto">
					<div class="form-group">
						<label for="day-schedule" class="form-label">Day Schedule</label><br>

						<select class="show-tick select-picker form-control w-100" multiple name="day-schedule[]" id="day-schedule" required>
							@php ($day = now()->startOfWeek(\Carbon\Carbon::SUNDAY))
							@for ($i = 0; $i < 7; $i++)
							<option
								value="{{ $i }}"
								data-tokens="{{ $day->format("l") }}"
								{{ in_array($i, explode(",", App\Settings::getValue("day-schedule"))) ? 'selected' : '' }}
								>
								{{ $day->format("l") }}
								@php($day = $day->copy()->addDay())
							</option>
							@endfor
						</select>
						<br><span class="text-danger small">{{$errors->first('day-schedule')}}</span>
					</div>
				</div>

				{{-- OPENING TIME --}}
				<div class="col-6 col-lg-3 mx-auto">
					<div class="form-group">
						<label for="opening" class="form-label">Opening Time</label>
						<input type="time" class="form-control w-100" name="opening" id="opening" value="{{ App\Settings::getValue('opening') == null ? '17:00' : App\Settings::getValue('opening') }}" required>
						<span class="text-danger small">{{$errors->first('opening')}}</span>
					</div>
				</div>
				
				{{-- CLOSING TIME --}}
				<div class="col-6 col-lg-3 mx-auto">
					<div class="form-group">
						<label for="closing" class="form-label">Closing Time</label>
						<input type="time" class="form-control w-100" name="closing" id="closing" value="{{ App\Settings::getValue('closing') == null ? '17:00' : App\Settings::getValue('closing') }}" min="{{ App\Settings::getValue('opening') == null ? '17:00' : App\Settings::getValue('opening') }}" required>
						<span class="text-danger small">{{$errors->first('closing')}}</span>
					</div>
				</div>
			</div>

			<hr class="hr-thick">
			<h3 class="text-center font-weight-bold mb-5">Reaching Out</h3>

			<div class="row">
				{{-- PUBLIC CONTACT --}}
				<div class="col-12 col-lg-6 form-group">
					<label class="form-label" for="contacts">Telephone Number(s)</label>
					<input name="contacts" id="contacts" class="customLook custom-scrollbar form-control" value="{{ App\Settings::getValue('contacts') == null ? '080-3980-4560' : App\Settings::getValue('contacts') }}">
					<span class="text-danger small">
						@if ($errors->has('contact-single'))
						{{ $errors->first('contact-single') }}
						@elseif ($errors->has('contacts'))
						{{ $errors->first('contacts') }}
						@else
							@foreach($errors->get('contacts.*') as $error)
								{{ $error[0] }}
							@endforeach
						@endif
					</span>
				</div>

				{{-- PUBLIC EMAIL --}}
				<div class="col-12 col-lg-6 form-group">
					<label class="form-label">Email Address</label>
					<input name="emails" id="emails" class="customLook custom-scrollbar form-control" value="{{ App\Settings::valueToJson('emails') == null ? 'partycolor3f@gmail.com' : App\Settings::valueToJson('emails') }}">
					<span class="text-danger small">
						@if ($errors->has('email-single'))
						{{ $errors->first('email-single') }}
						@elseif ($errors->has('emails'))
						{{ $errors->first('emails') }}
						@else
							@foreach($errors->get('emails.*') as $error)
								{{ $error[0] }}
							@endforeach
						@endif
					</span>
				</div>

				{{-- ADDRESS --}}
				<div class="col-12 form-group">
					<label class="form-label">Address</label>
					<textarea name="address" class="form-control not-resizable" placeholder="Address" rows="5">{{ App\Settings::getValue('address') == null ? 'Don Hilario Avenue, Club Manila East Compound, Barangay San Juan Taytay, Rizal 1920 Philippines' : App\Settings::getValue('address') }}</textarea>
				</div>
			</div>

			@if (Auth::user()->hasPermission('settings_tab_edit'))
			<button type="submit" class="btn btn-success" data-action="update">Update</button>
			<button type="button" class="btn btn-secondary" id="revert">Reset Form</button>
			@endif
		</form>
	</div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/image-input.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/text-counter.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/custom-tagify.css') }}" />
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/image-input.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/text-counter.js') }}"></script>
<script type="text/javascript">
	$(document).ready(() => {
		// Tagify
		{
			$('#contacts').tagify({
				keepInvalidTags: true,
				originalInputValueFormat: v => v.map(item => item.value).join(","),
				pattern: /^\+*(?=.{7,14})[\d\s-]{7,15}$/
			});

			$('#emails').tagify({
				keepInvalidTags: true,
				originalInputValueFormat: v => v.map(item => item.value).join(","),
				pattern: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/
			});
		}

		@if (Auth::user()->hasPermission('settings_tab_edit'))
		$('#revert').on('click', (e) => {
			location.reload();
		});
		@else
		$.each($('form').find('input, textarea'), (k, v) => {
			$(v).prop('readonly', true);
		});

		$('div.tag .tag-i').remove();
		@endif

		// Select Picker
		$('.select-picker').selectpicker({
			liveSearch: true,
			liveSearchStyle: "contains",
			style: "btn-white border-secondary-light",
		}).trigger('change').trigger('change.bs.select');
		$('.select-picker').find("input[type=search]").addClass("dont-validate");

		// Adjusting Closing Time
		$("#opening").on('change', (e) => {
			let obj = $(e.currentTarget);
			let closing = $("#closing");

			closing.attr('min', obj.val());
		});
	});
</script>
@endsection