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
