<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	function sanitize_spsu_data( $input ) {

		if(is_array($input)){
		
			$new_input = array();
	
			foreach ( $input as $key => $val ) {
				$new_input[ $key ] = (is_array($val)?sanitize_spsu_data($val):sanitize_text_field( $val ));
			}
			
		}else{
			$new_input = sanitize_text_field($input);
		}
		
		return $new_input;
	}
		
	if(!function_exists('pre')){
	function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}	 
	} 	

	if(!function_exists('pree')){
	function pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		}	 
	} 
		
	
	#IMAGE - START
	
	function wpsu_formatSizeUnits($bytes)
	{
			if ($bytes >= 1073741824)
			{
				$bytes = number_format($bytes / 1073741824, 2) . ' <span class="gbb">GB</span>';
			}
			elseif ($bytes >= 1048576)
			{
				$bytes = number_format($bytes / 1048576, 2) . ' <span class="mbb">MB</span>';
			}
			elseif ($bytes >= 1024)
			{
				$bytes = number_format($bytes / 1024, 2) . ' <span class="kbb">KB</span>';
			}
			elseif ($bytes > 1)
			{
				$bytes = $bytes . ' <span class="bbb">bytes</span>';
			}
			elseif ($bytes == 1)
			{
				$bytes = $bytes . ' byte';
			}
			else
			{
				$bytes = '0 bytes';
			}
	
			return $bytes;
	}	


	
	function wpsu_is_image($source) { 
		
		$info = getimagesize($source); 
		
		if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/gif' || $info['mime'] == 'image/png') 
		return true;
		else
		return false;
	}		
	
	function wpsu_load_img_module(){
?>
    <?php global $dir_size, $wpsu_total_bytes;


    if(!empty(array_filter($dir_size))): $fs_arr = array();


    ?>

    <div class="alert alert-info" style="margin-top: 20px">

        <h3 <?php echo (!isset($_GET['type'])?'title="'.__('Click here to get information about image compression', 'wp-speedup').'"':''); ?>>
		    <?php echo __('Your image files are currently using', 'wp-speedup').' <span></span> '.__('of your bandwidth!', 'wp-speedup'); ?>
        </h3>
        <span><?php echo __('You can compress and try the compressed version of your images by switching directories.', 'wp-speedup'); ?></span>

    </div>


    	<ul class="list-group">
        	<?php

			$dir_listing = array();
			$index = 0;
			$dir_size_org['org'] = $dir_size['org'];
			foreach($dir_size_org as $k_type=>$arr_type): 
			
			foreach($arr_type as $k=>$arr):
			
			$fs = array_sum($arr); 
			$color = dechex(mt_rand(0, 0xFFFFFF));
			$index++;
			
			$dir_path = base64_decode($k);			
			$is_temp = wpsu_temp($dir_path);
			
			$temp_path = substr($dir_path, 0, -1).'_temp/';
			$temp_path_encoded = base64_encode($temp_path);
			$dir_size_temp = isset($dir_size['temp']) ? $dir_size['temp'] : array();
			$temp_path_exists = array_key_exists($temp_path_encoded, $dir_size_temp);
			
			
			if(!$is_temp){
			
				$dir_listing[] = '<li class="wpsu_link_dir list-group-item'.(wpsu_temp(base64_decode($k))?'wpsu_temp_text':'').'" data-linked="'.$k.'"><span title="'.__('Directory Name', 'wp-speedup').': '.ucwords(base64_decode($k)).'">'.ucwords($dir_path).'</span> (<span>'.wpsu_formatSizeUnits($fs).'</span>) ';
				if($temp_path_exists): 
				
				$fs_temp = array_sum($dir_size['temp'][$temp_path_encoded]); 
				
				$version_type = 'Switch '.($fs > $fs_temp?'to compressed':'back to <u>original</u>');
				
				$dir_listing[] = '- <span class="wpsu_temp_text" data-linked="'.$temp_path_encoded.'"><a title="'.__('Click here to switch directory', 'wp-speedup').'" class="wpsu_ud button button-secondary">'.$version_type.' version ('.wpsu_formatSizeUnits($fs_temp).')</a></span>'; endif; 
				$dir_listing[] = '</li>';
			
			}
			
			/*
			?>
            <li class="wpsu_link_dir  <?php echo (wpsu_temp(base64_decode($k))?'wpsu_temp_text':'');?>" data-linked="<?php echo $k; ?>"><span title="Directory Name: <?php echo ucwords(base64_decode($k)); ?>"><?php echo ucwords(base64_decode($k)); ?></span> (<span><?php echo wpsu_formatSizeUnits($fs); ?></span>) <?php if(wpsu_temp(base64_decode($k))): ?>- <a title="Click here to use this directory instead of its linked directory" class="wpsu_ud button button-secondary">Switch directory</a><?php endif; ?></li>
            <?php 
			*/
			
			if(!wpsu_temp($k)){ $fs_arr[] = (($k_type=='org')?$fs:0); } endforeach; endforeach; 
			echo implode('', $dir_listing);
			?>
        </ul>
   		<?php
			$total_bytes = array_sum($fs_arr);
			if($total_bytes>0){
				update_option('wpsu_total_bytes', $total_bytes);
			}else{
				$total_bytes = $wpsu_total_bytes;
			}
			$font_size = (float)wpsu_formatSizeUnits($total_bytes);
		?>

<!--			--><?php //echo (!isset($_GET['type'])?'<a class="op_linked button button-primary">'.__('Click here to optimize', 'wp-speedup').'</a>':''); ?>

            <script type="text/javascript" language="javascript">
	jQuery(document).ready(function($){
		$('.images_compression_report > .alert > h3 > span').html('<?php echo wpsu_formatSizeUnits($total_bytes); ?>').attr('style', 'font-size:<?php echo (($font_size/2)+12); ?>px');
	});
	</script>
    
    <?php endif; ?>
    	
    <?php
	}
	

	
	function wpsu_temp($dir){
		
		$ret = (substr(basename($dir), -5, 5)=='_temp' || substr(basename($dir), -5, 5)=='_wpsu');
		return $ret;
	}

	function wpsu_add_temp($dir, $temp='temp'){
		if(wpsu_temp($dir)){
			
			if(substr($dir, -1, 1)=='/')
			$dir = $dir.'/';
			
			return $dir;
		}else{
			$temp = str_replace($dir.'_', '', $temp);

			return ($dir.'_'.$temp.'/');
		}
	}
	function wpsu_clear_url($url){
		global $wpsu_live;
		$exp = explode('/', $url);
		$exp = array_filter($exp, 'strlen');


		return (($wpsu_live?'/':'').implode('/', $exp));
	}
	function wpsu_remove_temp($dir, $temp = '_temp'){
		$dir = wpsu_clear_url($dir);
		if(wpsu_temp($dir))
		return (substr($dir, 0, -(strlen($temp))));
		else
		return $dir;
	}	
	
	function wpsu_compression_check(){

		global $dir_size, $wpsu_compress_images;

		$upload_dir = wp_upload_dir();

		$basedir = $upload_dir['basedir'];	

		if(function_exists('wpsu_link_dir')){		
			wpsu_link_dir($basedir);
		}
	
			
		
		$objects = new RecursiveIteratorIterator(
					   new RecursiveDirectoryIterator($basedir), 
					   RecursiveIteratorIterator::SELF_FIRST);
		
		
		
		$dir_size = array('temp'=>array(),'org'=>array());
		
		if (!empty($objects)) {
			foreach($objects as $name => $object){


				$level_green = ($objects->getDepth()>0);
				
				$entry = $object->getFilename();//$object->getPathname()
				//pre($object->getPathname());
				if ($entry != "." && $entry != ".." && is_dir($object->getPathname())) {
					$dir = $object->getPathname();//$basedir.'/'.$entry;
					
					$dir_temp = wpsu_add_temp($dir);
					
					//if($level_green)
					//pre(!wpsu_temp($dir));
//                    pree($wpsu_compress_images);
//                    echo "Hello";

                    if(
							!wpsu_temp($dir) 
						&& 
							$wpsu_compress_images 
						&& 
							!is_dir($dir_temp) 
						&& 
							function_exists('wpsu_mkdir') 
						&&	
							$level_green
					){	
							wpsu_mkdir($dir_temp);
						
					}elseif(!$wpsu_compress_images && is_dir($dir_temp) && isset($_GET['wpsu_clear_imgs'])){

					    if(function_exists('wpsu_force_rmdir')){


						    wpsu_force_rmdir($dir_temp);

					    }

					}
					
					$dir = $dir.'/';
					
					if(is_dir($dir) && $level_green){
						
						$g_array = glob($dir.'*.*');
						
						if(is_array($g_array) && !empty($g_array)){
						
						foreach ($g_array as $filename) {
							
							if(wpsu_is_image($filename)){
								
							if(wpsu_temp($dir)){
								$dir_size['temp'][base64_encode($dir)][] = filesize($filename);
							}else{
								$dir_size['org'][base64_encode($dir)][] = filesize($filename);
							}
								$condition1 = (
									
									
										$wpsu_compress_images 
									
									&& 
										is_dir($dir_temp) 
									&& 
									(
											!file_exists($dir_temp.basename($filename)) 
										|| 
											isset($_GET['wpsu_ct'])
									)
								);
								
								if($condition1 && function_exists('wpsu_compress_images')){
									wpsu_compress_images($filename, $dir_temp.basename($filename), '50');
								}
								//pre($filename);								
							}else{
								//pre($filename);
							}
							
						}
						}
					}
				}
			}
			
		}


		
		
		
	}
	
	#IMAGE - END
	
	

	#CSS - START	

	if((!is_admin() && get_option('selection_css')) || is_admin()){

		

		if ( !class_exists( 'WPSUCSS' ) ) {

			require_once( dirname( __FILE__ ) . '/wpsu-css-class.php' );			

		}

		

		$wpsu_css = new WPSUCSS();

		

		// define plugin file path	

		$wpsu_css->set_plugin_file( __FILE__ );

		

		// define directory name of plugin	

		$wpsu_css->set_plugin_dir( basename( dirname( __FILE__ ) ) );

		

		// path to this plugin	

		$wpsu_css->set_plugin_path( dirname( __FILE__ ) );

		

		// URL to plugin	

		$wpsu_css->set_plugin_url( plugin_dir_url(__FILE__) );

	

		// call init

		$wpsu_css->init();		

		

	}

	

	#CSS - END

	







	function wpsu_menu()

	{

		global $su_name;





		 add_options_page($su_name, $su_name, 'install_plugins', 'wp_su', 'wp_su');

		 







	}



	function wp_su(){ 







		if ( !current_user_can( 'install_plugins' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-speedup' ) );
		}







		global $wpdb, $wpsu_dir, $su_pro, $su_data, $wpsu_css, $su_name; 



		$type = '';

		if(isset($_GET['type']))
		$type = $_GET['type'];

		

		switch($type){


				//include($wpsu_dir.'inc/wpsu_js_settings.php');
				//include($wpsu_dir.'inc/wpsu_img_settings.php');

			default:
			case 'js':
			case 'img':
			case 'css':

				include($wpsu_dir.'inc/wpsu_settings.php');

			break;

		}

		



	}	

	

	

	



	function wpsu_plugin_links($links) { 

		global $su_premium_link, $su_pro;

		

		$settings_link = '<a href="options-general.php?page=wp_su">'.__('Settings', 'wp-speedup').'</a>';

		

		if($su_pro){

			array_unshift($links, $settings_link); 

		}else{

			 

			$su_premium_link = '<a href="'.esc_url($su_premium_link).'" title="'.__('Go Premium', 'wp-speedup').'" target="_blank">'.__('Go Premium', 'wp-speedup').'</a>'; 

			array_unshift($links, $settings_link, $su_premium_link); 

		

		}

		

		

		return $links; 

	}

	function wpsu_footer_scripts(){
		global $su_pro;
?>
	<script type="text/javascript" language="javascript">
	var wpsu_pro = <?php echo $su_pro?1:0; ?>;
	</script>
<?php		
	}

	function register_su_scripts() {

		

			

		if (is_admin ()){

		

			wp_enqueue_media ();


			$translation_array = array(

				'this_url' => admin_url( 'options-general.php?page=wp_su' ),
				'wpsu_tab' => (isset($_GET['t'])?$_GET['t']:'0'),
				'go_premium' => __('Go premium for this feature.', 'wp-speedup')

			);
			

			 

			wp_enqueue_script(

				'wpsu-scripts',

				plugins_url('js/admin-scripts.js', dirname(__FILE__)),

				array('jquery')

			);


			wp_localize_script('wpsu-scripts', 'wpsu_obj', $translation_array);

			

			

		

			wp_register_style('wpsu-style', plugins_url('styles/admin-styles.css', dirname(__FILE__)));	

			

			wp_enqueue_style( 'wpsu-style' );

		

		}else{

					

			wp_register_style('wpsu-style', plugins_url('styles/front-styles.css', dirname(__FILE__)));	

			

			wp_enqueue_style( 'wpsu-style' );

		}

		

	

	} 

		

	if(!function_exists('wp_speedup')){

	function wp_speedup(){



		

		}

	}

	

	

		

	#JS - START	

			

	

	if ( ! function_exists( 'wpsu_cdata' ) ) :

	function wpsu_cdata( $key, $data = 'trolilol' ) {

		static $datas = array();

		if ( $data !== 'trolilol' ) {

			$datas[ $key ] = $data;

		}

		return isset( $datas[ $key ] ) ? $datas[ $key ] : null;

	}

	endif;

	

	

	function wdjs_get_all_deps( $scripts ) {

		global $wp_scripts;

		$out = array();

	

		if ( is_array( $scripts ) && ! empty( $scripts ) ) {

			foreach ( $scripts as $handle ) {

				if ( ! empty( $wp_scripts->registered[ $handle ]->deps ) ) {

					$deps = array_filter( (array) $wp_scripts->registered[ $handle ]->deps );

					if ( ! empty( $deps ) ) {

						$out = array_merge( $out, wpsu_get_all_deps( $deps ) );

					}

				}

	

				if ( ! empty( $wp_scripts->registered[ $handle ]->src ) ) {

					$out[ $handle ] = $handle;

				}

			}

		}

	

		return $out;

	}

	

	

	

	function wpsu_script_is_dependency( $my_script, $scripts ) {

		global $wp_scripts;

	

		if ( is_array( $scripts ) && ! empty( $scripts ) ) {

			foreach ( $scripts as $handle ) {

				if ( ! empty( $wp_scripts->registered[ $handle ]->deps ) ) {

					$deps = array_filter( (array) $wp_scripts->registered[ $handle ]->deps );

	

					if ( ! empty( $deps ) && ( in_array( $my_script, $deps ) || wpsu_script_is_dependency( $my_script, $deps ) ) ) {

						return true;

					}

				}

			}

		}

	

		return false;

	}

	

	

	

	

	

	function wpsu_save_do_not_defer_deps( $to_do ) {

		global $wp_scripts;

		$do_not_defer = (array) apply_filters( 'do_not_defer', array() );

		$do_not_defer = array_filter( (array) $do_not_defer );

	

		if ( ! empty( $do_not_defer ) ) {

			$do_not_defer = wpsu_get_all_deps( $do_not_defer );

		}

	

		$datas    = wpsu_cdata( 'wpsu_deferred_datas' );

		$datas    = is_array( $datas ) ? $datas : array();

		$deferred = $wp_scripts->queue;

		if ( ! empty( $do_not_defer ) ) {

			$deferred = array_diff( $deferred, $do_not_defer );

		}

		if ( ! empty( $deferred ) ) {

			foreach ( $deferred as $handle ) {

				if ( empty( $wp_scripts->registered[ $handle ]->extra['data'] ) ) {

					continue;

				}

				if ( ! isset( $datas[ $handle ] ) ) {

					$datas[ $handle ] = $wp_scripts->registered[ $handle ]->extra['data'] . "\n";

				}

				elseif ( strpos( $datas[ $handle ], $wp_scripts->registered[ $handle ]->extra['data'] ) === false ) {

					$datas[ $handle ] .= $wp_scripts->registered[ $handle ]->extra['data'] . "\n";

				}

				unset( $wp_scripts->registered[ $handle ]->extra['data'] );

			}

		}

	

		wpsu_cdata( 'wpsu_do_not_defer', $do_not_defer );

		wpsu_cdata( 'wpsu_deferred_datas', $datas );

	

		return $to_do;

	}

	

	



	

	function wpsu_save_dscripts( $src, $handle ) {

		global $wp_scripts;

		$do_not_defer = wpsu_cdata( 'wpsu_do_not_defer' );

	

		if ( ! isset( $do_not_defer[ $handle ] ) && isset( $wp_scripts->registered[ $handle ] ) ) {

			$deferred = wpsu_cdata( 'wpsu_deferred' );

			$deferred = is_array( $deferred ) ? $deferred : array();

			$deferred[ $handle ] = $handle;

			wpsu_cdata( 'wpsu_deferred', $deferred );

			return false;

		}

	

		return $src;

	}

	

	



	

	function wpsu_render_scripts() {

		global $wp_scripts, $wp_filter;

		$deferred = wpsu_cdata( 'wpsu_deferred' );

		$datas    = wpsu_cdata( 'wpsu_deferred_datas' );

	

		if ( ! empty( $deferred ) ) {

			$lab_ver   = '2.0.3';

			$lab_src   = WDJS_PLUGIN_URL . 'assets/js/scripts.js';

			// You also use my plugin SF Cache Busting, right? RIGHT?! ;)

			$lab_src   = function_exists( 'sfbc_build_src_for_cache_busting' ) ? sfbc_build_src_for_cache_busting( $lab_src, $lab_ver ) : $lab_src . '?ver=' . $lab_ver;

			$lab_src   = apply_filters( 'wpsu_labjs_src', $lab_src, $lab_ver );

	

			$start_tag = '<script' . ( apply_filters( 'wpsu_use_html5', false ) ? '' : ' type=\'text/javascript\'' ) . ">/* <![CDATA[ */\n";

			$end_tag   = "\n/* ]]> */</script>";

			$last_cond = null;

	

			$output    = '';

	

			// Data

			if ( ! empty( $datas ) ) {

	

				foreach ( $datas as $handle => $data ) {

					$condition = $wp_scripts->get_data( $handle, 'conditional' );

	

					// Not a conditionnal script.

					if ( ! $condition ) {

						if ( is_null( $last_cond ) ) {

							$output .= $start_tag;

						}

						elseif ( $last_cond ) {

							$output .= "$end_tag<![endif]-->\n$start_tag";

						}

						// if $last_cond === false, do nothing.

					}

					// Conditionnal script.

					else {

						$condition = trim( $condition );

						if ( is_null( $last_cond ) ) {

							$output .= "<!--[if $condition]>$start_tag";

						}

						elseif ( ! $last_cond ) {

							$output .= "$end_tag\n<!--[if $condition]>$start_tag";

						}

						elseif ( $last_cond !== $condition ) {

							$output .= "$end_tag<![endif]-->\n<!--[if $condition]>$start_tag";

						}

						// if $last_cond === $condition, do nothing.

					}

	

					$last_cond = $condition;

					$output .= $data;

				}

	

			}

	

			// Scripts

			if ( is_null( $last_cond ) ) {

				$output .= $start_tag;

			}

			elseif ( $last_cond ) {

				$output .= "$end_tag<![endif]-->\n$start_tag";

			}

	

			$output .= '(function(g,b,d){var c=b.head||b.getElementsByTagName("head"),D="readyState",E="onreadystatechange",F="DOMContentLoaded",G="addEventListener",H=setTimeout;function f(){';

			$output .= '$LAB';

	

			foreach ( $deferred as $handle ) {

				$src = $wp_scripts->registered[ $handle ]->src;

				if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $wp_scripts->content_url && 0 === strpos( $src, $wp_scripts->content_url ) ) ) {

					$src = $wp_scripts->base_url . $src;

				}

				$src = esc_url( $src );

	

				$output .= '.script(';

	

				// Handle scripts for IE.

				if ( $condition = $wp_scripts->get_data( $handle, 'conditional' ) ) {

					$src_string  = 'function(){';

						$src_string .= 'var div = document.createElement("div");';

						$src_string .= 'div.innerHTML = "<!--[if ' . $condition . ']><i></i><![endif]-->";';

						$src_string .= 'return div.getElementsByTagName("i").length ? "' . $src . '" : null;';

					$src_string .= '}';

				}

				else {

					$src_string = '"' . $src . '"';

				}

	

				$output .= apply_filters( 'wpsu_deferred_script_src', $src_string, $handle, $src );

				$output .= ')';

	

				$wait = apply_filters( 'wpsu_deferred_script_wait', '', $handle );

				if ( $wait || wpsu_script_is_dependency( $handle, $deferred ) ) {

					$output .= '.wait(' . $wait . ')';

				}

			}

	

			$output .= apply_filters( 'wpsu_before_end_lab', '' );

	

			$output .= ';}H(function(){if("item"in c){if(!c[0]){H(arguments.callee,25);return}c=c[0]}var a=b.createElement("script"),e=false;a.onload=a[E]=function(){if((a[D]&&a[D]!=="complete"&&a[D]!=="loaded")||e){return false}a.onload=a[E]=null;e=true;f()};a.src="' . $lab_src . '";c.insertBefore(a,c.firstChild)},0);if(b[D]==null&&b[G]){b[D]="loading";b[G](F,d=function(){b.removeEventListener(F,d,false);b[D]="complete"},false)}})(this,document);';

			$output .= $end_tag;

	

			echo $output;

		}

	}		

		

	#JS - END	
	
	#HTACCESS MAGIC BEGINS
	
	function wpsu_htaccess_magic( $rules )
	{
		$wpsu_options = get_option('wpsu_options', array());
		
		if(isset($wpsu_options['disable_hotlinking']) && $wpsu_options['disable_hotlinking']){
		$rules .= '
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?sparringmind.com [NC]
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?google.com [NC]
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?feeds2.feedburner.com/sparringmind [NC]
RewriteRule \.(jpg|jpeg|png|gif)$ – [NC,F,L]
				';
		}
				
		if(isset($wpsu_options['expires_header']) && $wpsu_options['expires_header']){
		$rules .= '
#BEGIN EXPIRES HEADERS
<IfModule mod_expires.c>
# Enable expirations
ExpiresActive On
# Default expiration: 1 hour after request
ExpiresDefault "now plus 1 hour"
# CSS and JS expiration: 1 week after request
ExpiresByType text/css "now plus 1 week"
ExpiresByType application/javascript "now plus 1 week"
ExpiresByType application/x-javascript "now plus 1 week"
# Image files expiration: 1 month after request
ExpiresByType image/bmp "now plus 1 month"
ExpiresByType image/gif "now plus 1 month"
ExpiresByType image/jpg "access 1 month"
ExpiresByType image/jpeg "now plus 1 month"
ExpiresByType image/jp2 "now plus 1 month"
ExpiresByType image/pipeg "now plus 1 month"
ExpiresByType image/png "now plus 1 month"
ExpiresByType image/svg+xml "now plus 1 month"
ExpiresByType image/tiff "now plus 1 month"
ExpiresByType image/vnd.microsoft.icon "now plus 1 month"
ExpiresByType image/x-icon "now plus 1 month"
ExpiresByType image/ico "now plus 1 month"
ExpiresByType image/icon "now plus 1 month"
ExpiresByType text/ico "now plus 1 month"
ExpiresByType application/ico "now plus 1 month"
# Webfonts
ExpiresByType font/truetype "access plus 1 month"
ExpiresByType font/opentype "access plus 1 month"
ExpiresByType application/x-font-woff "access plus 1 month"
ExpiresByType image/svg+xml "access plus 1 month"
ExpiresByType application/vnd.ms-fontobject "access plus 1 month"

ExpiresByType text/html "access 1 month"
ExpiresByType application/pdf "access 1 month"
ExpiresByType text/x-javascript "access 1 month"
ExpiresByType application/x-shockwave-flash "access 1 month"
ExpiresDefault "access 1 month"
</IfModule>
#END EXPIRES HEADERS
				';
		}
			
		if(isset($wpsu_options['cache_control']) && $wpsu_options['cache_control']){
		$rules .= '			
# BEGIN Cache-Control Headers
<ifModule mod_headers.c>
 <filesMatch "\.(ico|jpe?g|png|gif|swf)$">
 Header set Cache-Control "max-age=2592000, public"
 </filesMatch>
 <filesMatch "\.(css)$">
 Header set Cache-Control "max-age=604800, public"
 </filesMatch>
 <filesMatch "\.(js)$">
 Header set Cache-Control "max-age=216000, private"
 </filesMatch>
 <filesMatch "\.(x?html?|php)$">
 Header set Cache-Control "max-age=600, private, must-revalidate"
 </filesMatch>
</ifModule>
# END Cache-Control Headers	';
			
		}	
		
		if(isset($wpsu_options['deflate_compression']) && $wpsu_options['deflate_compression']){
		$rules .= '					
# BEGIN DEFLATE COMPRESSION
<IfModule mod_deflate.c>
# Compress HTML, CSS, JavaScript, Text, XML and fonts
 AddOutputFilterByType DEFLATE application/javascript
 AddOutputFilterByType DEFLATE application/rss+xml
 AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
 AddOutputFilterByType DEFLATE application/x-font
 AddOutputFilterByType DEFLATE application/x-font-opentype
 AddOutputFilterByType DEFLATE application/x-font-otf
 AddOutputFilterByType DEFLATE application/x-font-truetype
 AddOutputFilterByType DEFLATE application/x-font-ttf
 AddOutputFilterByType DEFLATE application/x-javascript
 AddOutputFilterByType DEFLATE application/xhtml+xml
 AddOutputFilterByType DEFLATE application/xml
 AddOutputFilterByType DEFLATE font/opentype
 AddOutputFilterByType DEFLATE font/otf
 AddOutputFilterByType DEFLATE font/ttf
 AddOutputFilterByType DEFLATE image/svg+xml
 AddOutputFilterByType DEFLATE image/x-icon
 AddOutputFilterByType DEFLATE text/css
 AddOutputFilterByType DEFLATE text/html
 AddOutputFilterByType DEFLATE text/javascript
 AddOutputFilterByType DEFLATE text/plain
 AddOutputFilterByType DEFLATE text/xml
</IfModule>
# END DEFLATE COMPRESSION';
		}
		
		if(isset($wpsu_options['gzip_compression']) && $wpsu_options['gzip_compression']){
		$rules .= '			
# BEGIN GZIP COMPRESSION
<IfModule mod_gzip.c>
mod_gzip_on Yes
mod_gzip_dechunk Yes
mod_gzip_item_include file \.(html?|txt|css|js|php|pl)$
mod_gzip_item_include handler ^cgi-script$
mod_gzip_item_include mime ^text/.*
mod_gzip_item_include mime ^application/x-javascript.*
mod_gzip_item_exclude mime ^image/.*
mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
</IfModule>
# END GZIP COMPRESSION';
		}

		if(isset($wpsu_options['unset_etag']) && $wpsu_options['unset_etag']){
		$rules .= '			
Header unset Pragma
FileETag None
Header unset ETag';
		}
		
		return $rules;
	}
	add_filter('mod_rewrite_rules', 'wpsu_htaccess_magic');


	function wpsu_init(){
		$wpsu_options = get_option('wpsu_options', array());
		if(isset($wpsu_options['ob_gzhandler']) && $wpsu_options['ob_gzhandler']){
			ob_start("ob_gzhandler");
		}
	}
	add_action('init', 'wpsu_init');

	add_action('wp_ajax_wpsu_test_ajax', 'wpsu_test_ajax');
    function wpsu_test_ajax(){

        if(isset($_GET['wpsu_ct']) || isset($_GET['wpsu_clear_imgs']) || isset($_GET['wpsu_link_dir'])){
                unset($_GET['wpsu_ct']);
//                unset($_GET['wpsu_link_dir']);
//                unset($_GET['wpsu_clear_imgs']);
                wpsu_compression_check();
        }

        ob_start();

        wpsu_load_img_module();

        $content = ob_get_contents();
        ob_clean();

        echo json_encode(array(

                'compress_report' => $content,
        ));

        wp_die();
    }