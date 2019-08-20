<?php
include_once 'dep.php';

class ControllerApiAddress extends Controller{

    public function add(){


        $customer_id = $this->customer->getId();
        if(!$customer_id){
          $this->respond_fail('NO_CUSTOMER');
          return;
        }

        $addr1 = $this->getInput('address_1');
        $addr2 = $this->getInput('address_2');
        $city = $this->getInput('city');
        $postcode = $this->getInput('postcode');

        $data = $this->getDataArray($addr1, $addr2, $city, $postcode);

        $this->load->model('account/address');
        $address_id = $this->model_account_address->addAddress($customer_id, $data);

        $data['address_id'] = $address_id;

        $this->respond_json($data);

    }

    public function delete(){
        $addr_id = $this->getInput('addr_id');
        $this->load->model('account/address');
        $this->model_account_address->deleteAddress($addr_id);
        $this->respond_json('');
    }

    public function list(){
        $this->load->model('account/address');
        $addresses = $this->model_account_address->getAddressesBasic();
        $this->respond_json(array('items' => $addresses));
    }

    private function getDataArray($addr1, $addr2, $city, $postcode){
        return array(
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'address_1' => $addr1,
            'address_2' => $addr2,
            'postcode' => $postcode,
            'country_id' => '99',
            'zone_id' => '1492',
            'default' => '0',
            'city' => $city
        );
    }

}