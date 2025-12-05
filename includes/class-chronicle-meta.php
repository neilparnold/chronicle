<?php
/**
 * Chronicle meta handling.
 *
 * @package Chronicle
 */

namespace Chronicle;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Meta {

    /**
     * Register meta hooks.
     *
     * @return void
     */
    public static function register() {
        self::register_post_meta();

        \add_action( 'add_meta_boxes', array( __CLASS__, 'register_metabox' ) );
        \add_action( 'save_post_chr_event', array( __CLASS__, 'save_meta' ), 10, 2 );
    }

    /**
     * Register meta fields for REST.
     *
     * @return void
     */
    public static function register_post_meta() {
        $args = array(
            'type'         => 'string',
            'single'       => true,
            'show_in_rest' => true,
            'auth_callback' => function() {
                return \current_user_can( 'edit_posts' );
            },
            'sanitize_callback' => '\sanitize_text_field',
        );

        \register_post_meta( 'chr_event', 'chr_event_start', $args );
        \register_post_meta( 'chr_event', 'chr_event_end', $args );
        \register_post_meta( 'chr_event', 'chr_event_location', $args );

        \register_post_meta(
            'chr_event',
            'chr_event_all_day',
            array(
                'type'              => 'boolean',
                'single'            => true,
                'show_in_rest'      => true,
                'auth_callback'     => function() {
                    return \current_user_can( 'edit_posts' );
                },
                'sanitize_callback' => function( $value ) {
                    return (bool) $value;
                },
            )
        );
    }

    /**
     * Register event details meta box.
     *
     * @return void
     */
    public static function register_metabox() {
        \add_meta_box(
            'chr_event_details',
            \__( 'Event Details', 'chronicle' ),
            array( __CLASS__, 'render_metabox' ),
            'chr_event',
            'normal',
            'high'
        );
    }

    /**
     * Render the meta box UI.
     *
     * @param \WP_Post $post Current post object.
     * @return void
     */
    public static function render_metabox( $post ) {
        \wp_nonce_field( 'chr_save_event_meta', 'chr_event_meta_nonce' );

        $start    = \get_post_meta( $post->ID, 'chr_event_start', true );
        $end      = \get_post_meta( $post->ID, 'chr_event_end', true );
        $location = \get_post_meta( $post->ID, 'chr_event_location', true );
        $all_day  = (bool) \get_post_meta( $post->ID, 'chr_event_all_day', true );

        $start_timestamp = $start ? \strtotime( $start ) : false;
        $end_timestamp   = $end ? \strtotime( $end ) : false;

        $start_date = $start_timestamp ? \gmdate( 'Y-m-d', $start_timestamp ) : '';
        $start_time = ( $start_timestamp && ! $all_day ) ? \gmdate( 'H:i', $start_timestamp ) : '';

        $end_date = $end_timestamp ? \gmdate( 'Y-m-d', $end_timestamp ) : '';
        $end_time = ( $end_timestamp && ! $all_day ) ? \gmdate( 'H:i', $end_timestamp ) : '';
        ?>
        <p class="chr-event-all-day">
            <label for="chr_event_all_day">
                <input type="checkbox" id="chr_event_all_day" name="chr_event_all_day" value="1" <?php checked( $all_day ); ?> />
                <?php \esc_html_e( 'All day event', 'chronicle' ); ?>
            </label>
        </p>
        <div class="chr-event-datetime">
            <div class="chr-event-field">
                <label for="chr_event_start_date"><strong><?php \esc_html_e( 'Start date', 'chronicle' ); ?></strong></label><br />
                <input type="date" id="chr_event_start_date" name="chr_event_start_date" value="<?php echo \esc_attr( $start_date ); ?>" class="widefat" />
            </div>
            <div class="chr-event-field chr-event-time">
                <label for="chr_event_start_time"><strong><?php \esc_html_e( 'Start time', 'chronicle' ); ?></strong></label><br />
                <input type="time" id="chr_event_start_time" name="chr_event_start_time" value="<?php echo \esc_attr( $start_time ); ?>" step="300" class="widefat" />
            </div>
        </div>
        <div class="chr-event-datetime">
            <div class="chr-event-field">
                <label for="chr_event_end_date"><strong><?php \esc_html_e( 'End date', 'chronicle' ); ?></strong></label><br />
                <input type="date" id="chr_event_end_date" name="chr_event_end_date" value="<?php echo \esc_attr( $end_date ); ?>" class="widefat" />
            </div>
            <div class="chr-event-field chr-event-time">
                <label for="chr_event_end_time"><strong><?php \esc_html_e( 'End time', 'chronicle' ); ?></strong></label><br />
                <input type="time" id="chr_event_end_time" name="chr_event_end_time" value="<?php echo \esc_attr( $end_time ); ?>" step="300" class="widefat" />
            </div>
        </div>
        <p>
            <label for="chr_event_location"><strong><?php \esc_html_e( 'Location', 'chronicle' ); ?></strong></label><br />
            <input type="text" id="chr_event_location" name="chr_event_location" value="<?php echo \esc_attr( $location ); ?>" class="widefat" />
        </p>
        <?php
    }

    /**
     * Persist event meta fields.
     *
     * @param int      $post_id The ID of the post being saved.
     * @param \WP_Post $post    The post object.
     * @return void
     */
    public static function save_meta( $post_id, $post ) {
        if ( ! isset( $_POST['chr_event_meta_nonce'] ) || ! \wp_verify_nonce( $_POST['chr_event_meta_nonce'], 'chr_save_event_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'chr_event' !== $post->post_type ) {
            return;
        }

        if ( ! \current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $all_day       = isset( $_POST['chr_event_all_day'] );
        $start_date    = isset( $_POST['chr_event_start_date'] ) ? \sanitize_text_field( \wp_unslash( $_POST['chr_event_start_date'] ) ) : '';
        $start_time    = isset( $_POST['chr_event_start_time'] ) ? \sanitize_text_field( \wp_unslash( $_POST['chr_event_start_time'] ) ) : '';
        $end_date      = isset( $_POST['chr_event_end_date'] ) ? \sanitize_text_field( \wp_unslash( $_POST['chr_event_end_date'] ) ) : '';
        $end_time      = isset( $_POST['chr_event_end_time'] ) ? \sanitize_text_field( \wp_unslash( $_POST['chr_event_end_time'] ) ) : '';
        $location      = isset( $_POST['chr_event_location'] ) ? \sanitize_text_field( \wp_unslash( $_POST['chr_event_location'] ) ) : '';

        $start = $start_date ? $start_date : '';
        $end   = $end_date ? $end_date : '';

        if ( $start && ! $all_day && $start_time ) {
            $start .= ' ' . $start_time;
        }

        if ( $end && ! $all_day && $end_time ) {
            $end .= ' ' . $end_time;
        }

        \update_post_meta( $post_id, 'chr_event_start', $start );
        \update_post_meta( $post_id, 'chr_event_end', $end );
        \update_post_meta( $post_id, 'chr_event_location', $location );
        \update_post_meta( $post_id, 'chr_event_all_day', $all_day ? '1' : '0' );
    }
}
