@extends('layouts.admin')

@section('title', 'Permissions')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-6">
					<h1>
						<a href="{{ route('admin.permissions.index') }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Permissions
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="col-12 col-md-10 mx-auto">
			<div class="card dark-shadow mb-5" id="inner-content">
				<h3 class="card-header font-weight-bold text-center">{{ $permission->name }}</h3>
				
				<div class="card-body">
					<table class="table table-striped my-0">
						<thead>
							<tr>
								<th class="text-center">Name</th>
								<th class="text-center">Role</th>
								<th class="text-center">Permission Type</th>
								<th class="text-center"></th>
							</tr>
						</thead>
						
						<tbody id="table-content">
							@forelse ($permission->allUsers() as $u)
							<tr>
								<td class="text-center align-middle mx-auto font-weight-bold">{{ $u->getName() }}</td>
								<td class="text-center align-middle mx-auto">{{ $u->type->name }}</td>
								<td class="text-center align-middle mx-auto">{{ $u->isUsingTypePermissions() ? 'Role Permissions' : 'Custom Permission' }}</td>
								<td class="text-center align-middle mx-auto">
									@if(Auth::user()->hasPermission('users_tab_permissions'))
									<a href="{{ route('admin.users.manage-permissions', [$u->id, 'from' => Request::path()]) }}" class="btn btn-primary"><i class="fas fa-user-lock mr-2"></i>Manage Permissions</a>
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
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
@endsection