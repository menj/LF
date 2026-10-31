<?php
/**
 * Publications showcase: the homepage books section with a parallax cover backdrop.
 *
 * The backdrop follows whichever book is active (hover, focus or the slow auto-rotation),
 * while every book stays a solid, readable, clickable card with its cover, status, title,
 * author and a link. The grid shows two or more rows.
 *
 * @package Langgam_Fikir
 * @since 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Print the showcase for a WP_Query of books.
 *
 * @param WP_Query $query Books to show.
 */
function langgam_fikir_render_book_showcase( $query ) {
    if ( ! $query->have_posts() ) {
        return;
    }

    $books = array();
    while ( $query->have_posts() ) {
        $query->the_post();
        $books[] = array(
            'id'       => get_the_ID(),
            'title'    => get_the_title(),
            'url'      => get_permalink(),
            'status'   => langgam_fikir_get_book_meta( get_the_ID(), 'status' ),
            'subtitle' => langgam_fikir_get_book_meta( get_the_ID(), 'subtitle' ),
            'author'   => langgam_fikir_get_book_meta( get_the_ID(), 'author' ),
            'year'     => langgam_fikir_get_book_meta( get_the_ID(), 'year' ),
            'cover'    => get_the_post_thumbnail( get_the_ID(), 'book-thumbnail', array( 'loading' => 'lazy', 'alt' => '' ) ),
            'backdrop' => get_the_post_thumbnail_url( get_the_ID(), 'large' ),
        );
    }
    wp_reset_postdata();

    $statuses = array( 'published', 'pre-order', 'coming-soon', 'reprinting', 'out-of-print' );
    $autoplay = 'off' !== langgam_fikir_get_option( 'showcase_autoplay' );
    ?>
    <div class="showcase" data-parallax-root data-showcase<?php echo $autoplay ? ' data-autoplay' : ''; ?>>
        <div class="showcase__backdrop" aria-hidden="true" data-parallax="0.2">
            <?php foreach ( $books as $i => $book ) : ?>
                <?php if ( $book['backdrop'] ) : ?>
                    <img class="showcase__bg<?php echo 0 === $i ? ' is-active' : ''; ?>" src="<?php echo esc_url( $book['backdrop'] ); ?>" alt="" data-index="<?php echo (int) $i; ?>" loading="lazy" decoding="async">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="showcase__inner">
            <div class="section-header-centered showcase__header">
                <h2 class="section-header-title"><?php echo esc_html( langgam_fikir_get_option( 'books_heading' ) ); ?></h2>
                <?php if ( langgam_fikir_get_option( 'books_subtitle' ) ) : ?>
                    <p class="section-header-subtitle"><?php echo esc_html( langgam_fikir_get_option( 'books_subtitle' ) ); ?></p>
                <?php endif; ?>
            </div>

            <ul class="showcase__grid">
                <?php foreach ( $books as $i => $book ) : ?>
                    <li class="showcase-card<?php echo 0 === $i ? ' is-active' : ''; ?>" data-index="<?php echo (int) $i; ?>">
                        <a class="showcase-card__link" href="<?php echo esc_url( $book['url'] ); ?>">
                            <span class="showcase-card__cover">
                                <?php if ( $book['cover'] ) : ?>
                                    <?php echo $book['cover']; // phpcs:ignore WordPress.Security.EscapeOutput -- core-generated image markup ?>
                                <?php else : ?>
                                    <span class="showcase-card__placeholder" aria-hidden="true"></span>
                                <?php endif; ?>
                            </span>
                            <span class="showcase-card__text">
                                <?php if ( in_array( $book['status'], $statuses, true ) ) : ?>
                                    <span class="book-status-badge <?php echo esc_attr( $book['status'] ); ?>"><?php echo esc_html( langgam_fikir_status_label( $book['status'] ) ); ?></span>
                                <?php endif; ?>
                                <span class="showcase-card__title"><?php echo esc_html( $book['title'] ); ?></span>
                                <?php if ( $book['subtitle'] ) : ?>
                                    <span class="showcase-card__subtitle"><?php echo esc_html( $book['subtitle'] ); ?></span>
                                <?php endif; ?>
                                <?php if ( $book['author'] || $book['year'] ) : ?>
                                    <span class="showcase-card__meta">
                                        <?php echo esc_html( trim( $book['author'] . ( $book['author'] && $book['year'] ? ' · ' : '' ) . $book['year'] ) ); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="showcase-card__more"><?php echo esc_html( langgam_fikir_get_option( 'label_learn_more' ) ); ?> <span aria-hidden="true">→</span></span>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ( langgam_fikir_get_option( 'view_more_label' ) ) : ?>
                <div class="view-more-container">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-light">
                        <?php echo esc_html( langgam_fikir_get_option( 'view_more_label' ) ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
