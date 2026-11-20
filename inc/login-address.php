<?php
/**
 * Private login address
 *
 * Moves the log in screen to an address of your choice (Theme Options > Login Page > Login
 * Address), for example /masuk/. While it is on, anyone who is not logged in and opens
 * wp-login.php, wp-register.php or anything under wp-admin is sent to the "redirect" address
 * (by default /404/, which shows the Page Not Found page), so automated password guessing
 * cannot find the form. Every login link WordPress prints (site_url, login_url, redirects,
 * password reset and privacy request emails) points at the private address.
 *
 * Locked out? Rename this theme's folder by FTP or the host's file manager: WordPress then
 * falls back to the default theme and the normal wp-login.php.
 *
 * The request is recognised at after_setup_theme (the earliest a theme can run) and handled at
 * wp_loaded, before WordPress decides which screen to show. Settings are read straight from
 * the stored options, without building the Theme Options schema, so nothing here loads
 * translations before init.
 *
 * @package Langgam_Fikir
 * @since 2.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Whether a slug can be the login or redirect address: not empty, not a WordPress file or
 * folder, and not a query variable WordPress already uses.
 */
function langgam_fikir_login_address_slug_ok( $slug ) {
    $slug = (string) $slug;
    if ( '' === $slug || false !== strpos( $slug, 'wp-login' ) || in_array( $slug, array( 'wp-admin', 'wp-content', 'wp-includes', 'wp-json', 'admin', 'dashboard' ), true ) ) {
        return false;
    }
    if ( class_exists( 'WP' ) ) {
        $wp = new WP();
        if ( in_array( $slug, array_merge( $wp->public_query_vars, $wp->private_query_vars ), true ) ) {
            return false;
        }
    }
    return true;
}

/**
 * Clean a submitted slug; an unusable one falls back to $default.
 */
function langgam_fikir_login_address_clean_slug( $value, $default ) {
    $slug = sanitize_title_with_dashes( (string) $value );
    return langgam_fikir_login_address_slug_ok( $slug ) ? $slug : $default;
}

/**
 * The stored settings: array( on, slug, redirect ).
 */
function langgam_fikir_login_address_settings() {
    $o = get_option( 'langgam_fikir_options', array() );
    $o = is_array( $o ) ? $o : array();
    return array(
        'on'       => ! empty( $o['login_hide'] ),
        'slug'     => langgam_fikir_login_address_clean_slug( isset( $o['login_slug'] ) ? $o['login_slug'] : '', 'login' ),
        'redirect' => langgam_fikir_login_address_clean_slug( isset( $o['login_redirect'] ) ? $o['login_redirect'] : '', '404' ),
    );
}

/**
 * Whether the private address is in force on this request.
 */
function langgam_fikir_login_address_active() {
    static $active = null;
    if ( null === $active ) {
        $s      = langgam_fikir_login_address_settings();
        $active = $s['on']
            && ! ( defined( 'WP_CLI' ) && WP_CLI )
            // Stand aside while another tool still moves the login (detected by inc/plugin-takeover.php).
            && ! ( function_exists( 'langgam_fikir_feature_conflict' ) && langgam_fikir_feature_conflict( 'login_address' ) );
    }
    return $active;
}

function langgam_fikir_login_address_trailing() {
    return '/' === substr( (string) get_option( 'permalink_structure' ), -1, 1 );
}

function langgam_fikir_login_address_slashit( $string ) {
    return langgam_fikir_login_address_trailing() ? trailingslashit( $string ) : untrailingslashit( $string );
}

/**
 * The address of a slug: /slug/ with pretty permalinks, /?slug without.
 */
function langgam_fikir_login_address_url_for( $slug, $scheme = null ) {
    /**
     * Filters the base address the login slug is added to (default: the home page).
     */
    $base = apply_filters( 'langgam_fikir_login_address_base', home_url( '/', $scheme ) );
    return get_option( 'permalink_structure' ) ? langgam_fikir_login_address_slashit( $base . $slug ) : $base . '?' . $slug;
}

/**
 * The private login address.
 */
function langgam_fikir_login_address_url( $scheme = null ) {
    $s = langgam_fikir_login_address_settings();
    return langgam_fikir_login_address_url_for( $s['slug'], $scheme );
}

/**
 * Where people who are not logged in are sent from wp-login.php and wp-admin.
 */
