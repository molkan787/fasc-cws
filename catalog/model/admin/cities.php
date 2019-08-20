<?php
class ModelAdminCities extends Model {

	public function getAllCities(){
		$cities = $this->getCities(0);

		foreach ($cities as &$city) {
			$city['childs'] = $this->getCities($city['city_id']);
		}
		return $cities;
	}

	public function getCities($parent = 0){
		$sql = "SELECT * FROM fasc_cities WHERE `parent` = '".(int)$parent."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function addCity($parent, $name_1, $name_2){
		$sql = "INSERT INTO fasc_cities SET parent = '".(int)$parent."', name_1 = '".$this->db->escape($name_1)."'";
		$sql .= ", name_2 = '".$this->db->escape($name_2)."'";
		$this->db->query($sql);
		return $this->db->getLastId();
	}

	public function deleteCity($city_id){
		$sql = "DELETE FROM fasc_cities WHERE city_id = '".(int)$city_id."' OR  parent = '".(int)$city_id."'";
		$this->db->query($sql);
	}

	public function editCity($city_id, $name_1, $name_2){
		$sql = "UPDATE fasc_cities SET name_1 = '".$this->db->escape($name_1)."'";
		$sql .= ", name_2 = '".$this->db->escape($name_2)."'";
		$sql .= " WHERE city_id = '".(int)$city_id."'";
		$this->db->query($sql);
	}

}