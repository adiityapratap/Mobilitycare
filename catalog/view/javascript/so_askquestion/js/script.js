function sendData(product_id) {
	$.ajax({
		type: 'POST',
		dataType: "json",
		cache: false,
		headers: { "cache-control": "no-cache" },
		data: {
			isAjax: 1,
			product_id: product_id,
			name: jQuery('#askquestion_name_'+product_id).val(),
			postcode: jQuery('#askquestion_postcode_'+product_id).val(),
			state: jQuery('#askquestion_state_'+product_id).val(),
			email: jQuery('#askquestion_email_'+product_id).val(),
			phone: jQuery('#askquestion_telephone_'+product_id).val(),
			message: jQuery('#askquestion_comment_'+product_id).val()
		},
		url: 'index.php?route=extension/module/so_askquestion/sendData',
		beforeSend: function(){
			$('#content-send-'+product_id).button('loading');
			$('#askquestion_success_'+product_id).html('').hide();
			$('#askquestion_error_'+product_id).html('').hide();
		},
		success: function(data){
			if (data.status == 1) {
				$('#content-send-'+product_id).button('reset');
				$('#askquestion_error_'+product_id).html('');
				$('#askquestion_success_'+product_id).append(data.success).show();
				setTimeout(function(){$.fancybox.close()}, 3000);
				$('#ajax-form-'+product_id).each(function(){
					this.reset();
				});
				// location.reload();
			}
			else if (data.status == 2) {
				$('#askquestion_error_'+product_id).append(data.error).show();
				$('#content-send-'+product_id).button('reset');
			}
			else {
				$.each(data.errors, function(index, errors) {
					switch (errors.code)
					{
						case 701:
								$('#askquestion_name_'+product_id).val('');
								$('#askquestion_name_'+product_id).parent().removeClass('form-ok').addClass('form-error');
								$('#askquestion_name_'+product_id).attr("placeholder", errors.error);
							break;
						case 702:
								$('#askquestion_email_'+product_id).val('');
								$('#askquestion_email_'+product_id).parent().removeClass('form-ok').addClass('form-error');
								$('#askquestion_email_'+product_id).attr("placeholder", errors.error);
							break;
						case 703:
								$('#askquestion_telephone_'+product_id).val('');
								$('#askquestion_telephone_'+product_id).parent().removeClass('form-ok').addClass('form-error');
								$('#askquestion_telephone_'+product_id).attr("placeholder", errors.error);
							break;
							
							case 704:
								$('#askquestion_postcode_'+product_id).val('');
								$('#askquestion_postcode_'+product_id).parent().removeClass('form-ok').addClass('form-error');
								$('#askquestion_postcode_'+product_id).attr("placeholder", errors.error);
							break;
						case 705:
								$('#askquestion_comment_'+product_id).val('');
								$('#askquestion_comment_'+product_id).parent().removeClass('form-ok').addClass('form-error');
								$('#askquestion_comment_'+product_id).attr("placeholder", errors.error);
							break;
						case 404:
								$('#div-error-'+product_id).append(errors.error);
								$('#div-error-'+product_id).show();
							break;
					}
				});
				
				$('#content-send-'+product_id).button('reset');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			$('#askquestion_error_'+product_id).append('Please try again.').show();
			$('#content-send-'+product_id).button('reset');
			console.error && console.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	})
}