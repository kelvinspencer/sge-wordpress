<?php

/*
 * My_Home_Listing_Shortcode class
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Listing_Shortcode' ) ) :

class My_Home_Listing_Shortcode {

    /**
     * init
     *
     * Generate shortcode output base on provided options
     */
	public function init( $atts ) {
	    $defaults = array(
            'map'                           => false,
            'show_advanced'                 => 'true',
            'show_clear'                    => 'true',
            'show_sort_by'                  => 'true',
            'show_view_types'               => 'true',
            'search_form_advanced_number'   => 3
        );

        $atts = array_merge( $defaults, vc_map_get_attributes( 'mh_listing', $atts ) );
		// new listing
		$listing = new My_Home_Listing( $atts );
        ob_start();
        ?>
		<div>

		<?php
		if ( $atts['search_form_position'] == 'top' ) : ?>
			<div class="mh-search-classic mh-search-top">
				<?php $listing->search_form(); ?>
				<?php $listing->listing(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $atts['search_form_position'] == 'left' ) : ?>
			<div class="mh-search-classic mh-search-left">
				<div class="mh-layout__sidebar-left">
					<?php $listing->search_form(); ?>
                    <?php dynamic_sidebar( 'mh-listing' ); ?>
                </div>
				<div class="mh-layout__content-right">
					<?php $listing->listing(); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $atts['search_form_position'] == 'right' ) : ?>
			<div class="mh-search-classic mh-search-right">
				<div class="mh-layout__content-left">
					<?php $listing->listing(); ?>
                </div>
				<div class="mh-layout__sidebar-right">
					<?php $listing->search_form(); ?>
                    <?php dynamic_sidebar( 'mh-listing' ); ?>
                </div>
			</div>
		<?php endif; ?>

		<?php if ( $atts['search_form_position'] == 'hide' ) : ?>
            <?php  $listing->listing(); ?>
		<?php endif;
        ?>
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
		// VC Form MH Listing
		// Here we setup MH Listing element (basic data and params)
		return array(
			'name'      => esc_html__( 'Property Listings', 'myhome-core' ),
			'base'      => 'mh_listing',
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
			'category'  => esc_html__( 'MyHome', 'myhome-core' ),
			'params'    => My_Home_Listing::get_settings()
		);
	}
}

endif;