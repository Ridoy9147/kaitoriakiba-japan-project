<?php
/**
 * Kaitori Akiba — Walk-in Order Creation (店頭買取登録).
 *
 * Lets staff create a buyback_order by hand for a customer who sells in-store, then
 * immediately open the printable invoice (shop + customer copy / PDF).
 *
 * Reuses the same meta keys as the public application so walk-in orders show up in
 * the Orders list and Customers screen exactly like online ones. Bank details are
 * encrypted at rest (inc/crypto.php). Admin-only + nonce protected.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Register the submenu under Buyback Orders. */
function kaitoriakiba_new_order_menu() {
    add_submenu_page(
        'edit.php?post_type=buyback_order',
        kaitori_t( 'New Walk-in Buyback', '店頭買取 新規登録' ),
        kaitori_t( 'New Walk-in', '店頭買取登録' ),
        kaitoriakiba_manage_cap(),
        'kaitori-new-order',
        'kaitoriakiba_new_order_page'
    );
}
add_action( 'admin_menu', 'kaitoriakiba_new_order_menu' );

/**
 * Create a walk-in buyback_order from the submitted form.
 *
 * @return int|WP_Error Order id on success.
 */
function kaitoriakiba_create_walkin_order() {
    $g  = function ( $k ) { return isset( $_POST[ $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) : ''; };

    $model   = $g( 'device_model' );
    $storage = $g( 'device_storage' );
    $price   = isset( $_POST['final_price'] ) ? (int) preg_replace( '/[^\d]/', '', (string) $_POST['final_price'] ) : 0;

    if ( '' === $model ) {
        return new WP_Error( 'model', kaitori_t( 'Please enter the device model.', '機種名を入力してください。' ) );
    }
    if ( $price <= 0 ) {
        return new WP_Error( 'price', kaitori_t( 'Please enter the agreed buyback amount.', '買取金額を入力してください。' ) );
    }

    $title   = 'KB-' . date( 'Ymd' ) . '-' . wp_rand( 1000, 9999 ) . ' [' . $model . ' ' . $storage . ']';
    $order_id = wp_insert_post( array(
        'post_title'  => $title,
        'post_status' => 'publish',
        'post_type'   => 'buyback_order',
    ) );
    if ( ! $order_id || is_wp_error( $order_id ) ) {
        return new WP_Error( 'insert', kaitori_t( 'Could not create the order.', '注文の作成に失敗しました。' ) );
    }

    $payment = ( $g( 'payment_method' ) === 'bank' ) ? 'bank' : 'cash';
    $email   = sanitize_email( wp_unslash( $_POST['applicant_email'] ?? '' ) );

    $meta = array(
        '_kaitori_walkin'        => 1,
        '_order_status'          => in_array( $g( 'order_status' ), array_keys( kaitoriakiba_order_statuses() ), true ) ? $g( 'order_status' ) : 'paid',
        '_applicant_name'        => $g( 'applicant_name' ),
        '_applicant_kana'        => $g( 'applicant_kana' ),
        '_applicant_email'       => $email,
        '_applicant_phone'       => $g( 'applicant_phone' ),
        '_device_model'          => $model,
        '_device_storage'        => $storage,
        '_device_color'          => $g( 'device_color' ),
        '_device_carrier'        => $g( 'device_carrier' ),
        '_device_imei'           => preg_replace( '/[^0-9A-Za-z]/', '', (string) ( $_POST['device_imei'] ?? '' ) ),
        '_device_serial'         => $g( 'device_serial' ),
        '_screen_condition'      => $g( 'screen_condition' ),
        '_body_condition'        => $g( 'body_condition' ),
        '_battery_health'        => (int) ( $_POST['battery_health'] ?? 0 ),
        '_delivery_method'       => 'store',
        '_payment_method'        => $payment,
        '_final_buyback_price'   => $price,
        '_estimated_price'       => $price,
        '_admin_memo'            => isset( $_POST['admin_memo'] ) ? sanitize_textarea_field( wp_unslash( $_POST['admin_memo'] ) ) : '',
    );

    if ( 'bank' === $payment ) {
        $meta['_bank_name']         = $g( 'bank_name' );
        $meta['_bank_branch']       = $g( 'bank_branch' );
        $meta['_bank_account_type'] = $g( 'bank_account_type' ) ?: '普通';
        $acc  = $g( 'bank_account_number' );
        $hold = $g( 'bank_holder_kana' );
        $meta['_bank_account_number'] = function_exists( 'kaitoriakiba_encrypt' ) ? kaitoriakiba_encrypt( $acc ) : $acc;
        $meta['_bank_holder_kana']    = function_exists( 'kaitoriakiba_encrypt' ) ? kaitoriakiba_encrypt( $hold ) : $hold;
    }

    foreach ( $meta as $k => $v ) {
        update_post_meta( $order_id, $k, $v );
    }

    // Link to a registered customer account by email, if any.
    if ( $email ) {
        $u = get_user_by( 'email', $email );
        if ( $u ) {
            update_post_meta( $order_id, '_customer_user_id', $u->ID );
        }
    }

    return $order_id;
}

/** Render the walk-in order form + handle submission. */
function kaitoriakiba_new_order_page() {
    if ( ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        wp_die( esc_html( kaitori_t( 'You do not have permission to access this page.', 'このページにアクセスする権限がありません。' ) ) );
    }

    $created_id = 0;
    $error_msg  = '';

    if ( isset( $_POST['kaitori_walkin_submit'] ) && check_admin_referer( 'kaitori_walkin_order' ) ) {
        $result = kaitoriakiba_create_walkin_order();
        if ( is_wp_error( $result ) ) {
            $error_msg = $result->get_error_message();
        } else {
            $created_id = (int) $result;
        }
    }

    $statuses = kaitoriakiba_order_statuses();
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-store" style="font-size:28px;vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'New Walk-in Buyback', '店頭買取 新規登録' ) ); ?></h1>

        <?php if ( $created_id ) :
            $view_url  = kaitoriakiba_invoice_url( $created_id, false );
            $print_url = kaitoriakiba_invoice_url( $created_id, true );
            $edit_url  = admin_url( 'post.php?post=' . $created_id . '&action=edit' );
        ?>
            <div class="notice notice-success" style="padding:16px;border-radius:8px;margin-top:16px;">
                <h2 style="margin:0 0 6px;"><?php echo esc_html( kaitori_t( 'Order created!', '買取注文を作成しました！' ) ); ?></h2>
                <p style="margin:0 0 12px;"><?php echo esc_html( kaitori_t( 'Invoice No.', '伝票番号' ) ); ?>: <strong><?php echo esc_html( get_the_title( $created_id ) ); ?></strong></p>
                <a href="<?php echo esc_url( $print_url ); ?>" target="_blank" class="button button-primary button-hero" style="background:#ef4444;border-color:#ef4444;">
                    <span class="dashicons dashicons-printer" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Open Invoice & Print / Save as PDF', '伝票を開いて印刷 / PDF保存' ) ); ?>
                </a>
                <a href="<?php echo esc_url( $view_url ); ?>" target="_blank" class="button button-hero"><?php echo esc_html( kaitori_t( 'Preview Invoice', '伝票プレビュー' ) ); ?></a>
                <a href="<?php echo esc_url( $edit_url ); ?>" class="button button-hero"><?php echo esc_html( kaitori_t( 'Edit Order', '注文を編集' ) ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=buyback_order&page=kaitori-new-order' ) ); ?>" class="button button-hero"><?php echo esc_html( kaitori_t( 'Register Another', '続けて登録' ) ); ?></a>
            </div>
        <?php else : ?>

            <?php if ( $error_msg ) : ?>
                <div class="notice notice-error"><p><strong><?php echo esc_html( $error_msg ); ?></strong></p></div>
            <?php endif; ?>

            <p style="color:#475569;max-width:760px;"><?php echo esc_html( kaitori_t(
                'Enter the details for a customer selling in-store, then create the order to generate a printable invoice (shop copy + customer copy).',
                '店頭でご来店のお客様の情報を入力し、注文を作成すると印刷用の買取伝票（店舗控え・お客様控え）が発行できます。'
            ) ); ?></p>

            <form method="post" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;max-width:900px;margin-top:14px;">
                <?php wp_nonce_field( 'kaitori_walkin_order' ); ?>

                <h2 style="margin-top:0;border-bottom:2px solid #f1f5f9;padding-bottom:8px;"><?php echo esc_html( kaitori_t( 'Customer', 'お客様情報' ) ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'Full Name', '氏名' ) ); ?></label></th>
                        <td><input type="text" name="applicant_name" class="regular-text" placeholder="山田 太郎"></td>
                        <th><label><?php echo esc_html( kaitori_t( 'Furigana', 'フリガナ' ) ); ?></label></th>
                        <td><input type="text" name="applicant_kana" class="regular-text" placeholder="ヤマダ タロウ"></td>
                    </tr>
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'Phone', '電話番号' ) ); ?></label></th>
                        <td><input type="text" name="applicant_phone" class="regular-text" placeholder="09012345678"></td>
                        <th><label><?php echo esc_html( kaitori_t( 'Email', 'メール' ) ); ?></label></th>
                        <td><input type="email" name="applicant_email" class="regular-text" placeholder="taro@example.com"></td>
                    </tr>
                </table>

                <h2 style="border-bottom:2px solid #f1f5f9;padding-bottom:8px;"><?php echo esc_html( kaitori_t( 'Device', '端末情報' ) ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'Model *', '機種名 *' ) ); ?></label></th>
                        <td><input type="text" name="device_model" class="regular-text" placeholder="iPhone 16 Pro" required></td>
                        <th><label><?php echo esc_html( kaitori_t( 'Storage', '容量' ) ); ?></label></th>
                        <td>
                            <select name="device_storage">
                                <option value="128GB">128GB</option>
                                <option value="256GB" selected>256GB</option>
                                <option value="512GB">512GB</option>
                                <option value="1TB">1TB</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'Color', 'カラー' ) ); ?></label></th>
                        <td><input type="text" name="device_color" class="regular-text" placeholder="<?php echo esc_attr( kaitori_t( 'Natural Titanium', 'ナチュラルチタニウム' ) ); ?>"></td>
                        <th><label><?php echo esc_html( kaitori_t( 'Carrier', 'キャリア' ) ); ?></label></th>
                        <td>
                            <select name="device_carrier">
                                <option value="SIMフリー">SIM FREE (SIMフリー)</option>
                                <option value="docomo">docomo</option>
                                <option value="au">au</option>
                                <option value="SoftBank">SoftBank</option>
                                <option value="RakutenMobile">Rakuten Mobile</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'IMEI', 'IMEI' ) ); ?></label></th>
                        <td>
                            <input type="text" id="walkin_imei" name="device_imei" class="regular-text" maxlength="15" placeholder="35xxxxxxxxxxxxx" style="font-family:monospace;width:70%;">
                            <button type="button" class="button kaitori-scan" data-field="imei" data-target="#walkin_imei" title="<?php echo esc_attr( kaitori_t( 'Scan barcode', 'バーコードをスキャン' ) ); ?>">📷 <?php echo esc_html( kaitori_t( 'Scan', 'スキャン' ) ); ?></button>
                        </td>
                        <th><label><?php echo esc_html( kaitori_t( 'Serial', 'シリアル番号' ) ); ?></label></th>
                        <td>
                            <input type="text" id="walkin_serial" name="device_serial" class="regular-text" style="width:70%;">
                            <button type="button" class="button kaitori-scan" data-field="serial" data-target="#walkin_serial" title="<?php echo esc_attr( kaitori_t( 'Scan barcode', 'バーコードをスキャン' ) ); ?>">📷 <?php echo esc_html( kaitori_t( 'Scan', 'スキャン' ) ); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'Screen condition', '画面状態' ) ); ?></label></th>
                        <td><input type="text" name="screen_condition" class="regular-text" placeholder="<?php echo esc_attr( kaitori_t( 'Mint, no scratches', '傷なし美品' ) ); ?>"></td>
                        <th><label><?php echo esc_html( kaitori_t( 'Body condition', '本体状態' ) ); ?></label></th>
                        <td><input type="text" name="body_condition" class="regular-text" placeholder="<?php echo esc_attr( kaitori_t( 'Good', '良好' ) ); ?>"></td>
                    </tr>
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'Battery %', 'バッテリー %' ) ); ?></label></th>
                        <td><input type="number" name="battery_health" min="0" max="100" value="100" style="width:90px;"> %</td>
                        <th></th><td></td>
                    </tr>
                </table>

                <h2 style="border-bottom:2px solid #f1f5f9;padding-bottom:8px;"><?php echo esc_html( kaitori_t( 'Buyback & Payment', '買取金額・支払' ) ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'Buyback amount (¥) *', '買取金額 (¥) *' ) ); ?></label></th>
                        <td>
                            <input type="number" name="final_price" min="0" step="1" style="width:180px;font-size:16px;font-weight:700;" required>
                            <span id="walkin_est_hint" style="margin-left:10px;color:#64748b;"></span>
                            <button type="button" class="button" id="walkin_est_btn" style="margin-left:6px;"><?php echo esc_html( kaitori_t( 'Suggest price', '参考価格を計算' ) ); ?></button>
                        </td>
                        <th><label><?php echo esc_html( kaitori_t( 'Payment method', '支払方法' ) ); ?></label></th>
                        <td>
                            <label style="margin-right:14px;"><input type="radio" name="payment_method" value="cash" checked onclick="kaitoriWalkinBank(false)"> <?php echo esc_html( kaitori_t( 'Cash on the spot', '店頭現金' ) ); ?></label>
                            <label><input type="radio" name="payment_method" value="bank" onclick="kaitoriWalkinBank(true)"> <?php echo esc_html( kaitori_t( 'Bank transfer', '銀行振込' ) ); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><label><?php echo esc_html( kaitori_t( 'Order status', '注文ステータス' ) ); ?></label></th>
                        <td>
                            <select name="order_status">
                                <?php foreach ( $statuses as $k => $st ) : ?>
                                    <option value="<?php echo esc_attr( $k ); ?>" <?php selected( $k, 'paid' ); ?>><?php echo esc_html( kaitori_t( $st['label'], $st['label_ja'] ) ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <th></th><td></td>
                    </tr>
                </table>

                <div id="walkin_bank_fields" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 16px;margin-bottom:14px;">
                    <strong><?php echo esc_html( kaitori_t( 'Bank transfer details', '振込先口座情報' ) ); ?></strong>
                    <table class="form-table" style="margin-top:4px;">
                        <tr>
                            <th><label><?php echo esc_html( kaitori_t( 'Bank name', '銀行名' ) ); ?></label></th>
                            <td><input type="text" name="bank_name" class="regular-text"></td>
                            <th><label><?php echo esc_html( kaitori_t( 'Branch', '支店名' ) ); ?></label></th>
                            <td><input type="text" name="bank_branch" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label><?php echo esc_html( kaitori_t( 'Account type', '口座種別' ) ); ?></label></th>
                            <td>
                                <select name="bank_account_type">
                                    <option value="普通"><?php echo esc_html( kaitori_t( 'Ordinary', '普通' ) ); ?></option>
                                    <option value="当座"><?php echo esc_html( kaitori_t( 'Current', '当座' ) ); ?></option>
                                </select>
                            </td>
                            <th><label><?php echo esc_html( kaitori_t( 'Account number', '口座番号' ) ); ?></label></th>
                            <td><input type="text" name="bank_account_number" class="regular-text" maxlength="12"></td>
                        </tr>
                        <tr>
                            <th><label><?php echo esc_html( kaitori_t( 'Account holder (Katakana)', '口座名義（カナ）' ) ); ?></label></th>
                            <td colspan="3"><input type="text" name="bank_holder_kana" class="regular-text"></td>
                        </tr>
                    </table>
                </div>

                <h2 style="border-bottom:2px solid #f1f5f9;padding-bottom:8px;"><?php echo esc_html( kaitori_t( 'Note', '備考' ) ); ?></h2>
                <p><textarea name="admin_memo" rows="2" style="width:100%;max-width:860px;" placeholder="<?php echo esc_attr( kaitori_t( 'Internal note (optional) — shown on the invoice.', '備考（任意）— 伝票にも表示されます。' ) ); ?>"></textarea></p>

                <p style="margin-top:18px;">
                    <button type="submit" name="kaitori_walkin_submit" value="1" class="button button-primary button-hero" style="background:#ef4444;border-color:#ef4444;">
                        <span class="dashicons dashicons-yes" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Create Order & Issue Invoice', '注文を作成して伝票を発行' ) ); ?>
                    </button>
                </p>
            </form>

            <script>
            function kaitoriWalkinBank(show){ var el=document.getElementById('walkin_bank_fields'); if(el) el.style.display = show ? 'block' : 'none'; }
            (function(){
                var btn = document.getElementById('walkin_est_btn');
                if (!btn || typeof ajaxurl === 'undefined') { return; }
                btn.addEventListener('click', function(){
                    function v(n){ var e=document.querySelector('[name="'+n+'"]'); return e ? e.value : ''; }
                    var hint = document.getElementById('walkin_est_hint');
                    hint.textContent = '<?php echo esc_js( kaitori_t( 'Calculating…', '計算中…' ) ); ?>';
                    jQuery.get(ajaxurl, {
                        action: 'kaitori_live_estimate',
                        model: v('device_model'),
                        storage: v('device_storage'),
                        screen_condition: v('screen_condition'),
                        body_condition: v('body_condition'),
                        battery_health: v('battery_health')
                    }, function(res){
                        if (res && res.success) {
                            hint.textContent = '<?php echo esc_js( kaitori_t( 'Reference:', '参考:' ) ); ?> ' + res.data.formatted;
                            var fp = document.querySelector('[name="final_price"]');
                            if (fp && !fp.value) { fp.value = res.data.price; }
                        } else { hint.textContent = ''; }
                    });
                });
            })();
            </script>

        <?php endif; ?>
    </div>
    <?php
}
