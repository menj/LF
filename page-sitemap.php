<?php
/**
 * Template Name: Sitemap
 *
 * A plain HTML sitemap for visitors: every page, every publication by category and the latest
 * articles, as Google's SEO starter guide recommends alongside the XML sitemap for crawlers.
 * Create a page called "Sitemap", pick this template and link it in the footer menu.
 *
 * @package Langgam_Fikir
 * @since 2.14.0
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'lf-sitemap' ); ?>>
            <div class="container">
                <div class="section section-padded">

                    <header class="entry-header mb-3">
                        <div class="page-title-header">
                            <?php if ( function_exists( 'langgam_fikir_breadcrumbs' ) ) { langgam_fikir_breadcrumbs(); } ?>
                            <?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
                        </div>
                    </header>

                    <?php if ( get_the_content() ) : ?>
                        <div class="entry-content"><?php the_content(); ?></div>
                    <?php endif; ?>

                    <div class="lf-sitemap-grid">

                        <section class="lf-sitemap-section" aria-labelledby="lf-sitemap-pages">
                            <h2 id="lf-sitemap-pages"><?php esc_html_e( 'Pages', 'langgam-fikir' ); ?></h2>
                            <ul>
                                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'langgam-fikir' ); ?></a></li>
                                <?php
                                wp_list_pages( array(
                                    'title_li' => '',
                                    'exclude'  => (string) get_the_ID(),
                                ) );
                                ?>
                            </ul>
                        </section>

                        <section class="lf-sitemap-section lf-sitemap-section--wide" aria-labelledby="lf-sitemap-books">
                            <h2 id="lf-sitemap-books">
                                <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>"><?php echo esc_html( post_type_archive_title( '', false ) ? post_type_archive_title( '', false ) : get_post_type_object( 'book' )->labels->name ); ?></a>
                            </h2>
                            <?php
                            $lf_listed     = array();
                            $lf_categories = get_terms( array( 'taxonomy' => 'book-category', 'hide_empty' => true ) );
                            if ( ! is_wp_error( $lf_categories ) ) :
                                foreach ( $lf_categories as $lf_category ) :
                                    $lf_books = get_posts( array(
                                        'post_type'      => 'book',
                                        'posts_per_page' => -1,
                                        'orderby'        => 'title',
                                        'order'          => 'ASC',
                                        'no_found_rows'  => true,
                                        'tax_query'      => array( array( 'taxonomy' => 'book-category', 'terms' => $lf_category->term_id ) ),
                                    ) );
                                    if ( ! $lf_books ) {
                                        continue;
                                    }
                                    ?>
                                    <h3><a href="<?php echo esc_url( get_term_link( $lf_category ) ); ?>"><?php echo esc_html( $lf_category->name ); ?></a></h3>
                                    <ul>
                                        <?php foreach ( $lf_books as $lf_book ) : $lf_listed[] = $lf_book->ID; ?>
                                            <li><a href="<?php echo esc_url( get_permalink( $lf_book ) ); ?>"><?php echo esc_html( get_the_title( $lf_book ) ); ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php
                                endforeach;
                            endif;

                            // Books without a category.
                            $lf_other = get_posts( array(
                                'post_type'      => 'book',
                                'posts_per_page' => -1,
                                'orderby'        => 'title',
                                'order'          => 'ASC',
                                'no_found_rows'  => true,
                                'post__not_in'   => $lf_listed ? $lf_listed : array( 0 ),
                            ) );
                            if ( $lf_other ) :
                                ?>
                                <?php if ( $lf_listed ) : ?><h3><?php esc_html_e( 'Other publications', 'langgam-fikir' ); ?></h3><?php endif; ?>
                                <ul>
                                    <?php foreach ( $lf_other as $lf_book ) : ?>
                                        <li><a href="<?php echo esc_url( get_permalink( $lf_book ) ); ?>"><?php echo esc_html( get_the_title( $lf_book ) ); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </section>

                        <section class="lf-sitemap-section" aria-labelledby="lf-sitemap-articles">
                            <h2 id="lf-sitemap-articles"><?php esc_html_e( 'Articles', 'langgam-fikir' ); ?></h2>
                            <ul>
                                <?php
                                wp_get_archives( array(
                                    'type'      => 'postbypost',
                                    'limit'     => 50,
                                    'post_type' => 'post',
                                ) );
                                ?>
                            </ul>
                            <?php
                            $lf_topics = get_categories( array( 'hide_empty' => true ) );
                            if ( $lf_topics ) :
                                ?>
                                <h3><?php esc_html_e( 'Article categories', 'langgam-fikir' ); ?></h3>
                                <ul>
                                    <?php foreach ( $lf_topics as $lf_topic ) : ?>
                                        <li><a href="<?php echo esc_url( get_category_link( $lf_topic ) ); ?>"><?php echo esc_html( $lf_topic->name ); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </section>

                    </div>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();
