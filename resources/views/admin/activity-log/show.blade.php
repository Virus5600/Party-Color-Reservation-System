@extends('layouts.admin')

@section('title', 'Activity Log - Log')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-6">
					<h1>
						<a href="{{ route('admin.activity-log.index') }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Activity Log
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="col-12 col-md-10 col-lg-8 mx-auto">
			<div class="card dark-shadow mb-5 {{ $log->is_marked == 1 ? "bg-warning" : "" }}" id="inner-content">
				<h3 class="card-header font-weight-bold text-center">{{ $log->description }}</h3>
				
				<div class="card-body d-flex flex-column">
					<ul class="list-group">
						@foreach($log->toArray() as $k => $v)
							@if (in_array($k, ['id', 'properties', 'description']))
								@continue
							@else
								<li class="list-group-item px-3 d-flex flex-{{ ($k == "reason") ? "column" : "row justify-content-between" }} flex-wrap">
									{!! ($k == "reason") ? "<div class='d-flex flex-row justify-content-between flex-wrap'>" : "" !!}
									<span class="font-weight-bold">{{ str_replace("_", " ", ucfirst($k)) }}</span>
									
									<span class="text-muted">
										@if ($k == 'user_id')
											@if ($v == 0)
											<small><i>({{ ($log["is_from_admin"] == 1 ? "User Deleted" : "N/A")}})</i></small>
											@else
											{{ $v }}
											<a href="{{ route('admin.users.show', [$log['user_id']]) }}" class="btn btn-sm btn-light" title="Track User" aria-label="Track User"><i class="fas fa-magnifying-glass fa-sm mr"></i></a>
											@endif
										@elseif (in_array($k, ['created_at', 'updated_at', 'deleted_at']))
										{!! $v == null ? "<small><i>(N/A)</i></small>" : Carbon\Carbon::parse($v)->format("(l) M d, Y h:i:s A") !!}
										@else
										{!! Str::of($k)->startsWith("is") ? ($v == 1 ? "True" : "False") : ($v == null ? "<small><i>(N/A)</i></small>" : $v) !!}
										@endif
									</span>
									{!! ($k == "reason") ? "</div>" : "" !!}

									@if ($k == "reason")
									<div class="btn-group mt-2" role="group" aria-label="Activity Marking Actions">
										@if ($log["is_marked"] == 0)
										<button class="btn btn-warning"
											data-scf="Reason for marking this as suspicious"
											data-scf-name="reason"
											data-scf-custom-title="Why is it suspicious"
											data-scf-target-uri="{{ route('admin.activity-log.mark', [$log->id]) }}"
											data-scf-reload="true"
											data-scf-use-textarea="true">
											Mark as Suspicious
										</button>
										@else
										<button class="btn btn-danger"
											data-scf="Reason for unmarking this as suspicious"
											data-scf-name="reason"
											data-scf-custom-title="Why revoke the mark"
											data-scf-target-uri="{{ route('admin.activity-log.unmark', [$log->id]) }}"
											data-scf-reload="true"
											data-scf-use-textarea="true">
											Unmark as Suspicious
										</button>
										@endif

										<button class="btn btn-primary"
											data-scf="Reason"
											data-scf-name="reason"
											data-scf-custom-title="Update reason for suspicion"
											data-scf-target-uri="{{ route('admin.activity-log.update', [$log->id]) }}"
											data-scf-reload="true"
											data-scf-use-textarea="true">
											Update Reason
										</button>
									</div>
									@endif
								</li>
							@endif
						@endforeach

						{{-- SUBJECT ITEM --}}
						<li class="list-group-item px-2">
							<div class="card floating-title mt-4 border-secondary">
								<h3 class="card-title text-left m-0 p-2">{{ "Item - {$subject_type} Data"  }}</h3>

								<div class="card-body pt-3">
									<div class="row">
										@foreach($log->properties as $k => $v)
											@if (is_array($v))
												@continue
											@else
												<div class="col-12 col-md-6 my-2 flex-fill">
													<div class="card border-secondary h-100">
														<h6 class="card-header card-title font-weight-bold">
															{{ str_replace("_", " ", ucfirst($k)) }}
														</h6>

														<div class="card-body py-2">
															<p class="card-text ml-3">
																{!! Str::endsWith($k, "ed") ? ($v == 1 ? "True" : "False") : ($v == null ? "<small><i>(N/A)</i></small>" : $v) !!}
															</p>
														</div>
													</div>
												</div>
											@endif
										@endforeach

										<div class="col-12 d-flex flex-row justify-content-center">
											<a href="{{ $showRoute }}" class="btn btn-primary">View Item</a>
										</div>
									</ul>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<style>
	.card.floating-title {
		position: relative;
	}

	.card.floating-title > .card-title {
		position: absolute;
		top: 0;
		left: 0;

		width: 75%;
		border-radius: 0.25rem;
		background-color: var(--secondary);
		color: #fff;

		-webkit-transform: translate(5%, -50%);
		-moz-transform: translate(5%, -50%);
		-ms-transform: translate(5%, -50%);
		-o-transform: translate(5%, -50%);
		transform: translate(5%, -50%);
	}

	.card.floating-title > .card-body {
		padding-top: 2.75rem !important;
	}
</style>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/swal-change-field.js') }}"></script>
@endsection