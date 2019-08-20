<?php
class ModelAdminGls extends Model {

	public function get($name = ''){

		$sql = "SELECT * FROM fasc_gls";
		if(!empty($name)){
			$sql .= " WHERE `name` = '".$this->db->escape($name)."'";
		}

		$query = $this->db->query($sql);

		$rows = $query->rows;

		$gls = array();
		foreach ($rows as $row) {
			$name = $row['name'];
			$lang = $row['lang_id'];
			if(!isset($gls[$name]))
				$gls[$name] = array();
			$gls[$name][$lang] = $row['content'];
		}

		return $gls;
	}

	public function set($data){

		foreach ($data as $name => $content) {
			foreach ($content as $lang => $value) {
				$this->db->query("UPDATE fasc_gls SET content = '".$this->db->escape($value)."' WHERE `name` = '".$this->db->escape($name)."' AND lang_id = '".(int)$lang."'");
			}
		}

	}

}