<?php 

require_once ('./php/libraries/eventhandler.php');
require_once ('./php/libraries/tools.php');
require_once 'vendor/autoload.php';

/****** REQUIRES FAKER LIBRARY ********/
// Uncomment the line above if you have installed fzaninotto / Faker library
// fzaninotto / Faker
/**************************************/

/****** REQUIRES PHPUNIT LIBRARY ********/
// Uncomment the line above if you have installed fzaninotto / Faker library
// phpunit / phpunit
/***********************************/

date_default_timezone_set('Europe/Paris');
ini_set('display_errors', 1);
error_reporting(E_ALL);


/**
 * 
 * Unittest allowing to test the array_data_validation tool
 * @author Aldeen Berluti
 *
 */
Class InputArrayFilterValidation_UT extends PHPUnit_Framework_TestCase {
    
    /**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_correct_data () {
        $faker = Faker\Factory::create();
        $array_data =  array();
        $inputs =  array();
        // Pack correct input data in array of data
        // set correct input data - simple emails
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($array_data,$faker->email);
        }
        // choose a filter to apply
        array_push($inputs, array('data' => $array_data, 'filter' => 
        FILTER_VALIDATE_EMAIL, 'action' => false));
        
        $array_data =  array();
         // set correct input data - simple ip
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($array_data,$faker->ipv4);
        }
        array_push($inputs, array('data' => $array_data, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => false));
        
        $array_data =  array();
        // set correct input data - simple int
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($array_data,$faker->randomNumber);
        }
        array_push($inputs, array('data' => $array_data, 'filter' => 
        FILTER_VALIDATE_INT, 'action' => false));
        
        $array_data =  array();
        // set correct empty data that must be ignored
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($array_data,'');
        }
        array_push($inputs, array('data' => $array_data, 'filter' => 
        FILTER_VALIDATE_INT, 'action' => false));
        
        //expect the array_data_validation to return true
        foreach ($inputs as $input) {
            $this->assertTrue(array_data_validation($input['data'], 
                $input['filter'], $input['action']));
        }
    }
    
    /**
     * 
     * Test behavior with several incorrect input datas
     */
    public function test_behavior_incorrect_data () {
        $array_data =  array();
        $inputs =  array();
        
        // Pack incorrect data in array of data
        // EMAIL INPUTS
        // set incorrect emails - non utf8 character
        $emails = array ('jeanmacŽ@acme.biz', 'jdoe@example.com', 'a@gmail.com',
        	'awl.stuff@dawson.com', 'dude@gmail.com', 'lalilalou@yahoo.com');
        // choose a filter to apply
        array_push($inputs, array('data' => $emails, 'filter' => 
        FILTER_VALIDATE_EMAIL, 'action' => true));
        
        // set incorrect emails - no @
        $emails = array ('jeanmaceacme.biz', 'jdoe@example.com', 'a@gmail.com',
        	'awl.stuff@dawson.com', 'dude@gmail.com', 'lalilalou@yahoo.com');
        // choose a filter to apply
        array_push($inputs, array('data' => $emails, 'filter' => 
        FILTER_VALIDATE_EMAIL, 'action' => true));
              
        // set incorrect emails - no prefix
        $emails = array ('@acme.biz', 'jdoe@example.com', 'a@gmail.com',
        	'awl.stuff@dawson.com', 'dude@gmail.com', 'lalilalou@yahoo.com');
        // choose a filter to apply
        array_push($inputs, array('data' => $emails, 'filter' => 
        FILTER_VALIDATE_EMAIL, 'action' => true));
        
        // set incorrect emails - empty string
        $emails = array ('', 'jdoe@example.com', 'a@gmail.com',
        	'awl.stuff@dawson.com', 'dude@gmail.com', 'lalilalou@yahoo.com');
        // choose a filter to apply
        array_push($inputs, array('data' => $emails, 'filter' => 
        FILTER_VALIDATE_EMAIL, 'action' => true));
              
        //expect the array_data_validation to return false
        foreach ($inputs as $input) {
            $this->assertFalse(array_data_validation($input['data'], 
                $input['filter'], $input['action']));
        }
        
        
        // IPV4 INPUTS
        $inputs =  array();
        // set incorrect input ips - empty string
        $ipv4 = array ('220.181.168.9', '', '220.181.168.9', 
        	'220.181.168.9', '220.181.168.9', '220.181.168.9', '220.181.168.9');
        array_push($inputs, array('data' => $ipv4, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => true));
        
        // set incorrect input ips - character in ip
        $ipv4 = array ('220.181.168.9', '220.181.168.9', 'abc.132.56.2', 
        	'220.181.168.9', '220.181.168.9', '220.181.168.9', '220.181.168.9');
        array_push($inputs, array('data' => $ipv4, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => true));
        
        // set incorrect input ips - one ip entity more
        $ipv4 = array ('220.181.168.9', '125.125.125.125.125', '220.181.168.9', 
        	'220.181.168.9', '220.181.168.9', '220.181.168.9', '220.181.168.9');
        array_push($inputs, array('data' => $ipv4, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => true));
        
        // set incorrect input ips - ip = '0'
        $ipv4 = array ('220.181.168.9', '0', '220.181.168.9', 
        	'220.181.168.9', '220.181.168.9', '220.181.168.9', '220.181.168.9');
        array_push($inputs, array('data' => $ipv4, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => true));
        
        // set incorrect input ips - one character is not a digit
        $ipv4 = array ('220.181.168.9', '140.120.5.2a5', '220.181.168.9', 
        	'220.181.168.9', '220.181.168.9', '220.181.168.9', '220.181.168.9');
        array_push($inputs, array('data' => $ipv4, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => true));
        
        // set incorrect input ips -  one ip entity less
        $ipv4 = array ('220.181.168.9', '25.25.25', '220.181.168.9', 
        	'220.181.168.9', '220.181.168.9', '220.181.168.9', '220.181.168.9');
        array_push($inputs, array('data' => $ipv4, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => true));
        
        // set incorrect input ips - coma instead of dot
        $ipv4 = array ('220.181.168.9', '25,25,25,25', '220.181.168.9', 
        	'220.181.168.9', '220.181.168.9', '220.181.168.9', '220.181.168.9');
        array_push($inputs, array('data' => $ipv4, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => true));
        
        // set incorrect input ips - out of range
        $ipv4 = array ('220.181.168.9', '380.0.890.1025', '220.181.168.9', 
        	'220.181.168.9', '220.181.168.9', '220.181.168.9', '220.181.168.9');
        array_push($inputs, array('data' => $ipv4, 'filter' => 
        FILTER_VALIDATE_IP, 'action' => true));
        
        //expect the array_data_validation to return false
        foreach ($inputs as $input) {
            $this->assertFalse(array_data_validation($input['data'], 
                $input['filter'], $input['action']));
        }
        
        // INTEGER INPUTS
        $inputs =  array();
        // set incorrect input int - set a float number
        $integers = array (896, '896', 8.96, 896, 896, 896, 896);
        array_push($inputs, array('data' => $integers, 'filter' => 
        FILTER_VALIDATE_INT, 'action' => true));
        
        // set incorrect input int - set an empty field
        $integers = array (896, '896', '', 896, 896, 896, 896);
        array_push($inputs, array('data' => $integers, 'filter' => 
        FILTER_VALIDATE_INT, 'action' => true));
                     
        // set incorrect input int - set null field
        $integers = array (896, '896', null, 896, 896, 896, 896);
        array_push($inputs, array('data' => $integers, 'filter' => 
        FILTER_VALIDATE_INT, 'action' => true));
        
        // set incorrect input int - set a string with no digit
        $integers = array (896, '896', 'abc', 896, 896, 896, 896);
        array_push($inputs, array('data' => $integers, 'filter' => 
        FILTER_VALIDATE_INT, 'action' => true));
        
        foreach ($inputs as $input) {
            $this->assertFalse(array_data_validation($input['data'], 
                $input['filter'], $input['action']));
        }
        
        // EMPTY INPUTS
        $inputs =  array();
        // set correct empty data which should not be ignored
        array_push($array_data,'');
        array_push($array_data, null);
        array_push($array_data,0);
        array_push($inputs, array('data' => $array_data, 'filter' => 
        FILTER_VALIDATE_EMAIL, 'action' => true));
        
        //expect the array_data_validation to return false
        foreach ($inputs as $input) {
            $this->assertFalse(array_data_validation($input['data'], 
                $input['filter'], $input['action']));
        }
    }
    
    /**
     * 
     * Test behavior with several wrong input data types
     */
    public function test_wrong_input_type () {
        $data = array (123, 4, 15550, 6, 9);
        $filter = FILTER_VALIDATE_INT;
        // set string instead of array and expect to be wrong
        // TODO: change as soon as a error handling would have been set up
        $this->assertFalse (array_data_validation ('This is not an array', $filter, false));
        //set inexisting filter and expect to return wrong or an exception
        $this->assertFalse (array_data_validation ($data, 'filter', false));
    }    
}

