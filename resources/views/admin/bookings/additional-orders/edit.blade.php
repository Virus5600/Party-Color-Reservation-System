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
						<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.bookings.additional-orders.index', [$booking->id]) }}');" class="text-dark text-decoration-none font-weight-normal">
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
					<form action="{{ route('admin.bookings.additional-orders.update', [$booking->id, $additionalOrder->id]) }}" method="POST" enctype="multipart/form-data" class="form needs-validation" data-continuous-validation="false">
						{{-- HIDDEN FIELDS --}}
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
							<div class="card-header d-flex">
								<h3 class="my-auto">Additional Order</h3>
							</div>

							<div class="row card-body">
								{{-- PRICE --}}
								<div class="form-group col-12 col-lg-6">
									<label for="price" class="h5">Price</label>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) }}</span>
										</div>
										<input type="number" readonly class="form-control" min="0" max="4294967295" step=".25" name="price" id="price" value="{{ number_format($additionalOrder->price, 2, ".", "") }}" required />
									</div>
									<span class="text-danger text-wrap">{{ $errors->first('price') }}</span>
								</div>
							
								{{-- EXTENSION --}}
								<div class="form-group col-12 col-lg-6">
									<label class="h5" for="extension">Extension</label>
									<div class="input-group">
										<input class="form-control has-spinner" type="number" name="extension" id="extension" value="{{ $additionalOrder->extension }}" min="0" max="4" step="0.5" required>
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
							<h3 class="card-header d-flex flex-row justify-content-between">
								<span class="mr-auto">Menu Information</span>
								<button class="btn btn-primary" type="button" id="addMenu" title="Add menu order"><i class="fas fa-plus-circle"></i></button>
							</h3>

							<div class="row card-body">
								{{-- MENU ITEMS --}}
								<div class="form-group col-12">
									<p class="h5">Menus</p>

									{{-- Dynamic form fields --}}
									<div class="row" id="menuField">
										{{-- IF THERE ARE MULTIPLE OR OLD VALUES --}}
										@php ($index = 0)
										@foreach ($additionalOrder->bookingMenus as $om)
										{{-- ORIGINAL --}}
										<div class="col-12 col-lg-4 my-2 position-relative" {{ $index == 0 ? 'id=origMenuForm' : '' }}>
											<div class="card h-100">
												<div class="card-body">
													<div class="form-group">
														<label class="form-label" for="menu">Menu Name</label><br>

														<select class="show-tick select-picker w-100 form-control" name="menu[]" required>
															@foreach ($menus as $m)
															<option
																value="{{ $m->id }}"
																data-subtext="{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . "{$m->price} - {$m->getFromDuration("H")} " . Str::plural("hour", $m->getFromDuration("H")) . " and {$m->getFromDuration("i")} " . Str::plural("minute", $m->getFromDuration("i")) }}"
																data-price="{{ $m->price }}"
																data-duration="{{ $m->getFromDuration() }}"
																{{ $om->menu->name == $m->name ? 'selected' : '' }}
																>
																{{ $m->name }}
															</option>
															@endforeach
														</select>
														<br><span class="text-danger text-wrap validation">{{ $errors->first("menu.{$index}") }}</span>
													</div>

													<div class="form-group">
														<label for="count" class="form-label">Count</label>
														<input type="number" min="1" class="form-control" name="count[]" value="{{ $om->count }}" required>
														<span class="text-danger text-wrap validation">{{ $errors->first("count.{$index}") }}</span>
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
										{{-- ORIGINAL END --}}
										@endforeach

										@for (; $index < count(old("menu") == null ? [] : old("menu")); $index++)
										<div class="col-12 col-lg-4 my-2 position-relative">
											<div class="card h-100">
												<div class="card-body">
													<div class="form-group">
														<label class="form-label" for="menu">Menu Name</label><br>

														<select class="show-tick select-picker w-100 form-control" name="menu[]" required>
															@foreach ($menus as $m)
															<option
																value="{{ $m->id }}"
																data-subtext="{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . "{$m->price} - {$m->getFromDuration("H")} " . Str::plural("hour", $m->getFromDuration("H")) . " and {$m->getFromDuration("i")} " . Str::plural("minute", $m->getFromDuration("i")) }}"
																data-price="{{ $m->price }}"
																data-duration="{{ $m->getFromDuration() }}"
																{{ old("menu.{$index}") == $m->name ? 'selected' : '' }}
																>
																{{ $m->name }}
															</option>
															@endforeach
														</select>
														<br><span class="text-danger text-wrap validation">{{ $errors->first("menu.{$index}") }}</span>
													</div>

													<div class="form-group">
														<label for="count" class="form-label">Count</label>
														<input type="number" min="1" class="form-control" name="count[]" value="{{ old("count.{$index}") }}" required>
														<span class="text-danger text-wrap validation">{{ $errors->first("count.{$index}") }}</span>
													</div>
												</div>
											</div>

											<div class="position-absolute d-flex flex-row" style="top: calc(0rem); right: calc(1rem - 1px);">
												<button type="button" class="rounded btn btn-white border-secondary-light" title="Remove contact information" onclick="$(this).parent().parent().remove();">
													<i class="fas fa-trash fa-sm text-danger"></i>
												</button>
											</div>
										@endfor
									</div>
								</div>
							</div>
						</div>

						<div class="d-flex">
							<button class="btn btn-success ml-auto" type="submit" data-action="submit" id="submit">Submit</button>
							<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.bookings.additional-orders.index', [$booking->id]) }}');" class="btn btn-danger ml-3 mr-auto">Cancel</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript">
	$(document).ready(() => {
		// Add menu
		$(document).on('click', '#addMenu', (e) => {
			let obj = $(e.currentTarget);
			let field = $("#menuField");
			let orig = $('#origMenuForm');
			let clone = orig.clone();

			// Clone cleaning
			clone.removeAttr('id');
			clone.find('.validation').text("");
			clone.find('textarea, input').val("").removeAttr("value");
			clone.find("select").removeAttr('data-last-value');
			clone.find(".is-invalid, .is-valid").removeClass('is-valid is-invalid');
			$(clone.find('option').removeAttr('selected').prop('selected', false)[0]).prop('selected', true);
			clone.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
			clone.find(".select-picker").selectpicker({
				liveSearch: true,
				style: "btn-white border-secondary-light"
			});
			clone.find(".select-picker").find("input[type=search]").addClass("dont-validate");


			let dataMin = orig.attr("data-min-1");
			if (typeof dataMin == 'undefined')
				dataMin = 'false';

			if (dataMin.toLowerCase() == 'true') {
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
			$(document).on('changed.bs.select', '[name="menu[]"]', (e) => {
				let price = $("#price");
				let menu = $("[name='menu[]']").get();
				let pax = $("[name='count[]']").get();
				let extension = $("#extension");

				if (menu.length > 0) {
					for (let index in menu) {
						let m = $(menu[index]);
						let p = $(pax[index]);

						let subtotal = parseFloat($($(m).find(`option[value="${m.val()}"]`)[0]).attr('data-price')) * p.val();

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
			$(document).on('change keyup keypress', '[name="count[]"], #extension', (e) => { $("[name='menu[]']").trigger('changed.bs.select') }).trigger('change');
		}
	});
</script>
@endsection