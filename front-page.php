<?php
/**
 * Template for displaying the homepage
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">

<?php
$announce_text = trim( (string) langgam_fikir_text( 'announce_text' ) );
if ( langgam_fikir_get_option( 'announce_enabled' ) && '' !== $announce_text ) :
    $announce_url   = langgam_fikir_get_option( 'announce_url' );
    $announce_label = trim( (string) langgam_fikir_text( 'announce_label' ) );
    ?>
<aside class="container lf-announce-wrap" aria-label="<?php esc_attr_e( 'Announcement', 'langgam-fikir' ); ?>">
    <div class="lf-announce">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
        <span class="lf-announce-text"><?php echo esc_html( $announce_text ); ?></span>
        <?php if ( $announce_url && '' !== $announce_label ) : ?>
            <a class="lf-announce-link" href="<?php echo esc_url( $announce_url ); ?>"><?php echo esc_html( $announce_label ); ?></a>
        <?php endif; ?>
    </div>
</aside>
<?php endif; ?>

<!-- Hero Section -->
<?php
$hero_layout  = (string) langgam_fikir_get_option( 'hero_layout' );
$about_url    = get_permalink( get_page_by_path( 'about-langgam-fikir' ) );
$hero_upload  = (int) langgam_fikir_get_option( 'hero_background_image' );
$hero_photo_s = $hero_upload ? null : langgam_fikir_hero_photo_urls();
if ( 'banner' !== $hero_layout ) :
    ?>
<section class="hero hero--split">
    <?php if ( langgam_fikir_get_option( 'ghost_wordmark' ) ) : ?>
        <span class="lf-wordmark" aria-hidden="true"><?php bloginfo( 'name' ); ?></span>
    <?php endif; ?>
    <div class="container hero-split-grid">
        <div class="hero-split-text">
            <h1><?php echo esc_html( langgam_fikir_text( 'hero_heading' ) ); ?></h1>
            <p class="hero-lead"><?php echo esc_html( langgam_fikir_get_option( 'hero_text' ) ); ?></p>
            <div class="hero-buttons">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-solid">
                    <?php echo esc_html( langgam_fikir_text( 'hero_primary_label' ) ); ?>
                </a>
                <a href="<?php echo esc_url( $about_url ); ?>" class="hero-link">
                    <?php echo esc_html( langgam_fikir_text( 'hero_secondary_label' ) ); ?>
                    <span aria-hidden="true">&rarr;</span>
                    <span class="screen-reader-text"><?php
                        /* translators: %s: site name */
                        printf( esc_html__( 'about %s', 'langgam-fikir' ), esc_html( get_bloginfo( 'name' ) ) );
                    ?></span>
                </a>
            </div>
        </div>
        <?php
        $hero_covers = array();
        if ( 'photo' !== langgam_fikir_get_option( 'hero_visual' ) ) {
            $cover_query = new WP_Query( array(
                'post_type'           => 'book',
                'posts_per_page'      => 6,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'no_found_rows'       => true,
                'ignore_sticky_posts' => true,
                'meta_query'          => array( array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ) ),
            ) );
            foreach ( $cover_query->posts as $cover_post ) {
                $hero_covers[] = $cover_post;
            }
            wp_reset_postdata();
            if ( count( $hero_covers ) < 3 ) {
                $hero_covers = array();
            }
        }
        if ( $hero_covers ) :
            ?>
            <div class="hero-split-media hero-covers">
                <?php foreach ( $hero_covers as $i => $cover_post ) : ?>
                    <a class="hero-cover hero-cover--<?php echo (int) ( $i + 1 ); ?>" href="<?php echo esc_url( get_permalink( $cover_post ) ); ?>">
                        <?php
                        echo get_the_post_thumbnail( $cover_post, 'medium', array(
                            'alt'           => esc_attr( get_the_title( $cover_post ) ),
                            'loading'       => 'eager',
                            'fetchpriority' => $i < 3 ? 'high' : 'auto',
                        ) );
                        ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php elseif ( $hero_upload || $hero_photo_s ) : ?>
            <div class="hero-split-media">
                <?php
                if ( $hero_upload ) {
                    // The LCP element: never lazy, high priority.
                    echo wp_get_attachment_image( $hero_upload, 'large', false, array(
                        'class'         => 'hero-split-img',
                        'alt'           => '',
                        'loading'       => 'eager',
                        'fetchpriority' => 'high',
                    ) );
                } else {
                    ?>
                    <img class="hero-split-img" src="<?php echo esc_url( $hero_photo_s['small'] ); ?>" srcset="<?php echo esc_url( $hero_photo_s['small'] ); ?> 960w, <?php echo esc_url( $hero_photo_s['full'] ); ?> 1920w" sizes="(min-width: 900px) 50vw, 100vw" alt="" loading="eager" fetchpriority="high" decoding="async" />
                <?php } ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php else : ?>
