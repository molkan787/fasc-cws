<?php
include_once 'dep.php';

class ControllerApiCpl extends Controller{

	public function custom(){

		$this->load->model('account/wishlist');
		$this->load->model('catalog/prt');

		$store_id = $this->config->get('config_store_id');
		$ids = $this->getInput('ids');
		$ids = explode(',', $ids);

		$length = count($ids);
		if($length < 1){
			$this->respond_json(array('items' => array()));
			return;
		}
		// Escaping
		for($i = 0; $i < $length; $i++){
			$ids[$i] = (int)$ids[$i];
		}
		$ids = join(',', $ids);

		$list = $this->model_catalog_prt->getProducts(array('list' => $ids, 'store' => $store_id, 'moreDetails' => true));

		$this->respond_json(array('items' => $list));
	}

	public function favorite(){

		if(!$this->customer->isLogged()){
			$this->respond_fail('NO_CUSTOEMR');
			return;
		}

		$this->load->model('account/wishlist');
		$this->load->model('catalog/prt');

		$store_id = $this->config->get('config_store_id');

		$ids = $this->model_account_wishlist->getWishlistStr($store_id);

		if(strlen($ids) == 0){
			$this->respond_json(array('items' => array()));
			return;
		}

		$list = $this->model_catalog_prt->getProducts(array('list' => $ids, 'moreDetails' => true));

		$this->respond_json(array('items' => $list));
	}

	public function changeFavorite(){
		if(!$this->customer->isLogged()){
			$this->respond_fail('NO_CUSTOEMR');
			return;
		}
		$product_id = $this->getInput('pid');
		$op = $this->getInput('op', 'add');

		if(empty($product_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$this->load->model('account/wishlist');

		if($op == 'add'){
			$this->model_account_wishlist->addWishlist($product_id);
			$this->respond_json('added');
		}else{
			$this->model_account_wishlist->deleteWishlist($product_id);
			$this->respond_json('removed');
		}
	}

}