<?php
require_once ('./eventshandler.php');
date_default_timezone_set('Europe/Paris');


Class EventCtxt
{
	protected $_ctxt;
	
	// Set up a correct context for a test
	public function get_CorrectCtxt ()
	{
		$RandomDigit = rand();
		$RandomDate = date('Y-m-d', strtotime( '+'.mt_rand(0,30).' days'));
		$RandomDate1 = date('Y-m-d', strtotime( '+'.mt_rand(0,30).' days'));
		$holder = new Member ('3001', 'Benny', date(DATE_RFC2822));
		$Brian = new Member ('001', 'Brian', date(DATE_RFC2822));
		$Sophie = new Member ('010', 'Sophie', date(DATE_RFC2822));
		$Marc = new Member ('120', 'Marc', date(DATE_RFC2822));
		
		$this->_ctxt = array( 
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
		return $this->_ctxt;
	}
	
}

Class Eventtest extends PHPUnit_Framework_TestCase
{
	protected $_fixture;
	protected $_fixtureCtxts;
	
	protected function setUp()
	{
        $this->_fixtureCtxts =  EventCtxt::get_CorrectCtxt ();
    }
	
	public function testProperCtxt ()
	{
		$this->_fixture = new Event ($this->_fixtureCtxts);
		$this->assertSame($this->_fixtureCtxts['id'], $this->_fixture->get_id(),
			'The Event id parameter : '.$this->_fixture->get_id() 
			.' does not match the initial value: '.$this->_fixtureCtxts['id']);
		$this->assertSame($this->_fixtureCtxts['location'], $this->_fixture->get_location(),
			'The Event location parameter : '.$this->_fixture->get_location() 
			.' does not match the initial value: '.$this->_fixtureCtxts['location']);
		$this->assertSame($this->_fixtureCtxts['type'], $this->_fixture->get_type(),
			'The Event type parameter : '.$this->_fixture->get_type() 
			.' does not match the initial value: '.$this->_fixtureCtxts['type']);
		$this->assertSame($this->_fixtureCtxts['StartingDate'], $this->_fixture->get_StartingDate(),
			'The Event StartingDate parameter : '.$this->_fixture->get_StartingDate() 
			.' does not match the initial value: '.$this->_fixtureCtxts['StartingDate']);
		$this->assertSame($this->_fixtureCtxts['EndingDate'], $this->_fixture->get_EndingDate(),
			'The Event EndingDate parameter : '.$this->_fixture->get_EndingDate() 
			.' does not match the initial value: '.$this->_fixtureCtxts['EndingDate']);
		$this->assertSame($this->_fixtureCtxts['MaxNbParticipants'], $this->_fixture->get_MaxNbParticipants(),
			'The Event MaxNbParticipants parameter : '.$this->_fixture->get_MaxNbParticipants() 
			.' does not match the initial value: '.$this->_fixtureCtxts['StartingDate']);
		$this->assertSame($this->_fixtureCtxts['participants'], $this->_fixture->get_participants(),
			'The Event participants parameter : '.$this->_fixture->get_participants() 
			.' does not match the initial value: '.$this->_fixtureCtxts['participants']);	
		$this->assertSame($this->_fixtureCtxts['languages'], $this->_fixture->get_languages(),
			'The Event languages parameter : '.$this->_fixture->get_languages() 
			.' does not match the initial value: '.$this->_fixtureCtxts['languages']);
		$this->assertSame($this->_fixtureCtxts['description'], $this->_fixture->get_description(),
			'The Event description parameter : '.$this->_fixture->get_description() 
			.' does not match the initial value: '.$this->_fixtureCtxts['description']);
	}
	
}

?>