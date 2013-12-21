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