<?php
require_once ('./eventshandler.php');
date_default_timezone_set('Europe/Paris');


Class EventCtxtHandler
{
	public $_value;
	protected $_ContextsList = array();
	
	function __Construct ($selector)
	{
		$this->_ContextsList = array ('proper','InvalidIdType');
		if (in_array($selector, $this->_ContextsList))
		{
			switch($selector)
			{
				case 'proper';
					$this->SetProperCtxt();
					break;
				case 'DigitIdType';
					$this->SetDigitIdType();
					break;
				case 'NullId';
					$this->SetNullId();
					break;
			}
		}
	}
	
	
	// Set up a proper context for a test
	public function SetProperCtxt ()
	{
		$RandomDigit = rand();
		$RandomDate = date('Y-m-d', strtotime( '+'.mt_rand(0,30).' days'));
		$RandomDate1 = date('Y-m-d', strtotime( '+'.mt_rand(0,30).' days'));
		$holder = new Member ('3001', 'Benny', date(DATE_RFC2822));
		$Brian = new Member ('001', 'Brian', date(DATE_RFC2822));
		$Sophie = new Member ('010', 'Sophie', date(DATE_RFC2822));
		$Marc = new Member ('120', 'Marc', date(DATE_RFC2822));
		
		$this->_value = array( 
			'id' => '19870000',
			'location' => 'Here I live for now: I am going to move out as soon as I get the geolocalisation library',
			'type' =>'Visits',
			'StartingDate' => $RandomDate,
			'EndingDate' => $RandomDate1,
			'holder' => $holder,
			'MaxNbParticipants' => $RandomDigit,
			'participants' => array ($Brian, $Marc, $Sophie),
			'languages' => array ('French', 'English', 'Turkish'),
			'description' => 'This is a description'
			);
	}
	
	// Set up a bad context for a test
	public function SetDigitIdType ()
	{
		$this->SetProperCtxt ();
		$this->_value['id'] = '985665'; 
	}
	
	public function SetNullId ()
	{
		$this->SetProperCtxt ();
		$this->_value['id'] = NULL; 
	}
}



Class Eventtest extends PHPUnit_Framework_TestCase
{
	protected $_fixtureCtxts;
	
	public function test_ProperCtxt ()
	{
		$context =  new EventCtxtHandler ('proper');
		$this->_fixture = new Event ($context->_value);
		$this->assertSame($context->_value['id'], $this->_fixture->get_id(),
			'The Event id parameter : '.$this->_fixture->get_id() 
			.' does not match the initial value: '.$context->_value['id']);
		$this->assertSame($context->_value['location'], $this->_fixture->get_location(),
			'The Event location parameter : '.$this->_fixture->get_location() 
			.' does not match the initial value: '.$context->_value['location']);
		$this->assertSame($context->_value['type'], $this->_fixture->get_type(),
			'The Event type parameter : '.$this->_fixture->get_type() 
			.' does not match the initial value: '.$context->_value['type']);
		$this->assertSame($context->_value['StartingDate'], $this->_fixture->get_StartingDate(),
			'The Event StartingDate parameter : '.$this->_fixture->get_StartingDate() 
			.' does not match the initial value: '.$context->_value['StartingDate']);
		$this->assertSame($context->_value['EndingDate'], $this->_fixture->get_EndingDate(),
			'The Event EndingDate parameter : '.$this->_fixture->get_EndingDate() 
			.' does not match the initial value: '.$context->_value['EndingDate']);
		$this->assertSame($context->_value['MaxNbParticipants'], $this->_fixture->get_MaxNbParticipants(),
			'The Event MaxNbParticipants parameter : '.$this->_fixture->get_MaxNbParticipants() 
			.' does not match the initial value: '.$context->_value['StartingDate']);
		$this->assertSame($context->_value['participants'], $this->_fixture->get_participants(),
			'The Event participants parameter : '.$this->_fixture->get_participants() 
			.' does not match the initial value: '.$context->_value['participants']);	
		$this->assertSame($context->_value['languages'], $this->_fixture->get_languages(),
			'The Event languages parameter : '.$this->_fixture->get_languages() 
			.' does not match the initial value: '.$context->_value['languages']);
		$this->assertSame($context->_value['description'], $this->_fixture->get_description(),
			'The Event description parameter : '.$this->_fixture->get_description() 
			.' does not match the initial value: '.$context->_value['description']);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	//public function test_InvalidIdType ()
//	{
//		$context =  new EventCtxtHandler ('InvalidIdType');
//		$this->_fixture = new Event ($context->_value);
//	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	//public function test_NullIdValue ()
//	{
//		$context =  new EventCtxtHandler ('NullId');
//		$this->_fixture = new Event ($context->_value);
//	}
}

?>