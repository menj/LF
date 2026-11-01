<?php
/**
 * Social: profile icons and share buttons
 *
 * Icons are the monochrome SVGs in assets/icons/, inlined and recoloured with currentColor, so
 * they take the theme's colours and hover effect and cost no extra requests. Profile links come
 * from Theme Options > Social & SEO; share buttons from the same tab (which networks, and on
 * which kinds of page).
 *
 * @package Langgam_Fikir
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Inline SVG for an icon in assets/icons/. Returns '' for an unknown icon.
 * The files are part of the theme (not user input), prepared once per request: the title and
 * fixed fills are dropped, and the icon is marked decorative (its link carries the label).
 */
function langgam_fikir_icon( $slug ) {
    static $cache = array();
    $slug = sanitize_key( $slug );

    if ( ! isset( $cache[ $slug ] ) ) {
        $file = LANGGAM_FIKIR_DIR . '/assets/icons/' . $slug . '.svg';
        $svg  = ( '' !== $slug && is_readable( $file ) ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file

        if ( $svg ) {
            $svg = preg_replace( '#<title>.*?</title>#s', '', $svg );
            $svg = preg_replace( '/\s(?:role|fill)="[^"]*"/', '', $svg );
            $svg = preg_replace( '/<svg/', '<svg class="lf-icon lf-icon-' . esc_attr( $slug ) . '" fill="currentColor" aria-hidden="true" focusable="false"', $svg, 1 );
        }
        $cache[ $slug ] = $svg;
    }

    return $cache[ $slug ];
}

/**
 * Profile networks: icon slug => array( label, option key ).
 * Facebook, Instagram, X and WhatsApp keep their original option keys.
 */
function langgam_fikir_social_networks() {
    return array(
        'facebook'  => array( 'Facebook', 'social_facebook' ),
        'instagram' => array( 'Instagram', 'social_instagram' ),
        'x'         => array( 'X', 'social_twitter' ),
        'whatsapp'  => array( 'WhatsApp', 'social_whatsapp' ),
        'threads'   => array( 'Threads', 'social_threads' ),
        'tiktok'    => array( 'TikTok', 'social_tiktok' ),
        'youtube'   => array( 'YouTube', 'social_youtube' ),
        'telegram'  => array( 'Telegram', 'social_telegram' ),
        'bluesky'   => array( 'Bluesky', 'social_bluesky' ),
        'mastodon'  => array( 'Mastodon', 'social_mastodon' ),
        'goodreads' => array( 'Goodreads', 'social_goodreads' ),
        'medium'    => array( 'Medium', 'social_medium' ),
        'substack'  => array( 'Substack', 'social_substack' ),
        'spotify'   => array( 'Spotify', 'social_spotify' ),
        'pinterest' => array( 'Pinterest', 'social_pinterest' ),
    );
}

/**
 * The profile URLs that are set, in display order: icon slug => URL.
 *
 * @param array $overrides Optional per-slug URLs that win over Theme Options (the Contact page's own Instagram).
 */
function langgam_fikir_social_profiles( $overrides = array() ) {
    $profiles = array();
    foreach ( langgam_fikir_social_networks() as $slug => $network ) {
        $url = ! empty( $overrides[ $slug ] ) ? $overrides[ $slug ] : langgam_fikir_get_option( $network[1] );
        if ( $url ) {
            $profiles[ $slug ] = $url;
        }
    }
    return $profiles;
}

/**
 * Print the row of profile icons. Prints nothing when no profile is set.
 *
 * @param string $context CSS modifier: footer, contact.
 * @param array  $overrides Per-slug URL overrides.
 */
function langgam_fikir_render_social_links( $context = 'footer', $overrides = array() ) {
    $profiles = langgam_fikir_social_profiles( $overrides );
    if ( ! $profiles ) {
        return;
    }

    $networks = langgam_fikir_social_networks();
    ?>
    <nav class="social-links social-links--<?php echo esc_attr( $context ); ?>" aria-label="<?php echo esc_attr( langgam_fikir_text( 'lbl_follow_us' ) ); ?>">
        <?php foreach ( $profiles as $slug => $url ) : ?>
            <a class="social-icon" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"
               aria-label="<?php echo esc_attr( sprintf( /* translators: %s: network name */ __( 'Langgam Fikir on %s (opens in a new tab)', 'langgam-fikir' ), $networks[ $slug ][0] ) ); ?>"
               title="<?php echo esc_attr( $networks[ $slug ][0] ); ?>">
                <?php echo langgam_fikir_icon( $slug ); // phpcs:ignore WordPress.Security.EscapeOutput -- theme-owned SVG file, see langgam_fikir_icon() ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <?php
}

/**
 * Share networks: icon slug => array( label, URL template with {url} and {text} ).
 */
function langgam_fikir_share_networks() {
    return array(
        'whatsapp' => array( 'WhatsApp', 'https://wa.me/?text={text}%20{url}' ),
        'facebook' => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u={url}' ),
        'x'        => array( 'X', 'https://x.com/intent/tweet?text={text}&url={url}' ),
        'telegram' => array( 'Telegram', 'https://t.me/share/url?url={url}&text={text}' ),
        'threads'  => array( 'Threads', 'https://www.threads.net/intent/post?text={text}%20{url}' ),
        'bluesky'  => array( 'Bluesky', 'https://bsky.app/intent/compose?text={text}%20{url}' ),
        'reddit'   => array( 'Reddit', 'https://www.reddit.com/submit?url={url}&title={text}' ),
    );
}

/**
 * Whether the share bar is switched on for a kind of page: book, post or page.
 */
function langgam_fikir_share_enabled_for( $kind ) {
    return (bool) langgam_fikir_get_option( 'share_on_' . $kind . 's' ) ;
}

/**
 * Print the share bar for the current post. Prints nothing when no network is enabled.
 */
function langgam_fikir_render_share_bar() {
    $networks = array();
    foreach ( langgam_fikir_share_networks() as $slug => $network ) {
        if ( langgam_fikir_get_option( 'share_' . $slug ) ) {
            $networks[ $slug ] = $network;
        }
    }
    $copy = (bool) langgam_fikir_get_option( 'share_copy' );
    if ( ! $networks && ! $copy ) {
        return;
    }

    $url  = rawurlencode( get_permalink() );
    $text = rawurlencode( get_the_title() );
    ?>
    <h2 class="screen-reader-text"><?php esc_html_e( 'Share this page', 'langgam-fikir' ); ?></h2>
    <div class="book-share-bar">
        <span class="book-share-label"><?php echo esc_html( langgam_fikir_text( 'lbl_share' ) ); ?></span>
        <div class="book-share-links">
            <?php foreach ( $networks as $slug => $network ) : ?>
                <a href="<?php echo esc_url( str_replace( array( '{url}', '{text}' ), array( $url, $text ), $network[1] ) ); ?>"
                   class="share-btn share-<?php echo esc_attr( $slug ); ?>"
                   target="_blank" rel="noopener noreferrer"
                   aria-label="<?php echo esc_attr( sprintf( /* translators: %s: network name */ __( 'Share on %s', 'langgam-fikir' ), $network[0] ) ); ?>">
                    <?php echo langgam_fikir_icon( $slug ); // phpcs:ignore WordPress.Security.EscapeOutput -- theme-owned SVG file ?>
                    <span><?php echo esc_html( $network[0] ); ?></span>
                </a>
            <?php endforeach; ?>
            <?php if ( $copy ) : ?>
                <button type="button" class="share-btn share-copy" data-url="<?php echo esc_attr( get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'langgam-fikir' ); ?>">
                    <svg class="lf-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    <span class="share-copy-text"><?php esc_html_e( 'Copy Link', 'langgam-fikir' ); ?></span>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * The URLs of every set profile, for Schema.org sameAs.
 */
function langgam_fikir_social_profile_urls() {
    return array_values( langgam_fikir_social_profiles() );
}
