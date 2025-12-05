<?php
/**
 * Main Chronicle plugin class.
 *
 * @package Chronicle
 */

namespace Chronicle;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Main {

	/**
	 * Singleton instance.
	 *
	 * @var Main|null
	 */
	protected static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Main
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Wire everything up here.
		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Init callback.
	 */
	public function init() {
		// Example: delegate to other classes.
		CPT::register();
		Taxonomies::register();
		Assets::register();
		Shortcodes::register();
	}
}
