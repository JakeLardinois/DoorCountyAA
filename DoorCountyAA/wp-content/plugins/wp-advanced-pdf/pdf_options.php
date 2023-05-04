<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
if(isset($_GET["ced_wpppdf_close"]) && $_GET["ced_wpppdf_close"]==true)
{
	//unset($_GET["ced_wpppdf_close"]);
	if(!session_id())
		session_start();
	$_SESSION["wpppdf_hide_email"]=true;
}
?>
<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<?php
	if(!session_id())
		session_start();
	if(!isset($_SESSION["wpppdf_hide_email"])):
		$actual_link = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$urlvars = parse_url($actual_link);
	$url_params = $urlvars["query"];
	?>
	<div class="wpppdf_img_email_image">
		<div class="wpppdf_email_main_content">
			<div class="wpppdf_cross_image_container">
				<a class="button-primary ced_wpppdf_cross_image" href="?<?php echo $url_params?>&ced_wpppdf_close=true">x</a>
			</div>
			<div class="ced-recom">
				<h4>Cedcommerce recommendations for you </h4>
			</div>
			<div class="wramvp_main_content__col">
				<p> 
					Looking forward to evolve your eCommerce?
					<a href="http://bit.ly/2LB1lZV" target="_blank">Sell on the TOP Marketplaces</a>
				</p>
				<div class="wramvp_img_banner">
					<a target="_blank" href="http://bit.ly/2LB1lZV"><img alt="market-place" src="<?php echo plugins_url().'/wp-advanced-pdf/asset/images/market-place-2.jpg'?>"></a> 
				</div>
			</div>
			<div class="wramvp_main_content__col">
				<p> 
					Leverage auto-syncing centralized order management and more with our
					<a href="http://bit.ly/2LB71TJ" target="_blank">Integration Extensions</a> 
				</p>
				<div class="wramvp_img_banner">
					<a target="_blank" href="http://bit.ly/2LB71TJ"><img alt="market-place" src="<?php echo plugins_url().'/wp-advanced-pdf/asset/images/market-place.jpg'?>"></a> 
				</div>
			</div>
			<div class="clear"></div>
			<div class="wramvp-support">
				<ul>
					<li><span class="wramvp-support__left">Contact Us :-</span><a href="mailto:support@cedcommerce.com"> support@cedcommerce.com </a>  </li>
					<li><span class="wramvp-support__right">Get expert's advice :-</span><a href="https://join.skype.com/bovbEZQAR4DC"> Join Us</a></li>
				</ul>
			</div>
		</div>
	</div>
<?php endif;?>

<h2> <?php _e('WP Advanced PDF Settings', 'wp-advanced-pdf'); ?></h2>
<div class="updated below-h2" id="wppdf_message"></div>
<div id="gde-tabcontent">
	<?php

	?><form method="post" name="WPPDF" action="options.php">
	<div id="gencontent" class="gde-tab gde-tab-active">
		<?php ptpdf_show_tab('general'); ?>
	</div>
</form>

</div>
</div>
<?php

function ptpdf_show_tab( $name ) {
	$tabfile = PTPDF_PATH . "/libs/wpppdf-tab-$name.php"; // die($tabfile);
	if (file_exists ( $tabfile )) {
		include_once ($tabfile);
	}
}
function ptpdf_profile_option( $option, $value, $label, $helptext = '' ) {
	echo "<option value=\"" . esc_attr ( $value ) . "\"";
	if (! empty ( $helptext )) {
		echo " title=\"" . esc_attr ( $helptext ) . "\"";
	}
	if ($option == $value) {
		echo ' selected="selected"';
	}
	echo ">$label &nbsp;</option>\n";
}
function ptpdf_profile_checkbox($val, $field, $default='1', $wrap='',  $label= '', $br = '', $disabled = false ) {
	if (! empty ( $wrap )) {
		echo '<span id="' . esc_attr ( $wrap ) . '">';
	}
	echo '<input type="checkbox" id="' . esc_attr ( $field ) . '" name="' . esc_attr ( $field ) . '"';
	if (($val == $default) || ($disabled)) {
		echo ' checked="checked"';
	}
	if ($disabled) {
		// used only for dx logging option due to global override in functions.php
		echo ' disabled="disabled"';
	}
	
	echo ' value="' . esc_attr ( $default ) . '"> <label for="' . esc_attr ( $field ) . '">' . htmlentities ( $label ) . '</label>';
	if (! empty ( $br )) {
		echo '<br/>';
	}
	if (! empty ( $wrap )) {
		echo '</span>';
	}
}
?>