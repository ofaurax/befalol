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
    require_once('login.php');
    
    if(!empty($_SERVER ['DOCUMENT_ROOT'])) {
    define ('_SERVER_DIR', $_SERVER ['DOCUMENT_ROOT']);
    define ('_INI_FILE_DIR', _SERVER_DIR."/befalol/database/config.ini" );    
    }
    
    session_start();
    $r = '';
    // if session initiated
    if (isset($_SESSION['user']))
    {
        $nationalities = NULL;
        $nationalities = Nationality::select_all_nationalities();
        // retrieve datas
               
        // Build the page
        // Left pannel
        $r .= get_div('left_panel', '<h1>Events</h1>'. 
            get_div('left_panel_box', 'Activities >') . get_div('left_panel_box', 'Visits >')
            . get_div('left_panel_box', 'Journeys >'). get_div('left_panel_box', 'Parties >'));
        $dump_r = '';
        $user = $_SESSION['user'];
        $user_events = $_SESSION['user_events'];
        $dump_r .= '<span class="title">Events</span>';
        $dump_r .= '<hr/>';
           
        // Table
        $dump_r .= '<table class="events_display">';
        $dump_r .= '<caption data-icon="v"> Event Information </caption>';
        $dump_r .= display_advanced_tr_row(array('Name', 'Type', 'Country', 'Check in', 'Check out'));
        foreach ($user_events as $event) {
            $event_id = $event->get_id();
            $event_name = utf8_decode($event->get_name());
            $event_type = utf8_decode($event->get_type());
            $event_country_name = utf8_decode($event->get_location());
            $event_starting_date = utf8_decode($event->get_starting_date());
            $event_ending_date = utf8_decode($event->get_ending_date());
            $dump_r .= display_advanced_row (array (
            '<a href="/befalol/php/event.php?id='.$event_id.'">'.$event_name.'</a>', 
            $event_type, $event_country_name, $event_starting_date, $event_ending_date));
        }
        $dump_r .= '</table>';
        // add the form to the existing html stream
        $r .= get_div('contentarea',$dump_r);
    } else {
        header('Location: ../index.php'); 
    }
    
    
  
    /*if ($this->feedback) {
        $r .= $this->feedback . "<br/><br/>";
    }*/
    $r .= '<a href="/befalol/php/userpage.php">Profile Page</a>'.'<br/>';
    $r .= '<a href="/befalol/php/eventposting.php">Post an Event</a>'.'<br/>';
    $r .= '<a href="/befalol/php/myevents.php">My events</a>'.'<br/>';
    $r .= '<a href="/befalol/php/events.php">List of all events</a>'.'<br/>';
    $r .= '<a href="/befalol/index.php?action=logout">Log out</a><br/>';

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="../css/backstyle.css">
	<title>Befalol Index</title>
</head>
	<body>
    	<?php  echo topbar_user();?>
    	<div id="container">
    		<div id="content">
    		<?php  echo $r;?>
    		</div> <!-- end content -->
    	</div> <!-- end container -->
	</body>
	<?php  echo get_footer();?>
