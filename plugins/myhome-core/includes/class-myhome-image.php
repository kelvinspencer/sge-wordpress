<?php
/*
 * My_Home_Image class
 *
 * Provide easy way of getting img markup including data for lazysizes (js library).
 * LazySizes: https://github.com/aFarkas/lazysizes
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Image' ) ) :

class My_Home_Image {

    /*
     * the_image
     *
     * Get img markup based on thumbnail id
     */
    public static function the_image( $thumbnail_id = null, $size = 'standard', $alt = '', $class = '' ) {
        $thumbnail_id = is_null( $thumbnail_id ) ? get_post_thumbnail_id() : $thumbnail_id;
        $prefix = 'myhome-';

        if ( $size == 'standard'
            || $size == 'additional'
            || $size == 'square' ) {
            $thumbnail_size = $prefix . $size . '-xs';
        }
        else {
            $thumbnail_size = $prefix . $size;
        }
        ob_start();
        ?>
        <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
             data-srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $thumbnail_id, $thumbnail_size ) ); ?>"
             class="lazyload <?php echo esc_attr( $class ); ?>" alt="<?php echo esc_attr( $alt ); ?>" data-sizes="auto">
        <?php
        echo ob_get_clean();
    }

}

endif;