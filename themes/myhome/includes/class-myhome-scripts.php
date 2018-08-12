<?php

/*
 * My_Home_Scripts
 *
 * Enqueue js and css files
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Scripts' ) ) :

class My_Home_Scripts {

	public function __construct() {
		// load scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
	}

	/*
	 * load_admin_scripts
	 *
	 * Load js and css files for admin user
	 */
	public function load_admin_scripts() {
	    $assets_js = get_template_directory_uri() . '/assets/js/';
        wp_enqueue_style( 'myhome-admin', get_template_directory_uri() . '/assets/css/mh-admin.css', array(), My_Home_Theme()->version );
        wp_enqueue_script( 'myhome-admin', $assets_js . 'admin.js', array(), My_Home_Theme()->version, true );
        wp_enqueue_script( 'myhome-vue-manifest', $assets_js . 'manifest.js', array(), My_Home_Theme()->version, true );
        wp_enqueue_script( 'myhome-vue-vendor', $assets_js . 'vendor.js', array(), My_Home_Theme()->version, true );
        wp_enqueue_script( 'myhome-vue-app', $assets_js . 'app.js', array(
            'myhome-vue-manifest', 'myhome-vue-vendor'
        ), My_Home_Theme()->version, true );
        wp_enqueue_script( 'lazy-sizes', get_template_directory_uri() . '/assets/js/lazysizes.min.js', array(), My_Home_Theme()->version, true );
    }

    /*
     * load_scripts
     *
     * Load all required js and css files
     */
	public function load_scripts() {
        /*
         * CSS Files
         */
        wp_enqueue_style( 'normalize', get_template_directory_uri() . '/assets/css/normalize.css', array(), My_Home_Theme()->version );
        wp_enqueue_style(
            'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), My_Home_Theme()->version
        );

        $rtl_support = My_Home_Theme()->settings->get( 'typography-rtl' );
        if ( ! empty( $rtl_support ) && $rtl_support ) {
            wp_enqueue_style( 'myhome-style', get_template_directory_uri() . '/style-rtl.css', array(), My_Home_Theme()->version );
            wp_enqueue_style( 'myhome-style-rtl-fix', get_template_directory_uri() . '/assets/css/rtl/fix.css', array( 'myhome-style' ), My_Home_Theme()->version );
        } else {
            wp_enqueue_style( 'myhome-style', get_stylesheet_uri(), array(), My_Home_Theme()->version );
        }
        /*
         * JS Files
         */
        if ( is_singular( 'estate' ) ) {
            wp_enqueue_script( 'jquery-ui-accordion' );
        }
        wp_enqueue_script(
            'myhome-bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js',
            array( 'jquery' ), false, true
        );
        wp_enqueue_script(
            'myhome-bootstrap-select', get_template_directory_uri() . '/assets/js/bootstrap-select.min.js',
            array( 'jquery' ), false, true
        );
        wp_enqueue_script(
            'myhome-mdl', get_template_directory_uri() . '/assets/js/material.min.js', array( 'jquery' ),
            My_Home_Theme()->version, true
        );
        wp_enqueue_script(
            'lazy-sizes', get_template_directory_uri() . '/assets/js/lazysizes.min.js', array(), false,
            true
        );
        wp_enqueue_script(
            'myhome-vue-manifest', get_template_directory_uri() . '/assets/js/manifest.js', array(),
            My_Home_Theme()->version, true
        );
        wp_enqueue_script(
            'myhome-vue-vendor', get_template_directory_uri() . '/assets/js/vendor.js', array(),
            My_Home_Theme()->version, true
        );
        wp_enqueue_script(
            'magnific-popup', get_template_directory_uri() . '/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ),
            My_Home_Theme()->version, true
        );
        wp_enqueue_script(
            'owl-carousel', get_template_directory_uri() . '/assets/js/owl.carousel.js',
            array( 'jquery' ), My_Home_Theme()->version, true
        );
        wp_enqueue_script(
            'myhome-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ),
            My_Home_Theme()->version, true
        );
        $google_api_key = My_Home_Theme()->settings->get( 'google-api-key' );
        if ( ! empty( $google_api_key ) ) {
            wp_enqueue_script(
                'infobox', get_template_directory_uri() . '/assets/js/infobox.min.js', array( 'jquery' ),
                My_Home_Theme()->version, true
            );
            wp_enqueue_script(
                'richmarker', get_template_directory_uri() . '/assets/js/richmarker.min.js',
                array( 'jquery' ), My_Home_Theme()->version, true
            );
        }
        wp_enqueue_script(
            'myhome-vue-app', get_template_directory_uri() . '/assets/js/app.js', array(
            'myhome-vue-manifest', 'myhome-vue-vendor' ), My_Home_Theme()->version, true
        );
        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }

        if ( is_singular( 'estate' ) ) {
            if ( My_Home_Theme()->settings->get( 'estate_slider' ) == 'single-estate-gallery' ) {
                wp_enqueue_script(
                    'myhome-estate-gallery', get_template_directory_uri() . '/assets/js/sliders/gallery.js',
                    array( 'jquery' ), My_Home_Theme()->version, true
                );
            }
            elseif ( My_Home_Theme()->settings->get( 'estate_slider' ) == 'single-estate-slider' ) {
                wp_enqueue_script(
                    'myhome-estate-slider', get_template_directory_uri() . '/assets/js/sliders/slider.js',
                    array( 'jquery' ), My_Home_Theme()->version, true
                );
            }
        }

        $google_api_key = My_Home_Theme()->settings->get( 'google-api-key' );
        if ( ! empty( $google_api_key ) ) {
            wp_enqueue_script(
                'google-maps-api',
                '//maps.googleapis.com/maps/api/js?key=' . $google_api_key . '&libraries=places',
                array( 'jquery' ),
                false,
                false
            );
            wp_enqueue_script(
                'google-maps-markerclusterer',
                get_template_directory_uri() . '/assets/js/markerclusterer.js',
                array( 'google-maps-api' ),
                My_Home_Theme()->version,
                false
            );
        }

        // load default fonts if redux options are not installed
        if ( ! class_exists( 'ReduxFramework' ) ) {
            wp_enqueue_style( 'prefix-fonts', $this->fonts_url(), array(), null );
        }

        $request = $_SERVER['REQUEST_URI'];
        $panel = My_Home_Theme()->settings->get( 'agent-panel_link' );
        // Load payment SDKs
        if ( class_exists( 'ReduxFramework' ) && strpos( $panel, $request ) !== false && $request != '/'
            && My_Home_Theme()->settings->get( 'payment' ) ) {
            if ( My_Home_Theme()->settings->get( 'payment-stripe' ) ) {
                wp_enqueue_script( 'stripe-checkout', 'https://checkout.stripe.com/checkout.js' );
            }
            if ( My_Home_Theme()->settings->get( 'payment-paypal' ) ) {
                wp_enqueue_script( 'paypal-checkout', 'https://www.paypalobjects.com/api/checkout.js' );
            }
        }

        $map_style = My_Home_Theme()->settings->get( 'map-style' );
        if ( empty( $map_style ) ) {
            $map_style = 'gray';
        }

        ob_start();
        ?>
        window.MyHome = {
            siteUrl: '<?php echo esc_url( site_url() ); ?>',
            adminPostUrl: '<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>',
            <?php if ( $map_style == 'custom' ) : ?>
            mapStyle: <?php echo My_Home_Theme()->settings->get( 'map-style_custom' ); ?>
            <?php endif; ?>
        };
        <?php
        $inline_js = ob_get_clean();
        wp_add_inline_script( 'myhome-main', $inline_js );

        $options = get_option( 'myhome_redux' );
        $request = $_SERVER['REQUEST_URI'];
        $panel = $options['mh-agent-panel_link'];
        if ( class_exists( 'ReduxFramework' ) && strpos( $panel, $request ) !== false && $request != '/' ) {
            wp_enqueue_style( 'dropzonejs', get_template_directory_uri() . '/assets/css/dropzone.css' );
            wp_enqueue_script( 'dropzonejs', get_template_directory_uri() . '/assets/js/dropzone.js' );
            ob_start();
            ?>
            Dropzone.autoDiscover = false;
            window.MyHomeSubmitProperty = {
                video: <?php echo esc_attr( $options['mh-estate_video'] ? 'true' : 'false' ); ?>,
                tour: <?php echo esc_attr( $options['mh-estate_virtual_tour'] ? 'true' : 'false' ); ?>,
                plans: <?php echo esc_attr( $options['mh-estate_plans'] ? 'true' : 'false' ); ?>,
                moderation: <?php echo esc_attr( $options['mh-agent-moderation'] ? 'true' : 'false' ); ?>,
                payment: <?php echo esc_attr( $options['mh-payment'] ? 'true' : 'false' ); ?>,
                paymentMessage: '<?php echo esc_attr( $options['mh-payment-message'] ); ?>'
            };

            <?php if ( $options['mh-payment'] ) :
                if ( My_Home_Theme()->settings->get( 'payment-stripe' ) ) {
                    wp_enqueue_script( 'stripe-checkout', 'https://checkout.stripe.com/checkout.js' );
                }
                if ( My_Home_Theme()->settings->get( 'payment-paypal' ) ) {
                    wp_enqueue_script( 'paypal-checkout', 'https://www.paypalobjects.com/api/checkout.js' );
                }
                ?>

                <?php if ( $options['mh-payment-stripe'] ) : ?>
                    window.MyHomeStripe = {
                        name: '<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>',
                        description: '<?php esc_attr_e( '1 property listing', 'myhome' ); ?>',
                        key: '<?php echo esc_attr( $options['mh-payment-stripe-key'] ); ?>',
                        <?php if ( empty( $options['mh-payment-stripe-cost'] ) ) : ?>
                            cost: '',
                        <?php else : ?>
                            cost: <?php echo esc_attr( $options['mh-payment-stripe-cost'] ); ?>,
                        <?php endif; ?>
                        currency: '<?php echo esc_attr( $options['mh-payment-stripe-currency'] ); ?>',
                        url: '<?php echo esc_attr( admin_url( 'admin-post.php?action=stripe-payment' ) ); ?>'
                    };
                <?php endif; ?>

                <?php if ( $options['mh-payment-paypal'] ) : ?>
                    window.MyHomePayPal = {
                        name: '<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>',
                        apiKey: '<?php echo esc_attr( $options['mh-payment-paypal-public_key'] ); ?>',
                        env: '<?php echo $options['mh-payment-paypal-sandbox'] ? 'sandbox' : 'production'; ?>',
                        locale: '<?php echo esc_attr( $options['mh-payment-paypal-locale'] ); ?>',
                        <?php if ( empty( $options['mh-payment-paypal-cost'] ) ) : ?>
                            cost: '',
                        <?php else : ?>
                            cost: <?php echo esc_attr( $options['mh-payment-paypal-cost'] ); ?>,
                        <?php endif; ?>
                        currency: '<?php echo esc_attr( $options['mh-payment-paypal-currency'] ); ?>',
                        executePaymentUrl: '<?php echo esc_attr( admin_url( 'admin-post.php?action=paypal-execute' ) ); ?>'
                    };
                <?php
                endif;
            endif;
            wp_add_inline_script( 'myhome-main', ob_get_clean() );
        }

        /*
         * Inline css
         */
        $inline_css = '';
        $color_primary = My_Home_Theme()->settings->get( 'color-primary' );
        if ( ! empty( $color_primary['color'] ) ) {
            $color = $this->hex2rgb( $color_primary['color'] ) . ',0.05';
            ob_start();
            ?>
            input[type=text]:focus,
            input[type=text]:active,
            input[type=search]:focus,
            input[type=search]:active,
            input[type=password]:focus,
            input[type=password]:active,
            textarea:focus,
            textarea:active.mh-active-input input,
            .mh-active-input-primary .mh-active-input .bootstrap-select.btn-group > .btn {
            background-color: rgba(<?php echo esc_html( $color ); ?>);
            }
            <?php
            $inline_css .= ob_get_clean();
        }

        $top_bar = My_Home_Theme()->settings->get( 'top-header-style' );
        ob_start();
        if ( $top_bar == 'big' ) {
            $logo_height = My_Home_Theme()->settings->get( 'logo-top-bar_height' );
            $logo_margin_top = My_Home_Theme()->settings->get( 'logo-top-bar_margin_top' );

            if ( ! empty( $logo_height ) ) :
            ?>
            @media (min-width: 1024px) {
                .mh-top-header-big__logo img {
                    height: <?php echo esc_html( $logo_height ); ?>px!important;
                }
            }
            <?php
            endif;

            if ( ! empty( $logo_margin_top ) ) :
            ?>
            @media (min-width: 1024px) {
                .mh-top-header-big__logo img {
                    margin-top: <?php echo esc_html( $logo_margin_top ); ?>px;
                }
            }
            <?php
            endif;
        } else {
            $logo_height = My_Home_Theme()->settings->get( 'logo-height' );
            $logo_margin_top = My_Home_Theme()->settings->get( 'logo-margin_top' );

            if ( ! empty( $logo_height ) ) :
            ?>
            @media (min-width:1023px) {
                html body #mega_main_menu.mh-primary .nav_logo img {
                    height: <?php echo esc_html( $logo_height ); ?>px!important;
                }
            }
            <?php
            endif;

            if ( ! empty( $logo_margin_top ) ) :
            ?>
            @media (min-width:1023px) {
                html body #mega_main_menu.mh-primary .nav_logo img {
                    margin-top: <?php echo esc_html( $logo_margin_top ); ?>px!important;
                }
            }
            <?php
            endif;
        }
        $inline_css .= ob_get_clean();

        wp_add_inline_style( 'myhome-style', $inline_css );
    }

    private function hex2rgb( $hex ) {
        $hex = str_replace( '#', '', $hex );

        if( strlen( $hex ) == 3 ) {
            $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
            $g = hexdec( substr( $hex, 1, 1) . substr( $hex, 1, 1 ) );
            $b = hexdec( substr( $hex, 2, 1) . substr( $hex, 2, 1 ) );
        } else {
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
        }
        $rgb = array( $r, $g, $b );
        return implode( ',', $rgb );
    }

    public function fonts_url() {
        $fonts_url = '';
        $fonts     = array();
        $subsets   = 'latin,latin-ext';

        /* translators: If there are characters in your language that are not supported by this font, translate this to 'off'. Do not translate into your own language. */
        if ( 'off' !== esc_html_x( 'on', 'Lato font: on or off', 'myhome' ) ) {
            array_push( $fonts, 'Lato:400italic,300,400,700' );
        }

        /* translators: If there are characters in your language that are not supported by this font, translate this to 'off'. Do not translate into your own language. */
        if ( 'off' !== esc_html_x( 'on', 'Play font: on or off', 'myhome' ) ) {
            array_push( $fonts, 'Play:400,700' );
        }

        if ( $fonts ) {
            $fonts_url = add_query_arg( array(
                'family' => urlencode( implode( '|', $fonts ) ),
                'subset' => urlencode( $subsets ),
            ), 'https://fonts.googleapis.com/css' );
        }

        return $fonts_url;
    }

}

endif;
