<?php

Class Member {
	
	protected $_id = 0;
	protected $_name = '';
	protected $_email = '';
	protected $_birthday_date = '';
	protected $_nationality = '';
	
	function __construct($id, $name, $birthday_date,$email='x.gilbert@martine.net', $nationality='French')
	{
    	$this->_id = $id;
	 	$this->_name = $name;
	 	$this->_email = $email;
	 	$this->_birthday_date = $birthday_date;
		$this->_nationality = $nationality;

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