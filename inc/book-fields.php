<?php
/**
 * Book fields: one schema for the editor box, saving, the book page and Schema.org
 *
 * The original book fields (subtitle, author, editor, translator, publisher, year, ISBN,
 * language, status, price) keep their meta keys. 2.11.0 adds original title, translated
 * from, edition, series, format, pages and dimensions. Status and price keep their own
 * controls in the editor (see inc/admin/meta-boxes.php).
 *
 * @package Langgam_Fikir
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Book formats (value => label).
 */
function langgam_fikir_book_format_choices() {
    return array(
        'paperback' => __( 'Paperback', 'langgam-fikir' ),
        'hardcover' => __( 'Hardcover', 'langgam-fikir' ),
        'ebook'     => __( 'E-book', 'langgam-fikir' ),
        'audiobook' => __( 'Audiobook', 'langgam-fikir' ),
    );
}

/**
 * Editor field groups. Each field: type (text|number|select), label, width (half|full),
 * placeholder, desc, and for selects choices.
 *
 * @return array<string, array{title:string, fields:array}>
 */
function langgam_fikir_book_field_groups() {
    return array(
        'identity'    => array(
            'title'  => __( 'Title & credits', 'langgam-fikir' ),
            'fields' => array(
                'subtitle'        => array( 'type' => 'text', 'label' => __( 'Subtitle', 'langgam-fikir' ), 'width' => 'full' ),
                'original_title'  => array( 'type' => 'text', 'label' => __( 'Original title', 'langgam-fikir' ), 'width' => 'full', 'placeholder' => __( 'For translations: the title in the original language', 'langgam-fikir' ) ),
                'author'          => array( 'type' => 'text', 'label' => __( 'Author', 'langgam-fikir' ), 'width' => 'half' ),
                'translator'      => array( 'type' => 'text', 'label' => __( 'Translator', 'langgam-fikir' ), 'width' => 'half', 'placeholder' => __( 'Leave blank if not applicable', 'langgam-fikir' ) ),
                'translated_from' => array( 'type' => 'text', 'label' => __( 'Translated from', 'langgam-fikir' ), 'width' => 'half', 'placeholder' => __( 'For example Arabic', 'langgam-fikir' ) ),
                'editor'          => array( 'type' => 'text', 'label' => __( 'Editor', 'langgam-fikir' ), 'width' => 'half', 'placeholder' => __( 'Leave blank if not applicable', 'langgam-fikir' ) ),
            ),
        ),
        'publication' => array(
            'title'  => __( 'Publication', 'langgam-fikir' ),
            'fields' => array(
                'publisher' => array( 'type' => 'text', 'label' => __( 'Publisher', 'langgam-fikir' ), 'width' => 'half', 'placeholder' => __( 'Leave blank if not applicable', 'langgam-fikir' ) ),
                'year'      => array( 'type' => 'number', 'label' => __( 'Publication year', 'langgam-fikir' ), 'width' => 'half', 'min' => 1900, 'max' => 2100 ),
                'edition'   => array( 'type' => 'text', 'label' => __( 'Edition', 'langgam-fikir' ), 'width' => 'half', 'placeholder' => __( 'For example First edition', 'langgam-fikir' ) ),
                'series'    => array( 'type' => 'text', 'label' => __( 'Series', 'langgam-fikir' ), 'width' => 'half', 'placeholder' => __( 'Series name and volume', 'langgam-fikir' ) ),
                'isbn'      => array( 'type' => 'text', 'label' => __( 'ISBN', 'langgam-fikir' ), 'width' => 'full', 'placeholder' => '978-629-96135-0-3' ),
            ),
        ),
        'physical'    => array(
            'title'  => __( 'Physical details', 'langgam-fikir' ),
            'fields' => array(
                'format'     => array( 'type' => 'select', 'label' => __( 'Format', 'langgam-fikir' ), 'width' => 'third', 'choices' => langgam_fikir_book_format_choices() ),
                'pages'      => array( 'type' => 'number', 'label' => __( 'Pages', 'langgam-fikir' ), 'width' => 'third', 'min' => 1, 'max' => 5000 ),
                'dimensions' => array( 'type' => 'text', 'label' => __( 'Size', 'langgam-fikir' ), 'width' => 'third', 'placeholder' => __( 'For example 14 × 21 cm', 'langgam-fikir' ) ),
            ),
        ),
    );
}

/**
 * Meta keys (without the leading underscore) saved from the editor's field groups.
 */
function langgam_fikir_book_group_field_keys() {
    $keys = array();
    foreach ( langgam_fikir_book_field_groups() as $group ) {
        $keys = array_merge( $keys, array_keys( $group['fields'] ) );
    }
    return $keys;
}

/**
 * Sanitise one editor field value.
 */
function langgam_fikir_sanitize_book_field( $key, $value ) {
    foreach ( langgam_fikir_book_field_groups() as $group ) {
        if ( ! isset( $group['fields'][ $key ] ) ) {
            continue;
        }
        $field = $group['fields'][ $key ];
        if ( 'select' === $field['type'] ) {
            return isset( $field['choices'][ $value ] ) ? $value : '';
        }
        if ( 'number' === $field['type'] ) {
            return '' === $value ? '' : (string) max( $field['min'], min( $field['max'], absint( $value ) ) );
        }
    }
    return sanitize_text_field( $value );
}

/**
 * Rows for the information card on the book page: label, value, in a fixed order.
 * Only rows with a value are returned, so a sparsely filled book simply shows fewer rows.
 *
 * @param int $post_id Book ID.
 * @return array<int, array{label:string, value:string}>
 */
