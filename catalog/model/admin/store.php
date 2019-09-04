<?php
class ModelAdminStore extends Model {
	public function addStore($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "store SET name = '" . $this->db->escape($data['config_name']) . "', `url` = '" . $this->db->escape($data['config_url']) . "', `ssl` = '" . $this->db->escape($data['config_ssl']) . "', `city_id` = '" . (int)$data['city_id'] . "', `region_id` = '" . (int)$data['region_id'] . "'");

		$store_id = $this->db->getLastId();

		// Layout Route
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE store_id = '0'");

		foreach ($query->rows as $layout_route) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_route['layout_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "', store_id = '" . (int)$store_id . "'");
		}

		$this->cache->delete('store');

		return $store_id;
	}

	public function setLogo($filename){
		$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '".$this->db->escape($filename)."' WHERE `key` = 'config_logo'");
	}

	public function getLogo(){
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = 0 AND `key` = 'config_logo' ");
		return $query->row['value'];
	}

	public function editStore($store_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "store SET min_total = '" . (int)$data['min_total'] . "', timing_from = '" . (int)$data['timing_from'] . "', timing_to = '" . (int)$data['timing_to'] . "', timing_slot = '" . (int)$data['timing_slot'] . "', fast_del_cost = '".(int)$data['fast_del_cost']."' WHERE store_id = '" . (int)$store_id . "'");

		$this->cache->delete('store');
	}

	public function deleteStore($store_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE store_id = '" . (int)$store_id . "'");
		$this->db->query("DELETE FROM fasc_users WHERE store_id = '" . (int)$store_id . "'");

		$this->cache->delete('store');
	}

	public function getDefaultUrl(){
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = 'config_ssl' LIMIT 1");
		if($query->num_rows){
			return $query->row['value'];
		}else{
			return '';
		}
	}

	public function getStoreBSD($store_id){
		$query = $this->db->query("SELECT store_id, min_total, timing_from, timing_to, timing_slot, fast_del_cost FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");

		$timing_from = (int)$query->row['timing_from'];
		$timing_to = (int)$query->row['timing_to'];

		if($timing_from == 12) $timing_from = '12 PM';
		else if($timing_from > 12) $timing_from = ($timing_from - 12) . ' PM';
		else $timing_from = $timing_from . ' AM';

		if($timing_to == 12) $timing_to = '12 PM';
		else if($timing_to > 12) $timing_to = ($timing_to - 12) . ' PM';
		else $timing_to = $timing_to . ' AM';

		return array(
			'timing_from' => $timing_from,
			'timing_to' => $timing_to,
			'timing_slot' => $query->row['timing_slot'],
			'min_total' => $query->row['min_total'],
			'fast_del_cost' => $query->row['fast_del_cost']
		);
	}

	public function getStore($store_id) {
		$query = $this->db->query("SELECT store_id, name, min_total, timing_from, timing_to, timing_slot, fast_del_cost FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");

		return $query->row;
	}

	public function getStoreCity($store_id) {
		$query = $this->db->query("SELECT city_id FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['city_id'];
	}

	public function getStoreByCity($city_id, $region_id) {
		$query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "store WHERE city_id = '" . (int)$city_id . "' AND region_id = '" . (int)$region_id . "' LIMIT 1");

		return $query->row;
	}

	public function getStores($minimal = false) {

		if($minimal){
			$sql = "SELECT store_id, name, city_id, region_id FROM " . DB_PREFIX . "store ORDER BY store_id ASC";
		}else{
			$sql = "SELECT * FROM " . DB_PREFIX . "store ORDER BY store_id ASC";
		}

		$query = $this->db->query($sql);
		$store_data = $query->rows;

		return $store_data;
	}

	public function getTotalStores() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store");

		return $query->row['total'];
	}

	public function getTotalStoresByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_layout_id' AND `value` = '" . (int)$layout_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByLanguage($language) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_language' AND `value` = '" . $this->db->escape($language) . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCurrency($currency) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND `value` = '" . $this->db->escape($currency) . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_country_id' AND `value` = '" . (int)$country_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_zone_id' AND `value` = '" . (int)$zone_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByCustomerGroupId($customer_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_customer_group_id' AND `value` = '" . (int)$customer_group_id . "' AND store_id != '0'");

		return $query->row['total'];
	}

	public function getTotalStoresByInformationId($information_id) {
		$account_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_account_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");

		$checkout_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_checkout_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");

		return ($account_query->row['total'] + $checkout_query->row['total']);
	}

	public function getTotalStoresByOrderStatusId($order_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_order_status_id' AND `value` = '" . (int)$order_status_id . "' AND store_id != '0'");

		return $query->row['total'];
	}
}