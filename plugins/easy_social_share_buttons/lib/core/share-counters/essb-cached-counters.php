<?php
/**
 * Cached Counters Update Functions
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2017 AppsCreo
 * @since 4.2
 *
 */

class ESSBCachedCounters {
	
	public static function prepare_list_of_networks_with_counter($networks, $active_networks_list) {		
		$basic_network_list = 'twitter,linkedin,facebook,pinterest,google,stumbleupon,vk,reddit,buffer,love,ok,mwp,xing,mail,print,comments,yummly';
		
		// updated in version 4.2 - now we have only avoid with counter networks
		$avoid_network_list = 'more,share,subscribe';
		
		$internal_counters = essb_option_bool_value('active_internal_counters');
		$no_mail_print_counter = essb_option_bool_value('deactive_internal_counters_mail');
		$twitter_counter = essb_option_value('twitter_counters');
		
		if ($twitter_counter == '')  {$twitter_counter = 'api'; }
		
		$basic_array = explode(',', $basic_network_list);
		$avoid_array = explode(',', $avoid_network_list);

		$count_networks = array();
		
		foreach ($networks as $k) {
			
			if (!in_array ( $k, $active_networks_list)) {
				continue;
			}
			
			if (in_array($k, $basic_array)) {
				if ($k == 'print' || $k == 'mail') {
					if (!$no_mail_print_counter) {
						$count_networks[] = $k;
					}
				}
				else {
					$count_networks[] = $k;
				}
 			}
 			
 			if (!in_array($k, $basic_array) && $internal_counters && !in_array($k, $avoid_array)) {
 				$custom_network_avoid = false;
 				$custom_network_avoid = apply_filters("essb4_no_counter_for_{$k}", $custom_network_avoid);
 				if (!$custom_network_avoid) {
 					$count_networks[] = $k;
 				}
 			}
		}		
		
		
		return $count_networks;
	}
	
	public static function is_fresh_cache($post_id) {		
		$is_fresh = true;
		
		if (isset ( $_SERVER ['HTTP_USER_AGENT'] ) && preg_match ( '/bot|crawl|slurp|spider/i', $_SERVER ['HTTP_USER_AGENT'] )) {
			$is_fresh = true;
		}
		else {
			$expire_time = get_post_meta ( $post_id, 'essb_cache_expire', true );
			$now = time ();
			
			$is_alive = ($expire_time > $now);
			
			if (true == $is_alive) {
				$is_fresh = true;
			}
			else {
				$is_fresh = false;
			}
		}

		$user_call_refresh = isset ( $_REQUEST ['essb_counter_update'] ) ? $_REQUEST ['essb_counter_update'] : '';
		if ($user_call_refresh == 'true') {
			$is_fresh = false;
		}
		
		return $is_fresh;
	}
		
	public static function all_socaial_networks() {
		$result = array();
		
		$networks = essb_available_social_networks();
		foreach ($networks as $key => $data) {
			$result[] = $key;
		}
		
		return $result;
	}
	
	public static function get_counters($post_id, $share = array(), $networks) {
		
		$cached_counters = array();
		$cached_counters['total'] = 0;
		
		if (!ESSBCachedCounters::is_fresh_cache($post_id)) {
			// since 4.2 we give option to display each time total counter based on all
			// social networks
			
			if (essb_option_bool_value('total_counter_all')) {
				$networks = self::all_socaial_networks();
			}
			
			$cached_counters = ESSBCachedCounters::update_counters($post_id, $share['url'], $share['full_url'], $networks);
			
			if (defined('ESSB3_SHARED_COUNTER_RECOVERY')) {
				
				$recovery_till_date = essb_option_value('counter_recover_date');
				$is_applying_for_recovery = true;
				
				// @since 3.4 - apply recovery till provided date only
				if (!empty($recovery_till_date)) {
					$is_applying_for_recovery = essb_recovery_is_matching_recovery_date($post_id, $recovery_till_date);
				}
				
				if ($is_applying_for_recovery) {
					$current_url = $share['full_url'];
					// get post meta recovery value
					// essb_activate_sharerecovery - post meta recovery address
					$post_essb_activate_sharerecovery = get_post_meta($post_id, 'essb_activate_sharerecovery', true);
					if (!empty($post_essb_activate_sharerecovery)) {
						$current_url = $post_essb_activate_sharerecovery;
					}
					else {
						$current_url = essb_recovery_get_alternate_permalink($current_url, $post_id);
					}					
					
					$recovery_counters = ESSBCachedCounters::update_counters($post_id, $current_url, $current_url, $networks, true);
					
					
					$cached_counters = essb_recovery_consolidate_results($cached_counters, $recovery_counters, $networks);
				}
			}
			
			$total_saved = false;
			foreach ($networks as $k) {
				
				if ($k == 'total') $total_saved = true;
				
				$single = isset($cached_counters[$k]) ? $cached_counters[$k] : '0';
				if (intval($single) > 0) {
					update_post_meta($post_id, 'essb_c_'.$k, $single);
				}
				else {
					$cached_counters[$k] =  intval(get_post_meta($post_id, 'essb_c_'.$k, true));
				}
			}
			
			if (!$total_saved) {
				$k = 'total';
				$single = isset($cached_counters[$k]) ? $cached_counters[$k] : '0';
				if (intval($single) > 0) {
					update_post_meta($post_id, 'essb_c_'.$k, $single);
				}
				else {
					$cached_counters[$k] =  intval(get_post_meta($post_id, 'essb_c_'.$k, true));
				}
			}
		}		
		else {
			foreach ($networks as $k) {
				$cached_counters[$k] = get_post_meta($post_id, 'essb_c_'.$k, true);
				$cached_counters['total'] += intval($cached_counters[$k]);
			}
		}		
		
		
		if (has_filter('essb4_get_cached_counters')) {
			$cached_counters = apply_filters('essb4_get_cached_counters', $cached_counters);
		}
		
		return $cached_counters;
	}
	
	public static function update_counters($post_id, $url, $full_url, $networks = array(), $recover_mode = false) {
		
		$twitter_counter = essb_options_value('twitter_counters');
		
		// changed in 4.2 to use internal counter when nothing is selected
		if ($twitter_counter == '')  {
			$twitter_counter = 'self';
		}
		
		$async_update_mode = essb_option_bool_value('cache_counter_refresh_async');
		
		if (!$async_update_mode) {
			essb_depend_load_function('essb_counter_request', 'lib/core/share-counters/essb-counter-update.php');
			$cached_counters = essb_counter_update_simple($post_id, $url, $full_url, $networks, $recover_mode, $twitter_counter);
		}
		else {
			essb_depend_load_class('ESSBAsyncShareCounters', 'lib/core/share-counters/essb-counter-update-async.php');
			$counter_parser = new ESSBAsyncShareCounters($post_id, $url, $full_url, $networks, $recover_mode, $twitter_counter);
			$cached_counters = $counter_parser->get_counters();
		}
		
		if (!$recover_mode) {
			//$time = floor(((date('U')/60)/60));
			//update_post_meta($post_id, 'essb_cache_timestamp', $time);
			// changed to cache_counter_refresh_new counter_mode
			//$expire_time = ESSBOptionValuesHelper::options_value($essb_options, 'counter_mode');
			$expire_time = essb_option_value('counter_mode');
			if ($expire_time == '') { $expire_time = 60; }
			update_post_meta ( $post_id, 'essb_cache_expire', (time () + ($expire_time * 60)) );
		}		
		
		return $cached_counters;
	}

	
}

?>