<?php
/**
 * Visible Breadcrumb Component
 *
 * Renders a visible breadcrumb trail mirroring the BreadcrumbList JSON-LD
 * already emitted from inc/schema.php. Use via langgam_fikir_breadcrumbs().
 *
 * @package Langgam_Fikir
 * @since 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the visible breadcrumb trail.
 *
 * Outputs nothing on the front page. On all other public-facing views, builds
 * an ordered trail from Home → ancestor crumbs → current page.
 */
function langgam_fikir_breadcrumbs() {
    if ( is_front_page() || is_admin() ) {
        return;
    }

    $items = array();

    // Home is always first
    $items[] = array(
        'label' => __( 'Home', 'langgam-fikir' ),
        'url'   => home_url( '/' ),
    );

    if ( is_singular( 'book' ) ) {
        $items[] = array(
            'label' => __( 'Publications', 'langgam-fikir' ),
            'url'   => get_post_type_archive_link( 'book' ),
        );

        // Primary book-category if assigned
        $cats = get_the_terms( get_the_ID(), 'book-category' );
        if ( $cats && ! is_wp_error( $cats ) ) {
            $cat = reset( $cats );
            $items[] = array(
                'label' => $cat->name,
                'url'   => get_term_link( $cat ),
            );
        }

        $items[] = array(
            'label' => get_the_title(),
            'url'   => '',
        );
    } elseif ( is_post_type_archive( 'book' ) ) {
        $items[] = array(
            'label' => __( 'Publications', 'langgam-fikir' ),
            'url'   => '',
        );
    } elseif ( is_singular( 'post' ) ) {
        $cats = get_the_category();
        if ( ! empty( $cats ) ) {
            $items[] = array(
                'label' => $cats[0]->name,
                'url'   => get_category_link( $cats[0]->term_id ),
            );
        }
        $items[] = array(
            'label' => get_the_title(),
            'url'   => '',
        );
    } elseif ( is_tax( 'book-category' ) || is_tax( 'book-tag' ) ) {
        $items[] = array(
            'label' => __( 'Publications', 'langgam-fikir' ),
            'url'   => get_post_type_archive_link( 'book' ),
        );
        $items[] = array(
            'label' => single_term_title( '', false ),
            'url'   => '',
        );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $items[] = array(
            'label' => single_term_title( '', false ),
            'url'   => '',
        );
    } elseif ( is_page() ) {
        global $post;
        $ancestors = array_reverse( get_post_ancestors( $post ) );
        foreach ( $ancestors as $ancestor_id ) {
            $items[] = array(
                'label' => get_the_title( $ancestor_id ),
                'url'   => get_permalink( $ancestor_id ),
            );
        }
        $items[] = array(
            'label' => get_the_title(),
            'url'   => '',
        );
    } elseif ( is_home() ) {
        $items[] = array(
            'label' => __( 'Resources', 'langgam-fikir' ),
            'url'   => '',
        );
    } elseif ( is_search() ) {
        $items[] = array(
            'label' => sprintf( __( 'Search results for "%s"', 'langgam-fikir' ), get_search_query() ),
            'url'   => '',
        );
    } elseif ( is_404() ) {
        $items[] = array(
            'label' => __( 'Page Not Found', 'langgam-fikir' ),
            'url'   => '',
        );
    } elseif ( is_archive() ) {
        $items[] = array(
            'label' => get_the_archive_title(),
            'url'   => '',
        );
    }

    // Don't render if only Home is in the trail
    if ( count( $items ) < 2 ) {
        return;
    }

    $separator = '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    $count     = count( $items );

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'langgam-fikir' ) . '">';
    echo '<ol class="breadcrumb-list">';

    foreach ( $items as $i => $item ) {
        $is_last = ( $i === $count - 1 );
        echo '<li class="breadcrumb-item' . ( $is_last ? ' breadcrumb-current' : '' ) . '">';
        if ( ! $is_last && ! empty( $item['url'] ) ) {
            echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
        } else {
            echo '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
        }
        echo '</li>';
        if ( ! $is_last ) {
            echo $separator; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }

    echo '</ol>';
    echo '</nav>';
}
