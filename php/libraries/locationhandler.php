<?php 

/**
 * 
 * This class allows to handle geocoding informations. Contains an array with all
 * geocoding information, and an id refering to the database, so information can be 
 * retrieved
 * @author Aldeen Berluti
 *
 */
Class Location  {
    
    private $_location_id = 0;
    private $_geocoded_location = null;
    
    /**
     *
     * Instanciate the Location object
     * @param array('key' => 'value',..) $parameters
     */
    function __construct($parameters) {
        $errno = TRUE;
        if (!empty($parameters) && is_array($parameters)) {
            foreach ($parameters as $key=>$value) {
                switch ($key){
                    case 'geocoded':
                        $this->set_location_infos($value);
                        break;
                    case 'location_id':
                       $this->set_location_id($value);
                        break;
                    default:
                        echo "This parameter $key does not exist";
                        break;
                }
            }
        }
        else {
            echo 'The input parameters of the '.get_class($this).
				 ' must be an array <br/>';
            return FALSE;
        }
    }
    
       
    
    /**
     * 
     * Set the location informations
     * @param unknown_type $geocoded_location
     */
    private function set_location_infos ($geocoded_location) {
        //TODO Check that fields are strings
        if ( !empty ($geocoded_location['latitude']) 
            && !empty ($geocoded_location['longitude']) 
            && !empty ($geocoded_location['bounds']) 
            && !empty ($geocoded_location['city']) 
            && !empty ($geocoded_location['country']) 
            && !empty ($geocoded_location['zipcode'])) {
                foreach ($geocoded_location as $key=>$value) {
                    if (is_string($value)) {
                        $this->_geocoded_location[$key] = utf8_encode($value);
                    } else if (is_array($value)) {
                        foreach ($value as $k=>$v) {
                            $this->_geocoded_location[$key][$k] = utf8_encode($v);
                        }
                    } else {
                        $this->_geocoded_location[$key] = $value;
                    }
                }
            $this->_geocoded_location = $geocoded_location;
        } else {
            echo 'The input parameters of the '.get_class($this).
				 ' does not match the requirement to set the location <br/>';
            return FALSE;
        }
    }
    
    
    /**
     * 
     * Get the location informations
     *
     */
    public function get_location_infos () {
        if(!empty($this->_geocoded_location)){
            $location_infos = array ();
            foreach ($this->_geocoded_location as $key=>$value) {
                    if (is_string($value)) {
                        $location_infos[$key] = utf8_decode($value);
                    } else if (is_array($value)) {
                        foreach ($value as $k=>$v) {
                            $location_infos[$key][$k] = utf8_decode($v);
                        }
                    } else {
                        $location_infos[$key] = $value;
                    }
                }
            return $location_infos;
        }
        else {
            echo 'The location is empty';
            return false;
        }
    }
    
       
	/**
     * 
     * set the location id
     * @param integer $location_id
     */
    private function set_location_id ($location_id) {
        if ( !empty ($location_id) && is_int($location_id)) {
            $this->_location_id = $location_id;
            return true;
        } else {
            echo 'The id parameters of the '.get_class($this).
				 ' class has to be an integer. <br/>';
            return FALSE;
        }
    }
    
    
	/**
     * 
     * get the location id
     */
    public function get_location_id () {
        if ( !empty ($this->_location_id) && is_int($this->_location_id)) {
            return $this->_location_id;
        } else {
            echo 'The id parameters of the '.get_class($this).
				 ' should have been an integer. <br/>';
            return false;
        }
    }
    
    
    /**
     * 
     * Enter description here ...
     */
    private function insert_bound_location() {
        $dbhandler = Null;
        $dbhandler = new SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler))  {
            echo 'Impossible to initiate communication with database </br>';
            return False;
        }
        // if bounds exists and is an array
        if (!empty($this->_geocoded_location['bounds']) && is_array($this->_geocoded_location['bounds'])) {
            // we encode data in utf8 and encode quotes
            foreach ($this->_geocoded_location['bounds'] as $key=>$value) {
                // set north, east, south and west variables
                $$key = htmlentities($value);
            } 
            $sql = 'INSERT INTO bounds (south, west, north, east) 
            VALUES(:south, :west, :north, :east)';
            $query = $dbhandler->_db_connection->prepare($sql);
            if ($query) {
                $query->bindValue(':south', $south, PDO::PARAM_STR);
                $query->bindValue(':west', $west, PDO::PARAM_STR);
                $query->bindValue(':north', $north, PDO::PARAM_STR);
                $query->bindValue(':east', $east, PDO::PARAM_STR);
                // PDO's execute() gives back TRUE when successful,
                // false when not
                $registration_success_state = $query->execute();
                if ($registration_success_state) {
                    echo "Bound location has been successfuly inserted in the
    						'bounds' table. <br/>";
                    return intval($dbhandler->_db_connection->lastInsertId());
                } else {
                    echo "Bound location failed to be inserted in the
					'bounds' table. <br/>";
                    return false;
                }
            }else {
                echo "The request for inserting bound location in the 'bounds' 
                	table has failed";
                print_r ($query->errorInfo()).'<br/>';
                return false;
            }
        }else {
            echo "The bound location parameter does not match the requirement, the
            	insertion could not have been done.' 
                	table has failed";
            return false;
        }
    }
        
    
    
    /**
     * 
     * Insert location information into the database and return the location id
     * in case of success. Return false if failure
     */
    public function insert_location () {
    // Get the database connection if it's not the case yet
        $dbhandler = Null;
        $dbhandler = new SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        $res = null;
        if (empty($dbhandler))  {
            echo 'Impossible to initiate communication with database </br>';
            return False;
        }
        
        // Check if the location already exists in the db;
        $res = $this->get_location_from_lat_long($this->_geocoded_location['latitude'],
            $this->_geocoded_location['longitude']);
        if (!$res) {
            $bound_id = $this->insert_bound_location();
            if (!empty($bound_id)) {
                // Format data for table
                $latitude = htmlentities($this->_geocoded_location['latitude'], ENT_QUOTES);
                $longitude = htmlentities($this->_geocoded_location['longitude'], ENT_QUOTES);
                $street_number = htmlentities($this->_geocoded_location['streetNumber'], ENT_QUOTES);
                $street_name = htmlentities($this->_geocoded_location['streetName'], ENT_QUOTES);
                $city_district = htmlentities($this->_geocoded_location['cityDistrict'], ENT_QUOTES);
                $city = htmlentities($this->_geocoded_location['city'], ENT_QUOTES);
                $zipcode = htmlentities($this->_geocoded_location['zipcode'], ENT_QUOTES);
                $county = htmlentities($this->_geocoded_location['county'], ENT_QUOTES);
                $county_code = htmlentities($this->_geocoded_location['countyCode'], ENT_QUOTES);
                $region = htmlentities($this->_geocoded_location['region'], ENT_QUOTES);
                $region_code = htmlentities($this->_geocoded_location['regionCode'], ENT_QUOTES);
                $country = htmlentities($this->_geocoded_location['country'], ENT_QUOTES);
                $country_code = htmlentities($this->_geocoded_location['countryCode'], ENT_QUOTES);
                  
                      
                $sql = 'INSERT INTO locations (latitude, longitude, bound_id, street_number, 
                street_name, city_district, city, zipcode, county, county_code, region,
                region_code, country, country_index) 
                VALUES(:latitude, :longitude, :bound_id, :street_number, :street_name, 
                :city_district, :city, :zipcode, :county, :county_code, :region, :region_code,
                :country, :country_index )';
                $query = $dbhandler->_db_connection->prepare($sql);
                if ($query) {
                    $query->bindValue(':latitude', $latitude, PDO::PARAM_STR);
                    $query->bindValue(':longitude', $longitude, PDO::PARAM_STR);
                    $query->bindValue(':bound_id', $bound_id, PDO::PARAM_INT);
                    $query->bindValue(':street_number', $street_number, PDO::PARAM_STR);
                    $query->bindValue(':street_name', $street_name, PDO::PARAM_STR);
                    $query->bindValue(':city_district', $city_district, PDO::PARAM_STR);
                    $query->bindValue(':city', $city, PDO::PARAM_STR);
                    $query->bindValue(':zipcode', $zipcode, PDO::PARAM_STR);
                    $query->bindValue(':county', $county, PDO::PARAM_STR);
                    $query->bindValue(':county_code', $county_code, PDO::PARAM_STR);
                    $query->bindValue(':region', $region, PDO::PARAM_STR);
                    $query->bindValue(':region_code', $region, PDO::PARAM_STR);
                    $query->bindValue(':country', $country, PDO::PARAM_STR);
                    $query->bindValue(':country_index', $country_code, PDO::PARAM_STR);
                    // PDO's execute() gives back TRUE when successful,
                    // false when not
                    $registration_success_state = $query->execute();
                    if ($registration_success_state) {
                        // retrieve the id of the user object
                        $location_id = intval($dbhandler->_db_connection->lastInsertId());
                        // update the id of the user object
                        if ($this->set_location_id($location_id)){
                            echo "Location $location_id has been successfuly inserted. <br/>";
                            return $location_id;
                        }else {
                            echo "The location inserting request has failed. <br/>";
                            return false;
                        }
                    } else {
                        echo "The location has failed to be inserted. <br/>";
                        print_r ($query->errorInfo());
                        return false;
                    }
                } else {
                    echo "The database request for inserted a location 
        			in the 'location' table could not be prepared.<br/>";
                    print_r ($dbhandler->_db_connection->errorInfo());
                    return false;
                }
            }else {
                return false;;
            }
        }else {
            if ($this->set_location_id($res)){
                return $res;
            }else {
                echo "The location inserting request has failed. <br/>";
                return false;
            }
        }
    }
    
	/**
	 * 
	 * Get location informations from location id and return a Location object
	 * @param integer $location_id
	 */
    static public function get_location_from_id($location_id){
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }
        // Look for all event types in the event types table
        $sql = 'SELECT * FROM locations WHERE id=:location_id';
        $query = $dbhandler->_db_connection->prepare($sql);
        $location_id = intval($location_id);
        if ($query) {
            $query->bindValue(':location_id', $location_id, PDO::PARAM_INT);
            $query->execute();
            $results = $query->fetchall();
            if ($results) {
                $results = $results[0];
                //then get the languages spoken at each event
                // Retrieve event holders
                $bound_id = intval($results['bound_id']);
                $sql = 'SELECT * FROM bounds WHERE id = :bound_id';
                $query = $dbhandler->_db_connection->prepare($sql);
                if ($query) {
                    $query->bindValue(':bound_id', $bound_id,PDO::PARAM_INT);
                    $query->execute();
                    $bound_res = $query->fetchall(PDO::FETCH_COLUMN);
                    if (empty($bound_res)) {
                        echo "There is no bound with this $bound_id <br/>";
                        return false;
                    }
                    foreach ($bound_res as $key=>$value) {
                        $bound_res[$key] = html_entity_decode($value);
                    }
                }else {
                    echo "The request for selecting bound location could not
					be prepared. <br/>";
                    return false;
                }
                
                $parameters =array('location_id' => $location_id, 'geocoded' => 
                array ('latitude' => intval($results['latitude']),
				'longitude' => html_entity_decode($results['longitude']), 
				'bounds' => $bound_res,
				'street_number' => html_entity_decode($results['street_number']), 
				'street_name' => html_entity_decode($results['street_name']), 
				'city_district' => html_entity_decode($results['city_district']), 
				'city' => html_entity_decode($results['city']), 
				'zipcode' => html_entity_decode($results['zipcode']),
		    	'county'=> html_entity_decode($results['county']), 
		    	'county_code' => html_entity_decode($results['county_code']), 
		    	'region' => html_entity_decode($results['region']), 
		    	'region_code' => html_entity_decode($results['region_code']),
				'country' => html_entity_decode( $results['country']),
                'country_index' => html_entity_decode($results['country_index'])
                ));
                // create new object location with input parameters
                try { 
                    $location = new Location ($parameters);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                return $location;
            } else {
                echo "There is no location with the $location_id in the 
                'locations' table.<br/>";
                return false;
            }
        } else {
            echo "The database request for selecting locations in the
			'locations' table could not be prepared.<br/>";
            return false;
        }
    }
    
    /**
     * 
     * Look up for existence of the location in the db from latitude and longitude
     * Return the id if existence, false if not existence
     * @param string $latitude
     * @param string $longitude
     */
    private function get_location_from_lat_long ($latitude, $longitude){
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }

        // Look for all locations in table
        $sql = 'SELECT id FROM locations WHERE latitude = :latitude AND longitude = :longitude';
        $query = $dbhandler->_db_connection->prepare($sql);
        if ($query) {
            $query->bindValue(':latitude', $latitude, PDO::PARAM_STR);
            $query->bindValue(':longitude', $longitude, PDO::PARAM_STR);
            $query->execute();
            $result = null;
            $result = $query->fetchall(PDO::FETCH_COLUMN);
            if (!empty($result)) {
                return intval($result[0]);
            } else {
                echo "There is no location with the latitude $latitude and 
                longitude $longitude in the  'locations' table.<br/>";
                return false;
            }
        } else {
            echo "The database request for selecting location with latitude $latitude and 
                longitude $longitude in the 'locations' table could not be prepared.<br/>";
            return false;
        }
    }
    
    
	/**
	 * 
	 * Look up for existence of the location in the db from address
	 * @param string $address
	 * @param string $zipcode
	 * @param string $city
	 * @param string $country_name
	 */
    public static function get_location_from_address ($address, $zipcode, 
                            $city, $country_name){
        $dbhandler = New SqliteDbHanlder (db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR));
        if (empty($dbhandler)) {
            echo 'Impossible to initiate communication with database </br>';
            return false;
        }

        // Look for all locations in table
        $sql = 'SELECT id FROM locations WHERE street_name = :address AND city = :city
        AND zipcode = :zipcode AND country = :country_name';
        $query = $dbhandler->_db_connection->prepare($sql);
        if ($query) {
            $query->bindValue(':address', $address, PDO::PARAM_STR);
            $query->bindValue(':city', $zipcode, PDO::PARAM_STR);
            $query->bindValue(':zipcode', $city, PDO::PARAM_STR);
            $query->bindValue(':country_name', $country_name, PDO::PARAM_STR);
            $query->execute();
            $result = null;
            $result = $query->fetchall(PDO::FETCH_COLUMN);
            if (!empty($result)) {
                return intval($result[0]);
            } else {
                echo "There is no location with for the ".implode(', ', array($address, $zipcode, 
                            $city, $country_name))." in the  'locations' table.<br/>";
                return false;
            }
        } else {
            echo "The database request for finding location ".implode(', ', array($address, $zipcode, 
            $city, $country_name))." in the 'locations' table could not be prepared.<br/>";
            return false;
        }
    }
      
}


/**
 * 
 * Compute km/miles distance between two geocoded locations
 * @param integer $lat1
 * @param integer $lng1
 * @param integer $lat2
 * @param integer $lng2
 * @param boolean $miles
 */
function distance2($lat1, $lng1, $lat2, $lng2, $miles = true)
{
	$pi80 = M_PI / 180;
	$lat1 *= $pi80;
	$lng1 *= $pi80;
	$lat2 *= $pi80;
	$lng2 *= $pi80;

	$r = 6372.797; // mean radius of Earth in km
	$dlat = $lat2 - $lat1;
	$dlng = $lng2 - $lng1;
	$s = acos(sin($lat1)*sin($lat2) + cos($lat1)*cos($lat2)*cos($lng1-$lng2));
	$km = $s * $c;

	return ($miles ? ($km * 0.621371192) : $km);
}


?>