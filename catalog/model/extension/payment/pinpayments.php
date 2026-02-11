<?php
class ModelExtensionPaymentPinPayments extends Model {
    public function getMethod($address, $total) {
        $this->load->language('extension/payment/pinpayments');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_pinpayments_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payment_pinpayments_status')) {
            $method_data = array(
                'code'       => 'pinpayments',
                'title'      => $this->language->get('heading_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_pinpayments_sort_order')
            );
        }

        return $method_data;
    }
}