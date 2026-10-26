<?php
/**
 * Kaitori Akiba — One-Click Demo Import
 *
 * A single admin page (Appearance → Kaitori Demo Import) to import or re-sync the
 * demo content (taxonomies, sample devices, pages, menu), optionally add sample
 * buyback orders + an eKYC record so the Orders/Customers screens have data, and
 * cleanly remove everything tagged as demo.
 *
 * Reuses the existing seeder in inc/demo-data.php. Admin-only.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Register the import page under Appearance. */
function kaitoriakiba_demo_import_menu() {
    add_theme_page(
        kaitori_t( 'Kaitori Demo Import', 'Kaitori デモインポート' ),
        kaitori_t( 'Demo Import', 'デモインポート' ),
        'manage_options',
        'kaitori-demo-import',
        'kaitoriakiba_demo_import_page'
    );
}
add_action( 'admin_menu', 'kaitoriakiba_demo_import_menu' );

/** Count helper. */
function kaitoriakiba_count_posts_meta( $post_type, $meta_key = '', $meta_value = '' ) {
    $args = array( 'post_type' => $post_type, 'post_status' => 'any', 'fields' => 'ids', 'posts_per_page' => -1 );
    if ( $meta_key ) {
        $args['meta_key'] = $meta_key;
        $args['meta_value'] = $meta_value;
    }
    return count( get_posts( $args ) );
}

/** Create a handful of sample buyback orders + one eKYC record (tagged demo). */
function kaitoriakiba_demo_add_sample_orders() {
    // Avoid duplicating demo orders.
    if ( kaitoriakiba_count_posts_meta( 'buyback_order', '_kaitori_demo', 1 ) > 0 ) {
        return 0;
    }

    $samples = array(
        array( 'name' => '佐藤 花子', 'kana' => 'サトウ ハナコ', 'email' => 'demo.hanako@example.com', 'phone' => '09011112222', 'model' => 'iPhone 16 Pro 256GB Black (SIMフリー)', 'storage' => '256GB', 'imei' => '350000000000011', 'status' => 'inspected', 'screen' => '傷なし美品', 'body' => '良好・小傷あり', 'battery' => 94 ),
        array( 'name' => '鈴木 一郎', 'kana' => 'スズキ イチロウ', 'email' => 'demo.ichiro@example.com', 'phone' => '09033334444', 'model' => 'Google Pixel 8 Pro 128GB Obsidian (SIMフリー)', 'storage' => '128GB', 'imei' => '350000000000022', 'status' => 'paid', 'screen' => '微細なスレあり', 'body' => '打痕・角削れあり', 'battery' => 88 ),
        array( 'name' => '田中 美咲', 'kana' => 'タナカ ミサキ', 'email' => 'demo.misaki@example.com', 'phone' => '09055556666', 'model' => 'SONY Xperia 1 VI 256GB Black (SIMフリー)', 'storage' => '256GB', 'imei' => '350000000000033', 'status' => 'received', 'screen' => '傷なし美品', 'body' => '新品同様', 'battery' => 99 ),
    );

    $count = 0;
    foreach ( $samples as $s ) {
        $title = 'KB-' . date( 'Ymd' ) . '-' . wp_rand( 1000, 9999 ) . ' [' . $s['model'] . ']';
        $oid = wp_insert_post( array( 'post_title' => $title, 'post_status' => 'publish', 'post_type' => 'buyback_order' ) );
        if ( ! $oid || is_wp_error( $oid ) ) {
            continue;
        }
        $price = function_exists( 'kaitoriakiba_calculate_quote' ) ? kaitoriakiba_calculate_quote( array(
            'model' => $s['model'], 'storage' => $s['storage'], 'screen_condition' => $s['screen'],
            'body_condition' => $s['body'], 'battery_health' => $s['battery'], 'installment_status' => 'no',
        ) ) : 100000;

        $meta = array(
            '_kaitori_demo' => 1,
            '_order_status' => $s['status'],
            '_applicant_name' => $s['name'], '_applicant_kana' => $s['kana'],
            '_applicant_email' => $s['email'], '_applicant_phone' => $s['phone'],
            '_device_model' => $s['model'], '_device_storage' => $s['storage'],
            '_device_color' => 'Black', '_device_carrier' => 'SIMフリー',
            '_device_imei' => $s['imei'],
            '_screen_condition' => $s['screen'], '_body_condition' => $s['body'],
            '_battery_health' => $s['battery'], '_camera_works' => '正常作動',
            '_delivery_method' => 'mail', '_estimated_price' => $price,
            '_order_kyc_status' => 'unverified',
        );
        foreach ( $meta as $k => $v ) {
            update_post_meta( $oid, $k, $v );
        }
        $count++;
    }

    // One sample eKYC record linked to the first demo customer.
    if ( kaitoriakiba_count_posts_meta( 'kyc_record', '_kaitori_demo', 1 ) === 0 ) {
        $kid = wp_insert_post( array( 'post_title' => 'KYC-' . date( 'Ymd' ) . '-' . wp_rand( 1000, 9999 ) . ' [佐藤 花子]', 'post_status' => 'publish', 'post_type' => 'kyc_record' ) );
        if ( $kid && ! is_wp_error( $kid ) ) {
            $kmeta = array(
                '_kaitori_demo' => 1, '_kyc_status' => 'pending',
                '_kyc_name' => '佐藤 花子', '_kyc_kana' => 'サトウ ハナコ',
                '_kyc_email' => 'demo.hanako@example.com', '_kyc_phone' => '09011112222',
                '_kyc_dob' => '1990-05-10', '_kyc_prefecture' => '東京都', '_kyc_city' => '千代田区',
                '_kyc_address' => '外神田1-2-3', '_kyc_doc_type' => 'Driver License (運転免許証)',
            );
            foreach ( $kmeta as $k => $v ) {
                update_post_meta( $kid, $k, $v );
            }
        }
    }

    return $count;
}

