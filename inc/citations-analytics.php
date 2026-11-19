<?php
/**
 * Citation analytics
 *
 * Counts citation views, copies and downloads (Theme Options > Citations > Citation Analytics)
 * and shows them under Appearance > Citation Analytics. Port of CitePress's analytics with the
 * same safeguards: off by default, data kept on this site only, optional consent check against
 * common cookie-consent plugins, per-visitor cooldown, retention purge, and the WordPress
 * privacy tools (policy text, exporter, eraser).
 *
 * Its own table, {prefix}lf_citation_analytics, so deleting the CitePress plugin (whose uninstall
 * drops {prefix}citepress_analytics) cannot take the history with it; the plugin's rows are
 * copied over once when the theme takes the plugin over.
 *
 * @package Langgam_Fikir
 * @since 2.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'LANGGAM_FIKIR_CITE_DB_VERSION', '1.0' );

/**
 * The table name.
 */
function langgam_fikir_cite_table() {
    global $wpdb;
    return preg_replace( '/[^A-Za-z0-9_]/', '', $wpdb->prefix . 'lf_citation_analytics' );
}

/**
 * Create (or upgrade) the table when its version changes.
 */
function langgam_fikir_cite_install_table() {
    if ( get_option( 'langgam_fikir_cite_db_version' ) === LANGGAM_FIKIR_CITE_DB_VERSION ) {
        return;
    }
    global $wpdb;
    $table   = langgam_fikir_cite_table();
    $charset = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( "CREATE TABLE {$table} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        post_id bigint(20) unsigned NOT NULL,
        citation_style varchar(50) NOT NULL,
        action_type varchar(20) NOT NULL,
        timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY post_id (post_id),
        KEY citation_style (citation_style),
        KEY action_type (action_type),
        KEY timestamp (timestamp)
    ) {$charset};" );
    update_option( 'langgam_fikir_cite_db_version', LANGGAM_FIKIR_CITE_DB_VERSION, false );
}
add_action( 'admin_init', 'langgam_fikir_cite_install_table' );
add_action( 'after_switch_theme', 'langgam_fikir_cite_install_table' );

/**
 * Copy CitePress's rows into the theme's table (once, from the plugin takeover).
 */
function langgam_fikir_cite_analytics_import() {
    global $wpdb;
    langgam_fikir_cite_install_table();
    $old = preg_replace( '/[^A-Za-z0-9_]/', '', $wpdb->prefix . 'citepress_analytics' );
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $old ) ) !== $old ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        return 0;
    }
    $new = langgam_fikir_cite_table();
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery -- both identifiers constrained to [A-Za-z0-9_]
    return (int) $wpdb->query( "INSERT INTO `{$new}` (post_id, citation_style, action_type, timestamp) SELECT post_id, citation_style, action_type, timestamp FROM `{$old}`" );
}

/* =========================================================================
 * Counting
 * ====================================================================== */

/**
 * Consent from the common consent plugins' cookies. No known cookie means no banner: allowed.
 */
