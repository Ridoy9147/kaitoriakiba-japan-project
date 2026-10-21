<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package KaitoriAkiba
 */

get_header();
?>
<div class="container" style="margin-top: 60px; margin-bottom: 80px; text-align: center;">
    <div style="max-width: 540px; margin: 0 auto; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 48px 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
        <div style="font-size: 64px; font-weight: 900; color: #ef4444; line-height: 1; margin-bottom: 14px;">404</div>
        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 12px;"><?php _e( 'Page Not Found (お探しのページが見つかりません)', 'kaitoriakiba' ); ?></h2>
        <p style="font-size: 13.5px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">
            <?php _e( '申し訳ありません。アクセスいただいたページは削除されたか、URLが変更された可能性があります。', 'kaitoriakiba' ); ?>
        </p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; padding:12px 28px; border-radius:8px;">
            <i class="fa fa-home"></i> <?php _e( 'トップページへ戻る (Back to Home)', 'kaitoriakiba' ); ?>
        </a>
    </div>
</div>
<?php
get_footer();
