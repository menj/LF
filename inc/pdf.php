<?php
/**
 * PDF files made on the server with headless Chrome
 *
 * When Theme Options > Print & PDF > Make PDFs with is "Chrome on this server", the Download PDF
 * button asks for ?lf-pdf=1 on an article, book or page. The theme runs Chrome (or Chromium)
 * headless on the page's own address, which prints with the theme's print stylesheet
 * (assets/css/print.css: paper size, margins and @page rules from the settings), and sends
 * the file. Each file is kept in uploads/langgam-fikir-pdf/ and reused until the page, the
 * theme version or the print settings change.
 *
 * Safety:
 *  - Chrome runs without a shell (proc_open with an argument list), with a time limit, on the
 *    site's own permalink only.
 *  - The program must be an executable whose name looks like Chrome, Chromium or Edge.
 *  - Only published, public, non-password-protected pages whose type has the button get a PDF.
 *  - One PDF is made at a time; anyone arriving meanwhile gets the print dialog instead.
 *  - Without a working Chrome nothing runs: the button falls back to the print dialog.
 *
 * @package Langgam_Fikir
 * @since 2.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Whether PHP may start programs here (proc_open present and not disabled).
 */
function langgam_fikir_pdf_can_exec() {
    if ( ! function_exists( 'proc_open' ) ) {
        return false;
    }
    $disabled = array_map( 'trim', explode( ',', strtolower( (string) ini_get( 'disable_functions' ) ) ) );
    return ! in_array( 'proc_open', $disabled, true );
}

/**
 * Run a program with arguments (no shell) and a time limit.
 *
 * @param string[] $command Program then its arguments.
 * @param int      $timeout Seconds.
 * @return array{code:int, out:string, timed_out:bool}
 */
