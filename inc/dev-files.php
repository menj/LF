<?php
/**
 * Development files clean-up
 *
 * The theme is uploaded by FTP from a working copy, which puts the repository (.git,
 * .gitignore and other hidden files) and tools/ (build scripts, self-tests) on the live server,
 * where the web server may serve or run them. This file removes them.
 *
 * What goes: every hidden file and folder at the top of the theme folder except .htaccess,
 * .user.ini and .well-known, and the tools/ and tests/ folders. Read-only files are made
 * writable first. A symlink is removed itself and never followed. data/, the code and the
 * rest of the theme stay.
 *
 * When: on the first request of any kind after the theme version changes, and again every hour
 * through WP-Cron (an upload can put .git back at any time). Hosts that set DISABLE_WP_CRON
 * must call wp-cron.php from a server cron at least hourly.
 *
 * Only one request may clean at a time. The right to clean is a lease held in the option
 * lf_devfiles_lease, whose value is "token|time":
 *  - add_option() takes a free lease and fails while the option exists;
 *  - the running clean-up renews it, at most once a minute, with a compare-and-set UPDATE;
 *  - a lease not renewed for five minutes belongs to a dead request and is taken over with one
 *    compare-and-set UPDATE that only one request can win;
 *  - a lease is released with one DELETE that matches only this request's own value.
 *
 * The version is recorded (lf_devfiles_version) only after the clean-up has returned and
 * nothing is left. When something cannot be removed (permissions, an unreadable folder) the
 * version is not recorded and the lease is kept, so the retry comes once the lease is five
 * minutes old and never on every request; one line a day is logged saying how many files are
 * left and that they should be deleted by FTP.
 *
 * Kept as they are: local and development sites (wp_get_environment_type()), and any site that
 * defines LANGGAM_FIKIR_KEEP_DOTFILES as true.
 *
 * Until the first clean-up has run, the theme's .htaccess refuses the hidden files and the
 * tools, tests, bin, docs and server folders.
 *
 * @package Langgam_Fikir
 * @since 2.17.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** The lease: "token|time", autoload off. */
define( 'LANGGAM_FIKIR_DEVFILES_LEASE', 'lf_devfiles_lease' );
/** The theme version for which the clean-up has finished. */
define( 'LANGGAM_FIKIR_DEVFILES_VERSION', 'lf_devfiles_version' );
/** The day (Y-m-d, UTC) a failure was last logged. */
define( 'LANGGAM_FIKIR_DEVFILES_LOGGED', 'lf_devfiles_logged' );
/** The hourly WP-Cron event. */
define( 'LANGGAM_FIKIR_DEVFILES_EVENT', 'langgam_fikir_devfiles_hourly' );
/** Seconds without a renewal after which a lease belongs to a dead request. */
define( 'LANGGAM_FIKIR_DEVFILES_STALE', 5 * MINUTE_IN_SECONDS );
/** Seconds between renewals by the running clean-up. */
define( 'LANGGAM_FIKIR_DEVFILES_RENEW', MINUTE_IN_SECONDS );

/* =========================================================================
 * Rules
 * ====================================================================== */

/**
 * Whether this site keeps its development files: a local or development site, or one that
 * defines LANGGAM_FIKIR_KEEP_DOTFILES as true.
 *
 * @return bool
 */
function langgam_fikir_devfiles_keep() {
    if ( defined( 'LANGGAM_FIKIR_KEEP_DOTFILES' ) && LANGGAM_FIKIR_KEEP_DOTFILES ) {
        return true;
    }
    return in_array( wp_get_environment_type(), array( 'local', 'development' ), true );
}

/**
 * The current time. A filter so a test can move the clock.
 *
 * @return int
 */
function langgam_fikir_devfiles_now() {
    return (int) apply_filters( 'langgam_fikir_devfiles_now', time() );
}

/**
 * The development entries at the top of a theme folder: hidden files and folders (not
 * .htaccess, .user.ini or .well-known) and the tools and tests folders. Symlinks count.
 *
 * @param string $dir The theme folder.
 * @return string[] Entry names.
 */
