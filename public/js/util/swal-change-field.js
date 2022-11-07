$(document).ready(() => {
	$(document).on('click', '[data-scf], .swal-change-field', (e) => {
		const obj = $(e.currentTarget);

		const field = obj.attr('data-scf');
		const fieldName = obj.attr('data-scf-name');
		const targetURI = obj.attr('data-scf-target-uri');
		fieldName = (typeof fieldName == 'undefined' ? field : fieldName);

		html = `
		<div class="row">
			<div class="col-12 my-2">
				<label class="form-label d-none" for="${fieldName}">${field}</label>
				<div class="input-group">
					<input class="form-control border-secondary border-right-0" type="text" name="${fieldName}" id="${fieldName}" aria-label="${field}" placeholder="${field}" />
				</div>
			</div>
		</div>
		`;

		Swal.fire({
			title: `Update ${field}`,
			html: html,
			confirmButtonText: 'Submit',
			cancelButtonText: 'Cancel',
			showCancelButton: true,
			focusConfirm: false,
			allowOutsideClick: false,
			preconfirm: () => {
				const fieldValue = Swal.getPopup().querySelector(`#${fieldName}`).value;

				if (fieldValue.length <= 0)
					Swal.showValidationMessage(`${fieldName} is required`);

				return {
					inputVal: fieldValue
				}
			}
		}).then((response) => {
			if (response.isConfirmed) {
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content');
					}
				})

				const dataPacket = {
					`_token`: $('meta[name="csrf-token"]').attr('content'),
					`${fieldName}`: response.value.fieldVal
				};

				$.post(
					targetURI,
					dataPacket
				).done((data) => {
					//
				});
			}
		});
	});
});