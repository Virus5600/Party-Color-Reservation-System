@extends('layouts.admin')

@section('title', 'Types')

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>(Role) Types</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row px-3">
						{{-- ADD --}}
						@if (Auth::user()->hasPermission('users_tab_create'))
						<div class="text-center text-md-right ml-md-auto mr-3">
							<a href="{{ route('admin.types.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Type</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'types'])
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
					<th class="text-center">Role Type</th>
					<th class="text-center">Number of Users</th>
					<th class="text-center">Number of Permissions</th>
					<th class="text-center">Status</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($types as $t)
				<tr class="enlarge-on-hover" id="tr-{{ $t->id }}">
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $t->name }}</td>
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $t->users_count }}</td>
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $t->permissions_count }}/{{ $totalPerms }} ({{ number_format(($t->permissions_count/$totalPerms) * 100, 2) }}%)</td>
					
					<td class="text-center align-middle mx-auto font-weight-bold">
						@if ($t->trashed())
							<i class="fas fa-circle mr-2 text-danger"></i>Inactive
						@else
							<i class="fas fa-circle mr-2 text-success"></i>Active
						@endif
					</td>

					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{ $t->id }}" aria-haspopup="true" aria-expanded="false">
								Actions
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{ $t->id }}">
								{{-- SHOW --}}
								<a href="{{ route('admin.types.show', [$t->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>


								{{-- EDIT --}}
								@if (Auth::user()->hasPermission('types_tab_edit'))
								<button class="dropdown-item"
									data-scf="New name..."
									data-scf-name="name"
									data-scf-target-uri="{{ route('admin.types.update', [$t->id]) }}"
									data-scf-custom-title="Change Name"
									data-scf-disable-button="true"
									data-scf-label="New name of (role) type"
									data-scf-reload="true">
									<i class="fas fa-pencil-alt mr-2"></i>Edit
								</button>
								@endif

								{{-- PERMISSIONS --}}
								@if (Auth::user()->hasPermission('types_tab_permissions'))
								<a href="{{ route('admin.types.manage-permissions', [$t->id]) }}" class="dropdown-item"><i class="fas fa-user-lock mr-2"></i>Manage Permissions</a>
								@endif

								{{-- STATUS [DELETE] --}}
								@if (Auth::user()->hasPermission('types_tab_delete'))
									@if ($t->trashed())
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.types.restore', [$t->id]) }}', undefined, 'Are you sure you want to activate this?');" class="dropdown-item"><i class="fas fa-toggle-on mr-2"></i>Activate</a>
									@else
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.types.delete', [$t->id]) }}', undefined, 'Are you sure you want to deactivate this?');" class="dropdown-item"><i class="fas fa-toggle-off mr-2"></i>Deactivate</a>
									@endif
								@endif

								{{-- DELETE [PERMANENT DELETE] --}}
								@if (Auth::user()->hasPermission('users_tab_perma_delete'))
								<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.types.permaDelete', [$t->id]) }}', undefined, 'Are you sure you want to delete this?')" class="dropdown-item"><i class="fas fa-trash mr-2"></i>Delete</a>
								@endif
							</div>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="3">Nothing to show~</td>
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
{{-- To someone who will handle this... if you can make this part more secure, I will be glad! QwQ --}}
<script type="text/javascript" src="{{ asset('js/util/swal-change-field.js') }}"></script>
@endsection