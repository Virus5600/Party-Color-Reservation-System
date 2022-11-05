@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>発表</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row">
						{{-- ADD --}}
						@if (Auth::user()->hasPermission('announcements_tab_create'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="{{ route('admin.inventory.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Item</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'inventories'])
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

	<div class="card dark-shadow overflow-x-scroll flex-fill mb-3" id="inner-content">
		<table class="table table-striped my-0">
			<thead>
				<tr>
					<th class="text-center">Item Name</th>
					<th class="text-center">Quantity</th>
					<th class="text-center">Status</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($items as $i)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $i->item_name }}</td>
					<td class="text-center align-middle mx-auto">{{ $i->quantity }}</td>
					<td class="text-center align-middle mx-auto"><i class="fas fa-circle {{ $i->trashed() ? 'text-info' : 'text-success' }} mr-2"></i>{{ $i->trashed() ? 'Inactive' : 'Active'}}</td>
					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$i->id}}" aria-haspopup="true" aria-expanded="false">
								Action
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$i->id}}">

								{{-- EDIT --}}
								@if (Auth::user()->hasPermission('inventory_tab_edit'))
								<a href="{{ route('admin.inventory.edit', [$i->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
								@endif
								
								{{-- DELETE --}}
								@if (Auth::user()->hasPermission('inventory_tab_delete'))
									@if ($i->trashed())
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.inventory.restore', [$i->id]) }}', undefined, 'Are you sure you want to activate this?');" class="dropdown-item"><i class="fas fa-recycle mr-2"></i>Set Active</a>
									@else
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.inventory.delete', [$i->id]) }}', undefined, 'Are you sure you want to deactivate this?');" class="dropdown-item"><i class="fas fa-trash mr-2"></i>Set Inactive</a>
									@endif
								@endif

								{{-- PERMANENT DELETE --}}
								@if (Auth::user()->hasPermission('inventory_tab_perma_delete'))
								<a onclick="confirmLeave('{{ route('admin.inventory.permaDelete', [$i->id]) }}', undefined, 'Are you sure you want to delete this?')" class="dropdown-item"><i class="fas fa-fire-alt mr-2"></i>Delete</a>
								@endif
							</div>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="5">Nothing to display~</td>
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