<?php
/**
 * Meta Boxes Registration
 *
 * @package Langgam_Fikir
 * @since 1.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add Book Meta Boxes
 */
function langgam_fikir_add_book_meta_boxes() {
    add_meta_box(
        'book_details',
        __( 'Book Details', 'langgam-fikir' ),
        'langgam_fikir_book_details_callback',
        'book',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'langgam_fikir_add_book_meta_boxes' );

/**
 * Add Contact Page Meta Box
 */
function langgam_fikir_add_contact_meta_box() {
    add_meta_box(
        'contact_info',
        __( 'Contact Information', 'langgam-fikir' ),
        'langgam_fikir_contact_meta_box_callback',
        'page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'langgam_fikir_add_contact_meta_box' );

/**
 * Add About Page Meta Box
 */
function langgam_fikir_add_about_meta_box() {
    add_meta_box(
        'about_logo',
        __( 'About Page Logo', 'langgam-fikir' ),
        'langgam_fikir_about_meta_box_callback',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'langgam_fikir_add_about_meta_box' );

/**
 * Save Book Meta Data
 */
function langgam_fikir_save_book_meta( $post_id ) {
    if ( ! isset( $_POST['langgam_fikir_book_meta_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['langgam_fikir_book_meta_nonce'], 'langgam_fikir_book_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Status, price and language have their own controls; every other book field comes from the
    // schema in inc/book-fields.php.
    $fields = array( 'book_status', 'book_price', 'book_language' );
    foreach ( langgam_fikir_book_group_field_keys() as $key ) {
        $fields[] = 'book_' . $key;
    }

    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            $value = wp_unslash( $_POST[ $field ] );
            $key   = substr( $field, 5 );
            update_post_meta( $post_id, '_' . $field, langgam_fikir_sanitize_book_field( $key, $value ) );
        }
    }
}
add_action( 'save_post_book', 'langgam_fikir_save_book_meta' );

/**
 * Save Contact Meta Data
 */
function langgam_fikir_save_contact_meta( $post_id ) {
    if ( ! isset( $_POST['langgam_fikir_contact_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['langgam_fikir_contact_nonce'], 'langgam_fikir_contact_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = array( 'contact_email', 'contact_phone', 'contact_whatsapp', 'contact_address' );

    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
        }
    }
}
add_action( 'save_post_page', 'langgam_fikir_save_contact_meta' );

/**
 * Save About Meta Data
 */
function langgam_fikir_save_about_meta( $post_id ) {
    if ( ! isset( $_POST['langgam_fikir_about_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['langgam_fikir_about_nonce'], 'langgam_fikir_about_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['about_logo_id'] ) ) {
        update_post_meta( $post_id, '_about_logo_id', absint( $_POST['about_logo_id'] ) );
    }
}
add_action( 'save_post_page', 'langgam_fikir_save_about_meta' );
