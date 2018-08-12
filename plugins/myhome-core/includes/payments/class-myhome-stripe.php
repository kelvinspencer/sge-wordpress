<?php

if ( ! class_exists( 'My_Home_Stripe' ) ) :

class My_Home_Stripe {

    public function __construct() {
        add_action( 'admin_post_stripe-payment', array( $this, 'payment' ) );
    }

    public function payment() {
        require_once plugin_dir_path( __DIR__ ) . 'libs/stripe-php-master/init.php';
        $options = get_option( 'myhome_redux' );

        \Stripe\Stripe::setApiKey( $options['mh-payment-stripe-secret_key'] );

        $token = $_POST['stripe_token'];
        $email = $_POST['stripe_email'];
        $estate_id = intval( $_POST['estate_id'] );

        $estate = get_post( $estate_id );
        if ( $estate->post_author != get_current_user_id() ) {
            die();
        }

        $state = get_post_meta( $estate_id, 'myhome_state', true );
        if ( ! empty( $state ) && $state == 'payed' ) {
            die();
        }

        try {
            $customer = \Stripe\Customer::create(
                array(
                    'email'  => $email,
                    'source' => $token
                )
            );

            $charge = \Stripe\Charge::create(
                array(
                    'description' => sprintf( esc_html__( '%s listing, Estate ID: %d', 'myhome-core' ), get_bloginfo( 'name' ), $estate_id ),
                    'customer'    => $customer->id,
                    'amount'      => $options['mh-payment-stripe-cost'],
                    'currency'    => $options['mh-payment-stripe-currency']
                )
            );

            if ( $options['mh-agent-moderation'] ) {
                $new_status = 'pending';
            }
            else {
                $new_status = 'publish';
            }

            update_post_meta( $estate_id, 'myhome_state', 'payed' );
            wp_update_post(
                array(
                    'ID'          => $estate_id,
                    'post_status' => $new_status
                )
            );
            $response = array( 'success' => true );
        } catch( Exception $e ) {
            $response = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }

        echo json_encode( $response );
    }

}

endif;