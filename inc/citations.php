<?php
/**
 * Citations
 *
 * Built-in replacement for the CitePress plugin (3.0.1, by MENJ, GPLv2+): a "Cite this" box
 * with 20 citation styles, copy, six download formats (BibTeX, RIS, EndNote, CSL-JSON, CFF,
 * Dublin Core), in-text citations and a reference list, a block, and Google Scholar tags.
 * Analytics live in inc/citations-analytics.php.
 *
 * Differences from the plugin:
 *  - Book pages are cited as books (author, year, title, edition, publisher, ISBN), and their
 *    downloads use book types. Articles are cited as web articles, as before.
 *  - The citation is rendered on the server too, so it shows without JavaScript and in print;
 *    the script only switches styles, copies and builds downloads.
 *  - [cite id="123"] cites another post or book (the plugin could only cite the current post).
 *  - The box follows the theme's palette, fonts and corners (assets/css/citations.css); no jQuery.
 *  - Settings: Theme Options > Citations. Existing [cite], [citepress_cite], [cite_bibliography],
 *    [citepress_cite_bibliography] and the citepress/block block keep working.
 *  - Nothing here is registered while the CitePress plugin is still active (it is switched
 *    off by inc/plugin-takeover.php), so the two never run at once.
 *
 * @package Langgam_Fikir
 * @since 2.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =========================================================================
 * Catalogue
 * ====================================================================== */

// The style and download-format catalogues (langgam_fikir_cite_format_names(),
// langgam_fikir_cite_export_names()) live in inc/options-schema.php with the other
// settings catalogues, because Theme Options needs them before this file loads.

/**
 * Templates for web articles (the plugin's, unchanged) and for books.
 *
 * Tokens: {author} {title} {sitename} {publisher} {permalink} (a link) {permalink_url}
 * {date} {date_dmy} (access date) {publication_date} {publication_date_dmy} {publication_year}
 * {language} {isbn} {edition} {ed} (" (edition)" or nothing) {isbn_iso} (" ISBN x." or nothing).
 *
 * @return array{web: array<string,string>, book: array<string,string>}
 */
function langgam_fikir_cite_templates() {
    static $templates = null;
    if ( null !== $templates ) {
        return $templates;
    }

    $templates = array(
        'web'  => array(
            'apa'       => '{author}. ({publication_date}). {title}. <em>{sitename}</em>. {permalink}',
            'mla'       => '{author}. "{title}." <em>{sitename}</em>, {publication_date}, {permalink}',
            'chicago'   => '{author}. "{title}." <em>{sitename}</em>. Last modified {date}. {permalink}',
            'harvard'   => '{author}. ({publication_date}). {title}. <em>{sitename}</em>. Available at: {permalink} (Accessed: {date})',
            'vancouver' => '{author}. {title}. <em>{sitename}</em>. {publication_date}; [cited {date}]. Available from: {permalink}',
            'ieee'      => '{author}, "{title}," <em>{sitename}</em>, {publication_date}. [Online]. Available: {permalink}. [Accessed: {date}]',
            'ama'       => '{author}. {title}. <em>{sitename}</em>. Published {publication_date}. Accessed {date}. {permalink}',
            'asa'       => '{author}. {publication_date}. "{title}." <em>{sitename}</em>. Retrieved {date} ({permalink})',
            'turabian'  => '{author}. "{title}." <em>{sitename}</em>. {publication_date}. {permalink}',
            'wikipedia' => '{{cite web |url={permalink_url} |title={title} |website={sitename} |author={author} |date={publication_date_dmy} |access-date={date_dmy} |language={language}}}',
            'acs'       => '{author}. {title}. <em>{sitename}</em> [Online] {publication_date}. {permalink} (accessed {date}).',
            'aip'       => '{author}, "{title}," <em>{sitename}</em> {publication_year}. [Online]. Available: {permalink}',
            'nlm'       => '{author}. {title}. <em>{sitename}</em> [Internet]. {publication_date} [cited {date}]. Available from: {permalink}',
            'aaa'       => '{author} {publication_date} {title}. <em>{sitename}</em>, electronic document, {permalink}, accessed {date}.',
            'apsa'      => '{author}. {publication_date}. "{title}." <em>{sitename}</em>. {permalink} (accessed {date}).',
            'oscola'    => '{author}, \'{title}\' (<em>{sitename}</em>, {publication_date}) &lt;{permalink}&gt; accessed {date}',
            'nature'    => '{author}. {title}. <em>{sitename}</em> {permalink} ({publication_date}).',
            'acm'       => '{author}. {publication_date}. {title}. <em>{sitename}</em>. Retrieved {date} from {permalink}',
            'bluebook'  => '{author}, <em>{title}</em>, {sitename} ({publication_date}), {permalink}.',
            'iso690'    => '{author}. {title}. <em>{sitename}</em> [online]. {publication_date} [viewed {date}]. Available from: {permalink}',
        ),
        'book' => array(
            'apa'       => '{author}. ({publication_year}). <em>{title}</em>{ed}. {publisher}.',
            'mla'       => '{author}. <em>{title}</em>{ed}. {publisher}, {publication_year}.',
            'chicago'   => '{author}. <em>{title}</em>{ed}. {publisher}, {publication_year}.',
            'harvard'   => '{author} ({publication_year}) <em>{title}</em>{ed}. {publisher}.',
            'vancouver' => '{author}. {title}{ed}. {publisher}; {publication_year}.',
            'ieee'      => '{author}, <em>{title}</em>{ed}. {publisher}, {publication_year}.',
            'ama'       => '{author}. <em>{title}</em>{ed}. {publisher}; {publication_year}.',
            'asa'       => '{author}. {publication_year}. <em>{title}</em>{ed}. {publisher}.',
            'turabian'  => '{author}. <em>{title}</em>{ed}. {publisher}, {publication_year}.',
            'wikipedia' => '{{cite book |title={title} |author={author} |year={publication_year} |edition={edition} |publisher={publisher} |isbn={isbn} |url={permalink_url} |access-date={date_dmy} |language={language}}}',
            'acs'       => '{author}. <em>{title}</em>{ed}; {publisher}, {publication_year}.',
            'aip'       => '{author}, <em>{title}</em>{ed} ({publisher}, {publication_year}).',
            'nlm'       => '{author}. {title}{ed}. {publisher}; {publication_year}.',
            'aaa'       => '{author} {publication_year} <em>{title}</em>{ed}. {publisher}.',
            'apsa'      => '{author}. {publication_year}. <em>{title}</em>{ed}. {publisher}.',
            'oscola'    => '{author}, <em>{title}</em>{ed} ({publisher} {publication_year})',
            'nature'    => '{author}. <em>{title}</em>{ed} ({publisher}, {publication_year}).',
            'acm'       => '{author}. {publication_year}. <em>{title}</em>{ed}. {publisher}.',
            'bluebook'  => '{author}, <em>{title}</em> ({publication_year}).',
            'iso690'    => '{author}. <em>{title}</em>{ed}. {publisher}, {publication_year}.{isbn_iso}',
        ),
    );

    /**
     * Filters the citation templates (same idea as CitePress's citepress_citation_styles).
     *
     * @param array $templates array( 'web' => style => template, 'book' => style => template ).
     */
    $templates = apply_filters( 'langgam_fikir_cite_templates', $templates );
    return $templates;
}

