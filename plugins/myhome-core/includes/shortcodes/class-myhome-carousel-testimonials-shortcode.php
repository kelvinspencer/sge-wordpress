<?php

/**
 * Created by PhpStorm.
 * User: pmallek
 * Date: 2017-02-02
 * Time: 16:35
 */
class My_Home_Carousel_Testimonials_Shortcode {

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
    public static function settings() {
        return array(
            'name'      => esc_html__( 'Testimonial Carousel', 'myhome-core'),
            'base'      => 'mh_carousel_testimonials',
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
            'category'  => esc_html__( 'MyHome', 'myhome-core' ),
            'params'    => array(
                // Visible
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Visible', 'myhome-core' ),
                    'param_name'    => 'owl_visible',
                    'value'         => array(
                        esc_html__( 'Default - 3', 'myhome-core' )  => 'owl-carousel--visible-3',
                        esc_html__( '1 ', 'myhome-core' )  			=> 'owl-carousel--visible-1',
                        esc_html__( '2 ', 'myhome-core' ) 			=> 'owl-carousel--visible-2',
                        esc_html__( '3 ', 'myhome-core' )  			=> 'owl-carousel--visible-3',
                    ),
                    'save_always'   => true
                ),
                // Limit
                array(
                    'type'          => 'textfield',
                    'heading'       => esc_html__( 'Limit', 'myhome-core' ),
                    'param_name'    => 'limit',
                    'value'         => '5'
                ),
                // Style
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Style', 'myhome-core' ),
                    'param_name'    => 'style',
                    'value'         => array(
                        esc_html__( 'Standard', 'myhome-core' )  	=> 'mh-testimonials--standard',
                        esc_html__( 'Cloud text', 'myhome-core' )  	=> 'mh-testimonials--cloud-text',
                        esc_html__( 'Transparent', 'myhome-core' )  => 'mh-testimonials--transparent',
                        esc_html__( 'Boxed', 'myhome-core' )  		=> 'mh-testimonials--boxed',
                    ),
                    'save_always'   => true
                ),
                // Color
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Color Scheme', 'myhome-core' ),
                    'param_name'    => 'color',
                    'value'         => array(
                        esc_html__( 'Dark', 'myhome-core' )  => 'mh-testimonials--dark',
                        esc_html__( 'Light', 'myhome-core' ) => 'mh-testimonials--light',
                    ),
                    'save_always'   => true
                ),
                // Dots
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Dots', 'myhome-core' ),
                    'param_name'    => 'owl_dots',
                    'value'         => array(
                        esc_html__( 'Yes', 'myhome-core' )  => '',
                        esc_html__( 'No', 'myhome-core' )  	=> 'owl-carousel--no-dots',
                    ),
                    'save_always'   => true
                ),
                // Auto play
                array(
                    'type'          => 'checkbox',
                    'heading'       => esc_html__( 'Auto play', 'myhome-core' ),
                    'param_name'    => 'owl_auto_play',
                    'value'         => 'true',
                    'std'           => 'true',
                    'save_always'   => true
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
            'color'         => 'mh-testimonials--dark',
            'style'         => 'mh-testimonials--standard',
            'owl_visible'   => 'owl-carousel--visible-3',
            'owl_dots'      => '',
            'owl_auto_play' => 'true',
            'limit'         => '5'
        );
        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_carousel_testimonials', $opts ) );
        }

        $class = $atts['owl_visible'] . ' ' . $atts['owl_dots'] . ' ' . $atts['color'] . ' ' . $atts['style'];
        if ( $atts['owl_auto_play'] != 'true' ) {
            $class .= ' owl-carousel--no-auto-play';
        }

        $args = array(
            'post_type'                 => 'testimonial',
            'posts_per_page'            => intval( $atts['limit'] ),
            'ignore_sticky_posts'       => true,
            'post_status'               => 'publish'
        );
        $testimonials = new WP_Query( $args );
        ob_start();

        if ( $testimonials->have_posts() ) : ?>
            <div class="owl-carousel <?php echo esc_attr( $class ); ?>">
            <?php while( $testimonials->have_posts() ) : $testimonials->the_post();
                if ( function_exists( 'get_field' ) ) {
                    $occupation = get_field( 'testimonial_occupation' );
                } else {
                    $occupation = '';
                }
                $author = get_the_title();
                ?>
                <div class="item">
                    <article class="mh-testimonial">
                        <div class="mh-testimonial__inner">
                            <blockquote class="mh-testimonial__text">
                                <?php the_content(); ?>
                            </blockquote>
                            <div class="mh-testimonial__photo">
                                <?php if ( has_post_thumbnail() ) :
                                    My_Home_Image::the_image(
                                        get_post_thumbnail_id(),
                                        'square',
                                        $author
                                    );
                                else :
                                    echo esc_html( $author );
                                endif; ?>
                            </div>
                            <?php if ( ! empty( $author ) || ! empty( $occupation ) ) : ?>
                                <div class="mh-testimonial__author-info">
                                    <?php if ( ! empty( $author ) ) : ?>
                                        <h3 class="mh-testimonial__author">
                                            <?php echo esc_html( $author );  ?>
                                        </h3>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $occupation ) ) : ?>
                                        <div class="mh-testimonial__occupation">
                                            <?php echo esc_html( $occupation );  ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php endif;
        return ob_get_clean();
    }

}