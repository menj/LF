<?php
/**
 * FAQ Meta Box — Repeatable Q/A Pairs
 *
 * Adds a FAQ meta box on `book` and `page` post types. Stores an array of
 * { question, answer } pairs in `_langgam_fikir_faq` post meta. Populated
 * data drives both the visible FAQ accordion (template-parts/content/faq-section.php)
 * and the FAQPage JSON-LD output in inc/schema.php.
 *
 * @package Langgam_Fikir
 * @since 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the FAQ meta box on book and page post types.
 */
function langgam_fikir_register_faq_meta_box() {
    $screens = array( 'book', 'page' );

    foreach ( $screens as $screen ) {
        add_meta_box(
            'langgam_fikir_faq',
            __( 'Frequently Asked Questions (FAQ)', 'langgam-fikir' ),
            'langgam_fikir_faq_meta_box_callback',
            $screen,
            'normal',
            'default'
        );
    }
}
add_action( 'add_meta_boxes', 'langgam_fikir_register_faq_meta_box' );

/**
 * Render the FAQ meta box.
 *
 * @param WP_Post $post The current post object.
 */
function langgam_fikir_faq_meta_box_callback( $post ) {
    wp_nonce_field( 'langgam_fikir_faq_meta', 'langgam_fikir_faq_nonce' );

    $faqs = get_post_meta( $post->ID, '_langgam_fikir_faq', true );
    if ( ! is_array( $faqs ) ) {
        $faqs = array();
    }
    ?>
    <p class="description">
        <?php esc_html_e( 'Add frequently asked questions and their answers. These will appear as a visible accordion section on the published page and also be emitted as FAQPage structured data for search engines and answer engines (Google, Bing, voice assistants, AI tools).', 'langgam-fikir' ); ?>
    </p>

    <div id="langgam-fikir-faq-list" class="langgam-fikir-faq-list" data-faq-count="<?php echo esc_attr( count( $faqs ) ); ?>">
        <?php
        if ( empty( $faqs ) ) {
            // Render an empty starter row
            langgam_fikir_render_faq_row( 0, '', '' );
        } else {
            foreach ( $faqs as $i => $faq ) {
                $q = isset( $faq['question'] ) ? $faq['question'] : '';
                $a = isset( $faq['answer'] ) ? $faq['answer'] : '';
                langgam_fikir_render_faq_row( $i, $q, $a );
            }
        }
        ?>
    </div>

    <p>
        <button type="button" class="button button-secondary" id="langgam-fikir-faq-add">
            <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
            <?php esc_html_e( 'Add Another Question', 'langgam-fikir' ); ?>
        </button>
    </p>

    <?php
    // Provide a hidden template row used by JS to clone new rows on the client
    echo '<script type="text/html" id="langgam-fikir-faq-template">';
    langgam_fikir_render_faq_row( '__INDEX__', '', '' );
    echo '</script>';
}

/**
 * Render a single FAQ row (Q + A).
 *
 * @param int|string $index Row index. Use __INDEX__ for the JS template row.
 * @param string     $question The stored question.
 * @param string     $answer The stored answer.
 */
function langgam_fikir_render_faq_row( $index, $question = '', $answer = '' ) {
    ?>
    <div class="langgam-fikir-faq-row" data-faq-index="<?php echo esc_attr( $index ); ?>">
        <div class="langgam-fikir-faq-row-header">
            <span class="langgam-fikir-faq-row-handle dashicons dashicons-menu" aria-hidden="true"></span>
            <strong class="langgam-fikir-faq-row-title">
                <?php
                printf(
                    /* translators: %s: FAQ row number */
                    esc_html__( 'Question %s', 'langgam-fikir' ),
                    is_numeric( $index ) ? esc_html( (int) $index + 1 ) : '#'
                );
                ?>
            </strong>
            <button type="button" class="button-link langgam-fikir-faq-remove" aria-label="<?php esc_attr_e( 'Remove this question', 'langgam-fikir' ); ?>">
                <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                <?php esc_html_e( 'Remove', 'langgam-fikir' ); ?>
            </button>
        </div>

        <div class="langgam-fikir-faq-row-body">
            <p>
                <label for="langgam_fikir_faq_<?php echo esc_attr( $index ); ?>_question">
                    <strong><?php esc_html_e( 'Question', 'langgam-fikir' ); ?></strong>
                </label>
                <input type="text"
                       id="langgam_fikir_faq_<?php echo esc_attr( $index ); ?>_question"
                       name="langgam_fikir_faq[<?php echo esc_attr( $index ); ?>][question]"
                       value="<?php echo esc_attr( $question ); ?>"
                       class="widefat"
                       placeholder="<?php esc_attr_e( 'e.g., What is the print run for this title?', 'langgam-fikir' ); ?>">
            </p>
            <p>
                <label for="langgam_fikir_faq_<?php echo esc_attr( $index ); ?>_answer">
                    <strong><?php esc_html_e( 'Answer', 'langgam-fikir' ); ?></strong>
                </label>
                <textarea id="langgam_fikir_faq_<?php echo esc_attr( $index ); ?>_answer"
                          name="langgam_fikir_faq[<?php echo esc_attr( $index ); ?>][answer]"
                          rows="4"
                          class="widefat"
                          placeholder="<?php esc_attr_e( 'Provide a clear, direct answer. Plain text only — HTML will be stripped on save.', 'langgam-fikir' ); ?>"><?php echo esc_textarea( $answer ); ?></textarea>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Save the FAQ meta box data.
 *
 * @param int $post_id Post ID.
 */
function langgam_fikir_save_faq_meta( $post_id ) {
    // Bail on autosave / revisions / missing nonce
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }
    if ( ! isset( $_POST['langgam_fikir_faq_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['langgam_fikir_faq_nonce'] ) ), 'langgam_fikir_faq_meta' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( empty( $_POST['langgam_fikir_faq'] ) || ! is_array( $_POST['langgam_fikir_faq'] ) ) {
        delete_post_meta( $post_id, '_langgam_fikir_faq' );
        return;
    }

    $clean = array();
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — values sanitised individually below
    $raw = wp_unslash( $_POST['langgam_fikir_faq'] );

    foreach ( $raw as $entry ) {
        if ( ! is_array( $entry ) ) {
            continue;
        }
        $q = isset( $entry['question'] ) ? sanitize_text_field( $entry['question'] ) : '';
        $a = isset( $entry['answer'] ) ? sanitize_textarea_field( $entry['answer'] ) : '';

        // Skip rows where either field is empty
        if ( $q === '' || $a === '' ) {
            continue;
        }

        $clean[] = array(
            'question' => $q,
            'answer'   => $a,
        );
    }

    if ( empty( $clean ) ) {
        delete_post_meta( $post_id, '_langgam_fikir_faq' );
    } else {
        update_post_meta( $post_id, '_langgam_fikir_faq', $clean );
    }
}
add_action( 'save_post_book', 'langgam_fikir_save_faq_meta' );
add_action( 'save_post_page', 'langgam_fikir_save_faq_meta' );
