<?php
/**
 * Google preferred sources
 *
 * Readers who add the site as a "preferred source" in Google Search see its articles first in
 * Top Stories, marked "Preferred", and in AI Mode and AI Overviews. This puts an "Add as a
 * preferred source on Google" button in the theme's own look:
 *   - a card after articles (and optionally book pages), a small button in the footer, and
 *     the [preferred_source] shortcode;
 *   - styled from the theme's colour, font and corner tokens (assets/css/preferred-source.css);
 *   - Google's advanced implementation with custom design: publisher.js is loaded with
 *     preferred-sources-control="manual" and the button calls addPreferredSource(), so readers
 *     add the site without leaving the page (assets/js/preferred-source.js);
 *   - the button is also a plain link to Google's source preferences page (the deeplink), so it
 *     works before the script loads, when it is blocked, and without JavaScript.
 *
 * Google's script is loaded only on pages that show the button, and only when the reader
 * scrolls near it, or never in link-only mode.
 * Settings: Theme Options > Social & SEO > Google Preferred Sources.
 *
 * Eligibility: only domain and subdomain sites (https://example.com/, https://news.example.com/)
 * can be preferred sources, not a site in a subfolder.
 *
 * @package Langgam_Fikir
 * @since 2.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The site's domain as Google's source preferences tool expects it (example.com, or
 * news.example.com for a subdomain). A leading www. is dropped.
 */
function langgam_fikir_gps_domain() {
    $host = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
    return preg_replace( '/^www\./', '', $host );
}

/**
 * Whether the site can be a preferred source: domain or subdomain level, not a subfolder.
 */
function langgam_fikir_gps_eligible() {
    $path = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
    return '' === $path && '' !== langgam_fikir_gps_domain();
}

/**
 * Google's source preferences page for this site.
 */
function langgam_fikir_gps_deeplink() {
    return 'https://www.google.com/preferences/source?q=' . rawurlencode( langgam_fikir_gps_domain() );
}

/**
 * Whether the button belongs on this kind of view: post, book, footer.
 */
function langgam_fikir_gps_enabled_for( $where ) {
    if ( ! langgam_fikir_get_option( 'gps_enabled', 0 ) || ! langgam_fikir_gps_eligible() ) {
        return false;
    }
    $defaults = array( 'post' => 1, 'book' => 0, 'footer' => 1 );
    return (bool) langgam_fikir_get_option( 'gps_on_' . $where . ( 'footer' === $where ? '' : 's' ), isset( $defaults[ $where ] ) ? $defaults[ $where ] : 0 );
}

/**
 * Editable text with {site} replaced by the site name.
 */
function langgam_fikir_gps_text( $key ) {
    return str_replace( '{site}', get_bloginfo( 'name' ), langgam_fikir_text( $key ) );
}

/**
 * Remembers that a button was printed, so the script and stylesheet load only then.
 */
function langgam_fikir_gps_used( $set = null ) {
    static $used = false;
    if ( null !== $set ) {
        $used = (bool) $set;
    }
    return $used;
}

/**
 * The button (a link that the script upgrades to Google's in-page flow).
 *
 * @param string $context card | footer | inline.
 */
function langgam_fikir_gps_button( $context = 'card' ) {
    langgam_fikir_gps_used( true );
    langgam_fikir_gps_enqueue();

    // Google's "G", in Google's colours, as on Google's own preferred-sources button. On the
    // filled button it sits on a white disc so the colours stay true on any palette.
    $icon = '<span class="lf-gps__mark" aria-hidden="true"><svg class="lf-gps__icon" width="18" height="18" viewBox="0 0 48 48" focusable="false">'
        . '<path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>'
        . '<path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>'
        . '<path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>'
        . '<path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>'
        . '</svg></span>';

    return sprintf(
        '<a class="lf-gps__btn lf-gps__btn--%1$s" href="%2$s" target="_blank" rel="noopener" data-lf-preferred-source>%3$s<span>%4$s</span><span class="screen-reader-text"> %5$s</span></a>',
        esc_attr( $context ),
        esc_url( langgam_fikir_gps_deeplink() ),
        $icon,
        esc_html( langgam_fikir_gps_text( 'gps_label' ) ),
        esc_html__( '(opens Google)', 'langgam-fikir' )
    );
}

/**
 * The card: heading, one line and the button.
 */
function langgam_fikir_gps_card() {
    $id      = 'lf-gps-' . wp_unique_id();
    $heading = langgam_fikir_gps_text( 'gps_heading' );
    $text    = langgam_fikir_gps_text( 'gps_text' );

    $html  = '<aside class="lf-gps lf-gps--card"' . ( '' !== $heading ? ' aria-labelledby="' . esc_attr( $id ) . '"' : ' aria-label="' . esc_attr__( 'Google preferred source', 'langgam-fikir' ) . '"' ) . '>';
    $html .= '<div class="lf-gps__body">';
    if ( '' !== $heading ) {
        $html .= '<p class="lf-gps__heading" id="' . esc_attr( $id ) . '">' . esc_html( $heading ) . '</p>';
    }
    if ( '' !== $text ) {
        $html .= '<p class="lf-gps__text">' . esc_html( $text ) . '</p>';
    }
    $html .= '</div>' . langgam_fikir_gps_button( 'card' ) . '</aside>';
    return $html;
}

/**
 * Template tag: the card after an article or a book.
 *
 * @param string $kind post | book.
 */
