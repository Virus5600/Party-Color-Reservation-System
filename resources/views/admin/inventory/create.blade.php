@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.inventory.index')}}');" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Inventory
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="mx-auto" style="max-width: 25rem !important; flex: 0 0 25rem;">
			<div class="card dark-shadow mb-5" id="inner-content">
				<div class="card-body">
					<form action="{{ route('admin.inventory.store') }}" method="POST" enctype="multipart/form-data">
						{{ csrf_field() }}

						<div class="row">
							{{-- ITEM NAME --}}
							<div class="form-group col-12">
								<label class="h5" for="title">Item Name</label>
								<input class="form-control" type="text" name="item_name" value="{{ old('item_name') }}"/>
							</div>

							{{-- QUANTITY --}}
							<div class="form-group col-12">
								<label class="h5" for="title">Quantity</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<button type="button" class="btn btn-secondary" onclick="$(this).parent().parent().find('[name=quantity]').trigger('change', ['-']);"><i class="fas fa-minus"></i></button>
									</div>
									<input class="form-control" type="number" min="0" max="4294967295" name="quantity" value="{{ old('quantity') ? old('quantity') : '0' }}"/>
									<div class="input-group-append">
										<button type="button" class="btn btn-secondary" onclick="$(this).parent().parent().find('[name=quantity]').trigger('change', ['+']);"><i class="fas fa-plus"></i></button>
									</div>
								</div>
							</div>

							{{-- UNIT OF MEASUREMENT --}}
							<div class="form-group col-12">
								<label class="h5" for="measurement_unit">Unit of Measurement</label>
								<input class="form-control" type="text" name="measurement_unit" value="{{ old('measurement_unit') }}"/>
							</div>

							{{-- IS ACTIVE --}}
							<div class="form-group col-6 my-auto">
								<div class="custom-control custom-switch custom-switch-md my-auto">
									<input type='checkbox' class="custom-control-input image-input-switch" name="is_active" id="is_active" />
									<label class="custom-control-label pt-1 pl-3" for="is_active">Set Active?</label>
								</div>
							</div>
						</div>

						<button class="btn btn-success ml-auto" type="submit" data-action="submit">Submit</button>
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.inventory.index')}}');" class="btn btn-danger ml-3 mr-auto">Cancel</a>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/custom-switch.css') }}" />
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

		$('[name=quantity]').on('change', (e, operation) => {
			let obj = $(e.currentTarget);
			let val = parseInt(obj.val());

			if (typeof operation == 'undefined')
				return;

			if (val < 4294967295 && operation == '+')
				obj.val(++val);
			else if (val > 0 && operation == '-')
				obj.val(--val);
		});

		let measurementUnit = [
			@foreach ($measurement_unit as $mu)
			'{{ $mu }}',
			@endforeach
		];

		$('[name=measurement_unit]').autocomplete({
			source: measurementUnit,
			minLength: 0,
			delay: 0
		}).on('click focus', (e) => {
			$(e.currentTarget).autocomplete('search', $(e.currentTarget).val());
		});
	});
</script>
@endsection