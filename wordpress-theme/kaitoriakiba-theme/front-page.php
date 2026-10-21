<?php
/**
 * Kaitori Akiba Front Page Template
 * 
 * 100% of homepage design and content is rendered directly through Elementor Page Builder,
 * preserving the exact Japanese Kaitori Akiba index.html design.
 * 
 * @package KaitoriAkiba
 */

get_header();

$front_id = (int) get_option( 'page_on_front' );
if ( ! $front_id ) {
    $home_page = get_page_by_path( 'home' );
    if ( ! $home_page ) {
        $pages = get_posts( array( 'post_type' => 'page', 'title' => 'Home', 'posts_per_page' => 1 ) );
        if ( ! empty( $pages ) ) {
            $home_page = $pages[0];
        }
    }
    if ( $home_page ) {
        $front_id = $home_page->ID;
    }
}
?>

<main id="primary" class="site-main kaitoriakiba-front-page elementor-content-wrap">
    <?php
    if ( $front_id > 0 ) :
        $front_post = get_post( $front_id );
        if ( $front_post ) :
            setup_postdata( $GLOBALS['post'] =& $front_post );
            the_content();
            wp_reset_postdata();
        endif;
    else :
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    endif;
    ?>
</main>

<?php
get_footer();
