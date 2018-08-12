<?php

/*
 * My_Home_Agents class
 *
 * Create agent role, setup custom fields and manage frontend agent interface.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Agents' ) ) :

class My_Home_Agents {

    public function __construct() {
        /*
         * Add actions and hooks related to agent
         */
        // set custom fields
        add_action( 'acf/init', array( $this, 'set_fields' ) );
        // remove admin bar
        add_action( 'after_setup_theme', array( $this, 'remove_admin_bar' ) );
        // create custom menu
        add_action( 'admin_menu', array( $this, 'custom_menu' ) );
        // manage agent caps
        add_action( 'redux/options/myhome_redux/settings/change', array( $this, 'update_agent_caps' ) );
        add_action( 'redux/options/myhome_redux/saved', array( $this, 'update_agent_caps' ) );
        add_action( 'redux/options/myhome_redux/reset', array( $this, 'update_agent_caps' ) );
        // filter estates which are displayed for agent
        add_filter( 'pre_get_posts', array( $this, 'estates_for_current_author' ) );
        // ability to assign agent
        add_filter( 'wp_dropdown_users', array( $this, 'assign_agent' ) );

        $options = get_option( 'myhome_redux' );
        if ( isset( $options['mh-payment'] ) && $options['mh-payment'] ) {
            if ( isset( $options['mh-payment-stripe'] ) && $options['mh-payment-stripe'] ) {
                new My_Home_Stripe();
            }

            if ( isset( $options['mh-payment-paypal'] ) && $options['mh-payment-paypal'] ) {
                new My_Home_PayPal();
            }
        }

        // disable backend for agent role
        if ( isset( $options['mh-agent-disable_backend'] ) && $options['mh-agent-disable_backend'] ) {
            add_action( 'admin_init', array( $this, 'disable_backend' ) );
        }

        /*
         * Frontend agent interface
         */
        // check is user is logged
        add_action( 'admin_post_nopriv_check_is_user', array( $this, 'check_is_user' ) );
        add_action( 'admin_post_check_is_user', array( $this, 'check_is_user' ) );
        // login user
        add_action( 'admin_post_nopriv_user_login', array( $this, 'user_login' ) );
        add_action( 'admin_post_user_login', array( $this, 'user_login' ) );
        // logout user
        add_action( 'admin_post_user_logout', array( $this, 'user_logout' ) );
        // register user
        if ( isset( $options['mh-agent-registration'] ) && $options['mh-agent-registration'] ) {
            add_action( 'admin_post_nopriv_user_register', array( $this, 'user_register' ) );
        }
        // get attributes - required for estate form
        add_action( 'admin_post_get_attributes', array( $this, 'get_attributes' ) );
        // submit/update estate
        add_action( 'admin_post_submit_estate', array( $this, 'submit_estate' ) );
        // add image to gallery
        add_action( 'admin_post_add_estate_image', array( $this, 'add_estate_image' ) );
        // add featured image
        add_action( 'admin_post_add_featured_image', array( $this, 'add_featured_image' ) );
        // delete estate
        add_action( 'admin_post_delete_estate', array( $this, 'delete_estate' ) );
        // get estate
        add_action( 'admin_post_get_estate', array( $this, 'get_estate' ) );
        // get agent estates
        add_action( 'admin_post_get_estates', array( $this, 'get_estates' ) );
        // update agent profile
        add_action( 'admin_post_update_profile', array( $this, 'update_profile' ) );

        add_action( 'admin_post_nopriv_user_reset_password', array( $this, 'reset_password' ) );
    }

    /*
     * disable_backend
     *
     * Disable backend for agents
     */
    public function disable_backend() {
        $user = wp_get_current_user();
        if ( ! in_array( 'agent', $user->roles ) ) {
            return;
        }

        $file = basename( $_SERVER['PHP_SELF'] );
        if ( is_admin() && $file != 'admin-post.php' && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )  ) {
            if ( ! empty( $options['mh-agent-panel_link'] ) ) {
                $redirect = $options['mh-agent-panel_link'];
            } else {
                $redirect = home_url();
            }
            wp_redirect( $redirect );
            exit;
        }
    }

    /*
     * get_estates
     *
     * Get estates which belongs to current user
     */
    public function get_estates() {
        $user_id = get_current_user_id();
        $query = new My_Home_Query_Estates();
        $query->set_limit( -1 );
        $query->set_user( $user_id );
        $query->set_all();
        if ( ! empty( My_Home_Core()->lang ) ) {
            $query->set_lang( My_Home_Core()->lang );
        }
        $results = $query->get_results();
        if ( isset( $results['estates'] ) ) {
            $estates = $results['estates'];
        } else {
            $estates = array();
        }

        echo json_encode( $estates );
    }

    public function reset_password() {
        $response = array( 'message' => '', 'success' => true );
        $result = $this->retrieve_password();
        if ( $result == true ) {
            $response['message'] = esc_html__( 'Check your email', 'myhome-core' );
        } elseif ( $result instanceof WP_Error ) {
            $response['success'] = false;
        } else {
            $response = array(
                'success' => false,
                'message' => esc_html__( 'Unknown error, try again.', 'myhome-core' )
            );
        }

        echo json_encode( $response );
    }

    // this is code from original WordPress retrieve_password function (wp-login.php) with little modifications
    // related to context of use
    public function retrieve_password() {
        $errors = new WP_Error();

        if ( empty( $_POST['user_login'] ) ) {
            $errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or email address.'));
        } elseif ( strpos( $_POST['user_login'], '@' ) ) {
            $user_data = get_user_by( 'email', trim( wp_unslash( $_POST['user_login'] ) ) );
            if ( empty( $user_data ) )
                $errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
        } else {
            $login = trim($_POST['user_login']);
            $user_data = get_user_by('login', $login);
        }

        /**
         * Fires before errors are returned from a password reset request.
         *
         * @since 2.1.0
         * @since 4.4.0 Added the `$errors` parameter.
         *
         * @param WP_Error $errors A WP_Error object containing any errors generated
         *                         by using invalid credentials.
         */
        do_action( 'lostpassword_post', $errors );

        if ( $errors->get_error_code() )
            return $errors;

        if ( !$user_data ) {
            $errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or email.'));
            return $errors;
        }

        // Redefining user_login ensures we return the right case in the email.
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        $key = get_password_reset_key( $user_data );

        if ( is_wp_error( $key ) ) {
            return $key;
        }

        $message = __( 'Someone has requested a password reset for the following account:', 'myhome-core' ) . "\r\n\r\n";
        $message .= network_home_url( '/' ) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'myhome-core' ) . "\r\n\r\n";
        $message .= __( 'To reset your password, visit the following address:', 'myhome-core' ) . "\r\n\r\n";
        $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";

        if ( is_multisite() ) {
            $blogname = get_network()->site_name;
        } else {
            /*
             * The blogname option is escaped with esc_html on the way into the database
             * in sanitize_option we want to reverse this for the plain text arena of emails.
             */
            $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        }

        /* translators: Password reset email subject. 1: Site name */
        $title = sprintf( esc_html__('[%s] Password Reset', 'myhome-core' ), $blogname );

        /**
         * Filters the subject of the password reset email.
         *
         * @since 2.8.0
         * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
         *
         * @param string  $title      Default email title.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

        /**
         * Filters the message body of the password reset mail.
         *
         * @since 2.8.0
         * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
         *
         * @param string  $message    Default mail message.
         * @param string  $key        The activation key.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

        if ( $message && ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
            wp_die( esc_html__( 'The email could not be sent.', 'myhome-core' ) );

        return true;
    }

    /*
     * update_profile
     *
     * Update agent information
     */
    public function update_profile() {
        if ( isset( $_POST['data'] ) ) {
            $data = $_POST['data'];
            $user_id = get_current_user_id();
            if ( isset( $data['facebook'] ) ) {
                update_field( 'myhome_agent_facebook', $data['facebook'], 'user_' . $user_id );
            }
            if ( isset( $data['linkedin'] ) ) {
                update_field( 'myhome_agent_linkedin', $data['linkedin'], 'user_' . $user_id );
            }
            if ( isset( $data['twitter'] ) ) {
                update_field( 'myhome_agent_twitter', $data['twitter'], 'user_' . $user_id );
            }
            if ( isset( $data['instagram'] ) ) {
                update_field( 'myhome_agent_instagram', $data['instagram'], 'user_' . $user_id );
            }
            if ( isset( $data['phone'] ) ) {
                update_field( 'myhome_agent_phone', $data['phone'], 'user_' . $user_id );
            }
            if ( isset( $data['image'] ) ) {
                update_field( 'myhome_agent_image', $data['image'], 'user_' . $user_id );
            }
            if ( isset( $data['name'] ) ) {
                $user_id = wp_update_user( array( 'ID' => $user_id, 'display_name' => $data['name'] ) );
            }
            $user = My_Home_Agent::get_agent( $user_id );
            echo json_encode( $user->get_data() );
        }
    }

    /*
     * get estate
     *
     * Get estate by id, current agent must be owner
     */
    public function get_estate() {
        $response = array();
        if ( isset( $_POST['estate_id'] ) ) {
            $estate = My_Home_Estate::get_estate( $_POST['estate_id'] );
            $agent = $estate->get_agent();
            if ( $agent->get_ID() == get_current_user_id() ) {
                $response['success'] = true;
                $response['estate']  = $estate->get_json_data();
            } else {
                $response['success'] = false;
            }
        } else {
            $response['success'] = false;
        }

        echo json_encode( $response );
    }

    /*
     * delete_estate
     *
     * Delete estate
     */
    public function delete_estate() {
        if ( ! is_user_logged_in() || ! current_user_can( 'edit_estates' ) ) {
            return;
        }

        $estate_id = intval( $_POST['estate_id'] );
        $post = get_post( $estate_id );
        $user_id = get_current_user_id();
        if ( ! is_null( $post ) && $post->post_author == $user_id ) {
            wp_delete_post( $estate_id );
            My_Home_Cache::clear_cache();
        }
    }

    /*
     * submit_estate
     *
     * Submit or update estate
     */
    public function submit_estate() {
        if ( ! is_user_logged_in() || ! isset( $_POST['data'] ) || ! current_user_can( 'edit_estates' ) ) {
            return;
        }

        $options = get_option( 'myhome_redux' );
        $response = array();
        $data = $_POST['data'];
        // prepare generic post data array
        $estate = array(
            'post_title'    => sanitize_text_field( $data['name'] ),
            'post_author'   => get_current_user_id(),
            'post_content'  => $data['description'],
            'post_status'   => 'draft',
            'post_type'     => 'estate'
        );

        // check if update or create new estate
        if ( ! empty( $_POST['data']['id'] ) ) {
            $estate_id = intval( $_POST['data']['id'] );
            $post = get_post( $estate_id );
            // check if this is real owner of estate (prevent any post request manipulation from any other user)
            if ( empty( $post ) || $post->post_author != get_current_user_id() ) {
                return;
            }

            $state = get_post_meta( $estate_id, 'myhome_state', true );
            // if this was already published property, check if should be moderated
            if ( ! empty( $state ) && $state == 'pre_payment' ) {
                $estate['post_status'] = 'draft';
            } elseif ( intval( $options['mh-agent-moderation'] ) ) {
                $estate['post_status'] = 'pending';
            } elseif ( $post->post_status == 'publish' ) {
                $estate['post_status'] = 'publish';
            }
            $estate['ID'] = $estate_id;
            wp_update_post( $estate );
        } else {
            if ( $options['mh-payment'] ) {
                $state = 'pre_payment';
            } else {
                $state = 'free';
                if ( $options['mh-agent-moderation'] ) {
                    $estate['post_status'] = 'pending';
                } else {
                    $estate['post_status'] = 'publish';
                }
            }
            $estate_id = wp_insert_post( $estate );
            if ( is_numeric( $estate_id ) ) {
                add_post_meta( $estate_id, 'myhome_state', $state );
            }
        }

        if ( is_numeric( $estate_id ) ) {
            // set featured image
            if ( isset( $data['image']['id'] ) ) {
                set_post_thumbnail( $estate_id, $data['image']['id'] );
            }

            // set gallery
            if ( isset( $data['gallery'] ) && count( $data['gallery'] ) ) {
                $gallery = array();
                foreach ( $data['gallery'] as $image ) {
                    array_push( $gallery, $image['id'] );
                }

                update_field( 'myhome_estate_gallery', $gallery, $estate_id );
            }

            // set plans
            if ( isset( $options['mh-estate_plans'] ) && $options['mh-estate_plans'] && isset( $data['plans'] )
                && count( $data['plans'] ) ) {
                update_field( 'myhome_estate_plans', array(), $estate_id );
                foreach ( $data['plans'] as $plan ) {
                    add_row( 'myhome_estate_plans', array(
                        'estate_plans_name'     => $plan['label'],
                        'estate_plans_image'    => $plan['image']['id']
                    ), $estate_id );
                }
            }

            // set video
            if ( isset( $options['mh-estate_video'] ) && $options['mh-estate_video'] ) {
                update_field( 'myhome_estate_video', $data['video'], $estate_id );
            }

            // set video
            if ( isset( $options['mh-estate_virtual_tour'] ) && $options['mh-estate_virtual_tour'] ) {
                update_field( 'myhome_estate_virtual_tour', $data['tour'], $estate_id );
            }

            // set address and location
            if ( isset( $data['address'] ) && isset( $data['location']['lat'] ) && $data['location']['lng'] ) {
                $value = array(
                    'address' => $data['address'],
                    'lat'     => $data['location']['lat'],
                    'lng'     => $data['location']['lng'],
                    'zoom'    => 10
                );
                update_field( 'myhome_estate_location', $value, $estate_id );
            }

            // get values from attribute fields
            foreach ( My_Home_Attribute::get_attributes() as $attr ) {
                if ( ! isset( $data['values'][$attr->get_slug()] ) ) {
                    continue;
                }

                if ( $attr->get_type() == 'field' ) {
                    update_field(
                        'myhome_estate_attr_' . $attr->get_slug(),
                        $data['values'][$attr->get_slug()],
                        $estate_id
                    );
                } elseif( $attr->get_type() == 'taxonomy' ) {
                    if ( $attr->like_tags() ) {
                        $values = explode( ',', $data['values'][$attr->get_slug()] );
                        foreach ( $values as $key => $tag ) {
                            $values[$key] = trim( $tag );
                        }
                    } else {
                        $values = array( $data['values'][$attr->get_slug()] );
                    }

                    wp_set_post_terms(
                        $estate_id,
                        $values,
                        $attr->get_slug()
                    );
                }
            }

            $response['estate_id'] = $estate_id;
            $response['success'] = true;
            My_Home_Cache::clear_cache();
        } elseif ( is_a( $estate_id, 'WP_Error' ) ) {
            $response['success'] = false;
        }

        echo json_encode( $response );
    }

    /*
     * add_estate_image
     */
    public function add_estate_image() {
        if ( ! current_user_can( 'edit_estates' ) ) {
            wp_die();
        }

        $mime = $_FILES['photo']['type'];
        if ( $mime != 'image/jpeg' && $mime != 'image/jpg' && $mime != 'image/png' ) {
            wp_die();
        }

        $image_id = media_handle_upload( 'photo', 0 );
        if (  is_object( $image_id ) ) {
            wp_die();
        }

        $image_url = wp_get_attachment_image_url( $image_id, array( 265, 265 ) );
        $image = array(
            'id'    => $image_id,
            'url'   => $image_url
        );

        echo json_encode( $image );
    }

    /*
     * add_featured_image
     */
    public function add_featured_image() {
        if ( ! current_user_can( 'edit_estates' ) ) {
            wp_die();
        }

        $mime = $_FILES['photo']['type'];
        if ( $mime != 'image/jpeg' && $mime != 'image/jpg' && $mime != 'image/png' ) {
            wp_die();
        }

        $image_id = media_handle_upload( 'photo', 0 );
        if (  is_object( $image_id ) ) {
            wp_die();
        }

        if ( ! empty( $_POST['post_id'] ) ) {
            $post_id = intval( $_POST['post_id'] );
            $post = get_post( $post_id );
            if ( ! empty( $post ) && $post->post_author == get_current_user_id() ) {
                set_post_thumbnail( $post, $image_id );
            }
        }

        $image_url = wp_get_attachment_image_url( $image_id, array( 265, 265 ) );
        $image = array(
            'id'    => $image_id,
            'url'   => $image_url
        );
        echo json_encode( $image );
    }

    /*
     * get_attributes
     *
     * Get attributes
     */
    public function get_attributes() {
        if ( ! current_user_can( 'edit_estates' ) ) {
            wp_die();
        }

        $attributes = My_Home_Attribute::get_attributes();
        $attributes_data = array();

        foreach ( $attributes as $attr ) {
            if ( $attr->get_type() == 'field' || $attr->get_type() == 'taxonomy' ) {
                array_push( $attributes_data, $attr->get_data() );
            }
        }

        $response['success']    = true;
        $response['attributes'] = $attributes_data;

        echo json_encode( $response );
    }

    /*
     * user_login
     *
     * Login process
     */
    public function user_login() {
        $credentials = array(
            'user_login'    => sanitize_text_field( $_POST['name'] ),
            'user_password' => sanitize_text_field( $_POST['password'] ),
            'remember'      => (boolean) $_POST['remember']
        );
        $response = array(
            'success'       => true,
            'message'       => '',
            'error_fields'  => array(
                'name'      => array(),
                'password'  => array()
            )
        );

        if ( empty( $credentials['user_login'] ) ) {
            $response['success'] = false;
            array_push( $response['error_fields']['name'], esc_html__( 'Empty field', 'myhome-core' ) );
        }

        if ( empty( $credentials['user_password'] ) ) {
            $response['success'] = false;
            array_push( $response['error_fields']['password'], esc_html__( 'Empty field', 'myhome-core' ) );
        }

        // if any field is empty then throw error
        if ( ! $response['success'] ) {
            $response['message'] = esc_html__( 'Check errors', 'myhome-core' );
            echo json_encode( $response );
            return false;
        }

        $user = wp_signon( $credentials );
        if ( is_a( $user, 'WP_User' ) ) {
            $agent = new My_Home_Agent( $user );
            $response['agent'] = $agent->get_data();
        } elseif ( is_a( $user, 'WP_Error' ) ) {
            $response['success'] = false;
            $response['message'] = esc_html__( 'Check errors', 'myhome-core' );

            foreach ( $user->errors as $key => $errors ) {
                switch ( $key ) {
                    case 'incorrect_password':
                        array_push(
                            $response['error_fields']['password'],
                            esc_html__( 'Wrong password', 'myhome-core' )
                        );
                        break;
                    case 'invalid_username':
                        array_push(
                            $response['error_fields']['name'],
                            esc_html__( 'Invalid username', 'myhome-core' )
                        );
                        break;
                }
            }
        } else {
            $response['success'] = false;
            $response['message'] = esc_html__( 'Unknown error, try again', 'myhome-core' );
        }
        echo json_encode( $response );
    }

    public function user_logout() {
        wp_logout();
    }

    /*
     * user_register
     *
     * Register new user
     */
    public function user_register() {
        $user_name                  = sanitize_text_field( $_POST['name'] );
        $user_password              = sanitize_text_field( $_POST['password'] );
        $user_email                 = sanitize_text_field( $_POST['email'] );
        $response                   = array( 'message' => '', 'success' => true );
        $response['error_fields']   = array( 'name' => array(), 'password' => array(), 'email' => array() );

        // validate fields if empty
        if ( empty( $user_name ) ) {
            array_push( $response['error_fields']['name'], esc_html__( 'Empty field', 'myhome-core' ) );
            $response['success'] = false;
        }

        if ( empty( $user_password ) ) {
            array_push( $response['error_fields']['password'], esc_html__( 'Empty field', 'myhome-core' ) );
            $response['success'] = false;
        }

        if ( empty( $user_email ) ) {
            array_push( $response['error_fields']['email'], esc_html__( 'Empty field', 'myhome-core' ) );
            $response['success'] = false;
        }

        // if any field is empty then throw error
        if ( ! $response['success'] ) {
            $response['message'] = esc_html__( 'Check errors', 'myhome-core' );
            echo json_encode( $response );
            return false;
        }

        // validate email
        if ( ! is_email( $user_email ) ) {
            array_push( $response['error_fields']['email'], esc_html__( 'Email is not valid', 'myhome-core' ) );
            $response['success'] = false;
            $response['message'] = esc_html__( 'Check errors', 'myhome-core' );
            echo json_encode( $response );
            return false;
        }

        // try create user
        $user_id = wp_create_user( $user_name, $user_password, $user_email );

        // if error returned
        if ( is_a( $user_id, 'WP_Error' ) ) {
            $response['success'] = false;
            $response['message'] = esc_html__( 'Check errors', 'myhome-core' );

            foreach ( $user_id->errors as $key => $errors ) {
                switch( $key ) {
                    case 'existing_user_email':
                        array_push(
                            $response['error_fields']['email'],
                            esc_html__( 'This email is already exists.', 'myhome-core' )
                        );
                        break;
                    case 'existing_user_login':
                        array_push(
                            $response['error_fields']['name'],
                            esc_html__( 'This username already exists.', 'myhome-core' )
                        );
                        break;
                }
            }
        } elseif ( is_numeric( $user_id ) ) { // if new user_id returned
            $user = get_user_by( 'id', $user_id );
            foreach ( $user->roles as $role ) {
                $user->remove_role( $role );
            }
            $user->add_role( 'agent' );
            $response['message'] = esc_html__( 'Successful registration! You can login now.', 'myhome-core' );
        } else { // prevent any unpredicted things
            $response['success'] = false;
            $response['message'] = esc_html__( 'Unknown error, please try again.', 'myhome-core' );
        }

        echo json_encode( $response );
    }

    /*
     * check_is_user
     *
     * Check if user is logged in
     */
    public function check_is_user() {
        $response = array();
        $is_user = is_user_logged_in();
        if ( $is_user ) {
            $user_id = get_current_user_id();
            $agent = My_Home_Agent::get_agent( $user_id );
            $response['is_user'] = true;
            $response['agent'] = $agent->get_data();
        } else {
            $response['is_user'] = false;
        }

        echo json_encode( $response );
    }

    /*
     * remove_admin_bar
     *
     * Remove admin toolbar for non admin users
     */
    public function remove_admin_bar() {
        if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
            show_admin_bar( false );
        }
    }

    /*
     * update_agent_caps
     *
     * Add agent caps for specific roles
     */
    public static function update_agent_caps() {
        $roles = array( 'editor', 'administrator', 'agent' );
        // Loop through each role and assign capabilities
        foreach ( $roles as $role ) {
            $role = get_role( $role );
            $role->add_cap('read');
            $role->add_cap( 'read_estate' );
            $role->add_cap( 'delete_estate' );
            $role->add_cap( 'edit_estates' );
            $role->add_cap( 'edit_others_estates' );
            $role->add_cap( 'publish_estates' );
            $role->add_cap( 'read_private_estates' );
            $role->add_cap( 'delete_estates' );
            $role->add_cap( 'delete_private_estates' );
            $role->add_cap( 'delete_published_estates' );
            $role->add_cap( 'delete_others_estates' );
            $role->add_cap( 'edit_private_estates' );
            $role->add_cap( 'edit_published_estates' );
            $role->add_cap( 'create_estates' );
            $role->add_cap( 'agent_cap' );
            $role->add_cap( 'upload_files' );
        }

        $agent = get_role( 'agent' );
        if ( is_null( $agent ) ) {
            return;
        }

        $agent->add_cap('read');
        $agent->add_cap( 'read_estate' );
        $agent->add_cap( 'delete_estate' );
        $agent->add_cap( 'edit_estates' );
        $agent->add_cap( 'read_private_estates' );
        $agent->add_cap( 'delete_estates' );
        $agent->add_cap( 'delete_private_estates' );
        $agent->add_cap( 'delete_published_estates' );
        $agent->add_cap( 'edit_private_estates' );
        $agent->add_cap( 'edit_published_estates' );
        $agent->add_cap( 'create_estates' );
        $agent->add_cap( 'agent_cap' );
        $agent->add_cap( 'upload_files' );
    }

    /*
     * assign_agent
     */
    public function assign_agent( $output ) {
        global $post;

        if ( $post->post_type == 'estate' )  {
            global $wpdb;
            $users = new WP_User_Query( array(
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'       => $wpdb->prefix . 'capabilities',
                        'value'     => 'administrator',
                        'compare'   => 'like'
                    ),
                    array(
                        'key'       => $wpdb->prefix . 'capabilities',
                        'value'     => 'agent',
                        'compare'   => 'like'
                    )
                )
            ) );

            $output = "<select id='post_author_override' name='post_author_override' class=''>";
            foreach ( $users->results as $user ) {
                $selected = $user->ID == $post->post_author ? 'selected="selected"' : '';
                $output .= "<option  $selected value='" . esc_attr( $user->ID ) . "'>" . esc_html( $user->display_name ) .
                    "</option>";
            }
            $output .= "</select>";
        }
        return $output;
    }

    /*
     * create
     *
     * Create agent role
     */
    public static function create() {
        add_role(
            'agent', esc_html__( 'Agent', 'myhome-core' ), array(
                'read'          => true,
                'edit_posts'    => false,
                'delete_posts'  => false,
                'publish_posts' => false,
                'upload_files'  => true
            )
        );
        My_Home_Agents::update_agent_caps();
    }

    /*
     * destroy
     *
     * Delete agent role
     */
    public static function destroy() {
        remove_role( 'agent' );
    }

    /*
     * estates_for_current_author
     *
     * Filter estates for specific agent
     */
    public function estates_for_current_author( $query ) {
        global $pagenow;

        if ( ! in_array( $pagenow, array( 'edit.php', 'upload.php', 'admin-ajax.php' ) ) || ! $query->is_admin ) {
            return $query;
        }

        $user = wp_get_current_user();
        if ( $user && ! in_array( 'agent', $user->roles ) ) {
            return $query;
        }

        $query->set( 'author', get_current_user_id() );

        add_filter( 'wp_count_posts', array( $this, 'wpse149143_wp_count_posts' ), 10, 3 );

        return $query;
    }

    function wpse149143_wp_count_posts( $counts, $type, $perm ) {
        global $wpdb;

        $query = "SELECT post_status, COUNT( * ) AS num_posts
						FROM {$wpdb->posts}
						WHERE post_type = %s AND (post_author = %d)
						GROUP BY post_status";
        $results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type, get_current_user_id() ), ARRAY_A );
        $counts = array_fill_keys( get_post_stati(), 0 );

        foreach ( $results as $row ) {
            $counts[$row['post_status']] = $row['num_posts'];
        }

        return (object) $counts;
    }

    /*
     * custom_menu
     *
     * Remove unnecessary menu elements for agent role
     */
    public function custom_menu() {
        $user = wp_get_current_user();
        if ( in_array( 'agent', $user->roles ) ) {
            remove_menu_page( 'vc-welcome' );
        }
    }

    /*
     * set_fields
     *
     * Set custom fields related to agents
     */
    public static function set_fields() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group(
            array(
                'key'      => 'myhome_agent',
                'title'    => esc_html__( 'Agent', 'myhome-core' ),
                'location' => array(
                    array(
                        array(
                            'param'    => 'user_role',
                            'operator' => '==',
                            'value'    => 'agent',
                        )
                    ),
                    array(
                        array(
                            'param'    => 'user_role',
                            'operator' => '==',
                            'value'    => 'administrator',
                        )
                    )
                ),
                'fields'   => array(
                    // Image
                    'myhome_agent_image'     => array(
                        'key'   => 'myhome_agent_image',
                        'label' => esc_html__( 'Image', 'myhome-core' ),
                        'name'  => 'agent_image',
                        'type'  => 'image',
                    ),
                    // Phone
                    'myhome_agent_phone' => array(
                        'key'   => 'myhome_agent_phone',
                        'label' => esc_html__( 'Phone', 'myhome-core' ),
                        'name'  => 'agent_phone',
                        'type'  => 'text',
                    ),
                    // Facebook
                    'myhome_agent_facebook'  => array(
                        'key'   => 'myhome_agent_facebook',
                        'label' => esc_html__( 'Facebook', 'myhome-core' ),
                        'name'  => 'agent_facebook',
                        'type'  => 'text',
                    ),
                    // Twitter
                    'myhome_agent_twitter'   => array(
                        'key'   => 'myhome_agent_twitter',
                        'label' => esc_html__( 'Twitter', 'myhome-core' ),
                        'name'  => 'agent_twitter',
                        'type'  => 'text',
                    ),
                    // Instagram
                    'myhome_agent_instagram' => array(
                        'key'   => 'myhome_agent_instagram',
                        'label' => esc_html__( 'Instagram', 'myhome-core' ),
                        'name'  => 'agent_instagram',
                        'type'  => 'text',
                    ),
                    // Linkedin
                    'myhome_agent_linkedin'  => array(
                        'key'   => 'myhome_agent_linkedin',
                        'label' => esc_html__( 'Linkedin', 'myhome-core' ),
                        'name'  => 'agent_linkedin',
                        'type'  => 'text',
                    ),
                ),
            )
        );
    }

}

endif;
