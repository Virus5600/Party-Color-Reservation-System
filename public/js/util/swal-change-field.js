$(document).ready(() => {
	let forSCFSwalFire = window.localStorage.getItem('forSCFSwalFire');
	if (forSCFSwalFire != null && forSCFSwalFire != 'null') {
		window.localStorage.removeItem('forSCFSwalFire');
		Swal.fire(JSON.parse(forSCFSwalFire));
	}

	$(document).on('click', '[data-scf], .swal-change-field', (e) => {
		const obj = $(e.currentTarget);

		let field = obj.attr('data-scf');
		let fieldName = obj.attr('data-scf-name');
		let targetURI = obj.attr('data-scf-target-uri');
		let customTitle = obj.attr('data-scf-custom-title');
		let disableButton = obj.attr('data-scf-disable-button');
		let useTextArea = obj.attr('data-scf-use-textarea');
		let label = obj.attr('data-scf-label');
		let reloadPage = obj.attr('data-scf-reload');
		let callback = obj.attr('data-scf-callback');

		fieldName = (typeof fieldName == 'undefined' ? field : fieldName);
		customTitle = (typeof customTitle == 'undefined' ? `Update ${field}` : customTitle);
		disableButton = (typeof disableButton == 'undefined' ? false : (disableButton.toLowerCase() === 'true'));
		useTextArea = (typeof useTextArea == 'undefined' ? false : (useTextArea.toLowerCase() === 'true'));
		reloadPage = (typeof reloadPage == 'undefined' ? false : (reloadPage.toLowerCase() === 'true'));

		html = '';

		if (typeof label != 'undefined')
			html = `
			<div class="row">
				<div class="col-12 my-2">
					<label class="form-label" for="${fieldName}">${label}</label>
					<div class="input-group">`;
		
		if (useTextArea)
			html +=		`<textarea class="form-control border-secondary my-2 not-resizable" rows="5" name="${fieldName}" id="${fieldName}" aria-label="${field}" placeholder="${field}"></textarea>`;
		else
			html +=		`<input class="form-control border-secondary my-2" type="text" name="${fieldName}" id="${fieldName}" aria-label="${field}" placeholder="${field}" />`;

		
		if (typeof label != 'undefined')
			html +=	`</div>
				</div>
			</div>
			`;

		obj.prop('disabled', disableButton);

		Swal.fire({
			title: customTitle,
			html: html,
			confirmButtonText: 'Submit',
			cancelButtonText: 'Cancel',
			showCancelButton: true,
			focusConfirm: false,
			allowOutsideClick: false,
			showLoaderOnConfirm: true,
			preConfirm: () => {
				const fieldValue = Swal.getPopup().querySelector(`#${fieldName}`).value;

				if (fieldValue.length <= 0)
					Swal.showValidationMessage(`${field} is required`);

				return {
					inputVal: fieldValue
				}
			}
		}).then((response) => {
			if (response.isConfirmed) {
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				const dataPacket = {
					"_token": $('meta[name="csrf-token"]').attr('content'),
					[fieldName]: response.value.inputVal
				};

				$.post(
					targetURI,
					dataPacket
				).done((data) => {
					let forSCFSwalFire;

					if (data.success) {
						forSCFSwalFire = {
							position: `top`,
							showConfirmButton: false,
							toast: true,
							timer: 10000,
							background: `#28a745`,
							customClass: {
								title: `text-white`,
								content: `text-white`,
								popup: `px-3`
							},
						};
						
						if (typeof data.title != 'undefined') {
							forSCFSwalFire.title = `${data.title}`;
							forSCFSwalFire.html = `${data.message}`;
						}
						else
							forSCFSwalFire.title = `${data.message}`;

						Swal.fire(forSCFSwalFire);
					}
					else {
						forSCFSwalFire = {
							position: `top`,
							showConfirmButton: false,
							toast: true,
							timer: 10000,
							background: `#dc3545`,
							customClass: {
								title: `text-white`,
								content: `text-white`,
								popup: `px-3`
							},
						};

						if (typeof data.title != 'undefined') {
							forSCFSwalFire.title = `${data.title}`;
							forSCFSwalFire.html = `${data.message}`;
						}
						else
							forSCFSwalFire.title = `${data.message}`;
						
						Swal.fire(forSCFSwalFire);
					}

					if (reloadPage && data.success) {
						setTimeout(() => {
							window.localStorage.setItem("forSCFSwalFire", JSON.stringify(forSCFSwalFire));
							window.location.reload();
						}, 2500);
					}

					if (typeof callback == 'string' && callback.length > 0 && data.success) {
						setTimeout(callback, 0);
					}
				}).fail((data) => {

					Swal.fire({
						title: `${data.title}`,
						html: `${data.message}`,
						position: `top`,
						showConfirmButton: false,
						toast: true,
						timer: 10000,
						background: `#dc3545`,
						customClass: {
							title: `text-white`,
							content: `text-white`,
							popup: `px-3`
						},
					});
				}).always(() => {
					obj.prop('disabled', false);
				});
			}
			else {
				obj.prop('disabled', false);
			}
		});
	});
});