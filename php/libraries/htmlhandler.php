<?php

/**
 * 
 * Display table row
 * @param string $RowTitle
 * @param string $row_HTML_input
 */
function display_row ($RowTitle, $row_HTML_input)
{
    $r = '';
    $r .= '<tr>';
    $r .= '<td class="rowtitle">'.$RowTitle .'</td>' ;
    $r .= '<td class="rowinput">'.$row_HTML_input .'</td>' ;
    $r .= '<tr>';
    return $r;
}



/**
 * 
 * Return rows and columns from the Input parameters which match the 
 * following format (array(Column0Title, array(Column0Input0,Column0Input1,..), 
 * Column1Title, array(Column1Input0,Column1Input1,...),...))
 * @param array $Inputs
 */
function display_col ($Inputs)
{
    $r = '';
    $r .= '</tr>';
    $biggest_array_size = 0;
    foreach ($Inputs as $ColTitle => $ArrayInput) {
        $r .= '<th>'.$ColTitle .'</th>' ;
        $biggest_array_size = ((sizeof($ArrayInput)>$biggest_array_size)? 
            sizeof($ArrayInput) : $biggest_array_size);
    }
    $r .= '</tr>';
    for ($i = 0 ; $i <= $biggest_array_size ; $i++) {
        $r .= '<tr>';
        foreach ($Inputs as $ColTitle => $ArrayInput) {
            if (isset($ArrayInput[$i])) {
                $r .= '<td>'.$ArrayInput[$i] .'</td>' ;
            }
        }
        $r .= '</tr>';
    }
    
    return $r;
}


/**
 * 
 * Display table row with element of the array input parameter in a new column
 * @param array $row_HTML_inputs
 */
function display_advanced_row ($row_HTML_inputs)
{
    $r = '';
    $r .= '<tr>';
    foreach ($row_HTML_inputs as $row_HTML_input) {
        $r .= '<td>'.$row_HTML_input .'</td>' ;
    }
    $r .= '</tr>';
    return $r;
}

/**
 * 
 * Display table header row with element of the array input parameter in a new column
 * @param array $row_HTML_inputs
 */
function display_advanced_tr_row($row_HTML_inputs) {
    $r = '';
    $r .= '<tr>';
    foreach ($row_HTML_inputs as $row_HTML_input) {
        $r .= '<th>'.$row_HTML_input .'</th>' ;
    }
    $r .= '</tr>';
    return $r;
}


/**
 * 
 * Display table row with element of the array input parameter in a new column. 
 * Title would be the first column
 * @param string $title
 * @param array $row_HTML_inputs
 */
function display_advanced_row_and_title ($title, $row_HTML_inputs)
{
    $r = '';
    $r .= '<tr>';
    $r .= '<td>'.$title .'</td>' ;
    foreach ($row_HTML_inputs as $row_HTML_input) {
        $r .= '<td>'.$row_HTML_input .'</td>' ;
    }
    $r .= '</tr>';
    return $r;
}

/**
 * 
 * Display a drop down list. Can set up a default value, a css class and html parameters
 * To be continued...
 * @param array($key=>$value) $SelectParameters
 * @param array list $InputValues
 * @param string $DefaultValue
 * @param string $CssClass
 */
function display_dropdownlist ($SelectParameters, $InputValues, $DefaultValue, $CssClass)
{
    $r = '<select class:"'.$CssClass.'" ';
    // Set up parameters for select input
    if (is_array ($SelectParameters))
    {
        if (array_key_exists ('name' , $SelectParameters))
        {
            $r.= ' name="'.$SelectParameters['name'].'"';
        }
        if (array_key_exists ('multiple' , $SelectParameters))
        {
            if ($SelectParameters['multiple'] == True)
            {
                $r .= ' multiple';
            }
        }
        if (array_key_exists ('required' , $SelectParameters))
        {
            if ($SelectParameters['required'] == True)
            {
                $r .= ' required';
            }
        }
    }
    $r .= '/>';
    // Look after the value
    // If default value is empty, add empty field to select options
    //if (empty($DefaultValue)){
    //	$r .= '<option selected value=""> </option>';
    //}
    foreach ($InputValues as $Inputvalue)
    {
        if (!strcmp($Inputvalue,$DefaultValue)) {
            $r .= '<option selected value="'.$Inputvalue.'">'.$Inputvalue.'</option>';
        }else {
            $r .= '<option value="'.$Inputvalue.'">'.$Inputvalue.'</option>';
        }
    }
    $r .= '</select>';
    return $r;
}

/**
 * 
 * Return HTML Header with css file loading according to session status
 */
function get_header () {

    if ((isset($_SESSION) && !empty($_SESSION['user']))) {
        return '<!DOCTYPE html> <html>
		<head>
        <link rel="stylesheet" type="text/css" href="css/backstyle.css">
        <title>Befalol Index</title>
        </head>';
    }else {
       return '<!DOCTYPE html> <html>
       <head>
        <link rel="stylesheet" type="text/css" href="css/index.css">
        <title>Befalol Index</title>
        </head>';
    }
}
/**
 * 
 * Return HTML footer
 */
function get_footer () {
   return '<div class="footer">
    		<h2> Here will come the footer soon :</h2>
    	 </div>'; 
}


/**
 * 
 * Return HTML left part of Topbar/Navbar for user front office website, once logged
 */
function topbar_user ()
{
    $r = '';
    $r = '<div id="topbar">
   			<ul>
   				<li>
                 	<p id="logo">LOGO</p>
                </li>
                <li>
                 	<a href="../index.php">Befalol</a>
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

/**
 * 
 * Return HTML right part of Topbar/Navbar for user front office website, once logged
 */
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
                        <a href="../index.php?action=logout">Log out</a>
                    </li>
            	</ul>
			</div>';
     return $r;
}

/**
 * 
 * Return HTML right part of Topbar/Navbar for user homepage front office website, once logged
 * To be changed / This is a temporary solution
 */
function toprightbar_index_logged() {
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
                        <a href="index.php?action=logout">Log out</a>
                    </li>
            	</ul>
			</div>';
     return $r;
}

/**
 * 
 * Return HTML left part of Topbar/Navbar for public front office website
 */
function topbar_index ()
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

/**
 * 
 * Return HTML left part of Topbar/Navbar for user homepage front office website, once logged
 */
function topbar_index_logged ()
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
    $r .= toprightbar_index_logged ();
    $r .= '</ul></div>';
    return $r;
}


/**
 * 
 * Return HTML right part of Topbar/Navbar for public front office website
 */
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


/**
 * 
 * return an HTML image according to src file,  id div string, text or css class name
 * @param string $image_path
 * @param string $div_id
 * @param string $text
 * @param string $class_img
 */
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