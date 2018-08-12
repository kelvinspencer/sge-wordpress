<?php
/*
 * My_Home_Post_Types class
 *
 * This class register post types which are not enough important to have own class.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Post_Types' ) ) :

class My_Home_Post_Types {

    public function __construct() {
        add_action( 'init', array( $this, 'register' ) );
        add_action( 'acf/init', array( $this, 'set_fields' ) );
    }

    /*
     * set_fields
     *
     * Set custom fields for post types registered by this class
     */
    public function set_fields() {
        acf_add_local_field_group(
            array(
                'key'        => 'myhome_testimonial',
                'title'      => '<span class="dashicons dashicons-admin-home"></span> '
                    . esc_html__( 'Testimonial author', 'myhome-core' ),
                'fields'     => array(
                    array(
                        'key'           => 'myhome_testimonial_occupation',
                        'label'         => esc_html__( 'Author occupation', 'myhome-core' ),
                        'name'          => 'testimonial_occupation',
                        'type'          => 'text',
                        'default_value' => '',
                    )
                ),
                'location'   => array(
                    array(
                        array(
                            'param'    => 'post_type',
                            'operator' => '==',
                            'value'    => 'testimonial',
                        ),
                    ),
                ),
            )
        );

        acf_add_local_field_group(
            array(
                'key'        => 'myhome_client',
                'title'      => esc_html__( 'Client', 'myhome-core' ),
                'fields'     => array(
                    array(
                        'key'           => 'myhome_client_link',
                        'label'         => esc_html__( 'Client link', 'myhome-core' ),
                        'name'          => 'client_link',
                        'type'          => 'text',
                        'default_value' => '',
                    )
                ),
                'location'   => array(
                    array(
                        array(
                            'param'    => 'post_type',
                            'operator' => '==',
                            'value'    => 'client',
                        ),
                    ),
                ),
            )
        );
    }

    /*
     * register
     *
     * Register custom post types
     */
    public function register() {
        register_post_type( 'client', array(
            'labels' => array(
                'name'			     => esc_html__( 'Clients', 'myhome-core' ),
                'singular_name'	     => esc_html__( 'Client', 'myhome-core' ),
                'menu_name'          => esc_html__( 'Clients', 'myhome-core' ),
                'name_admin_bar'     => esc_html__( 'Add new Client', 'myhome-core' ),
                'add_new'            => esc_html__( 'Add New Client', 'myhome-core' ),
                'add_new_item'       => esc_html__( 'Add New Client', 'myhome-core' ),
                'new_item'           => esc_html__( 'New Client', 'myhome-core' ),
                'edit_item'          => esc_html__( 'Edit Client', 'myhome-core' ),
                'view_item'          => esc_html__( 'View Client', 'myhome-core' ),
                'all_items'          => esc_html__( 'Clients', 'myhome-core' ),
                'search_items'       => esc_html__( 'Search Clients', 'myhome-core' ),
                'not_found'          => esc_html__( 'No Clients found.', 'myhome-core' ),
                'not_found_in_trash' => esc_html__( 'No Clients found in Trash.', 'myhome-core' )
            ),
            'show_in_rest'      => false,
            'query_var'         => true,
            'public'		    => true,
            'has_archive'	    => false,
            'show_in_nav_menus' => false,
            'menu_position'     => 24,
            'menu_icon'         => 'dashicons-admin-home',
            'supports'		    => array(
                'title',
                'thumbnail',
            )
        ) );

        register_post_type( 'testimonial', array(
            'labels' => array(
                'name'			     => esc_html__( 'Testimonials', 'myhome-core' ),
                'singular_name'	     => esc_html__( 'Testimonial', 'myhome-core' ),
                'menu_name'          => esc_html__( 'Testimonials', 'myhome-core' ),
                'name_admin_bar'     => esc_html__( 'Add new Testimonial', 'myhome-core' ),
                'add_new'            => esc_html__( 'Add New Testimonial', 'myhome-core' ),
                'add_new_item'       => esc_html__( 'Add New Testimonial', 'myhome-core' ),
                'new_item'           => esc_html__( 'New Testimonial', 'myhome-core' ),
                'edit_item'          => esc_html__( 'Edit Testimonial', 'myhome-core' ),
                'view_item'          => esc_html__( 'View Testimonial', 'myhome-core' ),
                'all_items'          => esc_html__( 'Testimonials', 'myhome-core' ),
                'search_items'       => esc_html__( 'Search Testimonials', 'myhome-core' ),
                'not_found'          => esc_html__( 'No Testimonials found.', 'myhome-core' ),
                'not_found_in_trash' => esc_html__( 'No Testimonials found in Trash.', 'myhome-core' )
            ),
            'show_in_rest'      => false,
            'query_var'         => true,
            'public'		    => true,
            'has_archive'	    => false,
            'show_in_nav_menus' => false,
            'menu_position'     => 24,
            'menu_icon'         => 'dashicons-admin-home',
            'supports'		    => array(
                'title',
                'editor',
                'thumbnail',
            )
        ) );
    }

}

endif;