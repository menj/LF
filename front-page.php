<?php
/**
 * Template for displaying the homepage
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1><?php echo esc_html( langgam_fikir_get_option( 'hero_text' ) ); ?></h1>
        <div class="hero-buttons">
            <?php $about_url = langgam_fikir_get_page_url( 'about' ); ?>
            <?php if ( langgam_fikir_get_option( 'hero_primary_label' ) ) : ?>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-light">
                    <?php echo esc_html( langgam_fikir_get_option( 'hero_primary_label' ) ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $about_url && langgam_fikir_get_option( 'hero_secondary_label' ) ) : ?>
                <a href="<?php echo esc_url( $about_url ); ?>" class="btn btn-outline-light">
                    <?php echo esc_html( langgam_fikir_get_option( 'hero_secondary_label' ) ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- About Snippet Section -->
<section class="about-snippet">
    <div class="container">
        <div class="about-snippet-grid">
            <div class="about-snippet-image-wrapper">
                <div class="about-snippet-image">
                    <?php
                    // Image chosen in Theme Settings, then the About page featured image.
                    $snippet_image_id = (int) langgam_fikir_stored_option( 'about_snippet_image' );
                    $about_page_id    = langgam_fikir_get_special_page_id( 'about' );
                    if ( ! $snippet_image_id && $about_page_id && has_post_thumbnail( $about_page_id ) ) {
                        $snippet_image_id = (int) get_post_thumbnail_id( $about_page_id );
                    }

                    if ( $snippet_image_id ) :
                        echo wp_get_attachment_image( $snippet_image_id, 'large', false, array( 'loading' => 'lazy' ) );
                    else :
                        ?>
                        <div class="about-snippet-placeholder" aria-hidden="true"></div>
                    <?php endif; ?>
                </div>
                <?php if ( langgam_fikir_get_option( 'about_badge_number' ) || langgam_fikir_get_option( 'about_badge_label' ) ) : ?>
                    <div class="about-snippet-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21V7"/><path d="m16 12-4-5-4 5"/><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                        <span class="badge-number"><?php echo esc_html( langgam_fikir_get_option( 'about_badge_number' ) ); ?></span>
                        <span class="badge-label"><?php echo esc_html( langgam_fikir_get_option( 'about_badge_label' ) ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="about-snippet-content">
                <h2><?php echo esc_html( langgam_fikir_get_option( 'about_snippet_heading' ) ); ?></h2>
                <?php echo langgam_fikir_paragraphs( langgam_fikir_get_option( 'about_snippet_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                <?php if ( $about_url && langgam_fikir_get_option( 'about_snippet_button' ) ) : ?>
                    <a href="<?php echo esc_url( $about_url ); ?>" class="btn btn-primary">
                        <?php echo esc_html( langgam_fikir_get_option( 'about_snippet_button' ) ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Featured Books Section -->
<section class="books-section">
    <div class="container">
        <div class="section-header-centered">
            <h2 class="section-header-title"><?php echo esc_html( langgam_fikir_get_option( 'books_heading' ) ); ?></h2>
            <p class="section-header-subtitle"><?php echo esc_html( langgam_fikir_get_option( 'books_subtitle' ) ); ?></p>
        </div>
        <div class="books-grid">
            <?php
            $books_count = langgam_fikir_get_option( 'featured_books_count' );
            $featured_books = new WP_Query( array(
                'post_type'      => 'book',
                'posts_per_page' => $books_count,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );

            if ( $featured_books->have_posts() ) :
                while ( $featured_books->have_posts() ) : $featured_books->the_post();
                    ?>
                    <article class="book-card">
                        <div class="book-cover">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'book-thumbnail' ); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" class="book-cover-placeholder">
                                    <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                    <span class="placeholder-title"><?php the_title(); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php
                            $status = langgam_fikir_get_book_meta( get_the_ID(), 'status' );
                            if ( $status === 'pre-order' ) :
                                ?>
                                <span class="book-status pre-order"><?php _e( 'Pre-Order', 'langgam-fikir' ); ?></span>
                            <?php elseif ( $status === 'coming-soon' ) : ?>
                                <span class="book-status coming-soon"><?php _e( 'Coming Soon', 'langgam-fikir' ); ?></span>
                            <?php elseif ( $status === 'reprinting' ) : ?>
                                <span class="book-status reprinting"><?php _e( 'Reprinting', 'langgam-fikir' ); ?></span>
                            <?php elseif ( $status === 'published' ) : ?>
                                <span class="book-status published"><?php _e( 'Published', 'langgam-fikir' ); ?></span>
                            <?php elseif ( $status === 'out-of-print' ) : ?>
                                <span class="book-status out-of-print"><?php _e( 'Out of Print', 'langgam-fikir' ); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="book-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <?php
                        $subtitle = langgam_fikir_get_book_meta( get_the_ID(), 'subtitle' );
                        if ( $subtitle ) :
                            ?>
                            <p class="book-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                        <div class="book-meta">
                            <?php
                            $author = langgam_fikir_get_book_meta( get_the_ID(), 'author' );
                            $year   = langgam_fikir_get_book_meta( get_the_ID(), 'year' );
                            
                            if ( $author ) {
                                printf( '%s: %s', esc_html( langgam_fikir_get_option( 'author_label' ) ), esc_html( $author ) );
                                echo '<br>';
                            }
                            if ( $year ) {
                                printf( '%s: %s', esc_html( langgam_fikir_get_option( 'year_label' ) ), esc_html( $year ) );
                            }
                            ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="btn btn-outline-dark">
                            <?php _e( 'Learn More', 'langgam-fikir' ); ?>
                        </a>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        
        <div class="view-more-container">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="btn btn-outline-dark">
                <?php echo esc_html( langgam_fikir_get_option( 'view_more_label' ) ); ?>
            </a>
        </div>
    </div>
</section>

<!-- Resources Section -->
<section class="resources-section">
    <div class="container">
        <div class="section-header-centered">
            <h2 class="section-header-title"><?php echo esc_html( langgam_fikir_get_option( 'resources_heading' ) ); ?></h2>
            <p class="section-header-subtitle"><?php echo esc_html( langgam_fikir_get_option( 'resources_subtitle' ) ); ?></p>
        </div>
        
        <div class="resources-grid">
            <?php
            $resources_count = langgam_fikir_get_option( 'featured_resources_count' );
            $resources = new WP_Query( array(
                'post_type'      => 'post',
                'posts_per_page' => $resources_count,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );

            if ( $resources->have_posts() ) :
                while ( $resources->have_posts() ) : $resources->the_post();
                    ?>
                    <article class="resource-card">
                        <h3>
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div class="resource-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            <?php _e( 'Read More', 'langgam-fikir' ); ?>
                        </a>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        
        <?php $blog_page_id = (int) get_option( 'page_for_posts' ); ?>
        <?php if ( $blog_page_id && langgam_fikir_get_option( 'view_more_label' ) ) : ?>
            <div class="view-more-container">
                <a href="<?php echo esc_url( get_permalink( $blog_page_id ) ); ?>" class="btn btn-outline-dark">
                    <?php echo esc_html( langgam_fikir_get_option( 'view_more_label' ) ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
