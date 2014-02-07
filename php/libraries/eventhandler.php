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
 * 'holders_ids' => Member,
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
    protected $_name = '';
    protected $_type = '';
    protected $_starting_date = '';
    protected $_ending_date = '';
    protected $_holders_ids = array();
    protected $_max_nb_participants = 0;
    protected $_participants = array();
    protected $_languages = array();
    protected $_description = '';
    protected $_location = null;


    // Define different types of events allowed
    static public $_type_range = array('Visits', 'Activities', 'Journeys', 'Parties');


    function __construct($parameters) {
        if (($this->set_up()) && (is_array($parameters))) {
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
                    case 'event_name':
                        $this->set_name($value);
                        break;
                    case 'event_id':
                        $this->set_id($value);
                        break;
                    case 'event_type':
                        $this->set_type ($value);
                        break;
                    case 'event_starting_date':
                        $this->set_starting_date ($value);
                        break;
                    case 'event_ending_date':
                        $this->set_ending_date ($value);
                        break;
                    case 'event_holders_ids':
                        $this->set_holders_ids ($value);
                        break;
                    case 'event_max_nb_participants':
                        $this->set_max_nb_participants ($value);
                        break;
                    case 'event_languages':
                        $this->set_languages ($value);
                        break;
                    case 'event_description':
                        $this->set_description ($value);
                        break;
                    case 'event_participants':
                        $this->set_participants ($value);
                        break;
                    case 'event_location':
                        $this->set_location ($value);
                        break;
                    default:
                        echo "This parameter $key does not exist";
                        break;
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
    protected function set_up() {
        $this->_parameters_list = array ('event_name', 'event_type',
		'event_starting_date', 'event_ending_date', 'event_max_nb_participants',
		'event_holders_ids', 'event_languages', 'event_description', 'event_location');
        return true;
    }


    /**
     *
     * Set the  key=>value given, as an attribute of the user object. Key would
     * be the name of the attribute and value its value
     * @param array $parameter
     */
    /*public function set_string_attribute ($parameter) {
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
	*/

     /**
     *
     * Set event location. The input parameters must be an object from the geocodinghandler
     * @param array $location_parameters
     */
    public function set_location ($location_parameters) {
        if (!empty($location_parameters) && 
            (get_class($location_parameters) == 'Location'))
        {
           $this->_location = $location_parameters;
           return true;
        }
        else {
            echo 'The input parameters must be an object from the Location
             class <br/>';
            return FALSE;
        }
    }

    
    /**
     *
     * Set event location. The input parameters must be an object from the geocodinghandler
     * @param array $location_parameters
     */
    public function get_location () {
        if (!empty($this->_location) && (get_class($this->_location)=='Location'))
        {
           return $this->_location;
        }
        else {
            echo 'The location parameters should have been an object from the Location
             class <br/>';
            return FALSE;
        }
    }
    
    
    /**
     *
     * Set the id parameter
     * @param integer $id
     */
    public function set_id ($id) {
        if (is_int($id))	{
            $this->_id = $id;
            return true;
        } else {
            trigger_error('The Id parameter of the '.get_class($this)
            .' must be an integer', E_USER_ERROR);
            return E_USER_ERROR;
        }
    }


    /**
     *
     * Get the id parameter
     */
    public  function get_id () {
        if (is_int($this->_id)) {
            return $this->_id;
        } else {
            trigger_error('The Id parameter of the '.get_class($this)
            .' should have been an integer', E_USER_ERROR);
            return E_USER_ERROR;
        }
    }

    /**
     *
     * Set the event name
     * @param string $name
     */
    protected  function set_name ($name) {
        if (is_string($name)) {
            $this->_name = $name;
            return False;
        } else {
            trigger_error('The name parameter of the '.get_class($this)
            .' must be a string', E_USER_ERROR);
            return E_USER_ERROR;
        }

    }

    /**
     *
     * Get the event
     */
    public  function get_name ()
    {
        // HERE : Check if it's really a name and not bullshit
        if (is_string($this->_name)) {
            return $this->_name;
        } else {
            trigger_error('The name parameter of the '.get_class($this)
            .' should have been a string', E_USER_ERROR);
            return E_USER_ERROR;
        }
    }


    
    /**
     *
     * Set the type parameter
     * @param string $type
     */
    protected  function set_type ($type) {
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
    protected  function set_starting_date ($starting_date) {
        if ($starting_date && (check_and_valid_date($starting_date, true)))	{
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
        if ($this->_starting_date && (check_and_valid_date($this->_starting_date, true))) {
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
    protected  function set_ending_date ($ending_date) {
        if ($ending_date && (check_and_valid_date($ending_date, true))) {
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
        if ($this->_ending_date && (check_and_valid_date($this->_ending_date, true))) {
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
     * @param integer $holders_ids
     */
    protected  function set_holders_ids($id_list) {
        $participant = '';
        // if participants_list does not exist
        if (!$id_list) {
            trigger_error("The participants parameter of the '.get_class($this)
            .' must either be a list of strings or a string", E_USER_ERROR);
            return E_USER_ERROR;
        }
        // but if Participantlist exists and it's an array, treat it like an array
        elseif (is_array($id_list)) {
            foreach ($id_list as $id) {
                if (is_int($id)) {
                    array_push ($this->_holders_ids, $id);
                } else {
                    echo $id;
                    trigger_error('The id parameter of the '. get_class($this)
                    .' must either be a integer', E_USER_ERROR);
                    return E_USER_ERROR;
                }
            }
            return FALSE;
        }
        // and if it's not an array, treat it like it's not
        else {
            $id = $id_list;
            if (is_int($id)) {
                array_push ($this->_holders_ids, $id);
                return False;
            } else {
                trigger_error('The id parameter of the '.get_class($this)
                    .' must either be an integer', E_USER_ERROR);
                return E_USER_ERROR;
            }
        }
    }

    
    /**
     *
     * Get the holders ids list
     */
    public  function get_holders_ids () {
        if (empty($this->_holders_ids)) {
            return False;
        } else {
            return $this->_holders_ids;
        }
        if ($this->_holders_ids && is_int($this->_holders_ids)) {
            return $this->_holders_ids;
        } else {
            trigger_error('The id parameter of the '.get_class($this)
            .' must be an integer', E_USER_ERROR);
            return E_USER_ERROR;
        }
    }

    /**
     *
     * set the maximal number of participants parameter
     * @param integer $max_nb_participants
     */
    protected  function set_max_nb_participants ($max_nb_participants)	{
        if (is_int($max_nb_participants)) {
            $this->_max_nb_participants = $max_nb_participants;
            return False;
        } else {
            trigger_error('The max_nb_participants parameter of the '
            .get_class($this) .' must be a integer', E_USER_ERROR);
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
        if (empty($this->_participants)) {
            return False;
        } else {
            return $this->_participants;
        }
    }
    
	/**
     *
     * Get nb of participants registered at the event
     */
    public  function get_current_participants_nb () {
        if (empty($this->_participants)) {
            return False;
        } else if (is_array($this->_participants)) {
            return count($this->_participants);
        } else return 1;
    }

    /**
     *
     * Set spoken languages for the event
     * @param array of languages $languages
     */
    protected  function set_languages ($languages) {
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
    protected function set_description($description) {
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
        $r .= 'max_nb_participants: '. $this->get_max_nb_participants() . '<br />';
        $r .= 'description: '. $this->get_description() . '<br />';
        $r .= 'holders_ids: ';
        if (is_array ($this->get_holders_ids())) {
            foreach ($this->get_holders_ids() as $holder_id)	{
                $r .= $holder_id . ', ';
            }
        } else {
            $r .= $this->get_holders_ids() . ', ';
        }
        $r.= '<br />';
        
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
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
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
    public function insert_event (){
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }
        /*$res = Country::fetch_country_data($this->_event_country_name);
        if (!$res) {
            return false;
        }*/
        $event_name = htmlentities($this->_name, ENT_QUOTES);
        $event_type = htmlentities($this->_type, ENT_QUOTES);
        $event_checkin = htmlentities($this->_starting_date, ENT_QUOTES);
        $event_checkout = htmlentities($this->_ending_date, ENT_QUOTES);
        $event_max_nb_participants = $this->_max_nb_participants;
        $event_description = htmlentities($this->_description, ENT_QUOTES);
        $event_location_id = $this->_location->get_location_id();
        
        
        $sql = 'INSERT INTO events (event_name, event_type, 
        event_max_nb_of_participants, event_starting_date, event_ending_date, 
		event_description, event_location_id) VALUES(:event_name, :event_type, 
		:event_max_nb_participants, :event_starting_date, :event_ending_date, 
		:event_description, :event_location_id)';
        $query = $dbhandler->_db_connection->prepare($sql);
        if ($query) {
            $query->bindValue(':event_name', $event_name, PDO::PARAM_STR);
            $query->bindValue(':event_type', $event_type, PDO::PARAM_STR);
            $query->bindValue(':event_starting_date', $event_checkin, PDO::PARAM_STR);
            $query->bindValue(':event_ending_date', $event_checkout, PDO::PARAM_STR);
            $query->bindValue(':event_max_nb_participants',
            $event_max_nb_participants, PDO::PARAM_INT);
            $query->bindValue(':event_description', $event_description, PDO::PARAM_STR);
            $query->bindValue(':event_location_id', $event_location_id, PDO::PARAM_INT);
            	
            // PDO's execute() gives back TRUE when successful,
            // false when not
            $registration_success_state = $query->execute();
            if ($registration_success_state) {
                $event_id = intval($dbhandler->_db_connection->lastInsertId());
                if ($this->set_id($event_id)) {
                    if ($this->insert_spoken_languages()) {
                        if ($this->insert_event_holders()) {
                            echo "$event_name has been successfuly inserted in the
    						'event' table. <br/>";
                            return $event_id;
                        }else {
                            //TODO remove what have been inserted;
                            return false;
                        }
                    }
                    else {
                        //TODO remove what have been inserted;
                        return false;
                    }
                }else {
                    echo 'Impossible to retrieve the event id. <br/>';
                    return false;
                }
            } else {
                echo "$event_name failed to be inserted in the
				'events' table. <br/>";
                print_r ($query->errorInfo()).'<br/>';
                print_r (array ($event_name, $event_type, $event_checkin, 
                $event_checkout, $event_max_nb_participants, 
                $event_description, $event_location_id)).'<br/>';
                return false;
            }
        } else {
            echo "The database request for inserting $event_name
			in the 'events' table could not be prepared.<br/>";
            return false;
        }
    }

    /**
     *
     * Insert a new spoken language in the  db for this event id, return false
     * if failure or true in case of success
     */
    protected function insert_spoken_languages () {
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }
        $event_id = $this->_id;
        foreach ($this->_languages as $language) {
            /* uncomment the line below will create key constraint pb
            $language = htmlentities($language, ENT_QUOTES);*/
            $sql = 'INSERT INTO event_languages (event_id, language_name)
			VALUES (:event_id, :language_name)';
            $query = $dbhandler->_db_connection->prepare($sql);
            if ($query) {
                $query->bindValue(':event_id', $event_id, PDO::PARAM_INT);
                $query->bindValue(':language_name', $language, PDO::PARAM_STR);
                // PDO's execute() gives back TRUE when successful,
                // false when not
                $registration_success_state = $query->execute();
                if ($registration_success_state) {
                    echo "$language has been successfuly inserted in the
					'event_languages' table for the event id $event_id. <br/>";
                } else {
                    echo "$language failed to be inserted in the
					'event_languages' table for the event id $event_id. <br/>";
                    print_r ($query->errorInfo());
                    echo '<br/>';
                    return false;
                }
            } else {
                echo "The database request for inserting $language
				in the 'event_languages' table for the event id $event_id 
				could not be prepared.<br/>";
                return false;
            }
        }
        return true;
    }
    
    
/**
     *
     * Insert a new event holder in the db return false
     * if failure or true in case of success
     */
    protected function insert_event_holders () {
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }
        $event_id = $this->_id;
        foreach ($this->_holders_ids as $holder_id) {
            $sql = 'INSERT INTO event_holders (event_id, user_id)
			VALUES (:event_id, :holder_id)';
            $query = $dbhandler->_db_connection->prepare($sql);
            if ($query) {
                $query->bindValue(':event_id', $event_id, PDO::PARAM_INT);
                $query->bindValue(':holder_id', $holder_id, PDO::PARAM_INT);
                // PDO's execute() gives back TRUE when successful,
                // false when not
                $registration_success_state = $query->execute();
                if ($registration_success_state) {
                    echo "User $holder_id has been successfuly inserted in the
					'event_holders' table for the event id $event_id. <br/>";
                } else {
                    echo "User $holder_id failed to be inserted as an holder of
                    the event id $event_id. <br/>";
                    print_r ($query->errorInfo());
                    echo '<br/>';
                    return false;
                }
            } else {
                echo "The database request for inserting User $holder_id
				in the 'event_holders' table for the event id $event_id 
				could not be prepared.<br/>";
                return false;
            }
        }
        return true;
    }
    
    
    
	/**
	 * 
	 * Insert a event type  in the db return false
     * if failure or true in case of success
	 * @param string $event_type
	 */
    public static function insert_event_type ($event_type) {
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }
        $event_type = htmlentities($event_type);
        $sql = 'INSERT INTO event_types (event_type_name)
		VALUES (:event_type)';
        $query = $dbhandler->_db_connection->prepare($sql);
        if ($query) {
            $query->bindValue(':event_type', $event_type, PDO::PARAM_STR);
            // PDO's execute() gives back TRUE when successful,
            // false when not
            $registration_success_state = $query->execute();
            if ($registration_success_state) {
                echo "$event_type has been successfuly inserted in the
				'event_types' table. <br/>";
            } else {
                echo "$event_type failed to be inserted in the
				'event_types' table. <br/>";
                print_r ($query->errorInfo());
                echo '<br/>';
                return false;
            }
        } else {
            echo "The database request for inserting $event_type
			in the 'event_languages' table could not be prepared.<br/>";
            return false;
        }
        return true;
    }
    
    
    
	/**
     *
     * Select all events in databse and return them as an array
     */
    static public function get_all_events(){
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }

        // Look for all event types in the event types table
        $sql = 'SELECT * FROM events';
        $query = $dbhandler->_db_connection->prepare($sql);
        if ($query) {
            $query->execute();
            $results = $query->fetchall();
            if ($results) {
                $events = array();
                foreach ($results as $result_row) {
                    //then get the languages spoken at each event
                    $sql = 'SELECT language_name FROM event_languages
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
                    // Retrieve event holders
                    $sql = 'SELECT user_id FROM event_holders
    					WHERE event_id = :event_id';
                    $query = $dbhandler->_db_connection->prepare($sql);
                    if ($query) {
                        $query->bindValue(':event_id', $result_row['event_id'],
                        PDO::PARAM_INT);
                        $query->execute();
                        $holders_res = $query->fetchall(PDO::FETCH_COLUMN);
                        if ($holders_res) {
                            $holders_ids = array();
                            foreach ($holders_res as $key=>$value) {
                                // as it is an integer, no need for decoding
                                array_push ($holders_ids, intval($value));
                            }
                        } else {
                            echo 'There is no holder for this event <br/>';
                            return false;
                        }
                    }else {
                        echo "The request for selecting event holders could not
    					be prepared. <br/>";
                        return false;
                    }
                    
                    try {
                    $event_location = Location::get_location_from_id($result_row['event_location_id']);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                    $parameters = array ('event_id' => intval($result_row['event_id']),
    				'event_name' => html_entity_decode($result_row['event_name']), 
    				'event_location' => $event_location, 
    				'event_type' => html_entity_decode($result_row['event_type']), 
    				'event_starting_date' => html_entity_decode($result_row['event_starting_date']),
    		    	'event_ending_date'=> html_entity_decode($result_row['event_ending_date']), 
    		    	'event_max_nb_participants' => intval($result_row['event_max_nb_of_participants']), 
    		    	'event_holders_ids' => $holders_ids, 
    		    	'event_description' => html_entity_decode( $result_row['event_description']),
    				'event_languages' => $languages);
                    // create new object event with input parameters
                    $event = new Event ($parameters);
                    if (!empty($event)) {
                        // create an array of event objects
                        array_push ($events, $event);
                    }
                }
                return $events;
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
     * Get event information from event id and return an event object
     */
    static public function get_event_from_id($event_id){
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }

        // Look for all event types in the event types table
        $sql = 'SELECT * FROM events WHERE event_id=:event_id';
        $query = $dbhandler->_db_connection->prepare($sql);
        if ($query) {
            $query->bindValue(':event_id', $event_id, PDO::PARAM_INT);
            $query->execute();
            $results = $query->fetchall();
            if ($results) {
                $results = $results[0];
                //then get the languages spoken at each event
                $sql = 'SELECT language_name FROM event_languages
					WHERE event_id = :event_id';
                $query = $dbhandler->_db_connection->prepare($sql);
                if ($query) {
                    $query->bindValue(':event_id', $event_id,
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
                // Retrieve event holders
                $sql = 'SELECT user_id FROM event_holders
					WHERE event_id = :event_id';
                $query = $dbhandler->_db_connection->prepare($sql);
                if ($query) {
                    $query->bindValue(':event_id', $event_id,
                    PDO::PARAM_INT);
                    $query->execute();
                    $holders_res = $query->fetchall(PDO::FETCH_COLUMN);
                    if ($holders_res) {
                        $holders_ids = array();
                        foreach ($holders_res as $key=>$value) {
                            // as it is an integer, no need for decoding
                            array_push ($holders_ids, intval($value));
                        }
                    } else {
                        echo 'There is no holder for this event <br/>';
                        return false;
                    }
                }else {
                    echo "The request for selecting event holders could not
					be prepared. <br/>";
                    return false;
                }
                try {
                    $event_location = Location::get_location_from_id($results['event_location_id']);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                $parameters = array ('event_id' => intval($results['event_id']),
				'event_name' => html_entity_decode($results['event_name']), 
				'event_location' => $event_location, 
				'event_type' => html_entity_decode($results['event_type']), 
				'event_starting_date' => html_entity_decode($results['event_starting_date']),
		    	'event_ending_date'=> html_entity_decode($results['event_ending_date']), 
		    	'event_max_nb_participants' => intval($results['event_max_nb_of_participants']), 
		    	'event_holders_ids' => $holders_ids, 
		    	'event_description' => html_entity_decode( $results['event_description']),
				'event_languages' => $languages);
                // create new object event with input parameters
                $event = new Event ($parameters);
                return $event;
            } else {
                echo "There is no event types with the $event_id in the 
                'event_types' table.<br/>";
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
     * Select and return an array of all users holding specific event
     */
    static public function select_holders_ids ($event_id) {
        // Get the database connection if it's not the case yet
        $dbhandler = new SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }
        // Look for existing languages_name in the nationalities table
        $sql = 'SELECT user_id FROM event_holders WHERE event_id = :event_id';
        $query = $dbhandler->_db_connection->prepare($sql);
        if ($query) {
            $query->bindValue(':event_id', $event_id, PDO::PARAM_INT);
            $query->execute();
            $id_results = $query->fetchall();
            $holders_ids = array ();
            foreach($id_results as $id) {
                array_push($holders_ids, intval($id['user_id']));
            }
            return $holders_ids;
        }else {
             echo "The database request for selecting event holders in the
				'event_holders' table could not be prepared.<br/>";
                 return false;
        }                      
    }
}

?>