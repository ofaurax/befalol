<?php 

/**
 *
 * Check if input personal informations match the requirements
 */
 function check_user_info()
{
    $feedback = array ();
 
    // validating the input
    if (!empty($_POST['user_name'])
    && strlen($_POST['user_email']) <= 64
    && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
    && check_no_digit($_POST['user_lastname'], false)
    && strlen($_POST['user_lastname']) <= 64
    && check_no_digit($_POST['user_firstname'], false)
    && strlen($_POST['user_firstname']) <= 64
    && check_no_digit($_POST['user_nationality'], false)
    && check_no_digit($_POST['user_gender'], false)
    && check_and_valid_date($_POST['user_birthday'], False)        
    && (!is_it_futur ($_POST['user_birthday']))) {
        $feedback['status'] = true;
        $_SESSION['feedback'] = $feedback;
        return true;     
    } elseif (empty($_POST['user_email'])) {
        $feedback['msg'] = "Email cannot be empty";
    } elseif (strlen($_POST['user_email']) > 64) {
        $feedback['msg'] = "Email cannot be longer than 64 characters";
    } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
        $feedback['msg'] = "Your email address is not in a valid email format";
    } elseif (!check_no_digit($_POST['user_lastname'], false)){
        $feedback['msg'] = "Your lastname doesn't match the field requirements";
    } elseif (strlen($_POST['user_lastname']) > 64) {
        $feedback['msg'] = "Your Lastname cannot be longer than 64 characters or 
        shorter than 2 characters";
    } elseif (!check_no_digit($_POST['user_firstname'], false)){
        $feedback['msg'] = "Your firstname doesn't match the field requirements";
    } elseif (strlen($_POST['user_firstname']) > 64) {
        $feedback['msg'] = "Firstname cannot be longer than 64 characters";
    } elseif (!check_and_valid_date($_POST['user_birthday'], 0)){
        $feedback['msg'] = "Your birthday doesn't match the field requirements
         - mm/dd/yyyy - or is not a valid date";
    } elseif (is_it_futur($_POST['user_birthday'], 0)){
        $feedback['msg'] = "You probably would not be using this website 
        if you were not born :).";
    } elseif (!check_no_digit($_POST['user_nationality'], false)){
        $feedback['msg'] = "Your nationality doesn't match the field requirements";
    } elseif (!check_no_digit($_POST['user_gender'], false)){
        $feedback['msg'] = "Your gender is incorrect";
    } else {
        $feedback['msg'] = "An unknown error occurred.";
    }
    $feedback['status'] = false;
    $_SESSION['feedback'] = $feedback;
    // default return
    return false;
}


/**
 *
 * Save input personal informations into db and Session
 */
function save_user_info ()
{
    if (check_user_info()){
        $feedback = array ();
        // Retrieve datas from form
        $user_name = filter_var($_POST['user_name'],
         FILTER_SANITIZE_STRING);
        //$user_password = $_POST['user_password'];
        $user_email = filter_var($_POST['user_email'], 
        FILTER_SANITIZE_EMAIL);
        $user_birthday = filter_var($_POST['user_birthday'], 
        FILTER_SANITIZE_STRING);
        $user_nationality = utf8_encode(filter_var($_POST['user_nationality'], 
        FILTER_SANITIZE_STRING));
        $user_lastname = utf8_encode(filter_var($_POST['user_lastname'], 
        FILTER_SANITIZE_STRING));
        $user_firstname = utf8_encode(filter_var($_POST['user_firstname'], 
        FILTER_SANITIZE_STRING));
        $user_gender = utf8_encode(filter_var($_POST['user_gender'], 
        FILTER_SANITIZE_STRING));
        $user_password_hash = 
            $_SESSION['user']->get_string_attribute('user_password_hash');
       
        /*if (isset($_POST['user_password_new'])) {
            $user_password_hash = password_hash($_POST['user_password_new'],
             PASSWORD_DEFAULT);
        }else {
            $user_password_hash = 
            $_SESSION['user']->get_string_attribute('user_password_hash');
        }*/
        	
        //Create an user object with it
        $parameters =  array ('user_id' =>
        $_SESSION['user']->get_string_attribute('user_id'),
			'user_name' => $user_name,
			'user_email' => $user_email,
			'user_birthday' => $user_birthday,
			'user_nationality' => $user_nationality,
			'user_lastname' => $user_lastname,
			'user_firstname' => $user_firstname,
            'user_password_hash' => $user_password_hash,
        	'user_gender' => $user_gender);
        $user = new User ($parameters);
        	
        // save new datas in database;
        if ($user->update_user_data()) {
            // Save them in the user session
            $_SESSION['user'] = $user;
            $feedback ['status'] = true;
            $_SESSION['feedback'] = $feedback;
            return true;
        } else {
            $feedback ['msg'] = 'The update of your profile information has failed';
            $feedback ['status'] = false;
            $_SESSION['feedback'] = $feedback;
            return false;
        }
    }else {
        return false;
    }
}


