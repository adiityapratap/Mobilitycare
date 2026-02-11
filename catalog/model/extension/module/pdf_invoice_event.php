<?php
class ModelExtensionModulePdfInvoiceEvent extends Model {
    public function addInvoiceToEmail($route, &$args, &$output) {
        if (!isset($args[0]) || !is_object($args[0])) return;
 error_log("Event for mail order  triggered ");
        $mail = $args[0]; // Mail object
        if (!isset($args[1]) || !is_array($args[1])) return;

        $order_id = $args[1]['order_id'] ?? 0;
        if (!$order_id) return;

        // Generate invoice PDF
        $this->load->model('extension/module/pdf_invoice');
        $invoice_file = $this->model_extension_module_pdf_invoice->generateInvoice($order_id);

        if ($invoice_file && file_exists($invoice_file)) {
            $mail->addAttachment($invoice_file);
        }
    }
}
