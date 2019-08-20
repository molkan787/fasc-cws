<?php
class ControllerModuleEcproductcarousel extends Controller {
	private $error = array();

	public function index() {

		$this->language->load('module/ecproductcarousel');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model("catalog/category");
		$this->load->model('catalog/product');
		$this->load->model('localisation/order_status');
		$this->load->model('ecproductcarousel/product');

		$this->model_ecproductcarousel_product->checkInstall();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$action = isset($this->request->post["action"])?$this->request->post["action"]:"";

			if(!empty($this->request->post['ecproductcarousel_module'])){
				foreach($this->request->post['ecproductcarousel_module'] as $key=>$module){
					$custom_position = isset($module['custom_position'])?$module["custom_position"]:"";
					if(!empty($custom_position)){
						$this->request->post['ecproductcarousel_module'][$key]['position'] = $custom_position;	
					}
					
				}
			}
			
			$this->model_setting_setting->editSetting('ecproductcarousel', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			if($action == "save_stay"){
				$this->redirect($this->url->link('module/ecproductcarousel', 'token=' . $this->session->data['token'], 'SSL'));
			}else{
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
			
		}
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_header_top'] = $this->language->get('text_header_top');
		$this->data['text_header_bottom'] = $this->language->get('text_header_bottom');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
        $this->data['text_footer_top'] = $this->language->get('text_footer_top');
		$this->data['text_footer_bottom'] = $this->language->get('text_footer_bottom');
        $this->data['text_alllayout'] = $this->language->get('text_alllayout');
		$this->data['text_default'] = $this->language->get('text_default');
		
		$this->data['entry_coupon'] = $this->language->get('entry_coupon');
		$this->data['entry_content'] = $this->language->get('entry_content');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_custom_position'] = $this->language->get('entry_custom_position');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_save_stay'] = $this->language->get('button_save_stay');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_new_block'] = $this->language->get('button_add_new_block');
		$this->data['text_alllayout'] = $this->language->get('text_all_layout');


		
		$this->data['tab_block'] = $this->language->get('tab_block');
		$this->data['token'] = $this->session->data["token"];
		$this->data['positions'] = array( 
										  'content_top',
										  'content_bottom',
										  'column_left',
										  'column_right'
		);
		$this->data['effects'] = array( 
										  'none',
										  'scroll',
										  'directscroll',
										  'fade',
										  'crossfade',
										  'cover',
										  'cover-fade',
										  'uncover',
										  'uncover-fade'
		);

		$this->data['modes']  = array('default' => $this->language->get('Default'), 
									 'owl' => 'Owl Carousel');

		$this->load->model('localisation/language'); 
   		$languages = $this->model_localisation_language->getLanguages();
		$this->data['languages'] = $languages;

		$this->load->model('sale/coupon');
		$data = array();
		$data["limit"] = 9999;
		$data["start"] = 0;
   		$coupons = $this->model_sale_coupon->getCoupons($data);
		$this->data['coupons'] = $coupons;

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['dimension'])) {
			$this->data['error_dimension'] = $this->error['dimension'];
		} else {
			$this->data['error_dimension'] = array();
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/ecproductcarousel', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('module/ecproductcarousel', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['create_coupon'] = $this->url->link('sale/coupon/insert', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['modules'] = array();

		if (isset($this->request->post['ecproductcarousel_module'])) {
			$this->data['modules'] = $this->request->post['ecproductcarousel_module'];
		} elseif ($this->config->get('ecproductcarousel_module')) {
			$this->data['modules'] = $this->config->get('ecproductcarousel_module');
		}
		$this->data["general"] = array();
		if (isset($this->request->post['ecproductcarousel_general'])) {
			$this->data['general'] = $this->request->post['ecproductcarousel_general'];
		} elseif ($this->config->get('ecproductcarousel_general')) {
			$this->data['general'] = $this->config->get('ecproductcarousel_general');
		}
    	
		if($this->data['modules']){
			foreach($this->data['modules'] as $key=>$module){
				if(isset($module['featured_product'])){
					$products = explode(',', $module['featured_product']);

					$module['products'] = array();
					
					foreach ($products as $product_id) {
						$product_info = $this->model_catalog_product->getProduct($product_id);
						
						if ($product_info) {
							$module['products'][] = array(
								'product_id' => $product_info['product_id'],
								'name'       => $product_info['name']
							);
						}
					}
					$this->data['modules'][$key] = $module;
				}
				
			}
		}
		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();

		$this->data['source_from'] = array("latest" => $this->language->get("text_latest"),
										  "bestseller" => $this->language->get("text_bestseller"),
										  "special" => $this->language->get("text_special"),
										  "mostviewed" => $this->language->get("text_mostviewed"),
										  "featured" => $this->language->get("text_featured"),
										  "related" => $this->language->get("text_related"),
										  "alsobought" => $this->language->get("text_alsobought"),
										  "alsoviewed" => $this->language->get("text_alsoviewed"),
										  "random" => $this->language->get("text_random") );

		$this->data['categories'] = array();
    	
    	$this->data['categories'] = $this->model_catalog_category->getCategories(array());
   		$this->data['yesno'] = array('1'=>$this->language->get("text_yes"),
   									 "0"=>$this->language->get("text_no"));
   		$this->data['order_status'] = $this->model_localisation_order_status->getOrderStatuses();
   		
		$this->template = 'module/ecproductcarousel.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/ecproductcarousel')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['ecproductcarousel_module'])) {
			/**
			foreach ($this->request->post['ecproductcarousel_module'] as $key => $value) {
				
			}
			*/
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>
