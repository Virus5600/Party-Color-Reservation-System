@extends('layouts.admin')

@section('title', 'Reservation')

@section('content')
@php ($datetime = now()->timezone('Asia/Tokyo'))
@php ($isEightPM = $datetime->gt('08:00 PM'))

<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.reservations.index')}}');" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Reservation
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
					<form action="{{ route('admin.reservations.store') }}" method="POST" enctype="multipart/form-data">
						{{ csrf_field() }}

						{{-- RESERVATION INFORMATION --}}
						<div class="card my-2">
							<h3 class="card-header">Reservation Information</h3>

							<div class="row card-body">
								{{-- MENU NAME --}}
								<div class="form-group col-12 col-lg-4">
									<label class="h5" for="reservation_date">Reservation Date</label>
									<input class="form-control" type="date" name="reservation_date" min="{{ $isEightPM ? $datetime->addDay()->format("Y-m-d") : $datetime->format("Y-m-d") }}" value="{{ old('reservation_date') }}"/>
									<span class="text-danger">{{ $errors->first('reservation_date') }}</span>
								</div>

								{{-- PAX --}}
								<div class="form-group col-12 col-lg-4">
									<label class="h5" for="pax">Pax</label>
									<input class="form-control" type="number" name="pax" min="1" max="{{ App\Settings::getValue('capacity') }}" value="{{ old('pax') ? old('pax') : '1' }}"/>
									<div class="d-flex flex-row">
										<span class="text-danger mr-auto">{{ $errors->first('pax') }}</span>
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
										<input type="number" readonly class="form-control" min="0" max="4294967295" step=".25" name="price" id="price" value="{{ old('price') ? number_format(old('price'), 2, ".", "") : number_format(0, 2, ".", "") }}">
									</div>
									<span class="text-danger">{{ $errors->first('price') }}</span>
								</div>
							</div>

							<div class="row card-body">
								{{-- RESERVATION TIME --}}
								<div class="form-group col-12 col-lg-6">
									<label class="h5" for="reservation_time">Time</label>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">Hour</span>
										</div>
										
										<input class="form-control has-spinner text-right" type="number" name="time_hour" id="time_hour" min="17" max="19" value="{{ old('time_hour') ? old('time_hour') : ($isEightPM ? '17' : $datetime->format('H')) }}"/>
										
										<div class="input-group-prepend input-group-append">
											<span class="input-group-text">:</span>
										</div>
										
										<input class="form-control has-spinner" type="number" name="time_min" id="time_min" min="0" max="59" value="{{ old('time_min') ? old('time_min') : ($isEightPM ? "00" : $datetime->format('i')) }}"/>

										<div class="input-group-append">
											<span class="input-group-text">Minute</span>
										</div>
									</div>
									<input type="hidden" name="reservation_time" id="reservation_time" value="{{ old('reservation_time') ? old('reservation_time') : "01:00" }}">
									<span class="text-danger">{{ $errors->first('reservation_time') }}</span>
								</div>

								{{-- DURATION --}}
								<div class="form-group col-12 col-lg-6">
									<label class="h5" for="duration">Extension</label>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">Hour</span>
										</div>
										
										<input class="form-control has-spinner text-right" type="number" name="duration_hour" id="duration_hour" min="0" max="4" value="{{ old('duration_hour') ? old('duration_hour') : '0' }}"/>
										
										<div class="input-group-prepend input-group-append">
											<span class="input-group-text">:</span>
										</div>
										
										<input class="form-control has-spinner" type="number" name="duration_min" id="duration_min" min="0" max="30" step="30" value="{{ old('duration_min') ? old('duration_min') : '00' }}"/>

										<div class="input-group-append">
											<span class="input-group-text">Minute</span>
										</div>
									</div>
									<input type="hidden" name="duration" id="duration" value="{{ old('duration') ? old('duration') : "01:00" }}">
									<span class="text-danger">{{ $errors->first('duration') }}</span>
								</div>
							</div>
						</div>

						{{-- MENU INFORMATION --}}
						<div class="card my-2">
							<h3 class="card-header d-flex flex-row">
								<span class="mr-auto">Menu Information</span>
								<button class="btn btn-primary ml-auto" type="button" id="addMenu"><i class="fas fa-plus-circle"></i></button>
							</h3>

							<div class="row card-body">
								{{-- MENU ITEMS --}}
								<div class="form-group col-12">
									<p class="h5">Menus</p>

									{{-- Dynamic form fields --}}
									<div class="row" id="menuField">
										@php ($index = 0)
										@if (old('menu'))
											{{-- Iterate through the old reservation item values --}}
											@foreach (old('menu') as $mi)
											<div class="col-12 col-md-4 my-2 position-relative" {{ $index == 0 ? 'id=origMenuForm' : ''}}>
												<div class="card h-100">
													<div class="card-body">
														<div class="form-group">
															<label class="form-label" for="menu[]">Menu Name</label><br>

															<select class="show-tick select-picker w-100" name="menu[]">
																<option data-hidden="true" value="0" {{ old("menu.{$index}") ? '' : 'selected' }}>Select</option>
																@foreach ($menus as $m)
																<option
																	value="{{ $m->id }}"
																	data-subtext="{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . "{$m->price} - {$m->getFromDuration("H")} " . Str::plural("hour", $m->getFromDuration("H")) . " and {$m->getFromDuration("i")} " . Str::plural("minute", $m->getFromDuration("i")) }}"
																	data-price="{{ $m->price }}"
																	data-duration="{{ $m->getFromDuration() }}"
																	>
																	{{ $m->item_name }}
																</option>
																@endforeach
															</select>

															<br><span class="text-danger validation">{{ $errors->first("menu.".old("new_menu_index")[$index]) }}</span>
														</div>
													</div>
												</div>

												@if ($index++ > 0)
												<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);">
													<button type="button" class="rounded btn btn-white border-secondary-light" onclick="$(this).parent().parent().remove();">
														<i class="fas fa-trash fa-sm text-danger"></i>
													</button>
												</div>
												@endif
											</div>
											@endforeach
											{{-- Reservation item iteration end --}}
										@else
										<div class="col-12 col-md-4 my-2 position-relative" id="origMenuForm">
											<div class="card h-100">
												<div class="card-body">
													<div class="form-group">
														<label class="form-label" for="menu[]">Menu Name</label><br>

														<select class="show-tick select-picker w-100" name="menu[]">
															<option data-hidden="true" value="0" selected>Select</option>
															@foreach ($menus as $m)
															<option
																value="{{ $m->id }}"
																data-subtext="{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . "{$m->price} - {$m->getFromDuration("H")} " . Str::plural("hour", $m->getFromDuration("H")) . " and {$m->getFromDuration("i")} " . Str::plural("minute", $m->getFromDuration("i")) }}"
																data-price="{{ $m->price }}"
																data-duration="{{ $m->getFromDuration() }}"
																>
																{{ $m->name }}
															</option>
															@endforeach
														</select>
														<br><span class="text-danger validation">{{ $errors->first('menu.0') }}</span>
													</div>
												</div>
											</div>
										</div>
										@endif
									</div>
								</div>
							</div>
						</div>

						{{-- CONTACT INFORMATION --}}
						<div class="card my-2">
							<h3 class="card-header d-flex flex-row">
								<span class="mr-auto">Contact Information</span>
								<button class="btn btn-primary ml-auto" type="button" id="addContact"><i class="fas fa-plus-circle"></i></button>
							</h3>
							
							<div class="row card-body">
								{{-- CONTACT ITEMS --}}
								<div class="form-group col-12">
									<p class="h5">Contacts</p>

									{{-- Dynamic form fields --}}
									<div class="row" id="contactField">
										@php ($index = 0)
										@if (old('contact'))
											{{-- TODO: CREATE THE CONTACT INFORMATION FORM --}}
											@foreach (old('contact_name') as $c)
											<div class="col-12 col-md-4 my-2 position-relative" {{ $index == 0 ? 'id=origContactForm' : '' }}>
												<div class="card h-100">
													<div class="card-body">
														<div class="form-group">
															<label for="contact_name[]" class="form-label">Contact Name</label>
															<input name="contact_name[]" class="form-control" type="text" value="{{ old("contact_name.{$index}") }}" />
															<span class="text-danger validation">{{ $errors->first("contact_name." . old("new_menu_index")[$index]) }}</span>
														</div>

														<div class="form-group">
															<label for="contact_email[]" class="form-label">Email</label>
															<input type="text" class="form-control" name="contact_email[]" value="{{ old(" contact_email.{$index}") }}" />
															<span class="text-danger validation">{{ $errors->first("contact_email." . old("new_menu_index")[$index]) }}</span>
														</div>
													</div>
												</div>

												@if ($index++ > 0)
												<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);">
													<button type="button" class="rounded btn btn-white border-secondary-light" onclick="$(this).parent().parent().remove();">
														<i class="fas fa-trash fa-sm text-danger"></i>
													</button>
												</div>
												@endif
											</div>
											@endforeach
										@else
										<div class="col-12 col-md-4 my-2 position-relative" id="origContactForm">
											<div class="card h-100">
												<div class="card-body">
													<div class="form-group">
														<label for="contact_name[]" class="form-label">Contact Name</label>
														<input name="contact_name[]" class="form-control" type="text" value="{{ old("contact_name.0") }}" />
														<span class="text-danger validation">{{ $errors->first("contact_name.0") }}</span>
													</div>

													<div class="form-group">
														<label for="contact_email[]" class="form-label">Email</label>
														<input type="text" class="form-control" name="contact_email[]" value="{{ old(" contact_email.0") }}" />
														<span class="text-danger validation">{{ $errors->first("contact_email.0") }}</span>
													</div>

													<div class="form-group">
														<label for="contact_number[]" class="form-label">Contact Number</label>
														<input type="text" class="form-control" name="contact_number[]" value="{{ old(" contact_email.0") }}" />
														<span class="text-danger validation">{{ $errors->first("contact_number.0") }}</span>
													</div>
												</div>
											</div>

											@if ($index++ > 0)
											<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);">
												<button type="button" class="rounded btn btn-white border-secondary-light" onclick="$(this).parent().parent().remove();">
													<i class="fas fa-trash fa-sm text-danger"></i>
												</button>
											</div>
											@endif
										</div>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="d-flex">
							<button class="btn btn-success ml-auto" type="submit" data-action="submit">Submit</button>
							<a href="javascript:void(0);" onclick="confirmLeave('@{{route('admin.reservation.index')}}');" class="btn btn-danger ml-3 mr-auto">Cancel</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('css')
