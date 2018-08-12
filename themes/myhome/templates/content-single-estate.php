<?php
global $myhome_estate;
global $myhome_agent;
$myhome_agent = $myhome_estate->get_agent();

if ( $myhome_estate->get_slider_name() == 'single-estate-slider' ) :
    if ( $myhome_estate->has_gallery() ) :
        get_template_part( 'templates/single-estate-partials/slider' );
    else :
        if ( get_the_post_thumbnail_url() ): ?>
        <div class="mh-estate__huge-image">
            <a class="mh-popup" href="<?php the_post_thumbnail_url(); ?>"
               title="<?php the_title_attribute(); ?>">
                <div class="mh-estate__huge-image__single mh-background-cover" style="background-image: url('<?php echo the_post_thumbnail_url()?>');"></div>
            </a>
        </div>
        <?php else: ?>
            <div class="mh-estate__huge-no-image">
                <br>
            </div>
        <?php endif ?>
    <?php endif;
endif; ?>

<?php if ( $myhome_estate->get_slider_name() == 'single-estate-gallery' ): ?>

    <div class="mh-top-title mh-top-title--single-estate">
        <div class="mh-layout">
            <h1 class="mh-top-title__heading"><?php echo esc_html( get_the_title() ); ?></h1>
            <?php if ( $myhome_estate->has_address() ) : ?>
                <div class="small-text">
                    <a href="#myhome-estate-map"><i class="flaticon-pin"></i></a>
                    <?php echo esc_html( $myhome_estate->get_address() ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ( $myhome_estate->get_slider_name() == 'single-estate-slider' ): ?>
<div class="position-relative">
    <div class="mh-slider-single">
        <div class="mh-slider-single__content">
            <div class="mh-layout">
                <div class="mh-slider-single__top">
                    <div class="mh-slider-single__name-price">
                        <h1 class="mh-slider-single__name">
                            <?php echo esc_html( get_the_title() ); ?>
                        </h1>
                        <?php if ( $myhome_estate->has_price() ) :  ?>
                        <div class="mh-slider-single__price">
                            <?php echo esc_html( $myhome_estate->get_price() ); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mh-slider-single__bottom">
                    <?php if ( $myhome_estate->has_address() ) :  ?>
                    <div class="mh-slider-single__address">
                        <i class="flaticon-pin"></i>
                        <span><?php echo esc_html( $myhome_estate->get_address() ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $myhome_agent->has_phone() ) : ?>
                        <div class="mh-slider-single__phone">
                            <a href="tel:<?php echo esc_attr( $myhome_agent->get_phone_href() ); ?>">
                                <i class="flaticon-phone"></i>
                                <span><?php echo esc_html( $myhome_agent->get_phone() ); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<article class="mh-layout position-relative">

    <?php if ( $myhome_estate->has_sidebar() ) : ?>
        <div class="mh-layout__content-left">
    <?php else: ?>
        <div class="mh-estate__no-sidebar">
    <?php endif; ?>

        <?php if ( ! $myhome_estate->has_gallery()
            && $myhome_estate->get_slider_name() == 'single-estate-gallery' ) : ?>
            <?php if ( has_post_thumbnail() ): ?>
                <div class="mh-estate__main-image">
                    <a class="mh-popup" href="<?php the_post_thumbnail_url(); ?>"
                       title="<?php the_title_attribute(); ?>">
                        <?php My_Home_Theme()->images->get( 'standard' ); ?>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ( $myhome_estate->has_gallery()
            && $myhome_estate->get_slider_name() == 'single-estate-gallery' ) : ?>
            <div class="mh-estate__slider">
                <?php get_template_part( 'templates/single-estate-partials/gallery' ); ?>
            </div>
        <?php endif; ?>

        <?php if ( $myhome_estate->has_map() || $myhome_estate->has_price() || $myhome_agent->has_phone() ) : ?>
            <div class="mh-display-mobile">
                <div class="position-relative">
                    <div class="mh-estate__details">
                        <?php if ( $myhome_estate->has_gallery()
                            && $myhome_estate->get_slider_name() == 'single-estate-gallery' ) : ?>
                            <?php if ( $myhome_estate->has_price() ) : ?>
                                <div class="mh-estate__details__price">
                                    <?php echo esc_html( $myhome_estate->get_price() ); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ( $myhome_agent->has_phone() ) : ?>
                                <div class="mh-estate__details__phone">
                                    <a href="tel:<?php echo esc_attr( $myhome_agent->get_phone_href() ); ?>">
                                        <i class="flaticon-phone"></i> <?php echo esc_html( $myhome_agent->get_phone() ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ( $myhome_estate->has_map() ) : ?>
                            <div class="mh-estate__details__map">
                                <a href="#map" class="smooth">
                                    <i class="flaticon-pin"></i> <?php esc_html_e( 'See Map', 'myhome' ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $myhome_estate->has_attributes() ) : ?>
            <div class="mh-estate__section">
                <div class="mh-estate__list">
                    <ul class="mh-estate__list__inner">
                    <?php foreach ( $myhome_estate->get_attributes() as $myhome_attr ) :
                        if ( ! $myhome_attr->show || $myhome_attr->new_box ) {
                            continue;
                        } ?>

                        <li class="mh-estate__list__element">
                            <strong><?php echo esc_html( $myhome_attr->name ); ?>:</strong>
                            <?php
                                if ( $myhome_attr->has_archive ) :
                                    foreach ( $myhome_attr->elements as $myhome_key => $element ) :
                                        echo esc_html( $myhome_key ? ', ' : '' ); ?>
                                        <a href="<?php echo esc_url( $element->link ) ?>"
                                           title="<?php echo esc_attr( $element->name ) ?>">
                                           <?php echo esc_html( $element->name ); ?>
                                            <i class="fa fa-external-link" aria-hidden="true"></i>
                                        </a>
                                    <?php
                                    endforeach;
                                else :
                                    foreach ( $myhome_attr->elements as $myhome_key => $myhome_element ) :
                                        echo ( $myhome_key ? ', ' : '' ) .  esc_html( $myhome_element->name );
                                        if ( ! empty( $myhome_attr->display_after ) ) {
                                            echo esc_html( ' ' . $myhome_attr->display_after );
                                        }
                                    endforeach;
                                endif;
                            ?>
                        </li>

                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ( $myhome_estate->get_tags() as $myhome_tag ) : ?>
            <div class="mh-estate__section">
                <h3 class="mh-estate__section__heading">
                    <?php echo esc_html( $myhome_tag->name ); ?>
                </h3>

                <div class="mh-estate__list">
                    <ul class="mh-estate__list__inner">
                    <?php foreach ( $myhome_tag->elements as $key => $myhome_element ) : ?>
                        <li class="mh-estate__list__element mh-estate__list__element--dot">
                            <?php if ( $myhome_tag->has_archive ) : ?>
                            <a href="<?php echo esc_url( $myhome_element->link ); ?>"
                               title="<?php echo esc_attr( $myhome_element->name ); ?>">
                                <?php echo esc_html( $myhome_element->name ); ?>
                            </a>
                            <?php else : ?>
                                <?php echo esc_html( $myhome_element->name ); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="mh-estate__section mh-estate__section--details">
            <h3 class="mh-estate__section__heading"><?php esc_html_e( 'Details', 'myhome' ); ?></h3>
            <?php the_content(); ?>
        </div>

        <?php if ( $myhome_estate->has_video_plan() ) : ?>
            <div class="mh-estate__section">
                <h3 class="mh-estate__section__heading"><?php esc_html_e( 'Video', 'myhome' ); ?></h3>
                <div class="mh-video-wrapper">
                    <?php $myhome_estate->video_plan(); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $myhome_estate->has_virtual_tour() ) : ?>
            <div class="mh-estate__section">
                <h3 class="mh-estate__section__heading"><?php esc_html_e( 'Virtual tour', 'myhome' ); ?></h3>
                <div class="mh-video-wrapper">
                    <?php $myhome_estate->virtual_tour(); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $myhome_estate->has_plans() ) : ?>
            <div class="mh-estate__section">
                <h3 class="mh-estate__section__heading"><?php esc_html_e( 'Plans', 'myhome' ); ?></h3>
                    <div class="mh-accordion">
                        <?php foreach ( $myhome_estate->get_plans() as $myhome_plan ) : ?>
                            <h3>
                                <i class="fa fa-minus"></i> <i class="fa fa-plus"></i>
                                <?php echo esc_html( $myhome_plan['name'] ); ?>
                            </h3>
                            <div>
                            <?php if ( ! empty( $myhome_plan['image'] ) ) : ?>
                                <a class="mh-estate__plan-thumbnail-wrapper mh-popup"
                                   href="<?php echo esc_url( $myhome_plan['image'] ); ?>">
                                    <img data-srcset="<?php echo esc_attr( $myhome_plan['image_srcset'] ); ?>"
                                         alt="<?php echo esc_attr( $myhome_plan['name'] ); ?>"
                                         data-sizes="auto"
                                         class="lazyload">
                                </a>
                            <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
            </div>
        <?php endif; ?>

        <div class="mh-estate__estate-info">
            <ul>
                <li>
                    <span><?php esc_html_e( 'ID:', 'myhome' ) ?></span>
                    <?php the_ID(); ?>
                </li>
                <li>
                    <span><?php esc_html_e( 'Published:', 'myhome' ) ?></span>
                    <?php the_date(); ?>
                </li>
                <li>
                    <span><?php esc_html_e( 'Last Update:', 'myhome' ) ?></span>
                    <?php the_modified_date(); ?>
                </li>
                <li>
                    <span><?php esc_html_e( 'Views:', 'myhome' ) ?></span>
                    <?php echo esc_html( $myhome_estate->get_views() ); ?>
                </li>
            </ul>
        </div>
    </div>

