<?php
include_once 'dep.php';

class ControllerApiAccount extends Controller{

	public function changepassword(){

		$this->load->model('admin/users');

		$user = $this->model_admin_users->loadCurrent();

		if(!$user){
			$this->respond_fail('ERROR');
			return;
		}

		$user_id = $user['user_id'];
		$old = $this->getInput('old');
		$new = $this->getInput('new');

		$correct_pwd = $this->model_admin_users->checkPassword($user_id, $old);

		if($correct_pwd){
			$this->model_admin_users->setPassword($user_id, $new, true);
			$this->respond_json('');
		}else{
			$this->respond_fail('wrong_password');
		}

	}

	public function changeUsername(){
		$this->load->model('admin/users');

		$username = $this->getInput('username', '');

		if(strlen($username) < 6){
			$this->respond_fail('INVALID_INPUT');
			return;
		}

		$user = $this->model_admin_users->loadCurrent();

		if(!$user){
			$this->respond_fail('ERROR');
			return;
		}
		$user_id = $user['user_id'];

		$result = $this->model_admin_users->editUsername($user_id, $username);
		if($result == 0){
			$this->respond_json(array(
				'operation' => 'change_username',
				'user_id' => $user_id,
				'new_username' => $username
			));
		}else if($result == 1){
			$this->respond_fail('USERNAME_EXIST');
		}else if($result == 2){
			$this->respond_fail('INVALID_INPUT');
		}else{
			$this->respond_fail('UNKNOW_ERROR');
		}
	}

}