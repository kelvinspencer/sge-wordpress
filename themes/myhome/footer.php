<?php
$myhome_footer_class = '';
$myhome_footer_bottom_class = '';
$myhome_footer_style = My_Home_Theme()->layout->footer_style();

if ( My_Home_Theme()->layout->has_footer_background_image_parallax() ) :
    $myhome_footer_class .= ' mh-background-fixed';
endif;

if ( $myhome_footer_style == 'dark' || $myhome_footer_style == 'image' ) :
    $myhome_footer_class .= ' mh-footer-top--dark';
endif;

if ( $myhome_footer_style == 'image' ) :
    $myhome_footer_bottom_class .= ' mh-footer-bottom--transparent';
	$myhome_footer_style = '';
    $myhome_footer_style .= ' background-image:url('.esc_url( My_Home_Theme()->layout->get_footer_background_image_url() ).');';
endif;
?>

<footer class="mh-footer-top mh-background-cover <?php echo esc_attr( $myhome_footer_class ); ?>"
        style="<?php echo esc_attr( $myhome_footer_style ); ?>">

	<?php if ( My_Home_Theme()->layout->has_footer_widget_area_show() ) : ?>
	<div class="mh-footer__inner">
		<div class="mh-layout">
			<div class="mh-footer__row">

			<?php if ( My_Home_Theme()->layout->has_footer_widget_area_footer_information() ) : ?>

				<div class="mh-footer__row__column widget">
					<?php if ( My_Home_Theme()->layout->has_footer_logo() ) : ?>
						<div class="mh-footer__logo">
							<img src="<?php echo esc_url( My_Home_Theme()->layout->get_footer_logo() ); ?>"
								 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						</div>
					<?php endif; ?>

					<?php if ( My_Home_Theme()->layout->has_footer_text() ) : ?>
						<div class="mh-footer__text">
							<?php echo esc_html( My_Home_Theme()->layout->get_footer_text() ); ?>
						</div>
					<?php endif; ?>

					<?php if ( My_Home_Theme()->layout->has_footer_address() ) : ?>
						<address class="mh-footer__contact">
							<i class="flaticon-pin"></i>
							<?php echo esc_html( My_Home_Theme()->layout->get_footer_address() ); ?>
						</address>
					<?php endif; ?>

					<?php if ( My_Home_Theme()->layout->has_footer_phone() ) : ?>
						<div class="mh-footer__contact">
							<a href="tel:<?php echo esc_attr( My_Home_Theme()->layout->get_footer_phone_href() ) ?>">
								<i class="flaticon-phone"></i>
								<?php echo esc_html( My_Home_Theme()->layout->get_footer_phone() ); ?>
							</a>
						</div>
					<?php endif; ?>

					<?php if ( My_Home_Theme()->layout->has_footer_email() ) : ?>
						<div class="mh-footer__contact">
							<a href="mailto:<?php echo esc_attr( My_Home_Theme()->layout->get_footer_email() ) ?>">
								<i class="flaticon-mail-2"></i>
								<?php echo esc_html( My_Home_Theme()->layout->get_footer_email() ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>

			<?php endif; ?>

			<?php get_template_part( 'templates/sidebar', 'footer' ); ?>
			</div>






		</div>
		
	</div>
	<?php endif; ?>



	<?php if ( My_Home_Theme()->layout->has_footer_copyright_area_show() ) : ?>

        <div class="mh-footer-bottom <?php echo esc_attr( $myhome_footer_bottom_class ); ?>">
            <div class="mh-layout">
            <?php if ( My_Home_Theme()->layout->has_footer_copyright_text() ) : ?>
                <?php echo My_Home_Theme()->layout->get_footer_copyright_text(); ?>
            <?php endif; ?>
            </div>
        </div>

	<?php endif; ?>
</footer>
<?php
My_Home_Theme()->layout->compare();
wp_footer();
?>
</body>
</html>