/**
 * 
 * Unittest class allowing to test the data_validation tool
 * @author Aldeen Berluti
 *
 */
Class InputFilterValidation_UT extends PHPUnit_Framework_TestCase {
    
    /**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_correct_data () {
        $email = 'example@gmail.com';
        $ipv4 = '';
        $integer = '15560';
        $this->assertTrue(data_validation($email, FILTER_VALIDATE_EMAIL, false));
        $this->assertTrue(data_validation($ipv4, FILTER_VALIDATE_IP, false));
        $this->assertTrue(data_validation($integer, FILTER_VALIDATE_INT, false));
    }
    
    /**
     * 
     * Test behavior with several incorrect input datas
     */
    public function test_behavior_incorrect_data () {
        
        // EMAIL INPUTS
        //set incorrect email - non utf8 character
        $email = 'jeanmacŽ@acme.biz';
        $this->assertFalse(data_validation($email, FILTER_VALIDATE_EMAIL, true));
        // set incorrect emails - no @
        $email = 'jeanmaceacme.biz';
        $this->assertFalse(data_validation($email, FILTER_VALIDATE_EMAIL, true));
        // set incorrect emails - no prefix
        $email = '@acme.biz';
        $this->assertFalse(data_validation($email, FILTER_VALIDATE_EMAIL, true));
        // set incorrect emails - empty string
        $email = '';
        $this->assertFalse(data_validation($email, FILTER_VALIDATE_EMAIL, true));
                
        
        $this->assertFalse(data_validation($email, FILTER_VALIDATE_EMAIL, true));
        // IPV4 INPUTS
        // set incorrect input ips - empty string       
        $ipv4 = '';
        $this->assertFalse(data_validation($ipv4, FILTER_VALIDATE_IP, true));
        // set incorrect input ips - character in ip    
        $ipv4 = 'abc.132.56.2';
        $this->assertFalse(data_validation($ipv4, FILTER_VALIDATE_IP, true));
        // set incorrect input ips - one ip entity more      
        $ipv4 = '125.125.125.125.125';
        $this->assertFalse(data_validation($ipv4, FILTER_VALIDATE_IP, true));
        // set incorrect input ips - ip = '0'   
        $ipv4 = '0';
        $this->assertFalse(data_validation($ipv4, FILTER_VALIDATE_IP, true));
        // set incorrect input ips - one character is not a digit       
        $ipv4 = '140.120.5.2a5';
        $this->assertFalse(data_validation($ipv4, FILTER_VALIDATE_IP, true));
        // set incorrect input ips -  one ip entity less    
        $ipv4 = '25.25.25';
        $this->assertFalse(data_validation($ipv4, FILTER_VALIDATE_IP, true));
         // set incorrect input ips - coma instead of dot       
        $ipv4 = '25,25,25,25';
        $this->assertFalse(data_validation($ipv4, FILTER_VALIDATE_IP, true));
        // set incorrect input ips - out of range     
        $ipv4 = '380.0.890.1025';
        $this->assertFalse(data_validation($ipv4, FILTER_VALIDATE_IP, true)); 
        
        
        // INTEGER INPUTS
        // set incorrect input int - set a float number
        $integer = 150.6;
        $this->assertFalse(data_validation($integer, FILTER_VALIDATE_INT, true));
        // set incorrect input int - set an empty field
        $integer = '';
        $this->assertFalse(data_validation($integer, FILTER_VALIDATE_INT, true));
        // set incorrect input int - set null field
        $integer = null;
        $this->assertFalse(data_validation($integer, FILTER_VALIDATE_INT, true));
        // set incorrect input int - set a string with no digit
        $integer = 'this is a string and not an integer';
        $this->assertFalse(data_validation($integer, FILTER_VALIDATE_INT, true));             
    }
    
    /**
     * 
     * Test behavior with several wrong input data types
     */
    public function test_wrong_input_type () {
        $data = 1560;
        $filter = FILTER_VALIDATE_INT;
        // set string instead of array and expect to be wrong
        // TODO: change as soon as a error handling would have been set up
        $this->assertFalse (data_validation ('This is not an array', $filter, false));
        //set inexisting filter and expect to return wrong or an exception
        $this->assertFalse (data_validation ($data, 'filter', false));
    }
}  

