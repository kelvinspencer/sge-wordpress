<?php
/*
 * My_Home_Shortcodes
 *
 * Register shortcodes and visual composer elements.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Shortcodes' ) ) :

class My_Home_Shortcodes {

	// vars
	private $shortcodes;

	public function __construct() {
		$this->shortcodes = array(
            // MH Listing
            array(
                'name'  => 'mh_listing',
                'class' => 'My_Home_Listing_Shortcode',
                'file'  => 'class-myhome-listing-shortcode.php'
            ),
            // MH Listing Map
            array(
                'name'  => 'mh_listing_map',
                'class' => 'My_Home_Listing_Map_Shortcode',
                'file'  => 'class-myhome-listing-map-shortcode.php'
            ),
            // MH Estate Map
            array(
                'name'  => 'mh_estate_map',
                'class' => 'My_Home_Estate_Map_Shortcode',
                'file'  => 'class-myhome-estate-map-shortcode.php'
            ),
            // MH Service
            array(
                'name'  => 'mh_service',
                'class' => 'My_Home_Service_Shortcode',
                'file'  => 'class-myhome-service-shortcode.php'
            ),
            // MH Heading
            array(
                'name'  => 'mh_heading',
                'class' => 'My_Home_Heading_Shortcode',
                'file'  => 'class-myhome-heading-shortcode.php'
            ),
            // MH Icon
            array(
                'name'  => 'mh_icon',
                'class' => 'My_Home_Icon_Shortcode',
                'file'  => 'class-myhome-icon-shortcode.php'
            ),
            // MH Simple Box
            array(
                'name'  => 'mh_simple_box',
                'class' => 'My_Home_Simple_Box_Shortcode',
                'file'  => 'class-myhome-simple-box-shortcode.php'
            ),
            // MH Button
            array(
                'name'  => 'mh_button',
                'class' => 'My_Home_Button_Shortcode',
                'file'  => 'class-myhome-button-shortcode.php'
            ),
            // MH Attribute Carousel
            array(
                'name'  => 'mh_carousel_attribute',
                'class' => 'My_Home_Carousel_Attribute_Shortcode',
                'file'  => 'class-myhome-carousel-attribute-shortcode.php'
            ),
            // MH Carousel Post
            array(
                'name'  => 'mh_carousel_post',
                'class' => 'My_Home_Carousel_Post_Shortcode',
                'file'  => 'class-myhome-carousel-post-shortcode.php'
            ),
			// MH Carousel Estate
            array(
                'name'  => 'mh_carousel_estate',
                'class' => 'My_Home_Carousel_Estate_Shortcode',
                'file'  => 'class-myhome-carousel-estate-shortcode.php'
            ),
            // MH Slider Estate
            array(
                'name'  => 'mh_slider_estate',
                'class' => 'My_Home_Slider_Estate_Shortcode',
                'file'  => 'class-myhome-slider-estate-shortcode.php'
            ),
            // MH Slider
            array(
                'name'  => 'mh_slider',
                'class' => 'My_Home_Slider_Shortcode',
                'file'  => 'class-myhome-slider-shortcode.php'
            ),
            // MH Carousel Agent
            array(
                'name'  => 'mh_carousel_agent',
                'class' => 'My_Home_Carousel_Agent_Shortcode',
                'file'  => 'class-myhome-carousel-agent-shortcode.php'
            ),
            // MH Testimonials
            array(
                'name'  => 'mh_carousel_testimonials',
                'class' => 'My_Home_Carousel_Testimonials_Shortcode',
                'file'  => 'class-myhome-carousel-testimonials-shortcode.php'
            ),
            // MH Clients
            array(
                'name'  => 'mh_carousel_clients',
                'class' => 'My_Home_Carousel_Clients_Shortcode',
                'file'  => 'class-myhome-carousel-clients-shortcode.php'
            ),
		);

		$this->init();
	}

	private function init() {
		foreach( $this->shortcodes as $shortcode ) {
			require_once plugin_dir_path( __FILE__ ) . 'shortcodes/' . $shortcode['file'];
			add_shortcode( $shortcode['name'], array( $this, $shortcode['name'] ) );
		}
	}

	public function get() {
		return $this->shortcodes;
	}

	public function mh_listing( $atts ) {
		$listing = new My_Home_Listing_Shortcode();
		return $listing->init( $atts );
	}

	public function mh_listing_map( $atts ) {
		$listing_map = new My_Home_Listing_Map_Shortcode();
		return $listing_map->init( $atts );
	}

    public function mh_service( $atts, $content ) {
        $grid = new My_Home_Service_Shortcode();
        return $grid->init( $atts, $content );
    }

    public function mh_icon( $atts ) {
        $grid = new My_Home_Icon_Shortcode();
        return $grid->init( $atts );
    }

    public function mh_heading( $atts ) {
        $grid = new My_Home_Heading_Shortcode();
        return $grid->init( $atts );
    }

    public function mh_simple_box( $atts, $content ) {
        $grid = new My_Home_Simple_Box_Shortcode();
        return $grid->init( $atts, $content );
    }

    public function mh_button( $atts ) {
        $grid = new My_Home_Button_Shortcode();
        return $grid->init( $atts );
    }

    public function mh_carousel_attribute( $atts ) {
	    $carousel_attribute = new My_Home_Carousel_Attribute_Shortcode();
	    return $carousel_attribute->init( $atts );
    }

    public function mh_carousel_post( $atts ) {
        $carousel_post = new My_Home_Carousel_Post_Shortcode();
        return $carousel_post->init( $atts );
    }

    public function mh_carousel_estate( $atts ) {
        $carousel_estate = new My_Home_Carousel_Estate_Shortcode();
        return $carousel_estate->init( $atts );
    }

    public function mh_slider_estate( $atts, $content = null ) {
        $slider_estate = new My_Home_Slider_Estate_Shortcode();
        return $slider_estate->init( $atts, $content );
    }

    public function mh_slider( $atts, $content = null ) {
        $slider = new My_Home_Slider_Shortcode();
        return $slider->init( $atts, $content );
    }

    public function mh_carousel_agent( $atts ) {
        $carousel_agent = new My_Home_Carousel_Agent_Shortcode();
        return $carousel_agent->init( $atts );
    }

    public function mh_carousel_testimonials( $atts ) {
	    $carousel_testimonials = new My_Home_Carousel_Testimonials_Shortcode();
	    return $carousel_testimonials->init( $atts );
    }

    public function mh_carousel_clients( $atts ) {
        $carousel_clients = new My_Home_Carousel_Clients_Shortcode();
        return $carousel_clients->init( $atts );
    }

    public function mh_estate_map( $atts ) {
        $estate_map = new My_Home_Estate_Map_Shortcode();
        return $estate_map->init( $atts );
    }
}

endif;
