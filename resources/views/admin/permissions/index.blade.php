@extends('layouts.admin')

@section('title', '権限')

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-6 text-center text-md-left">
					<h1>権限</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-6">
					<div class="row">
						{{-- ADD --}}
						<div class="col-12 col-md-6 text-center text-md-right">
							{{-- <a href="{{ route('admin.permissions.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Permission</a> --}}
						</div>

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'permissions'])
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
					<th class="text-center">権限の名前</th>
					<th class="text-center">権限を持つユーザー数</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($permissions as $p)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto font-weight-bold">@{{ $p->name }}</td>
					<td class="text-center align-middle mx-auto">@{{ $p->allUsers()->count() }}</td>

					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown@{{$p->id}}" aria-haspopup="true" aria-expanded="false">
								アクション
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown@{{$p->id}}">
								<a href="#@{{ route('admin.permissions.edit', [$p->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>編集</a>
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