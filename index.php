<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_include_path('./php/:'.get_include_path());
//require_once __DIR__.'/vendor/autoload.php';
require_once('eventshandler.php');
require_once('geographyhandler.php');
require_once('dbhandler.php');
require_once('formhandler.php');
require_once('usershandler.php');
require_once('libraries/tools.php');
require_once('login.php');

if(!empty($_SERVER ['DOCUMENT_ROOT'])) {
	define ('_SERVER_DIR', $_SERVER ['DOCUMENT_ROOT']);
	define ('_INI_FILE_DIR', _SERVER_DIR."/befalol/database/config.ini" );
}

session_start();

?>
<!DOCTYPE html>
<html>
<head>
  <title>Befalol Index</title>
</head>
<h1>Welcome to Befalol!</h1>
<p><?php echo date(DATE_RFC2822);  ?></p>

<?php
	//$dbhandler = new SqliteDbTableHanlder(db_parser (_INI_FILE_DIR,_SERVER_DIR));
	//$dbhandler->delete_all_tables();
	//$dbhandler->create_tables();
	//$dbhandler->db_disconnect();
	$login = new Login();
?>


