<?php
/**
 * Schema.org structured data, meta description, canonical and social meta
 *
 * One connected JSON-LD @graph per page. Every node has a stable @id, and the site-wide
 * ones use the same ids as Rank Math and Yoast ({home}/#organization, {home}/#website),
 * so the theme's nodes and a plugin's nodes describe one publisher and one website.
 *
 * Who prints what (Theme Options > Social & SEO > Search Engines):
 *  - No SEO plugin, or mode "theme": the theme prints the meta description, canonical
 *    (where core does not), Open Graph, Twitter Card and the full graph: WebSite,
 *    Organization, WebPage, BreadcrumbList, BlogPosting, Book, ItemList and FAQPage.
 *  - An SEO plugin is active (mode "auto"): the plugin owns titles, meta, social tags and
 *    the WebSite/Organization/WebPage nodes. The theme adds only what the plugin cannot
 *    know (Book, the publications ItemList, FAQPage and the visible breadcrumb trail when
 *    the plugin prints none), merged into the plugin's own graph for Rank Math and Yoast,
 *    or as a linked graph for other plugins. It also fills gaps in the plugin's
 *    Organization (sameAs, registration number, slogan) without overwriting its values.
 *
 * @package Langgam_Fikir
 * @since 2.1.0 (rewritten 2.14.0)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =========================================================================
 * Ownership: theme or SEO plugin
 * ====================================================================== */

/**
 * The active SEO plugin, or '' when none is detected.
 *
 * @return string rank-math | yoast | aioseo | seopress | seo-framework | ''
 */
function langgam_fikir_seo_plugin() {
    $plugin = '';
    if ( defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' ) ) {
        $plugin = 'rank-math';
    } elseif ( defined( 'WPSEO_VERSION' ) ) {
        $plugin = 'yoast';
    } elseif ( defined( 'AIOSEO_VERSION' ) || function_exists( 'aioseo' ) ) {
        $plugin = 'aioseo';
    } elseif ( defined( 'SEOPRESS_VERSION' ) ) {
        $plugin = 'seopress';
    } elseif ( defined( 'THE_SEO_FRAMEWORK_VERSION' ) ) {
        $plugin = 'seo-framework';
    }
    /**
     * Filters the detected SEO plugin. Return '' to make the theme treat the site as plugin-free.
     *
     * @param string $plugin Detected plugin slug.
     */
    return (string) apply_filters( 'langgam_fikir_seo_plugin', $plugin );
}

/**
 * Whether the theme prints the meta description, social tags and the site-wide schema nodes.
 */
function langgam_fikir_theme_owns_seo() {
    $mode = (string) langgam_fikir_get_option( 'seo_mode', 'auto' );
    if ( 'theme' === $mode ) {
        return true;
    }
    if ( 'plugin' === $mode ) {
        return false;
    }
    return '' === langgam_fikir_seo_plugin();
}

/**
 * Whether the active SEO plugin prints its own BreadcrumbList.
 */
function langgam_fikir_plugin_prints_breadcrumbs() {
    switch ( langgam_fikir_seo_plugin() ) {
        case 'rank-math':
            if ( class_exists( '\RankMath\Helper' ) && method_exists( '\RankMath\Helper', 'is_breadcrumbs_enabled' ) ) {
                return (bool) \RankMath\Helper::is_breadcrumbs_enabled();
            }
            return false;
        case 'yoast':
        case 'aioseo':
        case 'seopress':
        case 'seo-framework':
            return true;
    }
    return false;
}

/* =========================================================================
 * Shared helpers
 * ====================================================================== */

/**
 * A site-wide node id: {home}/#fragment (the convention Rank Math and Yoast share).
 */
function langgam_fikir_schema_id( $fragment ) {
    return home_url( '/' ) . '#' . $fragment;
}

/**
 * The canonical URL of the current view, or '' where none should be declared (search, 404).
 */
function langgam_fikir_current_url() {
    static $url = null;
    if ( null !== $url ) {
        return $url;
    }

    $paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

    if ( is_search() || is_404() ) {
        $url = '';
    } elseif ( is_singular() ) {
        $url = (string) wp_get_canonical_url();
    } elseif ( is_front_page() ) {
        $url = $paged > 1 ? get_pagenum_link( $paged ) : home_url( '/' );
    } elseif ( is_home() ) {
        $blog = (int) get_option( 'page_for_posts' );
        $url  = $paged > 1 ? get_pagenum_link( $paged ) : ( $blog ? get_permalink( $blog ) : home_url( '/' ) );
    } elseif ( is_post_type_archive() || is_category() || is_tag() || is_tax() || is_author() || is_date() ) {
        $url = get_pagenum_link( $paged );
    } else {
        $url = '';
    }

    // get_pagenum_link() keeps query strings such as ?sort=; a canonical should not. With plain
    // permalinks the page number and the archive itself are query arguments too, so those stay.
    if ( $url && ! is_singular() && false !== strpos( $url, '?' ) ) {
        $query = array();
        wp_parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );
        $keep  = array_intersect_key( $query, array_flip( array( 'paged', 'page', 'post_type', 'cat', 'tag', 'book-category', 'book-tag', 'author', 'm', 'year', 'monthnum', 'day' ) ) );
        $url   = add_query_arg( $keep, strtok( $url, '?' ) );
    }

    return $url = (string) $url;
}

/**
 * Plain text cut to a length on a word boundary, with an ellipsis when cut.
 */
function langgam_fikir_trim_text( $text, $length = 155 ) {
    $text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( (string) $text ) ) ) );
    if ( mb_strlen( $text ) <= $length ) {
        return $text;
    }
    $cut   = mb_substr( $text, 0, $length - 1 );
    $space = mb_strrpos( $cut, ' ' );
    if ( false !== $space && $space > $length * 0.6 ) {
        $cut = mb_substr( $cut, 0, $space );
    }
    return rtrim( $cut, " ,;:.-–—" ) . '…';
}

/**
 * Page language for schema and og:locale (HrefMaster aware).
 */
function langgam_fikir_schema_lang( $post_id = 0 ) {
    if ( function_exists( 'langgam_fikir_get_page_lang' ) ) {
        return langgam_fikir_get_page_lang( $post_id );
    }
    $locale = (string) get_locale();
    return str_replace( '_', '-', '' !== $locale ? $locale : 'en_US' );
}

