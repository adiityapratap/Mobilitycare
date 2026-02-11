<?php

class ModelExtensionModuleSoTopbar extends Model {

	public function getSocialNetworkType() {
		$this->load->model('setting/setting');
		$setting = $this->model_setting_setting->getSetting('module_so_topbar');
		$now = date('Y-m-d H:i:s');
		$html = '';
		if (isset($setting['module_so_topbar_social_network_show']) && $setting['module_so_topbar_social_network_show'] && $setting['module_so_topbar_social_network_date_start'] <= $now && ($setting['module_so_topbar_social_network_date_end'] == '' || $setting['module_so_topbar_social_network_date_end'] >= $now)) {
			$html .= '<div class="so-topbar social-type">
				<div class="so-topbar-inner">
					<div class="so-topbar-text">
						'.$setting['module_so_topbar_social_network_heading_text'][$this->config->get('config_language_id')].'
					</div>
					<div class="so-topbar-icon">';
						if (isset($setting['module_so_topbar_social_network_fb_show']) && $setting['module_so_topbar_social_network_fb_show']) {
							$html .= '<a href="'.$setting['module_so_topbar_social_network_fb_url'].'" class="fb"><i class="fa fa-facebook-square"></i></a>';
						}
						if (isset($setting['module_so_topbar_social_network_rss_show']) && $setting['module_so_topbar_social_network_rss_show']) {
							$html .= '<a href="'.$setting['module_so_topbar_social_network_rss_url'].'" class="rss"><i class="fa fa-rss-square"></i></a>';
						}
						if (isset($setting['module_so_topbar_social_network_tw_show']) && $setting['module_so_topbar_social_network_tw_show']) {
							$html .= '<a href="'.$setting['module_so_topbar_social_network_tw_url'].'" class="twitter"><i class="fa fa-twitter-square"></i></a>';
						}
						if (isset($setting['module_so_topbar_social_network_google_show']) && $setting['module_so_topbar_social_network_google_show']) {
							$html .= '<a href="'.$setting['module_so_topbar_social_network_google_url'].'" class="google"><i class="fa fa-google-plus-square"></i></a>';
						}
						if (isset($setting['module_so_topbar_social_network_linkedin_show']) && $setting['module_so_topbar_social_network_linkedin_show']) {
							$html .= '<a href="'.$setting['module_so_topbar_social_network_linkedin_url'].'" class="linkedin"><i class="fa fa-linkedin-square"></i></a>';
						}
						if (isset($setting['module_so_topbar_social_network_youtube_show']) && $setting['module_so_topbar_social_network_youtube_show']) {
							$html .= '<a href="'.$setting['module_so_topbar_social_network_youtube_url'].'" class="youtube"><i class="fa fa-youtube-square"></i></a>';
						}
						if (isset($setting['module_so_topbar_social_network_pinterest_show']) && $setting['module_so_topbar_social_network_pinterest_show']) {
							$html .= '<a href="'.$setting['module_so_topbar_social_network_pinterest_url'].'" class="google"><i class="fa fa-pinterest-square"></i></a>';
						}
						if (isset($setting['module_so_topbar_social_network_skype_show']) && $setting['module_so_topbar_social_network_skype_show']) {
							$html .= '<a href="'.$setting['module_so_topbar_social_network_skype_url'].'" class="skype"><i class="fa fa-skype"></i></a>';
						}
					$html .= '</div>
				</div>';
				if (isset($setting['module_so_topbar_allow_close']) && $setting['module_so_topbar_allow_close']) {
					$html .= '<div class="so-topbar-close"><i class="fa fa-close"></i></div>';
				}
			$html .= '</div>';
			$html .= '<script>
				jQuery(document).ready(function($) {
					$(window).load(function () {
					  	var date = new Date();
				      	date.setTime(date.getTime() + '.$setting['module_so_topbar_time_cookie'].' * 60 * 60 * 1000);

					  	if ($.cookie("_sotopbar") != "true") {
						    $(".so-topbar").removeClass("topbar_opened").removeClass("topbar_closed").addClass("topbar_opened");
						    $(document).on("click", ".so-topbar-close", function (e) {
						    	$.cookie("_sotopbar", "true", {
						        	expires: date
						      	});

								if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
						    });
					  	}
					  	else {
					  		$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
					  		$(".so-topbar .so-topbar-close").removeClass("topbar_clicked").addClass("topbar_clicked");
					  		$(".so-topbar .so-topbar-close").find("i").removeClass("fa-close").addClass("fa-angle-double-down");
					  		$(document).on("click", ".so-topbar-close", function (e) {
					  			$.cookie("_sotopbar", "false", {
						        	expires: date
						      	});

					  			if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
					  		})
					  	}
					});
				})
			</script>';
		}

		return $html;
	}

