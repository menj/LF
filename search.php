<?php
/**
 * Template for displaying search results
 *
 * @package Langgam_Fikir
 * @since 2.0.0
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">

    <section class="search-results-section">
        <div class="container">

            <header class="search-results-header reveal">
                <?php if ( function_exists( 'langgam_fikir_breadcrumbs' ) ) { langgam_fikir_breadcrumbs(); } ?>
                <div class="page-title-header">
                    <h1 class="page-title">
                        <?php
                        printf(
                            /* translators: %s: search query */
                            esc_html__( 'Search results for: %s', 'langgam-fikir' ),
                            '<span class="search-query">' . esc_html( get_search_query() ) . '</span>'
                        );
                        ?>
                    </h1>
                </div>
                <div class="search-results-form">
                    <?php get_search_form(); ?>
                </div>
            </header>

            <?php if ( have_posts() ) : ?>

                <p class="search-results-count">
                    <?php
                    printf(
                        /* translators: %s: number of results */
                        esc_html( _n( '%s result found', '%s results found', (int) $wp_query->found_posts, 'langgam-fikir' ) ),
                        '<strong>' . absint( $wp_query->found_posts ) . '</strong>'
                    );
                    ?>
                </p>

                <div class="search-results-grid stagger-children">
                    <?php
                    while ( have_posts() ) : the_post();
                        $post_type = get_post_type();
                    ?>
                        <article <?php post_class( 'search-result-card reveal' ); ?>>
                            <?php if ( 'book' === $post_type && has_post_thumbnail() ) : ?>
                                <div class="search-result-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'book-thumbnail' ); ?>
                                    </a>
                                </div>
                            <?php elseif ( has_post_thumbnail() ) : ?>
                                <div class="search-result-thumbnail search-result-thumbnail--wide">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="search-result-content">
                                <div class="search-result-meta">
                                    <?php if ( 'book' === $post_type ) : ?>
                                        <span class="search-result-type search-result-type--book"><?php esc_html_e( 'Book', 'langgam-fikir' ); ?></span>
                                        <?php
                                        $status = langgam_fikir_get_book_meta( get_the_ID(), 'status' );
                                        if ( $status ) :
                                            langgam_fikir_status_badge( $status, 'book-status-badge' );
                                        endif;
                                        ?>
                                    <?php elseif ( 'page' === $post_type ) : ?>
                                        <span class="search-result-type search-result-type--page"><?php esc_html_e( 'Page', 'langgam-fikir' ); ?></span>
                                    <?php else : ?>
                                        <span class="search-result-type search-result-type--post"><?php esc_html_e( 'Article', 'langgam-fikir' ); ?></span>
                                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                                    <?php endif; ?>
                                </div>

                                <h2 class="search-result-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                                <?php if ( 'book' === $post_type ) : ?>
                                    <?php
                                    $author = langgam_fikir_get_book_meta( get_the_ID(), 'author' );
                                    if ( $author ) :
                                    ?>
                                        <p class="search-result-author"><?php echo esc_html( $author ); ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <div class="search-result-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="read-more">
                                    <?php
                                    if ( 'book' === $post_type ) {
                                        esc_html_e( 'View Book', 'langgam-fikir' );
                                    } else {
                                        esc_html_e( 'Read More', 'langgam-fikir' );
                                    }
                                    ?>
                                    <span class="screen-reader-text"><?php
                                        /* translators: %s: post or book title */
                                        printf( esc_html__( 'about %s', 'langgam-fikir' ), esc_html( get_the_title() ) );
                                    ?></span>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => esc_html__( '&larr; Previous', 'langgam-fikir' ),
                    'next_text' => esc_html__( 'Next &rarr;', 'langgam-fikir' ),
                ) );
                ?>

            <?php else : ?>

                <div class="search-no-results reveal">
                    <h2><?php esc_html_e( 'Nothing found', 'langgam-fikir' ); ?></h2>
                    <p><?php esc_html_e( 'No results matched your search. Try different keywords or browse our catalogue.', 'langgam-fikir' ); ?></p>
                    <div class="search-no-results-form">
                        <?php get_search_form(); ?>
                    </div>
                    <div class="search-no-results-links">
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-dark">
                            <?php esc_html_e( 'Browse Books', 'langgam-fikir' ); ?>
                        </a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php
get_footer();
