@extends('layouts.admin')

@section('title', 'Additional Orders')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="{{ route('admin.bookings.additional-orders.index', [$booking_id]) }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Additional Orders
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="mx-auto col-10 col-sm-8 col-md-6 col-lg-4">
			<div class="card {{ $additionalOrder->trashed() ? "bg-danger text-white" : "" }} dark-shadow mb-5" id="inner-content">
				<h3 class="card-header text-center">
					<span class="d-block text-wrap">Additional Order from #{{ $additionalOrder->booking->control_no }}</span>
				</h3>
				
				<div class="card-body">
					<p class="h5 d-flex justify-content-between">
						<span>Orders:</span>
						<span>{{ $additionalOrder->fetchPrice() }}</span>
					</p>

					<ul class="list-group">
						@foreach($additionalOrder->bookingMenus as $bm)
						<li class="list-group-item {{ $additionalOrder->trashed() ? "bg-danger border-light" : "" }}">
							<span class="float-left">{{ $bm->menu->name }}</span>
							<span class="float-right">&times;{{ $bm->count }}</span>
						</li>
						@endforeach
					</ul>
				</div>
				
				<div class="card-footer text-center">
					@if (!$additionalOrder->trashed())
					<a href="{{ route('admin.bookings.additional-orders.edit', [$booking_id, $additionalOrder->id]) }}" class="btn btn-primary ml-3">Edit</a>
					@endif

					<a href="{{ route('admin.bookings.additional-orders.index', [$booking_id]) }}" class="btn btn-{{ $additionalOrder->trashed() ? "light" : "secondary" }} ml-3">Go Back</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection