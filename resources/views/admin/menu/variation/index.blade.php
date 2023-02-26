@extends('layouts.admin')

@section('title', "Menu Variation - {$menu->name}")

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4">
					<h1>
						<a href="{{route('admin.menu.index')}}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Menu
						</a>
					</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row">
						{{-- ADD --}}
						@if (Auth::user()->hasPermission('menu_var_tab_create'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="{{ route('admin.menu.variation.create', [$menu->id]) }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Item</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'menu_variations'])
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
					<td colspan="3">
						<p class="text-center m-0 d-flex flex-row flex-wrap justify-content-center">
							<span class="h2 mx-2 my-0 font-weight-normal">{{ $menu->name }}</span>
							<span class="badge badge-pill badge-{{ $menu->trashed() ? "danger" : "success" }} my-auto">{{ $menu->trashed() ? "Inactive" : "Active" }}</span>
						</p>
					</td>
				</tr>

				<tr>
					<th class="text-center">Variation Name</th>
					<th class="text-center">Status</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($variations as $v)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $v->name }}</td>
					
					<td class="text-center align-middle mx-auto">
						<i class="fas fa-circle {{ $v->trashed() ? 'text-danger' : 'text-success' }} mr-2"></i>
						{{ $v->trashed() ? 'Inactive' : 'Active'}}
					</td>
					
					<td class="text-center align-middle mx-auto">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$v->id}}" aria-haspopup="true" aria-expanded="false">
								Action
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$v->id}}">
								{{-- SHOW --}}
								<a href="{{ route('admin.menu.variation.show', [$menu->id, $v->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>

								{{-- EDIT --}}
								@if (Auth::user()->hasPermission('menu_var_tab_edit'))
								<a href="{{ route('admin.menu.variation.edit', [$menu->id, $v->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
								@endif
								
								{{-- DELETE --}}
								@if (Auth::user()->hasPermission('menu_var_tab_delete'))
									@if ($v->trashed())
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.menu.variation.restore', [$menu->id, $v->id]) }}', undefined, 'Are you sure you want to activate this?');" class="dropdown-item"><i class="fas fa-toggle-on mr-2"></i>Set Active</a>
									@else
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.menu.variation.delete', [$menu->id, $v->id]) }}', undefined, 'Are you sure you want to deactivate this?');" class="dropdown-item"><i class="fas fa-toggle-off mr-2"></i>Set Inactive</a>
									@endif
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
@endsection