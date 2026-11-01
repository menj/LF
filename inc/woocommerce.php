<?php
/**
 * WooCommerce Support and Customizations
 *
 * @package Langgam_Fikir
 * @since 1.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WooCommerce setup function
 */
function langgam_fikir_woocommerce_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'langgam_fikir_woocommerce_setup' );

/**
 * Disable WooCommerce default styles
 * Uncomment if you want to completely customize WooCommerce styles
 */
// add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Customize WooCommerce product columns
 */
function langgam_fikir_woocommerce_loop_columns() {
    return 3; // 3 products per row
}
add_filter( 'loop_shop_columns', 'langgam_fikir_woocommerce_loop_columns' );

/**
 * Customize products per page
 */
function langgam_fikir_woocommerce_products_per_page() {
    return 12; // 12 products per page
}
add_filter( 'loop_shop_per_page', 'langgam_fikir_woocommerce_products_per_page', 20 );
