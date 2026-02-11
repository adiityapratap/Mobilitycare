<?php
class ModelLocalisationGeoZone extends Model {
	public function addGeoZone($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");

		$geo_zone_id = $this->db->getLastId();

		if (isset($data['zone_to_geo_zone'])) {
			foreach ($data['zone_to_geo_zone'] as $value) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "' AND country_id = '" . (int)$value['country_id'] . "' AND zone_id = '" . (int)$value['zone_id'] . "'");				

				$this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
			}
		}

		$this->cache->delete('geo_zone');
		
		return $geo_zone_id;
	}
	
	public function getGeoZonesAndZones() {
    $query = $this->db->query("
        SELECT gz.geo_zone_id, gz.name AS geo_zone_name, gz.description, 
               z.name AS zone_name, c.name AS country_name
        FROM " . DB_PREFIX . "geo_zone gz
        LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone ztgz ON (gz.geo_zone_id = ztgz.geo_zone_id)
        LEFT JOIN " . DB_PREFIX . "zone z ON (ztgz.zone_id = z.zone_id)
        LEFT JOIN " . DB_PREFIX . "country c ON (ztgz.country_id = c.country_id)
        GROUP BY gz.geo_zone_id, z.zone_id
        ORDER BY gz.name ASC
    ");

    return $query->rows;
}

	
	public function addGeoZonePostcode($geo_zone_id, $postcode) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "geo_zone_to_postcode 
        SET geo_zone_id = '" . (int)$geo_zone_id . "', 
            zone_id = '0', 
            postcode = '" . $this->db->escape($postcode) . "'");
    }
    
    public function getGeoZonePostcodes($geo_zone_id) {
    $query = $this->db->query("SELECT postcode FROM " . DB_PREFIX . "geo_zone_to_postcode 
        WHERE geo_zone_id = '" . (int)$geo_zone_id . "' ORDER BY postcode ASC");

    return $query->rows;
}



	public function editGeoZone($geo_zone_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_modified = NOW() WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		if (isset($data['zone_to_geo_zone'])) {
			foreach ($data['zone_to_geo_zone'] as $value) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "' AND country_id = '" . (int)$value['country_id'] . "' AND zone_id = '" . (int)$value['zone_id'] . "'");				

				$this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
			}
		}

		$this->cache->delete('geo_zone');
	}

	public function deleteGeoZone($geo_zone_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		$this->cache->delete('geo_zone');
	}

	public function getGeoZone($geo_zone_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		return $query->row;
	}

	public function getGeoZones($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "geo_zone";

			$sort_data = array(
				'name',
				'description'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
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
		} else {
			$geo_zone_data = $this->cache->get('geo_zone');

			if (!$geo_zone_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name ASC");

				$geo_zone_data = $query->rows;

				$this->cache->set('geo_zone', $geo_zone_data);
			}

			return $geo_zone_data;
		}
	}

	public function getTotalGeoZones() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "geo_zone");

		return $query->row['total'];
	}

	public function getZoneToGeoZones($geo_zone_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		return $query->rows;
	}

	public function getTotalZoneToGeoZoneByGeoZoneId($geo_zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

		return $query->row['total'];
	}

	public function getTotalZoneToGeoZoneByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalZoneToGeoZoneByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}
}