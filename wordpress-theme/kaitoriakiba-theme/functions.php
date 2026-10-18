<?php
/**
 * Kaitori Akiba Theme Functions & Definitions
 * 
 * Full Elementor Page Builder Support & CPT Integration
 * 
 * @package KaitoriAkiba
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KAITORIAKIBA_VERSION', '2.0.0' );
define( 'KAITORIAKIBA_DIR', get_template_directory() );
define( 'KAITORIAKIBA_URI', get_template_directory_uri() );

/**
 * 1. Theme Setup
 */
function kaitoriakiba_theme_setup() {
    // Standard WordPress Features
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 260,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );

    // Elementor Support
    add_theme_support( 'elementor' );

    // Navigation Menus
    register_nav_menus( array(
        'primary'     => __( 'Main Header Navigation', 'kaitoriakiba' ),
        'top_utility' => __( 'Top Utility Bar Links', 'kaitoriakiba' ),
        'footer_1'    => __( 'Footer Services Menu', 'kaitoriakiba' ),
        'footer_2'    => __( 'Footer Guides Menu', 'kaitoriakiba' ),
    ) );
}
add_action( 'after_setup_theme', 'kaitoriakiba_theme_setup' );

/**
 * 2. Enqueue Styles & Scripts
 */
function kaitoriakiba_enqueue_scripts() {
    $uri = KAITORIAKIBA_URI;
    $ver = KAITORIAKIBA_VERSION;

    // Core Stylesheets
    wp_enqueue_style( 'bootstrap', $uri . '/assets/css/bootstrap.min.css', array(), '3.3.6' );
    wp_enqueue_style( 'font-awesome', $uri . '/assets/css/font-awesome.min.css', array(), '4.7.0' );
    wp_enqueue_style( 'base-style', $uri . '/assets/css/base.css', array( 'bootstrap' ), $ver );
    wp_enqueue_style( 'main-style', $uri . '/assets/css/style.css', array( 'base-style' ), $ver );
    wp_enqueue_style( 'swiper-css', $uri . '/assets/css/swiper3.1.0.min.css', array(), '3.1.0' );
    wp_enqueue_style( 'custom-modern', $uri . '/assets/css/custom-modern.css', array( 'main-style' ), $ver );
    wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array( 'custom-modern' ), $ver );

    // Scripts
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-autocomplete' );
    wp_enqueue_script( 'bootstrap-js', $uri . '/assets/js/bootstrap.min.js', array( 'jquery' ), '3.3.6', true );
    wp_enqueue_script( 'swiper-js', $uri . '/assets/js/swiper3.1.0.jquery.min.js', array( 'jquery' ), '3.1.0', true );

    // Theme JS with AJAX Localization
    wp_enqueue_script( 'kaitori-theme-app', $uri . '/assets/js/theme-app.js', array( 'jquery' ), $ver, true );
    wp_localize_script( 'kaitori-theme-app', 'kaitori_ajax', array(
        'ajax_url'   => admin_url( 'admin-ajax.php' ),
        'security'   => wp_create_nonce( 'kaitori_nonce' ),
        'home_url'   => home_url( '/' ),
        'theme_url'  => $uri,
        'i18n_error' => __( '通信エラーが発生しました。しばらくしてからもう一度お試しください。', 'kaitoriakiba' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'kaitoriakiba_enqueue_scripts' );

/**
 * 3. Load Modules & Custom Post Types
 */
require_once KAITORIAKIBA_DIR . '/inc/cpt.php';
require_once KAITORIAKIBA_DIR . '/inc/meta-boxes.php';
require_once KAITORIAKIBA_DIR . '/inc/ajax-handlers.php';
require_once KAITORIAKIBA_DIR . '/inc/demo-data.php';

/**
 * 4. Load Elementor Custom Widgets when Elementor is active
 */
function kaitoriakiba_load_elementor() {
    if ( did_action( 'elementor/loaded' ) ) {
        require_once KAITORIAKIBA_DIR . '/inc/elementor/elementor-init.php';
    }
}
add_action( 'init', 'kaitoriakiba_load_elementor', 20 );

/**
 * 5. Register Elementor Page Templates
 */
function kaitoriakiba_add_page_templates( $templates ) {
    $templates['template-elementor-fullwidth.php'] = __( 'Elementor Full Width (Kaitori Akiba)', 'kaitoriakiba' );
    $templates['template-elementor-canvas.php']    = __( 'Elementor Canvas (No Header/Footer)', 'kaitoriakiba' );
    $templates['page-sell-apply.php']              = __( 'Buyback Application Form (買取申込)', 'kaitoriakiba' );
    $templates['page-kyc-verify.php']              = __( 'eKYC Verification (本人確認)', 'kaitoriakiba' );
    $templates['page-register.php']                = __( 'Register & Login (会員登録)', 'kaitoriakiba' );
    return $templates;
}
add_filter( 'theme_page_templates', 'kaitoriakiba_add_page_templates' );

/**
 * 6. Helper: Get Device Formatted Price
 */
function kaitoriakiba_get_price( $post_id = null ) {
    $id = $post_id ?: get_the_ID();
    $price = get_post_meta( $id, '_max_buyback_price', true );
    return $price ? '¥' . number_format( (int)$price ) : '¥0';
}
