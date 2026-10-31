<?php
/**
 * Template Name: Contact Page
 * Template for displaying the Contact Us page
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main contact-page">
    
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
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </div>
                        
                        <?php
                        // Display form submission message if exists
                        $contact_state = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
                        $state_classes = array( 'success' => 'success', 'throttled' => 'error', 'error' => 'error' );
                        if ( isset( $state_classes[ $contact_state ] ) ) :
                            ?>
                            <div class="form-message <?php echo esc_attr( $state_classes[ $contact_state ] ); ?>" role="status">
                                <?php echo esc_html( langgam_fikir_get_option( 'contact_msg_' . $contact_state ) ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( '' !== trim( wp_strip_all_tags( get_the_content() ) ) ) : ?>
                            <div class="entry-content contact-intro"><?php the_content(); ?></div>
                        <?php endif; ?>
                        
                        <?php
                        // Get book information from URL parameters
                        $book_id = isset( $_GET['book_id'] ) ? intval( $_GET['book_id'] ) : 0;
                        $book_title = isset( $_GET['book_title'] ) ? sanitize_text_field( urldecode( $_GET['book_title'] ) ) : '';
                        
                        // Build pre-filled subject and message
                        $prefilled_subject = '';
                        $prefilled_message = '';
                        
                        if ( $book_id && $book_title ) {
                            $prefilled_subject = sprintf( __( 'Book Order - %s', 'langgam-fikir' ), $book_title );
                            
                            // Get book details from post meta
                            $author = get_post_meta( $book_id, '_book_author', true );
                            $year = get_post_meta( $book_id, '_book_year', true );
                            $price = get_post_meta( $book_id, '_book_price', true );
                            
                            $prefilled_message = sprintf(
                                __( "I am interested in ordering:\n\nBook: %s", 'langgam-fikir' ),
                                $book_title
                            );
                            
                            if ( $author ) {
                                $prefilled_message .= sprintf( __( "\nAuthor: %s", 'langgam-fikir' ), $author );
                            }
                            if ( $year ) {
                                $prefilled_message .= sprintf( __( "\nYear: %s", 'langgam-fikir' ), $year );
                            }
                            if ( $price ) {
                                $prefilled_message .= sprintf( __( "\nPrice: RM %s", 'langgam-fikir' ), $price );
                            }
                            
                            $prefilled_message .= __( "\n\nQuantity: \n\nPlease provide me with ordering details.\n\nThank you.", 'langgam-fikir' );
                        }
                        ?>
                        
                        <form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <input type="hidden" name="action" value="langgam_fikir_contact_form">
                            <?php wp_nonce_field( 'langgam_fikir_contact', 'contact_nonce' ); ?>
                            
                            <p class="contact-website-field" aria-hidden="true">
                                <label for="contact_website"><?php _e( 'Leave this field empty', 'langgam-fikir' ); ?></label>
                                <input type="text" id="contact_website" name="contact_website" value="" tabindex="-1" autocomplete="off">
                            </p>
                            
                            <div class="form-row-dual">
                                <div class="form-field">
                                    <input type="text" 
                                           name="contact_name" 
                                           class="form-input" 
                                           placeholder="<?php echo esc_attr( langgam_fikir_get_option( 'contact_ph_name' ) ); ?>" aria-label="<?php echo esc_attr( langgam_fikir_get_option( 'contact_ph_name' ) ); ?>"
                                           required>
                                </div>
                                
                                <div class="form-field">
                                    <input type="email" 
                                           name="contact_email" 
                                           class="form-input" 
                                           placeholder="<?php echo esc_attr( langgam_fikir_get_option( 'contact_ph_email' ) ); ?>" aria-label="<?php echo esc_attr( langgam_fikir_get_option( 'contact_ph_email' ) ); ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="form-field">
                                <input type="text" 
                                       name="contact_subject" 
                                       class="form-input" 
                                       placeholder="<?php echo esc_attr( langgam_fikir_get_option( 'contact_ph_subject' ) ); ?>" aria-label="<?php echo esc_attr( langgam_fikir_get_option( 'contact_ph_subject' ) ); ?>"
                                       value="<?php echo esc_attr( $prefilled_subject ); ?>"
                                       required>
                            </div>
                            
                            <div class="form-field">
                                <textarea name="contact_message" 
                                          class="form-textarea" 
                                          rows="8"
                                          placeholder="<?php echo esc_attr( langgam_fikir_get_option( 'contact_ph_message' ) ); ?>" aria-label="<?php echo esc_attr( langgam_fikir_get_option( 'contact_ph_message' ) ); ?>"
                                          required><?php echo esc_textarea( $prefilled_message ); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary form-submit">
                                <?php echo esc_html( langgam_fikir_get_option( 'contact_submit_label' ) ); ?>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Contact Info Section (Right - 40%) -->
                <div class="contact-info-area">
                    <div class="contact-info-inner">
                        <?php
                        // Contact details come from Theme Settings (older per-page values still work as a fallback)
                        $contact_description = langgam_fikir_meta_or_option( get_the_ID(), '_contact_description', 'contact_description' );
                        $contact_email = langgam_fikir_meta_or_option( get_the_ID(), '_contact_email', 'contact_email' );
                        $contact_phone = langgam_fikir_meta_or_option( get_the_ID(), '_contact_phone', 'contact_phone' );
                        $instagram_url = langgam_fikir_meta_or_option( get_the_ID(), '_contact_instagram', 'contact_instagram' );
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
                        
                        <?php if ( ! empty( $instagram_url ) ) : ?>
                            <div class="contact-social">
                                <p class="social-label"><?php echo esc_html( langgam_fikir_get_option( 'contact_follow_label' ) ); ?></p>
                                <a href="<?php echo esc_url( $instagram_url ); ?>" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
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
