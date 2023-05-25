<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	if ( !current_user_can( 'install_plugins' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-speedup') );
	}
	$saved = false;
	
	
  
	if(!empty($_POST) && isset($_POST['wpsu_options'])){
		if ( 
			! isset( $_POST['wpsu_nonce_action4_field'] ) 
			|| 
			! wp_verify_nonce( $_POST['wpsu_nonce_action4_field'], 'wpsu_nonce_action4' ) 
		) {
		
		   print __('Sorry, your nonce did not verify.', 'wp-speedup');
		   exit;
		
		} else {
		
		   // process form data
			update_option('wpsu_options', sanitize_spsu_data($_POST['wpsu_options']));
			$saved = true;
		   
		}  		
	}
	global $wpsu_css, $su_data, $su_pro, $su_premium_link, $wpsu_url;
	$wpsu_options_db = get_option('wpsu_options', array());
	//pree($wpsu_options_db);
	$wpsu_options = array('disable_hotlinking'=>"Don't use it unless you really need it.", 'expires_header'=>'', 'cache_control'=>'', 'deflate_compression'=>'Ask your hosting company if mod_deflate installed or not?', 'gzip_compression'=>'', 'ob_gzhandler'=>'', 'unset_etag'=>'Developers Only!');
?>	
<div class="wrap wpsu">
	
  <div class="head_area">
	<h2><span class="dashicons dashicons-welcome-widgets-menus"></span> <?php echo $su_data['Name'].' ('.$su_data['Version'].')'.($su_pro?' Pro':''); ?> - <?php echo __('Settings', 'wp-speedup'); ?></h2>
      <img height="200px;" class="wpsu_top_image" src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>/images/banner-1544x500.jpg" />

  </div>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active">DB <?php echo __('Booster', 'wp-speedup'); ?></a>
        <a class="nav-tab"><?php echo __('Caching Mode', 'wp-speedup'); ?></a>
        <a class="nav-tab"><?php echo __('Image Optimization', 'wp-speedup'); ?></a>
    </h2>


  <div class="nav-tab-content wpsu_booster_area">
  <?php 
  	global $wpdb;
  	$InnoDB = $wpdb->get_results("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE engine = 'InnoDB' AND TABLE_SCHEMA = '".DB_NAME."'");
	$MyISAM = $wpdb->get_results("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE engine != 'InnoDB' AND TABLE_SCHEMA = '".DB_NAME."'");
	
  ?>
      <div class="alert alert-info" style="margin-top: 20px">

            <h3>
            <?php if(!empty($InnoDB)){ ?>
            <?php echo __('There are', 'wp-speedup'); ?> <?php echo count($InnoDB); ?> <?php echo __('tables with', 'wp-speedup').' InnoDB '.__('engine type', 'wp-speedup'); ?>.<br />
            <?php } ?>
            <?php if(!empty($InnoDB) && !empty($MyISAM)){ ?>
            &
            <br />
            <?php  } ?>
            <?php if(!empty($MyISAM)){ ?>
            <?php echo __('There are', 'wp-speedup'); ?> <?php echo count($MyISAM); ?> <?php echo __('tables with', 'wp-speedup').' MyISAM '.__('engine type.', 'wp-speedup'); ?><br />
            <?php } ?>
            </h3>


      </div>


     <form action="" method="post">
<?php wp_nonce_field( 'wpsu_nonce_action3', 'wpsu_nonce_action3_field' ); ?>     
		
    
	<div class="wpsu_booster_wrapper">
    
    	<table >

            <tr >
                <td><h2><?php echo __('What is', 'wp-speedup'); ?> InnoDB?</h2></td>
                <td><h2><?php echo __('What is', 'wp-speedup'); ?> MyISAM?</h2></td>
            </tr>

            <tr>

                <td valign="top">
                    <p>
                        InnoDB <?php echo __('is a storage engine for', 'wp-speedup'); ?> MySQL.
                        MySQL 5.5<?php echo __('and later use it by default. ', 'wp-speedup'); ?>
                        InnoDB <?php echo __('provides the standard', 'wp-speedup').' ACID-compliant '.__('transaction features, along with foreign key support (Declarative Referential Integrity)', 'wp-speedup'); ?>
                    </p>
                </td>

                <td valign="top">
                    <p>
                        MyISAM <?php echo __('is the default storage engine for the', 'wp-speedup').' MySQL '.__('relational database management system versions prior to', 'wp-speedup'); ?> 5.5 1.
		                <?php echo __('It is based on the older', 'wp-speedup').' ISAM '.__('code but has many useful extensions.', 'wp-speedup'); ?>
		                <?php echo __('The major deficiency of', 'wp-speedup').' MyISAM '.__('is the absence of transactions support.', 'wp-speedup'); ?>
                    </p>
                </td>
            </tr>




        	<tr>

            	<td valign="top">

                    <div>
                        <h5>InnoDB <?php echo __('Advantages', 'wp-speedup'); ?></h5>
                        <ul>
                            <li>ACID <?php echo __('transactions', 'wp-speedup'); ?></li>
                            <li><?php echo __('row-level locking', 'wp-speedup'); ?></li>
                            <li><?php echo __('foreign key constraints', 'wp-speedup'); ?></li>
                            <li><?php echo __('automatic crash recovery', 'wp-speedup'); ?></li>
                            <li><?php echo __('table compression (read/write)', 'wp-speedup'); ?></li>
                            <li><?php echo __('spatial data types (no spatial indexes)', 'wp-speedup'); ?></li>
                        </ul>
                    </div>

                </td>


                <td valign="top">
                    <div>
                        <h5>MyISAM <?php echo __('Advantages', 'wp-speedup'); ?></h5>
                        <ul>
                            <li><?php echo __('fast COUNT(*)s (when WHERE, GROUP BY, or JOIN is not used)', 'wp-speedup'); ?></li>
                            <li><?php echo __('full text indexing (update: supported in', 'wp-speedup').' InnoDB '.__('from', 'wp-speedup'); ?> MySQL 5.6)</li>
                            <li><?php echo __('smaller disk footprint', 'wp-speedup'); ?></li>
                            <li><?php echo __('very high table compression (read only)', 'wp-speedup'); ?></li>
                            <li><?php echo __('spatial data types and indexes (R-tree) (update: supported in', 'wp-speedup').' InnoDB '.__('from', 'wp-speedup'); ?> MySQL 5.7)</li>
                        </ul>
                    </div>

                </td>
            </tr>

            <tr>
                <td>
                    <input type="submit" value="Convert all MyISAM to InnoDB" class="button-secondary button-large" name="itom_conversion_innodb" />
                </td>

                <td>
                    <input type="submit" value="Convert all InnoDB to MyISAM" class="button-secondary button-large" name="itom_conversion_myisam" />
                </td>
            </tr>

        </table>
    	
        
       
        
        	
    </div>
    
     </form>
    
        
  </div>
  
  <div class="nav-tab-content wpsu_todo_area hide">
  
  <p><?php echo __('Please test your website speed on these platforms before optimization.', 'wp-speedup'); ?></p>
  <a href="https://tools.pingdom.com/" target="_blank" class="button-secondary button-large"><?php echo __('Test on'); ?> pingdom.com</a>
  &nbsp;
  <a href="https://developers.google.com/speed/pagespeed/insights/?url=<?php echo home_url(); ?>" target="_blank" class="button-secondary button-large"><?php echo __('Test on google.com pagespeed', 'wp-speedup'); ?></a>
  
  <h4><?php echo __('Speed Up Todo list', 'wp-speedup'); ?>:</h4>
  <form action="" method="post">
