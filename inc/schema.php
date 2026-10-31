<?php
/**
 * Schema.org Structured Data & Social Meta
 *
 * Outputs JSON-LD schema, Open Graph tags, Twitter Cards,
 * and meta descriptions via wp_head hooks.
 *
 * @package Langgam_Fikir
 * @since 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Output all schema markup via wp_head
 */
function langgam_fikir_output_schema() {
    langgam_fikir_schema_website();
    langgam_fikir_schema_organization();
    langgam_fikir_schema_breadcrumbs();
    langgam_fikir_schema_page_type();
    langgam_fikir_schema_faq();        // Phase C — v2.5.0
    langgam_fikir_schema_itemlist();   // Phase C — v2.5.0
}
add_action( 'wp_head', 'langgam_fikir_output_schema', 5 );

/**
 * Output social meta tags via wp_head
 */
function langgam_fikir_output_social_meta() {
    langgam_fikir_meta_description();
    langgam_fikir_og_meta();
    langgam_fikir_twitter_meta();
}
add_action( 'wp_head', 'langgam_fikir_output_social_meta', 6 );

/**
 * Output font preload hints
 */
function langgam_fikir_preload_fonts() {
    $fonts_dir = get_stylesheet_directory_uri() . '/assets/fonts/';
    $fonts_path = get_stylesheet_directory() . '/assets/fonts/';

    $critical_fonts = array(
        'crimson-text-v19-latin-regular.woff2',
        'inter-v18-latin-regular.woff2',
    );

    foreach ( $critical_fonts as $font ) {
        if ( file_exists( $fonts_path . $font ) ) {
            printf(
                '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
                esc_url( $fonts_dir . $font )
            );
        }
    }
}
add_action( 'wp_head', 'langgam_fikir_preload_fonts', 1 );

// ─── Schema: WebSite + SearchAction ──────────────────────────

function langgam_fikir_schema_website() {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => get_bloginfo( 'name' ),
        'url'      => home_url( '/' ),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'        => 'EntryPoint',
                'urlTemplate'  => home_url( '/?s={search_term_string}' ),
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );

    $description = get_bloginfo( 'description' );
    if ( $description ) {
        $schema['description'] = $description;
    }

    // Site-wide language signal (v2.7.0 — HrefMaster aware)
    if ( function_exists( 'langgam_fikir_get_page_lang' ) ) {
        $schema['inLanguage'] = langgam_fikir_get_page_lang();
    }

    langgam_fikir_print_jsonld( $schema );
}

// ─── Schema: Organization ────────────────────────────────────

function langgam_fikir_schema_organization() {
    if ( ! is_front_page() ) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => langgam_fikir_get_option( 'footer_business_name', get_bloginfo( 'name' ) ),
        'url'      => home_url( '/' ),
    );

    // Logo
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
        if ( $logo_url ) {
            $schema['logo'] = array(
                '@type'      => 'ImageObject',
                'url'        => $logo_url,
            );
        }
    }

    // Contact
    $address = langgam_fikir_get_option( 'footer_address' );
    if ( $address ) {
        $schema['address'] = array(
            '@type'          => 'PostalAddress',
            'streetAddress'  => $address,
            'addressCountry' => 'MY',
        );
    }

    $phone = langgam_fikir_get_option( 'footer_phone' );
    if ( $phone ) {
        $schema['telephone'] = $phone;
    }

    $email = langgam_fikir_get_option( 'footer_email' );
    if ( $email ) {
        $schema['email'] = $email;
    }

    // Social links
    $same_as = array();
    $social_keys = array( 'social_facebook', 'social_instagram', 'social_twitter', 'social_whatsapp' );
    foreach ( $social_keys as $key ) {
        $url = langgam_fikir_get_option( $key );
        if ( $url ) {
            $same_as[] = $url;
        }
    }
    if ( ! empty( $same_as ) ) {
        $schema['sameAs'] = $same_as;
    }

    langgam_fikir_print_jsonld( $schema );
}

// ─── Schema: BreadcrumbList ──────────────────────────────────

