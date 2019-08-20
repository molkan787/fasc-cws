<?php
include_once 'utils.php';
define('SMS_API_KEY', '234776Al1CDdqQHj5ce17a88');

class ModelAccountOtp extends Model{

    public function sendCode($customer_id, $phone){
        $phone = $this->db->escape($phone);
        $token = $this->genToken();
        $code = $this->genCode();
        $query = $this->db->query("INSERT INTO otps VALUES ('', ".time().", 1, '".$phone."', '".$code."', '".$customer_id."', '".$token."')");
        $api_url = $this->get_ss_api($phone, $code);
        $api_resp = file_get_contents($api_url);
        return $token;
    }

    public function checkCode($phone, $token, $code){
        $phone = $this->db->escape($phone);
        $token = $this->db->escape($token);
        $code = $this->db->escape($code);
        $query = $this->db->query("SELECT * FROM otps WHERE active=1 AND phone='".$phone."' AND  token='".$token."' AND code='".$code."' ORDER BY id DESC LIMIT 1");
        if($query->num_rows > 0){
            $row = $query->row;
            $otp_id = $row["id"];
            $this->db->query("DELETE FROM otps WHERE id=".$otp_id);
            return $row["customer_id"];
        }else{
            return 0;
        }
    }


    private function genToken(){
        return rnd_str(8);
    }

    private function genCode(){
        return rnd_digit_str(6);
    }
    
    private function get_ss_api($num, $code){
        return "http://api.msg91.com/api/sendhttp.php?country=91&sender=WalkOn&route=4&mobiles=91".$num."&authkey=".SMS_API_KEY."&message=Verification code for WalkOn Retail is: ".$code;
    }


    public function sendOrderAlert($num, $order_amount){
        $url = $this->get_alertm_api($num, $order_amount);
        file_get_contents($url);
    }

    public function sendOrderAlertToAdmin($num, $data){
        $msg = 'A new order was placed with an amount of '.$data['amount'].' INR, Delivery: '.$data['delivery'];
        $url = $this->get_msg_api($num, $msg);
        file_get_contents($url);
    }

    private function get_alertm_api($num, $amount){
        $amount = NumberToFixed($amount, 2);
        return "http://api.msg91.com/api/sendhttp.php?country=91&sender=WalkOn&route=4&mobiles=91".$num."&authkey=".SMS_API_KEY."&message=Thank you for shopping with us, Your order with amount of ".$amount." INR has been placed successfully. Order will be reached to you soon.";
    }

    private function get_msg_api($num, $msg){
        $amount = NumberToFixed($amount, 2);
        return "http://api.msg91.com/api/sendhttp.php?country=91&sender=WalkOn&route=4&mobiles=91".$num."&authkey=".SMS_API_KEY."&message=".$msg;
    }

}