/**
 * 
 * Unittest class allowing to test the check_and_valid_date tool
 * @author Aldeen Berluti
 *
 */
Class DateValidation_UT extends PHPUnit_Framework_TestCase {
    
    /**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_correct_input_data () {
        $date = "11/25/2013";
        $this->assertTrue(check_and_valid_date($date, false));
        $date = "11/5/2013";
        $this->assertTrue(check_and_valid_date($date, false));
        $date = "11/25/2013 23:00:00";
        $this->assertTrue(check_and_valid_date($date, false));
        $date = "11/25/2013 23:00:00";
        $this->assertTrue(check_and_valid_date($date, false));
        $date = '';
        $date = "2013-11-25 23:00:00";
        $this->assertTrue(check_and_valid_date($date, false));
        $date = "1988-11-25 12:00";
        $this->assertTrue(check_and_valid_date($date, false));
        $date = "1986-01-25";
        $this->assertTrue(check_and_valid_date($date, false));
        $date = '';
        $this->assertTrue(check_and_valid_date($date, false));
        $date = null;
        $this->assertTrue(check_and_valid_date($date, false));
    }
    
    /**
     * 
     * Test behavior with several incorrect input datas
     */
    public function test_behavior_incorrect_input_data () {
        // incorrect date format - right format with hyphens is yyyy-mm-dd
        $date = "11-25-2013";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - right format with hyphens is yyyy-mm-dd hh:mm
        $date = "11-25-2013 23:00";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - right format with hyphens is yyyy-mm-dd hh:mm:ss 
        $date = "11-25-2013 23:00:00";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - right format with hyphens is yyyy-mm-dd hh:mm:ss 
        $date = "2013-25-12 23:00:00";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - right format with hyphens is yyyy-mm-dd hh:mm 
        $date = "2013-25-12 23:00";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - right format with hyphens is yyyy-mm-dd 
        $date = "2013-25-12";
        $this->assertFalse(check_and_valid_date($date, false));
         // incorrect date format - right format with hyphens is yyyy-mm-dd
        $date = "11-25-2013";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - European format with /
        $date = "25/11/2013";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - String which is not a date
        $date = "25/1100/2013";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - Still doesn't match the date format
        $date = "25/1100/201300";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format - bad time format
        $date = "11/30/2013 05 00";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format / non/existant hours
        $date = "11/30/2013 28:00:00";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date format / non/existant secondes
        $date = "11/30/2013 12:30:99";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date / non/existant date
        $date = "02/31/2014";
        $this->assertFalse(check_and_valid_date($date, false));
        // incorrect date - null
        $date = null;
        $this->assertFalse(check_and_valid_date($date, true));
        // incorrect date - empty string
        $date = '';
        $this->assertFalse(check_and_valid_date($date, true));
  }

	/**
     * 
     * Test behavior with several wrong input data types
     */
   public function test_wrong_input_type () {
        // set array instead of string and expect to be wrong
        $array = array('02-11-1998',);
        $this->assertFalse (check_and_valid_date ($array, false));
        // set integer instead of string and expect to be wrong
        $integer = 5885;
        $this->assertFalse (check_and_valid_date ($integer, false));
        // set float instead of string and expect to be wrong
        $float = 25.6;
        $this->assertFalse (check_and_valid_date ($float, false));
        // set date object instead of string and expect to be wrong
        $date = new DateTime('now');
        $this->assertFalse (check_and_valid_date ($date, false));
    }
}

