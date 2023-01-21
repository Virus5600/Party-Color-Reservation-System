@extends('layouts.admin')

@section('title', 'Menu')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.menu.index')}}');" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Menu
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
					<form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
						{{ csrf_field() }}

						<div class="row">
							{{-- MENU NAME --}}
							<div class="form-group col-12 col-lg-4">
								<label class="h5" for="menu_name">Menu Name</label>
								<input class="form-control" type="text" name="menu_name" value="{{ old('menu_name') }}"/>
								<span class="text-danger">{{ $errors->first('menu_name') }}</span>
							</div>

							{{-- PRICE --}}
							<div class="form-group col-12 col-lg-4">
								<label for="price" class="h5">Price</label>

								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) }}</span>
									</div>
									<input type="number" class="form-control" min="0" max="4294967295" step=".25" name="price" value="{{ old('price') ? number_format(old('price'), 2, ".", "") : number_format(0, 2, ".", "") }}">
								</div>
								<span class="text-danger validation">{{ $errors->first('price') }}</span>
							</div>

							{{-- DURATION --}}
							<div class="form-group col-12 col-lg-4">
								<label class="h5" for="duration">Duration</label>

								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Hour</span>
									</div>
									
									<input class="form-control has-spinner text-right" type="number" name="duration_hour" id="duration_hour" min="0" max="12" value="{{ old('duration_hour') ? old('duration_hour') : "01" }}"/>
									
									<div class="input-group-prepend input-group-append">
										<span class="input-group-text">:</span>
									</div>
									
									<input class="form-control has-spinner" type="number" name="duration_min" id="duration_min" min="0" max="59" value="{{ old('duration_min') ? old('duration_min') : "00" }}"/>

									<div class="input-group-append">
										<span class="input-group-text">Minute</span>
									</div>
								</div>
								<input type="hidden" name="duration" id="duration" value="{{ old('duration') ? old('duration') : "01:00" }}">
								<span class="text-danger validation">{{ $errors->first('duration') }}</span>
							</div>

							{{-- MENU ITEMS --}}
							<div class="form-group col-12">
								<p class="h5">Items</p>

								{{-- Dynamic form fields --}}
								<div class="row" id="itemField">
									@php ($index = 0)
									@if (old('menu_item') || old('amount'))
										{{-- Iterate through the old menu item values --}}
										@foreach (old('menu_item') as $mi)
										<div class="col-12 col-md-4 my-2 position-relative" {{ $index == 0 ? 'id=origForm' : ''}}>
											<div class="card h-100">
												<div class="card-body">
													{{-- ITEM NAME --}}
													<div class="form-group">
														<label class="form-label" for="menu_item[]">Item Name</label><br>

														<select class="show-tick select-picker w-100" name="menu_item[]">
															<option data-hidden="true" value="0" {{ old("menu_item.{$index}") ? '' : 'selected' }}>Select</option>
															@foreach ($items as $i)
															<option value="{{ $i->id }}" data-subtext="{{ $i->measurement_unit }}" {{ old("menu_item.{$index}") == $i->id ? 'selected' : '' }}>{{ $i->item_name }}</option>
															@endforeach
														</select>

														<br><span class="text-danger validation">{{ $errors->first("menu_item.".old("new_index")[$index]) }}</span>
													</div>

													{{-- AMOUNT --}}
													<div class="form-group">
														<label for="amount[]" class="form-label">Amount</label>

														<div class="input-group">
															<div class="input-group-prepend">
																<button type="button" class="btn btn-secondary quantity-decrement"><i class="fas fa-minus"></i></button>
															</div>
															<input type="number" class="form-control" min="0" max="4294967295" step="0.01" name="amount[]" value="{{ old("amount.{$index}") ? old("amount.{$index}") : '0' }}" />
															<div class="input-group-append">
																<span class="input-group-text unit-of-measurement">{{ old("menu_item.{$index}") ? $items->where('id', '=', old("menu_item.{$index}"))->first()->measurement_unit : "kg"}}</span>
																<button type="button" class="btn btn-secondary quantity-increment"><i class="fas fa-plus"></i></button>
															</div>
														</div>

														<span class="text-danger validation">{{ $errors->first("amount.".old("new_index")[$index]) }}</span>
													</div>

													{{-- IS UNLI? --}}
													<div class="form-check">
														@if (old("is_unlimited.{$index}") == 1)
														<input type="checkbox" class="form-check-input unlimited" aria-label="Is unlimited?" name="is_unlimited[]" checked>
														<input type="hidden" value="0" name="is_unlimited[]" disabled>
														@else
														<input type="checkbox" class="form-check-input unlimited" aria-label="Is unlimited?" name="is_unlimited[]">
														<input type="hidden" value="0" name="is_unlimited[]">
														@endif
														<label class="form-check-label">Unlimited?</label>
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
										{{-- Menu item iteration end --}}
									@else
									<div class="col-12 col-md-4 my-2 position-relative" id="origForm">
										<div class="card h-100">
											<div class="card-body">
												{{-- ITEM NAME --}}
												<div class="form-group">
													<label class="form-label" for="menu_item[]">Item Name</label><br>

													<select class="show-tick select-picker w-100" name="menu_item[]">
														<option data-hidden="true" value="0" selected>Select</option>
														@foreach ($items as $i)
														<option value="{{ $i->id }}" data-subtext="{{ $i->measurement_unit }}">{{ $i->item_name }}</option>
														@endforeach
													</select>
													<br><span class="text-danger">{{ $errors->first('menu_item.0') }}</span>
												</div>

												{{-- AMOUNT --}}
												<div class="form-group">
													<label for="amount[]" class="form-label">Amount</label>

													<div class="input-group">
														<div class="input-group-prepend">
															<button type="button" class="btn btn-secondary quantity-decrement"><i class="fas fa-minus"></i></button>
														</div>
														<input type="number" class="form-control" min="0" max="4294967295" name="amount[]" step="0.01" value="{{ old('amount.0') ? old('amount.0') : '0' }}" />
														<div class="input-group-append">
															<span class="input-group-text unit-of-measurement">kg</span>
															<button type="button" class="btn btn-secondary quantity-increment"><i class="fas fa-plus"></i></button>
														</div>
													</div>
													<span class="text-danger">{{ $errors->first('amount.0') }}</span>
												</div>

												{{-- IS UNLI? --}}
												<div class="form-check">
													<input type="checkbox" class="form-check-input unlimited" aria-label="Is unlimited?" name="is_unlimited[]">
													<input type="hidden" value="0" name="is_unlimited[]">
													<label class="form-check-label">Unlimited?</label>
												</div>
											</div>
										</div>
									</div>
									@endif
								</div>
							</div>
						</div>

						<hr>

						<div class="d-flex">
							<button class="btn btn-success ml-auto" type="submit" data-action="submit">Submit</button>
							<button class="btn btn-primary ml-3" type="button" id="addItem">Add Item</button>
							<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.menu.index')}}');" class="btn btn-danger ml-3 mr-auto">Cancel</a>
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
		// Decrement
		$(document).on('click', '.quantity-decrement:not(.disabled)', (e, elm) => {
			let obj = $(e.currentTarget);
			obj.parent().parent().find('[name="amount[]"]:not([readonly])').trigger('change', ['-', elm]);
			
			if (obj.hasClass('disabled') && parseInt(obj.attr('data-id')) !== NaN) {
				clearInterval(parseInt(obj.attr('data-id')));
				obj.removeAttr('data-id');
			}
		}).on('mousedown', '.quantity-decrement:not(.disabled)', (e) => {
			let obj = $(e.currentTarget);
			let id = setInterval(() => {obj.trigger('click')}, 100);
			obj.attr('data-id', id);
		}).on('mouseup mouseleave', '.quantity-decrement:not(.disabled)', (e) => {
			let obj = $(e.currentTarget);
			let id = parseInt(obj.attr('data-id'));
			clearInterval(id);
			obj.removeAttr('data-id');
		});

		// Increment
		$(document).on('click', '.quantity-increment:not(.disabled)', (e, elm) => {
			let obj = $(e.currentTarget);
			obj.parent().parent().find('[name="amount[]"]:not([readonly])').trigger('change', ['+', elm]);

			if (obj.hasClass('disabled') && parseInt(obj.attr('data-id')) !== NaN) {
				clearInterval(parseInt(obj.attr('data-id')));
				obj.removeAttr('data-id');
			}
		}).on('mousedown', '.quantity-increment:not(.disabled)', (e) => {
			let obj = $(e.currentTarget);
			let id = setInterval(() => {obj.trigger('click')}, 100);
			obj.attr('data-id', id);
		}).on('mouseup mouseleave', '.quantity-increment:not(.disabled)', (e) => {
			let obj = $(e.currentTarget);
			let id = parseInt(obj.attr('data-id'));
			clearInterval(id);
			obj.removeAttr('data-id');
		});

		// Amount Update
		$(document).on('change', '[name="amount[]"]:not([readonly])', (e, operation, elm) => {
			let obj = $(e.currentTarget);
			let val = parseInt(obj.val());

			if (val < 4294967295 && operation == '+') {
				obj.val(++val);
			}
			else if (val > 0 && operation == '-') {
				obj.val(--val);
			}

			// Increment
			if (val >= 4294967295) {
				$(obj.parent().find('.quantity-increment')).addClass('disabled');
				obj.val(4294967295);

				if (typeof elm != 'undefined') {
					let id = parseInt(elm.attr('data-id'));
					clearInterval(id);
				}
			}
			else
				$(obj.parent().find('.quantity-increment')).removeClass('disabled');

			// Decrement
			if (val <= 0) {
				$(obj.parent().find('.quantity-decrement')).addClass('disabled');
				obj.val(0);

				if (typeof elm != 'undefined') {
					let id = parseInt(elm.attr('data-id'));
					clearInterval(id);
				}
			}
			else
				$(obj.parent().find('.quantity-decrement')).removeClass('disabled');
		}).trigger('change');

		// Adding Item
		$(document).on('click', '#addItem', (e) => {
			let obj = $(e.currentTarget);
			let field = $("#itemField");
			let orig = $('#origForm');
			let clone = orig.clone();

			// Clone cleaning
			clone.removeAttr('id');
			clone.find(".validation").text("").trigger("change");
			clone.find('.unit-of-measurement').text("kg").trigger("change");
			clone.find('textarea, input').val("").trigger("change");
			clone.find('input[name="amount[]"]').val(0).trigger("change");
			clone.find('input[type=checkbox]').prop('checked', false).trigger("change");
			clone.find('input[type=hidden][name="is_unlimited[]"]').val("0").trigger("change");
			$(clone.find('option').removeAttr('selected').prop('selected', false)[0]).prop('selected', true).trigger("change");
			clone.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
			clone.find(".select-picker").selectpicker({
				liveSearch: true,
				style: "btn-white border-secondary-light"
			});

			// Adding the remove item
			let removeBtn = $(`<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);"><button type="button" class="rounded btn btn-white border-secondary-light" onclick="$(this).parent().parent().remove();"><i class="fas fa-trash fa-sm text-danger"></i></button></div>`);
			clone.append(removeBtn);

			field.append(clone);
			clone.find('input[type=checkbox]').trigger('change');
		});

		// Selectpicker
		$('.select-picker').selectpicker({
			liveSearch: true,
			style: "btn-white border-secondary-light"
		});

		$(document).on('changed.bs.select', '.select-picker', (e) => {
			let obj = $(e.currentTarget);
			let unitOfMeasurement = obj.closest('div.card-body').find('.unit-of-measurement');

			if (obj.val().length > 0)
				unitOfMeasurement.text($(obj.find('option')[parseInt(obj.val())]).attr('data-subtext'));
		});

		// Duration update
		$(document).on('keyup keydown keypress change click', '#duration_min, #duration_hour', (e) => {
			let hourE = $('#duration_hour');
			let minE = $('#duration_min');
			let durationE = $('#duration');
			let time = `{{ now()->format('Y-m-d') }}T${`0${parseInt(hourE.val())}`.slice(-2)}:${`0${minE.val()}`.slice(-2)}:00Z`;

			let date = new Date(time);
			durationE.val(`${`0${date.getUTCHours()}`.slice(-2)}:${`0${date.getUTCMinutes()}`.slice(-2)}`);
		});

		// Unlimited toggle
		$(document).on('change', '.unlimited', (e) => {
			let obj = $(e.currentTarget);
			let parent = obj.closest('.card-body');

			if (obj.prop('checked')) {
				obj.attr("value", "1");
				parent.find('[name="amount[]"]').prop("readonly", true);
				parent.find('[name="is_unlimited[]"][type=hidden]').prop("disabled", true);
			}
			else {
				obj.removeAttr("value");
				parent.find('[name="amount[]"]').prop("readonly", false);
				parent.find('[name="is_unlimited[]"][type=hidden]').prop("disabled", false);
			}
		})
		$('.unlimited').trigger('change');
	});
</script>
@endsection