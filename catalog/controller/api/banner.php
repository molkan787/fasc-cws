<?php
include_once 'dep.php';

class ControllerApiBanner extends Controller
{
	public function list(){

		$this->load->model('admin/banner');
		$this->load->model('tool/image');

		$banners = $this->model_admin_banner->getBannerImages(7, $this->config->get('config_store_id'));

		if(isset($banners[1])) $banners = $banners[1];
		else $banners = array();

		$this->respond_json(array('items' => $banners));

	}

	public function save(){

		checkAccess(AG_ADMIN);

		$this->load->model('admin/banner');

		$items = $this->getInput('items');
		/*if(empty($items)){
			$this->respond_fail('argument_missing');
			return;
		}*/

		$store = $this->config->get('config_store_id');

		$items = explode(',', $items);

		$ids = '';

		$sort_ptr = 0;

		foreach ($items as $item) {
			$sort_ptr++;
			$id_to_add;
			if(is_numeric($item)){
				$id_to_add = $item;
				$this->model_admin_banner->setImageSortOrder($item, $sort_ptr);
			}else{
				$id_to_add = $this->model_admin_banner->addImage(7, $store, $item, $sort_ptr);
			}
			if($ids != '') $ids .= ',';
			$ids .= $id_to_add;
		}

		$this->model_admin_banner->deleteImages(7, $store, $ids);
		$this->list();

	}

	public function save_ob(){
		$this->load->model('admin/banner');
		$store = $this->config->get('config_store_id');

		$image_id = $this->getInput('banner_id');
		$image = $this->getInput('image');
		$link = $this->getInput('link');

		if($image_id == 'new'){
			$image_id = $this->model_admin_banner->addImage(7, $store, $image, 0, $link);
		}else{
			$this->model_admin_banner->editImage($image_id, $image, $link);
		}

		$this->respond_json(array('banner_id' => $image_id));
	}

	public function info(){
		$this->load->model('admin/banner');
		$this->load->model('tool/image');
		$store = $this->config->get('config_store_id');
		$image_id = $this->getInput('banner_id');

		$data = $this->model_admin_banner->getImage($image_id);
		$data['image'] = url($this->model_tool_image->resize($data['image'], 250, 100));
		$this->respond_json($data);
	}

}