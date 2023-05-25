<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
	
	if ( isset( $_POST['selection_css'] )) {
					
			if ( 
				! isset( $_POST['wpsu_nonce_action1_field'] ) 
				|| ! wp_verify_nonce( $_POST['wpsu_nonce_action1_field'], 'wpsu_nonce_action1' ) 
			) {
			
			   print 'Sorry, your nonce did not verify.';
			   exit;
			
			} else {
			
			   // process form data
			   update_option( 'selection_css', (boolean)$_POST['selection_css']);
			}		

			
	}
	$selection_css = get_option('selection_css');	
		
?>	
<h3 class="wpsu_toggle css">Back</h3>
<div class="selection_div sub settings css">



                    
                
                
<?php if ( isset( $_POST[$wpsu_css->nspace . '_update_settings'] ) ): ?>
                <div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php echo __( 'Settings saved.', $wpsu_css->nspace ); ?></strong></p></div>
<?php endif; ?>
<?php 


if ( ! file_exists( $wpsu_css->tmp_dir ) ): ?>
                <div class="updated settings-error" id="setting-error-settings_updated">
                    <p><strong><?php echo __( 'Temporary directory does not exist.', 'wp-speedup'); ?> 
					<?php echo __('You will need to manually create this directory by using the commands below. ', 'wp-speedup'); ?> 
					<?php echo __('After running the commands, be sure to update your settings and select the "Save Changes" button below.', 'wp-speedup'); ?>.'</strong> <ul><li>mkdir ' . $wpsu_css->tmp_dir . ';</li><li>chmod 777 ' . $wpsu_css->tmp_dir . ';', $wpsu_css->nspace </li></ul></strong></p>
                    <p><strong><?php echo __( 'Alternatively, you may use your FTP client to create a directory called "tmp" directly in the this plugin directory rather than running the commands above.', 'wp-speedup'); ?>, $wpsu_css->nspace </strong></p>
                </div>
<?php elseif( ! is_writable( $wpsu_css->tmp_dir ) ): ?>
                <div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php echo __( 'Temporary directory is not writable. ', 'wp-speedup'); ?> 
                <?php echo __('You will need to manually fix permissions by using this command (or use your FTP client to give 777 permissions to the tmp directory)', 'wp-speedup'); ?>:</strong> <ul><li>chmod 777 ' . $wpsu_css->tmp_dir . ';', $wpsu_css->nspace </li></ul></p></div>
<?php elseif ( ! file_exists( $wpsu_css->css_settings_path ) ): ?>
                <div class="updated settings-error" id="setting-error-settings_updated">
                    <p><strong><?php echo __( 'Settings file does not exist. ', 'wp-speedup');?>
					<?php echo __('Select the "Save Changes" button below to generate this file.', 'wp-speedup'); ?>
					 <?php echo __('If the file does not exist after selecting the "Save Changes" button, you will need to manually create this file by using these commands (or using your FTP client to create the file)', 'wp-speedup'); ?>:</strong> <ul><li>touch ' . $wpsu_css->css_settings_path . ';</li><li>chmod 666 ' . $wpsu_css->css_settings_path . ';', $wpsu_css->nspace </li></ul></p>
                </div>
<?php elseif( ! is_writable( $wpsu_css->css_settings_path ) ): ?>
                <div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php echo __( 'Settings file is not writable. ');?> <?php echo __('You will need to manually fix permissions by using this command', 'wp-speedup'); ?>:</strong> <ul><li>chmod 666 ' . $wpsu_css->css_settings_path . ';', $wpsu_css->nspace </li></ul></p></div>
<?php endif; ?>
                <form method="post">
<?php wp_nonce_field( 'wpsu_nonce_action1', 'wpsu_nonce_action1_field' ); ?>                
                    <table class="form-table hide">
<?php foreach ( $wpsu_css->settings_fields as $key => $val ): ?>
                        <tr valign="top">
<?php if ( $val['type'] == 'legend' ): ?>
                            <th colspan="2" class="legend" scope="row"><strong><?php echo __( $val['label'], $wpsu_css->nspace ); ?></strong></th>
<?php else: ?>
                            <th scope="row"><label for="<?php echo $key; ?>"><?php echo __( $val['label'], $wpsu_css->nspace ); ?></label><?php if ( isset( $val['instruction'] ) ): ?><br><em><?php echo __( $val['instruction'], $wpsu_css->nspace ); ?></em><?php endif; ?></th>
                            <td>
<?php if( $val['type'] == 'money'): ?>
                                <span class="dollar-sign">$</span>
<?php endif; ?>
<?php if( $val['type'] == 'money' || $val['type'] == 'text' || $val['type'] == 'password' ): ?>
<?php
        if ( $val['type'] == 'money' ) $val['type'] = 'text';
        $value = $wpsu_css->get_settings_value( $key );
        if ( ! @strlen( $value ) ) $value = $val['default'];
?>
                                <input name="<?php echo $key; ?>" type="<?php echo $val['type']; ?>" id="<?php echo $key; ?>" class="regular-text" value="<?php echo stripslashes( htmlspecialchars( $value ) ); ?>" />
<?php elseif ( $val['type'] == 'select' ): ?>
<?php
        $value = $wpsu_css->get_settings_value( $key );
        if ( ! @strlen( $value ) && isset( $val['default'] ) ) $value = $val['default'];
?>
                                <?php echo $wpsu_css->select_field( $key, $val['values'], $value ); ?>
<?php elseif( $val['type'] == 'textarea' ): ?>
                                <textarea class="regular-text" cols="60" rows="10" name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo stripslashes( htmlspecialchars( $wpsu_css->get_settings_value( $key ) ) ); ?></textarea>
<?php endif; ?>
<?php if ( isset( $val['description'] ) ): ?>
                                <span class="description"><?php echo $val['description']; ?></span>
<?php endif; ?>
                            </td>
<?php endif; ?>
                        </tr>
<?php endforeach; ?>
                    </table>
                    <input type="hidden" name="<?php echo $wpsu_css->nspace; ?>_update_settings" value="1" />
                    
                    



	<div title="Click here for enable/disable" class="selection_css <?php echo ($selection_css==true)?'':'disabled'; ?>">SpeedUp CSS</div>
    <input type="hidden" name="selection_css" value="<?php echo $selection_css; ?>" />

                    <div style="clear:both; margin:20%"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'wp-speedup'); ?>" /></div>
                    
                </form>
            </div>
