<?php
require_once ('./basicerrorhandling.php');
require_once ('./usershandler.php');


//Class allowing to handle an event
Class Event {
	
	// Internal variables
	protected $_id = 0;
	protected $_location = '';
	protected $_type = '';
	protected $_StartingDate = '';
	protected $_EndingDate = '';
	protected $_holder = '';
	protected $_MaxNbParticipants = 0;
	protected $_Participants = array();
	protected $_languages = array();
	protected $_description = '';
	// Define different types of events allowed
	private $_TypeRange = array('Visits', 'Activities', 'Journeys', 'Parties');
	
	// constructor requires all parameters
	function __construct($id, $location, $type, $StartingDate, $EndingDate, $holder,
	 $MaxNbParticipants)
    {
    	$this->set_id($id);
		echo 'Je passe par la3';
	 	$this->set_location ($location);
	 	$this->set_type ($type);
	 	$this->set_StartingDate ($StartingDate);
		$this->set_EndingDate ($EndingDate);
	 	$this->set_holder ($holder);
	 	$this->set_MaxNbParticipants ($MaxNbParticipants);
	 	//$this->set_Participants = $Participants;
	 	//$this->set_languages = $languages;
		//$this->set_description = $description;
		
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
			trigger_error('The Id parameter of the'.get_class($this) .'must be a string', E_ERROR);
			return E_ERROR;
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
			trigger_error('The Id parameter of the'.get_class($this) 
			.'should have been a string', E_ERROR);
			return E_ERROR;
		}
	}
	
	/***** 
	Set the location parameter
	******/
	public  function set_location ($location)
	{
		// HERE : Check if it's really a location and not bullshit
		
	}
	
	/***** 
	Get the location parameter
	******/
	public  function get_location ()
	{
			return $this->_location;
			// HERE : Check if it's really a location and not bullshit
	}
	
	/***** 
	Set the type parameter
	******/
	public  function set_type ($type)
	{
		if (!in_array ($type, $this->_TypeRange))
		{
			trigger_error('The Type parameter of the'.get_class($this) 
			.'must be matching one of the following values {$this->_TypeRange}', E_ERROR);
			return E_ERROR;
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
		if (!in_array ($this->_type, $this->_TypeRange))
		{
			trigger_error('The Type parameter of the'.get_class($this) 
			.'should be matching one of the following values {$this->_TypeRange}', E_ERROR);
			return E_ERROR;
		}
		else
		{
			return $this->_type;
		}
		
	}
	
	/***** 
	Set the StartingDate parameter
	******/
	public  function set_StartingDate ($StartingDate)
	{
		if ($StartingDate && (IsDate($StartingDate)))
		{
			$this->_StartingDate = $StartingDate;
			return 0;
		}
		else 
		{
			trigger_error('The StartingDate parameter of the'.get_class($this) 
			.'must be a Date format', E_ERROR);
			return E_ERROR;
		}
	}
	
	/******
	// get the StartingDate parameter
	******/
	public  function get_StartingDate ()
	{
		if ($this->_StartingDate && (IsDate($this->_StartingDate)))
		{
			return $this->_StartingDate;
		}
		else 
		{
			trigger_error('The StartingDate parameter of the'.get_class($this) 
			.'should have been a Date format', E_ERROR);
			return E_ERROR;
		}
	}
	
	/******
	Set the Ending parameter
	******/
	public  function set_EndingDate ($EndingDate)
	{
		if ($EndingDate && (IsDate($EndingDate)))
		{
			$this->_EndingDate = $EndingDate;
			return 0;
		}
		else 
		{
			trigger_error('The EndingDate parameter of the'.get_class($this) 
			.'must be a Date format', E_ERROR);
			return E_ERROR;
		}
	}
	
	/******
	Get the EndingDate parameter
	******/
	public  function get_EndingDate ()
	{
		if ($this->_EndingDate && (IsDate($this->_EndingDate)))
		{
			return $this->_EndingDate;
		}
		else 
		{
			trigger_error('The EndingDate parameter of the'.get_class($this) 
			.'should have been a Date format', E_ERROR);
			return E_ERROR;
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
			trigger_error('The holder parameter of the'.get_class($this) 
			.'must be Member typed object', E_ERROR);
			return E_ERROR;
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
			trigger_error('The holder parameter of the'.get_class($this) 
			.'should have been a Member typed object', E_ERROR);
			return E_ERROR;
		}
	}
	
	// set the maximal number of participants parameter
	public  function set_MaxNbParticipants ($MaxNbParticipants)
	{
		if (is_numeric($MaxNbParticipants))
		{
			$this->_MaxNbParticipants = $MaxNbParticipants;
			return 0;
		}
		else 
		{
			trigger_error('The MaxNbParticipants parameter of the'.get_class($this) .'must be a digit', E_ERROR);
			return E_ERROR;
		}
	}
	
	/******
	Get the maximal number of participants parameter
	******/
	public  function get_MaxNbParticipants ()
	{
		if (is_numeric($this->_MaxNbParticipants))
		{
			return $this->_MaxNbParticipants;
		}
		else 
		{
			trigger_error('The MaxNbParticipants parameter of the'.get_class($this) .'should have been a digit', E_ERROR);
			return E_ERROR;
		}
	}
	
	/******
	Register members to the event (so they will become participant) 
	******/
	public  function set_Participants ($Participantlist)
	{
		$Participant = '';
		// if Participantlist does not exist
		if ($Participantlist == 0)
		{
			trigger_error('The Participants parameter of the'.get_class($this) .'must either be 
			an \'Member\' typed object or an array of \'Member\' typed objects', E_ERROR);
			return E_ERROR;
		}
		// but if Participantlist exists and it's an array, treat it like an array
		elseif (is_array($Participantslist))
		{
			foreach ($Participantlist as $Participant)		
			{
				if (get_class($Participant) == 'Member')
				{
					$this->_Participants += $Participant;
					return 0;
				}
				else 
				{
					trigger_error('The Participants parameter of the'.get_class($this) .'must either be 
					an \'Member\' typed object or an array of \'Member\' typed object', E_ERROR);
					return E_ERROR;
				}
			}
		}
		// and if it's not an array, treat it like it's not
		else
		{
			$Participant = $Participantlist;
			if (get_class($Participant) == 'Member')
			{
				$this->_Participants += $Participant;
				return 0;
			}
			else 
			{
				trigger_error('The Participants parameter of the'.get_class($this) .'must either be 
				an \'Member\' typed object or an array of \'Member\' typed objects', E_ERROR);
				return E_ERROR;
			}
		}
	}
	
	/******
	Get members registered to the event
	******/
	public  function get_Participants ()
	{
		if ($this->_Participants == 0)
		{
			return 0;
		}
		{
			return $this->_Participants;
		}
	} 

	/******
	Set spoken languages for the event  
	******/
	public  function set_languages ($Languages)
	{
		$Language = '';
		// if Languages does not exist
		if ($Languages == 0)
		{
			trigger_error('The languages parameter of the'.get_class($this) .'must either be 
			a string or an array of strings', E_ERROR);
			return E_ERROR;
		}
		// but if Languages exists and it's an array, treat it like an array
		elseif (is_array($Languages))
		{
			foreach ($Languages as $Language)		
			{
				if (is_string($Language))
				{
					$this->_languages += $Language;
					return 0;
				}
				else 
				{
					trigger_error('The languages parameter of the'.get_class($this) .'must either be 
					a string or an array of strings', E_ERROR);
					return E_ERROR;
				}
			}
		}
		// and if it's not an array, treat it like it's not
		else
		{
			$Language = $Languages;
			if (is_string($Language))
			{
				$this->_languages += $Language;
				return 0;
			}
			else 
			{
				trigger_error('The languages parameter of the'.get_class($this) .'must either be 
				a string or an array of strings', E_ERROR);
				return E_ERROR;
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
	
	// Return a string containing all the informations related to the event
	public  function render ()
	{
		$r = '<br />';
		$r .= 'Id: '. $this->get_id() . '<br />';
		$r .= 'Location: '. $this->get_location() . '<br />';
		$r .= 'Type: '. $this->get_type() . '<br />';
		$r .= 'StartingDate: '. $this->get_StartingDate() . '<br />';
		$r .= 'EndingDate: '. $this->get_EndingDate() . '<br />';
		$r .= 'Holder: '. $this->get_holder()->get_name() . '<br />';
		$r .= 'MaxNbParticipants: '. $this->get_MaxNbParticipants() . '<br />';
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