@extends('layouts.admin')

@section('title', 'Bookings')

@section('content')

@php
$extensionFee = App\Settings::getValue('extension_fee');
@endphp

<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-6 text-center text-md-left">
					<h1>Bookings</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-6">
					<div class="row h-100">
						{{-- ADD --}}
						<div class="col-12 col-md-6 text-center text-md-right mx-auto mr-lg-0 ml-lg-auto my-auto">
							<div class="dropdown">
								<button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="addButton">
									<i class="fa fa-plus-circle mr-2"></i>Add Booking
								</button>

								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="addButton">
									<a href="{{ route('admin.bookings.create', ['t' => 'r']) }}" class="dropdown-item">Add Reservation</a>
									<a href="{{ route('admin.bookings.create', ['t' => 'w']) }}" class="dropdown-item">Add Walk-Ins</a>
								</div>
							</div>
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

	<div class="card dark-shadow overflow-x-scroll flex-fill mb-3 h-100" id="booking_details">
		<span id="fullscreen_trigger" class="position-relative show-expand d-lg-none" data-target="#booking_details" data-affected="#calendar_container">
			<svg class="text-secondary btn btn-light m-1 p-1 position-absolute" style="top: 0; right: 0; width: 2rem; height: 2rem;" aria-labelledby="fullscreen_toggle" role="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
				<title id="fullscreen_toggle">Toggle Fullscreen</title>
				<path fill="currentColor" id="upperLeft" d="m 160 32 c 0 -17.7 -14.3 -32 -32 -32 s -32 14.3 -32 32 v 64 H 32 c -17.7 0 -32 14.3 -32 32 s 14.3 32 32 32 h 96 c 17.7 0 32 -14.3 32 -32 V 64 z"></path>
				<path fill="currentColor" id="lowerLeft" d="m 32 288 c -17.7 0 -32 14.3 -32 32 s 14.3 32 32 32 H 96 v 64 c 0 17.7 14.3 32 32 32 s 32 -14.3 32 -32 V 320 c 0 -17.7 -14.3 -32 -32 -32 H 32 z"></path>
				<path fill="currentColor" id="upperRight" d="m 352 32 c 0 -17.7 -14.3 -32 -32 -32 s -32 14.3 -32 32 v 96 c 0 17.7 14.3 32 32 32 h 96 c 17.7 0 32 -14.3 32 -32 s -14.3 -32 -32 -32 H 352 V 64 z"></path>
				<path fill="currentColor" id="lowerRight" d="M 320 288 c -17.7 0 -32 14.3 -32 32 v 96 c 0 17.7 14.3 32 32 32 s 32 -14.3 32 -32 V 352 h 64 c 17.7 0 32 -14.3 32 -32 s -14.3 -32 -32 -32 H 320 z"></path>
			</svg>
		</span>

		<div class="container-fluid p-3 py-5 py-lg-3 h-100" id="calendar_container">
			<div id="calendar" class="mx-auto h-100"></div>
		</div>
	</div>
</div>
@endsection

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="bearer" content="{{ session('bearer') }}">
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/lib/fullcalendar/fullcalendar.css') }}">
<style type="text/css">
	#booking_details {transition: 2.5s ease-in-out;}

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
	const extensionFee = {{ $extensionFee }};
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
		@foreach($bookings as $b)
		{
			id: {{ $b->id }},
			control_no: "{{ $b->control_no }}",
			booking_type: "{{ ucwords($b->booking_type) }}",
			title: "#{{ $b->control_no }} - Booking for {{ $b->contactInformation()->first()->contact_name }} ({{ ucwords($b->booking_type) }})",
			start: "{{ \Carbon\Carbon::parse("$b->reserved_at $b->start_at")->format("Y-m-d\TH:i:s") }}",
			end: "{{ \Carbon\Carbon::parse("$b->reserved_at $b->end_at")->format("Y-m-d\TH:i:s") }}",
			data_id: "{{ $b->id }}",
			color: "{{ $b->getStatusColorCode($b->getOverallStatus()) }}"
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

	const showBooking = '{{ route('admin.bookings.show', ["$1"]) }}';
	const editBooking = '{{ route('admin.bookings.edit', ["$1"]) }}';
	const deleteBooking = '{{ route('admin.bookings.delete', ["$1"]) }}';
	
	const approveBooking = '{{ route('admin.bookings.status.accept', ['$1']) }}';
	const rejectBooking = '{{ route('admin.bookings.status.reject', ['$1']) }}';
	const pendingBooking = '{{ route('admin.bookings.status.pending', ['$1']) }}';
	
	const acceptCancelBooking = '{{ route('admin.bookings.status.accept-cancel', ['$1']) }}';
	const rejectCancelBooking = '{{ route('admin.bookings.status.reject-cancel', ['$1']) }}';
	
	const archiveBooking = '{{ route('admin.bookings.archive', ['$1']) }}';
	const restoreBooking = '{{ route('admin.bookings.restore', ['$1']) }}';

	const bookingFetchOne = '{{ route('bookings.fetch-event', ['$1']) }}';

	const additionalOrdersIndex = '{{ route('admin.bookings.additional-orders.index', ['$1']) }}';

	const currencySymbol = '{{ (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) }}';
</script>

{{-- Calendar Initialization --}}
<script type="text/javascript" src="{{ asset('js/views/admin/bookings/index.js') }}"></script>
<script type="text/javascript" data-for-removal> $(document).ready(() => { $('[data-for-removal]').remove(); }); </script>
@endsection