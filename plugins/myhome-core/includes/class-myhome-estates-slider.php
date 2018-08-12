<?php
/*
 * My_Home_Estates_Slider class
 *
 * This class helps setup Revolution Slider for Estates Slier Shortcode (and Visual Composer element).
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

if ( ! class_exists( 'My_Home_Estates_Slider' ) ) :

class My_Home_Estates_Slider {

    private $style;
    private $estates;
    private $has_listing;

    public function __construct( $estates = array(), $content = null, $style = 'estate_slider_card', $css = '' ) {
        $this->estates      = $estates;
        $this->style        = $style;
        $this->has_listing  = ! empty( $content );
    }

    /*
     * set_style
     *
     * Define which style should be used
     */
    public function set_style( $style ) {
        $this->style = $style;
    }

    /*
     * set_estates
     *
     * Load estates (Array of My_Home_Estate objects)
     */
    public function set_estates( $estates ) {
        $this->estates = $estates;
    }

    /*
     * render
     *
     * Final output based on settings
     */
    public function render( $content ) {
        if ( ! count( $this->estates ) ) {
            return;
        }

        ob_start();
        ?>
        <div class="rev_slider_estate_placeholder">
            <div class="rev_slider_wrapper fullwidthbanner-container">
                <div id="<?php echo esc_attr( $this->style ); ?>" class="rev_slider myhome-rev_slider"
                     data-version="5.3.0">
                    <ul>
                        <?php foreach ( $this->estates as $key => $estate ) :
                            if ( $this->style == 'estate_slider_card' ) : ?>
                                <li data-index="<?php echo esc_attr( $key ); ?>"
                                    data-link="<?php echo esc_url( $estate->get_link() ); ?>"
                                    data-transition="slidehorizontal"
                                    data-slotamount="default"
                                    data-hideafterloop="0"
                                    data-hideslideonmobile="off"
                                    data-easein="default"
                                    data-easeout="default"
                                    data-masterspeed="600"
                                    data-thumb="<?php the_post_thumbnail_url( 'myhome-estate-min' ); ?>"
                                    data-rotate="0"
                                    data-saveperformance="off"
                                    data-title="<?php echo esc_html( $estate->get_name() ); ?>"
                                    data-description=""
                                    <?php if ( ! $key ) : ?>
                                        data-fstransition="fade" data-fsmasterspeed="300"
                                    <?php endif; ?>>
                                    <img src="<?php
                                        echo esc_url(
                                            wp_get_attachment_image_url( $estate->get_image_id(), 'mh-standard-xxxl' )
                                        ); ?>"
                                     alt="<?php echo esc_attr( $estate->get_name() ); ?>">

                                    <div class="tp-caption tp-shape tp-shapewrapper  tp-resizeme mh-mask-dark"
                                         data-x="['center','center','center','center','center']" data-hoffset="['0','0','0','0','0']"
                                         data-y="['middle','middle','middle','middle','middle']" data-voffset="['0','0','0','0','0']"
                                         data-width="full"
                                         data-height="full"
                                         data-whitespace="nowrap"
                                         data-type="shape"
                                         data-basealign="slide"
                                         data-responsive_offset="on"
                                         data-frames='[{"delay":0,"speed":300,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                         data-textAlign="['inherit','inherit','inherit','inherit','inherit']"
                                         data-paddingtop="[0,0,0,0,0]"
                                         data-paddingright="[0,0,0,0,0]"
                                         data-paddingbottom="[0,0,0,0,0]"
                                         data-paddingleft="[0,0,0,0,0]"></div>
                                    <div class="tp-caption  "
                                        <?php if ( $this->has_listing ) : ?>
                                            data-x="['left','left','left','center','center']"
                                            data-hoffset="['0','48','0','0','0']"
                                            data-y="['bottom','bottom','bottom','bottom','bottom']"
                                            data-voffset="['160','160','160','136','64']"
                                        <?php else: ?>
                                            data-x="['left','left','left','center','center']"
                                            data-hoffset="['0','0','0','0','0']"
                                            data-y="['bottom','bottom','bottom','bottom','bottom']"
                                            data-voffset="['276','276','226','36','36']"
                                        <?php endif; ?>
                                         data-whitespace="normal"
                                         data-type="text"
                                         data-responsive_offset="off"
                                         data-responsive="off"
                                         data-frames='[{"delay":0,"speed":600,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":600,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                         data-textAlign="['inherit','inherit','inherit','inherit']"
                                         data-paddingtop="[0,0,0,0]"
                                         data-paddingright="[0,0,0,0]"
                                         data-paddingbottom="[0,0,0,0]"
                                         data-paddingleft="[0,0,0,0]">
                                        <div class="mh-slider__card-default">
                                            <h3 class="mh-slider__card-default__heading">
                                                <?php echo esc_html( $estate->get_name() ); ?>
                                            </h3>
                                            <div class="position-relative">
                                                <?php if ( $estate->has_address() ) : ?>
                                                    <address class="mh-slider__card-default__address">
                                                        <i class="flaticon-pin"></i>
                                                        <span>
                                                            <?php echo esc_html( $estate->get_address() ); ?>
                                                        </span>
                                                    </address>
                                                <?php endif; ?>

                                                <?php if ( $estate->has_price() ) : ?>
                                                    <div class="mh-slider__card-default__price">
                                                        <?php echo esc_html( $estate->get_price() ); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            elseif ( $this->style == 'estate_slider_card_short' ) : ?>
                                <li data-index="<?php echo esc_attr( $key ); ?>"
                                    data-link="<?php echo esc_url( $estate->get_link() ); ?>"
                                    data-transition="slidehorizontal"
                                    data-slotamount="default"
                                    data-hideafterloop="0"
                                    data-hideslideonmobile="off"
                                    data-easein="default"
                                    data-easeout="default"
                                    data-masterspeed="600"
                                    data-thumb="<?php the_post_thumbnail_url( 'myhome-estate-min' ); ?>"
                                    data-rotate="0"
                                    data-saveperformance="off"
                                    data-title="<?php echo esc_html( $estate->get_name() ); ?>"
                                    data-description=""
                                    <?php if ( ! $key ) : ?>
                                        data-fstransition="fade" data-fsmasterspeed="300"
                                    <?php endif; ?>>
                                    <img src="<?php
                                    echo esc_url(
                                        wp_get_attachment_image_url( $estate->get_image_id(), 'mh-standard-xxxl' )
                                    ); ?>"
                                         alt="<?php echo esc_attr( $estate->get_name() ); ?>">

                                    <div class="tp-caption tp-shape tp-shapewrapper  tp-resizeme mh-mask-dark"
                                         data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                                         data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']"
                                         data-width="full"
                                         data-height="full"
                                         data-whitespace="nowrap"
                                         data-type="shape"
                                         data-basealign="slide"
                                         data-responsive_offset="on"
                                         data-frames='[{"delay":0,"speed":300,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                         data-textAlign="['inherit','inherit','inherit','inherit']"
                                         data-paddingtop="[0,0,0,0]"
                                         data-paddingright="[0,0,0,0]"
                                         data-paddingbottom="[0,0,0,0]"
                                         data-paddingleft="[0,0,0,0]"></div>
                                    <div class="tp-caption  "
                                        <?php if ( $this->has_listing ) : ?>
                                            data-x="['left','left','left','center','center']"
                                            data-hoffset="['0','0','0','0','0']"
                                            data-y="['bottom','bottom','bottom','bottom','bottom']"
                                            data-voffset="['276','276','226','136','64']"
                                        <?php else: ?>
                                            data-x="['left','left','left','center','center']"
                                            data-hoffset="['0','0','0','0','0']"
                                            data-y="['bottom','bottom','bottom','bottom','bottom']"
                                            data-voffset="['276','276','226','36','36']"
                                        <?php endif; ?>
                                         data-whitespace="normal"
                                         data-type="text"
                                         data-responsive_offset="off"
                                         data-responsive="off"
                                         data-frames='[{"delay":0,"speed":600,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":600,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                         data-textAlign="['inherit','inherit','inherit','inherit']"
                                         data-paddingtop="[0,0,0,0]"
                                         data-paddingright="[0,0,0,0]"
                                         data-paddingbottom="[0,0,0,0]"
                                         data-paddingleft="[0,0,0,0]">
                                        <div class="mh-slider__card-short">
                                            <h3 class="mh-slider__card-short__heading">
                                                <?php echo esc_html( $estate->get_name() ); ?>
                                            </h3>
                                            <?php if ( $estate->has_address() ) : ?>
                                                <address class="mh-slider__card-short__address">
                                                    <i class="flaticon-pin"></i>
                                                    <span>
                                                        <?php echo esc_html( $estate->get_address() ); ?>
                                                    </span>
                                                </address>
                                            <?php endif; ?>
                                            <?php if ( $estate->has_price() ) : ?>
                                                <div class="mh-slider__card-short__price">
                                                    <?php echo esc_html( $estate->get_price() ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php
                            elseif ( $this->style == 'estate_slider_transparent' ) : ?>
                                <li data-index="<?php echo esc_attr( $key ); ?>"
                                    data-link="<?php echo esc_url( $estate->get_link() ); ?>"
                                    data-transition="slidingoverlayhorizontal"
                                    data-slotamount="default"
                                    data-hideafterloop="0"
                                    data-hideslideonmobile="off"
                                    data-easein="Power3.easeInOut"
                                    data-easeout="default"
                                    data-masterspeed="900"
                                    data-thumb="<?php the_post_thumbnail_url( 'myhome-estate-min' ); ?>"
                                    data-rotate="0"
                                    data-saveperformance="off"
                                    data-title="<?php echo esc_attr( $estate->get_name() ); ?>"
                                    <?php if ( ! $key ) : ?>
                                        data-fstransition="fade" data-fsmasterspeed="300"
                                    <?php endif; ?>>

                                    <?php if ( $estate->has_image() ) : ?>
                                        <img src="<?php
                                        echo esc_url(
                                            wp_get_attachment_image_url( $estate->get_image_id(), 'mh-standard-xxxl' )
                                        ); ?>"
                                             alt="<?php echo esc_attr( $estate->get_name() ); ?>">
                                    <?php endif; ?>
                                    <div class="tp-caption tp-shape tp-shapewrapper tp-resizeme mh-mask-strong-dark"
                                         data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
                                         data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']"
                                         data-width="full"
                                         data-height="full"
                                         data-whitespace="normal"
                                         data-type="shape"
                                         data-basealign="slide"
                                         data-responsive_offset="on"
                                         data-frames='[{"delay":0,"speed":300,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"0","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                                         data-textAlign="['inherit','inherit','inherit','inherit']"
                                         data-paddingtop="[0,0,0,0]"
                                         data-paddingright="[0,0,0,0]"
                                         data-paddingbottom="[0,0,0,0]"
                                         data-paddingleft="[0,0,0,0]"></div>
                                    <div class="tp-caption  "
                                        <?php if ( $this->has_listing ) : ?>
                                            data-x="['center','center','center','center','center']"
                                            data-hoffset="['0','0','0','0','0']"
                                            data-y="['center','center','center','center','bottom']"
                                            data-voffset="['0','0','0','0','64']"
                                        <?php else: ?>
                                            data-x="['center','center','center','center','center']"
                                            data-hoffset="['0','0','0','0','0']"
                                            data-y="['center','center','center','bottom','bottom']"
                                            data-voffset="['0','0','0','36','24']"
                                        <?php endif; ?>
                                         data-width="none"
                                         data-height="none"
                                         data-whitespace="normal"
                                         data-type="text"
                                         data-responsive_offset="off"
                                         data-responsive="off"
                                         data-frames='[{"delay":0,"speed":600,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":600,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"}]'
                                         data-textAlign="['inherit','inherit','inherit','inherit']"
                                         data-paddingtop="[0,0,0,0]"
                                         data-paddingright="[0,0,0,0]"
                                         data-paddingbottom="[0,0,0,0]"
                                         data-paddingleft="[0,0,0,0]">
                                        <div class="mh-slider__transparent">
                                            <h3 class="mh-slider__transparent__title">
                                                <?php echo esc_html( $estate->get_name() ); ?>
                                            </h3>

                                            <?php if ( $estate->has_address() ) : ?>
                                                <address class="mh-slider__transparent__address">
                                                    <i class="flaticon flaticon-pin"></i> <?php echo esc_html( $estate->get_address() ); ?>
                                                </address>
                                            <?php endif; ?>

                                            <?php if ( $estate->has_price() ) : ?>
                                                <div class="mh-slider__transparent__price">
                                                    <?php esc_html_e( $estate->get_price() ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            endif;
                        endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <?php if ( $this->has_listing ) : ?>
            <div class="mh-slider__extra-content">
                <?php echo do_shortcode( $content, true ); ?>
            </div>
        <?php endif;

        echo ob_get_clean();
    }

}

endif;