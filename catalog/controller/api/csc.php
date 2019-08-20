<?php
include_once 'dep.php';

class ControllerApiCsc extends Controller{


	public function register(){

		$this->load->model('account/customer');
		$this->load->model('account/otp');

		$firstname = $this->getInput('firstname');
		$lastname = $this->getInput('lastname');
		$telephone = $this->getInput('telephone');
		$email = $this->getInput('email');

		$this->checkInputs(array($firstname, $lastname, $telephone));

		if(empty($email)){
			$email = "customer_" . $telephone . "@walkonretail.com";
		}

		if ($this->model_account_customer->getTotalCustomersByTelephone($telephone)) {
			$this->respond_fail('phone_exist');
			return;
		}

		$data = array(
			"customer_group_id" => "1",
			"firstname" => $firstname,
			"lastname" => $lastname,
			"telephone" => $telephone,
			"email" => $email,
			"password" => "basic_password",
			"confirm" => "basic_password",
			"newsletter" => "0",
			"agree" => "1"
		);

		$customer_id = $this->model_account_customer->addCustomer($data);

		$verify_token = $this->model_account_otp->sendCode($customer_id, $telephone);

		$this->respond_json(array('token' => $verify_token, 'customer_id' => $customer_id, 'telephone' => $telephone));

	}

	public function login(){
		$this->load->model('account/customer');
		$this->load->model('account/otp');

		$phone = $this->getInput('telephone');
		$customer = $this->model_account_customer->getCustomerByPhone($phone);

		if (!$customer) {
			$this->respond_fail('customer_not_found');
			return;
		}

		$verify_token = $this->model_account_otp->sendCode($customer['customer_id'], $customer['telephone']);

		$this->respond_json(array('token' => $verify_token, 'telephone' => $phone));
	}

	public function verify(){

		$phone = $this->getInput('telephone');
		$token = $this->getInput('token');
		$code = $this->getInput('code');

		if(strlen($code) != 6 || strlen($phone) < 9 || strlen($token) != 8 ){
			$this->respond_fail('invalid_arguments');
			return;
		}

		$this->load->model('account/customer');
		$this->load->model('account/otp');
		$this->load->model('account/address');

		$customer_id = $this->model_account_otp->checkCode($phone, $token, $code);

		if(intval($customer_id) == 0){
			$this->respond_fail('invalid_code');
			return;
		}

		$this->model_account_customer->verifyCustomer($customer_id);

		if($this->customer->loginById($customer_id)){
			$email = $this->customer->getEmail();
			if(substr($email, 0, 9) == 'customer_') $email = '';
			$customer = array(
				'id' => $this->customer->getId(),
				'firstname' => $this->customer->getFirstName(),
				'lastname' => $this->customer->getLastName(),
				'phone' => $this->customer->getTelephone(),
				'email' => $this->customer->getEmail()
			);
			$addresses = $this->model_account_address->getAddressesBasic();
			$this->respond_json(array(
				'customer_id' => $customer_id,
				'data' => $customer,
				'addresses' => $addresses
			));
		}else{
			$this->respond_fail('unknow_error');
		}

	}

	public function social_login(){

		$this->load->model('admin/social_login');
		$this->load->model('account/address');

		$platform = $this->getInput('platform');

		if($platform == 'fb' || $platform == '1'){

			$token = $this->getInput('token');

			if(empty($token)){
				$this->respond_fail('argument_missing');
				return;
			}

			if($this->model_admin_social_login->loginFB($token)){

				$email = $this->customer->getEmail();
				if(substr($email, 0, 9) == 'customer_') $email = '';
				$customer = array(
					'id' => $this->customer->getId(),
					'firstname' => $this->customer->getFirstName(),
					'lastname' => $this->customer->getLastName(),
					'phone' => $this->customer->getTelephone(),
					'email' => $this->customer->getEmail()
				);
				$addresses = $this->model_account_address->getAddressesBasic();
				$this->respond_json(array(
					'customer_id' => $customer_id,
					'data' => $customer,
					'addresses' => $addresses
				));

			}else{
				$this->respond_fail('unknow_error');
			}

		}else{
			$this->respond_fail('unknow_platform');
		}

	}

}