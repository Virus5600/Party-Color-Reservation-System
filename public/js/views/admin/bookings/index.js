$(document).ready(() => {
	calendarWrapper = $("#calendar");
	calendar = new FullCalendar.Calendar(calendarWrapper.get()[0], {
		// CALENDAR OPTIONS
		locale: `${window.lang}`,
		dayMaxEvents: true,
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
			daysOfWeek: daysOfWeek,
			startTime: startTime,
			endTime: endTime,
		},
		// SLOT OPTIONS
		slotMinTime: '14:00:00',
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
		windowResizeDelay: 250,
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
				<div class="fc-header-toolbar fc-toolbar fc-toolbar-ltr" id="customToolbar">
					<div class="fc-toolbar-chunk">
					</div>

					<div class="fc-toolbar-chunk">
						<div class="fc-button-group">
							<button type="button" title="Previous" aria-pressed="false" class="fc-prev-button fc-button fc-button-primary">
								<span class="fc-icon fc-icon-chevron-left"></span>
							</button>
							<button type="button" title="Today" ${$(calendar.el).find('.fc-day-today').length > 0 ? 'disabled=""' : ''} aria-pressed="false" class="fc-today-button fc-button fc-button-primary">Today</button>
							<button type="button" title="Next" aria-pressed="false" class="fc-next-button fc-button fc-button-primary">
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

			$("#calendar > .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr").not('#customToolbar:first').remove();
			$("#calendar").prepend(toolbar);

			$(".fc-prev-button").one('click', (e) => { calendar.prev(); $(".fc-today-button").prop('disabled', $('.fc-day-today').length > 0); });
			$(".fc-today-button").one('click', (e) => { calendar.today(); $(e.currentTarget).prop('disabled', true); });
			$(".fc-next-button").one('click', (e) => { calendar.next(); $(".fc-today-button").prop('disabled', $('.fc-day-today').length > 0); });

			$("#dayGridMonth").on('click', (e) => { calendar.changeView("dayGridMonth"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			$("#timeGridWeek").on('click', (e) => { calendar.changeView("timeGridWeek"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			$("#timeGridDay").on('click', (e) => { calendar.changeView("timeGridDay"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });

			$("#listWeek").on('click', (e) => { calendar.changeView("listWeek"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			$("#listDay").on('click', (e) => { calendar.changeView("listDay"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			$("#calendar > .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr").not('#customToolbar:first').remove();

			$('.fc-event').css('box-shadow', '0px 3px 10px #00000080');
		},
		// INITIALIZATION
		datesSet: (args) => {
			if (args.view.type == 'dayGridMonth') {
				calendar.setOption('dayHeaderFormat', {
					weekday: `short`
				});
			}
			else {
				calendar.setOption('dayHeaderFormat', {
					weekday: `${$("#calendar").width() < 992 ? 'short' : 'long'}`,
					month: `numeric`,
					day: `numeric`,
				});
			}

			let toolbar = $(`
				<div class="fc-header-toolbar fc-toolbar fc-toolbar-ltr" id="customToolbar">
					<div class="fc-toolbar-chunk">
						<h2 class="fc-toolbar-title" id="fc-dom-1"></h2>
					</div>

					<div class="fc-toolbar-chunk">
						<div class="fc-button-group">
							<button type="button" title="Previous" aria-pressed="false" class="fc-prev-button fc-button fc-button-primary">
								<span class="fc-icon fc-icon-chevron-left"></span>
							</button>
							<button type="button" title="Today" ${$(calendar.el).find('.fc-day-today').length > 0 ? 'disabled=""' : ''} aria-pressed="false" class="fc-today-button fc-button fc-button-primary">Today</button>
							<button type="button" title="Next" aria-pressed="false" class="fc-next-button fc-button fc-button-primary">
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

			$("#calendar > .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr").not('#customToolbar:first').remove();
			$("#calendar").prepend(toolbar);

			$(".fc-prev-button").one('click', (e) => { calendar.prev(); $(".fc-today-button").prop('disabled', $('.fc-day-today').length > 0); });
			$(".fc-today-button").one('click', (e) => { calendar.today(); $(e.currentTarget).prop('disabled', true); });
			$(".fc-next-button").one('click', (e) => { calendar.next(); $(".fc-today-button").prop('disabled', $('.fc-day-today').length > 0); });

			$("#dayGridMonth").on('click', (e) => { calendar.changeView("dayGridMonth"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			$("#timeGridWeek").on('click', (e) => { calendar.changeView("timeGridWeek"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			$("#timeGridDay").on('click', (e) => { calendar.changeView("timeGridDay"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });

			$("#listWeek").on('click', (e) => { calendar.changeView("listWeek"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			$("#listDay").on('click', (e) => { calendar.changeView("listDay"); $(e.currentTarget).parent().find("button.dropdown-item").removeClass("fc-button-primary"); $(e.currentTarget).addClass('fc-button-primary'); });
			
			$("#calendar > .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr").not('#customToolbar:first').remove();

			$('.fc-event').css('box-shadow', '0px 3px 10px #00000080');
		},
		// EVENTS
		events: events,
		selectable: false,
		eventClick: function (e, el) {
			var data = e.event.extendedProps
			let htmlContent = `<div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span></div>`;

			$.get(showBooking.replace('%241', data.data_id), (response) => {
				if (response.success) {
					let booking = response.booking;
					let date = new Date(booking.reserved_at);
					let createdAt = new Date(booking.created_at);
					let dateOpt = {year: 'numeric', month: 'long', day: 'numeric'};
					let fulldateOpt = {year: 'numeric', month: 'long', day: 'numeric', hours: '2-digits'};

					let phoneNumbers = ``;
					for (let pn of booking.phone_numbers.split("|"))
						phoneNumbers += `<a href="tel:${pn}">${pn}</a>, `;
					phoneNumbers = phoneNumbers.trim();
					phoneNumbers = phoneNumbers.substring(0, phoneNumbers.length-1);

					let callback = `updateCalendar(${data.data_id})`;

					// ACTION BUTTONS
					let pendingButtons = `<button onclick="updateCalendar(${booking.id}, '${approveBooking.replace('%241', booking.id)}', undefined, true, 'Are you sure you want to approve this booking?')" class="btn btn-success"><i class="fas fa-circle-check mr-2"></i>Approve</button>
						<button class="btn btn-danger" data-scf="Reason" data-scf-name="reason" data-scf-custom-title="Reason for rejection" data-scf-target-uri="${rejectBooking.replace('%241', booking.id)}" data-scf-use-textarea='true' data-scf-callback='${callback}'><i class="fas fa-circle-xmark mr-2"></i>Reject</a>`;
					
					let approvedButtons = `<button onclick="updateCalendar(${booking.id}, '${pendingBooking.replace('%241', booking.id)}', undefined, true, 'Are you sure you want to move this to pending?')" class="btn btn-warning"><i class="fas fa-clock mr-2"></i>Pending</a>
						<button class="btn btn-danger" data-scf="Reason" data-scf-name="reason" data-scf-custom-title="Reason for rejection" data-scf-target-uri="${rejectBooking.replace('%241', booking.id)}" data-scf-use-textarea='true' data-scf-callback='${callback}'><i class="fas fa-circle-xmark mr-2"></i>Reject</a>`;
					
					let rejectedButtons = `<button onclick="updateCalendar(${booking.id}, '${approveBooking.replace('%241', booking.id)}', undefined, true, 'Are you sure you want to approve this booking?')" class="btn btn-success"><i class="fas fa-circle-check mr-2"></i>Approve</button>
						<button onclick="updateCalendar(${booking.id}, '${pendingBooking.replace('%241', booking.id)}', undefined, true, 'Are you sure you want to move this to pending?')" class="btn btn-warning"><i class="fas fa-clock mr-2"></i>Pending</a>`;

					// ADDITIONAL ORDER BUTTONS
					let additionalOrderButtons = `<a href="${additionalOrdersIndex.replace('%241', booking.id)}" class="btn btn-primary">Additional Orders</a>`;

					// Subtotal of all additional orders
					let additionalOrdersSubtotal = 0;
					for (let a of booking.additional_orders)
						if (a.deleted_at == null)
							additionalOrdersSubtotal += parseFloat(a.price);

					htmlContent = `
						<div class="card">
							<h4 class="card-header d-flex">
								<span class="w-100 w-lg-50 mr-lg-auto">${date.toLocaleDateString(window.lang, dateOpt)} ${booking.start_at} - ${booking.end_at}</span>
								<span class="w-100 w-lg-50 ml-lg-auto">${currencySymbol} ${parseFloat(parseFloat(booking.price) + additionalOrdersSubtotal).toFixed(2)}</span>
							</h4>
							
							<div class="card-body">
								<div class="row text-left">
									<div class="col-12 col-lg-6 my-3 my-lg-0">
										<p><b>Control Number:</b> #${booking.control_no}</p>
									</div>

									<div class="col-12 col-lg-6 my-3 my-lg-0">
										<p><b>Booking Type:</b> ${booking.booking_type}</p>
									</div>

									<div class="col-12 col-lg-6 my-3 my-lg-0">
										<p><b>Pax:</b> &times;${booking.pax} people${booking.pax > 1 ? 's' : ''}</p>
										<p><b>Created:</b> ${createdAt.toLocaleDateString(window.lang, fulldateOpt)}</p>
									</div>
									
									<div class="col-12 col-lg-6 my-3 my-lg-0">
										<p><b>Extension:</b> ${booking.extension * 60} min (${booking.extension} hrs)</p>
										<p><b>Phone Numbers:</b> ${phoneNumbers}</p>
									</div>
								</div>
							</div>
							
							<div class="card-body text-left">
								<div class="row">
									<div class="col-12 col-lg-6">
										<div class="d-flex justify-content-between">
											<span class="h3">Menus</span>
											<span class="my-auto">${currencySymbol}${booking.price}</span>
										</div>

										<ul>`;
					// Write down booking menus
					for (let m of booking.menus)
						htmlContent += `<li>${m.name} (&times;${m.pivot.count})</li>`;
					htmlContent += `
										</ul>
									</div>

									<div class="col-12 col-lg-6">
										<h3>Contacts</h3>
										
										<ul>`;
					// Write down booking contacts
					for (let c of booking.contact_information)
						htmlContent += `<li>${c.contact_name} [<a href="mailto:${c.email}">${c.email}</a>]</li>`;
					htmlContent += 	`
										</ul>
									</div>
								</div>
							</div>

							<hr class="hr-thick">
							
							<div class="card-body text-left">
								<div class="row my-3">
									<div class="col-12 col-lg-6">
										<h3>Additional Orders</h3>
									</div>

									<div class="col-12 col-lg-6 d-flex justify-content-evenly">
										<i class="fas fa-square mr-2 text-danger my-auto"></i>
										<span class="my-auto">Voided</span>
									</div>
								</div>

								<div class="row my-3">`;
					// List down all additional orders
					if (booking.additional_orders.length > 0) {
						for (let a of booking.additional_orders) {
							htmlContent +=
										`<div class="col-12 col-lg-6 p-2">
											<div class="container-fluid p-2 border rounded border-secondary ${a.deleted_at == null ? '' : 'bg-danger text-white'}">
												<div class="d-flex justify-content-between">
													<span class="h5">Subtotal: </span>
													<span class="my-auto">${currencySymbol}${a.price}</span>
												</div>
												<hr class="hr-thick">

												<ul>`;
								for (let m of a.booking_menus)
									htmlContent += `<li>${m.menu.name} (&times;${m.count})</li>`;
							htmlContent +=
												`</ul>
											</div>
										</div>`;
						}
					}
					else {
						htmlContent += `<div class="col-12 p-2 text-center h3">[No Aadditional Orders Yet]</div>`;
					}
					htmlContent +=
								`</div>
							</div>
							
							<div class="card-body text-left m-0 p-0">
								<div class="card">
									<h3 class="card-header" style="color: white; background-color: ${response.colorCode}">Booking Status (${response.status})</h3>`;
					if (response.colorCode == "#dc3545") {
						htmlContent += `
									<div class="card-body">
										<h4>Reason:</h4>
										<p>${booking.reason}</p>
									</div>`;
					}
					htmlContent += `
								</div>
							</div>

							<div class="card-footer">
								<div class="btn-group" role="group" aria-label="Booking Actions">`;
					// Displaying Booking Action Buttons
					if (["Happening", "Done", "Ghosted", "Cancelled", "Rejected", "Unknown"].includes(response.status))
						htmlContent += `<button class="btn btn-primary" disabled>Edit</button>`;
					else
						htmlContent += `<a href="${editBooking.replace('%241', data.data_id)}" class="btn btn-primary">Edit</a>`;
					htmlContent += `
									<button onclick="confirmLeave('${deleteBooking.replace('%241', data.data_id)}', undefined, 'This cannot action cannot be undone once you\'ve decided to proceed.')" class="btn btn-danger">Remove</button>
								</div>`;
					// Displaying athe additional orders button
					htmlContent += `
								<div class="btn-group" role="group" aria-label="Status Actions">
									${additionalOrderButtons}
								</div>
								
								<div class="btn-group" role="group" aria-label="Status Actions" id="statusActionButtons">`;
					// Displaying Status Action Buttons
					if (response.status == 'Pending')
						htmlContent += pendingButtons;
					else if (["Approve", "Coming"].includes(response.status))
						htmlContent += approvedButtons;
					else if (response.status == 'Rejected')
						htmlContent += rejectedButtons;
					htmlContent += `
								</div>
							</div>
						</div>
					`;
				}
				else {
					htmlContent = `
						<h3 class="text-center">${response.message}</h3>
					`;
				}

				Swal.update({
					html: htmlContent,
				});
			}).fail((response) => {
				console.log(response);
			});

			Swal.fire({
				titleText: `${e.event._def.title}`,
				html: htmlContent,
				allowOutsideClick: true,
				allowEscapeKey: true,
				showConfirmButton: false,
				showCloseButton: true,
				focusConfirm: false,
				width: `75%`
			});
		}
	});

	calendar.render();

	var interval = setInterval(() => {$("#calendar > .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr").not('#customToolbar:first').remove();}, 0);
});

const updateCalendar = async function(id, route, reqType = 'get', shouldConfirm = false, message = 'Are you sure about that?') {
	let eventCal = calendar.getEventById(id);
	let skipUpdate = false;

	let confirmed = true;
	if (shouldConfirm) {
		confirmed = await confirmLeaveApi(undefined, message);
	}

	if (confirmed.isConfirmed) {
		if (typeof route != 'undefined') {
			let swalInfo;

			if (reqType == 'get') {
				skipUpdate = true;
				$.get(route, (response) => {
					console.log(response);
					if (response.success) {
						updateBG();

						Swal.fire({
							title: `${response.title}`,
							position: `top`,
							showConfirmButton: false,
							toast: true,
							timer: 2500,
							background: `var(--success)`,
							customClass: {
								title: `text-white`,
								content: `text-white`,
								popup: `px-3`
							},
						});
					}
				});
			}
			else {
				Swal.fire({
					title: `POST not yet supported`,
					position: `top`,
					showConfirmButton: false,
					toast: true,
					timer: 2500,
					background: `var(--info)`,
					customClass: {
						title: `text-white`,
						content: `text-white`,
						popup: `px-3`
					},
				});
			}
		}
	}

	const updateBG = () => {
		$.get(bookingFetchOne.replace('%241', id), (response) => {
			eventCal.setProp('color', response.props.statusColorCode);
		});
	};

	if (!skipUpdate)
		updateBG();
};