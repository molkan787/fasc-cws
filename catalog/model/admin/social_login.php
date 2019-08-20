<?php

define('FB_API_URL', 'https://graph.facebook.com/v3.2/me?fields=id%2Cname%2Cemail&access_token=');

class ModelAdminSocialLogin extends Model {


	public function loginFB($access_token){

		$s_data = $this->getFBUserData($access_token);

		if(!$s_data->id) return false;

		$localUserId = $this->getLocalUserId(1, $s_data->id);

		if(!$localUserId){
			$localUserId = $this->createLocalUser($s_data->id, 1, $s_data->email, $s_data->name);
		}

		$this->customer->loginById($localUserId);
		return true;
	}


	private function getFBUserData($access_token){

		$api_url = FB_API_URL . $access_token;
		$response = file_get_contents($api_url);

		$data = json_decode($response);
		return $data;
	}

	private function getLocalUserId($platform_id, $ref_id){
		$sql = "SELECT customer_id FROM " . DB_PREFIX . "customer WHERE s_ref = '".$this->db->escape($ref_id)."' AND s_ref_type = '".(int)$platform_id."'";
		$query = $this->db->query($sql);
		if($query->num_rows){
			return $query->row['customer_id'];
		}else{
			return null;
		}
	}

	private function createLocalUser($s_ref, $platform_id, $email, $name){
		$this->load->model('account/customer');

		$names = explode(' ', $name);
		$firstname = $names[0];
		$lastname = count($names) > 1 ? $names[1] : $names[0];

		$data = array(
			"customer_group_id" => "1",
			"firstname" => $firstname,
			"lastname" => $lastname,
			"telephone" => '',
			"email" => $email,
			"password" => "basic_password",
			"confirm" => "basic_password",
			"newsletter" => "0",
			"agree" => "1"
		);

		$customer_id = $this->model_account_customer->addCustomer($data);
		$this->model_account_customer->verifyCustomer($customer_id);

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET s_ref = '".$this->db->escape($s_ref)."', s_ref_type = '".(int)$platform_id."' WHERE customer_id = '".(int)$customer_id."'");

		return $customer_id;

	}


}