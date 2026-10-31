<?php
/**
 * Theme Setup Functions
 *
 * @package Langgam_Fikir
 * @since 1.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme Setup
 */
function langgam_fikir_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails
    add_theme_support( 'post-thumbnails' );
    
    // Set custom image sizes
    add_image_size( 'book-cover', 400, 533, true ); // 3:4 ratio
    add_image_size( 'book-thumbnail', 280, 373, true );
    add_image_size( 'book-showcase', 1600, 900, true ); // Full-bleed showcase background
    
    // Register navigation menus
    register_nav_menus( array(
        'primary'      => __( 'Primary Menu', 'langgam-fikir' ),
        'footer'       => __( 'Footer Menu', 'langgam-fikir' ),
        'footer-legal' => __( 'Footer Legal Menu', 'langgam-fikir' ),
    ) );

    // Switch default core markup to output valid HTML5
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Add theme support for selective refresh for widgets
    add_theme_support( 'customize-selective-refresh-widgets' );
    
    // Add support for custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
}
add_action( 'after_setup_theme', 'langgam_fikir_setup' );

/**
 * Set content width
 */
function langgam_fikir_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'langgam_fikir_content_width', 1200 );
}
add_action( 'after_setup_theme', 'langgam_fikir_content_width', 0 );

/**
 * Modify posts per page for blog/resources archive
 */
function langgam_fikir_modify_posts_per_page( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_home() ) {
        $query->set( 'posts_per_page', 6 );
    }
}
add_action( 'pre_get_posts', 'langgam_fikir_modify_posts_per_page' );

/**
 * Resource hints
 *
 * The homepage About image is the LCP element on mobile. When it is the
 * hardcoded Unsplash fallback (no featured image on the About page), warm
 * the connection to that origin from the <head>.
 *
 * @since 2.9.8
 */
function langgam_fikir_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' !== $relation_type || ! is_front_page() ) {
        return $urls;
    }

    $about_page = get_page_by_path( 'about-langgam-fikir' );
    if ( ! $about_page || ! has_post_thumbnail( $about_page->ID ) ) {
        $urls[] = 'https://images.unsplash.com';
    }

    return $urls;
}
add_filter( 'wp_resource_hints', 'langgam_fikir_resource_hints', 10, 2 );

/**
 * Keep the custom logo off fetchpriority="high"
 *
 * WordPress gives the first sizeable image on the page high priority, which
 * on this theme is the small header logo. Leave that slot for the real LCP
 * image instead.
 *
 * @since 2.9.8
 */
function langgam_fikir_custom_logo_attributes( $attr ) {
    $attr['fetchpriority'] = 'auto';
    return $attr;
}
add_filter( 'get_custom_logo_image_attributes', 'langgam_fikir_custom_logo_attributes' );
