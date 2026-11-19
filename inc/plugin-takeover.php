<?php
/**
 * Plugin takeover: plugins whose job the theme now does itself
 *
 * Each entry in langgam_fikir_absorbed_plugins() is a plugin that the theme replaces with a
 * built-in module. When the theme finds one of them active it:
 *   1. copies the plugin's settings into Theme Options, once (its `import` callback), so the
 *      site keeps behaving the same;
 *   2. deactivates the plugin, so the feature is not applied twice;
 *   3. shows a notice saying what happened and where the settings now live.
 * On the Plugins screen an absorbed plugin's row says "Built into the Langgam Fikir theme"
 * instead of offering Activate. The plugin's files and its own options are left in place, so
 * switching to another theme and re-activating the plugin restores it exactly.
 *
 * Note: themes listed on WordPress.org may not deactivate plugins. This is for this site.
 *
 * @package Langgam_Fikir
 * @since 2.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugins the theme has absorbed.
 *
 * @return array<string, array{name:string, settings:string, import:callable|null}>
 *         plugin basename => name shown to people, where its settings now live, import callback.
 */
function langgam_fikir_absorbed_plugins() {
    $plugins = array(
        'auto-justify-content/auto-justify-content.php' => array(
            'name'     => 'Auto Justify Content',
            'settings' => __( 'Theme Options > Design > Text Justification', 'langgam-fikir' ),
            'tab'      => 'design',
            'import'   => 'langgam_fikir_justify_import_plugin_settings',
        ),
    );

    /**
     * Filters the list of plugins the theme replaces.
     *
     * @param array $plugins plugin basename => array( name, settings, tab, import ).
     */
    return (array) apply_filters( 'langgam_fikir_absorbed_plugins', $plugins );
}

/**
 * Copy one plugin's settings into Theme Options, once per plugin.
 */
function langgam_fikir_takeover_import( $basename, $plugin ) {
    $done = (array) get_option( 'langgam_fikir_imported_plugins', array() );
    if ( isset( $done[ $basename ] ) ) {
        return false;
    }

    $values = array();
    if ( ! empty( $plugin['import'] ) && is_callable( $plugin['import'] ) ) {
        $values = (array) call_user_func( $plugin['import'] );
    }

    if ( $values ) {
        // Start from every current value (defaults filled in), so the Theme Options sanitiser,
        // which treats a missing checkbox as unticked, keeps everything else exactly as it is.
        $options = wp_parse_args( (array) get_option( 'langgam_fikir_options', array() ), langgam_fikir_option_defaults() );
        update_option( 'langgam_fikir_options', array_merge( $options, $values ) );
    }

    $done[ $basename ] = time();
    update_option( 'langgam_fikir_imported_plugins', $done, false );
    return (bool) $values;
}

/**
 * Find absorbed plugins that are active, import their settings and deactivate them.
 * Runs in the admin (on every admin page load) and right after the theme is activated.
 */
function langgam_fikir_takeover_run() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $log = (array) get_option( 'langgam_fikir_takeover_log', array() );

    foreach ( langgam_fikir_absorbed_plugins() as $basename => $plugin ) {
        $network = is_multisite() && is_plugin_active_for_network( $basename );
        if ( ! $network && ! is_plugin_active( $basename ) ) {
            continue;
        }

        $imported = langgam_fikir_takeover_import( $basename, $plugin );

        // Network-activated plugins can only be switched off by a network admin.
        if ( $network && ! current_user_can( 'manage_network_plugins' ) ) {
            $log[ $basename ] = array( 'time' => time(), 'status' => 'network', 'imported' => $imported );
            continue;
        }
        if ( ! $network && ! current_user_can( 'activate_plugins' ) ) {
            continue;
        }

        deactivate_plugins( $basename, false, $network );
        $log[ $basename ] = array( 'time' => time(), 'status' => 'deactivated', 'imported' => $imported );
    }

    update_option( 'langgam_fikir_takeover_log', $log, false );
}
add_action( 'admin_init', 'langgam_fikir_takeover_run', 1 );
add_action( 'after_switch_theme', 'langgam_fikir_takeover_run' );

/**
 * An absorbed plugin activated while this theme is active is switched straight off again.
 */
function langgam_fikir_takeover_on_activate( $basename ) {
    if ( isset( langgam_fikir_absorbed_plugins()[ $basename ] ) ) {
        langgam_fikir_takeover_run();
    }
}
add_action( 'activated_plugin', 'langgam_fikir_takeover_on_activate' );

