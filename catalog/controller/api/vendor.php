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

}