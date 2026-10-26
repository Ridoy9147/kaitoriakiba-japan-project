<?php
/**
 * The template for displaying all pages
 * Supports Elementor editing seamlessly while providing clean fallback.
 *
 * @package KaitoriAkiba
 */

get_header();

if ( kaitoriakiba_is_elementor_page() ) :
    ?>
    <main id="primary" class="site-main elementor-content-wrap">
        <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
        ?>
    </main>
    <?php
else :
    ?>
    <div class="container main-content-container" style="margin-top: 30px; margin-bottom: 60px;">
        <main id="primary" class="site-main" style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:32px 28px; box-shadow:0 4px 15px rgba(0,0,0,0.02);">
            <?php
            while ( have_posts() ) : the_post();
                ?>
                <header class="entry-header" style="margin-bottom: 24px; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 14px;">
                    <h1 class="entry-title" style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 0;"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content" style="font-size: 14.5px; line-height: 1.8; color: #334155;">
                    <?php the_content(); ?>
                </div>
                <?php
            endwhile;
            ?>
        </main>
    </div>
    <?php
endif;

get_footer();
