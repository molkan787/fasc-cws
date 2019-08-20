<?php
include_once 'dep.php';

class ControllerApiUsers extends Controller{

	public function list_master(){
		checkAccessGroup(AG_MASTER_ADMIN);

		$this->load->model('admin/users');

		$users = $this->model_admin_users->getUsers(0);

		$this->respond_json(array('items' => $users));
	}

	public function create_master(){
		checkAccessGroup(AG_MASTER_ADMIN);

		$this->load->model('admin/users');

		$password = _generateRandomString(10);

		$username = $this->getInput('username');
		$user_type = $this->getInput('user_type');
		$fullname = $this->getInput('fullname');

		if(empty($username) or empty($user_type) or empty($fullname)){
			$this->respond_fail('argument_missing');
			return;
		}

		if(strlen($username) < 5 or strlen($username) > 30 or ($user_type < 2 or $user_type > 4) or strlen($fullname) < 8 or strlen($fullname) > 40){
			$this->respond_fail('invalid_argument');
			return;
		}

		$username = 'm_'.$username;

		$user_id = $this->model_admin_users->createUser(0, $user_type, $username, $password, $fullname);

		if(!$user_id){
			$this->respond_fail('username_exit');
			return;
		}

		$this->respond_json(array('user_id' => $user_id, 'username' => $username, 'password' => $password));
	}

	public function delete_master(){
		checkAccessGroup(AG_MASTER_ADMIN);

		$this->load->model('admin/users');

		$user_id = $this->getInput('user_id');
		if(empty($user_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$this->model_admin_users->deleteUser($user_id);

		$this->respond_json(array('user_id' => (int)$user_id));
	}

	public function reset_master(){
		checkAccessGroup(AG_MASTER_ADMIN);

		$this->load->model('admin/users');

		$user_id = $this->getInput('user_id');
		if(empty($user_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$userData = $this->model_admin_users->getUser($user_id);
		if($userData['user_type'] == 1){
			$this->respond_fail('access_forbidden');
			return;
		}


		$password = _generateRandomString(10);

		$this->model_admin_users->setPassword($user_id, $password);

		$this->respond_json(array('user_id' => (int)$user_id, 'password' => $password));
	}

	public function change_status_master(){
		checkAccessGroup(AG_MASTER_ADMIN);

		$this->load->model('admin/users');

		$user_id = $this->getInput('user_id');
		$status = (int)$this->getInput('status');
		if(empty($user_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$this->model_admin_users->setStatus($user_id, $status);

		$this->respond_json(array('user_id' => (int)$user_id, 'status' => $status));
	}


	public function list(){
		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/users');

		$store_id = $this->config->get('config_store_id');

		$users = $this->model_admin_users->getUsers($store_id);

		$this->respond_json(array('items' => $users));
	}

	public function create(){
		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/users');

		$password = _generateRandomString(10);

		$username = $this->getInput('username');
		$user_type = $this->getInput('user_type');
		$fullname = $this->getInput('fullname');

		if(empty($username) or empty($user_type) or empty($fullname)){
			$this->respond_fail('argument_missing');
			return;
		}

		if(strlen($username) < 5 or strlen($username) > 30 or ($user_type < 11 or $user_type > 14) or strlen($fullname) < 8 or strlen($fullname) > 40){
			$this->respond_fail('invalid_argument');
			return;
		}

		$store_id = $this->config->get('config_store_id');

		$username = 's'.$store_id.'_'.$username;

		$user_id = $this->model_admin_users->createUser($store_id, $user_type, $username, $password, $fullname);

		if(!$user_id){
			$this->respond_fail('username_exit');
			return;
		}

		$this->respond_json(array('user_id' => $user_id, 'username' => $username, 'password' => $password));
	}

	public function delete(){
		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/users');

		$user_id = $this->getInput('user_id');
		if(empty($user_id)){
			$this->respond_fail('argument_missing');
			return;
		}

		$userData = $this->model_admin_users->getUser($user_id);
		if($userData['store_id'] != $this->config->get('config_store_id')){
			$this->respond_fail('access_forbidden');
			return;
		}

		$this->model_admin_users->deleteUser($user_id);

		$this->respond_json(array('user_id' => (int)$user_id));
	}

	public function reset(){
		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/users');

		$user_id = $this->getInput('user_id');
		if(empty($user_id)){
			$this->respond_fail('argument_missing');
			return;
		}
		$current = $this->model_admin_users->loadCurrent();
		$userData = $this->model_admin_users->getUser($user_id);

		if($userData['store_id'] != $this->config->get('config_store_id') || $userData['user_type'] < 12){
			if(intval($current['user_type']) != 1){
				$this->respond_fail('access_forbidden');
				return;
			}
		}

		$password = _generateRandomString(10);

		$this->model_admin_users->setPassword($user_id, $password);

		$this->respond_json(array('user_id' => (int)$user_id, 'password' => $password));
	}

	public function change_status(){
		checkAccessGroup(AG_ADMIN);

		$this->load->model('admin/users');

		$user_id = $this->getInput('user_id');
		$status = $this->getInput('status', 'none');
		if(empty($user_id) or $status == 'none'){
			$this->respond_fail('argument_missing');
			return;
		}

		$userData = $this->model_admin_users->getUser($user_id);
		if($userData['store_id'] != $this->config->get('config_store_id')){
			$this->respond_fail('access_forbidden');
			return;
		}

		$this->model_admin_users->setStatus($user_id, $status);

		$this->respond_json(array('user_id' => (int)$user_id, 'status' => $status));
	}

	public function login(){

		$username = $this->getInput('username');
		$password = $this->getInput('password');

		if(empty($username) or empty($password)){
			$this->respond_fail('argument_missing');
			return;
		}

		$userData = $this->model_admin_users->getUserByName($username);

		if(!$userData){
			$this->respond_fail('user_not_found');
			return;
		}

		$success = $this->model_admin_users->checkPassword($userData['user_id'], $password);

		if($success){
			$token = $this->model_admin_users->generateToken($userData['user_id']);
			$this->respond_json(array('token' => $token));
		}else{
			$this->respond_fail('wrong_password');
		}
	}

	public function logout(){
		$token = $this->getInput('api_token');
		if(strlen($token) == 32){
			$this->respond_json('');
		}else{
			$this->respond_fail('invalid_argument');
		}
	}

}