	public function getImageBannerType() {
		$this->load->model('setting/setting');
		$this->load->model('tool/image');
		$setting = $this->model_setting_setting->getSetting('module_so_topbar');
		$now = date('Y-m-d H:i:s');
		$html = '';
		if (isset($setting['module_so_topbar_image_banner_show']) && $setting['module_so_topbar_image_banner_show'] && $setting['module_so_topbar_image_banner_date_start'] <= $now && ($setting['module_so_topbar_image_banner_date_end'] == '' || $setting['module_so_topbar_image_banner_date_end'] >= $now)) {
			if ((int)$setting['module_so_topbar_image_banner_width'] && (int)$setting['module_so_topbar_image_banner_height']) {
				$_image = $this->model_tool_image->resize($setting['module_so_topbar_image_banner'], $setting['module_so_topbar_image_banner_width'], $setting['module_so_topbar_image_banner_height']);
			}
			else {
				if ($this->request->server['HTTPS']) {
					$_image = $this->config->get('config_ssl') . 'image/' . $setting['module_so_topbar_image_banner'];
				} else {
					$_image = $this->config->get('config_url') . 'image/' . $setting['module_so_topbar_image_banner'];
				}
			}
			$html .= '<div class="so-topbar image-banner">
				<div class="so-topbar-inner">
					<a href="'.$setting['module_so_topbar_image_banner_button_link'].'">
						<img src="'.$_image.'" alt="Image Banner" />
					</a>';
					if (isset($setting['module_so_topbar_image_banner_show_button']) && $setting['module_so_topbar_image_banner_show_button']) {
						$html .= '<div class="so-topbar-button">
							<a href="'.$setting['module_so_topbar_image_banner_button_link'].'" class="button-link">'.$setting['module_so_topbar_image_banner_button_text'][$this->config->get('config_language_id')].'</a>
						</div>';
					}
				$html .= '</div>';
				if (isset($setting['module_so_topbar_allow_close']) && $setting['module_so_topbar_allow_close']) {
					$html .= '<div class="so-topbar-close"><i class="fa fa-close"></i></div>';
				}
			$html .= '</div>';
			$html .= '<script>
				jQuery(document).ready(function($) {
					$(window).load(function () {
					  	var date = new Date();
				      	date.setTime(date.getTime() + '.$setting['module_so_topbar_time_cookie'].' * 60 * 60 * 1000);

					  	if ($.cookie("_sotopbar") != "true") {
						    $(".so-topbar").removeClass("topbar_opened").removeClass("topbar_closed").addClass("topbar_opened");
						    $(document).on("click", ".so-topbar-close", function (e) {
						    	$.cookie("_sotopbar", "true", {
						        	expires: date
						      	});

								if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
						    });
					  	}
					  	else {
					  		$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
					  		$(".so-topbar .so-topbar-close").removeClass("topbar_clicked").addClass("topbar_clicked");
					  		$(".so-topbar .so-topbar-close").find("i").removeClass("fa-close").addClass("fa-angle-double-down");
					  		$(document).on("click", ".so-topbar-close", function (e) {
					  			$.cookie("_sotopbar", "false", {
						        	expires: date
						      	});

					  			if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
					  		})
					  	}
					});
				})
			</script>';
		}

		return $html;
	}

