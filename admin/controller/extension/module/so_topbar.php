<?php

class ControllerExtensionModuleSoTopbar extends Controller {
	private $error = array();

	function index() {
		$this->load->language('extension/module/so_topbar');
		$this->load->model('extension/module/so_topbar');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$action = isset($this->request->post["action"]) ? $this->request->post["action"] : "";
			unset($this->request->post['action']);

			$this->model_setting_setting->editSetting('module_so_topbar', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			if($action == "save_edit") {
				$this->response->redirect($this->url->link('extension/module/so_topbar', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
		}

		$this->getForm();	
	}

	protected function getForm() {
		$this->document->addStyle('view/javascript/so_topbar/style.css');
		$this->document->addStyle('view/javascript/so_topbar/colorpicker.css');
		$this->document->addScript('view/javascript/so_topbar/colorpicker.js');
		$this->document->addScript('view/javascript/so_topbar/script.js');

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['countdown1_date_end'])) {
			$data['countdown1_date_end'] = $this->error['countdown1_date_end'];
		} else {
			$data['countdown1_date_end'] = '';
		}

		if (isset($this->error['countdown2_date_end'])) {
			$data['countdown2_date_end'] = $this->error['countdown2_date_end'];
		} else {
			$data['countdown2_date_end'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/so_topbar', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
		
		$data['action'] = $this->url->link('extension/module/so_topbar', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_so_topbar_status'])) {
			$data['module_so_topbar_status'] = $this->request->post['module_so_topbar_status'];
		} else {
			$data['module_so_topbar_status'] = $this->config->get('module_so_topbar_status');
		}

		if (isset($this->request->post['module_so_topbar_type'])) {
			$data['module_so_topbar_type'] = $this->request->post['module_so_topbar_type'];
		} else {
			$data['module_so_topbar_type'] = $this->config->get('module_so_topbar_type');
		}

		if (isset($this->request->post['module_so_topbar_allow_close'])) {
			$data['module_so_topbar_allow_close'] = $this->request->post['module_so_topbar_allow_close'];
		} else {
			$data['module_so_topbar_allow_close'] = $this->config->get('module_so_topbar_allow_close');
		}

		if (isset($this->request->post['module_so_topbar_close_background_color'])) {
			$data['module_so_topbar_close_background_color'] = $this->request->post['module_so_topbar_close_background_color'];
		} else {
			$data['module_so_topbar_close_background_color'] = $this->config->get('module_so_topbar_close_background_color');
		}

		if (isset($this->request->post['module_so_topbar_close_text_color'])) {
			$data['module_so_topbar_close_text_color'] = $this->request->post['module_so_topbar_close_text_color'];
		} else {
			$data['module_so_topbar_close_text_color'] = $this->config->get('module_so_topbar_close_text_color');
		}

		if (isset($this->request->post['module_so_topbar_time_cookie'])) {
			$data['module_so_topbar_time_cookie'] = $this->request->post['module_so_topbar_time_cookie'];
		} else {
			$data['module_so_topbar_time_cookie'] = $this->config->get('module_so_topbar_time_cookie');
		}

		if (isset($this->request->post['module_so_topbar_social_network_show'])) {
			$data['module_so_topbar_social_network_show'] = $this->request->post['module_so_topbar_social_network_show'];
		} else {
			$data['module_so_topbar_social_network_show'] = $this->config->get('module_so_topbar_social_network_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_date_start'])) {
			$data['module_so_topbar_social_network_date_start'] = $this->request->post['module_so_topbar_social_network_date_start'];
		} else {
			$data['module_so_topbar_social_network_date_start'] = $this->config->get('module_so_topbar_social_network_date_start');
		}

		if (isset($this->request->post['module_so_topbar_social_network_date_end'])) {
			$data['module_so_topbar_social_network_date_end'] = $this->request->post['module_so_topbar_social_network_date_end'];
		} else {
			$data['module_so_topbar_social_network_date_end'] = $this->config->get('module_so_topbar_social_network_date_end');
		}

		if (isset($this->request->post['module_so_topbar_social_network_background_color'])) {
			$data['module_so_topbar_social_network_background_color'] = $this->request->post['module_so_topbar_social_network_background_color'];
		} else {
			$data['module_so_topbar_social_network_background_color'] = $this->config->get('module_so_topbar_social_network_background_color');
		}

		if (isset($this->request->post['module_so_topbar_social_network_text_color'])) {
			$data['module_so_topbar_social_network_text_color'] = $this->request->post['module_so_topbar_social_network_text_color'];
		} else {
			$data['module_so_topbar_social_network_text_color'] = $this->config->get('module_so_topbar_social_network_text_color');
		}

		if (isset($this->request->post['module_so_topbar_social_network_social_color'])) {
			$data['module_so_topbar_social_network_social_color'] = $this->request->post['module_so_topbar_social_network_social_color'];
		} else {
			$data['module_so_topbar_social_network_social_color'] = $this->config->get('module_so_topbar_social_network_social_color');
		}

		if (isset($this->request->post['module_so_topbar_social_network_social_hover_color'])) {
			$data['module_so_topbar_social_network_social_hover_color'] = $this->request->post['module_so_topbar_social_network_social_hover_color'];
		} else {
			$data['module_so_topbar_social_network_social_hover_color'] = $this->config->get('module_so_topbar_social_network_social_hover_color');
		}

		if (isset($this->request->post['module_so_topbar_social_network_heading_text'])) {
			$data['module_so_topbar_social_network_heading_text'] = $this->request->post['module_so_topbar_social_network_heading_text'];
		} else {
			$data['module_so_topbar_social_network_heading_text'] = $this->config->get('module_so_topbar_social_network_heading_text');
		}

		if (isset($this->request->post['module_so_topbar_social_network_fb_show'])) {
			$data['module_so_topbar_social_network_fb_show'] = $this->request->post['module_so_topbar_social_network_fb_show'];
		} else {
			$data['module_so_topbar_social_network_fb_show'] = $this->config->get('module_so_topbar_social_network_fb_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_fb_url'])) {
			$data['module_so_topbar_social_network_fb_url'] = $this->request->post['module_so_topbar_social_network_fb_url'];
		} else {
			$data['module_so_topbar_social_network_fb_url'] = $this->config->get('module_so_topbar_social_network_fb_url');
		}

		if (isset($this->request->post['module_so_topbar_social_network_tw_show'])) {
			$data['module_so_topbar_social_network_tw_show'] = $this->request->post['module_so_topbar_social_network_tw_show'];
		} else {
			$data['module_so_topbar_social_network_tw_show'] = $this->config->get('module_so_topbar_social_network_tw_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_tw_url'])) {
			$data['module_so_topbar_social_network_tw_url'] = $this->request->post['module_so_topbar_social_network_tw_url'];
		} else {
			$data['module_so_topbar_social_network_tw_url'] = $this->config->get('module_so_topbar_social_network_tw_url');
		}

		if (isset($this->request->post['module_so_topbar_social_network_rss_show'])) {
			$data['module_so_topbar_social_network_rss_show'] = $this->request->post['module_so_topbar_social_network_rss_show'];
		} else {
			$data['module_so_topbar_social_network_rss_show'] = $this->config->get('module_so_topbar_social_network_rss_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_rss_url'])) {
			$data['module_so_topbar_social_network_rss_url'] = $this->request->post['module_so_topbar_social_network_rss_url'];
		} else {
			$data['module_so_topbar_social_network_rss_url'] = $this->config->get('module_so_topbar_social_network_rss_url');
		}

