<?php
/**
 * Template Name: Store Access (店舗アクセス)
 *
 * Store location, business hours and map. Previously linked everywhere but had no page.
 * Store details are filterable so SEO LocalBusiness schema can reuse them.
 *
 * @package KaitoriAkiba
 */

get_header();

if ( kaitoriakiba_is_elementor_page() ) {
    while ( have_posts() ) { the_post(); the_content(); }
    get_footer();
    return;
}

$store = kaitoriakiba_store_info();
?>
<div class="container" style="margin-top:34px; margin-bottom:60px;">
    <div style="max-width:860px; margin:0 auto;">
        <div style="text-align:center; margin-bottom:28px;">
            <h1 style="font-size:24px; font-weight:900; color:#0f172a;"><i class="fa fa-map-marker" style="color:#ef4444;"></i> <?php kaitori_te( 'Store Access', '店舗アクセス' ); ?></h1>
            <p style="color:#64748b; font-size:14px;"><?php echo esc_html( $store['name'] ); ?></p>
        </div>

        <div class="row">
            <div class="col-md-5" style="margin-bottom:20px;">
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:22px;">
                    <table style="width:100%; font-size:14px; line-height:1.9;">
                        <tr><td style="color:#64748b; width:120px; vertical-align:top;"><?php kaitori_te( 'Store', '店舗名' ); ?></td><td style="font-weight:700;"><?php echo esc_html( $store['name'] ); ?></td></tr>
                        <tr><td style="color:#64748b; vertical-align:top;"><?php kaitori_te( 'Address', '住所' ); ?></td><td><?php echo esc_html( $store['address'] ); ?></td></tr>
                        <tr><td style="color:#64748b; vertical-align:top;"><?php kaitori_te( 'Hours', '営業時間' ); ?></td><td><?php echo esc_html( $store['hours'] ); ?></td></tr>
                        <tr><td style="color:#64748b; vertical-align:top;"><?php kaitori_te( 'Closed', '定休日' ); ?></td><td><?php echo esc_html( $store['holiday'] ); ?></td></tr>
                        <tr><td style="color:#64748b; vertical-align:top;"><?php kaitori_te( 'Access', 'アクセス' ); ?></td><td><?php echo esc_html( $store['access'] ); ?></td></tr>
                        <?php if ( ! empty( $store['phone'] ) ) : ?>
                        <tr><td style="color:#64748b; vertical-align:top;"><?php kaitori_te( 'Phone', '電話' ); ?></td><td><a href="tel:<?php echo esc_attr( $store['phone'] ); ?>" style="color:#ef4444; font-weight:700;"><?php echo esc_html( $store['phone'] ); ?></a></td></tr>
                        <?php endif; ?>
                        <tr><td style="color:#64748b; vertical-align:top;"><?php kaitori_te( 'License', '許可' ); ?></td><td style="font-size:12.5px; color:#475569;"><?php echo esc_html( $store['license'] ); ?></td></tr>
                    </table>
                    <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                        <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; border-radius:8px;"><?php kaitori_te( 'Book In-Store Buyback', '店頭買取を予約' ); ?></a>
                        <a href="https://www.google.com/maps/search/<?php echo rawurlencode( $store['address'] ); ?>" target="_blank" rel="noopener" class="btn btn-default" style="font-weight:700; border-radius:8px;"><i class="fa fa-external-link"></i> <?php kaitori_te( 'Google Maps', 'Googleマップ' ); ?></a>
                    </div>
                </div>
            </div>
            <div class="col-md-7" style="margin-bottom:20px;">
                <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
                    <iframe title="<?php echo esc_attr( $store['name'] ); ?>" width="100%" height="380" style="border:0; display:block;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q=<?php echo rawurlencode( $store['address'] ); ?>&output=embed"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
