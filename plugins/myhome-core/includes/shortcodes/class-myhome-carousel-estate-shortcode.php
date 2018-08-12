<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Carousel_Estate_Shortcode' ) ) :

class My_Home_Carousel_Estate_Shortcode {

    /**
     * settings
     *
     * Prepare settings for Visual Composer element
     */
	public static function settings() {
	    $agents = My_Home_Agent::get_agents( -1, 0, false, false );
	    $agents_list = array( esc_html__( 'Any', 'myhome-core' ) => 0 );
	    foreach ( $agents as $agent ) {
	        $agents_list[$agent->display_name] = $agent->ID;
        }
	    $fields = array(
            // Style
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Style', 'myhome-core' ),
                'param_name'    => 'estate_style',
                'value'         => array(
                    esc_html__( 'Default', 'myhome-core' )  		=> '',
                    esc_html__( 'White Background', 'myhome-core' ) => 'mh-estate-vertical--white',
                    esc_html__( 'Dark Background', 'myhome-core' ) 	=> 'mh-estate-vertical--dark',
                ),
            ),
            // Properties in
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Properties IDs', 'myhome-core' ),
                'param_name'    => 'estates__in',
                'value'         => '',
                'save_always'   => true
            ),
            // Properties limit
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'textfield',
                'heading'       => esc_html__( 'Properties limit', 'myhome-core' ),
                'param_name'    => 'limit',
                'value'         => 5,
                'save_always'   => true
            ),
            // Featured
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'checkbox',
                'heading'       => esc_html__( 'Featured', 'myhome-core' ),
                'param_name'    => 'featured',
                'save_always'   => true,
                'value'         => 'true'
            ),
            // Sort by
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Sort by', 'myhome-core' ),
                'param_name'    => 'sort',
                'value'         => array(
                    esc_html__( 'Newest', 'myhome-core' )               => 'newest',
                    esc_html__( 'Price (high to low)', 'myhome-core' )  => 'priceHighToLow',
                    esc_html__( 'Price (low to high)', 'myhome-core' )  => 'priceLowToHigh',
                    esc_html__( 'Popular', 'myhome-core' )  			=> 'popular',
                ),
                'save_always'   => true
            ),
            // Agent
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Agent', 'myhome-core' ),
                'param_name'    => 'agent_id',
                'value'         => $agents_list,
                'save_always'   => true
            ),
            // Visible
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Visible', 'myhome-core' ),
                'param_name'    => 'owl_visible',
                'value'         => array(
                    esc_html__( 'Default - 3', 'myhome-core' )  => 'owl-carousel--visible-3',
                    esc_html__( '1 ', 'myhome-core' )  			=> 'owl-carousel--visible-1',
                    esc_html__( '2 ', 'myhome-core' ) 			=> 'owl-carousel--visible-2',
                    esc_html__( '3 ', 'myhome-core' )  			=> 'owl-carousel--visible-3',
                ),
                'save_always'   => true
            ),
            // Dots
            array(
                'group'         => esc_html__( 'General', 'myhome-core' ),
                'type'          => 'dropdown',
                'heading'       => esc_html__( 'Dots', 'myhome-core' ),
                'param_name'    => 'owl_dots',
                'value'         => array(
                    esc_html__( 'Yes', 'myhome-core' )  => '',
                    esc_html__( 'No', 'myhome-core' )  	=> 'owl-carousel--no-dots',
                ),
                'save_always'   => true
            ),
        );
        foreach ( My_Home_Attribute::get_attributes() as $attr ) {
            if ( ! $attr->like_tags() ) {
                if ( $attr->get_form_control() == 'text_range' || $attr->get_form_control() == 'select_range' ) {
                    array_push(
                        $fields, array(
                               'type'        => $attr->get_vc_type(),
                               'heading'     => sprintf( esc_html__( '%s from', 'myhome-core' ), $attr->get_name() ),
                               'param_name'  => $attr->get_slug() . '_from',
                               'group'       => esc_html__( 'Default values', 'myhome-core' ),
                               'save_always' => true,
                               'value'       => $attr->get_vc_values()
                               )
                    );
                    array_push(
                        $fields, array(
                               'type'        => $attr->get_vc_type(),
                               'heading'     => sprintf( esc_html__( '%s to', 'myhome-core' ), $attr->get_name() ),
                               'param_name'  => $attr->get_slug() . '_to',
                               'group'       => esc_html__( 'Default values', 'myhome-core' ),
                               'save_always' => true,
                               'value'       => $attr->get_vc_values()
                           )
                    );
                }
                else {
                    array_push(
                        $fields, array(
                               'type'        => $attr->get_vc_type(),
                               'heading'     => $attr->get_name(),
                               'param_name'  => $attr->get_slug(),
                               'group'       => esc_html__( 'Default values', 'myhome-core' ),
                               'save_always' => true,
                               'value'       => $attr->get_vc_values()
                           )
                    );
                }
            }
        }

        return array(
            'name'      => esc_html__( 'Property Carousel', 'myhome-core' ),
            'base'      => 'mh_carousel_estate',
            'icon'      => plugins_url( 'myhome-core/public/img/vc-icon.png' ),
            'category'  => esc_html__( 'MyHome', 'myhome-core' ),
            'params'    => $fields
        );
    }

    /**
     * init
     *
     * Generate shortcode output base on provided options
     */
    public function init( $opts ) {
        $atts = array(
            'align'   	    => '',
            'estate_style'  => '',
            'limit'        	=> 5,
            'owl_visible'   => 'owl-carousel--visible-3',
            'owl_dots'      => '',
            'sort'          => '',
            'estates__in'   => '',
            'agent_id'      => '',
            'featured'      => '',
            'objects'       => true
        );
        if ( function_exists( 'vc_map_get_attributes' ) ) {
            $atts = array_merge( $atts, vc_map_get_attributes( 'mh_carousel_estate', $opts ) );
        }

        if ( ! empty( My_Home_Core()->lang ) ) {
            $atts['lang'] = My_Home_Core()->lang;
        }

        $atts['featured'] = $atts['featured'] == 'true';
        $carousel_estates = My_Home_API::get( $atts );
        $translations = json_encode( My_Home_Translations::get_compare_button() );
        $options = get_option( 'myhome_redux' );
        $class = $atts['owl_visible'] . ' ' . $atts['owl_dots'];
        if ( count( $carousel_estates ) == 0 ) {
            return '';
        }
        ob_start();
        ?>
		<div class="mh-carousel owl-carousel <?php echo esc_attr( $class ); ?>">
		<?php foreach ( $carousel_estates['estates'] as $estate ) : ?>
			<div class="item">
                <article class="mh-estate-vertical <?php echo esc_attr( $atts['estate_style'] ); ?>">
                    <a href="<?php echo esc_url( $estate->get_link() ); ?>"
                       title="<?php echo esc_attr( $estate->get_name() ); ?>"
                       class="mh-thumbnail">
                        <span class="mh-thumbnail__inner">
                        <?php if ( $estate->has_image() ) :
                            My_Home_Image::the_image(
                                $estate->get_image_id(),
                                'standard',
                                $estate->get_name()
                            );
                        endif; ?>
                        </span>

                        <?php if ( ! empty( $options['mh-offer_type'] ) && $options['mh-offer_type'] ): ?>
                            <div class="mh-caption">
                                <div class="mh-caption__inner">
                                    <?php echo esc_html( $estate->get_offer_type() ); ?>
                                </div>
                            </div>
                        <?php
                        endif;

                        $labels = $estate->get_labels();
                        if ( count( $labels ) ) :
                            foreach ( $labels as $label ) : ?>
                                <div class="mh-caption mh-caption__position-'<?php echo esc_attr( $label['position'] ); ?>'">
                                    <div class="mh-caption__inner"
                                         style="color:<?php echo esc_attr( $label['color'] ); ?>;
                                                 background-color:<?php echo esc_attr( $label['bg_color'] ); ?>;">
                                        <?php echo esc_html( $label['name'] ); ?>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif; ?>

                        <div class="mh-estate-vertical__text">
                            <div class="mh-estate-vertical__text__inner">
                                <?php echo esc_html( $estate->get_excerpt() ); ?>
                            </div>
                        </div>
                    </a>

                    <div class="mh-estate-vertical__content">
                        <h3 class="mh-estate-vertical__heading">
                            <a href="<?php echo esc_url( $estate->get_link() ); ?>"
                               title="<?php echo esc_attr( $estate->get_name() ); ?>">
                                <?php echo esc_html( $estate->get_name() ); ?>
                            </a>
                        </h3>
                        <address class="mh-estate-vertical__subheading">
                            <?php echo esc_html( $estate->get_address() ); ?>
                        </address>
						<div class="mh-estate-vertical__primary">
							<?php echo esc_html( $estate->get_price() ); ?>
						</div>

                        <div>
                            <div class="mh-estate__list">
                                <?php foreach ( $estate->get_attributes() as $attr ) :
                                    if ( ! $attr->card ) {
                                        continue;
                                    } ?>

                                    <span class="mh-estate-vertical__more-info">
                                        <strong><?php echo esc_html( $attr->name ); ?>:</strong>
                                        <?php
                                        if ( $attr->has_archive ) :
                                            foreach ( $attr->elements as $key => $element ) :
                                                echo esc_html( $key ? ', ' : '' ); ?>
                                                    <?php echo esc_html( $element->name ); ?>
                                                <?php
                                            endforeach;
                                        else :
                                            foreach ( $attr->elements as $key => $myhome_element ) :
                                                echo ( $key ? ', ' : '' ) .  esc_html( $myhome_element->name );
                                                if ( ! empty( $attr->display_after ) ) {
                                                    echo esc_html( ' ' . $attr->display_after );
                                                }
                                            endforeach;
                                        endif;
                                        ?>
                                    </span>

                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mh-estate-vertical__bottom">
                            <div class="mh-estate-vertical__bottom__inner">
								<div class="mh-estate-vertical__date">
									<?php echo esc_html( $estate->get_added() ); ?>
								</div>
                                <div class="mh-estate-vertical__buttons-wrapper">
                                    <div class="mh-estate-vertical__buttons">
                                        <div class="mh-estate-vertical__buttons__single">
                                            <compare-button class="myhome-compare"
                                                            :translations='<?php echo esc_attr( $translations ); ?>'
                                                            :estate='<?php echo esc_attr( json_encode(
                                                                $estate->get_json_data()
                                                            ) ); ?>'>
                                            </compare-button>
                                        </div>
                                        <div class="mh-estate-vertical__buttons__single">
                                            <a href="<?php echo esc_url( $estate->get_link() ); ?>"
                                               title="<?php echo esc_attr( $estate->get_name() ); ?>"
                                               class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--primary-ghost">
                                                <?php esc_html_e( 'Details', 'myhome-core' );  ?>
                                                <span class="mdl-button__icon-right"><i class="fa fa-angle-right"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}

endif;
