<?php
/**
 * Print and PDF
 *
 * A print stylesheet for every page (assets/css/print.css), set up from Theme Options > Print &
 * PDF: paper size, margins and text size as @page rules, the site name and logo on top, the web
 * address, print date and a ready-made citation at the end, link addresses printed after
 * links, and images on or off. The menu, footer, buttons, forms and other screen-only parts are
 * left out.
 *
 * Articles, books and pages (each switchable) get a Print button beside the share buttons. With
 * Chrome on the server (inc/pdf.php) a Download PDF button sits beside it; otherwise the print
 * dialog's own Save as PDF does the job.
 *
 * @package Langgam_Fikir
 * @since 2.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The kind of page for the button switches: post, book or page ('' for anything else).
 */
function langgam_fikir_print_kind( $post = null ) {
    $type = get_post_type( $post );
    return in_array( $type, array( 'post', 'book', 'page' ), true ) ? $type : '';
}

/**
 * Whether the Print (and Download PDF) buttons belong on a post.
 */
function langgam_fikir_print_allowed( $post = null ) {
    $kind = langgam_fikir_print_kind( $post );
    return '' !== $kind && (bool) langgam_fikir_get_option( 'print_on_' . $kind . 's', 'page' === $kind ? 0 : 1 );
}

/**
 * Whether this request is Chrome making a server PDF (?lf-print=pdf).
 */
function langgam_fikir_print_is_pdf_render() {
    return isset( $_GET['lf-print'] ) && 'pdf' === $_GET['lf-print']; // phpcs:ignore WordPress.Security.NonceVerification
}

/* =========================================================================
 * Buttons
 * ====================================================================== */

/**
 * The buttons' HTML ('' when they do not belong on this post).
 */
function langgam_fikir_print_buttons_html() {
    $post = get_post();
    if ( ! $post || ! langgam_fikir_print_allowed( $post ) ) {
        return '';
    }

    $printer = '<svg class="lf-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6z"/></svg>';
    $file    = '<svg class="lf-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/><path d="M12 12v6"/><path d="m9 15 3 3 3-3"/></svg>';

    $server = function_exists( 'langgam_fikir_pdf_server_ready' ) && langgam_fikir_pdf_server_ready();
    $html   = sprintf(
        '<button type="button" class="share-btn lf-print-btn" data-lf-print aria-label="%s">%s<span>%s</span></button>',
        esc_attr( $server ? __( 'Print this page', 'langgam-fikir' ) : __( 'Print this page or save it as a PDF', 'langgam-fikir' ) ),
        $printer,
        esc_html( $server ? __( 'Print', 'langgam-fikir' ) : __( 'Print / PDF', 'langgam-fikir' ) )
    );
    if ( $server ) {
        $html .= sprintf(
            '<a class="share-btn lf-pdf-btn" href="%s" rel="nofollow" aria-label="%s">%s<span>%s</span></a>',
            esc_url( add_query_arg( 'lf-pdf', '1', get_permalink( $post ) ) ),
            esc_attr__( 'Download this page as a PDF file', 'langgam-fikir' ),
            $file,
            esc_html__( 'Download PDF', 'langgam-fikir' )
        );
    }
    return $html;
}

/**
 * Inside the share bar, when there is one (called from langgam_fikir_render_share_bar()).
 */
function langgam_fikir_print_in_share_bar() {
    $html = langgam_fikir_print_buttons_html();
    if ( '' !== $html ) {
        langgam_fikir_print_buttons_shown( true );
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- built and escaped above
    }
}
add_action( 'langgam_fikir_share_bar_end', 'langgam_fikir_print_in_share_bar' );

/**
 * Remembers whether the buttons were printed on this page.
 */
function langgam_fikir_print_buttons_shown( $set = null ) {
    static $shown = false;
    if ( null !== $set ) {
        $shown = (bool) $set;
    }
    return $shown;
}

/**
 * Template tag, after the share bar: the buttons on their own row when the share bar is off
 * or empty.
 */
function langgam_fikir_render_print_actions() {
    if ( langgam_fikir_print_buttons_shown() ) {
        return;
    }
    $html = langgam_fikir_print_buttons_html();
    if ( '' === $html ) {
        return;
    }
    langgam_fikir_print_buttons_shown( true );
    echo '<div class="book-share-bar lf-print-actions"><div class="book-share-links">' . $html . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built and escaped above
}

/* =========================================================================
 * Stylesheet, page set-up and script
 * ====================================================================== */

function langgam_fikir_print_body_classes( $classes ) {
    if ( langgam_fikir_get_option( 'print_links', 1 ) ) {
        $classes[] = 'lf-print-links';
    }
    if ( ! langgam_fikir_get_option( 'print_images', 1 ) ) {
        $classes[] = 'lf-print-no-images';
    }
    if ( langgam_fikir_print_is_pdf_render() ) {
        $classes[] = 'lf-print-pdf';
    }
    return $classes;
}
add_filter( 'body_class', 'langgam_fikir_print_body_classes' );

