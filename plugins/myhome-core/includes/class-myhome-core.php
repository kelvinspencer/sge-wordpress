<?php

/*
 * My_Home_Core Class
 *
 * My_Home_Core is a main MyHome theme plugin class. All things which should not be inside theme are initiated here.
 * (Shortcodes, VC Elements, Post types, Taxonomies and more)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Core' ) ) :

class My_Home_Core {

	private static $instance = false;
	private $plugin_name;
	private $plugin_path;
	private $plugin_url_path;
	private $plugin_version;
	private $api;
	private $vc;
	public $shortcodes;
	public $options;
	public $estates;
	public $property_types;
	public $agents;
	public $attributes;
	public $cache;
	public $post_types;
	public $lang;

	private function __construct() {
        // load required files
        $this->load_dependencies();
    }

	public function init() {
		$this->plugin_name      = esc_html__( 'MyHome Core', 'myhome-core' );
		$this->plugin_path      = plugin_dir_path( __FILE__ );
		$this->plugin_url_path  = plugin_dir_url( dirname( __FILE__ ) );
		$this->plugin_version   = '1.0.0';
		$this->lang             = apply_filters( 'wpml_current_language', NULL );

        // Initiate
        // used for searching estates
        $this->api              = new My_Home_API();
        // initiate attributes (property attributes)
        $this->attributes       = new My_Home_Attributes();
        // additional things related to property types (like custom fields)
        $this->property_types   = new My_Home_Property_Types();
        // initiate shortcodes
        $this->shortcodes       = new My_Home_Shortcodes();
        // initiate VC templates
        $this->vc               = new My_Home_VC();
        // initiate estates
        $this->estates          = new My_Home_Estates();
        // initiate agents
        $this->agents           = new My_Home_Agents();
        // initiate cache system
        $this->cache            = new My_Home_Cache();
        // register additional post types
        $this->post_types       = new My_Home_Post_Types();

        // register widgets
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );
        // Load scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
        // flush rewrite rules
        add_action( 'init', array( $this, 'flush_rewrite_rules' ), 100 );
        // load textdomain
        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action( 'init', array( $this, 'homepage_listing_rewrite' ) );
        add_filter( 'redirect_canonical', array( $this, 'homepage_listing_canonical' ), 10, 2 );
	}

    public function homepage_listing_rewrite() {
	    $homepage_id = get_option('page_on_front');
        add_rewrite_rule('^mh/?$', 'index.php?page_id=' . $homepage_id, 'top');
    }

    public function homepage_listing_canonical($redirect_url, $requested_url) {
        $homepage_id = get_option('page_on_front');
        $post = get_post();
        if ( is_page() && $post->ID == $homepage_id ) {
            return $requested_url;
        } else {
            return $redirect_url;
        }
    }

    /*
     * flush_rewrite_rules
     *
     * After creating new attributes which are based on taxonomies it's required to flush rewrite rules.
     */
    public function flush_rewrite_rules() {
        flush_rewrite_rules();
	    if ( false !== ( $check = get_transient( 'myhome_flush_rewrite_rules' ) ) && $check ) {
            flush_rewrite_rules();
            set_transient( 'myhome_flush_rewrite_rules', false, 24 * HOUR_IN_SECONDS );
        }
	}

	/*
	 * load_scripts
	 *
	 * Load additional plugin JS files
	 */
    public function load_scripts() {
	    wp_enqueue_script( 'myhome-core-main', $this->plugin_url_path . 'public/js/main.js', array( 'jquery' ), false, true );
    }

    /*
     * load_dependencies
     *
     * Load all required dependencies
     */
	private function load_dependencies() {
		/*
		 * Listing
		 */
		include_once $this->plugin_path . 'class-myhome-listing.php';
		include_once $this->plugin_path . 'class-myhome-translations.php';
		// shortcodes
		include_once $this->plugin_path . 'class-myhome-shortcodes.php';
		// visual composer integration
		include_once $this->plugin_path . 'class-myhome-vc.php';
        // create agent role and grant permissions
        include_once $this->plugin_path . 'models/class-myhome-agent.php';
        include_once $this->plugin_path . 'models/class-myhome-estate.php';
        include_once $this->plugin_path . 'models/class-myhome-term.php';
        include_once $this->plugin_path . 'models/class-myhome-attribute.php';
        include_once $this->plugin_path . 'class-myhome-query-estates.php';
        include_once $this->plugin_path . 'class-myhome-property-types.php';
		include_once $this->plugin_path . 'class-myhome-estates.php';
		include_once $this->plugin_path . 'class-myhome-agents.php';
		include_once $this->plugin_path . 'class-myhome-post-types.php';
		include_once $this->plugin_path . 'class-myhome-cache.php';
		include_once $this->plugin_path . 'class-myhome-image.php';
		include_once $this->plugin_path . 'class-myhome-attributes.php';
		include_once $this->plugin_path . 'class-myhome-estates-slider.php';
		// initiate rest api
		include_once $this->plugin_path . 'class-myhome-api.php';
		// widgets
		include_once $this->plugin_path . 'widgets/class-myhome-facebook-widget.php';
		include_once $this->plugin_path . 'widgets/class-myhome-infobox-widget.php';
		include_once $this->plugin_path . 'widgets/class-myhome-social-icons-widget.php';
		include_once $this->plugin_path . 'widgets/class-myhome-twitter-widget.php';
		// twitter api wrapper required by twitter widget https://github.com/J7mbo/twitter-api-php
		include_once $this->plugin_path . 'TwitterAPIExchange.php';

		// Payments
		include_once $this->plugin_path . 'payments/class-myhome-stripe.php';
		include_once $this->plugin_path . 'payments/class-myhome-paypal.php';
	}

	/*
	 * load_textdomain
	 *
	 * Load textdomain, used for translations
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'myhome-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/*
	 * get_instance
	 *
	 * Get My_Home_Core instance or create if doesn't exists
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/*
	 * register_widgets
	 *
	 * Register all widgets provided by MyHome theme plugin.
	 */
	public function register_widgets() {
		register_widget( 'My_Home_Facebook_Widget' );
		register_widget( 'My_Home_Twitter_Widget' );
		register_widget( 'My_Home_Infobox' );
		register_widget( 'My_Home_Social_Icons_Widget' );
	}

	/*
	 * activation
	 *
	 * Fire right after plugin activation.
	 */
	public function activation() {
	    // create attributes table
	    My_Home_Attributes::create_table();
	    // create additional table with property locations, way easier filtering by lat/lng.
		My_Home_Estates::create_table();
		// Create agent role
		My_Home_Agents::create();
	}

}

endif; // class exists
