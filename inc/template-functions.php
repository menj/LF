<?php
/**
 * Template Helper Functions
 *
 * @package Langgam_Fikir
 * @since 1.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ─── Note on HrefMaster Integration ─────────────────────────────────
 *
 * Per-post HTML lang and hreflang concerns are delegated to the
 * HrefMaster plugin when active. See `inc/hrefmaster-integration.php`
 * for the detection helper, page-language resolver, locale-switching
 * for singular pages with a per-post lang override, and the legacy
 * `langgam_fikir_hreflang_to_locale()` compatibility shim.
 */


/**
 * Get Book Meta Data Helper Function
 */
function langgam_fikir_get_book_meta( $post_id, $key ) {
    return get_post_meta( $post_id, '_book_' . $key, true );
}

/**
 * Default theme option values
 */
function langgam_fikir_option_defaults() {
    return array(
        'hero_text'                => __( 'Book publisher of ideas that transcend borders—bringing critical, thought-provoking works to readers who seek truth.', 'langgam-fikir' ),
        'hero_background_image'    => '',
        'featured_books_count'     => 6,
        'featured_resources_count' => 3,
        'contact_email'            => '',
        'footer_business_name'     => 'Langgam Fikir Enterprise',
        'footer_address'           => '',
        'footer_phone'             => '',
        'footer_email'             => '',
        'legal_entity_name'        => 'Langgam Fikir Enterprise',
        'registration_number'      => '',
        // Social
        'social_facebook'          => '',
        'social_instagram'         => '',
        'social_twitter'           => '',
        'social_whatsapp'          => '',
        // SEO
        'default_meta_description' => '',
        // Homepage badge
        'about_badge_number'       => '2+',
        'about_badge_label'        => __( 'Years of Excellence', 'langgam-fikir' ),
        // Featured showcase
        'showcase_enabled'         => 1,
        'showcase_heading'         => '',
        'showcase_count'           => 6,
        'showcase_autoplay'        => 1,
        'showcase_scope'           => 'all',
        // WebMCP agent tools
        'webmcp_enabled'            => 1,
        'webmcp_contact_form'       => 1,
        'webmcp_origin_trial_token' => '',
    );
}

/**
 * Get a single theme option
 *
 * @param string $key     Option key.
 * @param mixed  $default Override default value.
 * @return mixed
 */
function langgam_fikir_get_option( $key, $default = null ) {
    $defaults = langgam_fikir_option_defaults();
    $options  = get_option( 'langgam_fikir_options', array() );
    $options  = wp_parse_args( $options, $defaults );

    if ( null !== $default ) {
        return isset( $options[ $key ] ) && '' !== $options[ $key ] ? $options[ $key ] : $default;
    }

    return isset( $options[ $key ] ) ? $options[ $key ] : '';
}

/**
 * Get human-readable label for a book status
 */
function langgam_fikir_get_status_label( $status ) {
    $labels = array(
        'announced'       => __( 'Announced', 'langgam-fikir' ),
        'published'       => __( 'Published', 'langgam-fikir' ),
        'new-release'     => __( 'New Release', 'langgam-fikir' ),
        'limited-edition' => __( 'Limited Edition', 'langgam-fikir' ),
        'digital-only'    => __( 'Digital Only', 'langgam-fikir' ),
        'pre-order'       => __( 'Pre-Order', 'langgam-fikir' ),
        'coming-soon'     => __( 'Coming Soon', 'langgam-fikir' ),
        'sold-out'        => __( 'Sold Out', 'langgam-fikir' ),
        'reprinting'      => __( 'Reprinting', 'langgam-fikir' ),
        'out-of-print'    => __( 'Out of Print', 'langgam-fikir' ),
    );
    return isset( $labels[ $status ] ) ? $labels[ $status ] : ucwords( str_replace( '-', ' ', $status ) );
}

/**
 * Render a status badge span element
 *
 * @param string $status  The book status slug.
 * @param string $class   CSS class prefix ('book-status' for cards, 'book-status-badge' for single page).
 */