/**
 * Notice: which plugins were switched off, and where their settings went.
 */
function langgam_fikir_takeover_notice() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }
    $log     = (array) get_option( 'langgam_fikir_takeover_log', array() );
    $plugins = langgam_fikir_absorbed_plugins();
    $items   = array();

    foreach ( $log as $basename => $entry ) {
        if ( empty( $entry['status'] ) || ! empty( $entry['dismissed'] ) || ! isset( $plugins[ $basename ] ) ) {
            continue;
        }
        $plugin = $plugins[ $basename ];
        $link   = admin_url( 'themes.php?page=langgam-fikir-options&tab=' . rawurlencode( isset( $plugin['tab'] ) ? $plugin['tab'] : '' ) );

        if ( 'network' === $entry['status'] ) {
            $items[] = sprintf(
                /* translators: 1: plugin name, 2: settings location */
                esc_html__( '%1$s is network-activated, so only a network administrator can switch it off. The theme already does its job; its settings are in %2$s.', 'langgam-fikir' ),
                '<strong>' . esc_html( $plugin['name'] ) . '</strong>',
                '<a href="' . esc_url( $link ) . '">' . esc_html( $plugin['settings'] ) . '</a>'
            );
        } else {
            $items[] = sprintf(
                /* translators: 1: plugin name, 2: settings location */
                esc_html__( '%1$s was deactivated because the Langgam Fikir theme now does its job. Your settings were copied to %2$s. You can delete the plugin.', 'langgam-fikir' ),
                '<strong>' . esc_html( $plugin['name'] ) . '</strong>',
                '<a href="' . esc_url( $link ) . '">' . esc_html( $plugin['settings'] ) . '</a>'
            );
        }
    }

    if ( ! $items ) {
        return;
    }

    $dismiss = wp_nonce_url( add_query_arg( 'lf_dismiss_takeover', '1' ), 'lf_dismiss_takeover' );
    echo '<div class="notice notice-info lf-takeover-notice"><p>' . implode( '</p><p>', $items ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
    printf( '<p><a href="%s">%s</a></p></div>', esc_url( $dismiss ), esc_html__( 'Dismiss', 'langgam-fikir' ) );
}
add_action( 'admin_notices', 'langgam_fikir_takeover_notice' );
add_action( 'network_admin_notices', 'langgam_fikir_takeover_notice' );

/**
 * Dismiss the notice (until another plugin is taken over).
 */
function langgam_fikir_takeover_dismiss() {
    if ( empty( $_GET['lf_dismiss_takeover'] ) || ! current_user_can( 'activate_plugins' ) ) {
        return;
    }
    check_admin_referer( 'lf_dismiss_takeover' );

    $log = (array) get_option( 'langgam_fikir_takeover_log', array() );
    foreach ( $log as $basename => $entry ) {
        $log[ $basename ]['dismissed'] = 1;
    }
    update_option( 'langgam_fikir_takeover_log', $log, false );

    wp_safe_redirect( remove_query_arg( array( 'lf_dismiss_takeover', '_wpnonce' ) ) );
    exit;
}
add_action( 'admin_init', 'langgam_fikir_takeover_dismiss', 5 );

/**
 * Plugins screen: an absorbed plugin offers no Activate link, and says why.
 */
function langgam_fikir_takeover_action_links( $actions, $basename ) {
    $plugins = langgam_fikir_absorbed_plugins();
    if ( ! isset( $plugins[ $basename ] ) ) {
        return $actions;
    }
    unset( $actions['activate'] );
    $tab   = isset( $plugins[ $basename ]['tab'] ) ? $plugins[ $basename ]['tab'] : '';
    $label = sprintf(
        '<span class="lf-absorbed">%s</span> <a href="%s">%s</a>',
        esc_html__( 'Built into the Langgam Fikir theme.', 'langgam-fikir' ),
        esc_url( admin_url( 'themes.php?page=langgam-fikir-options&tab=' . rawurlencode( $tab ) ) ),
        esc_html__( 'Settings', 'langgam-fikir' )
    );
    return array( 'lf-absorbed' => $label ) + $actions;
}
add_filter( 'plugin_action_links', 'langgam_fikir_takeover_action_links', 20, 2 );
add_filter( 'network_admin_plugin_action_links', 'langgam_fikir_takeover_action_links', 20, 2 );
