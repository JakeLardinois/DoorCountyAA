<?php
/**
 * Plugin Name: WP Advanced PDF
 * Plugin URI: http://cedcommerce.com
 * Description: WP Advanced PDF plugin is a pdf generator for wordpress posts to pdf to archive and/or printing. Blog readers can export post into pdf and can print or send it to registered email address.
 * Version: 1.1.7
 * Text Domain: wp-advanced-pdf
 * Author: CedCommerce
 * Author URI: http://cedcommerce.com
 * Tested up to: 6
 * Domain Path: /languages
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (!session_id()) {
    session_start();
}

define ( 'PTPDF_PREFIX', 'ptpdf' );
global $blog_id;
if( $_SERVER['REQUEST_SCHEME'] == "https" ){
	define ( 'PTPDF_URL',  str_replace( "http://", "https://", WP_PLUGIN_URL . '/wp-advanced-pdf' ) );
} else {
	define ( 'PTPDF_URL', WP_PLUGIN_URL . '/wp-advanced-pdf' );
}
define ( 'CACHE_DIR', WP_CONTENT_DIR . '/uploads/wp-advanced-pdf/' . $blog_id );
define ( 'PTPDF_PATH', WP_PLUGIN_DIR . '/wp-advanced-pdf' );
if (! defined ( 'PDF_VERSION_NO' )) {
	define ( 'PDF_VERSION_NO', '1.1.7' );
}

if (function_exists ( 'is_multisite' ) && is_multisite ()) {
	include_once (ABSPATH . 'wp-admin/includes/plugin.php');
}

if (! class_exists ( 'wpppdf' )) {
	class wpppdf {
		private $options = array ();
		public $wppdf_error = 0;
		public $is_send = 0;
		public function __construct() {
			$this->options = get_option ( PTPDF_PREFIX );
			if (is_admin ()) {
				add_action ( 'admin_init', array ( &$this, 'ptp_on_admin_Init' ) );
				add_action ( 'admin_menu', array (&$this, 'ptp_add_admin_menu' ) );
				add_filter ( "plugin_action_links_" . plugin_basename ( __FILE__ ), array ( &$this, 'ptp_action_links_handler' ) );
				register_activation_hook ( plugin_basename ( __FILE__ ), array ( &$this, 'ptp_set_default_on_activate' ) );
				add_filter ( 'page_row_actions', array (&$this,'ptp_add_export_to_pdf_option_hier' ), 10, 2 );
				add_filter ( 'post_row_actions', array ( &$this, 'ptp_add_export_to_pdf_option_nhier' ), 10, 2 );
				add_action ( 'admin_footer-edit.php', array ( &$this,'ptp_custom_bulk_admin_footer' ) );
				add_action ( 'load-edit.php', array ( &$this, 'ptp_custom_bulk_action' ) );
				add_action ( 'admin_notices', array ( &$this, 'ptp_custom_bulk_admin_notices' ) );
			} else {
				add_action ( 'wp', array ( &$this, 'ptp_generate_post_to_pdf' ) );
				add_filter ( 'the_content', array (	&$this,	'ptp_add_pdf_link' ) );
			}
			add_filter ( 'cron_schedules', array ( &$this, 'ptp_pdfscheduleduration' ) );
			add_action ( 'wp', array ( &$this, 'ptp_cronstarter_activation' ) );
			register_deactivation_hook ( __FILE__, array ( &$this, 'ptp_cronstarter_deactivate' ) );
			add_action ( 'schedulecacheupdate', array (	&$this,	'ptp_update_cache' ) );
			add_action ( 'wp_enqueue_scripts', array ( &$this,	'ptp_ptpdf_theme_scripts' ) );
			add_action ( 'wp_ajax_nopriv_postajax_exportandmail', array (&$this, 'postajax_exportandmail_handle' ) );
			add_action('wp_ajax_wpppdf_send_mail',array(&$this,'wpppdf_send_mail'));
			
			register_activation_hook ( __FILE__, array ( &$this, 'ptp_export_install' ) );
			add_action ( 'wp_head', array (	&$this,	'ptp_hook_div_for_guest' ) );
			add_action ( 'init', array ( &$this, 'ptp_init_theme_method' ) );
			
			/**
			 * ADD FOLLOWING ACTION OR FITER INTO YOU MAIN CLASS FILE
			 */
			
			add_action ( 'plugins_loaded', array ( $this, 'load_textdomain' ) );
			add_filter ( 'plugin_row_meta', array (	$this, 'ptp_custom_plugin_row_meta'	), 10, 2 );	
			add_action ( 'wp_ajax_add_custom_font', array (	$this,'ptp_add_custom_font' ) );

			if( isset( $this->options[ 'postPublish' ] ) ) {
				add_action ( 'transition_post_status', array ( $this, PTPDF_PREFIX.'_email_on_publish' ), 10, 3 );
				add_action ( 'admin_notices', array ( &$this, 'ced_admin_notices' ) );
			}

		}

		function wpppdf_send_mail()
		{
			if(isset($_POST["flag"]) && $_POST["flag"]==true && !empty($_POST["emailid"]))
			{
				$to = "support@cedcommerce.com";
				$subject = "Wordpress Org Know More";
				$message = 'This user of our woocommerce extension "WP Advanced PDF" wants to know more about marketplace extensions.<br>';
				$message .= 'Email of user : '.$_POST["emailid"];
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$flag = wp_mail( $to, $subject, $message);	
				if($flag == 1)
				{
					echo json_encode(array('status'=>true,'msg'=>__('Soon you will receive the more details of this extension on the given mail.',"wp-advanced-pdf")));
				}
				else
				{
					echo json_encode(array('status'=>false,'msg'=>__('Sorry,an error occured.Please try again.',"wp-advanced-pdf")));
				}
			}
			else
			{
				echo json_encode(array('status'=>false,'msg'=>__('Sorry,an error occured.Please try again',"wp-advanced-pdf")));
			}
			wp_die();
		}
		
		/**
		 * Add custom plugin row meta
		 * @param array $links
		 * @param string $file
		 */
		function ptp_custom_plugin_row_meta( $links, $file ) {
			if ( strpos( $file, 'wp-advanced-pdf.php' ) !== false ) {
				$new_links = array(
						'demo' => '<a href=" http://demo.cedcommerce.com/wordpress/advanced-pdf/wp-login.php" target="_blank">Demo</a>',
						'documentation' => '<a href="http://demo.cedcommerce.com/wordpress/advanced-pdf/doc/index.html" target="_blank">Documentation</a>',
				);
				$links = array_merge( $links, $new_links );
			}
			return $links;
		}
		
		function load_textdomain() {
			$domain = "wp-advanced-pdf";
			$locale = apply_filters ( 'plugin_locale', get_locale (), $domain );
			load_textdomain ( $domain, PTPDF_PATH . '/languages/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain ( 'wp-advanced-pdf', false, plugin_basename ( dirname ( __FILE__ ) ) . '/languages' );
		}
		
		/**
		 * Step 1: add the custom Bulk Action to the select menus
		 */
		function ptp_custom_bulk_admin_footer() {
			
		}
		
		/**
		 * Step 2: handle the custom Bulk Action
		 *
		 * Based on the post http://wordpress.stackexchange.com/questions/29822/custom-bulk-action
		 */
		function ptp_custom_bulk_action() {
			global $typenow;
			$post_type = $typenow;
			if (isset ( $this->options [$post_type] )) {
				// get the action
				$wp_list_table = _get_list_table ( 'WP_Posts_List_Table' ); // depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc
				$action = $wp_list_table->current_action ();
				$allowed_actions = array (
						"export" 
				);
				if (! in_array ( $action, $allowed_actions ))
					return;
					// security check
				check_admin_referer ( 'bulk-posts' );
				// make sure ids are submitted. depending on the resource type, this may be 'media' or 'ids'
				if (isset ( $_REQUEST ['post'] )) {
					$post_ids = array_map ( 'intval', $_REQUEST ['post'] );
				}
				if (empty ( $post_ids ))
					return;
					// this is based on wp-admin/edit.php
				$sendback = remove_query_arg ( array (
						'exported',
						'untrashed',
						'deleted',
						'ids' 
				), wp_get_referer () );
				if (! $sendback)
					$sendback = admin_url ( "edit.php?post_type=$post_type" );
				$pagenum = $wp_list_table->get_pagenum ();
				$sendback = add_query_arg ( 'paged', $pagenum, $sendback );
				switch ($action) {
					case 'export' :
						$exported = 0;
						foreach ( $post_ids as $post_id ) {
							// add posts to in an array to convert into pdf
							$exported ++;
						}
						if ($exported)
							$this->ptp_generate_pdf_bulk_posts ( $post_ids );
						
						$sendback = add_query_arg ( array (
								'exported' => $exported,
								'ids' => join ( ',', $post_ids ) 
						), $sendback );
						break;
					
					default :
						return;
				}
				$sendback = remove_query_arg ( array (
						'action',
						'action2',
						'tags_input',
						'post_author',
						'comment_status',
						'ping_status',
						'_status',
						'post',
						'bulk_edit',
						'post_view' 
				), $sendback );
				
				wp_redirect ( $sendback );
				exit ();
			}
		}
		/**
		 * Step 3: display an admin notice on the Posts page after exporting
		 */
		function ptp_custom_bulk_admin_notices() {
			global $post_type, $pagenow;
			
			if ($pagenow == 'edit.php' && isset ( $this->options [$post_type] ) && isset ( $_REQUEST ['exported'] ) && ( int ) $_REQUEST ['exported']) {
				$message = sprintf ( _n ( 'Post exported.', '%s posts exported.', $_REQUEST ['exported'] ), number_format_i18n ( $_REQUEST ['exported'] ) );
				echo "<div class=\"updated\"><p>{$message}</p></div>";
			}
		}

		function ced_admin_notices() { 
			global $post_type, $pagenow;
			if ($pagenow == 'post.php' && isset($_SESSION['mail']) && $_SESSION['mail'] == 1 ) {
				?>
				<div data-dismissible="disable-done-notice-forever" class="update notice notice-success is-dismissible">
		        <p><?php _e( 'MAIL SENT!', 'sample-text-domain' ); ?></p>
		    	</div>
			<?php 	unset($_SESSION['mail']); 
			}
			
			else if ($pagenow == 'post.php' && isset($_SESSION['notmail']) && $_SESSION['notmail'] == 1 ) {
				?>
				<div data-dismissible="disable-done-notice-forever" class="error notice notice-success is-dismissible">
		        <p><?php _e( 'NOT SENT!', 'sample-text-domain' ); ?></p>
		    	</div>
			<?php 	unset($_SESSION['notmail']); 
			}

		}

		
		
		/**
		 * Sends postid to function ptp_generate_pdf_file_bulk and generates output
		 * 
		 * @param array $post_ids
		 *        	an array of postIDs of which to export into pdf
		 */
		function ptp_generate_pdf_bulk_posts($post_ids) {
			$filePath = CACHE_DIR . '/' . 'bulk' . '.pdf';
			$fileMime = 'pdf';
			$fileName = 'bulk.pdf';
			$this->ptp_generate_pdf_file_bulk ( $post_ids );
			$output = $this->output_Post_to_pdf_file ( $filePath, $fileName, $fileMime );
		}
		
		/**
		 * Generates pdf of an array of posts
		 * 
		 * @param array $post_ids        	
		 */
		function ptp_generate_pdf_file_bulk($post_ids) {
				// include only once class library
			if (! class_exists ( 'TCPDF' )) {
				require_once PTPDF_PATH . '/tcpdf_min/tcpdf.php';
			}
			if (! class_exists ( 'pdfheader' )) {
				require_once PTPDF_PATH . '/pdfheader.php';
			}
			if (! class_exists ( 'simple_html_dom_node' )) {
				require_once PTPDF_PATH . '/simplehtmldom/simple_html_dom.php';
			}
			$filePath = CACHE_DIR . '/' . 'bulk' . '.pdf';
			require_once PTPDF_PATH . '/export/adminbulk.php';
		}
		
		/**
		 * Adds exportpdf link for post type page and custom types
		 * 
		 * @param array $actions        	
		 * @param int $post        	
		 * @return array|string
		 */
		function ptp_add_export_to_pdf_option_hier($actions, $post) {
			if (isset ( $this->options ['admin_panel'] )) {
				if (! isset ( $this->options [$post->post_type] ))
					return $actions;
				$url = esc_url ( add_query_arg ( 'format', 'pdf', get_permalink ( $post->ID ) ) );
				$actions ['exportpdf'] = '<a href="' . $url . '" title="' . esc_attr__ ( 'Export to pdf', 'wp-advanced-pdf' ) . '">' . __ ( 'Exportpdf', 'wp-advanced-pdf' ) . '</a>';
				return $actions;
			}
			return $actions;
		}
		
		/**
		 * adds exportpdf link for post type post
		 * 
		 * @param array $actions        	
		 * @param int $post        	
		 * @return array|string
		 */
		function ptp_add_export_to_pdf_option_nhier($actions, $post) {
			if (isset ( $this->options ['admin_panel'] )) {
				if (! isset ( $this->options [$post->post_type] ))
					return $actions;
				$url = esc_url ( add_query_arg ( 'format', 'pdf', get_permalink ( $post->ID ) ) );
				$actions ['exportpdf'] = '<a href="' . $url . '" title="' . esc_attr__ ( 'Export to pdf', 'wp-advanced-pdf' ) . '">' . __ ( 'Exportpdf', 'wp-advanced-pdf' ) . '</a>';
			}
			return $actions;
		}
		function ptp_init_theme_method() {
			
			add_thickbox ();
		}
		
		/**
		 * Adds an div for popup to guest users
		 */
		function ptp_hook_div_for_guest() {
			$html = "<div style='float:left;padding:20px 20px 20px 20px;'><h4>Enter your email Address</h4>";
			$html .= '<input type="text" style="margin-top:10px" name="useremailID" id="useremailID"><input type="hidden" id="emailpostID">';
			$html .= "<input id='guest_email' style='margin-top:10px' class='button-primary' type='submit' name='email_submit' value='submit'></div>";
			$output = '<div id="examplePopup1" style="display:none;">' . $html . '</div>';
			echo $output;
		}
		
		/**
		 * enqueue scripts for ajax requests
		 */
		function ptp_ptpdf_theme_scripts() {
			wp_enqueue_style ( 'ptpdffrontend', PTPDF_URL . '/asset/css/front_end.css' , array(), PDF_VERSION_NO);
			wp_enqueue_script ( 'ajaxsave', PTPDF_URL. '/asset/js/ajaxsave.js', array ('jquery') , PDF_VERSION_NO );
			wp_localize_script ( 'ajaxsave', 'postajaxsave', array (
					'ajax_url' => admin_url ( 'admin-ajax.php' ),
					'baseUrl'  => PTPDF_URL
			) );
		}
		
		/**
		 * enqueue custom script for admin option pages
		 */
		function ptp_enqueue_custom_script() {
			global $post;
			wp_enqueue_script( 'wpppdf-select2-js', PTPDF_URL.'/asset/js/wpppdf-select2.min.js', array( 'jquery', 'jquery-ui-core' ), PDF_VERSION_NO);
			wp_enqueue_style( 'wpppdf-select2-css', PTPDF_URL.'/asset/css/wpppdf-select2.min.css', array(), PDF_VERSION_NO );
			wp_enqueue_style ( 'ptpdfadminstyle', PTPDF_URL . '/asset/css/admin.css', array(), PDF_VERSION_NO, 'all' );
			$post_export = null;
			if (isset ( $this->options ['admin_panel'] )) {
				global $post_type;
				if (isset ( $this->options [$post_type] )) {
					$post_export = true;
				}
			}
				
			wp_enqueue_script ( 'bulk', PTPDF_URL . '/asset/js/bulk.js' , array(),  PDF_VERSION_NO );
			$translation_array = array(
					'txt' => __( 'Export', 'wp-advanced-pdf' ),
					'post_export' => $post_export,
					'reset_nonce' => wp_create_nonce( 'wp-reset-nonce' ),
			);
			wp_localize_script ( 'bulk', 'bulk_obj', $translation_array);

			wp_localize_script('bulk','ajax_url',admin_url('admin-ajax.php'));
			if(isset($post)){
				if (! isset ( $this->options [$post->post_type] ))
					return false;
				if ('pdf' == (isset ( $_GET ['format'] ) ? $_GET ['format'] : null)) {
					
					$post = get_post ();
					$content = $post->the_content;
					if ($this->options ['ced_file_name'] == 'post_name') {
						$filePath = CACHE_DIR . '/' . $post->post_name . '.pdf';
						$fileName = $post->post_name . '.pdf';
					} else {
						$filePath = CACHE_DIR . '/' . $post->ID . '.pdf';
						$fileName = $post->ID . '.pdf';
					}
					$fileMime = 'pdf';
					if (! isset ( $this->options ['includefromCache'] )) {
						$this->generate_post_to_pdf_file ( $post->ID );
					} else {
						if (! file_exists ( $filePath )) {
							$this->generate_post_to_pdf_file ( $post->ID );
						}
					}
					$output = $this->output_Post_to_pdf_file ( $filePath, $fileName, $fileMime );
				}
			}
		}
		
		/**
		 * checks for update on admin init
		 */
		function ptp_on_admin_Init() {
			register_setting ( PTPDF_PREFIX.'_options', PTPDF_PREFIX, array (
					&$this,
					'ptp_on_update_options' 
			) );
			add_action ( 'admin_enqueue_scripts', array (
					&$this,
					'ptp_enqueue_custom_script' 
			) );
		}
		
		/**
		 * Reset PDF Setting currently updating
		 * 
		 * @param int $post        	
		 * @return int
		 */
		function ptp_on_update_options($option) {
			return $option;
		}
		function ptp_action_links_handler($action_links) {
			$settings_link = '<a href="options-general.php?page=' . plugin_basename ( __FILE__ ) . '">Settings</a>';
			array_unshift ( $action_links, $settings_link );
			return $action_links;
		}
		
		/**
		 * add an option page attached with a setting submenu
		 */
		function ptp_add_admin_menu() {
			$option_page = add_options_page ( 'WP Advanced PDF Options', 'WP Advanced PDF', 'manage_options', plugin_basename ( __FILE__ ), array (
					&$this,
					'ptp_option_page_handler' 
			) );
		}
		
		/**
		 * attach option page to submenu
		 */
		function ptp_option_page_handler() {
			if (! current_user_can ( 'manage_options' ))
				wp_die ( 'You don\'t have access to this page.' );
			if (! user_can_access_admin_page ())
				wp_die ( __ ( 'You do not have sufficient permissions to access this page', 'wp-advanced-pdf' ) );
			require (PTPDF_PATH . '/pdf_options.php');
		}
		
		function ptp_deafult_setting() {
			$default = array (
					'post' => 1,
					'page' => 1,
					'include' => 0,
					'pluginVer' => PDF_VERSION_NO,
					'content_placement' => 'before',
					'content_position' => 'left',
					'imagefactor' => 14,
					'imageScale' => 1.25,
					'header_font_pdf' => 'helvetica',
					'header_font_size' => 10,
					'footer_font_pdf' => 'helvetica',
					'footer_font_size' => 10,
					'content_font_pdf' => 'helvetica',
					'content_font_size' => 12,
					'marginHeader' => 5,
					'marginTop' => 27,
					'marginLeft' => 15,
					'marginRight' => 15,
					'footer_font_margin' => 10,
					'footer_min_height' => 0,
					'footer_cell_width' => 0,
					'footer_lcornerX' => 15,
					'footer_font_lcornerY' => 290,
					'footer_cell_fill' => 0,
					'footer_cell_auto_padding' => 1,
					'page_size' => 'LETTER',
					'unitmeasure' => 'mm',
					'page_orientation' => 'P',
					'custom_image_width' => 3,
					'custom_image_height' => 2,
					'set_rotation'   =>'0',
					'fontStretching' => '100',
					'fontSpacig' => '0',
			);
			return $default;
		}
		
		/**
		 * sets default values in plugin activate
		 */
		function ptp_set_default_on_activate() {
			// set dafault option on activate
			$default = $this->ptp_deafult_setting();
			
			if (! get_option ( PTPDF_PREFIX )) {
				add_option ( PTPDF_PREFIX, $default );
			}
			// create directory and move logo to upload directory
			if (! file_exists ( WP_CONTENT_DIR . '/uploads' )) {
				@mkdir ( WP_CONTENT_DIR . '/uploads' );
			}
		}
		
		/**
		 * generates pdf file of current post
		 * 
		 * @return boolean
		 */
		function ptp_generate_post_to_pdf() {
			
			global $post;
			if(http_response_code()!='404') {
				if (! isset ( $this->options [$post->post_type] ))
					return false;
				if ('pdf' == (isset ( $_GET ['format'] ) ? $_GET ['format'] : null)) {
					global $post;
					$post = get_post ();
					$content = $post->the_content;
					if ($this->options ['ced_file_name'] == 'post_name') {
						$filePath = CACHE_DIR . '/' . $post->post_name . '.pdf';
						$fileName = $post->post_name . '.pdf';
					} else {
						$filePath = CACHE_DIR . '/' . $post->ID . '.pdf';
						$fileName = $post->ID . '.pdf';
					}
					$fileMime = 'pdf';
					if (! isset ( $this->options ['includefromCache'] )) {
						$this->generate_post_to_pdf_file ( $post->ID );
					} else {
						if (! file_exists ( $filePath )) {
							$this->generate_post_to_pdf_file ( $post->ID );
						}
					}
					$output = $this->output_Post_to_pdf_file ( $filePath, $fileName, $fileMime );
				}
			}	
		}
		
		/**
		 * checks whether pdf for the post is in cache or not
		 * 
		 * @param int $postID        	
		 * @param string $useremailID        	
		 * @return boolean
		 */
		function ptp_email_pdf($postID, $useremailID) {
			$post = get_post ( $postID );
			$content = $post->post_content;
			
			if ($this->options ['ced_file_name'] == 'post_name') {
				$filePath = CACHE_DIR . '/' . $post->post_name . '.pdf';
				$fileName = $post->post_name . '.pdf';
			} else {
				$filePath = CACHE_DIR . '/' . $post->ID . '.pdf';
				$fileName = $post->ID . '.pdf';
			}
			
			$fileMime = 'pdf';
			if (! isset ( $this->options ['includefromCache'] )) {
				$this->generate_pdf_file_email ( $post->ID, $useremailID );
			} else {
				if (! file_exists ( $filePath )) {
					$this->generate_pdf_file_email ( $post->ID, $useremailID );
				}
			}
			// $output = $this->output_Post_to_pdf_file( $filePath, $fileName, $fileMime );
		}
		
		/**
		 * generate output of pdf generated
		 * 
		 * @param string $pdffile        	
		 * @param string $file_name        	
		 * @param string $mimepdftype        	
		 * @return boolean
		 */
		function output_Post_to_pdf_file($pdffile, $file_name, $mimepdftype = '') {
			if (! is_readable ( $pdffile ))
				return false;
			$size = filesize ( $pdffile );
			$file_name = rawurldecode ( $file_name );
			$known_mime_types = array (
					"pdf" => "application/pdf",
					"txt" => "text/plain",
					"html" => "text/html",
					"htm" => "text/html",
					"exe" => "application/octet-stream",
					"zip" => "application/zip",
					"doc" => "application/msword",
					"xls" => "application/vnd.ms-excel",
					"ppt" => "application/vnd.ms-powerpoint",
					"gif" => "image/gif",
					"png" => "image/png",
					"jpeg" => "image/jpg",
					"jpg" => "image/jpg",
					"php" => "text/plain" 
			);
			if ($mimepdftype == '') {
				$file_extension = strtolower ( substr ( strrchr ( $pdffile, "." ), 1 ) );
				if (array_key_exists ( $file_extension, $known_mime_types )) {
					$mimepdftype = $known_mime_types [$file_extension];
				} else {
					$mimepdftype = "application/force-download";
				}
			}
			@ob_end_clean ();
			
			if (ini_get ( 'zlib.output_compression' ))
				ini_set ( 'zlib.output_compression', 'Off' );
			header ( 'Content-Type: ' . $mimepdftype );
			header ( 'Content-Disposition: attachment; filename="' . $file_name . '"' );
			header ( "Content-Transfer-Encoding: binary" );
			header ( 'Accept-Ranges: bytes' );
			
			header ( "Cache-control: private" );
			header ( 'Pragma: private' );
			header ( "Expires: tue, 26 aug 2015 05:00:00 GMT" );
			
			if (isset ( $_SERVER ['HTTP_RANGE'] )) {
				list ( $a, $range ) = explode ( "=", $_SERVER ['HTTP_RANGE'], 2 );
				list ( $range ) = explode ( ",", $range, 2 );
				list ( $range, $range_end ) = explode ( "-", $range );
				$range = intval ( $range );
				if (! $range_end) {
					$range_end = $size - 1;
				} else {
					$range_end = intval ( $range_end );
				}
				$new_length = $range_end - $range + 1;
				header ( "HTTP/1.1 206 Partial Content" );
				header ( "Content-Length: $new_length" );
				header ( "Content-Range: bytes $range-$range_end/$size" );
			} else {
				$new_length = $size;
				header ( "Content-Length: " . $size );
			}
			
			$chunksize = 1 * (1024 * 1024);
			$bytes_send = 0;
			if ($pdffile = fopen ( $pdffile, 'r' )) {
				if (isset ( $_SERVER ['HTTP_RANGE'] ))
					fseek ( $pdffile, $range );
				while ( ! feof ( $pdffile ) && (! connection_aborted ()) && ($bytes_send < $new_length) ) {
					$buffer = fread ( $pdffile, $chunksize );
					print ($buffer) ;
					flush ();
					$bytes_send += strlen ( $buffer );
				}
				fclose ( $pdffile );
			} else
				return false;
			return true;
		}
		
		/**
		 * generate pdf of a post and send email to guest user
		 * 
		 * @param int $postID        	
		 * @param string $useremailID        	
		 */
		function generate_pdf_file_email($postID, $useremailID) {
			$post = get_post ( $postID );
			$content = $post->post_content;
			if (! class_exists ( 'TCPDF' )) {
				require_once PTPDF_PATH . '/tcpdf_min/tcpdf.php';
			}
			if (! class_exists ( 'pdfheader' )) {
				require_once PTPDF_PATH . '/pdfheader.php';
			}
			if (! class_exists ( 'simple_html_dom_node' )) {
				require_once PTPDF_PATH . '/simplehtmldom/simple_html_dom.php';
			}
			$post->post_content = apply_filters ( 'the_post_export_content', $post->post_content );
			$post->post_content = wpautop ( $post->post_content );
			$post->post_content = do_shortcode ( $post->post_content );
			
			if ($this->options ['ced_file_name'] == 'post_name') {
				$filePath = CACHE_DIR . '/' . $post->post_name . '.pdf';
			} else {
				$filePath = CACHE_DIR . '/' . $post->ID . '.pdf';
			}
			// new PDF document
			
			if (isset ( $this->options ['page_size'] )) {
				$pagesize = ($this->options ['page_size']);
			} else {
				$pagesize = PDF_PAGE_FORMAT;
			}
			if (isset ( $this->options ['unitmeasure'] )) {
				$unit = ($this->options ['unitmeasure']);
			} else {
				$unit = PDF_UNIT;
			}
			if (isset ( $this->options ['page_orientation'] )) {
				$orientation = ($this->options ['page_orientation']);
			} else {
				$orientation = PDF_PAGE_ORIENTATION;
			}
			$pdf = new CUSTOMPDF ( $orientation, $unit, $pagesize, true, 'UTF-8', false );
			// information about doc
			if (!empty ( $this->options ['rtl_support'] )) {//die();
				// set some language dependent data:
				$lg = Array();
				$lg['a_meta_charset'] = 'UTF-8';
				$lg['a_meta_dir'] = 'rtl';
				$lg['a_meta_language'] = 'fa';
				$lg['w_page'] = 'page';
					
				// set some language-dependent strings (optional)
				$pdf->setLanguageArray($lg);
				$pdf->setRTL(true);
			}
			$pdf->SetCreator ( 'Post to PDF plugin by CedCommerce with ' . PDF_CREATOR );
			$pdf->SetAuthor ( get_bloginfo ( 'name' ) );
			if (! empty ( $this->options ['custom_title'] )) {
				$pdf_title = $this->options ['custom_title'];
			} else {
				$pdf_title = $post->post_title;
			}
			
			//$pdf->SetTitle ( apply_filters ( 'the_post_title', $pdf_title ) );
			// logo width calculation
			if (isset ( $this->options ['page_header'] ) and ($this->options ['page_header']) == 'upload-image' and !empty ( $this->options ['logo_img_url'] )) {
				// if($this->options['page_header']=="web_icon") {
				// $logoImage_url = PTPDF_URL .'/asset/images/logo.png';
				// }
				if ($this->options ['page_header'] == "upload-image") {
					$logoImage_url = $this->options ['logo_img_url'];
				}
				$infologo = getimagesize ( $logoImage_url );
				try {
					if (isset ( $this->options ['imagefactor'] )) {
						$logo_width = @ ( int ) (($this->options ['imagefactor'] * $infologo [0]) / $infologo [1]);
					} else {
						$logo_width = @ ( int ) ((12 * $infologo [0]) / $infologo [1]);
					}
				}
				catch(Exception $e){
				  throw new Exception("Invalid Size Image..");
				  echo "Exception:".$e->getMessage();
				}
			}
			if (isset ( $this->options ['page_header'] ) and ($this->options ['page_header']) == 'None') {
			
				$logoImage_url="";
				$logo_width="";
			}
			$blog_name = get_bloginfo ( 'name' );
			$bolg_description = get_bloginfo ( 'description' );
			$home_url = home_url ();
			$ptpdfoption_status = get_option ( PTPDF_PREFIX );
			if(isset($ptpdfoption_status)){
				$name_status=isset($ptpdfoption_status['show_site_name'])? $ptpdfoption_status['show_site_name']: '' ;
				$desc_status=isset($ptpdfoption_status['show_site_descR'])? $ptpdfoption_status['show_site_descR']: '' ;
				$url_status=isset($ptpdfoption_status['show_site_URL'])? $ptpdfoption_status['show_site_URL']: '' ;
			}
			// for PHP 5.4 or below set default header data
			if (version_compare ( phpversion (), '5.4.0', '<' )) {
				if ($name_status == '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description . "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''),html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description. "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width  );
				}
			} elseif(version_compare ( phpversion (), '5.4.0', '>' )) {
				if ($name_status == '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description . "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''),html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description. "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width  );
				}
			}
				
			// set header and footer fonts
			if (($this->options ['header_font_size']) > 0) {
				$header_font_size = $this->options ['header_font_size'];
			} else {
				$header_font_size = 10;
			}
			if (($this->options ['footer_font_size']) > 0) {
				$footer_font_size = $this->options ['footer_font_size'];
			} else {
				$footer_font_size = 10;
			}
			$pdf->setHeaderFont ( array (
					$this->options ['header_font_pdf'],
					'',
					$header_font_size 
			) );
			$pdf->setFooterFont ( array (
					$this->options ['footer_font_pdf'],
					'',
					$footer_font_size 
			) );
			
			$pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );
			
			if (isset($this->options ['marginLeft']) ) {
				$pdf->SetLeftMargin ( $this->options ['marginLeft'] );
			} else {
				$pdf->SetLeftMargin ( PDF_MARGIN_LEFT );
			}
			
			if (isset($this->options ['marginRight'] )) {
				$pdf->SetRightMargin ( $this->options ['marginRight'] );
			} else {
				$pdf->SetRightMargin ( PDF_MARGIN_RIGHT );
			}
			
			if (isset($this->options ['marginTop'] )) {
				$pdf->SetTopMargin ( $this->options ['marginTop'] );
			} else {
				$pdf->SetTopMargin ( PDF_MARGIN_TOP );
			}
			if (isset($this->options ['logomTop'])) {
				$pdf->SetHeaderMargin ( $this->options ['logomTop'] );
			} else {
				$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
			}
			
			if (isset($this->options ['footer_font_margin'] )) {
				$pdf->SetFooterMargin ( $this->options ['footer_font_margin'] );
				// set auto page breaks
				$pdf->SetAutoPageBreak ( TRUE,  $this->options ['footer_font_margin']  );
			} else {
				$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );
				// set auto page breaks
				$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_FOOTER );
			}
			
			if ($this->options ['imageScale'] > 0) {
				$pdf->setImageScale ( $this->options ['imageScale'] );
			} else {
				$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );
			}
			
			// set default font subsetting mode
			$pdf->setFontSubsetting ( true );
			
			$pdf->SetFont ( $this->options ['content_font_pdf'], '', $this->options ['content_font_size'], '', true );
			
			if (! empty ( $this->options ['bullet_img_url'] )) {
				$temp = $this->options ['bullet_img_url'];
				$temp = end ( explode ( '/', $temp ) );
				$temp = end ( explode ( '.', $temp ) );
				$listsymbol = 'img|' . $temp . '|' . $this->options ['custom_image_width'] . '|' . $this->options ['custom_image_height'] . '|' . $this->options ['bullet_img_url'];
				$pdf->setLIsymbol ( $listsymbol );
			}
			
			// Add a page
			// This method has several options, check the source code documentation for more information.
