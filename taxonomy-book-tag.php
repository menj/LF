<?php
/**
 * Template for displaying book tag archives
 *
 * @package Langgam_Fikir
 * @since 2.0.1
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">

    <section class="books-archive">
        <div class="container">

            <header class="page-title-header reveal">
                <?php if ( function_exists( 'langgam_fikir_breadcrumbs' ) ) { langgam_fikir_breadcrumbs(); } ?>
                <h1 class="page-title">
                    <?php
                    printf(
                        /* translators: %s: tag name */
                        esc_html__( 'Tagged: %s', 'langgam-fikir' ),
                        '<span>' . single_term_title( '', false ) . '</span>'
                    );
                    ?>
                </h1>
                <?php
                $term_description = term_description();
                if ( $term_description ) :
                ?>
                    <div class="archive-description">
                        <?php echo wp_kses_post( $term_description ); ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if ( have_posts() ) : ?>

                <div class="books-grid stagger-children">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/content/book-card' );
                    endwhile;
                    ?>
                </div>

                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html__( '&larr; Previous', 'langgam-fikir' ),
                    'next_text' => esc_html__( 'Next &rarr;', 'langgam-fikir' ),
                ) );
                ?>

            <?php else : ?>

                <div class="no-results reveal">
                    <p><?php esc_html_e( 'No books found with this tag.', 'langgam-fikir' ); ?></p>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-dark">
                        <?php esc_html_e( 'Browse All Books', 'langgam-fikir' ); ?>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php
get_footer();
