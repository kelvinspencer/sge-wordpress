<?php
/*
 * My_Home_Listing class
 *
 * Class responsible for preparing Listing and Map Listing markup base on provided options.
 * Used by Listing Shortcode, Map Listing Shortcodes, My_Home_Term and My_Home_Agent.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Listing' ) ) :

class My_Home_Listing {

	private $atts;

    public function __construct( $atts = array() ) {
        $this->atts = $atts;
    }

    /*
     * search_form
     *
     * Display search form markup (vuejs component)
     */
	public function search_form() {
	    $property_type = '';
        $property_type_slug = My_Home_Core()->attributes->get_property_type_slug();
        if ( ! empty( $this->atts[$property_type_slug] ) ) {
            $property_type = $this->atts[$property_type_slug];
        } elseif ( ! empty( $this->atts['mh-property_type'] ) ) {
            $property_type = $this->atts['mh-property_type'];
        }
        $config = array(
            'label'             => $this->atts['label'],
            'controls'          => My_Home_Core()->attributes->get_form_controls( $this->atts ),
            'position'          => $this->atts['search_form_position'],
            'advancedNumber'    => intval( $this->atts['search_form_advanced_number'] ),
            'propertyType'      => $property_type,
            'showAdvanced'      => $this->atts['show_advanced'] == 'true' ? true : false,
            'showClear'         => $this->atts['show_clear'] == 'true' ? true : false
        );
        ob_start();
        ?>
        <search-form id="myhome-search-form"
                     :config='<?php echo esc_attr( json_encode( $config ) ); ?>'
                     :translations='<?php echo esc_attr( json_encode( My_Home_Translations::get_search_form() ) ); ?>'>
        </search-form>
        <?php
        echo ob_get_clean();
    }

    /*
     * get_attributes
     *
     * Helper method, filter which attributes should be displayed.
     */
    public function get_attributes() {
        $attributes = array();

        foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            if ( $this->atts[$attr->get_slug() . '_show'] == 'true' ) {
                array_push( $attributes, $attr->get_slug() );
            }
        }

        return $attributes;
    }

    /*
     * listing
     *
     * Display listing markup (vuejs component)
     */
    public function listing() {
        $lang = My_Home_Core()->lang;
        $params = array_merge( $this->atts, $_GET );
        if ( ! empty( $lang ) ) {
            $params['lang'] = $lang;
        }
        $config = array(
            'isHomepage'    => is_front_page(),
            'limit'         => intval ( $this->atts['estates_per_page'] ),
            'initArgs'      => $this->get_args(),
            'lazyLoad'      => $this->atts['lazy_loading'] == 'true' ? true : false,
            'lazyLoadLimit' => intval( $this->atts['lazy_loading_limit'] ),
            'loadMoreLabel' => $this->atts['load_more_button'],
            'loadPrevLabel' => $this->atts['load_prev_button'],
            'defaultView'   => $this->atts['listing_default_view'],
            'attributes'    => $this->get_attributes(),
            'firstPage'     => My_Home_API::get( $params ),
            'site'          => site_url(),
            'defaults'      => $this->get_options( $this->atts ),
            'filters'       => $this->get_options( $_GET ),
            'showSortBy'    => $this->atts['show_sort_by'] == 'true' ? true : false,
            'showViewTypes' => $this->atts['show_view_types'] == 'true' ? true : false,
            'lang'          => empty( $lang ) ? '' : $lang
        );
	    ob_start();
	    ?>
        <listing-grid id="myhome-listing"
                :config='<?php echo esc_attr( json_encode( $config ) ); ?>'
                :translations='<?php echo esc_attr( json_encode( My_Home_Translations::get_listing() ) ); ?>'>
        </listing-grid>
        <?php
	    echo ob_get_clean();
    }

    /*
     * listing_map
     *
     * Display listing map markup (vuejs component)
     */
    public function listing_map() {
        $this->atts['limit'] = -1;
        $translations =  json_encode( My_Home_Translations::get_listing_map() );
        $page = My_Home_API::get( $this->atts );
        $options = get_option( 'myhome_redux' );
        $map_style = empty( $options['mh-map-style'] ) ? 'gray' : $options['mh-map-style'];
        $config = array(
            'mapHeight'         => $this->atts['map_height'],
            'defaults'          => $this->get_options( $this->atts, true ),
            'attributes'        => $this->get_attributes(),
            'initEstates'       => $page['estates'],
            'site'              => site_url(),
            'mapStyle'          => $map_style,
            'lang'              => empty( My_Home_Core()->lang ) ? '' : My_Home_Core()->lang
        );
        ob_start();
        ?>
        <div id="myhome-listing-map">
            <listing-map :translations='<?php  echo esc_attr( $translations ); ?>'
                         :config='<?php echo esc_attr( json_encode( $config ) ); ?>'>
            </listing-map>
        </div>
        <?php
        echo ob_get_clean();
    }

    /*
     * get_options
     *
     * Due to nature of attribute (can be created by users) some listing options are also dynamic. Here we look for
     * existing settings base on attribute list.
     */
    private function get_options( $data, $map = false ) {
        $options    = array();
        $types      = array( '', '_from', '_to' );

        foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            foreach ( $types as $t ) {
                $key = $attr->get_slug() . $t;
                if ( isset( $data[$key] ) ) {
                    $options[$key] = $data[$key];
                }
            }
        }

        if ( isset( $data['agent_id'] ) ) {
            $options['agent_id'] = intval( $data['agent_id'] );
        }

        if ( $map ) {
            $options['limit'] = -1;
        }

        return (object) $options;
    }

    /*
     * get_settings
     *
     * Listing settings for Listing Visual Composer element
     */
    public static function get_settings() {
        $agents = My_Home_Agent::get_agents( -1, 0, false, false );
        $agents_list = array( esc_html__( 'Any', 'myhome-core' ) => 0 );
        foreach ( $agents as $agent ) {
            $agents_list[$agent->display_name] = $agent->ID;
        }

	    $fields = array(
            // Search form position
            array(
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Search form position', 'myhome-core' ),
                'param_name'    => 'search_form_position',
                'value'         => array(
                    esc_html__( 'Top', 'myhome-core' )      	=> 'top',
                    esc_html__( 'Right', 'myhome-core' )    	=> 'right',
                    esc_html__( 'Left', 'myhome-core' )     	=> 'left',
                    esc_html__( 'Hidden', 'myhome-core' )     	=> 'hide',
                ),
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Label
            array(
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Label', 'myhome-core' ),
                'param_name'    => 'label',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'dependency'    => array(
                    'element'   => 'search_form_position',
                    'value'     => array( 'top' )
                )
            ),
            // Advanced number
            array(
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Number of filters to show before the "Advanced" button', 'myhome-core' ),
                'param_name'    => 'search_form_advanced_number',
                'value'         => 3,
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'dependency'    => array(
                    'element'   => 'search_form_position',
                    'value'     => array( 'top' )
                )
            ),
            // Default view
            array(
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Default view', 'myhome-core' ),
                'param_name'    => 'listing_default_view',
                'value'         => array(
                    esc_html__( 'Two columns', 'myhome-core' )      => 'colTwo',
                    esc_html__( 'Three columns', 'myhome-core' )    => 'colThree',
                    esc_html__( 'Row', 'myhome-core' )    	        => 'row',
                ),
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Estates per page
            array(
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Estates limit (Number)', 'myhome-core' ),
                'param_name'    => 'estates_per_page',
                'value'         => '12',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Lazy loading
            array(
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Lazy loading', 'myhome-core' ),
                'param_name'    => 'lazy_loading',
                'value'         => 'true',
                'std'           => 'true',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Show more button every N loads
            array(
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Number of times estates will be loaded after clicking “Load More”', 'myhome-core' ),
                'param_name'    => 'lazy_loading_limit',
                'value'         => '1',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Load more button
            array(
                'type'          => 'textfield',
                'heading'       => esc_html__( '"Load More" button label', 'myhome-core' ),
                'param_name'    => 'load_more_button',
                'value'         => esc_html__( 'Load more', 'myhome-core' ),
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Load prev button
            array(
                'type'          => 'textfield',
                'heading'       => esc_html__( '"Load Previous" button label', 'myhome-core' ),
                'param_name'    => 'load_prev_button',
                'value'         => esc_html__( 'Load previous', 'myhome-core' ),
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Show advanced button
            array(
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Display "advanced" button', 'myhome-core' ),
                'param_name'    => 'show_advanced',
                'value'         => 'true',
                'std'           => 'true',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'dependency'    => array(
                    'element'   => 'search_form_position',
                    'value'     => array( 'top' )
                )
            ),
            // Show clear button
            array(
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Display "clear" button', 'myhome-core' ),
                'param_name'    => 'show_clear',
                'value'         => 'true',
                'std'           => 'true',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'dependency'    => array(
                    'element'   => 'search_form_position',
                    'value'     => array( 'top' )
                )
            ),
            //
            // Show short by
            array(
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Display "sort by"', 'myhome-core' ),
                'param_name'    => 'show_sort_by',
                'value'         => 'true',
                'std'           => 'true',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' ),
            ),
            // Show view types
            array(
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Display "view types"', 'myhome-core' ),
                'param_name'    => 'show_view_types',
                'value'         => 'true',
                'std'           => 'true',
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'save_always'   => true,
            ),
            // Agent
            array(
                'group'         => esc_html__( 'Default values', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Agent', 'myhome-core' ),
                'param_name'    => 'agent_id',
                'value'         => $agents_list,
                'save_always'   => true
            ),
        );
        $show = array();

	    foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            if ( ! $attr->like_tags() ) {
                if ( $attr->get_form_control() == 'text_range' || $attr->get_form_control() == 'select_range' ) {
                    array_push(
                        $fields, array(
                        'type'        => $attr->get_vc_type(),
                        'heading'     => sprintf( esc_html__( '%s from', 'myhome-core' ), $attr->get_name() ),
                        'param_name'  => $attr->get_slug() . '_from',
                        'group'       => esc_html__( 'Default values', 'myhome-core' ),
                        'save_always' => true,
                        'value'       => $attr->get_vc_values()
                    )
                    );
                    array_push(
                        $fields, array(
                        'type'        => $attr->get_vc_type(),
                        'heading'     => sprintf( esc_html__( '%s to', 'myhome-core' ), $attr->get_name() ),
                        'param_name'  => $attr->get_slug() . '_to',
                        'group'       => esc_html__( 'Default values', 'myhome-core' ),
                        'save_always' => true,
                        'value'       => $attr->get_vc_values()
                    )
                    );
                } else {
                    array_push(
                        $fields, array(
                        'type'        => $attr->get_vc_type(),
                        'heading'     => $attr->get_name(),
                        'param_name'  => $attr->get_slug(),
                        'group'       => esc_html__( 'Default values', 'myhome-core' ),
                        'save_always' => true,
                        'value'       => $attr->get_vc_values()
                    )
                    );
                }
            }
            array_push( $show, array(
                'type'          => 'checkbox',
                'heading'       => sprintf( esc_html__( 'Show %s', 'myhome-core' ), $attr->get_name() ),
                'param_name'    => $attr->get_slug() . '_show',
                'group'         => esc_html__( 'Show filters', 'myhome-core' ),
                'save_always'   => true,
                'value'         => 'true',
                'std'           => 'true',
            ) );
        }

        return array_merge( $fields, $show );
    }

    /*
     * get_map_settings
     *
     * Settings for Map Listing Visual Composer element
     */
    public static function get_map_settings() {
        $agents = My_Home_Agent::get_agents( -1, 0, false, false );
        $agents_list = array( esc_html__( 'Any', 'myhome-core' ) => 0 );
        foreach ( $agents as $agent ) {
            $agents_list[$agent->display_name] = $agent->ID;
        }
        $fields = array(
            // Search form position
            array(
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Search form style', 'myhome-core' ),
                'param_name'    => 'search_form_position',
                'value'         => array(
                    esc_html__( 'Bottom', 'myhome-core' )   => 'bottom',
                    esc_html__( 'Top', 'myhome-core' )      => 'top',
                    esc_html__( 'Hidden', 'myhome-core' )     => 'hide',
                ),
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Advanced number
            array(
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Number of filters to show before the "Advanced" button', 'myhome-core' ),
                'param_name'    => 'search_form_advanced_number',
                'value'         => 3,
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'dependency'    => array(
                    'element'   => 'search_form_position',
                    'value'     => array( 'top', 'bottom' )
                )
            ),
            // Wide
            array(
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Wide search form', 'myhome-core' ),
                'param_name'    => 'search_form_wide',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'value'         => 'false',
                'std'           => 'false',
                'dependency'    => array(
                    'element'   => 'search_form_position',
                    'value'     => array(
                        'top', 'bottom'
                    )
                )
            ),
            // Label
            array(
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Label', 'myhome-core' ),
                'param_name'    => 'label',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'dependency'    => array(
                    'element'   => 'search_form_position',
                    'value'     => array(
                        'top', 'bottom'
                    )
                )
            ),
            // Map height
            array(
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Map height', 'myhome-core' ),
                'param_name'    => 'map_height',
                'value'         => array(
                    esc_html__( 'Standard', 'myhome-core' )             => 'height-standard',
                    esc_html__( 'Tall', 'myhome-core' )                 => 'height-tall',
                ),
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Show advanced button
            array(
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Display "advanced" button', 'myhome-core' ),
                'param_name'    => 'show_advanced',
                'value'         => 'true',
                'std'           => 'true',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Show clear button
            array(
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Display "clear" button', 'myhome-core' ),
                'param_name'    => 'show_clear',
                'value'         => 'true',
                'std'           => 'true',
                'save_always'   => true,
                'group'         => esc_html__( 'General', 'myhome-core' )
            ),
            // Agent
            array(
                'group'         => esc_html__( 'Default values', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Agent', 'myhome-core' ),
                'param_name'    => 'agent_id',
                'value'         => $agents_list,
                'save_always'   => true
            ),
        );
        $show = array();

        foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            if ( ! $attr->like_tags() ) {
                if ( $attr->get_form_control() == 'text_range' || $attr->get_form_control() == 'select_range' ) {
                    array_push(
                        $fields, array(
                        'type'        => $attr->get_vc_type(),
                        'heading'     => sprintf( esc_html__( '%s from', 'myhome-core' ), $attr->get_name() ),
                        'param_name'  => $attr->get_slug() . '_from',
                        'group'       => esc_html__( 'Default values', 'myhome-core' ),
                        'save_always' => true,
                        'value'       => $attr->get_vc_values()
                    )
                    );
                    array_push(
                        $fields, array(
                        'type'        => $attr->get_vc_type(),
                        'heading'     => sprintf( esc_html__( '%s to', 'myhome-core' ), $attr->get_name() ),
                        'param_name'  => $attr->get_slug() . '_to',
                        'group'       => esc_html__( 'Default values', 'myhome-core' ),
                        'save_always' => true,
                        'value'       => $attr->get_vc_values()
                    )
                    );
                }
                else {
                    array_push(
                        $fields, array(
                        'type'        => $attr->get_vc_type(),
                        'heading'     => $attr->get_name(),
                        'param_name'  => $attr->get_slug(),
                        'group'       => esc_html__( 'Default values', 'myhome-core' ),
                        'save_always' => true,
                        'value'       => $attr->get_vc_values()
                    )
                    );
                }
            }

            array_push( $show, array(
                'type'          => 'checkbox',
                'heading'       => sprintf( esc_html__( 'Show %s', 'myhome-core' ), $attr->get_name() ),
                'param_name'    => $attr->get_slug() . '_show',
                'group'         => esc_html__( 'Show filters', 'myhome-core' ),
                'save_always'   => true,
                'value'         => 'true',
                'std'           => 'true',
            ) );
        }

        return array_merge( $fields, $show );
    }

    /*
     * get_args
     *
     * Get args from url
     */
    private function get_args() {
	    $args = array();
        if ( isset( $_GET['sort'] ) ) {
            $args['sort'] = sanitize_text_field( $_GET['sort'] );
        }

        if ( isset( $_GET['current_page'] ) ) {
            $args['currentPage'] = intval( $_GET['current_page'] );
        }

        if ( isset( $_GET['limit'] ) ) {
            $args['limit'] = intval( $_GET['limit'] );
        }

        return $args;
    }

}

endif;