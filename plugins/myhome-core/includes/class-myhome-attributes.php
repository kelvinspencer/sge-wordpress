<?php

/*
 * My_Home_Attributes class
 *
 * Attributes are additional fields related to estate. Plugin provide core attributes like property type or price.
 * It's possible to create additional attributes. Attribute can be represented as custom field (numeric) or taxonomy
 * (text). It's possible to filter estates by all existing attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

class My_Home_Attributes {

    private $table_name = 'myhome_attributes';

    public function __construct() {
        // register taxonomies
        add_action( 'init', array( $this, 'register_taxonomies' ), 0 );
        // create attributes admin menu
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        // actions used by attributes form
        add_action( 'admin_post_add_attribute', array( $this, 'create' ) );
        add_action( 'admin_post_update_attribute', array( $this, 'update' ) );
        add_action( 'admin_post_update_attribute_name', array( $this, 'update_name' ) );
        add_action( 'admin_post_update_attribute_slug', array( $this, 'update_slug' ) );
        add_action( 'admin_post_delete_attribute', array( $this, 'delete' ) );
        // set custom fields
        add_action( 'acf/init', array( $this, 'set_fields' ) );
        // clear cache
        add_action( 'acf/save_post', array( $this, 'clear_cache' ) );
        // set meta boxes
        add_action( 'add_meta_boxes', array( $this, 'set_meta_box' ) );
    }

    /*
     * clear_cache
     */
    public function clear_cache() {
        if ( ! empty( $_POST['acf'] ) && empty( $_POST['ID'] ) ) {
            My_Home_Cache::clear_cache();
        }
    }

    /*
     * get_form_controls
     *
     * Get attributes as search form controls. Define possible values and get My_Home_Attribute objects.
     */
    public function get_form_controls( $atts ) {
        $attributes = array();
        foreach ( My_Home_Attribute::get_attributes() as $attribute ) {
            if ( $atts[$attribute->get_slug() . '_show'] == 'true' ) {
                $control = $attribute->get_data();
                // set defaults
                foreach ( array( $atts, $_GET ) as $data ) {
                    foreach ( array( '', '_from', '_to' ) as $k ) {
                        $key = $attribute->get_slug() . $k;
                        if ( isset( $data[$key] ) && ! empty( $data[$key] ) && $data[$key] != 'any' ) {
                            $control['value' . $k] = $data[$key];
                        }
                    }
                }
                array_push( $attributes, $control );
            }
        }

        return $attributes;
    }

    /*
     * get_attributes
     *
     * Get attributes from database.
     */
    public function get_attributes() {
        $cache_key = 'myhome_attributes_base';
        if ( false !== ( $attributes = get_transient( $cache_key ) ) ) {
            return $attributes;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;
        // already here attributes are ordered by search form order set in options
        $attributes =  $wpdb->get_results( "SELECT * FROM $table_name ORDER BY form_order, ID " );
        $options = get_option( 'myhome_redux' );
        // check if offer type isn't disabled
        if ( isset( $options['mh-offer_type'] ) && ! $options['mh-offer_type'] ) {
            foreach ( $attributes as $key => $attr ) {
                if ( $attr->base_slug == 'offer_type' ) {
                    array_splice( $attributes, $key, 1 );
                }
            }
        }

        set_transient( $cache_key, $attributes, 4 * HOUR_IN_SECONDS );
        return $attributes;
    }

    /*
     * set_meta_box
     *
     * Meta box for attributes
     */
    public function set_meta_box() {
        $fields = $this->get_options();
        foreach ( $this->get_attributes() as $attr ) {
            // for tag is used core wordpress meta box
            if ( isset( $fields['options_' . $attr->attribute_slug . '_tags'] ) ) {
                $tags = $fields['options_' . $attr->attribute_slug . '_tags'];
            } else {
                $tags = false;
            }
            if ( ! empty( $tags ) && $tags ) {
                continue;
            }
            // remove default meta box
            remove_meta_box( 'tagsdiv-' . $attr->attribute_slug, 'estate', 'side' );
        }

        add_meta_box(
            'myhome_attributes_box',
            esc_html__( 'Property Attributes', 'myhome-core' ),
            array( $this, 'meta_box' ),
            'estate',
            'normal',
            'high'
        );
    }

    /*
     * meta_box
     *
     * Callback for add_meta_box
     */
    public function meta_box( $object ) {
        ob_start();
        ?>
        <div id="mh-admin-attributes">
            <?php foreach ( $this->get_attributes() as $attr ) :
                if ( $attr->attribute_type != 'taxonomy' ) {
                    continue;
                }
                if ( function_exists( 'get_field' ) ) {
                    $tags = get_field( 'myhome_' . $attr->attribute_slug . '_tags', 'option' );
                } else {
                    $tags = false;
                }
                if ( ! empty( $tags ) && $tags ) {
                    continue;
                }
                ?>
                <div class="acf-field acf-1of3">
                    <div class="acf-label">
                    <label><?php echo esc_html( $attr->attribute_name ); ?></label>
                    </div>
                    <div class="acf-input">
                    <?php
                        $this->meta_box_select( $object->ID, $attr->attribute_slug );
                    ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        echo ob_get_clean();
    }

    /*
     * meta_box_select
     *
     * Select field for attribute meta box
     */
    public function meta_box_select( $post_id, $taxonomy ) {
        $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0 ) );
        if ( ! is_array( $terms ) ) {
            $terms = array();
        }
        $terms_of_post = get_the_terms( $post_id, $taxonomy );
        if ( ! is_array( $terms_of_post ) ) {
            $terms_of_post = array();
        }
        $ids = array();
        if ( $terms_of_post ) {
            foreach ( $terms_of_post as $term ) {
                $ids[] = $term->term_id;
            }
        }
        ?>

        <div id="taxonomy-post_tag" class="categorydiv">
            <input type="hidden" name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]" value="0" />
            <select name="tax_input[<?php echo esc_attr( $taxonomy ); ?>][]">
                <option value=""></option>
                <?php foreach( $terms as $term ) { ?>
                <option value="<?php echo esc_attr( $term->slug ); ?>"
                        <?php if ( in_array( $term->term_id, $ids ) ) { ?>selected="selected"<?php } ?>>
                    <?php echo esc_html( $term->name ); ?>
                </option>
                <?php } ?>
            </select>
        </div>
    <?php
    }

    /*
     * set_fields
     *
     * Set custom fields
     */
    public function set_fields() {
        $taxonomies = array();

        foreach ( $this->get_attributes() as $attr ) {
            if ( $attr->attribute_type == 'field' ) {
                $type = esc_html__( 'Number field', 'myhome-core' );
            } elseif ( $attr->attribute_type == 'taxonomy' ) {
                $type = esc_html__( 'Text field', 'myhome-core' );
            } elseif ( $attr->base_slug == 'keyword' ) {
	            $this->add_keyword_settings();
	            $type = esc_html__( 'Keyword', 'myhome-core ' );
            } elseif ( $attr->base_slug == 'estate_id' ) {
                $this->add_estate_id_settings();
	            $type = esc_html__( 'Property ID', 'myhome-core ' );
            } else {
                $type = '';
            }

            acf_add_options_sub_page( array(
                'page_title' 	=> sprintf(
                    wp_kses( __(
                        '%1$s - view on the search form<div class="acf-settings-wrap__subtitle">%2$s</div>',
                        'myhome-core'
                    ), array( 'div' => array( 'class' => array() ) ) ),
                    $attr->attribute_name,
                    $type
                ),
                'menu_title'	=> $attr->attribute_name,
                'menu_slug'     => 'acf-options-' . $attr->attribute_slug,
                'parent_slug'	=> 'myhome_attributes',
                'autoload'      => true
            ) );
            if ( $attr->base_slug == 'keyword' || $attr->base_slug == 'estate_id' ) {
                continue;
            }

            $fields = array(
                array(
                    'key'       => 'myhome_' . $attr->attribute_slug . '_general',
                    'label'     => esc_html__( 'Basic', 'myhome-core' ),
                    'type'      => 'tab',
                    'placement' => 'top',
                )
            );

            if ( $attr->attribute_type == 'field' ) {
                $fields = array_merge( $fields, array(
                    // Search form control
                    array(
                        'key'       => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                        'label'     => esc_html__( 'Search form - display as:', 'myhome-core' ),
                        'name'      => $attr->attribute_slug . '_search_form_control',
                        'type'      => 'select',
                        'choices'   => array(
                            'text'          => esc_html__( 'Input field', 'myhome-core' ),
                            'text_range'    => esc_html__( 'Input field based on a range ( from - to )', 'myhome-core' ),
                            'select'        => esc_html__( 'Drop-down list ', 'myhome-core' ),
                            'select_range'  => esc_html__( 'Drop-down list based on a range ( from - to )', 'myhome-core' ),
                        )
                    )
                ) );
            } elseif( $attr->attribute_type == 'taxonomy' ) {
                array_push( $taxonomies, $attr->attribute_slug );

                $choices = array(
                    'select'        => esc_html__( 'Dropdown list', 'myhome-core' ),
                    'text'          => esc_html__( 'Input Text', 'myhome-core' ),
                    'checkbox'      => esc_html__( 'Checkbox', 'myhome-core' ),
                    'radio_button'  => esc_html__( 'Mixed - Radio / Select ( search form position left/right - radio button | search form position top/bottom: select)', 'myhome-core' )
                );

                if ( $attr->base_slug == 'property_type' ) {
                    unset( $choices['checkbox'] );
                }

                $fields = array_merge( $fields, array(
                    // Search form control
                    array(
                        'key'       => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                        'label'     => esc_html__( 'Search form - display as:', 'myhome-core' ),
                        'name'      => $attr->attribute_slug . '_search_form_control',
                        'type'      => 'select',
                        'choices'   => $choices
                    )
                ) );
            }

            if ( $attr->attribute_type == 'taxonomy' ) {
                $fields = array_merge( $fields, array(
                    // Default values
                    array(
                        'key'           => 'myhome_' . $attr->attribute_slug . '_default_values',
                        'label'         => esc_html__( 'Default values', 'myhome-core' ),
                        'name'          => $attr->attribute_slug . '_default_values',
                        'type'          => 'select',
                        'choices'       => array(
                            'all'           => esc_html__( 'All existing values', 'myhome-core' ),
                            'most_popular'  => esc_html__( 'Most popular', 'myhome-core' ),
                            'static'        => esc_html__( 'Static values', 'myhome-core' )
                        ),
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                    'operator'  => '!=',
                                    'value'     => 'text'
                                ),
                                array(
                                    'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                    'operator'  => '!=',
                                    'value'     => 'text_range'
                                )
                            )
                        )
                    ),
                    // Most popular limit
                    array(
                        'key'               => 'myhome_' . $attr->attribute_slug . '_most_popular_limit',
                        'label'             => esc_html__( 'Most popular values limit (number)', 'myhome-core' ),
                        'name'              => $attr->attribute_slug . '_most_popular_limit',
                        'type'              => 'text',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'     => 'myhome_' . $attr->attribute_slug . '_default_values',
                                    'operator'  => '==',
                                    'value'     => 'most_popular'
                                )
                            )
                        )
                    ),
                    // Static values
                    array(
                        'key'           => 'myhome_' . $attr->attribute_slug . '_static_values',
                        'label'         => esc_html__( 'Static values', 'myhome-core' ),
                        'name'          => $attr->attribute_slug . '_static_values',
                        'type'          => 'repeater',
                        'button_label'  => esc_html__( 'Add', 'myhome-core' ),
                        'sub_fields'    => array(
                            // Name
                            array(
                                'key'   => 'myhome_' . $attr->attribute_slug . '_static_values_name',
                                'label' => esc_html__( 'Name (visible)', 'myhome-core' ),
                                'name'  => 'name',
                                'type'  => 'text',
                            ),
                            // Value
                            array(
                                'key'   => 'myhome_' . $attr->attribute_slug . '_static_values_value',
                                'label' => esc_html__( 'Value', 'myhome-core' ),
                                'name'  => 'value',
                                'type'  => 'text',
                            ),
                        ),
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'     => 'myhome_' . $attr->attribute_slug . '_default_values',
                                    'operator'  => '==',
                                    'value'     => 'static'
                                ),
                                array(
                                    'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                    'operator'  => '!=',
                                    'value'     => 'text'
                                ),
                                array(
                                    'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                    'operator'  => '!=',
                                    'value'     => 'text_range'
                                )
                            ),
                        )
                    )
                ) );
            } elseif ( $attr->attribute_type == 'field' ) {
                // Static values
                array_push( $fields, array(
                    'key'           => 'myhome_' . $attr->attribute_slug . '_static_values',
                    'label'         => esc_html__( 'Static values', 'myhome-core' ),
                    'name'          => $attr->attribute_slug . '_static_values',
                    'type'          => 'repeater',
                    'button_label'  => esc_html__( 'Add', 'myhome-core' ),
                    'sub_fields'    => array(
                        // Name
                        array(
                            'key'   => 'myhome_' . $attr->attribute_slug . '_static_values_name',
                            'label' => esc_html__( 'Name (visible)', 'myhome-core' ),
                            'name'  => 'name',
                            'type'  => 'text',
                        ),
                        // Value
                        array(
                            'key'   => 'myhome_' . $attr->attribute_slug . '_static_values_value',
                            'label' => esc_html__( 'Value', 'myhome-core' ),
                            'name'  => 'value',
                            'type'  => 'text',
                        ),
                    ),
                    'conditional_logic' => array(
                        array(
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '==',
                                'value'     => 'select'
                            )
                        ),
                        array(
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '==',
                                'value'     => 'select_range'
                            )
                        )
                    )
                ) );
            }

            if ( $attr->base_slug != 'price' ) {
                // show on card
                $fields = array_merge( $fields, array(
                    array(
                        'key'   => 'myhome_' . $attr->attribute_slug . '_show_card',
                        'label' => esc_html__( 'Display on property card', 'myhome-core' ),
                        'name'  => $attr->attribute_slug . '_show_card',
                        'type'  => 'true_false'
                    ),
                    // show property
                    array(
                        'key'           => 'myhome_' . $attr->attribute_slug . '_show_property',
                        'label'         => esc_html__( 'Display on single property page', 'myhome-core' ),
                        'name'          => $attr->attribute_slug . '_show_property',
                        'type'          => 'true_false',
                        'default_value' => true
                    )
                ) );
            }

            $fields = array_merge( $fields, array(
                // advanced tab
                array(
                    'key'       => 'myhome_' . $attr->attribute_slug . '_advanced',
                    'label'     => esc_html__( 'Advanced', 'myhome-core' ),
                    'type'      => 'tab',
                    'placement' => 'left',
                )
            ) );

            if ( $attr->attribute_type == 'taxonomy' ) {
                // Use like wordpress tags (possible only when taxonomy)
                if ( $attr->base_slug != 'property_type' ) {
                    array_push( $fields, array(
                        'key'   => 'myhome_' . $attr->attribute_slug . '_tags',
                        'label' => esc_html__( 'Use as tags', 'myhome-core' ),
                        'name'  => $attr->attribute_slug . '_tags',
                        'type'  => 'true_false',
                    ) );
                    // new box
                    array_push( $fields, array(
                        'key'               => 'myhome_' . $attr->attribute_slug . '_new_box',
                        'label'             => esc_html__( 'Single property page: display in a separate section', 'myhome-core' ),
                        'instructions'      => '',
                        'name'              => $attr->attribute_slug . '_new_box',
                        'default_value'     => true,
                        'type'              => 'true_false',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field'     => 'myhome_' . $attr->attribute_slug . '_tags',
                                    'operator'  => '==',
                                    'value'     => '1'
                                )
                            )
                        )
                    ) );
                }
                array_push( $fields, array(
                    'key'           => 'myhome_' . $attr->attribute_slug . '_has_archive',
                    'label'         => esc_html__( 'Single property page: show as link', 'myhome-core' ),
                    'name'          => $attr->attribute_slug . '_has_archive',
                    'type'          => 'true_false',
                    'default_value' => true
                ) );
                // if attribute is like tags, field (search form) can be full width
                array_push( $fields, array(
                    'key'               => 'myhome_' . $attr->attribute_slug . '_checkbox_full_width',
                    'label'             => esc_html__( 'Search Form - display full width', 'myhome-core' ),
                    'instructions'      => '',
                    'name'              => $attr->attribute_slug . '_checkbox_full_width',
                    'type'              => 'true_false',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '==',
                                'value'     => 'checkbox'
                            )
                        )
                    )
                ) );
            } elseif ( $attr->attribute_type == 'field' ) {
                if ( $attr->base_slug != 'price' ) {
                    array_push(
                        $fields, array(
                        'key'          => 'myhome_' . $attr->attribute_slug . '_display_after',
                        'label'        => esc_html__( 'Unit of measure', 'myhome-core' ),
                        'name'         => $attr->attribute_slug . '_display_after',
                        'instructions' => esc_html__( 'It will be displayed after name eg. you can use that to add (sq feet) next to lot size, but it will be not used in link so it is much more useful.', 'myhome-core' ),
                        'type'         => 'text'
                    )
                    );
                }
            }

            $fields = array_merge( $fields, array(
                // Placeholder
                array(
                    'key'   => 'myhome_' . $attr->attribute_slug . '_placeholder',
                    'label' => esc_html__( 'Placeholder', 'myhome-core' ),
                    'name'  => $attr->attribute_slug . '_placeholder',
                    'type'  => 'text',
                    'instructions' => "Placeholder's name is by default a name of a field, but it can be changed below",
                    'conditional_logic' => array(
                        array(
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '!=',
                                'value'     => 'select_range'
                            ),
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '!=',
                                'value'     => 'text_range'
                            )
                        )
                    )
                ),
                // Placeholder from
                array(
                    'key'   => 'myhome_' . $attr->attribute_slug . '_placeholder_from',
                    'label' => esc_html__( 'Placeholder (default: from)', 'myhome-core' ),
                    'name'  => $attr->attribute_slug . '_placeholder_from',
                    'type'  => 'text',
                    'wrapper' => array (
                        'width' => '50%',
                    ),
                    'conditional_logic' => array(
                        array(
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '==',
                                'value'     => 'text_range'
                            )
                        ),
                        array(
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '==',
                                'value'     => 'select_range'
                            )
                        )
                    )
                ),
                // Placeholder to
                array(
                    'key'   => 'myhome_' . $attr->attribute_slug . '_placeholder_to',
                    'label' => esc_html__( 'Placeholder (default: to)', 'myhome-core' ),
                    'name'  => $attr->attribute_slug . '_placeholder_to',
                    'type'  => 'text',
                    'wrapper' => array (
                        'width' => '50%',
                    ),
                    'conditional_logic' => array(
                        array(
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '==',
                                'value'     => 'text_range'
                            )
                        ),
                        array(
                            array(
                                'field'     => 'myhome_' . $attr->attribute_slug . '_search_form_control',
                                'operator'  => '==',
                                'value'     => 'select_range'
                            )
                        )
                    )
                )
            ) );

            acf_add_local_field_group( array(
                'key'       => 'myhome_' . $attr->attribute_slug . '_attribute',
                'title'     => sprintf( esc_html__( '%s Settings', 'myhome-core' ), $attr->attribute_name ),
                'location'  => array(
                    array(
                        array(
                            'param'     => 'options_page',
                            'operator'  => '==',
                            'value'     => 'acf-options-' . $attr->attribute_slug,
                       )
                    )
                ),
                'fields' => $fields
           ) );
        }

        $location = array();
        foreach ( $taxonomies as $taxonomy ) {
            array_push( $location, array(
                array(
                    'param'     => 'taxonomy',
                    'operator'  => '==',
                    'value'     => $taxonomy
                )
            ) );
        }

        acf_add_local_field_group( array(
            'key'       => 'myhome_area',
            'title'     => esc_html__( 'Settings', 'myhome-core' ),
            'location'  => $location,
            'fields'    => array(
                array(
                    'key'   => 'myhome_term_image',
                    'label' => esc_html__( 'Image', 'myhome-core' ),
                    'name'  => 'term_image',
                    'type'  => 'image'
                ),
                array(
                    'key'           => 'myhome_term_image_wide',
                    'label'         => esc_html__( 'Image wide', 'myhome-core' ),
                    'instructions'  => esc_html__( 'Recommended size 1920x500 px', 'myhome-core' ),
                    'name'          => 'term_image_wide',
                    'type'          => 'image'
                ),
            )
        ) );
    }

    public function add_estate_id_settings() {
	    acf_add_local_field_group( array(
		    'key'       => 'myhome_estate_id_attribute',
		    'title'     => esc_html__( 'Property ID Settings', 'myhome-core' ),
		    'location'  => array(
			    array(
				    array(
					    'param'     => 'options_page',
					    'operator'  => '==',
					    'value'     => 'acf-options-property_id',
				    )
			    )
		    ),
		    'fields' => array(
			    // Placeholder
			    array(
				    'key'           => 'myhome_estate_id_placeholder',
				    'label'         => esc_html__( 'Placeholder', 'myhome-core' ),
				    'name'          => 'estate_id_placeholder',
				    'type'          => 'text',
				    'instructions'  => esc_html__(
					    'Default placeholder is always a name of a field, but you can change it below',
					    'myhome-core'
				    )
			    )
		    )
	    ) );
    }

    public function add_keyword_settings() {
        acf_add_local_field_group( array(
            'key'       => 'myhome_keyword_attribute',
            'title'     => esc_html__( 'Keyword Settings', 'myhome-core' ),
            'location'  => array(
                array(
                    array(
                        'param'     => 'options_page',
                        'operator'  => '==',
                        'value'     => 'acf-options-keyword',
                    )
                )
            ),
            'fields' => array(
                // Placeholder
                array(
                    'key'           => 'myhome_keyword_placeholder',
                    'label'         => esc_html__( 'Placeholder', 'myhome-core' ),
                    'name'          => 'keyword_placeholder',
                    'type'          => 'text',
                    'instructions'  => esc_html__(
                        'Default placeholder is always a name of a field, but you can change it below',
                        'myhome-core'
                    )
                )
            )
        ) );
    }

    /*
     * register_taxonomies
     *
     * Register taxonomies for attributes
     */
    public function register_taxonomies() {
        foreach ( $this->get_attributes() as $attr ) {
            if ( $attr->attribute_type != 'taxonomy' ) {
                continue;
            }

            $labels = array(
                'name'              => $attr->attribute_name,
                'singular_name'     => $attr->attribute_name,
                'search_items'      => sprintf( esc_html__( 'Search %s', 'myhome-core' ), $attr->attribute_name ),
                'all_items'         => sprintf( esc_html__( 'All %s', 'myhome-core' ), $attr->attribute_name ),
                'parent_item'       => sprintf( esc_html__( 'Parent %s', 'myhome-core' ), $attr->attribute_name ),
                'parent_item_colon' => sprintf( esc_html__( 'Parent %s:', 'myhome-core' ), $attr->attribute_name ),
                'edit_item'         => sprintf( esc_html__( 'Edit %s', 'myhome-core' ), $attr->attribute_name ),
                'update_item'       => sprintf( esc_html__( 'Update %s', 'myhome-core' ), $attr->attribute_name ),
                'add_new_item'      => sprintf( esc_html__( 'Add New %s', 'myhome-core' ), $attr->attribute_name ),
                'new_item_name'     => sprintf( esc_html__( 'New %s Name', 'myhome-core' ), $attr->attribute_name ),
                'menu_name'         => $attr->attribute_name
            );

            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'query_vars'             => true,
                'publicly_queryable'    => true,
                'has_archive'           => true,
                'rewrite'               => array( 'slug' => $attr->attribute_slug ),
                'capabilities'          => array(
                    'manage_terms'  => 'manage_categories',
                    'edit_terms'    => 'manage_categories',
                    'delete_terms'  => 'manage_categories',
                    'assign_terms'  => 'edit_estates'
                )
            );

            register_taxonomy( $attr->attribute_slug, 'estate', $args );
        }
    }

    /*
     * add_menu
     *
     * Menu with attribute settings
     */
    public function add_menu() {
        //create new top-level menu
        add_menu_page(
            esc_html__( 'Property fields', 'myhome-core' ),
            esc_html__( 'Property fields', 'myhome-core' ),
            'administrator',
            'myhome_attributes',
            array( $this, 'admin_page' ),
            '',
            '22'
        );
    }

    /*
     * admin_page
     *
     * Manage attributes
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1 class="mh-admin-heading"><?php esc_html_e( 'Property fields', 'myhome-core' ); ?></h1>
            <p class="mh-margin-bottom-small">
                <?php esc_html__( 'Use "Up" and "Down" arrows to change order of the options', 'myhome-core' ); ?>
            </p>
                <div class="mh-up-and-down">
                <?php
                $attributes = $this->get_attributes();
                if ( count( $attributes ) ) :
                    $translations = json_encode( My_Home_Translations::get_attributes_form() );
                    $attributes = json_encode( $attributes );
                    ?>
                    <attributes-form id="myhome-attributes-form" :translations='<?php echo esc_attr( $translations ); ?>'
                                     site='<?php echo esc_url( admin_url() ); ?>'
                                     :attributes='<?php echo esc_attr( $attributes ); ?>'></attributes-form>
                <?php endif; ?>
            </div>

            <h2 class="mh-admin-subheading"><?php esc_html_e( 'Add new field', 'myhome-core' ); ?></h2>
            <div class="mh-admin-section">
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php') );  ?>">
                    <input type="hidden" name="action" value="add_attribute">

                    <label for="attribute-name" class="mh-admin-label">
                        <?php esc_html_e( 'Name', 'myhome-core' ); ?>
                    </label>

                    <input type="text" id="attribute-name" name="attribute_name" class="regular-text"
                           placeholder="Field name" required>

                    <label for="attribute-type" class="mh-admin-label">
                        <?php esc_html_e( 'Field type', 'myhome-core' ); ?>
                    </label>

                    <select id="attribute-type" name="attribute_type">
                        <option value="field"><?php esc_html_e( 'Number field', 'myhome-core' ); ?></option>
                        <option value="taxonomy"><?php esc_html_e( 'Text field', 'myhome-core' ); ?></option>
                    </select>

                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary"
                               value="<?php esc_html_e( 'Create', 'myhome-core' ); ?>">
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    /*
     * It's possible to change regular slug even for core attributes. However plugin has to still recognize them.
     * It's done by base_slug.
     */
    public function get_property_type_slug() {
        foreach ( $this->get_attributes() as $attr ) {
            if ( $attr->base_slug == 'property_type' ) {
                return $attr->attribute_slug;
            }
        }

        return '';
    }

    public function get_offer_type_slug() {
        foreach ( $this->get_attributes() as $attr ) {
            if ( $attr->base_slug == 'offer_type' ) {
                return $attr->attribute_slug;
            }
        }

        return '';
    }

    /*
     * create_table
     *
     * Create attributes table
     */
    public static function create_table() {
        /*
         * Create attributes table and import predefined attributes.
         * IMPORTANT Attributes with base set to 1 should never be deleted. It will cause many errors.
         */
        $base_attributes = array(
            (object) array(
                'attribute_name'    => esc_html__( 'Property type', 'myhome-core' ),
                'attribute_slug'    => 'property-type',
                'attribute_type'    => 'taxonomy',
                'base'              => 1,
                'base_slug'         => 'property_type'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Offer type', 'myhome-core' ),
                'attribute_slug'    => 'offer-type',
                'attribute_type'    => 'taxonomy',
                'base'              => 1,
                'base_slug'         => 'offer_type'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'City', 'myhome-core' ),
                'attribute_slug'    => 'city',
                'attribute_type'    => 'taxonomy',
                'base'              => 0,
                'base_slug'         => 'city'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Price', 'myhome-core' ),
                'attribute_slug'    => 'price',
                'attribute_type'    => 'field',
                'base'              => 1,
                'base_slug'         => 'price'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Keyword', 'myhome-core' ),
                'attribute_slug'    => 'keyword',
                'attribute_type'    => 'core',
                'base'              => 2,
                'base_slug'         => 'keyword'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Bedrooms', 'myhome-core' ),
                'attribute_slug'    => 'price',
                'attribute_type'    => 'field',
                'base'              => 0,
                'base_slug'         => 'bedrooms'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Bathrooms', 'myhome-core' ),
                'attribute_slug'    => 'bathrooms',
                'attribute_type'    => 'field',
                'base'              => 0,
                'base_slug'         => 'bedrooms'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Property Size', 'myhome-core' ),
                'attribute_slug'    => 'property-size',
                'attribute_type'    => 'field',
                'base'              => 0,
                'base_slug'         => 'property_size'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Lot Size', 'myhome-core' ),
                'attribute_slug'    => 'lot-size',
                'attribute_type'    => 'field',
                'base'              => 0,
                'base_slug'         => 'lot_size'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Year', 'myhome-core' ),
                'attribute_slug'    => 'year',
                'attribute_type'    => 'field',
                'base'              => 0,
                'base_slug'         => 'year'
            ),
            (object) array(
                'attribute_name'    => esc_html__( 'Features', 'myhome-core' ),
                'attribute_slug'    => 'features',
                'attribute_type'    => 'taxonomy',
                'base'              => 0,
                'base_slug'         => 'features'
            ),
        );

        // Create attributes table
        global $wpdb;
        $table_name = $wpdb->prefix . 'myhome_attributes';
        $check_table = $wpdb->get_var( "SHOW TABLES LIKE '$table_name' " );
        if ( ! empty( $check_table ) && $check_table == $table_name ) {
            return;
        }
        $charset_collate = $wpdb->get_charset_collate();
        $query = "CREATE TABLE IF NOT EXISTS $table_name (
			ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			attribute_name varchar(191) DEFAULT '' NOT NULL,
			attribute_slug varchar(191) DEFAULT '' NOT NULL,
			attribute_type varchar(20) DEFAULT '' NOT NULL,
			form_order int(11) UNSIGNED DEFAULT 0 NOT NULL,
			base int(11) UNSIGNED DEFAULT 0 NOT NULL,
            base_slug varchar(191) DEFAULT '' NOT NULL,
			PRIMARY KEY  (ID)
			) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $query );

        // import predefined attributes
        foreach ( $base_attributes as $attr ) {
            $check = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE attribute_name = %s OR attribute_slug = %s",
                    $attr->attribute_name,
                    $attr->attribute_slug
                )
            );

            if ( $check ) {
                continue;
            }
            $form_order = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

            $wpdb->insert( $table_name, array(
                'attribute_name'    => $attr->attribute_name,
                'attribute_slug'    => $attr->attribute_slug,
                'attribute_type'    => $attr->attribute_type,
                'form_order'        => $form_order,
                'base'              => $attr->base,
                'base_slug'         => $attr->base_slug
            ) );
        }
    }

    /*
     * create
     *
     * Create new attribute
     */
    public function create() {
        if ( ! isset( $_POST['attribute_name'] ) || ! current_user_can( 'manage_options' ) ) {
            die( 'Access denied.' );
        }

        global $wpdb;
        $table_name     = $wpdb->prefix . $this->table_name;
        $name           = sanitize_text_field( $_POST['attribute_name'] );
        $slug           = sanitize_file_name( mb_strtolower( $_POST['attribute_name'], 'UTF-8' ) );
        $type           = sanitize_text_field( $_POST['attribute_type'] );
        $form_order     = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

        if ( empty( $name ) || empty( $slug ) || empty( $type ) ) {
            wp_redirect( admin_url( 'admin.php?page=myhome_attributes' ) );
        }
        // check if name or slug already exists
        $check = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE attribute_name = %s OR attribute_slug = %s ",
                $name,
                $slug
            )
        );
        if ( ! $check ) {
            // Create new attribute
            $wpdb->insert(
                $table_name,
                array(
                    'attribute_name'    => $name,
                    'attribute_slug'    => $slug,
                    'attribute_type'    => $type,
                    'form_order'        => $form_order
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%d'
                )
            );
        }

        My_Home_Cache::clear_cache();
        flush_rewrite_rules();
        wp_redirect( admin_url( 'admin.php?page=acf-options-' . $slug ) );
    }

    /*
     * update
     *
     * Update existing attribute
     */
    public function update() {
        if ( ! isset( $_POST['attribute_id'] ) || ! isset( $_POST['type'] )
            || ! current_user_can( 'manage_options' ) ) {
            die( 'Access denied.' );
        }
        $type = sanitize_text_field( $_POST['type'] );
        $attribute_id = intval( $_POST['attribute_id'] );
        $form_order = 0;
        $attributes = $this->get_attributes();

        foreach ( $attributes as $key => $attr ) {
            if ( $attr->ID == $attribute_id ) {
                $form_order = $attr->form_order;
                break;
            }
        }

        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;
        $options = get_option( 'myhome_redux' );

        $query = "
            SELECT ID, attribute_name, form_order
            FROM $table_name
            WHERE 
        ";

        // check if offer type isn't disabled
        if ( isset( $options['mh-offer_type'] ) && ! empty( $options['mh-offer_type'] ) ) {
            $query .= " base_slug != 'offer_type' AND ";
        }

        if ( $type == 'up' ) {
            $query .= "
                form_order < $form_order
                ORDER BY form_order DESC
                LIMIT 1
            ";
        } else {
            $query .= "
                form_order > $form_order
                ORDER BY form_order
                LIMIT 1
            ";
        }

        $change_attribute = $wpdb->get_row( $query );

        $wpdb->update(
            $table_name,
            array(
                'form_order' => $change_attribute->form_order
            ),
            array( 'ID' => $attribute_id )
        );
        $wpdb->update(
            $table_name,
            array(
                'form_order' => $form_order
            ),
            array( 'ID' => $change_attribute->ID )
        );
        My_Home_Core()->cache->clear_cache();
    }

    /*
     * update_name
     *
     * Update name of existing attribute
     */
    public function update_name() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['attribute_name'] ) && isset( $_POST['attribute_slug'] ) ) {
            $name = sanitize_text_field( $_POST['attribute_name'] );
            $slug = sanitize_text_field( $_POST['attribute_slug'] );
            if ( mb_strlen( $name, 'UTF-8' ) > 0 && mb_strlen( $slug, 'UTF-8' ) ) {
                global $wpdb;
                $table_name = $wpdb->prefix . $this->table_name;
                $result = $wpdb->update(
                    $table_name,
                    array( 'attribute_name' => $name ),
                    array( 'attribute_slug' => $slug ),
                    array( '%s' ),
                    array( '%s' )
                );
                echo json_encode( array( 'result' => $result ? true : false ) );
                My_Home_Cache::clear_cache();
            } else {
                echo json_encode( array( 'result' => false ) );
            }
        } else {
            echo json_encode( array( 'result' => false ) );
        }
    }

    /*
     * update_slug
     *
     * Update slug of existing attribute
     */
    public function update_slug() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['attribute_old_slug'] ) && isset( $_POST['attribute_new_slug'] ) ) {
            $old_slug = $_POST['attribute_old_slug'];
            $new_slug = sanitize_file_name( mb_strtolower( $_POST['attribute_new_slug'], 'UTF-8' ) );
            if ( mb_strlen( $old_slug, 'UTF-8' ) > 0 && mb_strlen( $new_slug, 'UTF-8' ) > 0 && $new_slug != 'post_tag'
                && $new_slug != 'category' && $new_slug != 'post-tag' ) {
                global $wpdb;
                $table_name = $wpdb->prefix . $this->table_name;
                // check if attribute with this slug already exists (avoid conflicts)
                $check = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM $table_name WHERE attribute_slug = %s ",
                        $new_slug
                    )
                );
                if ( $check ) {
                    echo json_encode( array( 'result' => false ) );
                    return;
                }
                // check if taxonomy with this slug already exists (avoid conflicts)
                $check = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s ",
                        $new_slug
                    )
                );
                if ( $check ) {
                    echo json_encode( array( 'result' => false ) );
                    return;
                }
                // update slug
                $result = $wpdb->update(
                    $table_name,
                    array( 'attribute_slug' => $new_slug ),
                    array( 'attribute_slug' => $old_slug ),
                    array( '%s' ),
                    array( '%s' )
                );
                $property_type_slug = $this->get_property_type_slug();
                $wpdb->query(
                    $wpdb->prepare(
                        "
                        UPDATE {$wpdb->options}
                        SET option_name = REPLACE(option_name, %s, %s ), option_value = REPLACE(option_value, %s, %s )
                        WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s
                        ",
                        $old_slug, $new_slug, $old_slug, $new_slug,
                        '%options_' . $old_slug . '_%',
                        '%' . $property_type_slug . '_%_property_type_' . $old_slug,
                        '%' . $old_slug . '_%_term_image%',
                        '%' . $old_slug . '_%_property_type_%'
                    )
                );
                // update taxonomy slug
                $wpdb->update(
                    $wpdb->term_taxonomy,
                    array( 'taxonomy' => $new_slug ),
                    array( 'taxonomy' => $old_slug ),
                    array( '%s' ),
                    array( '%s' )
                );
                My_Home_Cache::clear_cache();
                set_transient( 'myhome_flush_rewrite_rules', true, 24 * HOUR_IN_SECONDS );

                echo json_encode( array( 'result' => $result ? true : false, 'slug' => $new_slug ) );
            } else {
                echo json_encode( array( 'result' => false ) );
            }
        } else {
            echo json_encode( array( 'result' => false ) );
        }
    }

    /*
     * delete
     *
     * Delete existing attribute.
     * Attribute will be deleted, however data related to this attribute will stay untouched.
     */
    public function delete() {
        if ( ! isset( $_POST['attribute_id'] ) || ! current_user_can( 'manage_options' ) ) {
            die( 'Access denied.' );
        }

        $attribute_id = intval( $_POST['attribute_id'] );
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;
        $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE ID = %d", $attribute_id ) );
        My_Home_Core()->cache->clear_cache();
        flush_rewrite_rules();
        wp_redirect( admin_url( 'admin.php?page=myhome_attributes' ) );
    }

    /*
     * get_options
     *
     * Get options related to attributes. Regular ACF get_field would require way more database requests.
     */
    public function get_options() {
        $cache_key = 'myhome_attribute_options';
        if ( false !== ( $options = get_transient( $cache_key ) ) ) {
            return $options;
        }

        global $wpdb;
        $options = array();
        // get all options at once
        $data = $wpdb->get_results(
            " SELECT * FROM {$wpdb->options} WHERE option_name LIKE 'options%' ",
            ARRAY_A
        );

        foreach ( $data as $opt ) {
            $options[$opt['option_name']] = $opt['option_value'];
        }

        set_transient( $cache_key, $options, 4 * HOUR_IN_SECONDS );
        return $options;
    }

}