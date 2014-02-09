<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_include_path('./php/:'.get_include_path());

require_once('libraries/eventhandler.php');
require_once('libraries/geographyhandler.php');
require_once('libraries/dbhandler.php');
require_once('libraries/userhandler.php');
require_once('libraries/tools.php');
require_once('libraries/htmlhandler.php');
require_once('libraries/locationhandler.php');
require_once('libraries/login.php');
require_once('libraries/processing.php');

define ('_SERVER_DIR', getcwd());
define ('_URL_PATH', $_SERVER['SERVER_NAME'].trim($_SERVER['REQUEST_URI'], 'index.php'));
define ('_INI_DB_CONFIG_FILE', _SERVER_DIR."/ini/db_config.ini" );
define ('_INI_GEO_KEYS_CONFIG', _SERVER_DIR."/ini/geoloc_keys.ini");

/* Uncomment the line below for COMPOSER using */
define ('_COMPOSER_FLAG', false);
//define ('_COMPOSER_FLAG', true);

if (_COMPOSER_FLAG == true) {
    require_once __DIR__.'/vendor/autoload.php';
}

/** MAKE SURE YOU HAVE INSTALLED WILLDURAND/GEOCODER LIBRARY **/
$login = new Login();
?>
