<?php
/* $errno : error type
 $errstr : error message
 $errfile : error file
 $errline : error coding line */

function handle_basic_errors ($errno,$errstr,$errfile,$errline)
{
    // We identify the error type
    switch($errno)
    {
        case E_USER_ERROR :
            $type = "Fatal error:";
            echo '<p><strong>$type</strong> : '.$errstr.'</p>';
            break;
        case E_USER_WARNING :
            $type = "Error:";
            echo '<p><strong>$type</strong> : '.$errstr.'</p>';
            break;
        case E_USER_NOTICE :
            $type = "Warning:";
            echo '<p><strong>$type</strong> : '.$errstr.'</p>';
            break;
        case E_ERROR :
            $type = "Fatal error";
            echo '<p><strong> $type </strong> : '.$errstr.'</p>';
            break;
        case E_WARNING :
            $type = "Error:";
            echo '<p><strong>$type</strong> : '.$errstr.'</p>';
            break;
        case E_NOTICE :
            $type = "Warning:";
            echo '<p><strong>$type</strong> : '.$errstr.'</p>';
            break;
        default :
            $type = "Unknown error:";
            echo '<p><strong>$type</strong> : '.$errstr.'</p>';
            break;
    }

    // We identify the error.
    $error = $type."Error Message : [".$errno."]".$errstr.
	"Line :".$errline." File :".$errfile;

    //Retrieve value of different arrays

    $info = date("d/m/Y H:i:s",time()).
	":".$_SERVER['REMOTE_ADDR'].
	"GET:".serialize($_GET).
	"POST:".serialize($_POST).
	"SERVER:".serialize($_SERVER).
    //"COOKIE:".(isset($_COOKIE)? serialize($_COOKIE) : "Undefined").
    //"SESSION:".(isset($_SESSION)? serialize($_SESSION) : "Undefined");

    // Open error file
    $handle = fopen("Errlog.txt", "a");

    // We write error messages and informations
    if ($handle)
    fwrite($handle,$error.$info);
    else echo"Unable to open the file.";

    fclose($handle);
}
?>