/**
 * An ImageObject for an attachment, or null.
 */
function langgam_fikir_schema_image( $attachment_id, $size = 'full', $id = '' ) {
    $attachment_id = (int) $attachment_id;
    if ( ! $attachment_id ) {
        return null;
    }
    $src = wp_get_attachment_image_src( $attachment_id, $size );
    if ( ! $src ) {
        return null;
    }
    $image = array(
        '@type'      => 'ImageObject',
        'url'        => $src[0],
        'contentUrl' => $src[0],
        'width'      => (int) $src[1],
        'height'     => (int) $src[2],
    );
    if ( $id ) {
        $image = array( '@id' => $id ) + $image;
    }
    $caption = trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
    if ( $caption ) {
        $image['caption'] = $caption;
    }
    return $image;
}

/**
 * Name of the publishing organisation.
 */
function langgam_fikir_org_name() {
    return (string) langgam_fikir_get_option( 'footer_business_name', get_bloginfo( 'name' ) );
}

/* =========================================================================
 * Meta description
 * ====================================================================== */

/**
 * The meta description for the current view (plain text, at most 155 characters).
 * Every view gets its own, as the Google starter guide recommends; the default is the last resort.
 */
function langgam_fikir_get_meta_description() {
    static $description = null;
    if ( null !== $description ) {
        return $description;
    }

    $text = '';

    // The front page comes first: with "Your latest posts" as the homepage, is_home() is true too.
    if ( is_front_page() ) {
        $text = langgam_fikir_get_option( 'default_meta_description' );
        if ( ! $text ) {
            $text = langgam_fikir_get_option( 'hero_text' );
        }
    } elseif ( is_singular( 'book' ) ) {
        $post_id = get_queried_object_id();
        $text    = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '';
        if ( ! $text ) {
            $author   = langgam_fikir_get_book_meta( $post_id, 'author' );
            $subtitle = langgam_fikir_get_book_meta( $post_id, 'subtitle' );
            $lead     = get_the_title( $post_id );
            if ( $subtitle ) {
                $lead .= ': ' . $subtitle;
            }
            if ( $author ) {
                /* translators: 1: book title, 2: author */
                $lead = sprintf( __( '%1$s, by %2$s.', 'langgam-fikir' ), $lead, $author );
            }
            $body = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
            $text = trim( $lead . ' ' . $body );
        }
    } elseif ( is_singular() ) {
        $post_id = get_queried_object_id();
        $text    = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
    } elseif ( is_post_type_archive( 'book' ) ) {
        $text = __( 'Browse all publications by Langgam Fikir: theology, history, philosophy, literature and comparative religion.', 'langgam-fikir' );
    } elseif ( is_home() ) {
        $text = __( 'Articles, interviews and updates from Langgam Fikir.', 'langgam-fikir' );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $text = term_description();
        if ( ! trim( wp_strip_all_tags( $text ) ) ) {
            /* translators: %s: term name */
            $text = sprintf( __( 'Books and articles in %s from Langgam Fikir.', 'langgam-fikir' ), single_term_title( '', false ) );
        }
    } elseif ( is_author() ) {
        $text = get_the_author_meta( 'description', (int) get_queried_object_id() );
    }

    if ( ! trim( wp_strip_all_tags( (string) $text ) ) ) {
        $text = langgam_fikir_get_option( 'default_meta_description', get_bloginfo( 'description' ) );
    }

    // Page 2+ of a listing gets a page number so no two URLs share a description.
    $paged = (int) get_query_var( 'paged' );
    $text  = langgam_fikir_trim_text( $text, $paged > 1 ? 140 : 155 );
    if ( $text && $paged > 1 ) {
        /* translators: %d: page number */
        $text .= ' ' . sprintf( __( 'Page %d.', 'langgam-fikir' ), $paged );
    }

    return $description = $text;
}

/* =========================================================================
 * Share image (Open Graph, Twitter, primaryImageOfPage)
 * ====================================================================== */

/**
 * The image for the current view: featured image, then the homepage hero image, then the logo.
 *
 * @return array{url:string,width:int,height:int,alt:string,type:string,is_logo:bool}|null
 */
function langgam_fikir_share_image() {
    static $image = false;
    if ( false !== $image ) {
        return $image;
    }

    $candidates = array();
    if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
        $candidates[] = array( (int) get_post_thumbnail_id( get_queried_object_id() ), false );
    }
    $candidates[] = array( (int) langgam_fikir_get_option( 'hero_background_image' ), false );

    // No featured image or upload: the bundled hero photo the homepage shows (not 'random',
    // which would give every share a different picture).
    $hero = (string) langgam_fikir_get_option( 'hero_photo' );
    if ( ! ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) && ! (int) langgam_fikir_get_option( 'hero_background_image' )
        && isset( langgam_fikir_hero_photos()[ $hero ] ) ) {
        $file = LANGGAM_FIKIR_DIR . '/assets/images/hero/' . $hero . '-1920.jpg';
        $size = file_exists( $file ) ? @getimagesize( $file ) : false; // phpcs:ignore WordPress.PHP.NoSilencedErrors
        if ( $size ) {
            return $image = array(
                'url'     => LANGGAM_FIKIR_URI . '/assets/images/hero/' . $hero . '-1920.jpg',
                'width'   => (int) $size[0],
                'height'  => (int) $size[1],
                'alt'     => langgam_fikir_hero_photos()[ $hero ],
                'type'    => 'image/jpeg',
                'is_logo' => false,
            );
        }
    }

    $candidates[] = array( (int) get_theme_mod( 'custom_logo' ), true );

    $image = null;
    foreach ( $candidates as $candidate ) {
        list( $id, $is_logo ) = $candidate;
        if ( ! $id ) {
            continue;
        }
        $src = wp_get_attachment_image_src( $id, 'large' );
        if ( ! $src ) {
            continue;
        }
        $alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
        $image = array(
            'url'     => $src[0],
            'width'   => (int) $src[1],
            'height'  => (int) $src[2],
            'alt'     => $alt ? $alt : ( is_singular() ? get_the_title( get_queried_object_id() ) : get_bloginfo( 'name' ) ),
            'type'    => (string) get_post_mime_type( $id ),
            'is_logo' => $is_logo,
        );
        break;
    }
    return $image;
}

