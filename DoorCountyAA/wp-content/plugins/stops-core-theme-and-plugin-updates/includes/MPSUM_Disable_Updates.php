<?php
/**
 * Controller class for disabling updates throughout WordPress.
 *
 * @package WordPress
 * @since 5.0.0
 */
class MPSUM_Disable_Updates {

	/**
	 * Holds the class instance.
	 *
	 * @since 5.0.0
	 * @access static
	 * @var MPSUM_Disable_Updates $instance
	 */
	private static $instance = null;

	private $is_core_updating_allowed = true;

	/**
	 * Set a class instance.
	 *
	 * Set a class instance.
	 *
	 * @since 5.0.0
	 * @access static
	 */
	public static function run() {
		if (null == self::$instance) {
			self::$instance = new self;
		}
	} //end get_instance

	/**
	 * Class constructor.
	 *
	 * Read in the plugin options and determine which updates are disabled.
	 *
	 * @since 5.0.0
	 * @access private
	 */
	private function __construct() {

		$core_options = MPSUM_Updates_Manager::get_options('core');

		// Disable Footer Nag
		if (defined('EUM_ENABLE_WORDPRESS_FOOTER_VERSION') && !EUM_ENABLE_WORDPRESS_FOOTER_VERSION) {
			add_filter('update_footer', '__return_empty_string', 11);
		}

		// Disable Browser Nag
		if (defined('EUM_ENABLE_BROWSER_NAG') && !EUM_ENABLE_BROWSER_NAG) {
			add_action('wp_dashboard_setup', array( $this, 'disable_browser_nag' ), 9);
			add_action('wp_network_dashboard_setup', array( $this, 'disable_browser_nag' ), 9);
		}

		// Recommended patch from Flywheel to turn on plugin and theme auto-updates.
		add_action('wp_update_plugins', array($this, 'maybe_auto_update'), 20);

		// Disable All Updates
		if (isset($core_options['all_updates']) && 'off' == $core_options['all_updates']) {
			new MPSUM_Disable_Updates_All();
			return;
		} else {
			add_filter('automatic_updater_disabled', '__return_false', PHP_INT_MAX - 10);
			add_filter('file_mod_allowed', array($this, 'allow_file_modifications_for_automatic_updating'), PHP_INT_MAX - 10, 2);
		}

		// Enable or disable version control protection
		if (Easy_Updates_Manager()->is_premium()) {
			if (isset($core_options['version_control']) && 'on' == $core_options['version_control']) {
				new MPSUM_Disable_VCS();
			}
		}

		if (isset($core_options['core_updates']) && 'off' == $core_options['core_updates']) {
			// Completely disable WordPress Updates
			new MPSUM_Disable_Updates_WordPress();
		} else {
			// Core Development Updates
			add_filter('allow_dev_auto_core_updates', array($this, 'core_should_update_to_new_version'), PHP_INT_MAX - 10);

			// Core Major Updates
			add_filter('allow_major_auto_core_updates', array($this, 'core_should_update_to_new_version'), PHP_INT_MAX - 10);

			// Core Minor Updates
			add_filter('allow_minor_auto_core_updates', array($this, 'core_should_update_to_new_version'), PHP_INT_MAX - 10);

			// Core Global Updates - a hook for making final decision as to whether core updating is allowed after examing other core-related hooks, also ensuring that no other filters will override our value
			add_filter('auto_update_core', array($this, 'is_core_updating_allowed'), PHP_INT_MAX - 10);
			if (!isset($core_options['core_updates']) || 'on' == $core_options['core_updates']) $this->is_core_updating_allowed = false; // on means manually update

			// Manually update / Disables Core Automatic Updates
			// When the __return_false function is hooked to the three filters above, that means core automatic updates is disabled or it's a manually update
		}

		// Disable Plugin Updates
		if (isset($core_options['plugin_updates']) && 'off' == $core_options['plugin_updates']) {
			new MPSUM_Disable_Updates_Plugins();
		}

		// Disable Theme Updates
		if (isset($core_options['theme_updates']) && 'off' == $core_options['theme_updates']) {
			new MPSUM_Disable_Updates_Themes();
		}

		// Disable Translation Updates
		if (isset($core_options['translation_updates']) && 'off' == $core_options['translation_updates']) {
			new MPSUM_Disable_Updates_Translations();
		}

		// Enable Translation Updates
		if (isset($core_options['translation_updates']) && 'automatic' == $core_options['translation_updates']) {
			add_filter('auto_update_translation', '__return_true', PHP_INT_MAX - 10);
			add_filter('async_update_translation', '__return_true', PHP_INT_MAX - 10);
		}
		
		// Disable Translation Updates
		if (!isset($core_options['translation_updates']) || in_array($core_options['translation_updates'], array('on', 'automatic_off'))) { // After 9.0.12 version, translation 'Disable auto updates' is removed and is united to 'Manually update' as they are just the same
			add_filter('auto_update_translation', '__return_false', PHP_INT_MAX - 10);
			add_filter('async_update_translation', '__return_false', PHP_INT_MAX - 10);
		}

		// Disable the Update Notification
		if (isset($core_options['notification_core_update_emails_plugins']) && 'off' == $core_options['notification_core_update_emails_plugins']) {
			add_filter('send_update_notification_email', array( $this, 'maybe_disable_emails' ), 10, 3);
		}
		if (isset($core_options['notification_core_update_emails_themes']) && 'off' == $core_options['notification_core_update_emails_themes']) {
			add_filter('send_update_notification_email', array( $this, 'maybe_disable_emails' ), 10, 3);
		}
		if (isset($core_options['notification_core_update_emails_translations']) && 'off' == $core_options['notification_core_update_emails_translations']) {
			add_filter('send_update_notification_email', array( $this, 'maybe_disable_emails' ), 10, 3);
		}

		// Enable Plugin Auto-updates
		if (isset($core_options['plugin_updates'])) {
			if ('automatic' === $core_options['plugin_updates']) {
				add_filter('auto_update_plugin',  '__return_true', PHP_INT_MAX - 10, 2);
			} elseif ('individual' == $core_options['plugin_updates']) {
				add_filter('auto_update_plugin',  array( $this, 'automatic_updates_plugins' ), PHP_INT_MAX - 10, 2);
			} elseif ('automatic_off' == $core_options['plugin_updates']) {
				add_filter('auto_update_plugin',  '__return_false', PHP_INT_MAX - 10, 2);
			} elseif ('on' === $core_options['plugin_updates']) { // if manually update option (on) is selected then the auto_update_plugin filter should return boolean false
				add_filter('auto_update_plugin',  '__return_false', 1, 2); // should be one that corresponds with earlier execution (as early as it could)
			}
		} else {
			// if none of the plugin updates setting is selected (it can happen on a fresh EUM install) also return boolean false for the auto_update_plugin filter
			add_filter('auto_update_plugin',  '__return_false', 1, 2); // should be one that corresponds with earlier execution (as early as it could)
		}

		// Enable Theme Auto-updates
		if (isset($core_options['theme_updates'])) {
			if ('automatic' === $core_options['theme_updates']) {
				add_filter('auto_update_theme',  '__return_true', PHP_INT_MAX - 10, 2);
			} elseif ('individual' == $core_options['theme_updates']) {
				add_filter('auto_update_theme',  array( $this, 'automatic_updates_theme' ), PHP_INT_MAX - 10, 2);
			} elseif ('automatic_off' == $core_options['theme_updates']) {
				add_filter('auto_update_theme',  '__return_false', PHP_INT_MAX - 10, 2);
			} elseif ('on' === $core_options['theme_updates']) { // if manually update option (on) is selected then the auto_update_theme filter should return boolean false
				add_filter('auto_update_theme',  '__return_false', 1, 2); // should be one that corresponds with earlier execution (as early as it could)
			}
		} else {
			add_filter('auto_update_theme',  '__return_false', 1, 2); // should be one that corresponds with earlier execution (as early as it could)
		}
		

		// Prevent updates on themes/plugins
		add_filter('site_transient_update_plugins', array( $this, 'disable_plugin_notifications' ), PHP_INT_MAX - 10);
		add_filter('site_transient_update_themes', array( $this, 'disable_theme_notifications' ), PHP_INT_MAX - 10);
		add_filter('http_request_args', array( $this, 'http_request_args_remove_plugins_themes' ), 5, 2);

		// Divi compatibility which allows automatic updates to occur
		if (isset($GLOBALS['et_core_updates'])) {
			$divi_upgrader = $GLOBALS['et_core_updates'];
			remove_action('after_setup_theme', array($divi_upgrader, 'remove_theme_update_actions'), 11);
		}
		
		// Initialize and configure notification email filters, whether to disable or enable based on pre-defined user settings
		MPSUM_Send_Email_Notifications::get_instance();
	} //end constructor

