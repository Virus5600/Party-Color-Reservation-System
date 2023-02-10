<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">

@extends('layouts.admin')

@section('title', 'Reservations')

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-6 text-center text-md-left">
					<h1>Reservations</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-6">
					<div class="row h-100">
						{{-- ADD --}}
						<div class="col-12 col-md-6 text-center text-md-right mx-auto mr-lg-0 ml-lg-auto my-auto">
							<a href="{{ route('admin.reservations.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Reservation</a>
						</div>
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

	{{-- STATUS --}}
	<h5>Status Legend:</h5>
	<div class="row">
		<label class="text-center col col-md-2 col-xl-2"><i class="fas fa-square mr-2 text-warning"></i>Pending</label>		
		<label class="text-center col col-md-2 col-xl-2"><i class="fas fa-square mr-2 text-danger"></i>Rejected/Cancelled</label>		
		<label class="text-center col col-md-4 col-xl-2"><i class="fas fa-square mr-2 text-info"></i>Coming</label>
		<label class="text-center col col-md-2 col-xl-2"><i class="fas fa-square mr-2 text-primary"></i>Happening</label>		
		<label class="text-center col col-md-2 col-xl-2"><i class="fas fa-square mr-2 text-secondary"></i>Done</label>
		<label class="text-center col col-md-2 col-xl-2"><i class="fas fa-square mr-2" style="color: #1e2b37;"></i>Others</label>
	</div>
	{{-- STATUS END --}}

	<div class="card dark-shadow overflow-x-scroll flex-fill mb-3" id="reservation_details">
		<span id="fullscreen_trigger" class="position-relative show-expand d-lg-none" data-target="#reservation_details" data-affected="#calendar_container">
			<svg class="text-secondary btn btn-light m-1 p-1 position-absolute" style="top: 0; right: 0; width: 2rem; height: 2rem;" aria-labelledby="fullscreen_toggle" role="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
				<title id="fullscreen_toggle">Toggle Fullscreen</title>
				<path fill="currentColor" id="upperLeft" d="m 160 32 c 0 -17.7 -14.3 -32 -32 -32 s -32 14.3 -32 32 v 64 H 32 c -17.7 0 -32 14.3 -32 32 s 14.3 32 32 32 h 96 c 17.7 0 32 -14.3 32 -32 V 64 z"></path>
				<path fill="currentColor" id="lowerLeft" d="m 32 288 c -17.7 0 -32 14.3 -32 32 s 14.3 32 32 32 H 96 v 64 c 0 17.7 14.3 32 32 32 s 32 -14.3 32 -32 V 320 c 0 -17.7 -14.3 -32 -32 -32 H 32 z"></path>
				<path fill="currentColor" id="upperRight" d="m 352 32 c 0 -17.7 -14.3 -32 -32 -32 s -32 14.3 -32 32 v 96 c 0 17.7 14.3 32 32 32 h 96 c 17.7 0 32 -14.3 32 -32 s -14.3 -32 -32 -32 H 352 V 64 z"></path>
				<path fill="currentColor" id="lowerRight" d="M 320 288 c -17.7 0 -32 14.3 -32 32 v 96 c 0 17.7 14.3 32 32 32 s 32 -14.3 32 -32 V 352 h 64 c 17.7 0 32 -14.3 32 -32 s -14.3 -32 -32 -32 H 320 z"></path>
			</svg>
		</span>

		<div class="container-fluid p-3" id="calendar_container">
			<div id="calendar" class="mx-auto"></div>

			{{-- Modal --}}
			<div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalCenterTitle">Reservation Information</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
			
						<div class="modal-body">
							'start_at': <br>
							'end_at': <br>
							'reserved_at': <br>
							'pax': <br>
							'contact_name':
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
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
<link rel="stylesheet" href="{{ asset('css/lib/fullcalendar/fullcalendar.css') }}">
<style type="text/css">
	#reservation_details {transition: 2.5s ease-in-out;}

	#fullscreen_trigger #upperLeft { animation: collapseUpperLeft 0.5s ease-in-out forwards; }
	#fullscreen_trigger #lowerLeft { animation: collapseLowerLeft 0.5s ease-in-out forwards; }
	#fullscreen_trigger #upperRight { animation: collapseUpperRight 0.5s ease-in-out forwards; }
	#fullscreen_trigger #lowerRight { animation: collapseLowerRight 0.5s ease-in-out forwards; }

	#fullscreen_trigger.show-expand #upperLeft { animation: expandUpperLeft 0.5s ease-in-out forwards; }
	#fullscreen_trigger.show-expand #lowerLeft { animation: expandLowerLeft 0.5s ease-in-out forwards; }
	#fullscreen_trigger.show-expand #upperRight { animation: expandUpperRight 0.5s ease-in-out forwards; }
	#fullscreen_trigger.show-expand #lowerRight { animation: expandLowerRight 0.5s ease-in-out forwards; }

	@keyframes collapseUpperLeft {
		from { transform: translateX(62.5%) translateY(62.5%); }
		to { transform: translateX(0%) translateY(0%); }
	}
	@keyframes collapseLowerLeft {
		from { transform: translateX(62.5%) translateY(-50%); }
		to { transform: translateX(0%) translateY(0%); }
	}
	@keyframes collapseUpperRight {
		from { transform: translateX(-62.5%) translateY(62.5%); }
		to { transform: translateX(0%) translateY(0%); }
	}
	@keyframes collapseLowerRight {
		from { transform: translateX(-62.5%) translateY(-50%); }
		to { transform: translateX(0%) translateY(0%); }
	}

	@keyframes expandUpperLeft {
		from { transform: translateX(0%) translateY(0%); }
		to { transform: translateX(62.5%) translateY(62.5%); }
	}
	@keyframes expandLowerLeft {
		from { transform: translateX(0%) translateY(0%); }
		to { transform: translateX(62.5%) translateY(-50%); }
	}
	@keyframes expandUpperRight {
		from { transform: translateX(0%) translateY(0%); }
		to { transform: translateX(-62.5%) translateY(62.5%); }
	}
	@keyframes expandLowerRight {
		from { transform: translateX(0%) translateY(0%); }
		to { transform: translateX(-62.5%) translateY(-50%); }
	}
