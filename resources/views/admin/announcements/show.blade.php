@extends('layouts.admin')

@section('title', 'Announcements')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-6">
					<h1>
						<a href="{{ route('admin.announcements.index', ['d' => $show_drafts, 'sd' => $show_softdeletes]) }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Announcements
						</a>
					</h1>
				</div>

				@if (Auth::user()->hasSomePermission('announcements_tab_delete', 'announcements_tab_publish', 'announcements_tab_unpublish'))
				{{-- Controls --}}
				<div class="col-6 d-flex flex-row-reverse">
					@if (Auth::user()->hasPermission('announcements_tab_delete'))
						@if ($announcement->trashed())
						<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.announcements.restore', [$announcement->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Restore this announcement?');" class="btn btn-success my-auto mx-1">Restore</a>
						@else
						<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.announcements.delete', [$announcement->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Delete this announcement?');" class="btn btn-danger my-auto mx-1">Delete</a>
						@endif
					@endif

					@if ($announcement->is_draft)
						@if(Auth::user()->hasPermission('announcements_tab_publish'))
						<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.announcements.publish', [$announcement->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Publish this announcement?');" class="btn btn-success my-auto mx-1">Publish</a>
						@endif
					@else
						@if(Auth::user()->hasPermission('announcements_tab_unpublish'))
						<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.announcements.unpublish', [$announcement->id, 'd' => $show_drafts, 'sd' => $show_softdeletes]) }}', undefined, 'Mark this announcement as draft?');" class="btn btn-info my-auto mx-1">Draft</a>
						@endif
					@endif
				</div>
				@endif
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="col-12 col-md-10 mx-auto">
			<div class="card dark-shadow mb-5" id="inner-content">
				<h3 class="card-header font-weight-bold text-center">{{ $announcement->title }}</h3>
				<div class="card-body">
					<div class="d-flex">
						<img class="img img-fluid w-100 w-md-75 w-lg-50 mx-auto border rounded" src="{{ $announcement->getPoster() }}" alt="{{ $announcement->title }}">
					</div>
					
					<hr class="hr-thick">

					<div class="content">
						{!! $announcement->content !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
@endsection