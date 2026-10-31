<?php
/**
 * FAQ Accordion Section Template Part
 *
 * Renders a visible accordion of FAQ entries when the current post has any
 * stored in `_langgam_fikir_faq` meta. Pairs with the FAQPage JSON-LD
 * emitted by langgam_fikir_schema_faq() so search results and the visible
 * page show the same data — Google's preferred consistency model.
 *
 * Output is silent when no FAQ entries exist.
 *
 * @package Langgam_Fikir
 * @since 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'langgam_fikir_get_faqs' ) ) {
    return;
}

$faqs = langgam_fikir_get_faqs();

if ( empty( $faqs ) ) {
    return;
}
?>

<section class="faq-section" aria-labelledby="faq-section-heading">
    <h2 id="faq-section-heading" class="faq-section-heading">
        <?php esc_html_e( 'Frequently Asked Questions', 'langgam-fikir' ); ?>
    </h2>

    <div class="faq-accordion" role="list">
        <?php foreach ( $faqs as $i => $faq ) : ?>
            <?php
            $faq_id    = 'faq-' . get_the_ID() . '-' . $i;
            $panel_id  = $faq_id . '-panel';
            $button_id = $faq_id . '-button';
            ?>
            <div class="faq-item" role="listitem">
                <h3 class="faq-question">
                    <button type="button"
                            class="faq-question-button"
                            id="<?php echo esc_attr( $button_id ); ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo esc_attr( $panel_id ); ?>">
                        <span class="faq-question-text"><?php echo esc_html( $faq['question'] ); ?></span>
                        <span class="faq-question-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                    </button>
                </h3>
                <div class="faq-answer"
                     id="<?php echo esc_attr( $panel_id ); ?>"
                     role="region"
                     aria-labelledby="<?php echo esc_attr( $button_id ); ?>"
                     hidden>
                    <p><?php echo esc_html( $faq['answer'] ); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
