<?php
/*
 * My_Home_Slider_Shortcode class
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Slider_Shortcode' ) ) :

class My_Home_Slider_Shortcode {

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
    public static function settings() {
        $sliders = array();
        if ( class_exists( 'RevSlider' ) ) {
            $rev_slider = new RevSlider();
            $sliders_all = $rev_slider->getAllSliderAliases();
            $sliders[esc_html__( 'Select slider', 'myhome-core' )]  = '';
            foreach ( $sliders_all as $slider ) {
                $sliders[$slider] = $slider;
            }
        }

        return array(
            'name'                      => esc_html__( 'Revolution Slider', 'myhome-core' ),
            'base'                      => 'mh_slider',
            'icon'                      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
            'admin_enqueue_js'          => plugins_url( 'myhome-core/public/js/admin_vc_slider.js' ),
            'js_view'                   => 'VcMhSliderView',
            'category'                  => esc_html__( 'MyHome', 'myhome-core' ),
            'is_container'              => true,
            'as_parent'                 => array(
                'only' => 'mh_listing'
            ),
            'params' => array(
                array(
                    'group'         => esc_html__( 'General', 'myhome-core' ),
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Slider', 'myhome-core' ),
                    'param_name'    => 'slider',
                    'value'         => $sliders,
                    'save_always'   => true
                )
            )
        );
    }

    /**
     * init
     *
     * Generate shortcode output base on provided options
     */
    public function init( $opts, $content = null ) {
        $content = wpb_js_remove_wpautop( $content, true );
        $atts = array(
            'slider' => ''
        );

        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_slider', $opts ) );
        }

        ob_start();
        ?>
        <div>
            <?php
            if ( ! empty( $atts['slider'] ) && function_exists( 'putRevSlider' ) ) :
                putRevSlider( $atts['slider'] );
            endif;

            if ( ! empty( $content ) ) :  ?>
                <div class="mh-slider__extra-content">
                    <?php echo do_shortcode( $content ); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_mh_slider extends WPBakeryShortCodesContainer {
    }
}

endif;