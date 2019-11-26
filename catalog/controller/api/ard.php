<?php
include_once 'dep.php';

class ControllerApiArd extends Controller{

    public function index(){

        $this->load->model('admin/customer');
        $this->load->model('admin/product');

        $store_id = $this->config->get('config_store_id');

        $customers = $this->model_admin_customer->getCustomers(array(
            'store' => $store_id
        ));
        $products = $this->model_admin_product->allProductsNames($store_id);

        $this->respond_json(array(
            'customers' => $customers,
            'products' => $products
        ));

    }

}