function langgam_fikir_status_badge( $status, $class = 'book-status' ) {
    if ( empty( $status ) ) {
        return;
    }
    printf(
        '<span class="%s %s">%s</span>',
        esc_attr( $class ),
        esc_attr( $status ),
        esc_html( langgam_fikir_get_status_label( $status ) )
    );
}

/**
 * Check if a book status allows ordering
 */
function langgam_fikir_status_is_orderable( $status ) {
    return in_array( $status, array( 'published', 'new-release', 'limited-edition', 'digital-only', 'pre-order' ), true );
}

/**
 * Check if a book status shows a price
 */
function langgam_fikir_status_has_price( $status ) {
    return in_array( $status, array( 'published', 'new-release', 'limited-edition', 'digital-only', 'pre-order' ), true );
}

/**
 * Get the books for the homepage Showcase section
 *
 * Runs the Showcase query once per request (static cache) and returns an
 * array of items: id, title, url, bg (image URL), status. Returns an empty
 * array when the Showcase is disabled or when fewer than 2 matching books
 * have featured images, so callers can rely on "empty = section absent".
 *
 * @since 2.9.1
 * @return array[]
 */
function langgam_fikir_get_showcase_items() {
    static $items = null;

    if ( null !== $items ) {
        return $items;
    }

    $items = array();

    if ( ! langgam_fikir_get_option( 'showcase_enabled' ) ) {
        return $items;
    }

    $showcase_count = (int) langgam_fikir_get_option( 'showcase_count', 6 );
    $showcase_count = max( 2, min( 9, $showcase_count ) );
    $showcase_scope = langgam_fikir_get_option( 'showcase_scope', 'all' );

    // Every showcased book needs a featured image to act as its background.
    $meta_query = array(
        array(
            'key'     => '_thumbnail_id',
            'compare' => 'EXISTS',
        ),
    );

    if ( 'available' === $showcase_scope ) {
        $meta_query[] = array(
            'key'     => '_book_status',
            'value'   => array( 'published', 'new-release', 'limited-edition', 'digital-only', 'pre-order' ),
            'compare' => 'IN',
        );
    } elseif ( 'pipeline' === $showcase_scope ) {
        $meta_query[] = array(
            'key'     => '_book_status',
            'value'   => array( 'announced', 'coming-soon' ),
            'compare' => 'IN',
        );
    }

    $showcase_query = new WP_Query( array(
        'post_type'      => 'book',
        'posts_per_page' => $showcase_count,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => $meta_query,
    ) );

    if ( $showcase_query->have_posts() ) {
        while ( $showcase_query->have_posts() ) {
            $showcase_query->the_post();
            $bg_url = langgam_fikir_showcase_image_url( get_post_thumbnail_id() );
            if ( ! $bg_url ) {
                continue;
            }
            $items[] = array(
                'id'     => get_the_ID(),
                'title'  => get_the_title(),
                'url'    => get_permalink(),
                'bg'     => $bg_url,
                'status' => langgam_fikir_get_book_meta( get_the_ID(), 'status' ),
            );
        }
        wp_reset_postdata();
    }

    // A one-item Showcase has nothing to swap between.
    if ( count( $items ) < 2 ) {
        $items = array();
    }

    return $items;
}

/**
 * Best available background URL for a Showcase cover
 *
 * Prefers the 'book-showcase' crop (1600x900). If that size was never
 * generated (covers uploaded before v2.9.0), falls back to 'large' rather
 * than the original upload, so a legacy cover can never put a multi-megabyte
 * file behind the section. Returns '' when the attachment has no file.
 *
 * @since 2.9.6
 * @param int $attachment_id Featured image attachment ID.
 * @return string
 */
