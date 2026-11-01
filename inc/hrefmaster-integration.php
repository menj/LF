<?php
/**
 * HrefMaster Plugin Integration
 *
 * Recognises the HrefMaster plugin (https://github.com/hrefmaster/hrefmaster)
 * and delegates all hreflang and HTML-lang concerns to it. This file is the
 * single source of truth for "what language is this page in" across the
 * theme — Schema.org `inLanguage`, Open Graph `og:locale`, and any future
 * locale-sensitive output should call `langgam_fikir_get_page_lang()` so
 * HrefMaster's per-post override takes precedence over the site default.
 *
 * Behaviour summary:
 *
 *  - When HrefMaster is ACTIVE:
 *      * The plugin owns hreflang `<link rel="alternate">` tag emission.
 *      * The plugin owns the `<html lang="">` attribute (when its
 *        "Enable HTML lang attribute" setting is on).
 *      * The theme reads the plugin's per-post `_hrefmaster_html_lang`
 *        meta when resolving page language for Schema/OG output.
 *      * An admin notice on the Theme Options page acknowledges the
 *        delegation so the site owner knows what's happening.
 *
 *  - When HrefMaster is INACTIVE:
 *      * The theme falls back to `get_locale()` for everything.
 *      * No hreflang tags are emitted (the theme deliberately does not
 *        synthesise them; that's a multilingual concern out of scope for
 *        a single-site publisher theme).
 *
 * @package Langgam_Fikir
 * @since   2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Whether the HrefMaster plugin is active on this site.
 *
 * Detection prefers the plugin's defined version constant — that's set
 * in hrefmaster.php on bootstrap, so its presence is unambiguous.
 *
 * @return bool
 */
function langgam_fikir_is_hrefmaster_active() {
    return defined( 'HREFMASTER_VERSION' ) || function_exists( 'hrefmaster_output_tags' );
}

/**
 * Resolve the BCP 47 language code for a page.
 *
 * Resolution order:
 *   1. HrefMaster per-post HTML lang override (`_hrefmaster_html_lang` meta)
 *      — if HrefMaster is active and the post has a value set
 *   2. HrefMaster's own self-lang resolver (which itself falls back to
 *      site locale) — if HrefMaster is active
 *   3. The theme's own `_book_language` post meta — book CPT only,
 *      independent of any plugin; this is the standalone fallback that
 *      keeps the Language row on book pages working without HrefMaster
 *   4. WordPress site locale via `get_locale()` — always available fallback
 *
 * Output is always normalised to hyphenated BCP 47 form (`en-US`, `ms-MY`)
 * regardless of which resolver supplied it. Callers wanting underscored
 * locale form (e.g. for `og:locale` which expects `en_US`) should convert
 * via `langgam_fikir_lang_to_og_locale()`.
 *
 * @param int $post_id Post ID. Defaults to current queried post.
 * @return string BCP 47 language code (e.g. "en-US", "ms-MY").
 */
function langgam_fikir_get_page_lang( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    // 1. HrefMaster per-post override (if plugin is active).
    if ( langgam_fikir_is_hrefmaster_active() && $post_id > 0 ) {
        $override = get_post_meta( $post_id, '_hrefmaster_html_lang', true );
        if ( ! empty( $override ) ) {
            return str_replace( '_', '-', sanitize_text_field( $override ) );
        }

        // 2. Defer to HrefMaster's own resolver if no per-post override.
        if ( function_exists( 'hrefmaster_get_self_lang' ) ) {
            return hrefmaster_get_self_lang( $post_id );
        }
    }

    // 3. Theme's own `_book_language` meta (standalone fallback for book CPT).
    if ( $post_id > 0 && get_post_type( $post_id ) === 'book' ) {
        $book_lang = get_post_meta( $post_id, '_book_language', true );
        if ( ! empty( $book_lang ) ) {
            return str_replace( '_', '-', sanitize_text_field( $book_lang ) );
        }
    }

    // 4. Final fallback: site locale, normalised to BCP 47.
    return str_replace( '_', '-', get_locale() );
}

/**
 * Convert a BCP 47 language code to the underscored locale form that
 * Open Graph's `og:locale` expects (e.g. "en-US" → "en_US").
 *
 * @param string $lang BCP 47 code.
 * @return string Underscored locale.
 */
function langgam_fikir_lang_to_og_locale( $lang ) {
    return str_replace( '-', '_', $lang );
}

/**
 * Compatibility shim: the legacy helper from earlier versions.
 *
 * Kept for any third-party code or templates that called the old function.
 * New code should call `langgam_fikir_get_page_lang()` instead.
 *
 * @deprecated 2.7.0 Use `langgam_fikir_get_page_lang()`.
 *
 * @param string $hreflang BCP 47 code.
 * @return string WordPress locale (e.g. "en_US") or empty string.
 */
