<?php
class ControllerModuleEcproductcarousel extends Controller {
	protected function index($setting) {
		static $module = 0;
		$this->language->load('module/ecproductcarousel');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$this->load->model('ecproductcarousel/product');
		$model = $this->model_ecproductcarousel_product;
		
		$setting['enable_carousel'] = isset($setting['enable_carousel'])?$setting['enable_carousel']:1; /*carousel | listing*/
		$this->data['lazy_load_image'] = isset($setting['lazy_load_image'])?$setting['lazy_load_image']:1;
		$this->data['enable_async'] = isset($setting['enable_async'])?$setting['enable_async']:0;

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
         	$this->data['base'] = $this->config->get('config_ssl');
	    } else {
	        $this->data['base'] = $this->config->get('config_url');
	    }
	    $this->data['scroll_effect'] = 'fade';
	    $this->data['carousel_type'] = isset($setting['carousel_type'])?$setting['carousel_type']:'default'; /*default or owl*/
	    $this->data['owl_theme'] = 'default'; /*isset($setting['owl_theme'])?$setting['owl_theme']:'green'; default or owl*/
	    //$this->data['carousel_type'] = "owl";
		if($this->data['enable_async']){

			if($this->data['carousel_type'] == "default") {
				$this->data['script'] =  $this->data['base'].'catalog/view/javascript/ecproductcarousel/ecproductcarousel.min.js';
			} elseif($this->data['carousel_type'] == "owl") {
				$this->data['script'] =  $this->data['base'].'catalog/view/javascript/ecproductcarousel/ecproductcarousel.owl.min.js';
			}
			
		}else{
			if($this->data['carousel_type'] == "default") {
				$this->document->addScript('catalog/view/javascript/ecproductcarousel/jquery.carouFredSel-6.2.1.js');
				/* optionally include helper plugins */
				$this->document->addScript('catalog/view/javascript/ecproductcarousel/helper-plugins/jquery.mousewheel.min.js');
				$this->document->addScript('catalog/view/javascript/ecproductcarousel/helper-plugins/jquery.touchSwipe.min.js');
				if($this->data['lazy_load_image']){
					$this->document->addScript('catalog/view/javascript/ecproductcarousel/jquery.lazy.min.js');
				}
			} elseif($this->data['carousel_type'] == "owl") {
				/* optionally include helper plugins */
				$this->document->addScript('catalog/view/javascript/ecproductcarousel/helper-plugins/jquery.mousewheel.min.js');
				$this->document->addScript('catalog/view/javascript/ecproductcarousel/helper-plugins/jquery.touchSwipe.min.js');
				$this->document->addScript('catalog/view/javascript/ecproductcarousel/owlcarousel/owl.carousel.min.js');
			}
		}