function langgam_fikir_devfiles_list( $dir ) {
    $found = array();
    $names = @scandir( $dir ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
    if ( ! is_array( $names ) ) {
        return $found;
    }
    $allowed = array( '.htaccess', '.user.ini', '.well-known' );
    foreach ( $names as $name ) {
        if ( '.' === $name || '..' === $name ) {
            continue;
        }
        if ( '.' === $name[0] ) {
            if ( ! in_array( $name, $allowed, true ) ) {
                $found[] = $name;
            }
            continue;
        }
        if ( in_array( $name, array( 'tools', 'tests' ), true ) ) {
            $path = $dir . '/' . $name;
            if ( is_link( $path ) || is_dir( $path ) ) {
                $found[] = $name;
            }
        }
    }
    return $found;
}

/* =========================================================================
 * Lease
 *
 * A lease is an array held by one request: 'value' (what this request last wrote) and
 * 'renewed' (when). All writes are single statements on the options table, and the option
 * cache is dropped after each, so no request trusts a stale cached copy.
 * ====================================================================== */

/**
 * Forget the cached copies of the lease.
 */
function langgam_fikir_devfiles_lease_uncache() {
    wp_cache_delete( LANGGAM_FIKIR_DEVFILES_LEASE, 'options' );
    wp_cache_delete( 'notoptions', 'options' );
}

/**
 * Read an option straight from the database, never from the cache.
 *
 * @param string $name Option name.
 * @return string|null Null when there is no such option.
 */
function langgam_fikir_devfiles_db_get( $name ) {
    global $wpdb;
    return $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $name ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * A new lease value for this request.
 *
 * @param int $now Time.
 * @return string
 */
function langgam_fikir_devfiles_lease_value( $now ) {
    return wp_generate_uuid4() . '|' . $now;
}

/**
 * Seconds since a lease value was last written. A value that cannot be read counts as ancient.
 *
 * @param string $value Lease value.
 * @param int    $now   Time.
 * @return int
 */
function langgam_fikir_devfiles_lease_age( $value, $now ) {
    $parts = explode( '|', (string) $value );
    if ( 2 !== count( $parts ) || ! preg_match( '/^\d+$/', $parts[1] ) ) {
        return PHP_INT_MAX;
    }
    return $now - (int) $parts[1];
}

/**
 * Change the lease from one value to another, if it still holds the first. One UPDATE: of
 * several requests holding the same old value, exactly one changes a row.
 *
 * @param string $from Value the lease must hold now.
 * @param string $to   New value.
 * @return bool Whether this request changed it.
 */
function langgam_fikir_devfiles_lease_swap( $from, $to ) {
    global $wpdb;
    $rows = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s", $to, LANGGAM_FIKIR_DEVFILES_LEASE, $from ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    langgam_fikir_devfiles_lease_uncache();
    return 1 === (int) $rows;
}

/**
 * Take over a lease that has gone stale.
 *
 * @param array  $lease This request's lease, filled in when it wins.
 * @param string $stale The stale value this request read.
 * @return bool
 */
function langgam_fikir_devfiles_lease_take_over( array &$lease, $stale ) {
    $now   = langgam_fikir_devfiles_now();
    $value = langgam_fikir_devfiles_lease_value( $now );
    if ( ! langgam_fikir_devfiles_lease_swap( $stale, $value ) ) {
        return false;
    }
    $lease = array( 'value' => $value, 'renewed' => $now );
    return true;
}

/**
 * Take the lease: a free one with add_option(), a stale one by compare-and-set.
 *
 * @param array $lease This request's lease, filled in when it is taken.
 * @return bool
 */
function langgam_fikir_devfiles_lease_acquire( array &$lease ) {
    $now   = langgam_fikir_devfiles_now();
    $value = langgam_fikir_devfiles_lease_value( $now );

    if ( add_option( LANGGAM_FIKIR_DEVFILES_LEASE, $value, '', 'no' ) ) {
        // add_option() is an upsert in core: make sure the row is really ours.
        langgam_fikir_devfiles_lease_uncache();
        if ( langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_LEASE ) !== $value ) {
            return false;
        }
        $lease = array( 'value' => $value, 'renewed' => $now );
        return true;
    }

    $current = langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_LEASE );
    if ( null === $current || langgam_fikir_devfiles_lease_age( $current, $now ) < LANGGAM_FIKIR_DEVFILES_STALE ) {
        return false; // Another request is cleaning, or has only just let go.
    }
    return langgam_fikir_devfiles_lease_take_over( $lease, $current );
}

/**
 * Renew the lease, at most once a minute unless forced.
 *
 * @param array $lease This request's lease.
 * @param bool  $force Renew now whatever the time.
 * @return bool False when the lease is no longer this request's.
 */
