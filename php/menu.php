<?php 

function get_header () {

    if ((isset($_SESSION) && !empty($_SESSION))) {
        return '<!DOCTYPE html> <html>
		<head>
        <link rel="stylesheet" type="text/css" href="css/style2.css">
        <title>Befalol Index</title>
        </head>';
    }else {
       return '<!DOCTYPE html> <html>
       <head>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Befalol Index</title>
        </head>';
    }
}

function get_footer () {
   return '<div class="footer">
    		<h2> Here will come the footer soon :</h2>
    	 </div>'; 
}

function topbar_user ()
{
    $r = '';
    $r = '<div id="topbar">
   			<ul>
   				<li>
                 	<p id="logo">LOGO</p>
                </li>
                <li>
                 	<a href="'.$_SERVER['SCRIPT_NAME'].'">Befalol</a>
                </li>
                <li>
                    <a href="#">Around me</a>
                </li>   
                <li>
                    <a href="#">My events</a>
                </li>'; 
    $r .= toprightbar_user ();
    $r .= '</ul></div>';
    return $r;
    
}

function toprightbar_user() {
    $r = '';
    $r = '<div id="toprightbar">
    			<ul>
               		<li>
                        <a href="#">Help</a>
                    </li>
                    <li>
                        <a href="#">Settings</a>
                    </li>
                    <li>
                        <a href="#">Log out</a>
                    </li>
            	</ul>
			</div>';
     return $r;
}

 function topbar_public ()
{
    $r = '';
    $r = '<div id="topbar">
   			<ul>
                <li>
                 	<a href="'.$_SERVER['SCRIPT_NAME'].'">Befalol</a>
                </li>
                <li>
                    <a href="#">Around me</a>
                </li>   
                <li>
                    <a href="#">Activities</a>
                </li>
                <li>
                    <a href="#">Visits</a>
                </li>
                <li>
                    <a href="#">Journeys</a>
                </li>
                <li>
                    <a href="#">Partying</a>
                </li>';
    $r .= toprightbar_public ();
    $r .= '</ul></div>';
    return $r;
}

function toprightbar_public() {
    $r = '';
    $r = '<div id="toprightbar">
    			<ul>
               		<li>
                        <a href="#">Help</a>
                    </li>
                    <li>
                        <a href="' . $_SERVER['SCRIPT_NAME'] . '?action=signin">Sign in</a>
                    </li>
                    <li>
                        <a href="' . $_SERVER['SCRIPT_NAME'] . '?action=login">Log in</a>
                    </li>
            	</ul>
			</div>';
     return $r;
}

function get_image_box($image_path, $div_id, $text, $class_img) {
    $r = '';
    $r .= '<div id="' . $div_id . '"><img src="' . $image_path . '" alt="' . 
        $text . '" title="Zoom" class ="' . $class_img . '" /></div>';
    return $r;
}

/**
 * 
 * Wrap a text in a div container
 * @param string $div_id
 * @param string $text
 */
function get_div ($div_id, $text) {
    $r = '';
    $r .= '<div id="' . $div_id . '">';
    $r .= $text;
    $r .= '</div>';
    return $r;
}

/**
 * 
 * Wrap a text in a span container
 * @param string $span_id
 * @param string $text
 */
function get_span ($span_class, $text) {
    $r = '';
    $r .= '<span class="' . $span_class . '">';
    $r .= $text;
    $r .= '</span>';
    return $r;
}



?>