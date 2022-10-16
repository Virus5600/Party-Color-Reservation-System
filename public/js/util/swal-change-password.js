$(document).ready(() => {
	$(document).on('click', '.change-password', (e) => {
		let obj = $(e.currentTarget);
		console.log(obj.attr('data-scp'));
		let data = JSON.parse(obj.attr('data-scp'));

		if(typeof data.preventDefault == 'undefined')
			data.preventDefault = false;
		if (typeof data.notify == 'undefined')
			data.notify = false;
		if (typeof data.name == 'undefined')
			data.name = "[NAME]"
		if (typeof data.targetURI == 'undefined') {
			Swal.fire({
				title: `Something went wrong, please contact the Webmaster to fix the error`,
				position: `top`,
				showConfirmButton: false,
				toast: true,
				background: `#dc3545`,
				customClass: {
					title: `text-white`,
					content: `text-white`,
					popup: `px-3`
				},
			});
			console.error("Swal Change Password cannot proceed:", "targetURI missing");
			return;
		}


		if (data.preventDefault)
			e.preventDefault();

		html = `
		<div class="row">
			<div class="col-12 my-2">
				<label class="form-label d-none" for="password">Password</label>
				<div class="input-group">
					<input class="form-control border-secondary border-right-0" type="password" name="password" id="password" aria-label="Password" aria-describedby="toggle-show-password" placeholder="Password" />
					<div class="input-group-append">
						<button type="button" class="btn bg-white border-secondary border-left-0 toggle-show-password" aria-label="Show Password" data-target="#password">
							<i class="fas fa-eye d-none" id="show"></i>
							<i class="fas fa-eye-slash" id="hide"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="col-12 my-2">
				<label class="form-label d-none" for="confirm_password">Old Password</label>
				<div class="input-group">
					<input class="form-control border-secondary border-right-0" type="password" name="confirm_password" id="confirm_password" aria-label="Confirm Password" aria-describedby="toggle-show-password" placeholder="Confirm Password" />
					<div class="input-group-append">
						<button type="button" class="btn bg-white border-secondary border-left-0 toggle-show-password" aria-label="Show Password" data-target="#confirm_password">
							<i class="fas fa-eye d-none" id="show"></i>
							<i class="fas fa-eye-slash" id="hide"></i>
						</button>
					</div>
				</div>
			</div>

			<div class="form-check col-12">
				<input type="checkbox" name="notify_affected" id="notify_affected" ${ data.notify ? "checked" : "" }>
				<label class="form-label" for="is_departamental_account">Notify affected user?</label>
			</div>
		</div>
		`;

		Swal.fire({
			title: `Change Password for<br><b>${data.name}</b>`,
			html: html,
			confirmButtonText: 'Update',
			showCancelButton: true,
			focusConfirm: false,
			allowOutsideClick: false,
			preConfirm: () => {
				const p = Swal.getPopup().querySelector('#password').value;
				const cp = Swal.getPopup().querySelector('#confirm_password').value;

				if (p.length < 8) {
					Swal.showValidationMessage(`Password should be at least 8 characters`);
				}
				else if (!p.match(/([a-z])([0-9])/gi)) {
					Swal.showValidationMessage(`Password must contain at least 1 letter and 1 number`);
				}
				else if (cp.length <= 0) {
					Swal.showValidationMessage(`You must confirm your password first`);
				}
				else if (p !== cp) {
					Swal.showValidationMessage(`Password does not match`);
				}

				data.notify = $('#notify_affected').prop('checked');

				return {
					password: p,
					confirm_password: cp,
					notify: data.notify
				}
			}
		}).then((response) => {
			if (response.isConfirmed) {
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				$.post(data.targetURI, {
					_token: $('meta[name="csrf-token"]').attr('content'),
					password: response.value.password,
					password_confirmation: response.value.confirm_password,
					notify_user: response.value.notify
				}).done((dataR) => {
					if (dataR.type == 'validation_error') {
						obj.attr('data-scp', JSON.stringify(data)).trigger('click');
						Swal.showValidationMessage(dataR.message);
					}
					else if (dataR.type == 'missing' || dataR.type == 'error') {
						Swal.fire({
							title: `${dataR.message}`,
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

						if (dataR.type == 'missing')
							$(data.for).remove();
					}
					else if (dataR.type == 'success') {
						Swal.fire({
							title: `${dataR.message}`,
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
						});
					}
				});
			}
		});
	});
});