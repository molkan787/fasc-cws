<?php
class ModelEcproductcarouselProduct extends Model {

	public function checkInstall(){
		$sql = " SHOW TABLES LIKE '".DB_PREFIX."customer_view_product'";
		$query = $this->db->query( $sql );

		if( count($query->rows) <=0 )
			$this->createTables();

		return ;
	}

	public function createTables(){
		$sql = array();
		$sql[] = "
			CREATE TABLE IF NOT EXISTS `".DB_PREFIX."customer_view_product` (
			  `customer_id` int(11) NOT NULL,
			  `product_id` int(11) NOT NULL,
			  `viewed` int(11) DEFAULT '0',
			  `date_added` datetime DEFAULT NULL,
			  `date_modified` datetime DEFAULT NULL,
			  `ip` varchar(40) DEFAULT NULL,
			  `browser` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`customer_id`,`product_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";

		foreach( $sql as $q ){
				$query = $this->db->query( $q );
			}

	}

	function get_client_ip() {
	     $ipaddress = '';
	     if (isset($_SERVER['HTTP_CLIENT_IP']))
	         $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	     else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	         $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	     else if(isset($_SERVER['HTTP_X_FORWARDED']))
	         $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	     else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	         $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	     else if(isset($_SERVER['HTTP_FORWARDED']))
	         $ipaddress = $_SERVER['HTTP_FORWARDED'];
	     else if(isset($_SERVER['REMOTE_ADDR']))
	         $ipaddress = $_SERVER['REMOTE_ADDR'];
	     else
	         $ipaddress = 'UNKNOWN';

	     return $ipaddress; 
	}
	public function updateViewed($product_id = 0){
		$query = $this->db->query("SELECT COUNT(*) as total FROM ".DB_PREFIX."customer_view_product WHERE customer_id=".(int)$this->customer->getId()." AND product_id=".(int)$product_id);

		$ip_address = $this->get_client_ip();
		//$browser = @get_browser(null, true);
		//$browser_name = isset($browser['browser'])?$browser['browser']:"";
		$browser_name = "";

		if($query->num_rows > 0 && $query->row['total'] > 0){

			$query = $this->db->query("UPDATE ".DB_PREFIX."customer_view_product SET `viewed` = (`viewed` + 1), date_modified='".date("Y-m-d H:i:s")."', ip='".$ip_address."', browser='".$browser_name."' WHERE customer_id=".(int)$this->customer->getId()." AND product_id=".(int)$product_id);
		}else{

			$query = $this->db->query("INSERT INTO ".DB_PREFIX."customer_view_product SET `viewed` = 1, customer_id=".(int)$this->customer->getId().", product_id=".(int)$product_id.", date_added='".date("Y-m-d H:i:s")."', date_modified='".date("Y-m-d H:i:s")."', ip='".$ip_address."', browser='".$browser_name."'");
		}
	}

	public function getProduct($product_id){
		return $this->model_catalog_product->getProduct($product_id);
	}
	public function getTotalBought($product_id = 0, $order_status_id = 5){
		$bought = 0;
		$order_status_id = is_array($order_status_id)?$order_status_id: array(5);
		$query = $this->db->query("SELECT sum(quantity) as total FROM `" . DB_PREFIX . "order_product` op
			LEFT JOIN `".DB_PREFIX."order` AS o ON op.order_id = o.order_id WHERE op.product_id = ".$product_id." AND o.order_status_id IN (".implode(",", $order_status_id).")");
		if($query->num_rows){
			return $query->row['total'];
		}
		return 0;
	}
	
	public function getProducts($data = array()) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
		$sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$customer_group_id . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special"; 
		
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";			
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}
		
			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}
		
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";	
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";			
			}	
		
			if (!empty($data['filter_filter'])) {
				$implode = array();
				
				$filters = explode(',', $data['filter_filter']);
				
				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}
				
				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";				
			}
		}	

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";
			
			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}
				
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}
			
			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}
			
			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
			}
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}	
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}		

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}		
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}
			
			$sql .= ")";
		}
					
		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		
		$sql .= " GROUP BY p.product_id";
		
		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";	
		}
		
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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
		$product_data = array();
				
		$query = $this->db->query($sql);
	
		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getRandomProducts($data = array()) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
		
		$sql = "SELECT p.product_id "; 
		
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";			
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}
			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ";
		$sql .= " WHERE p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id')."'";
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";	
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";			
			}
		}
		$sql .= " GROUP BY p.product_id";
		$sql .= " ORDER BY rand()";	
	
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}				

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
		
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();
				
		$query = $this->db->query($sql);
	
		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getPopularProducts($data) {
		$limit = isset($data['limit'])?$data['limit']:10;
  		$this->load->model('catalog/product');
		$product_data = array();

		$sql = "SELECT p.product_id "; 
		if (!empty($data['filter_category_id'])) {
				if (!empty($data['filter_sub_category'])) {
					$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";			
				} else {
					$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
				}
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ";
		$sql .= " WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";	
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";			
			}
		}
		$sql .= " GROUP BY p.product_id";
		$sql .= " ORDER BY p.viewed, p.date_added DESC ";
		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) { 		
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}
					 	 		
		return $product_data;
	}
	public function getLatestProducts($data) {
		$limit = isset($data['limit'])?$data['limit']:10;
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
		if (!empty($data['filter_category_id'])) {
			$product_data = $this->cache->get('product.latest.' . (int)$data['filter_category_id'] . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $customer_group_id . '.' . (int)$limit);
		}else{
			$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $customer_group_id . '.' . (int)$limit);
		}
		

		if (!$product_data) {
			$sql = "SELECT p.product_id "; 
		
			if (!empty($data['filter_category_id'])) {
				if (!empty($data['filter_sub_category'])) {
					$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";			
				} else {
					$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
				}
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product p";
			}
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ";
			$sql .= " WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id')."'";
			if (!empty($data['filter_category_id'])) {
				if (!empty($data['filter_sub_category'])) {
					$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";	
				} else {
					$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";			
				}
			}
			$sql .= " GROUP BY p.product_id";
			$sql .= " ORDER BY p.date_added DESC ";
			$sql .= " LIMIT " . (int)$limit;
			
			$query = $this->db->query($sql);
		 	 
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			if (!empty($data['filter_category_id'])) {
				$this->cache->set('product.latest.' . (int)$data['filter_category_id'] . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
			}else{
				$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
			}
		}
		
		return $product_data;
	}

	public function getProductSpecials($data = array()) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
		$join = "";
		$where = "";
		if (isset($data['filter_category_id']) && !empty($data['filter_category_id'])) {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (ps.product_id = p2c.product_id)";
			if(is_array($data['filter_category_id'])){
				$where .= " AND p2c.category_id IN (" . implode(",",$data['filter_category_id']) . ")";
			}else{
				$where .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}
		
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps ".$join." LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' ".$where." AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.date_added',
			'ps.price',
			'ps.date_end',
			'rating',
			'p.sort_order'
		);

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";
			
			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}
				
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}
			
			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}
			
			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
			}
		
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}	
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}		

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}		
			
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}
			
			$sql .= ")";				
		}

		$sql .= " GROUP BY ps.product_id";

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";	
		}
		
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$product_data = array();
		
		$query = $this->db->query($sql);
		foreach ($query->rows as $result) { 		
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}
		return $product_data;
	}


	public function getBestSellerProducts($data) {
		$limit = isset($data['limit'])?$data['limit']:10;
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
		if (!empty($data['filter_category_id'])) {		
			$product_data = $this->cache->get('product.bestseller.' . (int)$data['filter_category_id'] . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit);
		}else{
			$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit);
		}

		if (!$product_data) { 
			$product_data = array();
			$sql = "SELECT op.product_id, COUNT(*) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) ";

			if (!empty($data['filter_category_id'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id) ";
			}
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ";
			$sql .= " WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
			if (!empty($data['filter_category_id'])) {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";						
			}
			$sql .= " GROUP BY p.product_id";
			$sql .= " ORDER BY total DESC ";
			$sql .= " LIMIT " . (int)$limit;

			$query = $this->db->query($sql);
			
			foreach ($query->rows as $result) { 		
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			if (!empty($data['filter_category_id'])) {		
				$this->cache->set('product.bestseller.' . (int)$data['filter_category_id'] . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
			}else{
				$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
			}
			
		}
		
		return $product_data;
	}
	
	public function getProductAttributes($product_id) {
		$product_attribute_group_data = array();
		
		$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");
		
		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();
			
			$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");
			
			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text']		 	
				);
			}
			
			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);			
		}
		
		return $product_attribute_group_data;
	}
			
	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");
		
		foreach ($product_option_query->rows as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				$product_option_value_data = array();
			
				$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");
				
				foreach ($product_option_value_query->rows as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'name'                    => $product_option_value['name'],
						'image'                   => $product_option_value['image'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
									
				$product_option_data[] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $product_option_value_data,
					'required'          => $product_option['required']
				);
			} else {
				$product_option_data[] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $product_option['option_value'],
					'required'          => $product_option['required']
				);				
			}
      	}
		
		return $product_option_data;
	}
	
	public function getProductDiscounts($product_id) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $query->rows;		
	}
		
	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}
	
	public function getProductRelated($product_id, $limit = 10) {
		$product_data = array();
		$where = "pr.product_id = '" . (int)$product_id . "'";
		if(is_array($product_id)) {
			$where = "pr.product_id IN (" . implode(",", $product_id) . ")";
		}
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE ".$where." AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT ".(int)$limit);
		
		foreach ($query->rows as $result) { 
			$product_data[$result['related_id']] = $this->getProduct($result['related_id']);
		}
		
		return $product_data;
	}

	public function getAlsoboughtProducts($data, $enable_cache = false) {
		$product_data = array();
		$limit = isset($data['limit'])?$data['limit']:10;
		$product_id = isset($data['product_id'])?$data['product_id']:0;
		$order_status_id = isset($data['order_status_id'])?$data['order_status_id']:array(5);
		$data['filter_category_id'] = isset($data['filter_category_id'])?$data['filter_category_id']:0;

		if(is_array($product_id)) {
			$filter_product_id = implode("_", $product_id);
		} else {
			$filter_product_id = (int)$product_id;
		}
		if(is_array($order_status_id)) {
			$filter_order_status_id = implode("_", $order_status_id);
		} else {
			$filter_order_status_id = (int)$order_status_id;
		}
		$cache_name = "ecalsobought.products.".$filter_product_id.".".$filter_order_status_id.".".(int)$data['filter_category_id'].".".(int)$limit;

		if($enable_cache) {
			$product_data = $this->cache->get($cache_name);
		}
		

		if(!$product_data) {

			$sql = "SELECT op.product_id FROM ".DB_PREFIX."order_product op ";

			if (!empty($data['filter_category_id'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = op.product_id)";
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = op.product_id)";
			}
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ";

			if(is_array($product_id)){
				$order_query = $this->db->query("SELECT op.order_id FROM ".DB_PREFIX."order_product op LEFT JOIN ".DB_PREFIX."order o ON op.order_id = o.order_id WHERE product_id IN (".implode(",", $product_id).") AND order_status_id IN (".implode(",", $order_status_id).")");
				$order_id = array(0);
				if($order_query->num_rows > 0) {
					foreach($order_query->rows as $row ){
						$order_id[] = $row['order_id'];
					}
				}
				
				$sql .= " WHERE op.order_id IN (".implode(",", $order_id).") AND p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id')."' AND p.product_id NOT IN (".implode(",", $product_id).")";
			} else {
				$order_query = $this->db->query("SELECT op.order_id FROM ".DB_PREFIX."order_product op LEFT JOIN ".DB_PREFIX."order o ON op.order_id = o.order_id WHERE product_id=".(int)$product_id." AND order_status_id IN (".implode(",", $order_status_id).")");
				$order_id = array(0);
				if($order_query->num_rows > 0) {
					foreach($order_query->rows as $row ){
						$order_id[] = $row['order_id'];
					}
				}

				$sql .= " WHERE op.order_id IN (".implode(",", $order_id).") AND p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id')."' AND p.product_id !=".(int)$product_id;
			}
			
			if (!empty($data['filter_category_id'])) {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			$sql .= " GROUP BY p.product_id";
			$sql .= " ORDER BY p.date_added DESC ";
			$sql .= " LIMIT " . (int)$limit;

			$query = $this->db->query($sql);
			
			foreach ($query->rows as $result) { 
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			if($enable_cache) {
				$this->cache->set($cache_name, $product_data);
			}
			
		}
		return $product_data;
	}

	public function getAlsoviewedProducts($data){
		$product_data = array();
		$limit = isset($data['limit'])?$data['limit']:10;
		$product_id = isset($data['product_id'])?$data['product_id']:0;
		$order_status_id = isset($data['order_status_id'])?$data['order_status_id']:array(5);

		$sql = "SELECT cp.product_id FROM ".DB_PREFIX."customer_view_product cp ";

		if (!empty($data['filter_category_id'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = cp.product_id)";
			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = cp.product_id)";
		}
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) ";

		if(is_array($product_id)) {
			$sql .= " WHERE cp.customer_id IN (SELECT cp.customer_id FROM ".DB_PREFIX."customer_view_product cp LEFT JOIN ".DB_PREFIX."customer c ON cp.customer_id = c.customer_id WHERE product_id IN (".implode(",", $product_id).")) AND p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id')."' AND p.product_id NOT IN (".implode(",", $product_id).")";
		} else {
			$sql .= " WHERE cp.customer_id IN (SELECT cp.customer_id FROM ".DB_PREFIX."customer_view_product cp LEFT JOIN ".DB_PREFIX."customer c ON cp.customer_id = c.customer_id WHERE product_id=".(int)$product_id.") AND p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id')."' AND p.product_id !=".(int)$product_id;
		}

		if (!empty($data['filter_category_id'])) {
			$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
		}

		$sql .= " GROUP BY p.product_id";
		$sql .= " ORDER BY p.date_added DESC ";
		$sql .= " LIMIT " . (int)$limit;

		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) { 
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}
		
		return $product_data;
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->rows;
	}	
	
}
?>