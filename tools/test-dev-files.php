<?php
/**
 * Self-test for inc/dev-files.php (the development files clean-up).
 *
 * Run from a shell:  php tools/test-dev-files.php
 *
 * It needs no WordPress. It builds throw-away theme folders in the system temp folder, and
 * stands in for the few WordPress functions the clean-up uses: an options table on an
 * in-memory SQLite database (real UPDATE / DELETE statements with the same WHERE clauses as
 * on MySQL), an option cache, hooks and WP-Cron. Each "request" is one call into the clean-up
 * with its own lease, so races are played out by reading first and writing after.
 *
 * The permission failure runs as an unprivileged user (needs root and setpriv; skipped
 * otherwise), because root can delete anything.
 *
 * This folder is removed from live sites by the clean-up itself.
 *
 * @package Langgam_Fikir
 * @since 2.17.0
 */

if ( 'cli' !== PHP_SAPI ) {
    exit;
}

define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'DAY_IN_SECONDS', 86400 );

$theme_root = dirname( __DIR__ );
preg_match( "/define\( 'LANGGAM_FIKIR_VERSION', '([^']+)'/", file_get_contents( $theme_root . '/functions.php' ), $m );
define( 'LANGGAM_FIKIR_VERSION', $m[1] );

$base = rtrim( sys_get_temp_dir(), '/' ) . '/lf-devfiles-test-' . getmypid();
define( 'LANGGAM_FIKIR_DIR', $base . '/live' );

/* ---------------------------------------------------------------------------
 * Stand-ins for WordPress
 * ------------------------------------------------------------------------ */

class LF_Test_DB {
    public $options = 'wp_options';
    public $log     = array();

    public function prepare( $query, ...$args ) {
        $i = 0;
        return preg_replace_callback(
            '/%[sd]/',
            function ( $mt ) use ( &$i, $args ) {
                $a = $args[ $i++ ];
                return '%d' === $mt[0] ? (string) (int) $a : $GLOBALS['lf_pdo']->quote( (string) $a );
            },
            $query
        );
    }

    public function get_var( $query ) {
        $r = $GLOBALS['lf_pdo']->query( $query )->fetchColumn();
        return false === $r ? null : $r;
    }

    public function query( $query ) {
        $this->log[] = $query;
        return $GLOBALS['lf_pdo']->exec( $query );
    }
}

function lf_reset_site( $env = 'production' ) {
    $GLOBALS['lf_pdo'] = new PDO( 'sqlite::memory:' );
    $GLOBALS['lf_pdo']->exec( 'CREATE TABLE wp_options (option_name TEXT PRIMARY KEY, option_value TEXT, autoload TEXT)' );
    $GLOBALS['lf_cache']  = array();
    $GLOBALS['lf_cron']   = array();
    $GLOBALS['lf_writes'] = 0;
    $GLOBALS['lf_env']    = $env;
    $GLOBALS['lf_clock']  = null;
    $GLOBALS['lf_on_now'] = null;
    $GLOBALS['wpdb']      = new LF_Test_DB();
}

$GLOBALS['lf_hooks'] = array();
function add_filter( $tag, $fn, $priority = 10, $args = 1 ) {
    $GLOBALS['lf_hooks'][ $tag ][] = $fn;
    return true;
}
function add_action( $tag, $fn, $priority = 10, $args = 1 ) {
    return add_filter( $tag, $fn, $priority, $args );
}
function apply_filters( $tag, $value ) {
    if ( empty( $GLOBALS['lf_hooks'][ $tag ] ) ) {
        return $value;
    }
    foreach ( $GLOBALS['lf_hooks'][ $tag ] as $fn ) {
        $value = $fn( $value );
    }
    return $value;
}
function do_action( $tag ) {
    foreach ( isset( $GLOBALS['lf_hooks'][ $tag ] ) ? $GLOBALS['lf_hooks'][ $tag ] : array() as $fn ) {
        $fn();
    }
}

