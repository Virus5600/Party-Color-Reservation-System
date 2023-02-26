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
				<tr class="enlarge-on-hover {{ $a->is_marked == 1 ? "bg-warning text-dark" : "" }}" id="tr-{{ $a->id }}">
					<td class="text-center align-middle mx-auto">
						@if ($a->causer != null)
						{{ "{$a->causer->email}@{$a->ip_address}" }}
						@else
						{{ $a->ip_address }}
						@endif
					</td>

					<td class="text-center align-middle mx-auto">
						{!! $a->description !!}
						@if ($a->is_marked == 1)
						<span class="badge badge-danger">Marked as Suspicious</span>
						@endif
					</td>

					<td class="text-center align-middle mx-auto">
						{{ $a->created_at->format("M d, Y h:i A") }}
					</td>

					<td class="align-middle">
						<div class="dropdown">
							<button class="btn btn-{{ $a->is_marked == 1 ? "danger" : "primary" }} dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{ $a->id }}" aria-haspopup="true" aria-expanded="false">
								Actions
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$a->id}}">
								<a href="{{ route('admin.activity-log.show', [$a->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>
								<a href="{{ $a->causer != null ? route('admin.users.show', [$a->causer->id]) : "javascript:SwalFlash.info(`Cannot Find User`, `User account may already be deleted or an anonymous user.`, true, false, `center`, false);" }}" class="dropdown-item"><i class="fas fa-magnifying-glass-location mr-2"></i>Trace User</a>
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

			<tfoot>
				<tr>
					<td colspan="4">
						<div class="d-flex align-middle">
							{{ $activity->onEachSide(5)->links() }}
						</div>
					</td>
				</tr>
			</tfoot>
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