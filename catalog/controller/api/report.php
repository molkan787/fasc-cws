<?php
include_once 'dep.php';

class ControllerApiReport extends Controller{

	public function general(){
		$this->load->model('admin/report');

		$dateFrom = $this->getInput('from');
		$dateTo = $this->getInput('to');

		if(empty($dateFrom)) $dateFrom = date('Y-m-d', time() - 3600 * 24 * 7);
		if(empty($dateTo)) $dateTo = date('Y-m-d', time());

		$total_sales = $this->model_admin_report->getTotalSales($dateFrom, $dateTo);
		$orders = $this->model_admin_report->getTotalOrders($dateFrom, $dateTo);
		$customers = $this->model_admin_report->getTotalCustomers($dateFrom, $dateTo);

		$ordersPerDay = $this->model_admin_report->getOrdersPerDay($dateFrom, $dateTo);
		$customersPerDay = $this->model_admin_report->getCustomersPerDay($dateFrom, $dateTo);
		$salesPerDay = $this->model_admin_report->getSalesPerDay($dateFrom, $dateTo);
		$this->respond_json(array(
			'total_sales' => $total_sales,
			'orders' => $orders,
			'customers' => $customers,
			'ordersPerDay' => $ordersPerDay,
			'customersPerDay' => $customersPerDay,
			'salesPerDay' => $salesPerDay
		));
	}

	public function dailyReport(){
		$this->load->model('admin/report');
		$this->load->model('admin/setting');

		$date = $this->getInput('date');
		if(empty($date)) $date = date('Y-m-d', time());

		$total_sales = $this->model_admin_report->getTotalSales($date, $date);
		$orders = $this->model_admin_report->getTotalOrders($date, $date);
		$customers = $this->model_admin_report->getTotalCustomers($date, $date);

		$store = $this->model_admin_setting->getSettingValue('config_name', $this->config->get('config_store_id'));

		$this->respond_json(array(
			'date' => $date,
			'store' => $store,
			'total_sales' => $total_sales,
			'orders' => $orders,
			'customers' => $customers
		));

	}

}