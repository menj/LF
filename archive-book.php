<?php
/**
 * Template for displaying book archives
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">
    
    <section class="books-section">
        <div class="container">
            
            <div class="section-header-centered section-header--archive">
                <?php if ( function_exists( 'langgam_fikir_breadcrumbs' ) ) { langgam_fikir_breadcrumbs(); } ?>
                <h1 class="section-header-title"><?php _e( 'Our Publications', 'langgam-fikir' ); ?></h1>
                <p class="section-header-subtitle"><?php _e( 'Explore our complete catalog of works, from contemporary fiction to critical essays and historical accounts.', 'langgam-fikir' ); ?></p>
            </div>
            
            <form class="lf-search-pill" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="hidden" name="post_type" value="book">
                <label class="screen-reader-text" for="lf-book-search"><?php esc_html_e( 'Search books', 'langgam-fikir' ); ?></label>
                <input id="lf-book-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search by title or author&hellip;', 'langgam-fikir' ); ?>">
                <button type="submit" aria-label="<?php esc_attr_e( 'Search', 'langgam-fikir' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </button>
            </form>
            
            <?php get_template_part( 'template-parts/content/book-filters' ); ?>
            
            <?php if ( have_posts() ) : ?>
                
                <div class="books-grid books-grid--covers">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/content/book-card' );
                    endwhile; ?>
                </div>
                
                <?php
                // Pagination
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => __( '&larr; Previous', 'langgam-fikir' ),
                    'next_text' => __( 'Next &rarr;', 'langgam-fikir' ),
                ) );
                ?>
                
            <?php else : ?>
                
                <p><?php _e( 'No books found.', 'langgam-fikir' ); ?></p>
                
            <?php endif; ?>
            
        </div>
    </section>
    
</main>

<?php
get_footer();
