jQuery(document).ready(function ($) {
	$('form.utmm-export-form').on('submit', function (event) {
		var $form = $(this);
		event.preventDefault();
		var currentDate = new Date(),
			filename = 'utmm-export-' + currentDate.getDate() + '-' + ( currentDate.getMonth() + 1 ) + '-' + currentDate.getFullYear() + '-' + currentDate.getTime() + '',
			lead_start_date = $(':input[name="lead_start_date"]', $form).val(),
			lead_end_date = $(':input[name="lead_end_date"]', $form).val(),
			fields = $(':input[name="fields[]"]', $form).val(),
			nonce = $(':input[name="_wpnonce"]', $form).val();

		var data = {
			lead_start_date: lead_start_date,
			lead_end_date: lead_end_date,
			fields: fields,
			filename: filename,
			_wpnonce: nonce,
			action: 'utmm_export_csv',
		};

		// Disable multiple submissions while processing.
		$(':input', $form).prop('disabled', true);

		processStep(1, $form, data);
	});

	function processStep(step, $form, data) {
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: { ...data, step: step },
			success: function (response) {
				if ( response.data.url && 'finished' === response.data.step ) {
					$('.spinner').removeClass('is-active');
					$(':input', $form).prop('disabled', false);
					window.location = response.data.url;
				} else {
					$('.spinner').addClass('is-active');
					processStep(response.data.step, $form, data);
				}
			}
		})
	}
});
