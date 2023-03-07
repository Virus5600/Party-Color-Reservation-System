@extends('layouts.admin')

@section('title', 'Inventory')

@php
$user = auth()->user();
$editAllow = $user->hasPermission('inventory_tab_edit');
$deleteAllow = $user->hasPermission('inventory_tab_delete');
@endphp

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>Inventory</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row">
						{{-- ADD --}}
						@if ($user->hasPermission('inventory_tab_create'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="{{ route('admin.inventory.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Item</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'inventory'])
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

	<div class="card dark-shadow overflow-x-scroll flex-fill mb-3 h-100 d-flex flex-column" id="inner-content">
		<table class="table table-striped my-0">
			<thead>
				<tr>
					<th class="text-center">Item Name</th>
					<th class="text-center">In Stock</th>
					<th class="text-center">Status</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content" class="h-100">
				@forelse ($items as $i)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $i->item_name }}</td>
					<td class="text-center align-middle mx-auto">{{ $i->getInStock() }}</td>
					<td class="text-center align-middle mx-auto"><i class="fas fa-circle {{ $i->trashed() ? 'text-danger' : ($i->quantity > $i->critical_level ? 'text-success' : 'text-warning') }} mr-2"></i>{{ $i->trashed() ? 'Inactive' : ($i->quantity > $i->critical_level ? 'Active' : 'Critical') }}</td>
					<td class="text-center align-middle">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$i->id}}" aria-haspopup="true" aria-expanded="false">
								Action
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$i->id}}">
								{{-- SHOW --}}
								<a href="{{ route('admin.inventory.show', [$i->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>

								{{-- EDIT --}}
								@if ($editAllow)
								<a href="{{ route('admin.inventory.edit', [$i->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
								@endif
								
								{{-- DELETE --}}
								@if ($deleteAllow)
									@if ($i->trashed())
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.inventory.restore', [$i->id]) }}', undefined, 'Are you sure you want to activate this?');" class="dropdown-item"><i class="fas fa-toggle-on mr-2"></i>Set Active</a>
									@else
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.inventory.delete', [$i->id]) }}', undefined, 'Are you sure you want to deactivate this?');" class="dropdown-item"><i class="fas fa-toggle-off mr-2"></i>Set Inactive</a>
									@endif
								@endif
							</div>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="4">Nothing to display~</td>
				</tr>
				@endforelse
			</tbody>
		</table>

		<div id="table-paginate" class="w-100 d-flex align-middle my-3">
			{{ $items->onEachSide(5)->links() }}
		</div>
	</div>
</div>
@endsection

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/swal-change-field.js') }}"></script>
@endsection