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
        \add_shortcode( 'chronicle_events', array( __CLASS__, 'render_events' ) );

        // Temporary alias to avoid breaking early adopters of the scaffolded name.
        \add_shortcode( 'chronical', array( __CLASS__, 'render_events' ) );
    }

    /**
     * [chronicle_events] shortcode callback.
     *
     * @param array       $atts    Shortcode attributes.
     * @param string|null $content Enclosed content (if any).
     * @return string
     */
    public static function render_events( $atts = array(), $content = null ) {
        $atts = \shortcode_atts(
            array(
                'limit'    => 5,
                'order'    => 'ASC',
                'category' => '',
            ),
            $atts,
            'chronicle_events'
        );

        $query_args = array(
            'post_type'      => 'chr_event',
            'posts_per_page' => (int) $atts['limit'],
            'post_status'    => 'publish',
            'meta_key'       => 'chr_event_start',
            'orderby'        => 'meta_value',
            'order'          => 'ASC' === strtoupper( $atts['order'] ) ? 'ASC' : 'DESC',
            'meta_type'      => 'DATETIME',
        );

        if ( ! empty( $atts['category'] ) ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'chr_event_category',
                    'field'    => 'slug',
                    'terms'    => array_map( '\sanitize_title', array_map( 'trim', explode( ',', $atts['category'] ) ) ),
                ),
            );
        }

        $events = new \WP_Query( $query_args );

        if ( ! $events->have_posts() ) {
            return '<p class="chronicle-events__empty">' . \esc_html__( 'No events found.', 'chronicle' ) . '</p>';
        }

        ob_start();
        ?>
        <ul class="chronicle-events">
            <?php
            while ( $events->have_posts() ) {
                $events->the_post();

                $start    = \get_post_meta( \get_the_ID(), 'chr_event_start', true );
                $end      = \get_post_meta( \get_the_ID(), 'chr_event_end', true );
                $location = \get_post_meta( \get_the_ID(), 'chr_event_location', true );
                $all_day  = (bool) \get_post_meta( \get_the_ID(), 'chr_event_all_day', true );

                $date_format = \get_option( 'date_format' );
                $time_format = \get_option( 'time_format' );

                $start_pattern = $all_day ? $date_format : $date_format . ' ' . $time_format;
                $end_pattern   = $all_day ? $date_format : $date_format . ' ' . $time_format;

                $formatted_start = $start ? \date_i18n( $start_pattern, strtotime( $start ) ) : '';
                $formatted_end   = $end ? \date_i18n( $end_pattern, strtotime( $end ) ) : '';
                ?>
                <li class="chronicle-event__item">
                    <h3 class="chronicle-event__title"><a href="<?php \the_permalink(); ?>"><?php \the_title(); ?></a></h3>

                    <div class="chronicle-event__meta">
                        <?php if ( $formatted_start ) : ?>
                            <span class="chronicle-event__date chronicle-event__date--start">
                                <?php echo \esc_html( $formatted_start ); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( $formatted_end ) : ?>
                            <span class="chronicle-event__date chronicle-event__date--end">
                                <?php echo \esc_html( $formatted_end ); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( $location ) : ?>
                            <span class="chronicle-event__location">
                                <?php echo \esc_html( $location ); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="chronicle-event__excerpt">
                        <?php \the_excerpt(); ?>
                    </div>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
        \wp_reset_postdata();

        return ob_get_clean();
    }
}
