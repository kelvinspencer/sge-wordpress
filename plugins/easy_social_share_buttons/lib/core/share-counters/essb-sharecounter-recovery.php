<?php
/**
 * Share counter recovery functions
 * 
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 4.2
 *
 */

function essb_recovery_is_matching_recovery_date($post_id, $recover_till_date) {
	$is_matching = true;

	$post_publish_date = get_the_date("Y-m-d", $post_id);

	if (!empty($post_publish_date)) {
		$recover_till_time = strtotime($recover_till_date);
		$post_publish_time = strtotime($post_publish_date);
			
		if ($post_publish_time < $recover_till_time) {
			$is_matching = true;
		}
		else {
			$is_matching = false;
		}
	}

	return $is_matching;
}


function essb_recovery_get_alternate_permalink($url, $id) {

	global $essb_options;

	$new_url = $url;

	$recover_mode = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_mode');
	$recover_protocol = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_protocol');
	$recover_from_other_domain = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_domain');
	$recover_from_new_domain = ESSBOptionValuesHelper::options_value($essb_options, 'counter_recover_newdomain');
	$counter_recover_slash = ESSBOptionValuesHelper::options_bool_value($essb_options, 'counter_recover_slash');

	$new_url = apply_filters( 'essb4_recovery_filter', $new_url );
	
	if (empty($recover_from_new_domain) && $recover_mode == "domain") {
		$recover_from_new_domain = get_site_url();
	}

	// Setup the Default Permalink Structure
	if($recover_mode == 'default') {
		$domain = get_site_url();
		$new_url = $domain.'/?p='.$id;
	}

	// Setup the "Day and name" Permalink Structure
	if ($recover_mode == 'dayname') {
		$domain = get_site_url();
		$date = get_the_date('Y/m/d',$id);
		$slug = basename(get_permalink($id));
		$new_url = $domain.'/'.$date.'/'.$slug.'/';
	}
	// Setup the "Month and name" Permalink Structure
	if ($recover_mode == 'monthname') {
		$domain = get_site_url();
		$date = get_the_date('Y/m',$id);
		$slug = basename(get_permalink($id));
		$new_url = $domain.'/'.$date.'/'.$slug.'/';
	}
	// Setup the "Numeric" Permalink Structure
	if ($recover_mode == 'numeric') {
		$domain = get_site_url();
		$new_url = $domain.'/archives/'.$id.'/';
	}
	// Setup the "Post name" Permalink Structure
	if ($recover_mode == 'postname') {
		$domain = get_site_url();
		$post_data = get_post($id, ARRAY_A);
		$slug = $post_data['post_name'];
		$new_url = $domain.'/'.$slug.'/';
	}

	if ($recover_mode == "domain" && !empty($recover_from_other_domain)) {
		$current_site_url = get_site_url();
		if (!empty($recover_from_new_domain)) {
			$current_site_url = $recover_from_new_domain;
		}
		$new_url = str_replace($current_site_url, $recover_from_other_domain, $url);
	}


	if ($recover_protocol == "http2https") {
		$new_url = str_replace('https://','http://',$new_url);
	}

	if ($recover_protocol == "https2http") {
		$new_url = str_replace('http://','https://',$new_url);
	}

	if ($counter_recover_slash) {
		$new_url = rtrim($new_url,"/");
	}

	return $new_url;

}


function essb_recovery_consolidate_results($share_values, $additional_values, $networks) {
	$new_result = array();
	$new_result['total'] = 0;

	foreach ($networks as $k) {
		$one_share = isset($share_values[$k]) ? $share_values[$k] : 0;
		$two_share = isset($additional_values[$k]) ? $additional_values[$k] : 0;
			
		$new_result[$k] = intval($one_share) + intval($two_share);
			
		$new_result['total'] += intval($one_share) + intval($two_share);
	}

	return $new_result;
}