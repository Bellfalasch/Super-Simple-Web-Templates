<?php require('inc/globals.php'); ?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="no"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="no"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="no"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="no"> <!--<![endif]-->
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo $PAGE_title ?> - SITENAME</title>
	<?php if (isset($PAGE_desc)) { ?><meta name="description" content="<?= $PAGE_desc ?>" /><?php } ?>
	<?php if (DEV_ENV) { ?><meta name="robots" content="noindex, nofollow" /><?php } ?>
	<link href="<?= $SYS_folder ?>/_styles.css?v=<?php if (DEV_ENV) echo rand(); ?>" rel="stylesheet" type="text/css" />
	<link rel="stylesheet/less" type="text/css" href="<?= $SYS_folder ?>/css/less/styles.less" />
	<script src="<?= $SYS_folder ?>/js/less-1.3.0.min.js" type="text/javascript"></script>
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script>
	<script src="<?= $SYS_folder ?>/js/global.js?v=<?php if (DEV_ENV) echo rand(); ?>" type="text/javascript"></script>
	
	<?php
		/*	favicons
			If you have a physical file than uncomment the row just under here, otherwise the little code-snippet you see
			will generate an empty favicon for the browser so that we can escape any 404-errors (good practise + faster site)
			
			If you don't want to have a physical file on the server you could create it anywhere and then use php encode-function
			to get the base64 code for that image and use that in this template instead:
			
			echo base64_encode(file_get_contents('favicon.ico'));

			Courtesy of: http://davidwalsh.name/blank-favicon
		*/
	?>
	<!--<link href="<?= $SYS_folder ?>/favicon.ico" rel="shortcut icon" />-->
	<link href="data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=" rel="icon" type="image/x-icon" />
	
	<?php
		/*
			Template-specific header code
			Since I use a system with one header and one footer for every template file (each php-file in this folder is basically a template file)
			you might run in to cases where you need some code in the header and or footer to only be visible in certain templates. Then you can
			easily use the already prepared SYS_script which would contain "index" for "/index.php" and the in_array-function. Now you have the
			ability to edit even the master template based on which page people surf on.

			Example use:
		*/
	?>
	<?php if (in_array( $SYS_script, array('shop_dibs_accept','shop_done','shop_fail','dibs','validate','popupanddown','handlekurv') ) ) { ?>
		<!-- Yeah, this code only displayed on certain templates =) -->
	<?php } ?>
</head>
<body>

	<header>
		<h1>Logo</h1>
		<nav>
			Nav
		</nav>
	</header>

	<article>
<!-- /header -->