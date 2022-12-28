$(document).ready(() => {
	// Change submit to either "Updating" or "Submitting" after click
	$('[type=submit], [data-action]').on('click', (e) => {
		let btn = $(e.currentTarget);
		let action = btn.attr('data-action');
		let parentForm = btn.closest("form");

		// Checks if there's a 'needs-validation' class
		if (!parentForm.hasClass("needs-validation")) {
			// if there's none, add the class
			parentForm.addClass('needs-validation');
		}

		// Checks if there's a novalidate prop
		if (typeof parentForm[0].novalidate == 'undefined' || typeof parentForm[0].formnovalidate == 'undefined') {
			// if there's none, default the prop to true
			parentForm.prop("novalidate", true);
			parentForm.attr("novalidate", true);

			parentForm.prop("formnovalidate", true);
			parentForm.attr("formnovalidate", true);
		}

		// Checks for an id
		if (typeof parentForm.attr("id") == 'undefined') {
			// If there's no id, generate one
			parentForm.attr("id", `disableOnSubmit${Math.random().toString(16).slice(2)}`);
		}

		// If this button is already clicked
		if (btn.attr('data-clicked') == 'true') {
			// Prevent the event from being triggered once more
			e.preventDefault();
			e.stopPropagation();
		}
		// Otherwise...
		else {
			// Update inner html to the designated action text and spinner
			if (action == 'update')
				btn.html(`<div class="spinner-border spinner-border-sm text-light" role="status"><span class="sr-only"></span></div> Updating...`);
			else if (action == 'filter')
				btn.html(`<div class="spinner-border spinner-border-sm text-light" role="status"><span class="sr-only"></span></div> Filtering...`);
			else
				btn.html(`<div class="spinner-border spinner-border-sm text-light" role="status"><span class="sr-only"></span></div> Submitting...`);
			
			btn.addClass(`disabled cursor-default`);
			btn.attr('data-clicked', 'true');
		}

		// If continuous validation, uses the pseudo selectors, otherwise, uses the classes
		if (parentForm.attr("data-continuous-validation") == 'true')
			parentForm.addClass('was-validated');

		// Check if form is valid
		if (!document.forms[parentForm.attr('id')].reportValidity()) {
			e.preventDefault();
			e.stopPropagation();

			// If not, proceed to redo the earlier changes so button can be used to submit again
			btn.html(`Submit`)
				.removeClass(`disabled cursor-default`)
				.attr('data-clicked', 'false');

			parentForm.find(":invalid")
				.not(".dont-validate")
				.addClass("is-invalid")
				.removeClass("is-valid")
				.closest(".form-control:not(.bootstrap-select > select)")
				.addClass("is-invalid")
				.removeClass("is-valid");

			parentForm.find(":valid")
				.not(".dont-validate")
				.addClass("is-valid")
				.removeClass("is-invalid")
				.closest(".form-control:not(.bootstrap-select > select)")
				.addClass("is-valid")
				.removeClass("is-invalid");
			
			parentForm.removeClass(".was-validated")
				.find(".dont-validate")
				.removeClass("is-valid is-invalid")
				.closest(".form-control")
				.removeClass("is-valid is-invalid");
		}
	});

	$('form').on('submit', (e) => {$(this).find('[type=submit]').trigger('click');});
});