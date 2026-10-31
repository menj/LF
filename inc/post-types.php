<?php
/**
 * Custom Post Types
 *
 * @package Langgam_Fikir
 * @since 1.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Type: Books
 */
function langgam_fikir_register_books_post_type() {
    $labels = array(
        'name'                  => _x( 'Books', 'Post Type General Name', 'langgam-fikir' ),
        'singular_name'         => _x( 'Book', 'Post Type Singular Name', 'langgam-fikir' ),
        'menu_name'             => __( 'Books', 'langgam-fikir' ),
        'name_admin_bar'        => __( 'Book', 'langgam-fikir' ),
        'archives'              => __( 'Book Archives', 'langgam-fikir' ),
        'attributes'            => __( 'Book Attributes', 'langgam-fikir' ),
        'parent_item_colon'     => __( 'Parent Book:', 'langgam-fikir' ),
        'all_items'             => __( 'All Books', 'langgam-fikir' ),
        'add_new_item'          => __( 'Add New Book', 'langgam-fikir' ),
        'add_new'               => __( 'Add New', 'langgam-fikir' ),
        'new_item'              => __( 'New Book', 'langgam-fikir' ),
        'edit_item'             => __( 'Edit Book', 'langgam-fikir' ),
        'update_item'           => __( 'Update Book', 'langgam-fikir' ),
        'view_item'             => __( 'View Book', 'langgam-fikir' ),
        'view_items'            => __( 'View Books', 'langgam-fikir' ),
        'search_items'          => __( 'Search Book', 'langgam-fikir' ),
        'not_found'             => __( 'Not found', 'langgam-fikir' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'langgam-fikir' ),
        'featured_image'        => __( 'Book Cover', 'langgam-fikir' ),
        'set_featured_image'    => __( 'Set book cover', 'langgam-fikir' ),
        'remove_featured_image' => __( 'Remove book cover', 'langgam-fikir' ),
        'use_featured_image'    => __( 'Use as book cover', 'langgam-fikir' ),
        'insert_into_item'      => __( 'Insert into book', 'langgam-fikir' ),
        'uploaded_to_this_item' => __( 'Uploaded to this book', 'langgam-fikir' ),
        'items_list'            => __( 'Books list', 'langgam-fikir' ),
        'items_list_navigation' => __( 'Books list navigation', 'langgam-fikir' ),
        'filter_items_list'     => __( 'Filter books list', 'langgam-fikir' ),
    );

    $args = array(
        'label'                 => __( 'Book', 'langgam-fikir' ),
        'description'           => __( 'Books published by Langgam Fikir', 'langgam-fikir' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'taxonomies'            => array( 'book-category', 'book-tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-book',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array( 'slug' => 'publications', 'with_front' => false ),
    );

    register_post_type( 'book', $args );
}
add_action( 'init', 'langgam_fikir_register_books_post_type', 0 );

/**
 * Register Custom Taxonomies for Books
 */
function langgam_fikir_register_book_taxonomies() {
    // Book Categories
    $category_labels = array(
        'name'              => _x( 'Book Categories', 'taxonomy general name', 'langgam-fikir' ),
        'singular_name'     => _x( 'Book Category', 'taxonomy singular name', 'langgam-fikir' ),
        'search_items'      => __( 'Search Book Categories', 'langgam-fikir' ),
        'all_items'         => __( 'All Book Categories', 'langgam-fikir' ),
        'parent_item'       => __( 'Parent Book Category', 'langgam-fikir' ),
        'parent_item_colon' => __( 'Parent Book Category:', 'langgam-fikir' ),
        'edit_item'         => __( 'Edit Book Category', 'langgam-fikir' ),
        'update_item'       => __( 'Update Book Category', 'langgam-fikir' ),
        'add_new_item'      => __( 'Add New Book Category', 'langgam-fikir' ),
        'new_item_name'     => __( 'New Book Category Name', 'langgam-fikir' ),
        'menu_name'         => __( 'Book Categories', 'langgam-fikir' ),
    );

    $category_args = array(
        'labels'            => $category_labels,
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'book-category' ),
    );

    register_taxonomy( 'book-category', array( 'book' ), $category_args );

    // Book Tags
    $tag_labels = array(
        'name'                       => _x( 'Book Tags', 'taxonomy general name', 'langgam-fikir' ),
        'singular_name'              => _x( 'Book Tag', 'taxonomy singular name', 'langgam-fikir' ),
        'search_items'               => __( 'Search Book Tags', 'langgam-fikir' ),
        'popular_items'              => __( 'Popular Book Tags', 'langgam-fikir' ),
        'all_items'                  => __( 'All Book Tags', 'langgam-fikir' ),
        'edit_item'                  => __( 'Edit Book Tag', 'langgam-fikir' ),
        'update_item'                => __( 'Update Book Tag', 'langgam-fikir' ),
        'add_new_item'               => __( 'Add New Book Tag', 'langgam-fikir' ),
        'new_item_name'              => __( 'New Book Tag Name', 'langgam-fikir' ),
        'separate_items_with_commas' => __( 'Separate book tags with commas', 'langgam-fikir' ),
        'add_or_remove_items'        => __( 'Add or remove book tags', 'langgam-fikir' ),
        'choose_from_most_used'      => __( 'Choose from the most used book tags', 'langgam-fikir' ),
        'menu_name'                  => __( 'Book Tags', 'langgam-fikir' ),
    );

    $tag_args = array(
        'labels'            => $tag_labels,
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'book-tag' ),
    );

    register_taxonomy( 'book-tag', array( 'book' ), $tag_args );
}
add_action( 'init', 'langgam_fikir_register_book_taxonomies', 0 );

/**
 * Flush rewrite rules on theme activation
 */
function langgam_fikir_rewrite_flush() {
    langgam_fikir_register_books_post_type();
    langgam_fikir_register_book_taxonomies();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'langgam_fikir_rewrite_flush' );
