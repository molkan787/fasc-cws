<?php

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

$fp = fopen('log.txt', 'a');
fwrite($fp, 'Log: ' . $msg . "\r\n");
fclose($fp);

if($msg == '1'){
	echo "Happy coding!!";
}
