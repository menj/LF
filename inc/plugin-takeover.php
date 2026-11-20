<?php
/**
 * Plugin detection: plugins whose job the theme does itself
 *
 * This file is the only place that knows the names of those plugins. Each entry in
 * langgam_fikir_absorbed_plugins() is a plugin that a theme feature replaces. When the theme
 * finds one of them active it:
 *   1. copies the plugin's settings into Theme Options, once (its `import` callback), so the
 *      site keeps behaving the same;
 *   2. deactivates the plugin, so the feature is not applied twice;
 *   3. converts posts that still use the plugin's block or shortcode names to the theme's own
 *      (`content`), once, so nothing in the content refers to the plugin any more;
 *   4. shows a notice saying what happened and where the settings now live.
 * On the Plugins screen a detected plugin's row says "Built into the Langgam Fikir theme"
 * instead of offering Activate. The plugin's files and its own options are left in place.
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
 * Plugins the theme replaces.
 *
 * @return array<string, array> plugin basename => array(
 *     name     => the plugin's name, shown in the notice,
 *     settings => where its settings now live,
 *     tab      => Theme Options tab,
 *     feature  => the theme feature that replaces it,
 *     running  => callable: true while the plugin's code is loaded (the feature then stands aside),
 *     import   => callable: Theme Options values taken from the plugin's settings,
 *     content  => array( find => text that marks affected posts, rules => regex => replacement )
 *                 converting its block and shortcode names to the theme's,
 * ).
 */
function langgam_fikir_absorbed_plugins() {
    $plugins = array(
        'auto-justify-content/auto-justify-content.php' => array(
            'name'     => 'Auto Justify Content',
            'settings' => __( 'Theme Options > Design > Text Justification', 'langgam-fikir' ),
            'tab'      => 'design',
            'feature'  => 'justify',
            'import'   => 'langgam_fikir_takeover_import_justify',
        ),
        'citepress/citepress.php' => array(
            'name'     => 'CitePress',
            'settings' => __( 'Theme Options > Citations', 'langgam-fikir' ),
            'tab'      => 'citations',
            'feature'  => 'citations',
            'running'  => static function () {
                return defined( 'CITEPRESS_VERSION' ) || function_exists( 'citepress_display_citation' );
            },
            'import'   => 'langgam_fikir_takeover_import_citations',
            'content'  => array(
                'find'  => 'citepress',
                'rules' => array(
                    '#<!--(\s*/?)wp:citepress/block(?=[\s/])#' => '<!--$1wp:langgam-fikir/citation',
                    '#\[(/?)citepress_cite_bibliography(?=[\s\]/])#' => '[$1cite_bibliography',
                    '#\[(/?)citepress_cite(?=[\s\]/])#'              => '[$1cite',
                ),
            ),
        ),
    );

    /**
     * Filters the list of plugins the theme replaces.
     *
     * @param array $plugins plugin basename => array( name, settings, tab, feature, running, import, content ).
     */
    return (array) apply_filters( 'langgam_fikir_absorbed_plugins', $plugins );
}

/**
 * Whether a plugin that a theme feature replaces is still running. The feature registers
 * nothing until the plugin is switched off, so the two never clash.
 *
 * @param string $feature Feature key, e.g. 'citations'.
 */
function langgam_fikir_feature_conflict( $feature ) {
    foreach ( langgam_fikir_absorbed_plugins() as $plugin ) {
        if ( isset( $plugin['feature'] ) && $feature === $plugin['feature']
            && ! empty( $plugin['running'] ) && is_callable( $plugin['running'] ) && call_user_func( $plugin['running'] ) ) {
            return true;
        }
    }
    return false;
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

    if ( current_user_can( 'activate_plugins' ) && ! wp_doing_ajax() ) {
        langgam_fikir_takeover_migrate_content();
    }
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

        $converted = '';
        if ( ! empty( $entry['converted'] ) ) {
            $converted = ' ' . esc_html( sprintf(
                /* translators: %d: number of posts */
                _n( '%d post was updated to use the theme\'s own block and shortcode names.', '%d posts were updated to use the theme\'s own block and shortcode names.', (int) $entry['converted'], 'langgam-fikir' ),
                (int) $entry['converted']
            ) );
        }

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
            ) . $converted;
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

/* =========================================================================
 * Content: convert a detected plugin's block and shortcode names to the theme's
 * ====================================================================== */

