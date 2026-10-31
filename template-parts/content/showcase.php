<?php
/**
 * Template Part: Showcase
 *
 * Full-width interactive homepage section. A grid of book titles over a
 * dark full-bleed background; hovering (or keyboard-focusing) a title
 * crossfades the background to that book's featured image. Inspired by
 * the "interactive link showcase" pattern.
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

$showcase_heading = langgam_fikir_get_option( 'showcase_heading' );
$showcase_cols    = min( count( $showcase_items ), 3 );
?>
<section class="lf-showcase">
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
        <nav class="lf-showcase-grid lf-showcase-grid--cols-<?php echo esc_attr( $showcase_cols ); ?>" aria-label="<?php esc_attr_e( 'Showcase', 'langgam-fikir' ); ?>">
            <?php foreach ( $showcase_items as $item ) : ?>
                <a class="lf-showcase-item" href="<?php echo esc_url( $item['url'] ); ?>" data-showcase-target="<?php echo esc_attr( $item['id'] ); ?>">
                    <?php if ( $item['status'] ) : ?>
                        <span class="lf-showcase-status"><?php echo esc_html( langgam_fikir_get_status_label( $item['status'] ) ); ?></span>
                    <?php endif; ?>
                    <span class="lf-showcase-title"><?php echo esc_html( $item['title'] ); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</section>
