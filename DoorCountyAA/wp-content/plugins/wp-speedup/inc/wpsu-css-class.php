<?php if ( ! defined( 'ABSPATH' ) ) exit; 

class WPSUCSS {

	/**
	*Variables
	*/
	const nspace = 'wpsu-css';
	const pname = 'SPEEDUP CSS';
	const version = 2.0;

	protected $_plugin_file;
	protected $_plugin_dir;
	protected $_plugin_path;
	protected $_plugin_url;

	var $doc_root = '';
	var $cachetime = '';
	var $upload_path = '';
	var $upload_uri = '';
	var $tmp_dir = '';

	var $css_domain = '';
	var $css_path = '';
	var $css_uri = '';
	var $css_path_tmp = '';
	var $css_token = '';
	var $css_settings_path = '';

	var $settings_fields = array();
	var $settings_data = array();
	var $css_files_ignore = array( 'admin-bar.min.css', 'admin-bar.css', 'login.css', 'colors-fresh.css', 'wp-admin.css', 'dashicons.min.css' );
	var $css_handles_found = array();
	var $debug = false;

	/**
	*Constructor
	*
	*@return void
	*@since 0.1
	*/
	function __construct() {}

	/**
	*Init function
	*
	*@return void
	*@since 0.1
	*/
	function init() {

		// if delete css button is clicked, delete cache

		if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'deletecsscache' && isset( $_GET[ '_wpnonce' ] ) ) {
			add_action( 'admin_init', array( &$this, 'admin_bar_delete_cache' ) );
		}

		// set doc root

		$this->doc_root = ABSPATH;


		// add delete css cache button

		//add_action( 'wp_before_admin_bar_render', array( &$this, 'delete_cache_button' ) );

		// settings fields

		$this->settings_fields = array(
						'css_domain' => array(
								'label' => 'CSS Domain',
								'type' => 'text',
								'default' => get_option( 'siteurl' )
								),
						'cachetime' => array(
								'label' => 'Cache Expiration',
								'instruction' => 'How often to refresh CSS files in seconds.',
								'type' => 'select',
								'values' => array( '60' => '1 minute', '300' => '5 minutes', '900' => '15 minutes', '1800' => '30 minutes', '3600' => '1 hour' ),
								'default' => '300'
								),
						'htaccess_user_pw' => array(
								'label' => 'Username and Password',
								'instruction' => 'Use when site is accessed behind htaccess authentication -- syntax: username:password.',
								'type' => 'text',
								'default' => ''
								),
						'add_gf_css' => array(
								'label' => 'Add Gravity Forms CSS',
								'type' => 'select',
								'values' => array( 'No' => 'No', 'Yes' => 'Yes' ),
								'default' => 'No'
								),
						'ignore_files' => array(
								'label' => 'CSS Files to Ignore',
								'instruction' => 'Enter one per line. Only use the name of the CSS file (like style.css). You can be more specific about what CSS file to ignore by specifying the plugin and then the CSS file (like plugin:style.css).',
								'type' => 'textarea'
								),
						'compress' => array(
								'label' => 'GZIP Compress CSS output?',
								'type' => 'select',
								'values' => array( 'No' => 'No', 'Yes' => 'Yes' ),
								'default' => 'Yes'
								),
						'compress_html' => array(
                                'label' => 'GZIP Compress HTML output?',
                                'type' => 'select',
                                'values' => array( 'No' => 'No', 'Yes' => 'Yes' ),
                                'default' => 'No'
                                ),
						'debug' => array(
								'label' => 'Turn on debugging?',
								'type' => 'select',
								'values' => array( 'No' => 'No', 'Yes' => 'Yes' ),
								'default' => 'No'
								)
					);

		// settings data

