<?php
/**
 * Template for displaying single book posts
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <?php
            // Schema.org Book markup
            $author     = langgam_fikir_get_book_meta( get_the_ID(), 'author' );
            $editor     = langgam_fikir_get_book_meta( get_the_ID(), 'editor' );
            $translator = langgam_fikir_get_book_meta( get_the_ID(), 'translator' );
            $publisher  = langgam_fikir_get_book_meta( get_the_ID(), 'publisher' );
            $year       = langgam_fikir_get_book_meta( get_the_ID(), 'year' );
            $isbn       = langgam_fikir_get_book_meta( get_the_ID(), 'isbn' );
            $price      = langgam_fikir_get_book_meta( get_the_ID(), 'price' );
            $status     = langgam_fikir_get_book_meta( get_the_ID(), 'status' );
            if ( empty( $status ) ) {
                $status = 'published';
            }
            
            $schema = array(
                '@context'  => 'https://schema.org',
                '@type'     => 'Book',
                'name'      => get_the_title(),
                'url'       => get_permalink(),
            );
            
            if ( $author ) {
                $schema['author'] = array(
                    '@type' => 'Person',
                    'name'  => $author,
                );
            }
            
            if ( $editor ) {
                $schema['editor'] = array(
                    '@type' => 'Person',
                    'name'  => $editor,
                );
            }
            
            if ( $translator ) {
                $schema['translator'] = array(
                    '@type' => 'Person',
                    'name'  => $translator,
                );
            }
            
            if ( $publisher ) {
                $schema['publisher'] = array(
                    '@type' => 'Organization',
                    'name'  => $publisher,
                );
            }
            
            if ( $year ) {
                $schema['datePublished'] = $year . '-01-01';
            }
            
            if ( $isbn ) {
                $schema['isbn'] = $isbn;
            }
            
            if ( has_post_thumbnail() ) {
                $schema['image'] = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            }
            
            if ( has_excerpt() ) {
                $schema['description'] = get_the_excerpt();
            } elseif ( get_the_content() ) {
                $schema['description'] = wp_trim_words( get_the_content(), 30 );
            }
            
            // Add offer based on status
            if ( in_array( $status, array( 'published', 'new-release' ), true ) && $price ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'price'         => $price,
                    'priceCurrency' => 'MYR',
                    'availability'  => 'https://schema.org/InStock',
                    'url'           => get_permalink(),
                );
            } elseif ( $status === 'limited-edition' && $price ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'price'         => $price,
                    'priceCurrency' => 'MYR',
                    'availability'  => 'https://schema.org/LimitedAvailability',
                    'url'           => get_permalink(),
                );
            } elseif ( $status === 'digital-only' && $price ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'price'         => $price,
                    'priceCurrency' => 'MYR',
                    'availability'  => 'https://schema.org/OnlineOnly',
                    'url'           => get_permalink(),
                );
            } elseif ( $status === 'pre-order' && $price ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'price'         => $price,
                    'priceCurrency' => 'MYR',
                    'availability'  => 'https://schema.org/PreOrder',
                    'url'           => get_permalink(),
                );
            } elseif ( $status === 'coming-soon' ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'availability'  => 'https://schema.org/PreOrder',
                    'url'           => get_permalink(),
                );
            } elseif ( $status === 'announced' ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'availability'  => 'https://schema.org/PreOrder',
                    'url'           => get_permalink(),
                );
            } elseif ( in_array( $status, array( 'sold-out', 'reprinting' ), true ) ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'availability'  => 'https://schema.org/OutOfStock',
                    'url'           => get_permalink(),
                );
            } elseif ( $status === 'out-of-print' ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'availability'  => 'https://schema.org/Discontinued',
                    'url'           => get_permalink(),
                );
            }
            // Speakability for voice search
            $schema['speakable'] = langgam_fikir_book_speakable();

            // Per-book language signal (v2.7.0 / refined v2.8.0)
            // Resolution: HrefMaster meta → theme's _book_language → site locale
            if ( function_exists( 'langgam_fikir_get_page_lang' ) ) {
                $schema['inLanguage'] = langgam_fikir_get_page_lang( get_the_ID() );
            }
            
            // Reviews + aggregateRating (Phase D — v2.6.0)
            if ( function_exists( 'langgam_fikir_get_reviews' ) ) {
                $book_reviews = langgam_fikir_get_reviews();
                if ( ! empty( $book_reviews ) ) {
                    $schema_reviews = array();
                    foreach ( $book_reviews as $br ) {
                        $review_obj = array(
                            '@type'        => 'Review',
                            'reviewRating' => array(
                                '@type'       => 'Rating',
                                'ratingValue' => (int) $br['rating'],
                                'bestRating'  => 5,
                                'worstRating' => 1,
                            ),
                            'author'       => array(
                                '@type' => 'Person',
                                'name'  => $br['author'],
                            ),
                            'reviewBody'   => $br['body'],
                        );
                        if ( ! empty( $br['title'] ) ) {
                            $review_obj['name'] = $br['title'];
                        }
                        if ( ! empty( $br['date'] ) ) {
                            $review_obj['datePublished'] = $br['date'];
                        }
                        $schema_reviews[] = $review_obj;
                    }
                    $schema['review'] = $schema_reviews;

                    $agg = langgam_fikir_get_aggregate_rating();
                    if ( $agg ) {
                        $schema['aggregateRating'] = array(
                            '@type'       => 'AggregateRating',
                            'ratingValue' => $agg['average'],
                            'reviewCount' => $agg['count'],
                            'bestRating'  => 5,
                            'worstRating' => 1,
                        );
                    }
                }
            }
            ?>
            
            <script type="application/ld+json">
            <?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
            </script>
            
            <div class="container">
                <div class="single-book-content">
                    
                    <div class="book-image" data-parallax-root>
                        <div class="book-image-frame" data-parallax="0.05">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'book-cover' ); ?>
                            <?php else : ?>
                                <div class="book-cover-placeholder">
                                    <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                    <span class="placeholder-title"><?php the_title(); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="book-details">
                        <?php if ( function_exists( 'langgam_fikir_breadcrumbs' ) ) { langgam_fikir_breadcrumbs(); } ?>
                        <span class="book-status-badge <?php echo esc_attr( $status ); ?>">
                            <?php echo esc_html( strtoupper( str_replace( '-', ' ', $status ) ) ); ?>
                        </span>
                        
                        <h1><?php the_title(); ?></h1>
                        
                        <?php
                        $subtitle = langgam_fikir_get_book_meta( get_the_ID(), 'subtitle' );
                        if ( $subtitle ) :
                            ?>
                            <p class="book-subtitle-single"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                        
                        <?php
                        // Inline aggregate rating link (Phase D — v2.6.0)
                        if ( function_exists( 'langgam_fikir_get_aggregate_rating' ) ) {
                            $lf_inline_agg = langgam_fikir_get_aggregate_rating();
                            if ( $lf_inline_agg && function_exists( 'langgam_fikir_render_stars' ) ) :
                                ?>
                                <a class="book-inline-rating" href="#reviews-section-heading">
                                    <?php echo langgam_fikir_render_stars( $lf_inline_agg['average'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <span class="book-inline-rating-text">
                                        <?php
                                        printf(
                                            /* translators: 1: average rating, 2: review count */
                                            esc_html__( '%1$s (%2$d reviews)', 'langgam-fikir' ),
                                            esc_html( number_format( (float) $lf_inline_agg['average'], 1 ) ),
                                            (int) $lf_inline_agg['count']
                                        );
                                        ?>
                                    </span>
                                </a>
                                <?php
                            endif;
                        }
                        ?>
                        
                        <div class="book-meta-card">
                            <h2 class="screen-reader-text"><?php esc_html_e( 'Book Details', 'langgam-fikir' ); ?></h2>
                            <div class="book-meta-grid">
                                <?php if ( $author ) : ?>
                                    <div class="book-meta-item">
                                        <span class="meta-label"><?php _e( 'Author', 'langgam-fikir' ); ?></span>
                                        <span class="meta-value"><?php echo esc_html( $author ); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ( $publisher ) : ?>
                                    <div class="book-meta-item">
                                        <span class="meta-label"><?php _e( 'Publisher', 'langgam-fikir' ); ?></span>
                                        <span class="meta-value"><?php echo esc_html( $publisher ); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ( $editor ) : ?>
                                    <div class="book-meta-item">
                                        <span class="meta-label"><?php _e( 'Editor', 'langgam-fikir' ); ?></span>
                                        <span class="meta-value"><?php echo esc_html( $editor ); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ( $translator ) : ?>
                                    <div class="book-meta-item">
                                        <span class="meta-label"><?php _e( 'Translator', 'langgam-fikir' ); ?></span>
                                        <span class="meta-value"><?php echo esc_html( $translator ); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ( $year ) : ?>
                                    <div class="book-meta-item">
                                        <span class="meta-label"><?php _e( 'Year', 'langgam-fikir' ); ?></span>
                                        <span class="meta-value"><?php echo esc_html( $year ); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ( $isbn ) : ?>
                                    <div class="book-meta-item">
                                        <span class="meta-label"><?php _e( 'ISBN', 'langgam-fikir' ); ?></span>
                                        <span class="meta-value"><?php echo esc_html( $isbn ); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php
                                // Language row (v2.8.0) — show only when explicitly set
                                // (HrefMaster per-post lang OR theme's own _book_language meta)
                                if ( function_exists( 'langgam_fikir_book_has_explicit_language' )
                                    && langgam_fikir_book_has_explicit_language( get_the_ID() ) ) :
                                    $book_lang_code = langgam_fikir_get_page_lang( get_the_ID() );
                                    $book_lang_name = langgam_fikir_format_language_name( $book_lang_code );
                                    ?>
                                    <div class="book-meta-item">
                                        <span class="meta-label"><?php _e( 'Language', 'langgam-fikir' ); ?></span>
                                        <span class="meta-value" lang="<?php echo esc_attr( $book_lang_code ); ?>">
                                            <?php echo esc_html( $book_lang_name ); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ( $price && langgam_fikir_status_has_price( $status ) ) : ?>
                                <div class="book-meta-price">
                                    <span class="meta-label"><?php _e( 'Price', 'langgam-fikir' ); ?></span>
                                    <span class="price-amount">RM <?php echo number_format( (float) $price, 2 ); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="book-synopsis">
                            <h2><?php _e( 'About the Book', 'langgam-fikir' ); ?></h2>
                            <div class="book-synopsis-text">
                                <?php the_content(); ?>
                            </div>
                        </div>
                        
                        <?php
                        // Get book status
                        $book_status = langgam_fikir_get_book_meta( get_the_ID(), 'status' );
                        if ( empty( $book_status ) ) {
                            $book_status = 'published';
                        }
                        
                        // Show order section for orderable books
                        if ( langgam_fikir_status_is_orderable( $book_status ) ) :
                            ?>
                            <h2 class="screen-reader-text"><?php esc_html_e( 'Order This Book', 'langgam-fikir' ); ?></h2>
                            <div class="quantity-add-cart">
                                <div class="quantity-selector">
                                    <button class="quantity-btn quantity-decrease" aria-label="<?php esc_attr_e( 'Decrease quantity', 'langgam-fikir' ); ?>">−</button>
                                    <input type="number" class="quantity-input" value="1" min="1" max="99" aria-label="<?php esc_attr_e( 'Quantity', 'langgam-fikir' ); ?>">
                                    <button class="quantity-btn quantity-increase" aria-label="<?php esc_attr_e( 'Increase quantity', 'langgam-fikir' ); ?>">+</button>
                                </div>
                                
                                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                                    <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo absint( get_the_ID() ); ?>">
                                        <?php 
                                        if ( $book_status === 'pre-order' ) {
                                            _e( 'PRE-ORDER NOW', 'langgam-fikir' );
                                        } else {
                                            _e( 'ADD TO CART', 'langgam-fikir' );
                                        }
                                        ?>
                                    </button>
                                <?php else : ?>
                                    <?php
                                    // Build contact page URL with book information
                                    $contact_page = get_page_by_path( 'contact-langgam-fikir' );
                                    if ( $contact_page ) {
                                        $contact_url = add_query_arg( array(
                                            'book_id'    => get_the_ID(),
                                            'book_title' => urlencode( get_the_title() ),
                                        ), get_permalink( $contact_page ) );
                                    } else {
                                        $contact_url = home_url( '/contact-langgam-fikir/' );
                                    }
                                    ?>
                                    <a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn-primary add-to-cart-btn order-book-btn">
                                        <?php 
                                        if ( $book_status === 'pre-order' ) {
                                            _e( 'PRE-ORDER', 'langgam-fikir' );
                                        } else {
                                            _e( 'ORDER', 'langgam-fikir' );
                                        }
                                        ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php elseif ( $book_status === 'announced' ) : ?>
                            <div class="book-status-message announced">
                                <p><?php _e( 'This title has been announced. Stay tuned for further details.', 'langgam-fikir' ); ?></p>
                            </div>
                        <?php elseif ( $book_status === 'coming-soon' ) : ?>
                            <div class="book-status-message coming-soon">
                                <p><?php _e( 'This book is coming soon. Check back later for availability.', 'langgam-fikir' ); ?></p>
                            </div>
                        <?php elseif ( $book_status === 'sold-out' ) : ?>
                            <div class="book-status-message sold-out">
                                <p><?php _e( 'This book is currently sold out. Check back for updates on availability.', 'langgam-fikir' ); ?></p>
                            </div>
                        <?php elseif ( $book_status === 'reprinting' ) : ?>
                            <div class="book-status-message reprinting">
                                <p><?php _e( 'This book is currently being reprinted. It will be available again soon.', 'langgam-fikir' ); ?></p>
                            </div>
                        <?php elseif ( $book_status === 'out-of-print' ) : ?>
                            <div class="book-status-message out-of-print">
                                <p><?php _e( 'This book is currently out of print.', 'langgam-fikir' ); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Social Sharing Bar -->
                        <h2 class="screen-reader-text"><?php esc_html_e( 'Share This Book', 'langgam-fikir' ); ?></h2>
                        <div class="book-share-bar">
                            <span class="book-share-label"><?php esc_html_e( 'Share:', 'langgam-fikir' ); ?></span>
                            <div class="book-share-links">
                                <?php
                                $share_url   = rawurlencode( get_permalink() );
                                $share_title = rawurlencode( get_the_title() );
                                ?>
                                <a href="https://wa.me/?text=<?php echo $share_title; ?>%20<?php echo $share_url; ?>"
                                   class="share-btn share-whatsapp"
                                   target="_blank" rel="noopener noreferrer"
                                   aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'langgam-fikir' ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                    <span>WhatsApp</span>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>"
                                   class="share-btn share-facebook"
                                   target="_blank" rel="noopener noreferrer"
                                   aria-label="<?php esc_attr_e( 'Share on Facebook', 'langgam-fikir' ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    <span>Facebook</span>
                                </a>
                                <a href="https://x.com/intent/tweet?text=<?php echo $share_title; ?>&url=<?php echo $share_url; ?>"
                                   class="share-btn share-twitter"
                                   target="_blank" rel="noopener noreferrer"
                                   aria-label="<?php esc_attr_e( 'Share on X (Twitter)', 'langgam-fikir' ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    <span>X</span>
                                </a>
                                <button class="share-btn share-copy" data-url="<?php echo esc_attr( get_permalink() ); ?>"
                                        aria-label="<?php esc_attr_e( 'Copy link', 'langgam-fikir' ); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                    <span class="share-copy-text"><?php esc_html_e( 'Copy Link', 'langgam-fikir' ); ?></span>
                                </button>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
            
        </article>

        <?php
        // Related Books Section
        $book_categories = wp_get_post_terms( get_the_ID(), 'book-category', array( 'fields' => 'ids' ) );
        $related_args = array(
            'post_type'      => 'book',
            'posts_per_page' => 4,
            'post__not_in'   => array( get_the_ID() ),
            'orderby'        => 'rand',
        );
        if ( ! empty( $book_categories ) && ! is_wp_error( $book_categories ) ) {
            $related_args['tax_query'] = array(
                array(
                    'taxonomy' => 'book-category',
                    'field'    => 'term_id',
                    'terms'    => $book_categories,
                ),
            );
        }
        $related_books = new WP_Query( $related_args );

        // Reviews section (Phase D — v2.6.0): renders nothing if no reviews are set
        if ( function_exists( 'langgam_fikir_get_reviews' ) && ! empty( langgam_fikir_get_reviews() ) ) :
            ?>
            <div class="reviews-section-wrapper">
                <div class="container">
                    <?php get_template_part( 'template-parts/content/reviews-section' ); ?>
                </div>
            </div>
            <?php
        endif;

        // FAQ section (Phase C — v2.5.0): renders nothing if no FAQs are set
        if ( function_exists( 'langgam_fikir_get_faqs' ) && ! empty( langgam_fikir_get_faqs() ) ) :
            ?>
            <div class="faq-section-wrapper">
                <div class="container">
                    <?php get_template_part( 'template-parts/content/faq-section' ); ?>
                </div>
            </div>
            <?php
        endif;

        if ( $related_books->have_posts() ) :
            ?>
            <section class="related-books-section">
                <div class="container">
                    <div class="page-title-header page-title-header-sm centered">
                        <h2 class="page-title"><?php esc_html_e( 'You May Also Like', 'langgam-fikir' ); ?></h2>
                    </div>
                    <div class="books-grid stagger-children">
                        <?php
                        while ( $related_books->have_posts() ) :
                            $related_books->the_post();
                            get_template_part( 'template-parts/content/book-card' );
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    <?php endwhile; ?>
    
</main>

<?php
get_footer();
