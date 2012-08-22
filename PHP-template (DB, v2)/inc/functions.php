<?php
	// Get correct ip even if user is on a proxy
	// - http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
	function getRealIpAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		} 
		return $ip;
	}

	// If the array sent in contains any items an error-block will be printed out.
	function outputErrors($errors)
	{
		if (!empty($errors)) {
			echo "
				<div class='errors'>
					<h4>Feilmelding:</h4>
					<ul>";
			foreach( $errors as $err_row ) {
				echo "
						<li><i>&times;</i> ", $err_row, "</li>";
			}
			echo "
					</ul>
				</div>";
		}

	}

	// If the array sent in contains any items an error-block will be printed out.
	function printDebugger()
	{
		$debug = $_SESSION['debug'];

		if (!empty($debug)) {
			echo "
				<div class='debugger'>
					<h4>DEBUGGER:</h4>
					<ul>";
			foreach( $debug as $debug_row ) {
				echo "
						<li><i>&times;</i> ", $debug_row, "</li>";
			}
			echo "
					</ul>
				</div>";
		}
	}

	// Print any transactions-error from the database.
	function printError_tran()
	{
		outputErrors( $_SESSION['ERRORS_TRAN'] );
	}

	// Fire an error which easily can be checked for and/or printed out.
	function pushError($string)
	{
		array_push($_SESSION['ERRORS'], $string);
	}

	// Unique function for filling up on MySQL TRANSACTON-errors
	function pushError_tran($string)
	{
		array_push($_SESSION['ERRORS_TRAN'], $string);
	}

	// Put a post in the debug-array
	function pushDebug($string)
	{
		array_push($_SESSION['debug'], $string);
	}


	/**
	* Returns day name and month name in Norwegian instead of English on an English system.
	*
	* @param string $what	What name do you want extracted? D = Day, M = Month (supports lowercase as well)
	* @param date $thedate	The date (in correct dateformat) you want to extract name from.
	* @return string $what
	*/
	function strDateToNorwegian($what, $thedate)
	{
		// From day number set day name
		$daynum = date("N", strtotime($thedate) );
		switch($daynum) {
			case 1: $day = "Mandag"; break;
			case 2: $day = "Tirsdag"; break;
			case 3: $day = "Onsdag"; break;
			case 4: $day = "Torsdag"; break;
			case 5: $day = "Fredag"; break;
			case 6: $day = "Lørdag"; break;
			case 7: $day = "Søndag"; break;
			default: $day = "Ukjent"; break;
		}

		// From month number set month name
		$monthnum = date("n", strtotime($thedate) );
		switch($monthnum) {
			case 1: $month = "Januar"; break;
			case 2: $month = "Februar"; break;
			case 3: $month = "Mars"; break;
			case 4: $month = "April"; break;
			case 5: $month = "Mai"; break;
			case 6: $month = "Juni"; break;
			case 7: $month = "Juli"; break;
			case 8: $month = "August"; break;
			case 9: $month = "September"; break;
			case 10: $month = "Oktober"; break;
			case 11: $month = "November"; break;
			case 12: $month = "Desember"; break;
			default: $month = "Ukjent"; break;
		}

		// Depending on what we wanted to get from the function return that
		$returnthis = '';
		switch( strtoupper($what) )
		{
			case 'M': $returnthis = $month; break;
			case 'D': $returnthis = $day; break;
		}
		return $returnthis;
	}


	function randomAlphaNum($length)
	{
		$rangeMin = pow(36, $length-1); //smallest number to give length digits in base 36 
		$rangeMax = pow(36, $length)-1; //largest number to give length digits in base 36 
		$base10Rand = mt_rand($rangeMin, $rangeMax); //get the random number 
		$newRand = base_convert($base10Rand, 10, 36); //convert it 

		return $newRand; //spit it out 
	}

	// Not the worlds best validator, but it'll work for now.
	// http://www.linuxjournal.com/article/9585
	function isValidEmail($email)
	{
		if(preg_match("/[.+a-zA-Z0-9_-]+@[a-zA-Z0-9-]+.[a-zA-Z]+/", $email) > 0)
			return true;
		else
			return false;
	}

	// Simple fetch form-field with isset-check. Always returns a string. Never empty or null.
	function formGet($field)
	{
		if (isset($_POST[$field]))
			return removeHtmlEntities( trim($_POST[$field]) );
		else
			return '';
	}

	// Simple fetch get/url-parameter with isset-check. Always returns a string. Never empty or null.
	function qsGet($field)
	{
		if (isset($_GET[$field]))
			return removeHtmlEntities( trim($_GET[$field]) );
		else
			return '';
	}

	// Simple validation of length of a string (support for form handling).
	function isValidLength($string, $min, $max)
	{
		if (mb_strlen($string,'UTF-8') >= $min && mb_strlen($string,'UTF-8') <= $max)
			return true;
		else
			return false;
	}

	// Return true if positive number, or false if negative number!
	function sign( $number ) { 
		return ( $number > 0 ) ? 1 : ( ( $number < 0 ) ? -1 : 0 );
	}

	// Just somethink quick I used for some special SQL-need where I need to remove a lot of html formating that was common.
	function removeHtmlEntities($string) {
		$tmp = $string;
		$tmp = str_replace('&amp;','&',$tmp);
		$tmp = str_replace('&quot;','"',$tmp);
		$tmp = str_replace('&#039;',"'",$tmp);
		$tmp = str_replace('&apos;',"'",$tmp);
		$tmp = str_replace('&lt;','<',$tmp);
		$tmp = str_replace('&gt;','>',$tmp);

		return $tmp;
	}

?>