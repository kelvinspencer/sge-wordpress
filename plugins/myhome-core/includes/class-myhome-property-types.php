<?php
/*
 * My_Home_Property_Types class
 *
 * This class provide additional custom fields for property type custom post type.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Property_Types' ) ) :

class My_Home_Property_Types {

    public function __construct() {
        add_action( 'acf/init', array( $this, 'set_fields' ) );
    }

    /*
     * set_fields
     *
     * Not every filter should be displayed for every property type (example bathroom for property type 'land').
     * Thanks to these custom fields its possible to hide some fields in right situations.
     */
    public function set_fields() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        $property_type_slug = My_Home_Core()->attributes->get_property_type_slug();
        $attributes = My_Home_Core()->attributes->get_attributes();
        $fields = array();

        foreach ( $attributes as $attr ) {
            if ( $attr->attribute_slug == $property_type_slug ) {
                continue;
            }
            array_push( $fields, array(
                'key'            => 'myhome_property_type_' . $attr->attribute_slug,
                'label'          => $attr->attribute_name,
                'name'           => 'property_type_' . $attr->attribute_slug,
                'type'           => 'true_false',
                'default_value'  => 1
            ) );
        }

        acf_add_local_field_group( array(
           'key'       => 'myhome_property_type',
           'title'     => esc_html__( 'Filters', 'myhome-core' ),
           'location'  => array(
               array(
                   array(
                       'param'     => 'taxonomy',
                       'operator'  => '==',
                       'value'     => $property_type_slug,
                   )
               )
           ),
           'fields' => $fields
       ) );
    }

}

endif;