function langgam_fikir_showcase_image_url( $attachment_id ) {
    if ( ! $attachment_id ) {
        return '';
    }

    foreach ( array( 'book-showcase', 'large' ) as $size ) {
        $src = wp_get_attachment_image_src( $attachment_id, $size );
        // Index 3 is true only when a real intermediate file was returned,
        // false when WordPress silently substituted the original.
        if ( $src && ! empty( $src[3] ) ) {
            return $src[0];
        }
    }

    $src = wp_get_attachment_image_src( $attachment_id, 'full' );
    return $src ? $src[0] : '';
}

/**
 * Get the post IDs of books shown in the Showcase
 *
 * Used to exclude Showcase books from the Latest Publications grid so the
 * two homepage sections never show the same titles. Empty when the
 * Showcase does not render.
 *
 * @since 2.9.1
 * @return int[]
 */
function langgam_fikir_showcase_book_ids() {
    return wp_list_pluck( langgam_fikir_get_showcase_items(), 'id' );
}

/**
 * Handle Contact Form Submission
 */
function langgam_fikir_handle_contact_form() {
    // Verify nonce
    if ( ! isset( $_POST['contact_nonce'] ) || ! wp_verify_nonce( $_POST['contact_nonce'], 'langgam_fikir_contact' ) ) {
        wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
        exit;
    }

    // Honeypot check — if the hidden field is filled, it's a bot
    if ( ! empty( $_POST['website_url'] ) ) {
        // Silently reject — redirect as if success so bots don't know
        wp_redirect( add_query_arg( 'contact', 'success', wp_get_referer() ) );
        exit;
    }

    // Timestamp check — reject if submitted too quickly (< 3 seconds)
    if ( isset( $_POST['_lf_form_ts'] ) ) {
        $form_time  = absint( $_POST['_lf_form_ts'] );
        $now        = time();
        if ( $form_time > 0 && ( $now - $form_time ) < 3 ) {
            wp_redirect( add_query_arg( 'contact', 'success', wp_get_referer() ) );
            exit;
        }
    }

    // Rate limiting — 5 submissions per hour per IP
    $ip       = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? 'unknown' );
    $rate_key = 'lf_contact_' . md5( $ip );
    $attempts = (int) get_transient( $rate_key );
    if ( $attempts >= 5 ) {
        wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
        exit;
    }
    set_transient( $rate_key, $attempts + 1, HOUR_IN_SECONDS );

    // Sanitize form data
    $name    = sanitize_text_field( wp_unslash( $_POST['contact_name'] ?? '' ) );
    $email   = sanitize_email( wp_unslash( $_POST['contact_email'] ?? '' ) );
    $subject = sanitize_text_field( wp_unslash( $_POST['contact_subject'] ?? '' ) );
    $message = sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ?? '' ) );

    // Validate required fields
    if ( empty( $name ) || empty( $email ) || empty( $subject ) || empty( $message ) ) {
        wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
        exit;
    }

    if ( ! is_email( $email ) ) {
        wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
        exit;
    }

    // Get recipient email from theme options or use admin email
    $to = langgam_fikir_get_option( 'contact_email', get_option( 'admin_email' ) );

    // Build From header using site domain (SEC-02) — user email in Reply-To only
    $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
    $headers   = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo( 'name' ) . ' <wordpress@' . $site_host . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    $email_subject = sprintf(
        /* translators: %s: contact form subject */
        __( 'Contact Form: %s', 'langgam-fikir' ),
        $subject
    );

    $email_body = sprintf(
        '<p><strong>%1$s</strong> %2$s</p><p><strong>%3$s</strong> %4$s</p><p><strong>%5$s</strong> %6$s</p><p><strong>%7$s</strong></p><p>%8$s</p>',
        esc_html__( 'Name:', 'langgam-fikir' ),
        esc_html( $name ),
        esc_html__( 'Email:', 'langgam-fikir' ),
        esc_html( $email ),
        esc_html__( 'Subject:', 'langgam-fikir' ),
        esc_html( $subject ),
        esc_html__( 'Message:', 'langgam-fikir' ),
        nl2br( esc_html( $message ) )
    );

    // Send email
    $sent = wp_mail( $to, $email_subject, $email_body, $headers );

    // Redirect with status
    if ( $sent ) {
        wp_redirect( add_query_arg( 'contact', 'success', wp_get_referer() ) );
    } else {
        wp_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
    }
    exit;
}
add_action( 'admin_post_nopriv_langgam_fikir_contact', 'langgam_fikir_handle_contact_form' );
add_action( 'admin_post_langgam_fikir_contact', 'langgam_fikir_handle_contact_form' );

