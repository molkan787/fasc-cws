<?php
include_once 'dep.php';

class ControllerApiVendor extends Controller{


    public function list(){

        $this->load->model('admin/vendor');

        $store_id = $this->config->get('config_store_id');

        $vendors = $this->model_admin_vendor->getVendors($store_id);

        $this->respond_json(array( 
            'items' => $vendors
        ));

    }

    public function edit(){

        $this->load->model('admin/vendor');

        $store_id = $this->config->get('config_store_id');

        $data = json_decode($this->getInput('data'), true);

        $data['store_id'] = $store_id;

        $vendor_id = $this->model_admin_vendor->editVendor($data['id'], $data);

        $this->respond_json(array(
            'id' => $vendor_id
        ));

    }

    public function addPurchase(){
        $this->load->model('admin/vendor');

        $store_id = $this->config->get('config_store_id');

        $data = json_decode($this->getInput('data'), true);

        $data['store_id'] = $store_id;

        $vendor = $this->model_admin_vendor->getVendor($data['vendor_id']);

        if(intval($vendor['store_id']) != intval($store_id)){
            $this->respond_fail('VENDOR_NOT_OWNED_BY_THIS_STORE');
            return;
        }

        $purchase = $this->model_admin_vendor->addPurchase($data);

        $this->model_admin_vendor->changeVendorBalance($data['vendor_id'], -$purchase['total']);

        $this->respond_json($purchase);
    }

    public function addPayment(){
        $this->load->model('admin/vendor');

        $store_id = $this->config->get('config_store_id');

        $data = json_decode($this->getInput('data'), true);

        $data['store_id'] = $store_id;

        $vendor = $this->model_admin_vendor->getVendor($data['vendor_id']);

        if(intval($vendor['store_id']) != intval($store_id)){
            $this->respond_fail('VENDOR_NOT_OWNED_BY_THIS_STORE');
            return;
        }

        $purchase = $this->model_admin_vendor->addPayment($data);

        $this->model_admin_vendor->changeVendorBalance($data['vendor_id'], $data['amount']);

        $this->respond_json($purchase);
    }

    public function getPurchases(){

        $this->load->model('admin/vendor');

        $store_id = $this->config->get('config_store_id');

        $vendor_id = $this->getInput('vendor_id');

        $vendor = $this->model_admin_vendor->getVendor($vendor_id);

        if(intval($vendor['store_id']) != intval($store_id)){
            $this->respond_fail('VENDOR_NOT_OWNED_BY_THIS_STORE');
            return;
        }

        $purchases = $this->model_admin_vendor->getPurchases($vendor_id);

        $this->respond_json(array(
            'items' => $purchases
        ));

    }

    public function getPayments(){

        $this->load->model('admin/vendor');

        $store_id = $this->config->get('config_store_id');

        $vendor_id = $this->getInput('vendor_id');

        $vendor = $this->model_admin_vendor->getVendor($vendor_id);

        if(intval($vendor['store_id']) != intval($store_id)){
            $this->respond_fail('VENDOR_NOT_OWNED_BY_THIS_STORE');
            return;
        }

        $payments = $this->model_admin_vendor->getPayments($vendor_id);

        $this->respond_json(array(
            'items' => $payments
        ));

    }

}