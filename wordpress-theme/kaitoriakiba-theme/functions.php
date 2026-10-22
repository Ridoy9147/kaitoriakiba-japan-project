<?php
/**
 * Kaitori Akiba Theme Functions & Definitions
 * 
 * 100% Native WordPress Theme Architecture
 * All layout and sections render directly from theme templates matching index.html.
 * Zero external page builder dependencies.
 * 
 * @package KaitoriAkiba
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'KAITORIAKIBA_VERSION', '2.1.0' );
define( 'KAITORIAKIBA_DIR', get_template_directory() );
define( 'KAITORIAKIBA_URI', get_template_directory_uri() );

/**
 * 1. Theme Setup
 */
function kaitoriakiba_theme_setup() {
    // Add theme support
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

    // Register navigation menus
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
    wp_enqueue_style( 'jquery-ui-css', $uri . '/assets/css/jquery-ui.min.css', array(), '1.11.4' );
    wp_enqueue_style( 'font-awesome', $uri . '/assets/css/font-awesome.min.css', array(), '4.7.0' );
    wp_enqueue_style( 'base-style', $uri . '/assets/css/base.css', array( 'bootstrap' ), $ver );
    wp_enqueue_style( 'main-style', $uri . '/assets/css/style.css', array( 'base-style' ), $ver );
    wp_enqueue_style( 'swiper-css', $uri . '/assets/css/swiper3.1.0.min.css', array(), '3.1.0' );
    wp_enqueue_style( 'custom-modern', $uri . '/assets/css/custom-modern.css', array( 'main-style' ), $ver );
    wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array( 'custom-modern' ), $ver );

    // Core Scripts
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-autocomplete' );
    wp_enqueue_script( 'bootstrap-js', $uri . '/assets/js/bootstrap.min.js', array( 'jquery' ), '3.3.6', true );
    wp_enqueue_script( 'swiper-js', $uri . '/assets/js/swiper3.1.0.jquery.min.js', array( 'jquery' ), '3.1.0', true );
    wp_enqueue_script( 'layer-js', $uri . '/assets/js/layer/layer-min.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'global-js', $uri . '/assets/js/global.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'common-js', $uri . '/assets/js/common.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'response-layout-js', $uri . '/assets/js/response-layout.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'scroller-js', $uri . '/assets/js/scroller.js', array( 'jquery' ), '1.0', true );

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
 * 3. Load Modules & Custom Post Types (Buyback Devices, Orders, eKYC)
 */
if ( file_exists( KAITORIAKIBA_DIR . '/inc/cpt.php' ) ) {
    require_once KAITORIAKIBA_DIR . '/inc/cpt.php';
}
if ( file_exists( KAITORIAKIBA_DIR . '/inc/meta-boxes.php' ) ) {
    require_once KAITORIAKIBA_DIR . '/inc/meta-boxes.php';
}
if ( file_exists( KAITORIAKIBA_DIR . '/inc/ajax-handlers.php' ) ) {
    require_once KAITORIAKIBA_DIR . '/inc/ajax-handlers.php';
}
if ( file_exists( KAITORIAKIBA_DIR . '/inc/demo-data.php' ) ) {
    require_once KAITORIAKIBA_DIR . '/inc/demo-data.php';
}

/**
 * 4. Safe Helper: Check if a post/page is built with Elementor
 */
function kaitoriakiba_is_elementor_page( $post_id = 0 ) {
    if ( class_exists( '\Elementor\Plugin' ) ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }
        if ( $post_id ) {
            $doc = \Elementor\Plugin::$instance->documents->get( $post_id );
            if ( $doc && method_exists( $doc, 'is_built_with_elementor' ) ) {
                return $doc->is_built_with_elementor();
            }
        }
    }
    return false;
}

