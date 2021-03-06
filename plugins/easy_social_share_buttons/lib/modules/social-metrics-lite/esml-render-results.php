<?php
global $esml_data, $essb_options;

$esml_data = new ESMLRenderResultsHelper ();
$esml_data->generate_data ( $essb_options );
function esml_render_dashboard_view($options) {

}


?>


<div class="wrap">
<div class="essb-tabs" style="margin-bottom: 20px;">
		<div class="essb-tabs-title" style="padding-top: 15px; padding-bottom: 20px;">
			<div class="essb-tabs-version">
				<div class="essb-logo essb-logo32"></div>
				<div class="essb-text-afterlogo">
					<h3>Social Metrics Lite for Easy Social Share Buttons for WordPress</h3>
					<p>
						Version <strong><?php echo ESSB3_VERSION;?></strong>. &nbsp;<strong><a
							href="http://socialsharingplugin.com/version-changes/" target="_blank">See
								what's new in this version</a></strong>&nbsp;&nbsp;&nbsp;<strong><a
							href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo"
							target="_blank">Easy Social Share Buttons plugin homepage</a></strong>
					</p>
					
				</div>
			</div>
		</div>
		

	</div>

	<div class="essb-clear"></div>
	
	<?php EasySocialMetricsUpdater::printQueueLength(); ?>     

	<div class="essb-clear"></div>

	<div class="essb-title-panel">
	<form id="easy-social-metrics-lite" method="get" action="admin.php?page=easy-social-metrics-lite">
	<input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']) ?>" />
	<?php
	$range = (isset ( $_GET ['range'] )) ? $_GET ['range'] : 0;
	?>
	    			<label for="range">Show only:</label> <select name="range">
			<option value="1"
				<?php if ($range == 1) echo 'selected="selected"'; ?>>Items
				published within 1 Month</option>
			<option value="3"
				<?php if ($range == 3) echo 'selected="selected"'; ?>>Items
				published within 3 Months</option>
			<option value="6"
				<?php if ($range == 6) echo 'selected="selected"'; ?>>Items
				published within 6 Months</option>
			<option value="12"
				<?php if ($range == 12) echo 'selected="selected"'; ?>>Items
				published within 12 Months</option>
			<option value="0"
				<?php if ($range == 0) echo 'selected="selected"'; ?>>Items
				published anytime</option>
		</select>
	    					
	    					<?php do_action( 'esml_dashboard_query_options' ); // Allows developers to add additional sort options ?>
	    
	    					<input type="submit" name="filter" id="submit_filter"
			class="button" value="Filter"> <a
			href="<?php echo admin_url('admin.php?page=easy-social-metrics-lite&esml_sync_all=true'); ?>"
			class="button">Update all posts</a>
	    			<?php
								?>
								</form>
	</div>

	<!-- dashboard start -->
	<div class="essb-dashboard">

		<div class="row">

			<div class="twocols">
				<div class="essb-dashboard-panel">
					<div class="essb-dashboard-panel-title">
						<h4>Social Network Presentation</h4>
					</div>
					<div class="essb-dashboard-panel-content">
					<?php
					$esml_data->output_total_results ();
					
					//$esml_data->output_total_chart();
					?>
					</div>
				</div>
			</div>

			<div class="twocols left">
				<div class="essb-dashboard-panel">
					<div class="essb-dashboard-panel-title">
						<h4>Top Shared Content by Social Network</h4>
					</div>
					<div class="essb-dashboard-panel-content">
					<?php
					$esml_data->output_total_content ();
					?>
					</div>
				</div>
			</div>

		</div>

		<div class="row">

			<div class="essb-dashboard-panel">
				<div class="essb-dashboard-panel-title">
					<h4>Detailed Content Report</h4>
				</div>
				<div class="essb-dashboard-panel-content">
					<?php
					$esml_data->output_main_result ();
					?>
					</div>
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#esml-result').DataTable({ pageLength: 50});
} );
</script>

