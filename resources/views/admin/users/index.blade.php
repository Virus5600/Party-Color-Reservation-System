@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>Users</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row px-3">
						{{-- ADD --}}
						@if (Auth::user()->hasPermission('users_tab_create'))
						<div class="text-center text-md-right ml-md-auto mr-3">
							<a href="{{ route('admin.users.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Users</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'users'])
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
					<th class="text-center">User Image</th>
					<th class="text-center">Name</th>
					<th class="text-center">User Type</th>
					<th class="text-center">Email</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($users as $u)
				<tr class="enlarge-on-hover" id="tr-{{ $u->id }}">
					<td class="text-center">
						<img src="{{ $u->getAvatar() }}" alt="{{ $u->first_name }}'s Avatar" class="img img-fluid user-icon mx-auto rounded">
					</td>

					<td class="text-center align-middle mx-auto font-weight-bold">
						@if (Auth::user()->hasSomePermission('users_tab_delete', 'users_tab_perma_delete'))
							<span class="{{ $u->deleted_at ? 'text-danger' : 'text-success' }}">
								<i class="fas fa-circle small"></i>
							</span>
						@endif
						{{ $u->getName() }}
					</td>

					<td class="text-center align-middle mx-auto">
						{{ $u->type->name }}
					</td>
					
					<td class="text-center align-middle mx-auto">
						{{ $u->email }}
					</td>

					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$u->id}}" aria-haspopup="true" aria-expanded="false">
								Actions
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$u->id}}">
								{{-- SHOW --}}
								<a href="{{ route('admin.users.show', [$u->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>


								{{-- EDIT --}}
								@if (Auth::user()->hasPermission('users_tab_edit'))
								<a href="{{ route('admin.users.edit', [$u->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
								@endif

								{{-- PERMISSIONS --}}
								@if (Auth::user()->hasPermission('users_tab_permissions'))
								<a href="{{ route('admin.users.manage-permissions', [$u->id]) }}" class="dropdown-item"><i class="fas fa-user-lock mr-2"></i>Manage Permissions</a>
								@endif
								
								{{-- CHANGE PASSWORD (EDIT) --}}
								@if (Auth::user()->hasPermission('users_tab_edit') || Auth::user()->id == $u->id)
								<a href="javascript:void(0);" class="dropdown-item change-password" id="scp-{{ $u->id }}">
									<i class="fas fa-lock mr-2"></i>Change Password
									<script type="text/javascript">
										$(document).ready(() => {
											let data = `{
												"preventDefault": true,
												"name": "{{ $u->getName() }}",
												"targetURI": "{{ route('admin.users.change-password', [$u->id]) }}",
												"notify": true,
												"for": "#tr-{{ $u->id }}"
											}`;
											$('#scp-{{ $u->id }}').attr("data-scp", data);
											$('#scp-{{ $u->id }}').find('script').remove();
										});
									</script>
								</a>
								@endif

								{{-- STATUS [DELETE] --}}
								@if (Auth::user()->hasPermission('users_tab_delete'))
									@if ($u->trashed())
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.users.restore', [$u->id]) }}', undefined, 'Are you sure you want to activate this?');" class="dropdown-item"><i class="fas fa-toggle-on mr-2"></i>Activate</a>
									@else
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.users.delete', [$u->id]) }}', undefined, 'Are you sure you want to deactivate this?');" class="dropdown-item"><i class="fas fa-toggle-off mr-2"></i>Deactivate</a>
									@endif
								@endif

								{{-- DELETE [PERMANENT DELETE] --}}
								@if (Auth::user()->hasPermission('users_tab_perma_delete'))
								<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.users.permaDelete', [$u->id]) }}', undefined, 'Are you sure you want to delete this?')" class="dropdown-item"><i class="fas fa-trash mr-2"></i>Delete</a>
								@endif
							</div>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="5">Nothing to show~</td>
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
<script type="text/javascript" src="{{ asset('js/util/swal-change-password.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/login.js') }}"></script>
@endsection