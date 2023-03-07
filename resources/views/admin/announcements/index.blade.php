@extends('layouts.admin')

@section('title', 'Announcements')

@php
$user = auth()->user();
$editAllow = $user->hasPermission('announcements_tab_edit');
$publishViable = $user->hasSomePermission('announcements_tab_publish', 'announcements_tab_unpublish');
$publishAllow = $user->hasPermission('announcements_tab_publish');
$unpublishAllow = $user->hasPermission('announcements_tab_unpublish');
$deleteAllow = $user->hasPermission('announcements_tab_delete');
$permaDeleteAllow = $user->hasPermission('announcements_tab_perma_delete');
@endphp

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>Announcements</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row">
						{{-- ADD --}}
						@if (Auth::user()->hasPermission('announcements_tab_create'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="{{ route('admin.announcements.create', ['d' => $show_drafts, 'sd' => $show_softdeletes]) }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Announcements</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'announcement', 'etcInput' => array('sd' => $show_softdeletes, 'd' => $show_drafts)])
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

	<h6>
		@if ($show_drafts && $show_softdeletes)
		<a href="{{ route('admin.announcements.index') }}?d=0&sd=0" class="text-primary">Published</a> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=0" class="text-primary">Draft</a> | <span class="text-dark">All</span> | <a href="{{ route('admin.announcements.index') }}?d=0&sd=1" class="text-primary">Trashed</a>
		@elseif ($show_drafts)
		<a href="{{ route('admin.announcements.index') }}?d=0&sd=0" class="text-primary">Published</a> | <span class="text-dark">Draft</span> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=1" class="text-primary">All</a> | <a href="{{ route('admin.announcements.index') }}?d=0&sd=1" class="text-primary">Trashed</a>
		@elseif ($show_softdeletes)
		<a href="{{ route('admin.announcements.index') }}?d=0&sd=0" class="text-primary">Published</a> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=0" class="text-primary">Draft</a> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=1" class="text-primary">All</a> | <span class="text-dark">Trashed</span>
		@else
		<span class="text-dark">Published</span> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=0" class="text-primary">Draft</a> | <a href="{{ route('admin.announcements.index') }}?d=1&sd=1" class="text-primary">All</a> | <a href="{{ route('admin.announcements.index') }}?d=0&sd=1" class="text-primary">Trashed</a>
		@endif
	</h6>

	<div class="card dark-shadow overflow-x-scroll flex-fill mb-3 h-100 d-flex flex-column" id="inner-content">
		<table class="table table-striped my-0">
			<thead>
				<tr>
					<th class="text-center">Announcement Poster</th>
					<th class="text-center">Title</th>
					<th class="text-center">Published Date</th>
					<th class="text-center">Publisher</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($announcements as $a)
				<tr class="enlarge-on-hover">
					<td class="text-center align-middle mx-auto">
						<img src="{{ $a->getPoster() }}" alt="{{ $a->title }}" class="img img-fluid user-icon mx-auto rounded" data-fallback-image="{{ asset('uploads/announcements/default.png') }}">
					</td>

					<td class="text-center align-middle mx-auto font-weight-bold">
						<i class="fas fa-circle {{ $a->trashed() ? 'text-danger' : ($a->is_draft ? 'text-info' : 'text-success') }} mr-2"></i>{{ $a->title }}
					</td>

					<td class="text-center align-middle mx-auto">{{ $a->created_at->locale('en_US')->translatedFormat('M d, Y') }}</td>
					<td class="text-center align-middle mx-auto">{{ $a->user->getName() }}</td>

					<td class="align-middle mx-auto">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$a->id}}" aria-haspopup="true" aria-expanded="false">
								Actions
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$a->id}}">
								<a href="{{ route('admin.announcements.show', [$a->id]) }}?d={{ $show_drafts ? 1 : 0 }}&sd={{ $show_softdeletes ? 1 : 0 }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>

								{{-- EDIT --}}
								@if ($editAllow)
								<a href="{{ route('admin.announcements.edit', [$a->id]) }}?d={{ $show_drafts ? 1 : 0 }}&sd={{ $show_softdeletes ? 1 : 0 }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
								@endif

								{{-- PUBLISH/UNPUBLISH --}}
								@if ($publishViable)
									@if ($a->is_draft && $publishAllow)
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.announcements.publish', [$a->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Publish this announcement?');" class="dropdown-item"><i class="fas fa-upload mr-2"></i>Publish</a>
									@elseif (!$a->is_draft && $unpublishAllow)
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.announcements.unpublish', [$a->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Unpublish this announcement');" class="dropdown-item"><i class="fas fa-pencil-ruler mr-2"></i>Draft</a>
									@endif
								@endif
								
								{{-- DELETE --}}
								@if ($deleteAllow)
									@if ($a->trashed())
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.announcements.restore', [$a->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Are you sure you want to restore this?');" class="dropdown-item"><i class="fas fa-recycle mr-2"></i>Restore</a>
									@else
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.announcements.delete', [$a->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Are you sure you want to trash this?');" class="dropdown-item"><i class="fas fa-trash mr-2"></i>Trash</a>
									@endif
								@endif

								{{-- PERMANENT DELETE --}}
								@if ($permaDeleteAllow)
								<a onclick="confirmLeave('{{ route('admin.announcements.permaDelete', [$a->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Are you sure you want to permanently delete this?')" class="dropdown-item"><i class="fas fa-fire-alt mr-2"></i>Delete</a>
								@endif
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

		<div id="table-paginate" class="w-100 d-flex align-middle my-3">
			{{ $announcements->onEachSide(5)->links() }}
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
@endsection