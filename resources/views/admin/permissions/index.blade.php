@extends('layouts.admin')

@section('title', 'Permissions')

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-6 text-center text-md-left">
					<h1>Permissions</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-6">
					<div class="row">
						{{-- ADD --}}
						<div class="col-12 col-md-6 text-center text-md-right">
							{{-- <a href="{{ route('admin.permissions.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Permission</a> --}}
						</div>

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'permission'])
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
					<th class="text-center">Permission Name</th>
					<th class="text-center">Number of Permission Users</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($permissions as $p)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $p->name }}</td>
					<td class="text-center align-middle mx-auto">{{ $p->allUsers()->count() }}</td>

					<td class="align-middle">
						<a href="{{ route('admin.permissions.show', [$p->slug]) }}" class="btn btn-primary"><i class="fas fa-eye mr-2"></i>View</a>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="3">Nothing to display~</td>
				</tr>
				@endforelse
			</tbody>
		</table>

		<div id="table-paginate" class="w-100 d-flex align-middle my-3">
			{{ $permissions->onEachSide(5)->links() }}
		</div>
	</div>
</div>
@endsection