<?php
/**
 * Main template file
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main">
    
    <div class="container">
        <div class="section section-compact">
            
            <?php
            if ( have_posts() ) :

                if ( is_home() && ! is_front_page() ) :
                    ?>
                    <header>
                        <div class="page-title-header">
                            <h1 class="page-title"><?php single_post_title(); ?></h1>
                        </div>
                    </header>
                    <?php
                elseif ( is_archive() ) :
                    ?>
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
                    <?php
                elseif ( is_search() ) :
                    ?>
                    <header>
                        <div class="page-title-header">
                            <h1 class="page-title">
                                <?php echo esc_html( str_replace( '{query}', get_search_query(), langgam_fikir_get_option( 'label_search_results' ) ) ); ?>
                            </h1>
                        </div>
                    </header>
                    <?php
                endif;

                while ( have_posts() ) :
                    the_post();
                    ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'mb-4' ); ?>>
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-thumbnail mb-2">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'large' ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <header class="entry-header mb-2">
                            <?php
                            if ( is_singular() ) :
                                the_title( '<h1 class="entry-title">', '</h1>' );
                            else :
                                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                            endif;
                            ?>
                            
                            <div class="entry-meta">
                                <?php
                                printf(
                                    '<span class="posted-on">%s</span>',
                                    '<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>'
                                );
                                ?>
                            </div>
                        </header>
                        
                        <div class="entry-content mb-3">
                            <?php
                            if ( is_singular() ) :
                                the_content();
                            else :
                                the_excerpt();
                            endif;
                            ?>
                        </div>
                        
                        <?php if ( ! is_singular() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="read-more">
                                <?php echo esc_html( langgam_fikir_get_option( 'label_read_more' ) ); ?>
                            </a>
                        <?php endif; ?>
                        
                    </article>
                    
                <?php
                endwhile;

                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html( langgam_fikir_get_option( 'label_prev' ) ),
                    'next_text' => esc_html( langgam_fikir_get_option( 'label_next' ) ),
                ) );

            else :
                if ( is_search() ) :
                    ?>
                    <header>
                        <div class="page-title-header">
                            <h1 class="page-title">
                                <?php echo esc_html( str_replace( '{query}', get_search_query(), langgam_fikir_get_option( 'label_search_results' ) ) ); ?>
                            </h1>
                        </div>
                    </header>
                    <?php
                endif;
                ?>
                
                <p><?php echo esc_html( langgam_fikir_get_option( 'msg_nothing_found' ) ); ?></p>
                <?php get_search_form(); ?>
                
                <?php
            endif;
            ?>
            
        </div>
    </div>
    
</main>

<?php
get_footer();