/**
 * 
 * Check if information given by user and related to password respects the
 * requirements
 */
function check_user_pwd () {
    $feedback = array ();
	if (!empty($_POST['user_password_new']) && !empty($_POST['user_password_repeat']) 
	    && !empty($_POST['user_password'])
	    && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
	    && password_verify($_POST['user_password'], 
        $_SESSION['user']->get_string_attribute('user_password_hash'))
        && !password_verify($_POST['user_password_new'], 
        $_SESSION['user']->get_string_attribute('user_password_hash'))
        && strlen($_POST['user_password_new']) > 6 ) {
        $feedback['status'] = true;
        $_SESSION['feedback'] = $feedback;
        return true;
    } else if (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat']) 
	    || empty($_POST['user_password'])) {
	    $feedback['msg'] = "Please fill in all the required fields.";
	} else if ($_POST['user_password_new'] != $_POST['user_password_repeat']) {
	    $feedback['msg'] = "Please make sure that both passwords are similar.";
	} else if (!password_verify($_POST['user_password'], 
        $_SESSION['user']->get_string_attribute('user_password_hash'))) {
	    $feedback['msg'] = "Your current password is not valid.";
	} else if (password_verify($_POST['user_password_new'], 
        $_SESSION['user']->get_string_attribute('user_password_hash'))) {
        $feedback['msg'] = "Your new password must differ from the current one";
    } else if (strlen($_POST['user_password_new']) < 6 ) {
        $feedback['msg'] = "Password has a minimum length of 6 characters";
    } else {
        $feedback['msg'] = "Unknown error, please try again";
    }
    $feedback['status'] = false;
    $_SESSION['feedback'] = $feedback;
    return false;    
}


/**
 * 
 * Save input new password into db
 **/ 
function save_user_pwd () {
    if (check_user_pwd()){
        $feedback = array ();
        // Retrieve datas from form
        /* information in user object are already utf8-encoded so no need here 
         to decode and encode again */
        $user_name = $_SESSION['user']->get_string_attribute('user_name');
        //$user_password = $_POST['user_password'];
        $user_email = $_SESSION['user']->get_string_attribute('user_email');
        $user_birthday = $_SESSION['user']->get_string_attribute('user_birthday');
        $user_nationality = utf8_encode($_SESSION['user']->get_string_attribute('user_nationality'));
        $user_lastname = utf8_encode($_SESSION['user']->get_string_attribute('user_lastname'));
        $user_firstname = utf8_encode($_SESSION['user']->get_string_attribute('user_firstname'));
        $user_gender = utf8_encode($_SESSION['user']->get_string_attribute('user_gender'));
        $user_password_hash = password_hash($_POST['user_password_new'],
             PASSWORD_DEFAULT);          
        	
        //Create an user object with datas
        $parameters =  array ('user_id' =>
        $_SESSION['user']->get_string_attribute('user_id'),
			'user_name' => $user_name,
			'user_email' => $user_email,
			'user_birthday' => $user_birthday,
			'user_nationality' => $user_nationality,
			'user_lastname' => $user_lastname,
			'user_firstname' => $user_firstname,
            'user_password_hash' => $user_password_hash,
        	'user_gender' => $user_gender);
        $user = new User ($parameters);
        	
        // save new datas in database;
        if ($user->update_user_data()) {
            // Save them in the user session
            $_SESSION['user'] = $user;
            $feedback ['msg'] = 'Your password has been successfully changed';
            $feedback ['status'] = true;
            $_SESSION['feedback'] = $feedback;
            return true;
        } else {
            $feedback ['msg'] = 'Your password update has failed';
            $feedback ['status'] = false;
            $_SESSION['feedback'] = $feedback;
            return false;
        }
    }else {
        return false;
    }
    
}

/**
 *
 * Check if input event informations match the requirements
 */
