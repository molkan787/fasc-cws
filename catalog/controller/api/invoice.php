<?php
include_once 'dep.php';

use Konekt\PdfInvoice\InvoicePrinter;

class ControllerApiInvoice extends Controller{

	public function index(){

		$this->load->model('admin/order');
		$this->load->model('admin/setting');

		$order_id = $this->getInput('order_id');

		$order = $this->model_admin_order->getOrder($order_id);
		$items = $this->model_admin_order->getOrderProducts($order_id);

		/*if(!$this->customer->isLogged()){
			$this->respond_fail('NO_CUSTOMER');
			return;
		}
		if(intval($this->customer->getId()) != intval($order['customer_id'])){
			$this->respond_fail('ACCESS_DENIED');
			return;
		}*/

		$_ref = 'ORD-' . $order['order_id'];
		$_date = explode(' ', $order['date_added']);

		$_to = array($order['customer'], $order['shipping_address_1']);
		if(!empty($order['shipping_address_2'])) $_to[] = $order['shipping_address_2'];
		$_to[] = $order['shipping_city'];


		$company_name = "WalkOnRetail";
		$_from = array(
			$company_name,
			$this->model_admin_setting->getSettingValue('config_name', $order['store_id']),
			$this->config->get('config_address')
		);

		$total = floatval($order['total']);
		$subtotal = 0;

		$invoice = new InvoicePrinter('A4', 'INR', 'en');


		/* Header settings */
		//$invoice->setLogo("images/sample1.jpg");   //logo image path
		$invoice->flipflop();
		$invoice->setColor("#F36F24");      // pdf color scheme
		$invoice->setType("Order Invoice");    // Invoice Type
		$invoice->setReference($_ref);   // Reference
		$invoice->setDate($_date[0]);   //Billing Date
		//$invoice->setTime($_date[1]);   //Billing Time
		//$invoice->setDue(date('M dS ,Y',strtotime('+3 months')));    // Due Date
		$invoice->setFrom($_from);
		$invoice->setTo($_to);

		foreach ($items as $item) {
			$price = floatval($item['price']);
			$q = intval($item['quantity']);
			$ltotal = $price * $q;
			$subtotal += $ltotal;
			$invoice->addItem($item['name'], '', $q, false, $price, false, $ltotal);
		}

		//$invoice->addItem("AMD Athlon X2DC-7450","2.4GHz/1GB/160GB/SMP-DVD/VB",6,false,580,0,3480);

		$invoice->addTotal("Sub Total", $subtotal);
		$invoice->addTotal("Delivery fee", $total - $subtotal);
		$invoice->addTotal("Total", $total,true);

		if($order['paid']){
			$invoice->addBadge("Payment Paid");
		}

		$invoice->addTitle("Important Notice");

		$invoice->addParagraph("Thank you for shopping on WalkOnRetail!");

		$invoice->setFooternote($company_name);

		$invoice->render('INV-'.$order['order_id'].'.pdf','I'); 
		/* I => Display on browser, D => Force Download, F => local path save, S => return document as string */

	}

}