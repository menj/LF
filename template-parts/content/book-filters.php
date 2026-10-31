<?php
/**
 * Template Part: Book Filters
 *
 * Browse bar for the Publications archive and the book category and tag pages:
 * category chips, topic (tag) chips, a sort menu and a result count. Chips are
 * plain links to the existing taxonomy archives, so every state is crawlable.
 * The sort menu is a GET form (?sort=newest|oldest|title) handled in
 * inc/book-fields.php; it submits on change with JavaScript, and shows an Apply
 * button inside <noscript> for visitors without it.
 *
 * Usage (inside archive-book.php and the taxonomy templates):
 *   get_template_part( 'template-parts/content/book-filters' );
 *
 * @package Langgam_Fikir
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wp_query;

$archive_url = get_post_type_archive_link( 'book' );
$queried     = get_queried_object();
$current_cat = is_tax( 'book-category' ) && $queried ? (int) $queried->term_id : 0;
$current_tag = is_tax( 'book-tag' ) && $queried ? (int) $queried->term_id : 0;

$categories = get_terms( array( 'taxonomy' => 'book-category', 'hide_empty' => true, 'parent' => 0 ) );
$topics     = get_terms( array( 'taxonomy' => 'book-tag', 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 14 ) );
$categories = is_wp_error( $categories ) ? array() : $categories;
$topics     = is_wp_error( $topics ) ? array() : $topics;

$sort = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : 'newest'; // phpcs:ignore WordPress.Security.NonceVerification
$sort = in_array( $sort, array( 'newest', 'oldest', 'title' ), true ) ? $sort : 'newest';
$total = (int) $wp_query->found_posts;

// Nothing to browse or sort on a one-book catalogue.
if ( empty( $categories ) && empty( $topics ) && $total < 2 ) {
    return;
}

$form_action = $current_cat || $current_tag ? get_term_link( $queried ) : $archive_url;

// Chips keep the chosen sort order.
$with_sort = static function ( $url ) use ( $sort ) {
    return 'newest' === $sort ? $url : add_query_arg( 'sort', $sort, $url );
};
?>
<div class="book-filters reveal">

    <?php if ( $categories ) : ?>
        <nav class="book-filter-row" aria-label="<?php echo esc_attr( langgam_fikir_text( 'lbl_browse_category' ) ); ?>">
            <span class="book-filter-label"><?php echo esc_html( langgam_fikir_text( 'lbl_browse_category' ) ); ?></span>
            <a class="book-chip<?php echo ( ! $current_cat && ! $current_tag ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $with_sort( $archive_url ) ); ?>"<?php echo ( ! $current_cat && ! $current_tag ) ? ' aria-current="page"' : ''; ?>>
                <?php echo esc_html( langgam_fikir_text( 'lbl_all' ) ); ?>
            </a>
            <?php foreach ( $categories as $category ) : ?>
                <?php $is_current = $current_cat === (int) $category->term_id; ?>
                <a class="book-chip<?php echo $is_current ? ' is-active' : ''; ?>" href="<?php echo esc_url( $with_sort( get_term_link( $category ) ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
                    <?php echo esc_html( $category->name ); ?>
                    <span class="book-chip-count"><?php echo (int) $category->count; ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <?php if ( $topics ) : ?>
        <nav class="book-filter-row" aria-label="<?php echo esc_attr( langgam_fikir_text( 'lbl_browse_topics' ) ); ?>">
            <span class="book-filter-label"><?php echo esc_html( langgam_fikir_text( 'lbl_browse_topics' ) ); ?></span>
            <?php foreach ( $topics as $topic ) : ?>
                <?php $is_current = $current_tag === (int) $topic->term_id; ?>
                <a class="book-chip book-chip--topic<?php echo $is_current ? ' is-active' : ''; ?>" href="<?php echo esc_url( $with_sort( get_term_link( $topic ) ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
                    <?php echo esc_html( $topic->name ); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <div class="book-filter-bar">
        <p class="book-filter-count">
            <?php echo esc_html( str_replace( '{n}', number_format_i18n( $total ), langgam_fikir_text( 'lbl_titles_count' ) ) ); ?>
        </p>

        <?php if ( $total > 1 ) : ?>
            <form class="book-sort" method="get" action="<?php echo esc_url( $form_action ); ?>">
                <label for="book-sort-select"><?php echo esc_html( langgam_fikir_text( 'lbl_sort_by' ) ); ?></label>
                <select id="book-sort-select" name="sort" data-autosubmit>
                    <option value="newest" <?php selected( $sort, 'newest' ); ?>><?php echo esc_html( langgam_fikir_text( 'lbl_sort_newest' ) ); ?></option>
                    <option value="oldest" <?php selected( $sort, 'oldest' ); ?>><?php echo esc_html( langgam_fikir_text( 'lbl_sort_oldest' ) ); ?></option>
                    <option value="title" <?php selected( $sort, 'title' ); ?>><?php echo esc_html( langgam_fikir_text( 'lbl_sort_title' ) ); ?></option>
                </select>
                <noscript><button type="submit" class="btn btn-outline-dark book-sort-apply"><?php echo esc_html( langgam_fikir_text( 'lbl_apply' ) ); ?></button></noscript>
            </form>
        <?php endif; ?>
    </div>
</div>
