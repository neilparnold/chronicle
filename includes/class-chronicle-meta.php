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
        ?>
        <p>
            <label for="chr_event_start"><strong><?php \esc_html_e( 'Start date & time', 'chronicle' ); ?></strong></label><br />
            <input type="datetime-local" id="chr_event_start" name="chr_event_start" value="<?php echo \esc_attr( $start ); ?>" class="widefat" />
        </p>
        <p>
            <label for="chr_event_end"><strong><?php \esc_html_e( 'End date & time', 'chronicle' ); ?></strong></label><br />
            <input type="datetime-local" id="chr_event_end" name="chr_event_end" value="<?php echo \esc_attr( $end ); ?>" class="widefat" />
        </p>
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

        $start    = isset( $_POST['chr_event_start'] ) ? \sanitize_text_field( \wp_unslash( $_POST['chr_event_start'] ) ) : '';
        $end      = isset( $_POST['chr_event_end'] ) ? \sanitize_text_field( \wp_unslash( $_POST['chr_event_end'] ) ) : '';
        $location = isset( $_POST['chr_event_location'] ) ? \sanitize_text_field( \wp_unslash( $_POST['chr_event_location'] ) ) : '';

        \update_post_meta( $post_id, 'chr_event_start', $start );
        \update_post_meta( $post_id, 'chr_event_end', $end );
        \update_post_meta( $post_id, 'chr_event_location', $location );
    }
}
