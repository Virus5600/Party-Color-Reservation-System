@extends('layouts.admin')

@section('title', 'Menu')

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>Menu</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row">
						{{-- ADD --}}
						@if (Auth::user()->hasPermission('announcements_tab_create'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="{{ route('admin.menu.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Item</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'menus'])
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
					<th class="text-center">Menu Name</th>
					<th class="text-center">Status</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($menus as $m)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $m->name }}</td>
					<td class="text-center align-middle mx-auto"><i class="fas fa-circle {{ $m->trashed() ? 'text-info' : 'text-success' }} mr-2"></i>{{ $m->trashed() ? 'Inactive' : 'Active'}}</td>
					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$m->id}}" aria-haspopup="true" aria-expanded="false">
								Action
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$m->id}}">
								{{-- SHOW --}}
								<a href="{{ route('admin.menu.show', [$m->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>

								{{-- EDIT --}}
								@if (Auth::user()->hasPermission('menu_tab_edit'))
								<a href="@{{ route('admin.menu.edit', [$m->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
								@endif
								
								{{-- DELETE --}}
								@if (Auth::user()->hasPermission('menu_tab_delete'))
									@if ($m->trashed())
									<a href="javascript:void(0);" onclick="confirmLeave('@{{ route('admin.menu.restore', [$m->id]) }}', undefined, 'Are you sure you want to activate this?');" class="dropdown-item"><i class="fas fa-toggle-on mr-2"></i>Set Active</a>
									@else
									<a href="javascript:void(0);" onclick="confirmLeave('@{{ route('admin.menu.delete', [$m->id]) }}', undefined, 'Are you sure you want to deactivate this?');" class="dropdown-item"><i class="fas fa-toggle-off mr-2"></i>Set Inactive</a>
									@endif
								@endif

								{{-- PERMANENT DELETE --}}
								@if (Auth::user()->hasPermission('menu_tab_perma_delete'))
								<a href="javascript:void(0);" onclick="confirmLeave('@{{ route('admin.menu.permaDelete', [$m->id]) }}', undefined, 'Are you sure you want to delete this?')" class="dropdown-item"><i class="fas fa-trash mr-2"></i>Delete</a>
								@endif
							</div>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="3">Nothing to display~</td>
				</tr>
				@endforelse
			</tbody>
		</table>
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