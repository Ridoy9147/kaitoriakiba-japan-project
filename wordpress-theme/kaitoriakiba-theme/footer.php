<?php
/**
 * Kaitori Akiba Footer Template
 * 
 * 100% Authentic Kaitori Akiba Footer matching index.html:
 * - Sister Services Affiliated Banner Grid (href_list)
 * - Comprehensive Footer Sitemap (footer_sitemap)
 * - Bottom Bar Navigation (footer-bar)
 * - SSL & PrivacyMark (P-Mark) Security Certifications
 * - Corporate Info & Tokyo Secondhand Dealer License No. 301031307510
 * - Social Share Toolbar & Page-Top Button
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_uri = get_template_directory_uri();
?>

<!--footer-s-->
<div class="bg-white">
    <div class="href_list">
        <div class="container">
            <div class="gutter-10 clearflex row">
                <div class="href_item mtop10 col-xs-6 col-sm-3">
                    <a href="https://kaitoriakiba.jp" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180512/phone.jpg" alt="Smartphones" class="img-responsive" />
                    </a>
                </div>
                <div class="href_item mtop10 col-xs-6 col-sm-3">
                    <a href="https://ipadkaitori.jp" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180512/ipad.jpg" alt="Tablets" class="img-responsive" />
                    </a>
                </div>
                <div class="href_item mtop10 col-xs-6 col-sm-3">
                    <a href="https://gamekaitori.jp" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180512/game.jpg" alt="Gaming Consoles" class="img-responsive" />
                    </a>
                </div>
                <div class="href_item mtop10 col-xs-6 col-sm-3">
                    <a href="https://cosmekaitori.jp" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180512/meizhuang.jpg" alt="Cosmetics" class="img-responsive" />
                    </a>
                </div>
                <div class="href_item mtop10 col-xs-6 col-sm-3">
                    <a href="https://kadenkaitori.tokyo" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180702/20180628171113.jpg" alt="Home Appliances" class="img-responsive" />
                    </a>
                </div>
                <div class="href_item mtop10 col-xs-6 col-sm-3">
                    <a href="https://camerakaitori.tokyo" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180702/20180628171104.jpg" alt="Cameras" class="img-responsive" />
                    </a>
                </div>
                <div class="href_item mtop10 col-xs-6 col-sm-3">
                    <a href="https://pckaitori.tokyo" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180702/20180628170919.jpg" alt="PC &amp; Laptops" class="img-responsive" />
                    </a>
                </div>
                <div class="href_item mtop10 col-xs-6 col-sm-3">
                    <a href="https://kaitoriakiba.jp/" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180712/kaitoriakiba_link.jpg" alt="Kaitori Akiba Link" class="img-responsive" />
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Authentic Footer Sitemap -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 mbtm10 mtop20 sitemap">
                <button class="btn btn-success btn-block visible-xs mbtm10" type="button" data-toggle="collapse" data-target="#footer_sitemap">
                    Sitemap <i class="fa fa-bars" aria-hidden="true"></i> <i class="fa fa-sort" aria-hidden="true"></i>
                </button>
                <div class="footer_sitemap">
                    <div class="footer_sitemap_menu collapse in" id="footer_sitemap">
                        <ul>
                            <p><?php _e( 'Menu', 'kaitoriakiba' ); ?></p>
                            <li><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '買取申込 (Online Application)', 'kaitoriakiba' ); ?></span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/shop-purchase-flow/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '店頭買取の流れ (In-Store Flow)', 'kaitoriakiba' ); ?></span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/delivery-purchase-flow/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '郵送買取の流れ (Mail-in Flow)', 'kaitoriakiba' ); ?></span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/corporation-purchase-flow/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '法人買取案内 (Corporate Flow)', 'kaitoriakiba' ); ?></span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '本人確認 (eKYC Verification)', 'kaitoriakiba' ); ?></span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '新規会員登録 (Register)', 'kaitoriakiba' ); ?></span></a></li>
                        </ul>
                        <ul>
                            <p><?php _e( 'Price Lists by Carrier', 'kaitoriakiba' ); ?></p>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?carrier=docomo' ) ); ?>"><i class="fa fa-caret-right"></i><span>docomo 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?carrier=au' ) ); ?>"><i class="fa fa-caret-right"></i><span>au 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?carrier=softbank' ) ); ?>"><i class="fa fa-caret-right"></i><span>SoftBank 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?carrier=ymobile' ) ); ?>"><i class="fa fa-caret-right"></i><span>Y!mobile 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?carrier=uqmobile' ) ); ?>"><i class="fa fa-caret-right"></i><span>UQ mobile 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?carrier=simfree' ) ); ?>"><i class="fa fa-caret-right"></i><span>SIMフリー 買取価格一覧</span></a></li>
                        </ul>
                        <ul>
                            <p><?php _e( 'Price Lists by Series', 'kaitoriakiba' ); ?></p>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?series=iphone' ) ); ?>"><i class="fa fa-caret-right"></i><span>iPhone 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?series=xperia' ) ); ?>"><i class="fa fa-caret-right"></i><span>Xperia 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?series=galaxy' ) ); ?>"><i class="fa fa-caret-right"></i><span>Galaxy 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?series=aquos' ) ); ?>"><i class="fa fa-caret-right"></i><span>AQUOS 買取価格一覧</span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/devices/?series=pixel' ) ); ?>"><i class="fa fa-caret-right"></i><span>Google Pixel 買取価格一覧</span></a></li>
                        </ul>
                        <ul>
                            <p><?php _e( 'Customer Support & FAQ', 'kaitoriakiba' ); ?></p>
                            <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( 'よくある質問 (FAQ)', 'kaitoriakiba' ); ?></span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '分割残債あり(△)買取対応について', 'kaitoriakiba' ); ?></span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '店舗アクセス・営業時間案内', 'kaitoriakiba' ); ?></span></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><i class="fa fa-caret-right"></i><span><?php _e( '身分証明書・住民票の提出方法', 'kaitoriakiba' ); ?></span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="container-fluid footer-bar">
        <ul>
            <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
            <li><a href="<?php echo esc_url( home_url( '/company/' ) ); ?>">About Us</a></li>
            <li><a href="<?php echo esc_url( home_url( '/privacypolicy/' ) ); ?>">Privacy Policy</a></li>
            <li><a href="<?php echo esc_url( home_url( '/site-terms/' ) ); ?>">Buyback Terms</a></li>
            <li><a href="<?php echo esc_url( home_url( '/shop-terms/' ) ); ?>">In-Store Terms</a></li>
            <li><a href="<?php echo esc_url( home_url( '/delivery-terms/' ) ); ?>">Mail-in Terms</a></li>
            <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact Us</a></li>
        </ul>
    </div>
    <div class="container footer">
        <div class="col-xs-12 col-sm-4">
            <div class="ssl">
                <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/foot/ssl-logo.png" width="126px" alt="SSL 256-bit Secure" />
                <div class="cell_text">
                    <p><?php _e( '買取アキバでは、お客様のプライバシー保護のため256bit SSL暗号化通信を導入しております。', 'kaitoriakiba' ); ?></p>
                </div>
                <a href="https://privacymark.jp/" target="_blank" rel="noopener">
                    <div style="float: left;">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/foot/ba-logo.png" width="70px" alt="Privacy Mark" />
                        <div class="cell_text">
                            <p><?php _e( '個人情報保護体制を整備し、プライバシーマーク（Pマーク）認証を取得しております。', 'kaitoriakiba' ); ?></p>
                        </div>
                    </div>
                    <div class="clear" style="clear:both;"></div>
                </a>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4">
            <p class="text-center mtop25 mbtm25" style="color:#666; font-size:12px; line-height:1.8;">
                <strong style="color:#333; font-size:14px;">Kaitori Akiba Co., Ltd. (株式会社買取アキバ)</strong><br>
                東京都公安委員会許可 古物商許可証 第301031307510号<br>
                秋葉原店: 東京都千代田区外神田1丁目<br>
                公式サイト: <a href="https://kaitoriakiba.jp" style="color:#666;">https://kaitoriakiba.jp</a><br>
                Copyright &copy; <?php echo date( 'Y' ); ?> Kaitori Akiba (kaitoriakiba.jp). All Rights Reserved.
            </p>
        </div>
        <div class="col-xs-12 col-sm-4">
            <div class="share_ul hidden-xs" style="text-align:right; margin-top:25px;">
                <div class="facebook_share share-item" style="display:inline-block; margin:0 3px;">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( home_url('/') ); ?>" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/share/facebook.png" alt="Facebook Share" />
                    </a>
                </div>
                <div class="twitter_share share-item" style="display:inline-block; margin:0 3px;">
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( home_url('/') ); ?>&text=<?php echo urlencode( get_bloginfo('name') ); ?>" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/share/twitter.png" alt="Twitter Share" />
                    </a>
                </div>
                <div class="hatena_share share-item" style="display:inline-block; margin:0 3px;">
                    <a href="https://b.hatena.ne.jp/entry/<?php echo urlencode( home_url('/') ); ?>" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/share/B.png" alt="Hatena Bookmark" />
                    </a>
                </div>
                <div class="LINE_share share-item" style="display:inline-block; margin:0 3px;">
                    <a href="https://line.me/R/msg/text/?<?php echo urlencode( home_url('/') ); ?>" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/share/LINE.png" alt="LINE Share" />
                    </a>
                </div>
            </div>
        </div>
    </div>
    <a href="#" class="page-top" style="display:block;">▲</a>
</footer>
<!--footer-e-->

<?php wp_footer(); ?>
</body>
</html>
