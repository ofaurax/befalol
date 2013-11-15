<?php

Class Member {
	
	protected $_id = 0;
	protected $_name = '';
	protected $_email = '';
	protected $_BirthdayDate = '';
	protected $_Nationality = '';
	
	function __construct($id, $name, $BirthdayDate,$email='x.gilbert@martine.net', $Nationality='French')
	{
    	$this->_id = $id;
	 	$this->_name = $name;
	 	$this->_email = $email;
	 	$this->_BirthdayDate = $BirthdayDate;
		$this->_Nationality = $Nationality;

	}
	
	public function get_name()
	{
		if (($this->_name) && is_string($this->_name))
		{
			return $this->_name;	
		}
		else
		{
			trigger_error('The name parameter of the'.get_class($this) .'must either be 
			a string', E_ERROR);
			return E_ERROR;
		}
	}

}
?>