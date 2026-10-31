<?php
/**
 * Template Part: Book Card
 *
 * Displays a single book in a card format. Used in front-page.php,
 * archive-book.php, 404.php, and taxonomy archives.
 *
 * Usage:
 *   get_template_part( 'template-parts/content/book-card' );
 *
 * Expects to be called within a loop where the global $post is a 'book' CPT.
 *
 * @package Langgam_Fikir
 * @since 2.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$status   = langgam_fikir_get_book_meta( get_the_ID(), 'status' );
$subtitle = langgam_fikir_get_book_meta( get_the_ID(), 'subtitle' );
$author   = langgam_fikir_get_book_meta( get_the_ID(), 'author' );
$year     = langgam_fikir_get_book_meta( get_the_ID(), 'year' );
$price    = langgam_fikir_get_book_meta( get_the_ID(), 'price' );
?>
<article class="book-card reveal">
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
        <?php langgam_fikir_status_badge( $status ); ?>
    </div>
    <h3 class="book-title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
    <?php if ( $subtitle ) : ?>
        <p class="book-subtitle"><?php echo esc_html( $subtitle ); ?></p>
    <?php endif; ?>
    <?php
    $excerpt = wp_trim_words( get_the_excerpt(), 20, '&hellip;' );
    if ( $excerpt ) :
        ?>
        <p class="book-excerpt"><?php echo wp_kses_post( $excerpt ); ?></p>
    <?php endif; ?>
    <div class="book-meta">
        <?php
        if ( $author ) {
            printf( esc_html__( 'Author: %s', 'langgam-fikir' ), esc_html( $author ) );
            echo '<br>';
        }
        if ( $year ) {
            printf( esc_html__( 'Year: %s', 'langgam-fikir' ), esc_html( $year ) );
        }
        ?>
    </div>
    <div class="book-card-footer">
        <div class="book-card-price">
            <?php // Status is already shown by the cover badge; the slot holds a price or stays empty. ?>
            <?php if ( $price && langgam_fikir_status_has_price( $status ) ) : ?>
                <span class="book-card-price-amount">RM <?php echo esc_html( number_format( (float) $price, 2 ) ); ?></span>
            <?php endif; ?>
        </div>
        <a href="<?php the_permalink(); ?>" class="btn btn-outline-dark">
            <?php esc_html_e( 'Learn More', 'langgam-fikir' ); ?>
            <span class="screen-reader-text"><?php
                /* translators: %s: post or book title */
                printf( esc_html__( 'about %s', 'langgam-fikir' ), esc_html( get_the_title() ) );
            ?></span>
        </a>
    </div>
</article>
