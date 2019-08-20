<?php
class ModelAdminFasc extends Model {

	public function getImage($name, $ls = 0){

		$query = $this->db->query("SELECT base64 FROM fasc_imgs WHERE `name` = '".$this->db->escape($name)."' AND date_modified > '".(int)$ls."' LIMIT 1");

		if($query->num_rows){
			return $query->row['base64'];
		}else{
			return '';
		}

	}

	public function isNewer($name, $lastTime){

		$query = $this->db->query("SELECT date_modified FROM fasc_imgs WHERE `name` = '".$this->db->escape($name)."' LIMIT 1");

		if($query->num_rows){
			return intval($query->row['date_modified']) > $lastTime;
		}else{
			return false;
		}

	}

	public function getSettingValue($store_id, $name){
		$query = $this->db->query("SELECT `value` FROM fasc_setting WHERE store_id = '".(int)$store_id."' AND name = '".$this->db->escape($name)."' LIMIT 1");
		if($query->num_rows){
			return $query->row['value'];
		}else{
			return '';
		}
	}

	public function setSettingValue($store_id, $name, $value){
		$query = $this->db->query("SELECT `setting_id` FROM fasc_setting WHERE store_id = '".(int)$store_id."' AND name = '".$this->db->escape($name)."' LIMIT 1");
		if($query->num_rows){
			$setting_id = $query->row['setting_id'];
			$this->db->query("UPDATE fasc_setting SET `value` = '".$this->db->escape($value)."', date_modified = NOW() WHERE setting_id = '".$setting_id."'");
		}else{
			$this->db->query("INSERT INTO fasc_setting SET `store_id` = '".(int)$store_id."', `name` = '".$this->db->escape($name)."', `value` = '".$this->db->escape($value)."', date_modified = NOW()");
		}
	}

}