/**
 * The @page rules from the settings (also read by Chrome for server PDFs).
 */
function langgam_fikir_print_page_css() {
    $paper   = langgam_fikir_get_option( 'print_paper', 'A4' );
    $paper   = in_array( $paper, array( 'A4', 'A5', 'letter' ), true ) ? $paper : 'A4';
    $margins = array( 'narrow' => '12mm', 'normal' => '18mm', 'wide' => '25mm' );
    $margin  = $margins[ isset( $margins[ langgam_fikir_get_option( 'print_margins', 'normal' ) ] ) ? langgam_fikir_get_option( 'print_margins', 'normal' ) : 'normal' ];
    $size    = (int) langgam_fikir_get_option( 'print_size', '11' );
    $size    = in_array( $size, array( 10, 11, 12, 13 ), true ) ? $size : 11;

    // Links within this site need no printed address.
    $host = preg_replace( '/[^a-z0-9.\-]/', '', strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) );
    $own  = '' !== $host ? 'body.lf-print-links .entry-content a[href*="//' . $host . '"]::after,body.lf-print-links .book-synopsis a[href*="//' . $host . '"]::after{content:none!important}' : '';

    return '@page{size:' . $paper . ';margin:' . $margin . ' ' . $margin . ' calc(' . $margin . ' + 4mm)}'
        . '@media print{:root{--lf-print-size:' . $size . 'pt}' . $own . '}';
}

function langgam_fikir_print_assets() {
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();

    wp_enqueue_style( 'langgam-fikir-print', $uri . '/assets/css/print.css', array( 'langgam-fikir-main' ), filemtime( $dir . '/assets/css/print.css' ) );
    wp_add_inline_style( 'langgam-fikir-print', langgam_fikir_print_page_css() );

    if ( is_singular() && langgam_fikir_print_allowed() ) {
        wp_enqueue_script( 'langgam-fikir-print', $uri . '/assets/js/print.js', array(), filemtime( $dir . '/assets/js/print.js' ), true );
    }
}
add_action( 'wp_enqueue_scripts', 'langgam_fikir_print_assets', 40 );

/* =========================================================================
 * What only paper shows
 * ====================================================================== */

/**
 * Site name, tagline and logo at the top of the printout.
 */
function langgam_fikir_print_header() {
    if ( ! langgam_fikir_get_option( 'print_header', 1 ) ) {
        return;
    }
    $logo_id = (int) get_theme_mod( 'custom_logo' );
    $logo    = $logo_id ? wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'lf-print-logo', 'alt' => '', 'loading' => 'eager' ) ) : '';
    $tagline = get_bloginfo( 'description', 'display' );
    ?>
    <div class="lf-print-header" aria-hidden="true">
        <?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput -- core image markup ?>
        <div>
            <p class="lf-print-site"><?php bloginfo( 'name' ); ?></p>
            <?php if ( $tagline ) : ?>
                <p class="lf-print-tagline"><?php echo esc_html( $tagline ); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
add_action( 'wp_body_open', 'langgam_fikir_print_header', 1 );

/**
 * Web address, print date and a citation at the end of the printout.
 */
function langgam_fikir_print_footer() {
    $post     = is_singular() ? get_queried_object() : null;
    $address  = langgam_fikir_get_option( 'print_footer', 1 );
    $citation = '';

    if ( $post instanceof WP_Post && langgam_fikir_get_option( 'print_citation', 1 )
        && function_exists( 'langgam_fikir_cite_work' ) && in_array( $post->post_type, array( 'post', 'book' ), true )
        && ! ( function_exists( 'langgam_fikir_cite_auto_applies' ) && langgam_fikir_cite_auto_applies( $post ) ) ) {
        $styles   = array_filter( explode( ',', (string) langgam_fikir_get_option( 'cite_formats', 'apa' ) ) );
        $style    = $styles ? reset( $styles ) : 'apa';
        $citation = langgam_fikir_cite_render( langgam_fikir_cite_work( $post ), $style );
    }
    if ( ! $address && '' === $citation ) {
        return;
    }

    $url = $post instanceof WP_Post ? get_permalink( $post ) : home_url( add_query_arg( array() ) );
    ?>
    <div class="lf-print-footer" aria-hidden="true">
        <?php if ( '' !== $citation ) : ?>
            <p class="lf-print-cite"><strong><?php esc_html_e( 'Cite this:', 'langgam-fikir' ); ?></strong> <?php echo wp_kses_post( $citation ); ?></p>
        <?php endif; ?>
        <?php if ( $address ) : ?>
            <p class="lf-print-source">
                <?php
                printf(
                    /* translators: 1: web address, 2: date */
                    esc_html__( 'From %1$s, printed on %2$s.', 'langgam-fikir' ),
                    '<span class="lf-print-url">' . esc_html( $url ) . '</span>',
                    esc_html( wp_date( get_option( 'date_format' ) ) )
                );
                ?>
            </p>
        <?php endif; ?>
    </div>
    <?php
}
add_action( 'wp_footer', 'langgam_fikir_print_footer', 5 );
