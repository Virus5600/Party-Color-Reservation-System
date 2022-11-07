function openInput(obj) {
	$("input[name=" + obj.attr("id") + "]:not([readonly])").trigger("click");
}

function swapImgFile(obj) {
	let targetImgContainer = $($(obj).attr('data-target-image-container'));
	let targetNameContainer = $($(obj).attr('data-target-name-container'));

	if (obj.files && obj.files[0]) {
		let reader = new FileReader();

		reader.onload = function(e) {
			targetImgContainer.attr("src", e.target.result);
			targetNameContainer.html(obj.files[0].name);
		}

		reader.readAsDataURL(obj.files[0]);

		targetImgContainer.attr('onerror', `this.src="${$(this).attr('data-default-src')}";$(this).removeAttr('onerror');`);
	}
	else {
		targetImgContainer.attr("src", targetImgContainer.attr('data-default-src'));
		targetNameContainer.html(targetNameContainer.attr ('data-default-name'));
	}
}

// Handles missing images for image-input
$($('.image-input-scope input[type=file]').attr('data-target-image-container')).bind("error", function(e) {
	let obj = $(e.currentTarget);
	$(obj).attr("src", $(obj).attr('data-default-src'));
});

$(document).ready(function() {
	// Profile Image Changing
	$(".image-input-scope .image-input-float").on("click", function(e) {
		openInput($(this));
	});

	$(".image-input-scope .image-input-float").on("keydown", function(e) {
		if (e.keyCode == 32) {
			e.preventDefault();
			openInput($(this));
		}
	});

	$(".image-input-scope .image-input input[type=file]").on("change", function(e) {
		swapImgFile(this);
	});

	$(".image-input-scope .image-input input[type=text]").on("change, keyup", function(e) {
		let obj = $(e.currentTarget);

		if (obj.val().length == 0) {
			$(obj.attr('data-target-image-container')).attr("src", $(obj.attr('data-target-image-container')).attr('data-default-src'));
		}
		else {
			$(obj.attr('data-target-image-container')).attr("src", obj.val());
			$(obj.attr('data-target-image-container')).attr('onerror', `this.src="${$(this).attr('data-default-src')}";$(this).removeAttr('onerror');`);
		}
	});

	// Profile Image Changing method swapping (File to URL and vice versa)
	$($(".image-input-scope").attr('data-settings')).on('change', function(e) {
		let obj = $(e.currentTarget).find('.image-input-switch');
		let fileInp = $('.image-input-scope input[type=file]');
		let textInp = $('.image-input-scope input[type=text]');

		$(".image-input-scope input[name=avatar], .image-input-scope input[data-role=image-input]").removeAttr('name');

		if (!obj.prop('checked'))
			fileInp.attr('name', 'avatar');
		else
			textInp.attr('name', 'avatar');
	});

	// Remove Image (AJAX)
	$('.image-input-reset').on('click', function(e) {
		let obj = $(e.target);

		let dataPacket = {
			_token: $('[name=_token]').val(),
			type: obj.attr('data-category'),
			id: obj.attr('data-id')
		}

		$.post(
			obj.attr('data-target-url'), dataPacket
		).done((response) => {
			// VALIDATION ERROR
			if (response.type == 'validation_error') {
				let msg = "Parameter error:\n";

				$.each(response.errors, (k, v) => {
					msg += "\n[" + k + "]: " + v;
				});

				console.warn(msg);

				Swal.fire({
					icon: `info`,
					title: 'Something went wrong... Please contact web developers',
					position: `top`,
					showConfirmButton: false,
					toast: true,
					background: `#17a2b8`,
					customClass: {
						title: `text-white`,
						popup: `px-3`
					},
				});
			}
			// FATAL ERROR
			else if (response.type == 'error') {
				console.error('Something went wrong:\n', response.error);

				Swal.fire({
					icon: 'warning',
					title: 'An error occured, please contact the web developers immediately',
					position: 'top',
					showConfirmButton: false,
					toast: true,
					background: `#dc3545`,
					customClass: {
						title: `text-white`,
						popup: `px-3`
					},
				});
			}
			// SUCCESS
			else if (response.type == 'success') {
				Swal.fire({
					icon: `info`,
					title: response.message,
					position: `top`,
					showConfirmButton: false,
					toast: true,
					timer: 10000,
					background: `#17a2b8`,
					customClass: {
						title: `text-white`,
						popup: `px-3`
					},
				});

				$(obj.attr('data-target')).find('img.avatar').attr('src', response.fallback);
			}
			// EMPTY
			else if (response.type == 'empty') {
				console.warn(response.message);
			}
		}).fail((response) => {
			Swal.fire({
				icon: 'warning',
				title: 'An error occured, please contact the web developers immediately',
				position: 'top',
				showConfirmButton: false,
				toast: true,
				background: `#dc3545`,
				customClass: {
					title: `text-white`,
					popup: `px-3`
				},
			});
		});
	})
});