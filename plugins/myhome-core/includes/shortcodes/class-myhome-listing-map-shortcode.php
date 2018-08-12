<?php
/*
 * My_Home_Listing_Map_Shortcode class
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Listing_Map_Shortcode' ) ) :

class My_Home_Listing_Map_Shortcode {

    /**
     * init
     *
     * Generate shortcode output base on provided options
     */
	public function init( $opts ) {
        $options = get_option( 'myhome_redux' );
        if ( empty( $options['mh-google-api-key'] ) ) {
            ob_start();
            ?>
            <div class="mh-map-no-key">
                <div class="mh-layout">
                    <i class="flaticon flaticon-pin"></i><br>
                    <?php esc_html_e( 'Paste your Google Maps Api Key in your theme option to display map.', 'myhome-core' ); ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

	    $atts = array(
            'map'                           => true,
            'search_form_wide'              => 'false',
            'show_advanced'                 => 'true',
            'show_clear'                    => 'true',
            'show_sort_by'                  => 'true'
        );

	    if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_listing_map', $opts ) );
        }

		// new listing
		$listing = new My_Home_Listing( $atts );
	   if ( $atts['search_form_wide'] == 'true' ) {
	       $wrapper_class = 'mh-search-wide';
       } else {
	       $wrapper_class = '';
       }

       $map_placeholder_class = '';
       if ( $atts['map_height'] == 'height-standard' ) {
           $map_placeholder_class = 'mh-map-placeholder--standard';
       } elseif ( $atts['map_height'] == 'height-tall' ) {
           $map_placeholder_class = 'mh-map-placeholder--tall';
       }

		ob_start();?>

        <div class="mh-map-wrapper">
            <?php if ( $atts['search_form_position'] == 'top' ) : ?>
                <div class="mh-search-map-top <?php echo esc_attr( $wrapper_class ); ?>">
                    <div class="mh-layout">
                        <?php $listing->search_form(); ?>
                    </div>
                    <div class="mh-map-placeholder <?php echo esc_attr( $map_placeholder_class ); ?>">
                        <?php $listing->listing_map(); ?>
                    </div>
                </div>
            <?php elseif ( $atts['search_form_position'] == 'bottom' ) : ?>
                <div class="mh-search-map-bottom <?php echo esc_attr( $wrapper_class ); ?>">
                    <div class="mh-map-placeholder <?php echo esc_attr( $map_placeholder_class ); ?>">
                        <?php $listing->listing_map(); ?>
                    </div>
                    <div class="mh-layout">
                        <?php $listing->search_form(); ?>
                    </div>
                </div>
            <?php elseif ( $atts['search_form_position'] == 'hide' ) : ?>
                <div class="mh-search-hide <?php echo esc_attr( $wrapper_class ); ?>">
                    <?php $listing->listing_map(); ?>
                </div>
            <?php endif; ?>
        </div>
		<?php return ob_get_clean();
	}

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
	public static function settings() {
		return array(
			'name'      => esc_html__( 'Property Listings Map', 'myhome-core' ),
			'base'      => 'mh_listing_map',
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
			'category'  => esc_html__( 'MyHome', 'myhome-core' ),
			'params'    => My_Home_Listing::get_map_settings()
		);
	}

}

endif;