/**
 * Automatically use About template for page with slug 'about'
 */
function langgam_fikir_about_template( $template ) {
    if ( is_page() ) {
        global $post;
        $page_slug = $post->post_name;
        
        if ( $page_slug === 'about' || $page_slug === 'about-us' || $page_slug === 'about-langgam-fikir' ) {
            $about_template = locate_template( 'page-about.php' );
            if ( $about_template ) {
                return $about_template;
            }
        }
    }
    return $template;
}
add_filter( 'template_include', 'langgam_fikir_about_template' );

/**
 * Automatically use Contact template for page with slug 'contact'
 */
function langgam_fikir_contact_template( $template ) {
    if ( is_page() ) {
        global $post;
        $page_slug = $post->post_name;
        
        if ( $page_slug === 'contact' || $page_slug === 'contact-us' || $page_slug === 'contact-langgam-fikir' ) {
            $contact_template = locate_template( 'page-contact.php' );
            if ( $contact_template ) {
                return $contact_template;
            }
        }
    }
    return $template;
}
add_filter( 'template_include', 'langgam_fikir_contact_template' );

/**
 * Automatically use Privacy Policy template for page with slug 'privacy'
 */
function langgam_fikir_privacy_template( $template ) {
    if ( is_page() ) {
        global $post;
        $page_slug = $post->post_name;
        
        if ( $page_slug === 'privacy' || $page_slug === 'privacy-policy' ) {
            $privacy_template = locate_template( 'page-privacy.php' );
            if ( $privacy_template ) {
                return $privacy_template;
            }
        }
    }
    return $template;
}
add_filter( 'template_include', 'langgam_fikir_privacy_template' );

/**
 * Helper: Get FAQ entries for a post (Phase C — v2.5.0).
 *
 * Returns the array of { question, answer } pairs stored by the FAQ meta box.
 * Available on both front-end and admin since it lives in template-functions.php.
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return array Array of { question, answer } pairs (may be empty).
 */
function langgam_fikir_get_faqs( $post_id = null ) {
    if ( null === $post_id ) {
        $post_id = get_the_ID();
    }
    if ( ! $post_id ) {
        return array();
    }

    $faqs = get_post_meta( $post_id, '_langgam_fikir_faq', true );

    if ( ! is_array( $faqs ) ) {
        return array();
    }

    return array_values( array_filter( $faqs, function( $f ) {
        return is_array( $f ) && ! empty( $f['question'] ) && ! empty( $f['answer'] );
    } ) );
}

/**
 * Helper: Get reviews for a book (Phase D — v2.6.0).
 *
 * Returns sanitised review entries stored by the Reviews meta box.
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return array Array of { author, rating, title, body, date } entries.
 */
function langgam_fikir_get_reviews( $post_id = null ) {
    if ( null === $post_id ) {
        $post_id = get_the_ID();
    }
    if ( ! $post_id ) {
        return array();
    }

    $reviews = get_post_meta( $post_id, '_langgam_fikir_reviews', true );

    if ( ! is_array( $reviews ) ) {
        return array();
    }

    return array_values( array_filter( $reviews, function( $r ) {
        return is_array( $r )
            && ! empty( $r['author'] )
            && ! empty( $r['body'] )
            && ! empty( $r['rating'] )
            && (int) $r['rating'] >= 1
            && (int) $r['rating'] <= 5;
    } ) );
}

