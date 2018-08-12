<?php

if ( ! class_exists( 'My_Home_Carousel_Attribute_Shortcode' ) ) :

class My_Home_Carousel_Attribute_Shortcode {

    /**
     * init
     *
     * Generate shortcode output base on provided options
     */
    public function init( $opts ) {
        $atts = array(
            'attribute'         => '',
            'visible_number'    => 'owl-carousel--visible-3',
            'total_number'      => 5,
            'dots'              => ''
        );

        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_carousel_attribute', $opts ) );
        }
        $class = implode( ' ', array(
            $atts['dots'], $atts['visible_number']
        ) );

        $total_number = intval( $atts['total_number'] );
        $attribute = My_Home_Attribute::get_by_id( $atts['attribute'] );
        if ( ! $attribute ) {
            return '';
        }
        $terms = My_Home_Term::get( $attribute->get_slug(), $total_number, true, true );

        ob_start();
        ?>
        <div class="owl-carousel <?php echo esc_attr( $class ); ?>">
            <?php foreach ( $terms as $term ) : ?>
                <div class="item">
                    <div class="mh-box__content">
                            <a href="<?php echo esc_url( $term->get_link() ); ?>"
                               title="<?php echo esc_attr( $term->get_name() ); ?>"
                               class="mh-box">
                                <span class="mh-box__img-wrapper">
                                <?php if ( $term->has_image() ) :
                                        My_Home_Image::the_image(
                                            $term->get_image_id(),
                                            'additional',
                                            $term->get_name()
                                        );
                                endif; ?>
                                </span>
                                <div class="mh-box__middle">
                                    <h3 class="mh-box__title mh-heading mh-heading--style-3">
                                        <?php echo esc_attr( $term->get_name() ); ?>
                                    </h3>
                                </div>
                            </a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
    public static function settings() {
        $attributes = array();
        foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            if ( $attr->get_type() == 'taxonomy' ) {
                $attributes[$attr->get_name()] = $attr->get_ID();
            }
        }

        return array(
            'name'              => esc_html__( 'Attribute Carousel', 'myhome-core' ),
            'base'              => 'mh_carousel_attribute',
            'icon'              => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
            'category'          => esc_html__( 'MyHome', 'myhome-core' ),
            'content_element'   => true,
            'params'            => array(
                // Attribute
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Attribute', 'myhome-core' ),
                    'description'   => esc_html__( 'It shows options with at least one property assigned e.g. it will not show Washington if it has not at least one property assigned', 'myhome-core' ),
                    'param_name'    => 'attribute',
                    'value'         => $attributes,
                    'group'         => esc_html__( 'General', 'myhome-core' )
                ),
                // Visible number
                array(
                    'group'         => esc_html__( 'General', 'myhome-core' ),
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Visible number', 'myhome-core' ),
                    'param_name'    => 'visible_number',
                    'value'         => array(
                        esc_html__( 'Default - 3', 'myhome-core' )  => 'owl-carousel--visible-3',
                        esc_html__( '1 ', 'myhome-core' )  			=> 'owl-carousel--visible-1',
                        esc_html__( '2 ', 'myhome-core' ) 			=> 'owl-carousel--visible-2',
                        esc_html__( '3 ', 'myhome-core' )  			=> 'owl-carousel--visible-3',
                        esc_html__( '4 ', 'myhome-core' )  			=> 'owl-carousel--visible-4',
                        esc_html__( '5 ', 'myhome-core' )  			=> 'owl-carousel--visible-5',
                    )
                ),
                // Total number
                array(
                    'group'         => esc_html__( 'General', 'myhome-core' ),
                    'type'          => 'textfield',
                    'heading'       => esc_html__( 'Total elements number', 'myhome-core' ),
                    'param_name'    => 'total_number',
                    'value'         => 5,
                    'description'   => esc_html__( '0 or empty = all elements', 'myhome-core' )
                ),
                // Dots
                array(
                    'group'         => esc_html__( 'General', 'myhome-core' ),
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Dots', 'myhome-core' ),
                    'param_name'    => 'dots',
                    'value'         => array(
                        esc_html__( 'Yes', 'myhome-core' )  => '',
                        esc_html__( 'No', 'myhome-core' )  	=> 'owl-carousel--no-dots',
                    )
                )
            )
        );
    }
}

endif;