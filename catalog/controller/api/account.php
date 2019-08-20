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
			$this->model_admin_users->setPassword($user_id, $new);
			$this->respond_json('');
		}else{
			$this->respond_fail('wrong_password');
		}

	}

}