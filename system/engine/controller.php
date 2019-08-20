<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Controller class
*/
abstract class Controller {
	protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	protected function getInput($name, $default = ''){
        if(isset($_GET[$name])){
            return $_GET[$name];
        }else if(isset($_POST[$name])){
            return $_POST[$name];
        }else{
            return $default;
        }
    }

	protected function respond_json($data){
        $response = new stdClass();
        $response->status = 'OK';
        $response->lang = $this->config->get('config_language_id');
        $response->data = $data;
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($response));
		
	}
	
	protected function respond_fail($err_code){
        $response = new stdClass();
        $response->status = 'FAIL';
        $response->error_code = $err_code;
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($response));
    }

    protected function checkInputs($inputs){
        foreach ($inputs as $input) {
            if(strlen($input) < 1){
                die(json_encode(array('status' => 'FAIL', 'error_code' => 'argumnet_missing')));
            }
        }
    }
}