function check_event_info()
{
    $feedback = array ();                
    // validating the input
    if (!empty($_POST['event_name'])
    && (strlen($_POST['event_name']) > 3)
    && preg_match('#[\w]+#', $_POST['event_name'])
    && check_no_digit($_POST['event_type'], true)
    && (!empty($_POST['event_zipcode']))
    && check_no_digit($_POST['event_city'], true)
    && check_no_digit($_POST['event_country_name'], true)
    && data_validation($_POST['event_max_nb_participants'],
        FILTER_SANITIZE_NUMBER_INT, true)
    && check_and_valid_date($_POST['event_starting_date'], true)
    && check_and_valid_date($_POST['event_ending_date'], true)
    && (is_it_futur ($_POST['event_starting_date'].' '
        .$_POST['event_starting_time']))
    && (is_it_futur ($_POST['event_ending_date'].' '
        .$_POST['event_ending_time']))
    && check_and_valid_time($_POST['event_starting_time'], true)
    && check_and_valid_time($_POST['event_ending_time'], true)
    && data_validation($_POST['event_description'],
        FILTER_SANITIZE_STRING, true)
    && array_check_no_digit($_POST['event_spoken_languages'],true)) {
        $feedback['status'] = true;
        $_SESSION['feedback'] = $feedback;
        return true;
    } elseif (empty($_POST['event_name'])) {
        $feedback ['msg'] = "Event name cannot be empty";
    } elseif (strlen($_POST['event_name']) < 3) {
        $feedback ['msg'] = "Event name must be at least 3 characters long";
    } elseif (!check_no_digit($_POST['event_type'], true)) {
        $feedback['msg'] = "Event type cannot be empty or contain digit";
    } elseif (empty($_POST['event_address']) || empty($_POST['event_zipcode'])
    || empty($_POST['event_city']) || empty($_POST['event_country_name'])) {
        $feedback['msg'] = "Event location cannot be empty";
    } elseif (!check_no_digit($_POST['event_city'], true)) {
        $feedback['msg'] = "Event city is not a valid city";
    } elseif (!check_no_digit($_POST['event_country_name'], true)) {
        $feedback['msg'] = "Event country is not a valid country";
    }elseif (!data_validation($_POST['event_max_nb_participants'],
        FILTER_SANITIZE_NUMBER_INT, true)) {
        $feedback['msg'] = "Maximum number of participant must be a digit";
    } elseif (empty($_POST['event_starting_date'])) {
        $feedback['msg'] = "Event check in cannot be empty";
    } elseif (empty($_POST['event_ending_date'])) {
        $feedback['msg'] = "Event check out cannot be empty";
    } elseif (empty($_POST['event_description'])) {
        $feedback['msg'] = "Event description cannot be empty";
    } elseif (empty($_POST['event_spoken_languages'])) {
        $feedback['msg'] = "Event languages spoken cannot be empty";
    } elseif (empty($_POST['event_starting_time'])) {
        $feedback['msg'] = "Event check in time cannot be empty";
    } elseif (empty($_POST['event_ending_time'])) {
        $feedback['msg'] = "Event check out time cannot be empty";
    } elseif (!check_and_valid_date($_POST['event_starting_date'], true)){
        $feedback['msg'] = "The event check in date doesn't match the field
		requirements - mm/dd/yyyy - or is not a valid date";
    } elseif (!check_and_valid_date($_POST['event_ending_date'], true)){
        $feedback['msg'] = "The event check out date doesn't match the field
		requirements - mm/dd/yyyy - or is not a valid date";
    } elseif (!is_it_futur($_POST['event_starting_date'].' '
    .$_POST['event_starting_time'], true)){
        $feedback['msg'] = "I am not sure the time machine exists yet,
		please make sure the event check in date is valid.";
    } elseif (!is_it_futur($_POST['event_ending_date'].' '
    .$_POST['event_ending_time'], true)){
        $feedback['msg'] = "Well, you won't be able to end this event before
		you started it, please make sure the event check out is valid.";
    } elseif (!check_and_valid_time($_POST['event_starting_time'], true)){
        $feedback['msg'] = "The event check in time doesn't match the field
		requirements (hh:mm)";
    } elseif (!check_and_valid_time($_POST['event_ending_time'], true)){
        $feedback['msg'] = "The event check out time doesn't match the field
		requirements (hh:mm)";
    } elseif (!data_validation($_POST['event_description'],
    FILTER_SANITIZE_STRING, true)){
        $feedback['msg'] = "The event description does not match the field
		requirements";
    } elseif (!array_check_no_digit($_POST['event_spoken_languages'],true)){
        $feedback['msg'] = "The event spoken languages does not match the field
		requirements";
    } else {
        $feedback['msg'] = "An unknown error occurred.";
    }
    // default return
    $feedback['status'] = false;
    $_SESSION['feedback'] = $feedback;
    return false;    
}



