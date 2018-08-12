<?php

/*
 * My_Home_API
 *
 * Manage requests created by listing and map.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_API' ) ) :

class My_Home_API {

	public function __construct() {
	    /*
	     * Set endpoints
	     */
		add_action( 'rest_api_init', function () {
			register_rest_route( 'myhome/v1', '/estates', array(
				'methods'   => 'GET',
				'callback'  => array( 'My_Home_Api', 'get' ),
			) );
		} );
		add_action( 'rest_api_init', function () {
			register_rest_route( 'myhome/v1', '/estate', array(
				'methods'   => 'GET',
				'callback'  => array( 'My_Home_Api', 'get_estate' ),
			) );
		} );
		add_action( 'rest_api_init', function () {
			register_rest_route( 'myhome/v1', '/message', array(
				'methods'   => 'GET',
				'callback'  => array( 'My_Home_Api', 'message' ),
			) );
		} );
	}

	/*
	 * get
	 *
	 * Callback for /estates endpoint
	 */
	public static function get( $request ) {
	    if ( is_array( $request ) ) {
	        $params = $request;
        } else {
            $params = $request->get_params();
        }
	    $query = new My_Home_Query_Estates();

	    // set filters base on attributes
	    foreach ( My_Home_Attribute::get_attributes() as $attr ) {
	        foreach ( array( '' => '=', '_from' => '>=', '_to' => '<=' ) as $k => $compare ) {
	            $key = $attr->get_slug() . $k;
                if ( isset( $params[$key] ) && ! empty( $params[$key] ) && $params[$key] != 'any' ) {
                    $query->add_filter( $attr, $params[$key], $compare );
                }
            }
        }

        /*
         * Additional filters
         */
        // featured
        if ( isset( $params['featured'] ) ) {
            $query->set_featured( $params['featured'] );
        }
        // sort by
        if ( isset( $params['sort'] ) ) {
	        $query->set_sort_by( $params['sort'] );
        }
        // current page
        if ( isset( $params['current_page'] ) ) {
	        $query->set_page( $params['current_page'] );
        }
        // agent id
        if ( isset( $params['agent_id'] ) && ! empty( $params['agent_id'] ) ) {
	        $query->set_user( $params['agent_id'] );
        }
        // limit
	    if ( isset( $params['limit'] ) ) {
	        $query->set_limit( $params['limit'] );
        } elseif ( isset( $params['estates_per_page'] ) ) {
	        $query->set_limit( $params['estates_per_page'] );
        }
        // set language for WP_Query
        if ( ! empty( $params['lang'] ) ) {
            $query->set_lang( $params['lang'] );
        }
        // estates in (ids)
        if ( isset( $params['estates__in'] ) && ! empty( $params['estates__in'] ) ) {
	        $ids = explode( ',', $params['estates__in'] );
	        $estates = array();
	        if ( count( $ids ) ) {
	            foreach ( $ids as $id ) {
	                $temp_id = intval( $id );
	                if ( $temp_id > 0 ) {
	                    array_push( $estates, $temp_id );
                    }
                }
                $query->set_estates_in( $estates );
            }
        }
        // return My_Home_Objects or json data
        if ( isset( $params['objects'] ) ) {
	        $query->set_objects( $params['objects'] );
        }
        // check if requests is from map or listing
        if ( isset( $params['map'] ) && $params['map'] ) {
	        $query->set_map();
        }

        return $query->get_results();
    }

    // callback for /message used by 'submit question' form on single estate page
	public static function message( $request ) {
	    $params = $request->get_params();
        if ( ! isset( $params['agent_id' ] )
            && ! isset( $params['estate_id'] )
            && ! isset( $params['email'] )
            && ! isset( $params['phone'] )
            && ! isset( $params['message'] ) ) {
	        return array( 'result' => false );
        }

        $msg_email      = sanitize_text_field( $params['email'] );
        $msg_phone      = sanitize_text_field( $params['phone'] );
        $msg_message    = sanitize_text_field( $params['message'] );

        if ( mb_strlen( $msg_message, 'UTF-8' ) < 5 || ! is_email( $msg_email ) ) {
            return array( 'result' => false );
        }

        $agent_id   = intval( $params['agent_id'] );
	    $agent      = My_Home_Agent::get_agent( $agent_id );
	    $estate_id  = intval( $params['estate_id'] );
	    $estate     = My_Home_Estate::get_estate( $estate_id );
	    if ( is_null( $agent ) || is_null( $estate ) ) {
	        return array( 'result' => false );
        }

	    $email = $agent->get_email();
	    $title = wp_kses(
	        sprintf( __( 'Contact %s - %s', 'myhome-core' ), $msg_email, $estate->get_name() ),
            array()
        );
        $msg = wp_kses( sprintf(
            __( 'From: %s<br>Phone: %s<br>Message: %s<br>Estate: %s', 'myhome-core' ),
            $msg_email,
            $msg_phone,
            $msg_message,
            '<a href="' . esc_url( $estate->get_link() ) . '">' . esc_html( $estate->get_name() ) . '</a>'
        ), array(
            'br' => array(),
            'a' => array( 'href' => array() )
        ) );

        $return = wp_mail(
            $email,
            $title,
            $msg,
            array( 'Content-Type: text/html; charset=UTF-8' )
        );
        return array( 'result' => $return );
    }

    /*
     * get_estate
     *
     * Callback for /estate endpoint.
     * Used by compare estates module.
     */
	public static function get_estate( $request ) {
		$params = $request->get_params();
		if ( ! isset( $params['id'] ) ) {
			return '';
		}

		if ( ! empty( $params['lang'] ) ) {
            $params['id'] = apply_filters(
                'wpml_object_id',
                $params['id'],
                'estate',
                true,
                $params['lang']
            );

            global $sitepress;
            $sitepress->switch_lang( $params['lang'] );
            My_Home_Core()->lang = $params['lang'];
        }

		global $post;

		$post = get_post( $params['id'] );
		if ( $post->post_status != 'publish' || $post->post_type != 'estate' ) {
			return '';
		}

		setup_postdata( $post );

		$estate = new My_Home_Estate( $post );
		return $estate->get_json_data();
	}

}

endif;
