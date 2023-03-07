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

	if (e.type == 'submit') {
		e.preventDefault();

		parent = $(e.target);
		path = parent.attr('action');

		dataPacket = parent.serializeFormJSON();

		sendAjaxRequest = true;
	}
	else if (e.keyCode == 13) {
		e.preventDefault();

		parent = $(e.currentTarget);
		path = parent.attr('action');

		dataPacket = parent.serializeFormJSON();

		sendAjaxRequest = true;
	}

	if (sendAjaxRequest) {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
				'Authorization': `Bearer ${$("meta[name=bearer]").attr('content')}`
			}
		});

		$.post(
			path, dataPacket
		).done((response) => {
			console.table(response.content);

			if (response.missing) {
				window.location = response.redirect;
			}

			$('#inner-content #table-content').html(response.content.items);
			$('#inner-content #table-paginate').html(response.content.paginate);
			$('.enlarge-on-hover .dropdown').trigger('hide.bs.dropdown');

		}).fail((response) => {
			console.log(response.responseText);
			document.write(response.responseText);
		});
	}
});