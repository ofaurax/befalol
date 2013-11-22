<?php

// error reporting config
error_reporting(E_ALL);
ini_set('display_errors', 1);
if(!empty($_SERVER ['DOCUMENT_ROOT'])) {
	define ('_SERVER_DIR', $_SERVER ['DOCUMENT_ROOT']);
	define ('_INI_FILE_DIR', _SERVER_DIR."/befalol/database/config.ini" );
}

// Parse the config.ini file in order to return database informations
function db_parser ($ini,$server_path) 
{
	
	$db_path ='';
	$parse = parse_ini_file ( $ini , true ) ;

	$driver = $parse [ "db_driver" ] ;;
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


Class SqliteDbHanlder {
	
	public $_db_connection = NULL;
	
	function __Construct ($parameters) {
		$this->init_db ($parameters);
	}
	
	// init connection with the database, using PDO
	private function init_db ($parameters) {
		if ($parameters){
			// create new database file / connection
			try {
				$this->_db_connection = new PDO($parameters['dsn']);
				return TRUE;
			} catch (PDOException $e) {
				echo 'Database connection failed : '. $e->getMessage();
				return FALSE;
			}
		}
		return FALSE;
	}
	
	//function insert_country ($country)
	
	// Call all the methods which are going to create table in the db
	// KEEP THE ORDER OF CREATION, THERE ARE DEPENDENCIES
	// Return True if one of the methods fails
	function create_tables ()
	{
		$errno = TRUE;
		$errno = $errno && $this->create_countries_table ();
		$errno = $errno && $this->create_event_type_table ();
		$errno = $errno && $this->create_cities_table ();
		$errno = $errno && $this->create_languages_table ();
		$errno = $errno && $this->create_languages_event_table ();
		$errno = $errno && $this->create_users_table ();
		$errno = $errno && $this->create_events_table ();
		$errno = $errno && $this->create_participants_event_table ();
		$errno = $errno && $this->create_visited_countries_table ();
		return $errno;
		
	}
	
	
	
	// Call all the methods which are going to create table in the db
	// Return True if one of the methods fails
	function delete_all_tables ()
	{
		$errno = TRUE;
		$errno = $errno && $this->delete_table ('countries');
		$errno = $errno && $this->delete_table ('event_types');
		$errno = $errno && $this->delete_table ('cities');
		$errno = $errno && $this->delete_table ('languages');
		$errno = $errno && $this->delete_table ('languages_event');
		$errno = $errno && $this->delete_table ('users');
		$errno = $errno && $this->delete_table ('events');
		$errno = $errno && $this->delete_table ('participants_event');
		$errno = $errno && $this->delete_table ('visited_countries_by_users');
		return $errno;

	}
	
	
	// Call all the methods which are going to create table in the db
	// Return True if the method fails
	function delete_table ($table_name)
	{
		$errno = TRUE;
		// Delete the table in the database
		$sql = 'DROP TABLE IF EXISTS '.$table_name.';';
		
		// prepare and execute the sqlite request
		$errno = $this->prepare_and_execute_query ($sql);
		if ($errno == FALSE)
		{
			echo "The table $table_name could not been deleted";
		}
		//return TRUE if no connection with db
		return $errno;
	}
	
	
	// Prepare and execute the Input parameter query
	function prepare_and_execute_query ($sql)
	{
		if ($this->_db_connection){
			$query = $this->_db_connection->prepare($sql);
			return $query->execute();
		}
		echo ' The database connection could not been established';
		return FALSE;
	}
	
	
	// Create table for countries
	function create_countries_table ()
	{
		// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS countries(
				country_name TEXT PRIMARY KEY,
				country_nationality TEXT UNIQUE
				);';
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
		// Check if the table exists
		//$sql = 'SELECT * FROM countries';
//		$query = $this->_db_connection->prepare($sql);
//		$query->execute();
//		if ($query->columnCount()) {
//			echo 'The table "COUNTRIES" exits.'.'<br/>';
//			print_r ($query->errorInfo());
//			echo '<br/>';
//			print_r ($query->errorInfo());
//			//default return
//			return FALSE;
//		} else {
//			echo 'The table "COUNTRIES" does not exist.'.'<br/>';
//			print_r ($query->errorInfo());
//			return TRUE;
//		}
		
	}
	
	// Create table for cities
	function create_cities_table ()
	{
		// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS cities (
				city_name TEXT PRIMARY KEY,
				country_name TEXT,
				FOREIGN KEY (country_name) REFERENCES countries (country_name)
				);';	
									
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
	}
	
	// Create table for event type
	function create_event_type_table ()
	{
		// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS event_types (
				event_type_name TEXT PRIMARY KEY
				);';	
									
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
	}
	
	
	// Create table for events
	function create_events_table ()
	{
		// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS events (
				event_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				event_name TEXT NOT NULL,
				event_type TEXT NOT NULL ,
				event_location TEXT NOT NULL,
				event_holder_id INTEGER NOT NULL,
				event_max_nb_of_participants INTEGER,
				event_starting_date NUMERIC NOT NULL,
				event_ending_date NUMERIC NOT NULL,
				FOREIGN KEY (event_type) REFERENCES event_types (event_type_name),
				FOREIGN KEY (event_holder_id) REFERENCES users (user_id)
				);';
									
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
	}
	
	
	// Create table for languages
	function create_languages_table ()
	{
		// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS  languages (
				language_name TEXT PRIMARY KEY,
				language_country TEXT,
				FOREIGN KEY (language_country) REFERENCES countries (country_name)
				);';
									
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
	}
	
	// Create table for languages that will be spoken at an event
	function create_languages_event_table ()
	{
			// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS languages_event (
				event_id INTEGER,
				language_name TEXT,
				FOREIGN KEY (event_id) REFERENCES events (event_id),
				FOREIGN KEY (language_name) REFERENCES languages (language_name)
				);';
									
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
	}
	
	
	// Create table for users that will be participating to an event
	function create_participants_event_table ()
	{
		// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS participants_event (
				event_id INTEGER,
				user_id INTEGER ,
				FOREIGN KEY (event_id) REFERENCES events (event_id),
				FOREIGN KEY (user_id) REFERENCES users (user_id)
				);';
									
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
	}
	
	
	// Create table for users
	function create_users_table ()
	{
		// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS users (
				user_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				user_name TEXT NOT NULL,
				user_email TEXT NOT NULL UNIQUE,
				user_password_hash TEXT NOT NULL,
				user_birthday NUMERIC,
				user_nationality TEXT,
				user_lastname TEXT,
				user_firstname TEXT,
				FOREIGN KEY (user_nationality) REFERENCES countries (country_nationality));
				CREATE UNIQUE INDEX user_name_UNIQUE ON users (user_name);';
									
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
	}
	
	
	// Create table for countries that a user has already visited
	function create_visited_countries_table ()
	{
		// create new empty table inside the database (if table does not already exist)
		$sql = 'CREATE TABLE IF NOT EXISTS visited_countries_by_users (
				user_id INTEGER,
				visited_country TEXT,
				FOREIGN KEY (user_id) REFERENCES users (user_id),
				FOREIGN KEY (visited_country) REFERENCES countries (country_name)
				);';
									
		// prepare and execute the sqlite request
		// return FALSE if no connection with db
		return $this->prepare_and_execute_query ($sql);
	}
}

// parse configuration in the ini file
$dbhandler = new SqliteDbHanlder(db_parser (_INI_FILE_DIR,_SERVER_DIR));
$dbhandler->create_tables();
//$dbhandler->delete_all_tables();

?>