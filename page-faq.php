<?php
/**
 * Template Name: FAQ (よくある質問)
 *
 * Accordion FAQ page. The Q&A set is filterable so the same data feeds FAQPage
 * structured data (see inc/seo.php). Previously linked everywhere but had no page.
 *
 * @package KaitoriAkiba
 */

get_header();

if ( kaitoriakiba_is_elementor_page() ) {
    while ( have_posts() ) { the_post(); the_content(); }
    get_footer();
    return;
}

$faqs = kaitoriakiba_get_faqs();
?>
<div class="container" style="margin-top:34px; margin-bottom:60px;">
    <div style="max-width:820px; margin:0 auto;">
        <div style="text-align:center; margin-bottom:28px;">
            <h1 style="font-size:24px; font-weight:900; color:#0f172a;"><i class="fa fa-question-circle" style="color:#ef4444;"></i> <?php kaitori_te( 'Frequently Asked Questions (FAQ)', 'よくある質問 (FAQ)' ); ?></h1>
            <p style="color:#64748b; font-size:14px;"><?php kaitori_te( 'A collection of common questions from our customers about Kaitori Akiba services.', '買取アキバのサービスについて、お客様からよくいただくご質問をまとめました。' ); ?></p>
        </div>

        <div class="kaitori-faq">
            <?php foreach ( $faqs as $i => $faq ) : ?>
                <div class="kaitori-faq-item" style="border:1px solid #e2e8f0; border-radius:12px; margin-bottom:12px; overflow:hidden; background:#fff;">
                    <button type="button" class="kaitori-faq-q" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>" style="width:100%; text-align:left; background:none; border:none; padding:16px 20px; font-weight:800; font-size:15px; color:#0f172a; cursor:pointer; display:flex; justify-content:space-between; align-items:center; gap:12px;">
                        <span><i class="fa fa-comment-o" style="color:#ef4444; margin-right:8px;"></i><?php echo esc_html( $faq['q'] ); ?></span>
                        <i class="fa fa-chevron-down kaitori-faq-ico" style="transition:transform .2s; <?php echo 0 === $i ? 'transform:rotate(180deg);' : ''; ?>"></i>
                    </button>
                    <div class="kaitori-faq-a" style="padding:0 20px; max-height:<?php echo 0 === $i ? '500px' : '0'; ?>; overflow:hidden; transition:max-height .25s ease, padding .25s ease; <?php echo 0 === $i ? 'padding-bottom:18px;' : ''; ?>">
                        <div style="color:#334155; font-size:14px; line-height:1.8;"><?php echo wp_kses_post( wpautop( $faq['a'] ) ); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center; margin-top:28px;">
            <p style="color:#64748b; font-size:13.5px;"><?php kaitori_te( "If this doesn't solve your question, feel free to contact us.", '解決しない場合はお気軽にお問い合わせください。' ); ?></p>
            <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; border-radius:10px; padding:12px 28px;"><?php kaitori_te( 'Apply for Buyback →', '買取を申し込む →' ); ?></a>
        </div>
    </div>
</div>

<script>
(function(){
    document.querySelectorAll('.kaitori-faq-q').forEach(function(btn){
        btn.addEventListener('click', function(){
            var ans = btn.parentNode.querySelector('.kaitori-faq-a');
            var ico = btn.querySelector('.kaitori-faq-ico');
            var open = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', open ? 'false' : 'true');
            if (open) {
                ans.style.maxHeight = '0'; ans.style.paddingBottom = '0';
                if (ico) ico.style.transform = 'rotate(0deg)';
            } else {
                ans.style.maxHeight = '500px'; ans.style.paddingBottom = '18px';
                if (ico) ico.style.transform = 'rotate(180deg)';
            }
        });
    });
})();
</script>
<?php
get_footer();
