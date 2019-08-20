<?php
class ControllerExtensionPaymentCod extends Controller {
	public function index() {
		if($this->session->data['payment_methods_razor']){
			$this->load->model('admin/fasc');
			$total = floatval($this->session->data['order_total']) * 100;
			$email = $this->customer->getEmail();
			if(substr($email, 0, 9) == 'customer_') $email = '';
			$data = array(
				'razor' => true,
				'razor_key' => $this->model_admin_fasc->getSettingValue($this->config->get('config_store_id'), 'razor_key'),
				'phone' => $this->customer->getTelephone(),
				'name' => $this->customer->getFirstname() . $this->customer->getLastname(),
				'email' => $email,
				'merchant' => $this->config->get('config_name'),
				'total' => $total,
				'order_id' => $this->session->data['order_id']
			);
			return $this->load->view('extension/payment/cod', $data);
		}else{
			return $this->load->view('extension/payment/cod');
		}
	}

	public function confirm() {
		$json = array();
		
		if ($this->session->data['payment_method']['code'] == 'cod') {
			$this->load->model('checkout/order');
			$this->load->model('admin/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod_order_status_id'));
		
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
}