</style>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/swal-change-field.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
{{-- Calendar Actions --}}
<script type="text/javascript">
	var calendar, calendarWrapper;

	$(document).ready(() => {
		$("#fullscreen_trigger").on('click', (e) => {
			let obj = $(e.currentTarget);
			let target = $(obj.attr('data-target'));
			let affected = $(obj.attr('data-affected'));

			if (obj.hasClass('show-expand')) {
				obj.removeClass('show-expand');
				target.addClass('position-absolute w-100 h-100')
					.css('left', '0')
					.css('top', '0')
					.css('z-index', '100');

				calendar.currentData.options.windowResize(calendar, true);
			}
			else {
				obj.addClass('show-expand');
				target.removeClass('position-absolute w-100 h-100')
					.css('left', '0')
					.css('top', '0')
					.css('z-index', 'auto');
				
				calendar.currentData.options.windowResize(calendar, true);
			}
		});
	});
</script>
<script type="text/javascript" src="{{ asset('js/lib/fullcalendar/fullcalendar.js') }}"></script>
{{-- Calendar Pre-Initialization --}}
<script type="text/javascript" data-for-removal>
	const events = [
		@foreach($reservations as $r)
			@php ($menuName = "")
			@foreach($r->menus as $m)
				@php ($menuName .= " {$m->name},")
				@php ($menuName = trim($menuName))
			@endforeach
		{
			id: {{ $r->id }},
			title: "{{ substr($menuName, 0, strlen($menuName)-1) }} - Reservation for {{ $r->contactInformation()->first()->contact_name }}",
			start: "{{ \Carbon\Carbon::parse("$r->reserved_at $r->start_at")->format("Y-m-d\TH:i:s") }}",
			end: "{{ \Carbon\Carbon::parse("$r->reserved_at $r->end_at")->format("Y-m-d\TH:i:s") }}",
			data_id: "{{ $r->id }}",
			color: "{{ $r->getStatusColorCode($r->getOverallStatus()) }}"
		},
		@endforeach
	];

	const daysOfWeek = [
		@foreach(explode(",", App\Settings::getValue('day-schedule')) as $day)
		{{$day}},
		@endforeach
	];

	const startTime = '{{ \App\Settings::getValue('opening') }}';
	const endTime = '{{ \App\Settings::getValue('closing') }}';

	const showReservation = '{{ route('admin.reservations.show', ["$1"]) }}';
	const editReservation = '{{ route('admin.reservations.edit', ["$1"]) }}';
	const deleteReservation = '{{ route('admin.reservations.delete', ["$1"]) }}';
	
	const approveReservation = '{{ route('admin.reservations.status.accept', ['$1']) }}';
	const rejectReservation = '{{ route('admin.reservations.status.reject', ['$1']) }}';
	const pendingReservation = '{{ route('admin.reservations.status.pending', ['$1']) }}';
	
	const archiveReservation = '{{ route('admin.reservations.archive', ['$1']) }}';
	const restoreReservation = '{{ route('admin.reservations.restore', ['$1']) }}';

	const reservationFetchOne = '{{ route('reservations.fetch-event', ['$1']) }}';

	const currencySymbol = '{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) }}';
</script>

{{-- Calendar Initialization --}}
<script type="text/javascript" src="{{ asset('js/views/admin/reservations/index.js') }}"></script>
<script type="text/javascript" data-for-removal> $(document).ready(() => { $('[data-for-removal]').remove(); }); </script>
@endsection