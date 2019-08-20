<?php
class ModelAdminPromos extends Model {


	public function add($store_id, $data){
		$sql = "INSERT INTO fasc_promos SET store_id = '".(int)$store_id."', image = '".$this->db->escape($data['image'])."', ";
		$sql .= "link_type = '".(int)$data['link_type']."', link = '".$this->db->escape($data['link'])."', `format` = '".(int)$data['format']."', date_added = NOW(), date_modified = NOW()";
		$this->db->query($sql);
		$promo_id = $this->db->getLastId();
		$this->db->query("UPDATE fasc_promos SET sort_order = '".$promo_id."' WHERE promo_id = '".$promo_id."'");

		return $promo_id;
	}

	public function edit($promo_id, $data){
		$sql = "UPDATE fasc_promos SET ";
		if(!empty($data['image'])) $sql .= "image = '".$this->db->escape($data['image'])."', ";
		$sql .= "link_type = '".(int)$data['link_type']."', link = '".$this->db->escape($data['link'])."', `format` = '".(int)$data['format']."', date_modified = NOW() WHERE promo_id = '".(int)$promo_id."'";
		$this->db->query($sql);
	}

	public function delete($promo_id){
		$this->db->query("DELETE FROM fasc_promos WHERE promo_id = '".(int)$promo_id."'");
	}

	public function getPromos($store_id){
		$sql = "SELECT promo_id, image, link_type, link, format, sort_order FROM fasc_promos WHERE store_id = '".(int)$store_id."' ORDER BY sort_order ASC";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getPromo($promo_id){
		$sql = "SELECT promo_id, image, link_type, link, format, store_id FROM fasc_promos WHERE promo_id = '".(int)$promo_id."'";
		$query = $this->db->query($sql);
		if($query->num_rows){
			return $query->row;
		}else{
			return null;
		}
	}

	public function editOrder($ids, $store_id){
		$sort_i = 0;
		foreach ($ids as $id) {
			$this->db->query("UPDATE fasc_promos SET sort_order = '".$sort_i."' WHERE promo_id = '".(int)$id."' AND store_id = '".(int)$store_id."'");
			$sort_i++;
		}
	}

}