/**
 * Rewrite posts (all types and revisions, reusable blocks and templates included) and block
 * and text widgets, once per plugin, after the plugin is no longer active. Writes go straight
 * to the database, so no revisions are created and no save hooks run.
 */
function langgam_fikir_takeover_migrate_content() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $done = (array) get_option( 'langgam_fikir_migrated_content', array() );
    $log  = (array) get_option( 'langgam_fikir_takeover_log', array() );
    $todo = false;

    foreach ( langgam_fikir_absorbed_plugins() as $basename => $plugin ) {
        if ( empty( $plugin['content'] ) || isset( $done[ $basename ] ) ) {
            continue;
        }
        // While the plugin still runs, its content must keep its own names.
        if ( is_plugin_active( $basename ) || ( is_multisite() && is_plugin_active_for_network( $basename ) ) ) {
            continue;
        }
        $count              = langgam_fikir_takeover_rewrite_content( (string) $plugin['content']['find'], (array) $plugin['content']['rules'] );
        $done[ $basename ]  = array( 'time' => time(), 'posts' => $count );
        $todo               = true;
        if ( $count && isset( $log[ $basename ] ) ) {
            $log[ $basename ]['converted'] = $count;
        }
    }

    if ( $todo ) {
        update_option( 'langgam_fikir_migrated_content', $done, false );
        update_option( 'langgam_fikir_takeover_log', $log, false );
    }
}

/**
 * Apply regex => replacement pairs to every post and widget containing $find.
 *
 * @return int Number of posts changed (widgets not counted).
 */
function langgam_fikir_takeover_rewrite_content( $find, array $rules ) {
    global $wpdb;
    if ( '' === $find || ! $rules ) {
        return 0;
    }
    $like = '%' . $wpdb->esc_like( $find ) . '%';

    $changed = 0;
    $last    = 0;
    do {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_content FROM {$wpdb->posts} WHERE ID > %d AND post_content LIKE %s ORDER BY ID ASC LIMIT 200", $last, $like ) );
        foreach ( (array) $rows as $row ) {
            $last = (int) $row->ID;
            $new  = preg_replace( array_keys( $rules ), array_values( $rules ), $row->post_content );
            if ( is_string( $new ) && $new !== $row->post_content ) {
                $wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $last ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                clean_post_cache( $last );
                $changed++;
            }
        }
    } while ( count( (array) $rows ) === 200 );

    foreach ( array( 'widget_block' => 'content', 'widget_text' => 'text' ) as $option => $field ) {
        $widgets = get_option( $option );
        if ( ! is_array( $widgets ) ) {
            continue;
        }
        $dirty = false;
        foreach ( $widgets as $i => $widget ) {
            if ( is_array( $widget ) && isset( $widget[ $field ] ) && is_string( $widget[ $field ] ) && false !== stripos( $widget[ $field ], $find ) ) {
                $new = preg_replace( array_keys( $rules ), array_values( $rules ), $widget[ $field ] );
                if ( is_string( $new ) && $new !== $widget[ $field ] ) {
                    $widgets[ $i ][ $field ] = $new;
                    $dirty = true;
                }
            }
        }
        if ( $dirty ) {
            update_option( $option, $widgets );
        }
    }

    return $changed;
}

/* =========================================================================
 * Settings import: map each plugin's options to Theme Options values
 * ====================================================================== */

/**
 * Auto Justify Content: its ajc_* options.
 *
 * @return array Theme option values to merge.
 */
function langgam_fikir_takeover_import_justify() {
    $map = array(
        'ajc_enabled'  => 'justify_enabled',
        'ajc_hyphen'   => 'justify_hyphens',
        'ajc_mobile'   => 'justify_desktop',
        'ajc_fallback' => 'justify_fallback',
    );
    $values = array();
    foreach ( $map as $from => $to ) {
        $stored = get_option( $from, null );
        if ( null !== $stored ) {
            $values[ $to ] = rest_sanitize_boolean( $stored ) ? 1 : 0;
        }
    }

    $scope = get_option( 'ajc_scope', null );
    if ( in_array( $scope, array( 'blog_only', 'entire_site' ), true ) ) {
        $values['justify_scope'] = $scope;
    }

    $exclude = get_option( 'ajc_exclude', null );
    if ( null !== $exclude ) {
        $values['justify_exclude'] = langgam_fikir_justify_clean_selectors( $exclude );
    }

    return $values;
}

