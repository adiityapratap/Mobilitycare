<?php
class ModelCatalogWarrantyClaim extends Model {

    public function addClaim($data) {
        
     
        $this->db->query("INSERT INTO `" . DB_PREFIX . "warranty_claims` SET 
        full_name = '" . (isset($data['full_name']) ? $this->db->escape($data['full_name']) : '') . "',
        phone_number = '" . (isset($data['phone_number']) ? $this->db->escape($data['phone_number']) : '') . "',
        email = '" . (isset($data['email']) ? $this->db->escape($data['email']) : '') . "',
        address = '" . (isset($data['address']) ? $this->db->escape($data['address']) : '') . "',
        city = '" . (isset($data['city']) ? $this->db->escape($data['city']) : '') . "',
        state = '" . (isset($data['state']) ? $this->db->escape($data['state']) : '') . "',
        postcode = '" . (isset($data['postcode']) ? $this->db->escape($data['postcode']) : '') . "',
        product_name = '" . (isset($data['product_name']) ? $this->db->escape($data['product_name']) : '') . "',
        model_number = '" . (isset($data['model_number']) ? $this->db->escape($data['model_number']) : '') . "',
        serial_number = '" . (isset($data['serial_number']) ? $this->db->escape($data['serial_number']) : '') . "',
        purchase_date = '" . (isset($data['purchase_date']) ? $this->db->escape($data['purchase_date']) : '') . "',
        purchased_from = '" . (isset($data['purchased_from']) ? $this->db->escape($data['purchased_from']) : '') . "',
        dealer_name = '" . (isset($data['dealer_name']) ? $this->db->escape($data['dealer_name']) : '') . "',
        invoice_number = '" . (isset($data['invoice_number']) ? $this->db->escape($data['invoice_number']) : '') . "',
        issue_description = '" . (isset($data['issue_description']) ? $this->db->escape($data['issue_description']) : '') . "',
        issue_date = '" . (isset($data['issue_date']) ? $this->db->escape($data['issue_date']) : '') . "',
        issue_frequency = '" . (isset($data['issue_frequency']) ? $this->db->escape($data['issue_frequency']) : '') . "',
        troubleshooting_attempted = '" . (isset($data['troubleshooting_attempted']) ? $this->db->escape($data['troubleshooting_attempted']) : '') . "',
        troubleshooting_details = '" . (isset($data['troubleshooting_details']) ? $this->db->escape($data['troubleshooting_details']) : '') . "',
        proof_of_purchase = '" . (isset($data['proof_of_purchase']) ? $this->db->escape($data['proof_of_purchase']) : '') . "',
        photos_or_videos = '" . (isset($data['photos_or_videos']) ? $this->db->escape($data['photos_or_videos']) : '') . "',
        uploaded_files = '" . (isset($data['uploaded_files']) ? $this->db->escape($data['uploaded_files']) : '') . "',
        agreed_terms = '" . (isset($data['agreed_terms']) ? (int)$data['agreed_terms'] : 0) . "',
        date_added = NOW()
    ");
    
    return true;
}
    
}
?>