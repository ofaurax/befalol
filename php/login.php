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
                        case 'setpersonalinfo&changepwd':
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
                            break;
                        case 'seeyourevents':
                            echo 'Below is the list of all your events <br/>';
                            $this->ShowPageYourEvents();
                            break;
                        case 'seeallevents':
                            echo 'Below is the list of all events <br/>';
                            $this->ShowAllEvents();
                            break;
                        case 'seeevent':
                            $this->ShowPageEventInformation();
                            break;
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
                 	'user_name' => html_entity_decode($result_row->user_name),
 					'user_email' => html_entity_decode($result_row->user_email),
                	'user_birthday' => html_entity_decode($result_row->user_birthday),
                	'user_nationality' => html_entity_decode($result_row->user_nationality),
                	'user_lastname' => html_entity_decode($result_row->user_lastname),
                	'user_firstname' => html_entity_decode($result_row->user_firstname),
                	'user_password_hash' => $result_row->user_password_hash);
                $user = New User ($parameters);
                $_SESSION['user'] = $user;
                //load user events;
                $events = $user->select_user_events();
                 $_SESSION['user_events'] = $events;
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
        && strlen($_POST['user_password_new']) > 6 ) {
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
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=setpersonalinfo">Your personal informations</a>'.'<br/>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=createnewevent">Post a new Event</a>'.'<br/>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=seeyourevents">List of your events</a>'.'<br/>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=seeallevents">List of all events</a>'.'<br/>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=seeallusers">List of members</a>'.'<br/>';
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
            $user_name = $_SESSION['user']->get_string_attribute('user_name');
            $user_email = $_SESSION['user']->get_string_attribute('user_email');
            $user_lastname = utf8_decode($_SESSION['user']->get_string_attribute('user_lastname'));
            $user_firstname = utf8_decode($_SESSION['user']->get_string_attribute('user_firstname'));
            $user_birthday = $_SESSION['user']->get_string_attribute('user_birthday');
            $user_nationality = $_SESSION['user']->get_string_attribute('user_nationality');
            $nationalities = NULL;
            $nationalities = Nationality::select_all_nationalities();
            if (!empty($nationalities)) {
                //table
                $r .= '<table>';
                $r .= display_row('Username:', '<input type="text" name="user_name") value="'
                .$user_name .'"readonly/>');
                if (isset($_GET['changepwd'])){
                     $r .= display_advanced_row (array('Password:', 'Current password <input 
                     id="login_input_password" class="login_input" type="password" 
                     name="user_password" pattern=".{6,}" required autocomplete="off" />',
                     'New password <input id="login_input_password_new" class="login_input" 
                     type="password" name="user_password_new" pattern=".{6,}" 
                     required autocomplete="off" />',
                     'New password again <input id="login_input_password_new2" 
                     class="login_input" type="password" 
                     name="user_password_repeat" pattern=".{6,}" required 
                     autocomplete="off" />'));
                }
                $r .= display_row('Email:', '<input type="email" 
                	name="user_email" value="' . $user_email.'"required/>');
                $r .= display_row('Last name:', '<input type="text" 
                	name="user_lastname" value="'. $user_lastname.'"required/>');
                $r .= display_row('First name:', '<input type="text"
                	name="user_firstname" value="'. $user_firstname.'"required/>');
                $r .= display_row('Birthday:', '<input type="date" 
                	name="user_birthday" placeholder="mm/dd/yyyy" value="'
                    . $user_birthday .'"required/>');
                $r .= display_row('Nationality:', display_dropdownlist
                (array('name' => 'user_nationality', 'multiple' => FALSE,
					'required' => TRUE) , $nationalities, 
                $user_nationality));
                $r .= display_row('', '<a href="' . $_SERVER['SCRIPT_NAME'] . 
                '?action=setpersonalinfo&changepwd" >
                <input type="button" value = "Change Password" name="changepwd"/></a>
                <input type="submit" value = "Save" name="saveuserinfo"/>');
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
     * Show the Event information
     */
    private function ShowPageEventInformation ()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        $user_flag = false;
        // if an event has been selectioned 
        if (isset($_GET['id'])) {
           // if it is one held by the session user then process happens
            if (isset( $_SESSION['user_events']) || isset( $_SESSION['events'])) {
                foreach ($_SESSION['user_events'] as $event) {
                    if ($event->get_id() == $_GET['id']) {
                        // tell us that this event is one of the user
                        $user_flag = true;
                        $event_name = utf8_decode($event->get_name());
                        $event_type = $event->get_type();
                        $event_starting_date = $event->get_starting_date();
                        $event_ending_date = $event->get_ending_date();
                        $event_location = utf8_decode($event->get_location());
                        $event_max_nb_participants = $event->get_max_nb_participants();
                        $event_languages = $event->get_languages();
                        $event_participants = array ();
                        foreach ($event->get_participants() as $participant) {
                            array_push($event_participants, utf8_decode($participant));
                        }
                        $event_description = utf8_decode($event->get_description());
                        
                        $r = '<h2>'.$event_name . ' is one of your events </h2>';
                        //build it
                        $r .= '<table>';
                        $r .= display_row('Type', $event_type);
                        $r .= display_row('Location:', $event_location);
                        $r .= display_row('Check in:', $event_starting_date);
                        $r .= display_row('Check out date:', $event_ending_date);
                        $r .= display_row('Maximal number of participants:',
                        $event_max_nb_participants);
                        $r .= display_row('Languages spoken :',
                        display_dropdownlist('', $event_languages, 0));
                        $r .= display_row('Description:', $event_description);
                        $r .= display_row('Participants:', display_dropdownlist('',
                        $event_participants, 0));
                        $r .= '<table/>';
                        echo $r;
                        break;
                    }
                }
                // if the event is not a user's one, then we will allow read only
                // and display the holder information
                if ($user_flag == false) {
                    $events = $_SESSION['events'];
                    foreach ($events as $event) {
                        if ($event->get_id() == $_GET['id']) {
                            echo $event->display_event_information();
                            break;
                        }
                    }
                }
            // in case of the events array has been initialized in the session,
            // we ll get all the events informations in order to retrieve information
            // of the one we want to display    
            } else { 
                $events = Event::select_all_events();
                foreach ($events as $event) {
                    if ($event->get_id() === $_GET['id']) {
                        echo $event->display_event_information();
                        break;
                    }
                }
            }
        } else {
            $this->feedback = 'Impossible to retrieve event informations'.'<br/>';
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
            $countries = Country::select_all_countries();
            if (!empty($event_types) && !empty($languages) && !empty($countries)) {
                //table
                sort($languages);
                sort($event_types);
                sort($countries);
                $r .= '<table>';
                $r .= display_row('Event Name:', '<input type="text"
					name="event_name" placeholder="My new awesome event" size="25" required/>');
                $r .= display_row('Type', display_dropdownlist (array('name' =>
					'event_type', 'multiple' => FALSE, 'required' => TRUE) , 
                $event_types, ''));
                $r .= display_row('Location:', '<input type="text"
					name="event_address" placeholder="1, Lombard Street" 
					size=50 required/> <input type="text" placeholder="94133"
					name="event_zipcode" width="8" required/> <input type="text" 
					name="event_city_name" size="20" placeholder="San Francisco" required/>'
					.display_dropdownlist(array('multiple' => false, 'required' =>
					true, 'name' => 'event_country_name'), $countries, 'United States'));
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
            }else {
                $this->feedback = "The form could not have been loaded";
            }
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
            }else {
                $this->feedback = "There are no members, so who are you?
    			(please advise the webmaster if you face this message)";
            }
        }
    }
    
	/**
     *
     * Show all the user events
     */
    private function ShowAllEvents ()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        if (isset($_SESSION['user'])) {
            $r = '<h2>Events</h2>';
            //retrieve the user from session
            $events = Event::select_all_events();
            $_SESSION['events'] = $events;
            if (!empty($events)) {
                $event_types = Event::select_all_event_types();
                sort($event_types);
                $array_type_one = array ();
                $array_type_two = array ();
                $array_type_three = array ();
                $array_type_four = array ();
                //sort all event by event types
                foreach ($events as $event) {
                    switch ($event->get_type()) {
                        case $event_types[0]:
                            array_push($array_type_one, '<a href="' . 
                            $_SERVER['SCRIPT_NAME'] . 
							'?action=seeevent&id=' . $event->get_id() . '">'
							.utf8_decode($event->get_name()).'</a>');
                            break;
                        case $event_types[1]:
                            array_push($array_type_two, '<a href="' . 
                            $_SERVER['SCRIPT_NAME'] . 
							'?action=seeevent&id=' . $event->get_id() . '">'
							.utf8_decode($event->get_name()).'</a>');
                            break;
                        case $event_types[2]:
                            array_push($array_type_three, '<a href="' . 
                            $_SERVER['SCRIPT_NAME'] . 
							'?action=seeevent&id=' . $event->get_id() . '">'
							.utf8_decode($event->get_name()).'</a>');
                            break;
                        case $event_types[3]:
                            array_push($array_type_four, '<a href="' . 
                            $_SERVER['SCRIPT_NAME'] . 
							'?action=seeevent&id=' . $event->get_id() . '">'
							.utf8_decode($event->get_name()).'</a>');
                           break;
                        default:
                            echo 'This event has an invalid event type <br/>';
                            break;
                    }
                }
                // diplay the events in the table
                $r .= '<table>';
                $r .= display_col(array($event_types[0] => $array_type_one, 
                    $event_types[1] => $array_type_two, 
                    $event_types[2] => $array_type_three, 
                    $event_types[3] => $array_type_four));
                $r .= '</table>';
                echo $r;
                echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">Homepage</a>';
            }else {
                $this->feedback = "You have never created any event.";
            }
        }else {
            $this->feedback = "You must log in to access this function.";
            return false;
        }
    }

    /**
     *
     * Show all the user events
     */
    private function ShowPageYourEvents ()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        if (isset($_SESSION))
        {
            if (isset($_SESSION['user']) && (!empty($_SESSION['user']))) {
                $r = '<h2>Your events</h2>';
                //retrieve the user from session
                $user = $_SESSION['user'];
                $events = NULL;
                if (!isset( $_SESSION['user_events'])) {
                    $events = $user->select_user_events();
                    $_SESSION['user_events'] = $events;
                }else {
                    $events =  $_SESSION['user_events'];
                }
                if (!empty($events)) {
                    $event_types = Event::select_all_event_types();
                    sort($event_types);
                    $array_type_one = array ();
                    $array_type_two = array ();
                    $array_type_three = array ();
                    $array_type_four = array ();
                    //sort all event by event types
                    foreach ($events as $event) {
                        switch ($event->get_type()) {
                            case $event_types[0]:
                                array_push($array_type_one, '<a href="' . 
                                $_SERVER['SCRIPT_NAME'] . 
    							'?action=seeevent&id=' . $event->get_id() . '">'
    							.utf8_decode($event->get_name()).'</a>');
                                break;
                            case $event_types[1]:
                                array_push($array_type_two, '<a href="' . 
                                $_SERVER['SCRIPT_NAME'] . 
    							'?action=seeevent&id=' . $event->get_id() . '">'
    							.utf8_decode($event->get_name()).'</a>');
                                break;
                            case $event_types[2]:
                                array_push($array_type_three, '<a href="' . 
                                $_SERVER['SCRIPT_NAME'] . 
    							'?action=seeevent&id=' . $event->get_id() . '">'
    							.utf8_decode($event->get_name()).'</a>');
                                break;
                            case $event_types[3]:
                                array_push($array_type_four, '<a href="' . 
                                $_SERVER['SCRIPT_NAME'] . 
    							'?action=seeevent&id=' . $event->get_id() . '">'
    							.utf8_decode($event->get_name()).'</a>');
                               break;
                            default:
                                echo 'This event has an invalid event type <br/>';
                                break;
                        }
                    }
                    // diplay the events in the table
                    $r .= '<table>';
                    $r .= display_col(array($event_types[0] => $array_type_one, 
                        $event_types[1] => $array_type_two, 
                        $event_types[2] => $array_type_three, 
                        $event_types[3] => $array_type_four));
                    $r .= '</table>';
                    echo $r;
                    echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">Homepage</a>';
                }else {
                    $this->feedback = "You have never created any event.";
                }
            }else {
                $this->feedback = "You must log in to access this function.";
                return false;
            }
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
            $user_name = filter_var($_POST['user_name'],
             FILTER_SANITIZE_STRING);
            //$user_password = $_POST['user_password'];
            $user_email = filter_var($_POST['user_email'], 
            FILTER_SANITIZE_EMAIL);
            $user_birthday = filter_var($_POST['user_birthday'], 
            FILTER_SANITIZE_STRING);
            $user_nationality = utf8_encode(filter_var($_POST['user_nationality'], 
            FILTER_SANITIZE_STRING));
            $user_lastname = utf8_encode(filter_var($_POST['user_lastname'], 
            FILTER_SANITIZE_STRING));
            $user_firstname = utf8_encode(filter_var($_POST['user_firstname'], 
            FILTER_SANITIZE_STRING));
            if (isset($_POST['user_password_new'])) {
                $user_password_hash = password_hash($_POST['user_password_new'],
                 PASSWORD_DEFAULT);
            }else {
                $user_password_hash = 
                $_SESSION['user']->get_string_attribute('user_password_hash');
            }
            	
            //Create an user object with it
            $parameters =  array ('user_id' =>
            $_SESSION['user']->get_string_attribute('user_id'),
				'user_name' => $user_name,
				'user_email' => $user_email,
				'user_birthday' => $user_birthday,
				'user_nationality' => $user_nationality,
				'user_lastname' => $user_lastname,
				'user_firstname' => $user_firstname,
            	'user_password_hash' => $user_password_hash);
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
            $event_name = utf8_encode(filter_var($_POST['event_name'], 
                FILTER_SANITIZE_STRING));
            $event_type = filter_var($_POST['event_type'], 
                FILTER_SANITIZE_STRING);
            $event_starting_date = filter_var($_POST['event_starting_date'].' '
                .$_POST['event_starting_time'], FILTER_SANITIZE_STRING);
            $event_ending_date = filter_var($_POST['event_ending_date'].' '
                .$_POST['event_ending_time'], FILTER_SANITIZE_STRING);
            $event_description = utf8_encode(filter_var($_POST['event_description'], 
                FILTER_SANITIZE_STRING));
            $event_max_nb_participants = filter_var(
                $_POST['event_max_nb_participants'], FILTER_SANITIZE_NUMBER_INT);
            $event_address = utf8_encode(filter_var($_POST['event_address'], 
                FILTER_SANITIZE_STRING));
            $event_zipcode = utf8_encode(filter_var($_POST['event_zipcode'], 
                FILTER_SANITIZE_STRING));
            $event_city_name = utf8_encode(filter_var($_POST['event_city_name'], 
                FILTER_SANITIZE_STRING));
            $event_country_name = filter_var($_POST['event_country_name'], 
                FILTER_SANITIZE_STRING);
                
            $event_languages_spoken = array(); 
            foreach($_POST['event_spoken_languages'] as $language) {
                array_push($event_languages_spoken, filter_var($language, 
                FILTER_SANITIZE_STRING));
            }

            //Get the user id to use as the holder_id
            $event_holder_id = $_SESSION['user']->get_user_id();
             
            //Create an user object with it
            $parameters =  array ('event_name' => $event_name,
			'event_type' => $event_type, 
			'event_starting_date' => $event_starting_date,
	    	'event_ending_date'=> $event_ending_date, 
	    	'event_max_nb_participants' => intval($event_max_nb_participants), 
	    	'event_holder_id' => $event_holder_id, 
	    	'event_languages' => $event_languages_spoken, 
			'event_address' => $event_address,
			'event_zipcode' => $event_zipcode,
			'event_city_name' => $event_city_name,
			'event_country_name' => $event_country_name,
			'event_description' => $event_description);
            $event = new Event ($parameters);
            	
            // save new datas in database;
            if ($event->insert_event()) {

                // Save them in the user session
                if (!array_key_exists('events', $_SESSION)) {
                    $events = array();
                }else {
                    $events =  $_SESSION['user_events'];
                }
                array_push($events, $event);
                 $_SESSION['user_events'] = $events;
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
        && strlen($_POST['event_name'] > 3)
        && preg_match('#[\w]+#', $_POST['event_name'], '')
        && check_no_digit($_POST['event_type'], true)
        && (!empty($_POST['event_zipcode']))
        && check_no_digit($_POST['event_city_name'], true)
        && check_no_digit($_POST['event_country_name'], true)
        && data_validation($_POST['event_max_nb_participants'],
            FILTER_SANITIZE_NUMBER_INT, true)
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
        && array_check_no_digit($_POST['event_spoken_languages'],true)) {
            return true;
        } elseif (empty($_POST['event_name'])) {
            $this->feedback = "Event name cannot be empty";
        } elseif (!check_no_digit($_POST['event_type'], true)) {
            $this->feedback = "Event type cannot be empty or contain digit";
        } elseif (empty($_POST['event_address']) || empty($_POST['event_zipcode'])
        || empty($_POST['event_city_name']) || empty($_POST['event_country_name'])) {
            $this->feedback = "Event location cannot be empty";
        } elseif (!check_no_digit($_POST['event_city_name'], true)) {
            $this->feedback = "Event city is not a valid city";
        } elseif (!check_no_digit($_POST['event_country_name'], true)) {
            $this->feedback = "Event country is not a valid country";
        }elseif (!data_validation($_POST['event_max_nb_participants'],
            FILTER_SANITIZE_NUMBER_INT, true)) {
            $this->feedback = "Maximum number of participant must be a digit";
        } elseif (empty($_POST['event_starting_date'])) {
            $this->feedback = "Event check in cannot be empty";
        } elseif (empty($_POST['event_ending_date'])) {
            $this->feedback = "Event check out cannot be empty";
        } elseif (empty($_POST['event_description'])) {
            $this->feedback = "Event description cannot be empty";
        } elseif (empty($_POST['event_spoken_languages'])) {
            $this->feedback = "Event languages spoken cannot be empty";
        } elseif (empty($_POST['event_starting_time'])) {
            $this->feedback = "Event check in time cannot be empty";
        } elseif (empty($_POST['event_ending_time'])) {
            $this->feedback = "Event check out time cannot be empty";
        } elseif (!check_and_valid_date($_POST['event_starting_date'], true)){
            $this->feedback = "The event check in date doesn't match the field
			requirements - mm/dd/yyyy - or is not a valid date";
        } elseif (!check_and_valid_date($_POST['event_ending_date'], true)){
            $this->feedback = "The event check out date doesn't match the field
			requirements - mm/dd/yyyy - or is not a valid date";
        } elseif (!is_it_futur($_POST['event_starting_date'].' '
        .$_POST['event_starting_time'], true)){
            $this->feedback = "I am not sure the time machine exists yet,
			please make sure the event check in date is valid.";
        } elseif (!is_it_futur($_POST['event_ending_date'].' '
        .$_POST['event_ending_time'], true)){
            $this->feedback = "Well, you won't be able to end this event before
			you started it, please make sure the event check out is valid.";
        } elseif (!check_and_valid_time($_POST['event_starting_time'], true)){
            $this->feedback = "The event check in time doesn't match the field
			requirements (hh:mm)";
        } elseif (!check_and_valid_time($_POST['event_ending_time'], true)){
            $this->feedback = "The event check out time doesn't match the field
			requirements (hh:mm)";
        } elseif (!data_validation($_POST['event_description'],
        FILTER_SANITIZE_STRING, true)){
            $this->feedback = "The event description does not match the field
			requirements";
        } elseif (!array_check_no_digit($_POST['event_spoken_languages'],true)){
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
        // validation input password informations
        $password_ok_flag = false;
        if (isset($_POST['user_password'])) {
            if (!empty($_POST['user_password_new']) && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
            && password_verify($_POST['user_password'], 
                $_SESSION['user']->get_string_attribute('user_password_hash'))
            && strlen($_POST['user_password_new']) > 6 ) {
                $password_ok_flag = true;
            } else {
                $this->feedback = "Please check your informations' passwords.";
                return false;
            }
        } elseif (!isset($_POST['user_password'])) {
            $password_ok_flag = true;
        }
        
        // validating the input
        if ($password_ok_flag && !empty($_POST['user_name'])
        && strlen($_POST['user_email']) <= 64
        && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
        && check_no_digit($_POST['user_lastname'], false)
        && strlen($_POST['user_lastname']) <= 64
        && check_no_digit($_POST['user_firstname'], false)
        && strlen($_POST['user_firstname']) <= 64
        && check_no_digit($_POST['user_nationality'], false)
        && check_and_valid_date($_POST['user_birthday'], False)        
        && (!is_it_futur ($_POST['user_birthday']))) {
            return true;     
        } elseif (empty($_POST['user_email'])) {
            $this->feedback = "Email cannot be empty";
        } elseif (strlen($_POST['user_email']) > 64) {
            $this->feedback = "Email cannot be longer than 64 characters";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->feedback = "Your email address is not in a valid email format";
        } elseif (!check_no_digit($_POST['user_lastname'], false)){
            $this->feedback = "Your lastname doesn't match the field requirements";
        } elseif (strlen($_POST['user_lastname']) > 64) {
            $this->feedback = "Your Lastname cannot be longer than 64 characters or 
            shorter than 2 characters";
        } elseif (!check_no_digit($_POST['user_firstname'], false)){
            $this->feedback = "Your firstname doesn't match the field requirements";
        } elseif (strlen($_POST['user_firstname']) > 64) {
            $this->feedback = "Firstname cannot be longer than 64 characters";
        } elseif (!check_and_valid_date($_POST['user_birthday'], 0)){
            $this->feedback = "Your birthday doesn't match the field requirements
             - mm/dd/yyyy - or is not a valid date";
        } elseif (is_it_futur($_POST['user_birthday'], 0)){
            $this->feedback = "You probably would not be using this website 
            if you were not born :).";
        } elseif (!check_no_digit($_POST['user_nationality'], false)){
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
