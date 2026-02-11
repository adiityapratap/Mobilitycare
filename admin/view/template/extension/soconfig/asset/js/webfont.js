
function webfontData(){
	$('.so-ultimate-field-webfont').each(function(){
		var $that = $(this),
		
		webfont = {
			'fontFamily' : $that.find('.so-ultimate-webfont-list').val(),
			'fontSize'	: $that.find('.so-ultimate-webfont-size-input').val(),
			'fontWeight' : $that.find('.so-ultimate-webfont-weight-list').val(),
			'fontStyle' : $that.find('.so-ultimate-webfont-style-list').val(),
			'fontSubset' : $that.find('.so-ultimate-webfont-subset-list').val(),
		
		}
		console.log(JSON.stringify(webfont));
		$that.find('.so-ultimate-webfont-input').val(JSON.stringify(webfont) )

	});
}

$(function($) {
	
	//Get param adress url
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	};
	
	
	//Web Fonts
	$(document).on('change', '.so-ultimate-webfont-list', function(event) {
		event.preventDefault();
		
		var $that = $(this),
			fontName = $that.val()
			user_token = getUrlParameter('user_token');

		var systemFonts = [
			'Arial',
			'Tahoma',
			'Verdana',
			'Helvetica',
			'Times New Roman',
			'Trebuchet MS',
			'Georgia'
		];
		
		webfontData();
		if($.inArray(fontName, systemFonts) !== -1) {
			$that.closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-subset-list').html('').trigger("liszt:updated");
		} else {
			$.ajax({
				url: 'index.php?route=extension/module/soconfig/changeFontVariants&user_token='+user_token+'&fontName='+fontName,
				dataType : "html",
				success: function (response) {
					var font = $.parseJSON(response);
					$that.closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-subset-list').html(font.subsets).trigger("liszt:updated");
				}
			});

			var font = $that.val().replace(" ", "+");
			$('head').append("<link href='//fonts.googleapis.com/css?family="+ font +":100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic' rel='stylesheet' type='text/css'>");
		}
		
        $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-preview').fadeIn().css('font-family', $(this).val());

        return false;
	});
	
	// Font Size
	$(document).on('change', '.so-ultimate-webfont-size-input', function(event) {
		event.preventDefault();
		 webfontData();
        $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-preview').fadeIn().css({
        	'font-family': $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-list').val(),
        	'font-size': $(this).val() + 'px'
		});
	});

	// Font Weight
	$(document).on('change', '.so-ultimate-webfont-weight-list', function(event) {
		event.preventDefault();
		webfontData();
        $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-preview').fadeIn().css({
        	'font-family': $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-list').val(),
        	'font-size': $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-size-input').val() + 'px',
        	'font-weight': $(this).val()
		});
	});
	
	// Font Style
	$(document).on('change', '.so-ultimate-webfont-style-list', function(event) {
		event.preventDefault();
		webfontData();
        $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-preview').fadeIn().css({
			'font-family': $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-list').val(),
			'font-size': $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-size-input').val() + 'px',
        	'font-weight': $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-weight-list').val(),
        	'text-transform': $(this).val()
		});
    });

	//Font Subset
	$('.list-font-subset').on('change', function(event) {
		event.preventDefault();
		webfontData();
		var font = $(this).closest('.so-ultimate-field-webfont').find('.so-ultimate-webfont-list').val().replace(" ", "+");
		$('head').append("<link href='//fonts.googleapis.com/css?family="+ font +":300,400,500,600,700,800&subset="+ $(this).val() +"' rel='stylesheet' type='text/css'>");
    });
	
	
    //Update Fonts list
    $('.btn-update-so-ultimate-fonts').on('click', function(event){
        event.preventDefault();

        var $that   = $(this),
		user_token = getUrlParameter('user_token');
      
        $.ajax({
            url: 'index.php?route=extension/module/soconfig/updateGoogleFontList&user_token='+ user_token,
			dataType : "html",
            beforeSend: function(){
                $that.prepend('<i class="fa fa-spinner fa-spin"></i> ');
            },
            success: function (response) {
				var data = $.parseJSON(response);
				
				if (data.status){
					$that.after(data.message);
					$that.find('.fa-spinner').remove();
					$that.next().delay(1000).fadeOut(300, function(){
						$(this).remove();
					});
				}
            },
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
        });

        return false;

    });

});