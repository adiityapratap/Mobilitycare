<?php
class ControllerCommonMarketingPopup extends Controller {
  public function index() {
       $this->load->model('catalog/manufacturer');
      $data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
      
      // Captcha
      if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
          $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
      } else {
          $data['captcha'] = '';
      }
      
    return $this->load->view('common/marketing_popup', $data);
  }
}
