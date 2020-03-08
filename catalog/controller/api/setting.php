<?php
include_once 'dep.php';

class ControllerApiSetting extends Controller
{

	public function update_master_setting(){
		
		checkAccess(AG_MASTER_ADMIN);

		$logo = $this->getInput('logo');
		$ls_ad = $this->getInput('ls_ad');
		$order_phone = $this->getInput('order_phone');
		$cancel_timeout = $this->getInput('cancel_timeout');
		$gls = $this->getInput('gls');
		$gls = json_decode($gls, true);

		$this->load->model('admin/store');
		$this->load->model('admin/gls');
		$this->load->model('admin/setting');

		if(!empty($logo)){
			$this->model_admin_store->setLogo($logo);
		}

		if(!empty($ls_ad)){
			$this->model_admin_setting->editSettingValue('config', 'config_ls_ad', $ls_ad, 0);
		}

		$this->model_admin_setting->editSettingValue('config', 'config_order_phone', $order_phone, 0);
		$this->model_admin_setting->editSettingValue('config', 'config_cancel_order_timeout', $cancel_timeout, 0);
		
		$this->model_admin_gls->set($gls);

		$this->respond_json('');

	}

	public function get_master_setting(){

		checkAccess(AG_MASTER_ADMIN);

		$this->load->model('admin/store');
		$this->load->model('tool/image');
		$this->load->model('admin/cities');
		$this->load->model('admin/gls');
		$this->load->model('admin/setting');

		$cities = $this->model_admin_cities->getAllCities();

		$logo = $this->model_admin_store->getLogo();

		$ls_ad = $this->model_admin_setting->getSettingValue('config_ls_ad', 0);

		$gls = $this->model_admin_gls->get();

		$data = array(
			'logo' => url('image/' . $logo),
			'ls_ad' => url('image/' . $ls_ad),
			'cities' => $cities,
			'gls' => $gls,
			'order_phone' => $this->model_admin_setting->getSettingValue('config_order_phone', 0),
			'cancel_timeout' => $this->model_admin_setting->getSettingValue('config_cancel_order_timeout', 0)
		);

		$this->respond_json($data);
	}

	public function list_stores(){

		checkAccess(AG_MASTER_ADMIN);

		$this->load->model('admin/store');
		$this->load->model('admin/cities');

		$cities = $this->model_admin_cities->getAllCities();
		$stores = $this->model_admin_store->getStores();

		$this->respond_json(array('stores' => $stores, 'cities' => $cities));

	}

	public function add_store(){

		checkAccess(AG_MASTER_ADMIN);

		$store_id = (int)$this->getInput('store_id', 0);
		$newStore = $store_id == 0;
		$store_name = $this->getInput('name');
		$owner_name = $this->getInput('owner_name');
		$city_id = (int)$this->getInput('city_id', 0);
		$region_id = (int)$this->getInput('region_id', 0);
		$gstin = $this->getInput('gstin', '');
		$reg_no = $this->getInput('reg_no', '');
		$fssai = $this->getInput('fssai', '');


		if(strlen($store_name) < 5 or strlen($store_name) > 30 or strlen($owner_name) < 8 or strlen($owner_name) > 40){
			$this->respond_fail('invalid_arguments_values');
			return;
		}
		
		$this->load->model('admin/store');
		$this->load->model('admin/setting');
		$this->load->model('admin/users');

		$config_url = $this->model_admin_store->getDefaultUrl();

		$data = $this->getDataArray($store_name, $config_url, $city_id, $region_id);
		$data['gstin'] = $gstin;
		$data['reg_no'] = $gstin;
		$data['fssai'] = $gstin;

		if($newStore) $store_id = $this->model_admin_store->addStore($data);

		$this->model_admin_setting->editSetting('config', $data, $store_id);

		$username = NULL;
		$password = NULL;
		if($newStore){
			$username = 's'.$store_id.'_admin';
			$password = _generateRandomString(10);
			$this->model_admin_users->createUser($store_id, AG_ADMIN, $username, $password, $owner_name);
		}

		$this->respond_json(array('store_id' => $store_id, 'admin' => $username, 'password' => $password));

	}