		if($this->data['carousel_type'] == "default") {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/stylesheet/ecproductcarousel.css')) {
				$this->document->addStyle('catalog/view/theme/'.$this->config->get('config_template').'/stylesheet/ecproductcarousel.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/ecproductcarousel.css');
			}
		} elseif($this->data['carousel_type'] == "owl") {
			$this->document->addStyle('catalog/view/javascript/ecproductcarousel/owlcarousel/assets/owl.carousel.css');
			$this->document->addStyle('catalog/view/javascript/ecproductcarousel/owlcarousel/assets/owl.transitions.css');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/stylesheet/owl.theme.'.$this->data['owl_theme'].'.css')) {
				$this->document->addStyle('catalog/view/theme/'.$this->config->get('config_template').'/stylesheet/owl.theme.'.$this->data['owl_theme'].'.css');
			} elseif(file_exists(DIR_APPLICATION . 'view/javascript/ecproductcarousel/owlcarousel/assets/owl.theme.'.$this->data['owl_theme'].'.css')) {
				$this->document->addStyle('catalog/view/javascript/ecproductcarousel/owlcarousel/assets/owl.theme.'.$this->data['owl_theme'].'.css');
			}

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/stylesheet/ecproductcarousel_owl.css')) {
				$this->document->addStyle('catalog/view/theme/'.$this->config->get('config_template').'/stylesheet/ecproductcarousel_owl.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/ecproductcarousel_owl.css');
			}
		}
		
		$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
		
		
		
	    $lang_id = $this->config->get('config_language_id');

	    $category_id = isset($setting['category_id'])?$setting['category_id']:0;
	    $order_status_id = isset($setting['order_status_id'])?$setting['order_status_id']:array(5);
	    $filter_sub_category = isset($setting['filter_sub_category'])?$setting['filter_sub_category']:0;
	    $limit = isset($setting['limit'])?$setting['limit']: 9;
	    $source_from = isset($setting['source_from'])?$setting['source_from']:'latest';

	    $data = array(
			'sort'  => 'p.date_added',
			'filter_category_id' => $category_id,
			'filter_sub_category' => $filter_sub_category,
			'order' => 'DESC',
			'start' => 0,
			'limit' => $limit
		);

	    $this->data['products'] = array();
	    $results = array();
	    switch ($source_from) {
	    	case 'featured':
	    		$tmp = isset($setting['featured_product'])?$setting['featured_product']:"";
	    		$products = explode(",", $tmp);
	    		if(!empty($products)){
					$products = array_slice($products, 0, (int)$limit);
					foreach ($products as $product_id) {
						$product_info = $this->model_catalog_product->getProduct($product_id);
						$results[$product_id] = $product_info;
					}
				}
	    		break;
	    	case 'latest':
	    		$results = $model->getLatestProducts( $data );
	    		break;
	    	case 'bestseller':
	    		$results = $model->getBestSellerProducts( $data );
	    		break;
	    	case 'special':
	    		$results = $model->getProductSpecials( $data );
	    		break;
	    	case 'mostviewed':
	    		$results = $model->getPopularProducts( $data );
	    		break;
	    	case 'related':
	    		$route = isset($this->request->get['route'])?$this->request->get['route']:"";
	    		if($route == "product/product"){
	    			$product_id = isset($this->request->get['product_id'])?$this->request->get['product_id']:"0";
	    			$results = $model->getProductRelated( $product_id, $limit );
	    		} elseif ( $route == "checkout/cart") {
	    			$product_id = array();
	    			if( $this->cart->getProducts() ) {
	    				foreach ($this->cart->getProducts() as $product) {
	    					$product_id[] = $product['product_id'];
	    				}
	    				$results = $model->getProductRelated( $product_id, $limit );
	    			}

	    		}
	    		break;
	    	case 'alsobought':/*Who bought this also bought*/
	    		$route = isset($this->request->get['route'])?$this->request->get['route']:"";
	    		if($route == "product/product"){
	    			$product_id = isset($this->request->get['product_id'])?$this->request->get['product_id']:"0";
	    			$data = array("filter_category_id" => $category_id,
	    						  "order_status_id" => $order_status_id,	
	    						  "product_id" => $product_id,
	    						  "limit" => $limit);
	    			$results = $model->getAlsoboughtProducts( $data );
	    		} elseif ( $route == "checkout/cart") {
	    			$product_id = array();
	    			if( $this->cart->getProducts() ) {
	    				foreach ($this->cart->getProducts() as $product) {
	    					$product_id[] = $product['product_id'];
	    				}
	    			
	    				$data = array("filter_category_id" => $category_id,
	    						  "order_status_id" => $order_status_id,	
	    						  "product_id" => $product_id,
	    						  "limit" => $limit);
	    				$results = $model->getAlsoboughtProducts( $data );
	    			}

	    		}
	    		break;
	    	case 'alsoviewed':/*Who bought this also bought*/
	    		$route = isset($this->request->get['route'])?$this->request->get['route']:"";
	    		if($route == "product/product"){
	    			$product_id = isset($this->request->get['product_id'])?$this->request->get['product_id']:"0";
	    			$data = array("filter_category_id" => $category_id,
	    						  "order_status_id" => $order_status_id,	
	    						  "product_id" => $product_id,
	    						  "limit" => $limit);
	    			$results = $model->getAlsoviewedProducts( $data );
	    		} elseif ( $route == "checkout/cart") {
	    			$product_id = array();
	    			if( $this->cart->getProducts() ) {
	    				foreach ($this->cart->getProducts() as $product) {
	    					$product_id[] = $product['product_id'];
	    				}
	    			
	    				$data = array("filter_category_id" => $category_id,
	    						  "order_status_id" => $order_status_id,	
	    						  "product_id" => $product_id,
	    						  "limit" => $limit);
	    				$results = $model->getAlsoviewedProducts( $data );
	    			}

	    		}
	    		
	    		break;
	    	case 'random':
	    		$results = $model->getRandomProducts( $data );
	    		break;
	    }

	    $this->data['products'] = $this->getProducts($results, $setting);

	    $this->setCategoryLink( $category_id );

	    $this->data['module_title'] = isset($setting['title'][$lang_id])?$setting["title"][$lang_id]:"";
	    $this->data['module_description'] = isset($setting['description'][$lang_id])?$setting["description"][$lang_id]:"";
	    $this->data['module_description'] = html_entity_decode($this->data['module_description'], ENT_QUOTES, 'UTF-8');
	    $this->data['module_description'] = str_replace(array("&lt;","&gt;"),array("<",">"),$this->data['module_description']);

	    $this->data['carousel_item_width'] = isset($setting['carousel_item_width'])?$setting['carousel_item_width']:400;
	    $this->data['carousel_item_height'] = isset($setting['carousel_item_height'])?$setting['carousel_item_height']:400;
	    $this->data['carousel_mousewhell'] = isset($setting['carousel_mousewhell'])?$setting['carousel_mousewhell']:1;
	    $this->data['carousel_responsive'] = isset($setting['carousel_responsive'])?$setting['carousel_responsive']:1;
	    $this->data['carousel_auto'] = isset($setting['carousel_auto'])?$setting['carousel_auto']:0;
	    $this->data['itemsperpage'] = isset($setting['itemsperpage'])?$setting['itemsperpage']:6;
	    $this->data['cols'] = isset($setting['cols'])?$setting['cols']:3;
	    $this->data['duration'] = isset($setting['duration'])?$setting['duration']:1000;
	    $this->data['scroll_effect'] = isset($setting['scroll_effect'])?$setting['scroll_effect']:'scroll';
	    $this->data['carousel_width'] = isset($setting['carousel_width'])?trim($setting['carousel_width']):0;
	    $this->data['carousel_height'] = isset($setting['carousel_height'])?trim($setting['carousel_height']):0;

	    $this->data['default_items'] = isset($setting['default_items'])?(int)$setting['default_items']:4;
	    $this->data['mobile_items'] = isset($setting['mobile_items'])?$setting['mobile_items']:1;
	    $this->data['tablet_items'] = isset($setting['tablet_items'])?$setting['tablet_items']:2;
	    $this->data['portrait_items'] = isset($setting['portrait_items'])?$setting['portrait_items']:3;
	    $this->data['large_items'] = isset($setting['large_items'])?$setting['large_items']:5;
	    $this->data['loop'] = isset($setting['loop'])?$setting['loop']:1;
	    $this->data['margin_item'] = isset($setting['margin_item'])?$setting['margin_item']:0;
	    $this->data['show_nav'] = isset($setting['show_nav'])?$setting['show_nav']:1;
	    $this->data['rtl'] = isset($setting['rtl'])?$setting['rtl']:0;
	    $this->data['mouse_drag'] = isset($setting['mouse_drag'])?$setting['mouse_drag']:1;
	    $this->data['touch_drag'] = isset($setting['touch_drag'])?$setting['touch_drag']:1;
	    $this->data['slide_by'] = isset($setting['slide_by'])?$setting['slide_by']:1;
	    
	    
	    $this->data['show_addtocart'] = isset($setting['show_addtocart'])?$setting['show_addtocart']:1;
	    $this->data['show_product_name'] = isset($setting['show_product_name'])?$setting['show_product_name']:1;
	    $this->data['show_product_description'] = isset($setting['show_product_description'])?$setting['show_product_description']:1;
	    $this->data['show_price'] = isset($setting['show_price'])?$setting['show_price']:1;
	    $this->data['show_quickview'] = isset($setting['show_quickview'])?$setting['show_quickview']:1;
	    $this->data['show_sale_label'] = isset($setting['show_sale_label'])?$setting['show_sale_label']:1;
	    $this->data['show_category_name'] = isset($setting['show_category_name'])?$setting['show_category_name']:1;
	    $this->data['show_wishlist'] = isset($setting['show_wishlist'])?$setting['show_wishlist']:1;
	    $this->data['show_compare'] = isset($setting['show_compare'])?$setting['show_compare']:1;
	    $this->data['show_discount'] = isset($setting['show_discount'])?$setting['show_discount']:1;

	    $this->data['text_sale'] = $this->language->get("text_sale");
	    $this->data['text_quickview'] = $this->language->get("text_quickview");
	    $this->data['text_view_product'] = $this->language->get("text_view_product");
	    $this->data['button_wishlist'] = $this->language->get("button_wishlist");
	    $this->data['button_compare'] = $this->language->get("button_compare");
	    $this->data['button_cart'] = $this->language->get("button_cart");
	    $this->data['text_bought'] = $this->language->get("text_bought");

	   
	    $this->data['limit_chars'] = isset($setting['limit_chars'])?$setting['limit_chars']:58;

		$this->data['module'] = $module++;

		if($this->data['carousel_type'] == "default") {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/ecproductcarousel.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/module/ecproductcarousel.tpl';
			} else {
				$this->template = 'default/template/module/ecproductcarousel.tpl';
			}
		} elseif($this->data['carousel_type'] == "owl") {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/ecproductcarousel_owl.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/module/ecproductcarousel_owl.tpl';
			} else {
				$this->template = 'default/template/module/ecproductcarousel_owl.tpl';
			}
		}
		return $this->render();
	}
	/*
	(String) $arg = param1=value1|param2=value2|param3=value3
	*/
	public function carousel($args = null){
		$custom_settings = array();
		$tmp_settings = "";

		if(is_array($args)){
			$tmp_settings = isset($args[0])?$args[0]:"";
		}else{
			$tmp_settings = $args;
		}
		$tmp_array = explode("|", $tmp_settings);
		if($tmp_array){
			foreach($tmp_array as $val) {
				$val = trim($val);
				$tmp_array2 = explode("=", $val);
				if($tmp_array2 && count($tmp_array2) == 2) {
					$tmp_array2[0] = trim($tmp_array2[0]);
					$tmp_array2[1] = trim($tmp_array2[1]);
					$custom_settings[$tmp_array2[0]] = $tmp_array2[1];
				}
			}
		}

		$custom_settings['single_mode'] = true;
		
		return $this->index( $custom_settings );
	}
	private function getProducts( $results, $setting ){
		$setting['image_width'] = isset($setting['image_width'])?$setting['image_width']:200;
		$setting['image_height'] = isset($setting['image_height'])?$setting['image_height']:200;
		$setting['limit_chars'] = isset($setting['limit_chars'])?$setting['limit_chars']:58;
		$setting['strip_tags'] = isset($setting['strip_tags'])?$setting['strip_tags']:1;
		$setting['view_number_bought'] = isset($setting['view_number_bought'])?$setting['view_number_bought']:1;
		$setting['order_status_id'] = isset($setting['order_status_id'])?$setting['order_status_id']:5;

		$this->data['image_width'] = $setting['image_width'];
		$this->data['image_height'] = $setting['image_height'];
		$this->data['view_number_bought'] = $setting['view_number_bought'];
		

		$products = array();
		foreach ($results as $result) {
			$number_bought = 0;
			if($setting['view_number_bought']){
				$number_bought = $this->model_ecproductcarousel_product->getTotalBought( $result['product_id'], $setting['order_status_id'] );
			}
			$save_price = (float)$result['price'] - (float)$result['special'];
			$discount = 0;
			if((float)$result['price'] > 0) {
				$discount = round(($save_price/$result['price'])*100);
			}
			$save_price = $this->currency->format($this->tax->calculate($save_price, $result['tax_class_id'], $this->config->get('config_tax')));
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = false;
			}
						
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}
					
			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}
			
			if ($this->config->get('config_review_status')) {
				$rating = $result['rating'];
			} else {
				$rating = false;
			}

			$description = (html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'));
			if($setting['strip_tags'] ){
				$description = strip_tags($description);
			}
			$description = (strlen($description) <= $setting['limit_chars'])?$description:utf8_substr( $description,0, $setting['limit_chars'])."...";

			$products[] = array(
				'product_id' 	=> $result['product_id'],
				'thumb'   	 	=> $image,
				'name'    	 	=> $result['name'],
				'price'   	 	=> $price,
				'special' 	 	=> $special,
				'discount' 	 	=> $discount,
				'rating'     	=> $rating,
				'bought' 		=> $number_bought,
				'description'	=> $description,
				'reviews'    	=> sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'    	 	=> $this->url->link('product/product', 'product_id=' . $result['product_id']),
			);
		}
		return $products;
	}
	private function setCategoryLink($category_id = 0) {
		$this->data['category_link'] = "";
		$this->data['category_title'] = "";
		if($category_id){
			$query = $this->db->query("SELECT c.parent_id,cd.name FROM ".DB_PREFIX."category c LEFT JOIN ".DB_PREFIX."category_description cd ON (c.category_id = cd.category_id)  WHERE c.category_id=".(int)$category_id." AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			$parent_id = 0;

			if($query->num_rows > 0){
				$parent_id = $query->row['parent_id'];
				$this->data['category_title'] = html_entity_decode($query->row['name'], ENT_QUOTES, 'UTF-8');
				$this->data['category_title'] = strip_tags($this->data['category_title']);
			}
			if($parent_id){
				$this->data['category_link'] = $this->url->link('product/category', 'path=' . $parent_id."_".$category_id);
			}else{
				$this->data['category_link'] = $this->url->link('product/category', 'path=' . $category_id);
			}
			
		}
		return $this->data['category_link'];
	}
	
}
?>
