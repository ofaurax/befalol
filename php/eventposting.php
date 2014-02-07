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
    
    if(!empty($_SERVER ['DOCUMENT_ROOT'])) {
        define ('_SERVER_DIR', $_SERVER ['DOCUMENT_ROOT']);
        define ('_INI_DB_CONFIG_FILE', _SERVER_DIR."/befalol/ini/db_config.ini");
        define ('_INI_GEO_KEYS_CONFIG', _SERVER_DIR."/befalol/ini/geoloc_keys.ini");
    }
    
    require_once _SERVER_DIR .'/befalol/vendor/autoload.php';
    
    session_start();
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
        $r .= get_div('left_panel', '<h1>Events</h1>'. 
            get_div('left_panel_box', 'Activities >') . get_div('left_panel_box', 'Visits >')
            . get_div('left_panel_box', 'Journeys >'). get_div('left_panel_box', 'Parties >'));
        $event_types = NULL;
        $languages = NULL;
        $event_types = Event::select_all_event_types();
        $languages = Language::select_all_languages();
        $countries = Country::select_all_countries();
        if (!empty($event_types) && !empty($languages) && !empty($countries)) {
            sort($languages);
            sort($event_types);
            sort($countries);
            $dump_r = '';
            
            // Title
            $dump_r .= '<span class="title">Post a new event</span>';
            $dump_r .= '<hr/>';
            // Form
            $dump_r .=  '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] .
            '" name="posteventform">';
            
            // Table
            $dump_r .= '<table>';
            $dump_r .= '<caption data-icon="v"> Event Information </caption>';
            $dump_r .= display_row('Event Name:', '<input type="text"
					name="event_name" placeholder="My new awesome event" required/>');
            $dump_r .= display_row('Type', display_dropdownlist (array('name' =>
				'event_type', 'multiple' => FALSE, 'required' => TRUE) , 
            $event_types, '', 'event_types'));
            $dump_r .= display_row('Location:', '<input type="text"
				name="event_address" placeholder="1, Lombard Street" 
				size=50 required/> <input type="text" placeholder="94133"
				name="event_zipcode" required/> <input type="text" 
				name="event_city" size="20" placeholder="San Francisco" required/>'
				.display_dropdownlist(array('multiple' => false, 'required' =>
				true, 'name' => 'event_country_name'), $countries, 'United States','countries'));
			$dump_r .= display_row('Check in:', '<input type="date"
			name="event_starting_date" placeholder="mm/dd/yyyy" size="10" 
			required/>'.' Time :'. ' <input type="time" name="event_starting_time" 
			placeholder="hh:mm" size="8" max="23:00" required/>');
			$dump_r .= display_row('Check out:', '<input type="date"
			name="event_ending_date" placeholder="mm/dd/yyyy" size="10" required/>'.
			' Time :'.' <input type="time" name="event_ending_time" size="8" 
			placeholder="hh:mm" max="23:00" required/>');
			$dump_r .= display_row('Maximal number of participants:',
			'<input type="number" size="2" min="1" value="1"  
			name="event_max_nb_participants" required/>');
			$dump_r .= display_row('Languages spoken :',
			display_dropdownlist(array('multiple' => true, 'required' =>
			true, 'name' => 'event_spoken_languages[]'), $languages, 'English', 'languages'));
			$dump_r .= display_row('Description:',
			'<textarea type="text" name="event_description" maxlength="4000" 
			cols="30" row="10" placeholder="This is going to be mad!" 
			required/></textarea>');
			$dump_r .= display_row('', '<input type="submit" value =
			"Create Event" name="postevent"/>');
            $dump_r .= '</table>';
            $dump_r .= '</form>';
            
            
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

    $r .= '<a href="/befalol/php/userpage.php">Profile Page</a>'.'<br/>';
    $r .= '<a href="/befalol/php/event.php">Post an Event</a>'.'<br/>';
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
