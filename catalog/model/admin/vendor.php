<?php
class ModelAdminVendor extends Model {

    public function getVendors($store_id){

        $sql = "SELECT * FROM fasc_vendor WHERE store_id = " . (int)$store_id . " ORDER BY id DESC";

        $query = $this->db->query($sql);

        return $query->rows;

    }

    public function editVendor($id, $data){

        $new = $id == 'new';
        $sql = $new ? "INSERT INTO " : "UPDATE ";
        $sql .= "fasc_vendor SET ";

        $sql .= "name = '" . $this->db->escape($data['name']) . "', ";
        $sql .= "address = '" . $this->db->escape($data['address']) . "', ";
        $sql .= "place = '" . $this->db->escape($data['place']) . "', ";
        $sql .= "state = '" . $this->db->escape($data['state']) . "', ";
        $sql .= "gst = '" . (int)$data['gst'] . "', ";
        $sql .= "gst_number = '" . $this->db->escape($data['gst_number']) . "', ";
        $sql .= "bank_name = '" . $this->db->escape($data['bank_name']) . "', ";
        $sql .= "bank_account_number = '" . $this->db->escape($data['bank_account_number']) . "', ";
        $sql .= "bank_ifsc_code = '" . $this->db->escape($data['bank_ifsc_code']) . "', ";
        $sql .= "bank_account_type = '" . $this->db->escape($data['bank_account_type']) . "', ";

        $sql .= "date_modified = NOW() ";

        if($new){
            $sql .= ", date_added = NOW(), store_id = " . (int)$data['store_id'];
        }else{
            $sql .= "WHERE id = " . (int)$id . " AND store_id = " . (int)$data['store_id'];
        }

        $this->db->query($sql);

        if($new){
            return $this->db->getLastId();
        }else{
            return $id;
        }

    }

}