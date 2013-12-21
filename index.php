<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_include_path('./php/:'.get_include_path());
//require_once __DIR__.'/vendor/autoload.php';
require_once('eventhandler.php');
require_once('geographyhandler.php');
require_once('dbhandler.php');
require_once('formhandler.php');
require_once('userhandler.php');
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
<link rel="stylesheet" type="text/css" href="css/style.css">
<title>Befalol Index</title>
</head>
<h1>Welcome to Befalol!</h1>
<p>
<?php echo date(DATE_RFC2822);  ?>
</p>

<?php
    //$dbhandler = new SqliteDbTableHanlder(db_parser (_INI_FILE_DIR,_SERVER_DIR));
    //$dbhandler->delete_all_tables();
    //$dbhandler->create_tables();
    //$dbhandler->db_disconnect();
    /*$geocoder = new \Geocoder\Geocoder();
     $adapter  = new \Geocoder\HttpAdapter\BuzzHttpAdapter();
     $chain = new \Geocoder\Provider\ChainProvider(array(
     new \Geocoder\Provider\FreeGeoIpProvider($adapter),
     new \Geocoder\Provider\HostIpProvider($adapter),
     new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
     new \Geocoder\Provider\BingMapsProvider($adapter, '<API_KEY>'),
    
     ));
     $geocoder->registerProvider($chain);
    
     try {
         $geocode = $geocoder->geocode('78.228.245.112');
         var_export($geocode);
     } catch (Exception $e) {
     echo $e->getMessage();
     }*/
    $login = new Login();
?>