<?php
class ControllerExtensionPaymentPinPayments extends Controller {
//     public function index() {
//         $this->load->language('extension/payment/pinpayments');

//         $data['button_confirm'] = $this->language->get('button_confirm');
// $this->response->setOutput($this->load->view('extension/payment/pinpayments', $data));
//         // return $this->load->view('extension/payment/pinpayments', $data);
//     }
    
   public function index() {
    $this->load->language('extension/payment/pinpayments');

    $data['button_confirm'] = $this->language->get('button_confirm');
    $data['text_card_number'] = $this->language->get('text_card_number');
    $data['text_expiry_month'] = $this->language->get('text_expiry_month');
    $data['text_expiry_year'] = $this->language->get('text_expiry_year');
    $data['text_cvc'] = $this->language->get('text_cvc');
    $data['confirm'] = $this->url->link('extension/payment/pinpayments/confirm', '', true);

    // Return the view HTML (no JSON output here)
    return $this->load->view('extension/payment/pinpayments', $data);
}


    public function confirm() {
    $this->load->model('checkout/order');

    $json = [];

    if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
        $json['error'] = 'Invalid request';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Basic validation of required fields
    $required = ['card_number', 'expiry_month', 'expiry_year', 'cvc'];
    foreach ($required as $field) {
        if (empty($this->request->post[$field])) {
            $json['error'] = 'Missing ' . $field;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
    }

    if (empty($this->session->data['order_id'])) {
        $json['error'] = 'Order not found in session';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    $order_id = $this->session->data['order_id'];
    $order_info = $this->model_checkout_order->getOrder($order_id);

    if (!$order_info) {
        $json['error'] = 'Order not found';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Build charge payload
    $amount = (int) round($order_info['total'] * 100); // cents
    if ($amount <= 0) {
        $json['error'] = 'Invalid order total';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    $currency = (isset($this->config) ? $this->config->get('config_currency') : 'AUD');

    $charge_data = [
        'amount'      => $amount,
        'currency'    => $currency,
        'description' => 'Order #' . $order_id,
        'email'       => $order_info['email'],
        'ip_address'  => isset($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '',
        // Card fields (Charges API accepts card[...] directly)
        'card[number]'         => $this->request->post['card_number'],
        'card[expiry_month]'   => $this->request->post['expiry_month'],
        'card[expiry_year]'    => $this->request->post['expiry_year'],
        'card[cvc]'            => $this->request->post['cvc'],
        'card[name]'           => trim($order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']),
        'card[address_line1]'  => $order_info['payment_address_1'],
        'card[address_city]'   => $order_info['payment_city'],
        'card[address_state]'  => $order_info['payment_zone'],
        'card[address_postcode]'=> $order_info['payment_postcode'],
        'card[address_country]'=> $order_info['payment_country']
    ];

    // Secret key from config (ensure this is set in admin)
    $secret_key = $this->config->get('payment_pinpayments_secret_key');

    if (empty($secret_key)) {
        $json['error'] = 'Payment gateway not configured';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Send request to Pin Payments Charges API (test host used here)
    // $endpoint = 'https://test-api.pinpayments.com/1/charges';
    $endpoint = 'https://api.pinpayments.com/1/charges';
  
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ':'); // HTTP Basic auth with secret key
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($charge_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curl_errno) {
        // Connection-level error (host/network)
        $json['error'] = 'Connection error: ' . $curl_error;
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    $result = json_decode($response, true);
    
    error_log("Pin Request: " . print_r($charge_data, true));
error_log("Pin Response: " . $response);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $json['error'] = 'Invalid response from payment gateway';
        // optional: log $response for debugging
        if (isset($this->log)) {
            $this->log->write('PinPayments invalid json response: ' . $response);
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Top-level gateway error
    if (!empty($result['error'])) {
        $json['error'] = isset($result['error_description']) ? $result['error_description'] : 'Payment gateway error';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Success / 3DS / Failure handling per Pin Payments response structure
    if (!empty($result['response']['success']) && $result['response']['success'] === true) {
        // Mark order as paid (use your configured order status id)
        $order_status_id = $this->config->get('payment_pinpayments_order_status_id') ?: $this->config->get('config_order_status_id');
        $this->model_checkout_order->addOrderHistory($order_id, $order_status_id);

        $json['success'] = $this->url->link('checkout/success');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // 3D Secure: gateway may respond with redirect_url and Pending state
    if (!empty($result['response']['redirect_url'])) {
        $json['redirect'] = $result['response']['redirect_url'];
        $json['message']  = isset($result['response']['status_message']) ? $result['response']['status_message'] : 'Redirecting for authentication';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Other failure: try to extract helpful message
    $error_message = $result['response']['error_message'] ?? $result['error_description'] ?? 'Payment failed';
    $json['error'] = $error_message;

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}


}