<?php
class ModelAdminLoyalty extends Model {

    public function confirmStoreId($cardId, $store_id){

        $sql = "SELECT store_id FROM fasc_loyalty_card WHERE id = " . (int)$cardId;
        $query = $this->db->query($sql);
        $card = $query->row;

        if($card){
            return (intval($card['store_id']) == intval($store_id));
        }else{
            return false;
        }

    }

    public function getCardsByStore($store_id = 0, $limit = 20){
        $sql = "SELECT lc.*, CONCAT(c.firstname, ' ', c.lastname) AS customer FROM fasc_loyalty_card lc LEFT JOIN oc_customer c ON (lc.client_id = c.customer_id) WHERE lc.store_id = " . (int)$store_id . " GROUP BY lc.id LIMIT ".(int)$limit;
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function add($data){
        $store_id = (int)$data['store_id'];
        $customerId = (int)$data['customerId'];
        $number = $this->db->escape(strtoupper($data['number']));
        $balance = floatval($data['balance']);

        $sql = "SELECT id FROM fasc_loyalty_card WHERE `number` = '" . $number . "' LIMIT 1";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            return 'duplicate_number';
        }

        $sql = "INSERT INTO fasc_loyalty_card VALUES(NULL, ".$store_id.", ".$customerId.", '".$number."', '".$balance."', NOW(), NOW())";
        $query = $this->db->query($sql);

        return $this->db->getLastId();
    }

    public function getCardById($cardId){
        $sql = "SELECT lc.*, CONCAT(c.firstname, ' ', c.lastname) AS customer FROM fasc_loyalty_card lc LEFT JOIN oc_customer c ON (lc.client_id = c.customer_id) WHERE lc.id = " . (int)$cardId . " GROUP BY lc.id";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getCardByNumber($number){
        $number = $this->db->escape($number);
        $sql = "SELECT lc.*, CONCAT(c.firstname, ' ', c.lastname) AS customer FROM fasc_loyalty_card lc LEFT JOIN oc_customer c ON (lc.client_id = c.customer_id) WHERE lc.number = '" . $number. "' GROUP BY lc.id";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getCardByClientId($customerId){
        $sql = "SELECT lc.*, CONCAT(c.firstname, ' ', c.lastname) AS customer FROM fasc_loyalty_card lc LEFT JOIN oc_customer c ON (lc.client_id = c.customer_id) WHERE c.customer_id = " . (int)$customerId . " GROUP BY lc.id";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function deleteCardById($cardId){
        $sql = "DELETE FROM fasc_loyalty_card WHERE id = " . (int)$cardId;
        $this->db->query($sql);
        return true;
    }

    public function editBalance($cardId, $newBalance){
        $balance = $this->db->escape(floatval($newBalance));
        $sql = "UPDATE fasc_loyalty_card SET balance = '".$balance."' WHERE id = " . (int)$cardId;
        $this->db->query($sql);
        return true;
    }

    public function addBalance($cardId, $amount){
        $amt = floatval($amount);
        $neg = $amt < 0;
        $amt = $neg ? ($amt * -1) : $amt;
        $amt = $this->db->escape(floatval($amt));
        $sql = "UPDATE fasc_loyalty_card SET balance = balance ".($neg ? '-' : '+')." '".$amt."' WHERE id = ".(int)$cardId;
        $this->db->query($sql);
        return true;
    }

    public function searchCards($data, $limit = 20){
        $store_id = (int)$data['store_id'];
        $s = $this->db->escape(strtolower($data['query']));
        $sql = "SELECT lc.*, CONCAT(c.firstname, ' ', c.lastname) AS customer FROM fasc_loyalty_card lc LEFT JOIN oc_customer c ON (lc.client_id = c.customer_id) WHERE lc.store_id = ".$store_id." AND (lc.number LIKE '%".$s."%' OR CONCAT(c.firstname, ' ', c.lastname) LIKE '%".$s."%') GROUP BY lc.id LIMIT ".(int)$limit;
        $query = $this->db->query($sql);
        return $query->rows;
    }

}