<?php if ( $myhome_estate->has_sidebar() ) : ?>
    <aside class="mh-layout__sidebar-right">

        <div class="mh-display-desktop">
            <div class="position-relative">
                <div class="mh-estate__details">
                    <?php if ( $myhome_estate->has_price() || $myhome_agent->has_phone() ) : ?>
                        <?php if ( $myhome_estate->get_slider_name() == 'single-estate-gallery' ) : ?>
                                <?php if ( $myhome_estate->has_price() ) : ?>
                                    <div class="mh-estate__details__price">
                                        <?php echo esc_html( $myhome_estate->get_price() ); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ( $myhome_agent->has_phone() ) : ?>
                                    <div class="mh-estate__details__phone">
                                        <a href="tel:<?php echo esc_attr( $myhome_agent->get_phone_href() ); ?>">
                                            <i class="flaticon-phone"></i> <?php echo esc_html( $myhome_agent->get_phone() ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( $myhome_estate->has_map() ) : ?>
                        <div class="mh-estate__details__map">
                            <a href="#map" class="smooth">
                                <i class="flaticon-pin"></i> <?php esc_html_e( 'See Map', 'myhome' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ( $myhome_estate->has_contact_form() ) : ?>
        <section>
            <div class="mh-widget-title">
                <h3 class="mh-widget-title__text"><?php esc_html_e( 'Reply to the listing', 'myhome' ); ?></h3>
            </div>
            <contact-form :translations='<?php
                            echo esc_attr( json_encode( My_Home_Translations::get_contact_form())) ?>'
                          :agent-id='<?php echo esc_attr( $myhome_agent->get_ID() ); ?>'
                          :estate-id='<?php echo esc_attr( $myhome_estate->get_ID() ); ?>'
                          site="<?php echo esc_url( site_url() ); ?>"
                          class="myhome-contact-form"></contact-form>
        </section>
        <?php endif; ?>

        <?php if ( $myhome_estate->has_user_profile() ) : ?>
        <section class="mh-estate__agent">
            <div class="mh-widget-title">
                <h3 class="mh-widget-title__text">
                    <a href="<?php echo esc_url( $myhome_agent->get_link() ); ?>"
                       title="<?php echo esc_attr( $myhome_agent->get_name() ); ?>">
                        <?php echo esc_html( $myhome_agent->get_name() ); ?>
                    </a>
                </h3>
            </div>

            <div class="mh-estate__agent__content">

                <?php if ( $myhome_agent->has_image() )  : ?>
                    <a class="mh-estate__agent__thumbnail-wrapper"
                       href="<?php echo esc_url( $myhome_agent->get_link() ); ?>"
                       title="<?php echo esc_attr( $myhome_agent->get_name() ); ?>">
                        <?php
                            My_Home_Image::the_image(
                                $myhome_agent->get_image_id(),
                                'square',
                                $myhome_agent->get_name()
                            );
                        ?>
                    </a>
                <?php endif; ?>

                <div class="position-relative">

                    <?php if ( $myhome_agent->has_phone() ) : ?>
                        <div class="mh-estate__agent__phone">
                            <a href="tel:<?php echo esc_attr( $myhome_agent->get_phone_href() ); ?>">
                                <i class="flaticon-phone"></i><?php echo esc_html( $myhome_agent->get_phone() );  ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ( $myhome_agent->has_email() ) : ?>
                        <div class="mh-estate__agent__email">
                            <a href="mailto:<?php echo esc_attr( $myhome_agent->get_email() );  ?>">
                                <i class="flaticon-mail-2"></i><?php echo esc_html( $myhome_agent->get_email() );  ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php

                    $myhome_agent_facebook  = $myhome_agent->get_facebook();
                    $myhome_agent_twitter   = $myhome_agent->get_twitter();
                    $myhome_agent_instagram = $myhome_agent->get_instagram();
                    $myhome_agent_linkedin  = $myhome_agent->get_linkedin();

                    if ( ! empty( $myhome_agent_facebook ) || ! empty( $myhome_agent_twitter )
                    || ! empty( $myhome_agent_instagram ) || ! empty( $myhome_agent_linkedin ) ) : ?>
                        <div class="mh-estate__agent__social-icons">
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
                    <?php endif; ?>

                    <a href="<?php echo esc_url( $myhome_agent->get_link() ); ?>"
                       title="<?php echo esc_attr( $myhome_agent->get_name() ); ?>"
                       class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--primary mdl-button--full-width">
                        <?php printf( esc_html__( 'All by %s', 'myhome'), $myhome_agent->get_name() ); ?>
                    </a>

                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php dynamic_sidebar( 'mh-property-sidebar' ); ?>
    </aside>
<?php endif; ?>
</article>
<?php if ( $myhome_estate->has_map() ) : ?>
    <?php $myhome_estate->map(); ?>
<?php endif; ?>