// The test clock: when set, every look at the time moves it on one second.
add_filter(
    'langgam_fikir_devfiles_now',
    function ( $t ) {
        if ( null === $GLOBALS['lf_clock'] ) {
            return $t;
        }
        if ( $GLOBALS['lf_on_now'] ) {
            call_user_func( $GLOBALS['lf_on_now'], $GLOBALS['lf_clock'] );
        }
        return $GLOBALS['lf_clock']++;
    }
);

function wp_get_environment_type() {
    return $GLOBALS['lf_env'];
}
function wp_generate_uuid4() {
    return bin2hex( random_bytes( 16 ) );
}
function get_option( $name, $default = false ) {
    if ( ! array_key_exists( $name, $GLOBALS['lf_cache'] ) ) {
        $v                           = $GLOBALS['wpdb']->get_var( $GLOBALS['wpdb']->prepare( 'SELECT option_value FROM wp_options WHERE option_name = %s', $name ) );
        $GLOBALS['lf_cache'][ $name ] = null === $v ? false : $v;
    }
    return false === $GLOBALS['lf_cache'][ $name ] ? $default : $GLOBALS['lf_cache'][ $name ];
}
function lf_write_option( $name, $value ) {
    $GLOBALS['lf_writes']++;
    $GLOBALS['lf_pdo']->exec( 'INSERT OR REPLACE INTO wp_options VALUES (' . $GLOBALS['lf_pdo']->quote( $name ) . ',' . $GLOBALS['lf_pdo']->quote( (string) $value ) . ",'no')" );
    $GLOBALS['lf_cache'][ $name ] = (string) $value;
}
// Like core, add_option() is an upsert behind a "does it exist" check.
function add_option( $name, $value, $deprecated = '', $autoload = 'yes' ) {
    if ( false !== get_option( $name ) ) {
        return false;
    }
    lf_write_option( $name, $value );
    return true;
}
function update_option( $name, $value, $autoload = null ) {
    lf_write_option( $name, $value );
    return true;
}
function delete_option( $name ) {
    $GLOBALS['lf_writes']++;
    $GLOBALS['lf_pdo']->exec( 'DELETE FROM wp_options WHERE option_name = ' . $GLOBALS['lf_pdo']->quote( $name ) );
    $GLOBALS['lf_cache'][ $name ] = false;
    return true;
}
function wp_cache_delete( $key, $group = '' ) {
    unset( $GLOBALS['lf_cache'][ $key ] );
    return true;
}
function wp_next_scheduled( $hook ) {
    return isset( $GLOBALS['lf_cron'][ $hook ] ) ? $GLOBALS['lf_cron'][ $hook ] : false;
}
function wp_schedule_event( $time, $recurrence, $hook ) {
    $GLOBALS['lf_cron'][ $hook ] = $time;
    return true;
}
function wp_clear_scheduled_hook( $hook ) {
    unset( $GLOBALS['lf_cron'][ $hook ] );
}

require $theme_root . '/inc/dev-files.php';

/* ---------------------------------------------------------------------------
 * Helpers
 * ------------------------------------------------------------------------ */

$GLOBALS['lf_pass'] = 0;
$GLOBALS['lf_fail'] = 0;

function ok( $label, $condition, $detail = '' ) {
    if ( $condition ) {
        $GLOBALS['lf_pass']++;
        echo "  PASS  $label\n";
    } else {
        $GLOBALS['lf_fail']++;
        echo "  FAIL  $label" . ( '' !== $detail ? "  ($detail)" : '' ) . "\n";
    }
}

function lf_rm( $path ) {
    if ( is_link( $path ) || is_file( $path ) ) {
        @chmod( $path, 0644 );
        @unlink( $path );
        return;
    }
    if ( is_dir( $path ) ) {
        @chmod( $path, 0755 );
        foreach ( scandir( $path ) as $n ) {
            if ( '.' !== $n && '..' !== $n ) {
                lf_rm( $path . '/' . $n );
            }
        }
        @rmdir( $path );
    }
}

