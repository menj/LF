<?php
/**
 * Template for the "page not found" screen.
 *
 * A small scene of a bookshelf with a gap, one random quip from Theme Settings > Labels,
 * and a few real books to click instead. All text is editable.
 *
 * @package Langgam_Fikir
 */

get_header();

// One quip per line in Theme Settings; show a different one each time.
$quips = array_values( array_filter( array_map( 'trim', explode( "\n", (string) langgam_fikir_get_option( 'error_404_quips' ) ) ) ) );
$quip  = $quips ? $quips[ wp_rand( 0, count( $quips ) - 1 ) ] : '';

$suggestions_heading = langgam_fikir_get_option( 'error_404_suggestions_heading' );
$suggestions         = $suggestions_heading ? new WP_Query( array(
    'post_type'           => 'book',
    'posts_per_page'      => 3,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
) ) : null;
?>

<main id="primary" class="site-main error-404">
    <div class="container">
        <div class="section section-compact">

            <div class="error-404__scene" aria-hidden="true">
                <p class="error-404__code">404</p>

                <svg class="error-404__shelf" viewBox="0 0 360 150" xmlns="http://www.w3.org/2000/svg" focusable="false">
                    <rect class="shelf-board" x="6" y="128" width="348" height="9" rx="4.5"/>
                    <rect class="book book--a" x="22"  y="44" width="26" height="84" rx="3"/>
                    <rect class="book book--b" x="52"  y="62" width="22" height="66" rx="3"/>
                    <rect class="book book--c" x="78"  y="34" width="30" height="94" rx="3"/>
                    <rect class="book book--d" x="112" y="56" width="24" height="72" rx="3"/>
                    <!-- the gap, with a dashed outline where the missing book belongs -->
                    <rect class="book-gap" x="142" y="48" width="28" height="80" rx="3"/>
                    <text class="gap-mark" x="156" y="96" text-anchor="middle">?</text>
                    <!-- a book that has toppled onto its neighbour and wobbles -->
                    <g class="book-tilt">
                        <rect class="book book--e" x="176" y="42" width="26" height="86" rx="3"/>
                    </g>
                    <rect class="book book--f" x="212" y="64" width="28" height="64" rx="3"/>
                    <rect class="book book--a" x="244" y="38" width="24" height="90" rx="3"/>
                    <rect class="book book--c" x="272" y="58" width="30" height="70" rx="3"/>
                    <rect class="book book--b" x="306" y="46" width="26" height="82" rx="3"/>
                    <!-- the page that wandered off -->
                    <g class="page-adrift">
                        <path class="page-sheet" d="M318 14l22-6 8 24-22 6z"/>
                        <path class="page-line" d="M324 20l14-4M326 26l14-4"/>
                    </g>
                </svg>
            </div>

            <div class="error-404__body">
                <?php if ( $quip ) : ?>
                    <p class="error-404__quip"><?php echo esc_html( $quip ); ?></p>
                <?php endif; ?>

                <h1 class="page-title"><?php echo esc_html( langgam_fikir_get_option( 'error_404_heading' ) ); ?></h1>

                <div class="entry-content">
                    <?php echo langgam_fikir_paragraphs( langgam_fikir_get_option( 'error_404_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>

                <div class="error-404__actions">
                    <?php if ( langgam_fikir_get_option( 'error_404_button' ) ) : ?>
                        <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( langgam_fikir_get_option( 'error_404_button' ) ); ?></a>
                    <?php endif; ?>
                    <?php get_search_form(); ?>
                </div>
            </div>

            <?php if ( $suggestions && $suggestions->have_posts() ) : ?>
                <section class="error-404__suggestions">
                    <h2 class="error-404__suggestions-title"><?php echo esc_html( $suggestions_heading ); ?></h2>
                    <ul class="error-404__books">
                        <?php while ( $suggestions->have_posts() ) : $suggestions->the_post(); ?>
                            <li class="error-404__book">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'book-thumbnail' ); ?>
                                    <?php else : ?>
                                        <span class="error-404__cover" aria-hidden="true"></span>
                                    <?php endif; ?>
                                    <span class="error-404__book-title"><?php the_title(); ?></span>
                                </a>
                            </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                </section>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php
get_footer();
