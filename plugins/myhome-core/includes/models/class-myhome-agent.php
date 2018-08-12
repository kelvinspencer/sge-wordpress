<?php

/*
 * My_Home_Agent class
 *
 * This class represents a single agent.
 * Setup data and provide helpful methods.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Agent' ) ) :

class My_Home_Agent {

    private $id;
    private $email;
    private $phone;
    private $link;
    private $name;
    private $username;
    private $image;
    private $image_id;
    private $description;
    // social
    private $twitter;
    private $facebook;
    private $linkedin;
    private $instagram;

    /*
     * Create My_Home_Agent base on WP_User
     */
    public function __construct( $user ) {
        $options = get_option( 'myhome_redux' );
        $this->id           = $user->ID;
        $this->email        = $user->data->user_email;
        $this->link         = get_author_posts_url( $user->ID );
        $this->username     = $user->user_login;
        $this->name         = $user->display_name;
        $this->description  = get_the_author_meta( 'description', $user->ID );

        // get custom fields
        if ( function_exists( 'get_field' ) ) {
            $fields = array(
                'facebook',
                'twitter',
                'linkedin',
                'instagram',
                'phone'
            );
            foreach ( $fields as $field ) {
                $field_key = 'mh-agent-' . $field;
                if ( ! array_key_exists( $field_key, $options ) || empty( $options[$field_key] ) ) {
                    continue;
                }


                $this->$field = get_field( 'agent_' . $field, 'user_' . $user->ID );
            }
            $image = get_field( 'agent_image', 'user_' . $user->ID );
            if ( $image ) {
                $this->image    = $image['url'];
                $this->image_id = $image['ID'];
            }
        }
    }

    /*
     * get_agent
     *
     * Get agent base on user_id
     */
    public static function get_agent( $user_id = null ) {
        if ( is_null( $user_id ) ) {
            // works for example on author page
            $author_name = get_query_var( 'author_name' );
            $user        = $author_name ? get_user_by( 'slug', $author_name ) : get_userdata( get_query_var( 'author' ) );
        } else {
            $user = get_user_by( 'ID', $user_id );
        }

        if ( isset( $user ) && $user ) {
            // create My_Home_Agent object
            return new My_Home_Agent( $user );
        }
        else {
            return null;
        }
    }

    /*
     * get_data
     *
     * This method create array which is used when we want prepare json data
     */
    public function get_data() {
        $data = get_object_vars( $this );
        $query = new My_Home_Query_Estates();
        $query->set_limit( -1 );
        $query->set_user( $this->id );
        $query->set_all();
        if ( ! empty( My_Home_Core()->lang ) ) {
            $query->set_lang( My_Home_Core()->lang );
        }
        $results = $query->get_results();
        $data['estates'] = $results['estates'];

        return $data;
    }

    /*
     * load_data
     *
     * Used when My_Home_Agent is created from cache
     */
    public function load_data( $data ) {
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }

    /*
     * get_ID
     *
     * Get agent id (user_id)
     */
    public function get_ID() {
        return $this->id;
    }

    /*
     * has_email
     *
     * Check if agent email is set
     */
    public function has_email() {
        $options = get_option( 'myhome_redux' );
        if ( array_key_exists( 'mh-agent-email_show', $options ) && empty( $options['mh-agent-email_show'] ) ) {
            return false;
        }
        return ! empty( $this->email );
    }

    /*
     * get_email
     *
     * Get agent email
     */
    public function get_email() {
        return $this->email;
    }

    /*
     * has_phone
     *
     * Check if agent phone is set
     */
    public function has_phone() {
        return ! empty( $this->phone );
    }

    /*
     * get_phone
     *
     * Get agent phone
     */
    public function get_phone() {
        return $this->phone;
    }

    /*
     * get_phone_href
     *
     * Get agent phone (remove spaces)
     */
    public function get_phone_href() {
        return str_replace( array(' ','-','(', ')'), '', $this->phone );
    }

    /*
     * get_link
     *
     * Get url to agent page (author page)
     */
    public function get_link() {
        return $this->link;
    }

    /*
     * get_name
     *
     * Get agent name
     */
    public function get_name() {
        return $this->name;
    }

    /*
     * get_twitter
     *
     * Get Twitter profile url
     */
    public function get_twitter() {
        return $this->twitter;
    }

    /*
     * get_linkedin
     *
     * Get Linkedin profile url
     */
    public function get_linkedin() {
        return $this->linkedin;
    }

    /*
     * get_facebook
     *
     * Get Facebook profile url
     */
    public function get_facebook() {
        return $this->facebook;
    }

    /*
     * get_instagram
     *
     * Get Instagram profile url
     */
    public function get_instagram() {
        return $this->instagram;
    }

    /*
     * get_description
     *
     * Get agent description
     */
    public function get_description() {
        return $this->description;
    }

    /*
     * has_image
     *
     * Check if agent image is set
     */
    public function has_image() {
        return ! empty( $this->image );
    }

    /*
     * get_image_id
     *
     * Check if agent image id is set
     */
    public function get_image_id() {
        return $this->image_id;
    }

    /*
     * get_image
     *
     * Get agent image url
     */
    public function get_image() {
        return $this->image;
    }

    /*
     * get_agents
     *
     * Get array of My_Home_Agent objects base on arguments
     * $only_agents - only agent role or also administrator
     */
    public static function get_agents( $limit = -1, $offset = 0, $only_agents = false, $objects = true ) {
        $limit      = intval( $limit );
        $offset     = intval( $offset );
        $cache_key  = 'myhome_agents_' . $limit . '_' . $offset . '_';
        $cache_key  .= $only_agents ? 'agents' : 'all';
        if ( $objects ) {
            $cache_key .= $cache_key . '_objects';
        }

        if ( ! empty( My_Home_Core()->lang ) ) {
            $cache_key .= '_' . My_Home_Core()->lang;
        }

        if ( false !== ( $agents = get_transient( $cache_key ) ) ) {
            return $agents;
        }

        $agents = array();
        $args   = array(
            'fields'    => 'all',
            'role__in'  => $only_agents ? array( 'agent' ) : array( 'administrator', 'agent' )
        );

        if ( $limit ) {
            $args['number'] = $limit;
        }

        if ( $offset ) {
            $args['offset'] = $offset;
        }

        $users = get_users( $args );
        if ( ! $objects ) {
            set_transient( $cache_key, $users, 4 * HOUR_IN_SECONDS );
            return $users;
        }

        foreach ( $users as $user ) {
            array_push( $agents, new My_Home_Agent( $user ) );
        }
        set_transient( $cache_key, $agents, 4 * HOUR_IN_SECONDS );

        return $agents;
    }

    /*
     * listing
     *
     * Create listing for author page.
     */
    public function listing() {
        $options = get_option( 'myhome_redux' );
        $show_advanced      = is_null( $options['mh-listing-show_advanced'] ) ? true : intval( $options['mh-listing-show_advanced'] );
        $show_clear         = is_null( $options['mh-listing-show_clear'] ) ? true : intval( $options['mh-listing-show_clear'] );
        $show_sort_by       = is_null( $options['mh-listing-show_sort_by'] ) ? true : intval( $options['mh-listing-show_sort_by'] );
        $show_view_types    = is_null( $options['mh-listing-show_view_types'] ) ? true : intval( $options['mh-listing-show_view_types'] );
        $advanced_number    = is_null( $options['mh-listing-search_form_advanced_number'] ) ? 3 : intval( $options['mh-listing-search_form_advanced_number'] );
        // settings from Listing options page
        $atts = array(
            'lazy_loading'                  => $options['mh-listing-lazy_loading'] ? 'true' : 'false',
            'lazy_loading_limit'            => intval( $options['mh-listing-load_more_button_number'] ),
            'load_more_button'              => $options['mh-listing-load_more_button_label'],
            'load_prev_button'              => $options['mh-listing-load_prev_button_label'],
            'listing_default_view'          => $options['mh-listing-default_view'],
            'estates_per_page'              => $options['mh-listing-estates_limit'],
            'search_form_position'          => $options['mh-listing-search_form_position'],
            'label'                         => $options['mh-listing-label'],
            'search_form_advanced_number'   => $advanced_number,
            'show_advanced'                 => $show_advanced ? 'true' : 'false',
            'show_clear'                    => $show_clear ? 'true' : 'false',
            'show_sort_by'                  => $show_sort_by ? 'true' : 'false',
            'show_view_types'               => $show_view_types ? 'true' : 'false',
            'agent_id'                      => $this->id, // get estates only from this agent
            'map'                           => false
        );

        // prepare attributes
        foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            $value = $options['mh-listing-' . $attr->get_slug() . '_show'];
            $atts[$attr->get_slug() . '_show'] = $value ? 'true' : 'false';
        }

        // initiate listing
        $listing = new My_Home_Listing( $atts );
        ?>
        <div class="mh-listing--full-width mh-listing--horizontal-boxed">
            <?php $listing->listing(); ?>
        </div>
        <?php
    }

    public static function get_current() {
        return My_Home_Agent::get_agent( get_current_user_id() );
    }

}

endif;