<?php
	
	// Add RSS links to <head> section
	//automatic_feed_links();
	
	// Load jQuery
	if ( !is_admin() ) {
	   wp_deregister_script('jquery');
	   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), false);
	   wp_enqueue_script('jquery');
	}
	
	// Clean up the <head>
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');
    
	// Declare sidebar widget zone
	/*
    if (function_exists('register_sidebar')) {
    	register_sidebar(array(
    		'name' => 'Sidebar Widgets',
    		'id'   => 'sidebar-widgets',
    		'description'   => 'These are widgets for the sidebar.',
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2>',
    		'after_title'   => '</h2>'
    	));
    }
	*/
	
	add_filter( 'sidebars_widgets', 'disable_all_widgets' );
	function disable_all_widgets( $sidebars_widgets ) {
		if ( is_home() )
			$sidebars_widgets = array( false );
		return $sidebars_widgets;
	}
	
	// Register all the menus to be editable from admin > appearance > menus. 1. whatever 2. description/label shown in admin.
	if (function_exists('register_nav_menus')) {
		register_nav_menus(array(
			'main_nav' => 'Main Navigation Menu'
		));
	}
	
	// Exclude certain pages and posts from the search result (as in returning 0 found instead of just skipping echo).
	function fb_search_filter($query) {
		if ( !$query->is_admin && $query->is_search) {
			$query->set('post__not_in', array(4, 7, 41) ); // id of page or post
		}
		return $query;
	}
	add_filter( 'pre_get_posts', 'fb_search_filter' );
?>