function langgam_fikir_pdf_run( array $command, $timeout = 60 ) {
    $result = array( 'code' => -1, 'out' => '', 'timed_out' => false );
    if ( ! langgam_fikir_pdf_can_exec() ) {
        return $result;
    }

    $spec = array(
        0 => array( 'pipe', 'r' ),
        1 => array( 'pipe', 'w' ),
        2 => array( 'pipe', 'w' ),
    );
    // PHP 7.4+ runs an array command directly, with no shell in between.
    $proc = @proc_open( $command, $spec, $pipes ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
    if ( ! is_resource( $proc ) ) {
        return $result;
    }
    fclose( $pipes[0] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
    stream_set_blocking( $pipes[1], false );
    stream_set_blocking( $pipes[2], false );

    $start = microtime( true );
    $out   = '';
    while ( true ) {
        $out .= (string) stream_get_contents( $pipes[1] ) . (string) stream_get_contents( $pipes[2] );
        $status = proc_get_status( $proc );
        if ( ! $status['running'] ) {
            $result['code'] = (int) $status['exitcode'];
            break;
        }
        if ( microtime( true ) - $start > $timeout ) {
            proc_terminate( $proc, 9 );
            $result['timed_out'] = true;
            break;
        }
        usleep( 100000 );
    }
    $out .= (string) stream_get_contents( $pipes[1] ) . (string) stream_get_contents( $pipes[2] );
    fclose( $pipes[1] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
    fclose( $pipes[2] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
    $code = proc_close( $proc );
    if ( -1 === $result['code'] && ! $result['timed_out'] ) {
        $result['code'] = (int) $code;
    }
    $result['out'] = substr( trim( $out ), 0, 2000 );
    return $result;
}

/**
 * Whether a path may be run as Chrome: an executable file named like a Chromium browser.
 */
function langgam_fikir_pdf_path_ok( $path ) {
    $path = (string) $path;
    return '' !== $path
        && preg_match( '/(chrom|chrome|chromium|headless_shell|msedge|edge)[^\/\\\\]*$/i', $path )
        && @is_file( $path ) && @is_executable( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors -- open_basedir may forbid the check
}

/**
 * Places Chrome is usually installed.
 *
 * @return string[]
 */
function langgam_fikir_pdf_candidates() {
    $names = array( 'google-chrome', 'google-chrome-stable', 'chromium', 'chromium-browser', 'chrome', 'headless_shell', 'microsoft-edge', 'msedge' );
    $dirs  = array_filter( explode( PATH_SEPARATOR, (string) getenv( 'PATH' ) ) );
    $dirs  = array_unique( array_merge( $dirs, array( '/usr/bin', '/usr/local/bin', '/snap/bin', '/opt/homebrew/bin' ) ) );

    $paths = array();
    foreach ( $dirs as $dir ) {
        foreach ( $names as $name ) {
            $paths[] = rtrim( $dir, '/\\' ) . DIRECTORY_SEPARATOR . $name;
        }
    }
    return array_merge( $paths, array(
        '/opt/google/chrome/chrome',
        '/opt/pw-browsers/chromium',
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        '/Applications/Chromium.app/Contents/MacOS/Chromium',
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
    ) );
}

/**
 * Find and check Chrome. Cached for 12 hours (and refreshed when Theme Options are saved).
 *
 * @param bool $fresh Skip the cache.
 * @return array{ok:bool, path:string, version:string, reason:string}
 */
function langgam_fikir_pdf_chrome( $fresh = false ) {
    $configured = trim( (string) langgam_fikir_get_option( 'pdf_chrome_path', '' ) );
    $cache_key  = 'lf_pdf_chrome_' . md5( $configured );
    if ( ! $fresh ) {
        $cached = get_transient( $cache_key );
        if ( is_array( $cached ) ) {
            return $cached;
        }
    }

    $info = array( 'ok' => false, 'path' => '', 'version' => '', 'reason' => '' );
    if ( ! langgam_fikir_pdf_can_exec() ) {
        $info['reason'] = 'exec';
    } else {
        $paths = '' !== $configured ? array( $configured ) : langgam_fikir_pdf_candidates();
        foreach ( $paths as $path ) {
            if ( ! langgam_fikir_pdf_path_ok( $path ) ) {
                continue;
            }
            $run = langgam_fikir_pdf_run( array( $path, '--version' ), 15 );
            if ( 0 === $run['code'] && preg_match( '/(Chrom|Edge)/i', $run['out'] ) ) {
                $info = array( 'ok' => true, 'path' => $path, 'version' => $run['out'], 'reason' => '' );
                break;
            }
        }
        if ( ! $info['ok'] ) {
            $info['reason'] = '' !== $configured ? 'path' : 'missing';
        }
    }

    set_transient( $cache_key, $info, 12 * HOUR_IN_SECONDS );
    return $info;
}

/**
 * Whether the Download PDF button should be offered.
 */
function langgam_fikir_pdf_server_ready() {
    if ( 'chrome' !== langgam_fikir_get_option( 'pdf_engine', 'browser' ) ) {
        return false;
    }
    $chrome = langgam_fikir_pdf_chrome();
    return ! empty( $chrome['ok'] );
}

/* =========================================================================
 * Files
 * ====================================================================== */

/**
 * The folder PDFs are kept in (created on first use, not browsable).
 */
function langgam_fikir_pdf_dir() {
    $uploads = wp_upload_dir( null, false );
    $dir     = trailingslashit( $uploads['basedir'] ) . 'langgam-fikir-pdf';
    if ( ! is_dir( $dir ) && wp_mkdir_p( $dir ) ) {
        // phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        @file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" );
        @file_put_contents( $dir . '/.htaccess', "Require all denied\nDeny from all\n" );
        // phpcs:enable
    }
    return $dir;
}

/**
 * Everything a PDF depends on, so a change to any of them makes a fresh file.
 */
function langgam_fikir_pdf_fingerprint( WP_Post $post ) {
    $keys = array( 'print_paper', 'print_margins', 'print_size', 'print_header', 'print_footer', 'print_citation', 'print_links', 'print_images', 'color_scheme', 'cite_formats' );
    $opts = array();
    foreach ( $keys as $key ) {
        $opts[ $key ] = langgam_fikir_get_option( $key );
    }
    return substr( md5( $post->post_modified_gmt . '|' . LANGGAM_FIKIR_VERSION . '|' . wp_json_encode( $opts ) ), 0, 12 );
}

function langgam_fikir_pdf_file( WP_Post $post ) {
    return langgam_fikir_pdf_dir() . '/' . (int) $post->ID . '-' . langgam_fikir_pdf_fingerprint( $post ) . '.pdf';
}

/**
 * Remove a post's PDFs (all versions).
 */
function langgam_fikir_pdf_forget( $post_id ) {
    $dir = langgam_fikir_pdf_dir();
    foreach ( (array) glob( $dir . '/' . absint( $post_id ) . '-*.pdf' ) as $file ) {
        wp_delete_file( $file );
    }
}
add_action( 'save_post', 'langgam_fikir_pdf_forget' );
add_action( 'deleted_post', 'langgam_fikir_pdf_forget' );

/**
 * Make the PDF for a post with Chrome.
 *
 * @return string|WP_Error The file path.
 */
function langgam_fikir_pdf_make( WP_Post $post ) {
    $chrome = langgam_fikir_pdf_chrome();
    if ( empty( $chrome['ok'] ) ) {
        return new WP_Error( 'lf_pdf_chrome', 'Chrome is not available.' );
    }

    $file    = langgam_fikir_pdf_file( $post );
    $tmp     = $file . '.' . wp_generate_password( 6, false ) . '.tmp';
    $profile = trailingslashit( get_temp_dir() ) . 'lf-chrome-' . wp_generate_password( 8, false );

    $args = array(
        $chrome['path'],
        '--headless',
        '--disable-gpu',
        '--disable-extensions',
        '--disable-dev-shm-usage',
        '--disable-background-networking',
        '--disable-component-update',
        '--disable-sync',
        '--metrics-recording-only',
        '--no-first-run',
        '--no-default-browser-check',
        '--hide-scrollbars',
        '--mute-audio',
        '--run-all-compositor-stages-before-draw',
        '--virtual-time-budget=10000',
        '--no-pdf-header-footer',
        '--print-to-pdf-no-header',
        '--user-data-dir=' . $profile,
        '--print-to-pdf=' . $tmp,
    );
    if ( langgam_fikir_get_option( 'pdf_no_sandbox', 0 ) ) {
        $args[] = '--no-sandbox';
    }
    /**
     * Filters the address Chrome prints (default: the permalink, with a marker the theme
     * uses to leave the screen-only parts out).
     */
    $source = apply_filters( 'langgam_fikir_pdf_source_url', add_query_arg( 'lf-print', 'pdf', get_permalink( $post ) ), $post );

    // Chrome prints whatever it gets, error pages included: make sure the page itself loads.
    if ( preg_match( '#^https?://#i', $source ) ) {
        $check = wp_remote_get( $source, array(
            'timeout'             => 20,
            'redirection'         => 2,
            'limit_response_size' => 1048576,
            'sslverify'           => apply_filters( 'https_local_ssl_verify', false ),
        ) );
        $body = is_wp_error( $check ) ? '' : (string) wp_remote_retrieve_body( $check );
        if ( is_wp_error( $check ) || 200 !== (int) wp_remote_retrieve_response_code( $check ) || false === strpos( $body, 'lf-print-pdf' ) ) {
            set_transient( 'lf_pdf_last_error', array(
                'time'      => time(),
                'code'      => is_wp_error( $check ) ? 0 : (int) wp_remote_retrieve_response_code( $check ),
                'timed_out' => false,
                'out'       => is_wp_error( $check )
                    ? $check->get_error_message()
                    : ( 200 === (int) wp_remote_retrieve_response_code( $check )
                        ? 'The page loaded without the theme\'s print marker (a cache or security plugin may be serving another copy).'
                        : 'The page did not load for the server (HTTP ' . (int) wp_remote_retrieve_response_code( $check ) . ').' ),
            ), WEEK_IN_SECONDS );
            return new WP_Error( 'lf_pdf_source', 'The page did not load.' );
        }
    }
    $args[] = $source;

    /**
     * Filters the Chrome command line (program first, then arguments).
     */
    $args = (array) apply_filters( 'langgam_fikir_pdf_chrome_args', $args, $post );

    $run = langgam_fikir_pdf_run( $args, 90 );

    // Clean the throw-away profile.
    if ( is_dir( $profile ) && class_exists( 'RecursiveIteratorIterator' ) ) {
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $profile, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $it as $item ) {
            $item->isDir() ? @rmdir( $item->getPathname() ) : @unlink( $item->getPathname() ); // phpcs:ignore WordPress.PHP.NoSilencedErrors,WordPress.WP.AlternativeFunctions
        }
        @rmdir( $profile ); // phpcs:ignore WordPress.PHP.NoSilencedErrors,WordPress.WP.AlternativeFunctions
    }

    if ( ! is_file( $tmp ) || filesize( $tmp ) < 500 || '%PDF' !== (string) file_get_contents( $tmp, false, null, 0, 4 ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        if ( is_file( $tmp ) ) {
            wp_delete_file( $tmp );
        }
        set_transient( 'lf_pdf_last_error', array( 'time' => time(), 'code' => $run['code'], 'timed_out' => $run['timed_out'], 'out' => $run['out'] ), WEEK_IN_SECONDS );
        return new WP_Error( 'lf_pdf_failed', 'Chrome did not produce a PDF.' );
    }

    langgam_fikir_pdf_forget( $post->ID );
    rename( $tmp, $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename
    delete_transient( 'lf_pdf_last_error' );
    return $file;
}

/* =========================================================================
 * Download
 * ====================================================================== */

/**
 * ?lf-pdf=1 on a singular page: send the PDF, or fall back to the print dialog.
 */
function langgam_fikir_pdf_download() {
    if ( empty( $_GET['lf-pdf'] ) || ! is_singular() ) { // phpcs:ignore WordPress.Security.NonceVerification
        return;
    }
    $post = get_queried_object();
    if ( ! $post instanceof WP_Post || ! langgam_fikir_print_allowed( $post ) || 'publish' !== $post->post_status || post_password_required( $post ) ) {
        return;
    }

    $fallback = add_query_arg( 'lf-print', '1', get_permalink( $post ) );
    if ( ! langgam_fikir_pdf_server_ready() ) {
        wp_safe_redirect( $fallback );
        exit;
    }

    $file = langgam_fikir_pdf_file( $post );
    if ( ! is_file( $file ) ) {
        // One at a time: Chrome is heavy.
        if ( get_transient( 'lf_pdf_lock' ) ) {
            wp_safe_redirect( $fallback );
            exit;
        }
        set_transient( 'lf_pdf_lock', 1, 2 * MINUTE_IN_SECONDS );
        if ( function_exists( 'set_time_limit' ) ) {
            @set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
        }
        $made = langgam_fikir_pdf_make( $post );
        delete_transient( 'lf_pdf_lock' );
        if ( is_wp_error( $made ) ) {
            wp_safe_redirect( $fallback );
            exit;
        }
        $file = $made;
    }

    $name = sanitize_title( get_the_title( $post ) );
    nocache_headers();
    header( 'Content-Type: application/pdf' );
    header( 'Content-Disposition: attachment; filename="' . ( $name ? $name : 'document-' . $post->ID ) . '.pdf"' );
    header( 'Content-Length: ' . filesize( $file ) );
    header( 'X-Robots-Tag: noindex, nofollow' );
    header( 'X-Content-Type-Options: nosniff' );
    readfile( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
    exit;
}
add_action( 'template_redirect', 'langgam_fikir_pdf_download', 1 );

/**
 * Forget every PDF and the Chrome check when the print settings are saved.
 */
function langgam_fikir_pdf_options_saved( $old, $new ) {
    $keys = array( 'pdf_engine', 'pdf_chrome_path', 'pdf_no_sandbox', 'print_paper', 'print_margins', 'print_size', 'print_header', 'print_footer', 'print_citation', 'print_links', 'print_images' );
    foreach ( $keys as $key ) {
        if ( ( isset( $old[ $key ] ) ? $old[ $key ] : null ) !== ( isset( $new[ $key ] ) ? $new[ $key ] : null ) ) {
            foreach ( (array) glob( langgam_fikir_pdf_dir() . '/*.pdf' ) as $file ) {
                wp_delete_file( $file );
            }
            delete_transient( 'lf_pdf_chrome_' . md5( trim( (string) ( isset( $new['pdf_chrome_path'] ) ? $new['pdf_chrome_path'] : '' ) ) ) );
            delete_transient( 'lf_pdf_last_error' );
            return;
        }
    }
}
add_action( 'update_option_langgam_fikir_options', 'langgam_fikir_pdf_options_saved', 10, 2 );

/* =========================================================================
 * Admin status (Theme Options > Print & PDF)
 * ====================================================================== */

/**
 * What this server can do, as escaped HTML for the status field.
 */
function langgam_fikir_pdf_status_html() {
    $chrome = langgam_fikir_pdf_chrome( true );
    $engine = langgam_fikir_get_option( 'pdf_engine', 'browser' );
    $lines  = array();

    if ( $chrome['ok'] ) {
        $lines[] = '<span class="lf-status-ok">' . esc_html__( 'Chrome found:', 'langgam-fikir' ) . '</span> <code>' . esc_html( $chrome['path'] ) . '</code> (' . esc_html( $chrome['version'] ) . ')';
        $lines[] = 'chrome' === $engine
            ? esc_html__( 'Download PDF buttons are on.', 'langgam-fikir' )
            : esc_html__( 'Choose "Chrome on this server" above to offer Download PDF buttons.', 'langgam-fikir' );
    } elseif ( 'exec' === $chrome['reason'] ) {
        $lines[] = '<span class="lf-status-off">' . esc_html__( 'This server does not let PHP start programs (proc_open is disabled), so PDFs are made in the reader\'s browser.', 'langgam-fikir' ) . '</span>';
    } elseif ( 'path' === $chrome['reason'] ) {
        $lines[] = '<span class="lf-status-off">' . esc_html__( 'The Chrome program above was not found, is not executable, or did not answer. Check the path, or leave it empty to search.', 'langgam-fikir' ) . '</span>';
    } else {
        $lines[] = '<span class="lf-status-off">' . esc_html__( 'Chrome or Chromium is not installed on this server, so PDFs are made in the reader\'s browser. Your host can install it, or enter its path above.', 'langgam-fikir' ) . '</span>';
    }

    $error = get_transient( 'lf_pdf_last_error' );
    if ( is_array( $error ) && $chrome['ok'] ) {
        $lines[] = '<span class="lf-status-off">' . sprintf(
            /* translators: 1: date and time, 2: Chrome's output */
            esc_html__( 'The last PDF failed (%1$s). Chrome said: %2$s', 'langgam-fikir' ),
            esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $error['time'] ) ),
            '<code>' . esc_html( $error['timed_out'] ? __( 'no answer within 90 seconds', 'langgam-fikir' ) : ( '' !== $error['out'] ? $error['out'] : 'exit ' . $error['code'] ) ) . '</code>'
        ) . '</span>';
        $lines[] = esc_html__( 'Readers get the print dialog meanwhile. If Chrome cannot start, try "Run Chrome without its sandbox".', 'langgam-fikir' );
    }

    return '<p>' . implode( '</p><p>', $lines ) . '</p>';
}
