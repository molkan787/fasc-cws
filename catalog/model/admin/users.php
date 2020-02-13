<?php
include_once 'utils.php';

class ModelAdminUsers extends Model {

	public $currentUser = array();
	private $loaded = false;

	public function loadCurrent(){

		if($this->loaded){
			return $this->currentUser;
		}
		$this->loaded = true;

		$token = (isset($_GET['api_token']) ? $_GET['api_token'] : '');
		
		if(empty($token)) return null;

		$sql = "SELECT * FROM fasc_users_toks WHERE `token` = '".$this->db->escape($token)."' AND added_date > (NOW() - INTERVAL 30 DAY) LIMIT 1";
		$query = $this->db->query($sql);

		if($query->num_rows == 0) return null;;

		$user_id = $query->row['user_id'];

		$sql = "SELECT user_id, user_type, store_id, username, fullname FROM fasc_users WHERE user_id = '".(int)$user_id."' AND status = '1' LIMIT 1";
		$query = $this->db->query($sql);

		if($query->num_rows == 0) return null;;

		$user_type = $query->row['user_type'];
		$store_id = $query->row['store_id'];
		$username = $query->row['username'];
		$fullname = $query->row['fullname'];

		$sql = "SELECT * FROM fasc_users_access WHERE user_type = '".(int)$user_type."' LIMIT 1";
		$query = $this->db->query($sql);

		if($query->num_rows == 0) return null;;

		$this->currentUser['username'] = $username;
		$this->currentUser['user_type'] = $user_type;
		$this->currentUser['store_id'] = $store_id;
		$this->currentUser['user_id'] = $user_id;
		$this->currentUser['fullname'] = $fullname;
		$this->currentUser['ai'] = array();
		foreach ($query->row as $key => $value) {
			if(substr($key, 0, 3) == 'ai_'){
				$this->currentUser['ai'][substr($key, 3)] = $value;
			}else{
				$this->currentUser[$key] = $value;
			}
		}

		return $this->currentUser;
	}

	public function getUser($user_id){
		$sql = "SELECT user_id, user_type, store_id, status, fullname, username, added_date FROM fasc_users WHERE user_id = '".(int)$user_id."' LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getUserByName($username){
		$sql = "SELECT user_id, user_type, store_id, status, fullname, username, added_date FROM fasc_users WHERE username = '".$this->db->escape($username)."' LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getUsers($store_id){
		$sql = "SELECT user_id, user_type, store_id, status, fullname, username, added_date FROM fasc_users WHERE store_id = '".(int)$store_id."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function createUser($store_id, $user_type, $username, $password, $fullname){

		if((int)$user_type == 1) return false;

		$sql = "SELECT user_id FROM fasc_users WHERE username = '".$this->db->escape($username)."' LIMIT 1";
		$query = $this->db->query($sql);
		if($query->num_rows > 0) return false;

		$salt = generateRandomString(9);
		$password = md5($password.$salt);

		$sql = "INSERT INTO fasc_users SET `user_type` = '".(int)$user_type."', `store_id` = '".(int)$store_id."', `status` = 1";
		$sql .= ", `username` = '".$this->db->escape($username)."', `fullname` = '".$this->db->escape($fullname)."'";
		$sql .= ", `password` = '".$this->db->escape($password)."', `salt` = '".$this->db->escape($salt)."'";
		$sql .= ", `added_date` = NOW(), `modified_date` = NOW()";

		$this->db->query($sql);
		return $this->db->getLastId();
	}

	public function deleteUser($user_id){
		$sql = "SELECT user_type FROM fasc_users WHERE user_id = '".(int)$user_id."'";
		$query = $this->db->query($sql);
		if($query->num_rows == 0) return false;
		if(intval($query->row['user_type']) == 1) return false;

		$sql = "DELETE FROM fasc_users WHERE user_id = '".(int)$user_id."' AND user_type != 1";
		$query = $this->db->query($sql);

		return true;
	}

	public function setStatus($user_id, $status){
		$sql = "UPDATE fasc_users SET status = '".(int)$status."' WHERE user_id = '".(int)$user_id."' AND user_type != 1";
		$this->db->query($sql);
	}

	public function setPassword($user_id, $password, $force = false){
		$salt = generateRandomString(9);
		$password = md5($password.$salt);

		$sql = "SELECT user_type FROM fasc_users WHERE user_id = '".(int)$user_id."'";
		$query = $this->db->query($sql);
		if($query->num_rows == 0) return false;
		if(!$force AND intval($query->row['user_type']) == 1) return false;

		$sql = "UPDATE fasc_users SET `password` = '".$this->db->escape($password)."', `salt` = '".$this->db->escape($salt)."'";
		$sql .= ", modified_date = NOW() WHERE user_id = '".(int)$user_id."'";
		$this->db->query($sql);
		return true;
	}

	public function checkPassword($user_id, $password){

		$sql = "SELECT user_id, password, salt FROM fasc_users WHERE user_id = '".(int)$user_id."' AND status = 1";
		$query = $this->db->query($sql);
		if($query->num_rows == 0) return false;

		$salt = $query->row['salt'];
		$password = md5($password.$salt);

		if($password === $query->row['password']){
			return true;
		}else{
			return false;
		}
	}

	public function generateToken($user_id){
		$token = generateRandomString(32);
		$sql = "DELETE FROM fasc_users_toks WHERE user_id = '".(int)$user_id."'";
		$this->db->query($sql);
		$sql = "INSERT INTO fasc_users_toks SET user_id = '".(int)$user_id."', token = '".$this->db->escape($token)."'";
		$sql .= ", added_date = NOW()";

		$this->db->query($sql);
		return $token;
	}

	public function deleteToken($token){
		$sql = "DELETE FROM fasc_users_toks WHERE token = '".$this->db->escape($token)."'";
		$this->db->query($sql);
	}

	public function editUsername($user_id, $username){
		if(!ctype_alnum($username)) return 2; // ERROR 2 = Invalid input.
		$escaped = $this->db->escape($username);
		$sql = "SELECT user_id FROM fasc_users WHERE username = '".$escaped."'";
		$query = $this->db->query($sql);
		if($query->num_rows > 0){
			return 1; // ERROR 1 = Username already used.
		}
		$sql = "UPDATE fasc_users SET username = '".$escaped."' WHERE user_id = '".(int)$user_id."'";
		$this->db->query($sql);
		return 0; // Code 0 = Success
	}

}