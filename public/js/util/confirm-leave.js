/**
 * Warns the user that they're leaving without saving their changes.
 * @param urlTo String value. The page they'r attempting to open.
 */
function confirmLeave(urlTo, title="Are you sure?", message = "You might have unsaved changes.") {
	Swal.fire({
		icon: 'warning',
		html: `<h4>${title}</h4><p>${message}</p>`,
		showDenyButton: true,
		confirmButtonText: 'Yes',
		denyButtonText: 'No'
	}).then((result) => {
		if (result.isConfirmed) {
			window.location.href = urlTo;
		}
	});
}