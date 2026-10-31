<?php
/**
 * The template for displaying comments
 *
 * @package Langgam_Fikir
 */

if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
            $comment_count = (int) get_comments_number();
            $title_html    = '<span>' . wp_kses_post( get_the_title() ) . '</span>';
            $template      = langgam_fikir_get_option( 1 === $comment_count ? 'label_comments_one' : 'label_comments_many' );
            echo str_replace(
                array( '{count}', '{title}' ),
                array( esc_html( number_format_i18n( $comment_count ) ), $title_html ),
                esc_html( $template )
            ); // phpcs:ignore WordPress.Security.EscapeOutput -- every part is escaped above.
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 50,
            ) );
            ?>
        </ol>

        <?php
        the_comments_navigation();

        if ( ! comments_open() ) :
            ?>
            <p class="no-comments"><?php echo esc_html( langgam_fikir_get_option( 'label_comments_closed' ) ); ?></p>
            <?php
        endif;

    endif;

    comment_form();
    ?>

</div>
