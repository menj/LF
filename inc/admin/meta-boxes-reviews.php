<?php
/**
 * Book Reviews Meta Box — Repeatable Rating + Review Entries
 *
 * Adds a Reviews meta box on the `book` post type. Stores an array of
 * { author, rating, title, body, date } pairs in `_langgam_fikir_reviews`
 * post meta. Drives both the visible reviews section on single-book.php
 * and the Review / aggregateRating properties on the Book JSON-LD.
 *
 * @package Langgam_Fikir
 * @since 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the Reviews meta box on the book post type only.
 */
function langgam_fikir_register_reviews_meta_box() {
    add_meta_box(
        'langgam_fikir_reviews',
        __( 'Book Reviews', 'langgam-fikir' ),
        'langgam_fikir_reviews_meta_box_callback',
        'book',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'langgam_fikir_register_reviews_meta_box' );

/**
 * Render the Reviews meta box.
 *
 * @param WP_Post $post Current post object.
 */
function langgam_fikir_reviews_meta_box_callback( $post ) {
    wp_nonce_field( 'langgam_fikir_reviews_meta', 'langgam_fikir_reviews_nonce' );

    $reviews = get_post_meta( $post->ID, '_langgam_fikir_reviews', true );
    if ( ! is_array( $reviews ) ) {
        $reviews = array();
    }
    ?>
    <p class="description">
        <?php esc_html_e( 'Add reviews from readers, critics, or partner publications. These will appear as a visible reviews section on the book page and as Review / aggregateRating structured data — unlocking star-rating rich results in Google search.', 'langgam-fikir' ); ?>
    </p>

    <div id="langgam-fikir-reviews-list" class="langgam-fikir-reviews-list" data-review-count="<?php echo esc_attr( count( $reviews ) ); ?>">
        <?php
        if ( empty( $reviews ) ) {
            langgam_fikir_render_review_row( 0 );
        } else {
            foreach ( $reviews as $i => $review ) {
                langgam_fikir_render_review_row( $i, $review );
            }
        }
        ?>
    </div>

    <p>
        <button type="button" class="button button-secondary" id="langgam-fikir-reviews-add">
            <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
            <?php esc_html_e( 'Add Another Review', 'langgam-fikir' ); ?>
        </button>
    </p>

    <?php
    // Hidden template row for JS cloning
    echo '<script type="text/html" id="langgam-fikir-review-template">';
    langgam_fikir_render_review_row( '__INDEX__' );
    echo '</script>';
}

/**
 * Render a single review row.
 *
 * @param int|string $index Row index. Use __INDEX__ for the JS template.
 * @param array      $review Stored review data { author, rating, title, body, date }.
 */
function langgam_fikir_render_review_row( $index, $review = array() ) {
    $defaults = array(
        'author' => '',
        'rating' => '',
        'title'  => '',
        'body'   => '',
        'date'   => '',
    );
    $r = wp_parse_args( $review, $defaults );
    ?>
    <div class="langgam-fikir-review-row" data-review-index="<?php echo esc_attr( $index ); ?>">
        <div class="langgam-fikir-review-row-header">
            <span class="langgam-fikir-review-row-handle dashicons dashicons-menu" aria-hidden="true"></span>
            <strong class="langgam-fikir-review-row-title">
                <?php
                printf(
                    /* translators: %s: review row number */
                    esc_html__( 'Review %s', 'langgam-fikir' ),
                    is_numeric( $index ) ? esc_html( (int) $index + 1 ) : '#'
                );
                ?>
            </strong>
            <button type="button" class="button-link langgam-fikir-review-remove" aria-label="<?php esc_attr_e( 'Remove this review', 'langgam-fikir' ); ?>">
                <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                <?php esc_html_e( 'Remove', 'langgam-fikir' ); ?>
            </button>
        </div>

        <div class="langgam-fikir-review-row-body">
            <div class="langgam-fikir-review-row-grid">
                <p>
                    <label for="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_author">
                        <strong><?php esc_html_e( 'Reviewer Name', 'langgam-fikir' ); ?></strong>
                    </label>
                    <input type="text"
                           id="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_author"
                           name="langgam_fikir_reviews[<?php echo esc_attr( $index ); ?>][author]"
                           value="<?php echo esc_attr( $r['author'] ); ?>"
                           class="widefat"
                           placeholder="<?php esc_attr_e( 'e.g., Dr Ahmad Faisal', 'langgam-fikir' ); ?>">
                </p>
                <p>
                    <label for="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_rating">
                        <strong><?php esc_html_e( 'Rating (1–5)', 'langgam-fikir' ); ?></strong>
                    </label>
                    <select id="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_rating"
                            name="langgam_fikir_reviews[<?php echo esc_attr( $index ); ?>][rating]">
                        <option value=""><?php esc_html_e( '— Select —', 'langgam-fikir' ); ?></option>
                        <?php for ( $s = 5; $s >= 1; $s-- ) : ?>
                            <option value="<?php echo esc_attr( $s ); ?>" <?php selected( (string) $r['rating'], (string) $s ); ?>>
                                <?php
                                printf(
                                    /* translators: %d: star rating */
                                    esc_html( _n( '%d star', '%d stars', $s, 'langgam-fikir' ) ),
                                    (int) $s
                                );
                                ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </p>
                <p>
                    <label for="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_date">
                        <strong><?php esc_html_e( 'Review Date', 'langgam-fikir' ); ?></strong>
                    </label>
                    <input type="date"
                           id="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_date"
                           name="langgam_fikir_reviews[<?php echo esc_attr( $index ); ?>][date]"
                           value="<?php echo esc_attr( $r['date'] ); ?>"
                           class="widefat">
                </p>
            </div>
            <p>
                <label for="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_title">
                    <strong><?php esc_html_e( 'Review Headline (optional)', 'langgam-fikir' ); ?></strong>
                </label>
                <input type="text"
                       id="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_title"
                       name="langgam_fikir_reviews[<?php echo esc_attr( $index ); ?>][title]"
                       value="<?php echo esc_attr( $r['title'] ); ?>"
                       class="widefat"
                       placeholder="<?php esc_attr_e( 'e.g., A masterful synthesis of Islamic philosophy', 'langgam-fikir' ); ?>">
            </p>
            <p>
                <label for="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_body">
                    <strong><?php esc_html_e( 'Review Body', 'langgam-fikir' ); ?></strong>
                </label>
                <textarea id="langgam_fikir_review_<?php echo esc_attr( $index ); ?>_body"
                          name="langgam_fikir_reviews[<?php echo esc_attr( $index ); ?>][body]"
                          rows="4"
                          class="widefat"
                          placeholder="<?php esc_attr_e( 'Review excerpt or full text. Plain text only.', 'langgam-fikir' ); ?>"><?php echo esc_textarea( $r['body'] ); ?></textarea>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Save reviews meta box data.
 *
 * @param int $post_id Post ID.
 */
function langgam_fikir_save_reviews_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }
    if ( ! isset( $_POST['langgam_fikir_reviews_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['langgam_fikir_reviews_nonce'] ) ), 'langgam_fikir_reviews_meta' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( empty( $_POST['langgam_fikir_reviews'] ) || ! is_array( $_POST['langgam_fikir_reviews'] ) ) {
        delete_post_meta( $post_id, '_langgam_fikir_reviews' );
        return;
    }

    $clean = array();
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — sanitised individually below
    $raw = wp_unslash( $_POST['langgam_fikir_reviews'] );

    foreach ( $raw as $entry ) {
        if ( ! is_array( $entry ) ) {
            continue;
        }
        $author = isset( $entry['author'] ) ? sanitize_text_field( $entry['author'] ) : '';
        $rating = isset( $entry['rating'] ) ? (int) $entry['rating'] : 0;
        $title  = isset( $entry['title'] ) ? sanitize_text_field( $entry['title'] ) : '';
        $body   = isset( $entry['body'] ) ? sanitize_textarea_field( $entry['body'] ) : '';
        $date   = isset( $entry['date'] ) ? sanitize_text_field( $entry['date'] ) : '';

        // Skip rows missing the essential fields
        if ( $author === '' || $body === '' || $rating < 1 || $rating > 5 ) {
            continue;
        }

        // Validate date format (YYYY-MM-DD); blank is fine
        if ( $date !== '' && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
            $date = '';
        }

        $clean[] = array(
            'author' => $author,
            'rating' => $rating,
            'title'  => $title,
            'body'   => $body,
            'date'   => $date,
        );
    }

    if ( empty( $clean ) ) {
        delete_post_meta( $post_id, '_langgam_fikir_reviews' );
    } else {
        update_post_meta( $post_id, '_langgam_fikir_reviews', $clean );
    }
}
add_action( 'save_post_book', 'langgam_fikir_save_reviews_meta' );
