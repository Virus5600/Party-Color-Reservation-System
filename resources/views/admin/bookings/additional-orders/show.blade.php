@extends('layouts.admin')

@section('title', 'Additional Orders')

@section('content')

@php
$extensionFee = App\Settings::getValue('extension_fee');
$currencySign = (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
@endphp

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
						@foreach($additionalOrder->orderable as $om)
						<li class="list-group-item {{ $additionalOrder->trashed() ? "bg-danger border-light" : "" }}">
							<span class="float-left">{{ $om->menuVariation->name }}</span>
							<span class="float-right">&times;{{ $om->count }}</span>
						</li>
						@endforeach
					</ul>

					<hr class="hr-thick">

					<p class="h5">Extension:</p>
					<ul style="list-style-type: none;">
						<li class="d-flex flex-row flex-wrap justify-content-between">
							<span>{{ $additionalOrder->extension }} {{ Str::plural("hr", $additionalOrder->extension) }} ({{ $additionalOrder->extension * 60 }} {{ Str::plural("min", ($additionalOrder->extension * 60)) }})</span>
							<span>{{ "{$currencySign}" . ($additionalOrder->extension * $extensionFee) }}</span>
						</li>
					</ul>

					<hr class="hr-thick">

					<p class="h5 d-flex justify-content-between">
						<span class="font-weight-bold">Total:</span>
						<span>{{ $additionalOrder->fetchPrice(false) }}</span>
					</p>
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