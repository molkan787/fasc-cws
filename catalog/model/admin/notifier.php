<?php
include_once 'utils.php';

class ModelAdminNotifier extends Model{

	public function notify_order($total, $del){
		$this->load->model('admin/fasc');
		$this->load->model('account/otp');

		$this->model_account_otp->sendOrderAlert($this->customer->getTelephone(), $total);
		
		$store_id = $this->config->get('config_store_id');
		$admin_phone = $this->model_admin_fasc->getSettingValue($store_id, 'not_phone');
		if(strlen($admin_phone) == 10){
			$this->model_account_otp->sendOrderAlertToAdmin($admin_phone, array('amount' => $total, 'delivery' => $del));
		}
	}

}