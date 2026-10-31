<?php
/**
 * Template for the "page not found" screen.
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main error-404">
    <div class="container">
        <div class="section section-compact">
            <div class="page-title-header">
                <h1 class="page-title"><?php echo esc_html( langgam_fikir_get_option( 'error_404_heading' ) ); ?></h1>
            </div>

            <div class="entry-content">
                <?php echo langgam_fikir_paragraphs( langgam_fikir_get_option( 'error_404_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                <?php if ( langgam_fikir_get_option( 'error_404_button' ) ) : ?>
                    <p><a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( langgam_fikir_get_option( 'error_404_button' ) ); ?></a></p>
                <?php endif; ?>
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
