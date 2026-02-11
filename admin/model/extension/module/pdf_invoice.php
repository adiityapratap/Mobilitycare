<?php
// File: admin/model/extension/module/pdf_invoice.php
class ModelExtensionModulePdfInvoice extends Model {
    
    public function install() {
        // Create invoice directory if it doesn't exist
        if (!is_dir(DIR_DOWNLOAD . 'invoices/')) {
            mkdir(DIR_DOWNLOAD . 'invoices/', 0755, true);
        }
        
        // Add invoice column to orders table if it doesn't exist
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'invoice'");
        if (!$query->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN `invoice` VARCHAR(255) NULL AFTER `order_status_id`");
        }
        
        // Create invoice log table for tracking
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pdf_invoice_log` (
            `log_id` INT(11) NOT NULL AUTO_INCREMENT,
            `order_id` INT(11) NOT NULL,
            `filename` VARCHAR(255) NOT NULL,
            `date_generated` DATETIME NOT NULL,
            `status` ENUM('generated', 'emailed', 'error') DEFAULT 'generated',
            `error_message` TEXT,
            PRIMARY KEY (`log_id`),
            INDEX `order_id` (`order_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        
        // Set default configuration values
        $default_settings = array(
            'module_pdf_invoice_status' => '1',
            'module_pdf_invoice_auto_generate' => '1',
            'module_pdf_invoice_attach_email' => '1',
            'module_pdf_invoice_primary_color' => '#e74c3c',
            'module_pdf_invoice_secondary_color' => '#2c3e50',
            'module_pdf_invoice_payment_terms' => '30 days',
            'module_pdf_invoice_footer_text' => 'Thank you for your business!',
            'module_pdf_invoice_show_serial' => '0',
            'module_pdf_invoice_invoice_prefix' => 'INV-'
        );
        
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_pdf_invoice', $default_settings);
        
        return true;
    }
    
    public function uninstall() {
        // Remove settings
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_pdf_invoice');
        
        // Optionally remove log table (uncomment if you want to remove logs on uninstall)
        // $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pdf_invoice_log`");
        
        return true;
    }
    
    public function getInvoicesByOrderId($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pdf_invoice_log` WHERE `order_id` = '" . (int)$order_id . "' ORDER BY `date_generated` DESC");
        return $query->rows;
    }
    
    public function getAllInvoices($data = array()) {
        $sql = "SELECT l.*, CONCAT(o.firstname, ' ', o.lastname) as customer, o.total, o.currency_code 
                FROM `" . DB_PREFIX . "pdf_invoice_log` l 
                LEFT JOIN `" . DB_PREFIX . "order` o ON (l.order_id = o.order_id)";
        
        $sort_data = array(
            'l.date_generated',
            'l.order_id',
            'customer',
            'o.total',
            'l.status'
        );
        
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY l.date_generated";
        }
        
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }
        
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
    public function getTotalInvoices() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pdf_invoice_log`");
        return $query->row['total'];
    }
    
    public function logInvoiceGeneration($order_id, $filename, $status = 'generated', $error_message = '') {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "pdf_invoice_log` SET 
            `order_id` = '" . (int)$order_id . "', 
            `filename` = '" . $this->db->escape($filename) . "', 
            `date_generated` = NOW(), 
            `status` = '" . $this->db->escape($status) . "', 
            `error_message` = '" . $this->db->escape($error_message) . "'");
        
        return $this->db->getLastId();
    }
    
