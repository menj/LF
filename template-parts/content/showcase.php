<?php
/**
 * Template Part: Showcase
 *
 * Full-width interactive homepage section. The books are the theme's own
 * book card (template-parts/content/book-card.php), the same component as
 * Latest Publications, set on a dark band with a blurred, parallax backdrop; the backdrop crossfades to the highlighted book's cover. A card
 * becomes highlighted on hover or keyboard focus, or by the optional slow
 * auto-rotation (Theme Options > Homepage > Showcase).
 *
 * Usage:
 *   get_template_part( 'template-parts/content/showcase' );
 *
 * Data comes from langgam_fikir_get_showcase_items() (see
 * inc/template-functions.php), which returns an empty array when the
 * Showcase is disabled or fewer than 2 matching books have featured
 * images — in that case this part renders nothing. Books shown here are
 * excluded from the Latest Publications grid via
 * langgam_fikir_showcase_book_ids(), so the two sections never repeat a
 * title.
 *
 * Backgrounds use the 'book-showcase' image size (1600x900 crop) via
 * langgam_fikir_showcase_image_url(), which falls back to 'large' (never
 * the original upload) for covers that predate the size. Image URLs are
 * emitted as data-showcase-src and applied by showcase.js only when the
 * section approaches the viewport, so a below-the-fold Showcase adds no
 * image weight to the initial load.
 *
 * Settings: Appearance > Theme Options > Homepage > Showcase.
 *
 * @package Langgam_Fikir
 * @since 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$showcase_items = langgam_fikir_get_showcase_items();

if ( empty( $showcase_items ) ) {
    return;
}

$showcase_heading  = langgam_fikir_get_option( 'showcase_heading' );
$showcase_cols     = min( count( $showcase_items ), 3 );
$showcase_autoplay = langgam_fikir_get_option( 'showcase_autoplay' );
?>
<section class="lf-showcase" data-showcase<?php echo $showcase_autoplay ? ' data-showcase-autoplay' : ''; ?>>
    <div class="lf-showcase-backgrounds" aria-hidden="true">
        <div class="lf-showcase-bg lf-showcase-bg-default is-active" data-showcase-bg="default"></div>
        <?php foreach ( $showcase_items as $item ) : ?>
            <div class="lf-showcase-bg" data-showcase-bg="<?php echo esc_attr( $item['id'] ); ?>" data-showcase-src="<?php echo esc_url( $item['bg'] ); ?>"></div>
        <?php endforeach; ?>
    </div>
    <div class="lf-showcase-overlay" aria-hidden="true"></div>
    <div class="container lf-showcase-inner">
        <?php if ( $showcase_heading ) : ?>
            <h2 class="lf-showcase-heading"><?php echo esc_html( $showcase_heading ); ?></h2>
        <?php endif; ?>
        <div class="lf-showcase-grid lf-showcase-grid--cols-<?php echo esc_attr( $showcase_cols ); ?>">
            <?php
            global $post;
            foreach ( $showcase_items as $item ) {
                $post = get_post( $item['id'] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- standard loop setup, restored below
                setup_postdata( $post );
                get_template_part( 'template-parts/content/book-card', null, array( 'showcase_target' => $item['id'] ) );
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>
