<?php
/**
 * Template Hooks and Filters
 *
 * @package Langgam_Fikir
 * @since 1.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Customize excerpt length
 */
function langgam_fikir_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'langgam_fikir_excerpt_length', 999 );

/**
 * Customize excerpt more text
 */
function langgam_fikir_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'langgam_fikir_excerpt_more' );

/**
 * Strip inline styles from block editor headings so child theme CSS applies
 * Twenty Twenty-Five injects inline styles on .wp-block-heading elements
 * that override child theme stylesheets regardless of specificity.
 */
function langgam_fikir_clean_block_heading_styles( $content ) {
    if ( empty( $content ) ) {
        return $content;
    }
    $content = preg_replace(
        '/<(h[1-6])([^>]*)\s+style="[^"]*"([^>]*)>/i',
        '<$1$2$3>',
        $content
    );
    return $content;
}
add_filter( 'the_content', 'langgam_fikir_clean_block_heading_styles', 999 );

/**
 * Fallback for the Footer Legal menu when no menu is assigned.
 * Outputs links to Privacy Policy and a placeholder Terms of Service page.
 */
function lf_footer_legal_fallback() {
    $privacy_page = get_privacy_policy_url();
    echo '<ul id="footer-legal-menu" class="menu">';
    if ( $privacy_page ) {
        echo '<li><a href="' . esc_url( $privacy_page ) . '">' . esc_html__( 'Privacy Policy', 'langgam-fikir' ) . '</a></li>';
    }
    echo '<li><a href="' . esc_url( home_url( '/terms-of-service/' ) ) . '">' . esc_html__( 'Terms of Service', 'langgam-fikir' ) . '</a></li>';
    echo '</ul>';
}

/**
 * Descriptive alt text for book covers (Google SEO starter guide: "use the alt attribute").
 *
 * When a cover's alt text is empty, looks like a file name ("IMG_2041", "cover-final-2"), or is
 * just the title retyped in lower case, it becomes "Cover of {Book Title}". Alt text an editor
 * wrote by hand is kept.
 *
 * @since 2.14.0
 */
function langgam_fikir_book_cover_alt( $attr, $attachment ) {
    if ( ! $attachment instanceof WP_Post ) {
        return $attr;
    }

    $book_id = 0;
    $post    = get_post();
    if ( $post && 'book' === $post->post_type && (int) get_post_thumbnail_id( $post ) === (int) $attachment->ID ) {
        $book_id = (int) $post->ID;
    } elseif ( $attachment->post_parent && 'book' === get_post_type( $attachment->post_parent ) && (int) get_post_thumbnail_id( $attachment->post_parent ) === (int) $attachment->ID ) {
        $book_id = (int) $attachment->post_parent;
    }
    if ( ! $book_id ) {
        return $attr;
    }

    $title = wp_strip_all_tags( get_the_title( $book_id ) );
    $alt   = isset( $attr['alt'] ) ? trim( (string) $attr['alt'] ) : '';

    $looks_like_file = '' !== $alt && ( preg_match( '/\.(jpe?g|png|webp|avif|gif)$/i', $alt ) || ( false === strpos( $alt, ' ' ) && preg_match( '/[-_\d]/', $alt ) ) );
    $retyped_title   = '' !== $alt && 0 === strcasecmp( $alt, $title ) && $alt !== $title;

    if ( '' === $alt || $looks_like_file || $retyped_title ) {
        /* translators: %s: book title */
        $attr['alt'] = sprintf( __( 'Cover of %s', 'langgam-fikir' ), $title );
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'langgam_fikir_book_cover_alt', 10, 2 );
