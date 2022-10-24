$(document).ready(() => {
	$(document).on('change keyup keydown', '.text-counter-input', (e) => {
		let obj = $(e.currentTarget);
		let parent = obj.parent();
		let counter = parent.find('.text-counter');
		let max = obj.attr('data-max');

		counter.text(max - obj.val().length);
		if (counter.text() < 0) {
			counter.addClass('bg-danger');
			obj.addClass('mark-danger');
		}
		else {
			counter.removeClass('bg-danger');
			obj.removeClass('mark-danger');
		}
	});

	$('.text-counter-input').trigger('change');
});