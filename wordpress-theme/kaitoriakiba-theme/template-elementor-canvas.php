<?php
/**
 * Template Name: Elementor Canvas (No Header/Footer)
 * Description: Clean blank canvas page template for landing pages.
 *
 * @package KaitoriAkiba
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'elementor-canvas' ); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<main id="primary" class="site-main kaitoriakiba-elementor-canvas">
    <?php
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
