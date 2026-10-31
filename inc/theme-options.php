<?php
/**
 * Theme options: colour schemes, typography, homepage and contact defaults.
 *
 * Everything is stored in a single option (langgam_fikir_options) and edited on a
 * tabbed page under Appearance > Theme Settings.
 *
 * @package Langgam_Fikir
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

const LANGGAM_FIKIR_OPTION = 'langgam_fikir_options';
const LANGGAM_FIKIR_PAGE   = 'langgam-fikir-settings';

/**
 * Colour schemes offered on the Colours tab.
 *
 * The full variable sets live in assets/css/schemes.css; the values here only
 * drive the swatches and the live preview in the settings screen, so keep the
 * primary/accent/background/surface/text values in sync with that file.
 */
function langgam_fikir_get_schemes() {
    return array(
        'earth'    => array(
            'label'   => __( 'Earth', 'langgam-fikir' ),
            'note'    => __( 'Warm and bookish. The original look.', 'langgam-fikir' ),
            'palette' => array( 'primary' => '#6B5B4A', 'accent' => '#C9A961', 'bg' => '#FFFFFF', 'surface' => '#F5F5F0', 'text' => '#333333' ),
        ),
        'slate'    => array(
            'label'   => __( 'Slate', 'langgam-fikir' ),
            'note'    => __( 'Cool, neutral and crisp.', 'langgam-fikir' ),
            'palette' => array( 'primary' => '#2E3A46', 'accent' => '#4F7288', 'bg' => '#FFFFFF', 'surface' => '#F3F5F7', 'text' => '#22292F' ),
        ),
        'midnight' => array(
            'label'   => __( 'Midnight', 'langgam-fikir' ),
            'note'    => __( 'Dark background, gold accents.', 'langgam-fikir' ),
            'palette' => array( 'primary' => '#E8DFD2', 'accent' => '#C9A961', 'bg' => '#14120F', 'surface' => '#1C1915', 'text' => '#E6E1D8' ),
        ),
        'auto'     => array(
            'label'   => __( 'Auto', 'langgam-fikir' ),
            'note'    => __( 'Earth by day, Midnight when the visitor\'s device prefers dark.', 'langgam-fikir' ),
            'palette' => array( 'primary' => '#6B5B4A', 'accent' => '#C9A961', 'bg' => '#FFFFFF', 'surface' => '#F5F5F0', 'text' => '#333333' ),
        ),
    );
}

require_once __DIR__ . '/settings-fields.php';

/**
 * Field definition for a key (cached), or null.
 */
function langgam_fikir_get_field( $key ) {
    static $map = null;
    if ( null === $map ) {
        $map = array();
        foreach ( langgam_fikir_get_settings_fields() as $tab ) {
            $map += $tab['fields'];
        }
    }
    return isset( $map[ $key ] ) ? $map[ $key ] : null;
}

/**
 * Value saved in Theme Settings, or null when the key has never been saved.
 */
function langgam_fikir_stored_option( $key ) {
    $options = get_option( LANGGAM_FIKIR_OPTION, array() );
    return ( is_array( $options ) && array_key_exists( $key, $options ) ) ? $options[ $key ] : null;
}

/**
 * Read a theme option: saved value, then pre-2.0 Customizer value, then the default.
 * A field flagged allow_empty can be saved blank on purpose (for example to hide an element).
 */
function langgam_fikir_get_option( $key ) {
    $stored = langgam_fikir_stored_option( $key );
    $field  = langgam_fikir_get_field( $key );

    if ( null !== $stored && '' !== $stored && 0 !== $stored ) {
        return $stored;
    }
    if ( '' === $stored && $field && ! empty( $field['allow_empty'] ) ) {
        return '';
    }

    $legacy = get_theme_mod( $key, null );
    if ( null !== $legacy && '' !== $legacy ) {
        return $legacy;
    }

    return $field ? $field['default'] : '';
}

/**
 * Theme Settings value first; otherwise a value saved by the pre-2.1 per-page meta boxes;
 * otherwise the default.
 */
