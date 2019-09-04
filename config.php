<?php

define('DEV', true);

if(DEV){
    define('HTTP_SERVER', 'http://fasc.local/');
    define('HTTPS_SERVER', 'http://fasc.local/');
}else{
    define('HTTP_SERVER', 'http://www.walkonretail.com/');
    define('HTTPS_SERVER', 'https://www.walkonretail.com/');
}

// Windows
// define('BASE_PATH', 'C:/xampp/htdocs/');
// define('BASE_PATH_WB', 'C:/xampp/htdocs/fasc/');

// Linux
define('BASE_PATH', 'C:/xampp/htdocs/');
define('BASE_PATH_WB', 'C:/xampp/htdocs/fasc/');

// DIR
define('DIR_APPLICATION', BASE_PATH_WB . 'catalog/');
define('DIR_SYSTEM', BASE_PATH_WB . 'system/');
define('DIR_IMAGE', BASE_PATH_WB . 'image/');
define('DIR_STORAGE', BASE_PATH . 'storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_DATABASE', 'wor');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// Windows
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

// Linux
// define('DB_USERNAME', 'm_user');
// define('DB_PASSWORD', 'pwd123');