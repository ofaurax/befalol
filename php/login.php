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
        $db_parameters = db_parser (_INI_DB_CONFIG_FILE,_SERVER_DIR);
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
            echo "Sorry, Befalol does not run on a PHP version older than 5.3.7 !";
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
        if (isset($_GET["action"]) && $_GET["action"] == "signin") {
            $this->doRegistration();
            $this->showPageRegistration();
        } else if  (isset($_GET["action"]) && $_GET["action"] == "login") {
            $this->showPageLoginForm();
        }
        else {
            // start the session, always needed!
            $this->doStartSession();
            // check for possible user interactions (login with session/post data or logout)
            $this->performUserLoginAction();
            // show "page", according to user's login status
            if ($this->getUserLoginStatus()) {
                $this->showHomePage();
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
            $_SESSION['_INI_GEO_KEYS_CONFIG'] = _INI_GEO_KEYS_CONFIG;
            $_SESSION['_SERVER_DIR'] = _SERVER_DIR;
            $_SESSION['_URL_PATH'] = _URL_PATH;
            $_SESSION['_INI_DB_CONFIG_FILE'] = _INI_DB_CONFIG_FILE;
            $_SESSION['_COMPOSER_FLAG'] = _COMPOSER_FLAG;
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
        if (isset($_SESSION['user'])) {
            $user_name = $_SESSION['user']->get_string_attribute('user_name');
            $_SESSION = null;
            session_unset();
            session_destroy();
            $this->user_is_logged_in = false;
            $this->feedback = '<p id="goodbye">See you soon ' . $user_name .'</p>' ;
        }
    }

    /**
     * The registration flow
     * @return bool
     */
    private function doRegistration()
    {
        if ($this->checkRegistrationData()) {
            if ($this->createDatabaseConnection()) {
                if ($this->createNewUser()) {
                    $this->showPageLoginForm();
                }
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
                //session_start();
                $parameters = array ('user_id' => intval($result_row->user_id),
                 	'user_name' => html_entity_decode($result_row->user_name),
 					'user_email' => html_entity_decode($result_row->user_email),
                	'user_birthday' => html_entity_decode($result_row->user_birthday),
                	'user_nationality' => html_entity_decode($result_row->user_nationality),
                	'user_lastname' => html_entity_decode($result_row->user_lastname),
                	'user_firstname' => html_entity_decode($result_row->user_firstname),
                	'user_gender' => html_entity_decode($result_row->user_gender),
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
        if (!isset($_POST["signin"])) {
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
     * Simple demo-"page" with the login form.
     * In a real application you would probably include an html-template here, but for this extremely simple
     * demo the "echo" statements are totally okay.
     */
    private function showPageLoginForm()
    {
        echo get_header();
        echo '<body>';
        echo topbar_public();
        
        echo '<div id="container">';
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
	    echo '<div id=content>
			<br/><br/><br/>
			<h1>From this point ahead <br/> your life will make more sense </h1>';
        echo '<div id="loginarea">';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] .'" name="loginform">';
        echo '<label for="username" data-icon="u"></label>';
        echo '<input class="loginbox" type="text" name="user_name" placeholder="Username" required  /> ';
        echo '<label for="username" data-icon="p"></label>';
        echo '<input class="pwdbox" type="password" name="user_password" placeholder="Password" required /> ';
        echo '<input type="submit"  name="login" value="Log in" />';
        echo '</form>';
        echo '</div>'; // loginarea
        echo '<h3> Not a member yet? </h3>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=signin"><input type="button" value="Sign in"> </a>';
        echo '</div>'; // content div
        echo '</div>'; //end container
        echo '</body>';
        echo get_footer();
    }    

    /**
     * Simple demo-"page" with the registration form.
     * In a real application you would probably include an html-template here, but for this extremely simple
     * demo the "echo" statements are totally okay.
     */
    private function showPageRegistration()
    {
        echo get_header();
        echo '<body>';
        echo topbar_public();
        echo '<div id="container">';
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }
        echo '<div id=content>
		<br/><br/><br/>
		<h1>From this point ahead <br/> your life will make more sense </h1>';
        echo '<div id="signinarea">';  
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=signin" name="registerform">';
        echo '<p>';
        echo '<label for="login_input_user_name" data-icon="u" >Username </label>';
        echo '<input id="login_input_user_name" type="text" placeholder="username" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />';
        echo '</p>';
        echo '<p>';
        echo '<label for="login_input_email" data-icon="e">Email </label>';
        echo '<input id="login_input_email" type="email" placeholder="email@example.com" name="user_email" required />';
        echo '</p>';
        echo '<p>';
        echo '<label for="login_input_password_new" data-icon="p">Password </label>';
        echo '<input id="login_input_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />';
        echo '</p>';
        echo '<p>';
        echo '<label for="login_input_password_repeat" data-icon="p">Confirm password</label>';
        echo '<input id="login_input_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />';
        echo '</p>';
        echo '<p>';
        echo '<input type="submit" name="signin" value="Sign in" />';
        echo '<p>';
        echo '</div>'; // signinarea
        echo '</form>';
        echo '</div>'; //end content
        echo '</div>'; //end container
        echo '</body>';
        echo get_footer();      
    }

  
	      
    private function showHomePage () {
         /* Display page */
        echo get_header();
        echo '<body>';
        echo topbar_user();
        echo '<div id="container">';
        
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }

        echo '<div id="content">'; 
        echo 'Hello ' . $_SESSION['user']->get_string_attribute('user_name') . ', you are logged in.<br/><br/>';
        echo '<a href="php/userpage.php">Profile Page</a>'.'<br/>';
        echo '<a href="php/eventposting.php">Post an Event</a>'.'<br/>';
        echo '<a href="php/myevents.php">My events</a>'.'<br/>';
        echo '<a href="php/events.php">List of all events</a>'.'<br/>';
        echo '<a href="?action=logout">Log out</a><br/>';
        echo '</div>'; //end content
        echo '</div>'; //end container
        echo '</body>';
        //echo $r;
        echo get_footer();
    }
  
}


/**
 *
 * Check if input personal informations match the requirements
 */
 function check_user_info()
{
    $feedback = array ();
 
    // validating the input
    if (!empty($_POST['user_name'])
    && strlen($_POST['user_email']) <= 64
    && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
    && check_no_digit($_POST['user_lastname'], false)
    && strlen($_POST['user_lastname']) <= 64
    && check_no_digit($_POST['user_firstname'], false)
    && strlen($_POST['user_firstname']) <= 64
    && check_no_digit($_POST['user_nationality'], false)
    && check_no_digit($_POST['user_gender'], false)
    && check_and_valid_date($_POST['user_birthday'], False)        
    && (!is_it_futur ($_POST['user_birthday']))) {
        $feedback['status'] = true;
        $_SESSION['feedback'] = $feedback;
        return true;     
    } elseif (empty($_POST['user_email'])) {
        $feedback['msg'] = "Email cannot be empty";
    } elseif (strlen($_POST['user_email']) > 64) {
        $feedback['msg'] = "Email cannot be longer than 64 characters";
    } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
        $feedback['msg'] = "Your email address is not in a valid email format";
    } elseif (!check_no_digit($_POST['user_lastname'], false)){
        $feedback['msg'] = "Your lastname doesn't match the field requirements";
    } elseif (strlen($_POST['user_lastname']) > 64) {
        $feedback['msg'] = "Your Lastname cannot be longer than 64 characters or 
        shorter than 2 characters";
    } elseif (!check_no_digit($_POST['user_firstname'], false)){
        $feedback['msg'] = "Your firstname doesn't match the field requirements";
    } elseif (strlen($_POST['user_firstname']) > 64) {
        $feedback['msg'] = "Firstname cannot be longer than 64 characters";
    } elseif (!check_and_valid_date($_POST['user_birthday'], 0)){
        $feedback['msg'] = "Your birthday doesn't match the field requirements
         - mm/dd/yyyy - or is not a valid date";
    } elseif (is_it_futur($_POST['user_birthday'], 0)){
        $feedback['msg'] = "You probably would not be using this website 
        if you were not born :).";
    } elseif (!check_no_digit($_POST['user_nationality'], false)){
        $feedback['msg'] = "Your nationality doesn't match the field requirements";
    } elseif (!check_no_digit($_POST['user_gender'], false)){
        $feedback['msg'] = "Your gender is incorrect";
    } else {
        $feedback['msg'] = "An unknown error occurred.";
    }
    $feedback['status'] = false;
    $_SESSION['feedback'] = $feedback;
    // default return
    return false;
}


 	/**
     *
     * Save input personal informations into db and Session
     */
    function save_user_info ()
    {
        if (check_user_info()){
            $feedback = array ();
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
            $user_gender = utf8_encode(filter_var($_POST['user_gender'], 
            FILTER_SANITIZE_STRING));
            $user_password_hash = 
                $_SESSION['user']->get_string_attribute('user_password_hash');
           
            /*if (isset($_POST['user_password_new'])) {
                $user_password_hash = password_hash($_POST['user_password_new'],
                 PASSWORD_DEFAULT);
            }else {
                $user_password_hash = 
                $_SESSION['user']->get_string_attribute('user_password_hash');
            }*/
            	
            //Create an user object with it
            $parameters =  array ('user_id' =>
            $_SESSION['user']->get_string_attribute('user_id'),
				'user_name' => $user_name,
				'user_email' => $user_email,
				'user_birthday' => $user_birthday,
				'user_nationality' => $user_nationality,
				'user_lastname' => $user_lastname,
				'user_firstname' => $user_firstname,
                'user_password_hash' => $user_password_hash,
            	'user_gender' => $user_gender);
            $user = new User ($parameters);
            	
            // save new datas in database;
            if ($user->update_user_data()) {
                // Save them in the user session
                $_SESSION['user'] = $user;
                $feedback ['status'] = true;
                $_SESSION['feedback'] = $feedback;
                return true;
            } else {
                $feedback ['msg'] = 'The update of your profile information has failed';
                $feedback ['status'] = false;
                $_SESSION['feedback'] = $feedback;
                return false;
            }
        }else {
            return false;
        }
    }
    
    
    /**
     * 
     * Check if information given by user and related to password respects the
     * requirements
     */
    function check_user_pwd () {
        $feedback = array ();
    	if (!empty($_POST['user_password_new']) && !empty($_POST['user_password_repeat']) 
    	    && !empty($_POST['user_password'])
    	    && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
    	    && password_verify($_POST['user_password'], 
            $_SESSION['user']->get_string_attribute('user_password_hash'))
            && !password_verify($_POST['user_password_new'], 
            $_SESSION['user']->get_string_attribute('user_password_hash'))
            && strlen($_POST['user_password_new']) > 6 ) {
            $feedback['status'] = true;
            $_SESSION['feedback'] = $feedback;
            return true;
        } else if (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat']) 
    	    || empty($_POST['user_password'])) {
    	    $feedback['msg'] = "Please fill in all the required fields.";
    	} else if ($_POST['user_password_new'] != $_POST['user_password_repeat']) {
    	    $feedback['msg'] = "Please make sure that both passwords are similar.";
    	} else if (!password_verify($_POST['user_password'], 
            $_SESSION['user']->get_string_attribute('user_password_hash'))) {
    	    $feedback['msg'] = "Your current password is not valid.";
    	} else if (password_verify($_POST['user_password_new'], 
            $_SESSION['user']->get_string_attribute('user_password_hash'))) {
            $feedback['msg'] = "Your new password must differ from the current one";
        } else if (strlen($_POST['user_password_new']) < 6 ) {
            $feedback['msg'] = "Password has a minimum length of 6 characters";
        } else {
            $feedback['msg'] = "Unknown error, please try again";
        }
        $feedback['status'] = false;
        $_SESSION['feedback'] = $feedback;
        return false;    
    }
    
    
    /**
     * 
     * Save input new password into db
     **/ 
    function save_user_pwd () {
        if (check_user_pwd()){
            $feedback = array ();
            // Retrieve datas from form
            /* information in user object are already utf8-encoded so no need here 
             to decode and encode again */
            $user_name = $_SESSION['user']->get_string_attribute('user_name');
            //$user_password = $_POST['user_password'];
            $user_email = $_SESSION['user']->get_string_attribute('user_email');
            $user_birthday = $_SESSION['user']->get_string_attribute('user_birthday');
            $user_nationality = utf8_encode($_SESSION['user']->get_string_attribute('user_nationality'));
            $user_lastname = utf8_encode($_SESSION['user']->get_string_attribute('user_lastname'));
            $user_firstname = utf8_encode($_SESSION['user']->get_string_attribute('user_firstname'));
            $user_gender = utf8_encode($_SESSION['user']->get_string_attribute('user_gender'));
            $user_password_hash = password_hash($_POST['user_password_new'],
                 PASSWORD_DEFAULT);          
            	
            //Create an user object with datas
            $parameters =  array ('user_id' =>
            $_SESSION['user']->get_string_attribute('user_id'),
				'user_name' => $user_name,
				'user_email' => $user_email,
				'user_birthday' => $user_birthday,
				'user_nationality' => $user_nationality,
				'user_lastname' => $user_lastname,
				'user_firstname' => $user_firstname,
                'user_password_hash' => $user_password_hash,
            	'user_gender' => $user_gender);
            $user = new User ($parameters);
            	
            // save new datas in database;
            if ($user->update_user_data()) {
                // Save them in the user session
                $_SESSION['user'] = $user;
                $feedback ['msg'] = 'Your password has been successfully changed';
                $feedback ['status'] = true;
                $_SESSION['feedback'] = $feedback;
                return true;
            } else {
                $feedback ['msg'] = 'Your password update has failed';
                $feedback ['status'] = false;
                $_SESSION['feedback'] = $feedback;
                return false;
            }
        }else {
            return false;
        }
        
    }

    /**
     *
     * Check if input event informations match the requirements
     */
    function check_event_info()
    {
        $feedback = array ();                
        // validating the input
        if (!empty($_POST['event_name'])
        && (strlen($_POST['event_name']) > 3)
        && preg_match('#[\w]+#', $_POST['event_name'])
        && check_no_digit($_POST['event_type'], true)
        && (!empty($_POST['event_zipcode']))
        && check_no_digit($_POST['event_city'], true)
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
            $feedback['status'] = true;
            $_SESSION['feedback'] = $feedback;
            return true;
        } elseif (empty($_POST['event_name'])) {
            $feedback ['msg'] = "Event name cannot be empty";
        } elseif (strlen($_POST['event_name']) < 3) {
            $feedback ['msg'] = "Event name must be at least 3 characters long";
        } elseif (!check_no_digit($_POST['event_type'], true)) {
            $feedback['msg'] = "Event type cannot be empty or contain digit";
        } elseif (empty($_POST['event_address']) || empty($_POST['event_zipcode'])
        || empty($_POST['event_city']) || empty($_POST['event_country_name'])) {
            $feedback['msg'] = "Event location cannot be empty";
        } elseif (!check_no_digit($_POST['event_city'], true)) {
            $feedback['msg'] = "Event city is not a valid city";
        } elseif (!check_no_digit($_POST['event_country_name'], true)) {
            $feedback['msg'] = "Event country is not a valid country";
        }elseif (!data_validation($_POST['event_max_nb_participants'],
            FILTER_SANITIZE_NUMBER_INT, true)) {
            $feedback['msg'] = "Maximum number of participant must be a digit";
        } elseif (empty($_POST['event_starting_date'])) {
            $feedback['msg'] = "Event check in cannot be empty";
        } elseif (empty($_POST['event_ending_date'])) {
            $feedback['msg'] = "Event check out cannot be empty";
        } elseif (empty($_POST['event_description'])) {
            $feedback['msg'] = "Event description cannot be empty";
        } elseif (empty($_POST['event_spoken_languages'])) {
            $feedback['msg'] = "Event languages spoken cannot be empty";
        } elseif (empty($_POST['event_starting_time'])) {
            $feedback['msg'] = "Event check in time cannot be empty";
        } elseif (empty($_POST['event_ending_time'])) {
            $feedback['msg'] = "Event check out time cannot be empty";
        } elseif (!check_and_valid_date($_POST['event_starting_date'], true)){
            $feedback['msg'] = "The event check in date doesn't match the field
			requirements - mm/dd/yyyy - or is not a valid date";
        } elseif (!check_and_valid_date($_POST['event_ending_date'], true)){
            $feedback['msg'] = "The event check out date doesn't match the field
			requirements - mm/dd/yyyy - or is not a valid date";
        } elseif (!is_it_futur($_POST['event_starting_date'].' '
        .$_POST['event_starting_time'], true)){
            $feedback['msg'] = "I am not sure the time machine exists yet,
			please make sure the event check in date is valid.";
        } elseif (!is_it_futur($_POST['event_ending_date'].' '
        .$_POST['event_ending_time'], true)){
            $feedback['msg'] = "Well, you won't be able to end this event before
			you started it, please make sure the event check out is valid.";
        } elseif (!check_and_valid_time($_POST['event_starting_time'], true)){
            $feedback['msg'] = "The event check in time doesn't match the field
			requirements (hh:mm)";
        } elseif (!check_and_valid_time($_POST['event_ending_time'], true)){
            $feedback['msg'] = "The event check out time doesn't match the field
			requirements (hh:mm)";
        } elseif (!data_validation($_POST['event_description'],
        FILTER_SANITIZE_STRING, true)){
            $feedback['msg'] = "The event description does not match the field
			requirements";
        } elseif (!array_check_no_digit($_POST['event_spoken_languages'],true)){
            $feedback['msg'] = "The event spoken languages does not match the field
			requirements";
        } else {
            $feedback['msg'] = "An unknown error occurred.";
        }
        // default return
        $feedback['status'] = false;
        $_SESSION['feedback'] = $feedback;
        return false;    
    }
    
    
    
    /**
     * 
     * Save event information into db & session
     **/ 
    function save_event_info () {
        if (check_event_info()){
            $feedback = array ();
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
            $event_city = utf8_encode(filter_var($_POST['event_city'], 
                FILTER_SANITIZE_STRING));
            $event_country_name = filter_var($_POST['event_country_name'], 
                FILTER_SANITIZE_STRING);
                
            $event_languages_spoken = array(); 
            foreach($_POST['event_spoken_languages'] as $language) {
                array_push($event_languages_spoken, filter_var($language, 
                FILTER_SANITIZE_STRING));
            }
            
            $keys = geoconfig_parser(_INI_GEO_KEYS_CONFIG, 'all');
            $geocoder = new \Geocoder\Geocoder();
            $adapter  = new \Geocoder\HttpAdapter\BuzzHttpAdapter();
            $chain    = new \Geocoder\Provider\ChainProvider(array(
                new \Geocoder\Provider\FreeGeoIpProvider($adapter),
                new \Geocoder\Provider\HostIpProvider($adapter),
                new \Geocoder\Provider\GoogleMapsProvider($adapter, 'fr_FR', 'France', true),
                new \Geocoder\Provider\BingMapsProvider($adapter, $keys['bing']),
                // ...
            ));
            
            $geocoder->registerProvider($chain);
            // if location information is already in the database, no need
            // to geolocalize again. Just get the location id from the database
            $location_id = Location::get_location_from_address($event_address,
                $event_zipcode, $event_city, $event_country_name);
            // but if it is not, then we geolocalize and add the new location in the database
            if (!$location_id) {
                try {
                    $address = implode(', ', array ($event_address, $event_zipcode, 
                                $event_city, $event_country_name));
                    $geocode = $geocoder->geocode($address);
                    $event_location = new Location(array('geocoded'=>$geocode->toArray()));
                    $location_id = $event_location->insert_location();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }else {
                // retrieve all the geocoding infos
                $event_location = Location::get_location_from_id($location_id);
            }
            //Get the user id to use as the holder_id
            $event_holders_ids = $_SESSION['user']->get_user_id();
             
            //Create an user object with it
            $parameters =  array ('event_name' => $event_name,
			'event_type' => $event_type, 
			'event_starting_date' => $event_starting_date,
	    	'event_ending_date'=> $event_ending_date, 
	    	'event_max_nb_participants' => intval($event_max_nb_participants), 
	    	'event_holders_ids' => $event_holders_ids, 
	    	'event_languages' => $event_languages_spoken, 
			'event_location' => $event_location,
			'event_description' => $event_description);
            /*TODO: add exception around the object creation (This is not the only one)*/
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
                $feedback['status'] = true;
                $_SESSION['feedback'] = $feedback;
                return true;
            }
            else {
                $feedback['msg'] = 'The event creation failed <br/>';
                $feedback['status'] = false;
                $_SESSION['feedback'] = $feedback;
                return false;
            }
        } else {
            return false;
        }
    }
     
    