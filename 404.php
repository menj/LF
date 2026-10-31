<?php
/**
 * Template for displaying 404 pages (Not Found)
 *
 * @package Langgam_Fikir
 * @since 2.0.0
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">

    <section class="error-404">
        <div class="container">

            <div class="error-404-content">
                <span class="error-404-code"><?php esc_html_e( '404', 'langgam-fikir' ); ?></span>
                <h1 class="error-404-title"><?php esc_html_e( 'Page Not Found', 'langgam-fikir' ); ?></h1>
                <p class="error-404-message">
                    <?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'langgam-fikir' ); ?>
                </p>

                <div class="error-404-search">
                    <?php get_search_form(); ?>
                </div>

                <div class="error-404-links">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                        <?php esc_html_e( 'Back to Homepage', 'langgam-fikir' ); ?>
                    </a>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-dark">
                        <?php esc_html_e( 'Browse Books', 'langgam-fikir' ); ?>
                    </a>
                </div>
            </div>

            <?php
            // Suggest some books
            $suggested_books = new WP_Query( array(
                'post_type'      => 'book',
                'posts_per_page' => 3,
                'orderby'        => 'rand',
                'meta_query'     => array(
                    array(
                        'key'     => '_book_status',
                        'value'   => array( 'published', 'new-release', 'limited-edition' ),
                        'compare' => 'IN',
                    ),
                ),
            ) );

            if ( $suggested_books->have_posts() ) :
            ?>
            <div class="error-404-suggestions reveal">
                <h2 class="section-header-title"><?php esc_html_e( 'You might enjoy', 'langgam-fikir' ); ?></h2>
                <div class="books-grid stagger-children">
                    <?php
                    while ( $suggested_books->have_posts() ) : $suggested_books->the_post();
                        get_template_part( 'template-parts/content/book-card' );
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php
get_footer();