function langgam_fikir_render_preferred_source( $kind = 'post' ) {
    if ( langgam_fikir_gps_enabled_for( $kind ) ) {
        echo langgam_fikir_gps_card(); // phpcs:ignore WordPress.Security.EscapeOutput -- built and escaped above
    }
}

/**
 * Template tag: the small button in the footer, under the profile icons.
 */
function langgam_fikir_render_preferred_source_footer() {
    if ( langgam_fikir_gps_enabled_for( 'footer' ) ) {
        echo '<div class="lf-gps lf-gps--footer">' . langgam_fikir_gps_button( 'footer' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built and escaped above
    }
}

/**
 * [preferred_source] or [preferred_source style="button"] anywhere in content.
 */
function langgam_fikir_gps_shortcode( $atts = array() ) {
    $atts = shortcode_atts( array( 'style' => 'card' ), $atts, 'preferred_source' );
    if ( ! langgam_fikir_get_option( 'gps_enabled', 0 ) || ! langgam_fikir_gps_eligible() ) {
        return '';
    }
    return 'button' === $atts['style']
        ? '<span class="lf-gps lf-gps--inline">' . langgam_fikir_gps_button( 'inline' ) . '</span>'
        : langgam_fikir_gps_card();
}
add_shortcode( 'preferred_source', 'langgam_fikir_gps_shortcode' );

/* =========================================================================
 * Assets
 * ====================================================================== */

/**
 * Styles and script, the first time a button is printed (in the footer if that comes late).
 */
function langgam_fikir_gps_enqueue() {
    static $done = false;
    if ( $done ) {
        return;
    }
    $done = true;

    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    wp_enqueue_style( 'langgam-fikir-preferred-source', $uri . '/assets/css/preferred-source.css', array( 'langgam-fikir-main' ), filemtime( $dir . '/assets/css/preferred-source.css' ) );
    wp_enqueue_script( 'langgam-fikir-preferred-source', $uri . '/assets/js/preferred-source.js', array(), filemtime( $dir . '/assets/js/preferred-source.js' ), true );

    $lang = strtolower( substr( (string) get_locale(), 0, 2 ) );
    wp_localize_script( 'langgam-fikir-preferred-source', 'lfPreferredSource', array(
        'load'  => 'link' === langgam_fikir_get_option( 'gps_script', 'near' ) ? 'link' : 'near',
        'lang'  => $lang ? $lang : 'en',
        'src'   => 'https://news.google.com/swg/js/v1/publisher.js',
    ) );
}

/**
 * Load the stylesheet in the head on views that will show the card, so it does not flash.
 */
function langgam_fikir_gps_head_assets() {
    if ( ( is_singular( 'post' ) && langgam_fikir_gps_enabled_for( 'post' ) )
        || ( is_singular( 'book' ) && langgam_fikir_gps_enabled_for( 'book' ) )
        || langgam_fikir_gps_enabled_for( 'footer' )
        || ( is_singular() && langgam_fikir_get_option( 'gps_enabled', 0 ) && has_shortcode( (string) get_post_field( 'post_content', get_queried_object_id() ), 'preferred_source' ) ) ) {
        langgam_fikir_gps_enqueue();
    }
}
add_action( 'wp_enqueue_scripts', 'langgam_fikir_gps_head_assets', 40 );

/* =========================================================================
 * Privacy and admin
 * ====================================================================== */

/**
 * Suggested privacy policy text (Settings > Privacy).
 */
function langgam_fikir_gps_privacy_text() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
        return;
    }
    wp_add_privacy_policy_content(
        __( 'Langgam Fikir theme: Google preferred sources', 'langgam-fikir' ),
        '<p>' . esc_html__( 'Some pages show an "Add as a preferred source on Google" button. When you scroll near it, your browser loads a script from news.google.com so the button can add this site to your Google Search preferences without leaving the page. Google receives your IP address and browser details as with any request to Google, and, if you use the button, records your choice in your Google account. Nothing is stored on this site.', 'langgam-fikir' ) . '</p>'
    );
}
add_action( 'admin_init', 'langgam_fikir_gps_privacy_text' );

/**
 * Status for Theme Options: eligibility, the address readers are sent to, and where to check.
 */
function langgam_fikir_gps_status_html() {
    $lines = array();
    if ( ! langgam_fikir_gps_eligible() ) {
        $lines[] = '<span class="lf-status-off">' . sprintf(
            /* translators: %s: site address */
            esc_html__( 'This site lives in a subfolder (%s). Google accepts only domain and subdomain sites as preferred sources, so the button stays hidden.', 'langgam-fikir' ),
            '<code>' . esc_html( home_url( '/' ) ) . '</code>'
        ) . '</span>';
    } else {
        $lines[] = '<span class="lf-status-ok">' . esc_html__( 'Eligible:', 'langgam-fikir' ) . '</span> <code>' . esc_html( langgam_fikir_gps_domain() ) . '</code>';
        $lines[] = sprintf(
            /* translators: %s: link to Google's source preferences page */
            esc_html__( 'Check that Google lists the site: open %s. If it is not found, the button would lead nowhere, so leave it off for now.', 'langgam-fikir' ),
            '<a href="' . esc_url( langgam_fikir_gps_deeplink() ) . '" target="_blank" rel="noopener">' . esc_html__( 'the source preferences page for this site', 'langgam-fikir' ) . '</a>'
        );
        $lines[] = esc_html__( 'To be highlighted in AI Mode and AI Overviews too, the site must be included in Search generative AI features in Google Search Console.', 'langgam-fikir' );
    }
    return '<p>' . implode( '</p><p>', $lines ) . '</p>';
}