function lf_put( $path, $body = 'x', $mode = 0644 ) {
    @mkdir( dirname( $path ), 0755, true );
    file_put_contents( $path, $body );
    chmod( $path, $mode );
}

/** A theme folder as it arrives by FTP from a working copy. */
function lf_build_theme( $dir ) {
    lf_rm( $dir );
    mkdir( $dir, 0755, true );
    lf_put( "$dir/style.css" );
    lf_put( "$dir/functions.php" );
    lf_put( "$dir/inc/dev-files.php" );
    lf_put( "$dir/data/books.json", '{}' );
    lf_put( "$dir/bin/keep.sh" );
    lf_put( "$dir/docs/keep.md" );
    lf_put( "$dir/tools.php" );
    lf_put( "$dir/.htaccess", 'RedirectMatch 403 x' );
    lf_put( "$dir/.user.ini", 'x=1' );
    lf_put( "$dir/.well-known/acme", 'x' );
    lf_put( "$dir/.gitignore" );
    lf_put( "$dir/.DS_Store" );
    lf_put( "$dir/.github/workflows/ci.yml" );
    lf_put( "$dir/.git/HEAD", 'ref: refs/heads/main' );
    lf_put( "$dir/.git/objects/ab/cdef0123", 'obj', 0444 ); // git keeps objects read-only
    chmod( "$dir/.git/objects/ab", 0555 );
    lf_put( "$dir/tools/build.sh" );
    lf_put( "$dir/tools/test-dev-files.php" );
    lf_put( "$dir/tests/a.php" );
}

function lf_lease() {
    return langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_LEASE );
}

function lf_new_lease() {
    return array( 'value' => '', 'renewed' => 0 );
}

/* ---------------------------------------------------------------------------
 * The checks
 * ------------------------------------------------------------------------ */

