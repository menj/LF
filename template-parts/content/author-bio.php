<?php
/**
 * Author Bio Template Part
 *
 * Renders the post author's bio block after the_content() on single.php.
 * Pulls from get_the_author_meta('description'), shows avatar, name (linked
 * to author archive), and a "View all posts" link.
 *
 * Outputs nothing if the author has no bio set.
 *
 * @package Langgam_Fikir
 * @since 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$author_id     = get_the_author_meta( 'ID' );
$author_bio    = get_the_author_meta( 'description', $author_id );
$author_name   = get_the_author_meta( 'display_name', $author_id );
$author_url    = get_author_posts_url( $author_id );
$author_avatar = get_avatar( $author_id, 96, '', $author_name, array( 'class' => 'author-bio-avatar' ) );
$author_site   = get_the_author_meta( 'user_url', $author_id );

// Don't render if there is no bio to show
if ( empty( $author_bio ) ) {
    return;
}
?>

<aside class="author-bio" aria-label="<?php esc_attr_e( 'About the author', 'langgam-fikir' ); ?>">
    <div class="author-bio-inner">
        <?php if ( $author_avatar ) : ?>
            <div class="author-bio-avatar-wrap">
                <a href="<?php echo esc_url( $author_url ); ?>" rel="author">
                    <?php echo $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — get_avatar returns sanitised HTML ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="author-bio-content">
            <p class="author-bio-eyebrow"><?php esc_html_e( 'About the author', 'langgam-fikir' ); ?></p>

            <h2 class="author-bio-name">
                <a href="<?php echo esc_url( $author_url ); ?>" rel="author"><?php echo esc_html( $author_name ); ?></a>
            </h2>

            <div class="author-bio-description">
                <?php echo wp_kses_post( wpautop( $author_bio ) ); ?>
            </div>

            <p class="author-bio-links">
                <a href="<?php echo esc_url( $author_url ); ?>" class="author-bio-archive-link" rel="author">
                    <?php
                    printf(
                        /* translators: %s: author display name */
                        esc_html__( 'View all posts by %s', 'langgam-fikir' ),
                        esc_html( $author_name )
                    );
                    ?>
                </a>
                <?php if ( ! empty( $author_site ) ) : ?>
                    <span class="author-bio-link-separator" aria-hidden="true">&middot;</span>
                    <a href="<?php echo esc_url( $author_site ); ?>" class="author-bio-website-link" rel="author external noopener">
                        <?php esc_html_e( 'Website', 'langgam-fikir' ); ?>
                    </a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</aside>
