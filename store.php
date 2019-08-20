<?php

$store_inited = false;
function store_init($config){
	global $store_inited;
	if($store_inited) return;
	$store_inited = true;

	$config->set('config_store_id', 1);
}

function store_getID(){
	return 1;
}