<?php
	if (!defined('EASY_UPDATES_MANAGER_MAIN_PATH')) die('No direct access allowed');

	$prohibited_active_constants = MPSUM_Constant_Checks::get_instance()->get_prohibited_active_constants();
	$html = '';
	if (in_array('DISABLE_WP_CRON', $prohibited_active_constants)) {
		$html .= sprintf('<li><strong>%s</strong>: %s</li>', 'DISABLE_WP_CRON', esc_html__('This constant prevents automatic updates scheduled tasks from being run within WordPress internal cron.', 'stops-core-theme-and-plugin-updates')." ".esc_html__('Typically, when enabled, automatic updates events are checked on every page load and any events due to run will be called during that page load.', 'stops-core-theme-and-plugin-updates')." ".esc_html__("However, if it's intentionally being set because you use external cron (server cron) then you can ignore this warning.", 'stops-core-theme-and-plugin-updates'));
	}
	$html = !empty($html) ? '<ul>'.$html.'</ul>' : $html;
	if (empty($html)) return;
?>

<div id="easy-updates-manager-constants-enabled" class="error">
	<div style="float:right;"><a href="#" onclick="jQuery('#easy-updates-manager-constants-enabled').slideUp(); jQuery.post(ajaxurl, {action: 'easy_updates_manager_ajax', subaction: 'dismiss_constant_notices', nonce: '<?php echo wp_create_nonce('easy-updates-manager-ajax-nonce'); ?>' });"><?php printf(__('Dismiss', 'stops-core-theme-and-plugin-updates')); ?></a></div>

	<h3><?php
		// Allow white label
		$eum_white_label = apply_filters('eum_whitelabel_name', __('Easy Updates Manager', 'stops-core-theme-and-plugin-updates'));
		echo sprintf(__("The following constants are set and will prevent automatic updates in %s.", 'stops-core-theme-and-plugin-updates'), $eum_white_label);
	?></h3>
	<div id="easy-updates-manager-constants-enabled-wrapper">
		<p><?php esc_html_e('Please check your wp-config.php file or other files for these constants and remove them to allow Easy Updates Manager to have control.', 'stops-core-theme-and-plugin-updates'); ?></p>
		<?php echo $html; ?>
	</div>
</div>
