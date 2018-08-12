<?php
/**
 * CSS related hooks.
 *
 * This file contains hook functions which are related to CSS.
 *
 * @package Onefold
 */

if ( ! function_exists( 'onefold_trigger_custom_css_action' ) ) :

	/**
	 * Do action theme custom CSS.
	 *
	 * @since 1.0.0
	 */
	function onefold_trigger_custom_css_action() {

		/**
		 * Hook - onefold_action_theme_custom_css.
		 */
		do_action( 'onefold_action_theme_custom_css' );

	}

endif;

add_action( 'wp_head', 'onefold_trigger_custom_css_action', 99 );
