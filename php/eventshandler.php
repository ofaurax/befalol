<?php
require_once ('basicerrorhandling.php');
require_once ('usershandler.php');
	 

/*Class allowing to handle an event
// @Input : variable type (key=>Value) ; array(
// 'id'=> string, 
// 'location' => string,
// 'type' => string, 
// 'starting_date' => date,  
// 'ending_date' => date, 
// 'holder' => Member,
// 'max_nb_participants' => integer, 
// 'Participants' => array (MemberX, MemberY, MemberW,..)
// 'languages' => string or array of strings, 
// 'description' => string )
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
	
	// constructor requires all parameters
	function __construct($parameters)
    {
		if (($this->set_up() == 0) && (is_array($parameters)))
		{	
			foreach ($this->_parameters_list as $key)
			{
				//echo $key. '<br />';
				if (!array_key_exists ($key , $parameters))
				{
					trigger_error('Input '.$key .' parameter is missing to instanciate'
					.get_class($this) .' object', E_USER_ERROR);
					return E_USER_ERROR;
				}
			}
			foreach ($parameters as $key=>$value)
			{
				switch ($key)
				{
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
				}
			}
		}
		else
		{
			trigger_error('The input parameters of the '.get_class($this) 
			.' must be an array', E_USER_ERROR);
			return E_USER_ERROR;
		}
    }
	
	/***** 
	Set the parameterlist defaut value
	// Check Mandatory Input values (Participants is not mandatory)
	******/
	public function set_up()
	{
		$this->_parameters_list = array ('id','location','type', 'starting_date', 'ending_date',
		'max_nb_participants', 'holder', 'languages', 'description');
	}
	
	/***** 
	Set the id parameter
	******/
	public function set_id ($id)
	{
		if (is_string($id))
		{
			$this->_id = $id;
			return 0;
		}
		else 
		{
			trigger_error('The Id parameter of the '.get_class($this) 
			.' must be a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/***** 
	Get the id parameter
	******/
	public  function get_id ()
	{
		if (is_string($this->_id))
		{
			return $this->_id;
		}
		else 
		{
			trigger_error('The Id parameter of the '.get_class($this) 
			.' should have been a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/***** 
	Set the location parameter
	******/
	public  function set_location ($location)
	{
		// HERE : Check if it's really a location and not bullshit
		if (is_string($location))
		{
			$this->_location = $location;
			return 0;
		}
		else 
		{
			trigger_error('The location parameter of the '.get_class($this) 
			.' must be a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
		
	}
	
	/***** 
	Get the location parameter
	******/
	public  function get_location ()
	{
		// HERE : Check if it's really a location and not bullshit
		if (is_string($this->_location))
		{
			return $this->_location;
		}
		else 
		{
			trigger_error('The location parameter of the '.get_class($this) 
			.' should have been a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/***** 
	Set the type parameter
	******/
	public  function set_type ($type)
	{
		if (!in_array ($type, self::$_type_range))
		{
			trigger_error('The Type parameter of the '.get_class($this) 
			.' must be matching one of the following values ' .self::$_type_range, E_USER_ERROR);
			return E_USER_ERROR;
		}
		else
		{
			$this->_type = $type;
			return 0;
		}
		
	}
	
	/***** 
	Get the type parameter
	******/
	public  function get_type ()
	{
		if (!in_array ($this->_type, self::$_type_range))
		{
			trigger_error('The Type parameter of the '.get_class($this) 
			.' should be matching one of the following values ' .self::$_type_range, E_USER_ERROR);
			return E_USER_ERROR;
		}
		else
		{
			return $this->_type;
		}
		
	}
	
	/***** 
	Set the starting_date parameter
	******/
	public  function set_starting_date ($starting_date)
	{
		if ($starting_date && (IsDate($starting_date)))
		{
			$this->_starting_date = $starting_date;
			return 0;
		}
		else 
		{
			trigger_error('The starting_date parameter of the '.get_class($this) 
			.' must be a Date format', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/******
	// get the starting_date parameter
	******/
	public  function get_starting_date ()
	{
		if ($this->_starting_date && (IsDate($this->_starting_date)))
		{
			return $this->_starting_date;
		}
		else 
		{
			trigger_error('The starting_date parameter of the '.get_class($this) 
			.' should have been a Date format', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/******
	Set the ending parameter
	******/
	public  function set_ending_date ($ending_date)
	{
		if ($ending_date && (IsDate($ending_date)))
		{
			$this->_ending_date = $ending_date;
			return 0;
		}
		else 
		{
			trigger_error('The ending_date parameter of the '.get_class($this) 
			.' must be a Date format', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/******
	Get the ending_date parameter
	******/
	public  function get_ending_date ()
	{
		if ($this->_ending_date && (IsDate($this->_ending_date)))
		{
			return $this->_ending_date;
		}
		else 
		{
			trigger_error('The ending_date parameter of the '.get_class($this) 
			.' should have been a Date format', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/******
	Set the holder parameter
	******/
	public  function set_holder($holder)
	{
		if ($holder && (get_class($holder) == 'Member'))
		{
			$this->_holder = $holder;
			return 0;
		}
		else 
		{
			trigger_error('The holder parameter of the '.get_class($this) 
			.' must be Member typed object', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/******
	Get the holder parameter
	******/
	public  function get_holder ()
	{
		if ($this->_holder && (get_class($this->_holder) == 'Member'))
		{
			return $this->_holder;
		}
		else 
		{
			trigger_error('The holder parameter of the '.get_class($this) 
			.' should have been a Member typed object', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/******
	// set the maximal number of participants parameter
	******/
	public  function set_max_nb_participants ($max_nb_participants)
	{
		if (is_numeric($max_nb_participants))
		{
			$this->_max_nb_participants = $max_nb_participants;
			return 0;
		}
		else 
		{
			trigger_error('The max_nb_participants parameter of the '
			.get_class($this) .' must be a digit', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/******
	Get the maximal number of participants parameter
	******/
	public  function get_max_nb_participants ()
	{
		if (is_numeric($this->_max_nb_participants))
		{
			return $this->_max_nb_participants;
		}
		else 
		{
			trigger_error('The max_nb_participants parameter of the'.get_class($this) 
			.'should have been a digit', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/******
	Register members to the event (so they will become participant) 
	******/
	public  function set_participants ($participants_list)
	{
		$participant = '';
		// if participants_list does not exist
		if (!$participants_list)
		{
			trigger_error('The participants parameter of the '.get_class($this) .' must either be 
			an \'Member\' typed object or an array of \'Member\' typed objects', E_USER_ERROR);
			return E_USER_ERROR;
		}
		// but if Participantlist exists and it's an array, treat it like an array
		elseif (is_array($participants_list))
		{
			foreach ($participants_list as $participant)		
			{
				if (get_class($participant) == 'Member')
				{
					array_push ($this->_participants, $participant);
				}
				else 
				{
					trigger_error('The participants parameter of the '.get_class($this) .' must either be 
					an \'Member\' typed object or an array of \'Member\' typed object', E_USER_ERROR);
					return E_USER_ERROR;
				}
			}
			return 0;
		}
		// and if it's not an array, treat it like it's not
		else
		{
			$participant = $participants_list;
			if (get_class($participant) == 'Member')
			{
				array_push ($this->_participants, $participant);
				return 0;
			}
			else 
			{
				trigger_error('The Participants parameter of the '.get_class($this) .' must either be 
				an \'Member\' typed object or an array of \'Member\' typed objects', E_USER_ERROR);
				return E_USER_ERROR;
			}
		}
	}
	
	/******
	Get members registered to the event
	******/
	public  function get_participants ()
	{
		if ($this->_participants == 0)
		{
			return 0;
		}
		{
			return $this->_participants;
		}
	} 

	/******
	Set spoken languages for the event  
	******/
	public  function set_languages ($languages)
	{
		$language = '';
		// if languages does not exist
		if (!$languages)
		{
			trigger_error('The languages parameter of the '.get_class($this) .' must either be 
			a string or an array of strings', E_USER_ERROR);
			return E_USER_ERROR;
		}
		// but if languages exists and it's an array, treat it like an array
		elseif (is_array($languages))
		{
			foreach ($languages as $language)		
			{
				if (is_string($language))
				{
					array_push ($this->_languages, $language);
				}
				else 
				{
					trigger_error('The languages parameter of the '.get_class($this) .
					' must either be an array of strings', E_USER_ERROR);
					return E_USER_ERROR;
				}
			}
			return 0;
		}
		// and if it's not an array, treat it like it's not
		else
		{
			$language = $languages;
			if (is_string($language))
			{
				array_push ($this->_languages, $language);
				return 0;
			}
			else 
			{
				trigger_error('The languages parameter of the '.get_class($this) .' must either be 
				a string or an array of strings', E_USER_ERROR);
				return E_USER_ERROR;
			}
		}
	}
	
	/******
	Get members registered to the event
	******/
	public  function get_languages ()
	{
		if ($this->_languages == 0)
		{
			return 0;
		}
		{
			return $this->_languages;
		}
	} 
	
	/***** 
	Set the description parameter
	******/
	public function set_description($description)
	{
		if (is_string($description))
		{
			$this->_description = $description;
			return 0;
		}
		else 
		{
			trigger_error('The description parameter of the '.get_class($this) 
			.' must be a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	/***** 
	Get the description parameter
	******/
	public  function get_description ()
	{
		if (is_string($this->_description))
		{
			return $this->_description;
		}
		else 
		{
			trigger_error('The description parameter of the '.get_class($this) 
			.' should have been a string', E_USER_ERROR);
			return E_USER_ERROR;
		}
	}
	
	// Return a string containing all the informations related to the event
	public  function render ()
	{
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
		if (is_array ($this->get_participants()))
		{
			foreach ($this->get_participants() as $Participant)
			{
				$r .= $Participant->get_name() . ', ';
			}
		}
		else
		{
			$r .= $this->get_participants()->get_name() . ', ';
		}
		$r.= '<br />';
			
		$r .= 'languages: ';
		if (is_array ($this->get_languages()))
		{
			foreach ($this->get_languages() as $language)
			{
				$r .= $language. ', ';
			}
		}
		else
		{
			$r .= $this->get_language(). ', ';
		}
		$r.= '<br />';
		return $r;
	}


}


	
function IsDate( $Str )
{
  $Stamp = strtotime( $Str );
  $Month = date( 'm', $Stamp );
  $Day   = date( 'd', $Stamp );
  $Year  = date( 'Y', $Stamp );

  return checkdate( $Month, $Day, $Year );
}

?>