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
        \add_action( 'init', [ $this, 'init' ] );
    }

    /**
     * Init callback.
     */
    public function init() {
        // Example: delegate to other classes.
        CPT::register();
        Taxonomies::register();
        Meta::register();
        Assets::register();
        Shortcodes::register();

        \add_filter( 'the_content', [ $this, 'render_event_details' ] );
    }

    /**
     * Append event metadata to the single event view.
     *
     * @param string $content Existing post content.
     * @return string
     */
    public function render_event_details( $content ) {
        if ( ! \is_singular( 'chr_event' ) || ! \in_the_loop() || ! \is_main_query() ) {
            return $content;
        }

        $start    = \get_post_meta( \get_the_ID(), 'chr_event_start', true );
        $end      = \get_post_meta( \get_the_ID(), 'chr_event_end', true );
        $location = \get_post_meta( \get_the_ID(), 'chr_event_location', true );
        $all_day  = (bool) \get_post_meta( \get_the_ID(), 'chr_event_all_day', true );

        $date_format = \get_option( 'date_format' );
        $time_format = \get_option( 'time_format' );

        $pattern = $all_day ? $date_format : $date_format . ' ' . $time_format;

        $formatted_start = $start ? \date_i18n( $pattern, strtotime( $start ) ) : '';
        $formatted_end   = $end ? \date_i18n( $pattern, strtotime( $end ) ) : '';

        ob_start();
        ?>
        <div class="chronicle-event__details">
            <?php if ( $formatted_start ) : ?>
                <p class="chronicle-event__datetime chronicle-event__datetime--start">
                    <strong><?php \esc_html_e( 'Starts', 'chronicle' ); ?></strong><br />
                    <span><?php echo \esc_html( $formatted_start ); ?></span>
                </p>
            <?php endif; ?>

            <?php if ( $formatted_end ) : ?>
                <p class="chronicle-event__datetime chronicle-event__datetime--end">
                    <strong><?php \esc_html_e( 'Ends', 'chronicle' ); ?></strong><br />
                    <span><?php echo \esc_html( $formatted_end ); ?></span>
                </p>
            <?php endif; ?>

            <?php if ( $location ) : ?>
                <p class="chronicle-event__location">
                    <strong><?php \esc_html_e( 'Location', 'chronicle' ); ?></strong><br />
                    <span><?php echo \esc_html( $location ); ?></span>
                </p>
            <?php endif; ?>
        </div>
        <?php

        return $content . ob_get_clean();
    }
}
