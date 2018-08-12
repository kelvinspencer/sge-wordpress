<?php

/**
 * Created by PhpStorm.
 * User: pmallek
 * Date: 2017-02-02
 * Time: 16:36
 */
class My_Home_Carousel_Clients_Shortcode {

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
    public static function settings() {
        return array(
            'name'      => esc_html__( 'Client Carousel', 'myhome-core'),
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
            'base'      => 'mh_carousel_clients',
            'category'  => esc_html__( 'MyHome', 'myhome-core' ),
            'params'    => array(
                // Visible
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Number of visible clients', 'myhome-core' ),
                    'param_name'    => 'owl_visible',
                    'value'         => array(
                        esc_html__( 'Default - 3', 'myhome-core' )  => 'owl-carousel--visible-3',
                        esc_html__( '1 ', 'myhome-core' )  			=> 'owl-carousel--visible-1',
                        esc_html__( '2 ', 'myhome-core' ) 			=> 'owl-carousel--visible-2',
                        esc_html__( '3 ', 'myhome-core' )  			=> 'owl-carousel--visible-3',
                        esc_html__( '4 ', 'myhome-core' )  			=> 'owl-carousel--visible-4',
                        esc_html__( '5 ', 'myhome-core' )  			=> 'owl-carousel--visible-5',
                    ),
                ),
                // Limit
                array(
                    'type'          => 'textfield',
                    'heading'       => esc_html__( 'Total number of clients to display', 'myhome-core' ),
                    'param_name'    => 'limit',
                    'value'         => '5'
                ),
                // Dots
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Display navigation dots', 'myhome-core' ),
                    'param_name'    => 'owl_dots',
                    'value'         => array(
                        esc_html__( 'Yes', 'myhome-core' )  => '',
                        esc_html__( 'No', 'myhome-core' )  	=> 'owl-carousel--no-dots',
                    ),
                ),
                // Image filter
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Image Hover Effect - Grayscale', 'myhome-core' ),
                    'param_name'    => 'image_filter',
                    'value'         => array(
                        esc_html__( 'No', 'myhome-core' )  	=> '',
                        esc_html__( 'Yes', 'myhome-core' )  => 'mh-clients--image-filter',
                    ),
                ),
                // Auto play
                array(
                    'type'          => 'checkbox',
                    'heading'       => esc_html__( 'Auto play', 'myhome-core' ),
                    'param_name'    => 'owl_auto_play',
                    'value'         => 'true',
                    'std'           => 'true',
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
    public function init( $opts ) {
        $atts = array(
            'owl_visible'   => 'owl-carousel--visible-3',
            'owl_dots'      => '',
            'owl_auto_play' => 'true',
            'image_filter'  => '',
            'limit'         => '5'
        );
        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_carousel_clients', $opts ) );
        }

        $class = $atts['owl_visible'] . ' ' . $atts['owl_dots'] . ' ' . $atts['image_filter'];
        if ( $atts['owl_auto_play'] != 'true' ) {
            $class .= ' owl-carousel--no-auto-play';
        }

        $args = array(
            'post_type'                 => 'client',
            'posts_per_page'            => intval( $atts['limit'] ),
            'ignore_sticky_posts'       => true,
            'post_status'               => 'publish'
        );
        $clients = new WP_Query( $args );
        ob_start();
        if ( $clients->have_posts() ) : ?>
            <div class="owl-carousel mh-clients <?php echo esc_attr( $class ); ?>">
                <?php while( $clients->have_posts() ) : $clients->the_post();
                    if ( function_exists( 'get_field' ) ) {
                        $link = get_field( 'client_link' );
                    } else {
                        $link = '#';
                    }
                    ?>
                    <div class="item">
                        <div class="mh-client">
                            <?php if ( ! empty( $link ) ) : ?>
                            <a href="<?php echo esc_url( $link ); ?>" title="<?php the_title_attribute(); ?>"
                               target="_blank">
                            <?php endif;
                                if ( has_post_thumbnail() ) :
                                    the_post_thumbnail('full');
                                else :
                                    the_title();
                                endif;
                            if ( ! empty( $link ) ) : ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php endif;
        return ob_get_clean();
    }

}