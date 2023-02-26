$(document).ready(() => {
	const monthlyIncomeChart = new Chart($(`#monthlyEarnings`), {
		type: 'bar',
		data: {
			datasets: dataset
		},
		options: {
			plugins: {
				title: {
					display: true,
					text: `Monthly Income`,
					font: {
						size: 25,
						weight: `normal`
					}
				},
				legend: {
					display: false
				}
			},
			responsive: true,
			maintainAspectRation: false,
			onClick: function(evt) {
				// Fetches the bar...
				const points = this.getElementsAtEventForMode(evt, 'nearest', {intersect: true}, true);

				// If there's a bar with data and no visible Swal...
				if (points.length > 0 && !Swal.isVisible()) {
					const ctx = points[0].element.$context;
					let htmlContent = `<div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span></div>`;

					// Send an AJAX request to the server to fetch the list of reservations for this particular month
					$.get(reservationFetchURL.replace('%241', ctx.raw.date), (response) => {
						// If success
						if (response.success) {
							// Build the view...
							let reservations = response.reservations;
							htmlContent = `
								<div class="overflow-y-auto overflow-x-auto custom-scrollbar">
									<table class="table table-striped my-0">
										<thead>
											<tr>
												<th class="text-center">Booking For</th>
												<th class="text-center">Pax</th>
												<th class="text-center">Income</th>
												<th class="text-center">Duration</th>
												<th class="text-center">Orders</th>
											</tr>
										</thead>

										<tbody id="table-content">`;

							for (let r of reservations) {
								let now = new Date(); now = `${now.getMonth()+1}/${now.getDate()}/${now.getFullYear()}`;
								let duration = new Date(`${now} ${r.end_at}`).getHours() - new Date(`${now} ${r.start_at}`).getHours();

								htmlContent += `
										<tr class="enlarge-on-hover">
											<td class="text-center align-middle">${r.contact_information[0].contact_name}</td>
											<td class="text-center align-middle">${r.pax}</td>
											<td class="text-center align-middle">¥ ${r.price}</td>
											<td class="text-center align-middle">${duration} ${(duration != 0 ? 'hours' : 'hour')}</td>
											<td class="text-center align-middle align-center justify-content-center d-flex flex-wrap">`;

								for (let m of r.menus) {
									htmlContent += `
											<span class="badge badge-pill badge-secondary m-1">${m.name}</span>
									`;
								}

								htmlContent += `
											</td>
										</tr>
								`;
							}

							htmlContent += `
										</tbody>
									</table>
								</div>
							`;

							// Then update the already fired up Swal...
							Swal.update({
								html: htmlContent
							});
						}
						else {
							htmlContent = `
								<h3 class="text-center">${response.message}</h3>
							`;
						}
					});

					// Fire the Swal.
					Swal.fire({
						titleText: `Income for the month of ${ctx.raw.date}`,
						html: htmlContent,
						allowOutsideClick: true,
						allowEscapeKey: true,
						showConfirmButton: false,
						showCloseButton: true,
						focusConfirm: false,
						width: `75%`
					});
				}
			},
			onResize: function(chart, size) {
				let canvas = $(chart.canvas);
				let parent = canvas.parent();

				const newWidth = parent.width();
				const newHeight = (parent.width() * (6/10));

				canvas.css('width', newWidth)
					.css('height', newHeight);
				chart.resize(newWidth, newHeight);
			}
		}
	});

	window.addEventListener('beforeprint', () => {
		monthlyIncomeChart.resize(600, 600);
	});

	window.addEventListener('afterprint', () => {
		monthlyIncomeChart.resize();
	});
});