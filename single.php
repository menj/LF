<?php
/**
 * Template for displaying single posts
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main single-post-page" tabindex="-1">
    
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
                            <?php if ( function_exists( 'langgam_fikir_breadcrumbs' ) ) { langgam_fikir_breadcrumbs(); } ?>
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
                                        <?php printf( _n( '%d min read', '%d min read', $reading_time, 'langgam-fikir' ), $reading_time ); ?>
                                    </span>
                                </span>
                            <?php endif; ?>
                            <?php
                            // Show "Last updated" only when modified date differs from published date by more than a day
                            $published_ts = get_the_time( 'U' );
                            $modified_ts  = get_the_modified_time( 'U' );
                            if ( $modified_ts - $published_ts > DAY_IN_SECONDS ) :
                                ?>
                                <span class="meta-divider"></span>
                                <span class="meta-item meta-item-updated">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.74 9.74 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                    <span class="post-updated">
                                        <?php
                                        printf(
                                            /* translators: %s: last-updated date */
                                            esc_html__( 'Updated %s', 'langgam-fikir' ),
                                            '<time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( get_the_modified_date() ) . '</time>'
                                        );
                                        ?>
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
                        <?php
                        // Auto-TOC for long-form posts (Phase D — v2.6.0): renders nothing if post is short
                        if ( function_exists( 'langgam_fikir_render_toc' ) ) {
                            langgam_fikir_render_toc();
                        }
                        ?>
                        <div class="entry-content">
                            <?php
                            the_content();

                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'langgam-fikir' ),
                                'after'  => '</div>',
                            ) );
                            ?>
                        </div>
                        
                        <?php get_template_part( 'template-parts/content/author-bio' ); ?>
                    </div>
                    
                    <?php
                    $tags = get_the_tags();
                    if ( $tags ) :
                        ?>
                        <footer class="single-post-footer">
                            <div class="single-post-tags">
                                <span class="tags-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                                    <?php _e( 'Tags', 'langgam-fikir' ); ?>
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
                                <span class="post-nav-label"><?php _e( 'Previous Article', 'langgam-fikir' ); ?></span>
                                <span class="post-nav-title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
                            </a>
                        <?php else : ?>
                            <div class="post-nav-link post-nav-placeholder"></div>
                        <?php endif; ?>
                        
                        <?php if ( $next_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="post-nav-link post-nav-next">
                                <span class="post-nav-label"><?php _e( 'Next Article', 'langgam-fikir' ); ?></span>
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
                            <h2 class="page-title"><?php _e( 'Related Articles', 'langgam-fikir' ); ?></h2>
                        </div>
                        
                        <div class="resources-grid">
                            <?php
                            while ( $related_query->have_posts() ) :
                                $related_query->the_post();
                                get_template_part( 'template-parts/content/resource-card' );
                            endwhile; ?>
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
