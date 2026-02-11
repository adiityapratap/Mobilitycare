<?php
class ModelExtensionPaymentNdisCheckout extends Model {
	public function getMethod($address, $total) {
	

       
		$method_data = array();

			$method_data = array(
				'code'       => 'ndis_checkout',
				'title'      => 'NDIS',
				'terms'      => '',
				'sort_order' => $this->config->get('payment_ndis_checkout_sort_order')
			);
	

		return $method_data;
	}
}