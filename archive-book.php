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
            
            <div class="section-header-centered">
                <?php if ( function_exists( 'langgam_fikir_breadcrumbs' ) ) { langgam_fikir_breadcrumbs(); } ?>
                <h1 class="section-header-title"><?php _e( 'Our Publications', 'langgam-fikir' ); ?></h1>
                <p class="section-header-subtitle"><?php _e( 'Explore our complete catalog of works, from contemporary fiction to critical essays and historical accounts.', 'langgam-fikir' ); ?></p>
            </div>
            
            <?php if ( have_posts() ) : ?>
                
                <div class="books-grid">
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
