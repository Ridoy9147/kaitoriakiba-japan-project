<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no, email=no">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ================= Top Utility Bar ================= -->
<div class="top-utility-bar">
    <div class="container">
        <div class="top-bar-inner">
            <div class="top-bar-notice">
                <span class="badge-official"><?php _e( 'Official Buyback Portal', 'kaitoriakiba' ); ?></span>
                <span class="notice-text hidden-xs"><?php _e( 'iPhone, Smartphone & Mobile Phone Specialist [Tokyo Akihabara]', 'kaitoriakiba' ); ?></span>
            </div>
            <ul class="top-nav-links">
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
                    <li><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="link-highlight"><i class="fa fa-shopping-cart"></i> <?php _e( 'iPhone Buyback', 'kaitoriakiba' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><i class="fa fa-id-card-o"></i> <?php _e( 'eKYC Verify', 'kaitoriakiba' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><i class="fa fa-user-plus"></i> <?php _e( 'Register', 'kaitoriakiba' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><i class="fa fa-sign-in"></i> <?php _e( 'Login', 'kaitoriakiba' ); ?></a></li>
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
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-link">
                    <?php
                    if ( has_custom_logo() ) {
                        the_custom_logo();
                    } else {
                        ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/upload/logo/logo.svg" title="<?php bloginfo( 'name' ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="brand-logo-img" />
                        <?php
                    }
                    ?>
                </a>
            </div>

            <!-- Header Quick Actions & Contact -->
            <div class="header-actions">
                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>" class="header-cta-btn">
                    <i class="fa fa-paper-plane"></i>
                    <span><?php _e( 'Apply for Buyback', 'kaitoriakiba' ); ?></span>
                </a>
                <div class="header-contact hidden-xs">
                    <a href="<?php echo esc_url( home_url( '/access/' ) ); ?>" class="shop-info-link">
                        <i class="fa fa-map-marker"></i>
                        <div class="shop-info-text">
                            <span class="shop-title"><?php _e( 'Akihabara Store', 'kaitoriakiba' ); ?></span>
                            <span class="shop-sub"><?php _e( 'Open 10:00 - 19:30', 'kaitoriakiba' ); ?></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Navigation Drawer Header -->
<div class="navbar-header visible-xs">
    <div class="header-menu">
        <ul>
            <li class="btn-dt"><a href="#index-search" data-toggle="collapse"><?php _e( 'Search', 'kaitoriakiba' ); ?></a></li>
            <li class="btn-yj"><a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php _e( 'Menu', 'kaitoriakiba' ); ?></a></li>
            <li class="btn-fr"><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php _e( 'Account', 'kaitoriakiba' ); ?></a></li>
        </ul>
    </div>
</div>