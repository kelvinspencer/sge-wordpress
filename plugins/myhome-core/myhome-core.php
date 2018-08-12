<?php
/*
Plugin Name: MyHome Core
Description: MyHome Core
Version: 1.0.7
Plugin URI: http://tangibledesign.net
 */
require_once 'includes/class-myhome-core.php';

function My_Home_Core() {
	return My_Home_Core::get_instance();
}

$my_home = My_Home_Core();

add_action( 'plugins_loaded', array( $my_home, 'init' ) );

// Plugin activation
register_activation_hook( __FILE__, array( $my_home, 'activation' ) );
