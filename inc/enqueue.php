<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package Langgam_Fikir
 * @since 1.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Parent and Child Theme Styles
 */
function langgam_fikir_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style( 
        'twentytwentyfive-style', 
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->parent()->get('Version')
    );
    
    // Enqueue child theme stylesheet
    wp_enqueue_style( 
        'langgam-fikir-style', 
        get_stylesheet_uri(),
        array( 'twentytwentyfive-style' ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'langgam_fikir_enqueue_styles', 15 );

/**
 * Enqueue Child Theme Assets (CSS and JavaScript)
 */
function langgam_fikir_enqueue_assets() {
    // Self-hosted fonts (GDPR compliant — no external requests)
    wp_enqueue_style( 
        'langgam-fikir-fonts', 
        get_stylesheet_directory_uri() . '/assets/css/fonts.css',
        array(),
        filemtime( get_stylesheet_directory() . '/assets/css/fonts.css' )
    );
    
    // Licensed fonts: Sabon Next LT, Kagnue, Ferments (only when the files are present, so a
    // copy of the theme without them falls back to Crimson Text without any 404s).
    $main_deps = array( 'langgam-fikir-style', 'langgam-fikir-fonts' );
    if ( file_exists( get_stylesheet_directory() . '/assets/fonts/sabon-next-lt-regular.woff2' ) ) {
        wp_enqueue_style(
            'langgam-fikir-fonts-premium',
            get_stylesheet_directory_uri() . '/assets/css/fonts-premium.css',
            array( 'langgam-fikir-fonts' ),
            filemtime( get_stylesheet_directory() . '/assets/css/fonts-premium.css' )
        );
        $main_deps[] = 'langgam-fikir-fonts-premium';
    }
    
    // Main CSS file
    wp_enqueue_style( 
        'langgam-fikir-main', 
        get_stylesheet_directory_uri() . '/assets/css/main.css',
        $main_deps,
        filemtime( get_stylesheet_directory() . '/assets/css/main.css' )
    );
    
    // Design layer: palettes, corner styles, font roles (variable overrides, chosen in Theme Options > Design)
    wp_enqueue_style(
        'langgam-fikir-design',
        get_stylesheet_directory_uri() . '/assets/css/design.css',
        array( 'langgam-fikir-main' ),
        filemtime( get_stylesheet_directory() . '/assets/css/design.css' )
    );
    $custom_colours = langgam_fikir_custom_colour_css();
    if ( $custom_colours ) {
        wp_add_inline_style( 'langgam-fikir-design', $custom_colours );
    }
    
    // Additional CSS for forms and extra pages (only where needed).
    // Match by assigned template first (Template Name templates are slug-independent),
    // then by slug for pages that pick up page-{slug}.php automatically.
    if ( is_page_template( array( 'page-contact.php', 'page-about.php', 'page-privacy.php' ) )
        || is_page( array( 'contact', 'about', 'privacy-policy' ) ) ) {
        wp_enqueue_style( 
            'langgam-fikir-additional', 
            get_stylesheet_directory_uri() . '/assets/css/additional.css',
            array( 'langgam-fikir-design' ),
            filemtime( get_stylesheet_directory() . '/assets/css/additional.css' )
        );
    }
    
    // Featured showcase assets (front page only, when enabled)
    if ( is_front_page() && langgam_fikir_get_option( 'showcase_enabled' ) ) {
        wp_enqueue_style(
            'langgam-fikir-showcase',
            get_stylesheet_directory_uri() . '/assets/css/showcase.css',
            array( 'langgam-fikir-design' ),
            filemtime( get_stylesheet_directory() . '/assets/css/showcase.css' )
        );
        
        wp_enqueue_script(
            'langgam-fikir-showcase',
            get_stylesheet_directory_uri() . '/assets/js/showcase.js',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/js/showcase.js' ),
            true
        );
    }
    
    // Main JavaScript
    wp_enqueue_script(
        'langgam-fikir-main',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        array(),
        filemtime( get_stylesheet_directory() . '/assets/js/main.js' ),
        true
    );
    
    // Add localized data for JavaScript
    wp_localize_script( 'langgam-fikir-main', 'langgamFikir', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'langgam_fikir_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'langgam_fikir_enqueue_assets', 20 );

/**
 * Enqueue Admin Styles and Scripts
 */
function langgam_fikir_enqueue_admin_assets( $hook ) {
    // Only load on post edit pages
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }
    
    // Get current screen to check post type
    $screen = get_current_screen();
    
    // Load admin CSS for pages and books
    if ( $screen && in_array( $screen->post_type, array( 'page', 'book' ), true ) ) {
        // Enqueue admin CSS
        wp_enqueue_style(
            'langgam-fikir-admin',
            get_stylesheet_directory_uri() . '/assets/css/admin.css',
            array(),
            filemtime( get_stylesheet_directory() . '/assets/css/admin.css' )
        );
        
        // Enqueue admin JavaScript on both — needed for About logo (pages) AND FAQ meta box (both)
        // Media uploader is only needed for pages.
        $deps = array( 'jquery', 'jquery-ui-sortable' );
        if ( 'page' === $screen->post_type ) {
            wp_enqueue_media();
            $deps[] = 'media-upload';
        }
        
        wp_enqueue_script(
            'langgam-fikir-admin',
            get_stylesheet_directory_uri() . '/assets/js/admin.js',
            $deps,
            filemtime( get_stylesheet_directory() . '/assets/js/admin.js' ),
            true
        );
    }
}
add_action( 'admin_enqueue_scripts', 'langgam_fikir_enqueue_admin_assets' );
