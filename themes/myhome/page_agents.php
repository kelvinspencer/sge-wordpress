<?php
/**
 * Template Name: Agent Frontend Panel
 */

get_header(); ?>

<?php if ( class_exists( 'My_Home_Core' ) ) : ?>
<div id="myhome-user-window" class="mh-user-panel-wrapper">
    <user-window url="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
                 :registration='<?php echo esc_attr( My_Home_Theme()->layout->is_frontend_registration_open() ); ?>'
                 :translations='<?php echo esc_attr( json_encode( My_Home_Translations::get_frontend_admin() ) ); ?>'>
    </user-window>
</div>
<?php endif; ?>

<?php get_footer();