	public function getSubcribeNewsletterType() {
		$this->load->model('setting/setting');
		$setting = $this->model_setting_setting->getSetting('module_so_topbar');
		$now = date('Y-m-d H:i:s');
		$html = '';

		if (isset($setting['module_so_topbar_subcribe_newsletter_show']) && $setting['module_so_topbar_subcribe_newsletter_show'] && $setting['module_so_topbar_subcribe_newsletter_date_start'] <= $now && ($setting['module_so_topbar_subcribe_newsletter_date_end'] == '' || $setting['module_so_topbar_subcribe_newsletter_date_end'] >= $now)) {
			$html .= '<div class="so-topbar subscribe-newsletter">
				<div class="so-topbar-inner">
					<div class="so-topbar-text">'.$setting['module_so_topbar_subcribe_newsletter_heading_text'][$this->config->get('config_language_id')].'</div>
					<div class="input-box">
						<input type="email" placeholder="Your email address..." name="txtemail" />
					</div>';
					if (isset($setting['module_so_topbar_subcribe_newsletter_show_button']) && $setting['module_so_topbar_subcribe_newsletter_show_button']) {
					$html .= '<div class="so-topbar-button">
							<button type="submit" onclick="return SubscribeNewsletter();" name="submit">'.$setting['module_so_topbar_subcribe_newsletter_button_text'][$this->config->get('config_language_id')].'</button>
						</div>';
					}
				$html .= '</div>';
				if (isset($setting['module_so_topbar_allow_close']) && $setting['module_so_topbar_allow_close']) {
					$html .= '<div class="so-topbar-close"><i class="fa fa-close"></i></div>';
				}
			$html .= '</div>';
			$html .= '<script>
				function SubscribeNewsletter() {
					var emailpattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        			var email = jQuery(".so-topbar input[name=\'txtemail\']").val();
        			if(email != "") {
        				if(!emailpattern.test(email)) {
			                alert (\'Invalid Email\');
			                return false;
			            }
        			}
					alert ("Please install the module so newletter custom popup !");
					return false;
				}
				jQuery(document).ready(function($) {
					$(".so-topbar input[name=\'txtemail\']").keypress(function(event) {
					  if (event.which == 13) {
					        SubscribeNewsletter();
					    }
					});

					$(window).load(function () {
					  	var date = new Date();
				      	date.setTime(date.getTime() + '.$setting['module_so_topbar_time_cookie'].' * 60 * 60 * 1000);

					  	if ($.cookie("_sotopbar") != "true") {
						    $(".so-topbar").removeClass("topbar_opened").removeClass("topbar_closed").addClass("topbar_opened");
						    $(document).on("click", ".so-topbar-close", function (e) {
						    	$.cookie("_sotopbar", "true", {
						        	expires: date
						      	});

								if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
						    });
					  	}
					  	else {
					  		$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
					  		$(".so-topbar .so-topbar-close").removeClass("topbar_clicked").addClass("topbar_clicked");
					  		$(".so-topbar .so-topbar-close").find("i").removeClass("fa-close").addClass("fa-angle-double-down");
					  		$(document).on("click", ".so-topbar-close", function (e) {
					  			$.cookie("_sotopbar", "false", {
						        	expires: date
						      	});

					  			if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
					  		})
					  	}
					});
				})
			</script>';
		}

		return $html;
	}

	public function getTwitterFeedSliderType() {
		$this->load->model('setting/setting');
		$setting = $this->model_setting_setting->getSetting('module_so_topbar');
		$now = date('Y-m-d H:i:s');
		$html = '';

		if (isset($setting['module_so_topbar_twitter_feed_slider_show']) && $setting['module_so_topbar_twitter_feed_slider_show'] && $setting['module_so_topbar_twitter_feed_slider_date_start'] <= $now && ($setting['module_so_topbar_twitter_feed_slider_date_end'] == '' || $setting['module_so_topbar_twitter_feed_slider_date_end'] >= $now)) {
			$html .= '<div class="so-topbar twitter-feed-slider">';
			$html .= '<div class="so-topbar-inner">';
			$html .= '<div id="content-twiter" class="ts-items"></div>';
			$html .= '</div>';
			$html .= '<script type="text/javascript">
				jQuery(document).ready(function($){
					var config = {
						"id": "'.$setting['module_so_topbar_twitter_feed_slider_id'].'",
						"domId": "",
						"maxTweets": '.$setting['module_so_topbar_twitter_feed_slider_count'].',
						"enableLinks": true,
						"showUser": false,
						"showTime": false,
						"showInteraction": false,
						"customCallback": handleTweets
					};
					
					function handleTweets(tweets){
						var x = tweets.length;
						var n = 0;
						var j = 0;
						var element = document.getElementById("content-twiter");
						var start = 0;
						var html = "<ul class=\"list-item owl2-carousel\">";
						while(n < x) {
							html += "<li class=\"ts-item item\">" + tweets[n].replace("<br>", " ") + "</li>";
							n++;
						}
						html += "</ul>";
						element.innerHTML = html;
					}
					twitterFetcher.fetch(config);

					$(window).load(function() {
						var owl = $(".so-topbar .owl2-carousel");
						owl.owlCarousel2({
							items: 1,
							nav: true,
							navText: [ "<i class=\"fa fa-caret-up\">", "<i class=\"fa fa-caret-down\">" ],
							lazyLoad: true,
							autoHeight: true,
							dots: false,
							animateOut: "slideOutDown",
  							animateIn: "slideInUp"
						});
					})
				})
			</script>';
			if (isset($setting['module_so_topbar_allow_close']) && $setting['module_so_topbar_allow_close']) {
				$html .= '<div class="so-topbar-close"><i class="fa fa-close"></i></div>';
			}
			$html .= '</div>';
			$html .= '<script>
				jQuery(document).ready(function($) {
					$(window).load(function () {
					  	var date = new Date();
				      	date.setTime(date.getTime() + '.$setting['module_so_topbar_time_cookie'].' * 60 * 60 * 1000);

					  	if ($.cookie("_sotopbar") != "true") {
						    $(".so-topbar").removeClass("topbar_opened").removeClass("topbar_closed").addClass("topbar_opened");
						    $(document).on("click", ".so-topbar-close", function (e) {
						    	$.cookie("_sotopbar", "true", {
						        	expires: date
						      	});

								if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
						    });
					  	}
					  	else {
					  		$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
					  		$(".so-topbar .so-topbar-close").removeClass("topbar_clicked").addClass("topbar_clicked");
					  		$(".so-topbar .so-topbar-close").find("i").removeClass("fa-close").addClass("fa-angle-double-down");
					  		$(document).on("click", ".so-topbar-close", function (e) {
					  			$.cookie("_sotopbar", "false", {
						        	expires: date
						      	});

					  			if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
					  		})
					  	}
					});
				})
			</script>';
		}

		return $html;
	}

	public function getCountdown1Type() {
		$this->load->model('setting/setting');
		$setting = $this->model_setting_setting->getSetting('module_so_topbar');
		$now = date('Y-m-d H:i:s');
		$html = '';

		if (isset($setting['module_so_topbar_countdown_layout1_show']) && $setting['module_so_topbar_countdown_layout1_show'] && $setting['module_so_topbar_countdown_layout1_date_start'] <= $now && ($setting['module_so_topbar_countdown_layout1_date_end'] == '' || $setting['module_so_topbar_countdown_layout1_date_end'] >= $now)) {
			$html .= '<script>
				function getTimeRemaining(endtime){
				  	var t = Date.parse(endtime) - Date.parse(new Date());
				  	var seconds = Math.floor( (t/1000) % 60 );
				  	var minutes = Math.floor( (t/1000/60) % 60 );
				  	var hours = Math.floor( (t/(1000*60*60)) % 24 );
				  	var days = Math.floor( t/(1000*60*60*24) );
				  	return {
				    	"total": t,
				    	"days": days,
				    	"hours": hours,
				    	"minutes": minutes,
				    	"seconds": seconds
				  	};
				}

				function initializeClock(id, endtime) {
					var clock = document.getElementById(id);
					var daysSpan = clock.querySelector(".days");
					var hoursSpan = clock.querySelector(".hours");
					var minutesSpan = clock.querySelector(".minutes");
					var secondsSpan = clock.querySelector(".seconds");

				  	function updateClock() {
				    	var t = getTimeRemaining(endtime);
				    	daysSpan.innerHTML = t.days + ":";
				    	hoursSpan.innerHTML = ("0" + t.hours).slice(-2) + ":";
				    	minutesSpan.innerHTML = ("0" + t.minutes).slice(-2) + ":";
				    	secondsSpan.innerHTML = ("0" + t.seconds).slice(-2);

				    	if (t.total <= 0) {
				      		clearInterval(timeinterval);
				    	}
				  	}

				  	updateClock();
				  	var timeinterval = setInterval(updateClock, 1000);
				}

				window.onload = function () {
					var deadline = new Date("'.$setting['module_so_topbar_countdown_layout1_date_end'].'");
					initializeClock("clockdiv", deadline);
				}
			</script>';
			$html .= '<div class="so-topbar countdown-layout1">
				<div class="so-topbar-inner">
					<div class="so-topbar-text">'.$setting['module_so_topbar_countdown_layout1_heading_text'][$this->config->get('config_language_id')].'</div>
					<div class="countdown-time">
						<div id="clockdiv">
						  	<div>
						    	<span class="days"></span>
						    	<div class="smalltext">Days</div>
						  	</div>
						  	<div>
						    	<span class="hours"></span>
						    	<div class="smalltext">Hours</div>
						  	</div>
						  	<div>
						    	<span class="minutes"></span>
						    	<div class="smalltext">Minutes</div>
						  	</div>
						  	<div>
						    	<span class="seconds"></span>
						    	<div class="smalltext">Seconds</div>
						  	</div>
						</div>
					</div>
				</div>';
				if (isset($setting['module_so_topbar_allow_close']) && $setting['module_so_topbar_allow_close']) {
					$html .= '<div class="so-topbar-close"><i class="fa fa-close"></i></div>';
				}
			$html .= '</div>';
			$html .= '<script>
				jQuery(document).ready(function($) {
					$(window).load(function () {
					  	var date = new Date();
				      	date.setTime(date.getTime() + '.$setting['module_so_topbar_time_cookie'].' * 60 * 60 * 1000);

					  	if ($.cookie("_sotopbar") != "true") {
						    $(".so-topbar").removeClass("topbar_opened").removeClass("topbar_closed").addClass("topbar_opened");
						    $(document).on("click", ".so-topbar-close", function (e) {
						    	$.cookie("_sotopbar", "true", {
						        	expires: date
						      	});

								if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
						    });
					  	}
					  	else {
					  		$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
					  		$(".so-topbar .so-topbar-close").removeClass("topbar_clicked").addClass("topbar_clicked");
					  		$(".so-topbar .so-topbar-close").find("i").removeClass("fa-close").addClass("fa-angle-double-down");
					  		$(document).on("click", ".so-topbar-close", function (e) {
					  			$.cookie("_sotopbar", "false", {
						        	expires: date
						      	});

					  			if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
					  		})
					  	}
					});
				})
			</script>';
		}

		return $html;
	}

	public function getCountdown2Type() {
		$this->load->model('setting/setting');
		$setting = $this->model_setting_setting->getSetting('module_so_topbar');
		$now = date('Y-m-d H:i:s');
		$html = '';

		if (isset($setting['module_so_topbar_countdown_layout1_show']) && $setting['module_so_topbar_countdown_layout1_show'] && $setting['module_so_topbar_countdown_layout1_date_start'] <= $now && ($setting['module_so_topbar_countdown_layout1_date_end'] == '' || $setting['module_so_topbar_countdown_layout1_date_end'] >= $now)) {
			$html .= '<script>
				function getTimeRemaining(endtime){
				  	var t = Date.parse(endtime) - Date.parse(new Date());
				  	var seconds = Math.floor( (t/1000) % 60 );
				  	var minutes = Math.floor( (t/1000/60) % 60 );
				  	var hours = Math.floor( (t/(1000*60*60)) % 24 );
				  	var days = Math.floor( t/(1000*60*60*24) );
				  	return {
				    	"total": t,
				    	"days": days,
				    	"hours": hours,
				    	"minutes": minutes,
				    	"seconds": seconds
				  	};
				}

				function initializeClock(id, endtime) {
					var clock = document.getElementById(id);
					var daysSpan = clock.querySelector(".days");
					var hoursSpan = clock.querySelector(".hours");
					var minutesSpan = clock.querySelector(".minutes");
					var secondsSpan = clock.querySelector(".seconds");

				  	function updateClock() {
				    	var t = getTimeRemaining(endtime);
				    	daysSpan.innerHTML = t.days;
				    	hoursSpan.innerHTML = ("0" + t.hours).slice(-2);
				    	minutesSpan.innerHTML = ("0" + t.minutes).slice(-2);
				    	secondsSpan.innerHTML = ("0" + t.seconds).slice(-2);

				    	if (t.total <= 0) {
				      		clearInterval(timeinterval);
				    	}
				  	}

				  	updateClock();
				  	var timeinterval = setInterval(updateClock, 1000);
				}

				window.onload = function () {
					var deadline = new Date("'.$setting['module_so_topbar_countdown_layout2_date_end'].'");
					initializeClock("clockdiv", deadline);
				}
			</script>';
			$html .= '<div class="so-topbar countdown-layout2">
				<div class="so-topbar-inner">
					<div class="so-topbar-text">'.$setting['module_so_topbar_countdown_layout2_heading_text'][$this->config->get('config_language_id')].'</div>
					<div class="countdown-time">
						<div id="clockdiv">
						  	<div>
						    	<span class="days"></span>
						    	<div class="smalltext">Days</div>
						  	</div>
						  	<div>
						    	<span class="hours"></span>
						    	<div class="smalltext">Hours</div>
						  	</div>
						  	<div>
						    	<span class="minutes"></span>
						    	<div class="smalltext">Minutes</div>
						  	</div>
						  	<div>
						    	<span class="seconds"></span>
						    	<div class="smalltext">Seconds</div>
						  	</div>
						</div>
					</div>';
					if (isset($setting['module_so_topbar_countdown_layout2_show_button']) && $setting['module_so_topbar_countdown_layout2_show_button']) {
						$html .= '<div class="so-topbar-button">
							<a href="'.$setting['module_so_topbar_countdown_layout2_button_link'].'" class="button-link">'.$setting['module_so_topbar_countdown_layout2_button_text'][$this->config->get('config_language_id')].'</a>
						</div>';
					}
				$html .= '</div>';
				if (isset($setting['module_so_topbar_allow_close']) && $setting['module_so_topbar_allow_close']) {
					$html .= '<div class="so-topbar-close"><i class="fa fa-close"></i></div>';
				}
			$html .= '</div>';
			$html .= '<script>
				jQuery(document).ready(function($) {
					$(window).load(function () {
					  	var date = new Date();
				      	date.setTime(date.getTime() + '.$setting['module_so_topbar_time_cookie'].' * 60 * 60 * 1000);

					  	if ($.cookie("_sotopbar") != "true") {
						    $(".so-topbar").removeClass("topbar_opened").removeClass("topbar_closed").addClass("topbar_opened");
						    $(document).on("click", ".so-topbar-close", function (e) {
						    	$.cookie("_sotopbar", "true", {
						        	expires: date
						      	});

								if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
						    });
					  	}
					  	else {
					  		$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
					  		$(".so-topbar .so-topbar-close").removeClass("topbar_clicked").addClass("topbar_clicked");
					  		$(".so-topbar .so-topbar-close").find("i").removeClass("fa-close").addClass("fa-angle-double-down");
					  		$(document).on("click", ".so-topbar-close", function (e) {
					  			$.cookie("_sotopbar", "false", {
						        	expires: date
						      	});

					  			if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
					  		})
					  	}
					});
				})
			</script>';
		}

		return $html;
	}

	public function getHtmlType() {
		$this->load->model('setting/setting');
		$setting = $this->model_setting_setting->getSetting('module_so_topbar');
		$now = date('Y-m-d H:i:s');
		$html = '';
		if (isset($setting['module_so_topbar_html_show']) && $setting['module_so_topbar_html_show'] && $setting['module_so_topbar_html_date_start'] <= $now && ($setting['module_so_topbar_html_date_end'] == '' || $setting['module_so_topbar_html_date_end'] >= $now)) {
			$html .= '<div class="so-topbar html-type">
				<div class="so-topbar-inner">
					<div class="so-topbar-text">'.htmlspecialchars_decode($setting['module_so_topbar_html_content'][$this->config->get('config_language_id')]).'</div>';
					if (isset($setting['module_so_topbar_html_show_button']) && $setting['module_so_topbar_html_show_button']) {
						$html .= '<div class="so-topbar-button">
							<a href="'.$setting['module_so_topbar_html_button_link'].'" class="button-link" target="_blank">'.$setting['module_so_topbar_html_button_text'][$this->config->get('config_language_id')].'</a>
						</div>';
					}
				$html .= '</div>';
				if (isset($setting['module_so_topbar_allow_close']) && $setting['module_so_topbar_allow_close']) {
					$html .= '<div class="so-topbar-close"><i class="fa fa-close"></i></div>';
				}
			$html .= '</div>';
			$html .= '<script>
				jQuery(document).ready(function($) {
					$(window).load(function () {
					  	var date = new Date();
				      	date.setTime(date.getTime() + '.$setting['module_so_topbar_time_cookie'].' * 60 * 60 * 1000);

					  	if ($.cookie("_sotopbar") != "true") {
						    $(".so-topbar").removeClass("topbar_opened").removeClass("topbar_closed").addClass("topbar_opened");
						    $(document).on("click", ".so-topbar-close", function (e) {
						    	$.cookie("_sotopbar", "true", {
						        	expires: date
						      	});

								if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
						    });
					  	}
					  	else {
					  		$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
					  		$(".so-topbar .so-topbar-close").removeClass("topbar_clicked").addClass("topbar_clicked");
					  		$(".so-topbar .so-topbar-close").find("i").removeClass("fa-close").addClass("fa-angle-double-down");
					  		$(document).on("click", ".so-topbar-close", function (e) {
					  			$.cookie("_sotopbar", "false", {
						        	expires: date
						      	});

					  			if ($(this).hasClass("topbar_clicked")) {
									$(this).removeClass("topbar_clicked");
									$(this).find("i").removeClass("fa-angle-double-down").addClass("fa-close");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_opened");
								}
								else {
									$(this).removeClass("topbar_clicked").addClass("topbar_clicked");
									$(this).find("i").removeClass("fa-close").addClass("fa-angle-double-down");
									$(".so-topbar").removeClass("topbar_closed").removeClass("topbar_opened").addClass("topbar_closed");
								}
					  		})
					  	}
					});
				})
			</script>';
		}

		return $html;
	}
}