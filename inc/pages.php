<?php
/**
 * Special pages: About, Contact, Privacy Policy and Terms.
 *
 * Which page plays which role is chosen in Theme Settings > Footer and pages. Pages whose
 * slug matches the old hard-coded slugs keep working, so existing sites are unaffected.
 *
 * @package Langgam_Fikir
 * @since 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Role => settings key, legacy slugs, template file and settings tab.
 */
function langgam_fikir_special_pages() {
    return array(
        'about'   => array( 'option' => 'about_page_id', 'slugs' => array( 'about-langgam-fikir', 'about-us', 'about' ), 'template' => 'page-about.php', 'tab' => 'about' ),
        'contact' => array( 'option' => 'contact_page_id', 'slugs' => array( 'contact-langgam-fikir', 'contact-us', 'contact' ), 'template' => 'page-contact.php', 'tab' => 'contact' ),
        'privacy' => array( 'option' => '', 'slugs' => array( 'privacy-policy', 'privacy' ), 'template' => 'page-privacy.php', 'tab' => '' ),
        'terms'   => array( 'option' => 'terms_page_id', 'slugs' => array( 'terms-of-service' ), 'template' => '', 'tab' => '' ),
    );
}

/**
 * ID of the published page that plays a role, or 0.
 */
function langgam_fikir_get_special_page_id( $role ) {
    static $cache = array();
    if ( isset( $cache[ $role ] ) ) {
        return $cache[ $role ];
    }

    $pages = langgam_fikir_special_pages();
    if ( ! isset( $pages[ $role ] ) ) {
        return 0;
    }
    $def = $pages[ $role ];
    $id  = 0;

    if ( $def['option'] ) {
        $chosen = (int) langgam_fikir_get_option( $def['option'] );
        if ( $chosen && 'publish' === get_post_status( $chosen ) ) {
            $id = $chosen;
        }
    } elseif ( 'privacy' === $role ) {
        $chosen = (int) get_option( 'wp_page_for_privacy_policy' );
        if ( $chosen && 'publish' === get_post_status( $chosen ) ) {
            $id = $chosen;
        }
    }

    if ( ! $id ) {
        foreach ( $def['slugs'] as $slug ) {
            $page = get_page_by_path( $slug );
            if ( $page && 'publish' === $page->post_status ) {
                $id = $page->ID;
                break;
            }
        }
    }

    $cache[ $role ] = $id;
    return $id;
}

/**
 * Permalink of a special page, or '' when none exists.
 */
function langgam_fikir_get_page_url( $role ) {
    $id = langgam_fikir_get_special_page_id( $role );
    return $id ? get_permalink( $id ) : '';
}

/**
 * Which role (about, contact, privacy, terms) a page plays, or ''.
 */
function langgam_fikir_get_page_role( $post_id ) {
    foreach ( array_keys( langgam_fikir_special_pages() ) as $role ) {
        if ( (int) $post_id === langgam_fikir_get_special_page_id( $role ) ) {
            return $role;
        }
    }
    return '';
}

/**
 * Use the About, Contact and Privacy layouts for the pages assigned to those roles.
 */
function langgam_fikir_special_page_template( $template ) {
    if ( ! is_page() ) {
        return $template;
    }

    $role  = langgam_fikir_get_page_role( get_queried_object_id() );
    $pages = langgam_fikir_special_pages();

    if ( $role && ! empty( $pages[ $role ]['template'] ) ) {
        $located = locate_template( $pages[ $role ]['template'] );
        if ( $located ) {
            return $located;
        }
    }

    return $template;
}
add_filter( 'template_include', 'langgam_fikir_special_page_template' );

/**
 * Tell editors where the text of a layout page lives. The page editor only controls the
 * body content, so a visible notice beats a collapsed meta box.
 */
