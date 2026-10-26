<?php
/**
 * Template Name: Member Dashboard (マイページ)
 *
 * Logged-in customer dashboard: buyback order history, eKYC status, and favorites.
 * Previously linked everywhere in the UI but had no template.
 *
 * @package KaitoriAkiba
 */

get_header();

if ( kaitoriakiba_is_elementor_page() ) {
    while ( have_posts() ) { the_post(); the_content(); }
    get_footer();
    return;
}
?>
<div class="container" style="margin-top:34px; margin-bottom:60px;">
<?php if ( ! is_user_logged_in() ) : ?>

    <div style="max-width:520px; margin:0 auto; background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:36px; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,.05);">
        <div style="font-size:40px; color:#ef4444; margin-bottom:10px;"><i class="fa fa-user-circle-o"></i></div>
        <h1 style="font-size:20px; font-weight:800; margin-bottom:8px;"><?php kaitori_te( 'Member Dashboard (My Page)', 'マイページ (Member Dashboard)' ); ?></h1>
        <p style="color:#64748b; font-size:14px; margin-bottom:20px;"><?php kaitori_te( 'Please log in to use My Page.', 'マイページのご利用にはログインが必要です。' ); ?></p>
        <a href="<?php echo esc_url( home_url( '/register/?action=login' ) ); ?>" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; border-radius:10px; padding:12px 28px;"><?php kaitori_te( 'Login / Register', 'ログイン / 会員登録' ); ?></a>
    </div>

