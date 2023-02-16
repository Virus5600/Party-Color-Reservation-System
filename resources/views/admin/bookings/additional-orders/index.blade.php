@extends('layouts.admin')

@section('title', 'Additional Orders')

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4">
					<h1>
						<a href="{{ route('admin.bookings.index') }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Additional Orders
						</a>
					</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row">
						{{-- ADD --}}
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="{{ route('admin.bookings.additional-orders.create', [$booking_id]) }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Orders</a>
						</div>

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'additional-orders'])
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="d-flex mb-2">
		<span class="my-auto">
			<i class="fas fa-square mr-2 text-danger"></i>Voided
		</span>
	</div>

	<div class="card dark-shadow overflow-x-scroll flex-fill mb-3" id="inner-content">
		<table class="table table-striped my-0">
			<thead>
				<tr>
					<th class="text-center">Item Name</th>
					<th class="text-center">Price</th>
					<th class="text-center">Extension</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($additional_orders as $o)
				<tr class="enlarge-on-hover {{ $o->trashed() ? 'bg-danger text-white' : '' }}">
					<td class="text-center align-middle mx-auto font-weight-bold">
						@php ($menus = [])
						@foreach ($o->menus as $i => $m)
							@if ($i < 3)
								@php (array_push($menus, $m->name))
							@else
								@break
							@endif
						@endforeach

						{{ implode(", ", $menus) }}
					</td>
					
					<td class="text-center align-middle mx-auto">{{ $o->fetchPrice() }}</td>
					<td class="text-center align-middle mx-auto">{{ $o->extension * 60 }} min</td>
					
					<td class="text-center align-middle">
						@if (!$o->trashed())
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$o->id}}" aria-haspopup="true" aria-expanded="false">
								Action
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$o->id}}">
								{{-- SHOW --}}
								<a href="{{ route('admin.bookings.additional-orders.show', [$booking_id, $o->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>Show</a>
								{{-- EDIT --}}
								<a href="{{ route('admin.bookings.additional-orders.edit', [$booking_id, $o->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
								{{-- DELETE --}}
								<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.bookings.additional-orders.void', [$booking_id, $o->id]) }}', undefined, 'Are you sure you want to deactivate this?');" class="dropdown-item"><i class="fas fa-ban mr-2"></i>Void Order</a>
							</div>
						</div>
						@else  
						<a href="{{ route('admin.bookings.additional-orders.show', [$booking_id, $o->id]) }}" class="btn btn-light"><i class="fas fa-eye mr-2"></i>Show</a>
						@endif
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="4">Nothing to display~</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
@endsection