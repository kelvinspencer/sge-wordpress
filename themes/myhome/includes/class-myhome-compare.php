<?php

/*
 * My_Home_Compare class
 *
 * Prepare 'compare estates' module
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Compare' ) ) :

class My_Home_Compare {

    /*
     * show
     *
     * Setup CompareEstates module
     */
	public function show() {
	    if ( class_exists( 'My_Home_Translations' ) && class_exists( 'My_Home_Core' ) ) {
            ob_start();
            ?>
            <div id="myhome-compare-estates">
                <compare-estates site="<?php echo esc_url( site_url() ); ?>"
                                 <?php if ( ! empty( My_Home_Core()->lang ) ) : ?>
                                     lang="<?php echo esc_attr( My_Home_Core()->lang ); ?>"
                                 <?php endif; ?>
                                 :translations='<?php echo json_encode( My_Home_Translations::get_compare() ); ?>'>
                </compare-estates>
            </div>
            <?php
            echo ob_get_clean();
        }
	}

}

endif;
