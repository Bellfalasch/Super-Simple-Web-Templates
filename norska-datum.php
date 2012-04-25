<?php
/** 
 * Returns day name and month name in Norwegian instead of English on an English system.
 *
 * @param string $what			What name do you want extracted? D = Day, M = Month (supports lowercase as well)
 * @param date $thedate			The date (in correct dateformat) you want to extract name from.
 * @return string $what				
 */
function strDateToNorwegian($what, $thedate)
{
	// From day number set day name
	$daynum = date("N", strtotime($thedate) );
	switch($daynum) {
		case 1:	 $day = "Mandag";  break;
		case 2:	 $day = "Tirsdag"; break;
		case 3:	 $day = "Onsdag";  break;
		case 4:	 $day = "Torsdag"; break;
		case 5:	 $day = "Fredag";  break;
		case 6:	 $day = "Lørdag";  break;
		case 7:	 $day = "Søndag";  break;
		default: $day = "Ukjent";  break;
	}
	
	// From month number set month name
	$monthnum  = date("n", strtotime($thedate) );
	switch($monthnum) {
		case 1:  $month = "Januar";    break;
		case 2:  $month = "Februar";   break;
		case 3:  $month = "Mars";      break;
		case 4:  $month = "April";     break;
		case 5:  $month = "Mai";       break;
		case 6:  $month = "Juni";      break;
		case 7:  $month = "Juli";      break;
		case 8:  $month = "August";    break;
		case 9:  $month = "September"; break;
		case 10: $month = "Oktober";   break;
		case 11: $month = "November";  break;
		case 12: $month = "Desember";  break;
		default: $month = "Ukjent";    break;
	}

	// Depending on what we wanted to get from the function return that
	$returnthis = '';
	switch( strtoupper($what) )
	{
		case 'M': $returnthis = $month;	break;
		case 'D': $returnthis = $day;	break;
	}
	return $returnthis;
}


// * Simple example output:
// Get Norwegian day name from timestamp (make sure it is correct date format with "strtotime" if working towards database).
echo strDateToNorwegian('D', date(DATE_W3C) ) . '<br />';

// * Database example:
// echo transDate('D', strtotime( $row->timestamp )) . '<br />';

// * Get Norwegian month name from timestamp
echo strDateToNorwegian('M', date(DATE_W3C) ) . '<br />';

?>