function lf_run_main( $base ) {
    $dir = "$base/theme";

    echo "\nWhich entries are listed\n";
    lf_build_theme( $dir );
    $listed = langgam_fikir_devfiles_list( $dir );
    sort( $listed );
    ok( 'lists hidden entries, tools and tests', array( '.DS_Store', '.git', '.github', '.gitignore', 'tests', 'tools' ) === $listed, implode( ' ', $listed ) );
    ok( 'does not list .htaccess, .user.ini, .well-known, bin, docs, tools.php, code or data', ! array_intersect( array( '.htaccess', '.user.ini', '.well-known', 'bin', 'docs', 'tools.php', 'inc', 'data', 'style.css' ), $listed ) );

    echo "\nSites that keep their files\n";
    foreach ( array( 'local', 'development' ) as $env ) {
        lf_reset_site( $env );
        lf_build_theme( $dir );
        $r = langgam_fikir_devfiles_run( $dir );
        ok( "$env site: nothing is removed", 'kept' === $r && is_dir( "$dir/.git" ) && is_dir( "$dir/tools" ), $r );
        ok( "$env site: no version, no lease", false === get_option( LANGGAM_FIKIR_DEVFILES_VERSION ) && null === lf_lease() );
        ok( "$env site: the hourly callback does nothing", 'kept' === langgam_fikir_devfiles_hourly( $dir ) && is_dir( "$dir/.git" ) );
    }
    lf_reset_site( 'staging' );
    lf_build_theme( $dir );
    ok( 'staging site: cleaned (only local and development keep files)', 'done' === langgam_fikir_devfiles_run( $dir ) && ! is_dir( "$dir/.git" ) );

    echo "\nA fresh lease held by another request\n";
    lf_reset_site();
    lf_build_theme( $dir );
    $other = 'other-request|' . time();
    add_option( LANGGAM_FIKIR_DEVFILES_LEASE, $other, '', 'no' );
    $r = langgam_fikir_devfiles_run( $dir );
    ok( 'keeps this request out', 'busy' === $r, $r );
    ok( 'removes nothing', is_dir( "$dir/.git" ) && is_dir( "$dir/tools" ) );
    ok( 'leaves the other request\'s lease as it was', lf_lease() === $other );
    ok( 'does not record the version', false === get_option( LANGGAM_FIKIR_DEVFILES_VERSION ) );
    $almost = 'other-request|' . ( time() - LANGGAM_FIKIR_DEVFILES_STALE + 30 );
    lf_write_option( LANGGAM_FIKIR_DEVFILES_LEASE, $almost );
    ok( 'a lease 4m30s old is still respected', 'busy' === langgam_fikir_devfiles_run( $dir ) && lf_lease() === $almost );

    echo "\nTwo requests that read the same stale lease\n";
    lf_reset_site();
    $stale = 'dead-request|' . ( time() - 400 );
    add_option( LANGGAM_FIKIR_DEVFILES_LEASE, $stale, '', 'no' );
    $a_read = langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_LEASE );
    $b_read = langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_LEASE );
    ok( 'both see it stale', $a_read === $stale && $b_read === $stale && langgam_fikir_devfiles_lease_age( $a_read, time() ) >= LANGGAM_FIKIR_DEVFILES_STALE );
    $a = lf_new_lease();
    $b = lf_new_lease();
    $a_won = langgam_fikir_devfiles_lease_take_over( $a, $a_read );
    $b_won = langgam_fikir_devfiles_lease_take_over( $b, $b_read );
    ok( 'exactly one wins the take-over', $a_won && ! $b_won );
    ok( 'the lease holds the winner\'s value', lf_lease() === $a['value'] && '' === $b['value'] );
    langgam_fikir_devfiles_lease_release( $b );
    ok( 'the loser cannot release it', lf_lease() === $a['value'] );
    ok( 'the loser cannot renew it', ! langgam_fikir_devfiles_lease_renew( $b, true ) );

    echo "\nA stale lease is taken over and the files go\n";
    lf_reset_site();
    lf_build_theme( $dir );
    $outside = "$base/outside";
    lf_rm( $outside );
    lf_put( "$outside/keep.txt", 'precious' );
    lf_put( "$outside/dir/keep2.txt", 'precious' );
    symlink( "$outside/dir", "$dir/.cache" );          // a hidden symlink to a folder
    symlink( "$outside/keep.txt", "$dir/.link-file" ); // a hidden symlink to a file
    symlink( "$outside/dir", "$dir/tools/up" );        // a symlink inside a folder that goes
    symlink( "$outside/dir", "$dir/.git/objects/lnk" );
    symlink( "$base/missing", "$dir/.broken" );        // a broken symlink
    add_option( LANGGAM_FIKIR_DEVFILES_LEASE, 'dead-request|' . ( time() - 400 ), '', 'no' );
    $r = langgam_fikir_devfiles_run( $dir );
    ok( 'the run finishes', 'done' === $r, $r );
    ok( 'hidden entries are gone (.git with read-only objects, .gitignore, .github, .DS_Store)', ! file_exists( "$dir/.git" ) && ! file_exists( "$dir/.gitignore" ) && ! file_exists( "$dir/.github" ) && ! file_exists( "$dir/.DS_Store" ) );
    ok( 'tools/ and tests/ are gone', ! file_exists( "$dir/tools" ) && ! file_exists( "$dir/tests" ) );
    ok( 'nothing is listed any more', array() === langgam_fikir_devfiles_list( $dir ) );
    ok( 'code, data, bin, docs stay', is_file( "$dir/style.css" ) && is_file( "$dir/functions.php" ) && is_file( "$dir/inc/dev-files.php" ) && is_file( "$dir/data/books.json" ) && is_file( "$dir/bin/keep.sh" ) && is_file( "$dir/docs/keep.md" ) && is_file( "$dir/tools.php" ) );
    ok( '.htaccess, .user.ini and .well-known stay', is_file( "$dir/.htaccess" ) && is_file( "$dir/.user.ini" ) && is_file( "$dir/.well-known/acme" ) );
    ok( 'symlinks are removed (to a folder, to a file, nested, broken)', ! is_link( "$dir/.cache" ) && ! is_link( "$dir/.link-file" ) && ! is_link( "$dir/.broken" ) );
    ok( 'what the symlinks pointed to is untouched', 'precious' === @file_get_contents( "$outside/keep.txt" ) && 'precious' === @file_get_contents( "$outside/dir/keep2.txt" ) );
    ok( 'the version is recorded', LANGGAM_FIKIR_VERSION === get_option( LANGGAM_FIKIR_DEVFILES_VERSION ) && LANGGAM_FIKIR_VERSION === langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_VERSION ) );
    ok( 'the lease is freed', null === lf_lease() );

    echo "\nIt does not run twice for one version\n";
    lf_put( "$dir/.git/HEAD", 'back' );
    $writes = $GLOBALS['lf_writes'];
    $r      = langgam_fikir_devfiles_run( $dir );
    ok( 'a later request does nothing', 'current' === $r && is_file( "$dir/.git/HEAD" ) && $writes === $GLOBALS['lf_writes'], $r );

    echo "\nAnother request finished while this one waited (stale cache)\n";
    lf_reset_site();
    lf_build_theme( $dir );
    get_option( LANGGAM_FIKIR_DEVFILES_VERSION ); // this request cached "no version"
    $GLOBALS['lf_pdo']->exec( "INSERT INTO wp_options VALUES ('" . LANGGAM_FIKIR_DEVFILES_VERSION . "','" . LANGGAM_FIKIR_VERSION . "','yes')" ); // ...then another request finished
    $r = langgam_fikir_devfiles_run( $dir );
    ok( 'it re-reads the version from the database and stops', 'current' === $r && is_dir( "$dir/.git" ), $r );
    ok( 'it gives the lease back', null === lf_lease() );

    echo "\nA running clean-up renews its lease\n";
    lf_reset_site();
    lf_build_theme( $dir );
    for ( $i = 0; $i < 300; $i++ ) {
        lf_put( "$dir/tools/f$i.txt" );
    }
    $GLOBALS['lf_clock'] = 2000000000; // one second per look at the clock
    $start               = $GLOBALS['lf_clock'];
    $renewals            = array();
    $GLOBALS['wpdb']->log = array();
    $r                   = langgam_fikir_devfiles_run( $dir );
    $end                 = $GLOBALS['lf_clock'];
    foreach ( $GLOBALS['wpdb']->log as $q ) {
        if ( 0 === strpos( $q, 'UPDATE' ) ) {
            $renewals[] = $q;
        }
    }
    $elapsed = $end - $start;
    ok( 'it finishes', 'done' === $r, $r );
    ok( "it renewed ($elapsed seconds, " . count( $renewals ) . ' renewals)', count( $renewals ) >= 3 );
    ok( 'never more than once a minute', count( $renewals ) <= (int) floor( $elapsed / 60 ), count( $renewals ) . ' renewals in ' . $elapsed . 's' );
    $GLOBALS['lf_clock'] = null;

    echo "\nA request that loses its lease stops\n";
    lf_reset_site();
    lf_build_theme( $dir );
    for ( $i = 0; $i < 200; $i++ ) {
        lf_put( "$dir/tools/f$i.txt" );
    }
    $GLOBALS['lf_clock']  = 2000000000;
    $stolen               = false;
    $GLOBALS['lf_on_now'] = function ( $t ) use ( &$stolen ) {
        if ( ! $stolen && $t > 2000000000 + 100 ) { // another request takes the lease over
            $stolen = true;
            $GLOBALS['lf_pdo']->exec( "UPDATE wp_options SET option_value = 'thief|" . $t . "' WHERE option_name = '" . LANGGAM_FIKIR_DEVFILES_LEASE . "'" );
        }
    };
    $r = langgam_fikir_devfiles_run( $dir );
    $GLOBALS['lf_clock']  = null;
    $GLOBALS['lf_on_now'] = null;
    ok( 'it stops with "lost"', 'lost' === $r, $r );
    ok( 'it does not record the version', false === get_option( LANGGAM_FIKIR_DEVFILES_VERSION ) );
    ok( 'it does not release the other request\'s lease', 0 === strpos( (string) lf_lease(), 'thief|' ) );

    echo "\nThe hourly callback\n";
    lf_reset_site();
    lf_build_theme( $dir );
    ok( 'first clean-up', 'done' === langgam_fikir_devfiles_run( $dir ) );
    $writes = $GLOBALS['lf_writes'];
    $q      = count( $GLOBALS['wpdb']->log );
    $r      = langgam_fikir_devfiles_hourly( $dir );
    ok( 'leaves a folder with no development entries alone', 'clean' === $r && $writes === $GLOBALS['lf_writes'] && $q === count( $GLOBALS['wpdb']->log ), $r );
    ok( '...and keeps the recorded version', LANGGAM_FIKIR_VERSION === get_option( LANGGAM_FIKIR_DEVFILES_VERSION ) );
    lf_put( "$dir/.git/HEAD", 'uploaded again' );
    lf_put( "$dir/.git/objects/zz/obj", 'obj', 0444 );
    lf_put( "$dir/.gitignore" );
    $r = langgam_fikir_devfiles_hourly( $dir );
    ok( 'removes a .git uploaded after the version was recorded', 'done' === $r && ! file_exists( "$dir/.git" ) && ! file_exists( "$dir/.gitignore" ), $r );
    ok( '...and records the version again, lease freed', LANGGAM_FIKIR_VERSION === langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_VERSION ) && null === lf_lease() );
    lf_put( "$dir/.git/HEAD", 'again' );
    add_option( LANGGAM_FIKIR_DEVFILES_LEASE, 'other-request|' . time(), '', 'no' );
    $r = langgam_fikir_devfiles_hourly( $dir );
    ok( 'runs under the same lease: with another request cleaning it waits', 'busy' === $r && is_file( "$dir/.git/HEAD" ), $r );
    ok( '...and the forgotten version lets the next request retry', false === get_option( LANGGAM_FIKIR_DEVFILES_VERSION ) );

    echo "\nScheduling\n";
    lf_reset_site();
    lf_build_theme( LANGGAM_FIKIR_DIR );
    langgam_fikir_devfiles_init();
    ok( 'a live site: init cleans the theme folder', ! file_exists( LANGGAM_FIKIR_DIR . '/.git' ) && ! file_exists( LANGGAM_FIKIR_DIR . '/tools' ) );
    ok( 'a live site schedules the hourly event', false !== wp_next_scheduled( LANGGAM_FIKIR_DEVFILES_EVENT ) );
    do_action( 'switch_theme' );
    ok( 'switching theme clears it', false === wp_next_scheduled( LANGGAM_FIKIR_DEVFILES_EVENT ) );
    foreach ( array( 'local', 'development' ) as $env ) {
        lf_reset_site( $env );
        langgam_fikir_devfiles_init();
        ok( "a $env site does not schedule it", false === wp_next_scheduled( LANGGAM_FIKIR_DEVFILES_EVENT ) );
    }
    lf_reset_site();
    wp_schedule_event( time(), 'hourly', LANGGAM_FIKIR_DEVFILES_EVENT );
    $GLOBALS['lf_env'] = 'local';
    langgam_fikir_devfiles_init();
    ok( 'a site that became local drops an old event', false === wp_next_scheduled( LANGGAM_FIKIR_DEVFILES_EVENT ) );
    ok( 'the callback is hooked to the event', in_array( 'langgam_fikir_devfiles_hourly', $GLOBALS['lf_hooks'][ LANGGAM_FIKIR_DEVFILES_EVENT ], true ) );

    echo "\nThe constant that keeps files\n";
    lf_reset_site();
    lf_build_theme( $dir );
    define( 'LANGGAM_FIKIR_KEEP_DOTFILES', true );
    $r = langgam_fikir_devfiles_run( $dir );
    ok( 'LANGGAM_FIKIR_KEEP_DOTFILES keeps everything on a production site', 'kept' === $r && is_dir( "$dir/.git" ) && is_dir( "$dir/tools" ), $r );
    ok( '...and the hourly callback leaves it alone', 'kept' === langgam_fikir_devfiles_hourly( $dir ) && is_dir( "$dir/.git" ) );
}

