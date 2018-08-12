<?php

if ( ! class_exists( 'My_Home_PayPal' ) ) :

class My_Home_PayPal {

    public function __construct() {
        add_action( 'admin_post_paypal-token', array( $this, 'get_client_token' ) );
        add_action( 'admin_post_paypal-execute', array( $this, 'execute' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'head' ) );
    }

    public function head() {
        $options = get_option( 'myhome_redux' );
        $request = $_SERVER['REQUEST_URI'];
        $panel = $options['mh-agent-panel_link'];

        if ( class_exists( 'ReduxFramework' ) && strpos( $panel, $request ) !== false && $request != '/' ) {
            ob_start();
            ?>
                window.MyHomePayPal = {
                    name: '<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>',
                    apiKey: '<?php echo esc_attr( $options['mh-payment-paypal-public_key'] ); ?>',
                    env: '<?php echo $options['mh-payment-paypal-sandbox'] ? 'sandbox' : 'production'; ?>',
                    locale: '<?php echo esc_attr( $options['mh-payment-paypal-locale'] ); ?>',
                    cost: <?php echo esc_attr( $options['mh-payment-paypal-cost'] ); ?>,
                    currency: '<?php echo esc_attr( $options['mh-payment-paypal-currency'] ); ?>',
                    executePaymentUrl: '<?php echo esc_attr( admin_url( 'admin-post.php?action=paypal-execute' ) ); ?>'
                };
            <?php
            wp_add_inline_script( 'myhome-core-main', ob_get_clean() );
        }
    }

    public function get_access_token() {
        $options = get_option( 'myhome_redux' );

        $credentials = array(
            $options['mh-payment-paypal-public_key'],
            $options['mh-payment-paypal-secret_key']
        );

        if ( ! empty( $options['mh-payment-paypal-sandbox'] ) && $options['mh-payment-paypal-sandbox'] ) {
            $url = 'https://api.sandbox.paypal.com/v1/oauth2/token';
        } else {
            $url = 'https://api.paypal.com/v1/oauth2/token';
        }

        $args = 'grant_type=client_credentials';

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_USERPWD, $credentials[0] . ':' . $credentials[1] );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $args );
        $response = curl_exec( $ch );
        curl_close( $ch );
        if ( $response ) {
            $data = json_decode( $response );
            if ( ! empty( $data->access_token ) ) {
                return $data->access_token;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function execute() {
        if ( false === ( $access_token = $this->get_access_token() ) ) {
            die();
        }

        $estate_id = $_POST['estateID'];

        $estate = get_post( $estate_id );
        if ( $estate->post_author != get_current_user_id() ) {
            die();
        }

        $state = get_post_meta( $estate_id, 'myhome_state', true );
        if ( ! empty( $state ) && $state == 'payed' ) {
            die();
        }

        $options = get_option( 'myhome_redux' );
        $payment_id = $_POST['paymentID'];
        $args = array( 'payer_id' => $_POST['payerID'] );
        if ( ! empty( $options['mh-payment-paypal-sandbox'] ) && $options['mh-payment-paypal-sandbox'] ) {
            $url = 'https://api.sandbox.paypal.com/v1/payments/payment/' . $payment_id . '/execute/';
        } else {
            $url = 'https://api.paypal.com/v1/payments/payment/' . $payment_id . '/execute/';
        }

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $args ) );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            "Authorization:Bearer $access_token",
            "Content-Type:application/json"
        ) );
        $response = curl_exec( $ch );

        curl_close( $ch );
        if ( $response ) {
            $data = json_decode( $response );
            if ( ! empty( $data->state) && $data->state == 'approved' ) {
                if ( ! empty( $options['mh-agent-moderation'] ) ) {
                    $new_status = 'pending';
                } else {
                    $new_status = 'publish';
                }

                update_post_meta( $estate_id, 'myhome_state', 'payed' );
                add_post_meta( $estate_id, 'myhome_paypal_payment_id', $payment_id, true );
                wp_update_post(
                    array(
                        'ID'          => $estate_id,
                        'post_status' => $new_status
                    )
                );
                echo json_encode( array( 'success' => true ) );
            } else {
                die();
            }
        } else {
            die();
        }
    }

}

endif;