function langgam_fikir_cite_consent_from_cookies() {
    $c = static function ( $name ) {
        return isset( $_COOKIE[ $name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) ) : null;
    };

    if ( null !== ( $v = $c( 'cookieyes-consent' ) ) ) {
        return false !== strpos( $v, 'analytics:yes' );
    }
    if ( null !== ( $v = $c( 'cmplz_statistics' ) ) ) {
        return 'allow' === $v;
    }
    if ( null !== ( $v = $c( 'CookieConsent' ) ) ) {
        return false !== strpos( $v, 'statistics:true' );
    }
    if ( isset( $_COOKIE['moove_gdpr_popup'] ) ) {
        $moove = json_decode( stripslashes( wp_unslash( $_COOKIE['moove_gdpr_popup'] ) ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- decoded and compared only
        return is_array( $moove ) && isset( $moove['thirdparty'] ) && '1' === (string) $moove['thirdparty'];
    }
    if ( null !== ( $v = $c( 'borlabs-cookie' ) ) ) {
        return false !== strpos( $v, '"statistics":true' );
    }
    return true;
}

/**
 * AJAX: record one view, copy or download. No nonce on purpose (as in CitePress): pages are
 * cached, so a nonce would expire for anonymous visitors, and this only adds to counters for
 * published posts with whitelisted values, rate-limited per visitor.
 */
function langgam_fikir_cite_track() {
    // phpcs:disable WordPress.Security.NonceVerification.Missing
    if ( ! langgam_fikir_get_option( 'cite_analytics', 0 ) || apply_filters( 'langgam_fikir_cite_optout', false ) ) {
        wp_send_json_success( array( 'tracked' => false ) );
    }
    if ( langgam_fikir_get_option( 'cite_consent', 1 ) ) {
        $consent = apply_filters( 'langgam_fikir_cite_consent', null );
        if ( null === $consent ) {
            $consent = langgam_fikir_cite_consent_from_cookies();
        }
        if ( ! $consent ) {
            wp_send_json_success( array( 'tracked' => false ) );
        }
    }

    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
    $action  = isset( $_POST['action_type'] ) ? sanitize_key( wp_unslash( $_POST['action_type'] ) ) : '';
    $style   = isset( $_POST['citation_style'] ) ? sanitize_key( wp_unslash( $_POST['citation_style'] ) ) : '';
    // phpcs:enable WordPress.Security.NonceVerification.Missing

    if ( ! $post_id || 'publish' !== get_post_status( $post_id ) ) {
        wp_send_json_error( null, 400 );
    }
    $allowed = 'export' === $action ? array_keys( langgam_fikir_cite_export_names() ) : array_keys( langgam_fikir_cite_format_names() );
    if ( ! in_array( $action, array( 'view', 'copy', 'export' ), true ) || ! in_array( $style, $allowed, true ) ) {
        wp_send_json_error( null, 400 );
    }

    $mode = (string) langgam_fikir_get_option( 'cite_cooldown', 'session' );
    if ( 'none' !== $mode ) {
        $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
        $id = 'ip_hash' === $mode ? $ip : ( wp_get_session_token() ? wp_get_session_token() : $ip . $ua );
        $key = 'lf_cite_' . md5( $action . '|' . $post_id . '|' . $id . '|' . wp_salt( 'nonce' ) );
        if ( get_transient( $key ) ) {
            wp_send_json_success( array( 'tracked' => false ) );
        }
        set_transient( $key, 1, 'view' === $action ? MINUTE_IN_SECONDS : 10 );
    }

    global $wpdb;
    $ok = $wpdb->insert( langgam_fikir_cite_table(), array( 'post_id' => $post_id, 'citation_style' => $style, 'action_type' => $action ), array( '%d', '%s', '%s' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    wp_cache_delete( 'stats', 'langgam_fikir_cite' );
    wp_send_json_success( array( 'tracked' => (bool) $ok ) );
}
add_action( 'wp_ajax_lf_cite_track', 'langgam_fikir_cite_track' );
add_action( 'wp_ajax_nopriv_lf_cite_track', 'langgam_fikir_cite_track' );

/* =========================================================================
 * Retention
 * ====================================================================== */

function langgam_fikir_cite_schedule() {
    if ( ! wp_next_scheduled( 'langgam_fikir_cite_purge' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'langgam_fikir_cite_purge' );
    }
}
add_action( 'admin_init', 'langgam_fikir_cite_schedule' );

function langgam_fikir_cite_unschedule() {
    wp_clear_scheduled_hook( 'langgam_fikir_cite_purge' );
}
add_action( 'switch_theme', 'langgam_fikir_cite_unschedule' );

function langgam_fikir_cite_purge_old() {
    $days = (int) langgam_fikir_get_option( 'cite_retention', '90' );
    if ( $days > 0 ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare( 'DELETE FROM `' . langgam_fikir_cite_table() . '` WHERE timestamp < DATE_SUB(NOW(), INTERVAL %d DAY)', $days ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery
        wp_cache_delete( 'stats', 'langgam_fikir_cite' );
    }
}
add_action( 'langgam_fikir_cite_purge', 'langgam_fikir_cite_purge_old' );

/* =========================================================================
 * Privacy tools
 * ====================================================================== */

function langgam_fikir_cite_privacy_text() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) || ! langgam_fikir_get_option( 'cite_analytics', 0 ) ) {
        return;
    }
    $text = '<p>' . esc_html__( 'When you view, copy or download a citation, the site may count it: which article or book, which citation style or file format, and when. No name, email or account is stored with it.', 'langgam-fikir' ) . '</p>';
    if ( 'ip_hash' === langgam_fikir_get_option( 'cite_cooldown', 'session' ) ) {
        $text .= '<p>' . esc_html__( 'To avoid counting the same visitor twice, a one-way hash of your IP address is kept for up to a minute.', 'langgam-fikir' ) . '</p>';
    }
    if ( langgam_fikir_get_option( 'cite_consent', 1 ) ) {
        $text .= '<p>' . esc_html__( 'Nothing is counted unless you accept statistics cookies.', 'langgam-fikir' ) . '</p>';
    }
    $days = (int) langgam_fikir_get_option( 'cite_retention', '90' );
    /* translators: %d: number of days */
    $text .= '<p>' . ( $days ? esc_html( sprintf( __( 'Counts are deleted after %d days.', 'langgam-fikir' ), $days ) ) : esc_html__( 'Counts are kept until an administrator deletes them.', 'langgam-fikir' ) ) . '</p>';
    wp_add_privacy_policy_content( __( 'Citations', 'langgam-fikir' ), wp_kses_post( $text ) );
}
add_action( 'admin_init', 'langgam_fikir_cite_privacy_text' );

/**
 * The counts hold no personal data, so export and erase have nothing to return; registering
 * them still lets the privacy tools report that clearly. Sites that link counts to people can
 * map an email to post IDs with the langgam_fikir_cite_identity filter (as CitePress allowed).
 */
function langgam_fikir_cite_privacy_exporter( $exporters ) {
    $exporters['langgam-fikir-citations'] = array(
        'exporter_friendly_name' => __( 'Citation analytics', 'langgam-fikir' ),
        'callback'               => static function ( $email ) {
            $ids  = apply_filters( 'langgam_fikir_cite_identity', array(), $email );
            $data = array();
            if ( $ids ) {
                global $wpdb;
                $in   = implode( ',', array_map( 'absint', (array) $ids ) );
                $rows = $wpdb->get_results( 'SELECT * FROM `' . langgam_fikir_cite_table() . '` WHERE post_id IN (' . $in . ') LIMIT 500' ); // phpcs:ignore WordPress.DB
                foreach ( $rows as $row ) {
                    $data[] = array(
                        'group_id'    => 'langgam-fikir-citations',
                        'group_label' => __( 'Citation analytics', 'langgam-fikir' ),
                        'item_id'     => 'lf-cite-' . $row->id,
                        'data'        => array(
                            array( 'name' => __( 'Post', 'langgam-fikir' ), 'value' => (int) $row->post_id ),
                            array( 'name' => __( 'Style or format', 'langgam-fikir' ), 'value' => $row->citation_style ),
                            array( 'name' => __( 'Action', 'langgam-fikir' ), 'value' => $row->action_type ),
                            array( 'name' => __( 'Time', 'langgam-fikir' ), 'value' => $row->timestamp ),
                        ),
                    );
                }
            }
            return array( 'data' => $data, 'done' => true );
        },
    );
    return $exporters;
}
add_filter( 'wp_privacy_personal_data_exporters', 'langgam_fikir_cite_privacy_exporter' );

function langgam_fikir_cite_privacy_eraser( $erasers ) {
    $erasers['langgam-fikir-citations'] = array(
        'eraser_friendly_name' => __( 'Citation analytics', 'langgam-fikir' ),
        'callback'             => static function ( $email ) {
            $ids     = apply_filters( 'langgam_fikir_cite_identity', array(), $email );
            $removed = 0;
            if ( $ids ) {
                global $wpdb;
                $in      = implode( ',', array_map( 'absint', (array) $ids ) );
                $removed = (int) $wpdb->query( 'DELETE FROM `' . langgam_fikir_cite_table() . '` WHERE post_id IN (' . $in . ')' ); // phpcs:ignore WordPress.DB
            }
            return array(
                'items_removed'  => $removed,
                'items_retained' => false,
                'messages'       => $removed ? array() : array( __( 'Citation analytics store no personal data.', 'langgam-fikir' ) ),
                'done'           => true,
            );
        },
    );
    return $erasers;
}
add_filter( 'wp_privacy_personal_data_erasers', 'langgam_fikir_cite_privacy_eraser' );

/* =========================================================================
 * Appearance > Citation Analytics
 * ====================================================================== */

function langgam_fikir_cite_admin_menu() {
    add_theme_page(
        __( 'Citation Analytics', 'langgam-fikir' ),
        __( 'Citation Analytics', 'langgam-fikir' ),
        'edit_theme_options',
        'langgam-fikir-citations',
        'langgam_fikir_cite_admin_page'
    );
}
add_action( 'admin_menu', 'langgam_fikir_cite_admin_menu' );

function langgam_fikir_cite_admin_assets( $hook ) {
    if ( 'appearance_page_langgam-fikir-citations' !== $hook ) {
        return;
    }
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    wp_enqueue_style( 'langgam-fikir-theme-options', $uri . '/assets/css/theme-options.css', array(), LANGGAM_FIKIR_VERSION );
    wp_enqueue_style( 'langgam-fikir-citations-admin', $uri . '/assets/css/citations-admin.css', array( 'langgam-fikir-theme-options' ), filemtime( $dir . '/assets/css/citations-admin.css' ) );
    wp_enqueue_script( 'langgam-fikir-citations-admin', $uri . '/assets/js/citations-admin.js', array(), filemtime( $dir . '/assets/js/citations-admin.js' ), true );
}
add_action( 'admin_enqueue_scripts', 'langgam_fikir_cite_admin_assets' );

/**
 * Delete every count (form on the analytics page).
 */
function langgam_fikir_cite_handle_purge() {
    if ( ! isset( $_POST['lf_cite_purge'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked below
        return;
    }
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_die( esc_html__( 'You are not allowed to do that.', 'langgam-fikir' ) );
    }
    check_admin_referer( 'lf_cite_purge' );
    global $wpdb;
    $wpdb->query( 'TRUNCATE TABLE `' . langgam_fikir_cite_table() . '`' ); // phpcs:ignore WordPress.DB
    wp_cache_delete( 'stats', 'langgam_fikir_cite' );
    wp_safe_redirect( add_query_arg( array( 'page' => 'langgam-fikir-citations', 'purged' => 1 ), admin_url( 'themes.php' ) ) );
    exit;
}
add_action( 'admin_init', 'langgam_fikir_cite_handle_purge' );

/**
 * Totals and top lists, cached for five minutes.
 */
function langgam_fikir_cite_stats() {
    $stats = wp_cache_get( 'stats', 'langgam_fikir_cite' );
    if ( false !== $stats ) {
        return $stats;
    }
    global $wpdb;
    $t = langgam_fikir_cite_table();
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery -- $t constrained to [A-Za-z0-9_]
    $totals = $wpdb->get_results( "SELECT action_type, COUNT(*) AS n FROM `{$t}` GROUP BY action_type", OBJECT_K );
    $stats  = array(
        'views'   => isset( $totals['view'] ) ? (int) $totals['view']->n : 0,
        'copies'  => isset( $totals['copy'] ) ? (int) $totals['copy']->n : 0,
        'exports' => isset( $totals['export'] ) ? (int) $totals['export']->n : 0,
        'styles'  => $wpdb->get_results( "SELECT citation_style AS k, COUNT(*) AS n FROM `{$t}` WHERE action_type IN ('view','copy') GROUP BY citation_style ORDER BY n DESC LIMIT 20" ),
        'posts'   => $wpdb->get_results( "SELECT post_id AS k, SUM(action_type='view') AS v, SUM(action_type='copy') AS c, SUM(action_type='export') AS e, COUNT(*) AS n FROM `{$t}` GROUP BY post_id ORDER BY n DESC LIMIT 15" ),
        'formats' => $wpdb->get_results( "SELECT citation_style AS k, COUNT(*) AS n FROM `{$t}` WHERE action_type = 'export' GROUP BY citation_style ORDER BY n DESC" ),
    );
    // phpcs:enable
    wp_cache_set( 'stats', $stats, 'langgam_fikir_cite', 5 * MINUTE_IN_SECONDS );
    return $stats;
}

function langgam_fikir_cite_admin_page() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }
    langgam_fikir_cite_install_table();
    $stats    = langgam_fikir_cite_stats();
    $names    = langgam_fikir_cite_format_names();
    $exports  = langgam_fikir_cite_export_names();
    $on       = (bool) langgam_fikir_get_option( 'cite_analytics', 0 );
    $settings = admin_url( 'themes.php?page=langgam-fikir-options&tab=citations' );
    $total    = max( 1, $stats['views'] + $stats['copies'] );
    ?>
    <div class="wrap lf-options-wrap lf-cite-admin">
        <h1 class="lf-options-title">
            <span class="dashicons dashicons-format-quote lf-options-icon" aria-hidden="true"></span>
            <?php esc_html_e( 'Citation Analytics', 'langgam-fikir' ); ?>
        </h1>

        <?php if ( isset( $_GET['purged'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'All citation counts were deleted.', 'langgam-fikir' ); ?></p></div>
        <?php endif; ?>

        <?php if ( ! $on ) : ?>
            <div class="lf-card lf-cite-admin__off">
                <p>
                    <?php
                    printf(
                        /* translators: %s: link to the settings */
                        esc_html__( 'Counting is off, so nothing new is recorded. Switch it on in %s.', 'langgam-fikir' ),
                        '<a href="' . esc_url( $settings ) . '">' . esc_html__( 'Theme Options > Citations', 'langgam-fikir' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="lf-cite-admin__totals">
            <?php
            foreach ( array(
                'views'   => __( 'Citations shown', 'langgam-fikir' ),
                'copies'  => __( 'Citations copied', 'langgam-fikir' ),
                'exports' => __( 'Files downloaded', 'langgam-fikir' ),
            ) as $key => $label ) :
                ?>
                <div class="lf-card lf-cite-admin__total">
                    <span class="lf-cite-admin__number"><?php echo esc_html( number_format_i18n( $stats[ $key ] ) ); ?></span>
                    <span class="lf-cite-admin__label"><?php echo esc_html( $label ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="lf-cite-admin__grid">
            <div class="lf-card">
                <h2 class="lf-card-title"><?php esc_html_e( 'Most cited', 'langgam-fikir' ); ?></h2>
                <?php if ( $stats['posts'] ) : ?>
                    <table class="widefat striped lf-cite-admin__table">
                        <thead><tr><th scope="col"><?php esc_html_e( 'Article or book', 'langgam-fikir' ); ?></th><th scope="col" class="num"><?php esc_html_e( 'Shown', 'langgam-fikir' ); ?></th><th scope="col" class="num"><?php esc_html_e( 'Copied', 'langgam-fikir' ); ?></th><th scope="col" class="num"><?php esc_html_e( 'Downloaded', 'langgam-fikir' ); ?></th></tr></thead>
                        <tbody>
                            <?php foreach ( $stats['posts'] as $row ) : ?>
                                <tr>
                                    <td><?php $title = get_the_title( (int) $row->k ); ?><a href="<?php echo esc_url( get_edit_post_link( (int) $row->k ) ); ?>"><?php echo esc_html( $title ? $title : '#' . (int) $row->k ); ?></a> <span class="lf-cite-admin__type"><?php echo esc_html( get_post_type( (int) $row->k ) === 'book' ? __( 'Book', 'langgam-fikir' ) : '' ); ?></span></td>
                                    <td class="num"><?php echo esc_html( number_format_i18n( (int) $row->v ) ); ?></td>
                                    <td class="num"><?php echo esc_html( number_format_i18n( (int) $row->c ) ); ?></td>
                                    <td class="num"><?php echo esc_html( number_format_i18n( (int) $row->e ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p class="lf-card-desc"><?php esc_html_e( 'No citations counted yet.', 'langgam-fikir' ); ?></p>
                <?php endif; ?>
            </div>

            <div class="lf-card">
                <h2 class="lf-card-title"><?php esc_html_e( 'Styles readers choose', 'langgam-fikir' ); ?></h2>
                <?php if ( $stats['styles'] ) : ?>
                    <ul class="lf-cite-admin__bars">
                        <?php foreach ( $stats['styles'] as $row ) : ?>
                            <?php $pct = round( 100 * (int) $row->n / $total ); ?>
                            <li>
                                <span class="lf-cite-admin__bar-label"><?php echo esc_html( isset( $names[ $row->k ] ) ? $names[ $row->k ] : $row->k ); ?></span>
                                <span class="lf-cite-admin__bar" aria-hidden="true"><span data-pct="<?php echo (int) $pct; ?>"></span></span>
                                <span class="lf-cite-admin__bar-value"><?php echo esc_html( number_format_i18n( (int) $row->n ) ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="lf-card-desc"><?php esc_html_e( 'No styles counted yet.', 'langgam-fikir' ); ?></p>
                <?php endif; ?>

                <h2 class="lf-card-title lf-cite-admin__sub"><?php esc_html_e( 'Downloads by format', 'langgam-fikir' ); ?></h2>
                <?php if ( $stats['formats'] ) : ?>
                    <ul class="lf-cite-admin__list">
                        <?php foreach ( $stats['formats'] as $row ) : ?>
                            <li><span><?php echo esc_html( isset( $exports[ $row->k ] ) ? $exports[ $row->k ] : $row->k ); ?></span> <strong><?php echo esc_html( number_format_i18n( (int) $row->n ) ); ?></strong></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="lf-card-desc"><?php esc_html_e( 'No downloads yet.', 'langgam-fikir' ); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="lf-card">
            <h2 class="lf-card-title"><?php esc_html_e( 'Your data', 'langgam-fikir' ); ?></h2>
            <p class="lf-card-desc">
                <?php
                $days = (int) langgam_fikir_get_option( 'cite_retention', '90' );
                echo esc_html( $days
                    /* translators: %d: number of days */
                    ? sprintf( __( 'Counts are kept on this site only and deleted automatically after %d days. No names, emails or accounts are stored.', 'langgam-fikir' ), $days )
                    : __( 'Counts are kept on this site only, until you delete them. No names, emails or accounts are stored.', 'langgam-fikir' ) );
                ?>
                <a href="<?php echo esc_url( $settings ); ?>"><?php esc_html_e( 'Change the settings', 'langgam-fikir' ); ?></a>
            </p>
            <form method="post" class="lf-cite-admin__purge" data-confirm="<?php esc_attr_e( 'Delete every citation count? This cannot be undone.', 'langgam-fikir' ); ?>">
                <?php wp_nonce_field( 'lf_cite_purge' ); ?>
                <button type="submit" name="lf_cite_purge" value="1" class="button button-secondary"><?php esc_html_e( 'Delete all counts', 'langgam-fikir' ); ?></button>
            </form>
        </div>
    </div>
    <?php
}