		$this->settings_data = unserialize( get_option( self::nspace . '-settings' ) );
		if ( ! $this->settings_data ) $this->settings_data = array();
		foreach ( $this->settings_fields as $key => $val ) {
			if ( ! isset( $this->settings_data[$key] ) ) $this->settings_data[$key] = '';
		}
		$this->cachetime = $this->get_settings_value( 'cachetime' );
		if ( ! @strlen( $this->cachetime ) ) $this->cachetime = 300;
		$this->css_domain = $this->get_settings_value( 'css_domain' );
		if ( ! @strlen( $this->css_domain ) ) $this->css_domain = get_option( 'siteurl' );
		if ( $this->settings_data['debug'] == 'Yes' ) $this->debug = true;
		if ( ! $this->settings_data['compress'] ) $this->settings_data['compress'] = 'Yes';
		if ( ! $this->settings_data['compress_html'] ) $this->settings_data['compress_html'] = 'No';

		// turn on output compression

		if ( $this->settings_data['compress_html'] == 'Yes' ) ob_start( 'ob_gzhandler' );

		// check upload dirs
		
		$this->check_upload_dirs();

		if ( is_admin() ) {

			// add admin settings page

			add_action( 'admin_menu', array( &$this, 'add_settings_page' ), 30 );
		}
		elseif ( strstr( $_SERVER['REQUEST_URI'], 'wp-login' ) || strstr( $_SERVER['REQUEST_URI'], 'gf_page=' ) || strstr( $_SERVER['REQUEST_URI'], 'preview=' ) ) {}
		elseif ( ! file_exists( $this->css_settings_path ) ) {}
		elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {}
		else {

			// add ignore files

			$ignore_list = preg_split( "/\r\n|\n|\r/", $this->settings_data['ignore_files'] );
			foreach ( $ignore_list as $item ) $this->css_files_ignore[] = trim( $item );
			$this->debug( 'Ignore list: ' . implode( ', ', $this->css_files_ignore ) );

			// add gravity forms css, if told to

			if ( $this->settings_data['add_gf_css'] == 'Yes' ) {
				$gf_src = $this->strip_domain( plugins_url( 'css/forms.css', 'gravityforms' ) );
				$gf_file = $this->get_file_from_src( $gf_src );
				$this->css_handles_found[$gf_src]['css_file'] = $gf_file;
			}

			// gather and SPEEDUP CSS function calls

			add_filter( 'print_styles_array', array( $this, 'gather_css' ) );
			add_filter( 'wp_head', array( $this, 'combine_css' ), 999999999 );

			// get rid of browser prefetching of next page from link rel="next" tag

			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
			remove_action( 'wp_head', 'adjacent_posts_rel_link' );

		}
	}

	/**
	*Check upload dirs
	*
	*@return void
	*@since 0.4
	*/
	function check_upload_dirs() {
		
		// make sure upload dirs exist and set file path and uri
		
		$upload_dir = wp_upload_dir();
		if ( ! file_exists( $upload_dir['basedir'] ) ) mkdir ( $upload_dir['basedir'] );
		$this->upload_path = $upload_dir['basedir'] . '/' . self::nspace . '/';
		$this->upload_uri = $upload_dir['baseurl'] . '/' . self::nspace . '/';
		if ( ! file_exists( $this->upload_path ) ) mkdir ( $this->upload_path );

		// create tmp directory

		$this->create_tmp_dir();

		// write settings to temp file so that css script has access to WP settings without having to load WP infrastructure

		$domain = $_SERVER['HTTP_HOST'];
		if ( function_exists( 'get_current_site' ) ) {
			$site = get_current_site();
			$domain = $site->domain;
		}
		$this->css_settings_path = $this->tmp_dir . $domain . '-settings.dat';
		if ( $this->cache_expired( $this->css_settings_path, false ) ) {
			$args = array( 'upload_path' => $this->upload_path, 'compress' => $this->settings_data['compress'] );
			$this->write_file( $this->css_settings_path, serialize( $args ) );
		}
	}

	/**
	*Create tmp dir
	*
	*@return void
	*@since 0.4
	*/
	function create_tmp_dir() {
		$this->tmp_dir = $this->get_plugin_path() . '/tmp/';
		if ( ! is_writable( dirname( $this->tmp_dir ) ) ) {
			$this->tmp_dir = sys_get_temp_dir() . '/';
			if ( ! file_exists( $this->tmp_dir ) ) wp_mkdir_p( $this->tmp_dir );
			$this->tmp_dir .= self::nspace . '/';
		}
		if ( ! file_exists( $this->tmp_dir ) ) wp_mkdir_p( $this->tmp_dir );
	}

	
	

	/**
	*Debug function
	*
	*@return void
	*@since 0.1
	*/
	function debug ( $msg ) {
		if ( $this->debug ) error_log( 'DEBUG: ' . $msg );
	}

	/**
	*Cache expired?
	*
	*@return boolean
	*@since 0.1
	*/
	function cache_expired ( $path, $debug = true ) {
		$mtime = 0;
		if( file_exists( $path ) && filesize( $path ) ) $mtime = @filemtime( $path );
		if ( ( time() - $mtime ) > $this->cachetime ) {
			if ( $debug ) $this->debug( 'Cache expired (' . $path . ')' );
			if ( $debug ) $this->debug( 'Time since (' . ( time() - $mtime ) . ') and Cache Time (' . $this->cachetime . ')' );
			return true;
		}
		if ( $debug ) $this->debug( 'Using cache (' . $path . ')' );
		return false;
	}

	/**
	*Gather CSS
	*
	*@return void
	*@since 0.1
	*/
	function gather_css ( $to_do ) {

		if ( empty( $to_do ) ) return $to_do;

		global $wp_styles;
		foreach ( $to_do as $key => $handle ) {
			$css_src = $this->strip_domain( $wp_styles->registered[$handle]->src );
			$css_file = $this->get_file_from_src( $css_src );

			// get context (plugin or theme)

			$context = $this->get_context( $css_src );

			// don't include css that we are to ignore

			if( ! in_array( $context . ':' . $css_file, $this->css_files_ignore ) && ! in_array( $css_file, $this->css_files_ignore )
				&& @strlen( $css_src ) && $this->file_exists( $css_src ) ) {
				$msg = 'CSS context & file found (' . $context . ':' . $css_file;
				if ( ! $context ) $msg = 'CSS file found (' . $css_file;
				$msg .= ')';
				$this->debug( $msg );
				$this->css_handles_found[$handle] = $css_src;
				unset( $to_do[$key] );
			}
		}

		if ( array_keys( $this->css_handles_found ) ) {

			// loop through and unset styles

			foreach ( $to_do as $key => $handle ) {
				$css_src = $this->strip_domain( $wp_styles->registered[$handle]->src );
				$css_file = $this->get_file_from_src( $css_src );
				$context = $this->get_context( $css_src );
				if( ! in_array( $context . ':' . $css_file, $this->css_files_ignore ) &&
					! in_array( $css_file, $this->css_files_ignore ) && $this->file_exists( $css_src ) ) {
					wp_deregister_style( $handle );
				}
			}
			foreach ( $wp_styles->queue as $key => $handle ) {
				if ( isset( $this->css_handles_found[$handle] ) ) {
					unset( $wp_styles->queue[$key] );
				}
			}
		}
		return $to_do;
	}

	/**
	*Set paths
	*
	*@return void
	*@since 1.5
	*/
	function set_paths () {

		// get name of file (token) based on md5 hash of css handles

		$this->css_token = md5( @implode( '', array_keys( $this->css_handles_found ) ) );

		// paths to css

		$this->css_path = $this->upload_path . $this->css_token . '.css';
		$this->css_uri = $this->upload_uri . $this->css_token . '.css';
		$this->css_path_tmp = $this->css_path . '.tmp';
	}

	/**
	*Get context function
	*
	*@return string
	*@since 1.5
	*/
	function get_context( $css_src = '' ) {
		preg_match( "/(plugins|themes)\/(.*)\/.*/", $css_src, $jmatches );
		$context = '';
		if ( $jmatches ) {
			$context = $jmatches[2];
			$context_list = explode( '/', $context );
			if ( $context_list ) $context = $context_list[0];
		}
		return $context;
	}

	/**
	*SPEEDUP CSS
	*
	*@return void
	*@since 0.1
	*/
	function combine_css () {

		// if no header handles found, return

		if ( ! @count( @array_keys( $this->css_handles_found ) ) ) return;

		$this->set_paths();

		$content = '';
		if ( $this->cache_expired( $this->css_path ) ) {

			// loop through css handles

			foreach ( $this->css_handles_found as $handle => $css_src ) {
				$css_file = $this->get_file_from_src( $css_src );
				if( $this->file_exists( $css_src ) ) {
					$css_content = '';

					// if file is a PHP script, pull content via curl

					if ( preg_match( "/\.php/", $css_src ) ) {
						$css_content = $this->curl_file_get_contents ( $css_src );
					}
					else $css_content = file_get_contents( $this->doc_root . $css_src );
					
					// change path to images

					foreach ( array( 'images', 'fonts', 'assets', 'img' ) as $folder ) {
					$css_content = str_replace(
									array( 
										'url(' . $folder . '/',
										"url('" . $folder . "/",
										'url("' . $folder . '/',
										'url(../' . $folder . '/',
										"url('../" . $folder . "/",
										'url("../' . $folder . '/'
									),
									array( 
										'url(' . dirname( $css_src ) . '/' . $folder . '/',
										'url(\'' . dirname( $css_src ) . '/' . $folder . '/',
										'url("' . dirname( $css_src ) . '/' . $folder . '/',
										'url(' . dirname( dirname( $css_src ) ) . '/' . $folder . '/',
										'url(\'' . dirname( dirname( $css_src ) ) . '/' . $folder . '/',
										'url("' . dirname( dirname( $css_src ) ) . '/' . $folder . '/'
									), 
									$css_content
								);
					}
					$content .= $this->compress( $css_content );
					unset( $css_content );
				}
			}
			$this->cache( $content );
			$this->debug( 'Create combined file (' . $this->css_path . ')' );
			@rename( $this->css_path_tmp, $this->css_path );
		}

		if ( file_exists ( $this->css_path ) ) {
			echo "\t\t<link rel='stylesheet' id='wpsu-css' href='" . str_replace( get_option( 'siteurl' ), $this->css_domain, $this->get_plugin_url() . 'speedup_css.php?token=' . $this->css_token . '&#038;ver=' . self::version ) . "' type='text/css' media='all' />\n";
		}
		return $to_do;
	}

	/**
	*File exists
	*
	*@return boolean
	*@since 0.1
	*/
	function file_exists( $src ) {
		if ( @strlen( $src ) && file_exists( $this->doc_root . $src ) ) return true;
		return false;
	}

	/**
	*Get file from src
	*
	*@return string
	*@since 0.1
	*/
	function get_file_from_src( $src ) {
		$frags = array();
		if ( @strlen( $src ) ) {
			$frags = explode( '/', $src );
			return $frags[count( $frags ) - 1];
		}
	}

	/**
	*cache data
	*
	*@return void
	*@since 0.1
	*/
	function cache( $content ) {
		if ( ! file_exists( $this->css_path_tmp ) ) $this->write_file( $this->css_path_tmp, $content );
	}

	/**
	*Write file
	*
	*@return void
	*@since 0.4
	*/
	function write_file ( $file, $content ) {
		if ( is_writable ( dirname( $file ) ) ) {
			$this->debug( 'Write: ' . $file );
			$fp = fopen( $file, "w" );
			if ( flock( $fp, LOCK_EX, $wouldblock ) ) { // do an exclusive lock
				fwrite( $fp, $content );
				flock( $fp, LOCK_UN, $wouldblock ); // release the lock
			}
			fclose( $fp );
		}
	}

	/**
	*Get content via curl
	*
	*@return string
	*@since 0.1
	*/
	function curl_file_get_contents( $src ) {
		$url = trim( $src );
		$url = preg_replace( "/http(|s):\/\//", "http://" . $this->get_settings_value( 'htaccess_user_pw' ) . "@", $url );
		$c = curl_init();
		curl_setopt( $c, CURLOPT_URL, $url );
		curl_setopt( $c, CURLOPT_FAILONERROR, false );
		curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $c, CURLOPT_VERBOSE, false );
		curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $c, CURLOPT_SSL_VERIFYHOST, false );
		if( count( $header ) ) curl_setopt( $c, CURLOPT_HTTPHEADER, $header );
		$contents = curl_exec( $c );
		curl_close( $c );
		return $contents;
	}

	/**
	*Strip domain from path
	*
	*@return string
	*@since 0.1
	*/
	function strip_domain( $src ) {
		if ( strpos( $src, 'http://' ) === false && strpos( $src, 'https://' ) === false ) return $src;
		$src = str_replace( array( 'http://', 'https://' ), array( '', '' ), $src );
		$frags = explode( '/', $src );
		array_shift( $frags );
		$src = implode( '/', $frags );
		if ( substr( $src, 0, 1 ) !== '/' ) $src = '/' . $src;
		return $src;
	}

	/**
	*Compress 
	*
	*@return string
	*@since 0.1
	*/
	function compress( $content ) {
		return $this->minify( $content );
	}

	/**
	*Minify
	*
	*@return string
	*@since 0.2
	*/
	function minify( $content ) {
		$items = array(
				'#/\*.*?\*/#s' => '',
				'/(\t|\r|\n)/' => '',
				'/[^}{]+{\s?}/' => '',
				'/\s*{\s*/' => '{',
				'/;}/' => '}',
				'/, /' => ',',
				'/; /' => ';',
				'/: /' => ':',
				'/\s+/' => ' ',
				);
		foreach( $items as $search => $replace ) $content = preg_replace( $search, $replace, $content );
		return $content;
	}

	/**
	*Add settings page
	*
	*@return void
	*@since 0.1
	*/
	function add_settings_page() {
		if ( current_user_can( 'manage_options' ) ){
			// add_options_page( self::pname, self::pname, 'manage_options', 'wpsu-css-settings', array( &$this, 'settings_page' ) );
		}
	}

	/**
	*Settings page
	*
	*@return void
	*@since 0.1
	*/
	function settings_page() {
		if ( isset( $_POST['wpsu-css_update_settings'] ) ) $this->update_settings();
		$this->show_settings_form();
	}

	/**
	*Show settings form
	*
	*@return void
	*@since 0.1
	*/
	function show_settings_form () {
		include( $this->get_plugin_path() . '/css.php' );
	}

	/**
	*Get single value from unserialized data
	*
	*@return string
	*@since 0.1
	*/
	function get_settings_value( $key = '' ) {
		if ( isset( $this->settings_data[$key] ) ) return $this->settings_data[$key];
	}

	/**
	*Remove option when plugin is deactivated
	*
	*@return void
	*@since 0.1
	*/
	function delete_settings() {
		delete_option( $this->option_key );
	}

	/**
	*Is associative array function
	*
	*@return string
	*@since 0.1
	*/
	function is_assoc( $arr ) {
		if ( isset ( $arr[0] ) ) return false;
		return true;
	}

	/**
	*Display a select form element
	*
	*@return string
	*@since 0.1
	*/
	function select_field( $name, $values, $value, $use_label = false, $default_value = '', $custom_label = '' ) {
		ob_start();
		$label = '-- please make a selection --';
		if ( @strlen( $custom_label ) ) {
			$label = $custom_label;
		}

		// convert indexed array into associative

		if ( ! $this->is_assoc( $values ) ) {
			$tmp_values = $values;
			$values = array();
			foreach ( $tmp_values as $tmp_value ) {
				$values[$tmp_value] = $tmp_value;
			}
		}
?>
	<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
		<?php if ( $use_label ): ?>
		<option value=""><?php echo $label; ?></option>

		<?php endif; ?>
		<?php foreach ( $values as $val => $label ) : ?>
			<option value="<?php echo $val; ?>"<?php if ($value == $val || ( $default_value == $val && @strlen( $default_value ) && ! @strlen( $value ) ) ) : ?> selected="selected"<?php endif; ?>><?php echo $label; ?></option>
		<?php endforeach; ?>
	</select>
<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	*Update settings form
	*
	*@return void
	*@since 0.1
	*/
	function update_settings() {
		$data = array();
		foreach( $this->settings_fields as $key => $val ) if( $val['type'] != 'legend' ) $data[$key] = $_POST[$key];
		$this->set_settings( $data );
		$this->delete_cache();
		$this->check_upload_dirs();
	}

	/**
	*Update serialized array option
	*
	*@return void
	*@since 0.1
	*/
	function set_settings( $data ) {
		$data = sanitize_spsu_data($data);
		update_option( self::nspace . '-settings', serialize( $data ) );
		$this->settings_data = $data;
	}

	/**
	*Delete cache
	*
	*@return void
	*@since 0.1
	*/
	function delete_cache() {
		$files = glob( $this->upload_path . '*.css' );
		if ( is_array( $files ) ) array_map( "unlink", $files );
		@unlink( $this->css_settings_path );
		if ( function_exists( 'wp_cache_clear_cache' ) ) wp_cache_clear_cache();
	}

	/**
	*Set plugin file
	*
	*@return void
	*@since 0.1
	*/
	function set_plugin_file( $plugin_file ) {
		$this->_plugin_file = $plugin_file;
	}

	/**
	*Get plugin file
	*
	*@return string
	*@since 0.1
	*/
	function get_plugin_file() {
		return $this->_plugin_file;
	}

	/**
	*Set plugin directory
	*
	*@return void
	*@since 0.1
	*/
	function set_plugin_dir( $plugin_dir ) {
		$this->_plugin_dir = $plugin_dir;
	}

	/**
	*Get plugin directory
	*
	*@return string
	*@since 0.1
	*/
	function get_plugin_dir() {
		return $this->_plugin_dir;
	}

	/**
	*Set plugin file path
	*
	*@return void
	*@since 0.1
	*/
	function set_plugin_path( $plugin_path ) {
		$this->_plugin_path = $plugin_path;
	}

	/**
	*Get plugin file path
	*
	*@return string
	*@since 0.1
	*/
	function get_plugin_path() {
		return $this->_plugin_path;
	}

	/**
	*Set plugin URL
	*
	*@return void
	*@since 0.1
	*/
	function set_plugin_url( $plugin_url ) {
		$this->_plugin_url = $plugin_url;
	}

	/**
	*Get plugin URL
	*
	*@return string
	*@since 0.1
	*/
	function get_plugin_url() {
		return $this->_plugin_url;
	}

	/**
	*Delete cache button
	*
	*@return void
	*@since 0.8
	*/
	function delete_cache_button() {
		global $wp_admin_bar;
		if ( ! is_user_logged_in() ) return false;
		if ( function_exists( 'current_user_can' ) && false == current_user_can( 'delete_others_posts' ) ) return false;
		$wp_admin_bar->add_menu(
								array(
										'parent' => '',
										'id' => 'delete-css-cache',
										'title' => __( 'Delete CSS Cache', 'wp-speedup'),
										'meta' => array( 'title' => __( 'Remove CSS Cache', 'wp-speedup'), self::nspace  ),
										'href' => wp_nonce_url( admin_url( 'index.php?action=deletecsscache&path=' . urlencode( $_SERVER[ 'REQUEST_URI' ] ) ), 'delete-css-cache' )
										) 
								);
	}

	/**
	*Admin bar delete cache
	*
	*@return void
	*@since 0.8
	*/
	function admin_bar_delete_cache() {
		if ( function_exists( 'current_user_can' ) && false == current_user_can( 'delete_others_posts' ) ) return false;
		if ( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'delete-css-cache' ) ) {
			$this->delete_cache();
			wp_redirect( preg_replace( '/[ <>\'\"\r\n\t\(\)]/', '', $_GET[ 'path' ] ) );
			die();
		}
	}
}