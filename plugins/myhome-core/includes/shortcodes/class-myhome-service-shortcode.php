<?php
/**
 * My_Home_Service_Shortcode class
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Service_Shortcode' ) ) :

class My_Home_Service_Shortcode {

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
	public static function settings() {
		return array(
			'name'      => esc_html__( 'Service Box', 'myhome-core' ),
			'base'      => 'mh_service',
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
			'category'  => esc_html__( 'MyHome', 'myhome-core' ),
			'params'    => array(
				// Title
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Title', 'myhome-core' ),
					'param_name'    => 'title',
				),
				// Content
				array(
					'type'          => 'textarea_html',
					'heading'       => esc_html__( 'Content', 'myhome-core' ),
					'param_name'    => 'content',
					'save_always'   => true
				),
				// Image
				array(
					'type'          => 'attach_image',
					'heading'       => esc_html__( 'Image', 'myhome-core' ),
					'param_name'    => 'image_id',
					'save_always'   => true
				),
				// Link
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Link', 'myhome-core' ),
					'param_name'    => 'service_link',
					'value'         => '#',
					'description'   => esc_html__( 'eg. http://xxxxxxx.xxx', 'myhome-core' ),
				),
				// Style
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Background color', 'myhome-core' ),
					'param_name'    => 'style',
					'value'         => array(
						esc_html__( 'Default', 'myhome-core' )  		 => '',
						esc_html__( 'White Background', 'myhome-core' )  => 'mh-service--white-background',
						esc_html__( 'Dark Background', 'myhome-core' )   => 'mh-service--dark-background',
					),
				),
				// Button show
				array(
					'group'         => esc_html__( 'Button', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Display button', 'myhome-core' ),
					'param_name'    => 'button_show',
					'value'         => array(
						esc_html__( 'Yes', 'myhome-core' )       => 1,
						esc_html__( 'No', 'myhome-core' )        => 0,
					),
					'save_always'   => true
				),
				// Style
				array(
					'group'         => esc_html__( 'Button', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Button style', 'myhome-core' ),
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
				array(
					'group'         => esc_html__( 'Button', 'myhome-core' ),
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Button text', 'myhome-core' ),
					'param_name'    => 'button_text',
					'value'         => esc_html__( 'Read More', 'myhome-core' ),
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
    public function init( $atts, $content = null ) {
        $atts = vc_map_get_attributes( 'mh_service', $atts );
        $atts = shortcode_atts( array(
            'image_id'  	=> '',
            'service_link'  => '#',
            'title'     	=> '',
            'style'     	=> '',
            'button_show'   => '1',
            'button_style'  => '',
            'button_text'   => esc_html__( 'Read More', 'myhome-core' ),
            'css'       	=> ''
        ), $atts );

	    $content = wpb_js_remove_wpautop( $content, true );

		// get custom css class
		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$css_class = apply_filters(
                VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,
                vc_shortcode_custom_css_class( $atts['css'], ' ' ),
                'mh_service',
                $atts
            );
		}
		else {
			$css_class = '';
		}

		$class          = $atts['style'] . ' ' . $css_class;
		$button_class   = $atts['button_style'];
        ob_start();
        ?>

		<article class="mh-service <?php echo esc_attr( $class ); ?>">
			<?php if ( ! empty( $atts['service_link'] ) ) : ?>
                <a href="<?php echo esc_url( $atts['service_link'] ); ?>"
                   title="<?php echo esc_attr( $atts['title'] ); ?>"
                   class="mh-service__image-wrapper">
                    <?php
                    if ( ! empty( $atts['image_id'] ) ) :
                        My_Home_Image::the_image( $atts['image_id'], 'standard',  $atts['title'] );
                    else :
                        echo esc_html( $atts['title'] );
                    endif;
                    ?>
                </a>
			<?php
            else :
                if ( ! empty( $atts['image_id'] ) ) :
                    My_Home_Image::the_image( $atts['image_id'], 'standard',  $atts['title'] );
                else :
                    echo esc_html( $atts['title'] );
                endif;
            endif
            ?>
			<div class="mh-service__inner">
				<h3 class="mh-service__heading"><?php echo esc_html( $atts['title'] );  ?></h3>
				<div class="mh-service__content">
					<?php echo wp_kses_post( $content ); ?>
				</div>
				<?php if ( $atts['button_show'] ) : ?>
					<div class="mh-service__btn">
						<a href="<?php echo esc_url( $atts['service_link'] ); ?>"
                           class="mdl-button mdl-js-button mdl-js-ripple-effect <?php echo esc_attr( $button_class ); ?>"
                           title="<?php echo esc_attr( $atts['title'] ); ?>">
							<?php echo esc_html( $atts['button_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</article>
        <?php
        return ob_get_clean();
    }

}

endif;