function langgam_fikir_devfiles_lease_renew( array &$lease, $force = false ) {
    $now = langgam_fikir_devfiles_now();
    if ( ! $force && $now - $lease['renewed'] < LANGGAM_FIKIR_DEVFILES_RENEW ) {
        return true;
    }
    $value = langgam_fikir_devfiles_lease_value( $now );
    if ( ! langgam_fikir_devfiles_lease_swap( $lease['value'], $value ) ) {
        return false;
    }
    $lease = array( 'value' => $value, 'renewed' => $now );
    return true;
}

/**
 * Let the lease go: one DELETE that matches only this request's own value.
 *
 * @param array $lease This request's lease.
 */
function langgam_fikir_devfiles_lease_release( array $lease ) {
    global $wpdb;
    if ( empty( $lease['value'] ) ) {
        return;
    }
    $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", LANGGAM_FIKIR_DEVFILES_LEASE, $lease['value'] ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    langgam_fikir_devfiles_lease_uncache();
}

/* =========================================================================
 * Removal
 * ====================================================================== */

/**
 * Remove one file, folder or symlink and everything under it.
 *
 * $ctx keeps the lease ('lease'), the count of things that could not be removed ('failed')
 * and whether the lease was lost ('lost'). The lease is renewed along the way.
 *
 * @param string $path File, folder or symlink.
 * @param array  $ctx  Shared state.
 */