<?php wp_nonce_field( 'wpsu_nonce_action4', 'wpsu_nonce_action4_field' ); ?>       
  <input type="hidden" name="wpsu_options[]" />
  <ol>
  	<li><strong><?php echo __('Turn off pingbacks and trackbacks', 'wp-speedup'); ?>:</strong> <a href="options-discussion.php" target="_blank"><small>
	<?php echo __('Uncheck', 'wp-speedup'); ?></small> 
    "<?php echo __('Allow link notifications from other blogs (pingbacks and trackbacks) on new articles', 'wp-speedup'); ?>"</a></li>
    <li><a href="upload.php" target="_blank"><strong><?php echo __('Images', 'wp-speedup'); ?>:</strong></a>
    <ul>
    <li>JPEG - <?php echo __('use for photos', 'wp-speedup'); ?></li>
    <li>PNG - <?php echo __('use for graphics (or not detailed images)', 'wp-speedup'); ?></li>
   	<li>GIF - <?php echo __('use for simple small graphics or images', 'wp-speedup'); ?></li>
    <li>BMP/TIFF - <?php echo __('do not use them', 'wp-speedup'); ?></li>
    </ul>
    </li>
    <li><strong><?php echo __('Optimization', 'wp-speedup'); ?>:</strong>
    <ul>
    <?php foreach($wpsu_options as $options=>$tooltip){ ?>
    	<li><input <?php checked(array_key_exists($options, $wpsu_options_db) && $wpsu_options_db[$options]); ?> id="<?php echo $options; ?>" type="checkbox" value="1" name="wpsu_options[<?php echo $options; ?>]" /><label for="<?php echo $options; ?>"><?php echo ucwords(str_replace('_', ' ', $options)); ?></label> <?php echo $tooltip!=''?' - <strong style="color:#8ac007">('.$tooltip.')</strong>':''; ?></li>
	<?php } ?>        
    </ul>
    </li>
	
    <li><strong><?php echo __('Permalink Settings', 'wp-speedup'); ?>:</strong> <a style="<?php echo ($saved?'color:red':''); ?>" href="options-permalink.php" target="_blank"><?php echo __('Update permalinks every time you made changes here', 'wp-speedup'); ?></a></li>
  </ol>

  <input type="submit" class="button-primary button-large" value="Save Changes" />
  <br /><br />

  <strong style="color:red"><?php echo __('Before making any changes, its recommended that you connect FTP and backup your', 'wp-speedup').' .htaccess '.__('file on root.', 'wp-speedup'); ?></strong>

  </form>
  

