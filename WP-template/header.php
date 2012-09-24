<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>
	<title><?php
			if (function_exists('is_tag') && is_tag()) {
				single_tag_title("Tag Archive for &quot;"); echo '&quot; - '; }
			elseif (is_archive()) {
				wp_title(''); echo ' Archive - '; }
			elseif (is_search()) {
				echo 'Search for &quot;'.wp_specialchars($s).'&quot; - '; }
			elseif (!(is_404()) && (is_single()) || (is_page())) {
				wp_title(''); echo ' - '; }
			elseif (is_404()) {
				echo 'Not Found - '; }
			if (is_home()) {
				bloginfo('name'); echo ' - '; bloginfo('description'); }
			else {
				 bloginfo('name'); }
			if ($paged>1) {
				echo ' - page '. $paged; }
			?></title>
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
	<?php wp_head(); ?>
</head>

<body>
	
	<div id="container">

		<div id="header">
			<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
			<div class="description"><?php bloginfo('description'); ?></div>
		</div>
		
		<div id="content-wrap">
			<?php wp_nav_menu(array(
				'menu' => 'Huvudmeny',
				'menu_id' => 'nav',
				'container' => false,
				'depth' => 1
			)); ?>
			
			<div id="content">