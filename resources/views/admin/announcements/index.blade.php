@extends('layouts.admin')

@section('title', '発表')

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>発表</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row">
						{{-- SHOW DELETED --}}
						@if (Auth::user()->hasSomePermission('announcements_tab_delete', 'announcements_tab_perma_delete'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<div class="btn-group-toggle" data-toggle="buttons">
								<label class="btn btn-secondary {{ $show_softdeletes == 1 ? 'active' : '' }}" for="show_softdeletes">
									<input type="checkbox" value="0" id="show_softdeletes" autocomplete="off" {{ $show_softdeletes == 1 ? 'checked' : '' }}> 削除されたを表示する
								</label>
							</div>
						</div>
						@endif

						{{-- ADD --}}
						@if (Auth::user()->hasPermission('announcements_tab_create'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="{{ route('admin.announcements.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>発表追加</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'announcements', 'etcInput' => array('sd' => $show_softdeletes)])
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
					<th class="text-center">発表のイメージ</th>
					<th class="text-center">発表のタイトル</th>
					<th class="text-center">発行日</th>
					<th class="text-center">発行する</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($announcements as $a)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto">{{ $a->title }}</td>
					<td class="text-center align-middle mx-auto font-weight-bold">{{ $a->title }}</td>
					<td class="text-center align-middle mx-auto">{{ $a->created_at }}</td>
					<td class="text-center align-middle mx-auto">{{ $a->user->getName() }}</td>

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
					<td class="text-center" colspan="5">Nothing to display~</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection