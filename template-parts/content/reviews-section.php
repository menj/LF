<?php
/**
 * Reviews Section Template Part
 *
 * Renders a visible reviews section when the current book has stored
 * reviews. Pairs with the Review / aggregateRating JSON-LD emitted in
 * single-book.php so search results and visible page show the same data.
 *
 * Outputs nothing when no reviews exist.
 *
 * @package Langgam_Fikir
 * @since 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'langgam_fikir_get_reviews' ) ) {
    return;
}

$lf_reviews = langgam_fikir_get_reviews();

if ( empty( $lf_reviews ) ) {
    return;
}

$lf_aggregate = langgam_fikir_get_aggregate_rating();
?>

<section class="reviews-section" aria-labelledby="reviews-section-heading">
    <h2 id="reviews-section-heading" class="reviews-section-heading">
        <?php esc_html_e( 'Reviews', 'langgam-fikir' ); ?>
    </h2>

    <?php if ( $lf_aggregate ) : ?>
        <div class="reviews-aggregate" role="group" aria-label="<?php esc_attr_e( 'Average rating', 'langgam-fikir' ); ?>">
            <span class="reviews-aggregate-score">
                <?php echo esc_html( number_format( (float) $lf_aggregate['average'], 1 ) ); ?>
            </span>
            <?php echo langgam_fikir_render_stars( $lf_aggregate['average'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <span class="reviews-aggregate-count">
                <?php
                printf(
                    /* translators: %d: total number of reviews */
                    esc_html( _n( 'Based on %d review', 'Based on %d reviews', $lf_aggregate['count'], 'langgam-fikir' ) ),
                    (int) $lf_aggregate['count']
                );
                ?>
            </span>
            <span class="screen-reader-text">
                <?php
                printf(
                    /* translators: 1: rating, 2: count */
                    esc_html__( 'Average rating %1$s out of 5 based on %2$d reviews', 'langgam-fikir' ),
                    esc_html( number_format( (float) $lf_aggregate['average'], 1 ) ),
                    (int) $lf_aggregate['count']
                );
                ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="reviews-list">
        <?php foreach ( $lf_reviews as $lf_review ) : ?>
            <article class="review-item" itemscope itemtype="https://schema.org/Review">
                <header class="review-header">
                    <?php echo langgam_fikir_render_stars( $lf_review['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span class="screen-reader-text">
                        <?php
                        printf(
                            /* translators: %d: rating value */
                            esc_html__( '%d out of 5 stars', 'langgam-fikir' ),
                            (int) $lf_review['rating']
                        );
                        ?>
                    </span>

                    <?php if ( ! empty( $lf_review['title'] ) ) : ?>
                        <h3 class="review-title"><?php echo esc_html( $lf_review['title'] ); ?></h3>
                    <?php endif; ?>
                </header>

                <blockquote class="review-body">
                    <p><?php echo esc_html( $lf_review['body'] ); ?></p>
                </blockquote>

                <footer class="review-footer">
                    <cite class="review-author">
                        <?php echo esc_html( $lf_review['author'] ); ?>
                    </cite>
                    <?php if ( ! empty( $lf_review['date'] ) ) : ?>
                        <time class="review-date" datetime="<?php echo esc_attr( $lf_review['date'] ); ?>">
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $lf_review['date'] ) ) ); ?>
                        </time>
                    <?php endif; ?>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
</section>