/**
 * In-text (parenthetical) templates.
 */
function langgam_fikir_cite_inline_templates() {
    return apply_filters( 'langgam_fikir_cite_inline_templates', array(
        'apa'       => '({author}, {publication_year}{page})',
        'mla'       => '({author}{page})',
        'chicago'   => '({author} {publication_year}{page})',
        'harvard'   => '({author} {publication_year}{page})',
        'vancouver' => '({ref_number})',
        'ieee'      => '[{ref_number}]',
        'ama'       => '({ref_number})',
        'asa'       => '({author} {publication_year}{page})',
        'turabian'  => '({author} {publication_year}{page})',
        'wikipedia' => '({author} {publication_year})',
        'acs'       => '({ref_number})',
        'aip'       => '{ref_number}',
        'nlm'       => '({ref_number})',
        'aaa'       => '({author} {publication_year}{page})',
        'apsa'      => '({author} {publication_year}{page})',
        'oscola'    => '(n {ref_number})',
        'nature'    => '{ref_number}',
        'acm'       => '[{ref_number}]',
        'bluebook'  => '({author} {publication_year})',
        'iso690'    => '({author}, {publication_year}{page})',
    ) );
}

/* =========================================================================
 * Settings
 * ====================================================================== */

/**
 * Whether the CitePress plugin is still running (the theme then stays out of its way).
 */
function langgam_fikir_cite_plugin_active() {
    return defined( 'CITEPRESS_VERSION' ) || function_exists( 'citepress_display_citation' );
}

/**
 * Post types a citation box may appear on.
 */
function langgam_fikir_cite_types() {
    $types = array();
    if ( langgam_fikir_get_option( 'cite_on_posts', 1 ) ) {
        $types[] = 'post';
    }
    if ( langgam_fikir_get_option( 'cite_on_books', 1 ) ) {
        $types[] = 'book';
    }
    if ( langgam_fikir_get_option( 'cite_on_pages', 0 ) ) {
        $types[] = 'page';
    }
    return $types;
}

/**
 * IDs of posts that never get a citation box.
 */
function langgam_fikir_cite_excluded() {
    return array_filter( array_map( 'absint', explode( ',', (string) langgam_fikir_get_option( 'cite_exclude' ) ) ) );
}

/**
 * Comma list option to an array limited to the known keys, in the stored order.
 */
function langgam_fikir_cite_list( $key, $known ) {
    $list = array_filter( array_map( 'trim', explode( ',', (string) langgam_fikir_get_option( $key ) ) ) );
    return array_values( array_intersect( $list, array_keys( $known ) ) );
}

/* =========================================================================
 * The work being cited
 * ====================================================================== */

/**
 * People for a post: guest-author meta, Co-Authors Plus, then the WordPress author.
 * Books: the book's own author field ("A, B & C" become three people).
 *
 * @return string[]
 */
function langgam_fikir_cite_people( $post ) {
    if ( 'book' === $post->post_type ) {
        $names = trim( (string) get_post_meta( $post->ID, '_book_author', true ) );
        if ( '' !== $names ) {
            return array_values( array_filter( array_map( 'trim', preg_split( '/\s*(?:,|&|\band\b|\bdan\b)\s*/u', $names ) ) ) );
        }
        return array();
    }

    $guest = trim( (string) get_post_meta( $post->ID, 'guest-author', true ) );
    if ( '' !== $guest ) {
        return array( sanitize_text_field( $guest ) );
    }
    if ( function_exists( 'get_coauthors' ) ) {
        $people = array();
        foreach ( (array) get_coauthors( $post->ID ) as $coauthor ) {
            $people[] = sanitize_text_field( $coauthor->display_name );
        }
        if ( $people ) {
            return $people;
        }
    }
    return array( sanitize_text_field( get_the_author_meta( 'display_name', (int) $post->post_author ) ) );
}

/**
 * "A", "A & B", "A, B & C", or "A et al." past the et al. threshold.
 */
function langgam_fikir_cite_join_people( $people ) {
    $people = array_values( array_filter( (array) $people ) );
    if ( ! $people ) {
        return '';
    }
    $threshold = max( 1, (int) langgam_fikir_get_option( 'cite_et_al', 3 ) );
    if ( count( $people ) > $threshold ) {
        return $people[0] . ' et al.';
    }
    if ( count( $people ) === 1 ) {
        return $people[0];
    }
    $last = array_pop( $people );
    return implode( ', ', $people ) . ' & ' . $last;
}

/**
 * Everything a citation needs about a post or a book.
 *
 * @param WP_Post|int $post        The work.
 * @param string      $author_over Optional author text that replaces the real one.
 * @return array
 */