function langgam_fikir_book_info_rows( $post_id ) {
    $get = static function ( $key ) use ( $post_id ) {
        return trim( (string) langgam_fikir_get_book_meta( $post_id, $key ) );
    };

    $format  = $get( 'format' );
    $formats = langgam_fikir_book_format_choices();
    $pages   = $get( 'pages' );

    $rows = array(
        array( 'author', $get( 'author' ) ),
        array( 'translator', $get( 'translator' ) ),
        array( 'translated_from', $get( 'translated_from' ) ),
        array( 'editor', $get( 'editor' ) ),
        array( 'original_title', $get( 'original_title' ) ),
        array( 'publisher', $get( 'publisher' ) ),
        array( 'year', $get( 'year' ) ),
        array( 'edition', $get( 'edition' ) ),
        array( 'series', $get( 'series' ) ),
        array( 'format', isset( $formats[ $format ] ) ? $formats[ $format ] : '' ),
        array( 'pages', $pages ),
        array( 'dimensions', $get( 'dimensions' ) ),
        array( 'isbn', $get( 'isbn' ) ),
    );

    $out = array();
    foreach ( $rows as $row ) {
        if ( '' !== $row[1] ) {
            $out[] = array( 'key' => $row[0], 'label' => langgam_fikir_text( 'lbl_' . $row[0] ), 'value' => $row[1] );
        }
    }
    return $out;
}

/**
 * Linked term chips for a book taxonomy, as an HTML string ('' when the book has none).
 */
function langgam_fikir_book_term_chips( $post_id, $taxonomy ) {
    $terms = get_the_terms( $post_id, $taxonomy );
    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return '';
    }

    $html = '';
    foreach ( $terms as $term ) {
        $link = get_term_link( $term );
        if ( is_wp_error( $link ) ) {
            continue;
        }
        $html .= '<a class="book-chip" href="' . esc_url( $link ) . '" rel="tag">' . esc_html( $term->name ) . '</a>';
    }
    return $html;
}

/**
 * Schema.org properties for the new fields, merged into the Book JSON-LD.
 *
 * @param int   $post_id Book ID.
 * @param array $schema  Book schema so far.
 * @return array
 */
function langgam_fikir_book_schema_extras( $post_id, $schema ) {
    $get = static function ( $key ) use ( $post_id ) {
        return trim( (string) langgam_fikir_get_book_meta( $post_id, $key ) );
    };

    if ( $pages = (int) $get( 'pages' ) ) {
        $schema['numberOfPages'] = $pages;
    }
    $format_map = array(
        'paperback' => 'https://schema.org/Paperback',
        'hardcover' => 'https://schema.org/Hardcover',
        'ebook'     => 'https://schema.org/EBook',
        'audiobook' => 'https://schema.org/AudiobookFormat',
    );
    if ( isset( $format_map[ $get( 'format' ) ] ) ) {
        $schema['bookFormat'] = $format_map[ $get( 'format' ) ];
    }
    if ( $edition = $get( 'edition' ) ) {
        $schema['bookEdition'] = $edition;
    }
    if ( $original = $get( 'original_title' ) ) {
        $schema['alternateName'] = $original;
    }
    if ( $series = $get( 'series' ) ) {
        $schema['isPartOf'] = array( '@type' => 'CreativeWorkSeries', 'name' => $series );
    }

    $genres   = wp_get_post_terms( $post_id, 'book-category', array( 'fields' => 'names' ) );
    $keywords = wp_get_post_terms( $post_id, 'book-tag', array( 'fields' => 'names' ) );
    if ( ! is_wp_error( $genres ) && $genres ) {
        $schema['genre'] = array_values( $genres );
    }
    if ( ! is_wp_error( $keywords ) && $keywords ) {
        $schema['keywords'] = implode( ', ', $keywords );
    }

    return $schema;
}

/**
 * Sort order for the book archive and book taxonomy archives (?sort=title|oldest|newest).
 * Read-only query parameter, whitelisted; no state is changed.
 *
 * @param WP_Query $query The query.
 */
function langgam_fikir_book_archive_sort( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( ! ( $query->is_post_type_archive( 'book' ) || $query->is_tax( array( 'book-category', 'book-tag' ) ) ) ) {
        return;
    }

    $sort = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
    if ( 'title' === $sort ) {
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'ASC' );
    } elseif ( 'oldest' === $sort ) {
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'ASC' );
    }
}
add_action( 'pre_get_posts', 'langgam_fikir_book_archive_sort' );

/**
 * Books list in the admin: Author, Status and Year columns after the title.
 */
function langgam_fikir_book_admin_columns( $columns ) {
    $out = array();
    foreach ( $columns as $key => $label ) {
        $out[ $key ] = $label;
        if ( 'title' === $key ) {
            $out['lf_author'] = __( 'Author', 'langgam-fikir' );
            $out['lf_status'] = __( 'Status', 'langgam-fikir' );
            $out['lf_year']   = __( 'Year', 'langgam-fikir' );
        }
    }
    return $out;
}
add_filter( 'manage_book_posts_columns', 'langgam_fikir_book_admin_columns' );

function langgam_fikir_book_admin_column_content( $column, $post_id ) {
    if ( 'lf_author' === $column ) {
        echo esc_html( langgam_fikir_get_book_meta( $post_id, 'author' ) );
    } elseif ( 'lf_year' === $column ) {
        echo esc_html( langgam_fikir_get_book_meta( $post_id, 'year' ) );
    } elseif ( 'lf_status' === $column ) {
        $status = langgam_fikir_get_book_meta( $post_id, 'status' );
        $status = $status ? $status : 'published';
        printf( '<span class="lf-col-status lf-col-status--%s">%s</span>', esc_attr( $status ), esc_html( langgam_fikir_get_status_label( $status ) ) );
    }
}
add_action( 'manage_book_posts_custom_column', 'langgam_fikir_book_admin_column_content', 10, 2 );
