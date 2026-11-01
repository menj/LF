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
