<?php
/**
 * Chronicle assets.
 *
 * @package Chronicle
 */

namespace Chronicle;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {

	/**
	 * Register asset hooks.
	 *
	 * @return void
	 */
	public static function register() {
		\add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend' ) );
		\add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin' ) );
	}

	/**
	 * Enqueue front-end scripts and styles.
	 *
	 * @return void
	 */
	public static function enqueue_frontend() {
		\wp_enqueue_style(
			'chronicle-frontend',
			CHR_PLUGIN_URL . 'assets/css/chronicle-frontend.css',
			array(),
			CHR_VERSION
		);

		\wp_enqueue_script(
			'chronicle-frontend',
			CHR_PLUGIN_URL . 'assets/js/chronicle-frontend.js',
			array( 'jquery' ),
			CHR_VERSION,
			true
		);
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public static function enqueue_admin( $hook_suffix ) {
		// You can restrict to specific admin screens later if you want.
		\wp_enqueue_style(
			'chronicle-admin',
			CHR_PLUGIN_URL . 'assets/css/chronicle-admin.css',
			array(),
			CHR_VERSION
		);

		\wp_enqueue_script(
			'chronicle-admin',
			CHR_PLUGIN_URL . 'assets/js/chronicle-admin.js',
			array( 'jquery' ),
			CHR_VERSION,
			true
		);
	}
}