// 			$pdf->AddPage ();
			
			if ($this->options ['fontStretching']) {
				$pdf->setFontStretching($this->options ['fontStretching']);
			}
			if ($this->options ['fontSpacig']) {
				$pdf->setFontSpacing($this->options ['fontSpacig']);
			}
			$page_format = array();
			if ($this->options ['set_rotation']) {
				$page_format['Rotate'] = $this->options ['set_rotation'];
			} else {
				$page_format['Rotate'] = 0;
			}
			$pdf->AddPage($this->options ['page_orientation'], $page_format, false, false);
			$html = '';
			if (isset ( $this->options ['CustomCSS_option'] )) {
				$html = '<style>' . $this->options ['Customcss'] . '</style>';
			}
			$html .= "<body>";
			$html .= "<h1 style=\"text-align:center\">".apply_filters ( 'the_post_title', $pdf_title )."</h1>";
			
			if (isset ( $this->options ['authorDetail'] ) and ! $this->options ['authorDetail'] == '') {
				$author_id = $post->post_author;
				$author_meta_key = $this->options ['authorDetail'];
				$author = get_user_meta ( $author_id );
				$html .= '<p><strong>Author : </strong>' . $author [$author_meta_key] [0] . '</p>';
			}
			
			if (isset ( $this->options ['postCategories'] )) {
				$categories = get_the_category ( $post->ID );
				if ($categories) {
					$html .= '<p><strong>Categories : </strong>' . $categories [0]->cat_name . '</p>';
				}
			}
			// Display tag list is set in config
			if (isset ( $this->options ['postTags'] )) {
				$tags = get_the_tags ( $post->the_tags );
				if ($tags) {
					$html .= '<p><strong>Tagged as : </strong>';
					foreach ( $tags as $tag ) {
						$tag_link = get_tag_link ( $tag->term_id );
						$html .= '<a href="' . $tag_link . '">' . $tag->name . '</a>';
						if (next ( $tags )) {
							$html .= ', ';
						}
					}
					$html .= '</p>';
				}
			}
			// Display date if set in config
			if (isset ( $this->options ['postDate'] )) {
				$newDate = date ( "d-m-Y", strtotime ( $post->post_date ) );
				$html .= '<p><strong>Date : </strong>' . $newDate . '</p>';
			}
			
			// Set some content to print
			//$html .= '<h1>' . html_entity_decode ( $pdf_title, ENT_QUOTES ) . '</h1>';
			
			// Display feachered image if set in config on page/post
			if (isset ( $this->options ['show_feachered_image'] )) {
				if (has_post_thumbnail ( $post->ID )) {
					$html .= get_the_post_thumbnail ( $post->ID );
				}
			}
			$post_content = $post->post_content;
			if (empty ( $post->post_content )) {
				$post_content = isset ( $this->options ['docEntryTpl'] ) ? $this->options ['docEntryTpl'] : '';
			}
			$html .= htmlspecialchars_decode ( htmlentities ( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
			$html .="</body>";
			$dom = new simple_html_dom ();
			$dom->load ( $html );
			
			foreach ( $dom->find ( 'img' ) as $e ) {
				$exurl = ''; // external streams
				$imsize = FALSE;
				$file = $e->src;
				// check if we are passing an image as file or string
				if ($file [0] === '@') {
					// image from string
					$imgdata = substr ( $file, 1 );
				} else { // image file
					if ($file [0] === '*') {
						// image as external stream
						$file = substr ( $file, 1 );
						$exurl = $file;
					}
					// check if is local file
					if (! @file_exists ( $file )) {
						// encode spaces on filename (file is probably an URL)
						$file = str_replace ( ' ', '%20', $file );
					}
					if (@file_exists ( $file )) {
						// get image dimensions
						$imsize = @getimagesize ( $file );
					}
					if ($imsize === FALSE) {
						$imgdata = TCPDF_STATIC::fileGetContents ( $file );
					}
				}
				if (isset ( $imgdata ) and ($imgdata !== FALSE) and (strpos ( $file, '__tcpdf_img' ) === FALSE)) {
					// check Image size
					$imsize = @getimagesize ( $file );
				}
				if ($imsize === FALSE) {
					$e->outertext = '';
				} else {
					// End Image Check
					if (preg_match ( '/alignleft/i', $e->class )) {
						$imgalign = 'left';
					} elseif (preg_match ( '/alignright/i', $e->class )) {
						$imgalign = 'right';
					} elseif (preg_match ( '/aligncenter/i', $e->class )) {
						$imgalign = 'center';
						$htmlimgalign = 'middle';
					} else {
						$imgalign = 'none';
					}
					
					$e->class = null;
					$e->align = $imgalign;
					if (isset ( $htmlimgalign )) {
						$e->style = 'float:' . $htmlimgalign;
					} else {
						$e->style = 'float:' . $imgalign;
					}
					
					if (strtolower ( substr ( $e->src, - 4 ) ) == '.svg') {
						$e->src = null;
						if($imgalign!='none'){
							$e->outertext = '<div style="text-align:' . $imgalign . '">[ SVG: ' . $e->alt . ' ]</div><br/>';
						}
					} else {
						if($imgalign!='none'){
							$e->outertext = '<div style="text-align:' . $imgalign . '">' . $e->outertext . '</div>';
						}
					}
				}
			}
			$html = $dom->save ();
			$dom->clear ();
			$pdf->setFormDefaultProp ( array (
					'lineWidth' => 1,
					'borderStyle' => 'solid',
					'fillColor' => array (
							255,
							255,
							200 
					),
					'strokeColor' => array (
							255,
							128,
							128 
					) 
			) );
			// Print text using writeHTML
			$pdf->writeHTML ( $html, true, 0, true, 0 );
			if (isset ( $this->options ['add_watermark'] )) {
				$no_of_pages = $pdf->getNumPages ();
				for($i = 1; $i <= $no_of_pages; $i ++) {
					$pdf->setPage ( $i );
					
					// Get the page width/height
					$myPageWidth = $pdf->getPageWidth ();
					$myPageHeight = $pdf->getPageHeight ();
					
					// Find the middle of the page and adjust.
					$myX = ($myPageWidth / 2) - 75;
					$myY = ($myPageHeight / 2) + 25;
					
					// Set the transparency of the text to really light
					$pdf->SetAlpha ( 0.09 );
					
					// Rotate 45 degrees and write the watermarking text
					$pdf->StartTransform ();
					$rotate_degr = isset ( $this->options ['rotate_water'] ) ? $this->options ['rotate_water'] : '45';
					$pdf->Rotate ( $rotate_degr, $myX, $myY );
					$water_font = isset ( $this->options ['water_font'] ) ? $this->options ['water_font'] : 'courier';
					$pdf->SetFont ( $water_font, "", 30 );
					$watermark_text = isset ( $this->options ['watermark_text'] ) ? $this->options ['watermark_text'] : '';
					$pdf->Text ( $myX, $myY, $watermark_text );
					$pdf->StopTransform ();
					
					// Reset the transparency to default
					$pdf->SetAlpha ( 1 );
				}
			}
			if (isset ( $this->options ['add_watermark_image'] )) {
				if (! empty ( $this->options ['background_img_url'] )) {
					$no_of_pages = $pdf->getNumPages ();
					for($i = 1; $i <= $no_of_pages; $i ++) {
						$pdf->setPage ( $i );
						
						$myPageWidth = $pdf->getPageWidth ();
						$myPageHeight = $pdf->getPageHeight ();
						$myX = ($myPageWidth / $myPageWidth) - 50; // WaterMark Positioning
						$myY = ($myPageHeight / $myPageHeight) - 40;
						$ImageT = isset ( $this->options ['water_img_t'] ) ? $this->options ['water_img_t'] : '';
						// Set the transparency of the text to really light
						$pdf->SetAlpha ( $ImageT );
						
						// Rotate 45 degrees and write the watermarking text
						$pdf->StartTransform ();
						$ImageW = isset ( $this->options ['water_img_h'] ) ? $this->options ['water_img_h'] : '';
						$ImageH = isset ( $this->options ['water_img_w'] ) ? $this->options ['water_img_w'] : '';
						
						$watermark_img = isset ( $this->options ['background_img_url'] ) ? $this->options ['background_img_url'] : '';
						$pdf->Image ( $watermark_img, $myX, $myY, $ImageW, $ImageH, '', '', '', true, 150 );
						
						$pdf->StopTransform ();
						
						// Reset the transparency to default
						$pdf->SetAlpha ( 1 );
					}
				}
			}
			// ---------------------------------------------------------
			if (! is_dir ( CACHE_DIR )) {
				mkdir ( CACHE_DIR, 0755, true );
			}
			$pdf->Output ( $filePath, 'F' );
			$to = $useremailID;
			$from = 'Wp-Advanced-pdf';
			$subject = "Here is your pdf attachment";
			$headers = "from: $from ";
			$message = 'Please download attached PDF ';
			if (wp_mail ( $to, $subject, $message, $headers = '', $attachments = array (
					$filePath 
			) )) {
				$this->export_installdata ( $to );
				$response = array( 'SENT' => true );
				wp_send_json($response);
			} else {
				$response = array( 'NOTSENT' => true );
				wp_send_json($response);
			}
		}
		
		/**
		 * generate pdf file of a post given as parameter
		 * 
		 * @param int $postID        	
		 */
		function generate_post_to_pdf_file($postID) {
			
			$logo_width = '';
			$post = get_post ( $postID );
			$content = $post->post_content;
			if (! class_exists ( 'TCPDF' )) {
				require_once PTPDF_PATH . '/tcpdf_min/tcpdf.php';
			}
			if (! class_exists ( 'pdfheader' )) {
				require_once PTPDF_PATH . '/pdfheader.php';
			}
			if (! class_exists ( 'simple_html_dom_node' )) {
				require_once PTPDF_PATH . '/simplehtmldom/simple_html_dom.php';
			}
			$post->post_content = apply_filters ( 'the_post_export_content', $post->post_content );
			$post->post_content = wpautop ( $post->post_content );
			$post->post_content = do_shortcode ( $post->post_content );
			
			if ($this->options ['ced_file_name'] == 'post_name') {
				$filePath = CACHE_DIR . '/' . $post->post_name . '.pdf';
			} else {
				$filePath = CACHE_DIR . '/' . $post->ID . '.pdf';
			}
			// new PDF document
			
			if (isset ( $this->options ['page_size'] )) {
				$pagesize = ($this->options ['page_size']);
			} else {
				$pagesize = PDF_PAGE_FORMAT;
			}
			if (isset ( $this->options ['unitmeasure'] )) {
				$unit = ($this->options ['unitmeasure']);
			} else {
				$unit = PDF_UNIT;
			}
			if (isset ( $this->options ['page_orientation'] )) {
				$orientation = ($this->options ['page_orientation']);
			} else {
				$orientation = PDF_PAGE_ORIENTATION;
			}
			$pdf = new CUSTOMPDF ( $orientation, $unit, $pagesize, true, 'UTF-8', false );
			
			if (!empty ( $this->options ['rtl_support'] )) {//die();
				// set some language dependent data:
				$lg = Array();
				$lg['a_meta_charset'] = 'UTF-8';
				$lg['a_meta_dir'] = 'rtl';
				$lg['a_meta_language'] = 'fa';
				$lg['w_page'] = 'page';
					
				// set some language-dependent strings (optional)
				$pdf->setLanguageArray($lg);
				$pdf->setRTL(true);
			}
			
			// information about doc
			$pdf->SetCreator ( 'Post to PDF plugin by CedCommerce with ' . PDF_CREATOR );
			$pdf->SetAuthor ( get_bloginfo ( 'name' ) );
			if (! empty ( $this->options ['custom_title'] )) {
				$pdf_title = $this->options ['custom_title'];
			} else {
				$pdf_title = $post->post_title;
			}
			
			//$pdf->SetTitle ( apply_filters ( 'the_post_title', $pdf_title ) );
			
			// logo width calculation
			if (isset ( $this->options ['page_header'] ) and ($this->options ['page_header']) != 'None' and !empty ( $this->options ['logo_img_url'] )) {
				
				if ($this->options ['page_header'] == "upload-image") {
					$logoImage_url = $this->options ['logo_img_url'];
				}
				$infologo = getimagesize ( $logoImage_url );
				try {
					if (isset ( $this->options ['imagefactor'] )) {
						$logo_width = @ ( int ) (($this->options ['imagefactor'] * $infologo [0]) / $infologo [1]);
					} else {
						$logo_width = @ ( int ) ((12 * $infologo [0]) / $infologo [1]);
					}
				} 
				catch(Exception $e){
				  throw new Exception("Invalid Size Image..");
				  echo "Exception:".$e->getMessage();
				}
			}
			
			if (isset ( $this->options ['page_header'] ) and ($this->options ['page_header']) == 'None') {
			
				$logoImage_url="";
				$logo_width="";
			}
			
			$blog_name = get_bloginfo ( 'name' );
			$bolg_description = get_bloginfo ( 'description' );
			$home_url = home_url ();
			$ptpdfoption_status = get_option ( PTPDF_PREFIX );
			if(isset($ptpdfoption_status)){
				$name_status=isset($ptpdfoption_status['show_site_name'])? $ptpdfoption_status['show_site_name']: '' ;
				$desc_status=isset($ptpdfoption_status['show_site_descR'])? $ptpdfoption_status['show_site_descR']: '' ;
				$url_status=isset($ptpdfoption_status['show_site_URL'])? $ptpdfoption_status['show_site_URL']: '' ;
			}
			
			// for PHP 5.4 or below set default header data
			if (version_compare ( phpversion (), '5.4.0', '<' )) {
				if ($name_status == '1' && $desc_status  == '1' && $url_status  == '1'){ 
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description . "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''),html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description. "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width  );
				}
			} elseif(version_compare ( phpversion (), '5.4.0', '>' )) {
				if ($name_status == '1' && $desc_status  == '1' && $url_status  == '1'){ 
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description . "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''),html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description. "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  == '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status == '1' && $desc_status  != '1' && $url_status  == '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
				}
				if ($name_status != '1' && $desc_status  != '1' && $url_status  != '1'){
					$pdf->SetHeaderData ( $logoImage_url, $logo_width  );
				}
			}
			
			// set header and footer fonts
			if (($this->options ['header_font_size']) > 0) {
				$header_font_size = $this->options ['header_font_size'];
			} else {
				$header_font_size = 10;
			}
			if (($this->options ['footer_font_size']) > 0) {
				$footer_font_size = $this->options ['footer_font_size'];
			} else {
				$footer_font_size = 10;
			}
			$pdf->setHeaderFont ( array (
					$this->options ['header_font_pdf'],
					'',
					$header_font_size 
			) );
			$pdf->setFooterFont ( array (
					$this->options ['footer_font_pdf'],
					'',
					$footer_font_size 
			) );
			
			if (isset($this->options ['marginLeft'])) {
				$pdf->SetLeftMargin ( $this->options ['marginLeft'] );
			} else {
				$pdf->SetLeftMargin ( PDF_MARGIN_LEFT );
			}
			
			if (isset($this->options ['marginRight'] )) {
				$pdf->SetRightMargin ( $this->options ['marginRight'] );
			} else {
				$pdf->SetRightMargin ( PDF_MARGIN_RIGHT );
			}
			
			if (isset($this->options ['marginTop'] )) {
				$pdf->SetTopMargin ( $this->options ['marginTop'] );
			} else {
				$pdf->SetTopMargin ( PDF_MARGIN_TOP );
			}
			if ((isset($this->options ['logomTop']) )) {
				$pdf->SetHeaderMargin ( $this->options ['logomTop'] );
			} else {
				$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
			}
			
			if (isset($this->options ['footer_font_margin'] )) {
				$pdf->SetFooterMargin ( $this->options ['footer_font_margin'] );
				// set auto page breaks
				$pdf->SetAutoPageBreak ( TRUE,  $this->options ['footer_font_margin']  );
			} else {
				$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );
				// set auto page breaks
				$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_FOOTER );
			}
			
			// set image scale factor
			
			if ($this->options ['imageScale'] > 0) {
				$pdf->setImageScale ( $this->options ['imageScale'] );
			} else {
				$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );
			}
			
			// set default font subsetting mode
			$pdf->setFontSubsetting ( true );
			
 			$pdf->SetFont ( $this->options ['content_font_pdf'], '', $this->options ['content_font_size'], '', true );
			
			if (! empty ( $this->options ['bullet_img_url'] )) {
				$temp = $this->options ['bullet_img_url'];
				$temp = end ( explode ( '/', $temp ) );
				$temp = end ( explode ( '.', $temp ) );
				$listsymbol = 'img|' . $temp . '|' . $this->options ['custom_image_width'] . '|' . $this->options ['custom_image_height'] . '|' . $this->options ['bullet_img_url'];
				$pdf->setLIsymbol ( $listsymbol );
			}
			// Add a page
			
			
			if ($this->options ['fontStretching']) {
				$pdf->setFontStretching($this->options ['fontStretching']);
			}
			if ($this->options ['fontSpacig']) {
				$pdf->setFontSpacing($this->options ['fontSpacig']);
			}
			$page_format = array();
			if ($this->options ['set_rotation']) {
				$page_format['Rotate'] = $this->options ['set_rotation'];
			} else {
				$page_format['Rotate'] = 0;
			}
			$pdf->AddPage($this->options ['page_orientation'], $page_format, false, false);
			$html = '';
			if (isset ( $this->options ['CustomCSS_option'] )) {
				$html = '<style>' . $this->options ['Customcss'] . '</style>';
			}
			$html .= "<body>";
			$html .= "<h1 style=\"text-align:center\">".apply_filters ( 'the_post_title', $pdf_title )."</h1>";
			if (isset ( $this->options ['authorDetail'] ) and ! $this->options ['authorDetail'] == '') {
				$author_id = $post->post_author;
				$author_meta_key = $this->options ['authorDetail'];
				$author = get_user_meta ( $author_id );
				$html .= '<p><strong>Author : </strong>' . $author [$author_meta_key] [0] . '</p>';
			}
			
			if (isset ( $this->options ['postCategories'] )) {
				$categories = get_the_category ( $post->ID );
				if ($categories) {
					$html .= '<p><strong>Categories : </strong>' . $categories [0]->cat_name . '</p>';
				}
			}
			// Display tag list is set in config
			if (isset ( $this->options ['postTags'] )) {
				$tags = get_the_tags ( $post->the_tags );
				if ($tags) {
					$html .= '<p><strong>Tagged as : </strong>';
					foreach ( $tags as $tag ) {
						$tag_link = get_tag_link ( $tag->term_id );
						$html .= '<a href="' . $tag_link . '">' . $tag->name . '</a>';
						if (next ( $tags )) {
							$html .= ', ';
						}
					}
					$html .= '</p>';
				}
			}
			// Display date if set in config
			if (isset ( $this->options ['postDate'] )) {
				$newDate = date ( "d-m-Y", strtotime ( $post->post_date ) );
				$html .= '<p><strong>Date : </strong>' . $newDate . '</p>';
			}
			
			// Set some content to print
			//$html .= '<h1>' . html_entity_decode ( $pdf_title, ENT_QUOTES ) . '</h1>';
			
			// Display feachered image if set in config on page/post
			if (isset ( $this->options ['show_feachered_image'] )) {
				if (has_post_thumbnail ( $post->ID )) {
					$html .= get_the_post_thumbnail ( $post->ID );
					
				}
			}
			$post_content = $post->post_content;
			if (empty ( $post->post_content )) {
				$post_content = isset ( $this->options ['docEntryTpl'] ) ? $this->options ['docEntryTpl'] : '';
			}
			$html .= htmlspecialchars_decode ( htmlentities ( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
			$html .="</body>";
			$dom = new simple_html_dom ();
			$dom->load ( $html );
			
			foreach ( $dom->find ( 'img' ) as $e ) {
				$exurl = ''; // external streams
				$imsize = FALSE;
				$file = $e->src;
				// check if we are passing an image as file or string
				if ($file [0] === '@') {
					// image from string
					$imgdata = substr ( $file, 1 );
				} else { // image file
					if ($file [0] === '*') {
						// image as external stream
						$file = substr ( $file, 1 );
						$exurl = $file;
					}
					// check if is local file
					if (! @file_exists ( $file )) {
						// encode spaces on filename (file is probably an URL)
						$file = str_replace ( ' ', '%20', $file );
					}
					if (@file_exists ( $file )) {
						// get image dimensions
						$imsize = @getimagesize ( $file );
					}
					if ($imsize === FALSE) {
						$imgdata = TCPDF_STATIC::fileGetContents ( $file );
					}
				}
				if (isset ( $imgdata ) and ($imgdata !== FALSE) and (strpos ( $file, '__tcpdf_img' ) === FALSE)) {
					// check Image size
					$imsize = @getimagesize ( $file );
				}
				if ($imsize === FALSE) {
					$e->outertext = '';
				} else {
					// End Image Check
					if (preg_match ( '/alignleft/i', $e->class )) {
						$imgalign = 'left';
					} elseif (preg_match ( '/alignright/i', $e->class )) {
						$imgalign = 'right';
					} elseif (preg_match ( '/aligncenter/i', $e->class )) {
						$imgalign = 'center';
						$htmlimgalign = 'middle';
					} else {
						$imgalign = 'none';
					}
					$e->class = null;
					$e->align = $imgalign;
					if (isset ( $htmlimgalign )) {
						$e->style = 'float:' . $htmlimgalign;
					} else {
						$e->style = 'float:' . $imgalign;
					}
					
					if (strtolower ( substr ( $e->src, - 4 ) ) == '.svg') {
						$e->src = null;
						if($imgalign!='none'){
							$e->outertext = '<div style="text-align:' . $imgalign . '">[ SVG: ' . $e->alt . ' ]</div><br/>';
						}
					} else {
						if($imgalign!='none'){
							$e->outertext = '<div style="text-align:' . $imgalign . '">' . $e->outertext . '</div>';
						}
					}
				}
			}
			/******parsing dom element and passing null action attribute if action attribute is not set ***/
			foreach ($dom->find ('form') as $e)
			{
				if(!isset($e->attr['action']))
				{
 					$e->action = '';
				}
			}

			$html = $dom->save ();
			$dom->clear ();
			$pdf->setFormDefaultProp ( array (
					'lineWidth' => 1,
					'borderStyle' => 'solid',
					'fillColor' => array (
							255,
							255,
							200 
					),
					'strokeColor' => array (
							255,
							128,
							128 
					) 
			) );
			
			// Print text using writeHTML
			$pdf->writeHTML ( $html, true, 0, true, 0 );
			if (isset ( $this->options ['add_watermark'] )) {
				$no_of_pages = $pdf->getNumPages ();
				for($i = 1; $i <= $no_of_pages; $i ++) {
					$pdf->setPage ( $i );
					
					// Get the page width/height
					$myPageWidth = $pdf->getPageWidth ();
					$myPageHeight = $pdf->getPageHeight ();
					
					// Find the middle of the page and adjust.
					$myX = ($myPageWidth / 2) - 75;
					$myY = ($myPageHeight / 2) + 25;
					
					// Set the transparency of the text to really light
					$pdf->SetAlpha ( 0.09 );
					
					// Rotate 45 degrees and write the watermarking text
					$pdf->StartTransform ();
					$rotate_degr = isset ( $this->options ['rotate_water'] ) ? $this->options ['rotate_water'] : '45';
					$pdf->Rotate ( $rotate_degr, $myX, $myY );
					$water_font = isset ( $this->options ['water_font'] ) ? $this->options ['water_font'] : 'courier';
					$pdf->SetFont ( $water_font, "", 30 );
					$watermark_text = isset ( $this->options ['watermark_text'] ) ? $this->options ['watermark_text'] : '';
					$pdf->Text ( $myX, $myY, $watermark_text );
					$pdf->StopTransform ();
					
					// Reset the transparency to default
					$pdf->SetAlpha ( 1 );
				}
			}
			if (isset ( $this->options ['add_watermark_image'] )) {

				if (! empty ( $this->options ['background_img_url'] )) {
					$no_of_pages = $pdf->getNumPages ();
					for($i = 1; $i <= $no_of_pages; $i ++) {
						$pdf->setPage ( $i );
				
						$myPageWidth = $pdf->getPageWidth ();
						$myPageHeight = $pdf->getPageHeight ();
						$myX = ($myPageWidth / $myPageWidth) - 50; // WaterMark Positioning
						$myY = ($myPageHeight / $myPageHeight) - 40;
						$ImageT = isset ( $this->options ['water_img_t'] ) ? $this->options ['water_img_t'] : '';
						// Set the transparency of the text to really light
						$pdf->SetAlpha ( $ImageT );
				
						// Rotate 45 degrees and write the watermarking text
						$pdf->StartTransform ();
						$ImageW = isset ( $this->options ['water_img_h'] ) ? $this->options ['water_img_h'] : '';
						$ImageH = isset ( $this->options ['water_img_w'] ) ? $this->options ['water_img_w'] : '';
				
						$watermark_img = isset ( $this->options ['background_img_url'] ) ? $this->options ['background_img_url'] : '';
						$pdf->Image ( $watermark_img, $myX, $myY, $ImageW, $ImageH, '', '', '', true, 150 );
				
						$pdf->StopTransform ();
				
						// Reset the transparency to default
						$pdf->SetAlpha ( 1 );
					}
				}
					
			}
			// ---------------------------------------------------------
			if (! is_dir ( CACHE_DIR )) {
				mkdir ( CACHE_DIR, 0755, true );
			}
			$pdf->Output ( $filePath, 'F' );
			global $current_user;
			if ($current_user) {
				global $wp_version;
				if ( $wp_version >= '4.5.0' ) 
				{
					wp_get_current_user();        
				}
				else
				{
					get_currentuserinfo ();
				}
				$this->export_installdata ( $current_user->user_email, $current_user->user_nicename );
			}
		}
		
		/**
		 * adds a link button for selected post types by admin
		 * this function adds different link for different sinerios
		 * adds a checks whether to add link for the post type or not
		 * 
		 * @param string $content        	
		 * @return string
		 */
		function ptp_add_pdf_link($content) {
			if (! isset ( $this->options ['front_end'] )) {
				return $content;
			}
			$button = $this->ptp_pdf_icon ();
			if ('beforeandafter' == $this->options ['content_placement']) {
				$content = '<div style="min-height: 30px;display: inline-block;">' . $button . '</div>' . $content . '<div style="min-height: 30px;display: inline-block;">' . $button . '</div>';
			} elseif ('after' == $this->options ['content_placement']) {
				$content = $content . '<div style="min-height: 30px;display: inline-block;">' . $button . '</div>';
			} else {
				$content = '<div style="min-height: 30px;display: inline-block;">' . $button . '</div>' . $content;
			}
			return $content;
		}
		
		/**
		 * specifies the link to attach to pdf links
		 * 
		 * @return void|boolean|string
		 */
		function ptp_pdf_icon() {
			if (! isset ( $this->options ['front_end'] )) {
				return;
			}
			
			if (isset ( $this->options ['availability'] ) and $this->options ['availability'] == 'private' and ! is_user_logged_in ()) {
				return;
			}
			if ('pdf' == (isset ( $_GET ['format'] ) ? $_GET ['format'] : null)) {
				return;
			}
			global $post;
			if (! isset ( $this->options [$post->post_type] ))
				return false;
			
			if (($this->options ['link_button'] == 'default')) {
				$linkURL = PTPDF_URL . '/asset/images/pdf.png';
			} else if (!empty ( $this->options ['custon_link_url'] )) {
				$linkURL = $this->options ['custon_link_url'];
			} else {
				$linkURL = PTPDF_URL . '/asset/images/pdf.png';
			}
			if (isset ( $this->options ['content_position'] )) {
				if ($this->options ['content_position'] == 'left') {
					$style = 'style="float: left;max-width: 50px;"';
				} else if ($this->options ['content_position'] == 'right') {
					$style = 'style="float: right;max-width: 50px;"';
				} else if ($this->options ['content_position'] == 'center') {
					$style = 'style="margin: auto;max-width: 50px;"';
				} else {
					$style = 'max-width: 50px;';
				}
			}
			global $post;
			if (is_user_logged_in ()) {
				if (! is_singular ()) {
					return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url ( add_query_arg ( 'format', 'pdf', get_permalink ( $post->ID ) ) ) . '" title="Download PDF"><img ' . $style . ' alt="Download PDF" src="' . $linkURL . '"></a>';
				} else {
					return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url ( add_query_arg ( 'format', 'pdf' ) ) . '" title="Download PDF"><img  width="50px"  ' . $style . ' alt="Download PDF" src="' . $linkURL . '"></a>';
				}
			} else {
				if (! is_singular ()) {
					$html = '<input id="' . $post->ID . '" ' . $style . ' src="' . $linkURL . '" alt="#TB_inline?height=230&amp;width=400&amp;inlineId=examplePopup1" title="Export pdf to your Email" class="thickbox export-pdf" type="image" value="Export to PDF" />';
				} else {
					$html = '<input id="' . $post->ID . '" ' . $style . ' src="' . $linkURL . '" alt="#TB_inline?height=230&amp;width=400&amp;inlineId=examplePopup1" title="Export pdf to your Email" class="thickbox export-pdf" type="image" value="Export to PDF" />';
				}
				return $html;
			}
		}
		
		/**
		 * adds custom interval for cache updation
		 * can add multiple schedules
		 * 
		 * @param array $schedules        	
		 * @return multitype:NULL Ambigous <string, mixed>
		 */
		function ptp_pdfscheduleduration($schedules) {
			if (isset ( $this->options ['cache_updation_sch'] ) && $this->options ['cache_updation_sch'] != 'none') {
				$schedules ['cacheinterval'] = array (
						'interval' => $this->options ['cache_updation_sch'],
						'display' => __ ( 'PDF Cache Schedule Interval', 'wp-advanced-pdf' ) 
				);
				update_option ( 'cron_cacheinterval', $this->options ['cache_updation_sch'] );
			}
			return $schedules;
		}
		
		/**
		 * schedule an event to update a cache if cache updation is set by admin
		 * checks if already scheduled cache updation
		 */
		function ptp_cronstarter_activation() {
			if (isset ( $this->options ['cache_updation_sch'] ) && $this->options ['cache_updation_sch'] != 'none') {
				if (! wp_next_scheduled ( 'schedulecacheupdate' )) {
					wp_schedule_event ( time (), 'cacheinterval', 'schedulecacheupdate' );
				}
			}
		}
		
		/**
		 * deactive scheduled event on plugin deactivation
		 */
		function ptp_cronstarter_deactivate() {
			// find out when the last event was scheduled
			$timestamp = wp_next_scheduled ( 'schedulecacheupdate' );
			// unschedule previous event if any
			wp_unschedule_event ( $timestamp, 'schedulecacheupdate' );
		}
		
		/**
		 * updates cache
		 * called by scheduled event
		 */
		function ptp_update_cache() {
			global $wpdb;
			$interval = get_option ( 'cron_cacheinterval_prev' );
			if (empty ( $interval )) {
				$interval = get_option ( 'cron_cacheinterval' );
				update_option ( 'cron_cacheinterval_prev', $interval );
			}
			$post_status = 'publish';
			$date_today = date ( "l" );
			$date_from = date ( "Y-m-d H:i:s", strtotime ( $date_today ) - $interval );
			$post_types = get_post_types ( array (
					'public' => true 
			), 'names' );
			$post_types_all;
			foreach ( $post_types as $post_type ) {
				if (isset ( $this->options [$post_type] ))
					$post_types_all .= "'" . $post_type . "',";
			}
			$post_types_all = trim ( $post_types_all, ',' );
			$post_ids = $wpdb->get_col ( $wpdb->prepare ( "SELECT ID FROM $wpdb->posts WHERE post_type IN ( {$post_types_all} ) AND  post_status = %s AND post_date >= %s AND post_date <= %s ", $post_status, $date_from, $date_today ) );
			foreach ( $post_ids as $post_id ) {
				$this->generate_post_to_pdf_file ( $post_id );
			}
		}
		
		/**
		 * hooked for send email to guest users
		 */
		function postajax_exportandmail_handle() {
			if (defined ( 'DOING_AJAX' ) && DOING_AJAX) {
				$ced_email_to = is_email ( sanitize_text_field ( $_POST ['ced_email_to'] ) );
				$postID = sanitize_text_field ( $_POST ['postid'] );
				if ($ced_email_to) {
					$this->ptp_email_pdf ( $postID, $ced_email_to );
				} else {
					$response = array( 'INVALIDEMAIL' => true );
					wp_send_json($response);
				}
			}
		}
		
		/**
		 * creates a table to collect record of pdf generated
		 */
		function ptp_export_install() {
			global $wpdb;
			
			$table_name = $wpdb->prefix . "ExporttoPDFRecord";
			$charset_collate = $wpdb->get_charset_collate ();
			
			$sql = "CREATE TABLE $table_name (
		   id mediumint(9) NOT NULL AUTO_INCREMENT,
		   emailid varchar(100) NOT NULL,
		   userName varchar(100) NOT NULL,
		   exportdate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		   useripaddr varchar(100) NOT NULL,
		   UNIQUE KEY id (id)
		   ) $charset_collate;";
			
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta ( $sql );
			add_option ( "Export_db_version", "1.0" );
		}
		
		/**
		 * put data in the table
		 * 
		 * @param string $emailid        	
		 * @param string $userName        	
		 */
		function export_installdata($emailid, $userName = 'guest') {
			// Add some data to table
			global $wpdb;
			$table_name = $wpdb->prefix . "ExporttoPDFRecord";
			$REMOTE_ADDR = $_SERVER ['REMOTE_ADDR'];
			if (! empty ( $_SERVER ['X_FORWARDED_FOR'] )) {
				$X_FORWARDED_FOR = explode ( ',', $_SERVER ['X_FORWARDED_FOR'] );
				if (! empty ( $X_FORWARDED_FOR )) {
					$REMOTE_ADDR = trim ( $X_FORWARDED_FOR [0] );
				}
			}			/*
			 * Some php environments will use the $_SERVER['HTTP_X_FORWARDED_FOR'] variable to capture visitor address information.
			 */
			elseif (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
				$HTTP_X_FORWARDED_FOR = explode ( ',', $_SERVER ['HTTP_X_FORWARDED_FOR'] );
				if (! empty ( $HTTP_X_FORWARDED_FOR )) {
					$REMOTE_ADDR = trim ( $HTTP_X_FORWARDED_FOR [0] );
				}
			}
			$userIPAddress = preg_replace ( '/[^0-9a-f:\., ]/si', '', $REMOTE_ADDR );
			$wpdb->insert ( $table_name, array (
					'emailid' => $emailid,
					'userName' => $userName,
					'exportdate' => current_time ( 'mysql' ),
					'useripaddr' => $userIPAddress 
			) );
		}
		
		function ptp_add_custom_font() {
			
			if ( ! current_user_can( 'manage_options' ) ) {
				return wp_send_json_error( 'You are not allow to do this.' );
			}
			
			if(isset($_FILES)) {
				if (! class_exists ( 'TCPDF' )) {
					require_once PTPDF_PATH . '/tcpdf_min/tcpdf.php';
				}
				
				$file = $_FILES[0]['tmp_name'];
				$file_name = basename($_FILES[0]['name'], ".ttf");
				if(file_exists($file)) {
					$fontname = TCPDF_FONTS::addTTFfont($file, 'TrueTypeUnicode', '', 96);
				}
				if($fontname) {
					$fonts = get_option('ptp_custom_fonts', array());
					$fonts[$file_name]=$fontname;
					update_option('ptp_custom_fonts', $fonts);
					$fonts = array($fontname, $file_name);
					wp_send_json_success( $fonts );
				}
				else {
					wp_send_json_error( 'Error occured due to wrong file you have added or there may be permission error.' );
				}
			}
		}

		function ptpdf_email_on_publish( $new_status, $old_status, $post ) {
			if ( 'publish'==$new_status && 'publish' !== $old_status )
			{
				$postID = $post->ID ;
				$content = $post->post_content;
				if (! class_exists ( 'TCPDF' )) {
					require_once PTPDF_PATH . '/tcpdf_min/tcpdf.php';
				}
				if (! class_exists ( 'pdfheader' )) {
					require_once PTPDF_PATH . '/pdfheader.php';
				}
				if (! class_exists ( 'simple_html_dom_node' )) {
					require_once PTPDF_PATH . '/simplehtmldom/simple_html_dom.php';
				}
				$post->post_content = apply_filters ( 'the_post_export_content', $post->post_content );
				$post->post_content = wpautop ( $post->post_content );
				$post->post_content = do_shortcode ( $post->post_content );
				
				if ($this->options ['ced_file_name'] == 'post_name') {
					$filePath = CACHE_DIR . '/' . $post->post_name . '.pdf';
				} else {
					$filePath = CACHE_DIR . '/' . $post->ID . '.pdf';
				}
				// new PDF document
				
				if (isset ( $this->options ['page_size'] )) {
					$pagesize = ($this->options ['page_size']);
				} else {
					$pagesize = PDF_PAGE_FORMAT;
				}
				if (isset ( $this->options ['unitmeasure'] )) {
					$unit = ($this->options ['unitmeasure']);
				} else {
					$unit = PDF_UNIT;
				}
				if (isset ( $this->options ['page_orientation'] )) {
					$orientation = ($this->options ['page_orientation']);
				} else {
					$orientation = PDF_PAGE_ORIENTATION;
				}
				$pdf = new CUSTOMPDF ( $orientation, $unit, $pagesize, true, 'UTF-8', false );
				// information about doc
				if (!empty ( $this->options ['rtl_support'] )) {//die();
					// set some language dependent data:
					$lg = Array();
					$lg['a_meta_charset'] = 'UTF-8';
					$lg['a_meta_dir'] = 'rtl';
					$lg['a_meta_language'] = 'fa';
					$lg['w_page'] = 'page';
						
					// set some language-dependent strings (optional)
					$pdf->setLanguageArray($lg);
					$pdf->setRTL(true);
				}
				$pdf->SetCreator ( 'Post to PDF plugin by CedCommerce with ' . PDF_CREATOR );
				$pdf->SetAuthor ( get_bloginfo ( 'name' ) );
				if (! empty ( $this->options ['custom_title'] )) {
					$pdf_title = $this->options ['custom_title'];
				} else {
					$pdf_title = $post->post_title;
				}
				
				//$pdf->SetTitle ( apply_filters ( 'the_post_title', $pdf_title ) );
				// logo width calculation
				if (isset ( $this->options ['page_header'] ) and ($this->options ['page_header']) == 'upload-image' and !empty ( $this->options ['logo_img_url'] )) {
					// if($this->options['page_header']=="web_icon") {
					// $logoImage_url = PTPDF_URL .'/asset/images/logo.png';
					// }
					if ($this->options ['page_header'] == "upload-image") {
						$logoImage_url = $this->options ['logo_img_url'];
					}
					$infologo = getimagesize ( $logoImage_url );
					try {
						if (isset ( $this->options ['imagefactor'] )) {
							$logo_width = @ ( int ) (($this->options ['imagefactor'] * $infologo [0]) / $infologo [1]);
						} else {
							$logo_width = @ ( int ) ((12 * $infologo [0]) / $infologo [1]);
						}
					}
					catch(Exception $e){
					  throw new Exception("Invalid Size Image..");
					  echo "Exception:".$e->getMessage();
					}
				}
				if (isset ( $this->options ['page_header'] ) and ($this->options ['page_header']) == 'None') {
				
					$logoImage_url="";
					$logo_width="";
				}
				$blog_name = get_bloginfo ( 'name' );
				$bolg_description = get_bloginfo ( 'description' );
				$home_url = home_url ();
				$ptpdfoption_status = get_option ( PTPDF_PREFIX );
				if(isset($ptpdfoption_status)){
					$name_status=isset($ptpdfoption_status['show_site_name'])? $ptpdfoption_status['show_site_name']: '' ;
					$desc_status=isset($ptpdfoption_status['show_site_descR'])? $ptpdfoption_status['show_site_descR']: '' ;
					$url_status=isset($ptpdfoption_status['show_site_URL'])? $ptpdfoption_status['show_site_URL']: '' ;
				}
				// for PHP 5.4 or below set default header data
				if (version_compare ( phpversion (), '5.4.0', '<' )) {
					if ($name_status == '1' && $desc_status  == '1' && $url_status  == '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description . "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status == '1' && $desc_status  == '1' && $url_status  != '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status == '1' && $desc_status  != '1' && $url_status  != '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status != '1' && $desc_status  != '1' && $url_status  == '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''),html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status != '1' && $desc_status  == '1' && $url_status  == '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description. "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status != '1' && $desc_status  == '1' && $url_status  != '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status == '1' && $desc_status  != '1' && $url_status  == '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status != '1' && $desc_status  != '1' && $url_status  != '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width  );
					}
				} elseif(version_compare ( phpversion (), '5.4.0', '>' )) {
					if ($name_status == '1' && $desc_status  == '1' && $url_status  == '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description . "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status == '1' && $desc_status  == '1' && $url_status  != '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status == '1' && $desc_status  != '1' && $url_status  != '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status != '1' && $desc_status  != '1' && $url_status  == '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''),html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status != '1' && $desc_status  == '1' && $url_status  == '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description. "\n" . $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status != '1' && $desc_status  == '1' && $url_status  != '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode(''), html_entity_decode ( $bolg_description, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status == '1' && $desc_status  != '1' && $url_status  == '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width, html_entity_decode ( $blog_name, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode ( $home_url, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES )  );
					}
					if ($name_status != '1' && $desc_status  != '1' && $url_status  != '1'){
						$pdf->SetHeaderData ( $logoImage_url, $logo_width  );
					}
				}
					
				// set header and footer fonts
				if (($this->options ['header_font_size']) > 0) {
					$header_font_size = $this->options ['header_font_size'];
				} else {
					$header_font_size = 10;
				}
				if (($this->options ['footer_font_size']) > 0) {
					$footer_font_size = $this->options ['footer_font_size'];
				} else {
					$footer_font_size = 10;
				}
				$pdf->setHeaderFont ( array (
						$this->options ['header_font_pdf'],
						'',
						$header_font_size 
				) );
				$pdf->setFooterFont ( array (
						$this->options ['footer_font_pdf'],
						'',
						$footer_font_size 
				) );
				
				$pdf->SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );
				
				if (isset($this->options ['marginLeft']) ) {
					$pdf->SetLeftMargin ( $this->options ['marginLeft'] );
				} else {
					$pdf->SetLeftMargin ( PDF_MARGIN_LEFT );
				}
				
				if (isset($this->options ['marginRight'] )) {
					$pdf->SetRightMargin ( $this->options ['marginRight'] );
				} else {
					$pdf->SetRightMargin ( PDF_MARGIN_RIGHT );
				}
				
				if (isset($this->options ['marginTop'] )) {
					$pdf->SetTopMargin ( $this->options ['marginTop'] );
				} else {
					$pdf->SetTopMargin ( PDF_MARGIN_TOP );
				}
				if (isset($this->options ['logomTop'])) {
					$pdf->SetHeaderMargin ( $this->options ['logomTop'] );
				} else {
					$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
				}
				
				if (isset($this->options ['footer_font_margin'] )) {
					$pdf->SetFooterMargin ( $this->options ['footer_font_margin'] );
					// set auto page breaks
					$pdf->SetAutoPageBreak ( TRUE,  $this->options ['footer_font_margin']  );
				} else {
					$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );
					// set auto page breaks
					$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_FOOTER );
				}
				
				if ($this->options ['imageScale'] > 0) {
					$pdf->setImageScale ( $this->options ['imageScale'] );
				} else {
					$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );
				}
				
				// set default font subsetting mode
				$pdf->setFontSubsetting ( true );
				
				$pdf->SetFont ( $this->options ['content_font_pdf'], '', $this->options ['content_font_size'], '', true );
				
				if (! empty ( $this->options ['bullet_img_url'] )) {
					$temp = $this->options ['bullet_img_url'];
					$temp = end ( explode ( '/', $temp ) );
					$temp = end ( explode ( '.', $temp ) );
					$listsymbol = 'img|' . $temp . '|' . $this->options ['custom_image_width'] . '|' . $this->options ['custom_image_height'] . '|' . $this->options ['bullet_img_url'];
					$pdf->setLIsymbol ( $listsymbol );
				}
				
				// Add a page
				// This method has several options, check the source code documentation for more information.
	// 			$pdf->AddPage ();
				
				if ($this->options ['fontStretching']) {
					$pdf->setFontStretching($this->options ['fontStretching']);
				}
				if ($this->options ['fontSpacig']) {
					$pdf->setFontSpacing($this->options ['fontSpacig']);
				}
				$page_format = array();
				if ($this->options ['set_rotation']) {
					$page_format['Rotate'] = $this->options ['set_rotation'];
				} else {
					$page_format['Rotate'] = 0;
				}
				$pdf->AddPage($this->options ['page_orientation'], $page_format, false, false);
				$html = '';
				if (isset ( $this->options ['CustomCSS_option'] )) {
					$html = '<style>' . $this->options ['Customcss'] . '</style>';
				}
				$html .= "<body>";
				$html .= "<h1 style=\"text-align:center\">".apply_filters ( 'the_post_title', $pdf_title )."</h1>";
				
				if (isset ( $this->options ['authorDetail'] ) and ! $this->options ['authorDetail'] == '') {
					$author_id = $post->post_author;
					$author_meta_key = $this->options ['authorDetail'];
					$author = get_user_meta ( $author_id );
					$html .= '<p><strong>Author : </strong>' . $author [$author_meta_key] [0] . '</p>';
				}
				
				if (isset ( $this->options ['postCategories'] )) {
					$categories = get_the_category ( $post->ID );
					if ($categories) {
						$html .= '<p><strong>Categories : </strong>' . $categories [0]->cat_name . '</p>';
					}
				}
				// Display tag list is set in config
				if (isset ( $this->options ['postTags'] )) {
					$tags = get_the_tags ( $post->the_tags );
					if ($tags) {
						$html .= '<p><strong>Tagged as : </strong>';
						foreach ( $tags as $tag ) {
							$tag_link = get_tag_link ( $tag->term_id );
							$html .= '<a href="' . $tag_link . '">' . $tag->name . '</a>';
							if (next ( $tags )) {
								$html .= ', ';
							}
						}
						$html .= '</p>';
					}
				}
				// Display date if set in config
				if (isset ( $this->options ['postDate'] )) {
					$newDate = date ( "d-m-Y", strtotime ( $post->post_date ) );
					$html .= '<p><strong>Date : </strong>' . $newDate . '</p>';
				}
				
				// Set some content to print
				//$html .= '<h1>' . html_entity_decode ( $pdf_title, ENT_QUOTES ) . '</h1>';
				
				// Display feachered image if set in config on page/post
				if (isset ( $this->options ['show_feachered_image'] )) {
					if (has_post_thumbnail ( $post->ID )) {
						$html .= get_the_post_thumbnail ( $post->ID );
					}
				}
				$post_content = $post->post_content;
				if (empty ( $post->post_content )) {
					$post_content = isset ( $this->options ['docEntryTpl'] ) ? $this->options ['docEntryTpl'] : '';
				}
				$html .= htmlspecialchars_decode ( htmlentities ( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
				$html .="</body>";
				$dom = new simple_html_dom ();
				$dom->load ( $html );
				
				foreach ( $dom->find ( 'img' ) as $e ) {
					$exurl = ''; // external streams
					$imsize = FALSE;
					$file = $e->src;
					// check if we are passing an image as file or string
					if ($file [0] === '@') {
						// image from string
						$imgdata = substr ( $file, 1 );
					} else { // image file
						if ($file [0] === '*') {
							// image as external stream
							$file = substr ( $file, 1 );
							$exurl = $file;
						}
						// check if is local file
						if (! @file_exists ( $file )) {
							// encode spaces on filename (file is probably an URL)
							$file = str_replace ( ' ', '%20', $file );
						}
						if (@file_exists ( $file )) {
							// get image dimensions
							$imsize = @getimagesize ( $file );
						}
						if ($imsize === FALSE) {
							$imgdata = TCPDF_STATIC::fileGetContents ( $file );
						}
					}
					if (isset ( $imgdata ) and ($imgdata !== FALSE) and (strpos ( $file, '__tcpdf_img' ) === FALSE)) {
						// check Image size
						$imsize = @getimagesize ( $file );
					}
					if ($imsize === FALSE) {
						$e->outertext = '';
					} else {
						// End Image Check
						if (preg_match ( '/alignleft/i', $e->class )) {
							$imgalign = 'left';
						} elseif (preg_match ( '/alignright/i', $e->class )) {
							$imgalign = 'right';
						} elseif (preg_match ( '/aligncenter/i', $e->class )) {
							$imgalign = 'center';
							$htmlimgalign = 'middle';
						} else {
							$imgalign = 'none';
						}
						
						$e->class = null;
						$e->align = $imgalign;
						if (isset ( $htmlimgalign )) {
							$e->style = 'float:' . $htmlimgalign;
						} else {
							$e->style = 'float:' . $imgalign;
						}
						
						if (strtolower ( substr ( $e->src, - 4 ) ) == '.svg') {
							$e->src = null;
							if($imgalign!='none'){
								$e->outertext = '<div style="text-align:' . $imgalign . '">[ SVG: ' . $e->alt . ' ]</div><br/>';
							}
						} else {
							if($imgalign!='none'){
								$e->outertext = '<div style="text-align:' . $imgalign . '">' . $e->outertext . '</div>';
							}
						}
					}
				}
				$html = $dom->save ();
				$dom->clear ();
				$pdf->setFormDefaultProp ( array (
						'lineWidth' => 1,
						'borderStyle' => 'solid',
						'fillColor' => array (
								255,
								255,
								200 
						),
						'strokeColor' => array (
								255,
								128,
								128 
						) 
				) );
				// Print text using writeHTML
				$pdf->writeHTML ( $html, true, 0, true, 0 );
				if (isset ( $this->options ['add_watermark'] )) {
					$no_of_pages = $pdf->getNumPages ();
					for($i = 1; $i <= $no_of_pages; $i ++) {
						$pdf->setPage ( $i );
						
						// Get the page width/height
						$myPageWidth = $pdf->getPageWidth ();
						$myPageHeight = $pdf->getPageHeight ();
						
						// Find the middle of the page and adjust.
						$myX = ($myPageWidth / 2) - 75;
						$myY = ($myPageHeight / 2) + 25;
						
						// Set the transparency of the text to really light
						$pdf->SetAlpha ( 0.09 );
						
						// Rotate 45 degrees and write the watermarking text
						$pdf->StartTransform ();
						$rotate_degr = isset ( $this->options ['rotate_water'] ) ? $this->options ['rotate_water'] : '45';
						$pdf->Rotate ( $rotate_degr, $myX, $myY );
						$water_font = isset ( $this->options ['water_font'] ) ? $this->options ['water_font'] : 'courier';
						$pdf->SetFont ( $water_font, "", 30 );
						$watermark_text = isset ( $this->options ['watermark_text'] ) ? $this->options ['watermark_text'] : '';
						$pdf->Text ( $myX, $myY, $watermark_text );
						$pdf->StopTransform ();
						
						// Reset the transparency to default
						$pdf->SetAlpha ( 1 );
					}
				}
				if (isset ( $this->options ['add_watermark_image'] )) {
					if (! empty ( $this->options ['background_img_url'] )) {
						$no_of_pages = $pdf->getNumPages ();
						for($i = 1; $i <= $no_of_pages; $i ++) {
							$pdf->setPage ( $i );
							
							$myPageWidth = $pdf->getPageWidth ();
							$myPageHeight = $pdf->getPageHeight ();
							$myX = ($myPageWidth / $myPageWidth) - 50; // WaterMark Positioning
							$myY = ($myPageHeight / $myPageHeight) - 40;
							$ImageT = isset ( $this->options ['water_img_t'] ) ? $this->options ['water_img_t'] : '';
							// Set the transparency of the text to really light
							$pdf->SetAlpha ( $ImageT );
							
							// Rotate 45 degrees and write the watermarking text
							$pdf->StartTransform ();
							$ImageW = isset ( $this->options ['water_img_h'] ) ? $this->options ['water_img_h'] : '';
							$ImageH = isset ( $this->options ['water_img_w'] ) ? $this->options ['water_img_w'] : '';
							
							$watermark_img = isset ( $this->options ['background_img_url'] ) ? $this->options ['background_img_url'] : '';
							$pdf->Image ( $watermark_img, $myX, $myY, $ImageW, $ImageH, '', '', '', true, 150 );
							
							$pdf->StopTransform ();
							
							// Reset the transparency to default
							$pdf->SetAlpha ( 1 );
						}
					}
				}
				// ---------------------------------------------------------
				if (! is_dir ( CACHE_DIR )) {
					mkdir ( CACHE_DIR, 0755, true );
				}
		    	if( isset( $this->options[ 'postPublishs' ] ) ) {
			    	foreach ( $this->options[ 'postPublish' ] as $key => $value ) {
				    	$args=array(
				    		'role' => $value
				    		);
				    	$query = new WP_User_Query($args); 
				    	$authors = $query->get_results();
				    	if(!empty($authors))
				    	{
				    		$pdf->Output ( $filePath, 'F' );
							$from = 'Wp-Advanced-pdf';
							$subject = "Here is your pdf attachment";
							$headers = "from: $from ";
							$message = 'Please download attached PDF ';
				    		foreach ($authors as $author)
				    		{
				    			$author_info = get_userdata($author->ID);
				    			$to = $author_info->user_email;
								if (wp_mail ( $to, $subject, $message, $headers = '', $attachments = array (
										$filePath 
								) ))
								{
									$this->export_installdata ( $to );
									
									$_SESSION['mail'] = 1;
									$response = array( 'SENT' => true );
								}
								else
								{
									$_SESSION['notmail'] = 1;
									$response = array( 'NOTSENT' => true );								
								}
				    		}
				    	}
				    }
				}
			}
		}
	}
	$wpppdf = new wpppdf ();
}
?>
