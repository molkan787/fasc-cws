<?php
include_once 'dep.php';

class ControllerApiPromos extends Controller{

	public function list(){

		$this->load->model('admin/promos');
		$this->load->model('tool/image');

		$store_id = $this->config->get('config_store_id');

		$items = $this->model_admin_promos->getPromos($store_id);

		foreach ($items as &$item) {
			$p_f = (int)$item['format'];
			$i_h = 200;
			$i_w = ($p_f == 1) ? 500 : ($p_f == 2 ? 230 : 150);
			$item['image'] = url($this->model_tool_image->resize($item['image'], $i_w, $i_h));
		}

		$this->respond_json(array('items' => $items));
	}

	public function add(){

		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/promos');
		$this->load->model('tool/image');
		$store_id = $this->config->get('config_store_id');

		$data = array();
		$data['image'] = $this->getInput('image');
		$data['link_type'] = $this->getInput('link_type', 1);
		$data['link'] = $this->getInput('link');
		$data['format'] = $this->getInput('format');

		$promo_id = $this->model_admin_promos->add($store_id, $data);
		
		$p_f = (int)$data['format'];
		$i_h = 200;
		$i_w = ($p_f == 1) ? 500 : ($p_f == 2 ? 230 : 150);

		$this->respond_json(array(
			'promo_id' => $promo_id,
			'image' => (empty($data['image']) ? '' : url($this->model_tool_image->resize($data['image'], $i_w, $i_h)))
		));
	}

	public function edit(){

		checkAccessGroup(AG_ADMIN);


		$this->load->model('admin/promos');
		$this->load->model('tool/image');
		$store_id = (int)$this->config->get('config_store_id');

		$promo_id = $this->getInput('promo_id');

		$promo = $this->model_admin_promos->getPromo($promo_id);

		if((int)$promo['store_id'] != $store_id){
			$this->respond_fail('not_found');
			return;
		}

		$data = array();
		$data['image'] = $this->getInput('image');
		$data['link_type'] = $this->getInput('link_type', 1);
		$data['link'] = $this->getInput('link');
		$data['format'] = $this->getInput('format');

		$this->model_admin_promos->edit($promo_id, $data);

		$p_f = (int)$data['format'];
		$i_h = 200;
		$i_w = ($p_f == 1) ? 500 : ($p_f == 2 ? 230 : 150);

		$this->respond_json(array(
			'promo_id' => $promo_id,
			'image' => (empty($data['image']) ? '' : url($this->model_tool_image->resize($data['image'], $i_w, $i_h)))
		));
	}

	public function editOrder(){
		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/promos');
		$store_id = (int)$this->config->get('config_store_id');

		$ids = $this->getInput('ids');
		$ids = explode(',', $ids);

		$this->model_admin_promos->editOrder($ids, $store_id);
		$this->respond_json('');
	}

	public function info(){
		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/promos');
		$store_id = (int)$this->config->get('config_store_id');

		$promo_id = $this->getInput('promo_id');

		$promo = $this->model_admin_promos->getPromo($promo_id);

		if((int)$promo['store_id'] == $store_id || true){
			$this->respond_json($promo);
		}else{
			$this->respond_fail('not_found');
		}

	}

	public function delete(){
		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/promos');
		$store_id = (int)$this->config->get('config_store_id');

		$promo_id = $this->getInput('promo_id');

		$promo = $this->model_admin_promos->getPromo($promo_id);

		if((int)$promo['store_id'] == $store_id){
			$this->model_admin_promos->delete($promo_id);
			$this->respond_json('');
		}else{
			$this->respond_fail('not_found');
		}

	}

}