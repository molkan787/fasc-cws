<?php
include_once 'dep.php';

class ControllerApiOrderadm extends Controller
{

	public function change(){

		checkAccess(AG_ADMIN);

		$operation = $this->getInput('operation');

		if(empty($operation)){
			$this->respond_fail('argument_missing');
			return;
		}

		$this->load->model('admin/order');

		if($operation == 'status'){

			$order_id = $this->getInput('order_id', '');
			$status = $this->getInput('status', '');
			if(!empty($order_id) || !empty($status)){

				$this->model_admin_order->setStatus($order_id, $status);

				$this->respond_json(array('order_id' => (int)$order_id, 'status' => (int)$status, 'operation' => $operation));

			}else{
				$this->respond_fail('argument_missing');
			}

		}else if ($operation == 'delete'){
			$order_id = $this->getInput('order_id', '');
			if(!empty($order_id)){
				$this->model_admin_order->delete($order_id);
				$this->respond_json(array('order_id' => (int)$order_id, 'operation' => $operation));
			}else{
				$this->respond_fail('argument_missing');
			}

		}else{
			$this->respond_fail('invalid_argument');
		}
	}

	public function info(){

		checkAccess(AG_ADMIN);

		$order_id = $this->getInput('order_id', '');
		if(empty($order_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$this->load->model('admin/order');


		$order = $this->model_admin_order->getOrder($order_id);
		if($order){
			$order['items'] = $this->model_admin_order->getOrderProducts($order_id);
		}else{
			$this->respond_fail('not_found');
			return;
		}

		$this->respond_json($order);

	}

	public function list(){
		checkAccess(AG_ADMIN);

		$this->load->model('admin/order');
		$this->load->model('admin/users');

		$user = $this->model_admin_users->loadCurrent();
		$limited = (intval($user['user_type']) == 14);

		$start = $this->getInput('start', 0);
		$limit = $this->getInput('limit', 20);

		$store_id = $this->config->get('config_store_id');

		$filters = array(
			'order' => 'DESC',
			'start' => $start,
			'limit' => $limit,
			'store' => $store_id,

			'limited' => $limited,

			'customer_id' => $this->getInput('customer_id', ''),
			'filter_order_status_id' => $this->getInput('status', ''),
			'filter_date_added' => $this->getInput('order_date', '')
		);

		$orders = $this->model_admin_order->getOrders($filters);
		$totalOrders = $this->model_admin_order->getTotalOrdersByStoreId($store_id);

		$response = array(
			'total' => $totalOrders,
			'items' => $orders
		);

		$this->respond_json($response);

	}

	public function infoCS(){

		if(!$this->customer->isLogged()){
			$this->respond_fail('NO_CUSTOMER');
			return;
		}

		$customer_id = $this->customer->getId();

		$order_id = $this->getInput('order_id', '');
		if(empty($order_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$this->load->model('admin/order');


		$order = $this->model_admin_order->getOrder($order_id);

		if(intval($order['customer_id']) != $customer_id){
			$this->respond_fail('not_found');
			return;
		}

		if($order){
			$order['items'] = $this->model_admin_order->getOrderProducts($order_id);
		}else{
			$this->respond_fail('not_found');
			return;
		}

		$this->respond_json($order);

	}

	public function listCS(){

		if(!$this->customer->isLogged()){
			$this->respond_fail('NO_CUSTOMER');
			return;
		}

		$customer_id = $this->customer->getId();

		$this->load->model('admin/order');

		$start = $this->getInput('start', 0);
		$limit = $this->getInput('limit', 30);

		$filters = array(
			'order' => 'DESC',
			'start' => $start,
			'limit' => $limit,
			'limited' => false,

			'customer_id' => $customer_id,
			'filter_order_status_id' => $this->getInput('status', ''),
			'filter_date_added' => $this->getInput('order_date', '')
		);

		$orders = $this->model_admin_order->getOrders($filters);

		$response = array(
			'items' => $orders
		);

		$this->respond_json($response);

	}

	public function cancel(){

		$this->load->model('admin/order');

		$order_id = $this->getInput('order_id', '');
		if(!empty($order_id)){

			$order = $this->model_admin_order->getOrder($order_id);

			if($order){
				if(intval($order['customer_id']) == $this->customer->getId()){

					$canCancel = $this->model_admin_order->canCancel($order_id);
					
					if($canCancel){
						$this->model_admin_order->setStatus($order_id, 7);
						$this->respond_json(array('order_id' => (int)$order_id));
					}else{
						$this->respond_fail('too_late');
					}


				}else{
					$this->respond_fail('not_found');
				}

			}else{
				$this->respond_fail('not_found');
			}

		}else{
			$this->respond_fail('argument_missing');
		}
	}

	public function removeProduct(){
		$this->load->model('admin/order');

		$order_id = $this->getInput('order_id');
		$product_id = $this->getInput('product_id');

		$this->model_admin_order->removeProduct($order_id, $product_id, true);

		$this->respond_json('');

	}

}