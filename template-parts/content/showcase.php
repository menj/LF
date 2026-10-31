<?php
/**
 * Template Part: Showcase
 *
 * Full-width interactive homepage section. A grid of readable book cards
 * (cover, status, title, author, excerpt, link) over a blurred, parallax
 * backdrop; the backdrop crossfades to the highlighted book's cover. A card
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
        <nav class="lf-showcase-grid lf-showcase-grid--cols-<?php echo esc_attr( $showcase_cols ); ?>" aria-label="<?php esc_attr_e( 'Showcase', 'langgam-fikir' ); ?>">
            <?php foreach ( $showcase_items as $item ) : ?>
                <a class="lf-showcase-item" href="<?php echo esc_url( $item['url'] ); ?>" data-showcase-target="<?php echo esc_attr( $item['id'] ); ?>">
                    <span class="lf-showcase-cover">
                        <?php if ( $item['cover'] ) : ?>
                            <?php echo $item['cover']; // phpcs:ignore WordPress.Security.EscapeOutput -- core-generated image markup ?>
                        <?php else : ?>
                            <span class="lf-showcase-cover-empty" aria-hidden="true"></span>
                        <?php endif; ?>
                    </span>
                    <span class="lf-showcase-body">
                        <?php if ( $item['status'] ) : ?>
                            <span class="lf-showcase-status"><?php echo esc_html( langgam_fikir_get_status_label( $item['status'] ) ); ?></span>
                        <?php endif; ?>
                        <span class="lf-showcase-title"><?php echo esc_html( $item['title'] ); ?></span>
                        <?php if ( $item['subtitle'] ) : ?>
                            <span class="lf-showcase-subtitle"><?php echo esc_html( $item['subtitle'] ); ?></span>
                        <?php endif; ?>
                        <?php if ( $item['author'] || $item['year'] ) : ?>
                            <span class="lf-showcase-meta"><?php echo esc_html( trim( $item['author'] . ( $item['author'] && $item['year'] ? ' · ' : '' ) . $item['year'] ) ); ?></span>
                        <?php endif; ?>
                        <?php if ( $item['excerpt'] ) : ?>
                            <span class="lf-showcase-excerpt"><?php echo esc_html( $item['excerpt'] ); ?></span>
                        <?php endif; ?>
                        <span class="lf-showcase-more"><?php esc_html_e( 'Learn More', 'langgam-fikir' ); ?> <span aria-hidden="true">&rarr;</span></span>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</section>
