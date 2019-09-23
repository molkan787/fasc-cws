<?php
include_once 'utils.php';

class ModelCatalogPrt extends Model {

	public function getProductBasics($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'tag'              => $query->row['tag'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'price'		       => $query->row['price'],
				'discount'         => $query->row['discount'],
				'special'          => $query->row['special'],
				'subtract'         => $query->row['subtract'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status']
			);
		} else {
			return false;
		}
	}

	public function getProductsPos($store_id) {
        $this->load->model('tool/image');

		$sql = "SELECT p.product_id, p.cat, p.barcode, p.quantity, p.price, p.discount_type, p.discount_amt, p.image, p.gst, p.hsn, pd.name  FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sql .= " AND p.store_id = '" . (int)$store_id. "'";
		$sql .= " AND p.status = '1'";
		$sql .= " GROUP BY p.product_id ORDER BY p.product_id DESC";

		$query = $this->db->query($sql);

		$result = array();
		$l = count($query->rows);
		for($i = 0; $i < $l; $i++){
			$prt = $query->rows[$i];
			$price = floatval($prt['price']);
			if((int)$prt['discount_amt'] > 0){
				if((int)$prt['discount_type'] == 1){
					$price -= $price * ((int)$prt['discount_amt'] / 100);
				}else{
					$price -= (int)$prt['discount_amt'];
				}
			}
			array_push($result, array(
				'id' => $prt['product_id'],
				'quantity' => $prt['quantity'],
				'barcode' => $prt['barcode'],
				'real_price' => floatval($prt['price']),
				'price' => $price,
				'name' => $prt['name'],
				'cat' => $prt['cat'],
				'tax' => intval($prt['gst']),
				'hsn' => $prt['hsn'],
				'image' => url($this->model_tool_image->resize($prt['image'], 50, 50))
			));
		}

		return $result;
	}

