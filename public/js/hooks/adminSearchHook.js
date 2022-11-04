$(document).on("keypress", (e) => {
	let target = $("#admin-search input[name=search]");
	if (String.fromCharCode(e.keyCode) == target.attr('accesskey')) {
		if (!target.is(':focus'))
			e.preventDefault();
		target.focus();
	}
});

$('#admin-search input[name=search]').on('focusin focusout', (e) => {
	let obj = $(e.currentTarget);

	if (e.type == 'focusin')
		obj.attr('placeholder', 'Search...');
	else if (e.type == 'focusout')
		obj.attr('placeholder', 'Press / to search');
});

$('#admin-search').on('submit keypress', (e) => {
	let sendAjaxRequest = false;
	let parent, path, dataPacket;
	let content = $('#content').get();

	// e.preventDefault();
	// console.log(e);

	if (e.type == 'submit') {
		e.preventDefault();

		parent = $(e.target);
		path = parent.attr('action');

		dataPacket = {
			_token: parent.find('[name=_token]').val(),
			search: parent.find('[name=search]').val(),
			type: parent.find('[name=type]').val()
		}

		// console.log(dataPacket);
		sendAjaxRequest = true;
	}
	else if (e.keyCode == 13) {
		e.preventDefault();

		parent = $(e.currentTarget);
		path = parent.attr('action');

		dataPacket = parent.serializeFormJSON();

		// console.log(dataPacket);
		sendAjaxRequest = true;
	}

	if (sendAjaxRequest) {
		// console.log(path);
		$.post(
			path, dataPacket
		).done((response) => {
			console.log(response);

			var newTable = ``;
			$.each(response.content, (k, v) => {
				// OPENING
				newTable += `<tr id="tr-${v.id}">`;

				// console.warn('Value:');
				// console.log(v);

				// IMAGE TAB
				if (response.has_image) {
					let src = response.asset + '/' + v[response.image];
					
					newTable += `<td class="text-center"><img src="${src}" alt="${v[response.img_alt]} Logo" class="img img-fluid user-icon mx-auto" data-fallback-img="${response.asset + '/default.png'}"></td>`;
				}

				// HEADER TAB
				if (response.has_header)
					newTable += `<td class="text-center align-middle mx-auto font-weight-bold">${response.has_soft_del ? (v['deleted_at'] == null ? '<span class="text-success"><i class="fas fa-circle small"></i> </span>' : '<span class="text-danger"><i class="fas fa-circle small"></i> </span>') : ''}${v[response.header]}</td>`;

				// CONTENT
				let orderIndex = 0;
				$.each(v, (vk, vv) => {
					if (response.content_order[orderIndex] == vk) {
						newTable += `<td class="text-center align-middle mx-auto">${vv}</td>`;
						orderIndex++;
					}
				});

				// CLOSING
				newTable += `
					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown${v.id}" aria-haspopup="true" aria-expanded="false">
								Actions
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown${v.id}">
								<a href="${response.edit_url + '/' + v.id}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>`;

				if (response.type == 'users' || response.type == 'departments') {
					let managePermsURI = response.etc.perm_manage_url;
					let re = /{([^}]+)}/g;

					while (matches = re.exec(managePermsURI)) {
						managePermsURI = managePermsURI.replace(matches[0], v[matches[1]]);
					}

					newTable += `<a href="${managePermsURI}" class="dropdown-item"><i class="fas fa-user-lock mr-2"></i>Manage Permissions</a>`;

					// SPECIAL DEPARTMENT CASE
					if (response.type == 'departments') {

						if (response.etc.delete_url_perms) {
							let delURI = v['deleted_at'] == null ? response.etc.delete_url : response.etc.restore_url;
							while (matches = re.exec(delURI)) {
								delURI = delURI.replace(matches[0], v[matches[1]]);
							}
							newTable += `<a href="${delURI}" class="dropdown-item"><i class="fas fa-${v['deleted_at'] == null ? 'trash' : 'recycle'} mr-2"></i>${v['deleted_at'] == null ? 'Delete' : 'Restore'}</a>`;
						}

						if (response.etc.perma_delete_url_perms) {
							let permaDelURI = response.etc.perma_delete_url;
							while (matches = re.exec(permaDelURI)) {
								permaDelURI = permaDelURI.replace(matches[0], v[matches[1]]);
							}
							newTable += `<a href="${permaDelURI}" class="dropdown-item"><i class="fas fa-fire-alt mr-2"></i>Delete Permanently</a>`;
						}
					}
				}

				if (response.has_change_password) {
					let changePassURI = response.change_pass_URI;
					let re = /{([^}]+)}/g;

					while (matches = re.exec(changePassURI)) {
						changePassURI = changePassURI.replace(matches[0], v[matches[1]]);
					}

					newTable +=	`<a href="javascript:void(0);" class="dropdown-item change-password" id="scp-${v.id}" data-scp='{"preventDefault":true,"name":"${v[response.header]}","targetURI":"${changePassURI}","notify":true,"for":"#tr-${v.id}"}'>
						<i class="fas fa-lock mr-2"></i>Change Password
					</a>`;
				}

				newTable +=	`</div>
						</div>
					</td>
				</tr>`;
			});

			if (response.content.length <= 0) {
				newTable = `<tr><td class="text-center" colspan="${response.data_length + 1}">Nothing to display~</td></tr>`
			}

			$('#inner-content #table-content').html(newTable);
		});
	}
});