/* =========================================================================
 * Head output: description, canonical, Open Graph, Twitter
 * ====================================================================== */

function langgam_fikir_output_social_meta() {
    if ( ! langgam_fikir_theme_owns_seo() ) {
        return;
    }

    $description = langgam_fikir_get_meta_description();
    if ( $description ) {
        printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
    }

    // Core prints rel=canonical on singular views only; the theme covers the front page and archives.
    $canonical = langgam_fikir_current_url();
    if ( $canonical && ! is_singular() ) {
        printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
    }

    langgam_fikir_og_meta( $description, $canonical );
    langgam_fikir_twitter_meta( $description );
}
add_action( 'wp_head', 'langgam_fikir_output_social_meta', 2 );

/**
 * The page's own title for social cards (no site-name suffix).
 */
function langgam_fikir_social_title() {
    if ( is_front_page() ) {
        $tagline = get_bloginfo( 'description' );
        return $tagline ? get_bloginfo( 'name' ) . ' — ' . $tagline : get_bloginfo( 'name' );
    }
    if ( is_singular() ) {
        return get_the_title( get_queried_object_id() );
    }
    if ( is_post_type_archive( 'book' ) ) {
        return post_type_archive_title( '', false );
    }
    if ( is_home() ) {
        $blog = (int) get_option( 'page_for_posts' );
        return $blog ? get_the_title( $blog ) : __( 'Resources & Insights', 'langgam-fikir' );
    }
    if ( is_category() || is_tag() || is_tax() ) {
        return single_term_title( '', false );
    }
    return wp_get_document_title();
}

function langgam_fikir_og_meta( $description, $url ) {
    $type  = is_singular( 'book' ) ? 'book' : ( is_singular( 'post' ) ? 'article' : 'website' );
    $image = langgam_fikir_share_image();
    $tags  = array(
        'og:locale'      => str_replace( '-', '_', langgam_fikir_schema_lang() ),
        'og:type'        => $type,
        'og:site_name'   => get_bloginfo( 'name' ),
        'og:title'       => langgam_fikir_social_title(),
        'og:description' => $description,
        'og:url'         => $url,
    );
    if ( $image ) {
        $tags['og:image']        = $image['url'];
        $tags['og:image:width']  = $image['width'];
        $tags['og:image:height'] = $image['height'];
        $tags['og:image:alt']    = $image['alt'];
        $tags['og:image:type']   = $image['type'];
    }

    echo "<!-- Open Graph -->\n";
    foreach ( $tags as $property => $content ) {
        if ( '' !== (string) $content ) {
            printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), 'og:url' === $property || 'og:image' === $property ? esc_url( $content ) : esc_attr( $content ) );
        }
    }

    if ( is_singular( 'book' ) ) {
        $post_id = get_queried_object_id();
        $isbn    = preg_replace( '/[^0-9Xx]/', '', (string) langgam_fikir_get_book_meta( $post_id, 'isbn' ) );
        $year    = (string) langgam_fikir_get_book_meta( $post_id, 'year' );
        if ( $isbn ) {
            printf( '<meta property="book:isbn" content="%s">' . "\n", esc_attr( $isbn ) );
        }
        if ( preg_match( '/^\d{4}$/', $year ) ) {
            printf( '<meta property="book:release_date" content="%s">' . "\n", esc_attr( $year ) );
        }
        $tags = wp_get_post_terms( $post_id, 'book-tag', array( 'fields' => 'names' ) );
        foreach ( is_wp_error( $tags ) ? array() : array_slice( $tags, 0, 6 ) as $tag ) {
            printf( '<meta property="book:tag" content="%s">' . "\n", esc_attr( $tag ) );
        }
    } elseif ( is_singular( 'post' ) ) {
        $post_id = get_queried_object_id();
        printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c', $post_id ) ) );
        printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c', $post_id ) ) );
        $categories = get_the_category( $post_id );
        if ( $categories ) {
            printf( '<meta property="article:section" content="%s">' . "\n", esc_attr( $categories[0]->name ) );
        }
        foreach ( array_slice( wp_get_post_tags( $post_id, array( 'fields' => 'names' ) ), 0, 6 ) as $tag ) {
            printf( '<meta property="article:tag" content="%s">' . "\n", esc_attr( $tag ) );
        }
    }
}

function langgam_fikir_twitter_meta( $description ) {
    $image = langgam_fikir_share_image();
    // A large card needs a wide picture; a square logo reads better as a summary card.
    $card  = ( $image && ! $image['is_logo'] && $image['width'] >= 600 ) ? 'summary_large_image' : 'summary';

    echo "<!-- Twitter Card -->\n";
    printf( '<meta name="twitter:card" content="%s">' . "\n", esc_attr( $card ) );
    printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( langgam_fikir_social_title() ) );
    if ( $description ) {
        printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
    }
    if ( $image ) {
        printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image['url'] ) );
        printf( '<meta name="twitter:image:alt" content="%s">' . "\n", esc_attr( $image['alt'] ) );
    }
    $handle = langgam_fikir_x_handle();
    if ( $handle ) {
        printf( '<meta name="twitter:site" content="@%s">' . "\n", esc_attr( $handle ) );
    }
}

/**
 * The X/Twitter handle from the profile URL in Theme Options, or ''.
 */
function langgam_fikir_x_handle() {
    $url = (string) langgam_fikir_get_option( 'social_twitter' );
    if ( preg_match( '#(?:twitter|x)\.com/@?([A-Za-z0-9_]{1,15})(?:[/?]|$)#', $url, $m ) ) {
        return $m[1];
    }
    return '';
}

/* =========================================================================
 * Graph nodes
 * ====================================================================== */

