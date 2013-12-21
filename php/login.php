<?php

/**
 * Class Login
 * An entire php login script in one file, one class.
 *
 * Potential TODO:
 * 1. POST & GET directly in methods ? would be cleaner to pass this into the methods ?
 * 2. "Don't use else" rule ? Might be useful in VERY good PHP code, but c'mon, ELSE makes code much more readable,
 *    especially in this little script
 * 3. Max level of "if nesting" should be only ONE. But I think in this little script that's not necessary and would
 *    make things more complicated
 *
 * @package php-login
 * @author Panique
 * @link https://github.com/panique/php-login/
 * @license http://opensource.org/licenses/MIT MIT License
 */
class Login
{
    /**
     * @var string Type of used database (currently only SQLite, but feel free 
     * to expand this with mysql etc)
     */
    private $db_type = "sqlite"; //

    /**
     * @var string Path of the database file
     */
    //TODO Change that by ini config parser
    private $db_sqlite_path = "";

    /**
     * @var object Database connection
     */
    private $db_connection = null;

    /**
     * @var bool Login status of user
     */
    private $user_is_logged_in = false;

    /**
     * @var string System messages, likes errors, notices, etc.
     */
    public $feedback = "";


    /**
     * Does necessary checks for PHP version and PHP password compatibility 
     * library and runs the application
     */
    public function __construct()
    {
        if ($this->performMinimumRequirementsCheck() && $this->init()) {
            $this->runApplication();
        }
    }

