<?php
/**
 * Chronicle CPT registration.
 *
 * @package Chronicle
 */

namespace Chronicle;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CPT {

    public static function register() {
        \register_post_type(
            'chr_event',
            [
                'labels'       => [
                    'name'          => \__( 'Events', 'chronicle' ),
                    'singular_name' => \__( 'Event', 'chronicle' ),
                ],
                'public'       => true,
                'has_archive'  => true,
                'rewrite'      => [
                    'slug' => 'events',
                ],
                'show_in_rest' => true,
                'supports'     => [ 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ],
            ]
        );
    }
}
