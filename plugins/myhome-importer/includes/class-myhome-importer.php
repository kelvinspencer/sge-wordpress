<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 'on' );
ini_set( 'memory_limit', '96M' );
/*
 * My_Home_Importer
 *
 * Demo importer for MyHome theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Importer' ) ) :

class My_Home_Importer {

    private $demos;
    private $plugin_url;
    private $cdn_server = 'http://myhome-cdn.tangibledesign.net/';
    private $search = array(
        'http://export-1.test-tangibledesign.net',
        'http://export-2.test-tangibledesign.net',
        'http://export-3.test-tangibledesign.net',
        'http://export-4.test-tangibledesign.net',
        'http://export-5.test-tangibledesign.net',
        'http://export-6.test-tangibledesign.net'
    );

    public function __construct() {
        $this->plugin_url = plugins_url( '/myhome-importer/' );
        $this->set_demos();
        add_action( 'admin_menu', array( $this, 'init' ) );
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'myhome_importer' ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
        }

        // set admin_post callbacks
        add_action( 'admin_post_myhome_importer_init', array( $this, 'prepare' ) );
        add_action( 'admin_post_myhome_importer_add_posts', array( $this, 'add_posts' ) );
        add_action( 'admin_post_myhome_importer_add_comments', array( $this, 'add_comments' ) );
        add_action( 'admin_post_myhome_importer_add_options', array( $this, 'add_options' ) );
        add_action( 'admin_post_myhome_importer_add_locations', array( $this, 'add_locations' ) );
        add_action( 'admin_post_myhome_importer_add_users', array( $this, 'add_users' ) );
        add_action( 'admin_post_myhome_importer_add_media', array( $this, 'add_media' ) );
        add_action( 'admin_post_myhome_importer_add_terms', array( $this, 'add_terms' ) );
        add_action( 'admin_post_myhome_importer_add_term_taxonomy', array( $this, 'add_term_taxonomy' ) );
        add_action( 'admin_post_myhome_importer_add_term_relationships', array( $this, 'add_term_relationships' ) );
        add_action( 'admin_post_myhome_importer_add_term_meta', array( $this, 'add_term_meta' ) );
        add_action( 'admin_post_myhome_importer_add_attributes', array( $this, 'add_attributes' ) );
        add_action( 'admin_post_myhome_importer_add_redux', array( $this, 'add_redux' ) );
        add_action( 'admin_post_myhome_importer_add_sliders', array( $this, 'add_sliders' ) );
        add_action( 'admin_post_myhome_importer_clear_cache', array( $this, 'clear_cache' ) );

        // load textdomain
        add_action( 'init', array( $this, 'load_textdomain' ) );
    }

    /*
     * load_textdomain
     *
     * Load textdomain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'myhome-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /*
     * set_demos
     *
     * Here we setup all data related to every demo
     */
    private function set_demos() {
        $demo_path      = $this->plugin_url . 'demos/';
        $images_path    = $this->plugin_url . 'assets/images/';
        $this->demos    = array(
            (object) array(
                'key'           => 'demo_1',
                'name'          => esc_html__( 'Default (v1)', 'myhome-importer' ),
                'image'         => $images_path . '1.jpg',
                'meta'          => $demo_path . 'demo_1/meta.json',
                'features'      => array(
                    esc_html__( 'Content: Full', 'myhome-importer' ),
                    esc_html__( 'Homepage: Slider v1', 'myhome-importer' ),
                    esc_html__( 'Primary color: Blue (#2eade3)', 'myhome-importer' ),
                    esc_html__( 'Header: Top bar small + menu', 'myhome-importer' ),
                ),
                'url'           => 'http://v1.myhometheme.net'
            ),
            (object) array(
                'key'           => 'demo_2',
                'name'          => esc_html__( 'New York (v2)', 'myhome-importer' ),
                'image'         => $images_path . '2.jpg',
                'meta'          => $demo_path . 'demo_2/meta.json',
                'features'      => array(
                    esc_html__( 'Content: Full', 'myhome-importer' ),
                    esc_html__( 'Homepage: Map - one city', 'myhome-importer' ),
                    esc_html__( 'Primary color: Raspberry (#e03356)', 'myhome-importer' ),
                    esc_html__( 'Header: Top bar primary + menu', 'myhome-importer' ),
                ),
                'url'           => 'http://v2.myhometheme.net'
            ),
            (object) array(
                'key'           => 'demo_3',
                'name'          => esc_html__( 'US (v3)', 'myhome-importer' ),
                'image'         => $images_path . '3.jpg',
                'meta'          => $demo_path . 'demo_3/meta.json',
                'features'      => array(
                    esc_html__( 'Content: Full', 'myhome-importer' ),
                    esc_html__( 'Homepage: Search form left', 'myhome-importer' ),
                    esc_html__( 'Primary color: Mint (#00a99d)', 'myhome-importer' ),
                    esc_html__( 'Header: Menu', 'myhome-importer' ),
                ),
                'url'           => 'http://v3.myhometheme.net'
            ),
            (object) array(
                'key'           => 'demo_4',
                'name'          => esc_html__( 'Classic Directory (v4)', 'myhome-importer' ),
                'image'         => $images_path . '4.jpg',
                'meta'          => $demo_path . 'demo_4/meta.json',
                'features'      => array(
                    esc_html__( 'Content: Full', 'myhome-importer' ),
                    esc_html__( 'Homepage: Hero Image', 'myhome-importer' ),
                    esc_html__( 'Primary color: Mint (#00a99d)', 'myhome-importer' ),
                    esc_html__( 'Header: Big top bar + primary background menu', 'myhome-importer' ),
                ),
                'url'           => 'http://v4.myhometheme.net'
            ),
        );
    }

    /*
     * init
     *
     * Create admin sub page and attach it to tools menu
     */
    public function init() {
        add_submenu_page(
            'tools.php',
            esc_html__( 'MyHome Demo Importer', 'myhome-importer' ),
            esc_html__( 'MyHome Demo Importer', 'myhome-importer' ),
            'administrator',
            'myhome_importer',
            array( $this, 'page')
        );
    }

    /*
     * page
     *
     * Admin page html output
     */
    public function page() {
        ob_start();
        ?>
        <div id="myhome-importer">
            <demo-importer url="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
                           :demos='<?php echo esc_attr( json_encode( $this->demos ) ); ?>'
                           :translations='<?php echo esc_attr( $this->get_strings() ); ?>'></demo-importer>
        </div>
        <?php
        echo ob_get_clean();
    }

    /*
     * load_scripts
     *
     * Load required css and js files
     */
    public function load_scripts() {
        // load js
        wp_enqueue_script( 'myhome-importer', plugins_url( '/myhome-importer/assets/js/build.js' ), array(), true, true );
        // load styles
        wp_enqueue_style( 'myhome-importer', plugins_url( '/myhome-importer/assets/css/style.css' ) );
    }

    /*
     * Callbacks for admin-post.php actions
     */

    // add post
    public function add_posts() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start      = intval( $_POST['start'] );
            $end        = intval( $_POST['limit'] );
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/posts.json';
            $posts      = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $post = $posts[$i]['post'];
                $post_meta = $posts[$i]['post_meta'];

                $wpdb->insert( $wpdb->posts, $post );
                if ( is_array( $post_meta ) ) {
                    foreach ( $post_meta as $key => $meta ) {
                        $wpdb->insert(
                            $wpdb->postmeta,
                            $meta
                        );
                    }
                }
            }
        }
    }

    // add terms
    public function add_terms() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start      = intval( $_POST['start'] );
            $end        = intval( $_POST['limit'] );
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/terms.json';
            $terms      = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $wpdb->insert( $wpdb->terms, $terms[$i] );
            }
        }
    }

    // add term_taxonomies
    public function add_term_taxonomy() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start          = intval( $_POST['start'] );
            $end            = intval( $_POST['limit'] );
            $demo_key       = sanitize_text_field( $_POST['demoKey'] );
            $file           = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/term_taxonomy.json';
            $term_taxonomy  = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $wpdb->insert( $wpdb->term_taxonomy, $term_taxonomy[$i] );
            }
        }
    }

    // add term_relationships
    public function add_term_relationships() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start              = intval( $_POST['start'] );
            $end                = intval( $_POST['limit'] );
            $demo_key           = sanitize_text_field( $_POST['demoKey'] );
            $file               = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/term_relationships.json';
            $term_relationship  = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $wpdb->insert( $wpdb->term_relationships, $term_relationship[$i] );
            }
        }
    }

    // add term_meta
    public function add_term_meta() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start      = intval( $_POST['start'] );
            $end        = intval( $_POST['limit'] );
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/term_meta.json';
            $term_meta  = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $wpdb->insert( $wpdb->termmeta, $term_meta[$i] );
            }
        }
    }

    // add locations
    public function add_locations() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start      = intval( $_POST['start'] );
            $end        = intval( $_POST['limit'] );
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/locations.json';
            $locations  = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            $table = $wpdb->prefix . 'myhome_locations';

            for ( $i = $start; $i < $end; $i++ ) {
                $wpdb->insert( $table, $locations[$i] );
            }
        }
    }

    // add comment
    public function add_comments() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start      = intval( $_POST['start'] );
            $end        = intval( $_POST['limit'] );
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/comments.json';
            $comments   = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $comment = $comments[$i]['comment'];
                $comment_meta = $comments[$i]['comment_meta'];
                $wpdb->insert( $wpdb->comments,  $comment );
                if ( is_array( $comment_meta ) ) {
                    foreach ( $comment_meta as $meta ) {
                        $wpdb->insert( $wpdb->commentmeta, $meta );
                    }
                }
            }
        }
    }

    // add options
    public function add_options() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start      = intval( $_POST['start'] );
            $end        = intval( $_POST['limit'] );
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/options.json';
            $options    = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $option = $options[$i];
                $wpdb->query( "
                    DELETE FROM {$wpdb->options}
                    WHERE option_name = '" . $option['option_name'] . "'
                " );

                if ( $option['option_name'] == 'mega_main_menu_options' ) {
                    $value = unserialize( $option['option_value'] );
                    $value['last_modified'] = time();
                    $option['option_value'] = serialize( $value );
                }

                if ( $option['option_name'] == 'widget_myhome-image-widget' ) {
                    $values = unserialize( $option['option_value'] ) ;
                    if ( is_array( $values ) ) {
                        foreach ( $values as $key => $value ) {
                            if ( ! empty( $values[$key]['image_url'] ) ) {
                                $values[$key]['image_url'] = str_replace( $this->search, site_url(), $values[$key]['image_url'] );
                            }
                        }
                    }
                    $option['option_value'] = serialize( $values );
                }

                $wpdb->insert(
                    $wpdb->options,
                    array(
                        'option_name'   => $option['option_name'],
                        'option_value'  => $option['option_value'],
                        'autoload'      => $option['autoload']
                    )
                );
            }
        }
    }

    // add user
    public function add_users() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start          = intval( $_POST['start'] );
            $end            = intval( $_POST['limit'] );
            $demo_key       = sanitize_text_field( $_POST['demoKey'] );
            $file           = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/users.json';
            $users          = json_decode( file_get_contents( $file ), true );
            $current_user   = wp_get_current_user();
            $site_url       = get_site_url();

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $user = $users[$i]['user'];
                if ( $user['ID'] == get_current_user_id() || $user['user_login'] == 'admin' ) {
                    continue;
                }

                $user['user_pass'] = $current_user->data->user_pass;
                $user_meta = $users[$i]['user_meta'];
                $wpdb->insert( $wpdb->users,  $user );
                foreach ( $user_meta as $meta ) {
                    $wpdb->insert( $wpdb->usermeta, $meta );
                }
            }
        }
    }

    // add attachment
    public function add_media() {
        $path = $this->cdn_server . 'wp-content/uploads/';
        $upload_dir = wp_upload_dir();
        $save_path = $upload_dir['basedir'] . '/';

        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start      = intval( $_POST['start'] );
            $end        = intval( $_POST['limit'] );
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/media.json';
            $media      = json_decode( file_get_contents( $file ), true );

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $attachment = $media[$i]['attachment'];
                $attachment_meta = $media[$i]['attachment_meta'];
                $wpdb->insert( $wpdb->posts, $attachment );
                foreach ( $attachment_meta as $meta ) {
                    if ( $meta['meta_key'] == '_wp_attached_file' ) {
                        $name   = $save_path . $meta['meta_value'];
                        $source = $path . $meta['meta_value'];
                        $dir    =  dirname( $name );
                        if ( ! file_exists( $dir ) ) {
                            mkdir( $dir );
                        }
                        $response = wp_remote_get( $source, array(
                            'timeout' => 60
                        ) );

                        $file = $response['body'];
                        file_put_contents( $name, $file );
                        $metadata = wp_generate_attachment_metadata( $attachment['ID'], $name );
                        if ( ! empty( $metadata ) ) {
                            wp_update_attachment_metadata( $attachment['ID'], $metadata );
                        }
                        $wpdb->insert( $wpdb->postmeta, $meta );
                    }
                }
            }
        }
    }

    // add attributes
    public function add_attributes() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start      = intval( $_POST['start'] );
            $end        = intval( $_POST['limit'] );
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/attributes.json';
            $attributes = json_decode( file_get_contents( $file ), true );
            print_r($attributes);

            global $wpdb;
            $table = $wpdb->prefix . 'myhome_attributes';

            for ( $i = $start; $i < $end; $i++ ) {
                $wpdb->insert( $table, $attributes[$i] );
            }
        }
    }

    // add sliders
    public function add_sliders() {
        if ( isset( $_POST['demoKey'] ) && isset( $_POST['start'] ) && isset( $_POST['limit'] ) ) {
            $start          = intval( $_POST['start'] );
            $end            = intval( $_POST['limit'] );
            $demo_key       = sanitize_text_field( $_POST['demoKey'] );
            $file           = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/sliders.json';
            $sliders        = json_decode( file_get_contents( $file ), true );
            $sliders_array  = array();

            foreach ( $sliders as $key => $data ) {
                array_push( $sliders_array, array(
                    'key'   => $key,
                    'data'  => $data
                ) );
            }

            global $wpdb;
            for ( $i = $start; $i < $end; $i++ ) {
                $key = $sliders_array[$i]['key'];
                $data = $sliders_array[$i]['data'];

                $table = $wpdb->prefix . $key;
                $wpdb->query( "DELETE FROM $table " );
                foreach ( $data as $row ) {
                    if ( $key == 'revslider_slides' ) {
                        $params = json_decode( $row['params'], true );
                        foreach ( $params as $k => $param ) {
                            $params[$k] = str_replace( $this->search, site_url(), $param );
                        }
                    }
                    $wpdb->insert( $table, $row );
                }
            }
        }
    }

    // add redux options
    public function add_redux() {
        if ( isset( $_POST['demoKey'] ) ) {
            $demo_key   = sanitize_text_field( $_POST['demoKey'] );
            $file       = WP_PLUGIN_DIR . '/myhome-importer/demos/' . $demo_key . '/redux.json';
            $redux      = json_decode( file_get_contents( $file ), true );
            $images     = array(
                'mh-logo',
                'mh-logo-dark',
                'mh-logo-top-bar',
                'mh-top-title-background-image-url',
                'mh-footer-background-image-url',
                'mh-footer-logo'
            );

            foreach ( $images as $image ) {
                if ( empty( $redux[$image] ) ) {
                    continue;
                }
                $redux[$image]['url'] = str_replace( $this->search, site_url(), $redux[$image]['url'] );
                $redux[$image]['thumbnail'] = str_replace( $this->search, site_url(), $redux[$image]['thumbnail'] );
            }

            if ( ! empty( $redux['mh-agent-panel_link'] ) ) {
                $redux['mh-agent-panel_link'] = str_replace( $this->search, site_url(), $redux['mh-agent-panel_link'] );
            }

            update_option( 'myhome_redux', $redux );
        }
    }

    // clear all existing transients
    public function clear_cache() {
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%transient_%' " );
        flush_rewrite_rules();
        wp_load_alloptions();
        wp_cache_delete ( 'alloptions', 'options' );
    }

    // IMPORTANT: Almost all data existing before demo import will be deleted.
    // Remove all existing data which may conflict with demo data.
    public function prepare() {
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->prefix}myhome_attributes " );
        $wpdb->query( "DELETE FROM {$wpdb->prefix}myhome_locations " );
        $wpdb->query( "DELETE FROM {$wpdb->posts} " );
        $wpdb->query( "DELETE FROM {$wpdb->postmeta} " );
        $wpdb->query( "DELETE FROM {$wpdb->commentmeta} " );
        $wpdb->query( "DELETE FROM {$wpdb->comments} " );
        $wpdb->query( "DELETE FROM {$wpdb->terms} " );
        $wpdb->query( "DELETE FROM {$wpdb->term_taxonomy} " );
        $wpdb->query( "DELETE FROM {$wpdb->term_relationships} " );
        $wpdb->query( "DELETE FROM {$wpdb->termmeta} " );
        $wpdb->query( "DELETE FROM {$wpdb->users} WHERE ID != 1 AND ID != " . get_current_user_id() );
        $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE user_id != 1 AND user_id != " . get_current_user_id() );
    }

    // strings for translation
    public function get_strings() {
        return json_encode( array(
            'demo_loaded'           => __( 'You have successfully loaded MyHome Demo', 'myhome-importer' ),
            'demo_online'           => __( 'Demo online', 'myhome-importer' ),
            'plugin_name'           => __( 'MyHome Demo Importer', 'myhome-importer' ),
            'load_demo'             => __( 'Import demo', 'myhome-importer' ),
            'posts'                 => __( 'Posts', 'myhome-importer' ),
            'comments'              => __( 'Comments', 'myhome-importer' ),
            'users'                 => __( 'Users', 'myhome-importer' ),
            'media'                 => __( 'Media', 'myhome-importer' ),
            'terms'                 => __( 'Terms', 'myhome-importer' ),
            'term_taxonomy'         => __( 'Term taxonomy', 'myhome-importer' ),
            'term_relationships'    => __( 'Term relationships', 'myhome-importer' ),
            'term_meta'             => __( 'Term meta', 'myhome-importer' ),
            'options'               => __( 'Options', 'myhome-importer' ),
            'locations'             => __( 'Locations', 'myhome-importer' ),
            'attributes'            => __( 'Attributes', 'myhome-importer' ),
            'sliders'               => __( 'Sliders', 'myhome-importer' ),
            'clear_cache'           => __( 'Clear cache', 'myhome-importer' ),
            'available_demos'       => __( 'Demos:', 'myhome-importer' ),
            'redux'                 => __( 'MyHome Settings', 'myhome-importer' ),
            'time_left_resume'      => __( 'Next attempt to resume in', 'myhome-importer' ),
            'error_message'         => __( 'Ops, something went wrong', 'myhome-importer' ),
            'description'           => wp_kses_post( __( 'IMPORTANT - Loading Demo will remove all of your database content.
Before you start importing demo make sure you activated all required plugins.
            ' ) )
        ) );
    }

}

endif;