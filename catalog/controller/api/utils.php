<?php


function url($path){
	//return str_replace('fasc.local', '192.168.1.5/fasc', $path);
	$base_link = HTTPS_SERVER;
	if(substr($path, 0, 4) == 'http') return $path;
	return $base_link.$path;
}

function _generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}