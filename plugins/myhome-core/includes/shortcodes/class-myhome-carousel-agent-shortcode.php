<?php
/**
 * My_Home_Carousel_Agent_Shortcode class
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Carousel_Agent_Shortcode' ) ) :

    /**
     * Class My_Home_Carousel_Agent_Shortcode
     */
    class My_Home_Carousel_Agent_Shortcode {

        /**
         * settings
         *
         * Prepare settings for Visual Composer element
         */
        public static function settings() {
            return array(
                'name'     => esc_html__( 'Agent Carousel', 'myhome-core' ),
                'base'     => 'mh_carousel_agent',
                'icon'     => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
                'category' => esc_html__( 'MyHome', 'myhome-core' ),
                'params'   => array(
                    // Visible
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Visible', 'myhome-core' ),
                        'param_name' => 'owl_visible',
                        'value'      => array(
                            esc_html__( 'Default - 3', 'myhome-core' ) => 'owl-carousel--visible-3',
                            esc_html__( '1 ', 'myhome-core' )          => 'owl-carousel--visible-1',
                            esc_html__( '2 ', 'myhome-core' )          => 'owl-carousel--visible-2',
                            esc_html__( '3 ', 'myhome-core' )          => 'owl-carousel--visible-3',
                            esc_html__( '4 ', 'myhome-core' )          => 'owl-carousel--visible-4',
                        ),
                    ),
                    // Agents limit
                    array(
                        'type'       => 'textfield',
                        'heading'    => esc_html__( 'Agents limit', 'myhome-core' ),
                        'param_name' => 'limit',
                        'value'      => 5,
                    ),
                    // Exclude admins (only agent role)
                    array(
                        'type'       => 'checkbox',
                        'heading'    => esc_html__( 'Exclude admins', 'myhome-core' ),
                        'param_name' => 'exclude_admin',
                        'value'      => 'true',
                        'std'        => 'true',
                    ),
                    // Style
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Style', 'myhome-core' ),
                        'param_name' => 'agent_style',
                        'value'      => array(
                            esc_html__( 'Default', 'myhome-core' )          => '',
                            esc_html__( 'White Background', 'myhome-core' ) => 'mh-agent--white',
                            esc_html__( 'Dark Background', 'myhome-core' )  => 'mh-agent--dark',
                        ),
                    ),
                    // Show description
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Show description', 'myhome-core' ),
                        'param_name' => 'description_show',
                        'value'      => array(
                            esc_html__( 'Yes', 'myhome-core' ) => 1,
                            esc_html__( 'No', 'myhome-core' )  => 0,
                        ),
                    ),
                    // Show Social Icons
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Show social icons', 'myhome-core' ),
                        'param_name' => 'social_icons_show',
                        'value'      => array(
                            esc_html__( 'Yes', 'myhome-core' ) => 1,
                            esc_html__( 'No', 'myhome-core' )  => 0,
                        ),
                    ),
                    // Show email
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Show Email', 'myhome-core' ),
                        'param_name' => 'email_show',
                        'value'      => array(
                            esc_html__( 'Yes', 'myhome-core' ) => 1,
                            esc_html__( 'No', 'myhome-core' )  => 0,
                        ),
                    ),
                    // Show phone
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Show phone', 'myhome-core' ),
                        'param_name' => 'phone_show',
                        'value'      => array(
                            esc_html__( 'Yes', 'myhome-core' ) => 1,
                            esc_html__( 'No', 'myhome-core' )  => 0,
                        ),
                    ),
                    // Show button
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Show button', 'myhome-core' ),
                        'param_name' => 'button_show',
                        'value'      => array(
                            esc_html__( 'Yes', 'myhome-core' ) => 1,
                            esc_html__( 'No', 'myhome-core' )  => 0,
                        ),
                    ),
                    // Dots
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Dots', 'myhome-core' ),
                        'param_name' => 'owl_dots',
                        'value'      => array(
                            esc_html__( 'Yes', 'myhome-core' ) => '',
                            esc_html__( 'No', 'myhome-core' )  => 'owl-carousel--no-dots',
                        ),
                    ),
                ),
            );
        }

        /**
         * init
         *
         * Generate shortcode output base on provided options
         *
         * @param $opts
         *
         * @return string
         */
        public function init( $opts ) {
            $atts = array(
                'limit'             => 5,
                'agent_style'       => '',
                'description_show'  => 1,
                'social_icons_show' => 1,
                'email_show'        => 1,
                'phone_show'        => 1,
                'button_show'       => 1,
                'exclude_admin'     => 'true',
                'owl_visible'       => 'owl-carousel--visible-3',
                'owl_dots'          => '',
            );

            if ( function_exists( 'vc_map_get_attributes' ) ) {
                $atts = array_merge( $atts, vc_map_get_attributes( 'mh_carousel_agent', $opts ) );
            }

            $exclude_admin   = $atts['exclude_admin'] == 'true';
            $carousel_agents = My_Home_Agent::get_agents( $atts['limit'], 0, $exclude_admin );
            $class           = $atts['owl_visible'] . ' ' . $atts['owl_dots'];
            ob_start();
            ?>

            <div class="owl-carousel <?php echo esc_attr( $class ); ?>">

                <?php foreach ( $carousel_agents as $agent ) : ?>
                    <article class="mh-agent <?php echo esc_attr( $atts['agent_style'] ); ?>">
                        <a href="<?php echo esc_url( $agent->get_link() ); ?>"
                           class="mh-agent__thumbnail" title="<?php echo esc_attr( $agent->get_name() ); ?>">
                            <?php if ( $agent->has_image() ) :
                                My_Home_Image::the_image(
                                    $agent->get_image_id(),
                                    'square',
                                    $agent->get_name()
                                );
                            endif; ?>
                        </a>

                        <div class="mh-agent__content">
                            <h3 class="mh-agent__heading">
                                <a href="<?php echo esc_url( $agent->get_link() ); ?>">
                                    <?php echo esc_attr( $agent->get_name() ); ?>
                                </a>
                            </h3>

                            <?php if ( ! empty( $atts['description_show'] ) ) : ?>
                                <?php if ( $agent->get_description() != '' ) : ?>
                                    <div class="mh-agent__text">
                                        <?php
                                        echo esc_html(
                                            wp_trim_words(
                                                $agent->get_description(),
                                                35,
                                                '...'
                                            )
                                        );
                                        ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="mh-agent-contact">
                                <?php if ( ! empty( $atts['email_show'] ) ) : ?>
                                    <?php if ( $agent->has_email() ) : ?>
                                        <div class="mh-agent-contact__element">
                                            <a href="mailto:<?php echo esc_attr( $agent->get_email() ); ?>">
                                                <i class="flaticon-mail-2"></i>
                                                <?php echo esc_html( $agent->get_email() ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ( ! empty( $atts['phone_show'] ) ) : ?>
                                    <?php if ( $agent->get_phone() != '' ) : ?>
                                        <div class="mh-agent-contact__element">
                                            <a href="tel:<?php echo esc_attr( $agent->get_phone_href() ); ?>">
                                                <i class="flaticon-phone"></i>
                                                <?php echo esc_html( $agent->get_phone() ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <?php if ( ! empty( $atts['social_icons_show'] ) ) : ?>
                                <div class="mh-agent__social-wrapper">
                                    <div class="mh-agent__social">
                                        <?php if ( $agent->get_facebook() != '' ) : ?>
                                            <a href="<?php echo esc_url( $agent->get_facebook() ); ?>"
                                               title="<?php esc_attr_e( 'Facebook', 'myhome-core' ); ?>"
                                               target="_blank">
                                                <i class="fa fa-facebook"></i>
                                            </a>
                                        <?php endif;

                                        if ( $agent->get_twitter() != '' ) : ?>
                                            <a href="<?php echo esc_url( $agent->get_twitter() ); ?>"
                                               title="<?php esc_attr_e( 'Twitter', 'myhome-core-core' ); ?>"
                                               target="_blank">
                                                <i class="fa fa-twitter"></i>
                                            </a>
                                        <?php endif;

                                        if ( $agent->get_instagram() != '' ) : ?>
                                            <a href="<?php echo esc_url( $agent->get_instagram() ); ?>"
                                               title="<?php esc_attr_e( 'Instagram', 'myhome-core' ); ?>"
                                               target="_blank">
                                                <i class="fa fa-instagram"></i>
                                            </a>
                                        <?php endif;

                                        if ( $agent->get_linkedin() != '' ) : ?>
                                            <a href="<?php echo esc_url( $agent->get_linkedin() ); ?>"
                                               title="<?php esc_attr_e( 'Linkedin', 'myhome-core' ); ?>"
                                               target="_blank">
                                                <i class="fa fa-linkedin"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ( ! empty( $atts['button_show'] ) ) : ?>
                                <div class="mh-agent__button-wrapper">
                                    <div class="mh-agent__button">
                                        <a href="<?php echo esc_url( $agent->get_link() ); ?>"
                                           title="<?php echo esc_attr( $agent->get_name() ); ?>"
                                           class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--primary-ghost">
                                            <?php esc_html_e( 'Full Profile', 'myhome-core' ); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>


                <?php endforeach; ?>
            </div>
            <?php
            return ob_get_clean();
        }

    }

endif;
