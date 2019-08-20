<?php
class ControllerAccountVerify extends Controller {
	public function index() {

		$this->load->language('account/verify');

		if(isset($this->request->post["otp"])){
			if($this->verify()){

			}else{
				$data['error_warning'] = $this->language->get('text_invalid_code');
			}
		}

		if(isset($_GET["sn"]) && $this->customer->isLogged()){
			$customer_id = $this->customer->getId();
			$phone = $this->customer->getTelephone();
			$this->load->model('account/otp');
			$verify_token = $this->model_account_otp->sendCode($customer_id, $phone);

			$this->response->redirect($this->url->link('account/verify&vt=' . $verify_token . "&phone=" . $phone));
		}


		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_verification'),
			'href' => $this->url->link('account/verify')
		);


		if (isset($_GET['phone'])) {
			$data['phone'] = $_GET['phone'];
		} else {
			$data['phone'] = '';
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/verify', $data));
	}

	private function verify(){

		if(isset($_GET["phone"]) && isset($_GET["vt"])){

			$code = $this->request->post["otp"];
			$phone = $_GET["phone"];
			$token = $_GET["vt"];

			if(strlen($code) != 6 || strlen($phone) < 9 || strlen($token) != 8 ){
				return false;
			}

			$this->load->model('account/customer');
			$this->load->model('account/otp');

			$customer_id = $this->model_account_otp->checkCode($phone, $token, $code);

			if(intval($customer_id) == 0){
				return false;
			}

			$this->model_account_customer->verifyCustomer($customer_id);
			
			if($this->customer->isLogged()){
				$this->response->redirect($this->url->link('account/success'));
			}else{

				$customer = $this->model_account_customer->getCustomer($customer_id);
				$this->customer->login($customer['email'], 'basic_password');

				$this->response->redirect($this->url->link('common/home'));
			}
			
		}else{
			return false;
		}

	}
}