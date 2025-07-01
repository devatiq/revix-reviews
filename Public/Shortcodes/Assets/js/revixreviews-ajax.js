jQuery(document).ready(function($) {
	$('#revixreviews-feedback-form').on('submit', function(e) {
		e.preventDefault();

		const formData = $(this).serializeArray();
		formData.push({ name: 'nonce', value: revixreviews_ajax_obj.nonce });

		$.post(revixreviews_ajax_obj.ajax_url, formData, function(response) {
			if (response.success) {
				Swal.fire({
					title: 'Thank you!',
					text: response.data.message,
					icon: 'success'
				});
				$('#revixreviews-feedback-form')[0].reset();
			} else {
				Swal.fire({
					title: 'Error!',
					text: response.data.message,
					icon: 'error'
				});
			}
		}).fail(function() {
			Swal.fire({
				title: 'Server Error!',
				text: 'Something went wrong. Please try again.',
				icon: 'error'
			});
		});
	});
});