	public function getProductsNames($store_id) {

		$sql = "SELECT p.product_id, pd.name  FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sql .= " AND p.store_id = '" . (int)$store_id. "'";
		$sql .= " GROUP BY p.product_id ORDER BY p.product_id DESC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProducts($data = array()) {
        $this->load->model('tool/image');
        $this->load->model('account/wishlist');
		$moreDetails = (isset($data['moreDetails']) && $data['moreDetails']);

		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (isset($data['list']) && $data['list'] !== '') {
			$sql .= " AND p.product_id IN (" . $this->db->escape($data['list']) . ")";
		}

		if (isset($data['store']) && $data['store'] !== '') {
			$sql .= " AND p.store_id = '" . (int)$data['store'] . "'";
		}

		if (!empty($data['name'])) {
			$name = $this->db->escape($data['name']);
			$sql .= " AND (pd.name LIKE '%" . $name . "%' OR p.product_id = '" . $name . "')";
		}

		if (isset($data['stock']) && $data['stock'] !== '') {
			$sql .= " AND p.quantity ";
			if($data['stock'] == 'out') $sql .= "= 0";
			else if($data['stock'] == 'low') $sql .= "< 3";
			else if($data['stock'] == 'high') $sql .= "> 3";
		}

		if (isset($data['status']) && $data['status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['status'] . "'";
		}

		if (isset($data['cat']) && $data['cat'] !== '') {
			$sql .= " AND (p.cat = '" . (int)$data['cat'] . "' OR p.brand = '" . (int)$data['cat'] . "')";
		}

		if (isset($data['subcat']) && $data['subcat'] !== '') {
			$sql .= " AND (p.subcat = '" . (int)$data['subcat'] . "' OR p.sub_brand = '" . (int)$data['subcat'] . "')";
		}

		if (isset($data['child_subcat']) && $data['child_subcat'] !== '') {
			$sql .= " AND p.child_subcat = '" . (int)$data['child_subcat'] . "'";
		}

		if (isset($data['discount']) && $data['discount'] !== '') {
			$sql .= " AND p.discount_amt ";
			$sql .= ($data['discount'] == 'with') ? '> 0' : '= 0';
		}

		if($moreDetails){
			$sql .= " AND status = '1'";
		}

		$sql .= " GROUP BY p.product_id";

		if(isset($data['order_by']) && $data['order_by'] !== ''){
			$sql .= " ORDER BY ".$data['order_by'];
			if(isset($data['order']) && $data['order'] == 'ASC'){
				$sql .= " ASC";
			}else{
				$sql .= " DESC";
			}
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

		$result = array();
		$l = count($query->rows);


		
		if($moreDetails){
			$wishlist = $this->model_account_wishlist->getWishlistObj();

			for($i = 0; $i < $l; $i++){
				$prt = $query->rows[$i];
				array_push($result, array(
					'product_id' => $prt['product_id'],
					'quantity' => $prt['quantity'],
					'title' => $prt['name'],
					'price' => $prt['price'],
					'spf' => $prt['spf'],
					'spf_unit' => $prt['spf_unit'],
					'discount_amt' => $prt['discount_amt'],
					'discount_type' => $prt['discount_type'],
					'image' => url($this->model_tool_image->resize($prt['image'], 120, 120)),
					'in_wishlist' => isset($wishlist[$prt['product_id']])
				));
			}
		}else{
			for($i = 0; $i < $l; $i++){
				$prt = $query->rows[$i];
				array_push($result, array(
					'product_id' => $prt['product_id'],
					'quantity' => $prt['quantity'],
					'title' => $prt['name'],
					'status' => $prt['status'],
					'image' => url($this->model_tool_image->resize($prt['image'], 120, 120))
				));
			}
		}

		return $result;
	}

	public function getBasicChanges($time, $ids){
		$cast2Int = function ($item){
			return (int)$item;
		};
		$ids = count($ids) ? implode(',', array_map($cast2Int, $ids)) : false;

		$sql = "SELECT p.product_id, p.cat, p.barcode, p.quantity, p.price, p.discount_amt, p.discount_type, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id) WHERE p.date_modified >= FROM_UNIXTIME(".(int)$time.") GROUP BY p.product_id";

		if($ids){
			$sql .= " AND product_id IN (".$ids.")";
		}

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $query->rows;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductRelated($product_id) {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->getProduct($result['related_id']);
		}

		return $product_data;
	}

	public function getProductLayoutId($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($filters = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($filters['filter_category_id'])) {
			if (!empty($filters['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($filters['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($filters['filter_category_id'])) {
			if (!empty($filters['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$filters['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$filters['filter_category_id'] . "'";
			}

			if (!empty($filters['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $filters['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($filters['filter_name']) || !empty($filters['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($filters['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $filters['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($filters['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($filters['filter_name']) . "%'";
				}
			}

			if (!empty($filters['filter_name']) && !empty($filters['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($filters['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $filters['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($filters['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($filters['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($filters['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($filters['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($filters['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($filters['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($filters['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($filters['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($filters['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$filters['filter_manufacturer_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProfile($product_id, $recurring_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r JOIN " . DB_PREFIX . "product_recurring pr ON (pr.recurring_id = r.recurring_id AND pr.product_id = '" . (int)$product_id . "') WHERE pr.recurring_id = '" . (int)$recurring_id . "' AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

		return $query->row;
	}

	public function getProfiles($product_id) {
		$query = $this->db->query("SELECT rd.* FROM " . DB_PREFIX . "product_recurring pr JOIN " . DB_PREFIX . "recurring_description rd ON (rd.language_id = " . (int)$this->config->get('config_language_id') . " AND rd.recurring_id = pr.recurring_id) JOIN " . DB_PREFIX . "recurring r ON r.recurring_id = rd.recurring_id WHERE pr.product_id = " . (int)$product_id . " AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getTotalProductSpecials() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}
}
