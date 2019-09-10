<?php
include_once 'dep.php';

class ControllerApiProduct extends Controller
{

    public function delete(){
        checkAccess(AG_ADMIN);

        $multiple = ($this->getInput('multiple') == 'true');
        $product_id = $this->getInput('product_id', '');

        if(empty($product_id)){
            $this->respond_fail('argument_missing');
            return;
        }

        $this->load->model('admin/product');

        $user = $this->model_admin_users->loadCurrent();
		$checkStoreId = (intval($user['user_type']) > 10);

        if($multiple){
            $pids = explode(',', $product_id);
            foreach ($pids as $pid) {
                $this->model_admin_product->deleteProduct($pid, $checkStoreId);
            }
        }else{
            $this->model_admin_product->deleteProduct($product_id, $checkStoreId);
        }

        $this->respond_json('');
    }

    public function status(){
        $product_id = $this->getInput('product_id', '');
        $operation = $this->getInput('operation');

        $this->load->model('admin/product');

        $status = ($operation == 'enable') ? 1 : 0;
        $pids = explode(',', $product_id);
        $this->model_admin_product->setStatus($pids, $status, true);

        $this->respond_json('');

    }

    public function info(){

        if(empty($this->request->get['product_id'])){
            $this->respond_fail('argument_missing');
            return;
        }

        $this->load->model('tool/image');
        $this->load->model('admin/product');

        $product_id = (int)$this->getInput('product_id');
        
        $product_info = $this->model_admin_product->getProduct($product_id);
		if(!$product_info){
			$this->respond_fail('not_found');
			return;
		}

        $img_url = $this->model_tool_image->resize($product_info['image'], 250, 250);
        $img_url = url($img_url);
        $product_info['image'] = $img_url;
		$product_info['images'] = $this->model_admin_product->getProductImages($product_info['product_id']);

        foreach ($product_info['images'] as &$item) {
            $item['image'] = url($this->model_tool_image->resize($item['image'], 250, 250));
        }

        if($this->customer->isLogged()){
            $this->load->model('account/wishlist');
            $inWishList = $this->model_account_wishlist->isInWishList($product_id);
            $product_info['in_wishlist'] = $inWishList;
        }

        $this->respond_json($product_info);

    }

    public function listNames(){
        
        $this->load->model('catalog/prt');

        $store_id = $this->config->get('config_store_id');

        $products = $this->model_catalog_prt->getProductsNames( $store_id );

        $this->respond_json(array(
            'items' => $products
        ));
    }

    public function list()
    {
        $this->load->model('catalog/prt');

        $ptype = $this->getInput('ptype', '');
        $cps = $ptype != '';

        $filters = array();
        $filters['store'] = $cps ? 0 : $this->config->get('config_store_id');
        $filters['start'] = $this->getInput('start', '0');
        $filters['limit'] = $this->getInput('limit', '40');
        $filters['cat'] = $this->getInput('cat', '');
        $filters['subcat'] = $this->getInput('subcat', '');
        $filters['child_subcat'] = $this->getInput('child_subcat', '');
        $filters['name'] = $this->getInput('name', '');
        $filters['stock'] = $this->getInput('stock', '');
        $filters['discount'] = $this->getInput('discount', '');
        if($ptype == 'cps'){
            $filters['status'] = '1';
        }

        $order_by = $this->getInput('order_by', '');
        $order = $this->getInput('order', '');
        if($order_by == 'price'){
            $filters['order_by'] = 'p.fprice';
            if($order == 'high_to_low'){
                $filters['order'] = 'DESC';
            }else{
                $filters['order'] = 'ASC';
            }
        }else{
            $filters['order_by'] = 'p.sort_order';
            $filters['order'] = 'ASC';
        }

        $moreDetails = $this->getInput('side');
        if($moreDetails == 'client'){
            $filters['moreDetails'] = true;
        }

        $child_subcat = array();
        if(isset($filters['moreDetails']) && $filters['moreDetails']){
            if(empty($filters['child_subcat']) && !empty($filters['subcat'])){
                $this->load->model('admin/category');
                $child_subcat = $this->model_admin_category->getChildsBasis($filters['subcat']);
            }
        }


        $products = $this->model_catalog_prt->getProducts($filters);
        //$products = $this->model_admin_product->getProducts($filters);
        $list = $products;
        $data = new stdClass();
        $data->length = count($list);
        $data->items = $list;
        $data->child_subcat = $child_subcat;

        $this->respond_json($data);
    }

