<?php
/**
 * Template for displaying book archives
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main">
    
    <section class="books-section">
        <div class="container">
            
            <div class="section-header-centered">
                <h1 class="section-header-title"><?php echo esc_html( langgam_fikir_get_option( 'books_archive_heading' ) ); ?></h1>
                <p class="section-header-subtitle"><?php echo esc_html( langgam_fikir_get_option( 'books_archive_subtitle' ) ); ?></p>
            </div>
            
            <?php if ( have_posts() ) : ?>
                
                <div class="books-grid">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        ?>
                        <article class="book-card">
                            <div class="book-cover">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'book-thumbnail' ); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php the_permalink(); ?>" class="book-cover-placeholder">
                                        <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                        <span class="placeholder-title"><?php the_title(); ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php
                            $status = langgam_fikir_get_book_meta( get_the_ID(), 'status' );
                            if ( in_array( $status, array( 'published', 'pre-order', 'coming-soon', 'reprinting', 'out-of-print' ), true ) ) :
                                ?>
                                <span class="book-status <?php echo esc_attr( $status ); ?>"><?php echo esc_html( langgam_fikir_status_label( $status ) ); ?></span>
                            <?php endif; ?>
                            </div>
                            <h3 class="book-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <?php
                            $subtitle = langgam_fikir_get_book_meta( get_the_ID(), 'subtitle' );
                            if ( $subtitle ) :
                                ?>
                                <p class="book-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                            <?php endif; ?>
                            <div class="book-meta">
                                <?php
                                $author = langgam_fikir_get_book_meta( get_the_ID(), 'author' );
                                $year   = langgam_fikir_get_book_meta( get_the_ID(), 'year' );
                                
                                if ( $author ) {
                                    printf( '%s: %s', esc_html( langgam_fikir_get_option( 'author_label' ) ), esc_html( $author ) );
                                    echo '<br>';
                                }
                                if ( $year ) {
                                    printf( '%s: %s', esc_html( langgam_fikir_get_option( 'year_label' ) ), esc_html( $year ) );
                                }
                                ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline-dark">
                                <?php echo esc_html( langgam_fikir_get_option( 'label_learn_more' ) ); ?>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <?php
                // Pagination
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html( langgam_fikir_get_option( 'label_prev' ) ),
                    'next_text' => esc_html( langgam_fikir_get_option( 'label_next' ) ),
                ) );
                ?>
                
            <?php else : ?>
                
                <p><?php echo esc_html( langgam_fikir_get_option( 'msg_no_books' ) ); ?></p>
                
            <?php endif; ?>
            
        </div>
    </section>
    
</main>

<?php
get_footer();
