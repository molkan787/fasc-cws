<?php
include_once 'dep.php';

class ControllerApiRtdc extends Controller{

	public function index(){
		checkAccess(AG_ADMIN);

		$this->load->model('admin/order');
		$this->load->model('admin/users');

		$user = $this->model_admin_users->loadCurrent();
		$limited = (intval($user['user_type']) == 14);

		$store_id = $this->config->get('config_store_id');

		$lastCheck = (int)$this->getInput('time');

		$totalOrders = $this->model_admin_order->getNewOrdersCountByStoreId($store_id, $lastCheck, $limited);

		$response = array(
			'orders_count' 	=> $totalOrders,
			'products' 	=> $products,
			'time' 			=> time()
		);

		$this->respond_json($response);
	}

	public function v2(){ //
		checkAccess(AG_ADMIN);

		$this->load->model('admin/order');
		$this->load->model('admin/users');

		$user = $this->model_admin_users->loadCurrent();
		$limited = (intval($user['user_type']) == 14);

		$store_id = $this->config->get('config_store_id');

		$lastCheck = $this->getInput('time');
		if($lastCheck == 'NOW') $lastCheck = time();

		$products = array();
		if(!$limited){
			$this->load->model('catalog/prt');
			$prts = $this->model_catalog_prt->getBasicChanges($lastCheck, array());
			foreach($prts as $p){
				$price = floatval($p['price']);
				$d_type = intval($p['discount_type']);
				$d_amt = intval($p['discount_amt']);
				if($d_type != 0 && $d_amt > 0){
					$discount = $d_type == 1 ? ($d_amt / 100) * $price : $d_amt;
					$price -= $discount;
				}
				$products[] = array(
					'id' 		=> $p['product_id'],
					'price' 	=> $price,
					'quantity' 	=> $p['quantity'],
					'name' 	=> $p['name'],
					'cat' 	=> $p['cat'],
					'barcode' 	=> $p['barcode']
				);
			}
		}

		$orders = $this->model_admin_order->getNewOrdersBasisByStoreId($store_id, $lastCheck, $limited);

		$response = array(
			'orders' => $orders,
			'products' 	=> $products,
			'time' => time()
		);

		$this->respond_json($response);
	}


}