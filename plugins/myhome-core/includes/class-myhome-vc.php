<?php
/*
 * My_Home_VC class
 *
 * Init Visual Composer elements.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_VC' ) ) :

class My_Home_VC {

	public function __construct() {
		add_action( 'vc_before_init', array( $this, 'init' ) );
		add_action( 'vc_load_default_templates', array( $this, 'custom_templates' ) );
	}

	/*
	 * init
	 *
	 * Initiate VC elements
	 */
	public function init() {
		$shortcodes = My_Home_Core()->shortcodes->get();
		foreach ( $shortcodes as $shortcode ) {
            vc_lean_map( $shortcode['name'], array( $shortcode['class'], 'settings' ) );
		}

        vc_remove_element( 'mega_main_menu' );
        vc_remove_element( 'deprecated' );
	}

	/*
	 * custom_templates
	 *
	 * Define custom visual composer templates
	 */
	public function custom_templates( $data ) {
		$data = array(); // remove existing templates

        return $data;
	}

}

endif;
