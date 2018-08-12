<?php

/*
 * My_Home_ACF class
 *
 * This class initiate general purpose custom fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_ACF' ) ) :

class My_Home_ACF {

    /*
     * register_fields
     *
     * Initiate registering fields
     */
	public function register_fields() {
		$this->add_page_fields();
	}

	/*
	 * add_page_fields
	 *
	 * Setup page fields
	 */
	private function add_page_fields() {
	    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	        return;
        }

		acf_add_local_field_group( array(
			'key'       => 'myhome_page',
			'title'     => esc_html__( 'Page settings', 'myhome' ),
			'position'  => 'side',
			'location'  => array(
				array(
					array(
						'param'     => 'post_type',
						'operator'  => '==',
						'value'     => 'page'
					)
				)
			),
			'fields' => array(
				// Sidebar position
				array(
					'key'           => 'myhome_page_header',
					'label'         => esc_html__( 'Menu', 'myhome' ),
					'name'          => 'page_header',
					'type'          => 'select',
					'default_value' => 'right',
					'choices'       => array(
						'default' => esc_html__( 'Default', 'myhome' ),
                        'mh-header--transparent' => esc_html__( 'Transparent', 'myhome' ),
                        'mh-header--transparent mh-header--transparent-dark' => esc_html__(
                            'Transparent - dark gradient', 'myhome'
                        )
                    )
				)
			)
		) );
	}

}

endif;
