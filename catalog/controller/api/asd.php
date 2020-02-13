<?php
include_once 'dep.php';

class ControllerApiAsd extends Controller{

	public function index(){

		$this->load->model('admin/category');
		$this->load->model('admin/banner');
		$this->load->model('admin/gls');
		$this->load->model('admin/setting');
		$this->load->model('account/address');
		$this->load->model('admin/store');
		$this->load->model('admin/fasc');

		$lastUpdate = (int)$this->getInput('ls');
		$new_logo = $this->model_admin_fasc->isNewer('logo', $lastUpdate);
		$new_ls_ad = $this->model_admin_fasc->isNewer('ls_ad', $lastUpdate);
		$new_update = $new_logo || $new_ls_ad ;

		$store_id = $this->config->get('config_store_id');

		$razor_av = $this->model_admin_fasc->getSettingValue($store_id, 'razor_key') != '';

		$setStoreId = ($this->getInput('ssi') == 'true');
		if($setStoreId && $this->customer->getId()){
			$store_id = (int)$_GET['store_id'];
			$this->config->set('config_store_id', $store_id);
			$this->load->model('admin/customer');
			$this->model_admin_customer->setCustomerStoreId($this->customer->getId(), $store_id);
		}

		$cps_categories = $this->model_admin_category->getCategoriesWithChilds(0);
		$categories = $this->model_admin_category->getCategoriesWithChilds();
		$banners = $this->model_admin_banner->getBannerImages(7, $this->config->get('config_store_id'), true);
		$gls = $this->model_admin_gls->get();

		if(isset($banners[1])) $banners = $banners[1];
		else $banners = array();

		$store_setting =  $this->model_admin_setting->getSetting('config', $store_id);

		$city_names = $this->model_admin_setting->getStoreCityNames($store_id);
		$addresses = $this->model_account_address->getAddressesBasic();

		
		$customer = null;
		if($this->customer->getId()){
			$customer = array(
				'id' => $this->customer->getId(),
				'firstname' => $this->customer->getFirstName(),
				'lastname' => $this->customer->getLastName(),
				'phone' => $this->customer->getTelephone(),
				'email' => $this->customer->getEmail()
			);
		}

		$bsd = $this->model_admin_store->getStoreBSD($store_id);

		$data = array(
			'session_id' => $this->session->getId(),
			'store_id' => $store_id,
			'city_names' => $city_names,
			'cats' => $categories,
			'cps_cats' => $cps_categories,
			'banners' => $banners,
			'gls' => $gls,
			'customer' => $customer,
			'addresses' => $addresses,
			'contact_info' => array(
				'phone' => $this->config->get('config_telephone'),
				'email' => $this->config->get('config_email'),
				'name' => $store_setting['name'],
				'address' => $this->config->get('config_address'),
				'order_phone' => $this->config->get('config_order_phone')
			),
			'bsd' => $bsd,
			'new_update' => $new_update,
			'razor_av' => $razor_av,
			'time' => time(),
		);

		$this->respond_json($data);
	}

	public function wel_txt(){
		$this->load->model('admin/gls');
		$gls = $this->model_admin_gls->get('welcome_text');
		$this->respond_json($gls);
	}

	public function sas(){
		$this->load->model('admin/store');
		$this->load->model('admin/cities');

		$cities = $this->model_admin_cities->getAllCities();
		$stores = $this->model_admin_store->getStores(true);

		$this->respond_json(array('stores' => $stores, 'cities' => $cities));
	}

	public function set_si(){

		$this->load->model('admin/store');

		$city_id = (int)$this->getInput('city_id');
		$region_id = (int)$this->getInput('region_id');

		$store = $this->model_admin_store->getStoreByCity($city_id, $region_id);
		if($store){
			$store_id = $store['store_id'];
			$this->setStoreId($store_id);
			$this->respond_json(array('store_id' => $store_id));
		}else{
			$this->respond_fail('no_store');
		}

	}

	public function getGLU(){

		$this->load->model('admin/fasc');

		$ls = (int)$this->getInput('ls', 0);

		$logo = $this->model_admin_fasc->getImage('logo', $ls);
		$ls_ad = $this->model_admin_fasc->getImage('ls_ad', $ls);

		$this->respond_json(array(
			'time' => time(),
			'logo' => $logo,
			'ls_ad' => $ls_ad
		));
	}

	public function liveUpdate(){
		$this->load->model('catalog/prt');
		$this->load->model('admin/store');

		$lastUpdate = (int)$this->getInput('time');
		$pids = $this->getInput('pids', '');
		$pids = explode(',', $pids);

		$store_id = $this->config->get('config_store_id');
		$bsd = $this->model_admin_store->getStoreBSD($store_id);
		$products = $this->model_catalog_prt->getBasicChanges($lastUpdate, $pids);

		$this->respond_json(array(
			'bsd' => $bsd,
			'products' => $products,
			'time' => time()
		));

	}

	private function setStoreId($store_id){
		setcookie('store_id', $store_id, time() + 60 * 60 * 24 * 360);
		$customer_id = $this->customer->getId();
		if($customer_id){
			$this->db->query("UPDATE  " . DB_PREFIX . "customer SET store_id = '".(int)$store_id."' WHERE customer_id = '".(int)$customer_id."'");
		}
	}

}