function langgam_fikir_devfiles_remove( $path, array &$ctx ) {
    if ( $ctx['lost'] ) {
        return;
    }
    if ( ! langgam_fikir_devfiles_lease_renew( $ctx['lease'] ) ) {
        $ctx['lost'] = true;
        return;
    }

    // A symlink is removed itself and never followed. (Windows removes one to a folder with rmdir.)
    if ( is_link( $path ) ) {
        if ( ! @unlink( $path ) && ! @rmdir( $path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
            ++$ctx['failed'];
        }
        return;
    }

    if ( is_dir( $path ) ) {
        if ( ! is_readable( $path ) || ! is_writable( $path ) || ! is_executable( $path ) ) {
            @chmod( $path, 0755 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
        }
        $names = @scandir( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
        if ( ! is_array( $names ) ) {
            ++$ctx['failed']; // An unreadable folder.
            return;
        }
        $before = $ctx['failed'];
        foreach ( $names as $name ) {
            if ( '.' !== $name && '..' !== $name ) {
                langgam_fikir_devfiles_remove( $path . '/' . $name, $ctx );
            }
        }
        if ( $ctx['lost'] ) {
            return;
        }
        if ( $before === $ctx['failed'] && ! @rmdir( $path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
            ++$ctx['failed'];
        }
        return;
    }

    // Git keeps its objects read-only.
    if ( ! is_writable( $path ) ) {
        @chmod( $path, 0644 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
    }
    if ( ! @unlink( $path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
        ++$ctx['failed'];
    }
}

/**
 * Remove every development entry in the theme folder.
 *
 * @param string $dir   The theme folder.
 * @param array  $lease This request's lease (renewed while this runs).
 * @return array{failed:int, lost:bool} Entries that could not be removed, and whether the lease was lost.
 */
function langgam_fikir_devfiles_clean( $dir, array &$lease ) {
    $ctx = array( 'lease' => &$lease, 'failed' => 0, 'lost' => false );
    foreach ( langgam_fikir_devfiles_list( $dir ) as $name ) {
        langgam_fikir_devfiles_remove( $dir . '/' . $name, $ctx );
    }
    return array( 'failed' => $ctx['failed'], 'lost' => $ctx['lost'] );
}

/**
 * Log, at most once a day, that files are left that only FTP can delete.
 *
 * @param int $count Files left.
 */
function langgam_fikir_devfiles_log_failure( $count ) {
    $today = gmdate( 'Y-m-d', langgam_fikir_devfiles_now() );
    if ( get_option( LANGGAM_FIKIR_DEVFILES_LOGGED ) === $today ) {
        return;
    }
    update_option( LANGGAM_FIKIR_DEVFILES_LOGGED, $today, false );
    error_log( sprintf( 'Langgam Fikir: %d development file(s) in the theme folder could not be removed (permissions or an unreadable folder). Delete the hidden files (.git, .gitignore and the like) and the tools and tests folders by FTP.', max( 1, (int) $count ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
}

/* =========================================================================
 * Running
 * ====================================================================== */

/**
 * Clean the theme folder if the clean-up has not finished for this theme version.
 *
 * @param string $dir The theme folder.
 * @return string 'kept' (this site keeps its files), 'current' (nothing to do), 'busy' (another
 *                request has the lease), 'lost' (the lease was lost part-way), 'failed' (something
 *                is left) or 'done'.
 */
function langgam_fikir_devfiles_run( $dir ) {
    if ( langgam_fikir_devfiles_keep() ) {
        return 'kept';
    }
    if ( LANGGAM_FIKIR_VERSION === get_option( LANGGAM_FIKIR_DEVFILES_VERSION ) ) {
        return 'current';
    }

    $lease = array( 'value' => '', 'renewed' => 0 );
    if ( ! langgam_fikir_devfiles_lease_acquire( $lease ) ) {
        return 'busy';
    }

    // Another request may have finished while this one waited: ask the database, not the cache.
    if ( langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_VERSION ) === LANGGAM_FIKIR_VERSION ) {
        wp_cache_delete( LANGGAM_FIKIR_DEVFILES_VERSION, 'options' );
        wp_cache_delete( 'alloptions', 'options' );
        langgam_fikir_devfiles_lease_release( $lease );
        return 'current';
    }

    ignore_user_abort( true );
    @set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, Squiz.PHP.DiscouragedFunctions

    $result = langgam_fikir_devfiles_clean( $dir, $lease );
    if ( $result['lost'] ) {
        return 'lost'; // The lease is another request's now; leave it alone.
    }

    $left = langgam_fikir_devfiles_list( $dir );
    if ( 0 === $result['failed'] && ! $left ) {
        update_option( LANGGAM_FIKIR_DEVFILES_VERSION, LANGGAM_FIKIR_VERSION, true );
        langgam_fikir_devfiles_lease_release( $lease );
        return 'done';
    }

    // Keep the lease, renewed now: the next try is five minutes from now, not on every request.
    langgam_fikir_devfiles_lease_renew( $lease, true );
    langgam_fikir_devfiles_log_failure( $result['failed'] ? $result['failed'] : count( $left ) );
    return 'failed';
}

/**
 * Every request: keep the hourly event in step with the site, then clean if the theme version
 * has changed.
 */
function langgam_fikir_devfiles_init() {
    if ( langgam_fikir_devfiles_keep() ) {
        if ( wp_next_scheduled( LANGGAM_FIKIR_DEVFILES_EVENT ) ) {
            wp_clear_scheduled_hook( LANGGAM_FIKIR_DEVFILES_EVENT );
        }
        return;
    }
    if ( ! wp_next_scheduled( LANGGAM_FIKIR_DEVFILES_EVENT ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'hourly', LANGGAM_FIKIR_DEVFILES_EVENT );
    }
    langgam_fikir_devfiles_run( LANGGAM_FIKIR_DIR );
}
add_action( 'init', 'langgam_fikir_devfiles_init', 1 );

/**
 * Hourly: an upload can put .git back at any time. Does nothing when the folder has no
 * development entries; otherwise forgets the recorded version and cleans under the same lease.
 *
 * @param string|null $dir The theme folder (default: this theme's).
 * @return string What langgam_fikir_devfiles_run() returned, or 'clean' when there was nothing to do.
 */
function langgam_fikir_devfiles_hourly( $dir = null ) {
    $dir = is_string( $dir ) ? $dir : LANGGAM_FIKIR_DIR;
    if ( langgam_fikir_devfiles_keep() ) {
        return 'kept';
    }
    if ( ! langgam_fikir_devfiles_list( $dir ) ) {
        return 'clean';
    }
    delete_option( LANGGAM_FIKIR_DEVFILES_VERSION );
    return langgam_fikir_devfiles_run( $dir );
}
add_action( LANGGAM_FIKIR_DEVFILES_EVENT, 'langgam_fikir_devfiles_hourly' );

/**
 * Switching away from the theme: stop the hourly event.
 */
function langgam_fikir_devfiles_unschedule() {
    wp_clear_scheduled_hook( LANGGAM_FIKIR_DEVFILES_EVENT );
}
add_action( 'switch_theme', 'langgam_fikir_devfiles_unschedule' );
