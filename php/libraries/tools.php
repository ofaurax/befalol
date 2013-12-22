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
 * @param boolean $actionforempty, 0: ignore emptyness, 1: validation will fail if emptyness
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

/**
 * 
 * Test if a date is in the futur or not. Return true if futur, false if not
 * @param string $string_date
 */
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
 * Check if there is no digit in a string. Return true if no digit, false if digit.
 * @param string $string
 * @param bool $actionforempty 0: ignore emptyness, 1: validation will fail if 
 * emptyness
 */
function check_no_digit ($string, $actionforempty) {
    $reg = '#[^\D]#';
    // if empty and actionforempty is true, then considered as fail
    if (empty($string) && $actionforempty) {
        return false;
    // if empty and actionforempty is false then considered as success
    } else if (empty($string)) {
        return true;
    // if not empty, apply the regex matching
    }else if (is_string ($string)) {
        preg_match($reg, $string, $matches);
        //print_r ($matches);
        if (!empty ($matches[0])) {
            return false;
        }else {
            return true;
        }
    }else {
        echo 'Please, give us a string. <br/>';
        return false;
    }
}

/**
 * 
 * Check if there is no digit in a array of string. Return true if no digit, false if digit.
 * @param array $array_of_string
 * @param bool $actionforempty 0: ignore emptyness, 1: validation will fail if 
 * emptyness
 */
function array_check_no_digit ($array_of_string, $actionforempty) {
    $reg = '#[^\D]#';
    if (is_array($array_of_string)) {
        foreach ($array_of_string as $string) {
            // if empty and actionforempty is true, then considered as fail
            if (empty($string) && $actionforempty) {
                return false;
            // if empty and actionforempty is false then considered as success
            } else if (empty($string)) {
                continue;
            // if not empty, apply the regex matching
            }else if (is_string ($string)) {
                preg_match($reg, $string, $matches);
                //print_r ($matches);
                if (!empty ($matches[0])) {
                    return false;
                }else {
                    continue;
                }
            }else {
                echo 'Please, give us a string. <br/>';
                return false;
            }
        }
        return true;
    } else {
        echo 'The input parameters must be an array <br>.';
        return false;
    }
}



?>