/** Remove all content tagged as demo (devices, orders, eKYC). Pages are kept. */
function kaitoriakiba_demo_remove( $include_pages = false ) {
    $removed = 0;
    foreach ( array( 'kaitori_device', 'buyback_order', 'kyc_record' ) as $pt ) {
        $ids = get_posts( array( 'post_type' => $pt, 'post_status' => 'any', 'fields' => 'ids', 'posts_per_page' => -1, 'meta_key' => '_kaitori_demo', 'meta_value' => 1 ) );
        foreach ( $ids as $id ) {
            wp_delete_post( $id, true );
            $removed++;
        }
    }
    return $removed;
}

/** Render the admin page + handle actions. */
function kaitoriakiba_demo_import_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html( kaitori_t( 'Permission denied.', '権限がありません。' ) ) );
    }

    $notice = '';
    if ( isset( $_POST['kaitori_demo_action'] ) && check_admin_referer( 'kaitori_demo_import' ) ) {
        $action = sanitize_text_field( $_POST['kaitori_demo_action'] );
        if ( 'import' === $action ) {
            kaitoriakiba_run_initial_setup();
            flush_rewrite_rules();
            $notice = kaitori_t( 'Demo data (devices, categories, pages, menu) imported / re-synced.', 'デモデータ（機種・分類・固定ページ・メニュー）をインポート/再同期しました。' );
        } elseif ( 'samples' === $action ) {
            $n = kaitoriakiba_demo_add_sample_orders();
            $notice = $n > 0
                ? sprintf( kaitori_t( 'Created %d sample orders and an eKYC record.', 'サンプル注文 %d 件とeKYCレコードを作成しました。' ), $n )
                : kaitori_t( 'Sample orders already exist (no duplicates created).', 'サンプル注文は既に存在します（重複作成なし）。' );
        } elseif ( 'remove' === $action ) {
            $n = kaitoriakiba_demo_remove();
            $notice = sprintf( kaitori_t( 'Removed %d demo items (pages kept).', 'デモコンテンツ %d 件を削除しました（固定ページは保持）。' ), $n );
        }
    }

    // Status.
    $devices  = kaitoriakiba_count_posts_meta( 'kaitori_device' );
    $orders   = kaitoriakiba_count_posts_meta( 'buyback_order' );
    $kycs     = kaitoriakiba_count_posts_meta( 'kyc_record' );
    $cats     = (int) wp_count_terms( array( 'taxonomy' => 'device_category', 'hide_empty' => false ) );
    $pages_ok = get_page_by_path( 'sell-apply' ) && get_page_by_path( 'mypage' ) && get_page_by_path( 'faq' );
    $demo_dev = kaitoriakiba_count_posts_meta( 'kaitori_device', '_kaitori_demo', 1 );
    $demo_ord = kaitoriakiba_count_posts_meta( 'buyback_order', '_kaitori_demo', 1 );
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-database-import" style="font-size:28px;"></span> <?php echo esc_html( kaitori_t( 'Kaitori Demo Import (One-Click)', 'Kaitori デモインポート (ワンクリック)' ) ); ?></h1>
        <?php if ( $notice ) : ?>
            <div class="notice notice-success is-dismissible"><p><strong><?php echo esc_html( $notice ); ?></strong></p></div>
        <?php endif; ?>

        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;max-width:760px;margin-top:16px;">
            <h2 style="margin-top:0;"><?php echo esc_html( kaitori_t( 'Current Status', '現在の状態' ) ); ?></h2>
            <table class="widefat striped" style="max-width:520px;">
                <tbody>
                    <tr><td><?php echo esc_html( kaitori_t( 'Devices', '機種 (Devices)' ) ); ?></td><td><strong><?php echo (int) $devices; ?></strong> <?php echo $demo_dev ? '<small>(' . (int) $demo_dev . ' demo)</small>' : ''; ?></td></tr>
                    <tr><td><?php echo esc_html( kaitori_t( 'Categories', '分類 (Categories)' ) ); ?></td><td><strong><?php echo (int) $cats; ?></strong></td></tr>
                    <tr><td><?php echo esc_html( kaitori_t( 'Buyback Orders', '買取注文 (Orders)' ) ); ?></td><td><strong><?php echo (int) $orders; ?></strong> <?php echo $demo_ord ? '<small>(' . (int) $demo_ord . ' demo)</small>' : ''; ?></td></tr>
                    <tr><td><?php echo esc_html( kaitori_t( 'eKYC Records', 'eKYC レコード' ) ); ?></td><td><strong><?php echo (int) $kycs; ?></strong></td></tr>
                    <tr><td><?php echo esc_html( kaitori_t( 'Required Pages', '必須固定ページ' ) ); ?></td><td><?php echo $pages_ok ? '<span style="color:#15803d;font-weight:700;">✓ OK</span>' : '<span style="color:#b91c1c;font-weight:700;">' . esc_html( kaitori_t( 'Not created', '未作成' ) ) . '</span>'; ?></td></tr>
                </tbody>
            </table>
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:20px;max-width:760px;">
            <form method="post" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px;flex:1;min-width:230px;">
                <?php wp_nonce_field( 'kaitori_demo_import' ); ?>
                <h3 style="margin-top:0;">1. <?php echo esc_html( kaitori_t( 'Import Demo', 'デモをインポート' ) ); ?></h3>
                <p style="color:#64748b;font-size:13px;"><?php echo esc_html( kaitori_t( 'Creates / re-syncs categories, sample devices, pages and menu (no duplicates).', '分類・サンプル機種・固定ページ・メニューを作成/再同期します（重複なし）。' ) ); ?></p>
                <input type="hidden" name="kaitori_demo_action" value="import">
                <button class="button button-primary button-hero"><?php echo esc_html( kaitori_t( '▶ Import with One Click', '▶ ワンクリックでインポート' ) ); ?></button>
            </form>

            <form method="post" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px;flex:1;min-width:230px;">
                <?php wp_nonce_field( 'kaitori_demo_import' ); ?>
                <h3 style="margin-top:0;">2. <?php echo esc_html( kaitori_t( 'Sample Orders + eKYC', 'サンプル注文 + eKYC' ) ); ?></h3>
                <p style="color:#64748b;font-size:13px;"><?php echo esc_html( kaitori_t( 'Adds sample data so you can try the Orders, Customers and eKYC screens.', '注文・顧客・本人確認の画面を試すためのサンプルデータを追加します。' ) ); ?></p>
                <input type="hidden" name="kaitori_demo_action" value="samples">
                <button class="button button-secondary button-hero"><?php echo esc_html( kaitori_t( '＋ Add Sample Orders', '＋ サンプル注文を追加' ) ); ?></button>
            </form>
        </div>

        <form method="post" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:18px;max-width:760px;margin-top:16px;" onsubmit="return confirm('<?php echo esc_js( kaitori_t( 'This will delete all devices, orders and eKYC created as demo. Are you sure?', 'デモとして作成された機種・注文・eKYCをすべて削除します。よろしいですか？' ) ); ?>');">
            <?php wp_nonce_field( 'kaitori_demo_import' ); ?>
            <h3 style="margin-top:0;color:#9a3412;">3. <?php echo esc_html( kaitori_t( 'Remove Demo Data', 'デモデータを削除' ) ); ?></h3>
            <p style="color:#9a3412;font-size:13px;"><?php echo esc_html( kaitori_t( 'Deletes only devices, orders and eKYC tagged "demo" (pages and real data are kept).', '「demo」タグ付きの機種・注文・eKYCのみ削除します（固定ページ・実データは保持）。' ) ); ?></p>
            <input type="hidden" name="kaitori_demo_action" value="remove">
            <button class="button" style="color:#b91c1c;border-color:#fca5a5;"><?php echo esc_html( kaitori_t( '🗑 Remove Demo Data', '🗑 デモデータを削除' ) ); ?></button>
        </form>
    </div>
    <?php
}
