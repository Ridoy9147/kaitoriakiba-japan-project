<?php
/**
 * Template Name: Elementor Full Width (Kaitori Akiba)
 * Description: Full-width page template designed for Elementor Page Builder with Kaitori Akiba header and footer.
 *
 * @package KaitoriAkiba
 */

get_header();
?>

<main id="primary" class="site-main kaitoriakiba-elementor-fullwidth" style="width: 100%; padding: 0; margin: 0;">
    <?php
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php
get_footer();
