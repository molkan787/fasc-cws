<?php

$store = array(
	'slug' => 'd'
);

function store_getSlug(){
	global $store;
	return $store['slug'];
}