<?php
/**
 * Plugin Name: Chronicle by NEMC
 * Description: A clean, developer-friendly events calendar system for WordPress.
 * Version: 1.0.0
 * Author: Northeast Media Collective
 * Author URI: https://northeastmediacollective.com
 * Text Domain: chronicle
 */

defined( 'ABSPATH' ) || exit;

define( 'CHR_VERSION', '1.0.0' );
define( 'CHR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CHR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load required files.
 *
 * Load in dependency order so each class is available when used.
 */
require_once CHR_PLUGIN_DIR . 'includes/class-chronicle-main.php';
require_once CHR_PLUGIN_DIR . 'includes/class-chronicle-cpt.php';
require_once CHR_PLUGIN_DIR . 'includes/class-chronicle-taxonomies.php';
require_once CHR_PLUGIN_DIR . 'includes/class-chronicle-meta.php';
require_once CHR_PLUGIN_DIR . 'includes/class-chronicle-assets.php';
require_once CHR_PLUGIN_DIR . 'includes/class-chronicle-shortcodes.php';

/**
 * Bootstrap Chronicle.
 */
function chronicle() {
    return Chronicle\Main::instance();
}
\add_action( 'plugins_loaded', 'chronicle' );