function langgam_fikir_schema_organization_node() {
    $org = array(
        '@type' => 'Organization',
        '@id'   => langgam_fikir_schema_id( 'organization' ),
        'name'  => langgam_fikir_org_name(),
        'url'   => home_url( '/' ),
    );

    $site_name = get_bloginfo( 'name' );
    if ( $site_name && $site_name !== $org['name'] ) {
        $org['alternateName'] = $site_name;
    }
    $legal = (string) langgam_fikir_get_option( 'legal_entity_name' );
    if ( $legal ) {
        $org['legalName'] = $legal;
    }
    $tagline = get_bloginfo( 'description' );
    if ( $tagline ) {
        $org['slogan'] = $tagline;
    }
    $about = (string) langgam_fikir_get_option( 'hero_text' );
    if ( $about ) {
        $org['description'] = langgam_fikir_trim_text( $about, 300 );
    }

    $logo = langgam_fikir_schema_image( get_theme_mod( 'custom_logo' ), 'full', langgam_fikir_schema_id( 'logo' ) );
    if ( $logo ) {
        $org['logo']  = $logo;
        $org['image'] = array( '@id' => $logo['@id'] );
    }

    $address = (string) langgam_fikir_get_option( 'footer_address' );
    if ( $address ) {
        $org['address'] = array(
            '@type'          => 'PostalAddress',
            'streetAddress'  => $address,
            'addressCountry' => 'MY',
        );
    }

    $email = (string) langgam_fikir_get_option( 'footer_email' );
    $phone = (string) langgam_fikir_get_option( 'footer_phone' );
    if ( $email ) {
        $org['email'] = $email;
    }
    if ( $phone ) {
        $org['telephone'] = $phone;
    }
    if ( $email || $phone ) {
        $contact = array(
            '@type'             => 'ContactPoint',
            'contactType'       => 'customer service',
            'areaServed'        => 'MY',
            'availableLanguage' => array( 'en', 'ms' ),
        );
        if ( $email ) {
            $contact['email'] = $email;
        }
        if ( $phone ) {
            $contact['telephone'] = $phone;
        }
        $org['contactPoint'] = $contact;
    }

    $registration = langgam_fikir_registration_identifier();
    if ( $registration ) {
        $org['identifier'] = $registration;
    }

    $same_as = langgam_fikir_social_profile_urls();
    if ( $same_as ) {
        $org['sameAs'] = array_values( array_unique( $same_as ) );
    }

    return $org;
}

/**
 * The SSM business registration number as a PropertyValue, or null.
 */
function langgam_fikir_registration_identifier() {
    $number = trim( (string) langgam_fikir_get_option( 'registration_number' ) );
    if ( '' === $number ) {
        return null;
    }
    return array(
        '@type'      => 'PropertyValue',
        'propertyID' => 'SSM',
        'name'       => __( 'Business registration number (SSM)', 'langgam-fikir' ),
        'value'      => $number,
    );
}

function langgam_fikir_schema_website_node() {
    $site = array(
        '@type'      => 'WebSite',
        '@id'        => langgam_fikir_schema_id( 'website' ),
        'url'        => home_url( '/' ),
        'name'       => get_bloginfo( 'name' ),
        'publisher'  => array( '@id' => langgam_fikir_schema_id( 'organization' ) ),
        'inLanguage' => langgam_fikir_schema_lang(),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url( '/?s={search_term_string}' ),
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );
    $tagline = get_bloginfo( 'description' );
    if ( $tagline ) {
        $site['description'] = $tagline;
    }
    return $site;
}

/**
 * BreadcrumbList for the current view, or null on the front page (a single item is not a trail).
 */
function langgam_fikir_schema_breadcrumb_node() {
    if ( is_front_page() ) {
        return null;
    }

    $trail   = array();
    $trail[] = array( __( 'Home', 'langgam-fikir' ), home_url( '/' ) );
    $books_label = __( 'Publications', 'langgam-fikir' ); // Same word as the visible trail (inc/breadcrumbs.php).
    $here    = langgam_fikir_current_url();

    if ( is_singular( 'book' ) ) {
        $trail[] = array( $books_label, get_post_type_archive_link( 'book' ) );
        $terms   = get_the_terms( get_queried_object_id(), 'book-category' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $trail[] = array( $terms[0]->name, get_term_link( $terms[0] ) );
        }
        $trail[] = array( get_the_title( get_queried_object_id() ), $here );
    } elseif ( is_singular( 'post' ) ) {
        // Home > category > post, as in the visible trail (inc/breadcrumbs.php).
        $categories = get_the_category( get_queried_object_id() );
        if ( $categories ) {
            $trail[] = array( $categories[0]->name, get_category_link( $categories[0]->term_id ) );
        }
        $trail[] = array( get_the_title( get_queried_object_id() ), $here );
    } elseif ( is_page() ) {
        foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
            $trail[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
        }
        $trail[] = array( get_the_title( get_queried_object_id() ), $here );
    } elseif ( is_post_type_archive( 'book' ) ) {
        $trail[] = array( $books_label, get_post_type_archive_link( 'book' ) );
    } elseif ( is_tax( array( 'book-category', 'book-tag' ) ) ) {
        $trail[] = array( $books_label, get_post_type_archive_link( 'book' ) );
        $trail[] = array( single_term_title( '', false ), $here );
    } elseif ( is_home() ) {
        $trail[] = array( langgam_fikir_social_title(), $here );
    } elseif ( is_category() || is_tag() || is_archive() ) {
        $trail[] = array( wp_strip_all_tags( get_the_archive_title() ), $here );
    } elseif ( is_search() ) {
        /* translators: %s: search query */
        $trail[] = array( sprintf( __( 'Search: %s', 'langgam-fikir' ), get_search_query() ), '' );
    } elseif ( is_404() ) {
        $trail[] = array( __( 'Page not found', 'langgam-fikir' ), '' );
    }

    if ( count( $trail ) < 2 ) {
        return null;
    }

    $items = array();
    foreach ( $trail as $i => $crumb ) {
        $item = array(
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => wp_strip_all_tags( (string) $crumb[0] ),
        );
        if ( $crumb[1] && ! is_wp_error( $crumb[1] ) ) {
            $item['item'] = (string) $crumb[1];
        }
        $items[] = $item;
    }

    $base = $here ? $here : home_url( '/' ) . ( is_search() ? '?s=' . rawurlencode( get_search_query() ) : '' );
    return array(
        '@type'           => 'BreadcrumbList',
        '@id'             => $base . '#breadcrumb',
        'itemListElement' => $items,
    );
}