function langgam_fikir_login_address_redirect_url( $scheme = null ) {
    $s = langgam_fikir_login_address_settings();
    if ( get_option( 'permalink_structure' ) ) {
        return langgam_fikir_login_address_slashit( home_url( '/', $scheme ) . $s['redirect'] );
    }
    return home_url( '/', $scheme ) . '?' . $s['redirect'];
}

/* =========================================================================
 * Recognise the request
 * ====================================================================== */

/**
 * Set once the request is for wp-login.php or wp-register.php itself (which then shows the
 * site's 404 page instead).
 */
function langgam_fikir_login_address_blocked( $set = null ) {
    static $blocked = false;
    if ( null !== $set ) {
        $blocked = (bool) $set;
    }
    return $blocked;
}

function langgam_fikir_login_address_boot() {
    if ( ! langgam_fikir_login_address_active() ) {
        return;
    }
    global $pagenow;

    $s       = langgam_fikir_login_address_settings();
    $uri     = isset( $_SERVER['REQUEST_URI'] ) ? rawurldecode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- compared only
    $request = wp_parse_url( $uri );
    $path    = isset( $request['path'] ) ? untrailingslashit( $request['path'] ) : '';

    if ( ( false !== strpos( $uri, 'wp-login.php' ) || site_url( 'wp-login', 'relative' ) === $path ) && ! is_admin() ) {
        langgam_fikir_login_address_blocked( true );
        $_SERVER['REQUEST_URI'] = langgam_fikir_login_address_slashit( '/' . str_repeat( '-/', 10 ) );
        $pagenow                = 'index.php'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
    } elseif ( home_url( $s['slug'], 'relative' ) === $path
        || ( ! get_option( 'permalink_structure' ) && isset( $_GET[ $s['slug'] ] ) && empty( $_GET[ $s['slug'] ] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        $_SERVER['SCRIPT_NAME'] = $s['slug'];
        $pagenow                = 'wp-login.php'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
    } elseif ( ( false !== strpos( $uri, 'wp-register.php' ) || site_url( 'wp-register', 'relative' ) === $path ) && ! is_admin() ) {
        langgam_fikir_login_address_blocked( true );
        $_SERVER['REQUEST_URI'] = langgam_fikir_login_address_slashit( '/' . str_repeat( '-/', 10 ) );
        $pagenow                = 'index.php'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
    }

    // The Customizer is for logged-in people only.
    if ( 'customize.php' === $pagenow && ! is_user_logged_in() ) {
        wp_die( esc_html__( 'This has been disabled.', 'langgam-fikir' ), 403 );
    }

    // WordPress would otherwise send /login, /admin and /dashboard to the real screens.
    remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );

    add_action( 'wp_loaded', 'langgam_fikir_login_address_route' );
    add_action( 'init', 'langgam_fikir_login_address_block_signup' );
    add_action( 'template_redirect', 'langgam_fikir_login_address_privacy_confirm' );
    add_filter( 'site_url', 'langgam_fikir_login_address_filter_url', 10, 3 );
    add_filter( 'network_site_url', 'langgam_fikir_login_address_filter_url', 10, 3 );
    add_filter( 'wp_redirect', 'langgam_fikir_login_address_filter_redirect', 10, 2 );
    add_filter( 'login_url', 'langgam_fikir_login_address_login_url', 10, 3 );
    add_filter( 'user_request_action_email_content', 'langgam_fikir_login_address_privacy_email', 999, 2 );
    add_filter( 'site_status_tests', 'langgam_fikir_login_address_site_health' );
}
add_action( 'after_setup_theme', 'langgam_fikir_login_address_boot', 1 );

/* =========================================================================
 * Route
 * ====================================================================== */

/**
 * Send people who are not logged in away from wp-admin; show the 404 page for wp-login.php;
 * and show the log in screen at the private address.
 */
function langgam_fikir_login_address_route() {
    global $pagenow;

    $uri     = isset( $_SERVER['REQUEST_URI'] ) ? rawurldecode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- compared only
    $request = wp_parse_url( $uri );
    $path    = isset( $request['path'] ) ? $request['path'] : '';

    /**
     * Fires before the private login address redirects or shows anything.
     */
    do_action( 'langgam_fikir_login_address_before_redirect', $request );

    // Password-protected posts post to wp-login.php?action=postpass: leave that alone.
    // phpcs:disable WordPress.Security.NonceVerification
    $postpass = isset( $_REQUEST['action'], $_POST['post_password'] ) && is_string( $_REQUEST['action'] ) && 'postpass' === wp_unslash( $_REQUEST['action'] )
        && ( ! isset( $_POST['action'] ) || ( is_string( $_POST['action'] ) && 'postpass' === wp_unslash( $_POST['action'] ) ) );
    // phpcs:enable
    if ( $postpass ) {
        return;
    }

    if ( is_admin() && ! is_user_logged_in() && ! wp_doing_ajax() && ! wp_doing_cron() && 'admin-post.php' !== $pagenow && '/wp-admin/options.php' !== $path ) {
        wp_safe_redirect( langgam_fikir_login_address_redirect_url() );
        exit;
    }

    if ( ! is_user_logged_in() && isset( $_GET['wc-ajax'] ) && 'profile.php' === $pagenow ) { // phpcs:ignore WordPress.Security.NonceVerification
        wp_safe_redirect( langgam_fikir_login_address_redirect_url() );
        exit;
    }

    if ( ! is_user_logged_in() && '/wp-admin/options.php' === $path ) {
        header( 'Location: ' . langgam_fikir_login_address_redirect_url() );
        exit;
    }

    $query = isset( $_SERVER['QUERY_STRING'] ) && '' !== $_SERVER['QUERY_STRING'] ? '?' . wp_unslash( $_SERVER['QUERY_STRING'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- passed through to the same site

    if ( 'wp-login.php' === $pagenow && '' !== $path && langgam_fikir_login_address_slashit( $path ) !== $path && get_option( 'permalink_structure' ) ) {
        wp_safe_redirect( langgam_fikir_login_address_slashit( langgam_fikir_login_address_url() ) . $query );
        exit;
    }

    if ( langgam_fikir_login_address_blocked() ) {
        // Multisite: someone coming back from wp-activate.php is sent to the login.
        $referer = wp_get_referer();
        if ( $referer && false !== strpos( $referer, 'wp-activate.php' ) ) {
            $parts = wp_parse_url( $referer );
            if ( ! empty( $parts['query'] ) ) {
                parse_str( $parts['query'], $args );
                if ( ! empty( $args['key'] ) && function_exists( 'wpmu_activate_signup' ) ) {
                    $result = wpmu_activate_signup( $args['key'] );
                    if ( is_wp_error( $result ) && in_array( $result->get_error_code(), array( 'already_active', 'blog_taken' ), true ) ) {
                        wp_safe_redirect( langgam_fikir_login_address_url() . $query );
                        exit;
                    }
                }
            }
        }

        // Show the site's own 404 page.
        $pagenow = 'index.php'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
        if ( ! defined( 'WP_USE_THEMES' ) ) {
            define( 'WP_USE_THEMES', true );
        }
        wp();
        require_once ABSPATH . WPINC . '/template-loader.php';
        exit;
    }

    if ( 'wp-login.php' === $pagenow ) {
        // wp-login.php reads these as globals.
        global $error, $interim_login, $action, $user_login; // phpcs:ignore

        if ( is_user_logged_in() && ! isset( $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
            $requested = isset( $_REQUEST['redirect_to'] ) ? wp_unslash( $_REQUEST['redirect_to'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- checked by wp_safe_redirect
            /**
             * Filters where a logged-in visitor of the login address is sent (default: the dashboard).
             */
            wp_safe_redirect( apply_filters( 'langgam_fikir_login_address_logged_in_redirect', admin_url(), $requested, wp_get_current_user() ) );
            exit;
        }

        require_once ABSPATH . 'wp-login.php';
        exit;
    }
}

/**
 * Without multisite, wp-signup.php and wp-activate.php have nothing to do.
 */
function langgam_fikir_login_address_block_signup() {
    $uri = isset( $_SERVER['REQUEST_URI'] ) ? rawurldecode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- compared only
    /**
     * Filters whether wp-signup.php and wp-activate.php stay reachable on a single site.
     */
    if ( ! is_multisite() && ( false !== strpos( $uri, 'wp-signup' ) || false !== strpos( $uri, 'wp-activate' ) ) && ! apply_filters( 'langgam_fikir_login_address_signup', false ) ) {
        wp_die( esc_html__( 'This feature is not enabled.', 'langgam-fikir' ) );
    }
}

/* =========================================================================
 * Links and emails
 * ====================================================================== */

/**
 * Rewrite a wp-login.php address to the private one, keeping its query.
 */
function langgam_fikir_login_address_rewrite( $url, $scheme = null ) {
    global $pagenow;
    $original = $url;

    if ( false !== strpos( $url, 'wp-login.php?action=postpass' ) ) {
        return $url;
    }
    if ( is_multisite() && 'install.php' === $pagenow ) {
        return $url;
    }

    if ( false !== strpos( $url, 'wp-login.php' ) && false === strpos( (string) wp_get_referer(), 'wp-login.php' ) ) {
        if ( is_ssl() ) {
            $scheme = 'https';
        }
        $parts = explode( '?', $url, 2 );
        if ( isset( $parts[1] ) ) {
            parse_str( $parts[1], $args );
            if ( isset( $args['login'] ) ) {
                $args['login'] = rawurlencode( $args['login'] );
            }
            $url = add_query_arg( $args, langgam_fikir_login_address_url( $scheme ) );
        } else {
            $url = langgam_fikir_login_address_url( $scheme );
        }
    }

    // A visitor posting a password for a protected post keeps the real address.
    if ( isset( $_POST['post_password'] ) && ! is_user_logged_in() ) { // phpcs:ignore WordPress.Security.NonceVerification
        return $original;
    }
    // Gravity Forms pages that need the real address.
    if ( ! is_user_logged_in() && isset( $_GET['gf_page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        return $original;
    }

    return $url;
}

function langgam_fikir_login_address_filter_url( $url, $path = '', $scheme = null ) {
    return langgam_fikir_login_address_rewrite( $url, $scheme );
}

function langgam_fikir_login_address_filter_redirect( $location, $status = 302 ) {
    if ( false !== strpos( $location, 'https://wordpress.com/wp-login.php' ) ) {
        return $location;
    }
    return langgam_fikir_login_address_rewrite( $location );
}

/**
 * A 404 page gets no login link; a re-login from options.php goes to the dashboard.
 */
function langgam_fikir_login_address_login_url( $login_url, $redirect, $force_reauth ) {
    if ( is_404() ) {
        return '#';
    }
    if ( false === $force_reauth || empty( $redirect ) ) {
        return $login_url;
    }
    $parts = explode( '?', $redirect );
    if ( admin_url( 'options.php' ) === $parts[0] ) {
        $login_url = admin_url();
    }
    return $login_url;
}

/**
 * Privacy request emails: the confirm link goes through wp-login.php, which then forwards it
 * (below), so the private address is never mailed out.
 */
function langgam_fikir_login_address_privacy_email( $email_text, $email_data ) {
    $s = langgam_fikir_login_address_settings();
    return str_replace( '###CONFIRM_URL###', esc_url_raw( str_replace( $s['slug'] . '/', 'wp-login.php', $email_data['confirm_url'] ) ), $email_text );
}

function langgam_fikir_login_address_privacy_confirm() {
    // phpcs:disable WordPress.Security.NonceVerification -- the key is checked by wp_validate_user_request_key()
    if ( isset( $_GET['action'], $_GET['request_id'], $_GET['confirm_key'] ) && 'confirmaction' === $_GET['action'] ) {
        $request_id = absint( $_GET['request_id'] );
        $key        = sanitize_text_field( wp_unslash( $_GET['confirm_key'] ) );
        if ( ! is_wp_error( wp_validate_user_request_key( $request_id, $key ) ) ) {
            wp_safe_redirect( add_query_arg( array(
                'action'      => 'confirmaction',
                'request_id'  => $request_id,
                'confirm_key' => $key,
            ), langgam_fikir_login_address_url() ) );
            exit;
        }
    }
    // phpcs:enable
}

/**
 * Site Health's loopback test requests wp-admin while logged out, which now redirects.
 */
function langgam_fikir_login_address_site_health( $tests ) {
    unset( $tests['async']['loopback_requests'] );
    return $tests;
}

/* =========================================================================
 * Admin
 * ====================================================================== */

/**
 * After saving Theme Options: say where the login now is.
 */
function langgam_fikir_login_address_saved_notice() {
    // phpcs:ignore WordPress.Security.NonceVerification -- display only
    if ( empty( $_GET['settings-updated'] ) || ! isset( $_GET['page'] ) || 'langgam-fikir-options' !== $_GET['page'] || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $s = langgam_fikir_login_address_settings();
    if ( ! $s['on'] ) {
        return;
    }
    $url = langgam_fikir_login_address_url();
    printf(
        '<div class="notice notice-success"><p>%s</p></div>',
        sprintf(
            /* translators: %s: login address */
            esc_html__( 'Your login address is %s. Bookmark it: wp-login.php and wp-admin no longer show the login form to visitors.', 'langgam-fikir' ),
            '<strong><a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a></strong>'
        )
    );
}
add_action( 'admin_notices', 'langgam_fikir_login_address_saved_notice' );
