<?php
include_once 'dep.php';

class ControllerApiCspv extends Controller{

	public function razor(){

		$order_id = (int)$this->getInput('order_id');
		$payment_id = $this->getInput('payment_id');

		if(empty($payment_id) || empty($order_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$this->load->model('admin/payments');
		$this->load->model('admin/order');
		$this->load->model('checkout/order');

		$payment = $this->model_admin_payments->getPaymentDetail($payment_id);
		
		if($payment->status == 'authorized'){

			$_order_id = $this->extractOrderId($payment->description);

			$amount = intval($payment->amount) / 100;
			$amount_refunded = intval($payment->amount_refunded);

			if($payment->currency !== 'INR' || $amount_refunded > 0 || $payment->entity !== 'payment' || $_order_id != $order_id){
				$this->respond_fail('invalid_data');
				return;
			}

			$order = $this->model_admin_order->getOrderBasis($order_id);

			if(!$order){
				$this->respond_fail('order_not_found');
				return;
			}

			$order_total = floatval($order['total']);

			if($amount < $order_total){
				$this->respond_fail('invalid_data');
				return;
			}

			$this->model_admin_order->markAsPaid($order_id);
			$this->model_checkout_order->addOrderHistory($order_id, 1);

			$this->db->query("UPDATE oc_order SET payment_method = 'Credit Card/NetBanking', payment_code = 'razor' WHERE order_id = '".(int)$order_id."'");

			$this->load->model('admin/notifier');
			$this->model_admin_notifier->notify_order($order['total'], $order['del_timing']);

			$redirect = $this->url->link('checkout/success');
			$this->respond_json($redirect);

		}else{
			$this->respond_fail('invalid_data');
		}

	}

	private function extractOrderId($text){
		$chars = str_split($text);
		$buff = '';
		$buffering = false;

		foreach ($chars as $char) {
			if($buffering){
				$buff .= $char;
			}else if($char == '#'){
				$buffering = true;
			}
		}

		return intval($buff);

	}

}