    /**
     * 
     * Init the internal variable
     */
    Private function init () {
    	$db_parameters = db_parser (_INI_FILE_DIR,_SERVER_DIR);
    	if (!empty($db_parameters)) {
    		$this->db_sqlite_path = $db_parameters['db_path'];
    		return true;
    	}else {
    		echo 'Sorry, it was impossible to get the informations to connect 
    			to the database';
    		return false;
    	}
    }
    
    
    /**
     * Performs a check for minimum requirements to run this application.
     * Does not run the further application when PHP version is lower than 5.3.7
     * Does include the PHP password compatibility library when PHP version lower than 5.5.0
     * (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
     * @return bool Success status of minimum requirements check, default is false
     */
    private function performMinimumRequirementsCheck()
    {
        if (version_compare(PHP_VERSION, '5.3.7', '<')) {
            echo "Sorry, Simple PHP Login does not run on a PHP version older than 5.3.7 !";
        } elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
            require_once("libraries/password_compatibility_library.php");
            return true;
        } elseif (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            return true;
        }
        return false;
    }

    /**
     * This is basically the controller that handles the entire flow of the application.
     */
    public function runApplication()
    {
        // check is user wants to see register page (etc.)
        if (isset($_GET["action"]) && $_GET["action"] == "register") {
            $this->doRegistration();
            $this->showPageRegistration();
        } else {
            // start the session, always needed!
            $this->doStartSession();
            // check for possible user interactions (login with session/post data or logout)
            $this->performUserLoginAction();
            // show "page", according to user's login status
            if ($this->getUserLoginStatus()) {
                $this->showPageLoggedIn();
                if (isset($_GET["action"])) {
	                switch ($_GET["action"]) {
		            	case 'setpersonalinfo':
		            		echo 'You can change your personnal data here <br/>';
		            		$this->SaveUserInformations();
		            		$this->ShowPageUserInformation();
		            		break;
		            	case 'createnewevent':
		            		echo 'You can change your create your new event here <br/>';
		            		$this->SaveEventInformations();
		            		$this->ShowPageEventCreation();
		            		break;
		            	case 'seeallusers':
		            		echo 'Below is the list of all website members <br/>';
		            		$this->ShowPageAllUsers();
		            	default:
		            		echo 'This is an unknown action';
		            		break;
	                }
                }
            } else {
                $this->showPageLoginForm();
            }
        }
    }

    /**
     * Creates a PDO database connection (in this case to a SQLite flat-file database)
     * @return bool Database creation success status, false by default
     */
    private function createDatabaseConnection()
    {
        try {
            $this->db_connection = new PDO($this->db_type . ':' . $this->db_sqlite_path);
            return true;
        } catch (PDOException $e) {
            $this->feedback = "PDO database connection problem: " . $e->getMessage();
        } catch (Exception $e) {
            $this->feedback = "General problem: " . $e->getMessage();
        }
        return false;
    }

    /**
     * Handles the flow of the login/logout process. According to the circumstances, a logout, a login with session
     * data or a login with post data will be performed
     */
    private function performUserLoginAction()
    {
        if (isset($_GET["action"]) && $_GET["action"] == "logout") {
            $this->doLogout();
        } elseif (!empty($_SESSION['user']) && ($_SESSION['user_is_logged_in'])) {
            $this->doLoginWithSessionData();
        } elseif (isset($_POST["login"])) {
            $this->doLoginWithPostData();
        }
    }

    /**
     * Simply starts the session.
     * It's cleaner to put this into a method than writing it directly into runApplication()
     */
    private function doStartSession()
    {
		if (!isset ($_SESSION)){
        	session_start();
		}
    }

    /**
     * Set a marker (NOTE: is this method necessary ?)
     */
    private function doLoginWithSessionData()
    {
        $this->user_is_logged_in = true; // ?
    }

    /**
     * Process flow of login with POST data
     */
    private function doLoginWithPostData()
    {
        if ($this->checkLoginFormDataNotEmpty()) {
            if ($this->createDatabaseConnection()) {
                $this->checkPasswordCorrectnessAndLogin();
            }
        }
    }

    /**
     * Logs the user out
     */
    private function doLogout()
    {
        $_SESSION = array();
        session_destroy();
        $this->user_is_logged_in = false;
        $this->feedback = "You were just logged out.";
    }

    /**
     * The registration flow
     * @return bool
     */
    private function doRegistration()
    {
        if ($this->checkRegistrationData()) {
            if ($this->createDatabaseConnection()) {
                $this->createNewUser();
            }
        }
        // default return
        return false;
    }
 
    
    /**
     * Validates the login form data, checks if username and password are provided
     * @return bool Login form data check success state
     */
    private function checkLoginFormDataNotEmpty()
    {
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {
            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Username field was empty.";
        } elseif (empty($_POST['user_password'])) {
            $this->feedback = "Password field was empty.";
        }
        // default return
        return false;
    }

    /**
     * Checks if user exits, if so: check if provided password matches the one in the database
     * @return bool User login success status
     */
    private function checkPasswordCorrectnessAndLogin()
    {
        $sql = 'SELECT * FROM users WHERE user_name = :user_name LIMIT 1';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $_POST['user_name']);
        $query->execute();

        // Btw that's the weird way to get num_rows in PDO with SQLite:
        // if (count($query->fetchAll(PDO::FETCH_NUM)) == 1) {
        // Holy! But that's how it is. $result->numRows() works with SQLite pure, but not with SQLite PDO.
        // This is so crappy, but that's how PDO works.
        // As there is no numRows() in SQLite/PDO (!!) we have to do it this way:
        // If you meet the inventor of PDO, punch him. Seriously.
        $result_row = $query->fetchObject();
        if ($result_row) {
            // using PHP 5.5's password_verify() function to check password
            if (password_verify($_POST['user_password'], $result_row->user_password_hash)) {
                // write user data into PHP SESSION [a file on your server]
                $parameters = array ('user_id' => intval($result_row->user_id), 
                 	'user_name' => $result_row->user_name,
 					'user_email' => $result_row->user_email,
                	'user_birthday' => $result_row->user_birthday,
                	'user_nationality' => $result_row->user_nationality,
                	'user_lastname' => $result_row->user_lastname,
                	'user_firstname' => $result_row->user_firstname);
				$user = New User ($parameters);
                $_SESSION['user'] = $user;
                //$_SESSION['user_email'] = $result_row->user_email;
                $_SESSION['user_is_logged_in'] = true;
                $this->user_is_logged_in = true;
                return true;
            } else {
                $this->feedback = "Wrong password.";
            }
        } else {
            $this->feedback = "This user does not exist.";
        }
        // default return
        return false;
    }

    /**
     * Validates the user's registration input
     * @return bool Success status of user's registration data validation
     */
    private function checkRegistrationData()
    {
        // if no registration form submitted: exit the method
        if (!isset($_POST["register"])) {
            return false;
        }

        // validating the input
        if (!empty($_POST['user_name'])
            && strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            && !empty($_POST['user_password_new'])
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) {
            // only this case return true, only this case is valid
            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Empty Username";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $this->feedback = "Empty Password";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $this->feedback = "Password and password repeat are not the same";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $this->feedback = "Password has a minimum length of 6 characters";
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $this->feedback = "Username cannot be shorter than 2 or longer than 64 characters";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $this->feedback = "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
        } elseif (empty($_POST['user_email'])) {
            $this->feedback = "Email cannot be empty";
        } elseif (strlen($_POST['user_email']) > 64) {
            $this->feedback = "Email cannot be longer than 64 characters";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->feedback = "Your email address is not in a valid email format";
        } else {
            $this->feedback = "An unknown error occurred.";
        }

        // default return
        return false;
    }

    /**
     * Creates a new user.
     * @return bool Success status of user registration
     */
    private function createNewUser()
    {
        // remove html code etc. from username and email
        $user_name = htmlentities($_POST['user_name'], ENT_QUOTES);
        $user_email = htmlentities($_POST['user_email'], ENT_QUOTES);
        $user_password = $_POST['user_password_new'];
        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 char hash string.
        // the constant PASSWORD_DEFAULT comes from PHP 5.5 or the password_compatibility_library
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

        $sql = 'SELECT * FROM users WHERE user_name = :user_name';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->execute();

        // As there is no numRows() in SQLite/PDO (!!) we have to do it this way:
        // If you meet the inventor of PDO, punch him. Seriously.
        $result_row = $query->fetchObject();
        if ($result_row) {
            $this->feedback = "Sorry, that username is already taken. Please choose another one.";
        } else {
            $sql = 'INSERT INTO users (user_name, user_password_hash, user_email)
                    VALUES(:user_name, :user_password_hash, :user_email)';
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':user_name', $user_name);
            $query->bindValue(':user_password_hash', $user_password_hash);
            $query->bindValue(':user_email', $user_email);
            // PDO's execute() gives back TRUE when successful, FALSE when not
            // @link http://stackoverflow.com/q/1661863/1114320
            $registration_success_state = $query->execute();

            if ($registration_success_state) {
                $this->feedback = "Your account has been created successfully. You can now log in.";
                return true;
            } else {
                $this->feedback = "Sorry, your registration failed. Please go back and try again.";
            }
        }
        // default return
        return false;
    }

    /**
     * Simply returns the current status of the user's login
     * @return bool User's login status
     */
    public function getUserLoginStatus()
    {
        return $this->user_is_logged_in;
    }

    /**
     * Simple demo-"page" that will be shown when the user is logged in.
     * In a real application you would probably include an html-template here, but for this extremely simple
     * demo the "echo" statements are totally okay.
     */
    private function showPageLoggedIn()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo 'Hello ' . $_SESSION['user']->get_string_attribute('user_name') . ', you are logged in.<br/><br/>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=setpersonalinfo">Personal informations</a>'.'<br/>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=createnewevent">Post a new Event</a>'.'<br/>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=seeallusers">See other members</a>'.'<br/>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=logout">Log out</a><br/>';
    }

    /**
     * Simple demo-"page" with the login form.
     * In a real application you would probably include an html-template here, but for this extremely simple
     * demo the "echo" statements are totally okay.
     */
    private function showPageLoginForm()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }

        echo '<h2>Login</h2>';

        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '" name="loginform">';
        echo '<label for="login_input_username">Username</label> ';
        echo '<input id="login_input_username" type="text" name="user_name" required /> ';
        echo '<label for="login_input_password">Password</label> ';
        echo '<input id="login_input_password" type="password" name="user_password" required /> ';
        echo '<input type="submit"  name="login" value="Log in" />';
        echo '</form>';

        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=register">Register new account</a>';
    }

    /**
     * Simple demo-"page" with the registration form.
     * In a real application you would probably include an html-template here, but for this extremely simple
     * demo the "echo" statements are totally okay.
     */
    private function showPageRegistration()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }

        echo '<h2>Registration</h2>';

        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=register" name="registerform">';
        echo '<label for="login_input_username">Username (only letters and numbers, 2 to 64 characters)</label>';
        echo '<input id="login_input_username" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />';
        echo '<label for="login_input_email">User\'s email</label>';
        echo '<input id="login_input_email" type="email" name="user_email" required />';
        echo '<label for="login_input_password_new">Password (min. 6 characters)</label>';
        echo '<input id="login_input_password_new" class="login_input" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />';
        echo '<label for="login_input_password_repeat">Repeat password</label>';
        echo '<input id="login_input_password_repeat" class="login_input" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />';
        echo '<input type="submit" name="register" value="Register" />';
        echo '</form>';

        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">Homepage</a>';
    }


	 /**
	  * 
	  * Show the user form for user personal information
	  */
    private function ShowPageUserInformation ()
    {
     	if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
    	if (isset($_SESSION))
    	{
	        $r = '<h2>Your personal informations</h2>';
			//build it
			$r .=  '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . 
	        '?action=setpersonalinfo" name="userinfoform">';
			$nationalities = NULL;
			$nationalities = Nationality::select_all_nationalities();
			if (!empty($nationalities)) {
				//table
				$r .= '<table>';
				$r .= display_row('Username:', '<input type="text" name="user_name") value="'
				 . $_SESSION['user']->get_string_attribute('user_name').'"readonly/>');
				$r .= display_row('Email:', '<input type="email" name="user_email" value="'
				 . $_SESSION['user']->get_string_attribute('user_email').'"required/>');
				$r .= display_row('Last name:', '<input type="text" name="user_lastname" value="'
				 . $_SESSION['user']->get_string_attribute('user_lastname').'"required/>');
				$r .= display_row('First name:', '<input type="text" name="user_firstname" value="'
				 . $_SESSION['user']->get_string_attribute('user_firstname').'"required/>');
				$r .= display_row('Birthday:', '<input type="date" name="user_birthday"  value="'
				 . $_SESSION['user']->get_string_attribute('user_birthday').'"required/>');
				$r .= display_row('Nationality:', display_dropdownlist 
					(array('name' => 'user_nationality', 'multiple' => FALSE, 
					'required' => TRUE) , $nationalities, 
					$_SESSION['user']->get_string_attribute('user_nationality')));
				$r .= display_row('', '<input type="submit" value = "Save" name="saveuserinfo"/>');
				$r .= '<table/>';
				$r .= '</form>';
				echo $r;
				echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">Homepage</a>';
    		}else {
    			$this->feedback = "The form could not have been loaded";
    		}
    	}
    }
    
    
	/**
	  * 
	  * Show the user form for event creation
	  */
    private function ShowPageEventCreation ()
    {
     	if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
    	if (isset($_SESSION))
    	{
	        $r = '<h2>Your personal informations</h2>';
			//build it
			$r .=  '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . 
	        '?action=createnewevent" name="neweventcreation">';
			$event_types = NULL;
			$languages = NULL;
			$event_types = Event::select_all_event_types();
			$languages = Language::select_all_languages();
			if (!empty($event_types) && !empty($languages)) {
				//table
				sort($languages);
				sort($event_types);
				$r .= '<table>';
				$r .= display_row('Event Name:', '<input type="text" 
					name="event_name" placeholder="My new awesome event" size="25" required/>');
				$r .= display_row('Type', display_dropdownlist (array('name' => 
					'event_type', 'multiple' => FALSE, 'required' => TRUE) , 
					$event_types, ''));
				$r .= display_row('Location:', '<input type="text" 
					name="event_location" size=50 required/>');
				$r .= display_row('Check in:', '<input type="date" 
					name="event_starting_date" placeholder="mm/dd/yyyy" size="10" 
					required/>'.' Time :'. ' <input type="time" name="event_starting_time" 
					placeholder="hh:mm" size="8" max="23:00" required/>');
				$r .= display_row('Check out date:', '<input type="date" 
					name="event_ending_date" placeholder="mm/dd/yyyy" size="10" required/>'.
					' Time :'.' <input type="time" name="event_ending_time" size="8" 
					placeholder="hh:mm" max="23:00" required/>');
				$r .= display_row('Maximal number of participants:', 
					'<input type="number" size="2" min="1" value="1"  
					name="event_max_nb_participants" required/>');
				$r .= display_row('Tell us which language will be spoken :', 
					display_dropdownlist(array('multiple' => true, 'required' => 
					true, 'name' => 'event_spoken_languages[]'), $languages, 'English'));
				$r .= display_row('Tell us more about this event:', 
					'<textarea type="text" name="event_description" maxlength="4000" 
					cols="30" row="10" placeholder="This is going to be mad!" 
					required/></textarea>');
				$r .= display_row('', '<input type="submit" value = 
					"Create Event" name="createevent"/>');
				$r .= '<table/>';
				$r .= '</form>';
				echo $r;
				echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">Homepage</a>';
    		}//else {
    	//	$this->feedback = "The form could not have been loaded";
    	//	}
    	}
    }
    
    
    
	/**
	 * 
	 * Show all members of the website
	 */
    private function ShowPageAllUsers ()
    {
     	if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
    	if (isset($_SESSION))
    	{
	        $r = '<h2>Members</h2>';
			//build it
			$users = NULL;
			$users = User::select_all_user();
			if (!empty($users)) {
				$r .= '<table>';
				foreach ($users as $user) {
					$r .= display_row($user, '');
				}
				$r .= '</table>';
				echo $r;
				echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">Homepage</a>';
    		}//else {
    	//	$this->feedback = "The form could not have been loaded";
    	//	}
    	}
    }
    
    
    /**
     * 
     * Save input personal informations into db and Session
     */
    private function SaveUserInformations ()
    {
    	if (!isset($_POST['saveuserinfo'])) {
    		return False;
    	}
    	if ($this->checkPICorrectness()){
	    	// Retrieve datas from form
	    	$user_name = $_POST['user_name'];
	    	//$user_password = $_POST['user_password'];
	    	$user_email = $_POST['user_email'];
	    	$user_birthday = $_POST['user_birthday'];
	    	$user_nationality = $_POST['user_nationality'];
	    	$user_lastname = $_POST['user_lastname'];
	    	$user_firstname = $_POST['user_firstname'];
			
	    	//Create an user object with it
			$parameters =  array ('user_id' => 
				$_SESSION['user']->get_string_attribute('user_id'),
				'user_name' => $user_name,
				'user_email' => $user_email,
				'user_birthday' => $user_birthday,
				'user_nationality' => $user_nationality,
				'user_lastname' => $user_lastname,
				'user_firstname' => $user_firstname );
			$user = new User ($parameters);
			
			// save new datas in database;
			if ($user->update_user_data()) {
	    		// Save them in the user session
	    		$_SESSION['user'] = $user;
			} else {
				$this->feedback = 'Your personal information update has failed <br/>';
			}
    	}    	
    }

    /**
     * 
     * Save event into db and Session
     */
    private function SaveEventInformations ()
    {
    	if (!isset($_POST['createevent']) || (!isset ($_SESSION))) {
    		return False;
    	}
    	
    	if ($this->checkEICorrectness()){
	    	// Retrieve datas from form
	    	$event_name = $_POST['event_name'];
	    	$event_type = $_POST['event_type'];
	    	$event_location = $_POST['event_location'];
	    	$event_starting_date = $_POST['event_starting_date'].' '
	    		.$_POST['event_starting_time'];
	    	$event_ending_date = $_POST['event_ending_date'].' '
	    		.$_POST['event_ending_time'];
	    	$event_description = $_POST['event_description'];
	    	$event_max_nb_participants = $_POST['event_max_nb_participants'];
	    	$event_languages_spoken = $_POST['event_spoken_languages'];
	    	//Get the user id to use as the holder_id
	    	$event_holder_id = $_SESSION['user']->get_user_id();
				    	
	    	//Create an user object with it
			$parameters =  array ('event_name' => $event_name, 'event_location' => $event_location, 
			'event_type' => $event_type, 'event_starting_date' => $event_starting_date,
	    	'event_ending_date'=> $event_ending_date, 
	    	'event_max_nb_participants' => intval($event_max_nb_participants), 
	    	'event_holder_id' => $event_holder_id, 
	    	'event_languages' => $event_languages_spoken, 
			'event_description' => $event_description);
			$event = new Event ($parameters);
			
			// save new datas in database;
			if ($event->insert_event()) {
	    	
				// Save them in the user session
				if (!array_key_exists('events', $_SESSION)) {
					$events = array();
				}else {
					$events = $_SESSION['events'];
				}
				
				array_push($events, $event);
		    	$_SESSION['events'] = $events;
			}
			else {
				$this->feedback = 'The event creation failed <br/>';
			}
    	}
    	
	//echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=register">Register new account</a>';
    	
    	
    }
    
	/**
     * 
     * Check if input event informations match the requirements
     */
    private function checkEICorrectness()
    {
        // if no registration form submitted: exit the method
    	if (!isset($_POST['createevent'])) {
    		return False;
    	}
		
        // validating the input
        if (!empty($_POST['event_name'])
        	&& data_validation($_POST['event_name'], 
        	FILTER_SANITIZE_STRING, true)
            && data_validation($_POST['event_type'], 
            FILTER_SANITIZE_STRING, true)
			&& data_validation($_POST['event_location'], 
			FILTER_SANITIZE_STRING, true)
			&& data_validation($_POST['event_max_nb_participants'], 
			FILTER_SANITIZE_STRING, true)
			&& check_and_valid_date($_POST['event_starting_date'], true)
			&& check_and_valid_date($_POST['event_ending_date'], true)
			&& (is_it_futur ($_POST['event_starting_date'].' '
			.$_POST['event_starting_time']))
			&& (is_it_futur ($_POST['event_ending_date'].' '
			.$_POST['event_ending_time']))
			&& check_and_valid_time($_POST['event_starting_time'], true)
			&& check_and_valid_time($_POST['event_ending_time'], true)
			&& data_validation($_POST['event_description'], 
			FILTER_SANITIZE_STRING, true)
			&& array_data_validation($_POST['event_spoken_languages'], 
			FILTER_SANITIZE_STRING, true)) {
			return true;       
        } elseif (empty($_POST['event_name'])) {            
       		$this->feedback = "Event name cannot be empty";
        } elseif (empty($_POST['event_type'])) {            
       		$this->feedback = "Event type cannot be empty";
        } elseif (empty($_POST['event_location'])) {            
       		$this->feedback = "Event location cannot be empty";
        } elseif (empty($_POST['event_starting_date'])) {            
       		$this->feedback = "Event check in cannot be empty";
        } elseif (empty($_POST['event_ending_date'])) {            
       		$this->feedback = "Event check out cannot be empty";
        } elseif (empty($_POST['event_description'])) {            
       		$this->feedback = "Event description cannot be empty";
        } elseif (empty($_POST['event_languages_spoken'])) {            
       		$this->feedback = "Event languages spoken cannot be empty";
        } elseif (empty($_POST['event_starting_time'])) {            
       		$this->feedback = "Event check in time cannot be empty";
        } elseif (empty($_POST['event_ending_time'])) {            
       		$this->feedback = "Event check out time cannot be empty";
        } elseif (empty($_POST['event_spoken_languages'])) {            
       		$this->feedback = "Event spoken languages cannot be empty";
        } elseif (!data_validation($_POST['event_name'], FILTER_SANITIZE_STRING,
        	 true)){
			$this->feedback = "The event name does not match the requirements";
        } elseif (!data_validation($_POST['event_type'], FILTER_SANITIZE_STRING,
        	 true)){
			$this->feedback = "The event type does not match the field 
			requirements";
        } elseif (!data_validation($_POST['event_location'], 
        	FILTER_SANITIZE_STRING, true)){
			$this->feedback = "The event location does not match the field 
			requirements";
        } elseif (!check_and_valid_date($_POST['event_starting_date'], true)){
			$this->feedback = "The event check in date doesn't match the field 
			requirements (MM/DD/YYYY)";
        } elseif (!check_and_valid_date($_POST['event_ending_date'], true)){
			$this->feedback = "The event check out date doesn't match the field 
			requirements (MM/DD/YYYY)";
        } elseif (!check_and_valid_time($_POST['event_starting_time'], true)){
			$this->feedback = "The event check in time doesn't match the field 
			requirements (HH/MM)";
        } elseif (!check_and_valid_time($_POST['event_ending_time'], true)){
			$this->feedback = "The event check out time doesn't match the field 
			requirements (HH/MM)";
        } elseif (!is_it_futur($_POST['event_starting_date'].' '
        	.$_POST['event_starting_time'], true)){
			$this->feedback = "I am not sure the time machine exists yet, 
			please make sure the event check in date is valid.";
        } elseif (!is_it_futur($_POST['event_ending_date'].' '
        	.$_POST['event_ending_time'], true)){
			$this->feedback = "Well, you won't be able to end this event before 
			you started it, please make sure the event check out is valid.";
        } elseif (!data_validation($_POST['event_description'], 
        	FILTER_SANITIZE_STRING, true)){
			$this->feedback = "The event description does not match the field 
			requirements";
        } elseif (!array_data_validation($_POST['event_spoken_languages'], 
        	FILTER_SANITIZE_STRING, true)){
			$this->feedback = "The event spoken languages does not match the field 
			requirements";
        } else {
            $this->feedback = "An unknown error occurred.";
        }
        // default return
        return false;
    }
    
    
    /**
     * 
     * Check if input personal informations match the requirements
     */
    private function checkPICorrectness()
    {
        // if no registration form submitted: exit the method
    	if (!isset($_POST['saveuserinfo'])) {
    		return False;
    	}
       // validating the input
        if (!empty($_POST['user_name'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
			&& data_validation($_POST['user_lastname'], FILTER_SANITIZE_STRING, 0)
			&& data_validation($_POST['user_firstname'], FILTER_SANITIZE_STRING, 0)
			&& check_and_valid_date($_POST['user_birthday'], 0)
			&& (!is_it_futur ($_POST['user_birthday']))
			&& data_validation($_POST['user_nationality'], FILTER_SANITIZE_STRING, 0)) {
			return true;       
            
        } elseif (empty($_POST['user_email'])) {            
       		$this->feedback = "Email cannot be empty";
        } elseif (strlen($_POST['user_email']) > 64) {
            $this->feedback = "Email cannot be longer than 64 characters";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->feedback = "Your email address is not in a valid email format";
        } elseif (!data_validation($_POST['user_lastname'], FILTER_SANITIZE_STRING, 0)){
			$this->feedback = "Your lastname doesn't match the field requirements";
        } elseif (!data_validation($_POST['user_firstname'], FILTER_SANITIZE_STRING, 0)){
			$this->feedback = "Your firstname doesn't match the field requirements";
        } elseif (!check_and_valid_date($_POST['user_birthday'], 0)){
			$this->feedback = "Your birthday doesn't match the field requirements (MM/DD/YYYY)";
        } elseif (is_it_futur($_POST['user_birthday'], 0)){
			$this->feedback = "You probably would not be using this website if you were not born :).";
        } elseif (!data_validation($_POST['user_nationality'], FILTER_SANITIZE_STRING, 0)){
			$this->feedback = "Your nationality doesn't match the field requirements";
        } else {
            $this->feedback = "An unknown error occurred.";
        }
        // default return
        return false;
    }
    
}


// runs the app
//$login = new Login();
