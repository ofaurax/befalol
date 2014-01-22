<?php

/**
 *
 * This class allows us to have access a sql database using PDO
 * @author Aldeen Berluti
 *
 */
Class SqliteDbHanlder {


    public $_db_connection = NULL;

    /**
     *
     * Build the object by creating connection with the database
     * @param unknown_type $parameters
     */
    function __Construct ($parameters) {
        $this->db_connect ($parameters);
        $this->enable_foreign_keys();
    }


    function __wakeup()
    {
        $this->dbh = new PDO($parameters['dsn']);
    }

    function __sleep()
    {
        return array('data');
    }


    /**
     *
     *  Init database connection
     * @param array('key' => 'value') $parameters
     */
    protected function db_connect ($parameters) {
        if ($parameters){
            // create new database file / connection
            try {
                $options =  $parameters['options'];
                $this->_db_connection = new PDO($parameters['dsn'], 'charset=UTF-8');
                return true;
            } catch (PDOException $e) {
                echo 'Database connection failed : '. $e->getMessage();
                return false ;
            }
        }
        return false ;
    }

    /**
     *
     * Enable foreign keys for the database
     */
    protected function enable_foreign_keys () {

        if (!empty($this->_db_connection)) {
            $this->_db_connection->exec( 'PRAGMA foreign_keys = ON;' );
            return true;
        }else {
            echo 'Unable to set the foreign keys on. <br/>';
            return false;
        }
    }

    /**
     *
     * Disable foreign keys for the database
     */
    protected function disable_foreign_keys () {

        if (!empty($this->_db_connection)) {
            $this->_db_connection->exec( 'PRAGMA foreign_keys = OFF;' );
            return true;
        }else {
            echo 'Unable to set the foreign keys off. <br/>';
            return false;
        }
    }

    /**
     *
     * Return the database connection
     */
    public function get_connection_object () {
        if (!empty($this->_db_connection))  {
            return $this->_db_connection;
        }
        else {return false;}
    }


    /**
     *
     * Prepare and execute the Input parameter query
     * @param string $sql
     */
    protected function prepare_and_execute_query ($sql)
    {
        if ($this->_db_connection){
            $query = $this->_db_connection->prepare($sql);
            if (!empty($query)) {
                return $query->execute();    
            }else {
                echo 'The request preparation has failed';
            }
        }
        echo ' The database connection could not been established';
        return false;
    }

    /**
     *
     * Disconnect with the database
     */
    public function db_disconnect ()
    {
        if ($this->_db_connection){
            $this->_db_connection = NULL;
            return true;
        }
        echo ' The database connection could not been terminated';
        return false;
    }
}



/**
 *
 * This class allows us to handle tables (mainly create and delete tables)
 * @author aldeen
 *
 */
