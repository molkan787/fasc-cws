<?php
define('RAZIR_API_URL', 'https://{KEY}:{SECRET}@api.razorpay.com/v1/payments/{PAYMENT_ID}');

class ModelAdminPayments extends Model {

	public function getPaymentDetail($payment_id){

		$key = $this->getRazorKey();
		$secret = $this->getRazorSecret();
		$api_url = $this->getApiUrl($key, $secret, $payment_id);
		
		$response = file_get_contents($api_url);
		$data = json_decode($response);

		return $data;
	}

	public function getRazorKey(){

		$this->load->model('admin/fasc');

		$store_id = $this->config->get('config_store_id');

		$api_key = $this->model_admin_fasc->getSettingValue($store_id, 'razor_key');
		return $api_key;
	}

	private function getRazorSecret(){

		$this->load->model('admin/fasc');

		$store_id = $this->config->get('config_store_id');

		$api_secret = $this->model_admin_fasc->getSettingValue($store_id, 'razor_secret');
		return $api_secret;
	}

	private function getApiUrl($key, $secret, $payment_id){
		$url = RAZIR_API_URL;
		$url = str_replace('{KEY}', $key, $url);
		$url = str_replace('{SECRET}', $secret, $url);
		$url = str_replace('{PAYMENT_ID}', $payment_id, $url);
		return $url;
	}

}