<style type="text/css">
	/* Chrome, Safari, Edge, Opera */
	input:not(.has-spinner)::-webkit-outer-spin-button,
	input:not(.has-spinner)::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
	}
</style>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript">
	$(document).ready(() => {
		$(document).on('click', '#addMenu', (e) => {
			let obj = $(e.currentTarget);
			let field = $("#menuField");
			let orig = $('#origMenuForm');
			let clone = orig.clone();
			console.log(orig);

			// Clone cleaning
			clone.removeAttr('id');
			clone.find('.validation').text("");
			clone.find('textarea, input').val("");
			clone.find("select").removeAttr('data-last-value');
			$(clone.find('option').removeAttr('selected').prop('selected', false)[0]).prop('selected', true);
			clone.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
			clone.find(".select-picker").selectpicker({
				liveSearch: true,
				style: "btn-white border-secondary-light"
			});

			// Adding the remove item
			let removeBtn = $(`<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);"><button type="button" class="rounded btn btn-white border-secondary-light" onclick="$(this).parent().parent().remove();"><i class="fas fa-trash fa-sm text-danger"></i></button></div>`);
			clone.append(removeBtn);

			field.append(clone);
		});

		$(document).on('click', '#addContact', (e) => {
			let obj = $(e.currentTarget);
			let field = $("#contactField");
			let orig = $('#origContactForm');
			let clone = orig.clone();
			console.log(orig);

			// Clone cleaning
			clone.removeAttr('id');
			clone.find('.validation').text("");
			clone.find('textarea, input').val("");
			clone.find("select").removeAttr('data-last-value');
			$(clone.find('option').removeAttr('selected').prop('selected', false)[0]).prop('selected', true);
			clone.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
			clone.find(".select-picker").selectpicker({
				liveSearch: true,
				style: "btn-white border-secondary-light"
			});

			// Adding the remove item
			let removeBtn = $(`<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);"><button type="button" class="rounded btn btn-white border-secondary-light" onclick="$(this).parent().parent().remove();"><i class="fas fa-trash fa-sm text-danger"></i></button></div>`);
			clone.append(removeBtn);

			field.append(clone);
		});

		$('.select-picker').selectpicker({
			liveSearch: true,
			style: "btn-white border-secondary-light"
		});

		$(document).on('changed.bs.select', '.select-picker', (e) => {
			let obj = $(e.currentTarget);
			let price = $("#price");

			if (obj.val().length > 0) {
				if (obj.attr("data-last-value") == undefined) {
					price.val( parseFloat(price.val()) + parseFloat($(obj.find('option')[parseInt(obj.val())]).attr('data-price')) );
					obj.attr("data-last-value", $(obj.find('option')[parseInt(obj.val())]).attr('data-price'));
				}
				else {
					price.val( parseFloat(price.val()) - parseFloat(obj.attr("data-last-value")) + parseFloat($(obj.find('option')[parseInt(obj.val())]).attr('data-price')) );
					obj.attr("data-last-value", $(obj.find('option')[parseInt(obj.val())]).attr('data-price'));
				}
			}
		});

		$(document).on('keyup keydown keypress change click', '#time_min, #time_hour', (e) => {
			let hourE = $('#time_hour');
			let minE = $('#time_min');
			let timeE = $('#reservation_time');
			let time = `{{ now()->format('Y-m-d') }}T${`0${parseInt(hourE.val())}`.slice(-2)}:${`0${minE.val()}`.slice(-2)}:00Z`;

			let date = new Date(time);
			timeE.val(`${`0${date.getUTCHours()}`.slice(-2)}:${`0${date.getUTCMinutes()}`.slice(-2)}`);
		});

		$(document).on('keyup keydown keypress change click', '#duration_min, #duration_hour', (e) => {
			let hourE = $('#duration_hour');
			let minE = $('#duration_min');
			let durationE = $('#duration');
			let time = `{{ now()->format('Y-m-d') }}T${`0${parseInt(hourE.val())}`.slice(-2)}:${`0${minE.val()}`.slice(-2)}:00Z`;

			let date = new Date(time);
			durationE.val(`${`0${date.getUTCHours()}`.slice(-2)}:${`0${date.getUTCMinutes()}`.slice(-2)}`);
		});
	});
</script>
@endsection