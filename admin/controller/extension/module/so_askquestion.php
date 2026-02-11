<?php
class ControllerExtensionModuleSoAskquestion extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/so_askquestion');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_so_askquestion', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/module/so_askquestion', 'user_token=' . $this->session->data['user_token'], true));
			}
			else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/so_askquestion', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/so_askquestion', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_so_askquestion_status'])) {
			$data['status'] = $this->request->post['module_so_askquestion_status'];
		} else {
			$data['status'] = $this->config->get('module_so_askquestion_status');
		}

		if (isset($this->request->post['module_so_askquestion_button_name'])) {
			$data['button_name'] = $this->request->post['module_so_askquestion_button_name'];
		} else {
			$data['button_name'] = $this->config->get('module_so_askquestion_button_name');
		}

		if (isset($this->request->post['module_so_askquestion_show_button'])) {
			$data['show_button'] = $this->request->post['module_so_askquestion_show_button'];
		} else {
			$data['show_button'] = $this->config->get('module_so_askquestion_show_button');
		}

		if (isset($this->request->post['module_so_askquestion_disable_add_cart'])) {
			$data['disable_add_cart'] = $this->request->post['module_so_askquestion_disable_add_cart'];
		} else {
			$data['disable_add_cart'] = $this->config->get('module_so_askquestion_disable_add_cart');
		}

		if (isset($this->request->post['module_so_askquestion_show_category'])) {
			$data['show_category'] = $this->request->post['module_so_askquestion_show_category'];
		} else {
			$data['show_category'] = $this->config->get('module_so_askquestion_show_category');
		}

		if (isset($this->request->post['module_so_askquestion_show_name'])) {
			$data['show_name'] = $this->request->post['module_so_askquestion_show_name'];
		} else {
			$data['show_name'] = $this->config->get('module_so_askquestion_show_name');
		}

		if (isset($this->request->post['module_so_askquestion_require_name'])) {
			$data['require_name'] = $this->request->post['module_so_askquestion_require_name'];
		} else {
			$data['require_name'] = $this->config->get('module_so_askquestion_require_name');
		}

		if (isset($this->request->post['module_so_askquestion_show_phone'])) {
			$data['show_phone'] = $this->request->post['module_so_askquestion_show_phone'];
		} else {
			$data['show_phone'] = $this->config->get('module_so_askquestion_show_phone');
		}

		if (isset($this->request->post['module_so_askquestion_require_phone'])) {
			$data['require_phone'] = $this->request->post['module_so_askquestion_require_phone'];
		} else {
			$data['require_phone'] = $this->config->get('module_so_askquestion_require_phone');
		}

		if (isset($this->request->post['module_so_askquestion_show_question'])) {
			$data['show_question'] = $this->request->post['module_so_askquestion_show_question'];
		} else {
			$data['show_question'] = $this->config->get('module_so_askquestion_show_question');
		}

		if (isset($this->request->post['module_so_askquestion_require_question'])) {
			$data['require_question'] = $this->request->post['module_so_askquestion_require_question'];
		} else {
			$data['require_question'] = $this->config->get('module_so_askquestion_require_question');
		}

		if (isset($this->request->post['module_so_askquestion_add_email'])) {
			$data['add_email'] = $this->request->post['module_so_askquestion_add_email'];
		} else {
			$data['add_email'] = $this->config->get('module_so_askquestion_add_email');
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['myToken'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('extension/module/so_askquestion', $data));		
	}

	public function install() {
		$this->load->model('setting/setting');
		$default_setting = array(
			'module_so_askquestion_status'	=> 1,
			'module_so_askquestion_button_name'	=> array(
				'1' => 'Ask Question'
			),
			'module_so_askquestion_show_button'	=> 0,
			'module_so_askquestion_disable_add_cart'	=> 1,
			'module_so_askquestion_show_category'	=> 1,
			'module_so_askquestion_show_name'	=> 1,
			'module_so_askquestion_require_name'	=> 1,
			'module_so_askquestion_show_phone'	=> 1,
			'module_so_askquestion_require_phone'	=> 1,
			'module_so_askquestion_show_question'	=> 1,
			'module_so_askquestion_require_question'	=> 1,
			'module_so_askquestion_add_email'	=> 'dulv@ytcvn.com'
		);
		$this->model_setting_setting->editSetting('module_so_askquestion', $default_setting);
	}

	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_so_askquestion');
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/account')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}