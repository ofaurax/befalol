<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    set_include_path('./php/:'.get_include_path());
    require_once('libraries/eventhandler.php');
    require_once('libraries/geographyhandler.php');
    require_once('libraries/dbhandler.php');
    require_once('libraries/userhandler.php');
    require_once('libraries/tools.php');
    require_once('libraries/htmlhandler.php');
    require_once('libraries/processing.php');
    
    session_start();
    define ('_SERVER_DIR', $_SESSION['_SERVER_DIR']);
    define ('_URL_PATH', $_SESSION['_URL_PATH']);
    define ('_INI_DB_CONFIG_FILE', $_SESSION['_INI_DB_CONFIG_FILE'] );
    define ('_INI_GEO_KEYS_CONFIG', $_SESSION['_INI_GEO_KEYS_CONFIG']);

    define ('_COMPOSER_FLAG', $_SESSION['_COMPOSER_FLAG']);
    
    if (_COMPOSER_FLAG == true) {
        require_once _SERVER_DIR.'/vendor/autoload.php';
    }
    
        
    $r = '';
    // if session initiated
    if (isset($_SESSION['user']))
    {
        
        $nationalities = NULL;
        $nationalities = Nationality::select_all_nationalities();
        $genders = NULL;
        $genders = User::get_all_gender_types();
        // if the member is not the user then we reading only on the user information
        // no form
        if (isset($_GET['id']) && ($_SESSION['user']->get_user_id() != $_GET['id'])) {
            try {
                $member_id = $_GET["id"];
                $member = User::get_user_from_id($member_id);
            } catch (Expection $e) {
                 echo 'Exception received : ',  $e->getMessage(), "\n";
            }
            // Build the page
            // Profile picture  and left pannel
            $r .= get_div('left_panel', '<div id="picture_frame"> <img src="../images/avatar.jpg"
                  class="user_picture" height="250px" /></div>' . '<h1>Settings</h1>'. 
                get_div('left_panel_box', 'Profile >') . get_div('left_panel_box', 'Password>')); 
            if (!empty($nationalities)) {
                $dump_r = '';
                
                // Username field
                $dump_r .= '<span class="title">'.$member->get_string_attribute('user_name').'</span>';
                $dump_r .= '<hr/>';                          
                // Table
                $dump_r .= '<table>';
                $dump_r .= '<caption data-icon="u"> Profile Information </caption>';
                $dump_r .= display_row('Birthday:', $member->get_string_attribute('user_birthday'));
                $dump_r .= display_row('Nationality:', $member->get_string_attribute('user_nationality'));
                $dump_r .= display_row('Gender:', $member->get_string_attribute('user_gender'));
                $dump_r .= '</table>';                       
                
                // add the form to the existing html stream
                $r .= get_div('contentarea',$dump_r);
            }else {
                $r .= 'he form could not have been loaded';
                //$this->feedback = "The form could not have been loaded";
            }
        // but if the member is the user, display a form so he can change informations    
        } else {
            // retrieve datas
            $user_name = $_SESSION['user']->get_string_attribute('user_name');
            $user_email = $_SESSION['user']->get_string_attribute('user_email');
            $user_lastname = utf8_decode($_SESSION['user']->get_string_attribute('user_lastname'));
            $user_firstname = utf8_decode($_SESSION['user']->get_string_attribute('user_firstname'));
            $user_birthday = $_SESSION['user']->get_string_attribute('user_birthday');
            $user_nationality = $_SESSION['user']->get_string_attribute('user_nationality');
            $user_gender = $_SESSION['user']->get_string_attribute('user_gender');
            
            //we check if forms have been sent
            if (isset($_POST['changeuserinfo'])) {
                // if information given to the form is correct process to change
                if (save_user_info()) {
                    // update datas for display
                    $user_name = $_SESSION['user']->get_string_attribute('user_name');
                    $user_email = $_SESSION['user']->get_string_attribute('user_email');
                    $user_lastname = utf8_decode($_SESSION['user']->get_string_attribute('user_lastname'));
                    $user_firstname = utf8_decode($_SESSION['user']->get_string_attribute('user_firstname'));
                    $user_birthday = $_SESSION['user']->get_string_attribute('user_birthday');
                    $user_nationality = $_SESSION['user']->get_string_attribute('user_nationality');
                    $user_gender = $_SESSION['user']->get_string_attribute('user_gender');
                // and if it is not, signal the user;
                }else {            
                    /*TODO  we should refill the form with datas*/
                    $r .= ($_SESSION['feedback']['msg']);
                }
            } else if (isset($_POST['changeuserpwd'])) {
                if (save_user_pwd()) {
                    $r .= ($_SESSION['feedback']['msg']);
                }else {
                    /*TODO  we should refill the form with datas*/
                    $r .= ($_SESSION['feedback']['msg']);
                }
            }
               
            // Build the page
            // Profile picture  and left pannel
            $r .= get_div('left_panel', '<div id="picture_frame"> <img src="../images/avatar.jpg"
                  class="user_picture" height="250px" /></div>' . '<h1>Settings</h1>'. 
                get_div('left_panel_box', 'Profile >') . get_div('left_panel_box', 'Password>')); 
            if (!empty($nationalities)) {
                $dump_r = '';
                
                // Username field
                $dump_r .= '<span class="title">'.$user_name.'</span>';
                $dump_r .= '<hr/>';
                // Form
                $dump_r .=  '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] .
                '" name="changeuserinfoform">';
                
                // Table
                $dump_r .= '<table>';
                $dump_r .= '<caption data-icon="u"> Profile Information </caption>';
                $dump_r .= display_row('User Name:', '<input type="text" name="user_name" value="'
                .$user_name .'"readonly />');
                $dump_r .= display_row('Last Name:', '<input type="text" 
                	name="user_lastname" value="'. $user_lastname.'"/>');
                $dump_r .= display_row('First name:', '<input type="text"
                	name="user_firstname" value="'. $user_firstname.'"/>');
                $dump_r .= display_row('Email:', '<input type="email" 
                	name="user_email" value="' . $user_email.'"required/>');
                $dump_r .= display_row('Birthday:', '<input type="date" 
                	name="user_birthday" placeholder="mm/dd/yyyy" value="'
                    . $user_birthday .'"/>');
                $dump_r .= display_row('Nationality:', display_dropdownlist
                (array('name' => 'user_nationality', 'multiple' => FALSE,
                	'required' => False) , $nationalities, $user_nationality, 'nationalities'));
                $dump_r .= display_row('Gender:', display_dropdownlist
                (array('name' => 'user_gender', 'multiple' => FALSE,
                	'required' => False) , $genders, $user_gender, 'genders'));
                $dump_r .= display_row('', '<input type="submit"  name="changeuserinfo" value="Save" />');
                $dump_r .= '</table>';
                $dump_r .= '</form>';
                
                //second form, second table for password change;
                $dump_r .=  '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . 
                '" name="changepwdform">';
                $dump_r .= '<table id="user_info">';
                $dump_r .= '<caption data-icon="p"> Change Password </caption>';
                $dump_r .= display_row('Current password:', '<input 
                    id="login_input_password" class="login_input" type="password" 
                    name="user_password" pattern=".{6,}" required autocomplete="off" />');
                $dump_r .= display_row('New password:', '<input id="login_input_password_new" 
                	class="login_input" type="password" name="user_password_new" 
                	pattern=".{6,}" required autocomplete="off" />');
                $dump_r .= display_row('Confirm password:', '<input id="login_input_password_new" 
                    class="login_input" type="password" name="user_password_repeat" 
                    pattern=".{6,}" required 
                    autocomplete="off" />');
                $dump_r .= display_row('', '<input type="submit"  name="changeuserpwd" value="Change Password" />');
                $dump_r .= '</table>';
                $dump_r .= '</form>';
                
                
                // add the form to the existing html stream
                $r .= get_div('contentarea',$dump_r);
                
            }else {
                $r .= 'he form could not have been loaded';
                //$this->feedback = "The form could not have been loaded";
            }
        }
    } else {
        header('Location: ../index.php'); 
    }

    $r .= '<a href="userpage.php">Profile Page</a>'.'<br/>';
    $r .= '<a href="eventposting.php">Post an Event</a>'.'<br/>';
    $r .= '<a href="myevents.php">My events</a>'.'<br/>';
    $r .= '<a href="events.php">List of all events</a>'.'<br/>';
    $r .= '<a href="../index.php?action=logout">Log out</a><br/>';

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="../css/backstyle.css">
	<title>Befalol Index</title>
</head>
	<body>
    	<?php  echo topbar_user();?>
    	<div id="container">
    		<div id="content">
    		<?php  echo $r;?>
    		</div> <!-- end content -->
    	</div> <!-- end container -->
	</body>
	<?php  echo get_footer();?>