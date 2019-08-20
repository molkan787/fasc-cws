<?php
include_once 'dep.php';

class ControllerApiClient extends Controller
{

	public function delete(){

		checkAccess(AG_ADMIN);

		$customer_id = $this->getInput('customer_id');

		if(empty($customer_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$this->load->model('admin/customer');

		$this->model_admin_customer->deleteCustomer($customer_id);

		$this->respond_json(array('customer_id' => $customer_id));

	}

	public function list(){

		$this->load->model('admin/customer');

		$filters = array(
			'sort'  => 'c.date_added',
			'order' => 'DESC',
			'start' => $this->getInput('start', 0),
			'limit' => $this->getInput('limit', 20),
			'store' => $this->config->get('config_store_id'),
			'filter_name' => $this->getInput('name'),
			'filter_phone' => $this->getInput('phone'),
			'filter_status' => $this->getInput('status'),
			'filter_date_added' => $this->getInput('reg_date')
		);

		$customers = $this->model_admin_customer->getCustomers($filters);

		$this->respond_json(array('total' => 10, 'items' => $customers));

	}
}