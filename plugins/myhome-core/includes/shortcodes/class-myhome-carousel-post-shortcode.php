<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Carousel_Post_Shortcode' ) ) :

class My_Home_Carousel_Post_Shortcode {

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
	public static function settings() {
		$categories_vc  = array(
			esc_html__( 'Any', 'myhome-core' ) => 0
		);
		$categories     = get_categories( array(
			'orderby'   => 'name',
			'order'     => 'ASC'
		) );

		foreach ( $categories as $category ) {
			$categories_vc[$category->name] = $category->term_id;
		}

		return array(
			'name'      => esc_html__( 'Post Carousel', 'myhome-core' ),
			'base'      => 'mh_carousel_post',
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
            'category'  => esc_html__( 'MyHome', 'myhome-core' ),
			'params'    => array(
				// Visible
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Max Visible', 'myhome-core' ),
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
				// Posts limit
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Posts limit', 'myhome-core' ),
					'param_name'    => 'posts_limit',
					'value'         => 5
				),
                // Read more
                array(
                    'group'         => esc_html__( 'General', 'myhome-core' ),
                    'type'          => 'textfield',
                    'heading'       => esc_html__( 'Read more text', 'myhome-core' ),
                    'param_name'    => 'read_more_text',
                    'value'         => esc_html__( 'Read more', 'myhome-core')
                ),
				// Style
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Style', 'myhome-core' ),
					'param_name'    => 'posts_style',
					'value'         => array(
						esc_html__( 'Default', 'myhome-core' )  		 => '',
						esc_html__( 'White Background', 'myhome-core' )  => 'mh-post-grid--white',
						esc_html__( 'Dark Background', 'myhome-core' )   => 'mh-post-grid--dark',
					),
				),
				// Dots
				array(
					'group'         => esc_html__( 'General', 'myhome-core' ),
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Dots', 'myhome-core' ),
					'param_name'    => 'owl_dots',
					'value'         => array(
						esc_html__( 'Yes', 'myhome-core' )  => '',
						esc_html__( 'No', 'myhome-core' )  	=> 'owl-carousel--no-dots',
					),
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
            'posts_style'       => '',
            'posts_limit'       => 5,
            'posts_category'    => '',
            'read_more_text'    => esc_html__( 'Read more', 'myhome-core' ),
            'owl_visible'  	    => 'owl-carousel--visible-3',
            'owl_dots'  	    => ''
        );
        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_carousel_post', $opts ) );
        }

	    $args = array(
		    'ignore_sticky_posts'   => true,
		    'posts_per_page'        => intval( $atts['posts_limit'] ),
	    );
	    $posts_category = intval( $atts['posts_category'] );
	    if ( $posts_category ) {
	    	$args['category'] = $posts_category;
	    }

	    if ( ! empty( My_Home_Core()->lang ) ) {
	        $args['suppress_filters'] = 0;
        }

	    $carousel_posts = get_posts( $args );
        $class = $atts['owl_visible'] . ' ' . $atts['owl_dots'];
        ob_start();

        global $post;
        ?>
		<div class="owl-carousel <?php echo esc_attr( $class ); ?>">
	        <?php foreach ( $carousel_posts as $post ) : setup_postdata( $post ); ?>
				<article
                    <?php post_class( 'mh-post-grid mh-post-grid--img-absolute '
                                      . $atts['posts_style'] ); ?>>
					<a href="<?php the_permalink(); ?>"
					   title="<?php the_title_attribute(); ?>"
					   class="mh-post-grid__thumbnail">
						<?php if ( has_post_thumbnail() ) : ?>
							<span class="mh-thumbnail__inner ">
								<?php My_Home_Image::the_image( get_post_thumbnail_id(), 'standard', get_the_title() ); ?>
							</span>
							<span class="mh-caption">
								<span class="mh-caption__inner"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span>
							</span>
						<?php endif; ?>
					</a>
					<div class="mh-post-grid__inner">
						<h3 class="mh-post-grid__heading">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<?php echo esc_html( get_the_title() ); ?>
							</a>
						</h3>
						<div class="mh-post-grid__excerpt">
							<?php echo esc_html( wp_trim_words( get_the_excerpt() , 35, '...' ) ); ?>
						</div>
						<div class="mh-post-grid__btn-wrapper">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"
                               class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--primary-ghost">
								<?php echo esc_html( $atts['read_more_text'] ); ?>
							</a>
						</div>
					</div>
				</article>
	        <?php endforeach; ?>
	    </div>
	    <?php
        wp_reset_postdata();
        return ob_get_clean();
    }

}

endif;
