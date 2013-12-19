<?php
require_once ('basicerrorhandling.php');
	 

/**
 * 
 * 
 * Class allowing to handle an event
 * @input : variable type (key=>Value) ; array(
 * 'id'=> string, 
 * 'location' => string,
 * 'type' => string, 
 * 'starting_date' => date,  
 * 'ending_date' => date, 
 * 'holder' => Member,
 * 'max_nb_participants' => integer, 
 * 'Participants' => array (UserX, UserY, UserW,..)
 * 'languages' => string or array of strings, 
 * 'description' => string )
 * @author Aldeen Berluti
 *
 */
Class Event {
	
	// Internal variables
	protected $_parameters_list;
	protected $_id = 0;
	protected $_location = '';
	protected $_type = '';
	protected $_starting_date = '';
	protected $_ending_date = '';
	protected $_holder = '';
	protected $_max_nb_participants = 0;
	protected $_participants = array();
	protected $_languages = array();
	protected $_description = '';
	// Define different types of events allowed
	static public $_type_range = array('Visits', 'Activities', 'Journeys', 'Parties');
	
	
	function __construct($parameters) {
		if (($this->set_up() == 0) && (is_array($parameters))) {	
			foreach ($this->_parameters_list as $key) {
				//echo $key. '<br />';
				if (!array_key_exists ($key , $parameters))	{
					trigger_error('Input '.$key .' parameter is missing to instanciate'
					.get_class($this) .' object', E_USER_ERROR);
					return E_USER_ERROR;
				}
			}
			foreach ($parameters as $key=>$value) {
				switch ($key){
					case 'id':
						$this->set_id($value);
						break;
					case 'location':
						$this->set_location ($value);
						break;
					case 'type':
						$this->set_type ($value);
						break;
					case 'starting_date':
						$this->set_starting_date ($value);
						break;
					case 'ending_date':
						$this->set_ending_date ($value);
						break;
					case 'holder':
						$this->set_holder ($value);
						break;
					case 'max_nb_participants':
						$this->set_max_nb_participants ($value);
						break;
					case 'languages':
						$this->set_languages ($value);
						break;
					case 'description':
						$this->set_description ($value);
						break;
					case 'participants':
						$this->set_participants ($value);
						break;
					default:
       					echo "This parameter $key does not exist";
				}
			}
		} else {
			trigger_error('The input parameters of the '.get_class($this) 
			.' must be an array', E_USER_ERROR);
			return E_USER_ERROR;
		}
    }
	
	/**
	 * 
	 * Set the parameterlist defaut value
	 */
	public function set_up() {
		$this->_parameters_list = array ('id','location','type', 'starting_date', 'ending_date',
		'max_nb_participants', 'holder', 'languages', 'description');
	}
	
	/**
	 * 
	 * Set the id parameter
	 * @param integer $id
	 */
	public function set_id ($id) {
		if (is_string($id))	{
			$this->_id = $id;
			return False;
		} else {
			trigger_error('The Id parameter of the '.get_class($this) 
			.' must be a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Get the id parameter
	 */
	public  function get_id () {
		if (is_string($this->_id)) {
			return $this->_id;
		} else {
			trigger_error('The Id parameter of the '.get_class($this) 
			.' should have been a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Set the location parameter
	 * @param string $location
	 */
	public  function set_location ($location) {
		// HERE : Check if it's really a location and not bullshit
		if (is_string($location)) {
			$this->_location = $location;
			return False;
		} else {
			trigger_error('The location parameter of the '.get_class($this) 
			.' must be a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
		
	}
	
	/**
	 * 
	 * Get the location parameter
	 */
	public  function get_location ()
	{
		// HERE : Check if it's really a location and not bullshit
		if (is_string($this->_location)) {
			return $this->_location;
		} else {
			trigger_error('The location parameter of the '.get_class($this) 
			.' should have been a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Set the type parameter
	 * @param string $type
	 */
	public  function set_type ($type) {
		if (!in_array ($type, self::$_type_range)) {
			trigger_error('The Type parameter of the '.get_class($this) 
			.' must be matching one of the following values ' .self::$_type_range, E_USER_ERROR);
			return E_USER_ERROR;
		} else {
			$this->_type = $type;
			return False;
		}
	}
	
	/** 
	 * 
	 * Get the type parameter
	 */
	public  function get_type () {
		if (!in_array ($this->_type, self::$_type_range)) {
			trigger_error('The Type parameter of the '.get_class($this) 
			.' should be matching one of the following values ' .self::$_type_range, E_USER_ERROR);
			return E_USER_ERROR;
		} else {
			return $this->_type;
		}
		
	}
	
	/** 
	 * 
	 * Set the starting_date parameter
	 * @param date $starting_date
	 */
	public  function set_starting_date ($starting_date) {
		if ($starting_date && (IsDate($starting_date)))	{
			$this->_starting_date = $starting_date;
			return False;
		} else {
			trigger_error('The starting_date parameter of the '.get_class($this) 
			.' must be a Date format', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	
	/**
	 * 
	 * get the starting_date parameter
	 */
	public  function get_starting_date () {
		if ($this->_starting_date && (IsDate($this->_starting_date))) {
			return $this->_starting_date;
		} else {
			trigger_error('The starting_date parameter of the '.get_class($this) 
			.' should have been a Date format', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Set the ending parameter
	 * @param date $ending_date
	 */
	public  function set_ending_date ($ending_date) {
		if ($ending_date && (IsDate($ending_date))) {
			$this->_ending_date = $ending_date;
			return False;
		} else {
			trigger_error('The ending_date parameter of the '.get_class($this) 
			.' must be a Date format', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Get the ending_date parameter
	 */
	public  function get_ending_date () {
		if ($this->_ending_date && (IsDate($this->_ending_date))) {
			return $this->_ending_date;
		} else {
			trigger_error('The ending_date parameter of the '.get_class($this) 
			.' should have been a Date format', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Set the holder id parameter
	 * @param integer $holder_id
	 */
	public  function set_holder_id($holder_id) {
		if (!empty($holder_id) && is_int($holder_id)) {
			$this->_holder_id = $holder_id;
			return False;
		} else {
			trigger_error('The holder id parameter of the '.get_class($this) 
			.' must be an integer', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Get the holder parameter
	 */
	public  function get_holder_id () {
		if ($this->_holder_id && is_int($holder_id)) {
			return $this->_holder_id;
		} else {
			trigger_error('The holder parameter of the '.get_class($this) 
			.' should have been an integer', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * set the maximal number of participants parameter
	 * @param integer $max_nb_participants
	 */
	public  function set_max_nb_participants ($max_nb_participants)	{
		if (is_int($max_nb_participants)) {
			$this->_max_nb_participants = $max_nb_participants;
			return False; 
		} else {
			trigger_error('The max_nb_participants parameter of the '
			.get_class($this) .' must be a digit', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Get the maximal number of participants parameter
	 */
	public  function get_max_nb_participants ()	{
		if (is_numeric($this->_max_nb_participants)) {
			return $this->_max_nb_participants;
		} else {
			trigger_error('The max_nb_participants parameter of the'.get_class($this) 
			.'should have been a digit', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Register members to the event (so they will become participants) 
	 * @param array of participants $participants_list
	 */
	public  function set_participants ($participants_list) {
		$participant = '';
		// if participants_list does not exist
		if (!$participants_list) {
			trigger_error('The participants parameter of the '.get_class($this)
				 .' must either be a \'Member\' typed object or an array of 
				 \'Member\' typed objects', E_USER_ERROR);
			return E_USER_ERROR;
		}
		// but if Participantlist exists and it's an array, treat it like an array
		elseif (is_array($participants_list)) {
			foreach ($participants_list as $participant) {
				if (get_class($participant) == 'Member') {
					array_push ($this->_participants, $participant);
				} else {
					trigger_error('The participants parameter of the 
						'.get_class($this) .' must either be a \'Member\' typed 
						object or an array of \'Member\' typed object',
						E_USER_ERROR);
					return E_USER_ERROR;
				}
			}
			return FALSE;
		}
		// and if it's not an array, treat it like it's not
		else {
			$participant = $participants_list;
			if (get_class($participant) == 'Member') {
				array_push ($this->_participants, $participant);
				return False;
			} else {
				trigger_error('The Participants parameter of the '.get_class($this) .'
				 	must either be	an \'Member\' typed object or an array of
					\'Member\' typed objects', E_USER_ERROR);
				return E_USER_ERROR;
			}
		}
	}
	
	/**
	 * 
	 * Get members registered to the event
	 */
	public  function get_participants () {
		if ($this->_participants == 0) {
			return False;
		} else {
			return $this->_participants;
		}
	} 

	/**
	 * 
	 * Set spoken languages for the event
	 * @param array of languages $languages
	 */
	public  function set_languages ($languages) {
		$language = '';
		// if languages does not exist
		if (!$languages){
			trigger_error('The languages parameter of the '.get_class($this) .' 
			must either be a string or an array of strings', E_USER_ERROR);
			return E_USER_ERROR;
		}
		// but if languages exists and it's an array, treat it like an array
		elseif (is_array($languages)){
			foreach ($languages as $language){
				if (is_string($language)){
					array_push ($this->_languages, $language);
				}else {
					trigger_error('The languages parameter of the '.get_class($this) .
					' must either be an array of strings', E_USER_ERROR);
					return E_USER_ERROR;
				}
			}
			return False;
		}
		// and if it's not an array, treat it like it's not
		else{
			$language = $languages;
			if (is_string($language)) {
				array_push ($this->_languages, $language);
				return False;
			} else {
				trigger_error('The languages parameter of the '.get_class($this) 
				.' must either be a string or an array of strings', E_USER_ERROR);
				return E_USER_ERROR;
			}
		}
	}
	
	/**
	 * 
	 * Get members registered to the event
	 */
	public  function get_languages (){
		if ($this->_languages == 0)	{
			return False;
		}else{
			return $this->_languages;
		}
	} 
	
	/**
	 * 
	 * Set the description parameter
	 * @param string $description
	 */ 
	public function set_description($description) {
		if (is_string($description)) {
			$this->_description = $description;
			return False;
		}else {
			trigger_error('The description parameter of the '.get_class($this) 
			.' must be a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Get the description parameter
	 */
	public  function get_description () {
		if (is_string($this->_description))	{
			return $this->_description;
		} else {
			trigger_error('The description parameter of the '.get_class($this) 
			.' should have been a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/**
	 * 
	 * Return a string containing all the informations related to the event
	 */
	public  function render () {
		$r = '<br />';
		$r .= 'Id: '. $this->get_id() . '<br />';
		$r .= 'Location: '. $this->get_location() . '<br />';
		$r .= 'Type: '. $this->get_type() . '<br />';
		$r .= 'starting_date: '. $this->get_starting_date() . '<br />';
		$r .= 'ending_date: '. $this->get_ending_date() . '<br />';
		$r .= 'Holder: '. $this->get_holder()->get_name() . '<br />';
		$r .= 'max_nb_participants: '. $this->get_max_nb_participants() . '<br />';
		$r .= 'description: '. $this->get_description() . '<br />';
		$r .= 'participants: ';
		if (is_array ($this->get_participants())) {
			foreach ($this->get_participants() as $Participant)	{
				$r .= $Participant->get_name() . ', ';
			}
		} else {
			$r .= $this->get_participants()->get_name() . ', ';
		}
		$r.= '<br />';
			
		$r .= 'languages: ';
		if (is_array ($this->get_languages())) {
			foreach ($this->get_languages() as $language) {
				$r .= $language. ', ';
			}
		}
		else {
			$r .= $this->get_language(). ', ';
		}
		$r.= '<br />';
		return $r;
	}
	
	/**
	 * 
	 * Select all event types in databse and return them as an array
	 */
	static public function select_all_event_types(){
		$dbhandler = New SqliteDbHanlder (db_parser (_INI_FILE_DIR,_SERVER_DIR));
		if (empty($dbhandler)) {
			echo 'Impossible to initiate communication with database </br>';
			return false;
		}
		
		// Look for all event types in the event types table
		$sql = 'SELECT * FROM event_types';
		$query = $dbhandler->_db_connection->prepare($sql);
		if ($query) {
			$query->execute();
			$results = $query->fetchall(PDO::FETCH_COLUMN);
			if ($results) {
				$event_types = array();
				foreach ($results as $key=>$value) {
					array_push ($event_types, html_entity_decode($value));
					}
				return $event_types;
			} else {
				echo "There is no event types in the 'event_types' table.<br/>";
				return false;
			}
		} else {
			echo "The database request for selecting event types in the 
			'event_types' table could not be prepared.<br/>";
			return false;
		}
	}
	
	
	/**
	 * 
	 * Insert a new event in the table, return false if failure or rowid of the 
	 * event if success
	 */
	public function insert_new_event (){
		$dbhandler = New SqliteDbHanlder (db_parser (_INI_FILE_DIR,_SERVER_DIR));
		if (empty($dbhandler)) {
			echo 'Impossible to initiate communication with database </br>';
			return false;
		}
		$event_name = htmlentities($this->_name, ENT_QUOTES);
		$event_location = htmlentities($this->_location, ENT_QUOTES);
		$event_type = htmlentities($this->_type, ENT_QUOTES);
		$event_checkin = htmlentities($this->_starting_date, ENT_QUOTES);
		$event_checkout = htmlentities($this->_ending_date, ENT_QUOTES);
		$event_holder = htmlentities($this->_holder, ENT_QUOTES);
		$event_max_nb_participants = htmlentities($this->_max_nb_participants, ENT_QUOTES);
		$event_description = htmlentities($this->_description, ENT_QUOTES);
		$event_participants = htmlentities($this->_participants, ENT_QUOTES);
		
		
		$sql = 'INSERT INTO event (event_name, event_type, event_location, 
		event_holder_id, event_max_nb_of_participants, event_starting_date, 
		event_ending_date) VALUES(:event_name, :event_type, :event_location, 
		:event_holder, :event_max_nb_of_participants, :event_starting_date, 
		:event_ending_date)';
		$query = $dbhandler->_db_connection->prepare($sql);
		if ($query) {
			$query->bindValue(':event_name', $event_name, PDO::PARAM_STR);
			$query->bindValue(':event_type', $event_type, PDO::PARAM_STR);
			$query->bindValue(':event_location', $event_location, PDO::PARAM_STR);
			$query->bindValue(':event_checkin', $event_checkin, PDO::PARAM_STR);
			$query->bindValue(':event_checkout', $event_checkout, PDO::PARAM_STR);
			$query->bindValue(':event_holder', $event_holder, PDO::PARAM_STR);
			$query->bindValue(':event_max_nb_of_participants', 
			$event_max_nb_of_participants, PDO::PARAM_STR);
			$query->bindValue(':event_description', $event_description, PDO::PARAM_STR);
			// PDO's execute() gives back TRUE when successful, 
			// false when not
			$registration_success_state = $query->execute();
			if ($registration_success_state) {
				//TODO Check that participants are in the user table and insert
				// them into the participants event table
				// return the event id back ( needed for the particpants)	
				echo "$event_name has been successfuly inserted in the 
				'event' table. <br/>";
				return true;
			} else {
				echo "$event_name failed to be inserted in the 
				'events' table. <br/>";
				//print_r ($query);
				return false;
			}
		} else {
			echo "The database request for inserting $event_name 
			in the 'events' table could not be prepared.<br/>";
			return false;
		}
	}
	
}

?>