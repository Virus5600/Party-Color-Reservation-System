$(document).ready(() => {
	$(document).on('click', '#toggle-show-password, .toggle-show-password', (e) => {
		let obj = $(e.currentTarget);
		let target = $(obj.attr('data-target'));
		let icons = {
			show: obj.find('#show'),
			hide: obj.find('#hide')
		}

		if (target.attr('type') == 'password') {
			obj.attr('aria-label', 'Hide Password');
			target.attr('type', 'text');
			icons.show.removeClass('d-none');
			icons.hide.addClass('d-none');
		}
		else {
			obj.attr('aria-label', 'Show Password');
			target.attr('type', 'password');
			icons.show.addClass('d-none');
			icons.hide.removeClass('d-none');
		}
	});
});