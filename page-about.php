<?php
/**
 * Template Name: About Page
 * Template for displaying the About Us page
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main about-page">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <div class="about-layout">
                <!-- Logo Section -->
                <div class="about-logo-section">
                    <div class="about-logo-container">
                        <?php
                        // Check if custom logo is set in About page meta
                        $custom_logo_id = get_post_meta( get_the_ID(), '_about_logo_id', true );
                        
                        if ( $custom_logo_id ) {
                            // Use custom About page logo
                            echo wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => 'about-logo-image' ) );
                        } elseif ( has_custom_logo() ) {
                            // Use site logo
                            $custom_logo_id = get_theme_mod( 'custom_logo' );
                            echo wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => 'about-logo-image' ) );
                        } else {
                            // Fallback to default logo URL
                            ?>
                            <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2025/08/LF-Logo-No-BG.png' ) ); ?>" 
                                 alt="<?php bloginfo( 'name' ); ?>" 
                                 class="about-logo-image">
                            <?php
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="about-content-area">
                    <div class="about-content-inner">
                        <div class="page-title-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </div>
                        
                        <div class="about-lead">
                            <p><?php _e( 'Langgam Fikir is an independent publishing house dedicated to bringing profound narratives and critical thought to the forefront of the Malaysian literary scene.', 'langgam-fikir' ); ?></p>
                        </div>
                        
                        <div class="about-text">
                            <h2><?php _e( 'Our Mission', 'langgam-fikir' ); ?></h2>
                            <?php 
                            $content = get_the_content();
                            if ( empty( trim( strip_tags( $content ) ) ) ) {
                                ?>
                                <p><?php _e( 'Founded in 2015, we operate with a singular vision: to publish books that matter. In an age of fleeting digital content, we believe in the enduring power of the physical book as a technology for preserving deep thought.', 'langgam-fikir' ); ?></p>
                                
                                <p><?php _e( 'We focus on translating key works of global philosophy and literature into Malay, while also nurturing local voices that speak with clarity and courage on contemporary issues.', 'langgam-fikir' ); ?></p>
                                <?php
                            } else {
                                the_content();
                            }
                            ?>
                            
                            <div class="mission-cards">
                                <div class="mission-card">
                                    <h3><?php _e( 'Translation', 'langgam-fikir' ); ?></h3>
                                    <p><?php _e( 'Bridging cultures by bringing world classics to local readers with high-fidelity translations.', 'langgam-fikir' ); ?></p>
                                </div>
                                <div class="mission-card">
                                    <h3><?php _e( 'Original Works', 'langgam-fikir' ); ?></h3>
                                    <p><?php _e( 'Providing a platform for Malaysian thinkers, poets, and storytellers to document our times.', 'langgam-fikir' ); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                        // Contact CTA Section
                        $contact_heading = langgam_fikir_meta_or_option( get_the_ID(), '_about_contact_heading', 'about_contact_heading' );
                        $contact_phone   = langgam_fikir_meta_or_option( get_the_ID(), '_about_contact_phone', 'about_contact_phone' );
                        
                        if ( empty( $contact_heading ) ) {
                            $contact_heading = __( 'Have any questions?', 'langgam-fikir' );
                        }
                        if ( empty( $contact_phone ) ) {
                            $contact_phone = '+60 1234 1234';
                        }
                        ?>
                        
                        <div class="about-contact-cta">
                            <p class="contact-prompt"><?php echo esc_html( $contact_heading ); ?></p>
                            <p class="contact-action">
                                <a href="<?php echo esc_url( home_url( '/contact-langgam-fikir/' ) ); ?>" class="contact-link">
                                    <?php _e( 'Contact us!', 'langgam-fikir' ); ?>
                                </a>
                            </p>
                            
                            <a href="tel:<?php echo esc_attr( str_replace( ' ', '', $contact_phone ) ); ?>" class="contact-phone-large">
                                <?php echo esc_html( $contact_phone ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </article>
        
    <?php endwhile; ?>
    
</main>

<?php
get_footer();
