<?php
/**
 * Design: palettes, fonts, corners, motion and the hero photo
 *
 * Front-end side of Theme Options > Design. Everything here is class- and variable-based:
 * the chosen options become body classes (palette, corners, fonts, parallax) and, for
 * individually picked colours, one small block of CSS custom properties. Components only
 * read variables, so no template or component stylesheet needs to know the choice.
 * Dark mode is out of scope (ssot.md 9.2): every palette is light.
 *
 * @package Langgam_Fikir
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Body classes for the design choices. Defaults add no class (they are the base CSS).
 */
function langgam_fikir_design_body_classes( $classes ) {
    $opt = static function ( $key ) {
        return (string) langgam_fikir_get_option( $key );
    };

    $scheme = $opt( 'color_scheme' );
    if ( isset( langgam_fikir_color_schemes()[ $scheme ] ) && 'earth' !== $scheme ) {
        $classes[] = 'lf-scheme-' . $scheme;
    }

    $corners = $opt( 'corner_style' );
    if ( in_array( $corners, array( 'sharp', 'round' ), true ) ) {
        $classes[] = 'lf-corners-' . $corners;
    }

    $parallax = $opt( 'parallax' );
    $classes[] = 'off' === $parallax ? 'lf-parallax-off' : ( 'all' === $parallax ? 'lf-parallax-all' : 'lf-parallax-desktop' );

    foreach ( array(
        'font_heading' => array( 'crimson', 'lf-heading-' ),
        'font_reading' => array( 'sabon', 'lf-reading-' ),
        'font_body'    => array( 'inter', 'lf-body-' ),
        'font_titles'  => array( 'kagnue', 'lf-titles-' ),
        'font_tagline' => array( 'ferments', 'lf-tagline-' ),
    ) as $key => $map ) {
        $value = $opt( $key );
        if ( '' !== $value && $value !== $map[0] ) {
            $classes[] = $map[1] . sanitize_html_class( $value );
        }
    }

    if ( '' !== langgam_fikir_custom_colour_css() ) {
        $classes[] = 'lf-has-custom-colours';
    }

    return $classes;
}
add_filter( 'body_class', 'langgam_fikir_design_body_classes' );

/**
 * Black or white-ish text, whichever reads better on the given background (WCAG contrast).
 */
function langgam_fikir_contrast_text( $hex ) {
    $hex = ltrim( (string) sanitize_hex_color( $hex ), '#' );
    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if ( 6 !== strlen( $hex ) ) {
        return '#FFFFFF';
    }

    $lum = 0;
    foreach ( array( 0.2126, 0.7152, 0.0722 ) as $i => $weight ) {
        $channel = hexdec( substr( $hex, $i * 2, 2 ) ) / 255;
        $channel = $channel <= 0.03928 ? $channel / 12.92 : pow( ( $channel + 0.055 ) / 1.055, 2.4 );
        $lum    += $weight * $channel;
    }

    // Contrast against white vs. against the dark text colour #2A2118 (luminance about 0.017).
    return ( 1.05 / ( $lum + 0.05 ) ) >= ( ( $lum + 0.05 ) / 0.067 ) ? '#FFFFFF' : '#2A2118';
}

/**
 * CSS custom properties for the colours picked individually in Theme Options > Design.
 * Empty string when none was picked.
 */
function langgam_fikir_custom_colour_css() {
    static $css = null;
    if ( null !== $css ) {
        return $css;
    }

    $vars = array();
    $pick = static function ( $key ) {
        return sanitize_hex_color( (string) langgam_fikir_get_option( $key ) );
    };

    if ( $primary = $pick( 'color_primary' ) ) {
        $vars['--color-primary'] = $primary;
        $vars['--color-bg-hero'] = $primary;
        $vars['--color-on-hero'] = langgam_fikir_contrast_text( $primary );
    }
    if ( $accent = $pick( 'color_accent' ) ) {
        $vars['--color-accent']      = $accent;
        $vars['--color-secondary']   = $accent;
        $vars['--color-accent-dark'] = 'color-mix(in srgb, ' . $accent . ' 78%, #000)';
        $vars['--color-on-accent']   = langgam_fikir_contrast_text( $accent );
    }
    if ( $background = $pick( 'color_background' ) ) {
        $vars['--color-bg-primary'] = $background;
    }
    if ( $surface = $pick( 'color_surface' ) ) {
        $vars['--color-bg-secondary'] = $surface;
        $vars['--color-bg-tint']      = $surface;
        $vars['--color-bg-muted']     = 'color-mix(in srgb, ' . $surface . ' 85%, var(--color-text))';
    }
    if ( $text = $pick( 'color_text' ) ) {
        $vars['--color-text']       = $text;
        $vars['--color-text-light'] = 'color-mix(in srgb, ' . $text . ' 78%, var(--color-bg-primary))';
        $vars['--color-text-muted'] = 'color-mix(in srgb, ' . $text . ' 60%, var(--color-bg-primary))';
        $vars['--color-border']     = 'color-mix(in srgb, ' . $text . ' 16%, var(--color-bg-primary))';
    }

    if ( ! $vars ) {
        return $css = '';
    }

    $css = 'body.lf-has-custom-colours{';
    foreach ( $vars as $name => $value ) {
        $css .= $name . ':' . $value . ';';
    }
    return $css .= '}';
}

/**
 * The hero photo to show: URLs of a 1920px and a 960px version, or null.
 * A picture chosen in Theme Options > Homepage > Hero Background Image wins over the bundled photos.
 *
 * @return array{full:string, small:string}|null
 */
function langgam_fikir_hero_photo_urls() {
    $uploaded = (int) langgam_fikir_get_option( 'hero_background_image' );
    if ( $uploaded ) {
        return null; // front-page.php renders the uploaded image itself.
    }

    $photos = langgam_fikir_hero_photos();
    $choice = (string) langgam_fikir_get_option( 'hero_photo' );
    if ( 'random' === $choice ) {
        $choice = array_rand( $photos );
    }
    if ( ! isset( $photos[ $choice ] ) ) {
        return null;
    }

    $path = LANGGAM_FIKIR_DIR . '/assets/images/hero/' . $choice;
    if ( ! file_exists( $path . '-1920.jpg' ) ) {
        return null;
    }

    return array(
        'full'  => LANGGAM_FIKIR_URI . '/assets/images/hero/' . $choice . '-1920.jpg',
        'small' => LANGGAM_FIKIR_URI . '/assets/images/hero/' . $choice . '-960.jpg',
    );
}
