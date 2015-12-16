<?php 
/* IP-LOC8
/*
/* INIT.PHP
/* Functions that run everytime - initialising the plugin. */

// INIT function
function bgloc_initialize_stuff() {
	// this is the global variable where all location info will be loaded - for other plugins to use
	global $visitorGeolocation; 
	// check if user first visit - Cookies!
	if(isset($_COOKIE['iploc8'])){
		$visitorGeolocation = unserialize(base64_decode($_COOKIE['iploc8']));
	// no cookie = first visit or cookies disabled...
	} else {
		// check if SESSION exists - if not, user just arrived
		if(!session_id()) { 
	        session_start();
			// first visit -> get location
			$iploc = new IpLocLocator;
			// get location and set cookie
			$visitorGeolocation = $iploc->setData($_SERVER['REMOTE_ADDR']);
			// set Session
			$_SESSION['iploc8'] = serialize($visitorGeolocation);
			// if not in WP admin -> do redirects/languages
			if (!is_admin()) {
				//add_filter( 'qtranslate_detect_language', 'set_qtranslate_language', 10, 1);
				$iploc->redirect();
			}
		// user already been here, cookies probably disabled, load from SESSION
		} else {
			$visitorGeolocation = unserialize($_SESSION['iploc8']);
		}
	}
		
}
// INIT action hook
add_action('plugins_loaded', 'bgloc_initialize_stuff',1);

add_action('init','startsession');
function startsession() {
	if(!session_id()) { 
		global $visitorGeolocation;
	    session_start();
    	$_SESSION['iploc8'] = serialize($visitorGeolocation);
    }
}