function langgam_fikir_page_editor_notice() {
    $screen = get_current_screen();
    if ( ! $screen || 'post' !== $screen->base || 'page' !== $screen->post_type || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
    $role    = $post_id ? langgam_fikir_get_page_role( $post_id ) : '';
    $pages   = langgam_fikir_special_pages();

    if ( ! $role || empty( $pages[ $role ]['tab'] ) ) {
        return;
    }

    $labels = array(
        'about'   => __( 'About page', 'langgam-fikir' ),
        'contact' => __( 'Contact page', 'langgam-fikir' ),
    );
    $url = admin_url( 'themes.php?page=' . LANGGAM_FIKIR_PAGE . '#' . $pages[ $role ]['tab'] );

    printf(
        '<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
        esc_html( sprintf(
            /* translators: %s: page role, e.g. "About page" */
            __( 'This is your %s. Its layout text (headings, cards, contact details and so on) is edited in Theme Settings; what you type below appears as the page body.', 'langgam-fikir' ),
            $labels[ $role ]
        ) ),
        esc_url( $url ),
        esc_html__( 'Open Theme Settings', 'langgam-fikir' )
    );
}
add_action( 'admin_notices', 'langgam_fikir_page_editor_notice' );

/**
 * Same hint inside the block editor, where classic admin notices are hidden in fullscreen mode.
 */
function langgam_fikir_enqueue_editor_panel() {
    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
    $screen  = get_current_screen();
    if ( ! $post_id || ! $screen || 'page' !== $screen->post_type || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $role  = langgam_fikir_get_page_role( $post_id );
    $pages = langgam_fikir_special_pages();
    if ( ! $role || empty( $pages[ $role ]['tab'] ) ) {
        return;
    }

    $handle = 'langgam-fikir-editor-panel';
    wp_enqueue_script(
        $handle,
        get_stylesheet_directory_uri() . '/assets/js/editor-panel.js',
        array( 'wp-plugins', 'wp-element', 'wp-editor', 'wp-edit-post' ),
        filemtime( get_stylesheet_directory() . '/assets/js/editor-panel.js' ),
        true
    );
    wp_localize_script(
        $handle,
        'langgamFikirEditor',
        array(
            'title'   => __( 'Theme content', 'langgam-fikir' ),
            'message' => __( 'The headings, cards, contact details and other text on this page are edited in Theme Settings. What you type in the editor appears as the page body.', 'langgam-fikir' ),
            'button'  => __( 'Open Theme Settings', 'langgam-fikir' ),
            'url'     => admin_url( 'themes.php?page=' . LANGGAM_FIKIR_PAGE . '#' . $pages[ $role ]['tab'] ),
        )
    );
}
add_action( 'enqueue_block_editor_assets', 'langgam_fikir_enqueue_editor_panel' );

/**
 * Primary menu fallback for sites that have not built a menu yet: Publications, Resources,
 * About and Contact (only the ones that exist). Replaced as soon as a menu is assigned.
 */
function lf_primary_menu_fallback() {
    $items = array();

    if ( get_post_type_object( 'book' ) ) {
        $items[ __( 'Publications', 'langgam-fikir' ) ] = get_post_type_archive_link( 'book' );
    }
    $blog_id = (int) get_option( 'page_for_posts' );
    if ( $blog_id ) {
        $items[ __( 'Resources', 'langgam-fikir' ) ] = get_permalink( $blog_id );
    }
    $items[ __( 'About', 'langgam-fikir' ) ]   = langgam_fikir_get_page_url( 'about' );
    $items[ __( 'Contact', 'langgam-fikir' ) ] = langgam_fikir_get_page_url( 'contact' );

    echo '<ul id="primary-menu" class="menu">';
    foreach ( $items as $label => $url ) {
        if ( $url ) {
            echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
        }
    }
    echo '</ul>';
}

/**
 * Footer Legal menu fallback: Privacy Policy and Terms of Service, when those pages exist.
 */
function lf_footer_legal_fallback() {
    $items = array(
        esc_html__( 'Privacy Policy', 'langgam-fikir' )   => langgam_fikir_get_page_url( 'privacy' ),
        esc_html__( 'Terms of Service', 'langgam-fikir' ) => langgam_fikir_get_page_url( 'terms' ),
    );

    echo '<ul id="footer-legal-menu" class="menu">';
    foreach ( $items as $label => $url ) {
        if ( $url ) {
            echo '<li><a href="' . esc_url( $url ) . '">' . $label . '</a></li>';
        }
    }
    echo '</ul>';
}
