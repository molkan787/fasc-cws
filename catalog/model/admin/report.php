<?php
class ModelAdminReport extends Model {

	public function getTotalSales($startDate = '', $endDate = ''){
		$cond = "";
		if(!empty($startDate)){ $cond = " AND DATE(date_added) >= '".$this->db->escape($startDate)."'"; }
		if(!empty($endDate)){ $cond .= " AND DATE(date_added) <= '".$this->db->escape($endDate)."'"; }
		$sql = "SELECT sum( CASE WHEN customer_id = 0 THEN total ELSE 0 END ) AS walkon, sum( CASE WHEN customer_id != 0 THEN total ELSE 0 END ) AS online, sum(total) AS total FROM ". DB_PREFIX ."order WHERE order_status_id = 5 AND store_id = '".(int)$this->config->get('config_store_id')."'".$cond;

		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getTotalOrders($startDate = '', $endDate = ''){
		$cond = "";
		if(!empty($startDate)){ $cond = " AND DATE(date_added) >= '".$this->db->escape($startDate)."'"; }
		if(!empty($endDate)){ $cond .= " AND DATE(date_added) <= '".$this->db->escape($endDate)."'"; }
		$sql = "SELECT COUNT(*) AS total FROM ". DB_PREFIX ."order WHERE order_status_id = 5 AND store_id = '".(int)$this->config->get('config_store_id')."'".$cond;
		$query = $this->db->query($sql);

		$completed = $query->row['total'];

		$sql = "SELECT COUNT(*) AS total FROM ". DB_PREFIX ."order WHERE order_status_id = 1 AND store_id = '".(int)$this->config->get('config_store_id')."'".$cond;
		$query = $this->db->query($sql);

		$pending = $query->row['total'];

		return array(
			"total" => $completed + $pending,
			"completed" => (int)$completed,
			"pending" => (int)$pending
		);
	}

	public function getTotalCustomers($startDate = '', $endDate = ''){
		$cond = "";
		if(!empty($startDate)){ $cond = " AND DATE(date_added) >= '".$this->db->escape($startDate)."'"; }
		if(!empty($endDate)){ $cond .= " AND DATE(date_added) <= '".$this->db->escape($endDate)."'"; }
		$sql = "SELECT COUNT(*) AS total FROM ". DB_PREFIX ."customer WHERE verified = 1 AND store_id = '".(int)$this->config->get('config_store_id')."'".$cond;
		$query = $this->db->query($sql);

		$verified = $query->row['total'];

		$sql = "SELECT COUNT(*) AS total FROM ". DB_PREFIX ."customer WHERE verified = 0 AND store_id = '".(int)$this->config->get('config_store_id')."'".$cond;
		$query = $this->db->query($sql);

		$not_verified = $query->row['total'];

		return array(
			"total" => $verified + $not_verified,
			"verified" => (int)$verified,
			"not_verified" => (int)$not_verified
		);
	}

	public function getOrdersPerDay($startDate = '', $endDate = ''){
		$cond = "";
		if(!empty($startDate)){ $cond = " AND DATE(date_added) >= '".$this->db->escape($startDate)."'"; }
		if(!empty($endDate)){ $cond .= " AND DATE(date_added) <= '".$this->db->escape($endDate)."'"; }
		$sql = "SELECT COUNT(*) AS total, DATE(date_added) date FROM ". DB_PREFIX ."order WHERE store_id = '".(int)$this->config->get('config_store_id')."'".$cond." GROUP BY date_added";
		$query = $this->db->query($sql);

		$items = array();
		$timestamp = strtotime($startDate);
		$day_secs = 3600 * 24;
		$ctime = strtotime($endDate);
		while(true){
			$cdate = date('Y-m-d', $ctime);
			$items[$cdate] = 0;
			$ctime -= $day_secs;
			if($cdate == $startDate || $ctime < $timestamp) break;
		}

		foreach ($query->rows as $item) {
			$items[$item['date']] = (int)$item['total'];
		}

		return $items;
	}


	public function getCustomersPerDay($startDate = '', $endDate = ''){
		$cond = "";
		if(!empty($startDate)){ $cond = " AND DATE(date_added) >= '".$this->db->escape($startDate)."'"; }
		if(!empty($endDate)){ $cond .= " AND DATE(date_added) <= '".$this->db->escape($endDate)."'"; }
		$sql = "SELECT COUNT(*) AS total, DATE(date_added) date FROM ". DB_PREFIX ."customer WHERE store_id = '".(int)$this->config->get('config_store_id')."'".$cond." GROUP BY date_added";
		$query = $this->db->query($sql);

		$items = array();
		$timestamp = strtotime($startDate);
		$day_secs = 3600 * 24;
		$ctime = strtotime($endDate);
		while(true){
			$cdate = date('Y-m-d', $ctime);
			$items[$cdate] = 0;
			$ctime -= $day_secs;
			if($cdate == $startDate) break;
		}

		foreach ($query->rows as $item) {
			$items[$item['date']] = (int)$item['total'];
		}

		return $items;
	}

	public function getSalesPerDay($startDate = '', $endDate = ''){
		$cond = "";
		if(!empty($startDate)){ $cond = " AND DATE(date_added) >= '".$this->db->escape($startDate)."'"; }
		if(!empty($endDate)){ $cond .= " AND DATE(date_added) <= '".$this->db->escape($endDate)."'"; }
		$sql = "SELECT DATE(date_added) date, sum( CASE WHEN customer_id = 0 THEN total ELSE 0 END ) AS walkon, sum( CASE WHEN customer_id != 0 THEN total ELSE 0 END ) AS online FROM ". DB_PREFIX ."order WHERE order_status_id = 5 AND store_id = '".(int)$this->config->get('config_store_id')."'".$cond." GROUP BY date_added";

		$query = $this->db->query($sql);

		$items = array();
		$timestamp = strtotime($startDate);
		$day_secs = 3600 * 24;
		$ctime = strtotime($endDate);
		while(true){
			$cdate = date('Y-m-d', $ctime);
			$items[$cdate] = array(
				'w' => 0,
				'o' => 0
			);
			$ctime -= $day_secs;
			if($cdate == $startDate) break;
		}

		foreach ($query->rows as $item) {
			$items[$item['date']]['w'] = $item['walkon'];
			$items[$item['date']]['o'] = $item['online'];
		}

		return $items;
	}

}