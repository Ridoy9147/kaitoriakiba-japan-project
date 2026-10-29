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

define( 'KAITORIAKIBA_VERSION', '2.3.0' );
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
    // Load translations (previously missing — strings never loaded despite __()/_e() usage).
    load_theme_textdomain( 'kaitoriakiba', get_template_directory() . '/languages' );

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
        'logged_in'  => is_user_logged_in() ? 1 : 0,
        'favorites'  => function_exists( 'kaitoriakiba_get_user_favorites' ) ? kaitoriakiba_get_user_favorites() : array(),
        'i18n_error' => kaitori_t( 'A communication error occurred. Please try again shortly.', '通信エラーが発生しました。しばらくしてからもう一度お試しください。' ),
        'i18n'       => array(
            'imei_error'    => kaitori_t( 'Please enter a valid 15-digit IMEI number.', 'IMEI番号は15桁の数字で入力してください。' ),
            'sending'       => kaitori_t( 'Sending…', '送信中...' ),
            'uploading'     => kaitori_t( 'Uploading documents…', '書類アップロード中...' ),
            'registering'   => kaitori_t( 'Registering…', '登録中...' ),
            'logging_in'    => kaitori_t( 'Logging in…', 'ログイン中...' ),
            'loading'       => kaitori_t( 'Loading…', '読み込み中...' ),
            'app_success'   => kaitori_t( 'Your buyback application is complete!', '買取申込が完了いたしました！' ),
            'kyc_success'   => kaitori_t( 'Your identity documents have been submitted.', '本人確認書類の提出が完了いたしました。' ),
            'reg_success'   => kaitori_t( 'Registration complete!', '会員登録が完了いたしました！' ),
            'login_success' => kaitori_t( 'Logged in.', 'ログインしました。' ),
        ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'kaitoriakiba_enqueue_scripts' );

/**
 * 3. Load Modules & Custom Post Types
 */
require_once KAITORIAKIBA_DIR . '/inc/i18n-switcher.php';
require_once KAITORIAKIBA_DIR . '/inc/form-security.php';
require_once KAITORIAKIBA_DIR . '/inc/seo.php';
require_once KAITORIAKIBA_DIR . '/inc/crypto.php';
require_once KAITORIAKIBA_DIR . '/inc/pricing.php';
require_once KAITORIAKIBA_DIR . '/inc/kyc-security.php';
require_once KAITORIAKIBA_DIR . '/inc/cpt.php';
require_once KAITORIAKIBA_DIR . '/inc/meta-boxes.php';
require_once KAITORIAKIBA_DIR . '/inc/admin-customers.php';
require_once KAITORIAKIBA_DIR . '/inc/invoice.php';
require_once KAITORIAKIBA_DIR . '/inc/admin-order-create.php';
require_once KAITORIAKIBA_DIR . '/inc/sell-gate.php';
require_once KAITORIAKIBA_DIR . '/inc/emails.php';
require_once KAITORIAKIBA_DIR . '/inc/ajax-handlers.php';
require_once KAITORIAKIBA_DIR . '/inc/demo-data.php';
require_once KAITORIAKIBA_DIR . '/inc/demo-import.php';
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
    $templates['page-mypage.php']                  = __( 'Member Dashboard (マイページ)', 'kaitoriakiba' );
    $templates['page-faq.php']                     = __( 'FAQ (よくある質問)', 'kaitoriakiba' );
    $templates['page-access.php']                  = __( 'Store Access (店舗アクセス)', 'kaitoriakiba' );
    return $templates;
}
add_filter( 'theme_page_templates', 'kaitoriakiba_add_page_templates' );

/**
 * Shared store information (used by the Access page, footer, and LocalBusiness schema).
 *
 * @return array
 */
