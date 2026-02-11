<?php
class ModelExtensionShippingFlat extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/flat');
		$this->load->model('extension/module/product_shipping');
		$shippingCost = 0;
		$shippingCharge= 0;
	
		 if (isset($this->session->data['payment_postcode'])) {
            $postcode = $this->session->data['payment_postcode'];
            $cart_products = $this->cart->getProducts();

            $shipping_data = [];

            foreach ($cart_products as $product) {
                
                $cost = $this->model_extension_module_product_shipping->getProductShippingByPostcode($product['product_id'], $postcode);
                $shippingCharge = ($cost*$product['quantity']);
                $shippingCost += $shippingCharge;
            
            }

        }
        
        $status = true;
// 		if (!$this->config->get('shipping_flat_geo_zone_id')) {
// 			$status = true;
// 		} elseif ($query->num_rows) {
// 			$status = true;
// 		} else {
// 			$status = false;
// 		}

		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['flat'] = array(
				'code'         => 'flat.flat',
				'title'        => $this->language->get('text_description'),
				'cost'         => $shippingCost,
				'tax_class_id' => $this->config->get('shipping_flat_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($shippingCost, $this->config->get('shipping_flat_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'flat',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_flat_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}