if ( ! function_exists( 'langgam_fikir_hreflang_to_locale' ) ) {
    function langgam_fikir_hreflang_to_locale( $hreflang ) {
        $map = array(
            'ms'    => 'ms_MY',
            'en'    => 'en_US',
            'ar'    => 'ar',
            'id'    => 'id_ID',
            'zh'    => 'zh_CN',
            'zh-tw' => 'zh_TW',
            'ja'    => 'ja',
            'ko'    => 'ko_KR',
            'fr'    => 'fr_FR',
            'de'    => 'de_DE',
            'es'    => 'es_ES',
            'pt'    => 'pt_BR',
            'tr'    => 'tr_TR',
            'ur'    => 'ur',
            'fa'    => 'fa_IR',
            'bn'    => 'bn_BD',
            'hi'    => 'hi_IN',
            'th'    => 'th',
        );

        $hreflang = strtolower( str_replace( '_', '-', $hreflang ) );
        if ( isset( $map[ $hreflang ] ) ) {
            return $map[ $hreflang ];
        }

        $parts = explode( '-', $hreflang );
        if ( isset( $map[ $parts[0] ] ) ) {
            return $map[ $parts[0] ];
        }

        return '';
    }
}

/**
 * Admin notice on the Theme Options page acknowledging the delegation.
 *
 * Displays only when HrefMaster is active AND we're on the theme's
 * Options screen. Soft, dismissible, informational — not a warning.
 */
function langgam_fikir_hrefmaster_admin_notice() {
    if ( ! langgam_fikir_is_hrefmaster_active() ) {
        return;
    }

    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || strpos( $screen->id, 'langgam-fikir' ) === false ) {
        return;
    }

    $version = defined( 'HREFMASTER_VERSION' ) ? HREFMASTER_VERSION : '';
    ?>
    <div class="notice notice-info is-dismissible">
        <p>
            <strong><?php esc_html_e( 'HrefMaster detected', 'langgam-fikir' ); ?></strong>
            <?php
            if ( $version ) {
                printf(
                    /* translators: %s: HrefMaster version number */
                    ' <span style="opacity:.7;">' . esc_html__( '(v%s)', 'langgam-fikir' ) . '</span>',
                    esc_html( $version )
                );
            }
            ?>
            &nbsp;&mdash;
            <?php esc_html_e( 'hreflang and HTML lang attribute management is delegated to the plugin. The theme automatically aligns Schema.org inLanguage and Open Graph og:locale with HrefMaster\'s per-page settings.', 'langgam-fikir' ); ?>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'langgam_fikir_hrefmaster_admin_notice' );

/**
 * ─── Locale Switching ──────────────────────────────────────────────
 *
 * When a singular page has a HrefMaster per-post HTML lang override
 * AND a matching `.mo` translation file exists in the theme's
 * `/languages/` directory, switch the WordPress locale for that
 * request so theme `__()` / `_e()` strings render in the page's
 * declared language. Restores on shutdown.
 *
 * Skipped silently when:
 *  - HrefMaster is inactive
 *  - The current request is not for a singular post
 *  - The post has no HrefMaster lang override
 *  - The override resolves to the same locale already active
 *  - No matching `.mo` file exists in the theme's languages folder
 *
 * This is opt-in by way of having the .mo files present; sites that
 * don't ship multi-language theme strings see no behaviour change.
 */
function langgam_fikir_hrefmaster_switch_locale() {
    if ( ! langgam_fikir_is_hrefmaster_active() ) {
        return;
    }

    if ( ! is_singular() || ! function_exists( 'switch_to_locale' ) ) {
        return;
    }

    $post_id = get_queried_object_id();
    if ( ! $post_id ) {
        return;
    }

    $override = get_post_meta( $post_id, '_hrefmaster_html_lang', true );
    if ( empty( $override ) ) {
        return;
    }

    // Map BCP 47 → WordPress underscored locale via the legacy helper.
    $locale = langgam_fikir_hreflang_to_locale( $override );
    if ( empty( $locale ) || $locale === get_locale() ) {
        return;
    }

    // Only switch when a matching .mo file actually ships with the theme.
    $mofile = get_stylesheet_directory() . '/languages/langgam-fikir-' . $locale . '.mo';
    if ( ! file_exists( $mofile ) ) {
        return;
    }

    switch_to_locale( $locale );
    unload_textdomain( 'langgam-fikir' );
    load_textdomain( 'langgam-fikir', $mofile );
}
add_action( 'template_redirect', 'langgam_fikir_hrefmaster_switch_locale' );

/**
 * Restore the previous WordPress locale on shutdown.
 *
 * Paired with `langgam_fikir_hrefmaster_switch_locale()` above; called
 * unconditionally because `restore_previous_locale()` is a no-op when
 * the stack is empty.
 */
function langgam_fikir_hrefmaster_restore_locale() {
    if ( function_exists( 'restore_previous_locale' ) ) {
        restore_previous_locale();
    }
}
add_action( 'shutdown', 'langgam_fikir_hrefmaster_restore_locale' );
