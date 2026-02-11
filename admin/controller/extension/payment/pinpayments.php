<?php
class ControllerExtensionPaymentPinPayments extends Controller {
    private $error = array();

    public function index() {
        ini_set('display_errors', 1); // Enables displaying errors in the browser
ini_set('display_startup_errors', 1); // Enables displaying errors during PHP startup
error_reporting(E_ALL); 
        $this->load->language('extension/payment/pinpayments');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_pinpayments', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_publishable_key'] = $this->language->get('entry_publishable_key');
        $data['entry_secret_key'] = $this->language->get('entry_secret_key');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['publishable_key'])) {
            $data['error_publishable_key'] = $this->error['publishable_key'];
        } else {
            $data['error_publishable_key'] = '';
        }

        if (isset($this->error['secret_key'])) {
            $data['error_secret_key'] = $this->error['secret_key'];
        } else {
            $data['error_secret_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/pinpayments', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/pinpayments', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_pinpayments_publishable_key'])) {
            $data['payment_pinpayments_publishable_key'] = $this->request->post['payment_pinpayments_publishable_key'];
        } else {
            $data['payment_pinpayments_publishable_key'] = $this->config->get('payment_pinpayments_publishable_key');
        }

        if (isset($this->request->post['payment_pinpayments_secret_key'])) {
            $data['payment_pinpayments_secret_key'] = $this->request->post['payment_pinpayments_secret_key'];
        } else {
            $data['payment_pinpayments_secret_key'] = $this->config->get('payment_pinpayments_secret_key');
        }

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_pinpayments_order_status_id'])) {
            $data['payment_pinpayments_order_status_id'] = $this->request->post['payment_pinpayments_order_status_id'];
        } else {
            $data['payment_pinpayments_order_status_id'] = $this->config->get('payment_pinpayments_order_status_id');
        }

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_pinpayments_geo_zone_id'])) {
            $data['payment_pinpayments_geo_zone_id'] = $this->request->post['payment_pinpayments_geo_zone_id'];
        } else {
            $data['payment_pinpayments_geo_zone_id'] = $this->config->get('payment_pinpayments_geo_zone_id');
        }

        if (isset($this->request->post['payment_pinpayments_status'])) {
            $data['payment_pinpayments_status'] = $this->request->post['payment_pinpayments_status'];
        } else {
            $data['payment_pinpayments_status'] = $this->config->get('payment_pinpayments_status');
        }

        if (isset($this->request->post['payment_pinpayments_sort_order'])) {
            $data['payment_pinpayments_sort_order'] = $this->request->post['payment_pinpayments_sort_order'];
        } else {
            $data['payment_pinpayments_sort_order'] = $this->config->get('payment_pinpayments_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/pinpayments', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/pinpayments')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_pinpayments_publishable_key']) {
            $this->error['publishable_key'] = $this->language->get('error_publishable_key');
        }

        if (!$this->request->post['payment_pinpayments_secret_key']) {
            $this->error['secret_key'] = $this->language->get('error_secret_key');
        }

        return !$this->error;
    }
}