    public function save()
    {
        checkAccess(AG_ADMIN);

        $this->load->model('admin/product');

        $pid = $this->getInput('pid');
        $ptype = $this->getInput('ptype');
        $cps = false;
        if($ptype == 'cps_admin'){
            $user = $this->model_admin_users->loadCurrent();
		    $cps = (intval($user['user_type']) < 10);
        }

        $store_id = $cps ? 0 : intval($this->config->get('config_store_id'));

        if($pid == 'new'){
            $data = $this->getDataArray($store_id);

            $pid = $this->model_admin_product->addProduct($data);

        } else {
            $_p = $this->model_admin_product->getProduct($pid);
            if(!$_p){
                $this->respond_fail('not_found');
                return;
            }
            if(intval($_p['store_id']) != $store_id){
                $this->respond_fail('product_not_owned');
                return;
            }
        }

        
        $desc = $_GET['desc'];
        if(!empty($desc)){
            $desc = json_decode($desc, true);
        }

        $data = array();
        $data['store_id'] = $store_id;
        $data['price'] = $this->getInput('price', '0');
        $data['quantity'] = $this->getInput('stock', '0');
        $data['barcode'] = $this->getInput('barcode');
        $data['spf'] = $this->getInput('spf');
        $data['spf_unit'] = $this->getInput('spf_unit');

        $data['discount_amt'] = $this->getInput('discount_amt', '0');
        $data['discount_type'] = $this->getInput('discount_type', '0');
        $data['gst'] = $this->getInput('gst', '0');
        $data['hsn'] = $this->getInput('hsn', 'HSN');
        $data['status'] = $this->getInput('status', '1');
        $data['cat'] = $this->getInput('cat', 0);
        $data['subcat'] = $this->getInput('subcat', 0);
        $data['child_subcat'] = $this->getInput('child_subcat', 0);
        $data['brand'] = $this->getInput('brand', 0);
        $data['sub_brand'] = $this->getInput('sub_brand', 0);

        $fprice = floatval($data['price']);
        $discount_type = intval($data['discount_type']);
        $discount_amt = intval($data['discount_amt']);

        if($discount_amt > 0){
            if($discount_type == 1){
                $fprice -= ($fprice * $discount_amt / 100);
            }else{
                $fprice -= $discount_amt;
            }
        }
        $data['fprice'] = $fprice;


        $image = $this->getInput('image', false);
        if($image){
            $data['image'] = $image;
        }

        $images_to_keep = $this->getInput('images_to_keep', '');
        $images_to_add = $this->getInput('images_to_add', '');
        $images_to_add = (empty($images_to_add) ? null : explode(',', $images_to_add));

        $this->model_admin_product->setProduct($pid, $data);
        $this->model_admin_product->setProductImages($pid, $images_to_keep, $images_to_add);

        $cats = array($data['cat'], $data['subcat'], $data['child_subcat'], $data['brand'], $data['sub_brand']);
        $this->model_admin_product->setProductCategories($pid, $cats);

        foreach ($desc as $lang_id => $value) {
            $this->model_admin_product->setProductDescription($pid, $lang_id, $value);
        }

        $da = (float)$data['discount_amt'];
        if($da > 0){
            $dt = (int)$data['discount_type'];
            $discount = floatval($data['price']);
            if($dt == 1) $discount = ($discount / 100) * (100 - $da);
            else if($dt == 2) $discount -= $da;
            $this->model_admin_product->setProductDiscount($pid, $discount);
        }else{
            $this->model_admin_product->deleteProductDiscount($pid);
        }
        

        $this->respond_json(array('product_id' => $pid));
    }

    public function copyCPSP(){
        $ids = $this->getInput('ids');
        if(empty($ids)){
            $this->respond_fail('invalid_input');
            return;
        }

        $ids = $this->escapeIntArray(explode(',', $ids));

        $store_id = $this->config->get('config_store_id');

        $this->load->model('catalog/prt');
        $this->load->model('admin/product');

        $filters = array(
            'list' => implode(',', $ids),
            'store' => 0
        );

        $products = $this->model_catalog_prt->getProducts($filters);

        $getId = function ($p){
            return $p['product_id'];
        };

        // Products ids list is now safe (filtred out products that belong to some stores)
        $ids = array_map($getId, $products);

        foreach($ids as $id){
            $new_id = $this->model_admin_product->copyProduct($id, $store_id);
            $this->model_admin_product->setStatus(array($new_id), 0);
        }

        $this->respond_json($ids);

    }


    public function sort_order(){

        $this->load->model('admin/product');

        $prts = $this->getInput('ids');
        $store_id = $this->config->get('config_store_id');

        $prts = $this->escapeIntArray(explode(',', $prts));

        $this->model_admin_product->setSortOrder($prts, $store_id);

        $this->respond_json('');
    }


    private function getDataArray($store_id)
    {
        return array(
            'product_description' =>
                array(
                1 =>
                    array(
                    'name' => '',
                    'description' => '',
                    'meta_title' => '',
                    'meta_description' => '',
                    'meta_keyword' => '',
                    'tag' => ''
                ),
                2 =>
                    array(
                    'name' => '',
                    'description' => '',
                    'meta_title' => '',
                    'meta_description' => '',
                    'meta_keyword' => '',
                    'tag' => ''
                )
            ),
            'store_id' => $store_id,
            'model' => 'model',
            'sku' => '',
            'upc' => '',
            'ean' => '',
            'jan' => '',
            'isbn' => '',
            'mpn' => '',
            'location' => '',
            'price' => '0.0000',
            'tax_class_id' => '9',
            'quantity' => '1',
            'minimum' => '1',
            'subtract' => '1',
            'stock_status_id' => '5',
            'shipping' => '1',
            'date_available' => '2018-12-11',
            'length' => '0.00000000',
            'width' => '0.00000000',
            'height' => '0.00000000',
            'length_class_id' => '1',
            'weight' => '0.00000000',
            'weight_class_id' => '1',
            'status' => '1',
            'sort_order' => '1',
            'manufacturer' => '',
            'manufacturer_id' => '0',
            'filter' => '',
            'product_store' =>
                array(
                    0 => $this->config->get('config_store_id')
                ),
            'download' => '',
            'related' => '',
            'option' => '',
            'image' => '',
            'points' => '0'
        );
    }

    private function escapeIntArray($arr){
        $escaper = function ($item){
            return (int)$item;
        };
        return array_map($escaper, $arr);
    }

}