/**
 * The WebPage node, typed for the view.
 */
function langgam_fikir_schema_webpage_node( $main_entity_id = '', $breadcrumb_id = '' ) {
    $url = langgam_fikir_current_url();
    if ( ! $url ) {
        return null;
    }

    $type = 'WebPage';
    if ( is_front_page() || is_home() || is_post_type_archive() || is_category() || is_tag() || is_tax() || is_date() ) {
        $type = 'CollectionPage';
    } elseif ( is_author() ) {
        $type = 'ProfilePage';
    } elseif ( is_singular( 'book' ) ) {
        $type = 'ItemPage';
    } elseif ( is_page() ) {
        $template = (string) get_page_template_slug( get_queried_object_id() );
        $slug     = (string) get_post_field( 'post_name', get_queried_object_id() );
        if ( 'page-about.php' === $template || in_array( $slug, array( 'about', 'about-us', 'about-langgam-fikir' ), true ) ) {
            $type = 'AboutPage';
        } elseif ( 'page-contact.php' === $template || in_array( $slug, array( 'contact', 'contact-us', 'contact-langgam-fikir' ), true ) ) {
            $type = 'ContactPage';
        }
    }

    $post_id = is_singular() ? get_queried_object_id() : 0;
    $page    = array(
        '@type'      => $type,
        '@id'        => $url . '#webpage',
        'url'        => $url,
        'name'       => wp_get_document_title(),
        'isPartOf'   => array( '@id' => langgam_fikir_schema_id( 'website' ) ),
        'inLanguage' => langgam_fikir_schema_lang( $post_id ),
    );

    $description = langgam_fikir_get_meta_description();
    if ( $description ) {
        $page['description'] = $description;
    }
    if ( is_front_page() || 'AboutPage' === $type || 'ContactPage' === $type ) {
        $page['about'] = array( '@id' => langgam_fikir_schema_id( 'organization' ) );
    }
    if ( $post_id ) {
        $page['datePublished'] = get_the_date( 'c', $post_id );
        $page['dateModified']  = get_the_modified_date( 'c', $post_id );
        if ( has_post_thumbnail( $post_id ) ) {
            $image = langgam_fikir_schema_image( get_post_thumbnail_id( $post_id ), 'full', $url . '#primaryimage' );
            if ( $image ) {
                $page['primaryImageOfPage'] = $image;
            }
        }
    }
    if ( $breadcrumb_id ) {
        $page['breadcrumb'] = array( '@id' => $breadcrumb_id );
    }
    if ( $main_entity_id ) {
        $page['mainEntity'] = array( '@id' => $main_entity_id );
    }
    return $page;
}

/**
 * A Person node for a WordPress user (the article author).
 */
function langgam_fikir_schema_person_node( $user_id ) {
    $url    = get_author_posts_url( $user_id );
    $person = array(
        '@type' => 'Person',
        '@id'   => $url . '#person',
        'name'  => get_the_author_meta( 'display_name', $user_id ),
        'url'   => $url,
    );
    $bio = get_the_author_meta( 'description', $user_id );
    if ( $bio ) {
        $person['description'] = langgam_fikir_trim_text( $bio, 300 );
    }
    $website = get_the_author_meta( 'user_url', $user_id );
    if ( $website ) {
        $person['sameAs'] = array( $website );
    }
    return $person;
}

/**
 * BlogPosting for a single article.
 */
function langgam_fikir_schema_article_node( $post_id ) {
    $url     = get_permalink( $post_id );
    $user_id = (int) get_post_field( 'post_author', $post_id );
    $content = (string) get_post_field( 'post_content', $post_id );

    $article = array(
        '@type'            => 'BlogPosting',
        '@id'              => $url . '#article',
        'headline'         => langgam_fikir_trim_text( get_the_title( $post_id ), 110 ),
        'url'              => $url,
        'datePublished'    => get_the_date( 'c', $post_id ),
        'dateModified'     => get_the_modified_date( 'c', $post_id ),
        'author'           => array( '@id' => get_author_posts_url( $user_id ) . '#person' ),
        'publisher'        => array( '@id' => langgam_fikir_schema_id( 'organization' ) ),
        'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
        'isPartOf'         => array( '@id' => $url . '#webpage' ),
        'inLanguage'       => langgam_fikir_schema_lang( $post_id ),
        'speakable'        => array(
            '@type'       => 'SpeakableSpecification',
            'cssSelector' => array( '.single-post-header .page-title', '.entry-content' ),
        ),
    );

    if ( has_post_thumbnail( $post_id ) ) {
        $article['image'] = array( '@id' => $url . '#primaryimage' );
        $article['thumbnailUrl'] = get_the_post_thumbnail_url( $post_id, 'large' );
    }
    $description = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : $content;
    if ( $description ) {
        $article['description'] = langgam_fikir_trim_text( $description, 200 );
    }
    // str_word_count() only knows ASCII letters; count Unicode words instead.
    $words = (int) preg_match_all( "/[\\p{L}\\p{N}][\\p{L}\\p{N}\\p{M}'’-]*/u", wp_strip_all_tags( strip_shortcodes( $content ) ) );
    if ( $words ) {
        $article['wordCount'] = $words;
    }
    $categories = get_the_category( $post_id );
    if ( $categories ) {
        $article['articleSection'] = wp_list_pluck( $categories, 'name' );
    }
    $tags = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );
    if ( $tags ) {
        $article['keywords'] = $tags;
    }
    if ( comments_open( $post_id ) || get_comments_number( $post_id ) ) {
        $article['commentCount'] = (int) get_comments_number( $post_id );
    }
    return $article;
}

/**
 * schema.org availability for a book status.
 */
function langgam_fikir_book_availability( $status ) {
    $map = array(
        'published'       => 'InStock',
        'new-release'     => 'InStock',
        'limited-edition' => 'LimitedAvailability',
        'digital-only'    => 'OnlineOnly',
        'pre-order'       => 'PreOrder',
        'coming-soon'     => 'PreOrder',
        'announced'       => 'PreOrder',
        'sold-out'        => 'OutOfStock',
        'reprinting'      => 'OutOfStock',
        'out-of-print'    => 'Discontinued',
    );
    return isset( $map[ $status ] ) ? 'https://schema.org/' . $map[ $status ] : '';
}

