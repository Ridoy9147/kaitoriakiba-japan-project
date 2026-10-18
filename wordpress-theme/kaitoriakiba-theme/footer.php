<?php
/**
 * Kaitori Akiba Footer Template
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

    <!-- Modern Site Footer -->
    <footer style="background: #0f172a; color: #94a3b8; padding: 50px 0 24px 0; margin-top: 60px;">
        <div class="container">
            <div class="row">
                <!-- Col 1: Brand & Store Info -->
                <div class="col-lg-4 col-md-4 col-sm-12" style="margin-bottom: 24px;">
                    <div style="font-size: 20px; font-weight: 900; color: #ffffff; margin-bottom: 12px;">
                        KAITORI AKIBA <span style="font-size: 13px; color: #ef4444;"><?php _e( '買取アキバ', 'kaitoriakiba' ); ?></span>
                    </div>
                    <p style="font-size: 13px; line-height: 1.6; color: #94a3b8;">
                        <?php _e( '秋葉原駅徒歩2分。iPhone、Android、iPad、MacBookなどの高価買取専門店。最新の市場相場に基づいた適正・透明な高額査定をお約束します。', 'kaitoriakiba' ); ?>
                    </p>
                    <div style="font-size: 13px; color: #cbd5e1; margin-top: 14px;">
                        <i class="fa fa-map-marker" style="color: #ef4444; margin-right: 6px;"></i> <?php _e( '東京都千代田区外神田1丁目（秋葉原店）', 'kaitoriakiba' ); ?><br>
                        <i class="fa fa-clock-o" style="color: #ef4444; margin-right: 6px;"></i> <?php _e( '営業時間: 10:00 〜 19:30 (年中無休)', 'kaitoriakiba' ); ?>
                    </div>
                </div>

                <!-- Col 2: Services Menu -->
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 24px;">
                    <div style="font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 14px;"><?php _e( 'サービス案内', 'kaitoriakiba' ); ?></div>
                    <ul style="list-style: none; padding: 0; font-size: 13px; line-height: 2;">
                        <?php
                        if ( has_nav_menu( 'footer_1' ) ) {
                            wp_nav_menu( array(
                                'theme_location' => 'footer_1',
                                'container'      => false,
                                'items_wrap'     => '%3$s',
                                'fallback_cb'    => false,
                            ) );
                        } else {
                            ?>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>" style="color: #94a3b8; text-decoration: none;"><?php _e( 'iPhone買取', 'kaitoriakiba' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>" style="color: #94a3b8; text-decoration: none;"><?php _e( '店頭買取の流れ', 'kaitoriakiba' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>" style="color: #94a3b8; text-decoration: none;"><?php _e( '郵送買取の流れ', 'kaitoriakiba' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>" style="color: #94a3b8; text-decoration: none;"><?php _e( '本人確認 (eKYC)', 'kaitoriakiba' ); ?></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>

                <!-- Col 3: Customer Guide -->
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" style="margin-bottom: 24px;">
                    <div style="font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 14px;"><?php _e( 'ご利用案内', 'kaitoriakiba' ); ?></div>
                    <ul style="list-style: none; padding: 0; font-size: 13px; line-height: 2;">
                        <?php
                        if ( has_nav_menu( 'footer_2' ) ) {
                            wp_nav_menu( array(
                                'theme_location' => 'footer_2',
                                'container'      => false,
                                'items_wrap'     => '%3$s',
                                'fallback_cb'    => false,
                            ) );
                        } else {
                            ?>
                            <li><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" style="color: #94a3b8; text-decoration: none;"><?php _e( '新規会員登録', 'kaitoriakiba' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/register/#login' ) ); ?>" style="color: #94a3b8; text-decoration: none;"><?php _e( 'ログイン', 'kaitoriakiba' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>" style="color: #94a3b8; text-decoration: none;"><?php _e( 'よくある質問', 'kaitoriakiba' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>" style="color: #94a3b8; text-decoration: none;"><?php _e( '店舗アクセス', 'kaitoriakiba' ); ?></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>

                <!-- Col 4: Legal & Antique License -->
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="margin-bottom: 24px;">
                    <div style="font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 14px;"><?php _e( '古物営業法に基づく表記', 'kaitoriakiba' ); ?></div>
                    <p style="font-size: 12px; line-height: 1.6; color: #64748b;">
                        <?php _e( '東京都公安委員会許可 第301031200000号<br>個人情報保護方針に基づき、お客様からお預かりした身分証明書および端末データは厳重に管理・保護いたします。', 'kaitoriakiba' ); ?>
                    </p>
                </div>
            </div>

            <!-- Copyright -->
            <div style="border-top: 1px solid #1e293b; padding-top: 20px; text-align: center; font-size: 12px; color: #64748b;">
                &copy; <?php echo date( 'Y' ); ?> Kaitori Akiba (<?php _e( '買取アキバ', 'kaitoriakiba' ); ?>). All Rights Reserved.
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