function langgam_fikir_schema_breadcrumbs() {
    if ( is_front_page() ) {
        return;
    }

    $items    = array();
    $position = 1;

    // Home is always first
    $items[] = array(
        '@type'    => 'ListItem',
        'position' => $position++,
        'name'     => esc_html__( 'Home', 'langgam-fikir' ),
        'item'     => home_url( '/' ),
    );

    if ( is_singular( 'book' ) ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => esc_html__( 'Books', 'langgam-fikir' ),
            'item'     => get_post_type_archive_link( 'book' ),
        );
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
        );
    } elseif ( is_singular( 'post' ) ) {
        $blog_page_id = get_option( 'page_for_posts' );
        if ( $blog_page_id ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => get_the_title( $blog_page_id ),
                'item'     => get_permalink( $blog_page_id ),
            );
        }
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $categories[0]->name,
                'item'     => get_category_link( $categories[0]->term_id ),
            );
        }
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
        );
    } elseif ( is_page() ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
        );
    } elseif ( is_post_type_archive( 'book' ) ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => esc_html__( 'Books', 'langgam-fikir' ),
        );
    } elseif ( is_tax( 'book-category' ) || is_tax( 'book-tag' ) ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => esc_html__( 'Books', 'langgam-fikir' ),
            'item'     => get_post_type_archive_link( 'book' ),
        );
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => single_term_title( '', false ),
        );
    } elseif ( is_category() || is_tag() || is_archive() ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_archive_title(),
        );
    } elseif ( is_search() ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => sprintf( esc_html__( 'Search: %s', 'langgam-fikir' ), get_search_query() ),
        );
    } elseif ( is_404() ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => esc_html__( 'Page not found', 'langgam-fikir' ),
        );
    }

    if ( count( $items ) < 2 ) {
        return;
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    );

    langgam_fikir_print_jsonld( $schema );
}

// ─── Schema: Page-type specific ──────────────────────────────

function langgam_fikir_schema_page_type() {
    if ( is_singular( 'post' ) ) {
        langgam_fikir_schema_article();
    } elseif ( is_page() ) {
        langgam_fikir_schema_webpage();
    }
    // Book schema is already in single-book.php — no duplication
}

/**
 * Article schema for blog posts with Speakability
 */
function langgam_fikir_schema_article() {
    global $post;

    $schema = array(
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => get_the_title(),
        'url'           => get_permalink(),
        'datePublished' => get_the_date( 'c' ),
        'dateModified'  => get_the_modified_date( 'c' ),
        'author'        => array(
            '@type' => 'Person',
            'name'  => get_the_author(),
            'url'   => get_author_posts_url( get_the_author_meta( 'ID' ) ),
        ),
        'publisher'     => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id'   => get_permalink(),
        ),
        'speakable'     => array(
            '@type'       => 'SpeakableSpecification',
            'cssSelector' => array(
                '.single-post-header .page-title',
                '.entry-content',
            ),
        ),
    );

    // Per-article language signal (v2.7.0 — HrefMaster aware)
    if ( function_exists( 'langgam_fikir_get_page_lang' ) ) {
        $schema['inLanguage'] = langgam_fikir_get_page_lang( get_the_ID() );
    }

    // Publisher logo
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
        if ( $logo_url ) {
            $schema['publisher']['logo'] = array(
                '@type' => 'ImageObject',
                'url'   => $logo_url,
            );
        }
    }

    // Featured image
    if ( has_post_thumbnail() ) {
        $img_id  = get_post_thumbnail_id();
        $img_url = wp_get_attachment_image_url( $img_id, 'large' );
        $img_meta = wp_get_attachment_metadata( $img_id );
        $schema['image'] = array(
            '@type'  => 'ImageObject',
            'url'    => $img_url,
            'width'  => $img_meta['width'] ?? 0,
            'height' => $img_meta['height'] ?? 0,
        );
    }

    // Word count and reading time
    $content    = get_the_content();
    $word_count = str_word_count( wp_strip_all_tags( strip_shortcodes( $content ) ) );
    if ( $word_count > 0 ) {
        $schema['wordCount'] = $word_count;
    }

    // Description
    $excerpt = get_the_excerpt();
    if ( $excerpt ) {
        $schema['description'] = wp_trim_words( $excerpt, 30 );
    }

    // Categories
    $categories = get_the_category();
    if ( ! empty( $categories ) ) {
        $schema['articleSection'] = $categories[0]->name;
    }

    langgam_fikir_print_jsonld( $schema );
}

/**
 * WebPage schema for static pages (About, Contact, etc.)
 */
function langgam_fikir_schema_webpage() {
    global $post;

    $slug = $post->post_name;
    $type = 'WebPage';

    if ( in_array( $slug, array( 'about', 'about-us', 'about-langgam-fikir' ), true ) ) {
        $type = 'AboutPage';
    } elseif ( in_array( $slug, array( 'contact', 'contact-us', 'contact-langgam-fikir' ), true ) ) {
        $type = 'ContactPage';
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => $type,
        'name'     => get_the_title(),
        'url'      => get_permalink(),
    );

    $description = get_the_excerpt();
    if ( $description ) {
        $schema['description'] = wp_trim_words( $description, 30 );
    }

    // Per-page language signal (v2.7.0 — HrefMaster aware)
    if ( function_exists( 'langgam_fikir_get_page_lang' ) ) {
        $schema['inLanguage'] = langgam_fikir_get_page_lang( $post->ID );
    }

    langgam_fikir_print_jsonld( $schema );
}

