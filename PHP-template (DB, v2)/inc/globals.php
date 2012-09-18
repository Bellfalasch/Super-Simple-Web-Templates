<?php require('inc/database.php'); ?>
<?php require('inc/functions.php'); ?>
<?php

	// Define environment; development on or off.
	DEFINE('DEV_ENV', false);

	if (DEV_ENV) {
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	}

	//////////////////////////////////////////////////////////////////////////////////
	// Get the current folder the files are in, account for different servers returning the FILE-var differently.
	$mappar = __FILE__;
	if ( strpos($mappar,'\\') > 0 ) {
		$mapparArr = explode('\\', $mappar); // localhost
	} else {
		$mapparArr = explode('/', $mappar); // dedicated server
	}
	$mapp = $mapparArr[count($mapparArr) - 3];
	
	// Dynamic links etc based on where we have the code-files
	if ($_SERVER['SERVER_NAME'] == 'localhost') {
	
		$SYS_folder = "/MY_FOLDER"; // If more folders just add as "/MY_FOLDER/SUBFOLDER/MORE/EXTRA"
		$SYS_url = "localhost";
	
	} else { // Live woop!
		
		$SYS_url = $_SERVER['SERVER_NAME'];
		$SYS_folder = "";
	}

	$currentFile = $_SERVER["SCRIPT_NAME"];
	$parts = explode('/', $currentFile);
	$currentFile = $parts[count($parts) - 1];
	$SYS_script = str_replace('.php','',$currentFile);
	//////////////////////////////////////////////////////////////////////////////////

	header('Content-type: text/html; charset=utf-8');
	header('X-UA-Compatible: IE=edge,chrome=1');
	
	if (!DEV_ENV)
		ini_set('session.gc_maxlifetime', '10800');

	session_cache_expire('30'); // default 180 minutes
	date_default_timezone_set('Europe/Oslo');
	setlocale(LC_TIME, 'no_NO.ISO_8859-1', 'norwegian', 'nb_NO.utf8', 'no_NO.utf8');

	ob_start();
	session_start();
	//ob_clean();

	$_SESSION['ERRORS'] = array(); // Reset the error-session on each page load =)
	$_SESSION['debug'] = array();

?>