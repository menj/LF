<?php
/**
 * Theme Options: renderers for the schema-driven tabs and cards
 *
 * Prints the cards defined in inc/options-schema.php using the same markup
 * (lf-card, lf-field, lf-help) as the hand-written Theme Options panels.
 *
 * @package Langgam_Fikir
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add the new tabs (design, pages, labels) to the tab list, after Homepage.
 *
 * @param array $tabs Existing tabs: slug => array( label, icon ).
 * @return array
 */
function langgam_fikir_extend_option_tabs( $tabs ) {
    $new = array();
    foreach ( langgam_fikir_extra_options_schema() as $slug => $tab ) {
        if ( empty( $tab['existing'] ) ) {
            $new[ $slug ] = array( 'label' => $tab['label'], 'icon' => $tab['icon'] );
        }
    }

    // Order: Homepage, Design, About & Contact Pages, Contact & Footer, Labels & 404, then the rest.
    $ordered = array();
    foreach ( $tabs as $slug => $tab ) {
        $ordered[ $slug ] = $tab;
        if ( 'homepage' === $slug ) {
            foreach ( array( 'design', 'pages' ) as $extra ) {
                if ( isset( $new[ $extra ] ) ) {
                    $ordered[ $extra ] = $new[ $extra ];
                }
            }
        }
        if ( 'contact' === $slug && isset( $new['labels'] ) ) {
            $ordered['labels'] = $new['labels'];
        }
    }
    return $ordered;
}

/**
 * Print one field's control.
 */
function langgam_fikir_render_extra_field( $key, $field, $value ) {
    $name = 'langgam_fikir_options[' . $key . ']';
    $id   = 'lf_' . $key;

    switch ( $field['type'] ) {
        case 'scheme':
            echo '<div class="lf-schemes" role="radiogroup" aria-label="' . esc_attr( $field['label'] ) . '">';
            foreach ( langgam_fikir_color_schemes() as $slug => $scheme ) {
                printf(
                    '<label class="lf-scheme"><input type="radio" name="%1$s" value="%2$s" %3$s><span class="lf-scheme-card"><span class="lf-scheme-swatch" aria-hidden="true">%4$s</span><span class="lf-scheme-name">%5$s</span><span class="lf-scheme-note">%6$s</span></span></label>',
                    esc_attr( $name ),
                    esc_attr( $slug ),
                    checked( $value, $slug, false ),
                    // Swatch chips carry their colour in a data attribute; design styling is applied by theme-options.js.
                    implode( '', array_map( static function ( $c ) {
                        return '<i data-color="' . esc_attr( $c ) . '"></i>';
                    }, $scheme['colors'] ) ), // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the callback
                    esc_html( $scheme['label'] ),
                    esc_html( $scheme['note'] )
                );
            }
            echo '</div>';
            break;

        case 'color':
            printf(
                '<span class="lf-color"><input type="color" class="lf-color-pick" value="%1$s" aria-label="%2$s"><input type="text" id="%3$s" name="%4$s" value="%5$s" class="lf-color-text" maxlength="7" placeholder="#RRGGBB" pattern="#[0-9A-Fa-f]{6}"><button type="button" class="button-link lf-color-clear">%6$s</button></span>',
                esc_attr( $value ? $value : '#cccccc' ),
                esc_attr( $field['label'] ),
                esc_attr( $id ),
                esc_attr( $name ),
                esc_attr( $value ),
                esc_html__( 'Clear', 'langgam-fikir' )
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
        echo '<p class="lf-help">' . esc_html( $field['desc'] ) . '</p>';
    }
}

/**
 * Print the cards of one tab. For existing tabs this appends to the hand-written cards.
 *
 * @param string $slug Tab slug.
 * @param array  $opts Current options merged with defaults.
 */
function langgam_fikir_render_extra_cards( $slug, $opts ) {
    $schema = langgam_fikir_extra_options_schema();
    if ( empty( $schema[ $slug ] ) ) {
        return;
    }

    foreach ( $schema[ $slug ]['cards'] as $card ) {
        echo '<div class="lf-card">';
        echo '<h2 class="lf-card-title">' . esc_html( $card['title'] ) . '</h2>';
        if ( ! empty( $card['desc'] ) ) {
            echo '<p class="lf-card-desc">' . esc_html( $card['desc'] ) . '</p>';
        }

        foreach ( $card['fields'] as $key => $field ) {
            $value = isset( $opts[ $key ] ) ? $opts[ $key ] : $field['default'];
            echo '<div class="lf-field">';
            if ( 'scheme' !== $field['type'] ) {
                printf( '<label for="lf_%s">%s</label>', esc_attr( $key ), esc_html( $field['label'] ) );
            } else {
                echo '<span class="lf-label">' . esc_html( $field['label'] ) . '</span>';
            }
            langgam_fikir_render_extra_field( $key, $field, $value );
            echo '</div>';
        }
        echo '</div>';
    }
}

/**
 * Print the panels of the new tabs.
 *
 * @param string $active_tab Active tab slug.
 * @param array  $opts       Current options merged with defaults.
 */
function langgam_fikir_render_extra_panels( $active_tab, $opts ) {
    foreach ( langgam_fikir_extra_options_schema() as $slug => $tab ) {
        if ( ! empty( $tab['existing'] ) ) {
            continue;
        }
        printf(
            '<div class="lf-panel %s" data-panel="%s" role="tabpanel">',
            $active_tab === $slug ? 'lf-panel--active' : '',
            esc_attr( $slug )
        );
        langgam_fikir_render_extra_cards( $slug, $opts );
        echo '</div>';
    }
}
