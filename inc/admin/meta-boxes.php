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
 */
function langgam_fikir_book_details_callback( $post ) {
    wp_nonce_field( 'langgam_fikir_book_meta', 'langgam_fikir_book_meta_nonce' );
    
    $subtitle   = get_post_meta( $post->ID, '_book_subtitle', true );
    $author     = get_post_meta( $post->ID, '_book_author', true );
    $editor     = get_post_meta( $post->ID, '_book_editor', true );
    $translator = get_post_meta( $post->ID, '_book_translator', true );
    $publisher  = get_post_meta( $post->ID, '_book_publisher', true );
    $year       = get_post_meta( $post->ID, '_book_year', true );
    $isbn       = get_post_meta( $post->ID, '_book_isbn', true );
    $status     = get_post_meta( $post->ID, '_book_status', true );
    if ( empty( $status ) ) {
        $status = 'published';
    }
    $price      = get_post_meta( $post->ID, '_book_price', true );
    $language   = get_post_meta( $post->ID, '_book_language', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="book_subtitle"><?php _e( 'Subtitle', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="text" id="book_subtitle" name="book_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" class="large-text">
            </td>
        </tr>
        <tr>
            <th><label for="book_author"><?php _e( 'Author', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="text" id="book_author" name="book_author" value="<?php echo esc_attr( $author ); ?>" class="large-text">
            </td>
        </tr>
        <tr>
            <th><label for="book_editor"><?php _e( 'Editor', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="text" id="book_editor" name="book_editor" value="<?php echo esc_attr( $editor ); ?>" class="large-text" placeholder="<?php esc_attr_e( 'Leave blank if not applicable', 'langgam-fikir' ); ?>">
            </td>
        </tr>
        <tr>
            <th><label for="book_translator"><?php _e( 'Translator', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="text" id="book_translator" name="book_translator" value="<?php echo esc_attr( $translator ); ?>" class="large-text" placeholder="<?php esc_attr_e( 'Leave blank if not applicable', 'langgam-fikir' ); ?>">
            </td>
        </tr>
        <tr>
            <th><label for="book_publisher"><?php _e( 'Publisher', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="text" id="book_publisher" name="book_publisher" value="<?php echo esc_attr( $publisher ); ?>" class="large-text" placeholder="<?php esc_attr_e( 'Leave blank if not applicable', 'langgam-fikir' ); ?>">
            </td>
        </tr>
        <tr>
            <th><label for="book_year"><?php _e( 'Publication Year', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="number" id="book_year" name="book_year" value="<?php echo esc_attr( $year ); ?>" min="1900" max="2100">
            </td>
        </tr>
        <tr>
            <th><label for="book_isbn"><?php _e( 'ISBN', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="text" id="book_isbn" name="book_isbn" value="<?php echo esc_attr( $isbn ); ?>" class="large-text" placeholder="978-629-96135-0-3">
            </td>
        </tr>
        <tr>
            <th><label for="book_language"><?php _e( 'Language', 'langgam-fikir' ); ?></label></th>
            <td>
                <?php
                $lang_choices = function_exists( 'langgam_fikir_book_language_choices' )
                    ? langgam_fikir_book_language_choices()
                    : array();
                ?>
                <select id="book_language" name="book_language" class="regular-text">
                    <option value=""><?php esc_html_e( '— Use site default —', 'langgam-fikir' ); ?></option>
                    <?php foreach ( $lang_choices as $code => $label ) : ?>
                        <option value="<?php echo esc_attr( $code ); ?>" <?php selected( $language, $code ); ?>>
                            <?php echo esc_html( $label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">
                    <?php esc_html_e( 'The language this book is written in. Used for the Language row on the book page and the Schema.org inLanguage property. If the HrefMaster plugin is active and sets a per-page lang for this book, that overrides this setting.', 'langgam-fikir' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Status', 'langgam-fikir' ); ?></label></th>
            <td>
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
            </td>
        </tr>
        <tr>
            <th><label for="book_price"><?php _e( 'Price (RM)', 'langgam-fikir' ); ?></label></th>
            <td>
                <input type="number" id="book_price" name="book_price" value="<?php echo esc_attr( $price ); ?>" step="0.01" min="0">
            </td>
        </tr>
    </table>
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
