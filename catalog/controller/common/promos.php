<?php
class ControllerCommonPromos extends Controller {
	public function index() {
        $this->load->model('admin/promos');
        $this->load->model('tool/image');

		$store_id = $this->config->get('config_store_id');

		$items = $this->model_admin_promos->getPromos($store_id);
		
		$rows = array();
		$row = array();
		$row_size = 0;

		foreach ($items as &$item) {
			$p_f = (int)$item['format'];
			$i_h = 200;
			$i_w = ($p_f == 1) ? 500 : ($p_f == 2 ? 230 : 150);
			$item['image'] = $this->model_tool_image->resize($item['image'], $i_w, $i_h);
			$item['link'] = trim($item['link']) ? 'href="index.php?route=product/list&ids='. $item['link'] . '"' : false;

			$row[] = $item;
			$row_size += $this->formatToSize($p_f);
			if($row_size >= 8){
				$rows[] = $row;
				$row = array();
				$row_size = 0;
			}
		}
		$rows[] = $row;
        
        $data = array();
        // $data['items'] = $items;
        $data['rows'] = $rows;

		return $this->load->view('common/promos', $data);
	}

	private function formatToSize($f){
		if($f == 1) return 4;
		else if($f == 2) return 2;
		else if($f == 3) return 1.333;
	}
}
