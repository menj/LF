<?php
/**
 * Text justification
 *
 * Built-in replacement for the Auto Justify Content plugin (by MENJ, GPLv2+): justified
 * paragraphs and lists with the last line aligned to the start, optional hyphenation, a
 * wide-screens-only switch, an "any article text" fallback and selectors to leave alone.
 * Settings: Theme Options > Design > Text Justification. The plugin's own ajc_* options
 * are copied there once (inc/plugin-takeover.php), so an upgraded site keeps its choices.
 *
 * The rules are static in assets/css/justify.css and switched on by body classes; only the
 * excluded selectors, which are typed in Theme Options, are added inline.
 *
 * @package Langgam_Fikir
 * @since 2.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Whether justification applies to the current front-end view.
 */
function langgam_fikir_justify_applies() {
    if ( is_admin() || ! langgam_fikir_get_option( 'justify_enabled', 1 ) ) {
        return false;
    }
    if ( 'entire_site' === langgam_fikir_get_option( 'justify_scope', 'blog_only' ) ) {
        return true;
    }
    return is_singular( 'post' );
}

/**
 * Body classes that switch the rules in justify.css on.
 */
function langgam_fikir_justify_body_classes( $classes ) {
    if ( ! langgam_fikir_justify_applies() ) {
        return $classes;
    }
    $classes[] = 'lf-justify';
    if ( 'entire_site' === langgam_fikir_get_option( 'justify_scope', 'blog_only' ) ) {
        $classes[] = 'lf-justify-site';
    }
    if ( langgam_fikir_get_option( 'justify_hyphens', 1 ) ) {
        $classes[] = 'lf-justify-hyphens';
    }
    if ( langgam_fikir_get_option( 'justify_desktop', 0 ) ) {
        $classes[] = 'lf-justify-desktop';
    }
    if ( langgam_fikir_get_option( 'justify_fallback', 0 ) ) {
        $classes[] = 'lf-justify-fallback';
    }
    return $classes;
}
add_filter( 'body_class', 'langgam_fikir_justify_body_classes' );

/**
 * Clean a comma-separated selector list (same rules as the plugin: no braces, no angle
 * brackets, no semicolons, so nothing typed here can break out of the rule).
 */
function langgam_fikir_justify_clean_selectors( $value ) {
    $value = preg_replace( '/\s+/', ' ', str_replace( array( "\r", "\n", "\t" ), ' ', (string) $value ) );
    $value = preg_replace( '/[^a-zA-Z0-9\.\#\-\_\s\,\:\[\]\=\(\)\"\'\*\>\+\~]/', '', trim( $value ) );
    $parts = array_filter( array_map( 'trim', explode( ',', $value ) ) );
    return implode( ', ', $parts );
}

/**
 * Stylesheet, plus the excluded selectors as one small inline rule.
 */
function langgam_fikir_justify_assets() {
    if ( ! langgam_fikir_justify_applies() ) {
        return;
    }

    wp_enqueue_style(
        'langgam-fikir-justify',
        get_stylesheet_directory_uri() . '/assets/css/justify.css',
        array( 'langgam-fikir-main' ),
        filemtime( get_stylesheet_directory() . '/assets/css/justify.css' )
    );

    $exclude = langgam_fikir_justify_clean_selectors( langgam_fikir_text( 'justify_exclude' ) );
    if ( '' === $exclude ) {
        return;
    }

    // One rule per selector: a browser drops a whole selector list if any one entry is invalid,
    // so a typo in one exclusion must not cancel the others.
    $css = '';
    foreach ( explode( ', ', $exclude ) as $selector ) {
        $css .= 'body.lf-justify ' . $selector . ',body.lf-justify ' . $selector . ' :is(p,li){text-align:inherit!important;text-align-last:auto!important;text-justify:auto!important;-webkit-hyphens:manual!important;hyphens:manual!important}';
    }
    wp_add_inline_style( 'langgam-fikir-justify', $css );
}
add_action( 'wp_enqueue_scripts', 'langgam_fikir_justify_assets', 30 );

/**
 * Copy the plugin's ajc_* options into Theme Options (called once by plugin-takeover.php).
 *
 * @return array Theme option values to merge.
 */
function langgam_fikir_justify_import_plugin_settings() {
    $map = array(
        'ajc_enabled'  => 'justify_enabled',
        'ajc_hyphen'   => 'justify_hyphens',
        'ajc_mobile'   => 'justify_desktop',
        'ajc_fallback' => 'justify_fallback',
    );
    $values = array();
    foreach ( $map as $from => $to ) {
        $stored = get_option( $from, null );
        if ( null !== $stored ) {
            $values[ $to ] = rest_sanitize_boolean( $stored ) ? 1 : 0;
        }
    }

    $scope = get_option( 'ajc_scope', null );
    if ( in_array( $scope, array( 'blog_only', 'entire_site' ), true ) ) {
        $values['justify_scope'] = $scope;
    }

    $exclude = get_option( 'ajc_exclude', null );
    if ( null !== $exclude ) {
        $values['justify_exclude'] = langgam_fikir_justify_clean_selectors( $exclude );
    }

    return $values;
}
