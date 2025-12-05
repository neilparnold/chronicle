<?php
/**
 * Chronicle taxonomies.
 *
 * @package Chronicle
 */

namespace Chronicle;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Taxonomies {

	/**
	 * Register all Chronicle taxonomies.
	 *
	 * Called from Main::init().
	 *
	 * @return void
	 */
	public static function register() {
		self::register_event_category();
	}

	/**
	 * Register Event Category taxonomy.
	 *
	 * @return void
	 */
	protected static function register_event_category() {
		$labels = array(
			'name'              => __( 'Event Categories', 'chronicle' ),
			'singular_name'     => __( 'Event Category', 'chronicle' ),
			'search_items'      => __( 'Search Event Categories', 'chronicle' ),
			'all_items'         => __( 'All Event Categories', 'chronicle' ),
			'parent_item'       => __( 'Parent Event Category', 'chronicle' ),
			'parent_item_colon' => __( 'Parent Event Category:', 'chronicle' ),
			'edit_item'         => __( 'Edit Event Category', 'chronicle' ),
			'update_item'       => __( 'Update Event Category', 'chronicle' ),
			'add_new_item'      => __( 'Add New Event Category', 'chronicle' ),
			'new_item_name'     => __( 'New Event Category Name', 'chronicle' ),
			'menu_name'         => __( 'Event Categories', 'chronicle' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true, // Category-style.
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'         => 'event-category',
				'with_front'   => false,
				'hierarchical' => true,
			),
		);

		\register_taxonomy(
			'chr_event_category',
			array( 'chr_event' ),
			$args
		);
	}
}
