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
                        // Theme Settings logo, then a logo saved by older per-page meta, then the site logo.
                        $logo_id = (int) langgam_fikir_stored_option( 'about_logo' );
                        if ( ! $logo_id ) {
                            $logo_id = (int) get_post_meta( get_the_ID(), '_about_logo_id', true );
                        }
                        if ( ! $logo_id && has_custom_logo() ) {
                            $logo_id = (int) get_theme_mod( 'custom_logo' );
                        }

                        if ( $logo_id ) {
                            echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'about-logo-image' ) );
                        } else {
                            ?>
                            <p class="about-logo-text"><?php bloginfo( 'name' ); ?></p>
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
                        
                        <?php $about_lead = langgam_fikir_get_option( 'about_lead' ); ?>
                        <?php if ( $about_lead ) : ?>
                            <div class="about-lead">
                                <?php echo langgam_fikir_paragraphs( $about_lead ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="about-text">
                            <?php $mission_heading = langgam_fikir_get_option( 'about_mission_heading' ); ?>
                            <?php if ( $mission_heading ) : ?>
                                <h2><?php echo esc_html( $mission_heading ); ?></h2>
                            <?php endif; ?>
                            <?php
                            // Text typed in the page editor wins; otherwise the Theme Settings text.
                            if ( '' === trim( wp_strip_all_tags( get_the_content() ) ) ) {
                                echo langgam_fikir_paragraphs( langgam_fikir_get_option( 'about_mission_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
                            } else {
                                the_content();
                            }

                            $cards = array();
                            foreach ( array( 1, 2 ) as $n ) {
                                $card_title = langgam_fikir_get_option( "about_card{$n}_title" );
                                $card_text  = langgam_fikir_get_option( "about_card{$n}_text" );
                                if ( $card_title || $card_text ) {
                                    $cards[] = array( $card_title, $card_text );
                                }
                            }
                            ?>
                            
                            <?php if ( $cards ) : ?>
                                <div class="mission-cards">
                                    <?php foreach ( $cards as $card ) : ?>
                                        <div class="mission-card">
                                            <?php if ( $card[0] ) : ?><h3><?php echo esc_html( $card[0] ); ?></h3><?php endif; ?>
                                            <?php if ( $card[1] ) : ?><p><?php echo esc_html( $card[1] ); ?></p><?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php
                        // Contact call-to-action
                        $contact_heading = langgam_fikir_meta_or_option( get_the_ID(), '_about_contact_heading', 'about_contact_heading' );
                        $contact_phone   = langgam_fikir_meta_or_option( get_the_ID(), '_about_contact_phone', 'about_contact_phone' );
                        $contact_url     = langgam_fikir_get_page_url( 'contact' );
                        $contact_label   = langgam_fikir_get_option( 'about_contact_label' );
                        ?>
                        
                        <?php if ( $contact_heading || $contact_url || $contact_phone ) : ?>
                            <div class="about-contact-cta">
                                <?php if ( $contact_heading ) : ?>
                                    <p class="contact-prompt"><?php echo esc_html( $contact_heading ); ?></p>
                                <?php endif; ?>
                                <?php if ( $contact_url && $contact_label ) : ?>
                                    <p class="contact-action">
                                        <a href="<?php echo esc_url( $contact_url ); ?>" class="contact-link"><?php echo esc_html( $contact_label ); ?></a>
                                    </p>
                                <?php endif; ?>
                                <?php if ( $contact_phone ) : ?>
                                    <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>" class="contact-phone-large">
                                        <?php echo esc_html( $contact_phone ); ?>
                                    </a>
                                <?php endif; ?>
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
