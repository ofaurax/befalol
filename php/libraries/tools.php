<?php  

// Parse the config.ini file in order to return database informations
function db_parser ($ini,$server_path) 
{
	
	$db_path ='';
	$parse = parse_ini_file ( $ini , true ) ;

	$driver = $parse [ "db_driver" ] ;
	$dsn = "${driver}:" ;
	
	foreach ( $parse [ "dsn" ] as $k => $v ) {
		if ($k = 'host'){
			$db_path = $server_path.$v;
			$dsn .= "${db_path}" ;
		}
	}
	//echo $server_path.'<br/>';
	//	echo $dsn.'<br/>';
    return array('dsn' => $dsn, 'db_path' => $db_path) ;

}



/**
 * Function validating input data
 * @param $data, the input to check
 * @param $filter, filter to apply to data in order to validate it
 * @param $actionforempty, 0: ignore emptyness, 1: validation will fail if emptyness
 */
 function data_validation ($data, $filter, $actionforempty) {
	
 	if (empty($data) && ($actionforempty==1)) {
 		// if data is empty and that the function is set to fail if empty, 
 		//then failing happens
 		return false;
 	}else if (!empty($data)) {
 		// check if data match the type
 		if (filter_var($data, $filter)) {
 			return true;
 		}else {
 			return false;
 		} 
 	}else {
 		// data is empty and can be ignored, function sucesses
 		return true;
 	}	
}

/**
 * 
 * Check if date format is correct then check validity of the date. Return true 
 * if the date has the right format and is valid
 * @param date $date
 * @param $actionforempty, 0: ignore emptyness, 1: validation will fail if emptyness
 */
function check_and_valid_date ($date, $actionforempty) {
	//TODO Must be motified according to the timezone
	if (empty($date) && ($actionforempty==1)) {
 		// if data is empty and that the function is set to fail if empty, 
 		//then failing happens
 		return false;
	}else if (!preg_match( '`^\d{1,2}/\d{1,2}/\d{4}$`' , $date)) {
		return false;
	} else {
		// check if month is correct
		preg_match( '`^\d{1,2}/(\d{1,2})/\d{4}$`' , $date, $buffer);
		if ((intval($buffer[1]) > 31) || (intval($buffer[1]) <= 0 )) {
			print 'je passe ici <br/>';
			print intval($buffer[1]) .'<br/>';
			return false;
		}
		// check if day is correct too
		preg_match( '`^(\d{1,2})/\d{1,2}/\d{4}$`' , $date, $buffer);
		if ((intval($buffer[1]) > 12) || (intval($buffer[1]) <= 0 )) {
			print 'je passe la <br/>';
			print intval($buffer[1]) .'<br/>';
			return false;
		}
		// check if the date is valid
		if (!IsDate($date)) {
			return false;
		}
		return true;
	}
}
	
function is_it_futur ($string_date) {
	// then check if year is not in the futur
	//TODO take into account time zone
	$date = new DateTime($string_date);
	$today = new DateTime('now');
	if ($date > $today) {
		return true;
	}
	return false;
}
/**
 * 
 * Check if a string is valid date
 * @param string/date $Str
 */
function IsDate( $Str )
{
  $Stamp = strtotime( $Str );
  $Month = date( 'm', $Stamp );
  $Day   = date( 'd', $Stamp );
  $Year  = date( 'Y', $Stamp );

  return checkdate( $Month, $Day, $Year );
}



?>