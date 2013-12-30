<?php

function display_row ($RowTitle, $RowHTMLInput)
{
    $r = '';
    $r .= '<tr>';
    $r .= '<td>'.$RowTitle .'<td>' ;
    $r .= '<td>'.$RowHTMLInput .'<td>' ;
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
    $r .= '<tr>';
    $biggest_array_size = 0;
    foreach ($Inputs as $ColTitle => $ArrayInput) {
        $r .= '<th>'.$ColTitle .'<th>' ;
        $biggest_array_size = ((sizeof($ArrayInput)>$biggest_array_size)? 
            sizeof($ArrayInput) : $biggest_array_size);
    }
    $r .= '<tr>';
    for ($i = 0 ; $i <= $biggest_array_size ; $i++) {
        $r .= '<tr>';
        foreach ($Inputs as $ColTitle => $ArrayInput) {
            if (isset($ArrayInput[$i])) {
                $r .= '<td>'.$ArrayInput[$i] .'<td>' ;
            }
        }
        $r .= '<tr>';
    }
    
    return $r;
}

function display_advanced_row ($RowHTMLInputs)
{
    $r = '';
    $r .= '<tr>';
    foreach ($RowHTMLInputs as $RowHTMLInput) {
        $r .= '<td>'.$RowHTMLInput .'<td>' ;
    }
    $r .= '<tr>';
    return $r;
}


function display_dropdownlist ($SelectParameters, $InputValues, $DefaultValue)
{
    $r = '<select';
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
?>