		if (isset($this->request->post['module_so_topbar_social_network_linkedin_show'])) {
			$data['module_so_topbar_social_network_linkedin_show'] = $this->request->post['module_so_topbar_social_network_linkedin_show'];
		} else {
			$data['module_so_topbar_social_network_linkedin_show'] = $this->config->get('module_so_topbar_social_network_linkedin_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_linkedin_url'])) {
			$data['module_so_topbar_social_network_linkedin_url'] = $this->request->post['module_so_topbar_social_network_linkedin_url'];
		} else {
			$data['module_so_topbar_social_network_linkedin_url'] = $this->config->get('module_so_topbar_social_network_linkedin_url');
		}

		if (isset($this->request->post['module_so_topbar_social_network_google_show'])) {
			$data['module_so_topbar_social_network_google_show'] = $this->request->post['module_so_topbar_social_network_google_show'];
		} else {
			$data['module_so_topbar_social_network_google_show'] = $this->config->get('module_so_topbar_social_network_google_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_google_url'])) {
			$data['module_so_topbar_social_network_google_url'] = $this->request->post['module_so_topbar_social_network_google_url'];
		} else {
			$data['module_so_topbar_social_network_google_url'] = $this->config->get('module_so_topbar_social_network_google_url');
		}

		if (isset($this->request->post['module_so_topbar_social_network_youtube_show'])) {
			$data['module_so_topbar_social_network_youtube_show'] = $this->request->post['module_so_topbar_social_network_youtube_show'];
		} else {
			$data['module_so_topbar_social_network_youtube_show'] = $this->config->get('module_so_topbar_social_network_youtube_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_youtube_url'])) {
			$data['module_so_topbar_social_network_youtube_url'] = $this->request->post['module_so_topbar_social_network_youtube_url'];
		} else {
			$data['module_so_topbar_social_network_youtube_url'] = $this->config->get('module_so_topbar_social_network_youtube_url');
		}

		if (isset($this->request->post['module_so_topbar_social_network_pinterest_show'])) {
			$data['module_so_topbar_social_network_pinterest_show'] = $this->request->post['module_so_topbar_social_network_pinterest_show'];
		} else {
			$data['module_so_topbar_social_network_pinterest_show'] = $this->config->get('module_so_topbar_social_network_pinterest_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_pinterest_url'])) {
			$data['module_so_topbar_social_network_pinterest_url'] = $this->request->post['module_so_topbar_social_network_pinterest_url'];
		} else {
			$data['module_so_topbar_social_network_pinterest_url'] = $this->config->get('module_so_topbar_social_network_pinterest_url');
		}

		if (isset($this->request->post['module_so_topbar_social_network_skype_show'])) {
			$data['module_so_topbar_social_network_skype_show'] = $this->request->post['module_so_topbar_social_network_skype_show'];
		} else {
			$data['module_so_topbar_social_network_skype_show'] = $this->config->get('module_so_topbar_social_network_skype_show');
		}

		if (isset($this->request->post['module_so_topbar_social_network_skype_url'])) {
			$data['module_so_topbar_social_network_skype_url'] = $this->request->post['module_so_topbar_social_network_skype_url'];
		} else {
			$data['module_so_topbar_social_network_skype_url'] = $this->config->get('module_so_topbar_social_network_skype_url');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_show'])) {
			$data['module_so_topbar_image_banner_show'] = $this->request->post['module_so_topbar_image_banner_show'];
		} else {
			$data['module_so_topbar_image_banner_show'] = $this->config->get('module_so_topbar_image_banner_show');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_date_start'])) {
			$data['module_so_topbar_image_banner_date_start'] = $this->request->post['module_so_topbar_image_banner_date_start'];
		} else {
			$data['module_so_topbar_image_banner_date_start'] = $this->config->get('module_so_topbar_image_banner_date_start');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_date_end'])) {
			$data['module_so_topbar_image_banner_date_end'] = $this->request->post['module_so_topbar_image_banner_date_end'];
		} else {
			$data['module_so_topbar_image_banner_date_end'] = $this->config->get('module_so_topbar_image_banner_date_end');
		}

		if (isset($this->request->post['module_so_topbar_image_banner'])) {
			$data['module_so_topbar_image_banner'] = $this->request->post['module_so_topbar_image_banner'];
		} elseif ($this->config->get('module_so_topbar_image_banner')) {
			$data['module_so_topbar_image_banner'] = $this->config->get('module_so_topbar_image_banner');
		} else {
			$data['module_so_topbar_image_banner'] = '';
		}
		
		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['module_so_topbar_image_banner']) && is_file(DIR_IMAGE . $this->request->post['module_so_topbar_image_banner'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['module_so_topbar_image_banner'], 100, 100);
		} elseif ($this->config->get('module_so_topbar_image_banner')) {
			$data['thumb'] = $this->model_tool_image->resize($this->config->get('module_so_topbar_image_banner'), 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->request->post['module_so_topbar_image_banner_width'])) {
			$data['module_so_topbar_image_banner_width'] = $this->request->post['module_so_topbar_image_banner_width'];
		} else {
			$data['module_so_topbar_image_banner_width'] = $this->config->get('module_so_topbar_image_banner_width');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_height'])) {
			$data['module_so_topbar_image_banner_height'] = $this->request->post['module_so_topbar_image_banner_height'];
		} else {
			$data['module_so_topbar_image_banner_height'] = $this->config->get('module_so_topbar_image_banner_height');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_show_button'])) {
			$data['module_so_topbar_image_banner_show_button'] = $this->request->post['module_so_topbar_image_banner_show_button'];
		} else {
			$data['module_so_topbar_image_banner_show_button'] = $this->config->get('module_so_topbar_image_banner_show_button');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_button_text'])) {
			$data['module_so_topbar_image_banner_button_text'] = $this->request->post['module_so_topbar_image_banner_button_text'];
		} else {
			$data['module_so_topbar_image_banner_button_text'] = $this->config->get('module_so_topbar_image_banner_button_text');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_button_link'])) {
			$data['module_so_topbar_image_banner_button_link'] = $this->request->post['module_so_topbar_image_banner_button_link'];
		} else {
			$data['module_so_topbar_image_banner_button_link'] = $this->config->get('module_so_topbar_image_banner_button_link');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_background_button_color'])) {
			$data['module_so_topbar_image_banner_background_button_color'] = $this->request->post['module_so_topbar_image_banner_background_button_color'];
		} else {
			$data['module_so_topbar_image_banner_background_button_color'] = $this->config->get('module_so_topbar_image_banner_background_button_color');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_background_button_hover_color'])) {
			$data['module_so_topbar_image_banner_background_button_hover_color'] = $this->request->post['module_so_topbar_image_banner_background_button_hover_color'];
		} else {
			$data['module_so_topbar_image_banner_background_button_hover_color'] = $this->config->get('module_so_topbar_image_banner_background_button_hover_color');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_background_button_text_color'])) {
			$data['module_so_topbar_image_banner_background_button_text_color'] = $this->request->post['module_so_topbar_image_banner_background_button_text_color'];
		} else {
			$data['module_so_topbar_image_banner_background_button_text_color'] = $this->config->get('module_so_topbar_image_banner_background_button_text_color');
		}

		if (isset($this->request->post['module_so_topbar_image_banner_background_button_text_hover_color'])) {
			$data['module_so_topbar_image_banner_background_button_text_hover_color'] = $this->request->post['module_so_topbar_image_banner_background_button_text_hover_color'];
		} else {
			$data['module_so_topbar_image_banner_background_button_text_hover_color'] = $this->config->get('module_so_topbar_image_banner_background_button_text_hover_color');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_show'])) {
			$data['module_so_topbar_subcribe_newsletter_show'] = $this->request->post['module_so_topbar_subcribe_newsletter_show'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_show'] = $this->config->get('module_so_topbar_subcribe_newsletter_show');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_date_start'])) {
			$data['module_so_topbar_subcribe_newsletter_date_start'] = $this->request->post['module_so_topbar_subcribe_newsletter_date_start'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_date_start'] = $this->config->get('module_so_topbar_subcribe_newsletter_date_start');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_date_end'])) {
			$data['module_so_topbar_subcribe_newsletter_date_end'] = $this->request->post['module_so_topbar_subcribe_newsletter_date_end'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_date_end'] = $this->config->get('module_so_topbar_subcribe_newsletter_date_end');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_background_color'])) {
			$data['module_so_topbar_subcribe_newsletter_background_color'] = $this->request->post['module_so_topbar_subcribe_newsletter_background_color'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_background_color'] = $this->config->get('module_so_topbar_subcribe_newsletter_background_color');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_text_color'])) {
			$data['module_so_topbar_subcribe_newsletter_text_color'] = $this->request->post['module_so_topbar_subcribe_newsletter_text_color'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_text_color'] = $this->config->get('module_so_topbar_subcribe_newsletter_text_color');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_heading_text'])) {
			$data['module_so_topbar_subcribe_newsletter_heading_text'] = $this->request->post['module_so_topbar_subcribe_newsletter_heading_text'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_heading_text'] = $this->config->get('module_so_topbar_subcribe_newsletter_heading_text');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_show_button'])) {
			$data['module_so_topbar_subcribe_newsletter_show_button'] = $this->request->post['module_so_topbar_subcribe_newsletter_show_button'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_show_button'] = $this->config->get('module_so_topbar_subcribe_newsletter_show_button');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_button_text'])) {
			$data['module_so_topbar_subcribe_newsletter_button_text'] = $this->request->post['module_so_topbar_subcribe_newsletter_button_text'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_button_text'] = $this->config->get('module_so_topbar_subcribe_newsletter_button_text');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_background_button_color'])) {
			$data['module_so_topbar_subcribe_newsletter_background_button_color'] = $this->request->post['module_so_topbar_subcribe_newsletter_background_button_color'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_background_button_color'] = $this->config->get('module_so_topbar_subcribe_newsletter_background_button_color');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_background_button_hover_color'])) {
			$data['module_so_topbar_subcribe_newsletter_background_button_hover_color'] = $this->request->post['module_so_topbar_subcribe_newsletter_background_button_hover_color'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_background_button_hover_color'] = $this->config->get('module_so_topbar_subcribe_newsletter_background_button_hover_color');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_background_button_text_color'])) {
			$data['module_so_topbar_subcribe_newsletter_background_button_text_color'] = $this->request->post['module_so_topbar_subcribe_newsletter_background_button_text_color'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_background_button_text_color'] = $this->config->get('module_so_topbar_subcribe_newsletter_background_button_text_color');
		}

		if (isset($this->request->post['module_so_topbar_subcribe_newsletter_background_button_text_hover_color'])) {
			$data['module_so_topbar_subcribe_newsletter_background_button_text_hover_color'] = $this->request->post['module_so_topbar_subcribe_newsletter_background_button_text_hover_color'];
		} else {
			$data['module_so_topbar_subcribe_newsletter_background_button_text_hover_color'] = $this->config->get('module_so_topbar_subcribe_newsletter_background_button_text_hover_color');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_show'])) {
			$data['module_so_topbar_twitter_feed_slider_show'] = $this->request->post['module_so_topbar_twitter_feed_slider_show'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_show'] = $this->config->get('module_so_topbar_twitter_feed_slider_show');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_date_start'])) {
			$data['module_so_topbar_twitter_feed_slider_date_start'] = $this->request->post['module_so_topbar_twitter_feed_slider_date_start'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_date_start'] = $this->config->get('module_so_topbar_twitter_feed_slider_date_start');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_date_end'])) {
			$data['module_so_topbar_twitter_feed_slider_date_end'] = $this->request->post['module_so_topbar_twitter_feed_slider_date_end'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_date_end'] = $this->config->get('module_so_topbar_twitter_feed_slider_date_end');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_screen_name'])) {
			$data['module_so_topbar_twitter_feed_slider_screen_name'] = $this->request->post['module_so_topbar_twitter_feed_slider_screen_name'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_screen_name'] = $this->config->get('module_so_topbar_twitter_feed_slider_screen_name');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_id'])) {
			$data['module_so_topbar_twitter_feed_slider_id'] = $this->request->post['module_so_topbar_twitter_feed_slider_id'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_id'] = $this->config->get('module_so_topbar_twitter_feed_slider_id');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_count'])) {
			$data['module_so_topbar_twitter_feed_slider_count'] = $this->request->post['module_so_topbar_twitter_feed_slider_count'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_count'] = $this->config->get('module_so_topbar_twitter_feed_slider_count');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_background_color'])) {
			$data['module_so_topbar_twitter_feed_slider_background_color'] = $this->request->post['module_so_topbar_twitter_feed_slider_background_color'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_background_color'] = $this->config->get('module_so_topbar_twitter_feed_slider_background_color');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_text_color'])) {
			$data['module_so_topbar_twitter_feed_slider_text_color'] = $this->request->post['module_so_topbar_twitter_feed_slider_text_color'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_text_color'] = $this->config->get('module_so_topbar_twitter_feed_slider_text_color');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_display_avatar'])) {
			$data['module_so_topbar_twitter_feed_slider_display_avatar'] = $this->request->post['module_so_topbar_twitter_feed_slider_display_avatar'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_display_avatar'] = $this->config->get('module_so_topbar_twitter_feed_slider_display_avatar');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_display_follow_button'])) {
			$data['module_so_topbar_twitter_feed_slider_display_follow_button'] = $this->request->post['module_so_topbar_twitter_feed_slider_display_follow_button'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_display_follow_button'] = $this->config->get('module_so_topbar_twitter_feed_slider_display_follow_button');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_autoplay'])) {
			$data['module_so_topbar_twitter_feed_slider_autoplay'] = $this->request->post['module_so_topbar_twitter_feed_slider_autoplay'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_autoplay'] = $this->config->get('module_so_topbar_twitter_feed_slider_autoplay');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_auto_timeout'])) {
			$data['module_so_topbar_twitter_feed_slider_auto_timeout'] = $this->request->post['module_so_topbar_twitter_feed_slider_auto_timeout'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_auto_timeout'] = $this->config->get('module_so_topbar_twitter_feed_slider_auto_timeout');
		}

		if (isset($this->request->post['module_so_topbar_twitter_feed_slider_pause_on_hover'])) {
			$data['module_so_topbar_twitter_feed_slider_pause_on_hover'] = $this->request->post['module_so_topbar_twitter_feed_slider_pause_on_hover'];
		} else {
			$data['module_so_topbar_twitter_feed_slider_pause_on_hover'] = $this->config->get('module_so_topbar_twitter_feed_slider_pause_on_hover');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout1_show'])) {
			$data['module_so_topbar_countdown_layout1_show'] = $this->request->post['module_so_topbar_countdown_layout1_show'];
		} else {
			$data['module_so_topbar_countdown_layout1_show'] = $this->config->get('module_so_topbar_countdown_layout1_show');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout1_date_start'])) {
			$data['module_so_topbar_countdown_layout1_date_start'] = $this->request->post['module_so_topbar_countdown_layout1_date_start'];
		} else {
			$data['module_so_topbar_countdown_layout1_date_start'] = $this->config->get('module_so_topbar_countdown_layout1_date_start');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout1_date_end'])) {
			$data['module_so_topbar_countdown_layout1_date_end'] = $this->request->post['module_so_topbar_countdown_layout1_date_end'];
		} else {
			$data['module_so_topbar_countdown_layout1_date_end'] = $this->config->get('module_so_topbar_countdown_layout1_date_end');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout1_heading_text'])) {
			$data['module_so_topbar_countdown_layout1_heading_text'] = $this->request->post['module_so_topbar_countdown_layout1_heading_text'];
		} else {
			$data['module_so_topbar_countdown_layout1_heading_text'] = $this->config->get('module_so_topbar_countdown_layout1_heading_text');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout1_background_color'])) {
			$data['module_so_topbar_countdown_layout1_background_color'] = $this->request->post['module_so_topbar_countdown_layout1_background_color'];
		} else {
			$data['module_so_topbar_countdown_layout1_background_color'] = $this->config->get('module_so_topbar_countdown_layout1_background_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout1_text_color'])) {
			$data['module_so_topbar_countdown_layout1_text_color'] = $this->request->post['module_so_topbar_countdown_layout1_text_color'];
		} else {
			$data['module_so_topbar_countdown_layout1_text_color'] = $this->config->get('module_so_topbar_countdown_layout1_text_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout1_countdown_color'])) {
			$data['module_so_topbar_countdown_layout1_countdown_color'] = $this->request->post['module_so_topbar_countdown_layout1_countdown_color'];
		} else {
			$data['module_so_topbar_countdown_layout1_countdown_color'] = $this->config->get('module_so_topbar_countdown_layout1_countdown_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout1_countdown_fontsize'])) {
			$data['module_so_topbar_countdown_layout1_countdown_fontsize'] = $this->request->post['module_so_topbar_countdown_layout1_countdown_fontsize'];
		} else {
			$data['module_so_topbar_countdown_layout1_countdown_fontsize'] = $this->config->get('module_so_topbar_countdown_layout1_countdown_fontsize');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_show'])) {
			$data['module_so_topbar_countdown_layout2_show'] = $this->request->post['module_so_topbar_countdown_layout2_show'];
		} else {
			$data['module_so_topbar_countdown_layout2_show'] = $this->config->get('module_so_topbar_countdown_layout2_show');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_date_start'])) {
			$data['module_so_topbar_countdown_layout2_date_start'] = $this->request->post['module_so_topbar_countdown_layout2_date_start'];
		} else {
			$data['module_so_topbar_countdown_layout2_date_start'] = $this->config->get('module_so_topbar_countdown_layout2_date_start');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_date_end'])) {
			$data['module_so_topbar_countdown_layout2_date_end'] = $this->request->post['module_so_topbar_countdown_layout2_date_end'];
		} else {
			$data['module_so_topbar_countdown_layout2_date_end'] = $this->config->get('module_so_topbar_countdown_layout2_date_end');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_heading_text'])) {
			$data['module_so_topbar_countdown_layout2_heading_text'] = $this->request->post['module_so_topbar_countdown_layout2_heading_text'];
		} else {
			$data['module_so_topbar_countdown_layout2_heading_text'] = $this->config->get('module_so_topbar_countdown_layout2_heading_text');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_show_button'])) {
			$data['module_so_topbar_countdown_layout2_show_button'] = $this->request->post['module_so_topbar_countdown_layout2_show_button'];
		} else {
			$data['module_so_topbar_countdown_layout2_show_button'] = $this->config->get('module_so_topbar_countdown_layout2_show_button');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_button_text'])) {
			$data['module_so_topbar_countdown_layout2_button_text'] = $this->request->post['module_so_topbar_countdown_layout2_button_text'];
		} else {
			$data['module_so_topbar_countdown_layout2_button_text'] = $this->config->get('module_so_topbar_countdown_layout2_button_text');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_button_link'])) {
			$data['module_so_topbar_countdown_layout2_button_link'] = $this->request->post['module_so_topbar_countdown_layout2_button_link'];
		} else {
			$data['module_so_topbar_countdown_layout2_button_link'] = $this->config->get('module_so_topbar_countdown_layout2_button_link');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_background_color'])) {
			$data['module_so_topbar_countdown_layout2_background_color'] = $this->request->post['module_so_topbar_countdown_layout2_background_color'];
		} else {
			$data['module_so_topbar_countdown_layout2_background_color'] = $this->config->get('module_so_topbar_countdown_layout2_background_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_text_color'])) {
			$data['module_so_topbar_countdown_layout2_text_color'] = $this->request->post['module_so_topbar_countdown_layout2_text_color'];
		} else {
			$data['module_so_topbar_countdown_layout2_text_color'] = $this->config->get('module_so_topbar_countdown_layout2_text_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_background_button_color'])) {
			$data['module_so_topbar_countdown_layout2_background_button_color'] = $this->request->post['module_so_topbar_countdown_layout2_background_button_color'];
		} else {
			$data['module_so_topbar_countdown_layout2_background_button_color'] = $this->config->get('module_so_topbar_countdown_layout2_background_button_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_background_button_hover_color'])) {
			$data['module_so_topbar_countdown_layout2_background_button_hover_color'] = $this->request->post['module_so_topbar_countdown_layout2_background_button_hover_color'];
		} else {
			$data['module_so_topbar_countdown_layout2_background_button_hover_color'] = $this->config->get('module_so_topbar_countdown_layout2_background_button_hover_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_background_button_text_color'])) {
			$data['module_so_topbar_countdown_layout2_background_button_text_color'] = $this->request->post['module_so_topbar_countdown_layout2_background_button_text_color'];
		} else {
			$data['module_so_topbar_countdown_layout2_background_button_text_color'] = $this->config->get('module_so_topbar_countdown_layout2_background_button_text_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_background_button_text_hover_color'])) {
			$data['module_so_topbar_countdown_layout2_background_button_text_hover_color'] = $this->request->post['module_so_topbar_countdown_layout2_background_button_text_hover_color'];
		} else {
			$data['module_so_topbar_countdown_layout2_background_button_text_hover_color'] = $this->config->get('module_so_topbar_countdown_layout2_background_button_text_hover_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_countdown_bgcolor'])) {
			$data['module_so_topbar_countdown_layout2_countdown_bgcolor'] = $this->request->post['module_so_topbar_countdown_layout2_countdown_bgcolor'];
		} else {
			$data['module_so_topbar_countdown_layout2_countdown_bgcolor'] = $this->config->get('module_so_topbar_countdown_layout2_countdown_bgcolor');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_countdown_color'])) {
			$data['module_so_topbar_countdown_layout2_countdown_color'] = $this->request->post['module_so_topbar_countdown_layout2_countdown_color'];
		} else {
			$data['module_so_topbar_countdown_layout2_countdown_color'] = $this->config->get('module_so_topbar_countdown_layout2_countdown_color');
		}

		if (isset($this->request->post['module_so_topbar_countdown_layout2_countdown_fontsize'])) {
			$data['module_so_topbar_countdown_layout2_countdown_fontsize'] = $this->request->post['module_so_topbar_countdown_layout2_countdown_fontsize'];
		} else {
			$data['module_so_topbar_countdown_layout2_countdown_fontsize'] = $this->config->get('module_so_topbar_countdown_layout2_countdown_fontsize');
		}

		if (isset($this->request->post['module_so_topbar_html_show'])) {
			$data['module_so_topbar_html_show'] = $this->request->post['module_so_topbar_html_show'];
		} else {
			$data['module_so_topbar_html_show'] = $this->config->get('module_so_topbar_html_show');
		}

		if (isset($this->request->post['module_so_topbar_html_date_start'])) {
			$data['module_so_topbar_html_date_start'] = $this->request->post['module_so_topbar_html_date_start'];
		} else {
			$data['module_so_topbar_html_date_start'] = $this->config->get('module_so_topbar_html_date_start');
		}

		if (isset($this->request->post['module_so_topbar_html_date_end'])) {
			$data['module_so_topbar_html_date_end'] = $this->request->post['module_so_topbar_html_date_end'];
		} else {
			$data['module_so_topbar_html_date_end'] = $this->config->get('module_so_topbar_html_date_end');
		}

		if (isset($this->request->post['module_so_topbar_html_content'])) {
			$data['module_so_topbar_html_content'] = $this->request->post['module_so_topbar_html_content'];
		} else {
			$data['module_so_topbar_html_content'] = $this->config->get('module_so_topbar_html_content');
		}

		if (isset($this->request->post['module_so_topbar_html_show_button'])) {
			$data['module_so_topbar_html_show_button'] = $this->request->post['module_so_topbar_html_show_button'];
		} else {
			$data['module_so_topbar_html_show_button'] = $this->config->get('module_so_topbar_html_show_button');
		}

		if (isset($this->request->post['module_so_topbar_html_button_text'])) {
			$data['module_so_topbar_html_button_text'] = $this->request->post['module_so_topbar_html_button_text'];
		} else {
			$data['module_so_topbar_html_button_text'] = $this->config->get('module_so_topbar_html_button_text');
		}

		if (isset($this->request->post['module_so_topbar_html_button_link'])) {
			$data['module_so_topbar_html_button_link'] = $this->request->post['module_so_topbar_html_button_link'];
		} else {
			$data['module_so_topbar_html_button_link'] = $this->config->get('module_so_topbar_html_button_link');
		}

		if (isset($this->request->post['module_so_topbar_html_background_color'])) {
			$data['module_so_topbar_html_background_color'] = $this->request->post['module_so_topbar_html_background_color'];
		} else {
			$data['module_so_topbar_html_background_color'] = $this->config->get('module_so_topbar_html_background_color');
		}

		if (isset($this->request->post['module_so_topbar_html_text_color'])) {
			$data['module_so_topbar_html_text_color'] = $this->request->post['module_so_topbar_html_text_color'];
		} else {
			$data['module_so_topbar_html_text_color'] = $this->config->get('module_so_topbar_html_text_color');
		}

		if (isset($this->request->post['module_so_topbar_html_background_button_color'])) {
			$data['module_so_topbar_html_background_button_color'] = $this->request->post['module_so_topbar_html_background_button_color'];
		} else {
			$data['module_so_topbar_html_background_button_color'] = $this->config->get('module_so_topbar_html_background_button_color');
		}

		if (isset($this->request->post['module_so_topbar_html_background_button_hover_color'])) {
			$data['module_so_topbar_html_background_button_hover_color'] = $this->request->post['module_so_topbar_html_background_button_hover_color'];
		} else {
			$data['module_so_topbar_html_background_button_hover_color'] = $this->config->get('module_so_topbar_html_background_button_hover_color');
		}

		if (isset($this->request->post['module_so_topbar_html_background_button_text_color'])) {
			$data['module_so_topbar_html_background_button_text_color'] = $this->request->post['module_so_topbar_html_background_button_text_color'];
		} else {
			$data['module_so_topbar_html_background_button_text_color'] = $this->config->get('module_so_topbar_html_background_button_text_color');
		}

		if (isset($this->request->post['module_so_topbar_html_background_button_text_hover_color'])) {
			$data['module_so_topbar_html_background_button_text_hover_color'] = $this->request->post['module_so_topbar_html_background_button_text_hover_color'];
		} else {
			$data['module_so_topbar_html_background_button_text_hover_color'] = $this->config->get('module_so_topbar_html_background_button_text_hover_color');
		}

		$data['header'] 		= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 		= $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/so_topbar', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/so_topbar')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($type = $this->request->post['module_so_topbar_type']) {
			if ($type == 'countdown1') {
				if ($this->request->post['module_so_topbar_countdown_layout1_date_end'] == '' || $this->request->post['module_so_topbar_countdown_layout1_date_end'] == '0000-00-00 00:00:00') {
					$this->error['countdown1_date_end'] = $this->language->get('error_date_end');
				}
			}
			if ($type == 'countdown2') {
				if (!$this->request->post['module_so_topbar_countdown_layout2_date_end'] || $this->request->post['module_so_topbar_countdown_layout2_date_end'] == '' || $this->request->post['module_so_topbar_countdown_layout2_date_end'] == '0000-00-00 00:00:00') {
					$this->error['countdown2_date_end'] = $this->language->get('error_date_end');
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	function install() {
		$this->load->model('setting/setting');
		$data_setting = array(
			'module_so_topbar_status'	=> 1,
			'module_so_topbar_type'	=> 'social',
			'module_so_topbar_allow_close'	=> 1,
			'module_so_topbar_close_background_color'	=> '#ff0000',
			'module_so_topbar_close_text_color'	=> '#ffffff',
			'module_so_topbar_time_cookie'	=> '1',
			'module_so_topbar_social_network_show'	=> 1,
			'module_so_topbar_social_network_background_color'	=> '#0f8db3',
			'module_so_topbar_social_network_text_color'	=> '#ffffff',
			'module_so_topbar_social_network_social_color'	=> '#ffffff',
			'module_so_topbar_social_network_social_hover_color'	=> '#f2d03b',
			'module_so_topbar_social_network_heading_text'	=> array('1' => 'Connect to us'),
			'module_so_topbar_social_network_fb_show'	=> 1,
			'module_so_topbar_social_network_fb_url'	=> 'https://www.facebook.com/MagenTech/',
			'module_so_topbar_social_network_tw_show'	=> 1,
			'module_so_topbar_social_network_tw_url'	=> 'https://twitter.com/magentech',
			'module_so_topbar_social_network_rss_show'	=> 1,
			'module_so_topbar_social_network_rss_url'	=> '',
			'module_so_topbar_social_network_linkedin_show'	=> 1,
			'module_so_topbar_social_network_linkedin_url'	=> '',
			'module_so_topbar_social_network_google_show'	=> 1,
			'module_so_topbar_social_network_google_url'	=> 'https://plus.google.com/+Magentech-responsive-magento-theme',
			'module_so_topbar_social_network_youtube_show'	=> 1,
			'module_so_topbar_social_network_youtube_url'	=> '',
			'module_so_topbar_social_network_pinterest_show'	=> 1,
			'module_so_topbar_social_network_pinterest_url'	=> '',
			'module_so_topbar_social_network_skype_show'	=> 1,
			'module_so_topbar_social_network_skype_url'	=> '',
			'module_so_topbar_image_banner_show'	=> '1',
			'module_so_topbar_image_banner_show_button'	=> '1',
			'module_so_topbar_image_banner_button_text'	=> array('1'=>'BUY NOW'),
			'module_so_topbar_image_banner_button_link'	=> 'http://opencart.opencartworks.com',
			'module_so_topbar_image_banner_background_button_color'	=> '#ffffff',
			'module_so_topbar_image_banner_background_button_hover_color'	=> '#ffbf00',
			'module_so_topbar_image_banner_background_button_text_color'	=> '#171721',
			'module_so_topbar_image_banner_background_button_text_hover_color'	=> '#ffffff',
			'module_so_topbar_subcribe_newsletter_show'	=> '1',
			'module_so_topbar_subcribe_newsletter_background_color'	=> '#ff7322',
			'module_so_topbar_subcribe_newsletter_text_color'	=> '#ffffff',
			'module_so_topbar_subcribe_newsletter_heading_text'	=> array('1'=>'Subscribe to our Newsletter'),
			'module_so_topbar_subcribe_newsletter_show_button'	=> '1',
			'module_so_topbar_subcribe_newsletter_button_text'	=> array('1'=>'Subscribe'),
			'module_so_topbar_subcribe_newsletter_background_button_color'	=> '#252525',
			'module_so_topbar_subcribe_newsletter_background_button_hover_color'	=> '#ffffff',
			'module_so_topbar_subcribe_newsletter_background_button_text_color'	=> '#ffffff',
			'module_so_topbar_subcribe_newsletter_background_button_text_hover_color'	=> '#252525',
			'module_so_topbar_twitter_feed_slider_show'	=> '1',
			'module_so_topbar_twitter_feed_slider_screen_name'	=> 'smartaddons',
			'module_so_topbar_twitter_feed_slider_id'	=> '321482620284321792',
			'module_so_topbar_twitter_feed_slider_count'	=> 6,
			'module_so_topbar_twitter_feed_slider_background_color'	=> '#c2e1e3',
			'module_so_topbar_twitter_feed_slider_text_color'	=> '#171a21',
			'module_so_topbar_countdown_layout1_show'	=> 1,
			'module_so_topbar_countdown_layout1_date_end'	=> '2018-12-31 00:00:00',
			'module_so_topbar_countdown_layout1_heading_text'	=> array('1'=>'Learning is cool, but knowing is better, and i know the key to success'),
			'module_so_topbar_countdown_layout1_background_color'	=> '#e31836',
			'module_so_topbar_countdown_layout1_text_color'	=> '#ffffff',
			'module_so_topbar_countdown_layout1_countdown_color'	=> '#c28282',
			'module_so_topbar_countdown_layout1_countdown_fontsize'	=> '300%',
			'module_so_topbar_countdown_layout2_show'	=> '1',
			'module_so_topbar_countdown_layout2_date_end'	=> '2018-12-31 00:00:00',
			'module_so_topbar_countdown_layout2_heading_text'	=> array('1'=>'The first of the month is coming. We have to get money'),
			'module_so_topbar_countdown_layout2_show_button'	=> '1',
			'module_so_topbar_countdown_layout2_button_text'	=> array('1'=>'Discovery'),
			'module_so_topbar_countdown_layout2_button_link'	=> '#',
			'module_so_topbar_countdown_layout2_background_color'	=> '#14191f',
			'module_so_topbar_countdown_layout2_text_color'	=> '#ffffff',
			'module_so_topbar_countdown_layout2_background_button_color'	=> '#e31836',
			'module_so_topbar_countdown_layout2_background_button_hover_color'	=> '#e31836',
			'module_so_topbar_countdown_layout2_background_button_text_color'	=> '#ffffff',
			'module_so_topbar_countdown_layout2_background_button_text_hover_color'	=> '#ffffff',
			'module_so_topbar_countdown_layout2_countdown_bgcolor'	=> '#141414',
			'module_so_topbar_countdown_layout2_countdown_color'	=> '#ffffff',
			'module_so_topbar_countdown_layout2_countdown_fontsize'	=> '300%',
			'module_so_topbar_html_show'	=> '1',
			'module_so_topbar_html_content'	=> array('1'=>'<p>The key to more success is to get a message once a week, very important my pictures. Can you <a href="http://opencart.opencartworks.com" target="_blank">Click here</a></p>'),
			'module_so_topbar_html_show_button'	=> '1',
			'module_so_topbar_html_button_text'	=> array('1'=>'SHOP NOW'),
			'module_so_topbar_html_button_link'	=> 'http://opencart.opencartworks.com',
			'module_so_topbar_html_background_color'	=> '#a02806',
			'module_so_topbar_html_text_color'	=> '#ffffff',
			'module_so_topbar_html_background_button_color'	=> '#f1e500',
			'module_so_topbar_html_background_button_hover_color'	=> '#f1e500',
			'module_so_topbar_html_background_button_text_color'	=> '#121212',
			'module_so_topbar_html_background_button_text_hover_color'	=> '#121212'			
		);

		$this->model_setting_setting->editSetting('module_so_topbar', $data_setting);
	}

	function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_so_topbar');
	}
}