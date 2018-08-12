<?php
global $myhome_estate;

$myhome_estate = My_Home_Estate::get_estate();
$myhome_estate->update_views();

get_header();

if ( have_posts() ): ?>

	<article id="post-<?php echo esc_attr( get_the_ID() ); ?>">
        <?php
            while ( have_posts() ) : the_post();
                get_template_part( 'templates/content-single', 'estate' );
            endwhile;
        ?>
	</article>

<?php
endif;

get_footer();
