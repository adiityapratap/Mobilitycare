<?php
class ModelExtensionModulePdfInvoice extends Model {
   public function generateInvoice($order_id,$order_status_id=1) {
 

    $this->load->model('checkout/order');
    $this->load->model('tool/upload');
    $order_info = $this->model_checkout_order->getOrder($order_id);
    
    $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
		if ($order_status_query->num_rows) {
			$order_status = $order_status_query->row['name'];
		} else {
			$order_status = '';
		}
		
   
    if (!$order_info) {
        error_log("❌ No order info found for Order ID: " . $order_id);
        return false;
    }

    // Load language for consistent formatting
    $language = new Language($order_info['language_code']);
    $language->load($order_info['language_code']);
    $language->load('mail/order_add');

    // Load TCPDF
    $tcpdf_path = DIR_SYSTEM . 'library/tcpdf/tcpdf.php';
    if (!file_exists($tcpdf_path)) {
        error_log("❌ TCPDF not found at: " . $tcpdf_path);
        return false;
    }
    require_once($tcpdf_path);
   

    try {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($order_info['store_name']);
        $pdf->SetTitle('Confirmation #' . $order_id);
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();

      

        // Check if logo exists
        $logo_path = DIR_IMAGE . 'catalog/mobilitycare-logo.png';
        $logo_exists = file_exists($logo_path);

        // --- HEADER WITH LOGO ---
        $html = '<table cellspacing="0" cellpadding="4" border="0" width="100%">
            <tr>
                <td width="60%">';
        
        if ($logo_exists) {
            $html .= '<img src="' . $logo_path . '" height="60" alt="' . $order_info['store_name'] . '"><br><br>';
        }
        
        $html .= '<strong>' . $order_info['store_name'] . '</strong><br>
                    Website: ' . $order_info['store_url'] . '<br>
                    Email: ' . $this->config->get('config_email') . '
                </td>
                <td width="40%" align="right">
                    <h2 style="color: #333;">ORDER CONFIRMATION</h2>
                    <strong>Confirmation No.:</strong> ' . $order_id . '<br>
                    <strong>Date:</strong> ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . '<br>
                   
                    <strong>Order Status:</strong> '.$order_status;

        // Get order status
        $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_info['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
        if ($order_status_query->num_rows) {
            $html .= $order_status_query->row['name'];
        }

        $html .= '</td>
            </tr>
        </table><br><br>';

        // --- CUSTOMER INFORMATION ---
        $html .= '<table cellspacing="0" cellpadding="4" border="0" width="100%" style="background-color:#f8f8f8;">
            <tr>
                <td><strong>Email:</strong> ' . $order_info['email'] . '</td>
                <td><strong>Telephone:</strong> ' . $order_info['telephone'] . '</td>
            </tr>';
        
        if ($order_info['payment_method']) {
            $html .= '<tr>
                <td><strong>Payment Method:</strong> ' . $order_info['payment_method'] . '</td>';
            if ($order_info['shipping_method']) {
                $html .= '<td><strong>Shipping Method:</strong> ' . $order_info['shipping_method'] . '</td>';
            } else {
                $html .= '<td></td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table><br><br>';

        // --- ADDRESSES ---
        // Format payment address
        $payment_format = $order_info['payment_address_format'] ?: '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
        
        $payment_find = array('{firstname}', '{lastname}', '{company}', '{address_1}', '{address_2}', '{city}', '{postcode}', '{zone}', '{zone_code}', '{country}');
        $payment_replace = array(
            $order_info['payment_firstname'], $order_info['payment_lastname'], $order_info['payment_company'],
            $order_info['payment_address_1'], $order_info['payment_address_2'], $order_info['payment_city'],
            $order_info['payment_postcode'], $order_info['payment_zone'], $order_info['payment_zone_code'], $order_info['payment_country']
        );
        $payment_address = trim(str_replace($payment_find, $payment_replace, $payment_format));
        $payment_address = preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), "\n", $payment_address);

        // Format shipping address
        $shipping_format = $order_info['shipping_address_format'] ?: '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
        
        $shipping_find = array('{firstname}', '{lastname}', '{company}', '{address_1}', '{address_2}', '{city}', '{postcode}', '{zone}', '{zone_code}', '{country}');
        $shipping_replace = array(
            $order_info['shipping_firstname'], $order_info['shipping_lastname'], $order_info['shipping_company'],
            $order_info['shipping_address_1'], $order_info['shipping_address_2'], $order_info['shipping_city'],
            $order_info['shipping_postcode'], $order_info['shipping_zone'], $order_info['shipping_zone_code'], $order_info['shipping_country']
        );
        $shipping_address = trim(str_replace($shipping_find, $shipping_replace, $shipping_format));
        $shipping_address = preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), "\n", $shipping_address);

        $html .= '<table cellspacing="0" cellpadding="8" border="1" width="100%">
            <tr>
                <td width="50%" style="background-color:#f0f0f0;"><strong>BILLING ADDRESS</strong><br><br>' . nl2br($payment_address) . '</td>
                <td width="50%" style="background-color:#f0f0f0;"><strong>SHIPPING ADDRESS</strong><br><br>' . nl2br($shipping_address) . '</td>
            </tr>
        </table><br><br>';

        // --- PRODUCTS TABLE ---
        $products = $this->model_checkout_order->getOrderProducts($order_id);
       

        $html .= '<table cellspacing="0" cellpadding="6" border="1" width="100%">
            <thead>
                <tr style="background-color:#e8e8e8;">
                    <th align="left" width="50%"><strong>Product</strong></th>
                    <th align="center" width="10%"><strong>Qty</strong></th>
                    <th align="right" width="20%"><strong>Unit Price</strong></th>
                    <th align="right" width="20%"><strong>Total</strong></th>
                </tr>
            </thead>
            <tbody>';

        foreach ($products as $product) {
            $html .= '<tr>
                <td valign="top" width="50%">';
            
            $html .= '<strong>' . $product['name'] . '</strong>';
            
            // Get product options
            $order_options = $this->model_checkout_order->getOrderOptions($order_id, $product['order_product_id']);
            if ($order_options) {
                $html .= '<br><small>';
                foreach ($order_options as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['value'];
                    } else {
                        $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
                        $value = $upload_info ? $upload_info['name'] : '';
                    }
                    
                    if (strlen($value) > 20) {
                        $value = substr($value, 0, 20) . '..';
                    }
                    
                    $html .= '<br>&nbsp;- ' . $option['name'] . ': ' . $value;
                }
                $html .= '</small>';
            }
            
            $html .= '</td>
                
                <td align="center" valign="top" width="10%">' . $product['quantity'] . '</td>
                <td align="right" valign="top" width="20%">' . $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']) . '</td>
                <td align="right" valign="top" width="20%">' . $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']) . '</td>
            </tr>';
        }

        // --- VOUCHERS ---
        $vouchers = $this->model_checkout_order->getOrderVouchers($order_id);
        foreach ($vouchers as $voucher) {
            $html .= '<tr>
                <td colspan="4"><strong>Gift Voucher:</strong> ' . $voucher['description'] . '</td>
                <td align="right">' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']) . '</td>
            </tr>';
        }

        $html .= '</tbody></table><br><br>';

        // --- ORDER TOTALS ---
        $totals = $this->model_checkout_order->getOrderTotals($order_id);
       

        $html .= '<table cellspacing="0" cellpadding="6" border="0" width="100%">';
        foreach ($totals as $total) {
            $is_total_line = (strtolower($total['code']) == 'total');
            $style = $is_total_line ? 'style="background-color:#f0f0f0; font-size:14px;"' : '';
            
            $html .= '<tr ' . $style . '>
                <td align="right" width="80%"><strong>' . $total['title'] . '</strong></td>
                <td align="right" width="20%"><strong>' . $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']) . '</strong></td>
            </tr>';
        }
        $html .= '</table><br><br>';

        // --- FOOTER ---
        if ($order_info['comment']) {
            $html .= '<table cellspacing="0" cellpadding="6" border="1" width="100%">
                <tr>
                    <td style="background-color:#fffacd;"><strong>Customer comment :</strong><br>' . nl2br($order_info['comment']) . '</td>
                </tr>
            </table><br><br>';
        }

        // Check for downloadable products
        $download_status = false;
        foreach ($products as $product) {
            $download_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$product['product_id'] . "'");
            if ($download_query->row['total']) {
                $download_status = true;
                break;
            }
        }
        
        $download_status = false; // lets not show download link , later we will see if client needs

        if ($download_status) {
            $html .= '<div style="background-color:#e7f3ff; padding:10px; border:1px solid #bee5eb;">
                <strong>Download Information:</strong><br>
                Your downloadable products will be available in your account: <br>
                ' . $order_info['store_url'] . 'index.php?route=account/download
            </div><br>';
        }

        // --- BANK DETAILS SECTION ---
        $html .= '<table cellspacing="0" cellpadding="8" border="0" width="100%" style="background-color:#f9f9f9; border-color:#ddd;">
            <tr>
                <td>
                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td colspan="2" style=" padding-bottom: 20px; margin-bottom: 25px;">
                                <strong style="font-size: 14px; color: #333;">BANK DETAILS</strong>
                            </td>
                        </tr>
                        <tr style="padding-bottom: 10px; margin-bottom: 10px;">
                            <td width="25%" style="padding: 4px 0;"><strong>Name:</strong></td>
                            <td width="75%" style="padding: 4px 0;">MobilityCare Pty Ltd</td>
                        </tr>
                        <tr style="padding-bottom: 10px; margin-bottom: 10px;">
                            <td style="padding: 4px 0;"><strong>BSB:</strong></td>
                            <td style="padding: 4px 0;">633-000</td>
                        </tr>
                        <tr style="padding-bottom: 10px; margin-bottom: 10px;">
                            <td style="padding: 4px 0;"><strong>Account Nbr:</strong></td>
                            <td style="padding: 4px 0;">131874703</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table><br>';

        $html .= '<hr><p style="text-align:center; color:#666; font-size:10px;">
            Thank you for ordering with Mobilitycare!<br>
            This Confirmation file was generated on ' . date('Y-m-d H:i:s') . '
        </p>';

        $pdf->writeHTML($html, true, false, true, false, '');
       

        $file = DIR_UPLOAD . 'Confirmation_' . $order_id . '.pdf';
        $pdf->Output($file, 'F'); // save to file
        

        if (!file_exists($file)) {
             error_log("❌ Confirmation file NOT found after generation: " . $file);
        } 

        return $file;
    } catch (Exception $e) {
        error_log("❌ Exception during PDF generation: " . $e->getMessage());
        return false;
    }
}
}
