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
							<div class="form-group col-12 col-lg-6">
								<label class="h5" for="menu_name">Menu Name</label>
								<input class="form-control" type="text" name="menu_name" value="{{ old('menu_name') }}"/>
								<span class="text-danger">{{ $errors->first('menu_name') }}</span>
							</div>

							{{-- PRICE --}}
							<div class="form-group col-12 col-lg-6">
								<label for="price" class="h5">Price</label>

								<div class="input-group"></div>
								<input type="number" class="form-control" min="0" max="4294967295" value="{{ old('price') ? old('price') : 0 }}">
								<span class="text-danger">{{ $errors->first('price') }}</span>
							</div>

							{{-- MENU ITEMS --}}
							<div class="form-group col-12">
								<p class="h5">Items</p>

								{{-- Dynamic form fields --}}
								<div class="row" id="itemField">
									<div class="col-12 col-md-4 my-2 position-relative" id="origForm">
										<div class="card">
											<div class="card-body">
												<div class="form-group">
													<label class="form-label" for="menu_item[]">Item Name</label><br>

													<select class="show-tick select-picker w-100" name="menu_item[]">
														<option data-hidden="true" value="0" selected>Select</option>
														@foreach ($items as $i)
														<option value="{{ $i->id }}" data-subtext="{{ $i->measurement_unit }}">{{ $i->item_name }}</option>
														@endforeach
													</select>
												</div>

												<div class="form-group">
													<label for="amount[]" class="form-label">Amount</label>

													<div class="input-group">
														<div class="input-group-prepend">
															<button type="button" class="btn btn-secondary quantity-decrement"><i class="fas fa-minus"></i></button>
														</div>
														<input type="number" class="form-control" min="0" max="4294967295" name="amount[]" value="{{ old('amount.1') ? old('amount.1') : '0' }}" />
														<div class="input-group-append">
															<span class="input-group-text unit-of-measurement">kg</span>
															<button type="button" class="btn btn-secondary quantity-increment"><i class="fas fa-plus"></i></button>
														</div>
													</div>
													<span class="text-danger">{{ $errors->first('amount.1') }}</span>
												</div>
											</div>
										</div>
									</div>
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
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
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
		$("#is_active").prop("checked", {{ old('is_active') or "true" }});

		$(document).on('click', '.quantity-decrement:not(.disabled)', (e, elm) => {
			$(e.currentTarget).parent().parent().find('[name="amount[]"]').trigger('change', ['-', elm]);
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

		$(document).on('click', '.quantity-increment:not(.disabled)', (e, elm) => {
			$(e.currentTarget).parent().parent().find('[name="amount[]"]').trigger('change', ['+', elm]);
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

		$(document).on('change', '[name="amount[]"]', (e, operation, elm) => {
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

		$('#addItem').on('click', (e) => {
			let obj = $(e.currentTarget);
			let field = $("#itemField");
			let orig = $('#origForm');
			let clone = orig.clone();

			// Clone cleaning
			clone.removeAttr('id');
			clone.find('.unit-of-measurement').text("kg");
			clone.find('textarea, input').val("");
			clone.find('input[name="amount[]"]').val(0);
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
			let unitOfMeasurement = obj.closest('div.card-body').find('.unit-of-measurement');

			if (obj.val().length > 0)
				unitOfMeasurement.text($(obj.find('option')[parseInt(obj.val())]).attr('data-subtext'));
		});
	});
</script>
@endsection