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
                if ( $description || is_customize_preview() ) :
                    ?>
                    <p class="tagline"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="footer-legal-column">
                <h3 class="footer-heading"><?php echo esc_html( langgam_fikir_get_option( 'footer_legal_heading' ) ); ?></h3>
                <nav class="footer-legal-navigation">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer-legal',
                            'menu_id'        => 'footer-legal-menu',
                            'container'      => false,
                            'fallback_cb'    => 'lf_footer_legal_fallback',
                        )
                    );
                    ?>
                </nav>
            </div>
            
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <p class="copyright">
                    &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php echo esc_html( langgam_fikir_get_option( 'footer_rights' ) ); ?>
                </p>
                <?php $company_info = langgam_fikir_get_option( 'footer_company_info' ); ?>
                <?php if ( $company_info ) : ?>
                    <p class="company-info"><?php echo esc_html( str_replace( '{site}', get_bloginfo( 'name' ), $company_info ) ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
