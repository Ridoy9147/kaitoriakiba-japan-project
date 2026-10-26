<?php
/**
 * The template for displaying all single posts
 *
 * @package KaitoriAkiba
 */

get_header();
?>
<div class="container main-content-container" style="margin-top: 30px; margin-bottom: 60px;">
    <main id="primary" class="site-main" style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:32px 28px; box-shadow:0 4px 15px rgba(0,0,0,0.02);">
        <?php
        while ( have_posts() ) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header" style="margin-bottom: 24px; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 14px;">
                    <h1 class="entry-title" style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;"><?php the_title(); ?></h1>
                    <div class="entry-meta" style="font-size: 12.5px; color: #64748b;">
                        <span><i class="fa fa-calendar"></i> <?php echo get_the_date(); ?></span>
                    </div>
                </header>

                <div class="entry-content" style="font-size: 14.5px; line-height: 1.8; color: #334155;">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </main>
</div>
<?php
get_footer();
