<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
Plugin Name: WP SpeedUp
Plugin URI: http://androidbubble.com/blog/wordpress/plugins/wp-speedup
Description: WP SpeedUp is a great plugin to speedup your WordPress website with a simple installation.
Version: 1.4.7
Author: Fahad Mahmood 
Author URI: https://www.androidbubbles.com
Text Domain: wp-speedup
Domain Path: /languages/
License: GPL2

This WordPress Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This free software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/ 

	


	define( 'WPSU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
	
	
	
	
	global $su_premium_link, $wpsu_dir, $wpsu_url, $su_pro, $su_data, $wpsu_css, $dir_size,  $wpsu_compress_images, $su_name, $wpsu_total_bytes, $wpsu_live;
	$wpsu_live = ($_SERVER['REMOTE_ADDR']!='127.0.0.1');



	$wpsu_dir = plugin_dir_path( __FILE__ );
    $wpsu_url = plugin_dir_url( __FILE__ );
	$rendered = FALSE;
	$su_pro = file_exists($wpsu_dir.'pro/wpsu_extended.php');
	$su_data = get_plugin_data(__FILE__);
	$su_premium_link = 'https://shop.androidbubbles.com/product/wp-speedup-pro';//https://shop.androidbubble.com/products/wordpress-plugin?variant=36439508746395';//
	$su_name = 'WP SpeedUp'.(' ('.$su_data['Version'].($su_pro?') Pro':')'));
	$wpsu_compress_images = (get_option('wpsu_compress_images') || isset($_GET['wpsu_ct']));
	$wpsu_compress_images = ($wpsu_compress_images?true:false);
	$wpsu_total_bytes = get_option('wpsu_total_bytes');

	
	$ap_data = get_plugin_data(__FILE__);
	
	
	
	include('inc/functions.php');
	
	if($su_pro){
		include('pro/wpsu_extended.php');
	}
	
	
        
	

	add_action( 'admin_enqueue_scripts', 'register_su_scripts' );
	add_action( 'wp_enqueue_scripts', 'register_su_scripts' );
	add_action('admin_footer', 'wpsu_footer_scripts');
	add_action('admin_init', 'wpsu_actions');
	
	function wpsu_actions(){
		
		global $wpdb;
		//pree($_POST);exit;
		if(!empty($_POST) && (isset($_POST['itom_conversion_innodb']) || isset($_POST['itom_conversion_myisam']))){
						
			if ( 
				! isset( $_POST['wpsu_nonce_action3_field'] ) 
				|| ! wp_verify_nonce( $_POST['wpsu_nonce_action3_field'], 'wpsu_nonce_action3' ) 
			) {
			
			   print 'Sorry, your nonce did not verify.';
			   exit;
			
			} else {
			
			   // process form data
			   if(isset($_POST['itom_conversion_innodb'])){
					$MyISAM = $wpdb->get_results("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE engine = 'MyISAM' AND TABLE_SCHEMA = '".DB_NAME."'", ARRAY_N);
//                    pree($MyISAM);exit;
					if(!empty($MyISAM)){
						foreach($MyISAM as $tbl){

                            $sql = "ALTER TABLE `".DB_NAME."`.`".current($tbl)."` ENGINE = InnoDB";
//							echo $sql.'<br />';
							$wpdb->query($sql);
						}
					}	
			   }
			
			
				if(isset($_POST['itom_conversion_myisam'])){
					$InnoDB = $wpdb->get_results("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE engine = 'InnoDB' AND TABLE_SCHEMA = '".DB_NAME."'", ARRAY_N);
//                    pree($InnoDB);exit;

                    if(!empty($InnoDB)){
						foreach($InnoDB as $tbl){
							//pree($table_name);exit;
                            $sql = "ALTER TABLE `".DB_NAME."`.`".current($tbl)."` ENGINE = MyISAM";
							//echo $sql.'<br />';
							$wpdb->query($sql);
						}
					}			
				}	
			}

		}
		

	}
	
	if(is_admin()){
		
		
		
		add_action( 'admin_menu', 'wpsu_menu' );		
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'wpsu_plugin_links' );	
		
		if((isset($_GET['page']) && $_GET['page']=='wp_su') || $wpsu_compress_images){
			add_action('admin_init', 'wpsu_compression_check');
		}
		
	}elseif(!defined( 'XMLRPC_REQUEST' ) && !defined( 'DOING_CRON' )){
		
	
		if(get_option('selection_js')){
			
			add_filter( 'print_scripts_array', 'wpsu_save_do_not_defer_deps' );
			add_filter( 'script_loader_src', 'wpsu_save_dscripts', PHP_INT_MAX, 2 );
			add_action( 'wp_footer', 'wpsu_render_scripts', PHP_INT_MAX );
		}
		
		
			add_action( 'wp_footer', 'wp_speedup' );									
		
	}	