function langgam_fikir_meta_or_option( $post_id, $meta_key, $option_key ) {
    $stored = langgam_fikir_stored_option( $option_key );
    if ( null !== $stored && '' !== $stored ) {
        return $stored;
    }

    $meta = get_post_meta( $post_id, $meta_key, true );
    if ( '' !== $meta && false !== $meta ) {
        return $meta;
    }

    return langgam_fikir_get_option( $option_key );
}

/**
 * Plain text with blank-line paragraphs, safe to print.
 */
function langgam_fikir_paragraphs( $text ) {
    return wp_kses_post( wpautop( esc_html( $text ) ) );
}

/**
 * Register the option and make sure it exists, so the first save is a plain update.
 */
function langgam_fikir_register_settings() {
    register_setting(
        'langgam_fikir_settings',
        LANGGAM_FIKIR_OPTION,
        array(
            'type'              => 'array',
            'sanitize_callback' => 'langgam_fikir_sanitize_options',
            'default'           => array(),
        )
    );

    if ( false === get_option( LANGGAM_FIKIR_OPTION ) ) {
        add_option( LANGGAM_FIKIR_OPTION, array() );
    }
}
add_action( 'admin_init', 'langgam_fikir_register_settings' );

/**
 * Sanitise the whole settings form. Unknown keys are dropped.
 */
function langgam_fikir_sanitize_options( $input ) {
    $input = is_array( $input ) ? $input : array();
    $clean = array();

    foreach ( langgam_fikir_get_settings_fields() as $tab ) {
        foreach ( $tab['fields'] as $key => $field ) {
            // options.php has already unslashed the submitted data.
            $raw = isset( $input[ $key ] ) ? $input[ $key ] : '';
            $raw = is_scalar( $raw ) ? (string) $raw : '';

            switch ( $field['type'] ) {
                case 'scheme':
                    $value = array_key_exists( $raw, langgam_fikir_get_schemes() ) ? $raw : $field['default'];
                    break;
                case 'select':
                    $value = array_key_exists( $raw, $field['choices'] ) ? $raw : $field['default'];
                    break;
                case 'color':
                    $value = (string) sanitize_hex_color( $raw );
                    break;
                case 'number':
                    $value = '' === $raw ? $field['default'] : min( $field['max'], max( $field['min'], absint( $raw ) ) );
                    break;
                case 'image':
                    $id    = absint( $raw );
                    $value = ( $id && wp_attachment_is_image( $id ) ) ? $id : 0;
                    break;
                case 'page':
                    $id    = absint( $raw );
                    $value = ( $id && 'page' === get_post_type( $id ) ) ? $id : 0;
                    break;
                case 'email':
                    $value = sanitize_email( $raw );
                    break;
                case 'url':
                    $value = esc_url_raw( $raw );
                    break;
                case 'textarea':
                    $value = sanitize_textarea_field( $raw );
                    break;
                default:
                    $value = sanitize_text_field( $raw );
            }

            $clean[ $key ] = $value;
        }
    }

    return $clean;
}

/*--------------------------------------------------------------
# Front end: body classes and custom colour variables
--------------------------------------------------------------*/

/**
 * Black or white, whichever is more readable on the given background.
 */
function langgam_fikir_contrast_text( $hex ) {
    $hex = ltrim( (string) sanitize_hex_color( $hex ), '#' );
    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if ( 6 !== strlen( $hex ) ) {
        return '#FFFFFF';
    }

    $lum = 0;
    foreach ( array( 0.2126, 0.7152, 0.0722 ) as $i => $weight ) {
        $channel = hexdec( substr( $hex, $i * 2, 2 ) ) / 255;
        $channel = $channel <= 0.03928 ? $channel / 12.92 : pow( ( $channel + 0.055 ) / 1.055, 2.4 );
        $lum    += $weight * $channel;
    }

    // WCAG contrast against white vs. against the dark text colour (#1A1610, luminance ~0.009).
    $against_white = 1.05 / ( $lum + 0.05 );
    $against_dark  = ( $lum + 0.05 ) / 0.059;

    return $against_white >= $against_dark ? '#FFFFFF' : '#1A1610';
}

