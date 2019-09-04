<?php
class ModelAdminVendor extends Model {

    public function getVendors($store_id){

        $sql = "SELECT * FROM fasc_vendor WHERE store_id = " . (int)$store_id . " ORDER BY id DESC";

        $query = $this->db->query($sql);

        $vendors = $query->rows;
        foreach($vendors as &$vendor){

            $vendor_id = $vendor['id'];
            $query = $this->db->query("SELECT purchase_date FROM fasc_purchase WHERE vendor_id = " . $vendor_id . " ORDER BY purchase_date DESC LIMIT 1");
            if($query->row){
                $vendor['last_purchase'] = $query->row['purchase_date'];
            }
        }

        return $vendors;

    }

    public function changeVendorBalance($vendor_id, $change_by){
        $change_by = floatval($change_by);
        $negative = $change_by < 0;
        if($negative) $change_by = $change_by * -1;
        $operator = $negative ? '-' : '+';
        $sql = "UPDATE fasc_vendor SET balance = balance " . $operator . ' ' . $change_by . " WHERE id = " . (int)$vendor_id;

        $this->db->query($sql);

        return true;

    }

    public function getVendor($id){

        $sql = "SELECT * FROM fasc_vendor WHERE id = " . (int)$id;

        $query = $this->db->query($sql);

        return $query->row;

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

    public function addPurchase($data){

        $sql = "INSERT INTO fasc_purchase SET store_id = " . (int)$data['store_id'] . ", vendor_id = " . (int)$data['vendor_id'];
        $sql .= ", invoice_no = '" . $this->db->escape($data['invoice_no']) . "'";
        $sql .= ", purchase_date = '" . $this->db->escape($data['purchase_date']) . "'";
        $sql .= ", total_value = '0', date_modified = NOW(), date_added = NOW()";

        $this->db->query($sql);

        $purchase_id = $this->db->getLastId();

        $total = 0;
        foreach($data['items'] as $item){

            $qty = (int)$item['qty'];
            $buy_price = floatval($item['buy_price']);

            $total += $buy_price * $qty;

            $sql = 'INSERT INTO fasc_purchase_items SET purchase_id = ' . $purchase_id;
            $sql .= ", name = '" . $this->db->escape($item['name']) . "'";
            $sql .= ", qty = " . $qty . ", rate = " . (int)$item['rate'] . ", gst = " . (int)$item['gst'];
            $sql .= ", buy_price = '" . $buy_price . "', sell_price = '" . floatval($item['sell_price']) . "'";

            $this->db->query($sql);

        }

        $sql = "UPDATE fasc_purchase SET total_value = '" . $total . "' WHERE id = " . $purchase_id;
        $this->db->query($sql);

        return array(
            'id' => $purchase_id,
            'total' => $total,
        );
    }

    public function addPayment($data){
        $sql = "INSERT INTO fasc_payments SET store_id = " . (int)$data['store_id'];
        $sql .= ", vendor_id = " . (int)$data['vendor_id'] . ", amount = '" . floatval($data['amount']) . "'";
        $sql .= ", payment_date = '" . $this->db->escape($data['payment_date']) . "'";
        $sql .= ", payment_method = '" . $this->db->escape($data['payment_method']) . "'";
        $sql .= ", reference = '" . $this->db->escape($data['reference']) . "'";
        $sql .= ", receiver_name = '" . $this->db->escape($data['receiver_name']) . "'";
        $sql .= ", attachment = '" . $this->db->escape($data['attachment']) . "'";
        $sql .= ", date_added = NOW(), date_modified = NOW()";

        $this->db->query($sql);

        return $this->db->getLastId();
    }

    public function getPurchases($vendor_id){

        $sql = "SELECT p.*, COUNT(i.purchase_id) AS items_count FROM fasc_purchase AS p LEFT OUTER JOIN fasc_purchase_items AS i ON i.purchase_id = p.id WHERE p.vendor_id = " . (int)$vendor_id . " GROUP BY p.id ORDER BY p.purchase_date DESC";

        $query = $this->db->query($sql);

        return $query->rows;

    }

    public function getPayments($vendor_id){

        $sql = "SELECT * FROM fasc_payments WHERE vendor_id = " . (int)$vendor_id . " ORDER BY payment_date, id DESC";

        $query = $this->db->query($sql);

        return $query->rows;

    }

}