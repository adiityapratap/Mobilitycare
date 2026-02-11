<?php
// File: admin/controller/extension/module/pdf_invoice.php
class ControllerExtensionModulePdfInvoice extends Controller {
    
    private $error = array();

    public function index() {
        $this->load->language('extension/module/pdf_invoice');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('setting/setting');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_pdf_invoice', $this->request->post);
            
            $this->session->data['success'] = $this->language->get('text_success');
            
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }
        
        // Load current settings with defaults
        $data['module_pdf_invoice_status'] = $this->config->get('module_pdf_invoice_status') ?: '1';
        $data['module_pdf_invoice_auto_generate'] = $this->config->get('module_pdf_invoice_auto_generate') ?: '1';
        $data['module_pdf_invoice_attach_email'] = $this->config->get('module_pdf_invoice_attach_email') ?: '1';
        $data['module_pdf_invoice_invoice_prefix'] = $this->config->get('module_pdf_invoice_invoice_prefix') ?: 'INV-';
        $data['module_pdf_invoice_primary_color'] = $this->config->get('module_pdf_invoice_primary_color') ?: '#e74c3c';
        $data['module_pdf_invoice_secondary_color'] = $this->config->get('module_pdf_invoice_secondary_color') ?: '#2c3e50';
        $data['module_pdf_invoice_payment_terms'] = $this->config->get('module_pdf_invoice_payment_terms') ?: '30 days';
        $data['module_pdf_invoice_footer_text'] = $this->config->get('module_pdf_invoice_footer_text') ?: 'Thank you for your business!';
        $data['module_pdf_invoice_show_serial'] = $this->config->get('module_pdf_invoice_show_serial') ?: '0';
        
        // Override with POST data if exists
        foreach ($data as $key => $value) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            }
        }
        
        // Error handling
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        
        // Success message
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        
        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/pdf_invoice', 'user_token=' . $this->session->data['user_token'], true)
        );
        
        $data['action'] = $this->url->link('extension/module/pdf_invoice', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['test_generate'] = $this->url->link('extension/module/pdf_invoice/testGenerate', 'user_token=' . $this->session->data['user_token'], true);
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('extension/module/pdf_invoice', $data));
    }
    
    public function testGenerate() {
        $json = array();
        
        try {
            // Get latest order for testing
            $this->load->model('sale/order');
            $filter_data = array(
                'start' => 0,
                'limit' => 1
            );
            $orders = $this->model_sale_order->getOrders($filter_data);
            
            if (empty($orders)) {
                $json['error'] = 'No orders found for testing. Please create an order first.';
            } else {
                $order_id = $orders[0]['order_id'];
                
                // Load the PDF invoice model
                $this->load->model('extension/module/pdf_invoice');
                
                // Generate test PDF
                $filename = $this->model_extension_module_pdf_invoice->generateTestInvoice($order_id);
                
                if ($filename) {
                    $json['success'] = 'Test PDF generated successfully: ' . $filename;
                    $json['download_url'] = HTTP_CATALOG . 'system/storage/download/invoices/' . $filename;
                } else {
                    $json['error'] = 'Failed to generate test PDF. Check error logs.';
                }
            }
        } catch (Exception $e) {
            $json['error'] = 'Error: ' . $e->getMessage();
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/pdf_invoice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        return !$this->error;
    }
    
    public function install() {
        $this->load->model('extension/module/pdf_invoice');
        $this->model_extension_module_pdf_invoice->install();
        
        // Install events
        $this->load->model('setting/event');
        
        // Remove existing events first
        $this->model_setting_event->deleteEventByCode('pdf_invoice_generation');
        $this->model_setting_event->deleteEventByCode('pdf_invoice_email_attachment');
        
        // Add new events
        $this->model_setting_event->addEvent(
            'pdf_invoice_generation', 
            'catalog/model/checkout/order/addOrderHistory/after', 
            'extension/module/pdf_invoice/generateOrderInvoice'
        );
        
        $this->model_setting_event->addEvent(
            'pdf_invoice_email_attachment', 
            'mail/before', 
            'extension/module/pdf_invoice/attachInvoiceToEmail'
        );
    }
    
    public function uninstall() {
        $this->load->model('extension/module/pdf_invoice');
        $this->model_extension_module_pdf_invoice->uninstall();
        
        // Remove events
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('pdf_invoice_generation');
        $this->model_setting_event->deleteEventByCode('pdf_invoice_email_attachment');
    }
}