<?php
/**
 * WebMCP agent tools
 *
 * Exposes the theme's existing public functionality to in-browser AI agents
 * via the WebMCP proposal (W3C Web Machine Learning CG draft):
 *
 *  - Imperative tools registered from assets/js/webmcp.js:
 *      search-publications  — read-only catalogue search
 *      get-publication      — read-only single-book lookup by slug
 *    Both call the public REST endpoint below, which reuses the theme's own
 *    meta and lifecycle helpers. No writes, no session-bound data.
 *
 *  - Declarative tool on the contact form (page-contact.php) via
 *    toolname / tooldescription / toolparamdescription attributes. The form
 *    deliberately omits toolautosubmit: an agent may fill it, the person
 *    submits it.
 *
 *  - Optional Origin Trial token (Chrome 149 / Edge 150) emitted as a
 *    <meta http-equiv="origin-trial"> tag while the API is behind trials.
 *
 * Everything here is progressive enhancement. The script feature-detects
 * document.modelContext and does nothing where it is absent; the REST
 * endpoint and form attributes are inert without an agent.
 *
 * Settings: Appearance > Theme Options > Social & SEO > Agent Tools (WebMCP).
 *
 * @package Langgam_Fikir
 * @since 2.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Whether WebMCP integration is switched on.
 *
 * @return bool
 */
function langgam_fikir_webmcp_enabled() {
    return (bool) langgam_fikir_get_option( 'webmcp_enabled', 1 );
}

/**
 * Whether the contact form should carry declarative tool attributes.
 *
 * @return bool
 */
function langgam_fikir_webmcp_contact_form_enabled() {
    return langgam_fikir_webmcp_enabled() && (bool) langgam_fikir_get_option( 'webmcp_contact_form', 1 );
}

/**
 * Echo toolname / tooldescription attributes for a form, when enabled.
 *
 * @param string $name        Tool name (ASCII letters, digits, _ - . only).
 * @param string $description Tool description shown to the agent.
 */
function langgam_fikir_webmcp_form_attrs( $name, $description ) {
    if ( ! langgam_fikir_webmcp_contact_form_enabled() ) {
        return;
    }
    printf(
        ' toolname="%s" tooldescription="%s"',
        esc_attr( preg_replace( '/[^A-Za-z0-9_.-]/', '-', $name ) ),
        esc_attr( $description )
    );
}

/**
 * Echo a toolparamdescription attribute for a form control, when enabled.
 *
 * @param string $description Parameter description shown to the agent.
 */
function langgam_fikir_webmcp_param_attr( $description ) {
    if ( ! langgam_fikir_webmcp_contact_form_enabled() ) {
        return;
    }
    printf( ' toolparamdescription="%s"', esc_attr( $description ) );
}

/**
 * Public, read-only representation of a book for agents.
 *
 * Only data already rendered on the public site is included.
 *
 * @param int  $post_id Book post ID.
 * @param bool $full    Include long-form fields (description, ISBN, credits).
 * @return array
 */
function langgam_fikir_webmcp_book_data( $post_id, $full = false ) {
    $status = langgam_fikir_get_book_meta( $post_id, 'status' );
    $price  = langgam_fikir_get_book_meta( $post_id, 'price' );

    $data = array(
        'id'        => (int) $post_id,
        'title'     => get_the_title( $post_id ),
        'subtitle'  => langgam_fikir_get_book_meta( $post_id, 'subtitle' ),
        'slug'      => get_post_field( 'post_name', $post_id ),
        'url'       => get_permalink( $post_id ),
        'author'    => langgam_fikir_get_book_meta( $post_id, 'author' ),
        'year'      => langgam_fikir_get_book_meta( $post_id, 'year' ),
        'language'  => langgam_fikir_get_book_meta( $post_id, 'language' ),
        'status'    => $status ? array(
            'key'   => $status,
            'label' => langgam_fikir_get_status_label( $status ),
        ) : null,
        'orderable' => $status ? langgam_fikir_status_is_orderable( $status ) : false,
        'price'     => ( $price && $status && langgam_fikir_status_has_price( $status ) )
            ? array( 'amount' => (float) $price, 'currency' => 'MYR' )
            : null,
        'excerpt'   => wp_strip_all_tags( wp_trim_words( get_the_excerpt( $post_id ), 40, '…' ) ),
        'cover'     => get_the_post_thumbnail_url( $post_id, 'book-cover' ) ?: null,
    );

    $terms = get_the_terms( $post_id, 'book-category' );
    $data['categories'] = ( $terms && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'name' ) : array();

    if ( $full ) {
        $data['editor']      = langgam_fikir_get_book_meta( $post_id, 'editor' );
        $data['translator']  = langgam_fikir_get_book_meta( $post_id, 'translator' );
        $data['publisher']   = langgam_fikir_get_book_meta( $post_id, 'publisher' );
        $data['isbn']        = langgam_fikir_get_book_meta( $post_id, 'isbn' );
        $data['description'] = wp_strip_all_tags( wp_trim_words( get_post_field( 'post_content', $post_id ), 200, '…' ) );
    }

    return $data;
}

/**
 * Register the public REST routes.
 */
