<?php

/*
 * My_Home_Redux
 *
 * Setup theme option with help of ReduxFramework (https://reduxframework.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Redux' ) ) :

	class My_Home_Redux {

		// options name
		private $opt_name = 'myhome_redux';
		// option prefix
		private $prefix = 'mh-';

		public function __construct() {
			if ( ! class_exists( 'Redux' ) ) {
				return;
			}

			add_action( 'redux/loaded', array( $this, 'redux_disable_ads' ) );
			add_action( 'init', array( $this, 'init' ) );
		}

		/*
		 * init
		 *
		 * Initiate options
		 */
		public function init() {
			/*
			 * Set Arguments
			 */
			$this->set_args();
			/*
			 * Sections
			 */
			$this->set_general_options();
			$this->set_header_options();
			$this->set_typography_options();
			$this->set_blog();
			$this->set_estate_options();
			$this->set_listing_options();
			$this->set_agent_options();
			$this->set_footer_options();
			$this->set_404_options();
			Redux::init( $this->opt_name );

			if ( function_exists( 'icl_object_id' ) ) {
				$this->register_wpml_strings();
			}
		}

		/*
		 * get
		 *
		 * Get specific option
		 */
		public function get( $first_param, $second_param = null ) {
			if ( ! class_exists( 'Redux' ) ) {
				return '';
			}

			global $myhome_redux;

			$first_param = $this->prefix . $first_param;

			if ( is_null( $second_param ) ) {
				if ( isset( $myhome_redux[ $first_param ] ) ) {
					return $myhome_redux[ $first_param ];
				} else {
					return '';
				}
			} else {
				if ( isset( $myhome_redux[ $first_param ] ) && isset( $myhome_redux[ $first_param ][ $second_param ] ) ) {
					return $myhome_redux[ $first_param ][ $second_param ];
				} else {
					return '';
				}
			}
		}

		/*
		 * Set redux arguments
		 */
		public function set_args() {
			$theme = wp_get_theme();

			$args = Array(
				'opt_name'            => $this->opt_name,
				'display_name'        => $theme->get( 'Name' ),
				'display_version'     => $theme->get( 'Version' ),
				'menu_type'           => 'menu',
				'allow_sub_menu'      => true,
				'menu_title'          => esc_html__( 'Theme Options', 'myhome' ),
				'page_title'          => esc_html__( 'MyHome Options', 'myhome' ),
				'google_api_key'      => '',
				'async_typography'    => false,
				'admin_bar'           => true,
				'admin_bar_icon'      => 'dashicons-portfolio',
				'admin_bar_priority'  => 50,
				'global_variable'     => '',
				'dev_mode'            => false,
				'show_options_object' => false,
				'update_notice'       => false,
				'customizer'          => false,
				'page_priority'       => null,
				'page_parent'         => 'themes.php',
				'page_permissions'    => 'manage_options',
				'last_tab'            => '',
				'page_icon'           => 'icon-themes',
				'page_slug'           => '',
				'save_defaults'       => true,
				'default_show'        => true,
				'default_mark'        => '',
				'show_import_export'  => true,
				'transient_time'      => 60 * MINUTE_IN_SECONDS,
				'output'              => true,
				'output_tag'          => true,
				'ajax_save'           => false,
			);
			Redux::setArgs( $this->opt_name, $args );
		}

		/*
		 * set_general_options
		 *
		 * General theme options
		 */
		public function set_general_options() {
			$section = array(
				'title'  => esc_html__( 'General', 'myhome' ),
				'id'     => 'myhome-general-opts',
				'icon'   => 'el el-cog',
				'fields' => array(
					// Primary color
					array(
						'id'       => 'mh-color-primary',
						'type'     => 'color_rgba',
						'title'    => esc_html__( 'Primary color', 'myhome' ),
						'subtitle' => esc_html__( 'Set primary color for all elements', 'myhome' ),
						'output'   => array(
							'background-color' => '
                              html body.myhome-body .mh-menu-primary-color-background .mh-header:not(.mh-header--transparent) #mega_main_menu.mh-primary > .menu_holder > .menu_inner > span.nav_logo,
                              html body.myhome-body .mh-menu-primary-color-background .mh-header:not(.mh-header--transparent) #mega_main_menu.mh-primary > .menu_holder > .mmm_fullwidth_container,
                              .myhome-body .mh-thumbnail__featured,
                              .myhome-body .calendar_wrap table tbody td a:hover,
                              .myhome-body .dropdown-menu > li.selected a,
                              .myhome-body .mdl-button.mdl-button--raised.mdl-button--primary,
                              .myhome-body .mdl-button.mdl-button--primary-ghost:hover,
                              .myhome-body .mdl-button.mdl-button--primary-ghost:active,
                              .myhome-body .mdl-button.mdl-button--primary-ghost:focus,
                              .myhome-body .mdl-button.mdl-button--compare-active,
                              .myhome-body .mdl-button.mdl-button--compare-active:hover,
                              .myhome-body .mdl-button.mdl-button--compare-active:active,
                              .myhome-body .mdl-button.mdl-button--compare-active:focus,
                              .myhome-body .mh-accordion .ui-accordion-header.ui-accordion-header-active,
                              .myhome-body .mh-caption__inner,
                              .myhome-body .mh-compare__price,
                              .myhome-body .mh-estate__slider__price,
                              .myhome-body .mh-estate__details__price,
                              .myhome-body .mh-heading--top-separator:after,
                              .myhome-body .mh-heading--bottom-separator:after,
                              .myhome-body .mh-loader,
                              .myhome-body .mh-loader:before,
                              .myhome-body .mh-loader:after,
                              .myhome-body .mh-map-panel__element button:hover,
                              .myhome-body .mh-map-panel .mh-map-panel__element button.mh-button--active,
                              .myhome-body .mh-map-panel .mh-map-panel__element button.mh-button--active:hover,
                              .myhome-body .mh-map-panel .mh-map-panel__element button.mh-button--active:active,
                              .myhome-body .mh-map-panel .mh-map-panel__element button.mh-button--active:focus,
                              .myhome-body .mh-map-zoom__element button:hover,
                              .myhome-body .mh-map-infobox,
                              .myhome-body .mh-post-single__nav__prev:before,
                              .myhome-body .mh-post-single__nav__next:before,
                              .myhome-body .mh-slider__card-short__price,
                              .myhome-body .mh-slider__card-default__price,
                              .myhome-body #estate_slider_card .tparrows:hover:before,
                              .myhome-body #estate_slider_card_short .tparrows:hover:before,
                              .myhome-body #mh_rev_slider_single .tparrows:hover:before,
                              .myhome-body #mh_rev_gallery_single .tparrows:hover:before,
                              .myhome-body .mh-social-icon:hover,
                              .myhome-body .mh-top-header--primary,
                              .myhome-body .mh-top-header-big:not(.mh-top-header-big--primary) .mh-top-header-big__panel,
                              .myhome-body .mh-top-header-big.mh-top-header-big--primary,
                              .myhome-body .mh-browse-estate__row:first-child,
                              .myhome-body .mh-widget-title__text:before,
                              .myhome-body .owl-carousel .owl-dots .owl-dot.active span,
                              .myhome-body .tagcloud a:hover,
                              .myhome-body .tagcloud a:active,
                              .myhome-body .tagcloud a:focus,
                              .myhome-body .mh-menu ul li a:before,
                              .myhome-body .widget_pages ul li a:before,
                              .myhome-body .widget_meta ul li a:before,
                              .myhome-body .widget_recent_entries ul li a:before,
                              .myhome-body .widget_nav_menu ul li a:before,
                              .myhome-body .widget_categories ul li a:before,
                              .myhome-body .widget_archive ul li a:before,
                              .myhome-body .calendar_wrap table #today,
                              .myhome-body .mh-background-color-primary,
                              .myhome-body .mh-user-panel__menu ul li.mh-user-panel__menu__li--active button,
                              .myhome-body .mh-user-panel__menu ul li.mh-user-panel__menu__li--active a,
                              .myhome-body .mh-top-header--primary .mh-top-bar-user-panel__user-info,
                              .myhome-body .mh-top-header-big .mh-top-bar-user-panel__user-info,   
                              .myhome-body .wpcf7-form .wpcf7-form-control.wpcf7-submit,                          
                              .myhome-body.mh-active-input-primary .mh-search__panel > div:not(:first-child) .is-checked .mdl-radio__inner-circle
                            ',
							'border-color'     => '
                              .myhome-body blockquote,
                              .myhome-body html body .mh-menu-primary-color-background #mega_main_menu.mh-primary > .menu_holder > .mmm_fullwidth_container,
                              .myhome-body input[type=text]:focus,
                              .myhome-body input[type=text]:active,
                              .myhome-body input[type=password]:focus,
                              .myhome-body input[type=password]:active,
                              .myhome-body input[type=email]:focus,
                              .myhome-body input[type=email]:active,
                              .myhome-body input[type=search]:focus,
                              .myhome-body input[type=search]:active,
                              .myhome-body textarea:focus,
                              .myhome-body textarea:active,
                              .myhome-body .sticky,
                              .myhome-body .mh-active-input input,
                              .myhome-body .mh-active-input .bootstrap-select.btn-group > .btn,
                              .myhome-body .mdl-button.mdl-button--primary-ghost,
                              .myhome-body .mh-compare,
                              .myhome-body .tagcloud a:hover, 
                              .myhome-body .tagcloud a:active,
                              .myhome-body .tagcloud a:focus,
                              .myhome-body .mh-map-panel,
                              .myhome-body .mh-map-zoom,
                              .myhome-body .mh-map-infobox:after,
                              .myhome-body .mh-map-infobox .mh-map-infobox__img-wrapper,
                              .myhome-body .mh-search-horizontal,
                              .myhome-body .mh-search-map-top .mh-search-horizontal,
                              .myhome-body .mh-social-icon:hover:after,
                              .myhome-body .mh-top-header--primary,
                              .myhome-body .owl-carousel .owl-dots .owl-dot.active span,
                              .myhome-body .mh-border-color-primary,
                              .myhome-body .mh-post .post-content blockquote,
                              .myhome-body .mh-user-panel-info,                              
                              .myhome-body.mh-active-input-primary .mh-search__panel > div:not(:first-child) .is-checked .mdl-radio__outer-circle,
                              html body.myhome-body .mh-menu-primary-color-background .mh-header:not(.mh-header--transparent) #mega_main_menu.mh-primary > .menu_holder > .mmm_fullwidth_container
                            ',
							'color'            => '
                              .myhome-body .mh-navbar__menu ul:first-child > li:hover > a,
                              .myhome-body .mh-navbar__container .mh-navbar__menu ul:first-child > li:hover > a:first-child,
                              .myhome-body .mh-pagination a:hover,
                              .myhome-body .page-numbers.current,
                              .myhome-body .mh-footer-top--dark a:hover,
                              .myhome-body .mh-footer-top--dark a:active,
                              .myhome-body .mh-footer-top--dark a:focus,                              
                              .myhome-body .mh-active-input input,
                              .myhome-body .mh-active-input .bootstrap-select.btn-group > .btn,
                              .myhome-body .mh-active-input .bootstrap-select.btn-group .dropdown-toggle .filter-option,
                              .myhome-body .mdl-button.mdl-button--primary-ghost,
                              .myhome-body .mdl-button.mdl-button--primary-ghost:hover,
                              .myhome-body .mdl-button.mdl-button--primary-ghost:active,
                              .myhome-body .mdl-button.mdl-button--primary-ghost:focus,
                              .myhome-body .mdl-button.mdl-button--primary-font,
                              html body #mega_main_menu.mh-primary #mh-submit-button a,
                              html body.myhome-body #mega_main_menu.mh-primary #mh-submit-button a i,
                              html body.myhome-body #mega_main_menu.mh-primary > .menu_holder > .menu_inner > ul > li:hover > a:after,
                              html body.myhome-body  #mega_main_menu.mh-primary > .menu_holder > .menu_inner > ul > li:hover > .item_link *,
                              .myhome-body .comment-edit-link:hover,
                              .myhome-body .comment-reply-link:hover,
                              .myhome-body .mh-compare__feature-list li a:hover,
                              .myhome-body .mh-compare__list__element a:hover,
                              .myhome-body .mh-compare__list__element a:hover i,
                              .myhome-body .mh-estate__list__element a:hover,
                              .myhome-body .mh-estate__list__element a:hover i,
                              .myhome-body .mh-estate-horizontal__primary,
                              .myhome-body .mh-estate-vertical__primary,
                              .myhome-body .mh-filters__button.mh-filters__button--active,
                              .myhome-body .mh-filters__button.mh-filters__button--active:hover,
                              .myhome-body button.mh-filters__right__button--active,
                              .myhome-body .mh-loader-wrapper-map,
                              .myhome-body .mh-loader,
                              .myhome-body .mh-form-container__reset:hover,
                              .myhome-body .mh-map-wrapper__noresults,
                              .myhome-body .mh-map-pin i,
                              .myhome-body .mh-navbar__wrapper #mh-submit-button a:hover,
                              .myhome-body .mh-pagination--single-post,
                              .myhome-body .mh-post-single__meta a:hover,
                              .myhome-body .mh-search__heading-big,
                              .myhome-body .mh-button-transparent:hover,
                              .myhome-body .mh-user-panel__plans__row .mh-user-panel__plans__cell-4 button:hover,
                              .myhome-body .mh-browse-estate__cell-3 a:hover,
                              .myhome-body .mh-browse-estate__cell-payment a:hover,
                              .myhome-body .mh-user-pagination li:hover,
                              .myhome-body .mh-user-pagination li.mh-user-pagination__element-active,
                              .myhome-body .mh-top-header-big__element:not(.mh-top-header-big__panel) a:hover,
                              .myhome-body .mh-color-primary,
                              .myhome-body .mh-top-header:not(.mh-top-header--primary) a:hover,
                              .myhome-body .mh-top-header-big .mh-top-header-big__social-icons a:hover,                              
                              .myhome-body .mh-top-header-big .mh-top-header-big__social-icons button:hover,
                              .myhome-body .mh-estate__details > div a:hover,
                              .myhome-body .recentcomments a:hover,
                              .myhome-body .rsswidget:hover,
                              .myhome-body .mh-post .post-content a:hover,
                              .myhome-body .link-primary:hover,                              
                              .myhome-body .mh-estate__agent__content a:hover,                              
                              .myhome-body.mh-active-input-primary .mh-search__panel > div:not(:first-child) .is-checked .mdl-radio__label
                            ',
						),
						'default'  => array(
							'color' => '#29aae3',
						),
					),
					// input active color
					array(
						'id'      => 'mh-input_active_color',
						'type'    => 'select',
						'title'   => esc_html__( 'Active inputs style', 'myhome' ),
						'default' => 'mh-active-input-primary',
						'options' => array(
							'mh-active-input-primary' => esc_html__( 'Primary color', 'myhome' ),
							'mh-active-input-dark'    => esc_html__( 'Gray', 'myhome' ),
						),
					),
					// google api key, required by maps and street view
					array(
						'id'       => 'mh-google-api-key',
						'type'     => 'text',
						'title'    => esc_html__( 'Google API Key', 'myhome' ),
						'subtitle' => wp_kses_post( __( 'Following instruction with images, can be found in your documentation:<br><br>
                        1. Go https://developers.google.com/maps/documentation/javascript/ <br>
                        2. Sign in with your Google Account <br>
                        3. Click "GET A KEY" button <br>
                        4. Enter new project name <br>
                        5. Select Yes below "I agree that my use of any services and related APIs is subject to my compliance with the applicable Terms of Service." <br>
                        6. Click - "CREATE AND ENABLE API" button <br>
                        7. Copy Your API Key into this field             
                        ', 'myhome' ) ),
						'default'  => '',
					),
					array(
						'id'      => 'mh-map-style',
						'title'   => esc_html__( 'Map style', 'myhome' ),
						'type'    => 'select',
						'options' => array(
							'gray'   => esc_html__( 'MyHome gray palette', 'myhome' ),
							'google' => esc_html__( 'Default by Google', 'myhome' ),
							'custom' => esc_html__( 'Custom (Snazzy Maps)', 'myhome' ),
						),
						'default' => 'gray',
					),
					array(
						'id'       => 'mh-map-style_custom',
						'title'    => esc_html__( 'Snazzy Maps', 'myhome' ),
						'subtitle' => esc_html__( 'Visit: https://snazzymaps.com/ - find your favorite map and copy "JAVASCRIPT STYLE ARRAY"', 'myhome' ),
						'type'     => 'textarea',
						'required' => array(
							array( 'mh-map-style', '=', 'custom' ),
						),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );
		}

		/*
		 * set_typography_options
		 *
		 * Header options
		 */
		public function set_typography_options() {
			$section = array(
				'title'  => esc_html__( 'Typography', 'myhome' ),
				'id'     => 'myhome-typography-opts',
				'icon'   => 'el el-font',
				'fields' => array(
					// font default
					array(
						'id'          => 'mh-typography-default',
						'type'        => 'typography',
						'title'       => esc_html__( 'Main font', 'myhome' ),
						'google'      => true,
						'font-backup' => true,
						'font-size'   => false,
						'line-height' => false,
						'font-style'  => false,
						'text-align'  => false,
						'output'      => array(
							'
                            body,
                            button,
                            input,
                            optgroup,
                            select,
                            textarea,
                            .mh-accordion .ui-accordion-header,
                            .mh-estate-horizontal__subheading,
                            .mh-estate-horizontal__primary,
                            .mh-estate-vertical__subheading,
                            .mh-estate-vertical__primary,
                            .mh-map-infobox,
                            .mh-user-panel-info__heading,
                            .mh-font-body
                        ',
						),
						'color'       => false,
						'units'       => 'px',
						'default'     => array(
							'google'      => true,
							'font-family' => 'Lato',
							'font-weight' => '400',
							'subsets'     => 'latin-ext',
						),
					),
					// font default italic
					array(
						'id'          => 'mh-typography-default-italic',
						'type'        => 'typography',
						'title'       => esc_html__( 'Main font - italic', 'myhome' ),
						'subtitle'    => esc_html__( 'Leave empty if "Main Font" has no separate italic version.', 'myhome' ),
						'google'      => true,
						'font-backup' => true,
						'font-size'   => false,
						'line-height' => false,
						'text-align'  => false,
						'output'      => array( ' .mh-main-font-italic' ),
						'color'       => false,
						'units'       => 'px',
						'default'     => array(
							'google'      => true,
							'font-family' => 'Lato',
							'font-style'  => 'italic',
							'font-weight' => '400',
							'subsets'     => 'latin-ext',
						),
					),
					// font default bold
					array(
						'id'          => 'mh-typography-default-bold',
						'type'        => 'typography',
						'title'       => esc_html__( 'Main font - bold (700)', 'myhome' ),
						'google'      => true,
						'font-backup' => true,
						'font-size'   => false,
						'font-style'  => false,
						'line-height' => false,
						'text-align'  => false,
						'output'      => array(
							'                     
                      .mh-estate-horizontal__primary,
                      .mh-estate-vertical__primary   
                     ',
						),
						'color'       => false,
						'units'       => 'px',
						'default'     => array(
							'google'      => true,
							'font-family' => 'Lato',
							'font-weight' => '700',
							'subsets'     => 'latin-ext',
						),
					),
					// font heading
					array(
						'id'          => 'mh-typography-heading',
						'type'        => 'typography',
						'title'       => esc_html__( 'Heading font', 'myhome' ),
						'google'      => true,
						'font-backup' => true,
						'font-size'   => false,
						'line-height' => false,
						'font-style'  => false,
						'text-align'  => false,
						'output'      => array(
							'
                            h2,
                            h3,
                            h4,
                            h5,
                            h6,
                            .mh-estate__details__price,
                            .mh-top-header,
                            .mh-top-header-big__panel,                            
                            h1,
                            .mh-caption__inner,
                            .mh-slider-single__price,
                            .mh-heading-font-bold,
                            .mh-search__results,
                            .mh-user-panel__user__content
                        ',
						),
						'color'       => false,
						'units'       => 'px',
						'default'     => array(
							'google'      => true,
							'font-family' => 'Play',
							'font-weight' => '400',
							'subsets'     => 'latin-ext',
						),
					),
					// font heading bold
					array(
						'id'          => 'mh-typography-heading-bold',
						'type'        => 'typography',
						'title'       => esc_html__( 'Heading font - bold (700)', 'myhome' ),
						'google'      => true,
						'font-backup' => false,
						'font-size'   => false,
						'font-style'  => false,
						'line-height' => false,
						'text-align'  => false,
						'output'      => array(
							'
                     h1,
                     .mh-caption__inner,
                     .mh-slider-single__price,
                     .mh-heading-font-bold,
                     .mh-search__results,
                     .mh-user-panel__user__content                     
                     ',
						),
						'color'       => false,
						'units'       => 'px',
						'default'     => array(
							'google'      => true,
							'font-family' => 'Play',
							'font-weight' => '700',
							'subsets'     => 'latin-ext',
						),
					),
					// rtl support
					array(
						'id'       => 'mh-typography-rtl',
						'type'     => 'switch',
						'default'  => false,
						'title'    => esc_html__( 'RTL text direction', 'myhome' ),
						'subtitle' => esc_html__( 'Right to left text direction', 'myhome' ),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );
		}

		/*
		 * set_header_options
		 *
		 * Header options
		 */
		public function set_header_options() {
			$section = array(
				'title' => esc_html__( 'Header', 'myhome' ),
				'id'    => 'myhome-header-opts',
				'icon'  => 'el el-cog',
			);
			Redux::setSection( $this->opt_name, $section );
			/*
			 * Top bar
			 */
			$section = array(
				'title'      => esc_html__( 'Header general', 'myhome' ),
				'id'         => 'myhome-top-header-general',
				'subsection' => true,
				'fields'     => array(
					// Logo
					array(
						'id'       => 'mh-logo',
						'default'  => array( 'url' => get_template_directory_uri() . '/assets/images/logo.png' ),
						'subtitle' => esc_html__( 'This is a default logo for desktop and mobile menu', 'myhome' ),
						'type'     => 'media',
						'title'    => esc_html__( 'Logo Default', 'myhome' ),
					),
					// Logo dark
					array(
						'id'       => 'mh-logo-dark',
						'default'  => array( 'url' => get_template_directory_uri() . '/assets/images/logo-transparent-menu.png' ),
						'subtitle' => esc_html__( 'This logo will be used on pages with transparent menu only', 'myhome' ),
						'type'     => 'media',
						'title'    => esc_html__( 'Additional logo for the transparent menu', 'myhome' ),
					),
					// Top Wide
					array(
						'id'       => 'mh-top-wide',
						'type'     => 'switch',
						'title'    => esc_html__( 'Full width menu and top bars container', 'myhome' ),
						'subtitle' => esc_html__( 'By default max width is 1170px, but you can change it for fullwidth', 'myhome' ),
						'default'  => 0,
					),
					// Top Wide
					array(
						'id'       => 'mh-menu-primary',
						'type'     => 'switch',
						'title'    => esc_html__( 'Menu background "primary color"', 'myhome' ),
						'subtitle' => esc_html__( 'Change the menu background into primary color and first level items font color into white', 'myhome' ),
						'default'  => 0,
					),
					// Logo height
					array(
						'id'       => 'mh-logo-height',
						'type'     => 'text',
						'default'  => '40',
						'title'    => esc_html__( 'Logo height (px)', 'myhome' ),
						'subtitle' => esc_html__( 'Height of the default logo and logo for transparent menu.', 'myhome' ),
					),
					// Logo margin top
					array(
						'id'      => 'mh-logo-margin_top',
						'type'    => 'text',
						'default' => '0',
						'title'   => esc_html__( 'Logo Margin Top (px)', 'myhome' ),
					),
					// Menu Height
					array(
						'id'       => 'mh-menu-height',
						'type'     => 'text',
						'default'  => '80',
						'title'    => esc_html__( 'Desktop Menu Height (px)', 'myhome' ),
						'subtitle' => esc_html__( 'Mega Main Menu plugin must be active', 'myhome' ),
					),
					// First level item align
					array(
						'id'       => 'mh-menu-first-level-item-align',
						'type'     => 'select',
						'title'    => esc_html__( 'First level item align', 'myhome' ),
						'subtitle' => esc_html__( 'Mega Main Menu plugin must be active', 'myhome' ),
						'options'  => array(
							'left'   => esc_html__( 'left', 'myhome' ),
							'right'  => esc_html__( 'right', 'myhome' ),
							'center' => esc_html__( 'center', 'myhome' ),
						),
						'default'  => 'left',
					),
					// Font size of first level item
					array(
						'id'       => 'mh-menu-first-level-item-size',
						'type'     => 'text',
						'default'  => '14',
						'title'    => esc_html__( 'Font size of first level item (px)', 'myhome' ),
						'subtitle' => esc_html__( 'Mega Main Menu plugin must be active', 'myhome' ),
					),
					// Font size of the dropdown menu item
					array(
						'id'       => 'mh-menu-dropdown-item-height',
						'type'     => 'text',
						'default'  => '12',
						'title'    => esc_html__( 'Font size of the dropdown menu item (px)', 'myhome' ),
						'subtitle' => esc_html__( 'Mega Main Menu plugin must be active', 'myhome' ),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );

			/*
			 * Top bar
			 */
			$section = array(
				'title'      => esc_html__( 'Top bar', 'myhome' ),
				'id'         => 'myhome-top-header',
				'subsection' => true,
				'fields'     => array(
					// Header style
					array(
						'id'       => 'mh-top-header-style',
						'type'     => 'select',
						'title'    => esc_html__( 'Top bar', 'myhome' ),
						'subtitle' => esc_html__( 'Additional bar with contact information at the top of the menu', 'myhome' ),
						'options'  => array(
							'none'          => esc_html__( 'none', 'myhome' ),
							'small'         => esc_html__( 'Small - white background', 'myhome' ),
							'small-primary' => esc_html__( 'Small - primary color background', 'myhome' ),
							'big'           => esc_html__( 'Big - white background', 'myhome' ),
						),
						'default'  => 'small',
					),
					// Hide top bar on mobile
					array(
						'id'       => 'mh-top-header-mobile',
						'type'     => 'switch',
						'title'    => esc_html__( 'Hide top bar on mobile', 'myhome' ),
						'default'  => true,
						'required' => array(
							array( 'mh-top-header-style', '!=', 'none' ),
						),
					),
					// Logo Big
					array(
						'id'       => 'mh-logo-top-bar',
						'default'  => array( 'url' => get_template_directory_uri() . '/assets/images/logo-top-bar.png' ),
						'subtitle' => esc_html__( 'This logo will be displayed for screens larger than 1024px only. On mobile theme will still use "Default Logo"', 'myhome' ),
						'type'     => 'media',
						'title'    => esc_html__( 'Logo - Top Bar Big', 'myhome' ),
						'required' => array(
							'mh-top-header-style',
							'=',
							'big',
						),
					),
					// Logo Big height
					array(
						'id'       => 'mh-logo-top-bar_height',
						'title'    => esc_html__( 'Logo - Top Bar Big height (px)', 'myhome' ),
						'type'     => 'text',
						'default'  => '50',
						'required' => array(
							'mh-top-header-style',
							'=',
							'big',
						),
					),
					// Logo Big margin top
					array(
						'id'       => 'mh-logo-top-bar_margin_top',
						'title'    => esc_html__( 'Logo - Top Bar Big margin top (px)', 'myhome' ),
						'default'  => '0',
						'type'     => 'text',
						'required' => array(
							'mh-top-header-style',
							'=',
							'big',
						),
					),
					// Address
					array(
						'id'       => 'mh-header-address',
						'default'  => esc_html__( '518-520 5th Ave, New York, USA', 'myhome' ),
						'type'     => 'text',
						'title'    => esc_html__( 'Address', 'myhome' ),
						'required' => array(
							'mh-top-header-style',
							'!=',
							'none',
						),
					),
					// Phone
					array(
						'id'       => 'mh-header-phone',
						'default'  => esc_html__( '(123) 345-6789', 'myhome' ),
						'type'     => 'text',
						'title'    => esc_html__( 'Phone', 'myhome' ),
						'required' => array(
							'mh-top-header-style',
							'!=',
							'none',
						),
					),
					// Email
					array(
						'id'       => 'mh-header-email',
						'default'  => esc_html__( 'support@tangibledesign.net', 'myhome' ),
						'type'     => 'text',
						'title'    => esc_html__( 'Email', 'myhome' ),
						'required' => array(
							'mh-top-header-style',
							'!=',
							'none',
						),
					),
					// Facebook
					array(
						'id'       => 'mh-header-facebook',
						'default'  => esc_html__( '#', 'myhome' ),
						'type'     => 'text',
						'title'    => esc_html__( 'Facebook (URL)', 'myhome' ),
						'required' => array(
							'mh-top-header-style',
							'!=',
							'none',
						),
					),
					// Linkedin
					array(
						'id'       => 'mh-header-linkedin',
						'default'  => esc_html__( '#', 'myhome' ),
						'type'     => 'text',
						'title'    => esc_html__( 'Linkedin (URL)', 'myhome' ),
						'required' => array(
							'mh-top-header-style',
							'!=',
							'none',
						),
					),
					// Twitter
					array(
						'id'       => 'mh-header-twitter',
						'default'  => esc_html__( '#', 'myhome' ),
						'type'     => 'text',
						'title'    => esc_html__( 'Twitter (URL)', 'myhome' ),
						'required' => array(
							'mh-top-header-style',
							'!=',
							'none',
						),
					),
					// Instagram
					array(
						'id'       => 'mh-header-instagram',
						'default'  => esc_html__( '#', 'myhome' ),
						'type'     => 'text',
						'title'    => esc_html__( 'Instagram (URL)', 'myhome' ),
						'required' => array(
							'mh-top-header-style',
							'!=',
							'none',
						),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );
		}


		/*
		 * set_blog
		 *
		 * Blog related options
		 */
		public function set_blog() {
			$section = array(
				'title' => esc_html__( 'Blog', 'myhome' ),
				'id'    => 'myhome-blog-opts',
				'icon'  => 'el el-file-edit',
			);
			Redux::setSection( $this->opt_name, $section );

			/*
			 * Blog general section
			 */
			$section = array(
				'title'      => esc_html__( 'Blog general', 'myhome' ),
				'id'         => 'myhome-blog-general-opts',
				'subsection' => true,
				'fields'     => array(
					// sidebar position
					array(
						'id'      => 'mh-blog-sidebar-position',
						'type'    => 'select',
						'title'   => esc_html__( 'Sidebar position', 'myhome' ),
						'options' => array(
							'left'  => esc_html__( 'Left', 'myhome' ),
							'right' => esc_html__( 'Right', 'myhome' ),
						),
						'default' => 'right',
					),
					// archive style
					array(
						'id'      => 'mh-blog-archive-style',
						'type'    => 'select',
						'title'   => esc_html__( 'Post grid style', 'myhome' ),
						'options' => array(
							'vertical'    => esc_html__( '1 column', 'myhome' ),
							'vertical-2x' => esc_html__( '2 columns', 'myhome' ),
						),
						'default' => 'vertical',
					),
					// read more text
					array(
						'id'      => 'mh-blog-more',
						'type'    => 'text',
						'title'   => esc_html__( 'Blog: "Read More" button text', 'myhome' ),
						'default' => esc_html__( 'Read more', 'myhome' ),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );

			/*
			 * Blog Single post section
			 */
			$section = array(
				'title'      => esc_html__( 'Single post', 'myhome' ),
				'id'         => 'myhome-blog-single-opts',
				'subsection' => true,
				'fields'     => array(
					// Show author
					array(
						'id'      => 'mh-blog-show-author',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display an author', 'myhome' ),
						'default' => true,
					),
					// Show tags
					array(
						'id'      => 'mh-blog-show-tags',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display tags', 'myhome' ),
						'default' => true,
					),
					// Show posts navigation
					array(
						'id'      => 'mh-blog-show-nav',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display navigation', 'myhome' ),
						'default' => true,
					),
					// Show comments
					array(
						'id'      => 'mh-blog-show-comments',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display comments', 'myhome' ),
						'default' => true,
					),
					// Show related posts
					array(
						'id'      => 'mh-blog-show-related',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display related posts', 'myhome' ),
						'default' => false,
					),
					// Related posts number
					array(
						'id'       => 'mh-blog-related-number',
						'type'     => 'text',
						'title'    => esc_html__( 'Total number of related posts to display', 'myhome' ),
						'default'  => '4',
						'required' => array(
							'mh-blog-show-related',
							'=',
							1,
						),
					),
					// Related posts style
					array(
						'id'       => 'mh-blog-related-style',
						'type'     => 'select',
						'title'    => esc_html__( 'Related posts style', 'myhome' ),
						'options'  => array(
							'vertical'    => esc_html__( '1 column', 'myhome' ),
							'vertical-2x' => esc_html__( '2 columns', 'myhome' ),
						),
						'default'  => 'vertical-2x',
						'required' => array(
							'mh-blog-show-related',
							'=',
							1,
						),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );

			/*
			 * Blog Top title section
			 */
			$section = array(
				'title'      => esc_html__( 'Blog top title', 'myhome' ),
				'id'         => 'myhome-top-title',
				'subsection' => true,
				'fields'     => array(
					// show top title
					array(
						'id'      => 'mh-top-title-show',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display top title on blog', 'myhome' ),
						'default' => 1,
					),
					// Top title style
					array(
						'id'       => 'mh-top-title-style',
						'type'     => 'select',
						'title'    => esc_html__( 'Blog top title style', 'myhome' ),
						'options'  => array(
							'default' => esc_html__( 'Gray', 'myhome' ),
							'image'   => esc_html__( 'Image', 'myhome' ),
						),
						'default'  => 'image',
						'required' => array(
							'mh-top-title-show',
							'=',
							'1',
						),
					),
					// Top title background
					array(
						'id'       => 'mh-top-title-background-image-url',
						'type'     => 'media',
						'default'  => array( 'url' => get_template_directory_uri() . '/assets/images/top-title.jpg' ),
						'title'    => esc_html__( 'Upload background image', 'myhome' ),
						'required' => array(
							'mh-top-title-style',
							'=',
							array( 'image' ),
						),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );
		}

		/*
		 * set_estate_options
		 *
		 * Estate options
		 */
		public function set_estate_options() {
			$offer_types_list = array();
			if ( class_exists( 'My_Home_Core' ) ) {
				$offer_type_slug = My_Home_Core()->attributes->get_offer_type_slug();
				$offer_types     = My_Home_Term::get( $offer_type_slug );
				foreach ( $offer_types as $offer_type ) {
					$offer_types_list[ $offer_type->name ] = $offer_type->name;
				}
			}

			$section = array(
				'title' => esc_html__( 'Property options', 'myhome' ),
				'id'    => 'myhome-estate-opts',
				'icon'  => 'el el-home',
			);
			Redux::setSection( $this->opt_name, $section );

			/*
			 * General section
			 */
			$section = array(
				'title'      => esc_html__( 'General', 'myhome' ),
				'id'         => 'myhome-estate-general-opts',
				'subsection' => true,
				'fields'     => array(
					// set slider for gallery on single estate page
					array(
						'id'      => 'mh-estate_slider',
						'type'    => 'select',
						'default' => 'single-estate-gallery',
						'title'   => esc_html__( 'Single property style', 'myhome' ),
						'options' => array(
							'single-estate-gallery' => esc_html__( 'Gallery', 'myhome' ),
							'single-estate-slider'  => esc_html__( 'Slider', 'myhome' ),
						),
					),
					// Show estate video
					array(
						'id'      => 'mh-estate_video',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display property video', 'myhome' ),
						'default' => true,
					),
					// Show estate virtual tour
					array(
						'id'      => 'mh-estate_virtual_tour',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display property virtual tour', 'myhome' ),
						'default' => true,
					),
					// Show estate plans
					array(
						'id'      => 'mh-estate_plans',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display property plans', 'myhome' ),
						'default' => true,
					),
					// Show sidebar
					array(
						'id'      => 'mh-estate_sidebar',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display sidebar', 'myhome' ),
						'default' => true,
					),
					// Show sidebar contact form
					array(
						'id'       => 'mh-estate_sidebar_contact_form',
						'type'     => 'switch',
						'title'    => esc_html__( 'Display sidebar - contact form', 'myhome' ),
						'default'  => true,
						'required' => array(
							array( 'mh-estate_sidebar', '=', true ),
						),
					),
					// Show sidebar agent
					array(
						'id'       => 'mh-estate_sidebar_user_profile',
						'type'     => 'switch',
						'title'    => esc_html__( 'Display sidebar - user profile', 'myhome' ),
						'default'  => true,
						'required' => array(
							array( 'mh-estate_sidebar', '=', true ),
						),
					),
					// slug for estate post type
					array(
						'id'       => 'mh-estate-slug',
						'type'     => 'text',
						'title'    => esc_html__( 'Slug', 'myhome' ),
						'subtitle' => esc_html__( 'Change slug from http://yourdomain/estate/estate-name to http://yourdomain/newslug/estate-name', 'myhome' ),
						'default'  => 'estate',
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );

			$section = array(
				'title'      => esc_html__( 'Offer type', 'myhome' ),
				'id'         => 'myhome-offer-type-opt',
				'subsection' => true,
				'fields'     => array(
					// use offer type
					array(
						'id'      => 'mh-offer_type',
						'type'    => 'switch',
						'title'   => esc_html__( 'Offer type', 'myhome' ),
						'default' => true,
					),
					// rent label
					array(
						'id'       => 'mh-estate_rent_label',
						'type'     => 'text',
						'title'    => esc_html__( 'Rent label', 'myhome' ),
						'default'  => esc_html__( '/month', 'myhome' ),
						'required' => array(
							array( 'mh-offer_type', '=', true ),
						),
					),
					// set for which value display monthly price
					array(
						'id'       => 'mh-estate_rent',
						'type'     => 'select',
						'title'    => esc_html__( 'Assign "Rent label" to appropriate option', 'myhome' ),
						'default'  => esc_html__( 'For Rent', 'myhome' ),
						'options'  => $offer_types_list,
						'required' => array(
							array( 'mh-offer_type', '=', true ),
						),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );

			$section = array(
				'title'      => esc_html__( 'Currency', 'myhome' ),
				'id'         => 'myhome-currency-opts',
				'subsection' => true,
				'fields'     => array(
					// set currency sign
					array(
						'id'      => 'mh-estate-currency_sign',
						'type'    => 'text',
						'title'   => esc_html__( 'Currency sign', 'myhome' ),
						'default' => '$',
					),
					// where display currency sign
					array(
						'id'      => 'mh-estate-currency_location',
						'type'    => 'select',
						'title'   => esc_html__( 'Currency sign location', 'myhome' ),
						'options' => array(
							'after_price'  => esc_html__( 'After price', 'myhome' ),
							'before_price' => esc_html__( 'Before price', 'myhome' ),
						),
						'default' => 'before_price',
					),
					// thousands separator
					array(
						'id'      => 'mh-estate-price_thousands_sep',
						'type'    => 'text',
						'title'   => esc_html__( 'Thousands separator', 'myhome' ),
						'default' => '.',
					),
					// decimal separator
					array(
						'id'      => 'mh-estate-price_decimal_sep',
						'type'    => 'text',
						'title'   => esc_html__( 'Decimal separator', 'myhome' ),
						'default' => ',',
					),
					// decimal
					array(
						'id'       => 'mh-estate-price_decimal',
						'type'     => 'text',
						'title'    => esc_html__( 'Decimal', 'myhome' ),
						'subtitle' => esc_html__( 'Sets the number of decimal points.', 'myhome' ),
						'default'  => '0',
					),
				),
			);

			Redux::setSection( $this->opt_name, $section );

			$section = array(
				'title'      => esc_html__( 'Show near', 'myhome' ),
				'id'         => 'myhome-near-by-opts',
				'subsection' => true,
				'fields'     => array(
					array(
						'id'      => 'mh-estate-show_near_active',
						'type'    => 'switch',
						'title'   => esc_html__( 'Active at start', 'myhome' ),
						'default' => false,
					),
					// distance units, important for near estates radius
					array(
						'id'      => 'mh-estate-distance_unit',
						'type'    => 'select',
						'title'   => esc_html__( 'Distance unit', 'myhome' ),
						'options' => array(
							'km'    => esc_html__( 'km', 'myhome' ),
							'miles' => esc_html__( 'miles', 'myhome' ),
						),
						'default' => 'miles',
					),
					// range for near estates feature
					array(
						'id'       => 'mh-estate-near_estates_range',
						'type'     => 'text',
						'title'    => esc_html__( '"show near" radius (unit set above)', 'myhome' ),
						'subtitle' => esc_html__( 'Properties around the selected pin with the set radius will be displayed after clicking "Show near" button.', 'myhome' ),
						'default'  => '20',
					),
				),
			);

			Redux::setSection( $this->opt_name, $section );
		}

		/*
		 * Agents options
		 */
		public function set_agent_options() {
			$section = array(
				'title' => esc_html__( 'Agents and payment', 'myhome' ),
				'id'    => 'myhome-agents-opts',
				'icon'  => 'el el-user',
			);
			Redux::setSection( $this->opt_name, $section );

			$section = array(
				'title'      => esc_html__( 'General', 'myhome' ),
				'id'         => 'myhome-agents-general',
				'subsection' => true,
				'fields'     => array(
					// Disable backend for agent role
					array(
						'id'      => 'mh-agent-disable_backend',
						'type'    => 'switch',
						'title'   => esc_html__( 'Disable backend for agent user', 'myhome' ),
						'default' => true,
					),
					// Enable frontend agent panel
					array(
						'id'      => 'mh-agent-panel',
						'type'    => 'switch',
						'title'   => esc_html__( 'Agent frontend panel', 'myhome' ),
						'default' => true,
					),
					// Show submit property button
					array(
						'id'       => 'mh-agent-submit_property',
						'type'     => 'switch',
						'title'    => esc_html__( 'Show submit property button', 'myhome' ),
						'required' => array(
							array( 'mh-agent-panel', '=', true ),
						),
						'default'  => true,
					),
					// Agent panel page link
					array(
						'id'       => 'mh-agent-panel_link',
						'type'     => 'text',
						'title'    => esc_html__( 'Panel page URL', 'myhome' ),
						'subtitle' => esc_html__( 'Usually: http://yourdomain/panel/', 'myhome' ),
						'default'  => '',
						'required' => array(
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// Disable frontend registration
					array(
						'id'       => 'mh-agent-registration',
						'type'     => 'switch',
						'title'    => esc_html__( 'Frontend registration', 'myhome' ),
						'required' => array(
							array( 'mh-agent-panel', '=', true ),
						),
						'default'  => false,
					),
					// Moderation
					array(
						'id'       => 'mh-agent-moderation',
						'type'     => 'switch',
						'title'    => esc_html__( 'Moderation', 'myhome' ),
						'subtitle' => esc_html__( 'If it is on, property added by user must be accepted by admin to show', 'myhome' ),
						'default'  => true,
						'required' => array(
							array( 'mh-agent-panel', '=', true ),
						),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );

			$section = array(
				'title'      => esc_html__( 'Agents', 'myhome' ),
				'id'         => 'myhome-agents',
				'subsection' => true,
				'fields'     => array(
					// phone
					array(
						'id'      => 'mh-agent-phone',
						'type'    => 'switch',
						'title'   => esc_html__( 'Phone', 'myhome' ),
						'default' => true,
					),
					// show email
					array(
						'id'      => 'mh-agent-email_show',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display email', 'myhome' ),
						'default' => true,
					),
					// facebook
					array(
						'id'      => 'mh-agent-facebook',
						'type'    => 'switch',
						'title'   => esc_html__( 'Facebook', 'myhome' ),
						'default' => true,
					),
					// twitter
					array(
						'id'      => 'mh-agent-twitter',
						'type'    => 'switch',
						'title'   => esc_html__( 'Twitter', 'myhome' ),
						'default' => true,
					),
					// instagram
					array(
						'id'      => 'mh-agent-instagram',
						'type'    => 'switch',
						'title'   => esc_html__( 'Instagram', 'myhome' ),
						'default' => true,
					),
					// linkedin
					array(
						'id'      => 'mh-agent-linkedin',
						'type'    => 'switch',
						'title'   => esc_html__( 'Linkedin', 'myhome' ),
						'default' => true,
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );

			$section = array(
				'title'      => esc_html__( 'Payments', 'myhome' ),
				'id'         => 'myhome-payments-general',
				'subsection' => true,
				'fields'     => array(
					// Payment module
					array(
						'id'       => 'mh-payment',
						'title'    => esc_html__( 'Payment module', 'myhome' ),
						'type'     => 'switch',
						'default'  => false,
						'required' => array(
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// Above payment methods message
					array(
						'id'       => 'mh-payment-message',
						'title'    => esc_html__( 'Message above payment methods', 'myhome' ),
						'type'     => 'text',
						'default'  => '',
						'required' => array(
							array( 'mh-agent-panel', '=', true ),
							array( 'mh-payment', '=', true ),
						),
					),
					// Stripe payments
					array(
						'id'       => 'mh-payment-stripe',
						'title'    => esc_html__( 'Stripe payment', 'myhome' ),
						'type'     => 'switch',
						'default'  => false,
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// Stripe currency
					array(
						'id'       => 'mh-payment-stripe-currency',
						'title'    => esc_html__( 'Stripe currency', 'myhome' ),
						'type'     => 'text',
						'default'  => 'usd',
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-stripe', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// Stripe Cost
					array(
						'id'       => 'mh-payment-stripe-cost',
						'title'    => esc_html__( 'Stripe Cost', 'myhome' ),
						'type'     => 'text',
						'default'  => '',
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-stripe', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// Stripe key
					array(
						'id'       => 'mh-payment-stripe-secret_key',
						'title'    => esc_html__( 'Stripe Secret Key', 'myhome' ),
						'type'     => 'text',
						'default'  => '',
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-stripe', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// Stripe key
					array(
						'id'       => 'mh-payment-stripe-key',
						'title'    => esc_html__( 'Stripe Publishable Key', 'myhome' ),
						'type'     => 'text',
						'default'  => '',
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-stripe', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// Paypal payments
					array(
						'id'       => 'mh-payment-paypal',
						'title'    => esc_html__( 'PayPal Payment', 'myhome' ),
						'type'     => 'switch',
						'default'  => false,
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// Paypal locale
					array(
						'id'       => 'mh-payment-paypal-locale',
						'title'    => esc_html__( 'PayPal locale', 'myhome' ),
						'type'     => 'select',
						'default'  => 'en_US',
						'options'  => array(
							'en_US' => 'en_US',
							'en_AU' => 'en_AU',
							'da_DK' => 'da_DK',
							'fr_FR' => 'fr_FR',
							'fr_CA' => 'fr_CA',
							'de_DE' => 'de_DE',
							'en_GB' => 'en_GB',
							'zh_HK' => 'zh_HK',
							'it_IT' => 'it_IT',
							'nl_NL' => 'nl_NL',
							'no_NO' => 'no_NO',
							'pl_PL' => 'pl_PL',
							'es_ES' => 'es_ES',
							'sv_SE' => 'sv_SE',
							'tr_TR' => 'tr_TR',
							'pt_BR' => 'pt_BR',
							'ja_JP' => 'ja_JP',
							'id_ID' => 'id_ID',
							'ko_KR' => 'ko_KR',
							'pt_PT' => 'pt_PT',
							'ru_RU' => 'ru_RU',
							'th_TH' => 'th_TH',
							'zh_CN' => 'zh_CN',
							'zh_TW' => 'zh_TW',
						),
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-paypal', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// PayPal Client ID
					array(
						'id'       => 'mh-payment-paypal-public_key',
						'title'    => esc_html__( 'PayPal Client ID', 'myhome' ),
						'type'     => 'text',
						'default'  => '',
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-paypal', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// PayPal Secret
					array(
						'id'       => 'mh-payment-paypal-secret_key',
						'title'    => esc_html__( 'PayPal Secret', 'myhome' ),
						'type'     => 'text',
						'default'  => '',
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-paypal', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// PayPal sandbox mode
					array(
						'id'       => 'mh-payment-paypal-sandbox',
						'title'    => esc_html__( 'PayPal Sandbox Mode', 'myhome' ),
						'type'     => 'switch',
						'default'  => true,
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-paypal', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// PayPal currency
					array(
						'id'       => 'mh-payment-paypal-currency',
						'title'    => esc_html__( 'PayPal Currency', 'myhome' ),
						'type'     => 'text',
						'default'  => 'USD',
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-paypal', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
					// PayPal cost
					array(
						'id'       => 'mh-payment-paypal-cost',
						'title'    => esc_html__( 'PayPal Cost', 'myhome' ),
						'type'     => 'text',
						'required' => array(
							array( 'mh-payment', '=', true ),
							array( 'mh-payment-paypal', '=', true ),
							array( 'mh-agent-panel', '=', true ),
						),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );
		}

		/*
		 * Listing options
		 */
		public function set_listing_options() {
			$fields = array(
				// initial card view
				array(
					'id'      => 'mh-listing-default_view',
					'type'    => 'select',
					'title'   => esc_html__( 'Default view', 'myhome' ),
					'default' => 'colTwo',
					'options' => array(
						'colTwo'   => esc_html__( 'Two columns', 'myhome' ),
						'colThree' => esc_html__( 'Three columns', 'myhome' ),
						'row'      => esc_html__( 'Row', 'myhome' ),
					),
				),
				// estates per page
				array(
					'id'      => 'mh-listing-estates_limit',
					'type'    => 'text',
					'title'   => esc_html__( 'Properties limit', 'myhome' ),
					'default' => '6',
				),
				// lazy loading
				array(
					'id'      => 'mh-listing-lazy_loading',
					'type'    => 'switch',
					'title'   => esc_html__( 'Lazy loading', 'myhome' ),
					'default' => true,
				),
				// when show load more button
				array(
					'id'       => 'mh-listing-load_more_button_number',
					'type'     => 'text',
					'title'    => esc_html__( 'Show load more button after N loads', 'myhome' ),
					'default'  => '2',
					'required' => array(
						'mh-listing-lazy_loading',
						'=',
						1,
					),
				),
				// load more button label
				array(
					'id'      => 'mh-listing-load_more_button_label',
					'type'    => 'text',
					'title'   => esc_html__( 'Load more button label', 'myhome' ),
					'default' => esc_html__( 'Load more', 'myhome' ),
				),
				// load preview button label
				array(
					'id'      => 'mh-listing-load_prev_button_label',
					'type'    => 'text',
					'title'   => esc_html__( 'Load previous button label', 'myhome' ),
					'default' => esc_html__( 'Load previous', 'myhome' ),
				),
				// search form position
				array(
					'id'       => 'mh-listing-search_form_position',
					'type'     => 'select',
					'title'    => esc_html__( 'Search form position', 'myhome' ),
					'subtitle' => esc_html__( 'Not applicable for Agents', 'myhome' ),
					'default'  => 'left',
					'options'  => array(
						'left'  => esc_html__( 'Left', 'myhome' ),
						'right' => esc_html__( 'Right', 'myhome' ),
						'top'   => esc_html__( 'Top', 'myhome' ),
					),
				),
				// listing label
				array(
					'id'       => 'mh-listing-label',
					'type'     => 'text',
					'title'    => esc_html__( 'Label', 'myhome' ),
					'default'  => '',
					'required' => array(
						array( 'mh-listing-search_form_position', '!=', 'left' ),
						array( 'mh-listing-search_form_position', '!=', 'right' ),
					),
				),
				// advanced number
				array(
					'id'       => 'mh-listing-search_form_advanced_number',
					'type'     => 'text',
					'default'  => 3,
					'title'    => esc_html__( 'Number of filters to show before the "Advanced" button', 'myhome' ),
					'required' => array(
						array( 'mh-listing-search_form_position', '!=', 'left' ),
						array( 'mh-listing-search_form_position', '!=', 'right' ),
					),
				),
				// Show advanced
				array(
					'id'       => 'mh-listing-show_advanced',
					'type'     => 'switch',
					'default'  => true,
					'title'    => esc_html__( 'Display "advanced" button', 'myhome' ),
					'required' => array(
						array( 'mh-listing-search_form_position', '!=', 'left' ),
						array( 'mh-listing-search_form_position', '!=', 'right' ),
					),
				),
				// Show advanced
				array(
					'id'       => 'mh-listing-show_clear',
					'type'     => 'switch',
					'default'  => true,
					'title'    => esc_html__( 'Display "clear" button', 'myhome' ),
					'required' => array(
						array( 'mh-listing-search_form_position', '!=', 'left' ),
						array( 'mh-listing-search_form_position', '!=', 'right' ),
					),
				),
				// Show sort by
				array(
					'id'      => 'mh-listing-show_sort_by',
					'type'    => 'switch',
					'default' => true,
					'title'   => esc_html__( 'Display "sort by"', 'myhome' ),
				),
				// Show view types
				array(
					'id'      => 'mh-listing-show_view_types',
					'type'    => 'switch',
					'default' => true,
					'title'   => esc_html__( 'Display "view types"', 'myhome' ),
				),
			);

			// setup attribute options
			if ( class_exists( 'My_Home_Attribute' ) ) {
				foreach ( My_Home_Attribute::get_attributes() as $attribute ) {
					// set if display this attribute on listing search form
					array_push( $fields, array(
						'id'       => 'mh-listing-' . $attribute->get_slug() . '_show',
						'type'     => 'switch',
						'title'    => sprintf( esc_html__( 'Show %s filter', 'myhome' ), $attribute->get_name() ),
						'subtitle' => esc_html__( 'Not applicable for Agents', 'myhome' ),
						'default'  => true,
					) );
				}
			}

			$section = array(
				'title'      => esc_html__( 'Listings', 'myhome' ),
				'id'         => 'myhome-listing-opts',
				'subsection' => true,
				'desc'       => esc_html__( "Below options will change Single Agent Page and Single Attribute Page (eg. property type, city).
                 It will not influence on any Visual Composer Element eg. Homepages / Maps.", 'myhome' ),
				'fields'     => $fields,
			);
			Redux::setSection( $this->opt_name, $section );
		}

		/*
		 * Footer options
		 */
		public function set_footer_options() {
			$section = array(
				'title'  => esc_html__( 'Footer', 'myhome' ),
				'id'     => 'myhome-footer-opts',
				'icon'   => 'el el-cog',
				'fields' => array(
					// footer style
					array(
						'id'      => 'mh-footer-style',
						'type'    => 'select',
						'title'   => esc_html__( 'Footer Style', 'myhome' ),
						'options' => array(
							'light' => esc_html__( 'Light background', 'myhome' ),
							'dark'  => esc_html__( 'Dark background', 'myhome' ),
							'image' => esc_html__( 'Image background', 'myhome' ),
						),
						'default' => 'dark',
					),
					// Footer image
					array(
						'id'       => 'mh-footer-background-image-url',
						'type'     => 'media',
						'default'  => array( 'url' => get_template_directory_uri() . '/assets/images/footer-background.jpg' ),
						'title'    => esc_html__( 'Upload Background Image', 'myhome' ),
						'required' => array(
							'mh-footer-style',
							'=',
							array( 'image' ),
						),
					),
					// Footer image as parallax
					array(
						'id'       => 'mh-footer-background-image-parallax',
						'type'     => 'switch',
						'title'    => esc_html__( 'Background Image Parallax', 'myhome' ),
						'default'  => 1,
						'required' => array(
							'mh-footer-style',
							'=',
							array( 'image' ),
						),
					),
					// Display widget area
					array(
						'id'      => 'mh-footer-widget-area-show',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display Widget Area', 'myhome' ),
						'default' => 1,
					),
					// Display Footer information
					array(
						'id'       => 'mh-footer-widget-area-footer-information',
						'type'     => 'switch',
						'title'    => esc_html__( 'Display Footer Widget', 'myhome' ),
						'default'  => 1,
						'required' => array(
							'mh-footer-widget-area-show',
							'=',
							1,
						),
					),
					// Logo
					array(
						'id'       => 'mh-footer-logo',
						'type'     => 'media',
						'default'  => array( 'url' => get_template_directory_uri() . '/assets/images/logo-footer.png' ),
						'title'    => esc_html__( 'Upload Footer Widget logo', 'myhome' ),
						'required' => array(
							'mh-footer-widget-area-footer-information',
							'=',
							1,
						),
					),
					// Information
					array(
						'id'       => 'mh-footer-text',
						'type'     => 'text',
						'title'    => esc_html__( 'Edit Footer Widget Text', 'myhome' ),
						'default'  => esc_html__( 'After a time we drew near the road, and as we did so we heard the clatter of hoofs and saw through the tree stems three cavalry soldiers riding slowly towards Woking.', 'myhome' ),
						'required' => array(
							'mh-footer-widget-area-footer-information',
							'=',
							1,
						),
					),
					// Phone
					array(
						'id'       => 'mh-footer-phone',
						'type'     => 'text',
						'title'    => esc_html__( 'Edit Footer Widget phone', 'myhome' ),
						'default'  => esc_html__( '(123) 345-6789', 'myhome' ),
						'required' => array(
							'mh-footer-widget-area-footer-information',
							'=',
							1,
						),
					),
					// Email
					array(
						'id'       => 'mh-footer-email',
						'type'     => 'text',
						'title'    => esc_html__( 'Edit Footer Widget email', 'myhome' ),
						'default'  => esc_html__( 'support@tangibledesing.net', 'myhome' ),
						'required' => array(
							'mh-footer-widget-area-footer-information',
							'=',
							1,
						),
					),
					// Address
					array(
						'id'       => 'mh-footer-address',
						'type'     => 'text',
						'title'    => esc_html__( 'Edit Footer Widget address', 'myhome' ),
						'default'  => esc_html__( '518-520 5th Ave, New York, USA', 'myhome' ),
						'required' => array(
							'mh-footer-widget-area-footer-information',
							'=',
							1,
						),
					),
					// Display copyrights
					array(
						'id'      => 'mh-footer-copyright-area-show',
						'type'    => 'switch',
						'title'   => esc_html__( 'Display Copyright Information', 'myhome' ),
						'default' => 1,
					),
					// Copyrights
					array(
						'id'       => 'mh-footer-copyright-text',
						'type'     => 'text',
						'title'    => esc_html__( 'Edit copyright text', 'myhome' ),
						'default'  => esc_html__( '2017 MyHome by TangibleDesign. All rights reserved.', 'myhome' ),
						'required' => array(
							'mh-footer-copyright-area-show',
							'=',
							1,
						),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );
		}


		/*
		 * 404 options
		 */
		public function set_404_options() {
			$section = array(
				'title'  => esc_html__( '404', 'myhome' ),
				'id'     => 'myhome-404-opts',
				'icon'   => 'el el-warning-sign',
				'fields' => array(
					// Error 404 Heading
					array(
						'id'      => 'mh-404-heading',
						'type'    => 'text',
						'title'   => esc_html__( 'Title', 'myhome' ),
						'default' => esc_html__( '404', 'myhome' ),
					),
					// Error 404 Text
					array(
						'id'      => 'mh-404-text',
						'type'    => 'text',
						'title'   => esc_html__( 'Subtitle', 'myhome' ),
						'default' => esc_html__( 'Page not found', 'myhome' ),
					),
				),
			);
			Redux::setSection( $this->opt_name, $section );
		}

		/*
		 * redux_disable_ads
		 *
		 * Disable redux ads
		 */
		public function redux_disable_ads( $redux ) {
			$redux->args['dev_mode'] = false;
		}

		public function register_wpml_strings() {
			$strings = array(
				// Header
				(object) array(
					'context' => esc_html__( 'Header address', 'myhome' ),
					'name'    => 'mh-header-address',
				),
				(object) array(
					'context' => esc_html__( 'Header phone', 'myhome' ),
					'name'    => 'mh-header-phone',
				),
				(object) array(
					'context' => esc_html__( 'Header email', 'myhome' ),
					'name'    => 'mh-header-email',
				),
				(object) array(
					'context' => esc_html__( 'Header Facebook', 'myhome' ),
					'name'    => 'mh-header-facebook',
				),
				(object) array(
					'context' => esc_html__( 'Header Twitter', 'myhome' ),
					'name'    => 'mh-header-twitter',
				),
				(object) array(
					'context' => esc_html__( 'Header Linkedin', 'myhome' ),
					'name'    => 'mh-header-linkedin',
				),
				(object) array(
					'context' => esc_html__( 'Header Instagram', 'myhome' ),
					'name'    => 'mh-header-instagram',
				),
				// Footer
				(object) array(
					'context' => esc_html__( 'Footer address', 'myhome' ),
					'name'    => 'mh-footer-address',
				),
				(object) array(
					'context' => esc_html__( 'Footer phone', 'myhome' ),
					'name'    => 'mh-footer-phone',
				),
				(object) array(
					'context' => esc_html__( 'Footer email', 'myhome' ),
					'name'    => 'mh-footer-email',
				),
				(object) array(
					'context' => esc_html__( 'Footer text', 'myhome' ),
					'name'    => 'mh-footer-text',
				),
				(object) array(
					'context' => esc_html__( 'Footer copyright text', 'myhome' ),
					'name'    => 'mh-footer-copyright-text',
				),
				// Blog
				(object) array(
					'context' => esc_html__( 'Blog read more text', 'myhome' ),
					'name'    => 'mh-blog-more',
				),
				// Properties
				(object) array(
					'context' => esc_html__( 'Offer type - rent label (/month)', 'myhome' ),
					'name'    => 'mh-estate_rent_label',
				),
				(object) array(
					'context' => esc_html__( 'Load more (button label)', 'myhome' ),
					'name'    => 'mh-listing-load_more_button_label',
				),
				(object) array(
					'context' => esc_html__( 'Load previous (button label)', 'myhome' ),
					'name'    => 'mh-listing-load_prev_button_label',
				),
				// Payments
				(object) array(
					'context' => esc_html__( 'Payments - message above payment methods', 'myhome' ),
					'name'    => 'mh-payment-message',
				),
			);

			global $myhome_redux;
			foreach ( $strings as $string ) {
				do_action( 'wpml_register_single_string', 'MyHome - Settings', $string->context, $myhome_redux[ $string->name ] );
			}
		}
	}

endif;
