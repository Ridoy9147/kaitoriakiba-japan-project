<?php
/**
 * Kaitori Akiba — Buyback Invoice (買取明細書) printable / PDF document.
 *
 * Renders a polished, A4 print-optimised invoice for any buyback_order, in two copies:
 *   - 店舗控え  (Shop Copy)
 *   - お客様控え (Customer Copy)
 *
 * Output is a standalone HTML page (no admin chrome) served through admin-post.php.
 * Staff download it as a PDF via the browser's "Save as PDF" print option, which
 * renders Japanese perfectly and needs no bundled PDF library. A print dialog can
 * auto-open with &auto=1.
 *
 * Access is restricted to users with the kaitori manage capability and nonce-checked.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Build a secure URL to the printable invoice for an order.
 *
 * @param int  $order_id
 * @param bool $auto_print Open the print dialog automatically.
 * @return string
 */
function kaitoriakiba_invoice_url( $order_id, $auto_print = false ) {
    $args = array(
        'action' => 'kaitori_print_invoice',
        'order'  => (int) $order_id,
    );
    if ( $auto_print ) {
        $args['auto'] = 1;
    }
    $url = add_query_arg( $args, admin_url( 'admin-post.php' ) );
    return wp_nonce_url( $url, 'kaitori_invoice_' . (int) $order_id );
}

/**
 * Collect every field needed for an order's invoice into a flat array.
 *
 * @param int $order_id
 * @return array|null
 */
function kaitoriakiba_invoice_data( $order_id ) {
    $order = get_post( $order_id );
    if ( ! $order || 'buyback_order' !== $order->post_type ) {
        return null;
    }

    $g = function ( $key ) use ( $order_id ) {
        return get_post_meta( $order_id, $key, true );
    };

    // Final agreed amount (walk-in) takes precedence over the online estimate.
    $final = (int) $g( '_final_buyback_price' );
    if ( $final <= 0 ) {
        $final = (int) $g( '_estimated_price' );
    }

    // Bank fields are encrypted at rest.
    $acc_number = $g( '_bank_account_number' );
    $acc_holder = $g( '_bank_holder_kana' );
    if ( function_exists( 'kaitoriakiba_decrypt' ) ) {
        $acc_number = kaitoriakiba_decrypt( $acc_number );
        $acc_holder = kaitoriakiba_decrypt( $acc_holder );
    }

    return array(
        'order_id'    => $order_id,
        'order_code'  => $order->post_title,
        'date'        => get_the_date( 'Y-m-d', $order ),
        'status'      => $g( '_order_status' ) ?: 'pending',
        'name'        => $g( '_applicant_name' ),
        'kana'        => $g( '_applicant_kana' ),
        'email'       => $g( '_applicant_email' ),
        'phone'       => $g( '_applicant_phone' ),
        'model'       => $g( '_device_model' ),
        'storage'     => $g( '_device_storage' ),
        'color'       => $g( '_device_color' ),
        'carrier'     => $g( '_device_carrier' ),
        'imei'        => $g( '_device_imei' ),
        'serial'      => $g( '_device_serial' ),
        'screen'      => $g( '_screen_condition' ),
        'body'        => $g( '_body_condition' ),
        'battery'     => $g( '_battery_health' ),
        'delivery'    => $g( '_delivery_method' ) ?: 'store',
        'payment'     => $g( '_payment_method' ) ?: ( $g( '_delivery_method' ) === 'mail' ? 'bank' : 'cash' ),
        'bank_name'   => $g( '_bank_name' ),
        'bank_branch' => $g( '_bank_branch' ),
        'acc_type'    => $g( '_bank_account_type' ),
        'acc_number'  => $acc_number,
        'acc_holder'  => $acc_holder,
        'price'       => $final,
        'memo'        => $g( '_admin_memo' ),
    );
}

/**
 * Render one invoice copy (returns HTML).
 *
 * @param array  $d          Data from kaitoriakiba_invoice_data().
 * @param string $copy_label Localised copy label (e.g. Shop Copy).
 * @param string $copy_type  'shop' | 'customer' (controls the accent colour).
 * @param string $logo_url   Logo shown in the header band + background watermark.
 * @return string
 */
