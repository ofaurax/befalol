<?php 

require_once ('./php/libraries/eventhandler.php');
require_once ('./php/libraries/geographyhandler.php');
require_once ('./php/libraries/dbhandler.php');
require_once ('./php/libraries/userhandler.php');
require_once ('./php/libraries/tools.php');
require_once ('./php/libraries/password_compatibility_library.php');

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
define ('_SERVER_DIR', getcwd());
define ('_INI_FILE_DIR', _SERVER_DIR."/database/config_UT.ini" );

/**
 * 
 * Unittest allowing to test the array_data_validation tool
 * @author Aldeen Berluti
 *
 */
Class UserObjectTest extends PHPUnit_Framework_TestCase {
    
    protected function setUp() {
        // Retrieve all nationalities
        $this->nationalities = Nationality::select_all_nationalities();
        $this->faker = Faker\Factory::create();
        
        $user_id = rand(0,500);
        $user_name = $this->faker->firstName;
        $user_lastname = $this->faker->lastName;
        $user_firstname = $this->faker->firstName;
        $user_birthday = $this->faker->dateTimeBetween($startDate = 
        	'-50 years', $endDate = '-10 years');
        $user_email = $this->faker->email;
        $user_nationality = $this->nationalities[array_rand($this->nationalities)];
        $user_password = $this->faker->word;
        
        $parameters = array ('user_id' => $user_id, 'user_name' => $user_name, 'user_lastname' => $user_lastname,
         'user_firstname' => $user_firstname, 'user_birthday' => $user_birthday->format('Y-m-d h:m:s'), 
         'user_email' => $user_email, 'user_nationality' => $user_nationality, 
         'user_password_hash' => password_hash($user_password, PASSWORD_DEFAULT));
        
        $this->user = new User($parameters);
        
    }

     /**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_correct_input_datas () {
        // test 50 times and check that input values are output values 
        for ($i=0; $i < 50 ; $i++) { 
            $user_id = rand(0,500);
            $user_name = $this->faker->firstName;
            $user_lastname = $this->faker->lastName;
            $user_firstname = $this->faker->firstName;
            $user_birthday = $this->faker->dateTimeBetween($startDate = 
            	'-50 years', $endDate = '-10 years');
            $user_email = $this->faker->email;
            $user_nationality = $this->nationalities[array_rand($this->nationalities)];
            $user_password = $this->faker->word;
            
            $parameters = array ('user_id' => $user_id, 'user_name' => $user_name, 'user_lastname' => $user_lastname,
             'user_firstname' => $user_firstname, 'user_birthday' => $user_birthday->format('Y-m-d h:m:s'), 
             'user_email' => $user_email, 'user_nationality' => $user_nationality, 
             'user_password_hash' => password_hash($user_password, PASSWORD_DEFAULT));
            
            $user = new User($parameters);
            $this->assertEquals($user_id, $user->get_user_id());
            $this->assertEquals($user_name, $user->get_string_attribute('user_name'));
            $this->assertEquals($user_lastname, $user->get_string_attribute('user_lastname'));
            $this->assertEquals($user_firstname, $user->get_string_attribute('user_firstname'));
            $this->assertEquals($user_email, $user->get_string_attribute('user_email'));
            $this->assertEquals($user_birthday->format('Y-m-d h:m:s'), $user->get_string_attribute('user_birthday'));        
            $this->assertEquals($user_nationality, $user->get_string_attribute('user_nationality'));
            $this->assertTrue(password_verify($user_password, $user->get_string_attribute('user_password_hash'))); 
        }
    }
    
    
/**
     * 
     * Test behavior with several correct input datas
     */
    public function test_behavior_incorrect_input_type () {
       // try to set a string as id type
       $this->assertFalse($this->user->set_user_id('abcd'));
       // try to set a float as id type
       $this->assertFalse($this->user->set_user_id(89.1));
       // try to set a Date object as id type
       $date = new DateTime('now');
       $this->assertFalse($this->user->set_user_id($date));
       // try to set a integer as user_name
       $this->assertFalse($this->user->set_string_attribute(array('user_name' => 980)));
       // try to set a float for as user_name
       $this->assertFalse($this->user->set_string_attribute(array('user_name' => 9,80)));
       // try to set a Date object for an id type
       $this->assertFalse($this->user->set_string_attribute(array('user_name' => $date)));
       // try to set a integer instead of a string date for a birthday date
       $this->assertFalse($this->user->set_user_birthday(18560));
       // try to set a float instead of a string date for a birthday date
       $this->assertFalse($this->user->set_user_birthday(89.8));
       // try to set a date object instead of a string date for a birthday date
       $this->assertFalse($this->user->set_user_birthday($date));
       // try to set an incorrect string date for a birthday date - wrong format
       $this->assertFalse($this->user->set_user_birthday('12.28.1998'));
       // try to set an incorrect string date for a birthday date - inexistant date
       $this->assertFalse($this->user->set_user_birthday('1998-13-28'));
       $this->assertFalse($this->user->set_user_birthday('13/28/1998'));
       $this->assertFalse($this->user->set_user_birthday('28/05/2005'));
       $this->assertFalse($this->user->set_user_birthday('2005-28-05'));
       // set a date in february which does not exists
       $this->assertFalse($this->user->set_user_birthday('02/31/2005'));
       $this->assertFalse($this->user->set_user_birthday('2005-02-31'));
    }
    
}
    
?>