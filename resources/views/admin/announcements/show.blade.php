@extends('layouts.admin')

@section('title', '発行')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-6">
					<h1>
						<a href="{{ route('admin.announcements.index', ['d' => $show_drafts, 'sd' => $show_softdeletes]) }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>発表
						</a>
					</h1>
				</div>

				{{-- Controls --}}
				<div class="col-6">
					<>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="col-12 col-md-8 mx-auto">
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