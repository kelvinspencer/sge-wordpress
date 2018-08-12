<?php

/**
 * Created by PhpStorm.
 * User: pmallek
 * Date: 2016-08-15
 * Time: 16:29
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Icon_Shortcode' ) ) :

class My_Home_Icon_Shortcode {

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
	public static function settings() {
		return array(
			'name'      => esc_html__( 'Icon', 'myhome-core' ),
			'base'      => 'mh_icon',
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
			'category'  => esc_html__( 'MyHome', 'myhome-core' ),
			'params'    => array(
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Icon', 'myhome-core' ),
					'param_name'    => 'icon',
					'value'         => array(
						esc_html__( 'home', 'myhome-core' ) 			=> 'home',
						esc_html__( 'air conditioner', 'myhome-core' ) 	=> 'air-conditioner',
						esc_html__( 'apartment', 'myhome-core' ) 		=> 'apartment',
						esc_html__( 'area', 'myhome-core' ) 			=> 'area',
						esc_html__( 'bath', 'myhome-core' ) 			=> 'bath',
						esc_html__( 'bath 2', 'myhome-core' ) 			=> 'bath-2',
						esc_html__( 'bathtub', 'myhome-core' ) 			=> 'bathtub',
						esc_html__( 'bed', 'myhome-core' ) 				=> 'bed',
						esc_html__( 'bulb', 'myhome-core' ) 			=> 'bulb',
						esc_html__( 'city', 'myhome-core' ) 			=> 'city',
						esc_html__( 'city 2', 'myhome-core' ) 			=> 'city-2',
						esc_html__( 'computer', 'myhome-core' ) 		=> 'computer',
						esc_html__( 'construction', 'myhome-core' ) 	=> 'construction',
						esc_html__( 'construction 2', 'myhome-core' ) 	=> 'construction-2',
						esc_html__( 'date', 'myhome-core' ) 			=> 'date',
						esc_html__( 'dishwasher', 'myhome-core' ) 		=> 'dishwasher',
						esc_html__( 'door', 'myhome-core' ) 			=> 'door',
						esc_html__( 'fence', 'myhome-core' ) 			=> 'fence',
						esc_html__( 'fireplace', 'myhome-core' ) 		=> 'fireplace',
						esc_html__( 'full size', 'myhome-core' ) 		=> 'full-size',
						esc_html__( 'furniture', 'myhome-core' ) 		=> 'furniture',
						esc_html__( 'garage', 'myhome-core' ) 			=> 'garage',
						esc_html__( 'home', 'myhome-core' ) 			=> 'home',
						esc_html__( 'home 2', 'myhome-core' ) 			=> 'home-2',
						esc_html__( 'home 3', 'myhome-core' ) 			=> 'home-3',
						esc_html__( 'home 4', 'myhome-core' ) 			=> 'home-4',
						esc_html__( 'home 5', 'myhome-core' ) 			=> 'home-5',
						esc_html__( 'home 6', 'myhome-core' ) 			=> 'home-6',
						esc_html__( 'plan', 'myhome-core' ) 			=> 'house-plan',
						esc_html__( 'plan 2', 'myhome-core' ) 			=> 'house-plan-2',
						esc_html__( 'interface', 'myhome-core' ) 		=> 'interface',
						esc_html__( 'layers', 'myhome-core' ) 			=> 'layers',
						esc_html__( 'lift', 'myhome-core' ) 			=> 'lift',
						esc_html__( 'location', 'myhome-core' ) 		=> 'location',
						esc_html__( 'location 2', 'myhome-core' ) 		=> 'location-2',
						esc_html__( 'mail', 'myhome-core' ) 			=> 'mail',
						esc_html__( 'mail 2', 'myhome-core' ) 			=> 'mail-2',
						esc_html__( 'map', 'myhome-core' ) 				=> 'map',
						esc_html__( 'medical', 'myhome-core' ) 			=> 'medical',
						esc_html__( 'microwave', 'myhome-core' ) 		=> 'microwave',
						esc_html__( 'multimedia', 'myhome-core' ) 		=> 'multimedia',
						esc_html__( 'office', 'myhome-core' ) 			=> 'office',
						esc_html__( 'office 2', 'myhome-core' ) 		=> 'office-2',
						esc_html__( 'owen', 'myhome-core' ) 			=> 'owen',
						esc_html__( 'parquet', 'myhome-core' ) 			=> 'parquet',
						esc_html__( 'phone', 'myhome-core' ) 			=> 'phone',
						esc_html__( 'pin', 'myhome-core' ) 				=> 'pin',
						esc_html__( 'prize', 'myhome-core' ) 			=> 'prize',
						esc_html__( 'rent', 'myhome-core' ) 			=> 'rent',
						esc_html__( 'roof', 'myhome-core' ) 			=> 'roof',
						esc_html__( 'school', 'myhome-core' ) 			=> 'school',
						esc_html__( 'school 2', 'myhome-core' ) 		=> 'school-2',
						esc_html__( 'shower', 'myhome-core' ) 			=> 'shower',
						esc_html__( 'sofa', 'myhome-core' ) 			=> 'sofa',
						esc_html__( 'sofa 2', 'myhome-core' ) 			=> 'sofa-2',
						esc_html__( 'sold', 'myhome-core' ) 			=> 'sold',
						esc_html__( 'stairs', 'myhome-core' ) 			=> 'stairs',
						esc_html__( 'swimming pool', 'myhome-core' ) 	=> 'swimming-pool',
						esc_html__( 'technology', 'myhome-core' ) 		=> 'technology',
						esc_html__( 'transport', 'myhome-core' ) 		=> 'transport',
						esc_html__( 'wall', 'myhome-core' ) 			=> 'wall',
						esc_html__( 'wardrobe', 'myhome-core' ) 		=> 'wardrobe',
						esc_html__( 'wifi', 'myhome-core' ) 			=> 'wifi',
						esc_html__( 'window', 'myhome-core' ) 			=> 'window',
					)
				),
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Icon size (font size)', 'myhome-core' ),
					'param_name'    => 'icon_size',
					'value'			=> '50',
				),
				// Icon Align
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Align', 'myhome-core' ),
					'param_name'    => 'icon_align',
					'value'         => array(
						esc_html__( 'Left', 'myhome-core' )  => '',
						esc_html__( 'Center', 'myhome-core' )  	=> 'center',
						esc_html__( 'Right', 'myhome-core' )  	=> 'right',
					),
				),
				// Icon Color
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Color', 'myhome-core' ),
					'param_name'    => 'icon_color',
					'value'         => array(
						esc_html__( 'Default', 'myhome-core' )  	=> 'mh-color-default',
						esc_html__( 'Primary', 'myhome-core' )  	=> 'mh-color-primary',
						esc_html__( 'Other', 'myhome-core' )  		=> 'mh-color-other',
					),
				),
				// Icon Color Other
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'colorpicker',
					'heading'       => esc_html__( 'Color Other', 'myhome-core' ),
					'param_name'    => 'icon_color_other',
					'dependency'    => array(
						'element'   => 'icon_color',
						'value'     => 'mh-color-other',
						'not_empty' => false
					)
				),
				// Shape
				array(
					'group'         => esc_html__( 'Shape', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Shape', 'myhome-core' ),
					'param_name'    => 'shape',
					'value'         => array(
						esc_html__( 'None', 'myhome-core' )  					=> '',
						esc_html__( 'Shape round', 'myhome-core' ) 				=> 'mh-icon-container--round',
						esc_html__( 'Shape rounded rectangle', 'myhome-core' ) 	=> 'mh-icon-container--rounded-rectangle',
						esc_html__( 'Shape square', 'myhome-core' ) 			=> 'mh-icon-container--square',
					),
				),
				array(
					'group'         => esc_html__( 'Shape', 'myhome-core' ),
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Padding', 'myhome-core' ),
					'param_name'    => 'shape_padding',
					'value'			=> '20',
					'dependency'    => array(
						'element'   => 'shape',
						'value'     => array(
							'mh-icon-container--round',
							'mh-icon-container--rounded-rectangle',
							'mh-icon-container--square',
						),
						'not_empty' => false
					)
				),
				// Shape: Border Width
				array(
					'group'         => esc_html__( 'Shape', 'myhome-core' ),
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Border Width', 'myhome-core' ),
					'param_name'    => 'shape_border_width',
					'value'			=> '2',
					'dependency'    => array(
						'element'   => 'shape',
						'value'     => array(
							'mh-icon-container--round',
							'mh-icon-container--rounded-rectangle',
							'mh-icon-container--square',
						),
						'not_empty' => false
					)
				),
				// Shape: Border Style
				array(
					'group'         => esc_html__( 'Shape', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Border Style', 'myhome-core' ),
					'param_name'    => 'shape_border_style',
					'value'         => array(
						esc_html__( 'Solid', 'myhome-core' )  	=> '',
						esc_html__( 'Dotted', 'myhome-core' )  	=> 'mh-border-dotted',
						esc_html__( 'Dashed', 'myhome-core' )  	=> 'mh-border-dashed',
						esc_html__( 'Double', 'myhome-core' )  	=> 'mh-border-double',
					),
					'dependency'    => array(
						'element'   => 'shape',
						'value'     => array(
							'mh-icon-container--round',
							'mh-icon-container--rounded-rectangle',
							'mh-icon-container--square',
						),
						'not_empty' => false
					)
				),
				// Shape: Border Color
				array(
					'group'         => esc_html__( 'Shape', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Border Color', 'myhome-core' ),
					'param_name'    => 'shape_border_color',
					'value'         => array(
						esc_html__( 'Default', 'myhome-core' )  	=> '',
						esc_html__( 'Primary', 'myhome-core' )  	=> 'mh-border-color-primary',
						esc_html__( 'Other', 'myhome-core' )  		=> 'mh-border-color-other',
					),
					'dependency'    => array(
						'element'   => 'shape',
						'value'     => array(
							'mh-icon-container--round',
							'mh-icon-container--square',
							'mh-icon-container--rounded-rectangle',
						),
						'not_empty' => false
					)
				),
				// Shape: Border Color Other
				array(
					'group'         => esc_html__( 'Shape', 'myhome-core' ),
					'type'          => 'colorpicker',
					'heading'       => esc_html__( 'Border Color Other', 'myhome-core' ),
					'param_name'    => 'shape_border_color_other',
					'dependency'    => array(
						'element'   => 'shape_border_color',
						'value'     => 'mh-border-color-other',
						'not_empty' => false
					)
				),
				// Background Color
				array(
					'group'         => esc_html__( 'Shape', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Background Color', 'myhome-core' ),
					'param_name'    => 'shape_background',
					'value'         => array(
						esc_html__( 'Default', 'myhome-core' )  	=> '',
						esc_html__( 'Primary', 'myhome-core' )  	=> 'mh-background-color-primary',
						esc_html__( 'Other', 'myhome-core' )  		=> 'mh-background-color-other',
					),
					'dependency'    => array(
						'element'   => 'shape',
						'value'     => array(
							'mh-icon-container--round',
							'mh-icon-container--square',
							'mh-icon-container--rounded-rectangle',
						),
						'not_empty' => false
					)
				),
				// Background Color Other
				array(
					'group'         => esc_html__( 'Shape', 'myhome-core' ),
					'type'          => 'colorpicker',
					'heading'       => esc_html__( 'Background Color Other', 'myhome-core' ),
					'param_name'    => 'shape_background_other',
					'dependency'    => array(
						'element'   => 'shape_background',
						'value'     => 'mh-background-color-other',
						'not_empty' => false
					)
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
    public function init( $atts ) {
        $atts = vc_map_get_attributes( 'mh_icon', $atts );
        $atts = shortcode_atts( array(
            'icon'    					=> 'air-conditioner',
            'icon_size'    				=> '50',
            'icon_align'    			=> '',
            'icon_color'    			=> 'mh-color-default',
            'icon_color_other'    		=> '',
            'shape'   					=> '',
            'shape_padding'   			=> '20',
            'shape_border_style'   		=> '',
            'shape_border_color'   		=> '',
            'shape_border_color_other'  => '',
            'shape_border_width'   		=> '',
            'shape_background'   		=> '',
            'shape_background_other'   	=> '',
            'css'     					=> ''
        ), $atts );

		// get custom css class
		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$css_class = apply_filters(
                VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,
                vc_shortcode_custom_css_class( $atts['css'], ' ' ),
                'mh_icon',
                $atts
            );
		}
		else {
			$css_class = '';
		}

        ob_start();

		if ( ! empty( $atts['shape'] ) ) :
			$shape_size = $atts['icon_size'] + $atts['shape_padding'] * 2;
			$shape_size_without_border = $shape_size - $atts['shape_border_width'] * 2;
            // wrapper style
            $wrapper_style = 'height:' . $shape_size . 'px;';
            $wrapper_style .= ! empty( $atts['icon_align'] ) ? 'text-align:' . $atts['icon_align'] . ';' : '';
            $wrapper_style .= 'padding-right:' . $shape_size . 'px;';
            // icon container class
            $icon_container_class = $atts['shape'] . ' ' . $atts['shape_border_style'] . ' ' . $atts['shape_background']
                . ' ' . $atts['shape_border_color'];
            // icon container style
            $icon_container_style = 'height:' . $shape_size . 'px;' . 'width:' . $shape_size . 'px;';
            $icon_container_style .= ! empty( $atts['shape_border_color_other'] ) ? 'border-color:'
                . $atts['shape_border_color_other'] . ';' : '';
            $icon_container_style .= ! empty( $atts['shape_border_width'] ) ? 'border-width:'
                . $atts['shape_border_width'] . 'px;' : '';
            $icon_container_style .= ! empty( $atts['shape_background_other'] ) ? 'background:'
                . $atts['shape_background_other'] . ';' : '';
            // icon class
            $icon_class = $atts['icon'] . ' ' . $atts['icon_color'];
            // icon style
            $icon_style = 'line-height:' . $shape_size_without_border . 'px;';
            $icon_style .= ! empty( $atts['icon_color_other'] ) ? 'color:' . $atts['icon_color_other'] . ';' : '';
            $icon_style .= ! empty( $atts['icon_size'] ) ? 'font-size:' . $atts['icon_size'] . 'px;' : '';
			?>
			<div class="mh-icon-wrapper" style="<?php echo esc_attr( $wrapper_style ); ?>">
				<div class="mh-icon-container <?php echo esc_attr( $icon_container_class ); ?>"
                     style="<?php echo esc_attr( $icon_container_style ); ?>">
					<i class="flaticon-<?php echo esc_attr( $icon_class ); ?>"
                       style="<?php echo esc_attr( $icon_style ); ?>"></i>
				</div>
			</div>

		<?php else :
            // wrapper style
            $wrapper_style = ! empty( $atts['icon_align'] ) ? 'text-align:' . $atts['icon_align'] . ';' : '';
            // icon class
            $icon_class = $atts['icon'] . ' ' . $atts['icon_color'];
            // icon style
            $icon_style = '';
            $icon_style .= ! empty( $atts['icon_color_other'] ) ? 'color:' . $atts['icon_color_other'] . ';' : '';
            $icon_style .= ! empty( $atts['icon_size'] ) ? 'font-size:' . $atts['icon_size'] . 'px;' : '';
            ?>
			<div class="<?php echo esc_attr( $css_class ); ?>" style="<?php echo esc_attr( $wrapper_style ); ?>">
				<i class="flaticon-<?php echo esc_attr( $icon_class ); ?>"
                   style="<?php echo esc_attr( $icon_style ); ?>"></i>
			</div>
			<?php
        endif;

        return ob_get_clean();
    }

}

endif;