function kaitoriakiba_store_info() {
    return apply_filters( 'kaitori_store_info', array(
        'name'    => kaitori_t( 'Kaitori Akiba Akihabara Main Store', 'Kaitori Akiba 秋葉原総本店' ),
        'address' => kaitori_t( '1 Sotokanda, Chiyoda-ku, Tokyo (2 min from Akihabara Sta.)', '東京都千代田区外神田1丁目（秋葉原駅 徒歩2分）' ),
        'hours'   => kaitori_t( '10:00 - 19:30', '10:00 〜 19:30' ),
        'holiday' => kaitori_t( 'Open daily', '年中無休' ),
        'access'  => kaitori_t( '2 min walk from JR Akihabara Sta. Electric Town Exit', 'JR秋葉原駅 電気街口より徒歩2分' ),
        'phone'   => '',
        'license' => kaitori_t( 'Tokyo Public Safety Commission License No. 301031200000', '東京都公安委員会許可 第301031200000号' ),
    ) );
}

/**
 * Shared FAQ data (used by the FAQ page and FAQPage structured data).
 *
 * @return array<int,array{q:string,a:string}>
 */
function kaitoriakiba_get_faqs() {
    return apply_filters( 'kaitori_faqs', array(
        array(
            'q' => kaitori_t( 'How long does it take from application to payment?', '買取の申込みから入金までどのくらいかかりますか？' ),
            'a' => kaitori_t( 'For mail-in buyback, appraisal and payment can be completed as fast as the same day the device arrives. For in-store buyback we pay in cash on the spot.', '郵送買取の場合、端末到着後、最短即日で査定・ご入金が可能です。店頭買取ではその場で現金払いに対応しております。' ),
        ),
        array(
            'q' => kaitori_t( 'Can you buy devices that still have an installment balance?', '分割払い（残債）が残っている端末も買取できますか？' ),
            'a' => kaitori_t( 'Yes. Devices with a remaining installment balance (usage restriction △) can generally be bought with no price reduction. Please check the details in the application form.', 'はい。分割残債あり（利用制限△）の端末も、原則として減額査定なしでお買取り可能です。詳細はお申込みフォームよりご確認ください。' ),
        ),
        array(
            'q' => kaitori_t( 'Why is identity verification (eKYC) required?', '本人確認（eKYC）はなぜ必要ですか？' ),
            'a' => kaitori_t( 'Under the Antique Dealer Act, identity verification is mandatory for transactions. You can submit a driver license, My Number card, residence card, etc. online.', '古物営業法に基づき、お取引の際には本人確認が義務付けられています。運転免許証・マイナンバーカード・在留カード等をオンラインでご提出いただけます。' ),
        ),
        array(
            'q' => kaitori_t( 'Is the quoted price final?', '査定金額は確定ですか？' ),
            'a' => kaitori_t( 'The online quote is an estimate. The final buyback amount is confirmed after inspecting the actual device condition and included accessories.', 'オンライン査定は概算金額です。最終的な買取金額は、実機の状態・付属品の有無を検品のうえ確定いたします。' ),
        ),
        array(
            'q' => kaitori_t( 'Are there any shipping fees?', '送料はかかりますか？' ),
            'a' => kaitori_t( 'Mail-in buyback shipping is free. We also provide a free packing kit on request.', '郵送買取の送料は無料です。無料梱包キットの発送にも対応しております。' ),
        ),
        array(
            'q' => kaitori_t( 'Do I need to turn off Activation Lock?', 'アクティベーションロックは解除が必要ですか？' ),
            'a' => kaitori_t( 'Yes. Please turn off "Find My iPhone", sign out of Apple ID / iCloud, and reset the device. Devices that still have a lock cannot be purchased.', 'はい。「iPhoneを探す」をオフにし、Apple ID / iCloudからサインアウトのうえ初期化をお願いいたします。ロックが残っている端末は買取不可となります。' ),
        ),
    ) );
}

/**
 * 6. Helper: Get Device Formatted Price
 */
function kaitoriakiba_get_price( $post_id = null ) {
    $id = $post_id ?: get_the_ID();
    $price = get_post_meta( $id, '_max_buyback_price', true );
    return $price ? '¥' . number_format( (int)$price ) : '¥0';
}