/**
 * Book node: everything the book page knows, linked to the publisher and the page.
 *
 * @param int  $post_id Book post ID.
 * @param bool $full    False for the short form used inside lists.
 */
function langgam_fikir_schema_book_node( $post_id, $full = true ) {
    $url  = get_permalink( $post_id );
    $meta = static function ( $key ) use ( $post_id ) {
        return trim( (string) langgam_fikir_get_book_meta( $post_id, $key ) );
    };

    $book = array(
        '@type' => 'Book',
        '@id'   => $url . '#book',
        'name'  => get_the_title( $post_id ),
        'url'   => $url,
    );

    foreach ( array( 'author', 'editor', 'translator' ) as $role ) {
        $names = $meta( $role );
        if ( '' === $names ) {
            continue;
        }
        // "A, B & C" become separate people.
        $people = array();
        foreach ( preg_split( '/\s*(?:,|&|\band\b|\bdan\b)\s*/u', $names ) as $name ) {
            if ( '' !== trim( $name ) ) {
                $people[] = array( '@type' => 'Person', 'name' => trim( $name ) );
            }
        }
        if ( $people ) {
            $book[ $role ] = 1 === count( $people ) ? $people[0] : $people;
        }
    }

    $isbn = preg_replace( '/[^0-9Xx]/', '', $meta( 'isbn' ) );
    if ( $isbn ) {
        $book['isbn'] = strtoupper( $isbn );
    }

    $thumb_id = (int) get_post_thumbnail_id( $post_id );
    if ( $thumb_id ) {
        $image = langgam_fikir_schema_image( $thumb_id, $full ? 'full' : 'medium' );
        if ( $image ) {
            $book['image'] = $full ? $image : $image['url'];
        }
    }

    if ( ! $full ) {
        return $book;
    }

    $book['mainEntityOfPage'] = array( '@id' => $url . '#webpage' );

    // The site's own books point at the Organization node; other imprints get their own name.
    $publisher = $meta( 'publisher' );
    $own_names = array_map( 'strtolower', array_filter( array( langgam_fikir_org_name(), get_bloginfo( 'name' ), (string) langgam_fikir_get_option( 'legal_entity_name' ) ) ) );
    if ( '' === $publisher || in_array( strtolower( $publisher ), $own_names, true ) ) {
        $book['publisher'] = array( '@id' => langgam_fikir_schema_id( 'organization' ) );
    } else {
        $book['publisher'] = array( '@type' => 'Organization', 'name' => $publisher );
    }

    // Only the year is known: ISO 8601 allows "2024", which is truer than an invented 1 January.
    $year = $meta( 'year' );
    if ( preg_match( '/^\d{4}$/', $year ) ) {
        $book['datePublished'] = $year;
    }

    $book['inLanguage'] = langgam_fikir_schema_lang( $post_id );

    $description = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : get_post_field( 'post_content', $post_id );
    if ( $description ) {
        $book['description'] = langgam_fikir_trim_text( $description, 500 );
    }

    if ( function_exists( 'langgam_fikir_book_schema_extras' ) ) {
        $book = langgam_fikir_book_schema_extras( $post_id, $book );
    }

    // Offer: a numeric price (RM 35.00 -> 35.00) when there is one, and the availability.
    $status = $meta( 'status' ) ? $meta( 'status' ) : 'published';
    $availability = langgam_fikir_book_availability( $status );
    if ( $availability ) {
        $offer = array(
            '@type'        => 'Offer',
            'url'          => $url,
            'availability' => $availability,
            'seller'       => array( '@id' => langgam_fikir_schema_id( 'organization' ) ),
        );
        $price = str_replace( ',', '', $meta( 'price' ) );
        if ( preg_match( '/\d+(?:\.\d{1,2})?/', $price, $m ) && (float) $m[0] > 0 ) {
            $offer['price']         = number_format( (float) $m[0], 2, '.', '' );
            $offer['priceCurrency'] = 'MYR';
            $offer['itemCondition'] = 'https://schema.org/NewCondition';
        }
        $book['offers'] = $offer;
    }

    // Reviews and their aggregate (only real, entered reviews).
    if ( function_exists( 'langgam_fikir_get_reviews' ) ) {
        $reviews = langgam_fikir_get_reviews( $post_id );
        if ( $reviews ) {
            $book['review'] = array();
            foreach ( $reviews as $review ) {
                $node = array(
                    '@type'        => 'Review',
                    'reviewRating' => array(
                        '@type'       => 'Rating',
                        'ratingValue' => (int) $review['rating'],
                        'bestRating'  => 5,
                        'worstRating' => 1,
                    ),
                    'author'       => array( '@type' => 'Person', 'name' => $review['author'] ),
                    'reviewBody'   => $review['body'],
                );
                if ( ! empty( $review['title'] ) ) {
                    $node['name'] = $review['title'];
                }
                if ( ! empty( $review['date'] ) ) {
                    $node['datePublished'] = $review['date'];
                }
                $book['review'][] = $node;
            }
            $aggregate = function_exists( 'langgam_fikir_get_aggregate_rating' ) ? langgam_fikir_get_aggregate_rating( $post_id ) : null;
            if ( $aggregate ) {
                $book['aggregateRating'] = array(
                    '@type'       => 'AggregateRating',
                    'ratingValue' => $aggregate['average'],
                    'reviewCount' => $aggregate['count'],
                    'bestRating'  => 5,
                    'worstRating' => 1,
                );
            }
        }
    }

    $book['speakable'] = langgam_fikir_book_speakable();

    return $book;
}

/**
 * The books listed on the current view, in display order: the homepage showcase and latest
 * publications, or the archive/term query.
 *
 * @return int[]
 */
