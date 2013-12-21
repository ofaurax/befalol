<?php

/**
 * 
 * Enter description here ...
 * @author Aldeen Berluti
 *
 */
Class User {
	
	protected $_user_id = 0;
	protected $_user_name = '';
	protected $_user_email = '';
	protected $_user_birthday = '';
	protected $_user_nationality = '';
	protected $_user_firstname = '';
	protected $_user_lastname = '';
	protected $_db_connection = NULL;
	
	
	
	/**
	 * 
	 * Instanciate the user object
	 * @param array('key' => 'value',..) $parameters
	 */
	function __construct($parameters) {
		$errno = TRUE;
		if (is_array($parameters)) {	
			foreach ($parameters as $key=>$value) {
				switch ($key) {
					case 'user_name':
					case 'user_nationality':
					case 'user_email':
					case 'user_lastname':
					case 'user_firstname':
						$errno = $this->set_string_attribute (array($key => $value)) && $errno;
						break;
					case 'user_id':
						$errno = $this->set_user_id ($value) && $errno;
						break;
					case 'user_password':
						$errno = $this->set_user_password ($value) && $errno;
						break;
					case 'user_birthday':
						$errno = $this->set_user_birthday ($value) && $errno;
						break;
					default:
						$errno = FALSE;  
       					echo "This parameter $key does not exist";
						break;
				}
			}
			return $errno;
		}
		else {
			echo 'The input parameters of the '.get_class($this).
				 ' must be an array <br/>';
			return FALSE;
		}
    }
	
    
    
    /**
	 * 
	 * Set the  key=>value given, as an attribute of the user object. Key would
	 * be the name of the attribute and value its value
	 * @param array $parameter
	 */
	public function set_string_attribute ($parameter) {
		if (is_array($parameter))
		{	
			foreach ($parameter as $key => $value){
				//echo $value.'<br/>';
				if (!empty($key)  
					&& is_string ($value) 
					&& !empty($value)  
					&& is_string ($key) ) {
					$dump_string = '_'.$key;
					$this->$dump_string = $value;
					return TRUE;		
				}
				else {
					echo "Impossible to set the $key attribute <br/> ";
					return FALSE;
				}
			}
		}
		else {
			echo 'The input parameters must be an array <br/>';
			return FALSE;
		}
	}
	
	
	/**
	 * 
	 * Set the user id
	 * @param integer $id
	 */
	public function set_user_id ($id)
	{
		if (!empty($id) && is_int($id)){
			$this->_user_id = $id;
			return True;
		}
		else {
			echo 'The id input parameters must be an integer <br/>';
			return FALSE;
		}		
	}
	
	/**
	 * 
	 * Get the user id
	 */
	public function get_user_id ()
	{
		if (!empty($this->_user_id) && is_int($this->_user_id)){
			return $this->_user_id;
		}
		else {
			echo 'The user id should have been an integer <br/>';
			return FALSE;
		}		
	} 
		
	/**
	 * 
	 * Set the user pwd
	 * @param string $pwd
	 */
	public function set_user_password ($pwd)
	{
		/*if (!empty($pwd)){
			$this->_user_birthday = $birthday;
			return True;
		}
		else {
			echo 'The input parameters must be an array';
			return FALSE;
		}*/		
	}

	
	/**
	 * 
	 * Set the user birthday date
	 * @param date $birthday
	 */
	public function set_user_birthday ($birthday)
	{
		if (!empty($birthday) && check_and_valid_date ($birthday, true)){
			$this->_user_birthday = $birthday;
			return True;
		}
		else {
			echo 'The input parameters must be a date <br/>';
			return FALSE;
		}		
	}
	
	/**
	 * 
	 * Get the value of the attribute named by the value of $attribute_name
	 * @param  string $attribute_name
	 */
	public function get_string_attribute($attribute_name)
	{
		if (!empty($attribute_name) && is_string($attribute_name))
		{
			$dump_var = '_'.$attribute_name;
			return $this->$dump_var;
		}
		else
		{
			trigger_error('The name parameter of the'.get_class($this) .'must 
			be a string', E_ERROR);
			return E_ERROR;
		}
		
	}
	
	
	public function update_user_data ()
	{
		// Get the database connection if it's not the case yet
		$dbhandler = Null;
		$dbhandler = new SqliteDbHanlder (db_parser (_INI_FILE_DIR,_SERVER_DIR));
		if (empty($dbhandler))  {
			echo 'Impossible to initiate communication with database </br>';
			return False;
		}
		// Update data for user in user table
		$user_nationality = htmlentities($this->_user_nationality, ENT_QUOTES);
		$user_name = htmlentities($this->_user_name, ENT_QUOTES);
		$user_email = htmlentities($this->_user_email, ENT_QUOTES);
		$user_lastname = htmlentities($this->_user_lastname, ENT_QUOTES);
		$user_firstname = htmlentities($this->_user_firstname, ENT_QUOTES);
		$user_birthday = htmlentities($this->_user_birthday, ENT_QUOTES);
		$sql = 'UPDATE users
		SET user_email = :user_email, user_lastname = :user_lastname, 
		user_firstname = :user_firstname, user_nationality = :user_nationality,
		user_birthday = :user_birthday WHERE user_name = :user_name';
		$query = $dbhandler->_db_connection->prepare($sql);
		if ($query) {
			$query->bindValue(':user_email', $user_email, 
					PDO::PARAM_STR);
			$query->bindValue(':user_name', $user_name, 
					PDO::PARAM_STR);
			$query->bindValue(':user_nationality', $user_nationality, 
					PDO::PARAM_STR);
			$query->bindValue(':user_birthday', $user_birthday, 
					PDO::PARAM_STR);
			$query->bindValue(':user_lastname', $user_lastname, 
					PDO::PARAM_STR);
			$query->bindValue(':user_firstname', $user_firstname, 
					PDO::PARAM_STR);
			// PDO's execute() gives back TRUE when successful, 
			// false when not
			$registration_success_state = $query->execute();
			if ($registration_success_state) {
				echo "$user_name has been successfuly updated. <br/>";
				return true;
			} else {
				echo "$user_name failed to be updated. <br/>";
				print_r ($query->errorInfo());
				return false;
			}
		} else {
			echo "The database request for updating $user_name datas
			in the 'users' table could not be prepared.<br/>";
			print_r ($dbhandler->_db_connection->errorInfo());
			return false;
		}
	}

	/**
	 * 
	 * Select and return an array of all user names exisisting in the table
	 */
	static public function select_all_user () {
		// Get the database connection if it's not the case yet
		$dbhandler = new SqliteDbHanlder (db_parser (_INI_FILE_DIR,_SERVER_DIR));
		if (empty($dbhandler)) {
			echo 'Impossible to initiate communication with database </br>';
			return false;
		}
		// Look for existing nationality_name in the nationalities table
		$sql = 'SELECT user_name FROM users';
		$query = $dbhandler->_db_connection->prepare($sql);
		if ($query) {
			$query->execute();
			$results = $query->fetchall(PDO::FETCH_COLUMN);
			if ($results) {
				$users = array();
				foreach ($results as $key=>$value) {
					array_push ($users, html_entity_decode($value));
					}
				return $users;
			} else {
				echo "There is no users in the 'users' table.<br/>";
				return false;
			}
		} else {
			echo "The database request for selecting users in the 
			'users'	table could not be prepared.<br/>";
			return false;
		}
	}	
	
	/**
	 * 
	 * Select and return an array of user events
	 */
	 public function select_user_events () {
		// Get the database connection if it's not the case yet
		$dbhandler = new SqliteDbHanlder (db_parser (_INI_FILE_DIR,_SERVER_DIR));
		if (empty($dbhandler)) {
			echo 'Impossible to initiate communication with database </br>';
			return false;
		}
		// Look for events held by the event holder id in the events table
		$sql = 'SELECT * FROM events WHERE event_holder_id = :event_holder_id';
		$query = $dbhandler->_db_connection->prepare($sql);
		if ($query) {
			$query->bindValue(':event_holder_id', $this->_user_id, PDO::PARAM_INT);
			$query->execute();
			$events = array();
			$results = $query->fetchall();
			foreach ($results as $result_row) {
				//then look for the languages spoken at this event 
				$sql = 'SELECT language_name FROM languages_event 
					WHERE event_id = :event_id';
				$query = $dbhandler->_db_connection->prepare($sql);
				if ($query) {
					$query->bindValue(':event_id', $result_row['event_id'], 
					PDO::PARAM_INT);
					$query->execute();
					$languages_res = $query->fetchall(PDO::FETCH_COLUMN);
					if ($languages_res) {
						$languages = array();
						foreach ($languages_res as $key=>$value) {
							array_push ($languages, html_entity_decode($value));
						}
					} else {
						echo 'There is no language spoken at this event <br/>';
						return false;
					}						
				}else {
					echo "The request for selecting spoken languages could not 
					be prepared. <br/>";
					return false;
				}
				// bundle all input parameters
				$parameters = array ('event_id' => intval($result_row['event_id']),
				'event_name' => html_entity_decode($result_row['event_name']), 
				'event_location' => html_entity_decode($result_row['event_location']), 
				'event_type' => html_entity_decode($result_row['event_type']), 
				'event_starting_date' => html_entity_decode($result_row['event_starting_date']),
		    	'event_ending_date'=> html_entity_decode($result_row['event_ending_date']), 
		    	'event_max_nb_participants' => intval($result_row['event_max_nb_of_participants']), 
		    	'event_holder_id' => intval($result_row['event_holder_id']), 
		    	'event_description' => html_entity_decode( $result_row['event_description']),
				'event_languages' => $languages);
				// create new object event with input parameters
				$event = new Event ($parameters);
				if (!empty($event)) {
					// create an array of event objects
					array_push ($events, $event);
				}
			}
			// return the array of events objects;
			return $events;
		} else {
			echo "The database request for selecting users in the 
			'users'	table could not be prepared.<br/>";
			return false;
		}
	}
}
?>