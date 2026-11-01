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
                        $has_social = (bool) langgam_fikir_social_profiles( array( 'instagram' => $social_instagram ) );
                        if ( $has_social ) : ?>
                            <div class="contact-social">
                                <p class="social-label"><?php echo esc_html( langgam_fikir_text( 'contact_follow_label' ) ); ?></p>
                                <?php langgam_fikir_render_social_links( 'contact', array( 'instagram' => $social_instagram ) ); ?>
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
