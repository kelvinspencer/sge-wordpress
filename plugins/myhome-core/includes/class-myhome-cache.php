<?php

/*
 * My_Home_Cache class
 *
 * To improve overall theme performance we use transitions. This class is keep eye on them and clear in some
 * situations. For example when estate is edited we delete transition related to this estate. In addition some other
 * transition must be deleted, for example related to listing or map so we always get right/fresh results.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Cache' ) ) :

class My_Home_Cache {

    // init hooks so cache is cleared when necessary
    public function __construct() {
        add_action( 'save_post_estate', array( $this, 'clear_cache' ) ) ;
        add_action( 'profile_update', array( $this, 'clear_agent_cache' ) );
        add_action( 'redux/options/myhome_redux/saved', array( $this, 'clear_cache' ) );
        add_action( 'redux/options/myhome_redux/reset', array( $this, 'clear_cache' ) );
        add_action( 'edit_terms', array( $this, 'clear_cache' ) );
        add_action( 'redux/myhome_redux/panel/before', array( $this, 'clear_cache_button' ) );
        add_action( 'admin_post_clear_cache', array( $this, 'clear_cache_button_action' ) );
    }

    /*
     * clear_cache_button
     *
     * Add clear cache button for theme options page
     */
    public function clear_cache_button() {
        ob_start();
        ?>
        <a href="<?php echo esc_url( admin_url('admin-post.php?action=clear_cache' ) ); ?>" class="button"
           style="display:none;" id="myhome-clear-cache">
            <?php esc_html_e( 'Clear cache', 'myhome-core' ); ?>
        </a>
        <?php
        echo ob_get_clean();
    }

    /*
     * clear_cache_button_action
     *
     * Callback for admin-post.php clear_cache action
     */
    public function clear_cache_button_action() {
        $this->clear_cache();
        if ( wp_redirect( admin_url( 'admin.php?page=MyHome' ) ) ) {
            exit;
        }
    }

    /*
     * clear_agent_cache
     *
     * Clear cache related to agents
     */
    public static function clear_agent_cache( $user_id ) {
        global $wpdb;
        $wpdb->query(
            "
            DELETE FROM {$wpdb->options}
            WHERE option_name LIKE '_transient_myhome_agents_%'
                OR option_name LIKE '_transient_timeout_myhome_agents_%'
            "
        );
    }

    /*
     * clear_cache
     *
     * Clear all existing myhome theme/plugin transients
     */
    public static function clear_cache() {
        global $wpdb;
        $wpdb->query(
            "
            DELETE FROM {$wpdb->options}
            WHERE option_name LIKE '%transient_myhome%'
                OR option_name LIKE '%transient_timeout_myhome%'
            "
        );
    }

}

endif;