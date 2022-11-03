@extends('layouts.admin')

@section('title', '発行')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.inventory.index')}}');" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>発表
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
						{{csrf_field()}}
						{{-- ITEM NAME --}}
						<div class="form-group">
							<label class="h5" for="title">Item Name</label>
							<input class="form-control" type="text" name="item_name" value="{{ old('item_name') }}"/>
						</div>
						{{-- QUANTITY --}}
						<div class="form-group">
							<label class="h5" for="title">Quantity</label>
							<input class="form-control" type="number" min="0" max="4294967295" name="quantity" value="{{ old('quantity') }}"/>
						</div>
						{{-- IS ACTIVE --}}
						<div class="form-group my-auto">
							<div class="custom-control custom-switch custom-switch-md">
								<input type='checkbox' class="custom-control-input image-input-switch" name="is_active" id="is_active" />
								<label class="custom-control-label pt-1 pl-3" for="is_active">Set Active?</label>
							</div>
						</div>

						<hr class="hr-thick">

						<div class="row py-3">
							<button class="btn btn-success ml-auto" type="submit" data-action="submit">Submit</button>
							<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.inventory.index')}}');" class="btn btn-danger ml-3 mr-auto">Cancel</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/custom-switch.css') }}" />
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript">
    $(document).ready(() => { $("#is_active").prop("checked", {{ old('is_active') or "true" }}); });
</script>
@endsection