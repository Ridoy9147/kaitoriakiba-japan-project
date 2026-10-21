<?php
/**
 * Main Template File (Fallback)
 *
 * @package KaitoriAkiba
 */

get_header();

if ( is_front_page() ) {
    get_template_part( 'front-page' );
} else {
    ?>
    <div class="container main-content-container" style="margin-top: 30px; margin-bottom: 60px;">
        <main id="primary" class="site-main" style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:32px 28px; box-shadow:0 4px 15px rgba(0,0,0,0.02);">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-bottom: 24px;">
                        <header class="entry-header" style="margin-bottom: 16px;">
                            <h1 class="entry-title" style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;"><?php the_title(); ?></h1>
                        </header>
                        <div class="entry-content" style="font-size: 14.5px; line-height: 1.8; color: #334155;">
                            <?php the_content(); ?>
                        </div>
                    </article>
                    <?php
                endwhile;
            else :
                echo '<div class="alert alert-info">' . esc_html__( '記事が見つかりませんでした。(Nothing found)', 'kaitoriakiba' ) . '</div>';
            endif;
            ?>
        </main>
    </div>
    <?php
}

get_footer();
