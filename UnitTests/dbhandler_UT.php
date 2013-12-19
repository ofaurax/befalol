<?php

// error reporting config
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once ('./php/dbhandler.php');
//require_once ('./vendor/autoload.php');

/****************************************************************/
/** Must change the path of _SERVER_DIR  according to the user **/
/****************************************************************/
define ('_SERVER_DIR', '/Users/aldeen/Desktop/Projet/Site');
/****************************************************************/
define ('_INI_FILE_DIR', _SERVER_DIR."/befalol/database/config.ini" );


Class SqliteDbTableHanlderUT extends PHPUnit_Framework_TestCase {
	
	public $_db = NULL;

	function SetUp () {
		$this->_db = new SqliteDbTableHanlder(db_parser (_INI_FILE_DIR,_SERVER_DIR));
	}
	
	// Test if return values are correct when attempt to create table is possible without 
	// database connexion
	function test_db_no_connection (){
	
		// Check that the database connection is set, because at this point, it should be.
		$this->assertNotEquals(NULL, $this->_db->_db_connection,'Database connexion must be set'); 
		// close the database connection
		$this->_db->_db_connection = NULL;
		// Then check weither wheither methods return the correct value when attempt table creation 
		$this->assertFalse($this->_db->create_countries_table(), 'Without database connexion, 
			It should be impossible to create table');
		$this->assertFalse($this->_db->create_event_type_table(), 'Without database connexion, 
			It should be impossible to create table');
		$this->assertFalse($this->_db->create_cities_table(), 'Without database connexion, 
			It should be impossible to create table');
		$this->assertFalse($this->_db->create_languages_table(), 'Without database connexion, 
			It should be impossible to create table');
		$this->assertFalse($this->_db->create_languages_event_table(), 'Without database connexion, 
			It should be impossible to create table');
		$this->assertFalse($this->_db->create_users_table(), 'Without database connexion, 
			It should be impossible to create table');
		$this->assertFalse($this->_db->create_events_table(), 'Without database connexion, 
			It should be impossible to create table');
		$this->assertFalse($this->_db->create_participants_event_table(), 'Without database connexion, 
			It should be impossible to create table');
		$this->assertFalse($this->_db->create_visited_countries_table(), 'Without database connexion, 
			It should be impossible to create table');		
	}
		
	function TearDown () {
		$this->_db->delete_all_tables ();
	}
	
}

?>