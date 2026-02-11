<?php
class ControllerExtensionModuleProductShipping extends Controller {
    public function getCost() {
        // we need to modify three files in order to any shipping update
        // catalog/model/extension/total/shipping.php
        // /catalog/model/extension/shipping/flat.php
        // catalog/controller/extension/module/product_shipping.php
        // catalog/model/extension/module/so_onepagecheckout.php
        
        $this->load->model('extension/module/product_shipping');

        $json = [];

        if (isset($this->request->post['postcode'])) {
            $postcode = $this->request->post['postcode'];
            $cart_products = $this->cart->getProducts();

            $shipping_data = [];

            foreach ($cart_products as $product) {
              
                $cost = $this->model_extension_module_product_shipping->getProductShippingByPostcode($product['product_id'], $postcode);
                // shipping cost will be multiplied by total qty of that product in cart
                $shipping_data[] = [
                    'product_id' => $product['product_id'],
                    'name'       => $product['name'],
                    'shipping_cost' => ($cost*$product['quantity'])
                ];
            }

            $json['success'] = true;
            $json['shipping_data'] = $shipping_data;
        } else {
            $json['success'] = false;
            $json['error'] = 'Postcode not provided';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function savePostcode() {
    if (isset($this->request->post['postcode'])) {
        $this->session->data['payment_postcode'] = $this->request->post['postcode'];
    }
}



}