/**
 * Helper: Aggregate rating for a book (Phase D — v2.6.0).
 *
 * Returns associative array { average, count } where average is rounded
 * to one decimal place, or null when no reviews exist.
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return array|null
 */
function langgam_fikir_get_aggregate_rating( $post_id = null ) {
    $reviews = langgam_fikir_get_reviews( $post_id );

    if ( empty( $reviews ) ) {
        return null;
    }

    $sum = 0;
    foreach ( $reviews as $r ) {
        $sum += (int) $r['rating'];
    }

    return array(
        'average' => round( $sum / count( $reviews ), 1 ),
        'count'   => count( $reviews ),
    );
}

/**
 * Helper: render a star-rating widget (Phase D — v2.6.0).
 *
 * Outputs 5 stars where filled-stars match the integer portion of $rating,
 * one half-star is rendered when the fractional portion >= 0.25, and the
 * remainder are empty stars. The wrapper carries `aria-hidden="true"` so
 * the visual stars don't pollute screen readers — callers pair this with
 * a separate .screen-reader-text label that conveys the rating textually.
 *
 * @param int|float $rating Rating value 1–5 (may be fractional).
 * @return string HTML for the star widget.
 */
function langgam_fikir_render_stars( $rating ) {
    $rating  = max( 0, min( 5, (float) $rating ) );
    $filled  = floor( $rating );
    $partial = $rating - $filled;
    $output  = '<span class="lf-stars" aria-hidden="true">';

    for ( $i = 1; $i <= 5; $i++ ) {
        if ( $i <= $filled ) {
            $output .= '<span class="lf-star lf-star-filled">&#9733;</span>';
        } elseif ( $i === (int) $filled + 1 && $partial >= 0.25 ) {
            $output .= '<span class="lf-star lf-star-half">&#9733;</span>';
        } else {
            $output .= '<span class="lf-star lf-star-empty">&#9733;</span>';
        }
    }

    $output .= '</span>';
    return $output;
}

/**
 * Filter wp_robots to add noindex/nofollow when the per-post toggle is on
 * (Phase D — v2.6.0).
 *
 * Reads `_langgam_fikir_noindex` post meta set by the Search Engine
 * Visibility meta box. Active on singular post / page / book views.
 *
 * @param array $robots Existing robots directives.
 * @return array Modified directives.
 */
function langgam_fikir_filter_robots( $robots ) {
    if ( is_singular( array( 'post', 'page', 'book' ) ) ) {
        $noindex = get_post_meta( get_queried_object_id(), '_langgam_fikir_noindex', true );
        if ( $noindex === '1' ) {
            $robots['noindex']  = true;
            $robots['nofollow'] = true;
            unset( $robots['index'] );
            unset( $robots['follow'] );
        }
    }
    return $robots;
}
add_filter( 'wp_robots', 'langgam_fikir_filter_robots' );

/**
 * Catalog of language choices for the Book Language meta box (v2.8.0).
 *
 * Returns BCP 47 → human-readable display name. Display names are wrapped in
 * `__()` so they translate alongside the rest of the theme strings — when the
 * site is in Malay, choices read "Bahasa Melayu" instead of "Malay", etc.
 *
 * Keep the list short and Langgam-Fikir-relevant. Sites that need additional
 * languages can filter `langgam_fikir_book_language_choices`.
 *
 * @return array<string,string> BCP 47 code => display name.
 */
