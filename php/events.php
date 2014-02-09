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
    require_once('libraries/processing.php');
    
    
    session_start();
    define ('_SERVER_DIR', $_SESSION['_SERVER_DIR']);
    define ('_URL_PATH', $_SESSION['_URL_PATH']);
    define ('_INI_DB_CONFIG_FILE', $_SESSION['_INI_DB_CONFIG_FILE'] );
    define ('_INI_GEO_KEYS_CONFIG', $_SESSION['_INI_GEO_KEYS_CONFIG']);
    define ('_COMPOSER_FLAG', $_SESSION['_COMPOSER_FLAG']);
    
    if (_COMPOSER_FLAG == true) {
        require_once _SERVER_DIR.'/vendor/autoload.php';
    }
    
    $r = '';
    // if session initiated
    if (isset($_SESSION['user']))
    {
        $nationalities = NULL;
        $nationalities = Nationality::select_all_nationalities();
        // retrieve datas
               
        //we check if forms have been sent
        if (isset($_POST['postevent'])) {
            // if information given to the form is correct process to change
            if (save_event_info()) {
                //$r .= ($_SESSION['feedback']['msg']);
            // and if it is not, signal the user;
            }else {            
                $r .= ($_SESSION['feedback']['msg']);
                /*TODO  we should refill the form with datas*/
            }
        }
        // Build the page
        // Left pannel
        $r .= get_div('left_panel', '<h1>Filters</h1>'. 
            get_div('left_panel_box', 'Event types >') . 
            get_div('left_panel_box', 'Distance >') . 
            get_div('left_panel_box', 'Dates/Times >') .
            get_div('left_panel_box', 'Languages spoken >'));
        $events = Event::get_all_events();
        $event_types = Event::select_all_event_types();
        $_SESSION['events'] = $events;
        if (!empty($events) && (!empty($event_types))) {
            sort($event_types);
            $dump_r = '';
            
            $dump_r .= '<span class="title">Events</span>';
            $dump_r .= '<hr/>';
           
            // Table
            $dump_r .= '<table class="events_display">';
            $dump_r .= '<caption data-icon="v"> Event Information </caption>';
            $dump_r .= display_advanced_tr_row(array('Name', 'Type', 'Country', 'Check in', 'Check out'));
            foreach ($events as $event) {
                $event_id = $event->get_id();
                $event_name = utf8_decode($event->get_name());
                $event_type = utf8_decode($event->get_type());
                $event_location = $event->get_location();
                $event_starting_date = utf8_decode($event->get_starting_date());
                $event_ending_date = utf8_decode($event->get_ending_date());
                
                $location_infos = $event_location->get_location_infos();
                $dump_r .= display_advanced_row (array (
                '<a href="event.php?id='.$event_id.'">'.$event_name.'</a>', 
                $event_type, $location_infos['country'], $event_starting_date, 
                $event_ending_date));
               
            }
            $dump_r .= '</table>';
            // add the form to the existing html stream
            $r .= get_div('contentarea',$dump_r);
            
        }else {
            $r .= 'he form could not have been loaded';
            //$this->feedback = "The form could not have been loaded";
        }
    } else {
        header('Location: ../index.php'); 
    }
    
    
  
    /*if ($this->feedback) {
        $r .= $this->feedback . "<br/><br/>";
    }*/
    $r .= '<a href="userpage.php">Profile Page</a>'.'<br/>';
    $r .= '<a href="eventposting.php">Post an Event</a>'.'<br/>';
    $r .= '<a href="myevents.php">My events</a>'.'<br/>';
    $r .= '<a href="events.php">List of all events</a>'.'<br/>';
    $r .= '<a href="../index.php?action=logout">Log out</a><br/>';

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
