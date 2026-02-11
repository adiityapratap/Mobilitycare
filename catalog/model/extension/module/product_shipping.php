<?php
class ModelExtensionModuleProductShipping extends Model {

    public function getProductShippingByPostcode($product_id, $postcode) {
        // Get geo zone for postcode
        $query = $this->db->query("SELECT geo_zone_id FROM " . DB_PREFIX . "geo_zone_to_postcode WHERE postcode = '" . $this->db->escape($postcode) . "' LIMIT 1");
        
        if ($query->num_rows) {
            $geo_zone_id = $query->row['geo_zone_id'];
            
            // Get product shipping cost for this geo zone
            $shipping_query = $this->db->query("SELECT cost FROM " . DB_PREFIX . "product_geo_zone_shipping WHERE product_id = '" . (int)$product_id . "' AND geo_zone_id = '" . (int)$geo_zone_id . "' LIMIT 1");
            
            if ($shipping_query->num_rows) {
                return (float)$shipping_query->row['cost'];
            }
        }
        return 0; // default cost if not found
    }
}