<section class="hero">
    <?php
    $hero_bg_id = langgam_fikir_get_option( 'hero_background_image' );
    if ( $hero_bg_id ) :
        $hero_bg_url = wp_get_attachment_image_url( $hero_bg_id, 'full' );
        if ( $hero_bg_url ) :
    ?>
        <div class="hero-bg" style="background-image: url('<?php echo esc_url( $hero_bg_url ); ?>');" aria-hidden="true"></div>
    <?php endif; endif; ?>
    <?php
    $hero_photo = $hero_bg_id ? null : langgam_fikir_hero_photo_urls();
    if ( $hero_photo ) :
        ?>
        <div class="hero-bg hero-bg--photo" style="--lf-hero-image: url('<?php echo esc_url( $hero_photo['full'] ); ?>'); --lf-hero-image-sm: url('<?php echo esc_url( $hero_photo['small'] ); ?>');" aria-hidden="true"></div>
    <?php endif; ?>
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
                <?php echo esc_html( langgam_fikir_text( 'hero_primary_label' ) ); ?>
            </a>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-langgam-fikir' ) ) ); ?>" class="btn btn-outline-light">
                <?php echo esc_html( langgam_fikir_text( 'hero_secondary_label' ) ); ?>
                <span class="screen-reader-text"><?php
                    /* translators: %s: site name */
                    printf( esc_html__( 'about %s', 'langgam-fikir' ), esc_html( get_bloginfo( 'name' ) ) );
                ?></span>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ( langgam_fikir_get_option( 'highlights_enabled' ) ) :
    $highlight_icons = array(
        1 => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
        2 => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        3 => '<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
    );
    $highlights = array();
    foreach ( $highlight_icons as $n => $icon ) {
        $label = langgam_fikir_text( 'highlight_' . $n );
        if ( '' !== trim( (string) $label ) ) {
            $highlights[] = array( 'icon' => $icon, 'label' => $label );
        }
    }
    if ( $highlights ) :
        ?>
<!-- Highlights Strip -->
<section class="lf-highlights" aria-label="<?php esc_attr_e( 'Highlights', 'langgam-fikir' ); ?>">
    <div class="container">
        <ul class="lf-highlights-inner">
            <?php foreach ( $highlights as $item ) : ?>
                <li class="lf-highlight">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup above ?></svg>
                    <span><?php echo esc_html( $item['label'] ); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
    <?php endif; endif; ?>

<!-- About Snippet Section -->
<section class="about-snippet">
    <div class="container">
        <div class="about-snippet-grid">
            <div class="about-snippet-image-wrapper reveal-left">
                <div class="about-snippet-image" data-parallax-root>
                    <?php
                    $about_page = get_page_by_path( 'about-langgam-fikir' );
                    if ( $about_page && has_post_thumbnail( $about_page->ID ) ) :
                        // This image is the LCP element on mobile: never lazy, always high priority.
                        echo get_the_post_thumbnail( $about_page->ID, 'large', array(
                            'alt'           => esc_attr__( 'About Langgam Fikir', 'langgam-fikir' ),
                            'loading'       => 'eager',
                            'fetchpriority' => 'high',
                            'data-parallax' => '0.06',
                        ) );
                    else :
                        ?>
                        <img data-parallax="0.06" src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1000&auto=format&fit=crop" alt="<?php esc_attr_e( 'Library of books', 'langgam-fikir' ); ?>" loading="eager" fetchpriority="high" decoding="async" />
                    <?php endif; ?>
                </div>
                <div class="about-snippet-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21V7"/><path d="m16 12-4-5-4 5"/><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    <span class="badge-number"><?php echo esc_html( langgam_fikir_get_option( 'about_badge_number', '2+' ) ); ?></span>
                    <span class="badge-label"><?php echo esc_html( langgam_fikir_get_option( 'about_badge_label', __( 'Years of Excellence', 'langgam-fikir' ) ) ); ?></span>
                </div>
            </div>
            <div class="about-snippet-content reveal-right">
                <h2><?php echo esc_html( langgam_fikir_text( 'about_snippet_heading' ) ); ?></h2>
                <?php echo langgam_fikir_paragraphs( langgam_fikir_text( 'about_snippet_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-langgam-fikir' ) ) ); ?>" class="btn btn-primary">
                    <?php echo esc_html( langgam_fikir_text( 'about_snippet_button' ) ); ?>
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
            <h2 class="section-header-title"><?php echo esc_html( langgam_fikir_text( 'books_heading' ) ); ?></h2>
            <p class="section-header-subtitle"><?php echo esc_html( langgam_fikir_text( 'books_subtitle' ) ); ?></p>
        </div>
        <?php $books_carousel = 'grid' !== langgam_fikir_get_option( 'books_layout' ); ?>
        <?php if ( $books_carousel ) : ?>
            <div class="lf-carousel-controls" hidden>
                <button type="button" class="lf-carousel-btn" data-dir="-1" aria-label="<?php esc_attr_e( 'Previous books', 'langgam-fikir' ); ?>"><span aria-hidden="true">&larr;</span></button>
                <button type="button" class="lf-carousel-btn" data-dir="1" aria-label="<?php esc_attr_e( 'Next books', 'langgam-fikir' ); ?>"><span aria-hidden="true">&rarr;</span></button>
            </div>
        <?php endif; ?>
        <div class="books-grid stagger-children<?php echo $books_carousel ? ' books-grid--carousel' : ''; ?>"<?php echo $books_carousel ? ' data-lf-carousel tabindex="0" role="region" aria-label="' . esc_attr__( 'Latest publications', 'langgam-fikir' ) . '"' : ''; ?>>
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
                <?php echo esc_html( langgam_fikir_text( 'view_more_label' ) ); ?>
                <span class="screen-reader-text"><?php esc_html_e( 'publications', 'langgam-fikir' ); ?></span>
            </a>
        </div>
    </div>
</section>

<!-- Resources Section -->
<section class="resources-section">
    <div class="container">
        <div class="section-header-centered reveal">
            <h2 class="section-header-title"><?php echo esc_html( langgam_fikir_text( 'resources_heading' ) ); ?></h2>
            <p class="section-header-subtitle"><?php echo esc_html( langgam_fikir_text( 'resources_subtitle' ) ); ?></p>
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
                <?php echo esc_html( langgam_fikir_text( 'view_more_label' ) ); ?>
                <span class="screen-reader-text"><?php esc_html_e( 'resources', 'langgam-fikir' ); ?></span>
            </a>
        </div>
    </div>
</section>

</main>

<?php
get_footer();
