<?php
/**
 * Template Name: About Page
 * Template for displaying the About Us page
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main about-page" tabindex="-1">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <div class="about-layout">
                <!-- Logo Section -->
                <div class="about-logo-section" data-parallax-root>
                    <div class="about-logo-container" data-parallax="0.1">
                        <?php
                        // Check if custom logo is set in About page meta
                        $about_logo_id = get_post_meta( get_the_ID(), '_about_logo_id', true );
                        
                        if ( $about_logo_id ) {
                            // Use custom About page logo
                            echo wp_get_attachment_image( $about_logo_id, 'full', false, array( 'class' => 'about-logo-image' ) );
                        } elseif ( has_custom_logo() ) {
                            // Use site logo
                            $site_logo_id = get_theme_mod( 'custom_logo' );
                            echo wp_get_attachment_image( $site_logo_id, 'full', false, array( 'class' => 'about-logo-image' ) );
                        } else {
                            // Text fallback — no hardcoded paths
                            ?>
                            <h2 class="about-logo-text-fallback"><?php bloginfo( 'name' ); ?></h2>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="about-content-area">
                    <div class="about-content-inner">
                        <div class="page-title-header">
                            <?php if ( function_exists( "langgam_fikir_breadcrumbs" ) ) { langgam_fikir_breadcrumbs(); } ?>
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </div>
                        
                        <?php if ( langgam_fikir_text( 'about_lead' ) ) : ?>
                        <div class="about-lead">
                            <?php echo langgam_fikir_paragraphs( langgam_fikir_text( 'about_lead' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="about-text">
                            <?php if ( langgam_fikir_text( 'about_mission_heading' ) ) : ?><h2><?php echo esc_html( langgam_fikir_text( 'about_mission_heading' ) ); ?></h2><?php endif; ?>
                            <?php 
                            $content = get_the_content();
                            if ( empty( trim( strip_tags( $content ) ) ) ) {
                                ?>
                                <?php echo langgam_fikir_paragraphs( langgam_fikir_text( 'about_mission_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                                <?php
                            } else {
                                the_content();
                            }
                            ?>
                            
                            <div class="mission-cards">
                                <?php if ( langgam_fikir_text( 'about_card1_title' ) || langgam_fikir_text( 'about_card1_text' ) ) : ?>
                                <div class="mission-card">
                                    <?php if ( langgam_fikir_text( 'about_card1_title' ) ) : ?><h3><?php echo esc_html( langgam_fikir_text( 'about_card1_title' ) ); ?></h3><?php endif; ?>
                                    <?php if ( langgam_fikir_text( 'about_card1_text' ) ) : ?><p><?php echo esc_html( langgam_fikir_text( 'about_card1_text' ) ); ?></p><?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if ( langgam_fikir_text( 'about_card2_title' ) || langgam_fikir_text( 'about_card2_text' ) ) : ?>
                                <div class="mission-card">
                                    <?php if ( langgam_fikir_text( 'about_card2_title' ) ) : ?><h3><?php echo esc_html( langgam_fikir_text( 'about_card2_title' ) ); ?></h3><?php endif; ?>
                                    <?php if ( langgam_fikir_text( 'about_card2_text' ) ) : ?><p><?php echo esc_html( langgam_fikir_text( 'about_card2_text' ) ); ?></p><?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php
                        // Contact CTA Section
                        $contact_heading = get_post_meta( get_the_ID(), '_about_contact_heading', true );
                        $contact_phone   = get_post_meta( get_the_ID(), '_about_contact_phone', true );
                        
                        // Theme Options first, then the older per-page value, then the default heading.
                        if ( empty( $contact_heading ) ) {
                            $contact_heading = langgam_fikir_text( 'about_contact_heading' );
                        }
                        if ( empty( $contact_phone ) ) {
                            $contact_phone = langgam_fikir_get_option( 'footer_phone' ); // blank unless set: no placeholder number
                        }
                        ?>
                        
                        <div class="about-contact-cta">
                            <?php if ( $contact_heading ) : ?>
                            <p class="contact-prompt"><?php echo esc_html( $contact_heading ); ?></p>
                            <?php endif; ?>
                            <p class="contact-action">
                                <a href="<?php echo esc_url( home_url( '/contact-langgam-fikir/' ) ); ?>" class="contact-link">
                                    <?php echo esc_html( langgam_fikir_text( 'about_contact_label' ) ); ?>
                                </a>
                            </p>
                            
                            <?php if ( $contact_phone ) : ?>
                            <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>" class="contact-phone-large">
                                <?php echo esc_html( $contact_phone ); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </article>
        
    <?php endwhile; ?>
    
</main>

<?php
get_footer();
