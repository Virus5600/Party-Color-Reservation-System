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

						{{-- SEARCH --}}
						{{-- @include('components.admin.admin-search', ['type' => 'reservations']) --}}
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

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
<script type="text/javascript">
	var calendar, calendarWrapper, events;

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
<script type="text/javascript" id="calendarScript">
	events = [
	];

	$(document).ready(() => {
		calendarWrapper = $("#calendar");
		calendar = new FullCalendar.Calendar(calendarWrapper.get()[0], {
			// CALENDAR OPTIONS
			// locale: 'ja',
			initialView: 'timeGridWeek',
			aspectRatio: 1,
			allDaySlot: false,
			listDaySideFormat: false,
			expandRows: true,
			nowIndicator: true,
			dayHeaderFormat: {
				weekday: `${$("#calendar").width() < 992 ? 'short' : 'long'}`,
				month: `numeric`,
				day: `numeric`,
			},
			headerToolbar: {
				left: '',
				center: 'dayGridMonth,timeGridWeek,timeGridDay listWeek,listDay prev,today,next',
				right: ''
			},
			titleFormat: {
				year: "numeric",
				month: "long",
				day: "numeric"
			},
			buttonText: {
				today: 'Today',
			},
			// BUSINESS HOURS
			businessHours: {
				daysOfWeek: [0, 3, 4, 5, 6],
				startTime: '17:00',
				endTime: '22:00',
			},
			// SLOT OPTIONS
			slotMinTime: '09:00:00',
			slotMaxTime: '24:00:00',
			slotLabelFormat: {
				hour: 'numeric',
				minute: '2-digit',
				hour12: false
			},
			slotLabelInterval: {
				minute: 30
			},
			// VIEW OPTIONS
			views: {
				dayGridMonth: { buttonText: 'Month' },
				timeGridWeek: { buttonText: 'Week' },
				timeGridDay: { buttonText: 'Day' },
				listWeek: { buttonText: 'List (Week)' },
				listDay: { buttonText: 'List (Day)' }
			},
			// RESIZING
			windowResizeDelay: 500,
			windowResize: (args, manual=false) => {
				// Set the format for day header
				if ($("#calendar").width() < 992)
					calendar.setOption('dayHeaderFormat', {weekday: 'short'});
				else
					calendar.setOption('dayHeaderFormat', {weekday: 'long'});

				// Disables full screen
				let viewType = "";
				if (manual) {
					viewType = args.currentData.viewApi.type;
				}
				else {
					viewType = args.view.type;
					if (!$("#fullscreen_trigger").hasClass("show-expand"))
						setTimeout(() => {$("#fullscreen_trigger").trigger('click')}, 100);
				}

				let = toolbar = $(`
					<div class="fc-header-toolbar fc-toolbar fc-toolbar-ltr">
						<div class="fc-toolbar-chunk">
						</div>

						<div class="fc-toolbar-chunk">
							<div class="fc-button-group">
								<button type="button" title="Previous week" aria-pressed="false" class="fc-prev-button fc-button fc-button-primary">
									<span class="fc-icon fc-icon-chevron-left"></span>
								</button>
								<button type="button" title="This week" ${$(calendar).find('.fc-day-today').length > 0 ? 'disabled=""' : ''} aria-pressed="false" class="fc-today-button fc-button fc-button-primary">Today</button>
								<button type="button" title="Next week" aria-pressed="false" class="fc-next-button fc-button fc-button-primary">
									<span class="fc-icon fc-icon-chevron-right"></span>
								</button>
							</div>
							<div class="dropdown fc-button">
								<button type="button" aria-pressed="false" title="View" class="fc-button fc-button-primary dropdown-toggle" id="viewDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Views</button>

								<div class="dropdown-menu" aria-labelledby="viewDropdown">
									<button type="button" title="Month view" aria-pressed="false" class="dropdown-item fc-dayGridMonth-button ${viewType == 'dayGridMonth' ? 'fc-button-primary' : ''}" id="dayGridMonth">Month</button>
									<button type="button" title="Week view" aria-pressed="true" class="dropdown-item fc-timeGridWeek-button ${viewType == 'timeGridWeek' ? 'fc-button-primary' : ''}" id="timeGridWeek">Week</button>
									<button type="button" title="Day view" aria-pressed="false" class="dropdown-item fc-timeGridDay-button ${viewType == 'timeGridDay' ? 'fc-button-primary' : ''}" id="timeGridDay">Day</button>
									<div class="dropdown-divider"></div>
									<button type="button" title="List (Week) view" aria-pressed="false" class="dropdown-item fc-listWeek-button ${viewType == 'listWeek' ? 'fc-button-primary' : ''}" id="listWeek">List (Week)</button>
									<button type="button" title="List (Day) view" aria-pressed="false" class="dropdown-item fc-listDay-button ${viewType == 'listDay' ? 'fc-button-primary' : ''}" id="listDay">List (Day)</button>
								</div>
							</div>
						</div>

						<div class="fc-toolbar-chunk">
						</div>
					</div>
				`);

				$("#calendar > .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr").remove();
				$("#calendar").prepend(toolbar);

				$(".fc-prev-button").on('click', (e) => { calendar.prev(); $(".fc-today-button").prop('disabled', $('.fc-day-today').length > 0); });
				$(".fc-today-button").on('click', (e) => { calendar.today(); });
				$(".fc-next-button").on('click', (e) => { calendar.next(); $(".fc-today-button").prop('disabled', $('.fc-day-today').length > 0); });

				$("#dayGridMonth").on('click', (e) => { calendar.changeView("dayGridMonth"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
				$("#timeGridWeek").on('click', (e) => { calendar.changeView("timeGridWeek"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
				$("#timeGridDay").on('click', (e) => { calendar.changeView("timeGridDay"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });

				$("#listWeek").on('click', (e) => { calendar.changeView("listWeek"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
				$("#listDay").on('click', (e) => { calendar.changeView("listDay"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			},
			// INITIALIZATION
			datesSet: (args) => {
				let toolbar = $(`
					<div class="fc-header-toolbar fc-toolbar fc-toolbar-ltr">
						<div class="fc-toolbar-chunk">
							<h2 class="fc-toolbar-title" id="fc-dom-1"></h2>
						</div>

						<div class="fc-toolbar-chunk">
							<div class="fc-button-group">
								<button type="button" title="Previous week" aria-pressed="false" class="fc-prev-button fc-button fc-button-primary">
									<span class="fc-icon fc-icon-chevron-left"></span>
								</button>
								<button type="button" title="This week" disabled="" aria-pressed="false" class="fc-today-button fc-button fc-button-primary">Today</button>
								<button type="button" title="Next week" aria-pressed="false" class="fc-next-button fc-button fc-button-primary">
									<span class="fc-icon fc-icon-chevron-right"></span>
								</button>
							</div>
							<div class="dropdown fc-button">
								<button type="button" aria-pressed="false" title="View" class="fc-button fc-button-primary dropdown-toggle" id="viewDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Views</button>

								<div class="dropdown-menu" aria-labelledby="viewDropdown">
									<button type="button" title="Month view" aria-pressed="false" class="dropdown-item fc-dayGridMonth-button ${args.view.type == 'dayGridMonth' ? 'fc-button-primary' : ''}" id="dayGridMonth">Month</button>
									<button type="button" title="Week view" aria-pressed="true" class="dropdown-item fc-timeGridWeek-button ${args.view.type == 'timeGridWeek' ? 'fc-button-primary' : ''}" id="timeGridWeek">Week</button>
									<button type="button" title="Day view" aria-pressed="false" class="dropdown-item fc-timeGridDay-button ${args.view.type == 'timeGridDay' ? 'fc-button-primary' : ''}" id="timeGridDay">Day</button>
									<div class="dropdown-divider"></div>
									<button type="button" title="List (Week) view" aria-pressed="false" class="dropdown-item fc-listWeek-button ${args.view.type == 'listWeek' ? 'fc-button-primary' : ''}" id="listWeek">List (Week)</button>
									<button type="button" title="List (Day) view" aria-pressed="false" class="dropdown-item fc-listDay-button ${args.view.type == 'listDay' ? 'fc-button-primary' : ''}" id="listDay">List (Day)</button>
								</div>
							</div>
						</div>

						<div class="fc-toolbar-chunk">
						</div>
					</div>
				`);

				$("#calendar > .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr").remove();
				$("#calendar").prepend(toolbar);

				$(".fc-prev-button").on('click', (e) => { calendar.prev(); $(".fc-today-button").prop('disabled', $('.fc-day-today').length > 0); });
				$(".fc-today-button").on('click', (e) => { calendar.today(); });
				$(".fc-next-button").on('click', (e) => { calendar.next(); $(".fc-today-button").prop('disabled', $('.fc-day-today').length > 0); });

				$("#dayGridMonth").on('click', (e) => { calendar.changeView("dayGridMonth"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
				$("#timeGridWeek").on('click', (e) => { calendar.changeView("timeGridWeek"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
				$("#timeGridDay").on('click', (e) => { calendar.changeView("timeGridDay"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });

				$("#listWeek").on('click', (e) => { calendar.changeView("listWeek"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
				$("#listDay").on('click', (e) => { calendar.changeView("listDay"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			},
			// EVENTS
			events: events,
		});

		calendar.render();
	});
</script>
@endsection