/**
 * CSS variable overrides for any colour the user set explicitly.
 */
function langgam_fikir_custom_colour_css() {
    $vars = array();

    $primary = sanitize_hex_color( (string) langgam_fikir_raw_option( 'color_primary' ) );
    if ( $primary ) {
        $vars['--color-primary']   = $primary;
        $vars['--color-bg-hero']   = $primary;
        $vars['--color-on-hero']   = langgam_fikir_contrast_text( $primary );
    }

    $accent = sanitize_hex_color( (string) langgam_fikir_raw_option( 'color_accent' ) );
    if ( $accent ) {
        $vars['--color-accent']      = $accent;
        $vars['--color-secondary']   = $accent;
        $vars['--color-accent-dark'] = 'color-mix(in srgb, ' . $accent . ' 78%, #000)';
        $vars['--color-on-accent']   = langgam_fikir_contrast_text( $accent );
        $vars['--color-focus-ring']  = 'color-mix(in srgb, ' . $accent . ' 30%, transparent)';
    }

    $background = sanitize_hex_color( (string) langgam_fikir_raw_option( 'color_background' ) );
    if ( $background ) {
        $vars['--color-bg-primary'] = $background;
    }

    $surface = sanitize_hex_color( (string) langgam_fikir_raw_option( 'color_surface' ) );
    if ( $surface ) {
        $vars['--color-bg-secondary'] = $surface;
        $vars['--color-bg-tint']      = $surface;
        $vars['--color-bg-muted']     = 'color-mix(in srgb, ' . $surface . ' 85%, var(--color-text))';
    }

    $text = sanitize_hex_color( (string) langgam_fikir_raw_option( 'color_text' ) );
    if ( $text ) {
        $vars['--color-text']       = $text;
        $vars['--color-text-light'] = 'color-mix(in srgb, ' . $text . ' 75%, var(--color-bg-primary))';
        $vars['--color-text-muted'] = 'color-mix(in srgb, ' . $text . ' 55%, var(--color-bg-primary))';
        $vars['--color-border']     = 'color-mix(in srgb, ' . $text . ' 15%, var(--color-bg-primary))';
    }

    if ( ! $vars ) {
        return '';
    }

    $css = 'body.lf-has-custom-colours{';
    foreach ( $vars as $name => $value ) {
        $css .= $name . ':' . $value . ';';
    }
    return $css . '}';
}

/**
 * Stored value without the legacy/default fallbacks (colour overrides have no legacy).
 */
function langgam_fikir_raw_option( $key ) {
    $options = get_option( LANGGAM_FIKIR_OPTION, array() );
    return ( is_array( $options ) && isset( $options[ $key ] ) ) ? $options[ $key ] : '';
}

/**
 * Add scheme, font and custom-colour classes to <body>.
 */
function langgam_fikir_body_classes( $classes ) {
    $scheme = langgam_fikir_get_option( 'color_scheme' );
    if ( ! array_key_exists( $scheme, langgam_fikir_get_schemes() ) ) {
        $scheme = 'earth';
    }
    $classes[] = 'lf-scheme-' . $scheme;

    $heading = langgam_fikir_get_option( 'font_heading' );
    if ( 'sabon' !== $heading ) {
        $classes[] = 'lf-heading-' . sanitize_html_class( $heading );
    }

    $body = langgam_fikir_get_option( 'font_body' );
    if ( 'inter' !== $body ) {
        $classes[] = 'lf-body-' . sanitize_html_class( $body );
    }

    if ( 'plain' === langgam_fikir_get_option( 'font_display' ) ) {
        $classes[] = 'lf-display-plain';
    }

    if ( '' !== langgam_fikir_custom_colour_css() ) {
        $classes[] = 'lf-has-custom-colours';
    }

    return $classes;
}
add_filter( 'body_class', 'langgam_fikir_body_classes' );

/**
 * Print the Dark/Light hint so form controls and scrollbars match the scheme.
 */
