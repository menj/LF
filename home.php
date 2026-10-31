<?php
/**
 * Template for displaying blog/resources archive (home.php)
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">
    
    <section class="resources-archive">
        <div class="container">
            
            <header class="archive-header screen-reader-text">
                <h1><?php _e( 'Resources', 'langgam-fikir' ); ?></h1>
            </header>
            
            <?php
            // Featured Resource Section
            $featured_post = new WP_Query( array(
                'posts_per_page' => 1,
                'post__in'       => get_option( 'sticky_posts' ),
                'ignore_sticky_posts' => 0,
            ) );
            
            if ( ! $featured_post->have_posts() ) {
                // If no sticky posts, get the latest post
                $featured_post = new WP_Query( array(
                    'posts_per_page' => 1,
                ) );
            }
            
            if ( $featured_post->have_posts() ) :
                ?>
                <div class="featured-resource">
                    <div class="page-title-header">
                        <h2 class="page-title"><?php _e( 'Featured Resources', 'langgam-fikir' ); ?></h2>
                    </div>
                    
                    <div class="featured-resource-carousel">
                        <?php
                        while ( $featured_post->have_posts() ) :
                            $featured_post->the_post();
                            ?>
                            <div class="featured-resource-content">
                                <?php
                                $categories = get_the_category();
                                if ( ! empty( $categories ) ) :
                                    ?>
                                    <span class="resource-category">
                                        <?php echo esc_html( $categories[0]->name ); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <h2>
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="featured-resource-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        
                        <!-- Carousel arrows (optional - can be made functional with JavaScript) -->
                        <div class="carousel-arrows">
                            <button class="carousel-arrow carousel-prev" aria-label="<?php esc_attr_e( 'Previous', 'langgam-fikir' ); ?>">
                                ‹
                            </button>
                            <button class="carousel-arrow carousel-next" aria-label="<?php esc_attr_e( 'Next', 'langgam-fikir' ); ?>">
                                ›
                            </button>
                        </div>
                    </div>
                </div>
                <?php
                wp_reset_postdata();
            endif;
            ?>
            
            <!-- Other Resources Section -->
            <div class="other-resources-section">
                <div class="page-title-header">
                    <h2 class="page-title"><?php _e( 'Other Resources', 'langgam-fikir' ); ?></h2>
                </div>
                
                <?php if ( have_posts() ) : ?>
                    
                    <div class="resources-grid">
                        <?php
                        // Exclude the featured post from the grid
                        $excluded = array();
                        if ( $featured_post->have_posts() ) {
                            $featured_post->rewind_posts();
                            while ( $featured_post->have_posts() ) {
                                $featured_post->the_post();
                                $excluded[] = get_the_ID();
                            }
                        }
                        
                        // Reset and start main loop
                        wp_reset_postdata();
                        
                        while ( have_posts() ) :
                            the_post();
                            
                            // Skip if this is the featured post
                            if ( in_array( get_the_ID(), $excluded ) ) {
                                continue;
                            }
                            ?>
                            <?php get_template_part( 'template-parts/content/resource-card' ); ?>
                        <?php endwhile; ?>
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
                    
                    <p><?php _e( 'No resources found.', 'langgam-fikir' ); ?></p>
                    
                <?php endif; ?>
            </div>
            
        </div>
    </section>
    
</main>

<?php
get_footer();
