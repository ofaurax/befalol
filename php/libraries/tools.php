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
    
    $options = array();
    foreach ( $parse [ "db_options" ] as $k => $v ) {
        $options [$k] = $v;
    }
    
	}
	//echo $server_path.'<br/>';
	//	echo $dsn.'<br/>';
	return array('dsn' => $dsn, 'db_path' => $db_path, 'options' => $options) ;

}


/**
 * Function validating input data
 * @param $array_data, the array input to check
 * @param $filter, filter to apply to each array item in order to validate it
 * @param $actionforempty, 0: ignore emptyness, 1: validation will fail if emptyness
 */
function array_data_validation ($array_data, $filter, $actionforempty) {
    if (is_array($array_data) && (is_int($filter))) {
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
    } else {
        echo 'The input data must be an array and the input filter an integer <br/>';
        return false;
    }
}


/**
 * Function validating input data
 * @param $data, the input to check
 * @param $filter, filter to apply to data in order to validate it
 * @param $actionforempty, False: ignore emptyness, True: validation will fail if emptyness
 */
function data_validation ($data, $filter, $actionforempty) {
    if (!is_array($data) && (is_int($filter))) {
        if (empty($data) && ($actionforempty == true)) {
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
    }else {
        echo 'The input data cannot be an array and the input filter must be 
        an integer <br/>';
        return false;
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
    if (empty($date) && ($actionforempty == 1)) {
        // if data is empty and that the function is set to fail if empty,
        //then failing happens
        return false;
    } else if (empty($date) && ($actionforempty == 0)) {
        return true;
    } else if (!empty($date) && is_string($date)) {
        // mm/dd/yyyy
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
            // check if year is correct too
            preg_match( '`^\d{1,2}/\d{1,2}/(\d{4})$`' , $date, $year);
            // check if the date is valid
            return checkdate( intval($month[1]), intval($day[1]), intval($year[1]));
        // yyyy-mm-dd
        }elseif (preg_match( '`^\d{4}-\d{1,2}-\d{1,2}$`' , $date)) { 
             // check if day is correct
            preg_match( '`^\d{4}-\d{1,2}-(\d{1,2})$`' , $date, $day);
            if ((intval($day[1]) > 31) || (intval($day[1]) <= 0 )) {
                return false;
            }
            // check if day is correct too
            preg_match( '`^\d{4}-(\d{1,2})-\d{1,2}$`' , $date, $month);
            if ((intval($month[1]) > 12) || (intval($month[1]) <= 0 )) {
                return false;
            }
            // check if year is correct too
            preg_match( '`(^\d{4})-\d{1,2}-\d{1,2}$`' , $date, $year);
            // check if the date is valid
            return checkdate( intval($month[1]), intval($day[1]), intval($year[1]));
        // mm/dd/yyyy hh:mm
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
            // check if hours are correct too
            preg_match( '`^\d{1,2}/\d{1,2}/\d{4}\s(\d{1,2}):\d{1,2}$`' , $date, $hours);
            if ((intval($hours[1]) > 23) || (intval($hours[1]) < 0 )) {
                return false;
            }
            // check if minutes are correct too
            preg_match( '`^\d{1,2}/\d{1,2}/\d{4}\s\d{1,2}:(\d{1,2})$`' , $date, $minutes);
            if ((intval($minutes[1]) > 59) || (intval($minutes[1]) < 0 )) {
                return false;
            }
            // check if year is correct too
            preg_match( '`^\d{1,2}/\d{1,2}/(\d{4})\s\d{1,2}:\d{1,2}$`' , $date, $year);
            // check if the date is valid
            return checkdate( intval($month[1]), intval($day[1]), intval($year[1]));
        // yyyy-mm-dd hh:mm
        } elseif (preg_match( '`^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}$`' , $date)){
            // check if day is correct
            preg_match( '`^\d{4}-\d{1,2}-(\d{1,2})\s\d{1,2}:\d{1,2}$`' , $date, $day);
            if ((intval($day[1]) > 31) || (intval($day[1]) <= 0 )) {
                return false;
            }
            // check if month is correct too
            preg_match( '`^\d{4}-(\d{1,2})-\d{1,2}\s\d{1,2}:\d{1,2}$`' , $date, $month);
            if ((intval($month[1]) > 12) || (intval($month[1]) <= 0 )) {
                return false;
            }
            // check if hours are correct too
            preg_match( '`^\d{4}-\d{1,2}-\d{1,2}\s(\d{1,2}):\d{1,2}$`' , $date, $hours);
            if ((intval($hours[1]) > 23) || (intval($hours[1]) < 0 )) {
                return false;
            }
            // check if minutes are correct too
            preg_match( '`^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:(\d{1,2})$`' , $date, $minutes);
            if ((intval($minutes[1]) > 59) || (intval($minutes[1]) < 0 )) {
                return false;
            }
            // check if year is correct too
            preg_match( '`^(\d{4})-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}$`' , $date, $year);
            // check if the date is valid
            return checkdate( intval($month[1]), intval($day[1]), intval($year[1]));
        // mm/dd/yyyy hh:mm:ss
        }elseif (preg_match( '`^\d{1,2}/\d{1,2}/\d{4}\s\d{1,2}:\d{1,2}:\d{1,2}$`' , $date)){
            // check if month is correct
            preg_match( '`^\d{1,2}/(\d{1,2})/\d{4}\s\d{1,2}:\d{1,2}:\d{1,2}$`' , $date, $day);
            if ((intval($day[1]) > 31) || (intval($day[1]) <= 0 )) {
                return false;
            }
            // check if day is correct too
            preg_match( '`^(\d{1,2})/\d{1,2}/\d{4}\s\d{1,2}:\d{1,2}:\d{1,2}$`' , $date, $month);
            if ((intval($month[1]) > 12) || (intval($month[1]) <= 0 )) {
                return false;
            }
            // check if hours are correct too
            preg_match( '`^\d{1,2}/\d{1,2}/\d{4}\s(\d{1,2}):\d{1,2}:\d{1,2}$`' , $date, $hours);
            if ((intval($hours[1]) > 23) || (intval($hours[1]) < 0 )) {
                return false;
            }
            // check if minutes are correct too
            preg_match( '`^\d{1,2}/\d{1,2}/\d{4}\s\d{1,2}:(\d{1,2}):\d{1,2}$`' , $date, $minutes);
            if ((intval($minutes[1]) > 59) || (intval($minutes[1]) < 0 )) {
                return false;
            }
            // check if secondes are correct too
            preg_match( '`^\d{1,2}/\d{1,2}/\d{4}\s\d{1,2}:\d{1,2}:(\d{1,2})$`' , $date, $secondes);
            if ((intval($secondes[1]) > 59) || (intval($secondes[1]) < 0 )) {
                return false;
            }
            // check if year is correct too
            preg_match( '`^\d{1,2}/\d{1,2}/(\d{4})\s\d{1,2}:\d{1,2}:\d{1,2}$`' , $date, $year);
            // check if the date is valid
            return checkdate( intval($month[1]), intval($day[1]), intval($year[1]));
        // yyyy-mm-dd hh:mm:ss
        } elseif (preg_match( '`^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}$`' , $date)){
            // check if day is correct
            preg_match( '`^\d{4}-\d{1,2}-(\d{1,2})\s\d{1,2}:\d{1,2}:\d{1,2}$`' , $date, $day);
            if ((intval($day[1]) > 31) || (intval($day[1]) <= 0 )) {
                return false;
            }
            // check if month is correct too
            preg_match( '`^\d{4}-(\d{1,2})-\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}$`' , $date, $month);
            if ((intval($month[1]) > 12) || (intval($month[1]) <= 0 )) {
                return false;
            }
            // check if hours are correct too
            preg_match( '`^\d{4}-\d{1,2}-\d{1,2}\s(\d{1,2}):\d{1,2}:\d{1,2}$`' , $date, $hours);
            if ((intval($hours[1]) > 23) || (intval($hours[1]) < 0 )) {
                return false;
            }
            // check if minutes are correct too
            preg_match( '`^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:(\d{1,2}):\d{1,2}$`' , $date, $minutes);
            if ((intval($minutes[1]) > 59) || (intval($minutes[1]) < 0 )) {
                return false;
            }
            // check if secondes are correct too
            preg_match( '`^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}:(\d{1,2})$`' , $date, $secondes);
            if ((intval($secondes[1]) > 59) || (intval($secondes[1]) < 0 )) {
                return false;
            }
            // check if year is correct too
            preg_match( '`^(\d{4})-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}$`' , $date, $year);
            // check if the date is valid
            return checkdate( intval($month[1]), intval($day[1]), intval($year[1]));
        } else {
            return false;
        }
    } else {
        return false;
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
    } else if (empty($time) && ($actionforempty == 0)) {
        return true;
    }else if (!empty($time) && is_string($time)) {
        if (preg_match( '`^\d{1,2}:\d{1,2}$`', $time)) {
            // check if hours are correct
            preg_match( '`^(\d{1,2}):\d{1,2}$`', $time, $hours);
            if ((intval($hours[1]) > 24) || (intval($hours[1]) < 0 )) {
                return false;
            }
            // check if minutes are correct too
            preg_match( '`^\d{1,2}:(\d{1,2})$`' , $time, $minutes);
            if ((intval($minutes[1]) > 59) || (intval($minutes[1]) < 0 )) {
                return false;
            }
            return true;
        } else if (preg_match( '`^\d{1,2}:\d{1,2}:\d{1,2}$`', $time)) {
            // check if hours are correct
            preg_match( '`^(\d{1,2}):\d{1,2}:\d{1,2}$`', $time, $hours);
            if ((intval($hours[1]) > 24) || (intval($hours[1]) < 0 )) {
                return false;
            }
            // check if minutes are correct too
            preg_match( '`^\d{1,2}:(\d{1,2}):\d{1,2}$`' , $time, $minutes);
            if ((intval($minutes[1]) > 59) || (intval($minutes[1]) < 0 )) {
                return false;
            }
            // check if seconds are correct too
            preg_match( '`^\d{1,2}:\d{1,2}:(\d{1,2})$`' , $time, $seconds);
            if ((intval($seconds[1]) > 59) || (intval($seconds[1]) < 0 )) {
                return false;
            }
            return true;
        }else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * 
 * Test if a date is in the futur or not. Return true if futur, false if not.
 * @param string $string_date
 */
function is_it_futur ($string_date) {
    // then check if year is not in the futur
    //TODO take into account time zone
    try {
        $date = new DateTime($string_date);
    }catch (Exception $e) {
        return false;
    }
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
    $reg = '#[\d]+#';
    // if empty and actionforempty is true, then considered as fail
    if (empty($string) && $actionforempty) {
        return false;
    // if empty and actionforempty is false then considered as success
    } else if (empty($string)) {
        return true;
    // if not empty, apply the regex matching
    }else if (is_string ($string)) {
        if (preg_match($reg, $string, $matches)) {
            //print_r ($matches);
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
    $reg = '#[\d]+#';
    // if input is empty and actionforempty is true then considered as failure
    if (empty($array_of_string) && $actionforempty) {
        return false;
    // else if the input is not empty and an array, we process checking
    } else if (!empty($array_of_string)) {
         if (is_array($array_of_string)) {
            foreach ($array_of_string as $string) {
                // if empty and actionforempty is true, then considered as failure
                if (empty($string) && $actionforempty) {
                    return false;
                // if empty and actionforempty is false then we continue checking
                // process
                } else if (empty($string)) {
                    continue;
                // if not empty and a string, apply the regex matching
                } else if (is_string ($string)) {
                    if (preg_match($reg, $string, $matches)) {
                        //print_r ($matches);
                        return false; // return false if digit
                    }else {
                        continue; // continue process if not
                    }
                // return false for other cases    
                }else {
                    echo 'The array must contain strings. <br/>';
                    return false;
                }
            }
            // if we arrive here, then no digit in strings -> success
            return true;
        // if input is not an array, then it is not what we expected -> failure
        } else {
             echo 'Please, give us an array of string. <br/>';
             return false;
        }
    // if input empty and $actionforempty is false -> success
    } else if (empty($array_of_string) && !$actionforempty)  {
        return true;
    // other cases lead to failure
    } else {
        echo 'The input parameters must be an array <br>.';
        return false;
    }
}

?>