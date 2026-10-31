<?php
/**
 * Archive template (categories, tags, dates, etc.)
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main">
    
    <section class="resources-archive">
        <div class="container">
            
            <header class="archive-header">
                <div class="page-title-header">
                    <h1 class="page-title"><?php the_archive_title(); ?></h1>
                </div>
                <?php
                $description = get_the_archive_description();
                if ( $description ) :
                    ?>
                    <div class="archive-description">
                        <?php echo wp_kses_post( $description ); ?>
                    </div>
                <?php endif; ?>
            </header>
            
            <?php if ( have_posts() ) : ?>
                
                <div class="resources-grid">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        ?>
                        <article class="resource-card">
                            <?php
                            $categories = get_the_category();
                            if ( ! empty( $categories ) ) :
                                ?>
                                <span class="resource-category">
                                    <?php echo esc_html( $categories[0]->name ); ?>
                                </span>
                            <?php endif; ?>
                            
                            <h3>
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="resource-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <div class="resource-meta">
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                    <?php echo esc_html( get_the_date() ); ?>
                                </time>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html( langgam_fikir_get_option( 'label_prev' ) ),
                    'next_text' => esc_html( langgam_fikir_get_option( 'label_next' ) ),
                ) );
                ?>
                
            <?php else : ?>
                
                <p><?php echo esc_html( langgam_fikir_get_option( 'msg_no_posts' ) ); ?></p>
                
            <?php endif; ?>
            
        </div>
    </section>
    
</main>

<?php
get_footer();
