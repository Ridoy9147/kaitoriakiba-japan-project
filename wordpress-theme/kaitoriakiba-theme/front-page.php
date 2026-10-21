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
?>

<main id="primary" class="site-main kaitoriakiba-front-page elementor-content-wrap">
    <?php
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php
get_footer();