	/**
	 * Maybe auto update based on if plugin cron has run
	 * Recommended patch from Flywheel to enable plugin/theme upgrades.
	 *
	 * @since 9.0.3
	 */
	public function maybe_auto_update() {
		if (wp_doing_cron() && ! doing_action('wp_maybe_auto_update')) {
			do_action('wp_maybe_auto_update');
		}
	}

	/**
	 * Maybe disable updates.
	 *
	 * Disable background translation emails, plugin emails, theme emails
	 *
	 * @since 5.2.0
	 * @access public
	 * @see __construct
	 * @param boolean $bool Whether to disable or not
	 * @param string  $type ( theme, plugin , translation )
	 * @return boolean
	 */
	public function maybe_disable_emails($bool, $type) {
		$core_options = MPSUM_Updates_Manager::get_options('core');
		if (isset($core_options['notification_core_update_emails_plugins']) && 'off' == $core_options['notification_core_update_emails_plugins'] && 'plugin' == $type) {
			 return false;
		}
		if (isset($core_options['notification_core_update_emails_themes']) && 'off' == $core_options['notification_core_update_emails_themes'] && 'theme' == $type) {
			 return false;
		}
		if (isset($core_options['notification_core_update_emails_translations']) && 'off' == $core_options['notification_core_update_emails_translations'] && 'translation' == $type) {
			 return false;
		}

		return $bool;

	}
	/**
	 * Disable the out-of-date browser nag on the WordPress Dashboard.
	 *
	 * Disable the out-of-date browser nag on the WordPress Dashboard.
	 *
	 * @since 5.0.0
	 * @access public
	 * @see __construct
	 * @internal uses wp_dashboard_setup action on single-site, wp_network_dashboard_setup action on multisite
	 */
	public function disable_browser_nag() {
		remove_meta_box('dashboard_browser_nag', 'dashboard-network', 'normal');
		remove_meta_box('dashboard_browser_nag', 'dashboard', 'normal');
	}

