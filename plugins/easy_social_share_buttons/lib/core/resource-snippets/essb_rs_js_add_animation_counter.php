<?php

if (!function_exists('essb_rs_js_add_animation_counter')) {
	
	add_filter('essb_js_buffer_footer', 'essb_rs_js_add_animation_counter');
	
	function essb_rs_js_add_animation_counter($buffer) {
		
		$script = '
		
		jQuery(document).ready(function($){
			$(".essb_counters .essb_animated").each(function() {
				var current_counter = $(this).attr("data-cnt") || "";
				var current_counter_result = $(this).attr("data-cnt-short") || "";
				
				if ($(this).hasClass("essb_counter_hidden")) return;
				
				$(this).countTo({
					from: 1,
					to: current_counter,
					speed: 800,
					onComplete: function (value) {
     					$(this).html(current_counter_result); 
    				}
				});
			});
		});
		';
		$script = trim(preg_replace('/\s+/', ' ', $script));
		return $buffer.$script;
	}
}