/**
 * Files that cannot be removed. Runs as an unprivileged user in a folder the parent built.
 */
function lf_run_permissions( $dir ) {
    echo "\nSomething cannot be removed (running as uid " . posix_getuid() . ")\n";
    lf_reset_site();
    $log = "$dir/php-error.log";
    ini_set( 'error_log', $log );
    $noon                = strtotime( '2030-06-15 12:00:00 UTC' );
    $GLOBALS['lf_clock'] = $noon;
    // Make time run only when a test says so.
    $GLOBALS['lf_hooks']['langgam_fikir_devfiles_now'] = array(
        function ( $t ) {
            return $GLOBALS['lf_clock'];
        },
    );
    $lines = function () use ( $log ) {
        return is_file( $log ) ? count( array_filter( file( $log ) ) ) : 0;
    };

    $r = langgam_fikir_devfiles_run( "$dir/theme" );
    ok( 'the run reports failure', 'failed' === $r, $r );
    ok( 'what could be removed is gone, what could not is left', ! file_exists( "$dir/theme/.gitignore" ) && is_file( "$dir/theme/.git/locked/x" ) );
    ok( 'the version is not recorded', null === langgam_fikir_devfiles_db_get( LANGGAM_FIKIR_DEVFILES_VERSION ) );
    ok( 'the lease is kept', null !== lf_lease() );
    ok( 'one line is logged, with the count and the FTP advice', 1 === $lines() && false !== strpos( (string) file_get_contents( $log ), '2 development file(s)' ) && false !== strpos( (string) file_get_contents( $log ), 'by FTP' ), (string) @file_get_contents( $log ) );

    $before = lf_lease();
    $r      = langgam_fikir_devfiles_run( "$dir/theme" );
    ok( 'the next request does not retry (fresh lease)', 'busy' === $r && lf_lease() === $before, $r );
    $GLOBALS['lf_clock'] = $noon + 4 * MINUTE_IN_SECONDS + 50;
    ok( '4m50s later it still does not retry', 'busy' === langgam_fikir_devfiles_run( "$dir/theme" ) );
    $GLOBALS['lf_clock'] = $noon + 5 * MINUTE_IN_SECONDS + 10;
    $r                   = langgam_fikir_devfiles_run( "$dir/theme" );
    ok( 'five minutes after the failure one request retries', 'failed' === $r && lf_lease() !== $before, $r );
    ok( 'the same day logs nothing more', 1 === $lines(), $lines() . ' lines' );
    $GLOBALS['lf_clock'] = $noon + DAY_IN_SECONDS;
    $r                   = langgam_fikir_devfiles_run( "$dir/theme" );
    ok( 'the next day logs again', 'failed' === $r && 2 === $lines(), $r . ', ' . $lines() . ' lines' );
}

