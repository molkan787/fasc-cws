<?php

define('AG_MASTER_ADMIN', 1);
define('AG_MASTER_USER_1', 2);
define('AG_MASTER_USER_2', 3);
define('AG_MASTER_USER_3', 4);

define('AG_ADMIN', 11);
define('AG_USER_1', 12);
define('AG_USER_2', 13);
define('AG_USER_3', 14);

define('AI_STORES', 'stores');
define('AI_USERS', 'users');
define('AI_POS', 'pos');
define('AI_DASHBOARD', 'dashboard');
define('AI_ORDERS', 'orders');
define('AI_PRODUCTS', 'products');
define('AI_CUSTOMERS', 'customers');
define('AI_CATEGORIES', 'categories');
define('AI_BANNERS', 'banners');
define('AI_OTHER', 'other');

define('AL_READ_WRITE', 2);
define('AL_READ', 1);

function checkAccess($arg){

}

function _checkAccess($ai, $al){
	$cua = $GLOBALS['modelUsers']->loadCurrent();

	if($cua){
		$aic = $cua['ai'][$ai];
		if($aic >= $al){
			return true;
		}

	}

	die(json_encode(array('status' => 'FAIL', 'error_code' => 'access_forbidden')));
}

function checkAccessGroup($ag){
	return true;
	$cua = $GLOBALS['modelUsers']->loadCurrent();
	if($cua['user_type'] == $ag or $cua['user_type'] == AG_MASTER_ADMIN){
		return true;
	}
	die(json_encode(array('status' => 'FAIL', 'error_code' => 'access_forbidden')));
}