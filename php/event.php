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
    require_once('login.php');
    
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
        $admin_flag = false;
        // retrieve datas      
        //we check if an event id have been sent
        if (isset($_GET['id'])) {
            // retrieve event id and user id
            try {
                $event_id = $_GET["id"];
                $user_id = $_SESSION['user']->get_user_id();
            } catch (Expection $e) {
                 echo 'Exception received : ',  $e->getMessage(), "\n";
            }
            if (!empty($event_id) && !empty($user_id)) {
                try {
                    // retrieve the event informations to display
                    $event = Event::get_event_from_id($event_id);
                    $admins_ids = $event->get_holders_ids();
                    $admins_names = array();
                    // retrieve event admins information
                    foreach ($admins_ids as $admin_id) {
                        $event_admin = User::get_user_from_id($admin_id);
                        //create a link to admins pages
                        array_push($admins_names, 
                        '<a href="userpage.php?id='.$admin_id.'">'
                        .$event_admin->get_string_attribute('user_name').'</a>');
                    }
                } catch (Expection $e) {
                     echo 'Exception received : ',  $e->getMessage(), "\n";
                }
                // check whether user is the event admin or not
                if  (in_array($user_id, $event->get_holders_ids())) {
                    $admin_flag=true;
                }
            } else {
                echo "We were not able to load the datas, please accept our 
                appologies and contact the webmaster";
            }
            
           
        }
        // Build the page
        // Left pannel
        $r .= get_div('left_panel', '<h1>Events</h1>'. 
            get_div('left_panel_box', 'Activities >') . get_div('left_panel_box', 'Visits >')
            . get_div('left_panel_box', 'Journeys >'). get_div('left_panel_box', 'Parties >'));
        $dump_r = '';
        
        // Title
        $dump_r .= '<span class="title">'.$event->get_name().'</span>';
        $dump_r .= '<hr/>';
        // Form
        $event_location = $event->get_location()->get_location_infos();
        // Table
        $dump_r .= '<table>';
        $dump_r .= '<caption data-icon="v"> Event Information </caption>';
        $dump_r .= display_row('<label> Location </label>',
        	'<label>'.implode(', ', array ($event_location['street_name'], 
            $event_location['zipcode'], $event_location['city'], 
            $event_location['country'])).'</label>');
        $dump_r .= display_row('<label> Check in date </label>',
        	'<label>'.$event->get_starting_date().'</label>');
        $dump_r .= display_row('<label> Check out date </label>',
        	'<label>'.$event->get_ending_date().'</label>');
        $dump_r .= display_row('<label> Participants </label>',
        	'<label>'.$event->get_current_participants_nb().'/'.$event->get_max_nb_participants().'</label>');
		$dump_r .= display_row('<label> Languages </label>', implode(', ', $event->get_languages()));
		$dump_r .= display_row('<label> Event Admins </label>', implode(', ',$admins_names));
		$dump_r .= display_row('Description:', $event->get_description());
		if ($admin_flag == True) {
		    $dump_r .= display_row('', '<a href="myevents.php?id='.$event_id.'">
		    <input type="submit" value="Edit Event" name="editevent"/></a>');
		}
        $dump_r .= '</table>';
        //add the form to the existing html stream
        $r .= get_div('contentarea',$dump_r);
    } else {
        header('Location: ../index.php'); 
    }
    
    
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
<style type="text/css"> 
</style> 
	<body>
    	<?php  echo topbar_user();?>
    	<div id="container">
    		<div id="content">
			<?php  echo $r;?>
    		</div> <!-- end content -->
    	</div> <!-- end container -->
	</body>
	<?php  echo get_footer();?>
        