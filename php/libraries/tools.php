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
 * @param $array_data, the array input to check
 * @param $filter, filter to apply to each array item in order to validate it
 * @param $actionforempty, 0: ignore emptyness, 1: validation will fail if emptyness
 */
function array_data_validation ($array_data, $filter, $actionforempty) {
    foreach ($array_data as $data) {
        if (empty($data) && ($actionforempty==1)) {
            // if data is empty and that the function is set to fail if empty,
            //then failing happens
            return false;
        }else if (!empty($data)) {
            // check if data match the type
            if (!filter_var($data, $filter)) {
                return false;
            }
        }// data is empty and can be ignored, function sucesses
    }
    return true;
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
    }else if (!empty($date)) {
        if (preg_match( '`^\d{1,2}/\d{1,2}/\d{4}$`' , $date)) {
            // check if month is correct
            preg_match( '`^\d{1,2}/(\d{1,2})/\d{4}$`' , $date, $day);
            if ((intval($day[1]) > 31) || (intval($day[1]) <= 0 )) {
                return false;
            }
            // check if day is correct too
            preg_match( '`^(\d{1,2})/\d{1,2}/\d{4}$`' , $date, $month);
            if ((intval($month[1]) > 12) || (intval($month[1]) <= 0 )) {
                return false;
            }
            // check if day is correct too
            preg_match( '`^\d{1,2}/\d{1,2}/(\d{4})$`' , $date, $year);
            // check if the date is valid
            return checkdate( intval($month[1]), intval($day[1]), intval($year[1]));
        }elseif (preg_match( '`^\d{1,2}/\d{1,2}/\d{4}\s\d{1,2}:\d{1,2}$`' , $date)){
            // check if month is correct
            preg_match( '`^\d{1,2}/(\d{1,2})/\d{4}\s\d{1,2}:\d{1,2}$`' , $date, $day);
            if ((intval($day[1]) > 31) || (intval($day[1]) <= 0 )) {
                return false;
            }
            // check if day is correct too
            preg_match( '`^(\d{1,2})/\d{1,2}/\d{4}\s\d{1,2}:\d{1,2}$`' , $date, $month);
            if ((intval($month[1]) > 12) || (intval($month[1]) <= 0 )) {
                return false;
            }
            // check if day is correct too
            preg_match( '`^\d{1,2}/\d{1,2}/(\d{4})\s\d{1,2}:\d{1,2}$`' , $date, $year);
            // check if the date is valid
            return checkdate( intval($month[1]), intval($day[1]), intval($year[1]));
        }
        else {
            return false;
        }
    } else {
        return true;
    }
}


/**
 *
 * Check if time format is correct then check validity of the date. Return true
 * if the time has the right format and is valid
 * @param date $date
 * @param $actionforempty, 0: ignore emptyness, 1: validation will fail if emptyness
 */
function check_and_valid_time ($time, $actionforempty) {
    //TODO Must be motified according to the timezone
    if (empty($time) && ($actionforempty==1)) {
        // if time is empty and that the function is set to fail if empty,
        //then failing happens
        return false;
    }else if (!empty($time)) {
        if (!preg_match( '`^\d{1,2}:\d{1,2}$`', $time)) {
            return false;
        } else {
            // check if hours are correct
            preg_match( '`^(\d{1,2}):\d{1,2}$`', $time, $buffer);
            if ((intval($buffer[1]) > 24) || (intval($buffer[1]) < 0 )) {
                return false;
            }
            // check if day is correct too
            preg_match( '`^\d{1,2}:(\d{1,2})$`' , $time, $buffer);
            if ((intval($buffer[1]) > 59) || (intval($buffer[1]) < 0 )) {
                return false;
            }
            return true;
        }
    } else {
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
    //TODO does not work for february
    $Stamp = strtotime( $Str );
    $Month = date( 'mm', $Stamp );
    $Day   = date( 'dd', $Stamp );
    $Year  = date( 'YYYY', $Stamp );
    echo $Month.'<br/>';
    echo $Day.'<br/>';
    echo $Year.'<br/>';
    return checkdate( $Month, $Day, $Year );
}



?>