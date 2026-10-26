<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
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
                    <li class="hidden-xs"><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="link-highlight"><i class="fa fa-shopping-cart"></i> <?php kaitori_te( 'iPhone Buyback', 'iPhone買取' ); ?></a></li>
                    <li class="hidden-xs"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><i class="fa fa-id-card-o"></i> <?php kaitori_te( 'eKYC Verify', '本人確認(eKYC)' ); ?></a></li>
                    <?php if ( is_user_logged_in() ) : ?>
                        <li><a href="<?php echo esc_url( home_url( '/mypage/' ) ); ?>"><i class="fa fa-user-circle-o"></i> <span><?php kaitori_te( 'My Page', 'マイページ' ); ?></span></a></li>
                        <li class="hidden-xs"><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>"><i class="fa fa-sign-out"></i> <?php kaitori_te( 'Logout', 'ログアウト' ); ?></a></li>
                    <?php else : ?>
                        <li class="hidden-xs"><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><i class="fa fa-user-plus"></i> <?php kaitori_te( 'Register', '会員登録' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/register/?action=login' ) ); ?>"><i class="fa fa-sign-in"></i> <span><?php kaitori_te( 'Login', 'ログイン' ); ?></span></a></li>
                    <?php endif; ?>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<!-- ================= Main Site Header ================= -->
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
                <!-- Mobile Menu Hamburger Button -->
                <button type="button" class="mobile-nav-toggle visible-xs" id="mobileNavToggle" aria-label="<?php esc_attr_e( 'Menu', 'kaitoriakiba' ); ?>">
                    <i class="fa fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Navigation Off-Canvas Drawer -->
<div class="mobile-nav-drawer" id="mobileNavDrawer" aria-hidden="true">
    <div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop"></div>
    <div class="mobile-drawer-content">
        <div class="mobile-drawer-header">
            <div class="mobile-drawer-title">
                <img src="<?php echo get_template_directory_uri(); ?>/upload/logo/logo.svg" alt="<?php bloginfo( 'name' ); ?>" style="max-height: 28px; width: auto;" />
            </div>
            <button type="button" class="mobile-drawer-close" id="mobileDrawerClose" aria-label="Close menu">&times;</button>
        </div>
        <div class="mobile-drawer-body">
            <div class="mobile-drawer-cta">
                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="btn-drawer-apply">
                    <i class="fa fa-paper-plane"></i> <?php kaitori_te( 'Apply for Buyback', '買取を申し込む' ); ?>
                </a>
            </div>
            <ul class="mobile-menu-links">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fa fa-home"></i> <?php kaitori_te( 'Home', 'ホーム' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/#searchtop' ) ); ?>"><i class="fa fa-search"></i> <?php kaitori_te( 'Device Price Search', '買取価格検索' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><i class="fa fa-id-card-o"></i> <?php kaitori_te( 'Identity Verification (eKYC)', 'オンライン本人確認 (eKYC)' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>"><i class="fa fa-map-marker"></i> <?php kaitori_te( 'Shop Access & Hours', '店舗案内・アクセス' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><i class="fa fa-question-circle"></i> <?php kaitori_te( 'FAQ', 'よくあるご質問' ); ?></a></li>
                <li class="divider"></li>
                <?php if ( is_user_logged_in() ) : ?>
                    <li><a href="<?php echo esc_url( home_url( '/mypage/' ) ); ?>"><i class="fa fa-user-circle"></i> <?php kaitori_te( 'My Page', 'マイページ' ); ?></a></li>
                    <li><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>"><i class="fa fa-sign-out"></i> <?php kaitori_te( 'Logout', 'ログアウト' ); ?></a></li>
                <?php else : ?>
                    <li><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><i class="fa fa-user-plus"></i> <?php kaitori_te( 'Member Registration (Free)', '新規会員登録（無料）' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/register/?action=login' ) ); ?>"><i class="fa fa-sign-in"></i> <?php kaitori_te( 'Login', 'ログイン' ); ?></a></li>
                <?php endif; ?>
            </ul>
            <div class="mobile-drawer-footer">
                <div class="mobile-store-info">
                    <p><strong><i class="fa fa-map-pin"></i> <?php kaitori_te( 'Tokyo Akihabara Store', '東京・秋葉原店' ); ?></strong></p>
                    <p><i class="fa fa-clock-o"></i> <?php kaitori_te( 'Hours: 10:00 - 19:30', '営業時間: 10:00〜19:30' ); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation App Bar -->
<nav class="mobile-bottom-app-bar visible-xs" aria-label="Mobile Navigation">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-item <?php echo is_front_page() ? 'active' : ''; ?>">
        <i class="fa fa-home"></i>
        <span><?php kaitori_te( 'Home', 'ホーム' ); ?></span>
    </a>
    <a href="<?php echo esc_url( home_url( '/#searchtop' ) ); ?>" class="nav-item">
        <i class="fa fa-search"></i>
        <span><?php kaitori_te( 'Search', '検索' ); ?></span>
    </a>
    <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="nav-item cta-item">
        <div class="cta-circle"><i class="fa fa-paper-plane"></i></div>
        <span><?php kaitori_te( 'Apply', '買取申込' ); ?></span>
    </a>
    <a href="<?php echo esc_url( is_user_logged_in() ? home_url( '/mypage/' ) : home_url( '/register/' ) ); ?>" class="nav-item">
        <i class="fa fa-user"></i>
        <span><?php kaitori_te( 'Account', 'アカウント' ); ?></span>
    </a>
    <button type="button" class="nav-item btn-open-mobile-drawer" id="mobileBottomMenuBtn">
        <i class="fa fa-bars"></i>
        <span><?php kaitori_te( 'Menu', 'メニュー' ); ?></span>
    </button>
</nav>
