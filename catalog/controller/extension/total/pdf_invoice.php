<?php
// File: catalog/controller/extension/module/pdf_invoice.php
class ControllerExtensionModulePdfInvoice extends Controller {
    
    public function index() {
        // This method can be used for admin interface if needed
    }
    
    public function generateOrderInvoice(&$route, &$args, &$output) {
        // This is the correct signature for event system
        
        // Check if PDF generation is enabled
        if (!$this->config->get('module_pdf_invoice_status')) {
            return; // PDF generation is disabled
        }
        
        // Get order ID from the arguments
        $order_id = isset($args[0]) ? $args[0] : null;
        
        if (!$order_id) {
            return;
        }
        
        try {
            $this->load->model('extension/total/pdf_invoice');
            
            $filename = $this->model_extension_total_pdf_invoice->generateInvoice($order_id);
            
            if ($filename) {
                // Save invoice filename to order
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `invoice` = '" . $this->db->escape($filename) . "' WHERE order_id = '" . (int)$order_id . "'");
                
                // Log success
                error_log("PDF Invoice generated for order $order_id: $filename");
                return $filename;
            } else {
                error_log("Failed to generate PDF invoice for order: $order_id");
                return false;
            }
        } catch (Exception $e) {
            error_log("PDF Invoice generation error: " . $e->getMessage());
            return false;
        }
    }
    
    public function attachInvoiceToEmail(&$route, &$args, &$output) {
        // This is the correct signature for email event system
        
        // Check if email attachment is enabled
        if (!$this->config->get('module_pdf_invoice_attach_email')) {
            return;
        }
        
        // For mail/order/before trigger, we need to extract order info differently
        if (!isset($args[0]) || !is_array($args[0])) {
            return;
        }
        
        $mail_data = &$args[0];
        
        // Try to find order ID in the mail data
        $order_id = null;
        
        // Look for order ID in subject
        if (isset($mail_data['subject'])) {
            if (preg_match('/order[#\s]*(\d+)/i', $mail_data['subject'], $matches)) {
                $order_id = $matches[1];
            }
        }
        
        // Look for order ID in text content
        if (!$order_id && isset($mail_data['text'])) {
            if (preg_match('/order[#\s]*(\d+)/i', $mail_data['text'], $matches)) {
                $order_id = $matches[1];
            }
        }
        
        // Look for order ID in HTML content
        if (!$order_id && isset($mail_data['html'])) {
            if (preg_match('/order[#\s]*(\d+)/i', $mail_data['html'], $matches)) {
                $order_id = $matches[1];
            }
        }
        
        if (!$order_id) {
            return; // No order ID found
        }
        
        try {
            // Check if invoice already exists
            $query = $this->db->query("SELECT `invoice` FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
            
            $filename = null;
            if ($query->num_rows && $query->row['invoice']) {
                $filename = $query->row['invoice'];
            } else {
                // Generate invoice if it doesn't exist
                $this->load->model('extension/total/pdf_invoice');
                $filename = $this->model_extension_total_pdf_invoice->generateInvoice($order_id);
                
                if ($filename) {
                    // Update order record
                    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `invoice` = '" . $this->db->escape($filename) . "' WHERE order_id = '" . (int)$order_id . "'");
                }
            }
            
            if ($filename) {
                // Find the actual file path
                $possible_paths = [
                    DIR_DOWNLOAD . 'invoices/' . $filename,
                    DIR_OPENCART . 'download/invoices/' . $filename,
                    DIR_SYSTEM . 'download/invoices/' . $filename
                ];
                
                if (defined('DIR_STORAGE')) {
                    $possible_paths[] = DIR_STORAGE . 'download/invoices/' . $filename;
                }
                
                foreach ($possible_paths as $filepath) {
                    if (file_exists($filepath)) {
                        // Initialize attachments array if it doesn't exist
                        if (!isset($mail_data['attachments'])) {
                            $mail_data['attachments'] = array();
                        }
                        
                        // Add attachment
                        $mail_data['attachments'][] = $filepath;
                        
                        error_log("PDF Invoice attached to email for order $order_id: $filepath");
                        return;
                    }
                }
                
                error_log("PDF Invoice file not found for order $order_id: $filename");
            }
        } catch (Exception $e) {
            error_log("PDF Invoice email attachment error: " . $e->getMessage());
        }
    }
}