// ─── Meta Description ────────────────────────────────────────

function langgam_fikir_meta_description() {
    $description = '';

    if ( is_singular( 'book' ) ) {
        $author   = langgam_fikir_get_book_meta( get_the_ID(), 'author' );
        $subtitle = langgam_fikir_get_book_meta( get_the_ID(), 'subtitle' );
        $excerpt  = get_the_excerpt();

        if ( $author && $subtitle ) {
            $description = $author . ' — ' . $subtitle;
        } elseif ( $excerpt ) {
            $description = $excerpt;
        }
    } elseif ( is_singular() ) {
        $description = get_the_excerpt();
        if ( ! $description ) {
            $description = wp_trim_words( get_the_content(), 25 );
        }
    } elseif ( is_post_type_archive( 'book' ) ) {
        $description = esc_html__( 'Browse all publications by Langgam Fikir — theology, history, philosophy, and comparative religion.', 'langgam-fikir' );
    } elseif ( is_home() ) {
        $description = esc_html__( 'Resources, articles, and insights from Langgam Fikir.', 'langgam-fikir' );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $description = term_description();
        if ( ! $description ) {
            $description = sprintf(
                /* translators: %s: term name */
                esc_html__( 'Browse all content in %s.', 'langgam-fikir' ),
                single_term_title( '', false )
            );
        }
    } elseif ( is_search() ) {
        $description = sprintf(
            /* translators: %s: search query */
            esc_html__( 'Search results for "%s" on Langgam Fikir.', 'langgam-fikir' ),
            get_search_query()
        );
    } elseif ( is_front_page() ) {
        $description = get_bloginfo( 'description' );
    }

    // Fallback to Theme Options default
    if ( ! $description ) {
        $description = langgam_fikir_get_option( 'default_meta_description', get_bloginfo( 'description' ) );
    }

    if ( $description ) {
        $description = wp_strip_all_tags( $description );
        $description = mb_substr( $description, 0, 160 );
        printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
    }
}

// ─── Open Graph Meta ─────────────────────────────────────────

function langgam_fikir_og_meta() {
    $title       = '';
    $description = '';
    $image       = '';
    $url         = '';
    $type        = 'website';

    if ( is_singular( 'book' ) ) {
        $title       = get_the_title();
        $subtitle    = langgam_fikir_get_book_meta( get_the_ID(), 'subtitle' );
        $description = $subtitle ? $subtitle : get_the_excerpt();
        $type        = 'book';
        $url         = get_permalink();
        if ( has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        }
    } elseif ( is_singular() ) {
        $title       = get_the_title();
        $description = get_the_excerpt();
        $type        = 'article';
        $url         = get_permalink();
        if ( has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        }
    } elseif ( is_front_page() ) {
        $title       = get_bloginfo( 'name' );
        $description = get_bloginfo( 'description' );
        $url         = home_url( '/' );
    } elseif ( is_post_type_archive( 'book' ) ) {
        $title       = esc_html__( 'Publications', 'langgam-fikir' );
        $description = esc_html__( 'Browse all publications by Langgam Fikir.', 'langgam-fikir' );
        $url         = get_post_type_archive_link( 'book' );
    } elseif ( is_home() ) {
        $title       = esc_html__( 'Resources & Insights', 'langgam-fikir' );
        $description = esc_html__( 'Articles, interviews, and updates from Langgam Fikir.', 'langgam-fikir' );
        $url         = home_url( '/' );
    } else {
        $title = wp_get_document_title();
        $url   = home_url( $_SERVER['REQUEST_URI'] ?? '/' );
    }

    // Fallback image to custom logo
    if ( ! $image ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $image = wp_get_attachment_image_url( $custom_logo_id, 'full' );
        }
    }

    // Clean description
    if ( $description ) {
        $description = wp_strip_all_tags( $description );
        $description = mb_substr( $description, 0, 200 );
    }

    if ( ! $title ) {
        return;
    }

    echo "\n<!-- Open Graph -->\n";
    printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
    printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
    printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
    printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
    // og:locale: respect HrefMaster per-page lang override when active (v2.7.0)
    if ( function_exists( 'langgam_fikir_get_page_lang' ) ) {
        $og_locale = langgam_fikir_lang_to_og_locale( langgam_fikir_get_page_lang() );
    } else {
        $og_locale = get_locale();
    }
    printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( $og_locale ) );

    if ( $description ) {
        printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
    }

    if ( $image ) {
        printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
    }

    // Book-specific OG
    if ( is_singular( 'book' ) ) {
        $isbn = langgam_fikir_get_book_meta( get_the_ID(), 'isbn' );
        $author = langgam_fikir_get_book_meta( get_the_ID(), 'author' );
        if ( $isbn ) {
            printf( '<meta property="book:isbn" content="%s">' . "\n", esc_attr( $isbn ) );
        }
        if ( $author ) {
            printf( '<meta property="book:author" content="%s">' . "\n", esc_attr( $author ) );
        }
    }

    // Article-specific OG
    if ( is_singular( 'post' ) ) {
        printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
        printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
        printf( '<meta property="article:author" content="%s">' . "\n", esc_attr( get_the_author() ) );
    }
}

