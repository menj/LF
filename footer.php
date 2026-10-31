    </div><!-- #content -->
    
    <footer id="colophon" class="site-footer">
        <div class="footer-content">
            
            <div class="footer-branding">
                <p class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                </p>
                <?php
                $description = get_bloginfo( 'description', 'display' );
                if ( $description ) :
                    ?>
                    <p class="tagline"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="footer-legal-column">
                <h3 class="footer-heading"><?php echo esc_html( langgam_fikir_text( 'footer_legal_heading' ) ); ?></h3>
                <nav class="footer-legal-navigation">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer-legal',
                        'menu_id'        => 'footer-legal-menu',
                        'container'      => false,
                        'fallback_cb'    => 'lf_footer_legal_fallback',
                    ) );
                    ?>
                </nav>
            </div>
            
            <?php
            $address = langgam_fikir_get_option( 'footer_address' );
            $phone   = langgam_fikir_get_option( 'footer_phone' );
            $email   = langgam_fikir_get_option( 'footer_email' );

            if ( $address || $phone || $email ) :
            ?>
            <div class="footer-contact-column" itemscope itemtype="https://schema.org/Organization">
                <h3 class="footer-heading"><?php echo esc_html( langgam_fikir_text( 'footer_contact_heading' ) ); ?></h3>
                <meta itemprop="name" content="<?php echo esc_attr( langgam_fikir_get_option( 'footer_business_name', get_bloginfo( 'name' ) ) ); ?>">
                <meta itemprop="url" content="<?php echo esc_url( home_url( '/' ) ); ?>">
                <div class="footer-contact-info">
                    <?php if ( $address ) : ?>
                        <div class="contact-item" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span itemprop="streetAddress"><?php echo esc_html( $address ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ( $phone ) : ?>
                        <div class="contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>" itemprop="telephone"><?php echo esc_html( $phone ); ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if ( $email ) : ?>
                        <div class="contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m2 7 8.97 5.7a1.94 1.94 0 0 0 2.06 0L22 7"/></svg>
                            <a href="mailto:<?php echo esc_attr( $email ); ?>" itemprop="email"><?php echo esc_html( $email ); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <p class="copyright">
                    &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php echo esc_html( langgam_fikir_text( 'footer_rights' ) ); ?>
                </p>
                <?php
                $legal_entity = langgam_fikir_get_option( 'legal_entity_name' );
                $reg_number   = langgam_fikir_get_option( 'registration_number' );

                if ( $legal_entity ) :
                ?>
                <p class="company-info">
                    <?php
                    if ( $reg_number ) {
                        printf(
                            esc_html__( '%1$s is a registered enterprise in Malaysia (Reg. No.: %2$s).', 'langgam-fikir' ),
                            esc_html( $legal_entity ),
                            esc_html( $reg_number )
                        );
                    } else {
                        printf(
                            esc_html__( '%s is a registered enterprise in Malaysia.', 'langgam-fikir' ),
                            esc_html( $legal_entity )
                        );
                    }
                    ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
