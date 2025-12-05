<?php
/**
 * Chronicle shortcodes.
 *
 * @package Chronicle
 */

namespace Chronicle;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	/**
	 * Register shortcodes.
	 *
	 * @return void
	 */
	public static function register() {
		\add_shortcode( 'chronical', array( __CLASS__, 'shortcode_chronical' ) );
	}

	/**
	 * [chronical] shortcode callback.
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Enclosed content (if any).
	 * @return string
	 */
	public static function shortcode_chronical( $atts = array(), $content = null ) {
		// Super basic for now.
		return 'hello world';
	}
}
