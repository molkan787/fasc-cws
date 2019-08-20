<?php
include_once 'dep.php';

class ControllerApiCommon extends Controller
{

	public function asd(){

		$this->load->model('admin/users');

		$user = $this->model_admin_users->loadCurrent();

		if(!$user){
			$this->respond_fail('NO_USER');
			return;
		}

		$data = array();
		$data['categories'] = $this->getCats();
		$data['user'] = $user;


		$this->respond_json($data);
	}

	private function getCats(){
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		$data = array();

		$cats = $this->model_catalog_category->getCategories(0, -1);

		$subcats = array();

		foreach ($cats as $cat) {
			$subs = $this->model_catalog_category->getCategories($cat['category_id'], -1);
			$subcats[$cat['category_id']] = $this->getCatsBasis($subs, true);
			foreach ($subs as $sub) {
				$childs = $this->model_catalog_category->getCategories($sub['category_id'], -1);
				$subcats[$sub['category_id']] = $this->getCatsBasis($childs, true);
			}
		}

		$data['cats'] = $this->getCatsBasis($cats);
		$data['subcats'] = &$subcats;
		return $data;
	}

	private function getCatsBasis($cats, $ignore_images = false){
		$result = array();
		foreach ($cats as $cat) {
			$_cat = array( 'id' => $cat['category_id'], 'text' => $cat['name'], 'gtype' => $cat['gtype'] );
			if(!$ignore_images){
				$_cat['image'] = url($this->model_tool_image->resize($cat['image'], 120, 120));
			}
			array_push($result, $_cat);
		}
		return $result;
	}
}