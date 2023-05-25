<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><h3 class="wpsu_toggle js">Back</h3>
<div class="selection_div settings sub js">
<strong style="color:red"><?php echo __('Not recommended unless you understand the javascript minification.', 'wp-speedup'); ?></strong>
<?php 

	
	if ( isset( $_POST['selection_js'] )) {
		if ( 
			! isset( $_POST['wpsu_nonce_action2_field'] ) 
			|| ! wp_verify_nonce( $_POST['wpsu_nonce_action2_field'], 'wpsu_nonce_action2' ) 
		) {
		
		   print 'Sorry, your nonce did not verify.';
		   exit;
		
		} else {
		
		   // process form data
		   update_option( 'selection_js', (boolean)$_POST['selection_js']);
		}

			
	}
	$selection_js = get_option('selection_js');	
?>	
<?php if ( isset( $_POST['selection_js'] )): ?>    
<div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php echo __( 'Settings saved.', 'wp-speedup'); ?></strong></p></div>
<?php endif; ?>
    
    
<form method="post">
<?php wp_nonce_field( 'wpsu_nonce_action2', 'wpsu_nonce_action2_field' ); ?>     


	<div title="Click here for enable/disable" class="selection_js <?php echo ($selection_js==true)?'':'disabled'; ?>">SpeedUp JS</div>
    <input type="hidden" name="selection_js" value="<?php echo $selection_js; ?>" />

<div style="clear:both; margin:20%"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'wp-speedup'); ?>" /></div>
</form>
</div>