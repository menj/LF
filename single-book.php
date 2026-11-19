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
            // Book JSON-LD is part of the page graph (inc/schema.php: langgam_fikir_schema_book_node()).
            $price  = langgam_fikir_get_book_meta( get_the_ID(), 'price' );
            $status = langgam_fikir_get_book_meta( get_the_ID(), 'status' );
            if ( empty( $status ) ) {
                $status = 'published';
            }
            ?>
            
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
                                <?php foreach ( langgam_fikir_book_info_rows( get_the_ID() ) as $info_row ) : ?>
                                    <div class="book-meta-item book-meta-item--<?php echo esc_attr( $info_row['key'] ); ?>">
                                        <span class="meta-label"><?php echo esc_html( $info_row['label'] ); ?></span>
                                        <span class="meta-value">
                                            <?php if ( $info_row['url'] ) : ?>
                                                <a class="meta-value-link" href="<?php echo esc_url( $info_row['url'] ); ?>" target="_blank" rel="noopener noreferrer">
                                                    <?php echo langgam_fikir_icon( 'oclc' ); // phpcs:ignore WordPress.Security.EscapeOutput -- theme-owned SVG file ?>
                                                    <?php echo esc_html( $info_row['value'] ); ?><span class="screen-reader-text"><?php esc_html_e( '(opens WorldCat in a new tab)', 'langgam-fikir' ); ?></span>
                                                </a>
                                            <?php else : ?>
                                                <?php echo esc_html( $info_row['value'] ); ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                                
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
                                
                                <?php
                                $book_category_chips = langgam_fikir_book_term_chips( get_the_ID(), 'book-category' );
                                $book_topic_chips    = langgam_fikir_book_term_chips( get_the_ID(), 'book-tag' );
                                ?>
                                <?php if ( $book_category_chips ) : ?>
                                    <div class="book-meta-item book-meta-item--terms">
                                        <span class="meta-label"><?php echo esc_html( langgam_fikir_text( 'lbl_category' ) ); ?></span>
                                        <span class="meta-value book-chips"><?php echo $book_category_chips; // phpcs:ignore WordPress.Security.EscapeOutput -- built and escaped in langgam_fikir_book_term_chips() ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ( $book_topic_chips ) : ?>
                                    <div class="book-meta-item book-meta-item--terms">
                                        <span class="meta-label"><?php echo esc_html( langgam_fikir_text( 'lbl_topics' ) ); ?></span>
                                        <span class="meta-value book-chips"><?php echo $book_topic_chips; // phpcs:ignore WordPress.Security.EscapeOutput -- built and escaped in langgam_fikir_book_term_chips() ?></span>
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
                        // "Cite this book" box (inc/citations.php), when Theme Options > Citations adds it to book pages.
                        if ( function_exists( 'langgam_fikir_cite_book_box' ) ) {
                            langgam_fikir_cite_book_box();
                        }
                        ?>
                        
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
                        <?php
                        if ( langgam_fikir_share_enabled_for( 'book' ) ) {
                            langgam_fikir_render_share_bar();
                        }
                        ?>

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
