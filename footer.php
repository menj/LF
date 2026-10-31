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
                <h3 class="footer-heading"><?php _e( 'Legal', 'langgam-fikir' ); ?></h3>
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
                    &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php _e( 'All rights reserved.', 'langgam-fikir' ); ?>
                </p>
                <p class="company-info">
                    <?php printf( 
                        __( '%s is a registered enterprise in Malaysia.', 'langgam-fikir' ),
                        get_bloginfo( 'name' )
                    ); ?>
                </p>
            </div>
        </div>
    </footer>
    
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
