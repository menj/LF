<?php
/**
 * Template Name: Contact Page
 * Template for displaying the Contact Us page
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main contact-page" tabindex="-1">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <div class="contact-layout">
                <!-- Form Section (Left - 60%) -->
                <div class="contact-form-area">
                    <div class="contact-form-inner">
                        <div class="page-title-header">
                            <?php if ( function_exists( "langgam_fikir_breadcrumbs" ) ) { langgam_fikir_breadcrumbs(); } ?>
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </div>
                        
                        <?php
                        // Display form submission message if exists
                        $contact_status = isset( $_GET['contact'] ) ? sanitize_key( $_GET['contact'] ) : '';
                        if ( 'success' === $contact_status ) :
                            ?>
                            <div class="form-message success">
                                <?php echo esc_html( langgam_fikir_text( 'contact_msg_success' ) ); ?>
                            </div>
                        <?php elseif ( 'error' === $contact_status ) : ?>
                            <div class="form-message error">
                                <?php echo esc_html( langgam_fikir_text( 'contact_msg_error' ) ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        // Get book information from URL parameters
                        $book_id = isset( $_GET['book_id'] ) ? intval( $_GET['book_id'] ) : 0;
                        $book_title = isset( $_GET['book_title'] ) ? sanitize_text_field( urldecode( $_GET['book_title'] ) ) : '';
                        
                        // Build pre-filled subject and message
                        $prefilled_subject = '';
                        $prefilled_message = '';
                        
                        if ( $book_id && $book_title ) {
                            $order_tokens = array(
                                '{title}'  => $book_title,
                                '{author}' => (string) get_post_meta( $book_id, '_book_author', true ),
                                '{year}'   => (string) get_post_meta( $book_id, '_book_year', true ),
                                '{price}'  => (string) get_post_meta( $book_id, '_book_price', true ),
                            );
                            $prefilled_subject = strtr( langgam_fikir_text( 'contact_order_subject' ), $order_tokens );
                            
                            // Drop lines whose author, year or price token has no value.
                            $order_lines = array();
                            foreach ( preg_split( '/\r\n|\r|\n/', langgam_fikir_text( 'contact_order_message' ) ) as $order_line ) {
                                foreach ( array( '{author}', '{year}', '{price}' ) as $optional_token ) {
                                    if ( false !== strpos( $order_line, $optional_token ) && '' === $order_tokens[ $optional_token ] ) {
                                        continue 2;
                                    }
                                }
                                $order_lines[] = strtr( $order_line, $order_tokens );
                            }
                            $prefilled_message = implode( "\n", $order_lines );
                        }
                        ?>
                        
                        <form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"<?php langgam_fikir_webmcp_form_attrs( 'contact-publisher', __( 'Compose a message to the publisher. Fill in the fields; the visitor reviews and sends it.', 'langgam-fikir' ) ); ?>>
                            <input type="hidden" name="action" value="langgam_fikir_contact">
                            <?php wp_nonce_field( 'langgam_fikir_contact', 'contact_nonce' ); ?>
                            <input type="hidden" name="_lf_form_ts" value="<?php echo absint( time() ); ?>">
                            <div class="lf-hp" aria-hidden="true">
                                <label for="website_url"><?php esc_html_e( 'Website', 'langgam-fikir' ); ?></label>
                                <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off"<?php langgam_fikir_webmcp_param_attr( __( 'Leave this field empty.', 'langgam-fikir' ) ); ?>>
                            </div>
                            
                            <div class="form-row-dual">
                                <div class="form-field">
                                    <input type="text" 
                                           name="contact_name" 
                                           class="form-input" 
                                           placeholder="<?php echo esc_attr( langgam_fikir_text( 'contact_ph_name' ) ); ?>"
                                           required<?php langgam_fikir_webmcp_param_attr( __( "The sender's full name.", 'langgam-fikir' ) ); ?>>
                                </div>
                                
                                <div class="form-field">
                                    <input type="email" 
                                           name="contact_email" 
                                           class="form-input" 
                                           placeholder="<?php echo esc_attr( langgam_fikir_text( 'contact_ph_email' ) ); ?>"
                                           required<?php langgam_fikir_webmcp_param_attr( __( "The sender's email address for the reply.", 'langgam-fikir' ) ); ?>>
                                </div>
                            </div>
                            
                            <div class="form-field">
                                <input type="text" 
                                       name="contact_subject" 
                                       class="form-input" 
                                       placeholder="<?php echo esc_attr( langgam_fikir_text( 'contact_ph_subject' ) ); ?>"
                                       value="<?php echo esc_attr( $prefilled_subject ); ?>"
                                       required<?php langgam_fikir_webmcp_param_attr( __( 'Short subject line for the message.', 'langgam-fikir' ) ); ?>>
                            </div>
                            
                            <div class="form-field">
                                <textarea name="contact_message" 
                                          class="form-textarea" 
                                          rows="8"
                                          placeholder="<?php echo esc_attr( langgam_fikir_text( 'contact_ph_message' ) ); ?>"
                                          required<?php langgam_fikir_webmcp_param_attr( __( 'The message body.', 'langgam-fikir' ) ); ?>><?php echo esc_textarea( $prefilled_message ); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary form-submit">
                                <?php echo esc_html( langgam_fikir_text( 'contact_submit_label' ) ); ?>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Contact Info Section (Right - 40%) -->
                <div class="contact-info-area">
                    <div class="contact-info-inner">
                        <?php
                        // Get contact info from page meta fields
                        $contact_description = get_post_meta( get_the_ID(), '_contact_description', true );
                        $contact_email = get_post_meta( get_the_ID(), '_contact_email', true );
                        $contact_phone = get_post_meta( get_the_ID(), '_contact_phone', true );

                        // Social links — page meta overrides Theme Options for Instagram
                        $instagram_meta = get_post_meta( get_the_ID(), '_contact_instagram', true );
                        $social_instagram = ! empty( $instagram_meta ) ? $instagram_meta : langgam_fikir_get_option( 'social_instagram' );
                        $social_facebook  = langgam_fikir_get_option( 'social_facebook' );
                        $social_twitter   = langgam_fikir_get_option( 'social_twitter' );
                        $social_whatsapp  = langgam_fikir_get_option( 'social_whatsapp' );
                        
                        // Defaults if not set
                        if ( empty( $contact_description ) ) {
                            $contact_description = langgam_fikir_text( 'contact_description' );
                        }
                        // Public email and phone: the page's own value, then Footer Contact in Theme Options.
                        // No placeholder values: an empty one is simply not shown.
                        if ( empty( $contact_email ) ) {
                            $contact_email = langgam_fikir_get_option( 'footer_email' );
                        }
                        if ( empty( $contact_phone ) ) {
                            $contact_phone = langgam_fikir_get_option( 'footer_phone' );
                        }
                        ?>
                        
                        <?php if ( $contact_description ) : ?>
                        <div class="contact-description">
                            <?php echo langgam_fikir_paragraphs( $contact_description ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ( $contact_email || $contact_phone ) : ?>
                        <div class="contact-details">
                            <?php if ( $contact_email ) : ?>
                            <div class="contact-detail-item">
                                <a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="contact-email">
                                    <?php echo esc_html( $contact_email ); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ( $contact_phone ) : ?>
                            <div class="contact-detail-item">
                                <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>" class="contact-phone">
                                    <?php echo esc_html( $contact_phone ); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php
                        $has_social = $social_instagram || $social_facebook || $social_twitter || $social_whatsapp;
                        if ( $has_social ) : ?>
                            <div class="contact-social">
                                <p class="social-label"><?php echo esc_html( langgam_fikir_text( 'contact_follow_label' ) ); ?></p>
                                <div class="contact-social-icons">
                                    <?php if ( $social_instagram ) : ?>
                                        <a href="<?php echo esc_url( $social_instagram ); ?>" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( $social_facebook ) : ?>
                                        <a href="<?php echo esc_url( $social_facebook ); ?>" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( $social_twitter ) : ?>
                                        <a href="<?php echo esc_url( $social_twitter ); ?>" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( $social_whatsapp ) : ?>
                                        <a href="<?php echo esc_url( $social_whatsapp ); ?>" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
        </article>
        
    <?php endwhile; ?>
    
</main>

<?php
get_footer();
