<?php
/**
 * Kaitori Akiba Theme Functions & Definitions
 * 
 * Full Elementor Page Builder Support & CPT Integration
 * 
 * @package KaitoriAkiba
 * @version 2.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KAITORIAKIBA_VERSION', '2.0.1' );
define( 'KAITORIAKIBA_DIR', get_template_directory() );
define( 'KAITORIAKIBA_URI', get_template_directory_uri() );

/**
 * 0. Emergency Diagnostics / Error Protection
 * Catches fatal errors and displays a clear diagnostic box instead of the blank critical error screen.
 */
register_shutdown_function( function() {
    $error = error_get_last();
    if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR ), true ) ) {
        if ( ob_get_level() ) {
            ob_clean();
        }
        echo '<div style="background:#fff1f2; border:2px solid #e11d48; color:#881337; padding:24px; font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif; margin:40px auto; max-width:850px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.08);">';
        echo '<h3 style="color:#be123c; margin-top:0; font-size:18px;">⚠️ Kaitori Akiba Theme Diagnostic</h3>';
        echo '<p style="margin:8px 0; font-size:14px;"><strong>Fatal Error:</strong> ' . htmlspecialchars( $error['message'] ) . '</p>';
        echo '<p style="margin:4px 0; font-size:13px; color:#4c0519;"><strong>File:</strong> <code>' . htmlspecialchars( $error['file'] ) . '</code></p>';
        echo '<p style="margin:4px 0; font-size:13px; color:#4c0519;"><strong>Line:</strong> <code>' . htmlspecialchars( $error['line'] ) . '</code></p>';
        echo '</div>';
    }
} );

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
require_once KAITORIAKIBA_DIR . '/inc/tgmpa/tgmpa-config.php';

/**
 * 4. Safe Helper: Check if a post/page is built with Elementor
 */
function kaitoriakiba_is_elementor_page( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_queried_object_id();
    }
    if ( ! $post_id ) {
        global $post;
        $post_id = isset( $post->ID ) ? $post->ID : 0;
    }
    if ( ! $post_id || $post_id <= 0 ) {
        return false;
    }

    // 1. Fastest & 100% safe check: Elementor stores '_elementor_edit_mode' meta on edited posts
    $edit_mode = get_post_meta( $post_id, '_elementor_edit_mode', true );
    if ( 'builder' === $edit_mode ) {
        return true;
    }

    // 2. Safe check with Elementor plugin instance
    if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
        try {
            if ( method_exists( '\Elementor\Plugin', 'instance' ) ) {
                $plugin = \Elementor\Plugin::instance();
                if ( $plugin && isset( $plugin->db ) && method_exists( $plugin->db, 'is_built_with_elementor' ) ) {
                    return (bool) $plugin->db->is_built_with_elementor( $post_id );
                }
            }
        } catch ( \Throwable $e ) {
            return false;
        }
    }

    return false;
}

/**
 * 5. Load Elementor Custom Widgets & Categories
 */
function kaitoriakiba_init_elementor() {
    static $elementor_inited = false;
    if ( $elementor_inited ) {
        return;
    }
    if ( did_action( 'elementor/loaded' ) ) {
        $elementor_inited = true;
        require_once KAITORIAKIBA_DIR . '/inc/elementor/elementor-init.php';
    }
}
add_action( 'elementor/init', 'kaitoriakiba_init_elementor' );
add_action( 'init', 'kaitoriakiba_init_elementor', 1 );

/**
 * 5. Register Elementor Page Templates
 */
function kaitoriakiba_add_page_templates( $templates ) {
    $templates['template-elementor-fullwidth.php'] = __( 'Elementor Full Width (Kaitori Akiba)', 'kaitoriakiba' );
    $templates['template-elementor-canvas.php']    = __( 'Elementor Canvas (No Header/Footer)', 'kaitoriakiba' );
    $templates['page-sell-apply.php']              = __( 'Buyback Application Form (買取申込)', 'kaitoriakiba' );
    $templates['page-kyc-verify.php']              = __( 'eKYC Verification (本人確認)', 'kaitoriakiba' );
    $templates['page-register.php']                = __( 'Register & Login (会員登録)', 'kaitoriakiba' );
    $templates['page-phone-single.php']            = __( 'Phone Single Showcase (端末詳細・買取価格)', 'kaitoriakiba' );
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
