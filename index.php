<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_include_path('./php/:'.get_include_path());
//require_once __DIR__.'/vendor/autoload.php';
require_once('libraries/eventhandler.php');
require_once('libraries/geographyhandler.php');
require_once('libraries/dbhandler.php');
require_once('libraries/userhandler.php');
require_once('libraries/tools.php');
require_once('libraries/htmlhandler.php');
require_once('login.php');

if(!empty($_SERVER ['DOCUMENT_ROOT'])) {
    define ('_SERVER_DIR', $_SERVER ['DOCUMENT_ROOT']);
    define ('_INI_FILE_DIR', _SERVER_DIR."/befalol/database/config.ini" );
}

$login = new Login();
?>
