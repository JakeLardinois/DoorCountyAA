<?php
if (!defined('ABSPATH')) die('No direct access.');

if (class_exists('MPSUM_CONSTANT_CHECKS')) return;

/**
 * Class MPSUM_CONSTANT_CHECKS
 *
 * Checks for wp-config constants that may disable the plugin.
 */
class MPSUM_CONSTANT_CHECKS {

	/**
	 * MPSUM_Reset_Options constructor.
	 */
	private function __construct() {
	}

	/**
	 * Returns a singleton instance
	 *
	 * @return MPSUM_Reset_Options object
	 */
	public static function get_instance() {
		static $instance = null;
		if (null === $instance) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Get a list of constants which are active but prohibited
	 *
	 * @return array a list of constants that may prevent automatic updates from being work properly
	 */
	public function get_prohibited_active_constants() {
		$constants = array();
		if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
			$constants[] = 'DISABLE_WP_CRON';
		}
		return $constants;
	}
}
