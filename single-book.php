<?php
/**
 * Template for displaying single book posts
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main">
    
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
                $schema['isbn'] = (string) $isbn;
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
            if ( $status === 'published' && $price ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'price'         => number_format( (float) $price, 2, '.', '' ),
                    'priceCurrency' => 'MYR',
                    'availability'  => 'https://schema.org/InStock',
                    'url'           => get_permalink(),
                );
            } elseif ( $status === 'pre-order' && $price ) {
                $schema['offers'] = array(
                    '@type'         => 'Offer',
                    'price'         => number_format( (float) $price, 2, '.', '' ),
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
            } elseif ( $status === 'reprinting' ) {
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
            ?>
            
            <script type="application/ld+json">
            <?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); ?>
            </script>
            
            <div class="container">
                <div class="single-book-content">
                    
                    <div class="book-image">
                        <div class="book-image-frame">
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
                        
                        <div class="book-meta-card">
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
                            </div>
                            
                            <?php if ( $price && in_array( $status, array( 'published', 'pre-order' ) ) ) : ?>
                                <div class="book-meta-price">
                                    <span class="meta-label"><?php _e( 'Price', 'langgam-fikir' ); ?></span>
                                    <span class="price-amount">RM <?php echo esc_html( number_format( (float) $price, 2 ) ); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="book-synopsis">
                            <h3><?php _e( 'Synopsis', 'langgam-fikir' ); ?></h3>
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
                        
                        // Show order section for published and pre-order books
                        if ( in_array( $book_status, array( 'published', 'pre-order' ) ) ) :
                            ?>
                            <div class="quantity-add-cart">
                                <div class="quantity-selector">
                                    <button class="quantity-btn quantity-decrease" aria-label="<?php esc_attr_e( 'Decrease quantity', 'langgam-fikir' ); ?>">−</button>
                                    <input type="number" class="quantity-input" value="1" min="1" max="99" aria-label="<?php esc_attr_e( 'Quantity', 'langgam-fikir' ); ?>">
                                    <button class="quantity-btn quantity-increase" aria-label="<?php esc_attr_e( 'Increase quantity', 'langgam-fikir' ); ?>">+</button>
                                </div>
                                
                                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                                    <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo esc_attr( get_the_ID() ); ?>">
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
                                    $contact_url = langgam_fikir_get_page_url( 'contact' );
                                    if ( $contact_url ) {
                                        $contact_url = add_query_arg( array(
                                            'book_id'    => get_the_ID(),
                                            'book_title' => urlencode( get_the_title() ),
                                        ), $contact_url );
                                    }
                                    ?>
                                    <?php if ( $contact_url ) : ?>
                                        <a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn-primary add-to-cart-btn order-book-btn">
                                            <?php echo esc_html( langgam_fikir_get_option( 'pre-order' === $book_status ? 'preorder_label' : 'order_label' ) ); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php elseif ( $book_status === 'coming-soon' ) : ?>
                            <div class="book-status-message coming-soon">
                                <p><?php echo esc_html( langgam_fikir_get_option( 'msg_coming_soon' ) ); ?></p>
                            </div>
                        <?php elseif ( $book_status === 'reprinting' ) : ?>
                            <div class="book-status-message reprinting">
                                <p><?php echo esc_html( langgam_fikir_get_option( 'msg_reprinting' ) ); ?></p>
                            </div>
                        <?php elseif ( $book_status === 'out-of-print' ) : ?>
                            <div class="book-status-message out-of-print">
                                <p><?php echo esc_html( langgam_fikir_get_option( 'msg_out_of_print' ) ); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>
            
        </article>
        
    <?php endwhile; ?>
    
</main>

<?php
get_footer();
