<?php
/**
 * Auto Table of Contents
 *
 * Auto-generates an anchor-link TOC from H2 headings inside the_content()
 * for long-form posts (default threshold: 1200 words). Adds slugified `id`
 * attributes to H2 elements via a content filter, and exposes a template
 * tag `langgam_fikir_render_toc()` for rendering the TOC list.
 *
 * @package Langgam_Fikir
 * @since 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Word-count threshold for auto-TOC generation. Filterable.
 *
 * @return int Threshold in words.
 */
function langgam_fikir_toc_threshold() {
    return (int) apply_filters( 'langgam_fikir_toc_threshold', 1200 );
}

/**
 * Whether the current post qualifies for auto-TOC.
 *
 * Conditions: must be singular post, must have at least 2 H2 headings,
 * and content must exceed the threshold word count.
 *
 * @return bool
 */
function langgam_fikir_should_render_toc() {
    if ( ! is_singular( 'post' ) ) {
        return false;
    }

    $post = get_post();
    if ( ! $post || empty( $post->post_content ) ) {
        return false;
    }

    $word_count = str_word_count( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
    if ( $word_count < langgam_fikir_toc_threshold() ) {
        return false;
    }

    // Need at least 2 H2 headings to be useful
    if ( ! preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/i', $post->post_content, $matches ) ) {
        return false;
    }
    if ( count( $matches[0] ) < 2 ) {
        return false;
    }

    return true;
}

/**
 * Add `id` attributes to H2 elements inside the_content() so anchor links
 * resolve correctly. Skips H2s that already have an id.
 *
 * Also caches the extracted heading list on the post for retrieval by
 * langgam_fikir_render_toc() without re-parsing.
 *
 * @param string $content Post content.
 * @return string Modified content.
 */
function langgam_fikir_add_h2_ids( $content ) {
    if ( ! langgam_fikir_should_render_toc() ) {
        return $content;
    }

    $headings   = array();
    $used_slugs = array();

    $content = preg_replace_callback(
        '/<h2(?P<attrs>[^>]*)>(?P<text>.*?)<\/h2>/is',
        function ( $m ) use ( &$headings, &$used_slugs ) {
            $attrs = $m['attrs'];
            $text  = wp_strip_all_tags( $m['text'] );

            // If H2 already has an id, respect it
            if ( preg_match( '/\bid=["\']([^"\']+)["\']/', $attrs, $idm ) ) {
                $slug = $idm[1];
            } else {
                $slug = sanitize_title( $text );
                if ( empty( $slug ) ) {
                    $slug = 'section-' . ( count( $headings ) + 1 );
                }
                // Ensure uniqueness
                $base    = $slug;
                $counter = 2;
                while ( in_array( $slug, $used_slugs, true ) ) {
                    $slug = $base . '-' . $counter;
                    $counter++;
                }
                $attrs = ' id="' . esc_attr( $slug ) . '"' . $attrs;
            }

            $used_slugs[] = $slug;
            $headings[]   = array(
                'slug' => $slug,
                'text' => $text,
            );

            return '<h2' . $attrs . '>' . $m['text'] . '</h2>';
        },
        $content
    );

    // Stash extracted headings on the post object for the renderer
    $post = get_post();
    if ( $post ) {
        $post->langgam_fikir_toc_headings = $headings;
    }

    return $content;
}
add_filter( 'the_content', 'langgam_fikir_add_h2_ids', 9 );

/**
 * Render the auto-generated table of contents.
 *
 * Must be called AFTER the_content() (or at least after WordPress has run
 * the_content filters once) since it depends on the heading cache built
 * by langgam_fikir_add_h2_ids().
 *
 * Renders nothing when conditions aren't met.
 */
function langgam_fikir_render_toc() {
    if ( ! langgam_fikir_should_render_toc() ) {
        return;
    }

    $post = get_post();
    if ( ! $post ) {
        return;
    }

    // Build heading cache if the filter hasn't fired yet (e.g. TOC rendered before the_content)
    if ( ! isset( $post->langgam_fikir_toc_headings ) ) {
        // Trigger the filter to populate the cache without echoing content
        apply_filters( 'the_content', $post->post_content );
    }

    $headings = isset( $post->langgam_fikir_toc_headings ) ? $post->langgam_fikir_toc_headings : array();

    if ( count( $headings ) < 2 ) {
        return;
    }
    ?>
    <nav class="lf-toc" aria-labelledby="lf-toc-heading">
        <h2 id="lf-toc-heading" class="lf-toc-heading">
            <?php esc_html_e( 'Contents', 'langgam-fikir' ); ?>
        </h2>
        <ol class="lf-toc-list">
            <?php foreach ( $headings as $h ) : ?>
                <li class="lf-toc-item">
                    <a href="#<?php echo esc_attr( $h['slug'] ); ?>"><?php echo esc_html( $h['text'] ); ?></a>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}
