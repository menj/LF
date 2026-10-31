<?php
/**
 * Template for displaying pages
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
            
            <div class="container">
                <div class="section section-padded">
                    
                    <header class="entry-header mb-3">
                        <div class="page-title-header">
                            <?php if ( function_exists( 'langgam_fikir_breadcrumbs' ) ) { langgam_fikir_breadcrumbs(); } ?>
                            <?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
                        </div>
                    </header>
                    
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-thumbnail mb-3">
                            <?php the_post_thumbnail( 'large' ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'langgam-fikir' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>
                    
                    <?php
                    // FAQ section (Phase C — v2.5.0): renders nothing if no FAQs are set
                    get_template_part( 'template-parts/content/faq-section' );
                    ?>
                    
                </div>
            </div>
            
        </article>
        
        <?php
        // If comments are open or there is at least one comment, load up the comment template.
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;
        ?>
        
    <?php endwhile; ?>
    
</main>

<?php
get_footer();
