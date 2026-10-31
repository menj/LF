<?php
/**
 * Langgam Fikir Child Theme Functions
 * Child theme of Twenty Twenty-Five
 *
 * @package Langgam_Fikir
 * @since 1.0.0
 * @version 2.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Define Theme Constants
 */
define( 'LANGGAM_FIKIR_VERSION', '2.10.0' );
define( 'LANGGAM_FIKIR_DIR', get_stylesheet_directory() );
define( 'LANGGAM_FIKIR_URI', get_stylesheet_directory_uri() );

/**
 * Load Theme Core Files
 */
require_once LANGGAM_FIKIR_DIR . '/inc/setup.php';           // Theme setup & configuration
require_once LANGGAM_FIKIR_DIR . '/inc/enqueue.php';         // Scripts & styles enqueuing
require_once LANGGAM_FIKIR_DIR . '/inc/post-types.php';      // Custom post types & taxonomies
require_once LANGGAM_FIKIR_DIR . '/inc/meta-boxes.php';      // Meta boxes for custom fields
require_once LANGGAM_FIKIR_DIR . '/inc/template-functions.php'; // Template helper functions
require_once LANGGAM_FIKIR_DIR . '/inc/template-hooks.php';  // Template hooks & filters
require_once LANGGAM_FIKIR_DIR . '/inc/schema.php';           // Schema.org & social meta
require_once LANGGAM_FIKIR_DIR . '/inc/breadcrumbs.php';      // Visible breadcrumb component
require_once LANGGAM_FIKIR_DIR . '/inc/toc.php';              // Auto-TOC for long posts (Phase D)
require_once LANGGAM_FIKIR_DIR . '/inc/hrefmaster-integration.php'; // HrefMaster plugin integration (v2.7.0)
require_once LANGGAM_FIKIR_DIR . '/inc/webmcp.php';              // WebMCP agent tools + public catalogue REST (v2.10.0)

/**
 * Load Admin-Specific Files
 */
if ( is_admin() ) {
    require_once LANGGAM_FIKIR_DIR . '/inc/admin/meta-boxes.php'; // Admin meta box handling
    require_once LANGGAM_FIKIR_DIR . '/inc/admin/meta-boxes-faq.php'; // FAQ meta box (Phase C)
    require_once LANGGAM_FIKIR_DIR . '/inc/admin/meta-boxes-reviews.php'; // Reviews meta box (Phase D)
    require_once LANGGAM_FIKIR_DIR . '/inc/admin/meta-boxes-robots.php'; // Robots toggle (Phase D)
    require_once LANGGAM_FIKIR_DIR . '/inc/admin/theme-options.php'; // Theme Options page
}

/**
 * Load WooCommerce Support (if active)
 */
if ( class_exists( 'WooCommerce' ) ) {
    require_once LANGGAM_FIKIR_DIR . '/inc/woocommerce.php';
}