	/**
	 * Enables plugin automatic updates on an individual basis.
	 *
	 * Enables plugin automatic updates on an individual basis.
	 *
	 * @since 5.0.0
	 * @access public
	 * @see __construct
	 * @internal uses auto_update_plugin filter
	 *
	 * @param bool   $update Whether the item has automatic updates enabled
	 * @param object $item   Object holding the asset to be updated
	 * @return bool True of automatic updates enabled, false if not
	 */
	public function automatic_updates_plugins($update, $item) {
		$plugin_automatic_options = MPSUM_Updates_Manager::get_options('plugins_automatic');
		if (MPSUM_Utils::is_wp_site_health_plugin_theme($item) || (isset($item->plugin) && in_array($item->plugin, $plugin_automatic_options))) {
			return true;
		}
		return false;
	}

	/**
	 * Enables theme automatic updates on an individual basis.
	 *
	 * Enables theme automatic updates on an individual basis.
	 *
	 * @since 5.0.0
	 * @access public
	 * @see __construct
	 * @internal uses auto_update_theme filter
	 *
	 * @param bool   $update Whether the item has automatic updates enabled
	 * @param object $item   Object holding the asset to be updated
	 * @return bool True of automatic updates enabled, false if not
	 */
	public function automatic_updates_theme($update, $item) {
		$theme_automatic_options = MPSUM_Updates_Manager::get_options('themes_automatic');
		if (MPSUM_Utils::is_wp_site_health_plugin_theme($item) || (isset($item->theme) && in_array($item->theme, $theme_automatic_options))) {
			return true;
		}
		return false;
	}

	/**
	 * Disables plugin notifications on an individual basis.
	 *
	 * Disables plugin notifications on an individual basis.
	 *
	 * @since 5.0.0
	 * @access public
	 * @see __construct
	 * @internal uses site_transient_update_plugins filter
	 *
	 * @param object $plugins Plugins that may have update notifications
	 * @return object Updated plugins list with updates
	 */
	public function disable_plugin_notifications($plugins) {
		if (!isset($plugins->response) || empty($plugins->response)) return $plugins;

		$plugin_options = MPSUM_Updates_Manager::get_options('plugins');
		foreach ($plugin_options as $plugin) {
			unset($plugins->response[$plugin]);
		}
		return $plugins;
	}

