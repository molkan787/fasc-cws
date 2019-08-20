<?php
// HTTP
//define('HTTP_SERVER', 'http://www.walkonretail.com/admin/');
//define('HTTP_CATALOG', 'http://www.walkonretail.com/');
define('HTTP_SERVER', 'http://fasc.local/admin/');
define('HTTP_CATALOG', 'http://fasc.local/');

// HTTPS
//define('HTTPS_SERVER', 'https://www.walkonretail.com/admin/');
//define('HTTPS_CATALOG', 'https://www.walkonretail.com/');
define('HTTPS_SERVER', 'http://fasc.local/admin/');
define('HTTPS_CATALOG', 'http://fasc.local/');

// Windows
// define('BASE_PATH', 'C:/xampp/htdocs/fasc/');

// Linux
define('BASE_PATH', '/P/Web/fasc/');

// DIR
define('DIR_APPLICATION', BASE_PATH . 'admin/');
define('DIR_SYSTEM', BASE_PATH . 'system/');
define('DIR_IMAGE',BASE_PATH . 'image/');
define('DIR_STORAGE', BASE_PATH . '../storage/');
define('DIR_CATALOG', BASE_PATH . 'catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
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
// define('DB_USERNAME', 'root');
// define('DB_PASSWORD', '123456');

// Linux
define('DB_USERNAME', 'm_user');
define('DB_PASSWORD', 'pwd123');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
