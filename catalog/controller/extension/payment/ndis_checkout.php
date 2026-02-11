<?php
class ControllerExtensionPaymentNdisCheckout extends Controller {
    public function index() {
        $this->load->language('extension/payment/ndis_checkout');

        $data['action'] = $this->url->link('extension/payment/ndis_checkout/save', '', true);
        $data['continue'] = $this->url->link('checkout/success');

        return $this->load->view('extension/payment/ndis_checkout', $data);
    }

    public function save() {
        $json = [];

        // Save posted data to session
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->session->data['ndis_checkout'] = [
                'dob' => $this->request->post['dob'],
                'participant_number' => $this->request->post['participant_number'],
                'plan_type' => $this->request->post['plan_type'],
                'manager_name' => $this->request->post['manager_name'],
                'manager_email' => $this->request->post['manager_email'],
                'manager_phone' => $this->request->post['manager_phone']
            ];

            $json['success'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function confirm() {
		if (isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code'] == 'ndis_checkout') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_ndis_checkout_order_status_id'));
		
		    
		    $json = array();
		    $json['redirect'] = $this->url->link('checkout/success');
		    
		}
		
			$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
}
