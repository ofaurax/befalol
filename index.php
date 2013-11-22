<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_include_path('./php/:'.get_include_path());
//require_once __DIR__.'/vendor/autoload.php';
require_once('eventshandler.php');
require_once('libraries/php-login-master/login.php');
session_start();

//$Aldeen = new Member ('001', 'Aldeen', date(DATE_RFC2822));
//$Olivier = new Member ('002', 'Olivier', date(DATE_RFC2822));
//$Marta = new Member ('003', 'Marta', date(DATE_RFC2822));

//$parameters = array ('id'=>'001', 'location'=>'RANDOM ADDRESS', 'type'=>'Activities', 'starting_date'=>date(DATE_RFC2822), 'ending_date'=>date(DATE_RFC2822),'holder'=>$Aldeen,'max_nb_participants'=>8, 'participants' => array($Olivier, $Marta), 'languages'=> 'English', 'description'=>'Give it a try');

//$first_event = new Event ($parameters);
//echo $first_event->render()
?>
<!DOCTYPE html>
<html>
<head>
  <title>Befalol Index</title>
</head>
<h1>Welcome to Befalol!</h1>
<p><?php echo date(DATE_RFC2822);  ?></p>
<p>Olivier and Aldeen were here.</p>
<p>Try number1: Event. </p>
<!-- <a href="http://localhost/befalol/php/events.php" title="Register" target="_blank">Register an event</a>
<a href="http://localhost/befalol/php/login.php" title="Login" target="_blank">Log yourself in</a> -->

</html>

<?php
	$login = new Login();
?>


