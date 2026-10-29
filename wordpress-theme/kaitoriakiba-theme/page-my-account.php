<?php
/**
 * Template Name: My Account / Member Dashboard (マイページ)
 *
 * Full member dashboard for customers: Buyback order history, eKYC status, and favorites.
 * Supports standard WordPress / WooCommerce "/my-account/" as well as Japanese "/mypage/".
 *
 * @package KaitoriAkiba
 */

if ( file_exists( get_template_directory() . '/page-mypage.php' ) ) {
    require get_template_directory() . '/page-mypage.php';
} else {
    get_header();
    ?>
    <div class="container" style="margin-top:40px; margin-bottom:60px;">
        <h2><?php kaitori_te( 'My Account', 'マイページ' ); ?></h2>
        <p><?php kaitori_te( 'Welcome to your account dashboard.', 'マイページへようこそ。' ); ?></p>
    </div>
    <?php
    get_footer();
}