	/**
	 * Disables theme notifications on an individual basis.
	 *
	 * Disables theme notifications on an individual basis.
	 *
	 * @since 5.0.0
	 * @access public
	 * @see __construct
	 * @internal uses site_transient_update_themes filter
	 *
	 * @param object $themes Themes that may have update notifications
	 * @return object Updated themes list with updates
	 */
	public function disable_theme_notifications($themes) {
		if (!isset($themes->response) || empty($themes->response)) return $themes;

		$theme_options = MPSUM_Updates_Manager::get_options('themes');
		foreach ($theme_options as $theme) {
			unset($themes->response[$theme]);
		}
		return $themes;
	}

	/**
	 * Disables theme and plugin http requests on an individual basis.
	 *
	 * Disables theme and plugin http requests on an individual basis.
	 *
	 * @since 5.0.0
	 * @access public
	 * @see __construct
	 * @internal uses http_request_args filter
	 *
	 * @param array  $r   Request array
	 * @param string $url URL requested
	 * @return array Updated Request array
	 */
	public function http_request_args_remove_plugins_themes($r, $url) {
		if (!MPSUM_Utils::is_wp_api($url)) {
			return $r;
		}

		if (isset($r['body']['plugins'])) {
			$r_plugins = json_decode($r['body']['plugins'], true);
			if (!empty($r_plugins['active'])) {
				if (is_array($r_plugins['active'])) {
					$plugin_options = MPSUM_Updates_Manager::get_options('plugins');
					foreach ($plugin_options as $plugin) {
						unset($r_plugins[$plugin]);
						if (false !== $key = array_search($plugin, $r_plugins['active'])) {
							unset($r_plugins['active'][$key]);
							$r_plugins['active'] = array_values($r_plugins['active']);
						}
					}
				} else {
					error_log("EUM: http_request_args_remove_plugins_themes(): the 'plugins' parameter was non-empty, but not an array; please report this in a support channel: ".serialize($r_plugins['active']));
				}
				$r['body']['plugins'] = json_encode($r_plugins);
			}
		}
		if (isset($r['body']['themes'])) {
			$r_themes = json_decode($r['body']['themes'], true);
			$theme_options = MPSUM_Updates_Manager::get_options('themes');
			foreach ($theme_options as $theme) {
				unset($r_themes[$theme]);
			}
			$r['body']['themes'] = json_encode($r_themes);
		}
		return $r;
	}

	/**
	 * Determine whether  WordPress Core should update to a new version or not
	 *
	 * @return boolean True if we should update to the new version, false otherwise
	 */
	public function core_should_update_to_new_version() {

		$core_options = MPSUM_Updates_Manager::get_options('core');

		if (doing_filter('allow_dev_auto_core_updates')) {
			$this->is_core_updating_allowed = isset($core_options['core_updates']) && in_array($core_options['core_updates'], array('automatic', 'automatic_minor')) && isset($core_options['automatic_development_updates']) && 'on' == $core_options['automatic_development_updates'] ? true : false;
		} elseif (doing_filter('allow_major_auto_core_updates')) {
			$this->is_core_updating_allowed = isset($core_options['core_updates']) && 'automatic' === $core_options['core_updates'] ? true : false;
		} elseif (doing_filter('allow_minor_auto_core_updates')) {
			$this->is_core_updating_allowed = isset($core_options['core_updates']) && in_array($core_options['core_updates'], array('automatic', 'automatic_minor')) ? true : false;
		}
		
		return $this->is_core_updating_allowed;
	}

	/**
	 * Allow and force file modifications for automatic updating
	 *
	 * @param boolean $file_mod_allowed Whether file modifications are allowed
	 * @param string  $context          The usage context
	 * @return boolean True if the context is automatic updater, false if file modification isn't allowed
	 */
	public function allow_file_modifications_for_automatic_updating($file_mod_allowed, $context) {
		if ('automatic_updater' === $context) return true;
		return $file_mod_allowed;
	}

	/**
	 * Return the value that previously has been looked over the core-related filters as to whether core updating is allowed or not
	 */
	public function is_core_updating_allowed() {
		return $this->is_core_updating_allowed;
	}
}
