<!DOCTYPE html>

<?
	// Dynamic links etc based on where we have the code-files
	// Needs to be set here so that require-url matcher.
	if ($_SERVER['SERVER_NAME'] == 'localhost') {
		$SYS_folder = '/morgenbladet/verv';
	} else {
		$SYS_folder = '';
	}

	$SYS_incroot = rtrim($_SERVER['DOCUMENT_ROOT'],"/") . $SYS_folder;

	//$SYS_file = basename($_SERVER['REQUEST_URI'], ".php");
	$currentFile = $_SERVER["SCRIPT_NAME"];
	$parts = explode('/', $currentFile);
	$currentFile = $parts[count($parts) - 1];
	$SYS_script = str_replace('.php','',$currentFile);
?>

<?php require( $SYS_incroot . '/inc/database.php'); ?>
<?php require( $SYS_incroot . '/inc/functions.php'); ?>
<?php require('_database.php'); ?>

<?php

	// Define environment; development on or off.
	DEFINE('DEV_ENV', true);

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
	
		$SYS_folder = "/morgenbladet/verv";
		$SYS_url = "localhost";
/*
		if ($mapp === 'dev') {
			$SYS_folder .= "/dev";
		} elseif ($mapp === 'demo') {
			$SYS_folder .= "/demo";
		} elseif ($mapp === 'live') {
			$SYS_folder .= "/live";
		} else {
			$SYS_folder .= "";
		}
*/	
	} else {
		
		//echo("test: " . $_SERVER['SERVER_NAME']);

		$SYS_url = $_SERVER['SERVER_NAME']; //"www.smartlapper.no";

		if ($mapp === 'dev') {
			$SYS_folder = "/dev";
		} elseif ($mapp === 'demo') {
			$SYS_folder = "/demo";
		} else {
			$SYS_folder = "";
		}
	}
	//////////////////////////////////////////////////////////////////////////////////

	header('Content-type: text/html; charset=utf-8');
	
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






	// Push important debugging data to the footer:
	pushDebug("
			folder: $SYS_folder -
			script: $SYS_script -
			sessionID: " . session_id() . "
			");

	if (isset($_SESSION['username'])) {
		
		pushDebug("
				[SESSION]
				username: " . $_SESSION['username'] . "
				mail: " . $_SESSION['mail'] . "
				level: " . $_SESSION['level'] . "
				id: " . $_SESSION['id']
				);
	}

	// Get system admin level into a variable.
	if (isset($_SESSION['level'])) {
		$SYS_adminlvl = $_SESSION['level'];
	} else {
		$SYS_adminlvl = 0;
		if ($SYS_script != "login" && $SYS_script != "index" )
		{
			ob_clean();
			header('Location: ' . $SYS_folder . '/_admin/login.php');
		}
	}

?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf8" />
	<title><?= $pagetitle ?> - Smartlapper</title>
	<link rel="shortcut icon" href="<?= $SYS_folder ?>/favicon.ico">
	<link rel="stylesheet" href="<?= $SYS_folder ?>/_admin/bootstrap.min.css" />
	<link rel="stylesheet" href="<?= $SYS_folder ?>/_admin/admin.css?v=<?php if (DEV_ENV) echo rand(); ?>" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>

	<?php
		function isActiveOn($pages) {
			global $SYS_script;
			$arrPages = explode(",",$pages);
			if (in_array($SYS_script,$arrPages))
				echo ' class="active"';
		}
	?>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				
				<a class="brand" href="http://www.nxt.no/">nxt cms</a>
				
				<div class="nav-collapse">

					<ul class="nav">
						<li<?php isActiveOn("login,index") ?>><a href="<?= $SYS_folder ?>/_admin/index.php">Start</a></li>
						<?php if ($SYS_adminlvl > 0) { ?>
							<li<?php isActiveOn("campaign,image") ?>><a href="<?= $SYS_folder ?>/_admin/campaign.php">Campaigns</a></li>
							<?php if ($SYS_adminlvl == 2) { ?>
							<li<?php isActiveOn("users") ?>><a href="<?= $SYS_folder ?>/_admin/users.php">Users</a></li>
							<?php } ?>
						<?php } ?>
					</ul>

				</div>
			</div>
		</div>
	</div>
	
	<div class="subnav subnav-fixed">
		<ul class="nav nav-pills">
			<?php if ($SYS_script == "login" || $SYS_script == "index") { ?>

				<li<?php isActiveOn("login,index") ?>><a href="<?= $SYS_folder ?>/_admin/login.php">Login</a></li>
				<?php if ($SYS_adminlvl > 0) { ?>
				<li><a href="<?= $SYS_folder ?>/_admin/login.php?do=logout">Sign out</a></li>
				<?php } ?>

			<?php } else if ($SYS_script == "users") { ?>

				<li<?php isActiveOn("users") ?>><a href="<?= $SYS_folder ?>/_admin/users.php">Users</a></li>

			<?php } else if ($SYS_script == "campaign" || $SYS_script == "image") { ?>

				<?php if ($SYS_adminlvl > 0) { ?>
					<li<?php isActiveOn("campaign") ?>><a href="<?= $SYS_folder ?>/_admin/campaign.php">Campaigns</a></li>
					<li<?php isActiveOn("image") ?>><a href="<?= $SYS_folder ?>/_admin/image.php">Image</a></li>
				<?php } ?>

			<?php } ?>
		</ul>
	</div>

	<div id="container">