// ─── Twitter Card Meta ───────────────────────────────────────

function langgam_fikir_twitter_meta() {
    $title = '';
    $description = '';
    $image = '';

    if ( is_singular() ) {
        $title       = get_the_title();
        $description = get_the_excerpt();
        if ( has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        }
    } elseif ( is_front_page() ) {
        $title       = get_bloginfo( 'name' );
        $description = get_bloginfo( 'description' );
    } else {
        $title = wp_get_document_title();
    }

    if ( ! $image ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $image = wp_get_attachment_image_url( $custom_logo_id, 'full' );
        }
    }

    if ( $description ) {
        $description = wp_strip_all_tags( mb_substr( $description, 0, 200 ) );
    }

    if ( ! $title ) {
        return;
    }

    echo "\n<!-- Twitter Card -->\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );

    if ( $description ) {
        printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
    }

    if ( $image ) {
        printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
    }
}

// ─── Speakability for single-book.php ────────────────────────

/**
 * Add Speakability to the existing Book schema in single-book.php
 *
 * This function is called from single-book.php to merge speakable
 * property into the book schema array before output.
 */
function langgam_fikir_book_speakable() {
    return array(
        '@type'       => 'SpeakableSpecification',
        'cssSelector' => array(
            '.book-details h1',
            '.book-synopsis-text',
        ),
    );
}

// ─── Helper: Print JSON-LD ───────────────────────────────────

function langgam_fikir_print_jsonld( $schema ) {
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
    echo "\n</script>\n";
}

// ─── FAQPage Schema (Phase C — v2.5.0) ─────────────────────────────────

/**
 * Output FAQPage JSON-LD when the current post has FAQ entries set.
 *
 * Reads from `_langgam_fikir_faq` post meta (managed by inc/admin/meta-boxes-faq.php).
 * Renders only on singular `book` and `page` views with at least one Q/A pair.
 *
 * @since 2.5.0
 */
function langgam_fikir_schema_faq() {
    if ( ! is_singular( array( 'book', 'page' ) ) ) {
        return;
    }

    if ( ! function_exists( 'langgam_fikir_get_faqs' ) ) {
        return;
    }

    $faqs = langgam_fikir_get_faqs( get_the_ID() );

    if ( empty( $faqs ) ) {
        return;
    }

    $main_entity = array();
    foreach ( $faqs as $faq ) {
        $main_entity[] = array(
            '@type'          => 'Question',
            'name'           => $faq['question'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => $faq['answer'],
            ),
        );
    }

    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $main_entity,
    );

    langgam_fikir_print_jsonld( $schema );
}

// ─── ItemList Schema (Phase C — v2.5.0) ────────────────────────────────

/**
 * Output ItemList JSON-LD on the book post-type archive (/publications/).
 *
 * Each book in the current query becomes a ListItem with position, name,
 * url, and image when available. Tells search engines this archive is
 * a curated catalog rather than a tag-style flat listing.
 *
 * @since 2.5.0
 */
function langgam_fikir_schema_itemlist() {
    if ( ! is_post_type_archive( 'book' ) && ! is_tax( array( 'book-category', 'book-tag' ) ) ) {
        return;
    }

    global $wp_query;

    if ( empty( $wp_query->posts ) ) {
        return;
    }

    $items    = array();
    $position = 0;

    foreach ( $wp_query->posts as $book ) {
        $position++;
        $item = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'url'      => get_permalink( $book ),
            'name'     => get_the_title( $book ),
        );

        $thumb = get_the_post_thumbnail_url( $book, 'medium' );
        if ( $thumb ) {
            $item['image'] = $thumb;
        }

        $items[] = $item;
    }

    if ( empty( $items ) ) {
        return;
    }

    if ( is_post_type_archive( 'book' ) ) {
        $list_name = __( 'Publications — Langgam Fikir', 'langgam-fikir' );
    } else {
        $list_name = single_term_title( '', false );
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => $list_name,
        'numberOfItems'   => count( $items ),
        'itemListElement' => $items,
    );

    langgam_fikir_print_jsonld( $schema );
}
