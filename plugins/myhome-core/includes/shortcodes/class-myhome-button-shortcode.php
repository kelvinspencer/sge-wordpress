<?php
/*
 * My_Home_Button_Shortcode
 *
 * Button shortcode used by Button VC element.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Button_Shortcode' ) ) :

class My_Home_Button_Shortcode {

	/**
	 * settings
     *
     * Prepare settings for Visual Composer element
	 */
	public static function settings() {
		return array(
			'name'      => esc_html__( 'Button', 'myhome-core' ),
			'base'      => 'mh_button',
			'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
			'category'  => esc_html__( 'MyHome', 'myhome-core' ),
			'params'    => array(
                // Text
				array(
					'group'         => esc_html__( 'Button', 'myhome-core' ),
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Text', 'myhome-core' ),
					'param_name'    => 'button_text',
					'value'         => esc_html__( 'Read More', 'myhome-core' ),
					'save_always'   => true
				),
				// Style
				array(
					'group'         => esc_html__( 'Button', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Style', 'myhome-core' ),
					'param_name'    => 'button_style',
					'value'         => array(
						esc_html__( 'Primary', 'myhome-core' )  		=> 'mdl-button--raised mdl-button--primary',
						esc_html__( 'Primary ghost', 'myhome-core' )  	=> 'mdl-button--raised mdl-button--primary-ghost',
						esc_html__( 'Transparent', 'myhome-core' )  	=> 'mdl-button--raised',
						esc_html__( 'White', 'myhome-core' )  			=> 'mdl-button--white',
						esc_html__( 'Dark', 'myhome-core' )  			=> 'mdl-button--raised mdl-button--dark',
					),
					'save_always'   => true
				),
				// Size
				array(
					'group'         => esc_html__( 'Button', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Size', 'myhome-core' ),
					'param_name'    => 'button_size',
					'value'         => array(
						esc_html__( 'Default', 'myhome-core' ) => '',
						esc_html__( 'Big', 'myhome-core' )     => 'mdl-button--lg'
					),
					'save_always'   => true
				),
				// Align
				array(
					'group'         => esc_html__( 'Button', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Align', 'myhome-core' ),
					'param_name'    => 'button_align',
					'value'         => array(
						esc_html__( 'Left', 'myhome-core' )    => 'left',
						esc_html__( 'Center', 'myhome-core' )  => 'center',
						esc_html__( 'Right', 'myhome-core' )   => 'right'
					),
					'save_always'   => true
				),
				// Link
				array(
					'group'         => esc_html__( 'Button', 'myhome-core' ),
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Link', 'myhome-core' ),
					'param_name'    => 'button_url',
					'value'         => '#',
					'description'   => esc_html__( 'eg. http://xxxxxxx.xxx', 'myhome-core' ),
					'save_always'   => true
				),
				// Css
				array(
					'type'          => 'css_editor',
					'heading'       => esc_html__( 'Css', 'myhome-core' ),
					'param_name'    => 'css',
					'group'         => esc_html__( 'Design options', 'myhome-core' ),
				),
			)
		);
	}

	/**
	 * init
     *
     * Generate shortcode output base on provided options
	 */
    public function init( $opts ) {
        $atts = array(
            'button_style' => '',
            'button_align' => 'left',
            'button_size'  => '',
            'button_text'  => esc_html__( 'Read More', 'myhome-core' ),
            'button_url'   => '#',
            'css'          => ''
        );
        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_button', $opts ) );
        }

		$button_align = 'text-align:' . $atts['button_align'] . ';';
		$button_style = $atts['button_style'] . ' ' . $atts['button_size'];

        if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
            $css_class = apply_filters(
                VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,
                vc_shortcode_custom_css_class( $atts['css'], ' ' ),
                'mh_button',
            $atts );
        } else {
            $css_class = '';
        }

        ob_start();
        ?>
		<div style="<?php echo esc_attr( $button_align ); ?>" class="<?php echo esc_attr( $css_class ); ?>">
            <a href="<?php echo esc_url( $atts['button_url'] ); ?>"
               class="mdl-button mdl-js-button mdl-js-ripple-effect <?php echo esc_attr( $button_style ); ?>"
               title="<?php echo esc_attr( $atts['button_text'] ); ?>">
                <?php echo esc_html( $atts['button_text'] ); ?>
            </a>
		</div>
        <?php
        return ob_get_clean();
    }

}

endif;