    public function updateInvoiceStatus($log_id, $status, $error_message = '') {
        $this->db->query("UPDATE `" . DB_PREFIX . "pdf_invoice_log` SET 
            `status` = '" . $this->db->escape($status) . "', 
            `error_message` = '" . $this->db->escape($error_message) . "' 
            WHERE `log_id` = '" . (int)$log_id . "'");
    }
    
    public function deleteInvoice($log_id) {
        // Get invoice info first
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pdf_invoice_log` WHERE `log_id` = '" . (int)$log_id . "'");
        
        if ($query->num_rows) {
            $invoice = $query->row;
            
            // Delete physical file
            $filepath = DIR_DOWNLOAD . 'invoices/' . $invoice['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Delete log entry
            $this->db->query("DELETE FROM `" . DB_PREFIX . "pdf_invoice_log` WHERE `log_id` = '" . (int)$log_id . "'");
            
            return true;
        }
        
        return false;
    }
    
    public function getInvoiceStatistics() {
        $data = array();
        
        // Total invoices generated
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pdf_invoice_log`");
        $data['total_invoices'] = $query->row['total'];
        
        // Invoices generated today
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pdf_invoice_log` WHERE DATE(date_generated) = CURDATE()");
        $data['today_invoices'] = $query->row['total'];
        
        // Invoices generated this month
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pdf_invoice_log` WHERE MONTH(date_generated) = MONTH(NOW()) AND YEAR(date_generated) = YEAR(NOW())");
        $data['month_invoices'] = $query->row['total'];
        
        // Error count
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pdf_invoice_log` WHERE status = 'error'");
        $data['error_invoices'] = $query->row['total'];
        
        // Recent invoices
        $query = $this->db->query("SELECT l.*, CONCAT(o.firstname, ' ', o.lastname) as customer 
            FROM `" . DB_PREFIX . "pdf_invoice_log` l 
            LEFT JOIN `" . DB_PREFIX . "order` o ON (l.order_id = o.order_id) 
            ORDER BY l.date_generated DESC LIMIT 5");
        $data['recent_invoices'] = $query->rows;
        
        return $data;
    }
    
    public function regenerateInvoice($order_id) {
        try {
            $filename = $this->generateTestInvoice($order_id);
            
            if ($filename) {
                $this->logInvoiceGeneration($order_id, $filename, 'generated');
                
                // Update order record
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `invoice` = '" . $this->db->escape($filename) . "' WHERE order_id = '" . (int)$order_id . "'");
                
                return $filename;
            } else {
                $this->logInvoiceGeneration($order_id, '', 'error', 'Failed to generate PDF');
                return false;
            }
        } catch (Exception $e) {
            $this->logInvoiceGeneration($order_id, '', 'error', $e->getMessage());
            return false;
        }
    }
    
    public function generateTestInvoice($order_id) {
        try {
            // Load order data
            $this->load->model('sale/order');
            $order_info = $this->model_sale_order->getOrder($order_id);
            $order_products = $this->model_sale_order->getOrderProducts($order_id);
            $order_totals = $this->model_sale_order->getOrderTotals($order_id);
            
            if (!$order_info) {
                throw new Exception('Order not found: ' . $order_id);
            }
            
            // Check if TCPDF exists
            $tcpdf_path = DIR_SYSTEM . 'library/tcpdf/tcpdf.php';
            if (!file_exists($tcpdf_path)) {
                throw new Exception('TCPDF library not found at: ' . $tcpdf_path);
            }
            
            // Include TCPDF library
            require_once($tcpdf_path);
            
            // Create PDF instance
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator($this->config->get('config_name'));
            $pdf->SetAuthor($this->config->get('config_name'));
            $pdf->SetTitle('Invoice #' . $order_info['order_id']);
            $pdf->SetSubject('Order Invoice');
            
            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Set margins
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 25);
            
            // Add page
            $pdf->AddPage();
            
            // Set font
            $pdf->SetFont('helvetica', '', 10);
            
            // Generate simple HTML content
            $html = $this->generateSimpleInvoiceHTML($order_info, $order_products, $order_totals);
            
            // Output HTML content
            $pdf->writeHTML($html, true, false, true, false, '');
            
            // Save PDF to file
            $filename = 'test_invoice_' . $order_id . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $invoice_dir = DIR_DOWNLOAD . 'invoices/';
            
            // Create directory if it doesn't exist
            if (!is_dir($invoice_dir)) {
                if (!mkdir($invoice_dir, 0755, true)) {
                    throw new Exception('Cannot create invoices directory: ' . $invoice_dir);
                }
            }
            
            $filepath = $invoice_dir . $filename;
            $pdf->Output($filepath, 'F');
            
            if (!file_exists($filepath)) {
                throw new Exception('PDF file was not created: ' . $filepath);
            }
            
            return $filename;
            
        } catch (Exception $e) {
            error_log('PDF Invoice Generation Error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function generateSimpleInvoiceHTML($order_info, $order_products, $order_totals) {
        $html = '
        <style>
            .header { text-align: center; margin-bottom: 30px; }
            .company-name { font-size: 20px; font-weight: bold; color: #e74c3c; }
            .invoice-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-top: 20px; }
            .info-section { margin: 20px 0; }
            .info-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            .info-table td { padding: 5px; border: 1px solid #ddd; }
            .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .items-table th { background-color: #2c3e50; color: white; padding: 8px; text-align: center; }
            .items-table td { padding: 8px; border: 1px solid #ddd; text-align: center; }
        </style>
        
        <div class="header">
            <div class="company-name">' . $this->config->get('config_name') . '</div>
            <div class="invoice-title">Test Invoice</div>
        </div>
        
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td><strong>Invoice No:</strong></td>
                    <td>' . $order_info['order_id'] . '</td>
                    <td><strong>Date:</strong></td>
                    <td>' . date('d-M-Y', strtotime($order_info['date_added'])) . '</td>
                </tr>
                <tr>
                    <td><strong>Customer:</strong></td>
                    <td colspan="3">' . $order_info['firstname'] . ' ' . $order_info['lastname'] . '</td>
                </tr>
            </table>
        </div>
        
        <table class="items-table">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>';
        
        foreach ($order_products as $product) {
            $html .= '
            <tr>
                <td>' . $product['name'] . '</td>
                <td>' . $product['quantity'] . '</td>
                <td>' . $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']) . '</td>
                <td>' . $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']) . '</td>
            </tr>';
        }
        
        $html .= '</table>';
        
        // Add totals
        foreach ($order_totals as $total) {
            $html .= '<p><strong>' . $total['title'] . ':</strong> ' . $total['text'] . '</p>';
        }
        
        return $html;
    }
    
    public function getOrdersWithoutInvoices($data = array()) {
        $sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) as customer, o.total, o.currency_code, o.date_added 
                FROM `" . DB_PREFIX . "order` o 
                LEFT JOIN `" . DB_PREFIX . "pdf_invoice_log` l ON (o.order_id = l.order_id) 
                WHERE l.order_id IS NULL AND o.order_status_id > 0";
        
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
    public function bulkGenerateInvoices($order_ids) {
        $results = array();
        
        foreach ($order_ids as $order_id) {
            $filename = $this->regenerateInvoice($order_id);
            $results[$order_id] = $filename ? 'success' : 'failed';
        }
        
        return $results;
    }
    
    public function cleanupOldInvoices($days = 365) {
        // Get old invoice records
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pdf_invoice_log` WHERE date_generated < DATE_SUB(NOW(), INTERVAL " . (int)$days . " DAY)");
        
        $deleted_count = 0;
        
        foreach ($query->rows as $invoice) {
            // Delete physical file
            $filepath = DIR_DOWNLOAD . 'invoices/' . $invoice['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Delete log entry
            $this->db->query("DELETE FROM `" . DB_PREFIX . "pdf_invoice_log` WHERE `log_id` = '" . (int)$invoice['log_id'] . "'");
            $deleted_count++;
        }
        
        return $deleted_count;
    }
    
    public function validateConfiguration() {
        $errors = array();
        
        // Check if TCPDF library exists
        if (!file_exists(DIR_SYSTEM . 'library/tcpdf/tcpdf.php')) {
            $errors[] = 'TCPDF library not found';
        }
        
        // Check if invoices directory exists and is writable
        $invoice_dir = DIR_DOWNLOAD . 'invoices/';
        if (!is_dir($invoice_dir)) {
            if (!mkdir($invoice_dir, 0755, true)) {
                $errors[] = 'Cannot create invoices directory';
            }
        } elseif (!is_writable($invoice_dir)) {
            $errors[] = 'Invoices directory is not writable';
        }
        
        // Check database table
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "pdf_invoice_log'");
        if (!$query->num_rows) {
            $errors[] = 'Invoice log table does not exist';
        }
        
        return $errors;
    }
}