/**
 * CitePress: its citepress_setting option, plus its analytics rows.
 *
 * @return array Theme option values.
 */
function langgam_fikir_takeover_import_citations() {
    $old = get_option( 'citepress_setting', null );
    if ( ! is_array( $old ) ) {
        return array();
    }
    $yes = static function ( $key, $default ) use ( $old ) {
        return isset( $old[ $key ] ) ? ( 'yes' === $old[ $key ] ? 1 : 0 ) : $default;
    };
    $pick = static function ( $key, $allowed, $default ) use ( $old ) {
        return isset( $old[ $key ] ) && in_array( (string) $old[ $key ], $allowed, true ) ? (string) $old[ $key ] : $default;
    };

    $types  = isset( $old['post_types'] ) ? (array) $old['post_types'] : array( 'post' );
    $styles = array_values( array_intersect( isset( $old['enabled_formats'] ) ? (array) $old['enabled_formats'] : array_keys( langgam_fikir_cite_format_names() ), array_keys( langgam_fikir_cite_format_names() ) ) );
    $exp    = array_values( array_intersect( isset( $old['enabled_export_formats'] ) ? (array) $old['enabled_export_formats'] : array_keys( langgam_fikir_cite_export_names() ), array_keys( langgam_fikir_cite_export_names() ) ) );

    $values = array(
        'cite_auto'      => $yes( 'auto_display', 0 ),
        'cite_position'  => $pick( 'display_position', array( 'top', 'bottom' ), 'bottom' ),
        'cite_on_posts'  => in_array( 'post', $types, true ) ? 1 : 0,
        // Book pages now get real book citations, so they are switched on whenever the plugin was on posts.
        'cite_on_books'  => ( in_array( 'book', $types, true ) || in_array( 'post', $types, true ) ) ? 1 : 0,
        'cite_on_pages'  => in_array( 'page', $types, true ) ? 1 : 0,
        'cite_exclude'   => implode( ',', array_filter( array_map( 'absint', explode( ',', isset( $old['excluded_posts'] ) ? (string) $old['excluded_posts'] : '' ) ) ) ),
        'cite_formats'   => implode( ',', $styles ? $styles : array( 'apa' ) ),
        'cite_exports'   => implode( ',', $exp ),
        'cite_picker'    => $pick( 'format_display_mode', array( 'dropdown', 'tabs', 'buttons' ), 'dropdown' ),
        'cite_box'       => $pick( 'box_style', array( 'classic', 'academic', 'minimal' ), 'classic' ),
        'cite_toggle'    => $yes( 'show_toggle', 1 ),
        'cite_et_al'     => isset( $old['et_al_threshold'] ) ? max( 1, min( 999, absint( $old['et_al_threshold'] ) ) ) : 3,
        'cite_scholar'   => $yes( 'show_google_scholar', 1 ),
        'cite_schema'    => $yes( 'show_jsonld', 1 ),
        'cite_analytics' => $yes( 'enable_analytics', 0 ),
        'cite_consent'   => $yes( 'require_consent_for_analytics', 1 ),
        'cite_cooldown'  => $pick( 'analytics_cooldown_mode', array( 'session', 'ip_hash', 'none' ), 'session' ),
        'cite_retention' => $pick( 'analytics_retention_days', array( '0', '30', '90', '180', '365' ), '90' ),
    );

    langgam_fikir_takeover_import_citation_counts();
    return $values;
}

/**
 * CitePress: copy its analytics rows into the theme's table, so deleting the plugin (whose
 * uninstall drops its table) cannot take the history with it.
 *
 * @return int Rows copied.
 */
function langgam_fikir_takeover_import_citation_counts() {
    global $wpdb;
    if ( ! function_exists( 'langgam_fikir_cite_table' ) ) {
        return 0;
    }
    langgam_fikir_cite_install_table();
    $old = preg_replace( '/[^A-Za-z0-9_]/', '', $wpdb->prefix . 'citepress_analytics' );
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $old ) ) !== $old ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        return 0;
    }
    $new = langgam_fikir_cite_table();
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery -- both identifiers constrained to [A-Za-z0-9_]
    return (int) $wpdb->query( "INSERT INTO `{$new}` (post_id, citation_style, action_type, timestamp) SELECT post_id, citation_style, action_type, timestamp FROM `{$old}`" );
}
