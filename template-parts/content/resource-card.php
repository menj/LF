<?php
/**
 * Template Part: Resource Card
 *
 * Displays a single post in a card format. Used in front-page.php,
 * home.php, single.php (related articles), and search results.
 *
 * Usage:
 *   get_template_part( 'template-parts/content/resource-card' );
 *
 * Expects to be called within a loop where the global $post is a standard post.
 *
 * @package Langgam_Fikir
 * @since 2.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$categories = get_the_category();
?>
<article class="resource-card reveal">
    <?php if ( ! empty( $categories ) ) : ?>
        <a class="resource-category" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>" rel="category">
            <?php echo esc_html( $categories[0]->name ); ?>
        </a>
    <?php endif; ?>

    <h3>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>

    <div class="resource-excerpt">
        <?php the_excerpt(); ?>
    </div>

    <div class="resource-card-footer">
        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
            <?php echo esc_html( get_the_date() ); ?>
        </time>
        <a href="<?php the_permalink(); ?>" class="read-more">
            <?php esc_html_e( 'Read More', 'langgam-fikir' ); ?>
            <span class="screen-reader-text"><?php
                /* translators: %s: post or book title */
                printf( esc_html__( 'about %s', 'langgam-fikir' ), esc_html( get_the_title() ) );
            ?></span>
        </a>
    </div>
</article>
