<?php
/*
 * My_Home_Slider_Estate_Shortcode class
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Slider_Estate_Shortcode' ) ) :

class My_Home_Slider_Estate_Shortcode {

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
    public static function settings() {
        $agents = My_Home_Agent::get_agents( -1, 0, false, false );
        $agents_list = array( esc_html__( 'Any', 'myhome-core' ) => 0 );
        foreach ( $agents as $agent ) {
            $agents_list[$agent->data->display_name] = $agent->ID;
        }

        $fields = array(
            // Style
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Slider Style', 'myhome-core' ),
                'param_name'    => 'estates_slider_style',
                'value'         => array(
                    esc_html__( 'Card', 'myhome-core' )         => 'estate_slider_card',
                    esc_html__( 'Card short', 'myhome-core' )   => 'estate_slider_card_short',
                    esc_html__( 'Transparent', 'myhome-core' )  => 'estate_slider_transparent',
                ),
                'save_always'   => true
            ),
            // Sort by
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Sort by', 'myhome-core' ),
                'param_name'    => 'sort',
                'value'         => array(
                    esc_html__( 'Newest', 'myhome-core' )               => 'newest',
                    esc_html__( 'Price (high to low)', 'myhome-core' )  => 'priceHighToLow',
                    esc_html__( 'Price (low to high)', 'myhome-core' )  => 'priceLowToHigh',
                    esc_html__( 'Popular', 'myhome-core' )  			=> 'popular',
                ),
                'save_always'   => true
            ),
            // Estates limit
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Estates limit', 'myhome-core' ),
                'param_name'    => 'limit',
                'value'         => 3,
                'save_always'   => true
            ),
            // Agent
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Narrow estates to single agent', 'myhome-core' ),
                'param_name'    => 'agent_id',
                'value'         => $agents_list,
                'save_always'   => true
            ),
            // Featured
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Narrow estates to featured only', 'myhome-core' ),
                'param_name'    => 'featured',
                'save_always'   => true,
                'value'         => 'true'
            ),
            // Estates in
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Narrow estates to IDs:' , 'myhome-core' ),
                'param_name'    => 'estates__in',
                'value'         => '',
                'save_always'   => true
            ),
        );

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
        }

        return array(
            'name'              => esc_html__( 'Properties Slider', 'myhome-core' ),
            'base'              => 'mh_slider_estate',
            'icon'              => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
            'admin_enqueue_js'  => plugins_url( 'myhome-core/public/js/admin_vc_slider_estate.js' ),
            'js_view'           => 'VcMhSliderEstateView',
            'category'          => esc_html__( 'MyHome', 'myhome-core' ),
            'is_container'      => true,
            'as_parent'         => array(
                'only' => 'mh_listing'
            ),
            'params' => $fields
        );
    }

    /**
     * init
     *
     * Generate shortcode output base on provided options
     */
    public function init( $opts, $content = null ) {
        $content = wpb_js_remove_wpautop( $content );
        $atts = array(
            'slider_style'  => 1,
            'align'         => '',
            'limit'         => 3,
            'sort'          => 'DESC',
            'estates__in'   => '',
            'featured'      => '',
            'agent_id'      => '',
            'objects'       => true
        );
        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_slider_estate', $opts ) );
        }

        $atts['featured'] = $atts['featured'] == 'true';

        if ( ! empty( My_Home_Core()->lang ) ) {
            $atts['lang'] = My_Home_Core()->lang;
        }

        $results = My_Home_API::get( $atts );

        $slider = new My_Home_Estates_Slider(
            $results['estates'],
            $content,
            $atts['estates_slider_style']
        );
        ob_start();
        $slider->render( $content );
        return ob_get_clean();
    }
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_mh_slider_estate extends WPBakeryShortCodesContainer {}
}

endif;