function langgam_fikir_cite_work( $post, $author_over = '' ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return array();
    }

    $is_book = ( 'book' === $post->post_type );
    $site    = html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $people  = '' !== trim( (string) $author_over ) ? array( sanitize_text_field( $author_over ) ) : langgam_fikir_cite_people( $post );
    $org     = function_exists( 'langgam_fikir_org_name' ) ? langgam_fikir_org_name() : $site;
    $lang    = function_exists( 'langgam_fikir_schema_lang' ) ? langgam_fikir_schema_lang( $post->ID ) : str_replace( '_', '-', get_locale() );

    $work = array(
        'id'        => (int) $post->ID,
        'kind'      => $is_book ? 'book' : 'web',
        'authors'   => $people,
        'author'    => langgam_fikir_cite_join_people( $people ),
        'title'     => html_entity_decode( get_the_title( $post ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
        'site'      => $site,
        'publisher' => $site,
        'url'       => get_permalink( $post ),
        'language'  => strtolower( substr( (string) $lang, 0, 2 ) ),
        'accessed'  => date_i18n( get_option( 'date_format' ) ),
        'accessedDMY' => date_i18n( 'j F Y' ),
        'accessedISO' => current_time( 'Y-m-d' ),
        'isbn'      => '',
        'edition'   => '',
    );

    if ( $is_book ) {
        $year      = trim( (string) get_post_meta( $post->ID, '_book_year', true ) );
        $publisher = trim( (string) get_post_meta( $post->ID, '_book_publisher', true ) );
        $work['year']       = preg_match( '/^\d{4}$/', $year ) ? $year : __( 'n.d.', 'langgam-fikir' );
        $work['published']  = $work['year'];
        $work['publishedDMY'] = $work['year'];
        $work['publishedISO'] = preg_match( '/^\d{4}$/', $year ) ? $year : '';
        $work['publisher']  = '' !== $publisher ? $publisher : $org;
        $work['isbn']       = strtoupper( preg_replace( '/[^0-9Xx]/', '', (string) get_post_meta( $post->ID, '_book_isbn', true ) ) );
        $work['edition']    = trim( (string) get_post_meta( $post->ID, '_book_edition', true ) );
        if ( ! $people ) {
            $work['author'] = $work['publisher'];
        }
    } else {
        $work['year']         = get_the_date( 'Y', $post );
        $work['published']    = get_the_date( '', $post );
        $work['publishedDMY'] = date_i18n( 'j F Y', get_post_time( 'U', false, $post ) );
        $work['publishedISO'] = get_the_date( 'Y-m-d', $post );
    }

    /**
     * Filters the data a citation is built from.
     *
     * @param array   $work The citation data.
     * @param WP_Post $post The cited post or book.
     */
    return apply_filters( 'langgam_fikir_cite_work', $work, $post );
}

/**
 * Render one citation as HTML (Wikipedia as plain markup, escaped).
 */
function langgam_fikir_cite_render( $work, $style ) {
    $templates = langgam_fikir_cite_templates();
    $set       = isset( $templates[ $work['kind'] ] ) ? $templates[ $work['kind'] ] : $templates['web'];
    $template  = isset( $set[ $style ] ) ? $set[ $style ] : $set['apa'];
    $e         = static function ( $text ) {
        return esc_html( (string) $text );
    };

    $tokens = array(
        '{author}'               => $e( $work['author'] ),
        '{title}'                => $e( $work['title'] ),
        '{sitename}'             => $e( $work['site'] ),
        '{publisher}'            => $e( $work['publisher'] ),
        '{permalink}'            => '<a href="' . esc_url( $work['url'] ) . '">' . $e( $work['url'] ) . '</a>',
        '{permalink_url}'        => $e( $work['url'] ),
        '{date}'                 => $e( $work['accessed'] ),
        '{date_dmy}'             => $e( $work['accessedDMY'] ),
        '{publication_date}'     => $e( $work['published'] ),
        '{publication_date_dmy}' => $e( $work['publishedDMY'] ),
        '{publication_year}'     => $e( $work['year'] ),
        '{language}'             => $e( $work['language'] ),
        '{isbn}'                 => $e( $work['isbn'] ),
        '{edition}'              => $e( $work['edition'] ),
        '{ed}'                   => '' !== $work['edition'] ? ' (' . $e( $work['edition'] ) . ')' : '',
        '{isbn_iso}'             => '' !== $work['isbn'] ? ' ISBN ' . $e( $work['isbn'] ) . '.' : '',
    );

    if ( 'wikipedia' === $style ) {
        // Plain wiki markup: no links, and empty parameters dropped.
        $tokens['{permalink}'] = $e( $work['url'] );
        $out = strtr( $template, $tokens );
        return preg_replace( '/ \|[a-z-]+=(?= \||\}\})/', '', $out );
    }

    $out = strtr( $template, $tokens );
    $out = preg_replace( array( '/\s+\./', '/\s+,/', '/\s{2,}/', '/\.\.+/' ), array( '.', ',', ' ', '.' ), $out );
    return trim( $out );
}

/* =========================================================================
 * Per-page state (reference numbering for [cite_bibliography])
 * ====================================================================== */

/**
 * Per-request state. Box ids count across the page; markers, references and the
 * "has a reference list" flag are kept per post being rendered (an archive can show several).
 */
function &langgam_fikir_cite_state() {
    static $state = array( 'instances' => 0, 'posts' => array() );
    return $state;
}

/**
 * State for the post whose content is being rendered (not the cited one).
 */
function &langgam_fikir_cite_post_state() {
    $state = &langgam_fikir_cite_state();
    $post  = get_post();
    $id    = $post ? (int) $post->ID : 0;
    if ( ! isset( $state['posts'][ $id ] ) ) {
        $content                = $post ? (string) $post->post_content : '';
        $state['posts'][ $id ]  = array(
            'markers' => 0,
            'refs'    => array(),
            'bib'     => $content && ( has_shortcode( $content, 'cite_bibliography' ) || has_shortcode( $content, 'citepress_cite_bibliography' ) || false !== strpos( $content, '"mode":"bibliography"' ) ),
        );
    }
    return $state['posts'][ $id ];
}

/**
 * Anchor of reference $number in the current post's list (post ID included, so two posts on
 * one page never share an id).
 */
function langgam_fikir_cite_ref_anchor( $number ) {
    $post = get_post();
    return 'lf-cite-ref-' . ( $post ? (int) $post->ID : 0 ) . '-' . (int) $number;
}

/**
 * Whether the current post has a reference list (shortcode or block in bibliography mode).
 */
function langgam_fikir_cite_has_bibliography() {
    $post_state = &langgam_fikir_cite_post_state();
    return $post_state['bib'];
}

/**
 * Register a cited work for the reference list; returns its number (shared by repeat citations).
 */
function langgam_fikir_cite_add_ref( $post_id, $style ) {
    $state = &langgam_fikir_cite_post_state();
    $state['markers']++;
    $number = 0;
    foreach ( $state['refs'] as $ref ) {
        if ( $ref['post_id'] === $post_id ) {
            $number = $ref['number'];
            break;
        }
    }
    if ( ! $number ) {
        $number = count( array_unique( wp_list_pluck( $state['refs'], 'post_id' ) ) ) + 1;
    }
    $state['refs'][] = array(
        'post_id' => $post_id,
        'number'  => $number,
        'marker'  => 'lf-cite-marker-' . ( get_post() ? (int) get_post()->ID : 0 ) . '-' . $state['markers'],
        'style'   => $style,
    );
    return array( $number, 'lf-cite-marker-' . ( get_post() ? (int) get_post()->ID : 0 ) . '-' . $state['markers'] );
}

/* =========================================================================
 * Output
 * ====================================================================== */

/**
 * [cite] / [citepress_cite] and the citepress/block block.
 *
 * Attributes (as in CitePress): style, mode (box|inline|bibliography), show_copy, show_export,
 * show_toggle, link, custom_author, formats, exclude_formats, page. New: id (cite another post).
 */
function langgam_fikir_cite_shortcode( $atts = array() ) {
    $atts = shortcode_atts( array(
        'id'              => 0,
        'style'           => '',
        'mode'            => 'box',
        'show_copy'       => 'true',
        'show_export'     => 'true',
        'show_toggle'     => 'true',
        'link'            => 'true',
        'custom_author'   => '',
        'formats'         => '',
        'exclude_formats' => '',
        'page'            => '',
    ), $atts, 'cite' );

    $post = $atts['id'] ? get_post( absint( $atts['id'] ) ) : get_post();
    if ( ! $post || ( $atts['id'] && 'publish' !== get_post_status( $post ) && ! current_user_can( 'read_post', $post->ID ) ) ) {
        return '';
    }
    if ( ! $atts['id'] && in_array( (int) $post->ID, langgam_fikir_cite_excluded(), true ) ) {
        return '';
    }

    $flag = static function ( $value ) {
        return in_array( strtolower( (string) $value ), array( 'true', '1', 'yes' ), true );
    };

    // Styles offered here: the shortcode's own list, or the Theme Options list, minus exclusions.
    $all     = langgam_fikir_cite_format_names();
    $formats = '' !== $atts['formats'] ? array_values( array_intersect( array_map( 'trim', explode( ',', sanitize_text_field( $atts['formats'] ) ) ), array_keys( $all ) ) ) : langgam_fikir_cite_list( 'cite_formats', $all );
    if ( '' !== $atts['exclude_formats'] ) {
        $formats = array_values( array_diff( $formats, array_map( 'trim', explode( ',', sanitize_text_field( $atts['exclude_formats'] ) ) ) ) );
    }
    if ( ! $formats ) {
        $formats = array( 'apa' );
    }
    $style = in_array( $atts['style'], $formats, true ) ? $atts['style'] : $formats[0];

    $mode = in_array( $atts['mode'], array( 'box', 'inline', 'bibliography' ), true ) ? $atts['mode'] : 'box';
    $work = langgam_fikir_cite_work( $post, $atts['custom_author'] );

    if ( 'inline' === $mode ) {
        return langgam_fikir_cite_inline( $work, $style, $atts['page'], $flag( $atts['link'] ) );
    }
    if ( 'bibliography' === $mode ) {
        return '<span class="lf-cite-reference">' . langgam_fikir_cite_render( $work, $style ) . '</span>';
    }

    // A box inside a post that has a reference list becomes a numbered marker.
    if ( langgam_fikir_cite_has_bibliography() ) {
        list( $number, $marker ) = langgam_fikir_cite_add_ref( $work['id'], $style );
        return sprintf(
            '<sup class="lf-cite-marker" id="%1$s"><a href="#%4$s" role="doc-noteref" aria-label="%3$s">[%2$d]</a></sup>',
            esc_attr( $marker ),
            (int) $number,
            /* translators: %d: reference number */
            esc_attr( sprintf( __( 'See reference %d', 'langgam-fikir' ), $number ) ),
            esc_attr( langgam_fikir_cite_ref_anchor( $number ) )
        );
    }

    return langgam_fikir_cite_box( $work, $formats, $style, array(
        'copy'   => $flag( $atts['show_copy'] ),
        'export' => $flag( $atts['show_export'] ),
        'toggle' => $flag( $atts['show_toggle'] ) && langgam_fikir_get_option( 'cite_toggle', 1 ),
    ) );
}

/**
 * A parenthetical in-text citation.
 */
function langgam_fikir_cite_inline( $work, $style, $page, $link ) {
    $surnames = array();
    foreach ( (array) $work['authors'] as $name ) {
        $parts      = preg_split( '/\s+/u', trim( $name ) );
        $surnames[] = end( $parts );
    }
    $who = $surnames ? langgam_fikir_cite_join_people( $surnames ) : $work['author'];

    $templates = langgam_fikir_cite_inline_templates();
    $template  = isset( $templates[ $style ] ) ? $templates[ $style ] : '({author}, {publication_year})';
    $numbered  = false !== strpos( $template, '{ref_number}' );
    $bib       = langgam_fikir_cite_has_bibliography();
    if ( $numbered && ! $bib ) {
        $template = '({author}, {publication_year}{page})';
    }

    $number = 0;
    $marker = '';
    if ( $bib ) {
        list( $number, $marker ) = langgam_fikir_cite_add_ref( $work['id'], $style );
    }

    $text = strtr( $template, array(
        '{author}'           => esc_html( $who ),
        '{publication_year}' => esc_html( $work['year'] ),
        '{ref_number}'       => (int) $number,
        '{page}'             => '' !== trim( (string) $page ) ? ', p. ' . esc_html( sanitize_text_field( $page ) ) : '',
    ) );

    $html = '<span class="lf-cite-inline"' . ( $marker ? ' id="' . esc_attr( $marker ) . '"' : '' ) . '>';
    if ( $link ) {
        $href  = $bib ? '#' . esc_attr( langgam_fikir_cite_ref_anchor( $number ) ) : esc_url( $work['url'] );
        $html .= '<a href="' . $href . '"' . ( $bib ? ' role="doc-noteref"' : '' ) . '>' . $text . '</a>';
    } else {
        $html .= $text;
    }
    return $html . '</span>';
}

/**
 * The citation box.
 *
 * @param array $work    langgam_fikir_cite_work().
 * @param array $formats Style keys offered.
 * @param string $style  The style shown first.
 * @param array $show    copy, export, toggle.
 */
function langgam_fikir_cite_box( $work, $formats, $style, $show ) {
    if ( ! $work ) {
        return '';
    }
    langgam_fikir_cite_enqueue();

    $state = &langgam_fikir_cite_state();
    $state['instances']++;
    $uid     = 'lf-cite-' . $state['instances'];
    $names   = langgam_fikir_cite_format_names();
    $picker  = (string) langgam_fikir_get_option( 'cite_picker', 'dropdown' );
    $box     = (string) langgam_fikir_get_option( 'cite_box', 'classic' );
    $exports = $show['export'] ? langgam_fikir_cite_list( 'cite_exports', langgam_fikir_cite_export_names() ) : array();
    $title   = 'book' === $work['kind'] ? __( 'Cite this book', 'langgam-fikir' ) : __( 'Cite this article', 'langgam-fikir' );

    // What the script needs to switch styles and build downloads.
    $data = $work;

    ob_start();
    ?>
    <section class="lf-cite citepress-citation-box" id="<?php echo esc_attr( $uid ); ?>" data-box="<?php echo esc_attr( $box ); ?>" data-picker="<?php echo esc_attr( $picker ); ?>" aria-labelledby="<?php echo esc_attr( $uid ); ?>-title" data-cite="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
        <div class="lf-cite__head">
            <p class="lf-cite__title" id="<?php echo esc_attr( $uid ); ?>-title">
                <svg class="lf-cite__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8.5 5.5C8.5 4.12 7.38 3 6 3S3.5 4.12 3.5 5.5c0 1.03.63 1.92 1.52 2.3-.15 1.52-.78 2.87-1.87 3.93a.5.5 0 0 0 .35.85c2.14 0 3.92-1.15 4.67-2.85.23-.53.33-1.1.33-1.73V5.5zm8 0C16.5 4.12 15.38 3 14 3s-2.5 1.12-2.5 2.5c0 1.03.63 1.92 1.52 2.3-.15 1.52-.78 2.87-1.87 3.93a.5.5 0 0 0 .35.85c2.14 0 3.92-1.15 4.67-2.85.23-.53.33-1.1.33-1.73V5.5z"/></svg>
                <span><?php echo esc_html( $title ); ?></span>
            </p>

            <?php if ( count( $formats ) > 1 ) : ?>
                <?php if ( 'tabs' === $picker || 'buttons' === $picker ) : ?>
                    <div class="lf-cite__styles lf-cite__styles--<?php echo esc_attr( $picker ); ?>" role="<?php echo 'tabs' === $picker ? 'tablist' : 'radiogroup'; ?>" aria-label="<?php esc_attr_e( 'Citation style', 'langgam-fikir' ); ?>">
                        <?php foreach ( $formats as $key ) : ?>
                            <?php $on = ( $key === $style ); ?>
                            <button type="button" class="lf-cite__style<?php echo $on ? ' is-active' : ''; ?>" data-style="<?php echo esc_attr( $key ); ?>"
                                <?php if ( 'tabs' === $picker ) : ?>role="tab" aria-selected="<?php echo $on ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $uid ); ?>-body" tabindex="<?php echo $on ? '0' : '-1'; ?>"<?php else : ?>role="radio" aria-checked="<?php echo $on ? 'true' : 'false'; ?>"<?php endif; ?>>
                                <?php echo esc_html( $names[ $key ] ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <label class="screen-reader-text" for="<?php echo esc_attr( $uid ); ?>-select"><?php esc_html_e( 'Citation style', 'langgam-fikir' ); ?></label>
                    <select class="lf-cite__select" id="<?php echo esc_attr( $uid ); ?>-select">
                        <?php foreach ( $formats as $key ) : ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $style ); ?>><?php echo esc_html( $names[ $key ] ); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            <?php else : ?>
                <span class="lf-cite__single"><?php echo esc_html( $names[ $formats[0] ] ); ?></span>
            <?php endif; ?>

            <div class="lf-cite__actions">
                <?php if ( $show['copy'] ) : ?>
                    <button type="button" class="lf-cite__btn lf-cite__copy" hidden>
                        <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.5 2h-7C5.67 2 5 2.67 5 3.5v7c0 .83.67 1.5 1.5 1.5h7c.83 0 1.5-.67 1.5-1.5v-7c0-.83-.67-1.5-1.5-1.5Zm0 8.5h-7v-7h7v7ZM2.5 6H4v1.5H2.5v5h7V11H11v1.5c0 .83-.67 1.5-1.5 1.5h-7c-.83 0-1.5-.67-1.5-1.5v-5C1 6.67 1.67 6 2.5 6Z"/></svg>
                        <span><?php esc_html_e( 'Copy', 'langgam-fikir' ); ?></span>
                    </button>
                <?php endif; ?>

                <?php if ( $exports ) : ?>
                    <div class="lf-cite__export" hidden>
                        <button type="button" class="lf-cite__btn lf-cite__export-btn" aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr( $uid ); ?>-menu">
                            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M8 11 3.5 6.5l1.06-1.06L7.25 8.13V1h1.5v7.13l2.69-2.69 1.06 1.06L8 11Zm-6 2.5h12V15H2v-1.5Z"/></svg>
                            <span><?php esc_html_e( 'Download', 'langgam-fikir' ); ?></span>
                        </button>
                        <ul class="lf-cite__menu" id="<?php echo esc_attr( $uid ); ?>-menu" role="menu" hidden>
                            <?php foreach ( $exports as $key ) : ?>
                                <li role="none"><button type="button" role="menuitem" class="lf-cite__menu-item" data-format="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( langgam_fikir_cite_export_names()[ $key ] ); ?></button></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ( $show['toggle'] ) : ?>
                    <button type="button" class="lf-cite__btn lf-cite__toggle" aria-expanded="true" aria-controls="<?php echo esc_attr( $uid ); ?>-body" hidden>
                        <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M8 11 3 6l1.06-1.06L8 8.88l3.94-3.94L13 6l-5 5Z"/></svg>
                        <span class="screen-reader-text"><?php esc_html_e( 'Show or hide the citation', 'langgam-fikir' ); ?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="lf-cite__body" id="<?php echo esc_attr( $uid ); ?>-body"<?php echo 'tabs' === $picker && count( $formats ) > 1 ? ' role="tabpanel"' : ''; ?>>
            <p class="lf-cite__output<?php echo 'wikipedia' === $style ? ' is-markup' : ''; ?>" data-style="<?php echo esc_attr( $style ); ?>"><?php echo langgam_fikir_cite_render( $work, $style ); // phpcs:ignore WordPress.Security.EscapeOutput -- every token is escaped in langgam_fikir_cite_render() ?></p>
        </div>
        <p class="lf-cite__status" role="status" aria-live="polite"></p>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * [cite_bibliography] / [citepress_cite_bibliography]: the numbered reference list.
 */
function langgam_fikir_cite_bibliography( $atts = array() ) {
    $atts = shortcode_atts( array(
        'style'       => '',
        'heading'     => __( 'References', 'langgam-fikir' ),
        'heading_tag' => 'h2',
        'numbered'    => 'true',
        'link_back'   => 'true',
    ), $atts, 'cite_bibliography' );

    $state = &langgam_fikir_cite_post_state();
    if ( ! $state['refs'] ) {
        return '';
    }
    langgam_fikir_cite_enqueue();

    $formats = langgam_fikir_cite_list( 'cite_formats', langgam_fikir_cite_format_names() );
    $style   = isset( langgam_fikir_cite_format_names()[ $atts['style'] ] ) ? $atts['style'] : ( $formats ? $formats[0] : 'apa' );
    $tag     = in_array( $atts['heading_tag'], array( 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ? $atts['heading_tag'] : 'h2';
    $list    = in_array( $atts['numbered'], array( 'true', '1' ), true ) ? 'ol' : 'ul';
    $back    = in_array( $atts['link_back'], array( 'true', '1' ), true );

    $entries = array();
    $markers = array();
    foreach ( $state['refs'] as $ref ) {
        $entries[ $ref['number'] ]   = $ref['post_id'];
        $markers[ $ref['number'] ][] = $ref['marker'];
    }
    ksort( $entries );

    $heading = apply_filters( 'langgam_fikir_cite_bibliography_heading', $atts['heading'] );
    $out     = '<section class="lf-cite-references" role="doc-endnotes">';
    if ( '' !== trim( (string) $heading ) ) {
        $out .= '<' . $tag . ' class="lf-cite-references__heading">' . esc_html( $heading ) . '</' . $tag . '>';
    }
    $out .= '<' . $list . ' class="lf-cite-references__list">';
    foreach ( $entries as $number => $post_id ) {
        $work = langgam_fikir_cite_work( $post_id );
        if ( ! $work ) {
            continue;
        }
        $links = '';
        if ( $back ) {
            foreach ( $markers[ $number ] as $i => $marker ) {
                $label  = count( $markers[ $number ] ) > 1 ? chr( 97 + $i ) : '↩';
                /* translators: %d: reference number */
                $links .= ' <a href="#' . esc_attr( $marker ) . '" class="lf-cite-references__back" aria-label="' . esc_attr( sprintf( __( 'Back to citation %d in the text', 'langgam-fikir' ), $number ) ) . '">' . esc_html( $label ) . '</a>';
            }
        }
        $out .= '<li id="' . esc_attr( langgam_fikir_cite_ref_anchor( $number ) ) . '" role="doc-endnote">' . langgam_fikir_cite_render( $work, $style ) . $links . '</li>';
    }
    $out .= '</' . $list . '></section>';

    return apply_filters( 'langgam_fikir_cite_bibliography_output', $out, $entries );
}

/**
 * Whether the automatic box belongs on the current singular view.
 */
function langgam_fikir_cite_auto_applies( $post = null ) {
    $post = get_post( $post );
    return $post
        && langgam_fikir_get_option( 'cite_auto', 0 )
        && is_singular( $post->post_type )
        && in_array( $post->post_type, langgam_fikir_cite_types(), true )
        && ! in_array( (int) $post->ID, langgam_fikir_cite_excluded(), true );
}

/**
 * Automatic box on articles and pages (book pages place it themselves, under the synopsis).
 */
function langgam_fikir_cite_auto_content( $content ) {
    if ( ! in_the_loop() || ! is_main_query() || 'book' === get_post_type() || ! langgam_fikir_cite_auto_applies() ) {
        return $content;
    }
    $box = langgam_fikir_cite_shortcode( array() );
    return 'top' === langgam_fikir_get_option( 'cite_position', 'bottom' ) ? $box . $content : $content . $box;
}

/**
 * Template tag for single-book.php: the automatic box for the current book.
 */
function langgam_fikir_cite_book_box() {
    if ( ! langgam_fikir_cite_plugin_active() && langgam_fikir_cite_auto_applies() ) {
        echo langgam_fikir_cite_shortcode( array() ); // phpcs:ignore WordPress.Security.EscapeOutput -- built and escaped above
    }
}

/* =========================================================================
 * Assets and the block
 * ====================================================================== */

/**
 * Stylesheet and script, once, only on pages that show a citation.
 */
function langgam_fikir_cite_enqueue() {
    static $done = false;
    if ( $done || is_admin() && ! wp_doing_ajax() && ! defined( 'REST_REQUEST' ) ) {
        return;
    }
    $done = true;
    $dir  = get_stylesheet_directory();
    $uri  = get_stylesheet_directory_uri();

    wp_enqueue_style( 'langgam-fikir-citations', $uri . '/assets/css/citations.css', array(), filemtime( $dir . '/assets/css/citations.css' ) );
    wp_enqueue_script( 'langgam-fikir-citations', $uri . '/assets/js/citations.js', array(), filemtime( $dir . '/assets/js/citations.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
    wp_localize_script( 'langgam-fikir-citations', 'lfCite', array(
        'templates' => langgam_fikir_cite_templates(),
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'analytics' => (bool) langgam_fikir_get_option( 'cite_analytics', 0 ),
        'i18n'      => array(
            'copied'     => __( 'Citation copied.', 'langgam-fikir' ),
            'copyFailed' => __( 'Copying failed. Select the citation and copy it by hand.', 'langgam-fikir' ),
            'downloaded' => __( 'Download started.', 'langgam-fikir' ),
            'message'    => __( 'If you use this work, please cite it as below.', 'langgam-fikir' ),
        ),
    ) );
}

/**
 * Load the assets in <head> when the page will show a citation, so the box never flashes unstyled.
 */
function langgam_fikir_cite_maybe_enqueue() {
    $post = get_post();
    if ( ! $post || ! is_singular() ) {
        return;
    }
    $content = (string) $post->post_content;
    if ( langgam_fikir_cite_auto_applies( $post )
        || has_shortcode( $content, 'cite' ) || has_shortcode( $content, 'citepress_cite' )
        || has_shortcode( $content, 'cite_bibliography' ) || has_shortcode( $content, 'citepress_cite_bibliography' )
        || has_block( 'citepress/block', $post ) ) {
        langgam_fikir_cite_enqueue();
    }
}

/**
 * The citepress/block block (same name and attributes as CitePress, plus postId).
 */
function langgam_fikir_cite_register_block() {
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();

    wp_register_script( 'langgam-fikir-citations-block', $uri . '/assets/js/citations-block.js', array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-i18n', 'wp-server-side-render' ), filemtime( $dir . '/assets/js/citations-block.js' ), true );
    wp_register_style( 'langgam-fikir-citations-editor', $uri . '/assets/css/citations.css', array(), filemtime( $dir . '/assets/css/citations.css' ) );

    $names   = langgam_fikir_cite_format_names();
    $options = array();
    foreach ( $names as $value => $label ) {
        $options[] = array( 'value' => $value, 'label' => $label );
    }
    wp_localize_script( 'langgam-fikir-citations-block', 'lfCiteBlock', array( 'formats' => $options ) );
    if ( function_exists( 'wp_set_script_translations' ) ) {
        wp_set_script_translations( 'langgam-fikir-citations-block', 'langgam-fikir', $dir . '/languages' );
    }

    register_block_type( 'citepress/block', array(
        'api_version'     => 2,
        'title'           => __( 'Citation', 'langgam-fikir' ),
        'editor_script'   => 'langgam-fikir-citations-block',
        'editor_style'    => 'langgam-fikir-citations-editor',
        'attributes'      => array(
            'citationStyle'  => array( 'type' => 'string',  'default' => 'apa' ),
            'mode'           => array( 'type' => 'string',  'default' => 'box' ),
            'showExport'     => array( 'type' => 'boolean', 'default' => true ),
            'showToggle'     => array( 'type' => 'boolean', 'default' => true ),
            'showCopy'       => array( 'type' => 'boolean', 'default' => true ),
            'showLink'       => array( 'type' => 'boolean', 'default' => true ),
            'customAuthor'   => array( 'type' => 'string',  'default' => '' ),
            'formats'        => array( 'type' => 'string',  'default' => '' ),
            'excludeFormats' => array( 'type' => 'string',  'default' => '' ),
            'page'           => array( 'type' => 'string',  'default' => '' ),
            'postId'         => array( 'type' => 'number',  'default' => 0 ),
        ),
        'render_callback' => 'langgam_fikir_cite_render_block',
    ) );
}

/**
 * Block attributes to shortcode attributes.
 */
function langgam_fikir_cite_render_block( $a ) {
    $yes = static function ( $key ) use ( $a ) {
        return ! isset( $a[ $key ] ) || $a[ $key ] ? 'true' : 'false';
    };
    return langgam_fikir_cite_shortcode( array(
        'id'              => isset( $a['postId'] ) ? absint( $a['postId'] ) : 0,
        'style'           => isset( $a['citationStyle'] ) ? $a['citationStyle'] : '',
        'mode'            => isset( $a['mode'] ) ? $a['mode'] : 'box',
        'show_copy'       => $yes( 'showCopy' ),
        'show_export'     => $yes( 'showExport' ),
        'show_toggle'     => $yes( 'showToggle' ),
        'link'            => $yes( 'showLink' ),
        'custom_author'   => isset( $a['customAuthor'] ) ? $a['customAuthor'] : '',
        'formats'         => isset( $a['formats'] ) ? $a['formats'] : '',
        'exclude_formats' => isset( $a['excludeFormats'] ) ? $a['excludeFormats'] : '',
        'page'            => isset( $a['page'] ) ? $a['page'] : '',
    ) );
}

/* =========================================================================
 * Scholarly metadata
 * ====================================================================== */

/**
 * Google Scholar (Highwire Press) tags for articles and books with citations on.
 */
function langgam_fikir_cite_scholar_tags() {
    if ( ! is_singular() || ! langgam_fikir_get_option( 'cite_scholar', 1 ) ) {
        return;
    }
    $post = get_queried_object();
    if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, langgam_fikir_cite_types(), true ) ) {
        return;
    }

    $work = langgam_fikir_cite_work( $post );
    $tags = array( array( 'citation_title', $work['title'] ) );
    foreach ( $work['authors'] as $person ) {
        $tags[] = array( 'citation_author', $person );
    }
    if ( 'book' === $work['kind'] ) {
        if ( '' !== $work['publishedISO'] ) {
            $tags[] = array( 'citation_publication_date', $work['publishedISO'] );
        }
        $tags[] = array( 'citation_publisher', $work['publisher'] );
        if ( '' !== $work['isbn'] ) {
            $tags[] = array( 'citation_isbn', $work['isbn'] );
        }
    } else {
        $tags[] = array( 'citation_publication_date', get_the_date( 'Y/m/d', $post ) );
        $tags[] = array( 'citation_journal_title', $work['site'] );
        $orcid  = trim( (string) get_post_meta( $post->ID, 'orcid', true ) );
        if ( '' !== $orcid ) {
            $tags[] = array( 'citation_author_orcid', $orcid );
        }
    }
    $tags[] = array( 'citation_language', $work['language'] );
    $tags[] = array( 'citation_public_url', $work['url'] );
    $tags[] = array( 'citation_fulltext_html_url', $work['url'] );

    /**
     * Filters the Google Scholar tags: list of array( name, content ).
     */
    $tags = apply_filters( 'langgam_fikir_cite_scholar_tags', $tags, $post );

    echo "<!-- Google Scholar -->\n";
    foreach ( $tags as $tag ) {
        if ( '' !== trim( (string) $tag[1] ) ) {
            printf( '<meta name="%s" content="%s">' . "\n", esc_attr( $tag[0] ), esc_attr( $tag[1] ) );
        }
    }
}

/**
 * Articles with citations on are ScholarlyArticle as well as BlogPosting (one node, not a copy).
 */
function langgam_fikir_cite_scholarly_type( $article, $post_id ) {
    if ( langgam_fikir_get_option( 'cite_schema', 1 ) && in_array( get_post_type( $post_id ), langgam_fikir_cite_types(), true ) ) {
        $article['@type'] = array( 'BlogPosting', 'ScholarlyArticle' );
    }
    return $article;
}

/* =========================================================================
 * Hooks (only while CitePress itself is not running)
 * ====================================================================== */

function langgam_fikir_cite_boot() {
    if ( langgam_fikir_cite_plugin_active() ) {
        return;
    }
    add_shortcode( 'cite', 'langgam_fikir_cite_shortcode' );
    add_shortcode( 'citepress_cite', 'langgam_fikir_cite_shortcode' );
    add_shortcode( 'cite_bibliography', 'langgam_fikir_cite_bibliography' );
    add_shortcode( 'citepress_cite_bibliography', 'langgam_fikir_cite_bibliography' );

    add_filter( 'the_content', 'langgam_fikir_cite_auto_content', 20 );
    add_action( 'wp_enqueue_scripts', 'langgam_fikir_cite_maybe_enqueue' );
    add_action( 'wp_head', 'langgam_fikir_cite_scholar_tags', 4 );
    add_filter( 'langgam_fikir_schema_article_node', 'langgam_fikir_cite_scholarly_type', 10, 2 );

    langgam_fikir_cite_register_block();
}
add_action( 'init', 'langgam_fikir_cite_boot', 20 );

/* =========================================================================
 * Import from CitePress (called once by inc/plugin-takeover.php)
 * ====================================================================== */

/**
 * Map the plugin's citepress_setting option to Theme Options values, and copy its analytics rows.
 *
 * @return array Theme option values.
 */
function langgam_fikir_cite_import_plugin_settings() {
    $old = get_option( 'citepress_setting', null );
    if ( ! is_array( $old ) ) {
        return array();
    }
    $yes = static function ( $key, $default ) use ( $old ) {
        return isset( $old[ $key ] ) ? ( 'yes' === $old[ $key ] ? 1 : 0 ) : $default;
    };
    $pick = static function ( $key, $allowed, $default ) use ( $old ) {
        return isset( $old[ $key ] ) && in_array( (string) $old[ $key ], $allowed, true ) ? (string) $old[ $key ] : $default;
    };

    $types  = isset( $old['post_types'] ) ? (array) $old['post_types'] : array( 'post' );
    $styles = array_values( array_intersect( isset( $old['enabled_formats'] ) ? (array) $old['enabled_formats'] : array_keys( langgam_fikir_cite_format_names() ), array_keys( langgam_fikir_cite_format_names() ) ) );
    $exp    = array_values( array_intersect( isset( $old['enabled_export_formats'] ) ? (array) $old['enabled_export_formats'] : array_keys( langgam_fikir_cite_export_names() ), array_keys( langgam_fikir_cite_export_names() ) ) );

    $values = array(
        'cite_auto'      => $yes( 'auto_display', 0 ),
        'cite_position'  => $pick( 'display_position', array( 'top', 'bottom' ), 'bottom' ),
        'cite_on_posts'  => in_array( 'post', $types, true ) ? 1 : 0,
        // Book pages now get real book citations, so they are switched on whenever the plugin was on posts.
        'cite_on_books'  => ( in_array( 'book', $types, true ) || in_array( 'post', $types, true ) ) ? 1 : 0,
        'cite_on_pages'  => in_array( 'page', $types, true ) ? 1 : 0,
        'cite_exclude'   => implode( ',', array_filter( array_map( 'absint', explode( ',', isset( $old['excluded_posts'] ) ? (string) $old['excluded_posts'] : '' ) ) ) ),
        'cite_formats'   => implode( ',', $styles ? $styles : array( 'apa' ) ),
        'cite_exports'   => implode( ',', $exp ),
        'cite_picker'    => $pick( 'format_display_mode', array( 'dropdown', 'tabs', 'buttons' ), 'dropdown' ),
        'cite_box'       => $pick( 'box_style', array( 'classic', 'academic', 'minimal' ), 'classic' ),
        'cite_toggle'    => $yes( 'show_toggle', 1 ),
        'cite_et_al'     => isset( $old['et_al_threshold'] ) ? max( 1, min( 999, absint( $old['et_al_threshold'] ) ) ) : 3,
        'cite_scholar'   => $yes( 'show_google_scholar', 1 ),
        'cite_schema'    => $yes( 'show_jsonld', 1 ),
        'cite_analytics' => $yes( 'enable_analytics', 0 ),
        'cite_consent'   => $yes( 'require_consent_for_analytics', 1 ),
        'cite_cooldown'  => $pick( 'analytics_cooldown_mode', array( 'session', 'ip_hash', 'none' ), 'session' ),
        'cite_retention' => $pick( 'analytics_retention_days', array( '0', '30', '90', '180', '365' ), '90' ),
    );

    if ( function_exists( 'langgam_fikir_cite_analytics_import' ) ) {
        langgam_fikir_cite_analytics_import();
    }
    return $values;
}
