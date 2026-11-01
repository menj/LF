<?php
/**
 * Template Name: Privacy Policy
 * Template for displaying the Privacy Policy page
 *
 * @package Langgam_Fikir
 */

get_header();
?>

<main id="primary" class="site-main" tabindex="-1">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <div class="container">
                <div class="privacy-content">
                    
                    <aside class="privacy-sidebar">
                        <nav class="privacy-navigation">
                            <h3 class="privacy-nav-heading"><?php _e( 'Contents', 'langgam-fikir' ); ?></h3>
                            <ul class="privacy-nav" id="privacy-nav">
                                <?php
                                $content = get_the_content();
                                preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/i', $content, $headings );
                                
                                if ( ! empty( $headings[1] ) ) :
                                    foreach ( $headings[1] as $index => $heading ) :
                                        $heading_text = strip_tags( $heading );
                                        $heading_id = 'section-' . ( $index + 1 );
                                        ?>
                                        <li>
                                            <a href="#<?php echo esc_attr( $heading_id ); ?>">
                                                <?php echo esc_html( $heading_text ); ?>
                                            </a>
                                        </li>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </ul>
                        </nav>
                    </aside>
                    
                    <div class="privacy-main">
                        <div class="page-title-header">
                            <?php if ( function_exists( "langgam_fikir_breadcrumbs" ) ) { langgam_fikir_breadcrumbs(); } ?>
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </div>
                        
                        <div class="entry-content">
                            <?php
                            $content = get_the_content();
                            $content = apply_filters( 'the_content', $content );
                            
                            preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/i', $content, $headings, PREG_OFFSET_CAPTURE );
                            
                            if ( ! empty( $headings[0] ) ) {
                                $offset = 0;
                                foreach ( $headings[0] as $index => $heading ) {
                                    $heading_id = 'section-' . ( $index + 1 );
                                    $original = $heading[0];
                                    $position = $heading[1] + $offset;
                                    
                                    $replacement = preg_replace( '/<h2/', '<h2 id="' . $heading_id . '"', $original, 1 );
                                    
                                    $content = substr_replace( $content, $replacement, $position, strlen( $original ) );
                                    $offset += strlen( $replacement ) - strlen( $original );
                                }
                            }
                            
                            echo wp_kses_post( $content );
                            ?>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </article>
        
    <?php endwhile; ?>
    
</main>

<?php
get_footer();
