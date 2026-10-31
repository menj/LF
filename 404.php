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

            <?php
            // One quip per line in Theme Options; a different one each time.
            $quips = array_values( array_filter( array_map( 'trim', explode( "\n", langgam_fikir_text( 'error_404_quips' ) ) ) ) );
            $quip  = $quips ? $quips[ wp_rand( 0, count( $quips ) - 1 ) ] : '';
            ?>

            <div class="error-404-content">
                <div class="error-404-scene" aria-hidden="true">
                    <span class="error-404-code"><?php esc_html_e( '404', 'langgam-fikir' ); ?></span>

                    <svg class="error-404-shelf" viewBox="0 0 360 150" xmlns="http://www.w3.org/2000/svg" focusable="false">
                        <rect class="shelf-board" x="6" y="128" width="348" height="9" rx="4.5"/>
                        <rect class="shelf-book shelf-book--a" x="22"  y="44" width="26" height="84" rx="3"/>
                        <rect class="shelf-book shelf-book--b" x="52"  y="62" width="22" height="66" rx="3"/>
                        <rect class="shelf-book shelf-book--c" x="78"  y="34" width="30" height="94" rx="3"/>
                        <rect class="shelf-book shelf-book--d" x="112" y="56" width="24" height="72" rx="3"/>
                        <rect class="shelf-gap" x="142" y="48" width="28" height="80" rx="3"/>
                        <text class="shelf-gap-mark" x="156" y="96" text-anchor="middle">?</text>
                        <g class="shelf-book-tilt">
                            <rect class="shelf-book shelf-book--e" x="176" y="42" width="26" height="86" rx="3"/>
                        </g>
                        <rect class="shelf-book shelf-book--f" x="212" y="64" width="28" height="64" rx="3"/>
                        <rect class="shelf-book shelf-book--a" x="244" y="38" width="24" height="90" rx="3"/>
                        <rect class="shelf-book shelf-book--c" x="272" y="58" width="30" height="70" rx="3"/>
                        <rect class="shelf-book shelf-book--b" x="306" y="46" width="26" height="82" rx="3"/>
                        <g class="shelf-page-adrift">
                            <path class="shelf-page" d="M318 14l22-6 8 24-22 6z"/>
                            <path class="shelf-page-line" d="M324 20l14-4M326 26l14-4"/>
                        </g>
                    </svg>
                </div>

                <?php if ( $quip ) : ?>
                    <p class="error-404-quip"><?php echo esc_html( $quip ); ?></p>
                <?php endif; ?>

                <h1 class="error-404-title"><?php echo esc_html( langgam_fikir_text( 'error_404_heading' ) ); ?></h1>
                <div class="error-404-message">
                    <?php echo langgam_fikir_paragraphs( langgam_fikir_text( 'error_404_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>

                <div class="error-404-search">
                    <?php get_search_form(); ?>
                </div>

                <div class="error-404-links">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                        <?php echo esc_html( langgam_fikir_text( 'error_404_home_label' ) ); ?>
                    </a>
                    <?php if ( langgam_fikir_text( 'error_404_books_label' ) ) : ?>
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-dark">
                            <?php echo esc_html( langgam_fikir_text( 'error_404_books_label' ) ); ?>
                        </a>
                    <?php endif; ?>
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

            if ( langgam_fikir_text( 'error_404_suggest' ) && $suggested_books->have_posts() ) :
            ?>
            <div class="error-404-suggestions reveal">
                <h2 class="section-header-title"><?php echo esc_html( langgam_fikir_text( 'error_404_suggest' ) ); ?></h2>
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
