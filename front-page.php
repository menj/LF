<?php
/**
 * Template for displaying the homepage
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">

<!-- Hero Section -->
<section class="hero">
    <?php
    $hero_bg_id = langgam_fikir_get_option( 'hero_background_image' );
    if ( $hero_bg_id ) :
        $hero_bg_url = wp_get_attachment_image_url( $hero_bg_id, 'full' );
        if ( $hero_bg_url ) :
    ?>
        <div class="hero-bg" style="background-image: url('<?php echo esc_url( $hero_bg_url ); ?>');" aria-hidden="true"></div>
    <?php endif; endif; ?>
    <div class="hero-overlay" aria-hidden="true"></div>
    <div class="hero-content">
        <h1>
            <?php 
            $hero_text = langgam_fikir_get_option( 'hero_text' );
            echo esc_html( $hero_text );
            ?>
        </h1>
        <div class="hero-buttons">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-light">
                <?php _e( 'View Books', 'langgam-fikir' ); ?>
            </a>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-langgam-fikir' ) ) ); ?>" class="btn btn-outline-light">
                <?php _e( 'Learn More', 'langgam-fikir' ); ?>
                <span class="screen-reader-text"><?php
                    /* translators: %s: site name */
                    printf( esc_html__( 'about %s', 'langgam-fikir' ), esc_html( get_bloginfo( 'name' ) ) );
                ?></span>
            </a>
        </div>
    </div>
</section>

<!-- About Snippet Section -->
<section class="about-snippet">
    <div class="container">
        <div class="about-snippet-grid">
            <div class="about-snippet-image-wrapper reveal-left">
                <div class="about-snippet-image">
                    <?php
                    $about_page = get_page_by_path( 'about-langgam-fikir' );
                    if ( $about_page && has_post_thumbnail( $about_page->ID ) ) :
                        // This image is the LCP element on mobile: never lazy, always high priority.
                        echo get_the_post_thumbnail( $about_page->ID, 'large', array(
                            'alt'           => esc_attr__( 'About Langgam Fikir', 'langgam-fikir' ),
                            'loading'       => 'eager',
                            'fetchpriority' => 'high',
                        ) );
                    else :
                        ?>
                        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1000&auto=format&fit=crop" alt="<?php esc_attr_e( 'Library of books', 'langgam-fikir' ); ?>" loading="eager" fetchpriority="high" decoding="async" />
                    <?php endif; ?>
                </div>
                <div class="about-snippet-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21V7"/><path d="m16 12-4-5-4 5"/><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    <span class="badge-number"><?php echo esc_html( langgam_fikir_get_option( 'about_badge_number', '2+' ) ); ?></span>
                    <span class="badge-label"><?php echo esc_html( langgam_fikir_get_option( 'about_badge_label', __( 'Years of Excellence', 'langgam-fikir' ) ) ); ?></span>
                </div>
            </div>
            <div class="about-snippet-content reveal-right">
                <h2><?php _e( 'Preserving the Art of Thoughtful Publishing', 'langgam-fikir' ); ?></h2>
                <p><?php _e( 'At Langgam Fikir, we believe that books are more than just paper and ink—they are vessels of culture, history, and human experience. Founded with a mission to elevate local voices and translate global wisdom.', 'langgam-fikir' ); ?></p>
                <p><?php _e( 'Our catalog spans history, philosophy, literature, and social sciences, carefully curated to challenge perspectives and enrich the mind.', 'langgam-fikir' ); ?></p>
                <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-langgam-fikir' ) ) ); ?>" class="btn btn-primary">
                    <?php _e( 'Read Our Full Story', 'langgam-fikir' ); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Showcase Section (interactive — settings: Theme Options > Homepage) -->
<?php get_template_part( 'template-parts/content/showcase' ); ?>

<!-- Latest Publications Section -->
<section class="books-section">
    <div class="container">
        <div class="section-header-centered reveal">
            <h2 class="section-header-title"><?php _e( 'Latest Publications', 'langgam-fikir' ); ?></h2>
            <p class="section-header-subtitle"><?php _e( "Our newest releases, representing the finest in contemporary thought and literature.", 'langgam-fikir' ); ?></p>
        </div>
        <div class="books-grid stagger-children">
            <?php
            $books_count = langgam_fikir_get_option( 'featured_books_count', 6 );
            $featured_books = new WP_Query( array(
                'post_type'      => 'book',
                'posts_per_page' => $books_count,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post__not_in'   => langgam_fikir_showcase_book_ids(), // Showcase books never repeat here
            ) );

            if ( $featured_books->have_posts() ) :
                while ( $featured_books->have_posts() ) : $featured_books->the_post();
                    get_template_part( 'template-parts/content/book-card' );
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        
        <div class="view-more-container">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-dark">
                <?php _e( 'VIEW MORE', 'langgam-fikir' ); ?>
                <span class="screen-reader-text"><?php esc_html_e( 'publications', 'langgam-fikir' ); ?></span>
            </a>
        </div>
    </div>
</section>

<!-- Resources Section -->
<section class="resources-section">
    <div class="container">
        <div class="section-header-centered reveal">
            <h2 class="section-header-title"><?php _e( 'Resources & Insights', 'langgam-fikir' ); ?></h2>
            <p class="section-header-subtitle"><?php _e( 'Articles, interviews, and updates from the world of Langgam Fikir.', 'langgam-fikir' ); ?></p>
        </div>
        
        <div class="resources-grid stagger-children">
            <?php
            $resources_count = langgam_fikir_get_option( 'featured_resources_count', 3 );
            $resources = new WP_Query( array(
                'post_type'      => 'post',
                'posts_per_page' => $resources_count,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );

            if ( $resources->have_posts() ) :
                while ( $resources->have_posts() ) : $resources->the_post();
                    get_template_part( 'template-parts/content/resource-card' );
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        
        <div class="view-more-container">
            <?php
            $blog_page_id = get_option( 'page_for_posts' );
            if ( $blog_page_id ) {
                $blog_url = get_permalink( $blog_page_id );
            } else {
                $blog_url = home_url( '/resources/' );
            }
            ?>
            <a href="<?php echo esc_url( $blog_url ); ?>" class="btn btn-outline-dark">
                <?php _e( 'VIEW MORE', 'langgam-fikir' ); ?>
                <span class="screen-reader-text"><?php esc_html_e( 'resources', 'langgam-fikir' ); ?></span>
            </a>
        </div>
    </div>
</section>

</main>

<?php
get_footer();
