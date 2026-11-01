<?php
/**
 * Per-Post Robots Meta Override
 *
 * Adds a "Hide from search engines" toggle as a side meta box on `post`,
 * `page`, and `book` post types. When enabled, filters `wp_robots` to add
 * `noindex` and `nofollow` directives, hiding the post from Google/Bing
 * indexing without affecting site-wide visibility.
 *
 * Stored as `_langgam_fikir_noindex` post meta (string '1' or empty).
 *
 * @package Langgam_Fikir
 * @since 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the side meta box on relevant post types.
 */
function langgam_fikir_register_robots_meta_box() {
    $screens = array( 'post', 'page', 'book' );

    foreach ( $screens as $screen ) {
        add_meta_box(
            'langgam_fikir_robots',
            __( 'Search Engine Visibility', 'langgam-fikir' ),
            'langgam_fikir_robots_meta_box_callback',
            $screen,
            'side',
            'low'
        );
    }
}
add_action( 'add_meta_boxes', 'langgam_fikir_register_robots_meta_box' );

/**
 * Render the side meta box.
 *
 * @param WP_Post $post Current post.
 */
function langgam_fikir_robots_meta_box_callback( $post ) {
    wp_nonce_field( 'langgam_fikir_robots_meta', 'langgam_fikir_robots_nonce' );

    $noindex = get_post_meta( $post->ID, '_langgam_fikir_noindex', true );
    ?>
    <p>
        <label>
            <input type="checkbox" name="langgam_fikir_noindex" value="1" <?php checked( $noindex, '1' ); ?>>
            <?php esc_html_e( 'Discourage search engines from indexing this entry', 'langgam-fikir' ); ?>
        </label>
    </p>
    <p class="description">
        <?php esc_html_e( 'When checked, this entry sends noindex/nofollow directives to search engines. Useful for thank-you pages, landing pages, draft-staged content, or anything you want excluded from Google/Bing without affecting site-wide visibility.', 'langgam-fikir' ); ?>
    </p>
    <?php
}

/**
 * Save the robots meta toggle.
 *
 * @param int $post_id Post ID.
 */
function langgam_fikir_save_robots_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }
    if ( ! isset( $_POST['langgam_fikir_robots_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['langgam_fikir_robots_nonce'] ) ), 'langgam_fikir_robots_meta' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( ! empty( $_POST['langgam_fikir_noindex'] ) ) {
        update_post_meta( $post_id, '_langgam_fikir_noindex', '1' );
    } else {
        delete_post_meta( $post_id, '_langgam_fikir_noindex' );
    }
}
add_action( 'save_post_post', 'langgam_fikir_save_robots_meta' );
add_action( 'save_post_page', 'langgam_fikir_save_robots_meta' );
add_action( 'save_post_book', 'langgam_fikir_save_robots_meta' );