/**
 * 
 * Unittest class allowing to test the check_and_valid_time tool
 * @author Aldeen Berluti
 *
 */
Class TimeValidation_UT extends PHPUnit_Framework_TestCase {
    
    /**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_correct_input_data () {
        // hh:mm
        $time = "21:00";
        $this->assertTrue(check_and_valid_time($time, false));
        // hh:mm:ss
        $time = "09:25:33";
        $this->assertTrue(check_and_valid_time($time, false));
        // h:m:s
        $time = "9:3:8";
        $this->assertTrue(check_and_valid_time($time, false));
        // with second input parameters to false, we expect the function to success
        // when empty 
        $time = null;
        $this->assertTrue(check_and_valid_time($time, false));
        // incorrect date - empty string
        $time = '';
        $this->assertTrue(check_and_valid_time($time, false));
    }
    
    /**
     * 
     * Test behavior with several incorrect input datas
     */
    public function test_behavior_incorrect_input_data () {
        // Incorrect time data - Hours witch do not exist
        $time = "25:00:00";
        $this->assertFalse(check_and_valid_time($time, false));
        // Incorrect time data - minutes witch do not exist
        $time = "12:70:00";
        $this->assertFalse(check_and_valid_time($time, false));
        // Incorrect time data - secondes witch do not exist
        $time = "12:00:80";
        $this->assertFalse(check_and_valid_time($time, false));
        // incorrect time format - time without colons
        $time = "120013";
        $this->assertFalse(check_and_valid_time($time, false));
        // incorrect time format - string with letters
        $time = "abc";
        $this->assertFalse(check_and_valid_time($time, false));
        // incorrect time format - string with digits and letters
        $time = "21:ab:01";
        $this->assertFalse(check_and_valid_time($time, false));
        // incorrect date format - do not match time format
        $time = "12:00:00:59";
        $this->assertFalse(check_and_valid_time($time, false));
        // incorrect date format - one more digit in each field
        $time = "111:222:333";
        $this->assertFalse(check_and_valid_time($time, false));
        // incorrect date - null
        // with second input parameters to true, we expect the function to fail
        // when empty 
        $time = null;
        $this->assertFalse(check_and_valid_time($time, true));
        // incorrect date - empty string
        $time = '';
        $this->assertFalse(check_and_valid_time($time, true));
    } 
  
	/**
     * 
     * Test behavior with several wrong input data types
     */
    public function test_wrong_input_type () {
        // set array instead of string and expect to be wrong
        $array = array('02:11:19',);
        $this->assertFalse (check_and_valid_time ($array, false));
        // set integer instead of string and expect to be wrong
        $integer = 021119;
        $this->assertFalse (check_and_valid_time ($integer, false));
        // set float instead of string and expect to be wrong
        $float = 211.19;
        $this->assertFalse (check_and_valid_time ($float, false));
        // set date object instead of string and expect to be wrong
        $time = new DateTime('now');
        $this->assertFalse (check_and_valid_time ($time, false));
    }
}

