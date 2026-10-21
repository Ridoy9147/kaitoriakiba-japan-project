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
add_action( 'elementor/editor/after_enqueue_styles', 'kaitoriakiba_enqueue_scripts' );
add_action( 'elementor/preview/enqueue_styles', 'kaitoriakiba_enqueue_scripts' );

/**
 * 3. Load Modules & Custom Post Types
 */
require_once KAITORIAKIBA_DIR . '/inc/cpt.php';
require_once KAITORIAKIBA_DIR . '/inc/meta-boxes.php';
require_once KAITORIAKIBA_DIR . '/inc/ajax-handlers.php';
require_once KAITORIAKIBA_DIR . '/inc/demo-data.php';
require_once KAITORIAKIBA_DIR . '/inc/tgmpa/tgmpa-config.php';

/**
 * 4. Safe Helper: Check if Elementor Editor or Preview is currently active
 */
function kaitoriakiba_is_elementor_active() {
    // Check GET query parameters commonly used by Elementor Editor & Preview iframe
    if ( isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) ) {
        return true;
    }

    if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
        try {
            $plugin = \Elementor\Plugin::$instance;
            if ( $plugin ) {
                if ( isset( $plugin->preview ) && method_exists( $plugin->preview, 'is_preview_mode' ) && $plugin->preview->is_preview_mode() ) {
                    return true;
                }
                if ( isset( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() ) {
                    return true;
                }
            }
        } catch ( \Throwable $e ) {
            // Ignore
        }
    }

    return false;
}

/**
 * Check if a post/page is built with Elementor
 */
