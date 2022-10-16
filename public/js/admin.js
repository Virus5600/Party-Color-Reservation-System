$(document).ready(() => {
	// All resizing events
	$(window).on('resize', (e) => {
		let win = $(e.target);
		let obj = $('[data-toggle=sidebar-collapse]');
		let target = $(obj.attr('data-target'));

		if (win.width() >= 992) {
			target.attr('aria-expanded', 'true');
			target.find('.aria-link').attr('aria-hidden', 'false');
			target.find('.aria-link').removeAttr('tabindex');
		}
		else {
			target.attr('aria-expanded', 'false');
			target.find('.aria-link').attr('aria-hidden', 'true');
			target.find('.aria-link').attr('tabindex', '-1');
		}
	});
	$(window).trigger('resize');

	// Toggle the sidebar on smaller screen
	$('[data-toggle=sidebar-collapse]').on('click', function(e) {
		let obj = $(e.currentTarget);
		let target = $(obj.attr('data-target'));

		if (target.hasClass("show")) {
			target.removeClass("show");
			target.attr('aria-expanded', 'false');
			target.find('.aria-link').attr('aria-hidden', 'true');
			target.find('.aria-link').attr('tabindex', '-1');
		}
		else {
			target.addClass("show");
			target.attr('aria-expanded', 'true');
			target.find('.aria-link').attr('aria-hidden', 'false');
			target.find('.aria-link').removeAttr('tabindex');
		}
	});

	// Disables an input while animation is in progress
	$(document).on('change', '.disable-while-animating', function(e) {
		let obj = $(e.currentTarget);
		let objAnim = obj;
		let objTarget = obj;

		if (typeof obj.attr('data-animating-target') != 'undefined')
			objAnim = $(obj.attr('data-animating-target'));
		if (typeof obj.attr('data-disable-target') != 'undefined')
			objTarget = $(obj.attr('data-disable-target'));

		objTarget.prop('disabled', true);

		objAnim.on('shown.bs.collapse hidden.bs.collapse', function(e) {
			objTarget.prop('disabled', false);
		});
	});

	// Select Picker
	$('select.selectpicker').selectpicker();
});