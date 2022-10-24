@extends('layouts.admin')

@section('title', '発表')

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
							<a href="{{ route('admin.announcements.create', ['d' => $show_drafts, 'sd' => $show_softdeletes]) }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>発表追加</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'announcements', 'etcInput' => array('sd' => $show_softdeletes, 'd' => $show_drafts)])
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

	<h6>
		@if ($show_drafts && $show_softdeletes)
		<a href="{{ route('admin.announcements.index') }}?d=0&sd=0" class="text-primary">公開</a> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=0" class="text-primary">ドラフト</a> | <span class="text-dark">全て</span> | <a href="{{ route('admin.announcements.index') }}?d=0&sd=1" class="text-primary">ごみ箱</a>
		@elseif ($show_drafts)
		<a href="{{ route('admin.announcements.index') }}?d=0&sd=0" class="text-primary">公開</a> | <span class="text-dark">ドラフト</span> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=1" class="text-primary">全て</a> | <a href="{{ route('admin.announcements.index') }}?d=0&sd=1" class="text-primary">ごみ箱</a>
		@elseif ($show_softdeletes)
		<a href="{{ route('admin.announcements.index') }}?d=0&sd=0" class="text-primary">公開</a> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=0" class="text-primary">ドラフト</a> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=1" class="text-primary">全て</a> | <span class="text-dark">ごみ箱</span>
		@else
		<span class="text-dark">公開</span> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=0" class="text-primary">ドラフト</a> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=1" class="text-primary">全て</a> | <a href="{{ route('admin.announcements.index') }}?d=0&sd=1" class="text-primary">ごみ箱</a>
		@endif
	</h6>

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
					<td class="text-center align-middle mx-auto">
						<img src="{{ $a->getPoster() }}" alt="{{ $a->title }}" class="img img-fluid user-icon mx-auto rounded">
					</td>
					<td class="text-center align-middle mx-auto font-weight-bold"><i class="fas fa-circle {{ $a->is_draft ? 'text-info' : 'text-success' }} mr-2"></i>{{ $a->title }}</td>
					<td class="text-center align-middle mx-auto">{{ $a->created_at->locale('ja_JP')->translatedFormat('M d, Y') }}</td>
					<td class="text-center align-middle mx-auto">{{ $a->user->getName() }}</td>

					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$a->id}}" aria-haspopup="true" aria-expanded="false">
								アクション
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$a->id}}">
								<a href="{{ route('admin.announcements.show', [$a->id]) }}?d={{ $show_drafts ? 1 : 0 }}&sd={{ $show_softdeletes ? 1 : 0 }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>表示</a>
								<a href="{{ route('admin.announcements.edit', [$a->id]) }}?d={{ $show_drafts ? 1 : 0 }}&sd={{ $show_softdeletes ? 1 : 0 }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>編集</a>
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