<?php else :
    $user   = wp_get_current_user();
    $uid    = $user->ID;
    $email  = $user->user_email;

    // Orders belonging to this customer (by linked user id OR applicant email).
    $orders = get_posts( array(
        'post_type'      => 'buyback_order',
        'posts_per_page' => 50,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(
            'relation' => 'OR',
            array( 'key' => '_customer_user_id', 'value' => $uid ),
            array( 'key' => '_applicant_email', 'value' => $email ),
        ),
    ) );

    // eKYC status.
    $kyc_status = get_user_meta( $uid, '_kaitori_kyc_status', true );
    if ( ! $kyc_status ) {
        $k = get_posts( array( 'post_type' => 'kyc_record', 'post_status' => 'any', 'numberposts' => 1, 'meta_key' => '_kyc_email', 'meta_value' => $email ) );
        $kyc_status = $k ? ( get_post_meta( $k[0]->ID, '_kyc_status', true ) ?: 'pending' ) : 'none';
    }
    $kyc_map = array(
        'verified' => array( 'bg' => '#dcfce7', 'c' => '#15803d', 't' => kaitori_t( '✓ Verified', '✓ 本人確認済み' ) ),
        'pending'  => array( 'bg' => '#fef3c7', 'c' => '#b45309', 't' => kaitori_t( '⏳ Under review', '⏳ 確認中' ) ),
        'rejected' => array( 'bg' => '#fee2e2', 'c' => '#b91c1c', 't' => kaitori_t( '✗ Rejected · resubmit', '✗ 不備あり・再提出' ) ),
        'none'     => array( 'bg' => '#f1f5f9', 'c' => '#64748b', 't' => kaitori_t( 'Not submitted', '未提出' ) ),
    );
    $kyc_info = isset( $kyc_map[ $kyc_status ] ) ? $kyc_map[ $kyc_status ] : $kyc_map['none'];

    $favorites = function_exists( 'kaitoriakiba_get_user_favorites' ) ? kaitoriakiba_get_user_favorites( $uid ) : array();
    ?>

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
        <h1 style="font-size:22px; font-weight:900; margin:0;"><i class="fa fa-user-circle" style="color:#ef4444;"></i> <?php echo esc_html( sprintf( kaitori_t( "%s's My Page", '%s 様のマイページ' ), $user->display_name ?: $user->user_login ) ); ?></h1>
        <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-default" style="font-weight:700; border-radius:8px;"><i class="fa fa-sign-out"></i> <?php kaitori_te( 'Logout', 'ログアウト' ); ?></a>
    </div>

    <!-- eKYC status card -->
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <div style="font-weight:800; font-size:15px; margin-bottom:4px;"><?php kaitori_te( 'Identity Verification (eKYC) Status', '本人確認 (eKYC) ステータス' ); ?></div>
            <span style="display:inline-block; padding:5px 14px; border-radius:12px; font-weight:800; font-size:13px; background:<?php echo esc_attr( $kyc_info['bg'] ); ?>; color:<?php echo esc_attr( $kyc_info['c'] ); ?>;"><?php echo esc_html( $kyc_info['t'] ); ?></span>
        </div>
        <?php if ( 'verified' !== $kyc_status ) : ?>
            <a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; border-radius:8px;"><?php kaitori_te( 'Verify identity →', '本人確認を行う →' ); ?></a>
        <?php endif; ?>
    </div>

    <!-- Orders -->
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; margin-bottom:22px;">
        <div style="font-weight:800; font-size:16px; margin-bottom:14px;"><i class="fa fa-clipboard" style="color:#ef4444;"></i> <?php kaitori_te( 'Buyback Application History', '買取お申込み履歴' ); ?></div>
        <?php if ( $orders ) : ?>
            <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
                <thead><tr style="text-align:left; color:#64748b; border-bottom:2px solid #f1f5f9;">
                    <th style="padding:10px 8px;"><?php kaitori_te( 'Order No.', '受付番号' ); ?></th>
                    <th style="padding:10px 8px;"><?php kaitori_te( 'Device', '機種' ); ?></th>
                    <th style="padding:10px 8px;"><?php kaitori_te( 'Quote', '査定額' ); ?></th>
                    <th style="padding:10px 8px;"><?php kaitori_te( 'Status', 'ステータス' ); ?></th>
                    <th style="padding:10px 8px;"><?php kaitori_te( 'Applied', '申込日' ); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ( $orders as $o ) :
                    $o_model = get_post_meta( $o->ID, '_device_model', true );
                    $o_stor  = get_post_meta( $o->ID, '_device_storage', true );
                    $o_price = (int) get_post_meta( $o->ID, '_estimated_price', true );
                    $o_st    = get_post_meta( $o->ID, '_order_status', true ) ?: 'pending';
                    $si      = function_exists( 'kaitoriakiba_get_order_status' ) ? kaitoriakiba_get_order_status( $o_st ) : array( 'label_ja' => $o_st, 'bg' => '#f1f5f9', 'color' => '#64748b' );
                ?>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 8px; font-weight:700;"><?php echo esc_html( $o->post_title ); ?></td>
                        <td style="padding:10px 8px;"><?php echo esc_html( trim( $o_model . ' ' . $o_stor ) ); ?></td>
                        <td style="padding:10px 8px; font-weight:800; color:#dc2626;">¥<?php echo number_format( $o_price ); ?></td>
                        <td style="padding:10px 8px;"><span style="padding:3px 10px; border-radius:10px; font-size:11.5px; font-weight:700; background:<?php echo esc_attr( $si['bg'] ); ?>; color:<?php echo esc_attr( $si['color'] ); ?>;"><?php echo esc_html( kaitori_t( $si['label'], $si['label_ja'] ) ); ?></span></td>
                        <td style="padding:10px 8px; color:#64748b;"><?php echo esc_html( get_the_date( 'Y.m.d', $o ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php else : ?>
            <p style="color:#64748b; margin:0;"><?php kaitori_te( 'No buyback applications yet.', 'まだ買取お申込みはありません。' ); ?> <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" style="color:#ef4444; font-weight:700;"><?php kaitori_te( 'Apply for buyback →', '買取を申し込む →' ); ?></a></p>
        <?php endif; ?>
    </div>

    <!-- Favorites -->
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px;">
        <div style="font-weight:800; font-size:16px; margin-bottom:14px;"><i class="fa fa-star" style="color:#f59e0b;"></i> <?php kaitori_te( 'Favorite Devices', 'お気に入り端末' ); ?></div>
        <?php if ( $favorites ) : ?>
            <div class="row">
            <?php foreach ( $favorites as $fid ) :
                $fp = get_post( $fid );
                if ( ! $fp || 'kaitori_device' !== $fp->post_type ) { continue; }
                $fprice = (int) get_post_meta( $fid, '_max_buyback_price', true );
                $fimg   = get_post_meta( $fid, '_device_primary_image_url', true );
                if ( ! $fimg ) { $fimg = has_post_thumbnail( $fid ) ? get_the_post_thumbnail_url( $fid, 'medium' ) : get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png'; }
            ?>
                <div class="col-sm-4 col-xs-6" style="margin-bottom:16px;">
                    <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; text-align:center;">
                        <a href="<?php echo esc_url( get_permalink( $fid ) ); ?>"><img src="<?php echo esc_url( $fimg ); ?>" alt="<?php echo esc_attr( get_the_title( $fid ) ); ?>" style="max-height:120px; object-fit:contain;"></a>
                        <div style="font-weight:700; font-size:13px; margin-top:8px;"><a href="<?php echo esc_url( get_permalink( $fid ) ); ?>" style="color:#0f172a; text-decoration:none;"><?php echo esc_html( get_the_title( $fid ) ); ?></a></div>
                        <div style="color:#dc2626; font-weight:800; margin-top:4px;">¥<?php echo number_format( $fprice ); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p style="color:#64748b; margin:0;"><?php kaitori_te( 'Add devices you like from the product list using the ★ button.', '商品一覧の「★」から気になる端末を登録できます。' ); ?></p>
        <?php endif; ?>
    </div>

<?php endif; ?>
</div>
<?php
get_footer();
