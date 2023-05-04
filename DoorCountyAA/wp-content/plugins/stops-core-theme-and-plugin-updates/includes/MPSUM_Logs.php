<?php
/**
 * Easy Updates Manager log controller
 *
 * Initializes the log table and sets up actions/events
 *
 * @package WordPress
 * @since 6.0.0
 */
class MPSUM_Logs {

	/**
	 * Holds the class instance.
	 *
	 * @since 6.0.0
	 * @access static
	 * @var MPSUM_Logs $instance
	 */
	public static $instance = null;

	/**
	 * Holds log messages
	 *
	 * @var array An array of log messages
	 */
	protected $log_messages = array();

	/**
	 * Holds update method information
	 *
	 * @var bool Determines whether auto update or manual
	 */
	protected $auto_update = false;
	
	// Format: key=<version>, value=array of method names to call
	private static $db_updates = array(
		'1.1.6' => array(
			'build_table',
		)
	);

	/**
	 * Holds version number of the table
	 *
	 * @since 6.0.0
	 * @access private
	 * @var string $slug
	 */
	private static $version = '1.1.6';

	/**
	 * Holds a variable for checkin the logs table
	 *
	 * @since 8.0.1
	 * @access private
	 * @var bool $log_table_exists
	 */
	private static $log_table_exists = false;

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
		return self::$instance;
	} //end get_instance

	/**
	 * Class constructor.
	 *
	 * Initialize the class
	 *
	 * @since 6.0.0
	 * @access private
	 */
	protected function __construct() {
		register_shutdown_function(array($this, 'perform_shutdown_task'));
		
		$this->check_updates();

		add_action('pre_auto_update', array($this, 'pre_auto_update'));
		add_filter('eum_i18n', array($this, 'logs_i18n'));
		add_filter('upgrader_package_options', array($this, 'initialize_log_messages'), 10, 1);
		add_action('automatic_updates_complete', array($this, 'log_automatic_updates'));
		add_action('upgrader_process_complete', array($this, 'log_updates'), 1, 2);
		add_filter('upgrader_pre_download', array($this, 'initialize_core_log_messages'), 10, 3);

	} //end constructor

	/**
	 * Make sure PHP fatal errors are caught during an update and/or write remaining update information in the $log_messages variable into the database (if any)
	 */
	public function perform_shutdown_task() {
		if (!is_array($this->log_messages) || empty($this->log_messages)) return;
		try {
			$stacktrace = maybe_serialize(apply_filters('eum_normalized_call_stack_args', $this->normalise_call_stack_args(debug_backtrace(false)))); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		} catch (Exception $e) {
			$stacktrace = serialize(array()); // if an exception still happens even after the call stack is already normalised then we won't provide stacktrace for a log entry
		// @codingStandardsIgnoreLine
		} catch (Error $e) {
			$stacktrace = serialize(array());
		}
		foreach ($this->log_messages as $type => $entities) {
			if (!is_array($entities) || empty($entities)) continue;
			foreach ($entities as $data) {
				$this->insert_log($data['name'], $type, $data['from'], $data['to'], $this->auto_update ? 'automatic' : 'manual', doing_action('upgrader_process_complete') || doing_action('automatic_updates_complete') ? 1 : 0, get_current_user_id(), '', $stacktrace);
			}
		}
	}

	/**
	 * Initialize information of an update for each plugin/theme/translation before running the update operation. The information will be stored in the $log_messages variable keyed by the name of what entity is being updated
	 *
	 * @param array $options Package options used by the upgrader
	 * @return array An array of associative arrays keyed by some strings
	 */
	public function initialize_log_messages($options) {
		if (isset($options['hook_extra'], $options['hook_extra']['action']) && 'update' !== $options['hook_extra']['action']) return $options;
		if (isset($options['hook_extra'], $options['hook_extra']['plugin'])) {
			$current = get_site_transient('update_plugins');
			if (isset($current->response[$options['hook_extra']['plugin']])) {
				$plugins = get_plugins(); // It's not expensive calling this API as WordPress has already cached the result in a WP Cache object
				if (!isset($this->log_messages['plugin'])) $this->log_messages['plugin'] = array();
				$this->log_messages['plugin'][$options['hook_extra']['plugin']] = array(
					'name' => isset($plugins[$options['hook_extra']['plugin']]) && isset($plugins[$options['hook_extra']['plugin']]['Name']) ? $plugins[$options['hook_extra']['plugin']]['Name'] : 'Unknown',
					'from' => isset($plugins[$options['hook_extra']['plugin']]) && isset($plugins[$options['hook_extra']['plugin']]['Version']) ? $plugins[$options['hook_extra']['plugin']]['Version'] : 0,
					'to' => isset($current->response[$options['hook_extra']['plugin']]) && is_object($current->response[$options['hook_extra']['plugin']]) && isset($current->response[$options['hook_extra']['plugin']]->new_version) ? $current->response[$options['hook_extra']['plugin']]->new_version : 0,
					'status' => 0
				);
			}
		} elseif (isset($options['hook_extra'], $options['hook_extra']['theme'])) {
			$current = get_site_transient('update_themes');
			if (isset($current->response[$options['hook_extra']['theme']])) {
				$themes = wp_get_themes(); // It's not expensive calling this API as WordPress has already cached the result as a static variable inside the function
				if (!isset($this->log_messages['theme'])) $this->log_messages['theme'] = array();
				$this->log_messages['theme'][$options['hook_extra']['theme']] = array(
					'name' => isset($themes[$options['hook_extra']['theme']]) && is_a($themes[$options['hook_extra']['theme']], 'WP_Theme') ? $themes[$options['hook_extra']['theme']]->get('Name') : 'Unknown',
					'from' => isset($themes[$options['hook_extra']['theme']]) && is_a($themes[$options['hook_extra']['theme']], 'WP_Theme') ? $themes[$options['hook_extra']['theme']]->get('Version') : 0,
					'to' => isset($current->response[$options['hook_extra']['theme']]) && isset($current->response[$options['hook_extra']['theme']]['new_version']) ? $current->response[$options['hook_extra']['theme']]['new_version'] : 0,
					'status' => 0
				);
			}
		} elseif (isset($options['hook_extra']) && isset($options['hook_extra']['language_update_type']) && isset($options['hook_extra']['language_update'])) { // translation
			if (!isset($this->log_messages['translation'])) $this->log_messages['translation'] = array();
			switch ($options['hook_extra']['language_update_type']) {
				case 'plugin':
					$current = get_site_transient('update_plugins');
					if (empty($current->translations)) break;
					$plugins = get_plugins();
					$plugins_by_slug = wp_parse_args(wp_list_pluck($current->no_update, 'plugin', 'slug'), wp_list_pluck($current->response, 'plugin', 'slug'));
					foreach ($current->translations as $translation) {
						if (isset($options['hook_extra']['language_update']->slug) && $translation['slug'] !== $options['hook_extra']['language_update']->slug) continue;
						if (isset($plugins[$plugins_by_slug[$translation['slug']]])) {
							$this->log_messages['translation'][$translation['slug']] = array(
								'name' => isset($plugins[$plugins_by_slug[$translation['slug']]]['Name']) ? $plugins[$plugins_by_slug[$translation['slug']]]['Name']. ' ('.$translation['language'].')' : 'Unknown ('.$translation['language'].')',
								'from' => isset($options['hook_extra']['language_update']->version) ? $options['hook_extra']['language_update']->version : 0,
								'to' => isset($translation['version']) ? $translation['version'] : 0,
								'status' => 0
							);
							break;
						}
					}
					break;
				case 'theme':
					$current = get_site_transient('update_themes');
					if (empty($current->translations)) break;
					$themes = wp_get_themes();
					foreach ($current->translations as $translation) {
						if (isset($options['hook_extra']['language_update']->slug) && $translation['slug'] !== $options['hook_extra']['language_update']->slug) continue;
						if (isset($themes[$translation['slug']])) {
							$this->log_messages['translation'][$translation['slug']] = array(
								'name' => is_a($themes[$translation['slug']], 'WP_Theme') ? $themes[$translation['slug']]->get('Name'). ' ('.$translation['language'].')' : 'Unknown ('.$translation['language'].')',
								'from' => isset($options['hook_extra']['language_update']->version) ? $options['hook_extra']['language_update']->version : 0,
								'to' => isset($translation['version']) ? $translation['version'] : 0,
								'status' => 0
							);
							break;
						}
					}
					break;
				default:
					// core
					$current = get_site_transient('update_core');
					if (empty($current->translations)) break;
					foreach ($current->translations as $translation) {
						$this->log_messages['translation']['wordpress_default_'.$translation['language']] = array(
							'name' => 'WordPress ('.$translation['language'].')',
							'from' => isset($options['hook_extra']['language_update']->version) ? $options['hook_extra']['language_update']->version : 0,
							'to' => isset($translation['version']) ? $translation['version'] : 0,
							'status' => 0
						);
					}
					break;
			}
		}
		return $options;
	}

	/**
	 * Initialize information of a core update before downloading its package. The information will be stored in the $log_messages variable keyed by 'core' string
	 *
	 * @param bool        $reply    Whether to bail without returning the package. Default false
	 * @param string      $package  The package file name
	 * @param WP_Upgrader $upgrader The WP_Upgrader instance
	 */
	public function initialize_core_log_messages($reply, $package, $upgrader) {
		add_filter('upgrader_post_install', array($this, 'set_update_status_by_result'), 10, 3);
		if (is_a($upgrader, 'Core_Upgrader')) {
			global $wp_version;
			$current = get_site_transient('update_core');
			if (!isset($this->log_messages['core']) || !is_array($this->log_messages['core'])) $this->log_messages['core'] = array();
			$item = array(
				'name' => 'WordPress',
				'from' => $wp_version,
				'to' => 0,
				'status' => 0,
				'notes' => ''
			);
			add_action('_core_updated_successfully', array($this, 'set_core_update_success_status'));
			add_filter('update_feedback', array($this, 'set_core_update_notes'), 1, 1);
			if (!$current || empty($current->updates)) return $reply;
			foreach ($current->updates as $update) {
				if (($this->auto_update && 'autoupdate' !== $update->response) || (!$this->auto_update && 'autoupdate' === $update->response)) continue;
				if (empty($update->packages)) continue;
				foreach ($update->packages as $download_url) {
					if ($download_url === $package) {
						$item['to'] = $update->current;
						$item['name'] .= " ({$update->locale})";
						break;
					}
				}
			}
			if (version_compare($wp_version, $item['to'], '==') && empty($this->log_messages['core'])) {
				$item['name'] .= ' (Reinstall)';
				$this->log_messages['core']['reinstall'] = $item;
			} elseif (version_compare($wp_version, $item['to'], '<') && empty($this->log_messages['core'])) {
				$this->log_messages['core']['new'] = $item;
			} elseif (!empty($this->log_messages['core'])) {
				$item['name'] .= ' (Rollback)';
				$item['from'] = isset($this->log_messages['core']['new']) ? $this->log_messages['core']['new']['to'] : $item['from'];
				$item['to'] = isset($this->log_messages['core']['new']) ? $this->log_messages['core']['new']['from'] : $item['to'];
				$this->log_messages['core']['rollback'] = $item;
			}
		}
		return $reply;
	}

	/**
	 * Log the information of updates to the log table, it fires when the upgrader process is complete during either manual or automatic operation and it logs only if the $log_messages variable class is not empty
	 *
	 * @param WP_Upgrader $wp_upgrader WP_Upgrader instance. In other contexts, $this, might be a Theme_Upgrader, Plugin_Upgrader, Core_Upgrade, or Language_Pack_Upgrader instance
	 * @param array       $hook_extra  Extra arguments passed to hooked filters
	 */
	public function log_updates($wp_upgrader, $hook_extra) {
		if (isset($hook_extra['action']) && 'update' !== $hook_extra['action']) return;
		try {
			$stacktrace = maybe_serialize(apply_filters('eum_normalized_call_stack_args', $this->normalise_call_stack_args(debug_backtrace(false)))); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		} catch (Exception $e) {
			$stacktrace = serialize(array()); // if an exception still happens even after the call stack is already normalised then we won't provide stacktrace for a log entry
		// @codingStandardsIgnoreLine
		} catch (Error $e) {
			$stacktrace = serialize(array());
		}
		$notes = '';
		if (is_a($wp_upgrader->skin, 'Automatic_Upgrader_Skin')) {
			foreach ($wp_upgrader->skin->get_upgrade_messages() as $message) {
				$notes .= $message . "\n\r\n\r";
			}
		}
		switch ($hook_extra['type']) {
			case 'translation':
				foreach ($hook_extra['translations'] as $translation) {
					$key = 'core' !== $translation['type'] && isset($this->log_messages['translation'][$translation['slug']]) ? $translation['slug'] : ('core' === $translation['type'] && isset($this->log_messages['translation']['wordpress_default_'.$translation['language']]) ? 'wordpress_default_'.$translation['language'] : '');
					if ('' === $key) continue;
					if (!isset($this->log_messages['translation'][$key])) continue;
					$this->insert_log($this->log_messages['translation'][$key]['name'], 'translation', $this->log_messages['translation'][$key]['from'], $this->log_messages['translation'][$key]['to'], $this->auto_update ? 'automatic' : 'manual', $this->log_messages['translation'][$key]['status'], get_current_user_id(), $notes, $stacktrace);
					unset($this->log_messages['translation'][$key]);
				}
				if (empty($this->log_messages['translation'])) unset($this->log_messages['translation']);
				break;
			case 'core':
				foreach ($this->log_messages['core'] as $core_type => $data) {
					$this->insert_log($data['name'], $hook_extra['type'], $data['from'], $data['to'], $this->auto_update ? 'automatic' : 'manual', $data['status'], get_current_user_id(), $notes ? $notes : $data['notes'], $stacktrace);
					unset($this->log_messages['core'][$core_type]);
				}
				if (empty($this->log_messages['core'])) unset($this->log_messages['core']);
				break;
			case 'plugin':
				$plugins = isset($hook_extra['plugins']) && is_array($hook_extra['plugins']) ? $hook_extra['plugins'] : (isset($hook_extra['plugin']) ? $hook_extra['plugin'] : array());
				foreach ((array) $plugins as $plugin) {
					if (!isset($this->log_messages['plugin'][$plugin])) continue;
					$this->insert_log($this->log_messages['plugin'][$plugin]['name'], $hook_extra['type'], $this->log_messages['plugin'][$plugin]['from'], $this->log_messages['plugin'][$plugin]['to'], $this->auto_update ? 'automatic' : 'manual', $this->log_messages['plugin'][$plugin]['status'], get_current_user_id(), $notes, $stacktrace);
					unset($this->log_messages['plugin'][$plugin]);
				}
				if (empty($this->log_messages['plugin'])) unset($this->log_messages['plugin']);
				break;
			case 'theme':
				$themes = isset($hook_extra['themes']) && is_array($hook_extra['themes']) ? $hook_extra['themes'] : (isset($hook_extra['theme']) ? $hook_extra['theme'] : array());
				foreach ((array) $themes as $theme) {
					if (!isset($this->log_messages['theme'][$theme])) continue;
					$this->insert_log($this->log_messages['theme'][$theme]['name'], $hook_extra['type'], $this->log_messages['theme'][$theme]['from'], $this->log_messages['theme'][$theme]['to'], $this->auto_update ? 'automatic' : 'manual', $this->log_messages['theme'][$theme]['status'], get_current_user_id(), $notes, $stacktrace);
					unset($this->log_messages['theme'][$theme]);
				}
				if (empty($this->log_messages['theme'])) unset($this->log_messages['theme']);
				break;
		}
	}

	/**
	 * Set notes to the $log_messages variable class during update of the WP core
	 *
	 * @param string $note The core update feedback messages
	 */
	public function set_core_update_notes($note) {
		if (!is_array($this->log_messages['core']) || empty($this->log_messages['core']) || !is_string($note)) return $note; // yes, the docblock states that $note is in string type, but that's not always true as somehow it can be WP_Error object
		end($this->log_messages['core']);
		if (!isset($this->log_messages['core'][key($this->log_messages['core'])]['notes']) || !is_string($this->log_messages['core'][key($this->log_messages['core'])]['notes'])) $this->log_messages['core'][key($this->log_messages['core'])]['notes'] = '';
		$this->log_messages['core'][key($this->log_messages['core'])]['notes'] .= $note . "\n\r\n\r";
		reset($this->log_messages['core']);
		return $note;
	}

	/**
	 * Set success to the core update status after WordPress core has been successfully updated
	 *
	 * Hooked to the {@see '_core_updated_successfully'} action, added when initializing core log info
	 */
	public function set_core_update_success_status() {
		remove_filter('update_feedback', array($this, 'set_core_update_notes'), 1, 1);
		if (!is_array($this->log_messages['core']) || empty($this->log_messages['core'])) return;
		end($this->log_messages['core']);
		$this->log_messages['core'][key($this->log_messages['core'])]['status'] = 1;
		reset($this->log_messages['core']);
	}

	/**
	 * Use result data to set update status of plugin/theme/translation after an installation has finished
	 *
	 * @param bool  $response   Installation response
	 * @param array $hook_extra Extra arguments passed to hooked filters
	 * @param array $result     Installation result data
	 * @return bool Installation response
	 */
	public function set_update_status_by_result($response, $hook_extra, $result) {
		if (isset($hook_extra['action']) && 'update' !== $hook_extra['action']) return $response;
		if (isset($hook_extra['plugin']) && isset($this->log_messages['plugin'][$hook_extra['plugin']])) {
			$this->log_messages['plugin'][$hook_extra['plugin']]['status'] = $result && !is_wp_error($result) ? 1 : 0;
		} elseif (isset($hook_extra['theme']) && isset($this->log_messages['theme'][$hook_extra['theme']])) {
			$this->log_messages['theme'][$hook_extra['theme']]['status'] = $result && !is_wp_error($result) ? 1 : 0;
		} elseif (isset($hook_extra['language_update_type']) && isset($hook_extra['language_update'])) {
			switch ($hook_extra['language_update_type']) {
				case 'core':
					$this->log_messages['translation']['wordpress_default_'.$hook_extra['language_update']->language]['status'] = $result && !is_wp_error($result) ? 1 : 0;
					break;
				default:
					// plugins and themes
					$this->log_messages['translation'][$hook_extra['language_update']->slug]['status'] = $result && !is_wp_error($result) ? 1 : 0;
					break;
			}
		}
		return $response;
	}

	/**
	 * Log update information to the log table when automatic updates is complete, and if the $log_messages variable class is not empty. This is final attempt to log updates to the database but this could also mean that nothing has to be done as in the previous action (upgrader_process_complete) the information has already been logged and has been removed from the $log_messages variable
	 * The reason why this method is hooked to the `automatic_updates_complete` and why we still need it is that in some cases when an error occurs during automatic updates the `upgrader_process_complete` action won't get executed, but this action `automatic_updates_complete`/method will. Actually, without having this method we can still log the info on shutdown, but it lacks of notes
	 *
	 * @param array $update_results The results of all attempted updates
	 */
	public function log_automatic_updates($update_results) {
		if (empty($update_results)) return;
		try {
			$stacktrace = maybe_serialize(apply_filters('eum_normalized_call_stack_args', $this->normalise_call_stack_args(debug_backtrace(false)))); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		} catch (Exception $e) {
			$stacktrace = serialize(array()); // if an exception still happens even after the call stack is already normalised then we won't provide stacktrace for a log entry
		// @codingStandardsIgnoreLine
		} catch (Error $e) {
			$stacktrace = serialize(array());
		}
		foreach ($update_results as $type => $results) {
			foreach ($results as $result) {
				$notes = '';
				if (!isset($result->messages)) $result->messages = array();
				foreach ($result->messages as $message) {
					$notes .= $message . "\n\r\n\r";
				}
				if ('core' === $type) {
					if (!isset($this->log_messages[$type])) continue;
					foreach ($this->log_messages[$type] as $core_type => $data) {
						$this->insert_log($data['name'], $type, $data['from'], $data['to'], 'automatic', $data['status'], get_current_user_id(), $notes ? $notes : $data['notes'], $stacktrace);
						unset($this->log_messages[$type][$core_type]);
					}
					if (empty($this->log_messages[$type])) unset($this->log_messages[$type]);
				} elseif ('translation' === $type) {
					if (!isset($this->log_messages[$type], $this->log_messages[$type][$result->item->slug])) continue;
					$this->insert_log($this->log_messages[$type][$result->item->slug]['name'], $type, $this->log_messages[$type][$result->item->slug]['from'], $this->log_messages[$type][$result->item->slug]['to'], 'automatic', isset($result->result) && $result->result && !is_wp_error($result->result) ? 1 : 0, get_current_user_id(), $notes, $stacktrace);
					unset($this->log_messages[$type][$result->item->slug]);
					if (empty($this->log_messages[$type])) unset($this->log_messages[$type]);
				} else {
					if (!isset($this->log_messages[$type], $this->log_messages[$type][$result->item->$type])) continue;
					$this->insert_log($this->log_messages[$type][$result->item->$type]['name'], $type, $this->log_messages[$type][$result->item->$type]['from'], $this->log_messages[$type][$result->item->$type]['to'], 'automatic', isset($result->result) && $result->result && !is_wp_error($result->result) ? 1 : 0, get_current_user_id(), $notes, $stacktrace);
					unset($this->log_messages[$type][$result->item->$type]);
					if (empty($this->log_messages[$type])) unset($this->log_messages[$type]);
				}
			}
		}
	}

	/**
	 * See if any database schema updates are needed, and perform them if so.
	 *
	 * @return void
	 */
	public static function check_updates() {
		$our_version = self::$version;
		$db_version = get_site_option('mpsum_log_table_version', '0');
		if (version_compare($our_version, $db_version, '>')) {
			foreach (self::$db_updates as $version => $updates) {
				if (version_compare($version, $db_version, '>')) {
					foreach ($updates as $update) {
						call_user_func(array(__CLASS__, $update));
					}
				}
			}
			MPSUM_Updates_Manager::update_option('mpsum_log_table_version', $our_version);
		}
	}

	/**
	 * Add webhook i18n
	 *
	 * @param array $i18n Array of internationalized strings
	 * @return array Updated array of internationalized strings
	 */
	public function logs_i18n($i18n) {
		$i18n['logs_no_items'] = __('No items found.', 'stops-core-theme-and-plugin-updates');
		return $i18n;
	}

	/**
	 * Set update method as automatic
	 */
	public function pre_auto_update() {
		$this->auto_update = true;
	}

	/**
	 * Finds and returns name of give update object
	 *
	 * @param object $translation Translation object
	 *
	 * @return array An array of name and version of updates
	 */
	public function get_update_name($translation) {
		$translation = (object) $translation;
		$type = $translation->type;
		$result = array();
		switch ($type) {
			case 'core':
				$result[$type]['name'] = 'WordPress';
				break;
			case 'theme':
				$theme = wp_get_theme($translation->slug);
				if ($theme->exists())
					$result[$type]['name'] = $theme->get('Name') . ' (' . $translation->language . ')';
					$result[$type]['new_version'] = $theme->get('Version');
				break;
			case 'plugin':
				if (! function_exists('get_plugins')) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				$plugin = get_plugins('/' . $translation->slug);
				$plugin = reset($plugin);
				if ($plugin)
					$result[$type]['name'] = $plugin['Name'] . ' (' . $translation->language . ')';
					$result[$type]['new_version'] = $plugin['Version'];
				break;
		}
		$result[$type]['version'] = $translation->version;
		return $result;
	}

	/**
	 * Inserts result of upgrade process message to log table
	 *
	 * @param string $name         Upgrade name
	 * @param string $type         Type of upgrade
	 * @param string $version_from Upgrade from version number
	 * @param string $version      Upgrade to version number
	 * @param string $action       Action type, manual or automatic
	 * @param int    $status       Status of upgrade
	 * @param int    $user_id      User responsible for the upgrade
	 */
	public function insert_log($name, $type, $version_from, $version, $action, $status, $user_id = 0, $notes = '', $stacktrace = '' ) {
		global $wpdb;
		$table_name = $wpdb->base_prefix . 'eum_logs';
		if ('' == $version_from) $version_from = '0.00';
		$notes = str_replace('&#8230;', '', $notes);

		// Strip URLs from notes
		$notes = preg_replace('/\?.*/', '', $notes);

		$wpdb->insert(
			$table_name,
			array(
				'user_id'      => $user_id,
				'name'         => $name,
				'type'         => $type,
				'version_from' => $version_from,
				'version'      => $version,
				'action'       => $action,
				'status'       => $status,
				'date'         => current_time('mysql'),
				'notes'        => $notes,
				'stacktrace'   => $stacktrace,
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);
	}

	/**
	 * Get the name of an translation item being updated.
	 *
	 * @since 6.0.3
	 * @access private
	 * @param	string $type type of translation update
	 * @param	string $slug Slug of item
	 * @return string The name of the item being updated.
	 */
	public function get_name_for_update($type, $slug) {
		if (! function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		switch ($type) {
			case 'core':
				return 'WordPress'; // Not translated

			case 'theme':
				$theme = wp_get_theme($slug);
				if ($theme->exists())
					return $theme->Get('Name');
				break;
			case 'plugin':
				$plugin_data = get_plugins('/' . $slug);
				$plugin_data = reset($plugin_data);
				if ($plugin_data)
					return $plugin_data['Name'];
				break;
		}
		return '';
	}

	/**
	 * Creates the log table
	 *
	 * Creates the log table
	 *
	 * @since 6.0.0
	 * @access public
	 */
	public static function build_table() {
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';

		// Get collation - From /wp-admin/includes/schema.php
		$charset_collate = '';
		if (! empty($wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if (! empty($wpdb->collate))
			$charset_collate .= " COLLATE $wpdb->collate";

		$sql = "CREATE TABLE {$tablename} (
						log_id BIGINT(20) NOT NULL AUTO_INCREMENT,
						user_id BIGINT(20) NOT NULL DEFAULT 0,
						name VARCHAR(255) NOT NULL,
						type VARCHAR(255) NOT NULL,
						version_from VARCHAR(255) NOT NULL,
						version VARCHAR(255) NOT NULL,
						action VARCHAR(255) NOT NULL,
						status VARCHAR(255) NOT NULL,
						notes TEXT NOT NULL,
						stacktrace TEXT DEFAULT NULL,
						date DATETIME NOT NULL,
						PRIMARY KEY  (log_id)
						) {$charset_collate};";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	/**
	 * Checks whether log table exists or not
	 *
	 * @return boolean True if table exists, otherwise false
	 */
	private function log_table_exists() {
		if (true === self::$log_table_exists) {
			return;
		}
		global $wpdb;
		$table_name = $wpdb->prefix.'eum_logs';
		self::$log_table_exists = (bool) $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		return self::$log_table_exists;
	}

	/**
	 * Clears the log table
	 *
	 * Clears the log table
	 *
	 * @since 6.0.0
	 * @access static
	 */
	public static function clear() {
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		$sql = "delete from $tablename";
		$wpdb->query($sql);
	}

	/**
	 * Drops the log table
	 *
	 * Drops the log table
	 *
	 * @since 6.0.0
	 * @access static
	 */
	public static function drop() {
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		$sql = "drop table if exists $tablename";
		$wpdb->query($sql);
		delete_site_option('mpsum_log_table_version');
	}

	/**
	 * Normalise call stacks by clearing out unnecessary objects from their arguments list, leaving the arguments as a string if they're not in an array/object type. The call stacks should be one that is generated by debug_backtrace() function.
	 *
	 * @param array $backtrace The output of the debug_backtrace() function
	 * @return array An array of associative arrays after being normalised
	 */
	public function normalise_call_stack_args($backtrace) {
		foreach ((array) $backtrace as $index => $element) {
			if (!isset($element['args']) || !is_array($element['args'])) $backtrace[$index]['args'] = array();
			foreach ($backtrace[$index]['args'] as $arg_index => $arg) {
				if (is_object($arg)) {
					$backtrace[$index]['args'][$arg_index] = "Object(".get_class($backtrace[$index]['args'][$arg_index]).")";
				} elseif (is_array($arg)) {
					$backtrace[$index]['args'][$arg_index] = "Array(".count($backtrace[$index]['args'][$arg_index]).")";
				} elseif (!is_string($backtrace[$index]['args'][$arg_index])) {
					$backtrace[$index]['args'][$arg_index] = '';
				}
			}
		}
		return $backtrace;
	}
}