<br />
<br />
<div class="wpsu_blog_posts">
<a href="https://profiles.wordpress.org/fahadmahmood/#content-plugins" target="_blank"><img height="160" src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>/images/mechanic_with_board.png" align="right" /></a>
	
  <strong><?php echo __('A few blog posts related to', 'wp-speedup').' .htaccess '.__('handling', 'wp-speedup'); ?>:</strong>
  <ul>
  	<li><a href="http://androidbubble.com/blog/category/website-development/htaccess/" target="_blank">.htaccess <?php echo __('at a Glance', 'wp-speedup'); ?></a></li>
    <li><a href="http://androidbubble.com/blog/website-development/htaccess/caching-with-htaccess/" target="_blank"><?php echo __('Caching with', 'wp-speedup'); ?> .htaccess</a></li>
    <li><a href="http://androidbubble.com/blog/website-development/htaccess/codeigniter-htaccess-issue-on-php-cgi-webhero-hosting/" target="_blank">htaccess <?php echo __('issue on', 'wp-speedup'); ?> php cgi</a></li>
    <li><a href="http://androidbubble.com/blog/website-development/htaccess/error-reporting-on-in-htaccess/" target="_blank"><?php echo __('Error Reporting', 'wp-speedup'); ?></a></li>
    <li><a href="http://androidbubble.com/blog/website-development/htaccess/caching-with-htaccess" target="_blank"><?php echo __('More Articles on', 'wp-speedup').' .htaccess '.__('usage', 'wp-speedup'); ?></a></li>
    
  </ul>
  
</div>  
  </div>

<!--<div class="nav-tab-content selection_div main hide">-->
<!---->
<!--    <div class="images_compression_report">-->
<!--        --><?php //wpsu_load_img_module(); ?>
<!--    </div>-->
<!---->
<!--</div>-->



<?php

    $file = 'wpsu_img_settings.php';
    if(file_exists($wpsu_css->get_plugin_path())){
        include($file);
    }
				

		$file = 'wpsu_css_settings.php';
		if(is_object($wpsu_css) && file_exists($wpsu_css->get_plugin_path())){
			include($file);
		}
		$file = 'wpsu_js_settings.php';
		if(is_object($wpsu_css) && file_exists($wpsu_css->get_plugin_path())){
			include($file);
		}
		if(isset($_GET['type']) && $_GET['type']=='img'){

		}

?>


    <div class="selection_css hide" title="<?php echo __('Click here for settings', 'wp-speedup'); ?>">CSS</div>
    <div class="selection_js hide" title="<?php echo __('Click here for settings', 'wp-speedup'); ?>">JS</div>

    <div class="wpsu_loader modal">
        <div class="modal-content">
            <img src="<?php echo $wpsu_url.'/images/loader.gif' ?>">
            <p><?php echo __('Please wait', 'wp-speedup'); ?>....</p>
        </div>
    </div>


    <style type="text/css">
    	#message, #wpfooter{ display:none; }
    </style>

    <script type="text/javascript" language="javascript">

        jQuery(document).ready(function($){


			<?php if(isset($_GET['t'])): ?>

            $('.nav-tab-wrapper .nav-tab:nth-child(<?php echo $_GET['t']+1; ?>)').click();

			<?php endif; ?>

        });

    </script>

</div>