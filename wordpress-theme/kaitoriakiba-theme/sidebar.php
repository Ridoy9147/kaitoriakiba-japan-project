<?php
/**
 * Sidebar Template for Kaitori Akiba
 *
 * Banners, social links and the newsletter box are editable at
 * Appearance → Sidebar. The carrier / brand / series logo lists come from the
 * matching taxonomy terms (logo per term is set on the term edit screen; a
 * legacy /upload/{folder}/{slug} image is used as a fallback). The Quick Links
 * block uses the "Sidebar Quick Links" menu when one is assigned.
 *
 * @package KaitoriAkiba
 */

$kt_uri = get_template_directory_uri();

// Resolve editable pieces (fall back to the theme defaults when unset).
$banner1_img  = kaitoriakiba_sidebar_setting( 'banner1_img', $kt_uri . '/assets/images/navigation/ad_top_1.jpg' );
$banner1_link = kaitoriakiba_sidebar_setting( 'banner1_link', home_url( '/sell-apply/' ) );
$social_img   = kaitoriakiba_sidebar_setting( 'social_img', $kt_uri . '/assets/images/navigation/zxlx_top.jpg' );
$twitter_url  = kaitoriakiba_social_url( 'twitter', 'https://twitter.com/kaitoriakiba' );
$line_url     = kaitoriakiba_social_url( 'line', 'https://lin.ee/5wvtHzl' );
$banner2_img  = kaitoriakiba_sidebar_setting( 'banner2_img', $kt_uri . '/upload/ad/20210621/member_rank02.png' );
$banner2_link = kaitoriakiba_sidebar_setting( 'banner2_link', home_url( '/register/' ) );
$news_img     = kaitoriakiba_sidebar_setting( 'newsletter_img', $kt_uri . '/assets/images/email/weekly.jpg' );
$news_text    = kaitoriakiba_sidebar_setting( 'newsletter_text', kaitori_t( "We'll email you the latest high-value buyback rates and member-only campaign news.", '最新の高価買取相場や会員限定キャンペーン情報をメールでお届けします。' ) );
$news_link    = kaitoriakiba_sidebar_setting( 'newsletter_link', home_url( '/register/' ) );
?>
<!--sidebar-s-->
<div class="collapse in" id="main-menu">
    <div class="navigation-ad">
        <ul class="gutter-0 img-margin" style="list-style:none; padding:0; margin:0;">
            <?php if ( $banner1_img ) : ?>
            <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 img-left" style="margin-bottom:10px;">
                <a href="<?php echo esc_url( $banner1_link ); ?>">
                    <img src="<?php echo esc_url( $banner1_img ); ?>" alt="First-Time Guide" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:8px;">
                </a>
            </li>
            <div class="clear"></div>
            <?php endif; ?>
            <li class="zxlx_nav_box" style="margin:0 0 10px 0; width:100%;">
                <?php if ( $social_img ) : ?>
                <div><img src="<?php echo esc_url( $social_img ); ?>" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" /></div>
                <?php endif; ?>
                <?php if ( $twitter_url ) : ?>
                <div><a href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url( $kt_uri ); ?>/assets/images/navigation/zxlx_twitter.jpg" alt="Twitter" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" border="0"></a></div>
                <?php endif; ?>
                <?php if ( $line_url ) : ?>
                <div><a href="<?php echo esc_url( $line_url ); ?>" target="_blank" rel="noopener"><img src="<?php echo esc_url( $kt_uri ); ?>/assets/images/navigation/zxlx_line.jpg" alt="Add on LINE" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" border="0"></a></div>
                <?php endif; ?>
            </li>
            <?php if ( $banner2_img ) : ?>
            <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 img-left" style="margin-bottom:10px;">
                <a href="<?php echo esc_url( $banner2_link ); ?>">
                    <img src="<?php echo esc_url( $banner2_img ); ?>" alt="Member Benefits" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:8px;">
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <div class="clearfix"></div>
    </div>

    <?php
    // Browse by Carrier / Brand / Series — from taxonomy terms (per-term logo
    // editable on the term screen; legacy /upload image used as a fallback).
    kaitoriakiba_render_sidebar_tax_box(
        'carrier', 's_career', 'career',
        kaitori_t( 'Browse by Carrier', 'キャリアから探す' ),
        array(
            'docomo' => 'upload/career/docomo.jpg', 'au' => 'upload/career/au.jpg', 'softbank' => 'upload/career/softbank.jpg',
            'Y!mobile' => 'upload/career/Y!mobile.jpg', 'uqmobile' => 'upload/career/uqmobile.jpg',
            'simfree' => 'upload/career/simfree.jpg', 'RakutenMobile' => 'upload/career/simfree.jpg',
        )
    );

    kaitoriakiba_render_sidebar_tax_box(
        'brand', 'brand', 'brand',
        kaitori_t( 'Browse by Brand', 'ブランドから探す' ),
        array(
            'apple' => 'upload/brand/apple.jpg', 'Xiaomi' => 'upload/brand/20200603/3.png', 'OPPO' => 'upload/brand/OPPO.jpg',
            'asus' => 'upload/brand/asus.jpg', 'fujitsu' => 'upload/brand/fujitsu.jpg', 'samsung' => 'upload/brand/samsung.jpg',
            'google' => 'upload/brand/google.jpg', 'huawei' => 'upload/brand/huawei.jpg', 'kyocera' => 'upload/brand/kyocera.jpg',
            'motorola' => 'upload/brand/moto.jpg', 'sharp' => 'upload/brand/sharp.jpg', 'sony' => 'upload/brand/sony.jpg',
        )
    );

    kaitoriakiba_render_sidebar_tax_box(
        'series', 'series', 'series',
        kaitori_t( 'Browse by Series', 'シリーズから探す' ),
        array(
            'iphone' => 'upload/series/iphone.jpg', 'xperia' => 'upload/series/xperia.jpg', 'galaxy' => 'upload/series/galaxy.jpg',
            'aquos' => 'upload/series/aquos.jpg', 'arrows' => 'upload/series/arrows.jpg', 'zenfone' => 'upload/series/zenfone.jpg',
            'simple-smartphone' => 'upload/series/simple-smartphone.jpg', 'raijin' => 'upload/series/raijin.jpg',
            'nexus' => 'upload/series/nexus.jpg', 'android-one' => 'upload/series/android-one.jpg',
        )
    );
    ?>

    <!-- Quick Links Menu -->
    <div class="navigation-box2" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
        <div class="navigation-h2" style="background:#0f172a; padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;"><i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i><span><?php kaitori_te( 'Information & Guides', 'ご案内・ガイド' ); ?></span></div>
        <ul class="navigation-h3" style="list-style:none; padding:10px 14px; margin:0;">
            <?php
            if ( has_nav_menu( 'sidebar_quick' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'sidebar_quick',
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'fallback_cb'    => false,
                    'link_before'    => '<i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span>',
                    'link_after'     => '</span>',
                ) );
            } else {
                ?>
                <li style="padding:8px 0; border-bottom:1px solid #f1f5f9;"><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span><?php kaitori_te( 'Online Application', '買取申込' ); ?></span></a></li>
                <li style="padding:8px 0; border-bottom:1px solid #f1f5f9;"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span><?php kaitori_te( 'eKYC Verification', '本人確認' ); ?></span></a></li>
                <li style="padding:8px 0; border-bottom:1px solid #f1f5f9;"><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span><?php kaitori_te( 'Register', '新規会員登録' ); ?></span></a></li>
                <li style="padding:8px 0; border-bottom:1px solid #f1f5f9;"><a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i><span><?php kaitori_te( 'Price List', '買取価格一覧' ); ?></span></a></li>
                <?php
            }
            ?>
        </ul>
    </div>

    <?php if ( $news_img || $news_text ) : ?>
    <!-- Newsletter Box -->
    <div class="email" style="margin-bottom: 20px;">
        <div class="wrapper">
            <?php if ( $news_img ) : ?>
            <div class="header">
                <img src="<?php echo esc_url( $news_img ); ?>" width="100%" alt="Weekly Newsletter" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px;" />
            </div>
            <?php endif; ?>
            <?php if ( $news_text ) : ?>
            <p class="info" style="font-size:12px; margin:10px 0 6px 0; color:#475569;"><?php echo esc_html( $news_text ); ?></p>
            <?php endif; ?>
            <div style="margin-bottom: 10px;">
                <a class="btn-ys block-btn btn-ys-cor1" style="background-color: #ef4444; color:#fff; display:block; text-align:center; padding:8px; border-radius:6px; font-weight:700; text-decoration:none;" href="<?php echo esc_url( $news_link ); ?>"><?php kaitori_te( 'Subscribe to Newsletter', 'メールマガジン登録' ); ?></a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<!--sidebar-e-->