function langgam_fikir_webmcp_register_routes() {
    if ( ! langgam_fikir_webmcp_enabled() ) {
        return;
    }

    register_rest_route( 'langgam-fikir/v1', '/publications', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'langgam_fikir_webmcp_rest_search',
        'permission_callback' => '__return_true',
        'args'                => array(
            'search'   => array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'status'   => array(
                'type'              => 'string',
                'enum'              => array( 'announced', 'coming-soon', 'pre-order', 'new-release', 'published', 'limited-edition', 'digital-only', 'reprinting', 'sold-out', 'out-of-print' ),
                'sanitize_callback' => 'sanitize_key',
            ),
            'category' => array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_title',
            ),
            'per_page' => array(
                'type'              => 'integer',
                'default'           => 10,
                'minimum'           => 1,
                'maximum'           => 20,
                'sanitize_callback' => 'absint',
            ),
        ),
    ) );

    register_rest_route( 'langgam-fikir/v1', '/publications/(?P<slug>[a-z0-9-]+)', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'langgam_fikir_webmcp_rest_single',
        'permission_callback' => '__return_true',
        'args'                => array(
            'slug' => array(
                'type'              => 'string',
                'required'          => true,
                'sanitize_callback' => 'sanitize_title',
            ),
        ),
    ) );
}
add_action( 'rest_api_init', 'langgam_fikir_webmcp_register_routes' );

/**
 * GET /langgam-fikir/v1/publications
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function langgam_fikir_webmcp_rest_search( WP_REST_Request $request ) {
    $args = array(
        'post_type'      => 'book',
        'post_status'    => 'publish',
        'posts_per_page' => min( 20, max( 1, (int) $request->get_param( 'per_page' ) ) ),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => false,
    );

    $search = $request->get_param( 'search' );
    if ( $search ) {
        $args['s']       = $search;
        $args['orderby'] = 'relevance';
    }

    $status = $request->get_param( 'status' );
    if ( $status ) {
        $args['meta_query'] = array(
            array(
                'key'   => '_book_status',
                'value' => $status,
            ),
        );
    }

    $category = $request->get_param( 'category' );
    if ( $category ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'book-category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $query = new WP_Query( $args );
    $items = array();
    foreach ( $query->posts as $post ) {
        $items[] = langgam_fikir_webmcp_book_data( $post->ID );
    }

    $response = rest_ensure_response( array(
        'total'    => (int) $query->found_posts,
        'count'    => count( $items ),
        'query'    => array(
            'search'   => $search ?: null,
            'status'   => $status ?: null,
            'category' => $category ?: null,
        ),
        'archive'  => get_post_type_archive_link( 'book' ),
        'items'    => $items,
    ) );
    $response->header( 'Cache-Control', 'public, max-age=300' );

    return $response;
}

/**
 * GET /langgam-fikir/v1/publications/{slug}
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function langgam_fikir_webmcp_rest_single( WP_REST_Request $request ) {
    $book = get_page_by_path( $request->get_param( 'slug' ), OBJECT, 'book' );

    if ( ! $book || 'publish' !== $book->post_status ) {
        return new WP_Error(
            'langgam_fikir_not_found',
            __( 'No publication with that slug.', 'langgam-fikir' ),
            array( 'status' => 404 )
        );
    }

    $response = rest_ensure_response( langgam_fikir_webmcp_book_data( $book->ID, true ) );
    $response->header( 'Cache-Control', 'public, max-age=300' );

    return $response;
}

/**
 * Enqueue the tool-registration script (front end, footer, feature-detected).
 */
function langgam_fikir_webmcp_enqueue() {
    if ( ! langgam_fikir_webmcp_enabled() ) {
        return;
    }

    wp_enqueue_script(
        'langgam-fikir-webmcp',
        get_stylesheet_directory_uri() . '/assets/js/webmcp.js',
        array(),
        filemtime( get_stylesheet_directory() . '/assets/js/webmcp.js' ),
        true
    );

    wp_localize_script( 'langgam-fikir-webmcp', 'langgamFikirWebMCP', array(
        'restUrl'   => esc_url_raw( rest_url( 'langgam-fikir/v1/publications' ) ),
        'siteName'  => get_bloginfo( 'name' ),
        'archive'   => get_post_type_archive_link( 'book' ),
        'statuses'  => array( 'announced', 'coming-soon', 'pre-order', 'new-release', 'published', 'limited-edition', 'digital-only', 'reprinting', 'sold-out', 'out-of-print' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'langgam_fikir_webmcp_enqueue', 25 );

/**
 * Emit the Origin Trial token, if one has been entered.
 */
function langgam_fikir_webmcp_origin_trial_meta() {
    if ( ! langgam_fikir_webmcp_enabled() ) {
        return;
    }
    $token = trim( (string) langgam_fikir_get_option( 'webmcp_origin_trial_token', '' ) );
    if ( '' === $token ) {
        return;
    }
    printf( '<meta http-equiv="origin-trial" content="%s">' . "\n", esc_attr( $token ) );
}
add_action( 'wp_head', 'langgam_fikir_webmcp_origin_trial_meta', 1 );
