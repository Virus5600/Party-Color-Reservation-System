@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>Activity Log</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row px-3">
						<div class="text-md-right ml-md-auto">
							{{-- SEARCH --}}
							@include('components.admin.admin-search', ['type' => 'activity'])
						</div>
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
					<th class="text-center">User</th>
					<th class="text-center">Action</th>
					<th class="text-center">Timestamp</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($activity as $a)
				<tr class="enlarge-on-hover" id="tr-{{ $a->id }}">
					<td class="text-center align-middle mx-auto">
						{{ $a->email }}
					</td>

					<td class="text-center align-midde mx-auto">
						{{ $a->action }}
					</td>

					<td class="text-center align-midde mx-auto">
						{{ $a->created_at->format("M d, Y h:i A") }}
					</td>

					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{ $a->id }}" aria-haspopup="true" aria-expanded="false">
								Actions
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$a->id}}">
								<a href="#" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>
								<a href="#" class="dropdown-item"><i class="fas fa-magnifying-glass-location mr-2"></i>Trace User</a>
							</div>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="5">Nothing to show~</td>
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
{{-- To someone who will handle this... if you can make this part more secure, I will be glad! QwQ --}}
@endsection