/* ---------------------------------------------------------------------------
 * Go
 * ------------------------------------------------------------------------ */

if ( isset( $argv[1] ) && '--perm' === $argv[1] ) {
    lf_run_permissions( $argv[2] );
    exit( $GLOBALS['lf_fail'] ? 1 : 0 );
}

echo "Development files clean-up: self-test (theme version " . LANGGAM_FIKIR_VERSION . ")\n";
mkdir( $base, 0755, true );
lf_reset_site();
lf_run_main( $base );

echo "\n.htaccess\n";
$htaccess = file_get_contents( $theme_root . '/.htaccess' );
ok( 'has mod_alias RedirectMatch 403 rules', substr_count( $htaccess, 'RedirectMatch 403' ) >= 2 );
ok( 'has a mod_rewrite fallback', false !== strpos( $htaccess, '<IfModule !mod_alias.c>' ) && false !== strpos( $htaccess, 'RewriteRule' ) );
preg_match_all( '/^RedirectMatch 403 (.+)$/m', $htaccess, $pats );
$blocked  = array( '/wp-content/themes/LF/.git/config', '/wp-content/themes/LF/.gitignore', '/wp-content/themes/LF/.github/x.yml', '/wp-content/themes/LF/tools/test.php', '/wp-content/themes/LF/tests/a.php', '/wp-content/themes/LF/bin/x.sh', '/wp-content/themes/LF/docs/a.md', '/blog/wp-content/themes/LF/.git/HEAD', '/wp-content/themes/LF/Tools/a.php' );
$allowed  = array( '/wp-content/themes/LF/style.css', '/wp-content/themes/LF/.well-known/acme-challenge/x', '/wp-content/themes/LF/.htaccess', '/wp-content/themes/LF/assets/css/tools.css', '/wp-content/themes/LF/inc/docs.php', '/wp-content/themes/LF/assets/tools/x.js' );
$test_url = function ( $url ) use ( $pats ) {
    foreach ( $pats[1] as $p ) {
        if ( preg_match( '~' . str_replace( '~', '\~', $p ) . '~', $url ) ) {
            return true;
        }
    }
    return false;
};
$bad = array();
foreach ( $blocked as $u ) {
    if ( ! $test_url( $u ) ) {
        $bad[] = "not refused: $u";
    }
}
foreach ( $allowed as $u ) {
    if ( $test_url( $u ) ) {
        $bad[] = "wrongly refused: $u";
    }
}
ok( 'the patterns refuse hidden files, tools, tests, bin, docs and spare .well-known, style.css and deeper folders of the same name', ! $bad, implode( '; ', $bad ) );

