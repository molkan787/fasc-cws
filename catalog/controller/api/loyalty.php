<?php
include_once 'dep.php';

class ControllerApiLoyalty extends Controller{

    public function list(){
        $this->load->model('admin/loyalty');
        $store_id = $this->config->get('config_store_id');

        $search = $this->getInput('search');
        $cards;
        if($search){
            $cards = $this->model_admin_loyalty->searchCards(array(
                'store_id' => $store_id,
                'query' => $search
            ), 100);
        }else{
            $cards = $this->model_admin_loyalty->getCardsByStore($store_id, 100);
        }

        $this->respond_json(array(
            'items' => $cards
        ));
    }

    
    public function add(){
        $this->load->model('admin/loyalty');
        $store_id = $this->config->get('config_store_id');

        $data = array(
            'number' => $this->getInput('number'),
            'customerId' => $this->getInput('customerId'),
            'store_id' => $store_id,
            'balance' => 0
        );

        $resp = $this->model_admin_loyalty->add($data);
        if(is_int($resp)){
            $card = $this->model_admin_loyalty->getCardById($resp);
            $this->respond_json($card);
        }else{
            $this->respond_fail($resp);
        }
    }

    public function delete(){

        $this->load->model('admin/loyalty');
        $store_id = $this->config->get('config_store_id');

        $cardId = $this->getInput('id');

        $card = $this->model_admin_loyalty->getCardById($cardId);

        if($card && intval($card['store_id']) == intval($store_id)){

            $this->model_admin_loyalty->deleteCardById($cardId);

            $this->respond_json($cardId);

        }else{
            $this->respond_fail('not_found');
        }

    }

    public function editBalance(){

        $this->load->model('admin/loyalty');
        $store_id = $this->config->get('config_store_id');
        $cardId = $this->getInput('id');
        $balance = $this->getInput('balance');

        if($this->model_admin_loyalty->confirmStoreId($cardId, $store_id)){
            $this->model_admin_loyalty->editBalance($cardId, $balance);
            $this->respond_json(array(
                'id' => $cardId,
                'balance' => floatval($balance)
            ));
        }else{
            $this->respond_fail('not_found');
        }

    }

    public function loadCard(){

        $this->load->model('admin/loyalty');
        $this->load->model('admin/customer');
        $store_id = (int)$this->config->get('config_store_id');

        $query = $this->getInput('query');
        if(empty($query) && strlen($query) < 5){
            $this->respond_fail('invalid_params');
            return;
        }

        $card = $this->model_admin_loyalty->getCardByNumber($query);
        if($card && intval($card['store_id']) != $store_id) $card = NULL;
        if(!$card){
            $customer = $this->model_admin_customer->getCustomerByPhone($query);
            if($customer){
                $card = $this->model_admin_loyalty->getCardByClientId($customer['customer_id']);
                if($card && intval($card['store_id']) != $store_id) $card = NULL;
            }
        }
        if(empty($card)) $card = NULL;

        $this->respond_json($card);

    }

}