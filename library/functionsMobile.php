<?php
/*****************function for generate us formate for vew in fronted*********/
function usMobleFormate($phonenumberV) {
	$phonenumber = explode ( "-", $phonenumberV );
	$phonenumber = '(' . $phonenumber [1] . ')' .' '.$phonenumber [2] . '-' . $phonenumber [3];
	return $phonenumber;
}