	public function delete_store(){

		checkAccess(AG_MASTER_ADMIN);

		$store_id = (int)$this->getInput('store_id');
		$master_password = $this->getInput("pwd");

		if($store_id == 0 or empty($master_password)){
			$this->respond_fail('invalid_argument');
			return;
		}

		$this->load->model('admin/users');
		$user = $this->model_admin_users->loadCurrent();

		if(!$this->model_admin_users->checkPassword($user['user_id'], $master_password)){
			$this->respond_fail('wrong_password');
			return;
		}

		$this->load->model('admin/store');
		$this->load->model('admin/setting');

		$this->model_admin_store->deleteStore($store_id);

		$this->model_admin_setting->deleteSetting('config', $store_id);

		$this->respond_json(array('store_id' => $store_id, 'msg' => 'Store ID:' . $store_id . ' was permanatly deleted.'));

	}

	public function store_setting(){

		checkAccess(AG_ADMIN);

		$this->load->model('admin/store');
		$this->load->model('admin/setting');
		$this->load->model('admin/fasc');

		$user = $this->model_admin_users->loadCurrent();
		$paymentInfo = (intval($user['user_type']) < 3);

		$store_id = $this->config->get('config_store_id');

		$store_data = $this->model_admin_store->getStore($store_id);

		$razor_key = $this->model_admin_fasc->getSettingValue($store_id, 'razor_key');
		$not_phone = $this->model_admin_fasc->getSettingValue($store_id, 'not_phone');
		$order_cancel_time = (int)$this->model_admin_fasc->getSettingValue($store_id, 'order_cancel_time');
		if(!$order_cancel_time) $order_cancel_time = 3600;

		if($store_data){
			$store_data['info'] = $this->model_admin_setting->getSetting('config', $store_id);
			$store_data['razor_key'] = $paymentInfo ? $razor_key : '';
			$store_data['not_phone'] = $not_phone;
			$store_data['order_cancel_time'] = $order_cancel_time;
		}

		$this->respond_json($store_data);

	}

	public function store_setting_save(){

		checkAccess(AG_ADMIN);

		$this->load->model('admin/store');
		$this->load->model('admin/setting');
		$this->load->model('admin/fasc');

		$store_id = $this->config->get('config_store_id');

		$user = $this->model_admin_users->loadCurrent();
		$paymentInfo = (intval($user['user_type']) < 3);

		$data = array(
			'min_total' => $this->getInput('min_total', 0),
			'timing_from' => $this->getInput('timing_from', 0),
			'timing_to' => $this->getInput('timing_to', 0),
			'timing_slot' => $this->getInput('timing_slot', 0),
			'fast_del_cost' => $this->getInput('fast_del_cost', 0)
		);

		$this->model_admin_store->editStore($store_id, $data);

		$config_name = $this->getInput('name');
		$config_telephone = $this->getInput('telephone');
		$config_email = $this->getInput('email');
		$config_address = $this->getInput('address');
		$order_phone = $this->getInput('order_phone');

		$razor_key = $this->getInput('razor_key');
		$razor_secret = $this->getInput('razor_secret');

		$not_phone = $this->getInput('not_phone');
		$order_cancel_time = $this->getInput('order_cancel_time');

		$this->model_admin_setting->editSettingValue('config', 'config_name', $config_name, $store_id);
		$this->model_admin_setting->editSettingValue('config', 'config_telephone', $config_telephone, $store_id);
		$this->model_admin_setting->editSettingValue('config', 'config_email', $config_email, $store_id);
		$this->model_admin_setting->editSettingValue('config', 'config_address', $config_address, $store_id);
		$this->model_admin_setting->editSettingValue('config', 'config_order_phone', $order_phone, $store_id);

		$this->model_admin_fasc->setSettingValue($store_id, 'order_cancel_time', $order_cancel_time);
		$this->model_admin_fasc->setSettingValue($store_id, 'not_phone', $not_phone);
		if($paymentInfo){
			$this->model_admin_fasc->setSettingValue($store_id, 'razor_key', $razor_key);
			if(strlen($razor_secret) > 0){
				$this->model_admin_fasc->setSettingValue($store_id, 'razor_secret', $razor_secret);
			}
		}

		$this->respond_json('');

	}

	private function getDataArray($name, $url, $city_id, $region_id){
		return array(
			  "config_url" => $url,
			  "config_ssl" => $url,
			  "config_meta_title" => $name,
			  "config_meta_description" => "",
			  "config_meta_keyword" => "",
			  "config_name" => $name,
			  "config_address" => "",
			  "config_email" => "",
			  "config_telephone" => "",
			  "config_order_phone" => "",
			  "config_secure" => '1',
			  "city_id" => $city_id,
			  "region_id" => $region_id
		);
	}

}