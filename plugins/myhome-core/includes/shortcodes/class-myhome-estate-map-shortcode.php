<?php

if ( ! class_exists( 'My_Home_Estate_Map_Shortcode' ) ) :

class My_Home_Estate_Map_Shortcode {

    /**
     * init
     *
     * Generate shortcode output base on provided options
     */
    public function init( $atts ) {
        $options = get_option( 'myhome_redux' );
        if ( empty( $options['mh-google-api-key'] ) ) {
            ob_start();
            ?>
            <h1 class="mh-map-set-api">
                <?php esc_html_e( 'Google API Key not set', 'myhome-core' ); ?>
            </h1>
            <?php
            return ob_get_clean();
        }

        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( array( 'estate_id' => '', ), vc_map_get_attributes( 'mh_estate_map', $atts ) );
        }

        $estate = My_Home_Estate::get_estate( $atts['estate_id'] );
        ob_start();
        if ( $estate->has_map() ) {
            $estate->map();
        }
        return ob_get_clean();
    }

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
    public static function settings() {
        $estates_list       = My_Home_Estate::get_estates_list();
        $estates_list_vc    = array();
        foreach ( $estates_list as $key => $title ) {
            $estates_list_vc[$title] = $key;
        }

        return array(
            'name'      => esc_html__( 'One Property Map', 'myhome-core' ),
            'base'      => 'mh_estate_map',
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
            'category'  => esc_html__( 'MyHome', 'myhome-core' ),
            'params'    => array(
                // Estate ID
                array(
                    'group'         => esc_html__( 'Estates', 'myhome-core' ),
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Property', 'myhome-core' ),
                    'param_name'    => 'estate_id',
                    'value'         => $estates_list_vc
                ),
            )
        );
    }

}

endif;