Class SqliteDbTableHanlder extends SqliteDbHanlder {


    /**
     *
     * Call all methods which are going to create tables in the db
     * Return false if one of the methods fails. The running order can be
     * changed as long as the foreign keys are not enable on the database
     */
    function create_tables ()
    {
        $errno = true;
        $errno = $this->disable_foreign_keys () && $errno;
        $errno = $this->create_countries_table () && $errno;
        $errno = $this->create_event_type_table () && $errno;
        /*$errno = $this->create_cities_table () && $errno;*/
        $errno = $this->create_languages_table () && $errno;
        $errno = $this->create_countries_languages_table () && $errno;
        $errno = $this->create_event_languages_table () && $errno;
        $errno = $this->create_users_table () && $errno;
        $errno = $this->create_events_table () && $errno;
        $errno = $this->create_event_participants_table () && $errno;
        $errno = $this->create_event_holders_table () && $errno;
        $errno = $this->create_visited_countries_table () && $errno;
        $errno = $this->create_nationalities_table () && $errno;
        $errno = $this->enable_foreign_keys () && $errno;
        return $errno;
    }



    /**
     *
     * Call all methods which are going to delete tables in the db
     * Return false if one of the methods fails. The running order can be
     * changed as long as the foreign keys are not enable on the database
     */
    function delete_all_tables ()
    {
        $errno = true;
        $errno = $this->disable_foreign_keys () && $errno;
        $errno = $this->delete_table ('countries') && $errno;
        $errno = $this->delete_table ('event_types') && $errno;
        /*$errno = $this->delete_table ('cities') && $errno;*/
        $errno = $this->delete_table ('languages') && $errno;
        $errno = $this->delete_table ('countries_languages') && $errno;
        $errno = $this->delete_table ('event_languages') && $errno;
        $errno = $this->delete_table ('users') && $errno;
        $errno = $this->delete_table ('events') && $errno;
        $errno = $this->delete_table ('event_participants') && $errno;
        $errno = $this->delete_table ('event_holders') && $errno;
        $errno = $this->delete_table ('visited_countries_by_users') && $errno;
        $errno = $this->delete_table ('nationalities') && $errno;
        $errno = $this->enable_foreign_keys () && $errno;
        return $errno;

    }


    /**
     *
     * Delete the table whose name has been given as Input parameter
     * @param string $table_name
     */
    function delete_table ($table_name)
    {
        $errno = true;
        // Delete the table in the database
        $sql = 'DROP TABLE IF EXISTS '.$table_name.';';

        // prepare and execute the sqlite request
        $errno = $this->prepare_and_execute_query ($sql);
        if ($errno == false )
        {
            echo "The table $table_name could not been deleted";
        }
        //return TRUE if no connection with db
        return $errno;
    }



    /**
     *
     *  Create table for countries
     */
    function create_countries_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS countries(
				country_name TEXT NOT NULL PRIMARY KEY,
				country_index TEXT NOT NULL UNIQUE
				);';
        // prepare and execute the sqlite request
        // return false if no connection with db
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
        //			return false;
        //		} else {
        //			echo 'The table "COUNTRIES" does not exist.'.'<br/>';
        //			print_r ($query->errorInfo());
        //			return TRUE;
        //		}

    }

    /**
     *
     * 	 Create table for cities
     */
    /*function create_cities_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS cities (
				city_name TEXT NOT NULL,
				country_name TEXT NOT NULL,
				FOREIGN KEY (country_name) REFERENCES countries (country_name)
				);';	
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }*/

    /**
     *
     * Create table for event type
     */
    function create_event_type_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS event_types (
				event_type_name TEXT PRIMARY KEY
				);';	
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }


    /**
     *
     * Create table for events
     */
    function create_events_table ()
    {
        // create new empty table inside the database
        //(if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS events (
				event_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				event_name TEXT NOT NULL,
				event_type TEXT NOT NULL,
				event_max_nb_of_participants INTEGER NOT NULL,
				event_starting_date TEXT NOT NULL,
				event_ending_date TEXT NOT NULL,
				event_description TEXT,
				event_address TEXT NOT NULL,
				event_zipcode TEXT NOT NULL,
				event_city_name TEXT NOT NULL,
				event_country_name TEXT NOT NULL,
				FOREIGN KEY (event_country_name) REFERENCES countries (country_name),
				FOREIGN KEY (event_type) REFERENCES event_types (event_type_name)
				);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }


    /**
     *
     *  Create table for languages
     */
    function create_languages_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS  languages (
				language_name TEXT PRIMARY KEY
				);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }


    
    /**
     *
     * Create table for languages that will be spoken at an event
     */
    function create_event_languages_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS event_languages (
				event_id INTEGER NOT NULL,
				language_name TEXT NOT NULL,
				FOREIGN KEY (event_id) REFERENCES events (event_id),
				FOREIGN KEY (language_name) REFERENCES languages (language_name)
				);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }

	/**
     *
     * Create table for languages
     */
    function create_countries_languages_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS  countries_languages (
				country_name TEXT NOT NULL,
				language_name TEXT NOT NULL,
				FOREIGN KEY (country_name) REFERENCES countries (country_name),
				FOREIGN KEY (language_name) REFERENCES languages (language_name)
				);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }

    /**
     *
     * Create table for users that will be participating to an event
     */
    function create_event_participants_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS event_participants (
				event_id INTEGER NOT NULL,
				user_id INTEGER,
				FOREIGN KEY (event_id) REFERENCES events (event_id),
				FOREIGN KEY (user_id) REFERENCES users (user_id)
				);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }

    
	/**
     *
     * Create table for users that hold the event
     */
    function create_event_holders_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS event_holders(
				event_id INTEGER NOT NULL,
				user_id INTEGER NOT NULL,
				FOREIGN KEY (event_id) REFERENCES events (event_id),
				FOREIGN KEY (user_id) REFERENCES users (user_id)
				);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }


    /**
     *
     * Create table for users
     */
    function create_users_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS users (
				user_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
				user_name TEXT NOT NULL,
				user_email TEXT NOT NULL UNIQUE,
				user_password_hash TEXT NOT NULL,
				user_birthday TEXT,
				user_nationality TEXT,
				user_lastname TEXT,
				user_firstname TEXT,
				FOREIGN KEY (user_nationality) REFERENCES nationalities (nationality_name));
				CREATE UNIQUE INDEX user_name_UNIQUE ON users (user_name);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }


    /**
     *
     * Create table for countries that a user has already visited
     */
    function create_visited_countries_table ()
    {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS visited_countries_by_users (
				user_id INTEGER NOT NULL,
				visited_country TEXT,
				FOREIGN KEY (user_id) REFERENCES users (user_id),
				FOREIGN KEY (visited_country) REFERENCES countries (country_name)
				);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }

    /**
     * Create table for all nationalities
     */
    function create_nationalities_table () {
        // create new empty table inside the database
        // (if table does not already exist)
        $sql = 'CREATE TABLE IF NOT EXISTS nationalities (
				nationality_name TEXT NOT NULL PRIMARY KEY
				);';
        	
        // prepare and execute the sqlite request
        // return false if no connection with db
        return $this->prepare_and_execute_query ($sql);
    }
}


// parse configuration in the ini file
//$dbhandler = new SqliteDbTableHanlder(db_parser (_INI_FILE_DIR,_SERVER_DIR));
//$dbhandler->delete_all_tables();
//$dbhandler->create_tables();

?>