function kaitoriakiba_is_elementor_page( $post_id = 0 ) {
    // If currently editing or previewing in Elementor, always return true
    if ( kaitoriakiba_is_elementor_active() ) {
        return true;
    }

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
        $data = get_post_meta( $post_id, '_elementor_data', true );
        if ( ! empty( $data ) && $data !== '[]' ) {
            return true;
        }
    }

    // 2. Safe check with Elementor plugin instance
    if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
        try {
            $plugin = \Elementor\Plugin::$instance;
            if ( $plugin && isset( $plugin->db ) && method_exists( $plugin->db, 'is_built_with_elementor' ) ) {
                if ( $plugin->db->is_built_with_elementor( $post_id ) ) {
                    $data = get_post_meta( $post_id, '_elementor_data', true );
                    if ( ! empty( $data ) && $data !== '[]' ) {
                        return true;
                    }
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

/**
 * 7. Generate Full Complete Elementor Schema for Homepage
 */
function kaitoriakiba_get_full_elementor_homepage_schema() {
    $theme_uri = get_template_directory_uri();
    return array(
        // Section 1: Process Flow 3-Big Buttons (guid - matching index.html top order)
        array(
            'id'       => 'sec_flow_buttons_' . wp_generate_password( 6, false, false ),
            'elType'   => 'section',
            'settings' => array(),
            'elements' => array(
                array(
                    'id'       => 'col_flow_buttons_' . wp_generate_password( 6, false, false ),
                    'elType'   => 'column',
                    'settings' => array( '_column_size' => 100 ),
                    'elements' => array(
                        array(
                            'id'         => 'wid_flow_buttons_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_process_flow',
                            'settings'   => array(),
                        ),
                    ),
                ),
            ),
        ),
        // Section 2: Urgent Announcements & Quick Guides (home_liuxiang + qingbao)
        array(
            'id'       => 'sec_announcement_' . wp_generate_password( 6, false, false ),
            'elType'   => 'section',
            'settings' => array(),
            'elements' => array(
                array(
                    'id'       => 'col_announcement_' . wp_generate_password( 6, false, false ),
                    'elType'   => 'column',
                    'settings' => array( '_column_size' => 100 ),
                    'elements' => array(
                        array(
                            'id'         => 'wid_announcement_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_announcement_banner',
                            'settings'   => array(),
                        ),
                    ),
                ),
            ),
        ),
        // Section 3: Dual Search Box (Keyword & Step-by-Step Category search)
        array(
            'id'       => 'sec_search_' . wp_generate_password( 6, false, false ),
            'elType'   => 'section',
            'settings' => array(),
            'elements' => array(
                array(
                    'id'       => 'col_search_' . wp_generate_password( 6, false, false ),
                    'elType'   => 'column',
                    'settings' => array( '_column_size' => 100 ),
                    'elements' => array(
                        array(
                            'id'         => 'wid_search_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_search_box',
                            'settings'   => array(),
                        ),
                    ),
                ),
            ),
        ),
        // Section 4: Hero Navigation Banners, Posters & Swiper Slider
        array(
            'id'       => 'sec_hero_slider_' . wp_generate_password( 6, false, false ),
            'elType'   => 'section',
            'settings' => array(),
            'elements' => array(
                array(
                    'id'       => 'col_hero_slider_' . wp_generate_password( 6, false, false ),
                    'elType'   => 'column',
                    'settings' => array( '_column_size' => 100 ),
                    'elements' => array(
                        array(
                            'id'         => 'wid_hero_slider_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_hero_banner',
                            'settings'   => array(),
                        ),
                    ),
                ),
            ),
        ),
        // Section 5: Main 2-Column Section (Sidebar 25% + Main Body 75%)
        array(
            'id'       => 'sec_main_two_columns_' . wp_generate_password( 6, false, false ),
            'elType'   => 'section',
            'settings' => array(),
            'elements' => array(
                // Left Column: Sidebar (Top Ads, Carriers, Brands, Series, Newsletter)
                array(
                    'id'       => 'col_sidebar_' . wp_generate_password( 6, false, false ),
                    'elType'   => 'column',
                    'settings' => array( '_column_size' => 25 ),
                    'elements' => array(
                        array(
                            'id'         => 'wid_side_ad_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_sidebar_ad',
                            'settings'   => array(),
                        ),
                        array(
                            'id'         => 'wid_side_carrier_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_taxonomy_browse',
                            'settings'   => array(
                                'data_mode'    => 'custom',
                                'display_mode' => 'carrier_full',
                                'box_title'    => 'キャリアから探す',
                                'box_link'     => array( 'url' => '/career/' ),
                                'custom_items_list' => array(
                                    array( 'item_title' => 'docomo', 'item_image' => array( 'url' => $theme_uri . '/upload/career/docomo.jpg' ), 'item_url' => array( 'url' => '/career/docomo/' ) ),
                                    array( 'item_title' => 'au', 'item_image' => array( 'url' => $theme_uri . '/upload/career/au.jpg' ), 'item_url' => array( 'url' => '/career/au/' ) ),
                                    array( 'item_title' => 'SoftBank', 'item_image' => array( 'url' => $theme_uri . '/upload/career/softbank.jpg' ), 'item_url' => array( 'url' => '/career/softbank/' ) ),
                                    array( 'item_title' => 'Y!mobile', 'item_image' => array( 'url' => $theme_uri . '/upload/career/Y!mobile.jpg' ), 'item_url' => array( 'url' => '/career/Y!mobile/' ) ),
                                    array( 'item_title' => 'UQ mobile', 'item_image' => array( 'url' => $theme_uri . '/upload/career/uqmobile.jpg' ), 'item_url' => array( 'url' => '/career/uqmobile/' ) ),
                                    array( 'item_title' => 'SIM FREE', 'item_image' => array( 'url' => $theme_uri . '/upload/career/simfree.jpg' ), 'item_url' => array( 'url' => '/career/simfree/' ) ),
                                    array( 'item_title' => 'RakutenMobile', 'item_image' => array( 'url' => $theme_uri . '/upload/career/20220526/楽天.jpg' ), 'item_url' => array( 'url' => '/career/RakutenMobile/' ) ),
                                ),
                            ),
                        ),
                        array(
                            'id'         => 'wid_side_brand_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_taxonomy_browse',
                            'settings'   => array(
                                'data_mode'        => 'custom',
                                'display_mode'     => 'brand_grid',
                                'box_title'        => 'ブランドから探す',
                                'box_link'         => array( 'url' => '/brand/' ),
                                'show_more_button' => 'yes',
                                'custom_items_list' => array(
                                    array( 'item_title' => 'Apple', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/apple.jpg' ), 'item_url' => array( 'url' => '/brand/apple/' ) ),
                                    array( 'item_title' => 'Xiaomi', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/20200603/3.png' ), 'item_url' => array( 'url' => '/brand/Xiaomi/' ) ),
                                    array( 'item_title' => 'OPPO', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/OPPO.jpg' ), 'item_url' => array( 'url' => '/brand/OPPO/' ) ),
                                    array( 'item_title' => 'ASUS', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/asus.jpg' ), 'item_url' => array( 'url' => '/brand/asus/' ) ),
                                    array( 'item_title' => 'FUJITSU', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/fujitsu.jpg' ), 'item_url' => array( 'url' => '/brand/fujitsu/' ) ),
                                    array( 'item_title' => 'SAMSUNG', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/samsung.jpg' ), 'item_url' => array( 'url' => '/brand/samsung/' ) ),
                                    array( 'item_title' => 'Google', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/google.jpg' ), 'item_url' => array( 'url' => '/brand/google/' ) ),
                                    array( 'item_title' => 'HUAWEI', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/huawei.jpg' ), 'item_url' => array( 'url' => '/brand/huawei/' ) ),
                                    array( 'item_title' => 'KYOCERA', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/kyocera.jpg' ), 'item_url' => array( 'url' => '/brand/kyocera/' ) ),
                                    array( 'item_title' => 'MOTOROLA', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/moto.jpg' ), 'item_url' => array( 'url' => '/brand/motorola/' ) ),
                                    array( 'item_title' => 'SHARP', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/sharp.jpg' ), 'item_url' => array( 'url' => '/brand/sharp/' ) ),
                                    array( 'item_title' => 'Sony', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/sony.jpg' ), 'item_url' => array( 'url' => '/brand/sony/' ) ),
                                ),
                            ),
                        ),
                        array(
                            'id'         => 'wid_side_series_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_taxonomy_browse',
                            'settings'   => array(
                                'data_mode'        => 'custom',
                                'display_mode'     => 'brand_grid',
                                'box_title'        => 'シリーズから探す',
                                'box_link'         => array( 'url' => '/series/' ),
                                'show_more_button' => 'yes',
                                'custom_items_list' => array(
                                    array( 'item_title' => 'iPhone', 'item_image' => array( 'url' => $theme_uri . '/upload/series/iphone.jpg' ), 'item_url' => array( 'url' => '/series/iphone/' ) ),
                                    array( 'item_title' => 'Xperia', 'item_image' => array( 'url' => $theme_uri . '/upload/series/xperia.jpg' ), 'item_url' => array( 'url' => '/series/xperia/' ) ),
                                    array( 'item_title' => 'Galaxy', 'item_image' => array( 'url' => $theme_uri . '/upload/series/galaxy.jpg' ), 'item_url' => array( 'url' => '/series/galaxy/' ) ),
                                    array( 'item_title' => 'arrows', 'item_image' => array( 'url' => $theme_uri . '/upload/series/arrows.jpg' ), 'item_url' => array( 'url' => '/series/arrows/' ) ),
                                    array( 'item_title' => 'Sweety', 'item_image' => array( 'url' => $theme_uri . '/upload/series/sweety.jpg' ), 'item_url' => array( 'url' => '/series/sweety/' ) ),
                                    array( 'item_title' => 'STREAM', 'item_image' => array( 'url' => $theme_uri . '/upload/series/stream.jpg' ), 'item_url' => array( 'url' => '/series/stream/' ) ),
                                    array( 'item_title' => 'ZenFone', 'item_image' => array( 'url' => $theme_uri . '/upload/series/zenfone.jpg' ), 'item_url' => array( 'url' => '/series/zenfone/' ) ),
                                    array( 'item_title' => 'Simple Smartphone', 'item_image' => array( 'url' => $theme_uri . '/upload/series/simple-smartphone.jpg' ), 'item_url' => array( 'url' => '/series/simple-smartphone/' ) ),
                                    array( 'item_title' => 'AQUOS', 'item_image' => array( 'url' => $theme_uri . '/upload/series/aquos.jpg' ), 'item_url' => array( 'url' => '/series/aquos/' ) ),
                                    array( 'item_title' => 'RAIJIN', 'item_image' => array( 'url' => $theme_uri . '/upload/series/raijin.jpg' ), 'item_url' => array( 'url' => '/series/raijin/' ) ),
                                    array( 'item_title' => 'Boss Den', 'item_image' => array( 'url' => $theme_uri . '/upload/series/bossden.jpg' ), 'item_url' => array( 'url' => '/series/bossden/' ) ),
                                    array( 'item_title' => 'SIRIUS', 'item_image' => array( 'url' => $theme_uri . '/upload/series/sirius.jpg' ), 'item_url' => array( 'url' => '/series/sirius/' ) ),
                                    array( 'item_title' => 'Nexus', 'item_image' => array( 'url' => $theme_uri . '/upload/series/nexus.jpg' ), 'item_url' => array( 'url' => '/series/nexus/' ) ),
                                ),
                            ),
                        ),
                        array(
                            'id'         => 'wid_side_newsletter_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_sidebar_newsletter',
                            'settings'   => array(),
                        ),
                    ),
                ),
                // Right Column: Main Body (News, Device Grid, Checkpoints, FAQ)
                array(
                    'id'       => 'col_main_body_' . wp_generate_password( 6, false, false ),
                    'elType'   => 'column',
                    'settings' => array( '_column_size' => 75 ),
                    'elements' => array(
                        array(
                            'id'         => 'wid_news_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_news_box',
                            'settings'   => array(),
                        ),
                        array(
                            'id'         => 'wid_device_grid_' . wp_generate_password( 6, false, false ),
                            'elType'     => 'widget',
                            'widgetType' => 'kaitori_device_grid',
                            'settings'   => array(
                                'posts_per_page' => 12,
                                'button_text'    => '買取申込',
                            ),
                        ),
                        array(
                            'id'         => 'wid_checkpoints_' . wp_generate_password( 6, false, false ),
                            'widgetType' => 'kaitori_checkpoints_box',
                            'elType'     => 'widget',
                            'settings'   => array(),
                        ),
                        array(
                            'id'         => 'wid_faq_' . wp_generate_password( 6, false, false ),
                            'widgetType' => 'kaitori_faq',
                            'elType'     => 'widget',
                            'settings'   => array(),
                        ),
                    ),
                ),
            ),
        ),
    );
}

/**
 * Automatically ensure Elementor homepage has all authentic sections populated
 * when opening Elementor editor or preview on Front Page / Home Page.
 */
function kaitoriakiba_auto_populate_elementor_homepage() {
    $post_id = 0;
    if ( isset( $_GET['post'] ) ) {
        $post_id = intval( $_GET['post'] );
    } elseif ( isset( $_GET['elementor-preview'] ) ) {
        $post_id = intval( $_GET['elementor-preview'] );
    } elseif ( is_front_page() || is_home() ) {
        $post_id = (int) get_option( 'page_on_front' );
    }

    if ( ! $post_id ) {
        $home_page = get_page_by_path( 'home' );
        if ( $home_page ) {
            $post_id = $home_page->ID;
        }
    }

    if ( $post_id > 0 ) {
        $front_id = (int) get_option( 'page_on_front' );
        $post = get_post( $post_id );
        $is_home = ( $post_id === $front_id ) || ( $post && 'home' === $post->post_name );

        if ( $is_home ) {
            $data = get_post_meta( $post_id, '_elementor_data', true );
            // Auto-populate if missing or incomplete, or explicitly requested via ?reload_elementor_schema=1
            if ( isset( $_GET['reload_elementor_schema'] ) || empty( $data ) || $data === '[]' || false === strpos( $data, 'kaitori_checkpoints_box' ) || false === strpos( $data, 'kaitori_news_box' ) || false === strpos( $data, 'kaitori_sidebar_ad' ) || false === strpos( $data, 'kaitori_sidebar_newsletter' ) ) {
                $schema = kaitoriakiba_get_full_elementor_homepage_schema();
                update_post_meta( $post_id, '_wp_page_template', 'template-elementor-fullwidth.php' );
                update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
                update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
                update_post_meta( $post_id, '_elementor_version', '3.20.0' );
                update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $schema ) ) );
            }
        }
    }
}
add_action( 'template_redirect', 'kaitoriakiba_auto_populate_elementor_homepage', 1 );
add_action( 'admin_init', 'kaitoriakiba_auto_populate_elementor_homepage' );

/**
 * 8. One-Click Admin Handler & Notice to Populate All Sections into Elementor Homepage
 */
function kaitoriakiba_elementor_homepage_admin_handler() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    if ( isset( $_GET['kaitori_action'] ) && 'load_elementor_home' === $_GET['kaitori_action'] && check_admin_referer( 'kaitori_load_elementor_home' ) ) {
        $home_page = get_page_by_path( 'home' );
        if ( ! $home_page ) {
            $front_id = get_option( 'page_on_front' );
            if ( $front_id ) {
                $home_page = get_post( $front_id );
            }
        }
        if ( ! $home_page ) {
            $home_id = wp_insert_post( array(
                'post_title'     => 'ホーム',
                'post_name'      => 'home',
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
            ) );
            $home_page = get_post( $home_id );
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $home_id );
        }

        if ( $home_page ) {
            $el_schema = kaitoriakiba_get_full_elementor_homepage_schema();
            update_post_meta( $home_page->ID, '_wp_page_template', 'template-elementor-fullwidth.php' );
            update_post_meta( $home_page->ID, '_elementor_edit_mode', 'builder' );
            update_post_meta( $home_page->ID, '_elementor_template_type', 'wp-page' );
            update_post_meta( $home_page->ID, '_elementor_version', '3.20.0' );
            update_post_meta( $home_page->ID, '_elementor_data', wp_slash( wp_json_encode( $el_schema ) ) );
            
            // Redirect with success message
            wp_safe_redirect( admin_url( 'post.php?post=' . $home_page->ID . '&action=elementor' ) );
            exit;
        }
    }
}
add_action( 'admin_init', 'kaitoriakiba_elementor_homepage_admin_handler' );

function kaitoriakiba_admin_elementor_notice() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $setup_url = wp_nonce_url( admin_url( 'index.php?kaitori_action=load_elementor_home' ), 'kaitori_load_elementor_home' );
    ?>
    <div class="notice notice-info" style="border-left: 4px solid #ef4444; background: #fff; padding: 14px 18px; margin: 15px 0; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <p style="font-size: 14.5px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">
            🎨 <strong>Kaitori Akiba: Elementor Full Page Editor Setup (買取アキバ エレメンター一括読み込み)</strong>
        </p>
        <p style="font-size: 13px; color: #475569; margin: 0 0 12px 0;">
            <?php _e( 'Click the button below to load all sections (Sidebar Brands, Key Selling Points, Pre-assessment Checkpoints, Device Grid, News Box, Process Flow) into Elementor. You can edit every title, image, and link directly with Elementor!', 'kaitoriakiba' ); ?>
        </p>
        <p style="margin: 0;">
            <a href="<?php echo esc_url( $setup_url ); ?>" class="button button-primary" style="background:#ef4444; border-color:#dc2626; font-weight:700; padding:4px 16px; height:auto;">
                <i class="dashicons dashicons-edit" style="vertical-align:middle; margin-right:4px;"></i> <?php _e( 'Load All Sections & Open in Elementor (全セクションを読み込んでElementorで編集)', 'kaitoriakiba' ); ?>
            </a>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'kaitoriakiba_admin_elementor_notice' );
