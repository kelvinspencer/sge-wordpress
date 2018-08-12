<?php
global $myhome_agent;
$myhome_agent = My_Home_Agent::get_agent();

get_header();
get_template_part( 'templates/top-title' );

?>
<div class="mh-layout mh-top-title-offset">
    <?php $myhome_agent->listing(); ?>
</div>
<?php
get_footer();