function langgam_fikir_color_scheme_meta() {
    if ( 'midnight' === langgam_fikir_get_option( 'color_scheme' ) ) {
        echo '<meta name="color-scheme" content="dark">' . "\n";
    } elseif ( 'auto' === langgam_fikir_get_option( 'color_scheme' ) ) {
        echo '<meta name="color-scheme" content="light dark">' . "\n";
    }
}
add_action( 'wp_head', 'langgam_fikir_color_scheme_meta', 1 );

/*--------------------------------------------------------------
# Admin: Appearance > Theme Settings
--------------------------------------------------------------*/

function langgam_fikir_add_settings_page() {
    add_theme_page(
        __( 'Theme Settings', 'langgam-fikir' ),
        __( 'Theme Settings', 'langgam-fikir' ),
        'manage_options',
        LANGGAM_FIKIR_PAGE,
        'langgam_fikir_render_settings_page'
    );
}
add_action( 'admin_menu', 'langgam_fikir_add_settings_page' );

function langgam_fikir_enqueue_settings_assets( $hook ) {
    if ( 'appearance_page_' . LANGGAM_FIKIR_PAGE !== $hook ) {
        return;
    }

    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();

    wp_enqueue_media();
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'langgam-fikir-settings', $uri . '/assets/css/settings.css', array( 'wp-color-picker' ), filemtime( $dir . '/assets/css/settings.css' ) );
    wp_enqueue_script( 'langgam-fikir-settings', $uri . '/assets/js/settings.js', array( 'wp-color-picker' ), filemtime( $dir . '/assets/js/settings.js' ), true );

    $palettes = array();
    foreach ( langgam_fikir_get_schemes() as $slug => $scheme ) {
        $palettes[ $slug ] = $scheme['palette'];
    }
    wp_localize_script(
        'langgam-fikir-settings',
        'langgamFikirSettings',
        array(
            'palettes' => $palettes,
            'i18n'     => array(
                'chooseTitle'  => __( 'Choose image', 'langgam-fikir' ),
                'chooseButton' => __( 'Use this image', 'langgam-fikir' ),
            ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'langgam_fikir_enqueue_settings_assets' );

/**
 * Render one field's control.
 */
function langgam_fikir_render_field( $key, $field, $value ) {
    $name = LANGGAM_FIKIR_OPTION . '[' . $key . ']';
    $id   = 'lf-' . $key;

    switch ( $field['type'] ) {
        case 'scheme':
            echo '<div class="lf-schemes" role="radiogroup" aria-label="' . esc_attr( $field['label'] ) . '">';
            foreach ( langgam_fikir_get_schemes() as $slug => $scheme ) {
                printf(
                    '<label class="lf-scheme"><input type="radio" name="%1$s" value="%2$s" %3$s><span class="lf-scheme__card"><span class="lf-swatch lf-swatch--%2$s" aria-hidden="true"><i></i><i></i><i></i></span><span class="lf-scheme__name">%4$s</span><span class="lf-scheme__note">%5$s</span></span></label>',
                    esc_attr( $name ),
                    esc_attr( $slug ),
                    checked( $value, $slug, false ),
                    esc_html( $scheme['label'] ),
                    esc_html( $scheme['note'] )
                );
            }
            echo '</div>';
            break;

        case 'color':
            printf(
                '<input type="text" id="%1$s" name="%2$s" value="%3$s" class="lf-color" data-preview="%4$s" data-default-color="">',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value ),
                esc_attr( $key )
            );
            break;

        case 'select':
            printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );
            foreach ( $field['choices'] as $choice => $label ) {
                printf( '<option value="%s" %s>%s</option>', esc_attr( $choice ), selected( $value, $choice, false ), esc_html( $label ) );
            }
            echo '</select>';
            break;

        case 'textarea':
            printf(
                '<textarea id="%s" name="%s" rows="%d" class="large-text">%s</textarea>',
                esc_attr( $id ),
                esc_attr( $name ),
                isset( $field['rows'] ) ? (int) $field['rows'] : 3,
                esc_textarea( $value )
            );
            break;

        case 'image':
            $src = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
            printf(
                '<div class="lf-image" data-lf-image><input type="hidden" id="%1$s" name="%2$s" value="%3$s"><div class="lf-image__preview" %4$s><img src="%5$s" alt=""></div><button type="button" class="button lf-image__choose">%6$s</button> <button type="button" class="button-link lf-image__remove" %7$s>%8$s</button></div>',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value ? $value : '' ),
                $src ? '' : 'hidden',
                esc_url( $src ),
                esc_html__( 'Choose image', 'langgam-fikir' ),
                $src ? '' : 'hidden',
                esc_html__( 'Remove', 'langgam-fikir' )
            );
            break;

        case 'page':
            wp_dropdown_pages(
                array(
                    'name'              => $name,
                    'id'                => $id,
                    'selected'          => (int) $value,
                    'show_option_none'  => __( '— Not set —', 'langgam-fikir' ),
                    'option_none_value' => 0,
                )
            );
            break;

        case 'number':
            printf(
                '<input type="number" id="%s" name="%s" value="%s" min="%d" max="%d" step="1" class="small-text">',
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value ),
                (int) $field['min'],
                (int) $field['max']
            );
            break;

        default:
            printf(
                '<input type="%s" id="%s" name="%s" value="%s" class="regular-text">',
                esc_attr( in_array( $field['type'], array( 'email', 'url' ), true ) ? $field['type'] : 'text' ),
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value )
            );
    }

    if ( ! empty( $field['desc'] ) ) {
        echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
    }
}