/**
 * 
 * Unittest class allowing to test the is_it_futur tool
 * @author Aldeen Berluti
 *
 */
Class IsDateFuturChecking_UT extends PHPUnit_Framework_TestCase {

    /**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_correct_format () {
        // futur dates
        // American format
        $date = "11/25/2090";
        $this->assertTrue(is_it_futur($date));
        $date = "11/5/2090";
        $this->assertTrue(is_it_futur($date));
        $date = "11/25/2090 23:00:00";
        $this->assertTrue(is_it_futur($date));
        $date = "11/25/2090 23:20";
        $this->assertTrue(is_it_futur($date));
        // other format can be tested too (Date and Time matching format)
        
        
        // past dates
        // American format
        $date = "11/25/2012";
        $this->assertFalse(is_it_futur($date));
        $date = "11/5/2012";
        $this->assertFalse(is_it_futur($date));
        $date = "12/25/2013 23:00:00";
        $this->assertFalse(is_it_futur($date));
         // other format can be tested too (Date and Time matching format)
    }
    
/**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_incorrect_format () {

        $date = "11-25-2090";
        $this->assertFalse(is_it_futur($date));
        $date = "10-30-2090";
        $this->assertFalse(is_it_futur($date));
        $date = "11-25-2090 23:00:00";
        $this->assertFalse(is_it_futur($date));        
        //try dd/mm/yyyy
        $date = "25/09/2090";
        $this->assertFalse(is_it_futur($date));
        //try empty and null
        $date = "";
        $this->assertFalse(is_it_futur($date));
        $date = null;
        $this->assertFalse(is_it_futur($date));
    }
  
}

/**
 * 
 * Unittest class allowing to test the is_it_futur tool
 * @author Aldeen Berluti
 *
 */
Class NoDigitInputValidation_UT extends PHPUnit_Framework_TestCase {
	/**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_correct_data () {
        $lastname = 'Gates';
        $this->assertTrue(check_no_digit($lastname, false));
        $firstname = 'Bill';
        $this->assertTrue(check_no_digit($firstname, false));
        $email = 'example@gmail.com';
        $this->assertTrue(check_no_digit($email, false));
        $sentence = 'This sentence does not contain any digit.';
        $this->assertTrue(check_no_digit($sentence, false));
        // with the Actionforempty set as False, we expect the function to return
        // true when the input data is empty
        $this->assertTrue(check_no_digit('', false));
        $this->assertTrue(check_no_digit(null, false));
    }
    
	/**
     * 
     * Test behavior with several incorrect input datas
     */
    public function test_behavior_incorrect_data () {
        $lastname = 'Gates01';
        $this->assertFalse(check_no_digit($lastname, false));
        $firstname = 'Bi2ll';
        $this->assertFalse(check_no_digit($firstname, false));
        $email = 'example8@gmail.com';
        $this->assertFalse(check_no_digit($email, false));
        $sentence = 'This sentence does contain 1 digit.';
        $this->assertFalse(check_no_digit($sentence, false));
        // with the Actionforempty set as true, we expect the function to return
        // false when the input data is empty
        $this->assertFalse(check_no_digit('', true));
        $this->assertFalse(check_no_digit(null, true));
    }
    
    
	/**
     * 
     * Test behavior with several wrong input data types
     */
    public function test_wrong_input_type () {
        // set array instead of string and expect to be wrong
        $array = array('abc', 'def');
        $this->assertFalse (check_no_digit ($array, false));
        // set integer instead of string and expect to be wrong
        $integer = 021119;
        $this->assertFalse (check_no_digit ($integer, false));
        // set float instead of string and expect to be wrong
        $float = 211.19;
        $this->assertFalse (check_no_digit ($float, false));
        // set date object instead of string and expect to be wrong
        $date = new DateTime('now');
        $this->assertFalse (check_no_digit ($date, false));
    }
    
}

Class ArrayNoDigitInputValidation_UT extends PHPUnit_Framework_TestCase {
	/**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_correct_data () {
        $faker = Faker\Factory::create();
        
        $names =  array();
        // Pack correct input data in array of data
        // set correct input data - simple names
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($names,$faker->name);
        }
        $this->assertTrue(array_check_no_digit($names, false));

        // set correct input data - simple emails (no digit - see faker)
        $words = array();
        for ($i = 0 ; $i <50 ; $i++) {
           $word = $faker->word;
           array_push($words,$word);
        }
        $this->assertTrue(array_check_no_digit($words, false));
        
        $sentences = array ();
        // set correct input - simple sentences (no digit - see faker)
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($sentences,$faker->sentence);
        }
        $this->assertTrue(array_check_no_digit($sentences, false));
        
        // with the Actionforempty set as False, we expect the function to return
        // true when the input data is empty
        $this->assertTrue(array_check_no_digit(array('This is not empty','','This is not
        either'), false));
        $this->assertTrue(array_check_no_digit(array('This is not null', 'This is not
        either', 'the next one will be null', null, "The last one is not null"), 
        false));
        
        $this->assertTrue(array_check_no_digit(null, false));
        $this->assertTrue(array_check_no_digit('', false)); 
    }
    
	/**
     * 
     * Test behavior with several incorrect input datas
     */
    public function test_behavior_incorrect_data () {
        $faker = Faker\Factory::create();
        $names =  array();
        // Pack incorrect input data in array of data
        // set correct input data - simple names
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($names,$faker->name);
        }
        // then add the bad one
        array_push($names,'Bil1l');
        // sort
        sort($names);
        $this->assertFalse(array_check_no_digit($names, false));

        $words =  array();
        // set correct input data - simple words
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($words,$faker->word);
        }
        // then add the bad one
        array_push($words,'1digit');
        // sort
        sort($words);
        $this->assertFalse(array_check_no_digit($words, false));
        
        $sentences =  array();
        // set correct input data - simple emails
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($sentences,$faker->sentence);
        }
        // then add the bad one
        array_push($sentences,'1 is the number of digit in this sentence');
        // sort
        sort($sentences);
        $this->assertFalse(array_check_no_digit($sentences, false));
        
        $dump_var = array ();
        for ($i = 0 ; $i <50 ; $i++) {
           array_push($dump_var,$faker->sentence);
           array_push($dump_var,$faker->words);
        }
        $dump_var2 = $dump_var; 
              
        // test with null
        array_push($dump_var,null);
        sort($dump_var);
        $this->assertFalse(array_check_no_digit($dump_var, true));
        
        // test with empty string
        array_push($dump_var2,'');
        sort($dump_var);
        $this->assertFalse(array_check_no_digit($dump_var2, true));
        
        // test with null
        $this->assertFalse(array_check_no_digit(null, true));
        
        // test with empty string
        $this->assertFalse(array_check_no_digit('', true));
        
    }
    
    
	/**
     * 
     * Test behavior with several wrong input data types
     */
    public function test_wrong_input_type () {
        // set array integer in the array - expect to be wrong
        $array = array('abc', 'def', 8965);
        $this->assertFalse (array_check_no_digit ($array, false));
        // set array date object in the array - expect to be wrong
        $array = array('abc', date(DATE_RFC2822), 'def');
        $this->assertFalse (array_check_no_digit ($array, false));
        // set array in the array - expect to be wrong
        $array = array(array('abc','def','ghi'),'abc', 'def');
        $this->assertFalse (array_check_no_digit ($array, false));
        // set integer instead of string and expect to be wrong
        $array = array('abc', 'def', 211.19);
        $this->assertFalse (array_check_no_digit ($array, false));
    }
    
}


?>