/**
 * 
 * Save event information into db & session
 **/ 
function save_event_info () {
    if (check_event_info()){
        $feedback = array ();
        // Retrieve datas from form
        $event_name = utf8_encode(filter_var($_POST['event_name'], 
            FILTER_SANITIZE_STRING));
        $event_type = filter_var($_POST['event_type'], 
            FILTER_SANITIZE_STRING);
        $event_starting_date = filter_var($_POST['event_starting_date'].' '
            .$_POST['event_starting_time'], FILTER_SANITIZE_STRING);
        $event_ending_date = filter_var($_POST['event_ending_date'].' '
            .$_POST['event_ending_time'], FILTER_SANITIZE_STRING);
        $event_description = utf8_encode(filter_var($_POST['event_description'], 
            FILTER_SANITIZE_STRING));
        $event_max_nb_participants = filter_var(
            $_POST['event_max_nb_participants'], FILTER_SANITIZE_NUMBER_INT);
        $event_address = utf8_encode(filter_var($_POST['event_address'], 
            FILTER_SANITIZE_STRING));
        $event_zipcode = utf8_encode(filter_var($_POST['event_zipcode'], 
            FILTER_SANITIZE_STRING));
        $event_city = utf8_encode(filter_var($_POST['event_city'], 
            FILTER_SANITIZE_STRING));
        $event_country_name = filter_var($_POST['event_country_name'], 
            FILTER_SANITIZE_STRING);
            
        $event_languages_spoken = array(); 
        foreach($_POST['event_spoken_languages'] as $language) {
            array_push($event_languages_spoken, filter_var($language, 
            FILTER_SANITIZE_STRING));
        }
        
        $keys = geoconfig_parser(_INI_GEO_KEYS_CONFIG, 'all');
        $geocoder = new \Geocoder\Geocoder();
        $adapter  = new \Geocoder\HttpAdapter\BuzzHttpAdapter();
        $chain    = new \Geocoder\Provider\ChainProvider(array(
            new \Geocoder\Provider\FreeGeoIpProvider($adapter),
            new \Geocoder\Provider\HostIpProvider($adapter),
            new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
            new \Geocoder\Provider\BingMapsProvider($adapter, $keys['bing']),
            // ...
        ));
        
        $geocoder->registerProvider($chain);
        // if location information is already in the database, no need
        // to geolocalize again. Just get the location id from the database
        $location_id = Location::get_location_from_address($event_address,
            $event_zipcode, $event_city, $event_country_name);
        // but if it is not, then we geolocalize and add the new location in the database
        if (!$location_id) {
            try {
                $address = implode(', ', array ($event_address, $event_zipcode, 
                            $event_city, $event_country_name));
                $geocode = $geocoder->geocode($address);
                $event_location = new Location(array('geocoded'=>$geocode->toArray()));
                $location_id = $event_location->insert_location();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }else {
            // retrieve all the geocoding infos
            $event_location = Location::get_location_from_id($location_id);
        }
        //Get the user id to use as the holder_id
        $event_holders_ids = $_SESSION['user']->get_user_id();
         
        //Create an user object with it
        $parameters =  array ('event_name' => $event_name,
		'event_type' => $event_type, 
		'event_starting_date' => $event_starting_date,
    	'event_ending_date'=> $event_ending_date, 
    	'event_max_nb_participants' => intval($event_max_nb_participants), 
    	'event_holders_ids' => $event_holders_ids, 
    	'event_languages' => $event_languages_spoken, 
		'event_location' => $event_location,
		'event_description' => $event_description);
        /*TODO: add exception around the object creation (This is not the only one)*/
        $event = new Event ($parameters);
        	
        // save new datas in database;
        if ($event->insert_event()) {

            // Save them in the user session
            if (!array_key_exists('events', $_SESSION)) {
                $events = array();
            }else {
                $events =  $_SESSION['user_events'];
            }
            array_push($events, $event);
            $_SESSION['user_events'] = $events;
            $feedback['status'] = true;
            $_SESSION['feedback'] = $feedback;
            return true;
        }
        else {
            $feedback['msg'] = 'The event creation failed <br/>';
            $feedback['status'] = false;
            $_SESSION['feedback'] = $feedback;
            return false;
        }
    } else {
        return false;
    }
}

?>