try {
	// jQuery
	window.$ = window.jQuery = window.jquery = require('jquery');

	// jQuery UI
	require('jquery-ui/dist/jquery-ui.min.js');

	// popper.js
	window.Popper = require('@popperjs/core');

	// Bootstrap 4
	require('bootstrap/dist/js/bootstrap.min')
	require('bootstrap-select/dist/js/bootstrap-select.min')

	// Sweetalert 2
	window.Swal = require('sweetalert2/dist/sweetalert2.all.min');

	// Fontawesome 6
	require('@fortawesome/fontawesome-free/js/solid.min.js');
	require('@fortawesome/fontawesome-free/js/regular.min.js');
	require('@fortawesome/fontawesome-free/js/brands.min.js');
	require('@fortawesome/fontawesome-free/js/fontawesome.min.js');

	// Summernote
	require('summernote/dist/summernote-bs4.min.js');
	require('summernote/dist/lang/summernote-ja-JP.min.js');
} catch (e) {
	console.error(e);
}