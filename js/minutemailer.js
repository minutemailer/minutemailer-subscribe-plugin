jQuery.noConflict();
jQuery(document).ready(function($){

    $('.minutemailer-widget-signup').on('submit', function(e){
    	// Stop submit
		e.preventDefault();

		// Get form values
		var $form = $(this);
		var url = $form.attr('action');
		var signup_name = $(this).find('.minutemailer-signup-name').val();
		var signup_email = $(this).find('.minutemailer-signup-email').val();
		var spam_validation = $(this).find('.minutemailer-hide-me').val();

		// Get result element
		$resultMessage = $form.find('.minutemailer-submit-result');

		// Check bot input field
		if(spam_validation){
			$resultMessage.html('Sorry, you have filled in a forbidden field only used to stop spam bots.');
			$resultMessage.show();
			return;
		}
		
		$.ajax({
			url: url,
			type: 'POST',
			crossDomain: true,
			data: "name=" + signup_name + "&email=" + signup_email,
			dataType: 'json',
			headers: {
				'Access-Control-Allow-Credentials': true
			},
			success: function(data) {
				if (data.error !== false || data.status !== 200) {
					$resultMessage.html('An unknown error has occured.');
				}
				// If any data is present show return message from Minutemailer.
				else if(data.data){
					$resultMessage.html(data.data);
				}

				// If data is empty the submit was successful.
				if(!data.data){
					$form.find('input').prop('disabled', true);
				}
				
				$resultMessage.show();
			}
		});
    });
});