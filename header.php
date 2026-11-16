<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'langgam-fikir' ); ?></a>

<div id="page" class="site">
    
    <header id="masthead" class="site-header">
        <div class="header-container">
            
            <div class="site-branding">
                <?php if ( has_custom_logo() ) : ?>
                    <div class="logo-text-wrapper">
                        <?php the_custom_logo(); ?>
                        <div class="site-identity">
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
                    </div>
                <?php else : ?>
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
                <?php endif; ?>
            </div>
            
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'langgam-fikir' ); ?>">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <div class="header-right">
                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'fallback_cb'    => false,
                        )
                    );
                    ?>
                </nav>
                
                <?php
                $header_cta_label = trim( (string) langgam_fikir_text( 'header_cta_label' ) );
                $header_cta_url   = langgam_fikir_get_option( 'header_cta_url' );
                if ( '' !== $header_cta_label && $header_cta_url ) :
                    ?>
                    <a class="btn btn-solid header-cta" href="<?php echo esc_url( $header_cta_url ); ?>"><?php echo esc_html( $header_cta_label ); ?></a>
                <?php endif; ?>

                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-link">
                        <svg class="cart-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 2L7 6H3L5 20H19L21 6H17L15 2H9Z" stroke-linejoin="round"/>
                            <path d="M9 10C9 11.6569 10.3431 13 12 13C13.6569 13 15 11.6569 15 10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
            
        </div>
    </header>
    
    <div id="content" class="site-content">
