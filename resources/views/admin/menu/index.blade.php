@extends('layouts.admin')

@section('title', 'Menu')

@php
$user = auth()->user();
$editAllow = $user->hasPermission('menu_tab_edit');
$deleteAllow = $user->hasPermission('menu_tab_delete');
@endphp

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
						@if ($user->hasPermission('menu_tab_create'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="javascript:void(0);" class="btn btn-success m-auto"
								data-scf="Menu Name"
								data-scf-name="menu_name"
								data-scf-custom-title="Add a Menu"
								data-scf-target-uri="{{ route('admin.menu.store') }}"
								data-scf-reload="true">
								<i class="fa fa-plus-circle mr-2"></i>Add Item
							</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'menu'])
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
					<th class="text-center">Menu Name</th>
					<th class="text-center">Variations</th>
					<th class="text-center">Status</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($menus as $m)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $m->name }}</td>
					<td class="text-center align-middle mx-auto">{{ $m->menu_variations_count }}</td>
					<td class="text-center align-middle mx-auto"><i class="fas fa-circle {{ $m->trashed() ? 'text-danger' : 'text-success' }} mr-2"></i>{{ $m->trashed() ? 'Inactive' : 'Active'}}</td>
					<td class="text-center align-middle mx-auto">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$m->id}}" aria-haspopup="true" aria-expanded="false">
								Action
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$m->id}}">
								{{-- SHOW --}}
								<a href="{{ route('admin.menu.variation.index', [$m->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>

								{{-- EDIT --}}
								@if ($editAllow)
								<a href="javascript:void(0);" class="dropdown-item"
									data-scf="Menu Name"
									data-scf-name="menu_name"
									data-scf-target-uri="{{ route('admin.menu.update', [$m->id]) }}"
									data-scf-reload="true">
									<i class="fas fa-pen-to-square mr-2"></i>Change Name
								</a>
								@endif
								
								{{-- DELETE --}}
								@if ($deleteAllow)
									@if ($m->trashed())
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.menu.restore', [$m->id]) }}', undefined, 'Are you sure you want to activate this?');" class="dropdown-item"><i class="fas fa-toggle-on mr-2"></i>Set Active</a>
									@else
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.menu.delete', [$m->id]) }}', undefined, 'Are you sure you want to deactivate this?');" class="dropdown-item"><i class="fas fa-toggle-off mr-2"></i>Set Inactive</a>
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
			{{ $menus->onEachSide(5)->links() }}
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