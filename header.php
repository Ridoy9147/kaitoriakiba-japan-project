<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, email=no">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<!-- ================= Top Utility Bar ================= -->
<div class="top-utility-bar">
    <div class="container">
        <div class="top-bar-inner">
            <div class="top-bar-notice">
                <span class="badge-official"><?php kaitori_te( 'Official Buyback Portal', '公式買取ポータル' ); ?></span>
                <span class="notice-text hidden-xs"><?php kaitori_te( 'iPhone, Smartphone & Mobile Phone Specialist [Tokyo Akihabara]', 'iPhone・スマホ・携帯電話 買取専門店【東京・秋葉原】' ); ?></span>
            </div>
            <ul class="top-nav-links">
                <?php if ( function_exists( 'kaitoriakiba_lang_switcher' ) ) { kaitoriakiba_lang_switcher(); } ?>
                <?php
                if ( has_nav_menu( 'top_utility' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'top_utility',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'fallback_cb'    => false,
                    ) );
                } else {
                    ?>
                    <li><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="link-highlight"><i class="fa fa-shopping-cart"></i> <?php kaitori_te( 'iPhone Buyback', 'iPhone買取' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><i class="fa fa-id-card-o"></i> <?php kaitori_te( 'eKYC Verify', '本人確認(eKYC)' ); ?></a></li>
                    <li><a href="<?php echo esc_url( kaitoriakiba_get_account_url() ); ?>"><i class="fa fa-user-circle-o"></i> <?php kaitori_te( 'My Account', 'マイページ' ); ?></a></li>
                    <?php if ( is_user_logged_in() ) : ?>
                        <li><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>"><i class="fa fa-sign-out"></i> <?php kaitori_te( 'Logout', 'ログアウト' ); ?></a></li>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><i class="fa fa-user-plus"></i> <?php kaitori_te( 'Register', '会員登録' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/register/?action=login' ) ); ?>"><i class="fa fa-sign-in"></i> <?php kaitori_te( 'Login', 'ログイン' ); ?></a></li>
                    <?php endif; ?>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<!-- ================= Main Site Header (Cart Badge Removed) ================= -->
<header class="site-header-modern">
    <div class="container">
        <div class="header-main-row">
            <!-- Brand Logo -->
            <div class="header-brand">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-link">
                        <img src="<?php echo get_template_directory_uri(); ?>/upload/logo/logo.svg" title="<?php bloginfo( 'name' ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="brand-logo-img" />
                    </a>
                    <?php
                }
                ?>
            </div>

            <!-- Header Quick Actions & Contact -->
            <div class="header-actions">
                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="header-cta-btn">
                    <i class="fa fa-paper-plane"></i>
                    <span><?php kaitori_te( 'Apply for Buyback', '買取を申し込む' ); ?></span>
                </a>
                <div class="header-contact hidden-xs">
                    <a href="<?php echo esc_url( home_url( '/access/' ) ); ?>" class="shop-info-link">
                        <i class="fa fa-map-marker"></i>
                        <div class="shop-info-text">
                            <span class="shop-title"><?php kaitori_te( 'Akihabara Store', '秋葉原店' ); ?></span>
                            <span class="shop-sub"><?php kaitori_te( 'Open 10:00 - 19:30', '営業 10:00〜19:30' ); ?></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>