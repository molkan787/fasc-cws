<?php
include_once 'dep.php';

class ControllerApiCities extends Controller{

	public function list(){

		$this->load->model('admin/cities');

		$cities = $this->model_admin_cities->getAllCities();


		$this->respond_json(array('items' => $cities));
	}

	public function delete(){

		checkAccessGroup(AG_MASTER_ADMIN);

		$this->load->model('admin/cities');

		$city_id = $this->getInput('city_id');

		$this->model_admin_cities->deleteCity($city_id);


		$this->respond_json(array('city_id' => $city_id));
	}

	public function edit(){

		checkAccessGroup(AG_MASTER_ADMIN);

		$this->load->model('admin/cities');

		$parent = $this->getInput('parent', 0);
		$city_id = $this->getInput('city_id');
		$name_1 = $this->getInput('name_1');
		$name_2 = $this->getInput('name_2');

		if($city_id == 'new'){
			$this->model_admin_cities->addCity($parent, $name_1, $name_2);
		}else{
			$this->model_admin_cities->editCity($city_id, $name_1, $name_2);
		}

		$this->respond_json(array('city_id' => $city_id));
	}

	public function add(){

		checkAccessGroup(AG_MASTER_ADMIN);

		$this->load->model('admin/cities');

		$parent = $this->getInput('parent');
		$name_1 = $this->getInput('name_1');
		$name_2 = $this->getInput('name_2');

		$this->model_admin_cities->addCity($parent, $name_1, $name_2);

		$this->respond_json(array('city_id' => $city_id));
	}

}