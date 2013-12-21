<?php

/**
 * 
 * Allow us to handle a country object
 * Creation - Db insertion - Db fetching
 * @author Aldeen Berluti
 *
 */
Class Country {
	
	protected $_country_name = '';
	protected $_country_index = '';
	protected $_country_nationality = '';	
	protected $_cities_names = NULL;
	protected $_languages_spoken = NULL;
	protected $_db_connection = NULL;

	/*TODO Thinks to reverse htmlentities on Select data from db 
	 * html_entity_decode
	 */
	
	
	/**
	 * 
	 * Instanciate the country object
	 * @param array('key' => 'value',..) $parameters
	 */
	function __construct($parameters) {
		$errno = TRUE;
		if (is_array($parameters)) {	
			foreach ($parameters as $key=>$value) {
				switch ($key) {
					case 'country_name':
						$errno = $this->set_country_name ($value) && $errno;
						break;
					case 'country_index':
						$errno = $this->set_country_index ($value) && $errno ;
						break;
					case 'country_nationality':
						$errno = $this->set_country_nationality ($value) && $errno ;
						break;
					case 'cities_names':
						$errno = $this->set_cities_names ($value) && $errno ;
						break;
					case 'languages_spoken':
						$errno = $this->set_languages_spoken ($value) && $errno ;
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
				 ' must be an array';
			return FALSE;
		}
    }
	
    
    
    
	/**
	 * Return the country name 
	 */
	public function get_country_name () {
		if (is_string ($this->_country_name) && !empty($this->_country_name)) {
			return $this->_country_name;
		}
		else {
			echo 'Impossible to get the country name';
			return FALSE;
		}
	}
	
	
	/**
	 * Return the country index 
	 */
	public function get_country_index () {
		if (is_string ($this->_country_index) && !empty($this->_country_index)) {
			return $this->_country_index;
		}
		else {
			echo 'Impossible to get the country index';
			return FALSE;
		}
	}
		
	/**
	 * 
	 * Set the country name given as a parameter 
	 * @param string $country_name
	 */
	public function set_country_name ($country_name) {
		if (is_string ($country_name) && !empty($country_name)) {
			$this->_country_name = $country_name;
			return TRUE;		
		}
		else {
			echo 'Impossible to set the country name';
			return FALSE;
		}
	}
	
	
/**
	 * 
	 * Set the country index given as a parameter 
	 * @param string $country_index
	 */
	public function set_country_index ($country_index) {
		if (is_string ($country_index) && !empty($country_index)) {
			$this->_country_index = $country_index;
			return TRUE;		
		}
		else {
			echo 'Impossible to set the country index';
			return FALSE;
		}
	}
	
	
	
	/**
	 * 
	 * Return the nationality of the country
	 */
	public function get_country_nationality () {
		if (is_string ($this->_country_nationality) 
			&& !empty($this->_country_nationality)) {
			return $this->_country_nationality;
		}
		else {
			echo 'Impossible to get the country nationality';
			return FALSE;
		}
	}
	
	/**
	 * 
	 *  Set the country nationality given as an input string
	 * @param string $country_nationality
	 */
	public function set_country_nationality ($country_nationality) {
		if (is_string ($country_nationality) && !empty($country_nationality)) {
			$this->_country_nationality = $country_nationality;
			return TRUE;		
		}
		else {
			echo 'Impossible to set the country nationality';
			return FALSE;
		}
	}
	
	
	/**
	 * 
	 *  Return the cities names of the country. Can be an array whether there  
	 *  are several names or a string if there is only one name
	 */
	public function get_cities_names () {
		if ((is_array ($this->_cities_names) && !empty($this->_cities_names)) 
		|| (is_string ($this->_cities_names) && !empty($this->_cities_names))) {
			return $this->_cities_names;
		}
		else {
			echo 'Impossible to get the cities names of the country ';
			return FALSE;
		}
	}
	
	/**
	 * 
	 * Set the country cities names given as an input array (name1, name2,..) 
	 * If there is only one name, this can be given as input string
	 * @param array(name1, name2,..) or string $cities_names
	 */
	public function set_cities_names ($cities_names) {
		if ((is_array ($cities_names) && !empty($cities_names)) 
		|| (is_string ($cities_names) && !empty($cities_names))) {
			$this->_cities_names = $cities_names;
			return TRUE;		
		}
		else {
			echo 'Impossible to set the cities names';
			return FALSE;
		}
	}
	
	
	/**
	 * 
	 * Return the languages spoken in the country. Can be an array whether  
	 * there are several languages or a string if there is only one name
	 */
	public function get_languages_spoken () {
		if ((is_array ($this->_languages_spoken) 
			&& !empty($this->_languages_spoken)) 
		|| (is_string ($this->_languages_spoken) 
			&& !empty($this->_languages_spoken))) {
			return $this->_languages_spoken;
		}
		else {
			echo 'Impossible to get the languages spoken in the country ';
			return FALSE;
		}
	}
	
	/**
	 * 
	 * Set the country languages given as an input array(language1, language2,..)
	 * If there is only one language, this can be given as input string
	 * @param array(language1, language2,..) or string $languages
	 */
	public function set_languages_spoken ($languages) {
		if ((is_array ($languages) && !empty($languages)) 
		|| (is_string ($languages) && !empty($languages))) {
			$this->_languages_spoken = $languages;
			return TRUE;		
		}
		else {
			echo 'Impossible to set the languages spoken';
			return FALSE;
		}
	}
	
	
	/**
	 * 
	 * Print the country informations
	 */
	public function render ()
	{
		$r = '<br />';
		$r .= 'Country name: '. $this->get_country_name() . '<br />';
		$r .= 'Country nationality: '
			  . $this->get_country_nationality() . '<br />';
		$r .= 'Cities names: ';
		$cities_names = $this->get_cities_names();
		if (is_array ($cities_names)) {
			foreach ($cities_names as $city_name){
				$r .= $city_name . ', ';
			}
		}else{
			$r .= $cities_names . ', ';
		}
		$r.= '<br />';
			
		$r .= 'Languages spoken: ';
		$languages_spoken = $this->get_languages_spoken();
		if (is_array ($languages_spoken))
		{
			foreach ($languages_spoken as $language)
			{
				$r .= $language. ', ';
			}
		}
		else
		{
			$r .= $languages_spoken . ', ';
		}
		$r.= '<br />';
		return $r;
	}
	
	
	
	/**
	 * 
	 * Look up for the existence of a city name for this country in the cities 
	 * table
	 * @param string $city_name
	 * @param string $country_name
	 */ 
	private function fetch_city_data ($city_name, $country_name) {
		if (!empty($city_name) && (!empty($country_name))) {
			if (empty ($this->_db_connection)) {
				$dbhandler = new SqliteDbHanlder ( 
										db_parser (_INI_FILE_DIR,_SERVER_DIR));
				$this->_db_connection = $dbhandler->get_connection_object();
			}
			$city_name = htmlentities($city_name, ENT_QUOTES);
			$country_name = htmlentities($country_name, ENT_QUOTES);
			// Look for existing city_name and country_name is the cities table
			$sql = 'SELECT * FROM cities WHERE 
			city_name = :city_name AND country_name = :country_name';
			$query = $this->_db_connection->prepare($sql);
			if ($query) {
				$query->bindValue(':country_name', $country_name, 
						PDO::PARAM_STR);
				$query->bindValue(':city_name', $city_name, PDO::PARAM_STR);
				$query->execute();
				$result_row = $query->fetchObject();
				if ($result_row) {
					echo "$city_name exists for the country $country_name 
							in the 'countries' table. <br/>";
					return true;
				} else {
					echo "$city_name does not exist for the country 
						$country_name in the 'countries' table. <br/>";
					return false;
				}
			} else {
				echo "The database request for fetching $city_name in the 
					'cities' table could not be prepared.<br/>";
				return false;
			}
		} else {
			echo "Sorry, the datas to fetch in the 'cities' table don't match 
			     the correct format.<br/> ";
			return false;
		}
	}	
	
	/**
	 * 
	 * Look up for the existence of a language spoken in a country in the 
	 * countries_languages table
	 * @param string $country_name
	 * @param string $language_spoken
	 */
	private function fetch_country_language_data ($country_name, 
					$language_spoken) {
		if (!empty($country_name) && (!empty($language_spoken))) {
			if (empty ($this->_db_connection)) {
				$dbhandler = new SqliteDbHanlder ( 
										db_parser (_INI_FILE_DIR,_SERVER_DIR));
				$this->_db_connection = $dbhandler->get_connection_object();
			}
			$language_spoken = htmlentities($language_spoken, ENT_QUOTES);
			$country_name = htmlentities($country_name, ENT_QUOTES);
			// Look for existing city_name and country_name is the cities table
			$sql = 'SELECT * FROM countries_languages WHERE 
			country_name = :country_name AND language_name = :language_spoken';
			$query = $this->_db_connection->prepare($sql);
			if ($query) { 
				$query->bindValue(':country_name', $country_name, 
						PDO::PARAM_STR);
				$query->bindValue(':language_spoken', $language_spoken, 
						PDO::PARAM_STR);
				$query->execute();
				$result_row = $query->fetchObject();
				if ($result_row) {
					echo "$language_spoken exists for the country $country_name 
							in the 'countries_languages' table.<br/>";
					return true;
				} else {
					echo "$language_spoken does not exist for the country 
						$country_name 
						in the 'countries_languages' table.<br/>";
					return false;
				}
			} else {
				echo "The database request for fetching $language_spoken in the
				 'countries_languages' table could not be prepared.<br/>";
				return false;
			}
		} else {
			echo "Sorry, the datas to fetch in the 'language_spoken' table don't
			 match the correct format.<br/> ";
			return false;
		}
	}	
	
	/**
	 * 
	 * Look up for a country in the countries table
	 * @param string $country_name
	 */
	private function fetch_country_data ($country_name) {
		if (!empty($country_name)) {
			// Get the database connection if it's not the case yet
			if (empty ($this->_db_connection)) {
				$dbhandler = new SqliteDbHanlder ( 
										db_parser (_INI_FILE_DIR,_SERVER_DIR));
				$this->_db_connection = $dbhandler->get_connection_object();
			}
			$country_name = htmlentities($country_name, ENT_QUOTES);
			// Look for existing city_name and country_name is the cities table
			$sql = 'SELECT * FROM countries WHERE country_name = :country_name';
			$query = $this->_db_connection->prepare($sql);
			if ($query) {
				$query->bindValue(':country_name', $country_name, 
						PDO::PARAM_STR);
				$query->execute();
				$result_row = $query->fetchObject();
				if ($result_row) {
					echo "$country_name exists in the 'countries' table.<br/>";
					return true;
				} else {
					echo "$country_name does not exist in the 'countries' 
					table.<br/>";
					return false;
				}
			} else {
				echo "The database request for fetching $country_name in the 
				'countries' table could not be prepared.<br/>";
				return false;
			}
		} else {
			echo "Sorry, the datas to fetch in the 'countries' table don't 
			match the correct format. <br/> ";
			return false;
		}
	}
	
	
	/**
	 * 
	 * Insert a language spoken for a country in the countries_languages table
	 * @param string $country_name
	 * @param string $language_spoken
	 */ 
	private function insert_language_spoken_data ($country_name, 
					$language_spoken) {
		if (!empty($language_spoken) && (!empty($country_name))) {
			// Get the database connection if it's not the case yet
			if (empty ($this->_db_connection)) {
				$dbhandler = new SqliteDbHanlder ( 
										db_parser (_INI_FILE_DIR,_SERVER_DIR));
				$this->_db_connection = $dbhandler->get_connection_object();
			}
			// Look for existing language and country_name is the 
			// countries_languages table. If this languages for this country 
			// name does not exists in the table, then we re going to
			// to insert it
			if (!$this->fetch_country_language_data($country_name, 
				$language_spoken)) {
				$language_spoken = htmlentities($language_spoken, ENT_QUOTES);
				$country_name = htmlentities($country_name, ENT_QUOTES);
				$sql = 'INSERT INTO countries_languages (country_name, 
				language_name) VALUES(:country_name, :language_spoken)';
				$query = $this->_db_connection->prepare($sql);
				if ($query) {
					$query->bindValue(':country_name', $country_name,
							PDO::PARAM_STR);
					$query->bindValue(':language_spoken', $language_spoken, 
							PDO::PARAM_STR);
					$registration_success_state = $query->execute();				
					if ($registration_success_state) {
						echo "The $language_spoken for the country 
						$country_name have been successfully inserted.<br/>";
						return true;
					} else {
						echo "Sorry, $language_spoken for the country 
						$country_name could not been inserted.<br/>";
						print_r ($query->errorinfo());
						return false;
					}
				} else {
					echo "The database request for inserting $language_spoken 
					in the 'countries_languages' table could not be prepared.<br/>";
					return false;
				}
			}else {
				return true;
			}
		}else {
			echo "Sorry, the datas to insert in the 'countries_languages' table
			 don't match the correct format. <br/> ";
			return false;
		}
	}
	
	
				
	/**
	 * 
	 * Insert a city name for this country in the cities table 
	 * @param string $city_name
	 * @param string $country_name
	 */
	private function insert_city_data ($city_name, $country_name) {
		if (!empty($city_name) && (!empty($country_name))) {
			// Get the database connection if it's not the case yet
			if (empty ($this->_db_connection)) {
				$dbhandler = new SqliteDbHanlder ( 
										db_parser (_INI_FILE_DIR,_SERVER_DIR));
				$this->_db_connection = $dbhandler->get_connection_object();
			}
			// Look for existing city_name and country_name is the cities table
			// If this city for this country name does not exists in the table, 
			// then we re going to insert it
			if (!$this->fetch_city_data($city_name, $country_name)) {
				$city_name = htmlentities($city_name, ENT_QUOTES);
				$country_name = htmlentities($country_name, ENT_QUOTES);
				$sql = 'INSERT INTO cities (city_name, country_name)
                    VALUES(:city_name, :country_name)';
				$query = $this->_db_connection->prepare($sql);
				if ($query) {
					echo $country_name.'<br/>';
					echo $city_name.'<br/>';
					$query->bindValue(':country_name', $country_name, 
							PDO::PARAM_STR);
					$query->bindValue(':city_name', $city_name, 
							PDO::PARAM_STR);
					$registration_success_state = $query->execute();				
					if ($registration_success_state) {
						echo "$city_name for the country $country_name have been
						 successfully inserted. <br/>";
						return true;
					} else {
						echo "Sorry, $city_name for the country $country_name 
						could not been inserted. <br/>";
						return false;
					}
				} else {
					echo "The database request for inserting $city_name in the 
					'cities' table could not be prepared.<br/>";
					return false;
				}
			}else {
				return True;
			}
		}else {
			echo "Sorry, the datas to insert in the 'cities' table don't match 
			the correct format. <br/> ";
			return false;
		}
	}
	
				
	/**
	 * 
	 * Insert the present country with its parameters in the tables
	 */
	public function insert_country_data ()
	{
		// Get the database connection if it's not the case yet
		if (empty ($this->_db_connection)) {
			$dbhandler = new SqliteDbHanlder ( 
									db_parser (_INI_FILE_DIR,_SERVER_DIR));
			$this->_db_connection = $dbhandler->get_connection_object();
		}
		$country_name = htmlentities($this->_country_name, ENT_QUOTES);
		$country_index = htmlentities($this->_country_index, ENT_QUOTES);
		if (!$this->fetch_country_data ($country_name)) {
			//$country_nationality = htmlentities($this->_country_nationality,
			//		ENT_QUOTES);
			/**TODO
			 * Add the part for nationalities if necessary
			 */
			$cities_names = $this->_cities_names;
			$languages_spoken = $this->_languages_spoken;
			// Insert country name and nationality into the countries table 
			$sql = 'INSERT INTO countries (country_name, country_index)
                   VALUES(:country_name, :country_index)';
			$query = $this->_db_connection->prepare($sql);
			if ($query) {
				$query->bindValue(':country_name', $country_name, 
						PDO::PARAM_STR);
				$query->bindValue(':country_index', $country_index, 
						PDO::PARAM_STR);
				// PDO's execute() gives back TRUE when successful, 
				// false when not
				$registration_success_state = $query->execute();
				// Now insert cities names and country_name into the cities 
				// table. 
				// If country insertion successed and there are several 
				// cities names
				if ($registration_success_state) {
					if (is_array ($cities_names)) {
						foreach ($cities_names as $city_name) {
							if ($this->insert_city_data ($city_name, 
												$country_name) == false) {
								return false;
							}
						}
					// If country insertion successed and there are only one city name
					} else if (!empty($cities_names)) {
						if ($this->insert_city_data ($cities_names, 
												$country_name) == false) {
						return false;
						}
					}
					// Insert language
					// If there are several languages spoken in this country
					if (is_array($languages_spoken)) {
						foreach ($languages_spoken as $language_spoken) {
							if ($this->insert_language_spoken_data 
							($country_name, $language_spoken) == false) {
								return false;
							}
						}
						echo "$country_name has been successfuly inserted in
						 the 'countries' table. <br/>";
						return true;	
					} else if (!empty($language_spoken)){
						// If there is only one language spoken in this 
						//country
						if ($this->insert_language_spoken_data 
							($country_name, $languages_spoken) == false) {
							return false;
						}
					}
					echo "$country_name has been successfuly inserted 
						in the 'countries' table. <br/>";
					return true;
				} else {
					echo "$country_name failed to be inserted in the 
					'countries' table. <br/>";
					return false;
				}
			} else {
				echo "The database request for inserting $country_name 
				in the 'countries' table could not be prepared.<br/>";
				return false;
			}
		} else {
			// If the country exists already on the countries table
			return false;
		}
	}
	
	/**
	 * 
	 * Select and return an array of all country names of the countries table
	 */
	static public function select_all_countries () {
		// Get the database connection if it's not the case yet
		$dbhandler = new SqliteDbHanlder (db_parser (_INI_FILE_DIR,_SERVER_DIR));
		if (empty($dbhandler)) {
			echo 'Impossible to initiate communication with database </br>';
			return false;
		}
		// Look for existing languages_name in the nationalities table
		$sql = 'SELECT country_name FROM countries';
		$query = $dbhandler->_db_connection->prepare($sql);
		if ($query) {
			$query->execute();
			$results = $query->fetchall(PDO::FETCH_COLUMN);
			if ($results) {
				$countries = array();
				foreach ($results as $key=>$value) {
					array_push ($countries, html_entity_decode($value));
					}
				return $countries;
			} else {
				echo "There is no country in the 'countries' table.<br/>";
				return false;
			}
		} else {
			echo "The database request for selecting country names in the 
			'countries'	table could not be prepared.<br/>";
			return false;
		}
	}	
}




/**
 * 
 * Allows us to handle a 'language' object 
 * @author Aldeen Berluti
 *
 */
class Language {
	
	private $_db_connection = NULL;
	private $_language_name = NULL;
	
	/**
	 * 
	 * Instanciate the language object
	 * @param string $language_name
	 */
	function __construct($language_name) {
		if (is_string($language_name)) {	
			return $this->set_language_name($language_name);
		}
		else {
			echo 'The input parameters of the '.get_class($this).
				 ' must be an string';
			return FALSE;
		}
    }
    
    
	/**
	 * Return the language name 
	 */
	public function get_language_name () {
		if (!empty($this->_language_name) && is_string ($this->_language_name)) {
			return $this->_language_name;
		}
		else {
			echo 'Impossible to get the language name';
			return FALSE;
		}
	}
		
	/**
	 * 
	 * Set the language name given as a parameter 
	 * @param string $language_name
	 */
	public function set_language_name ($language_name) {
		if (!empty($language_name) && is_string ($language_name)) {
			$this->_language_name = $language_name;
			return TRUE;		
		}
		else {
			echo 'Impossible to set the language name';
			return FALSE;
		}
	}
	
	
	/**
	 * 
	 * Look up for a language in the countries table
	 * @param string $language_name
	 */
	private function fetch_language_data ($language_name) {
		if (!empty($language_name)) {
			// Get the database connection if it's not the case yet
			if (empty ($this->_db_connection)) {
				$dbhandler = new SqliteDbHanlder ( 
										db_parser (_INI_FILE_DIR,_SERVER_DIR));
				$this->_db_connection = $dbhandler->get_connection_object();
			}
			$language_name = htmlentities($language_name, ENT_QUOTES);
			// Look for existing language_name in the languages table
			$sql = 'SELECT * FROM languages WHERE language_name = :language_name';
			$query = $this->_db_connection->prepare($sql);
			if ($query) {
				$query->bindValue(':language_name', $language_name,
						PDO::PARAM_STR);
				$query->execute();
				$result_row = $query->fetchObject();
				if ($result_row) {
					echo "$language_name exists in the 'languages' table.<br/>";
					return true;
				} else {
					echo "$language_name does not exist in the 'languages' 
					table.<br/>";
					return false;
				}
			} else {
				echo "The database request for fetching $language_name in the 
				'languages'	table could not be prepared.<br/>";
				return false;
			}
		} else {
			echo "Sorry, the datas to fetch in the 'countries' table don't 
			match the correct format. <br/> ";
			return false;
		}
	}	
	
	
	/**
	 * 
	 * Insert a language name into the language table 
	 */
	public function insert_language_data ()
	{
		// Get the database connection if it's not the case yet
		if (empty ($this->_db_connection)) {
			$dbhandler = new SqliteDbHanlder ( 
									db_parser (_INI_FILE_DIR,_SERVER_DIR));
			$this->_db_connection = $dbhandler->get_connection_object();
		}
		if (!$this->fetch_language_data ($this->_language_name)) {
			// Insert language name into the languages table
			$language_name = htmlentities($this->_language_name, ENT_QUOTES);
			$sql = 'INSERT INTO languages (language_name) VALUES(:language_name)';
			$query = $this->_db_connection->prepare($sql);
			if ($query) {
				$query->bindValue(':language_name', $language_name, 
						PDO::PARAM_STR);
				// PDO's execute() gives back TRUE when successful, 
				// false when not
				$registration_success_state = $query->execute();
				if ($registration_success_state) {
					echo "$language_name has been successfuly inserted in the 
					'languages table. <br/>";
					return true;
				} else {
					echo "$language_name failed to be inserted in the 
					'languages' table. <br/>";
					//print_r ($query);
					return false;
				}
			} else {
				echo "The database request for inserting $language_name 
				in the 'languages' table could not be prepared.<br/>";
				return false;
			}
		} else {
			// If the language exists already on the countries table
			return false;
		}
	}
	
	/**
	 * 
	 * Select and return an array of all languages of the languages table
	 */
	static public function select_all_languages () {
		// Get the database connection if it's not the case yet
		$dbhandler = new SqliteDbHanlder (db_parser (_INI_FILE_DIR,_SERVER_DIR));
		if (empty($dbhandler)) {
			echo 'Impossible to initiate communication with database </br>';
			return false;
		}
		// Look for existing languages_name in the nationalities table
		$sql = 'SELECT * FROM languages';
		$query = $dbhandler->_db_connection->prepare($sql);
		if ($query) {
			$query->execute();
			$results = $query->fetchall(PDO::FETCH_COLUMN);
			if ($results) {
				$languages = array();
				foreach ($results as $key=>$value) {
					array_push ($languages, html_entity_decode($value));
					}
				return $languages;
			} else {
				echo "There is no language in the 'languages' table.<br/>";
				return false;
			}
		} else {
			echo "The database request for selecting languages in the 
			'languages'	table could not be prepared.<br/>";
			return false;
		}
	}	
	
}




/**
 * 
 * Allows us to handle a 'Nationality' object 
 * @author Aldeen Berluti
 *
 */
class Nationality {
	
	private $_db_connection = NULL;
	private $_nationality_name = NULL;
	
	/**
	 * 
	 * Instanciate the nationality object
	 * @param string $nationality_name
	 */
	function __construct($nationality_name) {
		if (is_string($nationality_name)) {	
			return $this->set_nationality_name($nationality_name);
		}
		else {
			echo 'The input parameters of the '.get_class($this).
				 ' must be an string';
			return FALSE;
		}
    }
    
    
	/**
	 * Return the nationality name 
	 */
	public function get_nationality_name () {
		if (!empty($this->_nationality_name) && is_string ($this->_nationality_name)) {
			return $this->_nationality_name;
		}
		else {
			echo 'Impossible to get the nationality name';
			return FALSE;
		}
	}
		
	/**
	 * 
	 * Set the nationality name given as a parameter 
	 * @param string $nationality_name
	 */
	public function set_nationality_name ($nationality_name) {
		if (!empty($nationality_name) && is_string ($nationality_name)) {
			$this->_nationality_name = $nationality_name;
			return TRUE;		
		}
		else {
			echo 'Impossible to set the nationality name';
			return FALSE;
		}
	}
	
	
	/**
	 * 
	 * Look up for a nationality in the countries table
	 * @param string $nationality
	 */
	private function fetch_nationality_data ($nationality_name) {
		if (!empty($nationality_name)) {
			// Get the database connection if it's not the case yet
			$dbhandler = new SqliteDbHanlder ( db_parser (_INI_FILE_DIR,_SERVER_DIR));
			$nationality_name = htmlentities($nationality_name, ENT_QUOTES);
			// Look for existing nationality_name in the nationalities table
			$sql = 'SELECT * FROM nationalities WHERE nationality_name = :nationality_name';
			$query = $dbhandler->_db_connection->prepare($sql);
			if ($query) {
				$query->bindValue(':nationality_name', $nationality_name,
						PDO::PARAM_STR);
				$query->execute();
				$result_row = $query->fetchObject();
				if ($result_row) {
					echo "$nationality_name exists in the 'nationalities' table.<br/>";
					return true;
				} else {
					echo "$nationality_name does not exist in the 'nationalities' 
					table.<br/>";
					return false;
				}
			} else {
				echo "The database request for fetching $nationality_name in the 
				'nationalities'	table could not be prepared.<br/>";
				return false;
			}
		} else {
			echo "Sorry, the datas to fetch in the 'countries' table don't 
			match the correct format. <br/> ";
			return false;
		}
	}	
	
	
	/**
	 * 
	 * Select and return an array of nationalities of the nationalities table
	 */
	static public function select_all_nationalities () {
		// Get the database connection if it's not the case yet
		$dbhandler = new SqliteDbHanlder (db_parser (_INI_FILE_DIR,_SERVER_DIR));
		if (empty($dbhandler)) {
			echo 'Impossible to initiate communication with database </br>';
			return false;
		}
		// Look for existing nationality_name in the nationalities table
		$sql = 'SELECT * FROM nationalities';
		$query = $dbhandler->_db_connection->prepare($sql);
		if ($query) {
			$query->execute();
			$results = $query->fetchall(PDO::FETCH_COLUMN);
			if ($results) {
				$nationalities = array();
				foreach ($results as $key=>$value) {
					array_push ($nationalities, html_entity_decode($value));
					}
				return $nationalities;
			} else {
				echo "There is no nationality in the 'nationalities' table.<br/>";
				return false;
			}
		} else {
			echo "The database request for selecting nationalities in the 
			'nationalities'	table could not be prepared.<br/>";
			return false;
		}
	}	
	
	
	/**
	 * 
	 * Insert a nationality name into the nationalities table 
	 */
	public function insert_nationality_data ()
	{ 
		// Get the database connection if it's not the case yet
		if (empty ($this->_db_connection)) {
			$dbhandler = new SqliteDbHanlder ( 
									db_parser (_INI_FILE_DIR,_SERVER_DIR));
			$this->_db_connection = $dbhandler->get_connection_object();
		}
		if (!$this->fetch_nationality_data ($this->_nationality_name)) {
			// Insert nationality name into the nationalities table
			$nationality_name = htmlentities($this->_nationality_name, ENT_QUOTES);
			$sql = 'INSERT INTO nationalities (nationality_name) VALUES(:nationality_name)';
			$query = $this->_db_connection->prepare($sql);
			if ($query) {
				$query->bindValue(':nationality_name', $nationality_name, 
						PDO::PARAM_STR);
				// PDO's execute() gives back TRUE when successful, 
				// false when not
				$registration_success_state = $query->execute();
				if ($registration_success_state) {
					echo "$nationality_name has been successfuly inserted in the 
					'nationalities table. <br/>";
					return true;
				} else {
					echo "$nationality_name failed to be inserted in the 
					'nationalities' table. <br/>";
					//print_r ($query);
					return false;
				}
			} else {
				echo "The database request for inserting $nationality_name 
				in the 'nationalities' table could not be prepared.<br/>";
				return false;
			}
		} else {
			// If the nationality exists already on the countries table
			return false;
		}
	}
	
}

