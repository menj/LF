<?php
/**
 * Template for displaying single posts
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main single-post-page">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <div class="container">
                <div class="single-post-layout">
                    
                    <header class="single-post-header">
                        <?php
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) :
                            ?>
                            <div class="single-post-categories">
                                <?php foreach ( $categories as $cat ) : ?>
                                    <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="resource-category">
                                        <?php echo esc_html( $cat->name ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="page-title-header">
                            <?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
                        </div>
                        
                        <div class="single-post-meta">
                            <span class="meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                    <?php echo esc_html( get_the_date() ); ?>
                                </time>
                            </span>
                            <?php if ( get_the_author_meta( 'ID' ) ) : ?>
                                <span class="meta-divider"></span>
                                <span class="meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <span class="post-author"><?php echo esc_html( get_the_author() ); ?></span>
                                </span>
                            <?php endif; ?>
                            <?php
                            $reading_time = ceil( str_word_count( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ) ) / 200 );
                            if ( $reading_time > 0 ) :
                                ?>
                                <span class="meta-divider"></span>
                                <span class="meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span class="reading-time">
                                        <?php echo esc_html( str_replace( '{n}', (int) $reading_time, langgam_fikir_get_option( 'label_read_time' ) ) ); ?>
                                    </span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>
                    
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="single-post-thumbnail">
                            <?php the_post_thumbnail( 'large' ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="single-post-content">
                        <div class="entry-content">
                            <?php
                            the_content();

                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html( langgam_fikir_get_option( 'label_pages' ) ),
                                'after'  => '</div>',
                            ) );
                            ?>
                        </div>
                    </div>
                    
                    <?php
                    $tags = get_the_tags();
                    if ( $tags ) :
                        ?>
                        <footer class="single-post-footer">
                            <div class="single-post-tags">
                                <span class="tags-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                                    <?php echo esc_html( langgam_fikir_get_option( 'label_tags' ) ); ?>
                                </span>
                                <span class="tags-divider"></span>
                                <?php foreach ( $tags as $tag ) : ?>
                                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="post-tag">
                                        <?php echo esc_html( $tag->name ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </footer>
                    <?php endif; ?>
                    
                    <nav class="single-post-navigation">
                        <?php
                        $prev_post = get_previous_post();
                        $next_post = get_next_post();
                        ?>
                        
                        <?php if ( $prev_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" class="post-nav-link post-nav-prev">
                                <span class="post-nav-label"><?php echo esc_html( langgam_fikir_get_option( 'label_post_prev' ) ); ?></span>
                                <span class="post-nav-title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
                            </a>
                        <?php else : ?>
                            <div class="post-nav-link post-nav-placeholder"></div>
                        <?php endif; ?>
                        
                        <?php if ( $next_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="post-nav-link post-nav-next">
                                <span class="post-nav-label"><?php echo esc_html( langgam_fikir_get_option( 'label_post_next' ) ); ?></span>
                                <span class="post-nav-title"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
                            </a>
                        <?php endif; ?>
                    </nav>
                    
                </div>
            </div>
            
        </article>
        
        <?php
        // Related Posts Section
        $categories = get_the_category();
        if ( ! empty( $categories ) ) :
            $related_args = array(
                'category__in'   => wp_list_pluck( $categories, 'term_id' ),
                'post__not_in'   => array( get_the_ID() ),
                'posts_per_page' => 3,
                'orderby'        => 'date',
            );
            $related_query = new WP_Query( $related_args );
            
            if ( $related_query->have_posts() ) :
                ?>
                <section class="related-posts-section">
                    <div class="container">
                        <div class="page-title-header page-title-header-sm centered">
                            <h2 class="page-title"><?php echo esc_html( langgam_fikir_get_option( 'label_related' ) ); ?></h2>
                        </div>
                        
                        <div class="resources-grid">
                            <?php
                            while ( $related_query->have_posts() ) :
                                $related_query->the_post();
                                ?>
                                <article class="resource-card">
                                    <?php
                                    $rel_categories = get_the_category();
                                    if ( ! empty( $rel_categories ) ) :
                                        ?>
                                        <span class="resource-category">
                                            <?php echo esc_html( $rel_categories[0]->name ); ?>
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
                    </div>
                </section>
                <?php
                wp_reset_postdata();
            endif;
        endif;
        ?>
        
        <?php
        // Comments section
        if ( comments_open() || get_comments_number() ) :
            ?>
            <div class="single-post-comments">
                <div class="container">
                    <div class="single-post-comments-inner">
                        <?php comments_template(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
    <?php endwhile; ?>
    
</main>

<?php
get_footer();
