<?php 
/*
Plugin Name: IP Loc8
Plugin URI: http://talkingaboutthis.eu
Description: This plugins provides city and country info by user IP address
Author: Boyan Raichev 
Version: 1.0
Author URI: http://talkingaboutthis.eu/
*/

// GEOLOCATION
// use ipinfodb.com

define('BGLOC_DIR', plugin_dir_path( __FILE__ ));

require_once(BGLOC_DIR . 'includes/init.php');


// Class Autoloader
function bgloc_autoloader( $class_name ) {

    // autoload only classes starting with Kosher
    if ( 0 !== strpos( $class_name, 'IpLoc' ) ) {
        return;
    }

    $file_name = str_replace(
        array( 'iploc' ), // Remove prefix | Underscores 
        array( '' ),
        strtolower( $class_name ) // lowercase
    );
	
    // Compile our path from the current location
    $file = BGLOC_DIR.'includes/class.'. $file_name .'.php';

    // If a file is found
    if ( file_exists( $file ) ) { 
    	require_once( $file );
    }
}
spl_autoload_register( 'bgloc_autoloader' );

if (is_admin()) { $settings_page = new IpLocAdmin(); } 

// Activation
function bgloc_plugin_activate() {
    
}
register_activation_hook( __FILE__, 'bgloc_plugin_activate' );

// deactivation function
function bgloc_plugin_deactivate() {
	
}
register_deactivation_hook( __FILE__, 'bgloc_plugin_deactivate' );

// uninstallation function
function bgloc_plugin_uninstall() {
	// delete option from wp_options
	delete_option('iploc8');
	delete_option('iploc8_redir');	
}
register_uninstall_hook(__FILE__, 'bgloc_plugin_uninstall' );

?>