function langgam_fikir_schema_listed_book_ids() {
    if ( is_front_page() ) {
        $ids = function_exists( 'langgam_fikir_showcase_book_ids' ) ? array_map( 'intval', (array) langgam_fikir_showcase_book_ids() ) : array();
        $latest = get_posts( array(
            'post_type'      => 'book',
            'posts_per_page' => (int) langgam_fikir_get_option( 'featured_books_count', 6 ),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => $ids,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ) );
        return array_values( array_unique( array_merge( $ids, array_map( 'intval', $latest ) ) ) );
    }

    if ( is_post_type_archive( 'book' ) || is_tax( array( 'book-category', 'book-tag' ) ) ) {
        global $wp_query;
        return array_map( 'intval', wp_list_pluck( (array) $wp_query->posts, 'ID' ) );
    }

    return array();
}

/**
 * ItemList of the books on the page (a summary list: each item points at its book page).
 */
function langgam_fikir_schema_itemlist_node() {
    $ids = langgam_fikir_schema_listed_book_ids();
    if ( ! $ids ) {
        return null;
    }

    $base   = langgam_fikir_current_url() ? langgam_fikir_current_url() : home_url( '/' );
    $offset = 0;
    if ( ! is_front_page() ) {
        $paged  = max( 1, (int) get_query_var( 'paged' ) );
        $offset = ( $paged - 1 ) * (int) get_query_var( 'posts_per_page' );
    }

    $items = array();
    foreach ( $ids as $i => $id ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $offset + $i + 1,
            'url'      => get_permalink( $id ),
            'item'     => langgam_fikir_schema_book_node( $id, false ),
        );
    }

    if ( is_front_page() ) {
        $name = langgam_fikir_text( 'books_heading' );
        $name = $name ? $name : __( 'Latest Publications', 'langgam-fikir' );
    } elseif ( is_post_type_archive( 'book' ) ) {
        $name = post_type_archive_title( '', false );
    } else {
        $name = single_term_title( '', false );
    }

    return array(
        '@type'           => 'ItemList',
        '@id'             => $base . '#books',
        'name'            => wp_strip_all_tags( (string) $name ),
        'numberOfItems'   => count( $items ),
        'itemListOrder'   => langgam_fikir_schema_list_order(),
        'itemListElement' => $items,
    );
}

/**
 * The order of the book list: the archive's ?sort= (newest is the default and the homepage order).
 */
function langgam_fikir_schema_list_order() {
    $sort = is_front_page() ? 'newest' : ( isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : 'newest' ); // phpcs:ignore WordPress.Security.NonceVerification
    $map  = array(
        'newest' => 'ItemListOrderDescending',
        'oldest' => 'ItemListOrderAscending',
        'title'  => 'ItemListOrderAscending',
    );
    return 'https://schema.org/' . ( isset( $map[ $sort ] ) ? $map[ $sort ] : 'ItemListOrderDescending' );
}

/**
 * FAQPage for books and pages with FAQ entries.
 */
function langgam_fikir_schema_faq_node() {
    if ( ! is_singular( array( 'book', 'page' ) ) || ! function_exists( 'langgam_fikir_get_faqs' ) ) {
        return null;
    }
    $post_id = get_queried_object_id();
    $faqs    = langgam_fikir_get_faqs( $post_id );
    if ( ! $faqs ) {
        return null;
    }

    $questions = array();
    foreach ( $faqs as $faq ) {
        $questions[] = array(
            '@type'          => 'Question',
            'name'           => wp_strip_all_tags( $faq['question'] ),
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => $faq['answer'],
            ),
        );
    }

    $url = get_permalink( $post_id );
    return array(
        '@type'      => 'FAQPage',
        '@id'        => $url . '#faq',
        'url'        => $url,
        'isPartOf'   => array( '@id' => langgam_fikir_schema_id( 'website' ) ),
        'inLanguage' => langgam_fikir_schema_lang( $post_id ),
        'mainEntity' => $questions,
    );
}

/**
 * Speakable selectors for the book page.
 */
function langgam_fikir_book_speakable() {
    return array(
        '@type'       => 'SpeakableSpecification',
        'cssSelector' => array( '.book-details h1', '.book-synopsis-text' ),
    );
}

/* =========================================================================
 * Graph assembly
 * ====================================================================== */

/**
 * The nodes only the theme knows about: Book, the book list, FAQ and (when no plugin prints one)
 * the breadcrumb trail. Also returns the id the WebPage should name as its mainEntity.
 *
 * @return array{nodes:array, main:string, breadcrumb:string}
 */
function langgam_fikir_schema_theme_nodes( $include_breadcrumb ) {
    $nodes = array();
    $main  = '';

    if ( is_singular( 'book' ) ) {
        $book    = langgam_fikir_schema_book_node( get_queried_object_id() );
        $nodes[] = $book;
        $main    = $book['@id'];
    }

    $list = langgam_fikir_schema_itemlist_node();
    if ( $list ) {
        $nodes[] = $list;
        $main    = $main ? $main : $list['@id'];
    }

    $faq = langgam_fikir_schema_faq_node();
    if ( $faq ) {
        $nodes[] = $faq;
    }

    $breadcrumb_id = '';
    if ( $include_breadcrumb ) {
        $breadcrumb = langgam_fikir_schema_breadcrumb_node();
        if ( $breadcrumb ) {
            $nodes[]       = $breadcrumb;
            $breadcrumb_id = $breadcrumb['@id'];
        }
    }

    return array( 'nodes' => $nodes, 'main' => $main, 'breadcrumb' => $breadcrumb_id );
}

/**
 * The full graph when the theme owns SEO.
 */
function langgam_fikir_schema_graph() {
    $theme = langgam_fikir_schema_theme_nodes( true );
    $graph = array(
        langgam_fikir_schema_organization_node(),
        langgam_fikir_schema_website_node(),
    );

    $main = $theme['main'];
    if ( is_singular( 'post' ) ) {
        $post_id = get_queried_object_id();
        $article = langgam_fikir_schema_article_node( $post_id );
        $graph[] = $article;
        $graph[] = langgam_fikir_schema_person_node( (int) get_post_field( 'post_author', $post_id ) );
        $main    = $article['@id'];
    }

    $page = langgam_fikir_schema_webpage_node( $main, $theme['breadcrumb'] );
    if ( $page ) {
        $graph[] = $page;
    }

    /**
     * Filters the theme's JSON-LD graph before output.
     *
     * @param array $graph List of schema.org nodes.
     */
    return apply_filters( 'langgam_fikir_schema_graph', array_merge( $graph, $theme['nodes'] ) );
}

