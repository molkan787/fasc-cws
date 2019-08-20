<?php
class ModelAccountWishlist extends Model {
	public function addWishlist($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_wishlist SET customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', date_added = NOW()");
	}

	public function deleteWishlist($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");
	}

	public function getWishlist() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->rows;
	}

	public function isInWishList($product_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '".(int)$product_id."'");
		return ($query->num_rows > 0);
	}

	public function getWishlistStr($store_id = 0) {
        $query = $this->db->query("SELECT GROUP_CONCAT(cw.product_id) as products FROM " . DB_PREFIX . "customer_wishlist cw LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON cw.product_id = p2s.product_id WHERE cw.customer_id = '" . (int)$this->customer->getId() . "' AND p2s.store_id = '".(int)$store_id."'");

		return $query->row['products'];
	}

	public function getWishlistObj() {
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		$prts = array();
		foreach ($query->rows as $row) {
			$prts[$row['product_id']] = true;
		}
		return $prts;
	}


	public function getTotalWishlist() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
}
