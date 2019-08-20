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

		$lastCheck = $this->getInput('time');

		$totalOrders = $this->model_admin_order->getNewOrdersCountByStoreId($store_id, $lastCheck, $limited);

		$response = array(
			'orders_count' => $totalOrders,
			'time' => time()
		);

		$this->respond_json($response);
	}

	public function v2(){
		checkAccess(AG_ADMIN);

		$this->load->model('admin/order');
		$this->load->model('admin/users');

		$user = $this->model_admin_users->loadCurrent();
		$limited = (intval($user['user_type']) == 14);

		$store_id = $this->config->get('config_store_id');

		$lastCheck = $this->getInput('time');
		if($lastCheck == 'NOW') $lastCheck = time();

		$orders = $this->model_admin_order->getNewOrdersBasisByStoreId($store_id, $lastCheck, $limited);

		$response = array(
			'orders' => $orders,
			'time' => time()
		);

		$this->respond_json($response);
	}


}