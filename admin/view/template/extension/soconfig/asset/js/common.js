
$(document).ready(function() {
	//2. Viewport Wide Preview
	templatePreview();
	
})



function scrollToSection(iframe){
	var $root = $('html, body');
	var iframe = $('#live_preview_iframe').contents();
		
	$('.so-ultimate-group-header').on('click', '.so-ultimate-group-header-box', function () {
		$('html,body', iframe).animate({scrollTop: '0px'}, 500);
		
	});
	
	$('.so-ultimate-group-footer').on('click', '.so-ultimate-group-header-box', function () {
		$('html,body', iframe).animate({scrollTop:  iframe.find("footer").offset().top}, 500);
		
	});
}

	
function templatePreview(){
	var tbApp = {};
	var triggerIframeResize;
	$("html").addClass("live_preview");
	iframe = document.createElement("iframe");
	var urlCatalog = document.settingsform.storeUrl.value;  
		url = $.jurlp(urlCatalog),
	
	url.query({
		forceHTTPS         : location.protocol === 'https:' ? 1 : 0,
		setLivePreviewMode : url
	});

	if (location.protocol === 'https:') {
		url.scheme("https://");
	}

	iframe.id = "live_preview_iframe";
	iframe.src = url.url();
	iframe.onload = function() {
		scrollToSection($(this));
		
		triggerIframeResize = function($this) {
			$this.addClass('is-selected');
			$this.siblings().removeClass("is-selected");
			$('body').removeClass('sidebar--is-closed');
			
			return true;
		};
			

		
	}
	

	$('<div id="live_preview"></div>').appendTo("body").append(iframe);
	
	$('<div id="so_screen_switch">' +
	   '<div class="so_view_store"><a class="viewStore" href="'+urlCatalog+'" target="_blank"><svg class="icon-gm icon-gm-store" width="25" height="25"><use xlink:href="#icon-store"></use></svg> <span>View Store </span></a></div>' +
	  '  <div class="so_buttons_group">' +
	  '    <a id="so_button_screen_desktop" class="button_switch is-selected" href="javascript:;"><svg class="desktop" width="24" height="24"><use xlink:href="#desktop"/></svg></a>' +
	  '    <a id="so_button_screen_tablet"  class="button_switch " href="javascript:;"><svg class="tablet" width="24" height="24"><use xlink:href="#tablet" /></svg></a>' +
	  '    <a id="so_button_screen_mobile"  class="button_switch" href="javascript:;"><svg class="phone"  width="24" height="24"><use xlink:href="#phone"/></svg></a>' +
	  '    <a id="so_viewport_wide"  class="button_switch " href="javascript:;"><svg class="viewport-wide"  width="24" height="24"><use xlink:href="#viewport-wide"/></svg></a>' +
	  '  </div>' +
	  '  <div class="so_buttons_group"></div>' +
	  '</div>').appendTo("body");

	
	$('#so_viewport_wide').on('click',function(e){
		if (triggerIframeResize === undefined) return false;
		$('#live_preview').css('width', '');
		triggerIframeResize($(this));
		$('body').addClass('sidebar--is-closed');
		
	})
	
	$('#so_button_screen_desktop').on('click',function(e){
		if (triggerIframeResize === undefined) return false;
		$('#live_preview').css('width', '');
		triggerIframeResize($(this));
	})
	
	$('#so_button_screen_laptop').on('click',function(e){
		if (triggerIframeResize === undefined) return false;
		$('#live_preview').css('width', 1024);
		triggerIframeResize($(this));
	})
	
	$('#so_button_screen_tablet').on('click',function(e){
		if (triggerIframeResize === undefined) return false;
		$('#live_preview').css('width', 768);
		triggerIframeResize($(this));
	})
	
	$('#so_button_screen_mobile_landscape').on('click',function(e){
		if (triggerIframeResize === undefined) return false;
		$('#live_preview').css('width', 480);
		triggerIframeResize($(this));
	})
	
	$('#so_button_screen_mobile').on('click',function(e){
		if (triggerIframeResize === undefined) return false;
		$('#live_preview').css('width', 360);
		triggerIframeResize($(this));
	})
};
	
	