<?php
get_header();

global $myhome_term;
$myhome_term = My_Home_Term::get_term();
get_template_part( 'templates/top-title' );
?>
<div class="mh-layout mh-top-title-offset">
    <?php $myhome_term->listing(); ?>
</div>
<?php
get_footer();