echo "\nPermissions\n";
$perm = "$base/perm";
if ( 0 === posix_getuid() && trim( (string) shell_exec( 'command -v setpriv' ) ) ) {
    lf_rm( $perm );
    mkdir( $perm, 0777, true );
    chmod( $perm, 0777 );
    lf_put( "$perm/theme/style.css" );
    lf_put( "$perm/theme/.gitignore" );
    lf_put( "$perm/theme/.git/free/y" );
    lf_put( "$perm/theme/.git/locked/x" );
    lf_put( "$perm/theme/.git/locked/z" );
    foreach ( array( "$perm/theme", "$perm/theme/.git", "$perm/theme/.git/free" ) as $d ) {
        chmod( $d, 0777 );
    }
    foreach ( array( "$perm/theme/style.css", "$perm/theme/.gitignore", "$perm/theme/.git/free/y", "$perm/theme/.git/locked/x", "$perm/theme/.git/locked/z" ) as $f ) {
        chmod( $f, 0666 );
    }
    chown( "$perm/theme/.gitignore", 65534 );
    chmod( "$perm/theme/.git/locked", 0755 ); // owned by root: the unprivileged user can neither delete in it nor chmod it
    $cmd = 'setpriv --reuid=65534 --regid=65534 --clear-groups ' . escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' --perm ' . escapeshellarg( $perm ) . ' 2>&1';
    $out = array();
    exec( $cmd, $out, $code );
    echo implode( "\n", $out ) . "\n";
    foreach ( $out as $line ) {
        if ( 0 === strpos( trim( $line ), 'PASS' ) ) {
            $GLOBALS['lf_pass']++;
        } elseif ( 0 === strpos( trim( $line ), 'FAIL' ) ) {
            $GLOBALS['lf_fail']++;
        }
    }
    if ( $code && ! $GLOBALS['lf_fail'] ) {
        ok( 'the unprivileged run completed', false, "exit $code" );
    }
    lf_rm( $perm );
} else {
    echo "  SKIP  needs root and setpriv to run a clean-up that is not allowed to delete\n";
}

lf_rm( $base );
echo "\n" . $GLOBALS['lf_pass'] . ' passed, ' . $GLOBALS['lf_fail'] . " failed\n";
exit( $GLOBALS['lf_fail'] ? 1 : 0 );
