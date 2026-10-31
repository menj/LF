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
            </a>
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
                    $about_page = get_page_by_path( 'about-langgam-fikir' );
                    if ( $about_page && has_post_thumbnail( $about_page->ID ) ) :
                        echo get_the_post_thumbnail( $about_page->ID, 'large', array( 'alt' => esc_attr__( 'About Langgam Fikir', 'langgam-fikir' ) ) );
                    else :
                        ?>
                        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1000&auto=format&fit=crop" alt="<?php esc_attr_e( 'Library of books', 'langgam-fikir' ); ?>" loading="lazy" />
                    <?php endif; ?>
                </div>
                <div class="about-snippet-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21V7"/><path d="m16 12-4-5-4 5"/><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    <span class="badge-number">10+</span>
                    <span class="badge-label"><?php _e( 'Years of Excellence', 'langgam-fikir' ); ?></span>
                </div>
            </div>
            <div class="about-snippet-content">
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

<!-- Featured Books Section -->
<section class="books-section">
    <div class="container">
        <div class="section-header-centered">
            <h2 class="section-header-title"><?php _e( 'Featured Publications', 'langgam-fikir' ); ?></h2>
            <p class="section-header-subtitle"><?php _e( "Our editors' picks of the season, representing the finest in contemporary thought and literature.", 'langgam-fikir' ); ?></p>
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
                                printf( __( 'Penulis: %s', 'langgam-fikir' ), esc_html( $author ) );
                                echo '<br>';
                            }
                            if ( $year ) {
                                printf( __( 'Tahun: %s', 'langgam-fikir' ), esc_html( $year ) );
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
                <?php _e( 'VIEW MORE', 'langgam-fikir' ); ?>
            </a>
        </div>
    </div>
</section>

<!-- Resources Section -->
<section class="resources-section">
    <div class="container">
        <div class="section-header-centered">
            <h2 class="section-header-title"><?php _e( 'Resources & Insights', 'langgam-fikir' ); ?></h2>
            <p class="section-header-subtitle"><?php _e( 'Articles, interviews, and updates from the world of Langgam Fikir.', 'langgam-fikir' ); ?></p>
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
            </a>
        </div>
    </div>
</section>

<?php
get_footer();
