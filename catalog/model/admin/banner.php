<?php
class ModelAdminBanner extends Model {
	public function addBanner($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "'");

		$banner_id = $this->db->getLastId();

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $language_id => $value) {
				foreach ($value as $banner_image) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($banner_image['title']) . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" .  (int)$banner_image['sort_order'] . "'");
				}
			}
		}

		return $banner_id;
	}

	public function editBanner($banner_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "' WHERE banner_id = '" . (int)$banner_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $language_id => $value) {
				foreach ($value as $banner_image) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($banner_image['title']) . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");
				}
			}
		}
	}

	public function deleteBanner($banner_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
	}

	public function getBanner($banner_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");

		return $query->row;
	}

	public function getBanners($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "banner b";
		$sql .= " LEFT JOIN " . DB_PREFIX . "banner_image bi ON b.banner_id = bi.banner_id";
		$sql .= " WHERE b.store_id = '" . (int)$data['store'] . "'";


		$sort_data = array(
			'name',
			'status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getBannerImages($banner_id, $store, $high_quality = false) {
		$banner_image_data = array();

		$banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "' AND store_id = '" . (int)$store . "' ORDER BY sort_order ASC");

		$img_w = $high_quality ? 500 : 250;
		$img_h = 0;//$high_quality ? 200 : 100;
		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_data[$banner_image['language_id']][] = array(
				'id'      => $banner_image['banner_image_id'],
				'title'      => $banner_image['title'],
				'link'       => $banner_image['link'],
				'image'      =>  url($this->model_tool_image->resize($banner_image['image'], $img_w, $img_h)),
				'sort_order' => $banner_image['sort_order']
			);
		}

		return $banner_image_data;
	}

	public function addImage($banner_id, $store, $image, $sort_order, $link = ''){
		$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', language_id = '1', title = '', link = '".$this->db->escape($link)."', image = '" .  $this->db->escape($image) . "', sort_order = '" . $sort_order . "', store_id = '" . $store . "'");
		return $this->db->getLastId();
	}

	public function editImage($image_id, $image, $link){
		if(empty($image)){
			$this->db->query("UPDATE " . DB_PREFIX . "banner_image SET link = '".$this->db->escape($link)."' WHERE banner_image_id = '".(int)$image_id."' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		}else{
			$this->db->query("UPDATE " . DB_PREFIX . "banner_image SET link = '".$this->db->escape($link)."', image = '" .  $this->db->escape($image) . "' WHERE banner_image_id = '".(int)$image_id."' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		}
		
	}

	public function getImage($image_id){
		$query = $this->db->query("SELECT image, link FROM " . DB_PREFIX . "banner_image WHERE banner_image_id = '".(int)$image_id."'");
		if($query->num_rows){
			return $query->row;
		}else{
			return null;
		}
	}

	public function setImageSortOrder($image_id, $sort_order){
		$this->db->query("UPDATE " . DB_PREFIX . "banner_image SET sort_order = '" . (int)$sort_order . "' WHERE banner_image_id = '" . $image_id . "'");
	}

	public function deleteImages($banner_id, $store_id, $images_to_keep){
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "' AND store_id = '" . (int)$store_id . "'" . ($images_to_keep != '' ? " AND banner_image_id NOT IN (" . $images_to_keep . ")" : ""));
	}

	public function getTotalBanners() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");

		return $query->row['total'];
	}
}