function kaitoriakiba_invoice_copy_html( $d, $copy_label, $copy_type, $logo_url ) {
    $store = function_exists( 'kaitoriakiba_store_info' ) ? kaitoriakiba_store_info() : array();
    $yen   = function ( $n ) { return '¥' . number_format( (int) $n ); };
    $e     = 'esc_html';

    $pay_label = ( $d['payment'] === 'bank' )
        ? kaitori_t( 'Bank transfer', '銀行振込' )
        : kaitori_t( 'Cash on the spot', '店頭現金' );

    $st        = function_exists( 'kaitoriakiba_get_order_status' ) ? kaitoriakiba_get_order_status( $d['status'] ) : array( 'label' => $d['status'], 'label_ja' => $d['status'], 'bg' => '#f1f5f9', 'color' => '#334155' );
    $st_label  = kaitori_t( $st['label'], $st['label_ja'] );
    $has_bank  = ( $d['payment'] === 'bank' && ( $d['bank_name'] || $d['acc_number'] ) );

    ob_start();
    ?>
    <div class="inv-copy inv-copy-<?php echo esc_attr( $copy_type ); ?>">

        <!-- Brand header band -->
        <div class="inv-band">
            <div class="inv-band-left">
                <?php if ( $logo_url ) : ?><img class="inv-logo" src="<?php echo esc_url( $logo_url ); ?>" alt=""><?php endif; ?>
                <div class="inv-shop">
                    <div class="inv-shop-name"><?php echo $e( $store['name'] ?? 'Kaitori Akiba' ); ?></div>
                    <?php if ( ! empty( $store['address'] ) ) : ?><div class="inv-shop-line"><?php echo $e( $store['address'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $store['phone'] ) ) : ?><div class="inv-shop-line">TEL: <?php echo $e( $store['phone'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $store['license'] ) ) : ?><div class="inv-license"><?php echo $e( $store['license'] ); ?></div><?php endif; ?>
                </div>
            </div>
            <div class="inv-band-right">
                <div class="inv-doc-title"><?php echo $e( kaitori_t( 'Buyback Invoice', '買取明細書' ) ); ?></div>
                <div class="inv-doc-sub">BUYBACK INVOICE</div>
            </div>
        </div>

        <!-- Copy tag + meta ribbon -->
        <div class="inv-ribbon">
            <span class="inv-copy-pill"><?php echo $e( $copy_label ); ?></span>
            <div class="inv-ribbon-meta">
                <span class="inv-chip"><em><?php echo $e( kaitori_t( 'No.', '伝票番号' ) ); ?></em> <b><?php echo $e( $d['order_code'] ); ?></b></span>
                <span class="inv-chip"><em><?php echo $e( kaitori_t( 'Date', '発行日' ) ); ?></em> <b><?php echo $e( $d['date'] ); ?></b></span>
                <span class="inv-status" style="background:<?php echo esc_attr( $st['bg'] ); ?>;color:<?php echo esc_attr( $st['color'] ); ?>;"><?php echo $e( $st_label ); ?></span>
            </div>
        </div>

        <!-- Customer + device cards -->
        <div class="inv-grid">
            <section class="inv-card">
                <h4><?php echo $e( kaitori_t( 'Customer', 'お客様情報' ) ); ?></h4>
                <table class="inv-kv">
                    <tr><th><?php echo $e( kaitori_t( 'Name', '氏名' ) ); ?></th><td><strong><?php echo $e( $d['name'] ?: '—' ); ?></strong><?php if ( $d['kana'] ) : ?> <span class="inv-sub">（<?php echo $e( $d['kana'] ); ?>）</span><?php endif; ?></td></tr>
                    <tr><th><?php echo $e( kaitori_t( 'Phone', '電話' ) ); ?></th><td><?php echo $e( $d['phone'] ?: '—' ); ?></td></tr>
                    <tr><th><?php echo $e( kaitori_t( 'Email', 'メール' ) ); ?></th><td><?php echo $e( $d['email'] ?: '—' ); ?></td></tr>
                </table>
            </section>
            <section class="inv-card">
                <h4><?php echo $e( kaitori_t( 'Device', '端末情報' ) ); ?></h4>
                <table class="inv-kv">
                    <tr><th><?php echo $e( kaitori_t( 'Model', '機種' ) ); ?></th><td><strong><?php echo $e( $d['model'] ?: '—' ); ?></strong> <?php echo $e( $d['storage'] ); ?></td></tr>
                    <tr><th><?php echo $e( kaitori_t( 'Color / Carrier', 'カラー / キャリア' ) ); ?></th><td><?php echo $e( trim( ( $d['color'] ?: '—' ) . ' / ' . ( $d['carrier'] ?: '—' ) ) ); ?></td></tr>
                    <tr><th>IMEI</th><td class="inv-mono"><?php echo $e( $d['imei'] ?: '—' ); ?></td></tr>
                    <tr><th><?php echo $e( kaitori_t( 'Condition', '状態' ) ); ?></th><td><?php echo $e( trim( ( $d['screen'] ?: '—' ) . ' / ' . ( $d['body'] ?: '—' ) ) ); ?><?php echo $d['battery'] ? $e( ' ・ ' . kaitori_t( 'Battery', 'バッテリー' ) . ' ' . $d['battery'] . '%' ) : ''; ?></td></tr>
                </table>
            </section>
        </div>

        <!-- Total panel -->
        <div class="inv-total">
            <div class="inv-total-left">
                <span class="inv-total-label"><?php echo $e( kaitori_t( 'Buyback Total', '買取金額（合計）' ) ); ?></span>
                <span class="inv-total-pay"><?php echo $e( kaitori_t( 'Payment', 'お支払' ) . ': ' . $pay_label ); ?></span>
            </div>
            <div class="inv-total-amount"><?php echo $e( $yen( $d['price'] ) ); ?><span class="inv-total-tax"><?php echo $e( kaitori_t( '(tax incl.)', '（税込）' ) ); ?></span></div>
        </div>

        <?php if ( $has_bank ) : ?>
            <div class="inv-pay"><b><?php echo $e( kaitori_t( 'Transfer to', '振込先' ) ); ?>:</b>
                <?php echo $e( trim( $d['bank_name'] . ' ' . $d['bank_branch'] . ' ' . $d['acc_type'] ) ); ?>
                <?php echo $d['acc_number'] ? $e( ' ' . $d['acc_number'] ) : ''; ?>
                <?php echo $d['acc_holder'] ? $e( ' / ' . $d['acc_holder'] ) : ''; ?>
            </div>
        <?php endif; ?>

        <?php if ( $d['memo'] ) : ?>
            <div class="inv-memo"><b><?php echo $e( kaitori_t( 'Note', '備考' ) ); ?>:</b> <?php echo $e( $d['memo'] ); ?></div>
        <?php endif; ?>

        <div class="inv-legal">
            <?php echo $e( kaitori_t(
                'Identity verified under the Antique Dealer Act. By signing, the seller certifies legal ownership of the device and that it is not stolen or under a fraudulent contract.',
                '古物営業法に基づき本人確認を実施済み。ご署名をもって、売却者は本端末の正当な所有権を有し、盗品・不正契約品でないことを証明します。'
            ) ); ?>
        </div>

        <div class="inv-sign">
            <div class="inv-sign-box"><span><?php echo $e( kaitori_t( 'Seller signature', 'お客様ご署名' ) ); ?></span></div>
            <div class="inv-sign-box"><span><?php echo $e( kaitori_t( 'Staff in charge', '担当者' ) ); ?></span></div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * admin-post handler: output the full printable invoice page (both copies) and exit.
 */
function kaitoriakiba_print_invoice() {
    $order_id = isset( $_GET['order'] ) ? (int) $_GET['order'] : 0;

    if ( ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        wp_die( esc_html( kaitori_t( 'You do not have permission to view this invoice.', 'この伝票を表示する権限がありません。' ) ) );
    }
    if ( ! $order_id || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'kaitori_invoice_' . $order_id ) ) {
        wp_die( esc_html( kaitori_t( 'Invalid or expired invoice link.', '伝票リンクが無効または期限切れです。' ) ) );
    }

    $d = kaitoriakiba_invoice_data( $order_id );
    if ( ! $d ) {
        wp_die( esc_html( kaitori_t( 'Order not found.', '注文が見つかりませんでした。' ) ) );
    }

    $auto      = ! empty( $_GET['auto'] );
    // Logo shown in the header band and as a faint background watermark. Filterable.
    $logo_url  = apply_filters( 'kaitori_invoice_watermark_url', get_template_directory_uri() . '/assets/images/akiba-logo.png' );
    $shop_copy = kaitoriakiba_invoice_copy_html( $d, kaitori_t( 'Shop Copy', '店舗控え' ), 'shop', $logo_url );
    $cust_copy = kaitoriakiba_invoice_copy_html( $d, kaitori_t( 'Customer Copy', 'お客様控え' ), 'customer', $logo_url );
    $print_lbl = kaitori_t( 'Print / Save as PDF', '印刷 / PDF保存' );
    $close_lbl = kaitori_t( 'Close', '閉じる' );
    ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc_html( kaitori_t( 'Buyback Invoice', '買取明細書' ) . ' ' . $d['order_code'] ); ?></title>
    <style>
        :root { --inv-red:#ef4444; --inv-red-d:#dc2626; --inv-ink:#0f172a; --inv-line:#e2e8f0; }
        * { box-sizing: border-box; }
        body {
            margin: 0; background: #eef2f6; color: var(--inv-ink);
            font-family: "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, "Yu Gothic", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 12px; line-height: 1.5;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .inv-toolbar { position: sticky; top: 0; z-index: 10; background: var(--inv-ink); padding: 12px 16px; display: flex; gap: 10px; justify-content: center; }
        .inv-toolbar button { border: none; border-radius: 8px; padding: 10px 24px; font-size: 14px; font-weight: 800; cursor: pointer; }
        .inv-btn-print { background: var(--inv-red); color: #fff; }
        .inv-btn-close { background: #334155; color: #fff; }
        .inv-sheet { max-width: 190mm; margin: 16px auto; background: #fff; padding: 11mm 11mm; box-shadow: 0 6px 24px rgba(0,0,0,.12); }
        .inv-cut { border: none; border-top: 1.5px dashed #94a3b8; margin: 8mm 0; text-align: center; }
        .inv-cut span { position: relative; top: -9px; background: #fff; padding: 0 10px; color: #94a3b8; font-size: 10px; letter-spacing: 2px; }

        .inv-copy { position: relative; z-index: 0; overflow: hidden; padding: 2mm 0; }
        .inv-copy::before {
            content: ""; position: absolute; top: 54%; left: 50%;
            width: 110mm; height: 110mm; transform: translate(-50%, -50%);
            background: url('<?php echo esc_url( $logo_url ); ?>') center / contain no-repeat;
            opacity: .05; z-index: -1; pointer-events: none;
        }

        /* Header band */
        .inv-band { display: flex; justify-content: space-between; align-items: center; gap: 14px; padding-bottom: 10px; border-bottom: 3px solid var(--inv-red); }
        .inv-band-left { display: flex; align-items: center; gap: 12px; }
        .inv-logo { width: 46px; height: 46px; object-fit: contain; flex: none; }
        .inv-shop-name { font-size: 16px; font-weight: 900; letter-spacing: .3px; }
        .inv-shop-line { font-size: 10px; color: #475569; }
        .inv-license { font-size: 10px; color: var(--inv-red-d); font-weight: 700; margin-top: 2px; }
        .inv-band-right { text-align: right; }
        .inv-doc-title { font-size: 22px; font-weight: 900; letter-spacing: 4px; color: var(--inv-ink); line-height: 1.1; }
        .inv-doc-sub { font-size: 9px; letter-spacing: 3px; color: #94a3b8; font-weight: 700; }

        /* Ribbon */
        .inv-ribbon { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
        .inv-copy-pill { font-weight: 800; font-size: 12px; color: #fff; padding: 4px 14px; border-radius: 999px; letter-spacing: 1px; }
        .inv-copy-shop .inv-copy-pill { background: #1d4ed8; }
        .inv-copy-customer .inv-copy-pill { background: #15803d; }
        .inv-ribbon-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .inv-chip { font-size: 11px; color: #334155; background: #f1f5f9; border: 1px solid var(--inv-line); border-radius: 6px; padding: 3px 9px; }
        .inv-chip em { font-style: normal; color: #94a3b8; margin-right: 4px; }
        .inv-chip b { color: var(--inv-ink); }
        .inv-status { font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 999px; }

        /* Cards */
        .inv-grid { display: flex; gap: 10px; margin-top: 12px; }
        .inv-card { flex: 1; border: 1px solid var(--inv-line); border-radius: 10px; overflow: hidden; }
        .inv-card h4 { margin: 0; font-size: 11px; font-weight: 800; color: #fff; background: var(--inv-ink); padding: 6px 10px; letter-spacing: .5px; }
        table.inv-kv { width: 100%; border-collapse: collapse; }
        .inv-kv th, .inv-kv td { border-top: 1px solid #eef2f6; padding: 5px 10px; font-size: 11px; text-align: left; vertical-align: top; }
        .inv-kv tr:first-child th, .inv-kv tr:first-child td { border-top: none; }
        .inv-kv th { background: #f8fafc; color: #475569; font-weight: 700; width: 38%; white-space: nowrap; }
        .inv-sub { color: #64748b; font-size: 10px; }
        .inv-mono { font-family: "SFMono-Regular", Consolas, monospace; letter-spacing: .5px; }

        /* Total */
        .inv-total { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding: 12px 18px; border-radius: 12px;
            background: linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border: 2px solid #fdba74; }
        .inv-total-label { display: block; font-size: 13px; font-weight: 800; color: #9a3412; }
        .inv-total-pay { display: block; font-size: 10.5px; color: #b45309; margin-top: 2px; }
        .inv-total-amount { font-size: 28px; font-weight: 900; color: var(--inv-red-d); white-space: nowrap; }
        .inv-total-tax { font-size: 11px; font-weight: 700; color: #b45309; margin-left: 4px; }

        .inv-pay { margin-top: 8px; font-size: 11px; color: #334155; background: #f8fafc; border: 1px solid var(--inv-line); border-radius: 8px; padding: 6px 10px; }
        .inv-memo { margin-top: 8px; font-size: 11px; color: #334155; }
        .inv-legal { margin-top: 10px; font-size: 9px; color: #64748b; line-height: 1.6; border-left: 3px solid var(--inv-line); padding: 2px 0 2px 10px; }
        .inv-sign { display: flex; gap: 14px; margin-top: 12px; }
        .inv-sign-box { flex: 1; border: 1px solid #94a3b8; border-radius: 8px; height: 50px; position: relative; }
        .inv-sign-box span { position: absolute; top: 4px; left: 8px; font-size: 10px; color: #64748b; }

        @media print {
            body { background: #fff; }
            .inv-toolbar { display: none; }
            .inv-sheet { box-shadow: none; margin: 0; max-width: none; padding: 5mm; }
            .inv-copy { page-break-inside: avoid; }
            .inv-cut { page-break-inside: avoid; }
        }
        @page { size: A4; margin: 8mm; }
    </style>
</head>
<body>
    <div class="inv-toolbar">
        <button type="button" class="inv-btn-print" onclick="window.print();"><?php echo esc_html( $print_lbl ); ?></button>
        <button type="button" class="inv-btn-close" onclick="window.close();"><?php echo esc_html( $close_lbl ); ?></button>
    </div>
    <div class="inv-sheet">
        <?php echo $shop_copy; // phpcs:ignore WordPress.Security.EscapeOutput ?>
        <div class="inv-cut"><span><?php echo esc_html( kaitori_t( '— cut here —', '— キリトリ線 —' ) ); ?></span></div>
        <?php echo $cust_copy; // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </div>
    <?php if ( $auto ) : ?>
    <script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 400); });</script>
    <?php endif; ?>
</body>
</html>
    <?php
    exit;
}
add_action( 'admin_post_kaitori_print_invoice', 'kaitoriakiba_print_invoice' );

/* =========================================================================
 * COMBINED BUYBACK INVOICE (複数注文の一括買取明細書・請求書)
 * ========================================================================= */

/**
 * Register "Invoice Generate" submenu under Buyback Orders.
 */
function kaitoriakiba_combined_invoice_menu() {
    add_submenu_page(
        'edit.php?post_type=buyback_order',
        kaitori_t( 'Generate Invoice', '一括請求書・買取明細書作成' ),
        kaitori_t( 'Invoice Generate', '一括請求書作成' ),
        kaitoriakiba_manage_cap(),
        'kaitori-invoice-generate',
        'kaitoriakiba_combined_invoice_page'
    );
}
add_action( 'admin_menu', 'kaitoriakiba_combined_invoice_menu' );

/**
 * Add bulk action for combined invoice generation in the orders list table.
 */
function kaitoriakiba_invoice_bulk_actions( $bulk_actions ) {
    $bulk_actions['generate_combined_invoice'] = kaitori_t( 'Generate Combined Invoice', '選択した注文の一括明細書を作成' );
    return $bulk_actions;
}
add_filter( 'bulk_actions-edit-buyback_order', 'kaitoriakiba_invoice_bulk_actions' );

/**
 * Handle bulk action redirect to the combined invoice generator.
 */
function kaitoriakiba_handle_invoice_bulk_actions( $redirect_to, $doaction, $post_ids ) {
    if ( 'generate_combined_invoice' === $doaction && ! empty( $post_ids ) ) {
        return add_query_arg(
            array(
                'post_type' => 'buyback_order',
                'page'      => 'kaitori-invoice-generate',
                'order_ids' => implode( ',', array_map( 'intval', $post_ids ) ),
            ),
            admin_url( 'edit.php' )
        );
    }
    return $redirect_to;
}
add_filter( 'handle_bulk_actions-edit-buyback_order', 'kaitoriakiba_handle_invoice_bulk_actions', 10, 3 );

/**
 * Add "Invoice Generate" action button in the Buyback Orders admin list view.
 */
function kaitoriakiba_invoice_list_action_button() {
    $screen = get_current_screen();
    if ( $screen && 'edit-buyback_order' === $screen->id ) {
        ?>
        <script>
        jQuery(document).ready(function($){
            var $titleAction = $('.wrap .page-title-action').last();
            if ($titleAction.length && !$('#kaitori-gen-inv-btn').length) {
                $('<a id="kaitori-gen-inv-btn" href="<?php echo esc_url( admin_url( 'edit.php?post_type=buyback_order&page=kaitori-invoice-generate' ) ); ?>" class="page-title-action" style="background:#0f172a;color:#fff;border-color:#0f172a;margin-left:6px;"><span class="dashicons dashicons-media-document" style="font-size:16px;line-height:26px;vertical-align:top;margin-right:2px;"></span> <?php echo esc_js( kaitori_t( 'Invoice Generate', '一括請求書作成' ) ); ?></a>').insertAfter($titleAction);
            }
        });
        </script>
        <?php
    }
}
add_action( 'admin_footer', 'kaitoriakiba_invoice_list_action_button' );

/**
 * Search buyback orders for the invoice generator by keyword, order code, phone, email, or IMEI.
 *
 * @param string $query
 * @param int    $limit
 * @return array
 */
function kaitoriakiba_find_orders_for_invoice( $query, $limit = 30 ) {
    $query = trim( (string) $query );
    if ( '' === $query ) {
        return array();
    }

    $found_ids = array();

    // Check if comma or space-separated list of order codes or IDs
    if ( preg_match( '/[,、\s]+/', $query ) ) {
        $tokens = preg_split( '/[,、\s]+/', $query );
        foreach ( $tokens as $tok ) {
            $tok = trim( $tok );
            if ( '' === $tok ) continue;

            if ( is_numeric( $tok ) ) {
                $p = get_post( (int) $tok );
                if ( $p && 'buyback_order' === $p->post_type ) {
                    $found_ids[] = (int) $tok;
                }
            }
            $matches = get_posts( array(
                'post_type'      => 'buyback_order',
                'post_status'    => 'any',
                'title'          => $tok,
                'posts_per_page' => 5,
                'fields'         => 'ids',
            ) );
            if ( $matches ) {
                $found_ids = array_merge( $found_ids, $matches );
            }
        }
    }

    // Direct ID lookup
    if ( is_numeric( $query ) ) {
        $p = get_post( (int) $query );
        if ( $p && 'buyback_order' === $p->post_type ) {
            $found_ids[] = (int) $query;
        }
    }

    // Search by title (Order Code)
    $by_title = get_posts( array(
        'post_type'        => 'buyback_order',
        'post_status'      => 'any',
        's'                => $query,
        'posts_per_page'   => $limit,
        'fields'           => 'ids',
        'suppress_filters' => false,
    ) );
    if ( $by_title ) {
        $found_ids = array_merge( $found_ids, $by_title );
    }

    // Search by Meta (IMEI, Customer Name, Phone, Email, Model)
    $meta_keys = array(
        '_device_imei',
        '_applicant_name',
        '_applicant_kana',
        '_applicant_phone',
        '_applicant_email',
        '_device_model',
    );
    $meta_query = array( 'relation' => 'OR' );
    foreach ( $meta_keys as $mk ) {
        $meta_query[] = array(
            'key'     => $mk,
            'value'   => $query,
            'compare' => 'LIKE',
        );
    }
    $by_meta = get_posts( array(
        'post_type'      => 'buyback_order',
        'post_status'    => 'any',
        'posts_per_page' => $limit,
        'fields'         => 'ids',
        'meta_query'     => $meta_query,
    ) );
    if ( $by_meta ) {
        $found_ids = array_merge( $found_ids, $by_meta );
    }

    $found_ids = array_unique( array_filter( array_map( 'intval', $found_ids ) ) );
    $results   = array();

    foreach ( $found_ids as $oid ) {
        $d = kaitoriakiba_invoice_data( $oid );
        if ( $d ) {
            $results[] = $d;
        }
        if ( count( $results ) >= $limit ) {
            break;
        }
    }

    return $results;
}

/**
 * AJAX handler to search orders for the invoice generator.
 */
function kaitoriakiba_ajax_search_invoice_orders() {
    check_ajax_referer( 'kaitori_invoice_ajax', 'nonce' );

    if ( ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Permission denied.', '権限がありません。' ) ) );
    }

    $q = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
    if ( '' === $q ) {
        wp_send_json_success( array( 'orders' => array() ) );
    }

    $orders = kaitoriakiba_find_orders_for_invoice( $q );
    wp_send_json_success( array( 'orders' => $orders ) );
}
add_action( 'wp_ajax_kaitori_search_invoice_orders', 'kaitoriakiba_ajax_search_invoice_orders' );

/**
 * Render the "Invoice Generate" Admin Page.
 */
function kaitoriakiba_combined_invoice_page() {
    if ( ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        wp_die( esc_html( kaitori_t( 'Permission denied.', '権限がありません。' ) ) );
    }

    // Check for preloaded order IDs (e.g. from bulk actions)
    $preloaded_orders = array();
    if ( ! empty( $_GET['order_ids'] ) ) {
        $raw_ids = explode( ',', sanitize_text_field( wp_unslash( $_GET['order_ids'] ) ) );
        foreach ( $raw_ids as $rid ) {
            $oid = (int) $rid;
            if ( $oid > 0 ) {
                $d = kaitoriakiba_invoice_data( $oid );
                if ( $d ) {
                    $preloaded_orders[] = $d;
                }
            }
        }
    }

    $ajax_nonce = wp_create_nonce( 'kaitori_invoice_ajax' );
    $post_nonce = wp_create_nonce( 'kaitori_print_combined_invoice' );
    ?>
    <div class="wrap" style="max-width:1150px; margin:20px 0;">
        <h1 style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
            <span class="dashicons dashicons-media-document" style="font-size:30px; width:30px; height:30px; color:#ef4444;"></span>
            <?php echo esc_html( kaitori_t( 'Generate Combined Buyback Invoice', '一括請求書・買取明細書作成' ) ); ?>
        </h1>
        <p style="color:#64748b; font-size:13.5px; margin-top:2px; margin-bottom:20px;">
            <?php echo esc_html( kaitori_t(
                'Search and select multiple buyback applications for the same customer to generate and print a single consolidated invoice.',
                '同一のお客様による複数の買取申込（Application ID）を検索・追加し、1枚にまとめた一括買取明細書・請求書を発行・印刷できます。'
            ) ); ?>
        </p>

        <!-- Search & Add Section -->
        <div style="background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:20px 24px; box-shadow:0 4px 12px rgba(0,0,0,0.04); margin-bottom:24px;">
            <h2 style="font-size:15px; font-weight:800; color:#0f172a; margin:0 0 14px 0; display:flex; align-items:center; gap:6px;">
                <span class="dashicons dashicons-search" style="color:#2563eb;"></span>
                <?php echo esc_html( kaitori_t( '1. Search & Add Buyback Orders', '1. 買取注文を検索・追加' ) ); ?>
            </h2>

            <div style="display:grid; grid-template-columns: 2fr 1.5fr; gap:20px; align-items:start;">
                <!-- Live Search Box -->
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:6px;">
                        <?php echo esc_html( kaitori_t( 'Live Search (Order Code, Customer Name, Phone, Email, IMEI)', 'リアルタイム検索（申込ID、お客様名、電話番号、メール、IMEI）' ) ); ?>
                    </label>
                    <div style="position:relative;">
                        <input type="text" id="inv_search_input" placeholder="<?php echo esc_attr( kaitori_t( 'Type e.g. BB-202611-001, Yamada, 09012345678, or IMEI...', '申込ID、氏名、電話番号、IMEIを入力...' ) ); ?>" style="width:100%; height:40px; padding:0 12px; font-size:14px; border:1.5px solid #94a3b8; border-radius:8px;">
                        <span id="inv_search_spinner" class="spinner" style="position:absolute; right:10px; top:10px; margin:0; float:none;"></span>
                    </div>
                    <div id="inv_search_dropdown" style="display:none; margin-top:6px; background:#fff; border:1px solid #cbd5e1; border-radius:8px; max-height:280px; overflow-y:auto; box-shadow:0 10px 25px rgba(0,0,0,0.1); position:absolute; z-index:99; width:480px;"></div>
                </div>

                <!-- Paste Multiple Codes Box -->
                <div>
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:6px;">
                        <?php echo esc_html( kaitori_t( 'Or Paste Multiple Application IDs', 'または複数の申込IDをまとめて入力' ) ); ?>
                    </label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="inv_bulk_input" placeholder="BB-202611-0001, BB-202611-0002" style="flex:1; height:40px; padding:0 12px; font-size:13px; border:1.5px solid #94a3b8; border-radius:8px;">
                        <button type="button" id="inv_bulk_btn" class="button button-secondary" style="height:40px; font-weight:700; padding:0 16px;">
                            <?php echo esc_html( kaitori_t( '+ Add All', '+ まとめて追加' ) ); ?>
                        </button>
                    </div>
                    <span style="font-size:11px; color:#64748b; margin-top:4px; display:block;">
                        <?php echo esc_html( kaitori_t( 'Separate by comma or space', 'カンマまたはスペース区切りで入力' ) ); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Invoice Form -->
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" target="_blank" id="kaitori_combined_invoice_form">
            <input type="hidden" name="action" value="kaitori_print_combined_invoice">
            <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $post_nonce ); ?>">

            <!-- Selected Items Table -->
            <div style="background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:20px 24px; box-shadow:0 4px 12px rgba(0,0,0,0.04); margin-bottom:24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                    <h2 style="font-size:15px; font-weight:800; color:#0f172a; margin:0; display:flex; align-items:center; gap:6px;">
                        <span class="dashicons dashicons-smartphone" style="color:#ef4444;"></span>
                        <?php echo esc_html( kaitori_t( '2. Included Buyback Devices (Line Items)', '2. 明細に追加された買取端末' ) ); ?>
                    </h2>
                    <button type="button" id="inv_clear_all_btn" class="button button-link-delete" style="font-size:12px;">
                        <?php echo esc_html( kaitori_t( 'Clear All Items', 'すべてクリア' ) ); ?>
                    </button>
                </div>

                <table class="wp-list-table widefat fixed striped" style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="width:40px; text-align:center;">#</th>
                            <th style="width:140px; font-weight:700;"><?php echo esc_html( kaitori_t( 'Application ID', '申込ID' ) ); ?></th>
                            <th style="font-weight:700;"><?php echo esc_html( kaitori_t( 'Device Model & Storage', '機種・容量' ) ); ?></th>
                            <th style="width:170px; font-weight:700;"><?php echo esc_html( kaitori_t( 'Color / Carrier / IMEI', 'カラー / キャリア / IMEI' ) ); ?></th>
                            <th style="width:130px; font-weight:700;"><?php echo esc_html( kaitori_t( 'Condition', '状態' ) ); ?></th>
                            <th style="width:140px; text-align:right; font-weight:700;"><?php echo esc_html( kaitori_t( 'Buyback Price (¥)', '買取金額 (円)' ) ); ?></th>
                            <th style="width:60px; text-align:center;"></th>
                        </tr>
                    </thead>
                    <tbody id="inv_items_tbody">
                        <tr id="inv_empty_row" style="display:none;">
                            <td colspan="7" style="text-align:center; padding:32px 16px; color:#94a3b8; font-size:13.5px;">
                                <span class="dashicons dashicons-warning" style="font-size:24px; width:24px; height:24px; vertical-align:middle; margin-right:4px;"></span>
                                <?php echo esc_html( kaitori_t( 'No orders added yet. Search above or paste Application IDs.', 'まだ注文が追加されていません。上の検索バーまたは一括入力から追加してください。' ) ); ?>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:#fff7ed; border-top:2px solid #fdba74;">
                            <th colspan="4" style="text-align:right; font-size:14px; font-weight:800; color:#9a3412;">
                                <?php echo esc_html( kaitori_t( 'Total Devices (合計台数):', '合計台数:' ) ); ?>
                                <span id="inv_total_qty" style="color:#0f172a; margin-left:4px;">0</span> <?php echo esc_html( kaitori_t( 'devices', '台' ) ); ?>
                            </th>
                            <th style="text-align:right; font-size:14px; font-weight:800; color:#9a3412;">
                                <?php echo esc_html( kaitori_t( 'Grand Total:', '買取合計金額:' ) ); ?>
                            </th>
                            <th style="text-align:right; font-size:18px; font-weight:900; color:#dc2626;">
                                ¥<span id="inv_grand_total">0</span>
                                <small style="font-size:11px; font-weight:700; color:#b45309;"><?php echo esc_html( kaitori_t( '(tax incl.)', '（税込）' ) ); ?></small>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Customer & Payment Details Section -->
            <div style="background:#fff; border:1px solid #cbd5e1; border-radius:12px; padding:20px 24px; box-shadow:0 4px 12px rgba(0,0,0,0.04); margin-bottom:24px;">
                <h2 style="font-size:15px; font-weight:800; color:#0f172a; margin:0 0 16px 0; display:flex; align-items:center; gap:6px;">
                    <span class="dashicons dashicons-admin-users" style="color:#16a34a;"></span>
                    <?php echo esc_html( kaitori_t( '3. Customer & Payout Information (Auto-populated)', '3. お客様情報・お支払い情報（自動反映）' ) ); ?>
                </h2>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <!-- Customer Information -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px;">
                        <h3 style="margin:0 0 12px 0; font-size:13.5px; font-weight:800; color:#1e293b;"><?php echo esc_html( kaitori_t( 'Customer Details', 'お客様情報' ) ); ?></h3>
                        
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;"><?php echo esc_html( kaitori_t( 'Full Name', 'お名前 (氏名)' ) ); ?></label>
                            <input type="text" name="customer_name" id="cust_name" style="width:100%; height:36px; border-radius:6px; border:1px solid #cbd5e1;" required>
                        </div>
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;"><?php echo esc_html( kaitori_t( 'Furigana (Kana)', 'フリガナ' ) ); ?></label>
                            <input type="text" name="customer_kana" id="cust_kana" style="width:100%; height:36px; border-radius:6px; border:1px solid #cbd5e1;">
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                            <div>
                                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;"><?php echo esc_html( kaitori_t( 'Phone Number', '電話番号' ) ); ?></label>
                                <input type="text" name="customer_phone" id="cust_phone" style="width:100%; height:36px; border-radius:6px; border:1px solid #cbd5e1;">
                            </div>
                            <div>
                                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;"><?php echo esc_html( kaitori_t( 'Email Address', 'メールアドレス' ) ); ?></label>
                                <input type="email" name="customer_email" id="cust_email" style="width:100%; height:36px; border-radius:6px; border:1px solid #cbd5e1;">
                            </div>
                        </div>
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;"><?php echo esc_html( kaitori_t( 'Address (Optional)', 'ご住所' ) ); ?></label>
                            <input type="text" name="customer_address" id="cust_address" placeholder="<?php echo esc_attr( kaitori_t( 'e.g. Tokyo, Chiyoda-ku Sotokanda 1-2-3', '東京都千代田区外神田1-2-3' ) ); ?>" style="width:100%; height:36px; border-radius:6px; border:1px solid #cbd5e1;">
                        </div>
                    </div>

                    <!-- Payment & Invoice Meta -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px;">
                        <h3 style="margin:0 0 12px 0; font-size:13.5px; font-weight:800; color:#1e293b;"><?php echo esc_html( kaitori_t( 'Payment & Invoice Details', 'お支払・伝票設定' ) ); ?></h3>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                            <div>
                                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;"><?php echo esc_html( kaitori_t( 'Issue Date', '発行日' ) ); ?></label>
                                <input type="date" name="invoice_date" id="inv_date" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" style="width:100%; height:36px; border-radius:6px; border:1px solid #cbd5e1;" required>
                            </div>
                            <div>
                                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;"><?php echo esc_html( kaitori_t( 'Payment Method', 'お支払い方法' ) ); ?></label>
                                <select name="payment_method" id="inv_payment_method" style="width:100%; height:36px; border-radius:6px; border:1px solid #cbd5e1;">
                                    <option value="bank"><?php echo esc_html( kaitori_t( 'Bank Transfer (銀行振込)', '銀行振込' ) ); ?></option>
                                    <option value="cash"><?php echo esc_html( kaitori_t( 'Cash on the spot (店頭現金)', '店頭現金' ) ); ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- Bank Details Container -->
                        <div id="inv_bank_container" style="border:1px dashed #cbd5e1; border-radius:8px; padding:10px; background:#fff; margin-bottom:10px;">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:6px;">
                                <div>
                                    <label style="font-size:10.5px; font-weight:700; color:#64748b;"><?php echo esc_html( kaitori_t( 'Bank Name', '銀行名' ) ); ?></label>
                                    <input type="text" name="bank_name" id="cust_bank_name" placeholder="三菱UFJ銀行" style="width:100%; height:32px; font-size:12px; border-radius:4px; border:1px solid #cbd5e1;">
                                </div>
                                <div>
                                    <label style="font-size:10.5px; font-weight:700; color:#64748b;"><?php echo esc_html( kaitori_t( 'Branch Name', '支店名' ) ); ?></label>
                                    <input type="text" name="bank_branch" id="cust_bank_branch" placeholder="秋葉原支店" style="width:100%; height:32px; font-size:12px; border-radius:4px; border:1px solid #cbd5e1;">
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns:90px 1fr 1fr; gap:8px;">
                                <div>
                                    <label style="font-size:10.5px; font-weight:700; color:#64748b;"><?php echo esc_html( kaitori_t( 'Type', '口座種別' ) ); ?></label>
                                    <select name="bank_account_type" id="cust_bank_acc_type" style="width:100%; height:32px; font-size:12px; border-radius:4px; border:1px solid #cbd5e1;">
                                        <option value="普通">普通</option>
                                        <option value="当座">当座</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:10.5px; font-weight:700; color:#64748b;"><?php echo esc_html( kaitori_t( 'Account Number', '口座番号' ) ); ?></label>
                                    <input type="text" name="bank_account_number" id="cust_bank_acc_number" placeholder="1234567" style="width:100%; height:32px; font-size:12px; border-radius:4px; border:1px solid #cbd5e1;">
                                </div>
                                <div>
                                    <label style="font-size:10.5px; font-weight:700; color:#64748b;"><?php echo esc_html( kaitori_t( 'Holder (Kana)', '口座名義 (カナ)' ) ); ?></label>
                                    <input type="text" name="bank_holder_kana" id="cust_bank_holder" placeholder="ヤマダ タロウ" style="width:100%; height:32px; font-size:12px; border-radius:4px; border:1px solid #cbd5e1;">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;"><?php echo esc_html( kaitori_t( 'Memo / Notes (Printed on Invoice)', '備考・特記事項（伝票に印字されます）' ) ); ?></label>
                            <input type="text" name="invoice_memo" id="inv_memo" placeholder="<?php echo esc_attr( kaitori_t( 'e.g. Identity verified by Driver License', '例: 運転免許証にて本人確認済み' ) ); ?>" style="width:100%; height:36px; border-radius:6px; border:1px solid #cbd5e1;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generate & Print Action Bar -->
            <div style="display:flex; justify-content:space-between; align-items:center; background:#0f172a; padding:18px 24px; border-radius:12px; box-shadow:0 10px 25px rgba(15,23,42,0.15);">
                <div style="color:#fff;">
                    <span style="font-size:15px; font-weight:800; display:block;">
                        <?php echo esc_html( kaitori_t( 'Ready to issue invoice?', '一括買取明細書を発行しますか？' ) ); ?>
                    </span>
                    <span style="font-size:12px; color:#94a3b8;">
                        <?php echo esc_html( kaitori_t( 'Outputs print-optimized A4 format (Shop Copy + Customer Copy) in a new window.', '店舗控え・お客様控えがA4フォーマットで別ウィンドウに印刷プレビュー表示されます。' ) ); ?>
                    </span>
                </div>
                <button type="submit" id="inv_submit_btn" class="button button-primary" style="background:#ef4444; border-color:#ef4444; height:46px; padding:0 28px; font-size:15px; font-weight:800; border-radius:8px; display:inline-flex; align-items:center; gap:8px;">
                    <span class="dashicons dashicons-printer" style="font-size:20px; width:20px; height:20px;"></span>
                    <?php echo esc_html( kaitori_t( 'Generate & Print Combined Invoice', '一括請求書を発行・印刷' ) ); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Client-side script for invoice management -->
    <script>
    (function($){
        var selectedOrders = [];
        var preloaded = <?php echo wp_json_encode( $preloaded_orders ); ?>;

        function renderRows() {
            var $tbody = $('#inv_items_tbody');
            $tbody.find('.inv-item-row').remove();

            if (selectedOrders.length === 0) {
                $('#inv_empty_row').show();
                $('#inv_total_qty').text('0');
                $('#inv_grand_total').text('0');
                $('#inv_submit_btn').prop('disabled', true).css('opacity', 0.5);
                return;
            }

            $('#inv_empty_row').hide();
            $('#inv_submit_btn').prop('disabled', false).css('opacity', 1);

            var totalQty = 0;
            var grandTotal = 0;

            $.each(selectedOrders, function(idx, item){
                totalQty++;
                var price = parseInt(item.price, 10) || 0;
                grandTotal += price;

                var imeiDisplay = item.imei ? '<code style="font-size:11px;background:#f1f5f9;padding:2px 4px;border-radius:3px;">' + $('<div>').text(item.imei).html() + '</code>' : '<span style="color:#94a3b8;">—</span>';
                var colorCarrier = (item.carrier || '—') + ' / ' + (item.color || '—');
                var condition = (item.screen || '—') + ' / ' + (item.body || '—') + (item.battery ? ' (' + item.battery + '%)' : '');

                var rowHtml = '<tr class="inv-item-row" data-id="' + item.order_id + '">' +
                    '<td style="text-align:center; font-weight:700; color:#64748b;">' + (idx + 1) + '</td>' +
                    '<td>' +
                        '<input type="hidden" name="order_ids[]" value="' + item.order_id + '">' +
                        '<strong style="color:#0f172a; font-size:12.5px;">' + $('<div>').text(item.order_code).html() + '</strong>' +
                    '</td>' +
                    '<td>' +
                        '<strong>' + $('<div>').text(item.model || '—').html() + '</strong> ' +
                        '<span style="color:#64748b;">' + $('<div>').text(item.storage || '').html() + '</span>' +
                    '</td>' +
                    '<td style="font-size:11.5px;">' +
                        '<div>' + $('<div>').text(colorCarrier).html() + '</div>' +
                        '<div>' + imeiDisplay + '</div>' +
                    '</td>' +
                    '<td style="font-size:11.5px; color:#475569;">' + $('<div>').text(condition).html() + '</td>' +
                    '<td style="text-align:right;">' +
                        '<div style="display:inline-flex; align-items:center; gap:2px;">' +
                            '<span>¥</span>' +
                            '<input type="number" name="item_prices[' + item.order_id + ']" class="inv-item-price-input" value="' + price + '" style="width:100px; height:30px; text-align:right; font-weight:800; color:#dc2626; border-radius:4px; border:1px solid #cbd5e1;">' +
                        '</div>' +
                    '</td>' +
                    '<td style="text-align:center;">' +
                        '<button type="button" class="button button-small inv-remove-btn" data-id="' + item.order_id + '" style="color:#ef4444; border-color:#fee2e2;" title="<?php echo esc_attr( kaitori_t( 'Remove', '削除' ) ); ?>">✕</button>' +
                    '</td>' +
                '</tr>';

                $tbody.append(rowHtml);
            });

            $('#inv_total_qty').text(totalQty);
            $('#inv_grand_total').text(grandTotal.toLocaleString());
        }

        function addOrder(orderData) {
            // Check if already in list
            var exists = false;
            $.each(selectedOrders, function(i, o){
                if (parseInt(o.order_id, 10) === parseInt(orderData.order_id, 10)) {
                    exists = true;
                    return false;
                }
            });
            if (exists) {
                return false;
            }

            selectedOrders.push(orderData);

            // If first customer info is empty, populate from this order
            if (!$('#cust_name').val() && orderData.name) {
                $('#cust_name').val(orderData.name);
            }
            if (!$('#cust_kana').val() && orderData.kana) {
                $('#cust_kana').val(orderData.kana);
            }
            if (!$('#cust_phone').val() && orderData.phone) {
                $('#cust_phone').val(orderData.phone);
            }
            if (!$('#cust_email').val() && orderData.email) {
                $('#cust_email').val(orderData.email);
            }
            if (orderData.payment) {
                $('#inv_payment_method').val(orderData.payment).trigger('change');
            }
            if (!$('#cust_bank_name').val() && orderData.bank_name) {
                $('#cust_bank_name').val(orderData.bank_name);
            }
            if (!$('#cust_bank_branch').val() && orderData.bank_branch) {
                $('#cust_bank_branch').val(orderData.bank_branch);
            }
            if (orderData.acc_type) {
                $('#cust_bank_acc_type').val(orderData.acc_type);
            }
            if (!$('#cust_bank_acc_number').val() && orderData.acc_number) {
                $('#cust_bank_acc_number').val(orderData.acc_number);
            }
            if (!$('#cust_bank_holder').val() && orderData.acc_holder) {
                $('#cust_bank_holder').val(orderData.acc_holder);
            }

            renderRows();
            return true;
        }

        // Remove item
        $(document).on('click', '.inv-remove-btn', function(){
            var id = parseInt($(this).data('id'), 10);
            selectedOrders = $.grep(selectedOrders, function(o){
                return parseInt(o.order_id, 10) !== id;
            });
            renderRows();
        });

        // Price input change
        $(document).on('input change', '.inv-item-price-input', function(){
            var id = parseInt($(this).closest('tr').data('id'), 10);
            var newPrice = parseInt($(this).val(), 10) || 0;
            $.each(selectedOrders, function(i, o){
                if (parseInt(o.order_id, 10) === id) {
                    o.price = newPrice;
                    return false;
                }
            });
            // recalculate grand total
            var grand = 0;
            $('.inv-item-price-input').each(function(){
                grand += (parseInt($(this).val(), 10) || 0);
            });
            $('#inv_grand_total').text(grand.toLocaleString());
        });

        // Payment method toggle
        $('#inv_payment_method').on('change', function(){
            if ($(this).val() === 'cash') {
                $('#inv_bank_container').slideUp(150);
            } else {
                $('#inv_bank_container').slideDown(150);
            }
        });

        // Clear all
        $('#inv_clear_all_btn').on('click', function(){
            if (confirm('<?php echo esc_js( kaitori_t( 'Clear all items from this invoice?', 'すべての明細をクリアしますか？' ) ); ?>')) {
                selectedOrders = [];
                renderRows();
            }
        });

        // Live Search Handler
        var searchTimer = null;
        $('#inv_search_input').on('keyup input', function(){
            clearTimeout(searchTimer);
            var q = $.trim($(this).val());
            if (q.length < 2) {
                $('#inv_search_dropdown').hide().empty();
                $('#inv_search_spinner').removeClass('is-active');
                return;
            }

            $('#inv_search_spinner').addClass('is-active');
            searchTimer = setTimeout(function(){
                $.ajax({
                    url: ajaxurl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'kaitori_search_invoice_orders',
                        nonce: '<?php echo esc_js( $ajax_nonce ); ?>',
                        q: q
                    },
                    success: function(res) {
                        $('#inv_search_spinner').removeClass('is-active');
                        var $dropdown = $('#inv_search_dropdown');
                        $dropdown.empty();

                        if (res.success && res.data.orders && res.data.orders.length > 0) {
                            var listHtml = '<div style="padding:6px 0;">';
                            $.each(res.data.orders, function(i, ord){
                                listHtml += '<div class="inv-search-item" data-order=\'' + JSON.stringify(ord).replace(/'/g, "&apos;") + '\' style="padding:8px 12px; border-bottom:1px solid #f1f5f9; cursor:pointer; display:flex; justify-content:space-between; align-items:center;">' +
                                    '<div>' +
                                        '<strong style="color:#0f172a;">' + $('<div>').text(ord.order_code).html() + '</strong> ' +
                                        '<span style="font-size:12px; color:#475569;">— ' + $('<div>').text(ord.name || 'No Name').html() + ' (' + $('<div>').text(ord.phone || '').html() + ')</span>' +
                                        '<div style="font-size:11.5px; color:#64748b;">' + $('<div>').text(ord.model + ' ' + (ord.storage || '') + ' | ' + (ord.carrier || '')).html() + '</div>' +
                                    '</div>' +
                                    '<div style="text-align:right;">' +
                                        '<span style="font-weight:800; color:#dc2626; display:block;">¥' + (parseInt(ord.price, 10) || 0).toLocaleString() + '</span>' +
                                        '<button type="button" class="button button-small button-primary" style="font-size:11px; padding:0 8px; height:24px;">+ <?php echo esc_js( kaitori_t( 'Add', '追加' ) ); ?></button>' +
                                    '</div>' +
                                '</div>';
                            });
                            listHtml += '</div>';
                            $dropdown.html(listHtml).show();
                        } else {
                            $dropdown.html('<div style="padding:14px; text-align:center; color:#94a3b8; font-size:12.5px;"><?php echo esc_js( kaitori_t( 'No matching orders found.', '該当する注文が見つかりませんでした。' ) ); ?></div>').show();
                        }
                    },
                    error: function() {
                        $('#inv_search_spinner').removeClass('is-active');
                    }
                });
            }, 300);
        });

        // Click search item to add
        $(document).on('click', '.inv-search-item', function(){
            var orderData = $(this).data('order');
            if (typeof orderData === 'string') {
                orderData = JSON.parse(orderData);
            }
            if (orderData) {
                addOrder(orderData);
                $('#inv_search_dropdown').hide().empty();
                $('#inv_search_input').val('');
            }
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e){
            if (!$(e.target).closest('#inv_search_input, #inv_search_dropdown').length) {
                $('#inv_search_dropdown').hide();
            }
        });

        // Bulk input handler
        $('#inv_bulk_btn').on('click', function(){
            var raw = $.trim($('#inv_bulk_input').val());
            if (!raw) return;

            $('#inv_search_spinner').addClass('is-active');
            $.ajax({
                url: ajaxurl,
                type: 'GET',
                dataType: 'json',
                data: {
                    action: 'kaitori_search_invoice_orders',
                    nonce: '<?php echo esc_js( $ajax_nonce ); ?>',
                    q: raw
                },
                success: function(res) {
                    $('#inv_search_spinner').removeClass('is-active');
                    if (res.success && res.data.orders && res.data.orders.length > 0) {
                        $.each(res.data.orders, function(i, ord){
                            addOrder(ord);
                        });
                        $('#inv_bulk_input').val('');
                    } else {
                        alert('<?php echo esc_js( kaitori_t( 'No orders found matching the entered codes.', '入力されたコードに一致する注文が見つかりませんでした。' ) ); ?>');
                    }
                },
                error: function() {
                    $('#inv_search_spinner').removeClass('is-active');
                }
            });
        });

        // Load preloaded items
        if (preloaded && preloaded.length > 0) {
            $.each(preloaded, function(i, ord){
                addOrder(ord);
            });
        } else {
            renderRows();
        }

        // Form submit validation
        $('#kaitori_combined_invoice_form').on('submit', function(e){
            if (selectedOrders.length === 0) {
                e.preventDefault();
                alert('<?php echo esc_js( kaitori_t( 'Please add at least one buyback order.', '1件以上の買取注文を追加してください。' ) ); ?>');
                return false;
            }
            if (!$.trim($('#cust_name').val())) {
                e.preventDefault();
                alert('<?php echo esc_js( kaitori_t( 'Please enter customer name.', 'お客様名を入力してください。' ) ); ?>');
                $('#cust_name').focus();
                return false;
            }
            return true;
        });

    })(jQuery);
    </script>
    <?php
}

/**
 * Render one copy of the COMBINED Buyback Invoice (Shop Copy or Customer Copy).
 *
 * @param array  $inv        Consolidated invoice dataset.
 * @param string $copy_label 'Shop Copy' or 'Customer Copy'.
 * @param string $copy_type  'shop' | 'customer'.
 * @param string $logo_url
 * @return string HTML
 */
function kaitoriakiba_combined_invoice_copy_html( $inv, $copy_label, $copy_type, $logo_url ) {
    $store = function_exists( 'kaitoriakiba_store_info' ) ? kaitoriakiba_store_info() : array();
    $yen   = function ( $n ) { return '¥' . number_format( (int) $n ); };
    $e     = 'esc_html';

    $pay_label = ( $inv['payment']['method'] === 'bank' )
        ? kaitori_t( 'Bank transfer', '銀行振込' )
        : kaitori_t( 'Cash on the spot', '店頭現金' );

    $has_bank = ( $inv['payment']['method'] === 'bank' && ( ! empty( $inv['payment']['bank_name'] ) || ! empty( $inv['payment']['acc_number'] ) ) );

    ob_start();
    ?>
    <div class="inv-copy inv-copy-<?php echo esc_attr( $copy_type ); ?>">

        <!-- Brand header band -->
        <div class="inv-band">
            <div class="inv-band-left">
                <?php if ( $logo_url ) : ?><img class="inv-logo" src="<?php echo esc_url( $logo_url ); ?>" alt=""><?php endif; ?>
                <div class="inv-shop">
                    <div class="inv-shop-name"><?php echo $e( $store['name'] ?? 'Kaitori Akiba' ); ?></div>
                    <?php if ( ! empty( $store['address'] ) ) : ?><div class="inv-shop-line"><?php echo $e( $store['address'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $store['phone'] ) ) : ?><div class="inv-shop-line">TEL: <?php echo $e( $store['phone'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $store['license'] ) ) : ?><div class="inv-license"><?php echo $e( $store['license'] ); ?></div><?php endif; ?>
                </div>
            </div>
            <div class="inv-band-right">
                <div class="inv-doc-title"><?php echo $e( kaitori_t( 'Buyback Invoice', '買取明細書（一括）' ) ); ?></div>
                <div class="inv-doc-sub">COMBINED BUYBACK INVOICE</div>
            </div>
        </div>

        <!-- Copy tag + meta ribbon -->
        <div class="inv-ribbon">
            <span class="inv-copy-pill"><?php echo $e( $copy_label ); ?></span>
            <div class="inv-ribbon-meta">
                <span class="inv-chip"><em><?php echo $e( kaitori_t( 'Invoice No.', '伝票番号' ) ); ?></em> <b><?php echo $e( $inv['invoice_no'] ); ?></b></span>
                <span class="inv-chip"><em><?php echo $e( kaitori_t( 'Date', '発行日' ) ); ?></em> <b><?php echo $e( $inv['date'] ); ?></b></span>
                <span class="inv-chip"><em><?php echo $e( kaitori_t( 'Quantity', '買取台数' ) ); ?></em> <b style="color:#ef4444;"><?php echo (int) $inv['total_qty']; ?> <?php echo $e( kaitori_t( 'devices', '台' ) ); ?></b></span>
            </div>
        </div>

        <!-- Customer info bar -->
        <div class="inv-customer-bar" style="margin-top:10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <div style="font-size:12px;">
                <span style="color:#64748b; font-size:11px;"><?php echo $e( kaitori_t( 'Customer', 'お客様' ) ); ?>:</span>
                <strong style="font-size:14px; margin-left:4px;"><?php echo $e( $inv['customer']['name'] ); ?></strong>
                <?php if ( ! empty( $inv['customer']['kana'] ) ) : ?>
                    <span style="font-size:11px; color:#64748b;">（<?php echo $e( $inv['customer']['kana'] ); ?>）</span>
                <?php endif; ?>
                <span style="font-size:13px; font-weight:700; margin-left:4px;"><?php echo $e( kaitori_t( 'Esq.', '様' ) ); ?></span>
            </div>
            <div style="font-size:11px; color:#475569; display:flex; gap:12px; flex-wrap:wrap;">
                <?php if ( ! empty( $inv['customer']['phone'] ) ) : ?>
                    <span>TEL: <b><?php echo $e( $inv['customer']['phone'] ); ?></b></span>
                <?php endif; ?>
                <?php if ( ! empty( $inv['customer']['email'] ) ) : ?>
                    <span>MAIL: <b><?php echo $e( $inv['customer']['email'] ); ?></b></span>
                <?php endif; ?>
                <?php if ( ! empty( $inv['customer']['address'] ) ) : ?>
                    <span>ADDR: <b><?php echo $e( $inv['customer']['address'] ); ?></b></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Devices itemized table -->
        <div style="margin-top:10px;">
            <table class="inv-items-table" style="width:100%; border-collapse:collapse; font-size:10.5px; border:1px solid #cbd5e1;">
                <thead>
                    <tr style="background:#0f172a; color:#fff;">
                        <th style="padding:6px 6px; width:26px; text-align:center;">#</th>
                        <th style="padding:6px 8px; width:100px; text-align:left;"><?php echo $e( kaitori_t( 'App ID', '受付番号' ) ); ?></th>
                        <th style="padding:6px 8px; text-align:left;"><?php echo $e( kaitori_t( 'Device Model & Storage', '品名・機種・容量' ) ); ?></th>
                        <th style="padding:6px 8px; width:120px; text-align:left;"><?php echo $e( kaitori_t( 'Carrier / Color', 'キャリア / カラー' ) ); ?></th>
                        <th style="padding:6px 8px; width:130px; text-align:left;">IMEI / Serial</th>
                        <th style="padding:6px 8px; width:95px; text-align:left;"><?php echo $e( kaitori_t( 'Condition', '状態' ) ); ?></th>
                        <th style="padding:6px 8px; width:95px; text-align:right;"><?php echo $e( kaitori_t( 'Price (¥)', '買取単価' ) ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $inv['items'] as $idx => $it ) : ?>
                        <tr style="background:<?php echo ( $idx % 2 === 0 ) ? '#ffffff' : '#f8fafc'; ?>; border-top:1px solid #e2e8f0;">
                            <td style="padding:5px 6px; text-align:center; color:#64748b;"><?php echo (int) ( $idx + 1 ); ?></td>
                            <td style="padding:5px 8px; font-weight:700; color:#0f172a;"><?php echo $e( $it['order_code'] ); ?></td>
                            <td style="padding:5px 8px;">
                                <strong><?php echo $e( $it['model'] ?: '—' ); ?></strong>
                                <?php if ( ! empty( $it['storage'] ) ) : ?>
                                    <span style="color:#64748b;"><?php echo $e( $it['storage'] ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:5px 8px; color:#334155;">
                                <?php echo $e( trim( ( $it['carrier'] ?: '—' ) . ' / ' . ( $it['color'] ?: '—' ) ) ); ?>
                            </td>
                            <td style="padding:5px 8px; font-family:monospace; font-size:10px; color:#0f172a;">
                                <?php echo $e( $it['imei'] ?: '—' ); ?>
                            </td>
                            <td style="padding:5px 8px; font-size:10px; color:#475569;">
                                <?php echo $e( trim( ( $it['screen'] ?: '—' ) . '/' . ( $it['body'] ?: '—' ) ) ); ?>
                                <?php echo ! empty( $it['battery'] ) ? $e( ' (' . $it['battery'] . '%)' ) : ''; ?>
                            </td>
                            <td style="padding:5px 8px; text-align:right; font-weight:800; color:#dc2626;">
                                <?php echo $e( $yen( $it['price'] ) ); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Total panel -->
        <div class="inv-total" style="margin-top:10px; padding:10px 16px;">
            <div class="inv-total-left">
                <span class="inv-total-label"><?php echo $e( kaitori_t( 'Grand Buyback Total', '買取合計金額' ) ); ?></span>
                <span class="inv-total-pay">
                    <?php echo $e( kaitori_t( 'Total Devices', '合計台数' ) . ': ' . (int) $inv['total_qty'] . ' ' . kaitori_t( 'devices', '台' ) ); ?> | 
                    <?php echo $e( kaitori_t( 'Payment', 'お支払' ) . ': ' . $pay_label ); ?>
                </span>
            </div>
            <div class="inv-total-amount" style="font-size:26px;">
                <?php echo $e( $yen( $inv['total_price'] ) ); ?>
                <span class="inv-total-tax"><?php echo $e( kaitori_t( '(tax incl.)', '（税込）' ) ); ?></span>
            </div>
        </div>

        <!-- Bank details -->
        <?php if ( $has_bank ) : ?>
            <div class="inv-pay" style="margin-top:6px; font-size:10.5px; padding:5px 10px;">
                <b><?php echo $e( kaitori_t( 'Transfer to', '振込先' ) ); ?>:</b>
                <?php echo $e( trim( ( $inv['payment']['bank_name'] ?? '' ) . ' ' . ( $inv['payment']['bank_branch'] ?? '' ) . ' ' . ( $inv['payment']['acc_type'] ?? '' ) ) ); ?>
                <?php echo ! empty( $inv['payment']['acc_number'] ) ? $e( ' ' . $inv['payment']['acc_number'] ) : ''; ?>
                <?php echo ! empty( $inv['payment']['acc_holder'] ) ? $e( ' / 口座名義: ' . $inv['payment']['acc_holder'] ) : ''; ?>
            </div>
        <?php endif; ?>

        <!-- Memo -->
        <?php if ( ! empty( $inv['memo'] ) ) : ?>
            <div class="inv-memo" style="margin-top:6px; font-size:10.5px;">
                <b><?php echo $e( kaitori_t( 'Note', '備考' ) ); ?>:</b> <?php echo $e( $inv['memo'] ); ?>
            </div>
        <?php endif; ?>

        <!-- Legal notice -->
        <div class="inv-legal" style="margin-top:8px; font-size:8.5px; line-height:1.5;">
            <?php echo $e( kaitori_t(
                'Identity verified under the Antique Dealer Act. By signing, the seller certifies legal ownership of all listed devices and that none are stolen or under a fraudulent contract.',
                '古物営業法に基づき本人確認を実施済み。ご署名をもって、売却者は上記すべての端末の正当な所有権を有し、盗品・不正契約品でないことを証明します。'
            ) ); ?>
        </div>

        <!-- Signature boxes -->
        <div class="inv-sign" style="margin-top:10px; gap:12px;">
            <div class="inv-sign-box" style="height:44px;">
                <span><?php echo $e( kaitori_t( 'Seller signature', 'お客様ご署名' ) ); ?></span>
            </div>
            <div class="inv-sign-box" style="height:44px;">
                <span><?php echo $e( kaitori_t( 'Staff in charge', '担当者印' ) ); ?></span>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * admin-post handler: output the full printable COMBINED invoice page and exit.
 */
function kaitoriakiba_print_combined_invoice() {
    if ( ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        wp_die( esc_html( kaitori_t( 'You do not have permission to view this invoice.', 'この伝票を表示する権限がありません。' ) ) );
    }

    check_admin_referer( 'kaitori_print_combined_invoice' );

    $raw_order_ids = isset( $_POST['order_ids'] ) && is_array( $_POST['order_ids'] ) ? array_map( 'intval', $_POST['order_ids'] ) : array();
    $raw_order_ids = array_filter( array_unique( $raw_order_ids ) );

    if ( empty( $raw_order_ids ) ) {
        wp_die( esc_html( kaitori_t( 'No buyback orders selected.', '買取注文が選択されていません。' ) ) );
    }

    $custom_prices = isset( $_POST['item_prices'] ) && is_array( $_POST['item_prices'] ) ? $_POST['item_prices'] : array();

    $items       = array();
    $total_price = 0;
    $order_codes = array();

    foreach ( $raw_order_ids as $oid ) {
        $d = kaitoriakiba_invoice_data( $oid );
        if ( ! $d ) continue;

        // Custom price override from form if specified
        if ( isset( $custom_prices[ $oid ] ) && is_numeric( $custom_prices[ $oid ] ) ) {
            $d['price'] = (int) $custom_prices[ $oid ];
        }

        $items[]       = $d;
        $total_price  += $d['price'];
        $order_codes[] = $d['order_code'];
    }

    if ( empty( $items ) ) {
        wp_die( esc_html( kaitori_t( 'Valid orders not found.', '有効な注文が見つかりませんでした。' ) ) );
    }

    // Consolidated customer & invoice data
    $customer_name    = isset( $_POST['customer_name'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_name'] ) ) : ( $items[0]['name'] ?? '' );
    $customer_kana    = isset( $_POST['customer_kana'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_kana'] ) ) : ( $items[0]['kana'] ?? '' );
    $customer_phone   = isset( $_POST['customer_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_phone'] ) ) : ( $items[0]['phone'] ?? '' );
    $customer_email   = isset( $_POST['customer_email'] ) ? sanitize_email( wp_unslash( $_POST['customer_email'] ) ) : ( $items[0]['email'] ?? '' );
    $customer_address = isset( $_POST['customer_address'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_address'] ) ) : '';

    $payment_method = isset( $_POST['payment_method'] ) && 'cash' === $_POST['payment_method'] ? 'cash' : 'bank';
    $bank_name      = isset( $_POST['bank_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_name'] ) ) : ( $items[0]['bank_name'] ?? '' );
    $bank_branch    = isset( $_POST['bank_branch'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_branch'] ) ) : ( $items[0]['bank_branch'] ?? '' );
    $acc_type       = isset( $_POST['bank_account_type'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_account_type'] ) ) : ( $items[0]['acc_type'] ?? '普通' );
    $acc_number     = isset( $_POST['bank_account_number'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_account_number'] ) ) : ( $items[0]['acc_number'] ?? '' );
    $acc_holder     = isset( $_POST['bank_holder_kana'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_holder_kana'] ) ) : ( $items[0]['acc_holder'] ?? '' );

    $invoice_date = isset( $_POST['invoice_date'] ) ? sanitize_text_field( wp_unslash( $_POST['invoice_date'] ) ) : current_time( 'Y-m-d' );
    $invoice_memo = isset( $_POST['invoice_memo'] ) ? sanitize_text_field( wp_unslash( $_POST['invoice_memo'] ) ) : '';

    // Generate combined invoice number: e.g. INV-20261113-1234
    $invoice_no = 'INV-' . str_replace( '-', '', $invoice_date ) . '-' . substr( md5( implode( ',', $raw_order_ids ) ), 0, 4 );

    $inv_data = array(
        'invoice_no'  => $invoice_no,
        'date'        => $invoice_date,
        'order_codes' => $order_codes,
        'total_qty'   => count( $items ),
        'total_price' => $total_price,
        'customer'    => array(
            'name'    => $customer_name,
            'kana'    => $customer_kana,
            'phone'   => $customer_phone,
            'email'   => $customer_email,
            'address' => $customer_address,
        ),
        'payment'     => array(
            'method'      => $payment_method,
            'bank_name'   => $bank_name,
            'bank_branch' => $bank_branch,
            'acc_type'    => $acc_type,
            'acc_number'  => $acc_number,
            'acc_holder'  => $acc_holder,
        ),
        'memo'        => $invoice_memo,
        'items'       => $items,
    );

    $logo_url  = apply_filters( 'kaitori_invoice_watermark_url', get_template_directory_uri() . '/assets/images/akiba-logo.png' );
    $shop_copy = kaitoriakiba_combined_invoice_copy_html( $inv_data, kaitori_t( 'Shop Copy', '店舗控え' ), 'shop', $logo_url );
    $cust_copy = kaitoriakiba_combined_invoice_copy_html( $inv_data, kaitori_t( 'Customer Copy', 'お客様控え' ), 'customer', $logo_url );
    $print_lbl = kaitori_t( 'Print / Save as PDF', '印刷 / PDF保存' );
    $close_lbl = kaitori_t( 'Close', '閉じる' );
    ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc_html( kaitori_t( 'Combined Buyback Invoice', '一括買取明細書' ) . ' ' . $invoice_no ); ?></title>
    <style>
        :root { --inv-red:#ef4444; --inv-red-d:#dc2626; --inv-ink:#0f172a; --inv-line:#e2e8f0; }
        * { box-sizing: border-box; }
        body {
            margin: 0; background: #eef2f6; color: var(--inv-ink);
            font-family: "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, "Yu Gothic", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 12px; line-height: 1.5;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .inv-toolbar { position: sticky; top: 0; z-index: 10; background: var(--inv-ink); padding: 12px 16px; display: flex; gap: 10px; justify-content: center; }
        .inv-toolbar button { border: none; border-radius: 8px; padding: 10px 24px; font-size: 14px; font-weight: 800; cursor: pointer; }
        .inv-btn-print { background: var(--inv-red); color: #fff; }
        .inv-btn-close { background: #334155; color: #fff; }
        .inv-sheet { max-width: 195mm; margin: 16px auto; background: #fff; padding: 10mm 10mm; box-shadow: 0 6px 24px rgba(0,0,0,.12); }
        .inv-cut { border: none; border-top: 1.5px dashed #94a3b8; margin: 8mm 0; text-align: center; }
        .inv-cut span { position: relative; top: -9px; background: #fff; padding: 0 10px; color: #94a3b8; font-size: 10px; letter-spacing: 2px; }

        .inv-copy { position: relative; z-index: 0; overflow: hidden; padding: 2mm 0; }
        .inv-copy::before {
            content: ""; position: absolute; top: 54%; left: 50%;
            width: 110mm; height: 110mm; transform: translate(-50%, -50%);
            background: url('<?php echo esc_url( $logo_url ); ?>') center / contain no-repeat;
            opacity: .05; z-index: -1; pointer-events: none;
        }

        .inv-band { display: flex; justify-content: space-between; align-items: center; gap: 14px; padding-bottom: 8px; border-bottom: 3px solid var(--inv-red); }
        .inv-band-left { display: flex; align-items: center; gap: 12px; }
        .inv-logo { width: 44px; height: 44px; object-fit: contain; flex: none; }
        .inv-shop-name { font-size: 16px; font-weight: 900; letter-spacing: .3px; }
        .inv-shop-line { font-size: 10px; color: #475569; }
        .inv-license { font-size: 10px; color: var(--inv-red-d); font-weight: 700; margin-top: 2px; }
        .inv-band-right { text-align: right; }
        .inv-doc-title { font-size: 20px; font-weight: 900; letter-spacing: 3px; color: var(--inv-ink); line-height: 1.1; }
        .inv-doc-sub { font-size: 9px; letter-spacing: 2px; color: #94a3b8; font-weight: 700; }

        .inv-ribbon { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 8px; flex-wrap: wrap; }
        .inv-copy-pill { font-weight: 800; font-size: 11px; color: #fff; padding: 3px 12px; border-radius: 999px; letter-spacing: 1px; }
        .inv-copy-shop .inv-copy-pill { background: #1d4ed8; }
        .inv-copy-customer .inv-copy-pill { background: #15803d; }
        .inv-ribbon-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .inv-chip { font-size: 11px; color: #334155; background: #f1f5f9; border: 1px solid var(--inv-line); border-radius: 6px; padding: 2px 8px; }
        .inv-chip em { font-style: normal; color: #94a3b8; margin-right: 4px; }
        .inv-chip b { color: var(--inv-ink); }

        .inv-items-table th, .inv-items-table td { border: 1px solid #cbd5e1; }

        .inv-total { display: flex; justify-content: space-between; align-items: center; border-radius: 10px;
            background: linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border: 2px solid #fdba74; }
        .inv-total-label { display: block; font-size: 13px; font-weight: 800; color: #9a3412; }
        .inv-total-pay { display: block; font-size: 10px; color: #b45309; margin-top: 2px; }
        .inv-total-amount { font-weight: 900; color: var(--inv-red-d); white-space: nowrap; }
        .inv-total-tax { font-size: 10.5px; font-weight: 700; color: #b45309; margin-left: 4px; }

        .inv-pay { color: #334155; background: #f8fafc; border: 1px solid var(--inv-line); border-radius: 6px; }
        .inv-memo { color: #334155; }
        .inv-legal { color: #64748b; border-left: 3px solid var(--inv-line); padding: 2px 0 2px 8px; }
        .inv-sign { display: flex; }
        .inv-sign-box { flex: 1; border: 1px solid #94a3b8; border-radius: 6px; position: relative; }
        .inv-sign-box span { position: absolute; top: 3px; left: 6px; font-size: 9.5px; color: #64748b; }

        @media print {
            body { background: #fff; }
            .inv-toolbar { display: none; }
            .inv-sheet { box-shadow: none; margin: 0; max-width: none; padding: 4mm; }
            .inv-copy { page-break-inside: avoid; }
            .inv-cut { page-break-inside: avoid; }
        }
        @page { size: A4; margin: 6mm; }
    </style>
</head>
<body>
    <div class="inv-toolbar">
        <button type="button" class="inv-btn-print" onclick="window.print();"><?php echo esc_html( $print_lbl ); ?></button>
        <button type="button" class="inv-btn-close" onclick="window.close();"><?php echo esc_html( $close_lbl ); ?></button>
    </div>
    <div class="inv-sheet">
        <?php echo $shop_copy; // phpcs:ignore WordPress.Security.EscapeOutput ?>
        <div class="inv-cut"><span><?php echo esc_html( kaitori_t( '— cut here —', '— キリトリ線 —' ) ); ?></span></div>
        <?php echo $cust_copy; // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </div>
</body>
</html>
    <?php
    exit;
}
add_action( 'admin_post_kaitori_print_combined_invoice', 'kaitoriakiba_print_combined_invoice' );