function langgam_fikir_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $tabs = langgam_fikir_get_settings_fields();
    ?>
    <div class="wrap lf-settings">
        <h1><?php esc_html_e( 'Theme Settings', 'langgam-fikir' ); ?></h1>
        <?php settings_errors(); ?>

        <form method="post" action="options.php" class="lf-settings__form">
            <?php settings_fields( 'langgam_fikir_settings' ); ?>

            <div class="lf-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Settings sections', 'langgam-fikir' ); ?>">
                <?php foreach ( $tabs as $slug => $tab ) : ?>
                    <button type="button" role="tab" class="lf-tabs__tab" id="lf-tab-<?php echo esc_attr( $slug ); ?>" aria-controls="lf-panel-<?php echo esc_attr( $slug ); ?>" aria-selected="false" data-tab="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $tab['label'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ( $tabs as $slug => $tab ) : ?>
                <section class="lf-panel" role="tabpanel" id="lf-panel-<?php echo esc_attr( $slug ); ?>" aria-labelledby="lf-tab-<?php echo esc_attr( $slug ); ?>" data-panel="<?php echo esc_attr( $slug ); ?>">
                    <h2 class="lf-panel__title"><?php echo esc_html( $tab['label'] ); ?></h2>

                    <?php if ( 'colours' === $slug ) : ?>
                        <div class="lf-preview" id="lf-preview" aria-hidden="true">
                            <p class="lf-preview__eyebrow"><?php esc_html_e( 'Preview', 'langgam-fikir' ); ?></p>
                            <h3 class="lf-preview__title"><?php esc_html_e( 'Ideas that cross borders', 'langgam-fikir' ); ?></h3>
                            <p class="lf-preview__text"><?php esc_html_e( 'Critical, thought-provoking works for readers who seek truth.', 'langgam-fikir' ); ?></p>
                            <span class="lf-preview__button"><?php esc_html_e( 'View books', 'langgam-fikir' ); ?></span>
                        </div>
                    <?php endif; ?>

                    <table class="form-table" role="presentation">
                        <?php foreach ( $tab['fields'] as $key => $field ) : ?>
                            <?php
                            $value = 'color' === $field['type'] ? (string) langgam_fikir_raw_option( $key ) : langgam_fikir_get_option( $key );
                            ?>
                            <tr>
                                <th scope="row">
                                    <?php if ( 'scheme' === $field['type'] ) : ?>
                                        <?php echo esc_html( $field['label'] ); ?>
                                    <?php else : ?>
                                        <label for="lf-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                    <?php endif; ?>
                                </th>
                                <td><?php langgam_fikir_render_field( $key, $field, $value ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </section>
            <?php endforeach; ?>

            <?php submit_button( __( 'Save changes', 'langgam-fikir' ) ); ?>
        </form>
    </div>
    <?php
}
