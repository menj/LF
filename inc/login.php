<?php
/**
 * Branded login screen
 *
 * Restyles wp-login.php (log in, lost password, reset password, register) in the theme's own
 * look: the active palette, custom colours, corner style and font roles from Theme Options >
 * Design, the site logo, and a photo panel beside the form. Settings live in
 * Theme Options > Login Page.
 *
 * Styling is in assets/css/login.css. It reads the same CSS custom properties as the front end,
 * so a palette change applies here too. No JavaScript is needed.
 *
 * @package Langgam_Fikir
 * @since 2.13.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Whether the branded login screen is switched on.
 */
function langgam_fikir_login_enabled() {
    return (bool) langgam_fikir_get_option( 'login_custom', 1 );
}

/**
 * The chosen layout: split, photo or minimal.
 */
function langgam_fikir_login_layout() {
    $layout = (string) langgam_fikir_get_option( 'login_layout', 'split' );
    return in_array( $layout, array( 'split', 'photo', 'minimal' ), true ) ? $layout : 'split';
}

/**
 * URL of the logo shown above the form: the Custom Logo, then the Site Icon, else ''.
 */
function langgam_fikir_login_logo_url() {
    $logo_id = (int) get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $url = wp_get_attachment_image_url( $logo_id, 'medium' );
        if ( $url ) {
            return $url;
        }
    }
    return (string) get_site_icon_url( 256 );
}

/**
 * URL of the login photo, or '' for none.
 *
 * "hero" follows the homepage: the uploaded Hero Background Image, else the chosen hero photo.
 */
function langgam_fikir_login_photo_url() {
    $choice = (string) langgam_fikir_get_option( 'login_photo', 'hero' );
    if ( 'none' === $choice || 'minimal' === langgam_fikir_login_layout() ) {
        return '';
    }

    if ( 'hero' === $choice ) {
        $uploaded = (int) langgam_fikir_get_option( 'hero_background_image' );
        if ( $uploaded ) {
            $url = wp_get_attachment_image_url( $uploaded, 'large' );
            if ( $url ) {
                return $url;
            }
        }
        // The homepage was set to "No photo (solid colour)": match it.
        if ( 'none' === (string) langgam_fikir_get_option( 'hero_photo' ) ) {
            return '';
        }
        $photo = langgam_fikir_hero_photo_urls();
        if ( $photo ) {
            return $photo['full'];
        }
        $choice = 'library-corridor';
    }

    $photos = langgam_fikir_hero_photos();
    if ( 'random' === $choice ) {
        $choice = array_rand( $photos );
    }
    if ( ! isset( $photos[ $choice ] ) || ! file_exists( LANGGAM_FIKIR_DIR . '/assets/images/hero/' . $choice . '-1920.jpg' ) ) {
        return '';
    }
    return LANGGAM_FIKIR_URI . '/assets/images/hero/' . $choice . '-1920.jpg';
}

/**
 * Styles: fonts, the design layer (palettes, corners, font roles) and login.css.
 */
function langgam_fikir_login_assets() {
    if ( ! langgam_fikir_login_enabled() ) {
        return;
    }

    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();

    wp_enqueue_style( 'langgam-fikir-fonts', $uri . '/assets/css/fonts.css', array(), filemtime( $dir . '/assets/css/fonts.css' ) );
    $deps = array( 'login', 'langgam-fikir-fonts' );
    if ( file_exists( $dir . '/assets/fonts/sabon-next-lt-regular.woff2' ) ) {
        wp_enqueue_style( 'langgam-fikir-fonts-premium', $uri . '/assets/css/fonts-premium.css', array( 'langgam-fikir-fonts' ), filemtime( $dir . '/assets/css/fonts-premium.css' ) );
        $deps[] = 'langgam-fikir-fonts-premium';
    }

    wp_enqueue_style( 'langgam-fikir-login', $uri . '/assets/css/login.css', $deps, filemtime( $dir . '/assets/css/login.css' ) );
    // Palettes, corners and font roles come from the front-end design layer, so they never drift apart.
    wp_enqueue_style( 'langgam-fikir-design', $uri . '/assets/css/design.css', array( 'langgam-fikir-login' ), filemtime( $dir . '/assets/css/design.css' ) );

    // Per-site values as custom properties (logo, photo) plus any individually picked colours.
    $vars = array();
    $logo = langgam_fikir_login_logo_url();
    if ( $logo ) {
        $vars[] = '--lf-login-logo:url("' . esc_url( $logo ) . '")';
    }
    $photo = langgam_fikir_login_photo_url();
    if ( $photo ) {
        $vars[] = '--lf-login-photo:url("' . esc_url( $photo ) . '")';
    }
    $css = $vars ? 'body.login{' . implode( ';', $vars ) . '}' : '';
    $css .= langgam_fikir_custom_colour_css();
    if ( $css ) {
        wp_add_inline_style( 'langgam-fikir-design', $css );
    }
}
add_action( 'login_enqueue_scripts', 'langgam_fikir_login_assets' );

