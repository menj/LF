<?php
/**
 * Admin Meta Box Callbacks
 *
 * Contains the display and rendering functions for meta boxes
 *
 * @package Langgam_Fikir
 * @since 1.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Book Details Meta Box Callback
 *
 * Sections built from inc/book-fields.php (title & credits, publication, physical details),
 * then availability (status picker, price, language). A short hint lists what is still empty.
 */
function langgam_fikir_book_details_callback( $post ) {
    wp_nonce_field( 'langgam_fikir_book_meta', 'langgam_fikir_book_meta_nonce' );

    $status = get_post_meta( $post->ID, '_book_status', true );
    if ( empty( $status ) ) {
        $status = 'published';
    }
    $price    = get_post_meta( $post->ID, '_book_price', true );
    $language = get_post_meta( $post->ID, '_book_language', true );

    // What is still missing? Shown as a hint so a thin catalogue entry is easy to spot.
    $checks = array(
        __( 'Cover image', 'langgam-fikir' ) => has_post_thumbnail( $post ),
        __( 'Excerpt', 'langgam-fikir' )     => '' !== trim( (string) $post->post_excerpt ),
        __( 'Author', 'langgam-fikir' )      => '' !== (string) get_post_meta( $post->ID, '_book_author', true ),
        __( 'Year', 'langgam-fikir' )        => '' !== (string) get_post_meta( $post->ID, '_book_year', true ),
        __( 'ISBN', 'langgam-fikir' )        => '' !== (string) get_post_meta( $post->ID, '_book_isbn', true ),
        __( 'OCLC number', 'langgam-fikir' ) => '' !== (string) get_post_meta( $post->ID, '_book_oclc', true ),
        __( 'Pages', 'langgam-fikir' )       => '' !== (string) get_post_meta( $post->ID, '_book_pages', true ),
        __( 'Format', 'langgam-fikir' )      => '' !== (string) get_post_meta( $post->ID, '_book_format', true ),
        __( 'Category', 'langgam-fikir' )    => has_term( '', 'book-category', $post ),
        __( 'Topics', 'langgam-fikir' )      => has_term( '', 'book-tag', $post ),
    );
    $missing = array_keys( array_filter( $checks, static function ( $done ) {
        return ! $done;
    } ) );
    $filled  = count( $checks ) - count( $missing );
    ?>
    <div class="lf-bd">
        <div class="lf-bd-summary">
            <span class="lf-bd-count"><?php
                /* translators: 1: filled items, 2: total items */
                printf( esc_html__( '%1$d of %2$d details filled', 'langgam-fikir' ), (int) $filled, count( $checks ) );
            ?></span>
            <?php if ( $missing ) : ?>
                <span class="lf-bd-missing">
                    <?php esc_html_e( 'Still empty:', 'langgam-fikir' ); ?>
                    <?php echo esc_html( implode( ', ', $missing ) ); ?>
                </span>
            <?php endif; ?>
        </div>

        <?php foreach ( langgam_fikir_book_field_groups() as $group_id => $group ) : ?>
            <fieldset class="lf-bd-section lf-bd-section--<?php echo esc_attr( $group_id ); ?>">
                <legend class="lf-bd-title"><?php echo esc_html( $group['title'] ); ?></legend>
                <div class="lf-bd-grid">
                    <?php foreach ( $group['fields'] as $key => $field ) : ?>
                        <?php $value = get_post_meta( $post->ID, '_book_' . $key, true ); ?>
                        <div class="lf-bd-field lf-bd-field--<?php echo esc_attr( $field['width'] ); ?>">
                            <label for="book_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                            <?php if ( 'select' === $field['type'] ) : ?>
                                <select id="book_<?php echo esc_attr( $key ); ?>" name="book_<?php echo esc_attr( $key ); ?>">
                                    <option value=""><?php esc_html_e( '— Not set —', 'langgam-fikir' ); ?></option>
                                    <?php foreach ( $field['choices'] as $choice => $choice_label ) : ?>
                                        <option value="<?php echo esc_attr( $choice ); ?>" <?php selected( $value, $choice ); ?>><?php echo esc_html( $choice_label ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else : ?>
                                <input type="<?php echo 'number' === $field['type'] ? 'number' : 'text'; ?>"
                                       id="book_<?php echo esc_attr( $key ); ?>" name="book_<?php echo esc_attr( $key ); ?>"
                                       value="<?php echo esc_attr( $value ); ?>"
                                       <?php echo isset( $field['min'] ) ? 'min="' . (int) $field['min'] . '" max="' . (int) $field['max'] . '"' : ''; ?>
                                       <?php echo ! empty( $field['placeholder'] ) ? 'placeholder="' . esc_attr( $field['placeholder'] ) . '"' : ''; ?>>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>

        <fieldset class="lf-bd-section lf-bd-section--availability">
            <legend class="lf-bd-title"><?php esc_html_e( 'Availability', 'langgam-fikir' ); ?></legend>

            <div class="lf-bd-status">
                <fieldset class="lf-status-group">
                    <legend class="lf-status-legend"><?php _e( 'Available', 'langgam-fikir' ); ?></legend>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="published" <?php checked( $status, 'published' ); ?>>
                        <?php _e( 'Published', 'langgam-fikir' ); ?>
                    </label>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="new-release" <?php checked( $status, 'new-release' ); ?>>
                        <?php _e( 'New Release', 'langgam-fikir' ); ?>
                    </label>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="limited-edition" <?php checked( $status, 'limited-edition' ); ?>>
                        <?php _e( 'Limited Edition', 'langgam-fikir' ); ?>
                    </label>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="digital-only" <?php checked( $status, 'digital-only' ); ?>>
                        <?php _e( 'Digital Only', 'langgam-fikir' ); ?>
                    </label>
                </fieldset>
                <fieldset class="lf-status-group">
                    <legend class="lf-status-legend"><?php _e( 'Pipeline', 'langgam-fikir' ); ?></legend>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="announced" <?php checked( $status, 'announced' ); ?>>
                        <?php _e( 'Announced', 'langgam-fikir' ); ?>
                    </label>
                </fieldset>
                <fieldset class="lf-status-group">
                    <legend class="lf-status-legend"><?php _e( 'Upcoming', 'langgam-fikir' ); ?></legend>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="pre-order" <?php checked( $status, 'pre-order' ); ?>>
                        <?php _e( 'Pre-Order', 'langgam-fikir' ); ?>
                    </label>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="coming-soon" <?php checked( $status, 'coming-soon' ); ?>>
                        <?php _e( 'Coming Soon', 'langgam-fikir' ); ?>
                    </label>
                </fieldset>
                <fieldset class="lf-status-group">
                    <legend class="lf-status-legend"><?php _e( 'Unavailable', 'langgam-fikir' ); ?></legend>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="sold-out" <?php checked( $status, 'sold-out' ); ?>>
                        <?php _e( 'Sold Out', 'langgam-fikir' ); ?>
                    </label>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="reprinting" <?php checked( $status, 'reprinting' ); ?>>
                        <?php _e( 'Reprinting', 'langgam-fikir' ); ?>
                    </label>
                    <label class="lf-status-label">
                        <input type="radio" name="book_status" value="out-of-print" <?php checked( $status, 'out-of-print' ); ?>>
                        <?php _e( 'Out of Print', 'langgam-fikir' ); ?>
                    </label>
                </fieldset>
            </div>

            <div class="lf-bd-grid">
                <div class="lf-bd-field lf-bd-field--half">
                    <label for="book_price"><?php esc_html_e( 'Price (RM)', 'langgam-fikir' ); ?></label>
                    <input type="number" id="book_price" name="book_price" value="<?php echo esc_attr( $price ); ?>" step="0.01" min="0">
                </div>

                <div class="lf-bd-field lf-bd-field--half">
                    <label for="book_language"><?php esc_html_e( 'Language', 'langgam-fikir' ); ?></label>
                    <?php
                    $lang_choices = function_exists( 'langgam_fikir_book_language_choices' )
                        ? langgam_fikir_book_language_choices()
                        : array();
                    ?>
                    <select id="book_language" name="book_language">
                        <option value=""><?php esc_html_e( '— Use site default —', 'langgam-fikir' ); ?></option>
                        <?php foreach ( $lang_choices as $code => $label ) : ?>
                            <option value="<?php echo esc_attr( $code ); ?>" <?php selected( $language, $code ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php esc_html_e( 'The language the book is written in. Shown on the book page and used for Schema.org inLanguage.', 'langgam-fikir' ); ?></p>
                </div>
            </div>
        </fieldset>
    </div>
    <?php
}

/**
 * Contact Meta Box Callback
 */
function langgam_fikir_contact_meta_box_callback( $post ) {
    // Only show on contact pages
    $slug = $post->post_name;
    if ( $slug !== 'contact' && $slug !== 'contact-us' && $slug !== 'contact-langgam-fikir' ) {
        return;
    }
    
    wp_nonce_field( 'langgam_fikir_contact_meta', 'langgam_fikir_contact_nonce' );
    
    $email = get_post_meta( $post->ID, '_contact_email', true );
    $phone = get_post_meta( $post->ID, '_contact_phone', true );
    $whatsapp = get_post_meta( $post->ID, '_contact_whatsapp', true );
    $address = get_post_meta( $post->ID, '_contact_address', true );
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="contact_email"><?php _e( 'Email Address', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr( $email ); ?>" class="large-text">
                <p class="description"><?php _e( 'Contact email for the contact page.', 'langgam-fikir' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="contact_phone"><?php _e( 'Phone Number', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="text" id="contact_phone" name="contact_phone" value="<?php echo esc_attr( $phone ); ?>" class="large-text">
                <p class="description"><?php _e( 'Contact phone number.', 'langgam-fikir' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="contact_whatsapp"><?php _e( 'WhatsApp Number', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="text" id="contact_whatsapp" name="contact_whatsapp" value="<?php echo esc_attr( $whatsapp ); ?>" class="large-text">
                <p class="description"><?php _e( 'WhatsApp number (e.g., 601234567890).', 'langgam-fikir' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="contact_address"><?php _e( 'Address', 'langgam-fikir' ); ?></label></th>
            <td>
                <textarea id="contact_address" name="contact_address" rows="4" class="large-text"><?php echo esc_textarea( $address ); ?></textarea>
                <p class="description"><?php _e( 'Business address.', 'langgam-fikir' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * About Meta Box Callback
 */
function langgam_fikir_about_meta_box_callback( $post ) {
    // Only show on about pages
    $slug = $post->post_name;
    if ( $slug !== 'about' && $slug !== 'about-us' && $slug !== 'about-langgam-fikir' ) {
        return;
    }
    
    wp_nonce_field( 'langgam_fikir_about_meta', 'langgam_fikir_about_nonce' );
    
    $logo_id  = get_post_meta( $post->ID, '_about_logo_id', true );
    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
    
    ?>
    <p>
        <input type="hidden" id="about_logo_id" name="about_logo_id" value="<?php echo esc_attr( $logo_id ); ?>">
        <button type="button" class="button upload-logo-button"><?php _e( 'Upload Logo', 'langgam-fikir' ); ?></button>
        <button type="button" class="button remove-logo-button" <?php echo $logo_id ? '' : 'style="display:none;"'; ?>><?php _e( 'Remove Logo', 'langgam-fikir' ); ?></button>
    </p>
    <p class="description"><?php _e( 'Upload a custom logo for the about page.', 'langgam-fikir' ); ?></p>
    
    <div class="about-logo-preview" <?php echo $logo_id ? '' : 'style="display:none;"'; ?>>
        <?php if ( $logo_url ) : ?>
            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'Logo Preview', 'langgam-fikir' ); ?>" class="lf-logo-preview-img">
        <?php endif; ?>
    </div>
    <?php
}
