<?php
class ControllerExtensionPaymentNdisCheckout extends Controller {
    private $error = [];

    public function index() {
        $this->load->language('extension/payment/ndis_checkout');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_ndis', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment'));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
            ],
            [
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/payment/ndis_checkout', 'user_token=' . $this->session->data['user_token'])
            ]
        ];

        $data['action'] = $this->url->link('extension/payment/ndis_checkout', 'user_token=' . $this->session->data['user_token']);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

        $data['payment_ndis_status'] = $this->config->get('payment_ndis_status');
        $data['payment_ndis_sort_order'] = $this->config->get('payment_ndis_sort_order');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/ndis', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/ndis_checkout')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}
