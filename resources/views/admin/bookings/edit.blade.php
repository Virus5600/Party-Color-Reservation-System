@extends('layouts.admin')

@section('title', 'Booking')

@section('content')

@php
$datetime = now()->timezone('Asia/Tokyo');
$isEightPM = $datetime->gt('08:00 PM');
$new_contact_index = Session::get("new_contact_index");
$maxCap = App\Settings::getValue('capacity');
@endphp

<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.bookings.index')}}');" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Booking
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="mx-auto col-12 col-lg-10">
			<div class="card dark-shadow mb-5" id="inner-content">
				<div class="card-body">
					<form action="{{ route('admin.bookings.update', [$booking->id]) }}" method="POST" enctype="multipart/form-data" class="form needs-validation" data-continuous-validation="false">
						{{ csrf_field() }}
						{{-- GENERAL VALIDATION MESSAGE --}}
						@error('general')
							@foreach($errors->get('general') as $e)
							<div class="alert alert-danger alert-dismissable fade show text-wrap">
								<button type="buttone" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
								{!! $e !!}
							</div>
							@endforeach
						@enderror

						{{-- RESERVATION INFORMATION --}}
						<div class="card my-2">
							<div class="card-header d-flex flex-wrap">
								<h3 class="w-100 w-md-50 mb-3 mx-auto my-md-auto ml-lg-0 mr-lg-auto text-center text-lg-left">Booking Information</h3>
								<select class="show-tick select-picker form-control w-100 w-sm-75 w-md-50 w-lg-25 my-auto mx-auto ml-lg-auto mr-lg-0" name="booking_type" id="bookingType" required>
									@foreach (App\Booking::$bookingTypes as $t)
									<option value="{{ $t }}" {{ $booking->booking_type == $t ? "checked" : "" }}>{{ ucwords($t) }}</option>
									@endforeach
								</select>
							</div>

							<div class="row card-body">
								<div class="form-group col-12">
									<h3>Control No <span class="float-right" style="font-weight: normal;">#{{ $booking->control_no }}</span></h3>
									<hr class="hr-thick">
								</div>

								{{-- BOOKING DATE --}}
								<div class="form-group col-12 col-lg-4">
									<label class="h5 required" for="booking_date">Booking Date</label>
									<input class="form-control" type="date" name="booking_date" id="booking_date" min="{{ $isEightPM ? $datetime->addDay()->format("Y-m-d") : $datetime->format("Y-m-d") }}" value="{{ $booking->reserved_at }}" required />
									<span class="text-danger text-wrap">{{ $errors->first('booking_date') }}</span>
								</div>

								{{-- PAX --}}
								<div class="form-group col-12 col-lg-4">
									<label class="h5" for="pax">Pax</label>
									<input class="form-control has-spinner" type="number" id="pax" name="pax" min="1" max="{{ App\Settings::getValue('capacity') }}" value="{{ $booking->pax }}" required />
									<div class="d-flex flex-row">
										<span class="text-danger text-wrap mr-auto">{{ $errors->first('pax') }}</span>
										<span class="text-muted ml-auto small">Max: {{ App\Settings::getValue('capacity') }}</span>
									</div>
								</div>

								{{-- PRICE --}}
								<div class="form-group col-12 col-lg-4">
									<label for="price" class="h5">Price</label>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) }}</span>
										</div>
										<input type="number" readonly class="form-control" min="0" max="4294967295" step=".25" name="price" id="price" value="{{ number_format($booking->price, 2, ".", "") }}" required />
									</div>
									<span class="text-danger text-wrap">{{ $errors->first('price') }}</span>
								</div>
							</div>

							<div class="row card-body">
								{{-- RESERVATION TIME --}}
								<div class="form-group col-12 col-lg-6">
									<label class="h5" for="booking_time">Time</label>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">Hour</span>
										</div>

										<input class="form-control has-spinner text-right" title="Hour" type="number" name="time_hour" id="time_hour" min="17" max="19" value="{{ \Carbon\Carbon::createFromFormat("H:i", $booking->start_at)->format("H") }}" required />
										
										<div class="input-group-prepend input-group-append">
											<span class="input-group-text">:</span>
										</div>
										
										<input class="form-control has-spinner text-left" title="Minutes" type="number" name="time_min" id="time_min" min="0" max="59" value="{{ \Carbon\Carbon::createFromFormat("H:i", $booking->start_at)->format("i") }}" required />

										<div class="input-group-append">
											<span class="input-group-text">Minute</span>
										</div>
									</div>
									<input type="hidden" name="booking_time" id="booking_time" value="{{ old('booking_time') ? old('booking_time') : "17:00" }}" required />
									<span class="text-danger text-wrap">
										{{
											(strlen($errors->first('booking_time')) > 0 ? $errors->first('booking_time') : 
												(strlen($errors->first('time_hour')) > 0 ? $errors->first('time_hour') : 
													(strlen($errors->first('time_min')) > 0 ? $errors->first('time_min') : '')
												)
											)
										}}
									</span>
								</div>

								{{-- EXTENSION --}}
								<div class="form-group col-12 col-lg-6">
									<label class="h5" for="extension">Extension</label>
									<div class="input-group">
										<input class="form-control has-spinner" type="number" name="extension" id="extension" value="{{ $booking->extension }}" min="0" max="5" step="0.5" required>
										<div class="input-group-append">
											<span class="input-group-text">Hours</span>
										</div>
									</div>
									<span class="text-danger text-wrap">{{ $errors->first('extension') }}</span>
								</div>
							</div>
						</div>

						{{-- MENU INFORMATION --}}
						<div class="card my-2">
							<h3 class="card-header d-flex flex-row">
								<span class="mr-auto">Menu Information</span>
							</h3>

							<div class="row card-body">
								{{-- MENU ITEMS --}}
								<div class="form-group col-12">
									<p class="h5">Menus</p>

									{{-- Dynamic form fields --}}
									<div class="row" id="menuField">
										<div class="col-12 my-2 position-relative" id="origMenuForm">
											<div class="card h-100">
												<div class="card-body">
													<div class="form-group">
														<label class="form-label" for="menu">Menu Name</label><br>

														<select class="show-tick select-picker w-100 form-control" name="menu[]" id="menu" multiple required>
															@foreach ($menus as $m)
															<option
																value="{{ $m->id }}"
																data-subtext="{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . "{$m->price} - {$m->getFromDuration("H")} " . Str::plural("hour", $m->getFromDuration("H")) . " and {$m->getFromDuration("i")} " . Str::plural("minute", $m->getFromDuration("i")) }}"
																data-price="{{ $m->price }}"
																data-duration="{{ $m->getFromDuration() }}"
																{{ in_array($m->id, (old('menu') ? old('menu') : [])) ? 'selected' : '' }}
																{{ in_array($m->id, $booking->menus->pluck('id')->toArray()) ? 'selected' : '' }}
																>
																{{ $m->name }}
															</option>
															@endforeach
														</select>
														<br><span class="text-danger text-wrap validation">{{ $errors->first('menu') }}</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						{{-- CONTACT INFORMATION --}}
						<div class="card my-2">
							<h3 class="card-header d-flex flex-row">
								<span class="mr-auto">Contact Information</span>
								<button class="btn btn-primary ml-auto" type="button" id="addContact" title="Add contact information"><i class="fas fa-plus-circle"></i></button>
							</h3>
							
							<div class="row card-body">
								{{-- CONTACT ITEMS --}}
								<div class="form-group col-12">
									<p class="h5">Contacts</p>
									
									<div class="form-group">
										<label for="phone_numbers" class="form-label">Contact Number</label>

										<input name="phone_numbers" id="phone_numbers" class="customLook custom-scrollbar form-control" value="{{ implode(", ", explode("|", $booking->phone_numbers)) }}" required>
										<span class="text-danger text-wrap validation">
											@if ($errors->first("phone_numbers"))
											{{ $errors->first("phone_numbers") }}
											@else
											{{ $errors->first("phone_numbers.*") }}
											@endif
										</span>
									</div>

									{{-- Dynamic form fields --}}
									<div class="row" id="contactField">
										{{-- CONTACT FORM --}}
										@php ($index = 0)
										@php($new_contact_index = Session::get('new_contact_index'))
										@php($newIndexRange = count($new_contact_index))

										@foreach ($booking->contactInformation as $c)
										<div class="col-12 col-md-4 my-2 position-relative contact-form" {{ $index == 0 ? 'id=origContactForm data-min-1=true' : '' }}>
											<div class="card h-100">
												<div class="card-body">
													<div class="form-group">
														<label class="form-label">Contact Name</label>
														<input name="contact_name[]" class="form-control" type="text" title="Contact name" value="{{ $c->contact_name }}" {{ $index == 0 ? 'required' : '' }} />
														<span class="text-danger text-wrap validation">
															{{ $errors->first("contact_name.{$new_contact_index[$index]}") }}
														</span>
													</div>

													<div class="form-group">
														<label class="form-label">Email</label>
														<input type="email" class="form-control" name="contact_email[]" title="Contact email" value="{{ $c->email }}" {{ $index == 0 ? 'required' : '' }} />
														<span class="text-danger text-wrap validation">
															{{ $errors->first("contact_email.{$new_contact_index[$index]}") }}
														</span>
													</div>
												</div>
											</div>

											@if ($index++ > 0)
											<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);">
												<button type="button" class="rounded btn btn-white border-secondary-light" title="Remove contact information" onclick="$(this).parent().parent().remove();">
													<i class="fas fa-trash fa-sm text-danger"></i>
												</button>
											</div>
											@endif
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>

						<div class="d-flex">
							<button class="btn btn-success ml-auto" type="submit" data-action="submit">Submit</button>
							<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.bookings.index')}}');" class="btn btn-danger ml-3 mr-auto">Cancel</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/custom-tagify.css') }}" />
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript">
	window.swalAlert = function (arr) {
		Swal.fire({
			title: `${arr[0]}`,
			position: `top`,
			showConfirmButton: false,
			toast: true,
			timer: 5000,
			background: `#17a2b8`,
			customClass: {
				title: `text-white`,
				content: `text-white`,
				popup: `px-3`
			},
		});
	};

	$(document).ready(() => {
		// Add contact
		$(document).on('click', '#addContact', (e) => {
			let obj = $(e.currentTarget);
			let field = $("#contactField");
			let orig = $('#origContactForm');
			let clone = orig.clone();

			// Clone cleaning
			clone.removeAttr('id');
			clone.find('.validation').text("");
			clone.find('textarea, input').val("");
			clone.find("select").removeAttr('data-last-value');
			clone.find(".is-invalid, .is-valid").removeClass('is-valid is-invalid');
			$(clone.find('option').removeAttr('selected').prop('selected', false)[0]).prop('selected', true);
			clone.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
			clone.find(".select-picker").selectpicker({
				liveSearch: true,
				style: "btn-white border-secondary-light"
			});

			if (orig.attr("data-min-1").toLowerCase() == 'true') {
				clone.removeProp("required")
					.find("textarea, input, select")
					.removeProp("required");
				clone.removeAttr("required")
					.find("textarea, input, select")
					.removeAttr("required");
			}

			// Adding the remove item
			let removeBtn = $(`<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);"><button type="button" class="rounded btn btn-white border-secondary-light" onclick="$(this).parent().parent().remove();"><i class="fas fa-trash fa-sm text-danger"></i></button></div>`);
			clone.append(removeBtn);

			field.append(clone);
		});

		// Select Picker
		$('.select-picker').selectpicker({
			liveSearch: true,
			liveSearchStyle: "contains",
			style: "btn-white border-secondary-light",
		}).trigger('change').trigger('change.bs.select');
		$('.select-picker').find("input[type=search]").addClass("dont-validate");

		// Pricing related shits
		{
			// Select Picker on change
			$(document).on('changed.bs.select', '#menu', (e) => {
				let menu = $("#menu");
				let price = $("#price");
				let pax = $("#pax");
				let extension = $("#extension");

				if (menu.val().length > 0) {
					for (let index in menu.val()) {
						let subtotal = parseFloat($($(menu).find(`option[value="${menu.val()[index]}"]`)[0]).attr('data-price'));
						subtotal *= pax.val();

						if (index == 0)
							price.val(subtotal);
						else
							price.val(parseFloat(price.val()) + subtotal);
					}
					
					let extensionFee = 500;
					extensionFee *= parseFloat(extension.val());

					price.val((parseFloat(price.val()) + extensionFee).toFixed(2));
				}
				else
					price.val('0.00');
			}).trigger('changed.bs.select');

			// Pax on change
			$(document).on('change keyup keypress', '#pax, #extension', (e) => { $("#menu").trigger('changed.bs.select') }).trigger('change');
		}

		// Time update
		$(document).on('keyup keydown keypress change click', '#time_min, #time_hour', (e) => {
			let hourE = $('#time_hour');
			let minE = $('#time_min');
			let timeE = $('#booking_time');
			let time = `{{ now()->format('Y-m-d') }}T${`0${parseInt(hourE.val())}`.slice(-2)}:${`0${minE.val()}`.slice(-2)}:00Z`;

			let date = new Date(time);
			timeE.val(`${`0${date.getUTCHours()}`.slice(-2)}:${`0${date.getUTCMinutes()}`.slice(-2)}`);
		}).trigger('change');

		// Duration update
		$(document).on('keyup keydown keypress change click', '#duration_min, #duration_hour', (e) => {
			let hourE = $('#duration_hour');
			let minE = $('#duration_min');
			let durationE = $('#duration');
			let time = `{{ now()->format('Y-m-d') }}T${`0${parseInt(hourE.val())}`.slice(-2)}:${`0${minE.val()}`.slice(-2)}:00Z`;

			let date = new Date(time);
			durationE.val(`${`0${date.getUTCHours()}`.slice(-2)}:${`0${date.getUTCMinutes()}`.slice(-2)}`);
		}).trigger('change');

		// Tagify
		$('#phone_numbers').tagify({
			keepInvalidTags: true,
			originalInputValueFormat: v => v.map(item => item.value).join(","),
			pattern: /^\+*(?=.{7,14})[\d\s-]{7,15}$/
		});

	});
</script>
@endsection