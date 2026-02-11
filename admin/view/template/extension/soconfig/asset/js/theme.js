

$(document).ready(function() {
	//1.Change ColorPicker
	ChangeColor();
	ButtonSwitch();
	//alertTimeout();
	
	// 2.Datetimepicker
	$('.date').datetimepicker({
		language: document.cookie.match(new RegExp('language=([^;]+)'))[0],
		pickTime: false
	});
	
	
	// 3.Save settings
    $('.action-save-template').on('click',function(e){
        e.preventDefault();
        var self = this,
		data = $('#form-optimus').serialize(),
		$user_token = getUrlParameter('user_token');
		$store_id = getUrlParameter('store_id') ? getUrlParameter('store_id') : 0;
		
		
		$.ajax({
            type   : 'POST',
            url: 'index.php?route=extension/module/soconfig/saveThemeConfig&user_token='+$user_token+'&store_id='+$store_id,
            data   : data,
            beforeSend: function(){
              $(self).find('.fa').removeClass('fa-save').addClass('fa-spinner fa-spin');
              var iframe = $('#live_preview_iframe').contents();
              iframe.find('body').addClass('preloader');
            },
            success: function (response) {
                var data = jQuery.parseJSON(JSON.stringify(response));
                if(data.status){
                    document.getElementById('live_preview_iframe').contentWindow.location.reload(true);
                }
                $(self).find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-save');
            },
            error: function(){
                alert('Somethings wrong, Try again');
            }

        });

    });
	
	// 4.Select dropdown menu
	$(".select2-input").select2({});
	
	
	
	//----------Button Swicther -------------
	$('.so-ultimate-fieldset-header-inner').on('click',function(e){
        e.preventDefault();

        if( $(this).closest('.so-ultimate-fieldset').hasClass('active') ){
            return;
        }

        $('.so-ultimate-fieldset').removeClass('active');
        $(this).closest('.so-ultimate-fieldset').addClass('active');
        $('#so-ultimate-options').removeClass().addClass('active-so-ultimate-fieldset');
        $(this).closest('.so-ultimate-fieldset').find('.so-ultimate-group-list').find('.so-ultimate-group-wrap').first().addClass('active-group');
    });
	
	$('.so-ultimate-fieldset-toggle-icon').on('click',function(e){
        e.preventDefault();

        $('.so-ultimate-fieldset').removeClass('active');
        $('#frontend-themes, #so-ultimate-options').removeClass();
    });
	
	 $('.so-ultimate-group-header-box').on('click',function(e){
        e.preventDefault();

        if( $(this).closest('.so-ultimate-group-wrap').hasClass('active-group') ){
            $(this).closest('.so-ultimate-group-wrap').removeClass('active-group');
            return;
        }

        $('.so-ultimate-group-wrap').removeClass('active-group')
        $(this).closest('.so-ultimate-group-wrap').addClass('active-group');

    });
	
	var childParentEngine = function(){
		var classes = new Array();
		$("fieldset.parent").each(function(){
			var eleclass = $(this).attr('class').split(/\s/g);
			var $key = $.inArray("parent", eleclass);
			if( $key!=-1 ){
			  classes.push( eleclass[$key+1] );
			}
		});

		$("fieldset.parent").each(function(){

			var parent = $(this);
			var eleclass = $(this).attr('class').split(/\s/g);
			var childClassName = '.child';
			var conditionClassName = '';
			var i;
			
			for (i=0;i<eleclass.length;i++) {
			  if( $.inArray(eleclass[i], classes) < 0 ) {
				continue;
			  } else {

				var elecls =  '.' + eleclass[i];
				
				
				if( $(parent).prop('type')=='fieldset' ){
					$(childClassName+elecls).parents('.control-group').hide();
					  var selected = $(parent).find('input[type=radio]:checked');
					  var radios = $(parent).find('input[type=radio]');
					  var activeItems = conditionClassName+elecls+'_'+$(selected).val();
					  
					  var childitem =  $.trim(childClassName+elecls+activeItems);
					 
					  setTimeout(function(){
						$(childitem).parents('.control-group').show();
					  }, 100);
					$(radios).on("change", function(event){
						$(childClassName+elecls).parents('.control-group').hide();
						$(childClassName+elecls+conditionClassName+elecls+'_'+$.trim($(this).val())).parents('.control-group').show();
					  });
				}
				
			  }
			}
		});
	  
    }//end childParentEngine
	childParentEngine();
	
	
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
	
	//Select Home Style
	$(document).on('change', '.homepage-seleclist', function(event) {
		event.preventDefault();
		var $preview = $('#homepage-preview'),
			$keylayout = $(this).val(),
			$user_token = getUrlParameter('user_token'),
			$store_active = getUrlParameter('store_id'),
			$headerStyle = $('.header-seleclist'),
			$currentcolor = $('.field--listcolor select option').filter(":selected").val();
		
		$preview.find('img').attr('src', 'view/template/extension/soconfig/images/homepage/home' + $(this).val() + '.jpg');
		$preview.find('.layoutFormID').val( $keylayout);
		$headerStyle.val($keylayout).change();
		
		$.ajax({
			url: 'index.php?route=extension/module/soconfig/getTotalColorScheme&user_token='+$user_token+'&keylayout='+$keylayout ,
			dataType: 'json',
			success: function(json) {
				$.map(json, function(item) {
					var selectList = "<select name='seleclist'>";
					$.each(json, function (index, value) {
						($currentcolor == value['name']) ? $current = 'selected' : $current=''; 
						selectList += "<option "+$current+">" + value['name'] + "</option>";
					});
					selectList += "</select>";
					$select_id = $('.field--listcolor select');
					$select_id.html(selectList);
				});
				
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	   
	});
	
	// Select applyLayout
	$('#homepage-preview').on('click', '.applyHome', function () {
		if (confirm('Are you sure you want to apply default setting')) {
		var  $user_token = getUrlParameter('user_token');
		
		var	$store_active = typeof(getUrlParameter('store_id'))  != "undefined"  ? getUrlParameter('store_id'): '0';
		var	$keylayout = $('.layoutFormID').val();
			
			$.ajax({
				type   : 'POST',
				url: 'index.php?route=extension/module/soconfig/applyBaseHome&user_token='+$user_token+'&keylayout='+$keylayout+'&store_id='+ $store_active ,
				beforeSend: function(){
				  var iframe = $('#live_preview_iframe').contents();
				  iframe.find('body').addClass('preloader');
				},
				success: function (response) {
					document.getElementById('live_preview_iframe').contentWindow.location.reload(true);
				},
				error: function(){
					alert('Somethings wrong, Try again');
				}

			});
		}
	});
	
	//Select Blog Articles Style
	$(document).on('change', '.article-seleclist', function(event) {
		event.preventDefault();
		var $preview = $('#article-preview');
		$preview.find('img').attr('src', 'view/template/extension/soconfig/images/blogs/' + $(this).val() + '.png');
		
	});
	
	//Select Banners Style
	$(document).on('change', '.banner-seleclist', function(event) {
		event.preventDefault();
		var $preview = $('#banner-preview'),
			$keylayout = $(this).val();
		
		$preview.removeClass().addClass('preview-holder preview-holder--banners').addClass('banners-effect-'+$keylayout);
		
	});
	
	//Header Style Preview 
	headerPreview('select[name^="soconfig_general_store[typeheader]"]',$('.typeheader-preview'));
	headerPreview('select[name^="soconfig_general_store[typefooter]"]',$('.typefooter-preview'));
	
	$('#banner-preview').on('click', '.banners', function () {
	   $('#banner-preview .banners').removeClass("active");
	   $(this).addClass("active");
	   $(this).siblings("input:radio").prop("checked", true);
	});
	
	//liveColors Preview 
	$(document).on('change', '.field-list--liveColors .colorfield input', function(event) {
		event.preventDefault();
		
		var $preIframe = $("#live_preview_iframe").contents(),
			$headerparent =  $.parseJSON($('#headerparent').val()),
			$headertop =  $.parseJSON($('#headertop').val());
			$headercenter =  $.parseJSON($('#headercenter').val());
			$headerbottom =  $.parseJSON($('#headerbottom').val());
		
		$preIframe.find('#header').css("background-color", $headerparent['background']);
		$preIframe.find('#header').css("color", $headerparent['color']);
		
		$preIframe.find('.header-top').css("background-color", $headertop['background']);
		$preIframe.find('.header-top').css("color", $headertop['color']);
		
		$preIframe.find('.header-middle ').css("background-color", $headercenter['background']);
		$preIframe.find('.header-middle ').css("color", $headercenter['color']);
		
		$preIframe.find('.header-bottom ').css("background-color", $headerbottom['background']);
		$preIframe.find('.header-bottom ').css("color", $headerbottom['color']);
		
	});
})


function headerPreview(styleFooter,$footerPreview){
	$('.preview-holder--headerStyle',$footerPreview).removeClass('active');
	$('.headerStyle-'+$(styleFooter).val(),$footerPreview).addClass('active');
	
	$(document).on('change', styleFooter, function(event) {
		event.preventDefault();
		
		$('.preview-holder--headerStyle',$footerPreview).removeClass('active');
		$('.headerStyle-'+$(this).val(),$footerPreview).addClass('active');
		
	});
}

function liveColorData(){
	var $preIframe = $("#live_preview_iframe").contents();
	$('.so-ultimate-field-livecolor').each(function(){
		var $that = $(this),
		liveColor = {
			'background':$that.find('.so-ultimate-livecolor-background').val(),
			'color':$that.find('.so-ultimate-livecolor-color').val(),
		}
		
		$that.find('.so-ultimate-livecolors-input').val(JSON.stringify(liveColor));
		
	});
}


function ChangeColor() {
	var $colorfield = $('.colorfield input');
	$colorfield.each(function(i) {
		if (!$(this).val()) $(this).parent().find('.minicolors-swatch').addClass('input-clear-display'); 
	});	
	
	
	// Colorpicker 
	$colorfield.colorpicker({
		customClass: 'colorpicker-2x',
		align:'left',
        format: "hex",
		colorSelectors: {
                'black': '#000000',
                'white': '#ffffff',
                'red': '#FF0000',
                'default': '#777777',
                'primary': '#337ab7',
                'success': '#5cb85c',
                'info': '#5bc0de',
                'warning': '#f0ad4e',
                'danger': '#d9534f',
           },
		sliders: {saturation: {maxLeft: 150, maxTop: 150},hue: { maxTop: 150},alpha: { maxTop: 150}}
	}).on('changeColor.colorpicker', function(event){
		$(this).parent().find('.minicolors-swatch i').css("background-color", $(this).val());
		$(this).parent().find('.input-group-addon i').css("background-color", $(this).val());
		
		liveColorData();
		
		if(event.color.origFormat==null) {
		  //when select transparent color
		  $(this).parent().find('.minicolors-swatch').addClass('input-clear-display'); 
		} else {
		  $(this).parent().find('.minicolors-swatch').removeClass('input-clear-display');
		}
		
	});
	

}

function ButtonSwitch(){
	var tabs = $('.btn-toggle');
	tabs.each(function(i) {
		var tab = $(this).children('.btn');
		var ua = navigator.userAgent,
		event = (ua.match(/iPad/i)) ? "touchstart" : "click";
		tab.bind(event, function(e) {
			$(this).addClass(function() {
				if(!$(this).hasClass("btn-success"))return "btn-success ";
			});
			$(this).siblings().removeClass("btn-success");
		});
	});	
	
}

function alertTimeout (){
	var timer;
	clearTimeout(timer);
	timer = setTimeout(function () {
		$('.alert').addClass('fadeOut');
	}, 4000);
}

	