function langgam_fikir_book_language_choices() {
    $choices = array(
        'ms-MY'  => __( 'Malay (Malaysia)', 'langgam-fikir' ),
        'en-US'  => __( 'English (United States)', 'langgam-fikir' ),
        'en-GB'  => __( 'English (United Kingdom)', 'langgam-fikir' ),
        'en'     => __( 'English', 'langgam-fikir' ),
        'ar'     => __( 'Arabic', 'langgam-fikir' ),
        'id-ID'  => __( 'Indonesian', 'langgam-fikir' ),
        'zh-CN'  => __( 'Chinese (Simplified)', 'langgam-fikir' ),
        'zh-TW'  => __( 'Chinese (Traditional)', 'langgam-fikir' ),
        'ja'     => __( 'Japanese', 'langgam-fikir' ),
        'ko-KR'  => __( 'Korean', 'langgam-fikir' ),
        'fr-FR'  => __( 'French', 'langgam-fikir' ),
        'de-DE'  => __( 'German', 'langgam-fikir' ),
        'es-ES'  => __( 'Spanish', 'langgam-fikir' ),
        'pt-BR'  => __( 'Portuguese (Brazil)', 'langgam-fikir' ),
        'tr-TR'  => __( 'Turkish', 'langgam-fikir' ),
        'ur'     => __( 'Urdu', 'langgam-fikir' ),
        'fa-IR'  => __( 'Persian', 'langgam-fikir' ),
        'bn-BD'  => __( 'Bengali', 'langgam-fikir' ),
        'hi-IN'  => __( 'Hindi', 'langgam-fikir' ),
        'th'     => __( 'Thai', 'langgam-fikir' ),
        'ru-RU'  => __( 'Russian', 'langgam-fikir' ),
        'la'     => __( 'Latin', 'langgam-fikir' ),
        'he-IL'  => __( 'Hebrew', 'langgam-fikir' ),
        'el-GR'  => __( 'Greek', 'langgam-fikir' ),
    );

    /**
     * Filter the language choices shown on the Book Language meta box.
     *
     * @param array<string,string> $choices BCP 47 code => display name.
     */
    return apply_filters( 'langgam_fikir_book_language_choices', $choices );
}

/**
 * Format a BCP 47 language code as a human-readable display name (v2.8.0).
 *
 * Lookup order:
 *   1. Exact match in the language choices catalog (e.g. "ms-MY" → "Malay (Malaysia)")
 *   2. Base-language match (e.g. "ms-XX" → "ms" → "Malay")
 *   3. The raw BCP 47 code itself, returned uppercased before the hyphen
 *      (so unknown codes still render readably as "MS-XX" rather than empty)
 *
 * @param string $bcp47 BCP 47 language code.
 * @return string Human-readable display name.
 */
function langgam_fikir_format_language_name( $bcp47 ) {
    if ( empty( $bcp47 ) ) {
        return '';
    }

    $bcp47 = str_replace( '_', '-', $bcp47 );
    $choices = langgam_fikir_book_language_choices();

    if ( isset( $choices[ $bcp47 ] ) ) {
        return $choices[ $bcp47 ];
    }

    // Try base language portion only
    $base = strtok( $bcp47, '-' );
    if ( $base !== false && $base !== $bcp47 && isset( $choices[ $base ] ) ) {
        return $choices[ $base ];
    }

    // Final fallback: return the BCP 47 code itself
    return $bcp47;
}

/**
 * Helper: should the Book page show a Language row? (v2.8.0)
 *
 * Returns true when ANY of these are set for the current post:
 *   - HrefMaster per-post override (`_hrefmaster_html_lang` meta)
 *   - Theme's own `_book_language` meta
 *
 * Returns false when only the site default would be displayed — there's no
 * value in showing "Language: English" on every book on an English site.
 *
 * @param int $post_id Post ID. Defaults to current.
 * @return bool
 */
function langgam_fikir_book_has_explicit_language( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    if ( ! $post_id ) {
        return false;
    }

    // Check HrefMaster meta directly (works whether or not the plugin is active —
    // the meta key is universal; we just don't get the resolver helper without the plugin).
    $hm_override = get_post_meta( $post_id, '_hrefmaster_html_lang', true );
    if ( ! empty( $hm_override ) ) {
        return true;
    }

    // Theme's own meta
    $book_lang = get_post_meta( $post_id, '_book_language', true );
    if ( ! empty( $book_lang ) ) {
        return true;
    }

    return false;
}
