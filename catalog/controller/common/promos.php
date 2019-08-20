<?php
class ControllerCommonPromos extends Controller {
	public function index() {
        $this->load->model('admin/promos');
        $this->load->model('tool/image');

		$store_id = $this->config->get('config_store_id');

		$items = $this->model_admin_promos->getPromos($store_id);

		foreach ($items as &$item) {
			$p_f = (int)$item['format'];
			$i_h = 200;
			$i_w = ($p_f == 1) ? 500 : ($p_f == 2 ? 230 : 150);
			$item['image'] = $this->model_tool_image->resize($item['image'], $i_w, $i_h);
		}
        
        $data = array();
        $data['items'] = $items;

		return $this->load->view('common/promos', $data);
	}
}
