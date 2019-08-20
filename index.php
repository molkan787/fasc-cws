<?php
header("Access-Control-Allow-Origin: *");
/*if(substr($_SERVER["HTTP_HOST"], 0, 4) != 'www.'){
	 header("Location: https://www." . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
}

//If the HTTPS is not found to be "on"
if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on")
{
    //Tell the browser to redirect to the HTTPS URL.
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
    //Prevent the rest of the script from executing.
    exit;
}*/
require_once 'vendor/autoload.php';

// Version
define('VERSION', '3.0.2.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}
// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');