/**
 * Body classes: the same design classes as the front end, plus the layout and logo state.
 */
function langgam_fikir_login_body_class( $classes ) {
    if ( ! langgam_fikir_login_enabled() ) {
        return $classes;
    }

    $classes   = langgam_fikir_design_body_classes( $classes );
    $classes[] = 'lf-login';
    $classes[] = 'lf-login--' . langgam_fikir_login_layout();
    $classes[] = langgam_fikir_login_logo_url() ? 'lf-login-has-logo' : 'lf-login-no-logo';
    if ( langgam_fikir_login_photo_url() ) {
        $classes[] = 'lf-login-has-photo';
    }
    return $classes;
}
add_filter( 'login_body_class', 'langgam_fikir_login_body_class' );

/**
 * The logo links to the site, not wordpress.org, and is named after it.
 */
function langgam_fikir_login_header_url( $url ) {
    return langgam_fikir_login_enabled() ? home_url( '/' ) : $url;
}
add_filter( 'login_headerurl', 'langgam_fikir_login_header_url' );

function langgam_fikir_login_header_text( $text ) {
    return langgam_fikir_login_enabled() ? get_bloginfo( 'name', 'display' ) : $text;
}
add_filter( 'login_headertext', 'langgam_fikir_login_header_text' );

/**
 * Browser tab: "Log In ‹ Langgam Fikir" without the trailing "— WordPress".
 */
function langgam_fikir_login_title( $login_title, $title ) {
    if ( ! langgam_fikir_login_enabled() ) {
        return $login_title;
    }
    return sprintf( '%1$s &lsaquo; %2$s', $title, get_bloginfo( 'name', 'display' ) );
}
add_filter( 'login_title', 'langgam_fikir_login_title', 10, 2 );

/**
 * The brand panel: photo, site name, tagline and caption. Printed right after <body>.
 */
function langgam_fikir_login_brand_panel() {
    global $interim_login;
    if ( ! langgam_fikir_login_enabled() || ! empty( $interim_login ) || 'minimal' === langgam_fikir_login_layout() ) {
        return;
    }

    $name    = get_bloginfo( 'name', 'display' );
    $tagline = get_bloginfo( 'description', 'display' );
    $caption = langgam_fikir_text( 'login_caption' );
    $entity  = (string) langgam_fikir_get_option( 'legal_entity_name', $name );
    ?>
    <aside class="lf-login-brand" aria-hidden="true">
        <div class="lf-login-brand__inner">
            <span class="lf-login-brand__rule"></span>
            <p class="lf-login-brand__name"><?php echo esc_html( $name ); ?></p>
            <?php if ( $tagline ) : ?>
                <p class="lf-login-brand__tagline"><?php echo esc_html( $tagline ); ?></p>
            <?php endif; ?>
            <?php if ( $caption ) : ?>
                <p class="lf-login-brand__caption"><?php echo esc_html( $caption ); ?></p>
            <?php endif; ?>
        </div>
        <p class="lf-login-brand__foot">&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . ( $entity ? $entity : $name ) ); ?></p>
    </aside>
    <?php
}
add_action( 'login_header', 'langgam_fikir_login_brand_panel' );

/**
 * A heading and a short line above the form, worded for each screen.
 */
function langgam_fikir_login_intro( $message ) {
    global $action, $interim_login;
    if ( ! langgam_fikir_login_enabled() || ! empty( $interim_login ) ) {
        return $message;
    }

    $screen = $action ? (string) $action : 'login';
    switch ( $screen ) {
        case 'login':
            $heading = langgam_fikir_text( 'login_heading' );
            $text    = langgam_fikir_text( 'login_text' );
            break;
        case 'lostpassword':
        case 'retrievepassword':
            // WordPress prints its own instructions below, so only a heading is added here.
            $heading = __( 'Forgot your password?', 'langgam-fikir' );
            $text    = '';
            break;
        case 'rp':
        case 'resetpass':
            $heading = __( 'Choose a new password', 'langgam-fikir' );
            $text    = '';
            break;
        case 'register':
            $heading = __( 'Create an account', 'langgam-fikir' );
            $text    = '';
            break;
        default:
            return $message;
    }

    if ( '' === $heading && '' === $text ) {
        return $message;
    }

    $intro = '<div class="lf-login-intro">';
    if ( '' !== $heading ) {
        $intro .= '<h2 class="lf-login-intro__heading">' . esc_html( $heading ) . '</h2>';
    }
    if ( '' !== $text ) {
        $intro .= '<p class="lf-login-intro__text">' . esc_html( $text ) . '</p>';
    }
    $intro .= '</div>';

    return $intro . $message;
}
add_filter( 'login_message', 'langgam_fikir_login_intro' );
