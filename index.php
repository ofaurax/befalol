<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/vendor/autoload.php';
require_once ('./eventshandler.php');

$Aldeen = new Member ('001', 'Aldeen', date(DATE_RFC2822));
$Olivier = new Member ('002', 'Olivier', date(DATE_RFC2822));
$Marta = new Member ('003', 'Marta', date(DATE_RFC2822));

$parameters = array ('id'=>'001', 'location'=>'RANDOM ADDRESS', 'type'=>'Activities', 'StartingDate'=>date(DATE_RFC2822), 'EndingDate'=>date(DATE_RFC2822),'holder'=>$Aldeen,'MaxNbParticipants'=>8, 'participants' => array($Olivier, $Marta), 'languages'=> 'English', 'description'=>'Give it a try');

$FirstEvent = new Event ($parameters);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Befalol Index</title>
</head>
<h1>Welcome to Befalol!</h1>
<p><?php echo date(DATE_RFC2822);  ?></p>
<p>Olivier was here.</p>
<p>Aldeen is now trying to create an event.</p>
<p>Try number1: Event.</p>
<? echo $FirstEvent->render(); ?>
</html>