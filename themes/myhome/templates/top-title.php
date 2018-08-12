<?php

$myhome_top = true;
$myhome_top_title_class = array( 'mh-top-title' );
$myhome_top_title_title = '';
$myhome_top_title_text = '';
$myhome_top_title_background = '';
$myhome_options = get_option( 'myhome_redux' );

if ( is_home() ) :
    if ( ! class_exists( 'ReduxFramework' )
        || ( ! empty( $myhome_options['mh-top-title-show'] ) && $myhome_options['mh-top-title-show'] ) ) :
        $myhome_top_title_title = get_bloginfo( 'name' );
        $myhome_top_title_text  = get_bloginfo( 'description' );
        $myhome_top_title_style = My_Home_Theme()->layout->top_title_style();
        if ( $myhome_top_title_style == 'image' ) :
            $myhome_top_title_background = My_Home_Theme()->layout->get_top_title_background_image_url();
            if ( ! empty( $myhome_top_title_background ) ) {
                array_push( $myhome_top_title_class, 'mh-top-title--image-background' );
            }
        endif;
    else :
        $myhome_top = false;
    endif;
elseif ( is_category() || is_tag() || ( is_archive() && ! is_tax() && ! is_author() ) ) :
    if ( ! class_exists( 'ReduxFramework' )
        || ( ! empty( $myhome_options['mh-top-title-show'] ) && $myhome_options['mh-top-title-show'] ) ) :
        $myhome_top_title_title = get_the_archive_title();
        $myhome_top_title_text = get_the_archive_description();
        $myhome_top_title_style = My_Home_Theme()->layout->top_title_style();
        if ( $myhome_top_title_style == 'image' ) :
            $myhome_top_title_background = My_Home_Theme()->layout->get_top_title_background_image_url();
            if ( ! empty( $myhome_top_title_background ) ) {
                array_push( $myhome_top_title_class, 'mh-top-title--image-background' );
            }
        endif;
    else:
        $myhome_top = false;
    endif;
elseif ( is_singular( 'post' ) ) :
    if ( ! class_exists( 'ReduxFramework' )
        || ( ! empty( $myhome_options['mh-top-title-show'] ) && $myhome_options['mh-top-title-show'] ) ) :
        $myhome_top_title_title = esc_html__( 'Blog', 'myhome' );
        $myhome_top_title_style = My_Home_Theme()->layout->top_title_style();
        if ( $myhome_top_title_style == 'image' ) :
            $myhome_top_title_background = My_Home_Theme()->layout->get_top_title_background_image_url();
            if ( ! empty( $myhome_top_title_background ) ) {
                array_push( $myhome_top_title_class, 'mh-top-title--image-background' );
            }
        endif;
    else:
        $myhome_top = false;
    endif;
elseif ( is_singular( 'page' ) ) :
    $myhome_top_title_title = get_the_title();
elseif ( is_search() ) :
    $myhome_top_title_title = esc_html__( 'Search result for ', 'myhome' ) . get_search_query();
elseif ( is_tax() ) :
    global $myhome_term;

    $myhome_top_title_title = $myhome_term->get_name();
    $myhome_top_title_text = $myhome_term->get_description();
    if ( $myhome_term->has_image_wide() ) :
        array_push( $myhome_top_title_class, 'mh-top-title--parallax' );
        $myhome_top_title_background = $myhome_term->get_image_wide();
    endif;
elseif ( is_author() ) :
    global $myhome_agent;

    array_push( $myhome_top_title_class, 'mh-top-title--author' );
endif;

if ( ! empty( $myhome_top_title_background ) ) :
    $myhome_top_title_background = "background-image: url('$myhome_top_title_background');";
endif;

if ( $myhome_top ) :
?>
<section class="<?php echo esc_attr( implode( ' ', $myhome_top_title_class ) ); ?>"
         style="<?php echo esc_attr( $myhome_top_title_background ); ?>">
    <?php if ( ! empty( $myhome_top_title_title ) ) : ?>
        <h1 class="mh-top-title__heading"><?php echo esc_html( $myhome_top_title_title ); ?></h1>
    <?php endif; ?>

    <?php if ( ! empty( $myhome_top_title_text ) ) : ?>
        <div class="mh-top-title__subheading"><?php echo esc_html( $myhome_top_title_text ); ?></div>
    <?php endif; ?>

        <?php if ( is_author() ): ?>
        <div class="mh-layout">
            <div class="position-relative">
                <?php if ( $myhome_agent->has_image() ) : ?>
                    <div class="mh-top-title__avatar">
                        <?php My_Home_Image::the_image(
                            $myhome_agent->get_image_id(),
                            'square',
                            $myhome_agent->get_name() ); ?>
                    </div>
                <?php endif; ?>
                <div class="mh-top-title__author-info">
                    <div class="mh-top-title__author-info__content">
                        <h1 class="mh-top-title__heading">
                            <?php echo esc_html( $myhome_agent->get_name() ); ?>
                        </h1>
                        <?php if ( $myhome_agent->has_phone() || $myhome_agent->has_email() ) : ?>
                            <div class="mh-agent-contact">
                                <?php if ( $myhome_agent->has_email() ) : ?>
                                    <div class="mh-agent-contact__element">
                                        <a href="mailto:<?php echo esc_attr( $myhome_agent->get_email() ); ?>">
                                            <i class="flaticon-mail-2"></i>
                                            <?php echo esc_html( $myhome_agent->get_email() ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $myhome_agent->has_phone() ) : ?>
                                    <div class="mh-agent-contact__element">
                                        <a href="tel:<?php echo esc_attr( $myhome_agent->get_phone_href() ); ?>">
                                            <i class="flaticon-phone"></i>
                                            <?php echo esc_html( $myhome_agent->get_phone() ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif;

                        $myhome_agent_facebook  = $myhome_agent->get_facebook();
                        $myhome_agent_twitter   = $myhome_agent->get_twitter();
                        $myhome_agent_instagram = $myhome_agent->get_instagram();
                        $myhome_agent_linkedin  = $myhome_agent->get_linkedin();

                        if ( ! empty( $myhome_agent_facebook ) || ! empty( $myhome_agent_twitter )
                            || ! empty( $myhome_agent_instagram ) || ! empty( $myhome_agent_linkedin ) ) : ?>
                        <div class="mh-top-title__social-icons">
                            <?php if ( ! empty( $myhome_agent_facebook ) ) : ?>
                                <a href="<?php echo esc_url( $myhome_agent->get_facebook() ); ?>" target="_blank">
                                    <i class="fa fa-facebook"></i>
                                </a>
                            <?php endif;

                            if ( ! empty( $myhome_agent_twitter ) ) :  ?>
                                <a href="<?php echo esc_url( $myhome_agent->get_twitter() ); ?>" target="_blank">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            <?php endif;

                            if ( ! empty( $myhome_agent_instagram ) ) : ?>
                                <a href="<?php echo esc_url( $myhome_agent->get_instagram() ); ?>" target="_blank">
                                    <i class="fa fa-instagram"></i>
                                </a>
                            <?php endif;

                            if ( ! empty( $myhome_agent_linkedin ) ) : ?>
                                <a href="<?php echo esc_url( $myhome_agent->get_linkedin() ); ?>" target="_blank">
                                    <i class="fa fa-linkedin"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        endif;
    endif;
    ?>
</section>
<?php
endif;