/**
 * Print a JSON-LD graph.
 */
function langgam_fikir_print_jsonld( $schema ) {
    if ( empty( $schema ) ) {
        return;
    }
    if ( ! isset( $schema['@context'] ) ) {
        $schema = array( '@context' => 'https://schema.org', '@graph' => array_values( array_filter( $schema ) ) );
    }
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}

/**
 * wp_head output. With Rank Math or Yoast the theme's nodes go into the plugin's graph
 * (see the filters below), so nothing is printed here.
 */
function langgam_fikir_output_schema() {
    if ( langgam_fikir_theme_owns_seo() ) {
        langgam_fikir_print_jsonld( langgam_fikir_schema_graph() );
        return;
    }

    if ( in_array( langgam_fikir_seo_plugin(), array( 'rank-math', 'yoast' ), true ) ) {
        return;
    }

    // Other plugins: a separate graph whose nodes reference the plugin's #organization.
    $theme = langgam_fikir_schema_theme_nodes( ! langgam_fikir_plugin_prints_breadcrumbs() );
    langgam_fikir_print_jsonld( $theme['nodes'] );
}
add_action( 'wp_head', 'langgam_fikir_output_schema', 5 );

/* =========================================================================
 * SEO plugin integration: one graph instead of two
 * ====================================================================== */

/**
 * Add what the plugin's Organization lacks. Never overwrites a value the plugin set.
 */
function langgam_fikir_fill_plugin_organization( $node ) {
    $theme_org = langgam_fikir_schema_organization_node();
    foreach ( array( 'legalName', 'slogan', 'identifier', 'contactPoint', 'alternateName' ) as $key ) {
        if ( empty( $node[ $key ] ) && ! empty( $theme_org[ $key ] ) ) {
            $node[ $key ] = $theme_org[ $key ];
        }
    }
    if ( ! empty( $theme_org['sameAs'] ) ) {
        $existing       = isset( $node['sameAs'] ) ? (array) $node['sameAs'] : array();
        $node['sameAs'] = array_values( array_unique( array_merge( $existing, $theme_org['sameAs'] ) ) );
    }
    return $node;
}

/**
 * Point the plugin's WebPage at the theme's main entity (the book, or the book list).
 */
function langgam_fikir_link_plugin_webpage( $node, $main_id ) {
    if ( $main_id && empty( $node['mainEntity'] ) ) {
        $node['mainEntity'] = array( '@id' => $main_id );
    }
    return $node;
}

/**
 * Whether a node's @type includes one of the given types.
 */
function langgam_fikir_node_is( $node, $types ) {
    $node_types = isset( $node['@type'] ) ? (array) $node['@type'] : array();
    return (bool) array_intersect( $node_types, (array) $types );
}

/**
 * Rank Math: merge the theme's nodes into its graph. Rank Math's own Book or Article schema
 * for the "book" post type should be set to None (Titles & Meta > Books) so there is one Book.
 */
function langgam_fikir_rank_math_json_ld( $data ) {
    if ( langgam_fikir_theme_owns_seo() || ! is_array( $data ) ) {
        return $data;
    }

    $theme = langgam_fikir_schema_theme_nodes( ! langgam_fikir_plugin_prints_breadcrumbs() );
    $org   = langgam_fikir_schema_id( 'organization' );

    foreach ( $data as $key => $node ) {
        if ( ! is_array( $node ) ) {
            continue;
        }
        if ( isset( $node['@id'] ) && $node['@id'] === $org ) {
            $data[ $key ] = langgam_fikir_fill_plugin_organization( $node );
        } elseif ( langgam_fikir_node_is( $node, array( 'WebPage', 'CollectionPage', 'ItemPage', 'AboutPage', 'ContactPage', 'ProfilePage' ) ) ) {
            $data[ $key ] = langgam_fikir_link_plugin_webpage( $node, $theme['main'] );
            if ( $theme['breadcrumb'] && empty( $data[ $key ]['breadcrumb'] ) ) {
                $data[ $key ]['breadcrumb'] = array( '@id' => $theme['breadcrumb'] );
            }
        }
    }

    foreach ( $theme['nodes'] as $i => $node ) {
        $data[ 'langgamFikir' . $i ] = $node;
    }
    return $data;
}
add_filter( 'rank_math/json_ld', 'langgam_fikir_rank_math_json_ld', 99 );

/**
 * Yoast: same merge through its graph filter.
 */
function langgam_fikir_yoast_schema_graph( $graph ) {
    if ( langgam_fikir_theme_owns_seo() || ! is_array( $graph ) ) {
        return $graph;
    }

    $theme = langgam_fikir_schema_theme_nodes( false ); // Yoast always prints a BreadcrumbList.
    $org   = langgam_fikir_schema_id( 'organization' );

    foreach ( $graph as $i => $node ) {
        if ( ! is_array( $node ) ) {
            continue;
        }
        if ( isset( $node['@id'] ) && $node['@id'] === $org ) {
            $graph[ $i ] = langgam_fikir_fill_plugin_organization( $node );
        } elseif ( langgam_fikir_node_is( $node, array( 'WebPage', 'CollectionPage', 'ItemPage', 'AboutPage', 'ContactPage', 'ProfilePage' ) ) ) {
            $graph[ $i ] = langgam_fikir_link_plugin_webpage( $node, $theme['main'] );
        }
    }

    return array_merge( $graph, $theme['nodes'] );
}
add_filter( 'wpseo_schema_graph', 'langgam_fikir_yoast_schema_graph', 99 );

/* =========================================================================
 * Font preload
 * ====================================================================== */

function langgam_fikir_preload_fonts() {
    $fonts_dir  = get_stylesheet_directory_uri() . '/assets/fonts/';
    $fonts_path = get_stylesheet_directory() . '/assets/fonts/';

    foreach ( array( 'crimson-text-v19-latin-regular.woff2', 'inter-v18-latin-regular.woff2' ) as $font ) {
        if ( file_exists( $fonts_path . $font ) ) {
            printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $fonts_dir . $font ) );
        }
    }
}
add_action( 'wp_head', 'langgam_fikir_preload_fonts', 1 );
