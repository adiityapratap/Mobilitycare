<?php
class ModelExtensionTotalProductShipping extends Model {
    public function getTotal($total) {
        if (!isset($this->session->data['payment_postcode'])) {
            return;
        }

        $postcode = $this->session->data['payment_postcode'];

        $this->load->model('extension/module/product_shipping');

        $cart_products = $this->cart->getProducts();
        $shipping_total = 0;

        foreach ($cart_products as $product) {
            $shipping_total += $this->model_extension_module_product_shipping->getProductShippingByPostcode($product['product_id'], $postcode);
        }

        if ($shipping_total > 0) {
            $total['totals'][] = [
                'code'       => 'product_shipping',
                'title'      => 'Delivery Charge',
                'value'      => $shipping_total,
                'sort_order' => 2
            ];

            $total['total'] += $shipping_total;
        }
    }
}
