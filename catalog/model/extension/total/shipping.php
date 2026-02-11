<?php
class ModelExtensionTotalShipping extends Model {
	public function getTotal($total) {
	 
		if ($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) {
			$total['totals'][] = array(
				'code'       => 'shipping',
				'title'      => $this->session->data['shipping_method']['title'],
				'value'      => $this->session->data['shipping_method']['cost'],
				'sort_order' => $this->config->get('total_shipping_sort_order')
			);

			if ($this->session->data['shipping_method']['tax_class_id']) {
				$tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
						$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}
			$shippingCost = $this->session->data['shipping_method']['cost'];
			// because sometime old shipping cost shows bwcause of session issue
			if (isset($this->session->data['payment_postcode'])) {
			    
			    $this->load->model('extension/module/product_shipping');
            $postcode = $this->session->data['payment_postcode'];
            $cart_products = $this->cart->getProducts();
            $shippingCost = 0;
            $shippingCharge = 0;
            $shipping_data = [];
      
            foreach ($cart_products as $product) {
                
                $cost = $this->model_extension_module_product_shipping->getProductShippingByPostcode($product['product_id'], $postcode);
                $shippingCharge = ($cost*$product['quantity']);
                // echo "Ss".$shippingCharge; exit;
                $shippingCost += $shippingCharge;
            
            }
          $this->session->data['shipping_method']['cost'] = $shippingCost;
        }
        

			$total['total'] += $this->session->data['shipping_method']['cost'];
		}
	}
}