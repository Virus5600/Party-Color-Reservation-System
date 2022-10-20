/**
 * Warns the user that they're leaving without saving their changes.
 * @param urlTo String value. The page they'r attempting to open.
 */
function confirmLeave(urlTo, title="本気ですか？", message = "未保存の変更があります。") {
	Swal.fire({
		icon: 'warning',
		html: `<h4>${title}</h4><p>${message}</p>`,
		showDenyButton: true,
		confirmButtonText: 'はい',
		denyButtonText: 'いええ'
	}).then((result) => {
		if (result.isConfirmed) {
			window.location.href = urlTo;
		}
	});
}