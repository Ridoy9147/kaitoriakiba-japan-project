<?php
/**
 * Kaitori Akiba Theme Functions
 * Domain: kaitoriakiba.jp
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function kaitoriakiba_theme_setup() {
    // Add theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => __( 'Primary Navigation', 'kaitoriakiba' ),
        'footer'  => __( 'Footer Navigation', 'kaitoriakiba' ),
    ) );
}
add_action( 'after_setup_theme', 'kaitoriakiba_theme_setup' );

function kaitoriakiba_enqueue_scripts() {
    $theme_uri = get_template_directory_uri();

    // Stylesheets
    wp_enqueue_style( 'bootstrap', $theme_uri . '/assets/css/bootstrap.min.css', array(), '3.3.6' );
    wp_enqueue_style( 'jquery-ui-css', $theme_uri . '/assets/css/jquery-ui.min.css', array(), '1.11.4' );
    wp_enqueue_style( 'font-awesome', $theme_uri . '/assets/css/font-awesome.min.css', array(), '4.7.0' );
    wp_enqueue_style( 'base-style', $theme_uri . '/assets/css/base.css', array('bootstrap'), '1.0' );
    wp_enqueue_style( 'main-style', $theme_uri . '/assets/css/style.css', array('base-style'), '1.0' );
    wp_enqueue_style( 'swiper-css', $theme_uri . '/assets/css/swiper3.1.0.min.css', array(), '3.1.0' );
    wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array('main-style'), '1.0.0' );
    wp_enqueue_style( 'custom-modern', $theme_uri . '/assets/css/custom-modern.css', array('theme-style'), '1.0.0' );

    // Scripts
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'bootstrap-js', $theme_uri . '/assets/js/bootstrap.min.js', array('jquery'), '3.3.6', true );
    wp_enqueue_script( 'swiper-js', $theme_uri . '/assets/js/swiper3.1.0.jquery.min.js', array('jquery'), '3.1.0', true );
    wp_enqueue_script( 'layer-js', $theme_uri . '/assets/js/layer/layer-min.js', array('jquery'), '1.0', true );
    wp_enqueue_script( 'global-js', $theme_uri . '/assets/js/global.js', array('jquery'), '1.0', true );
    wp_enqueue_script( 'common-js', $theme_uri . '/assets/js/common.js', array('jquery'), '1.0', true );
    wp_enqueue_script( 'response-layout-js', $theme_uri . '/assets/js/response-layout.js', array('jquery'), '1.0', true );
    wp_enqueue_script( 'scroller-js', $theme_uri . '/assets/js/scroller.js', array('jquery'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'kaitoriakiba_enqueue_scripts' );
