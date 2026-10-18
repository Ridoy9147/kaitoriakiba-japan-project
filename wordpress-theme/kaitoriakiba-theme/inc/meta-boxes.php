<?php
/**
 * Kaitori Akiba Device Meta Boxes
 * Handles custom pricing ranks, specs, and storage options.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Meta Box for kaitori_device
 */
function kaitoriakiba_add_device_meta_boxes() {
    add_meta_box(
        'kaitori_device_pricing_meta',
        __( 'Buyback Pricing & Specifications (買取価格・スペック設定)', 'kaitoriakiba' ),
        'kaitoriakiba_device_meta_box_render',
        'kaitori_device',
        'normal',
        'high'
    );

    add_meta_box(
        'kaitori_order_meta',
        __( 'Application Details & Customer Information (申込詳細・お客様情報)', 'kaitoriakiba' ),
        'kaitoriakiba_order_meta_box_render',
        'buyback_order',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'kaitoriakiba_add_device_meta_boxes' );

/**
 * Render Device Pricing Meta Box
 */
function kaitoriakiba_device_meta_box_render( $post ) {
    wp_nonce_field( 'kaitori_device_meta_save', 'kaitori_device_meta_nonce' );

    $max_price     = get_post_meta( $post->ID, '_max_buyback_price', true );
    $price_s       = get_post_meta( $post->ID, '_price_rank_s', true );
    $price_a       = get_post_meta( $post->ID, '_price_rank_a', true );
    $price_b       = get_post_meta( $post->ID, '_price_rank_b', true );
    $price_c       = get_post_meta( $post->ID, '_price_rank_c', true );
    $price_d       = get_post_meta( $post->ID, '_price_rank_d', true );
    $installment   = get_post_meta( $post->ID, '_installment_deduction', true ) ?: '5000';
    $model_num     = get_post_meta( $post->ID, '_model_number', true );
    $jan_code      = get_post_meta( $post->ID, '_jan_code', true );
    $storages      = get_post_meta( $post->ID, '_storage_options', true ) ?: "128GB:152000\n256GB:168000\n512GB:184000\n1TB:202000";
    $colors        = get_post_meta( $post->ID, '_color_options', true ) ?: "ブラックチタニウム, ホワイトチタニウム, ナチュラルチタニウム, デザートチタニウム";
    ?>
    <style>
        .kaitori-meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .kaitori-meta-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; }
        .kaitori-meta-item label { display: block; font-weight: 700; margin-bottom: 6px; font-size: 13px; color: #1e293b; }
        .kaitori-meta-item input, .kaitori-meta-item textarea { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; }
        .kaitori-full-width { grid-column: span 3; }
        .price-badge-label { color: #dc2626; font-size: 11.5px; font-weight: bold; }
    </style>

    <div class="kaitori-meta-grid">
        <div class="kaitori-meta-item">
            <label for="max_buyback_price">最高買取価格 (Max Buyback Price ¥) *</label>
            <input type="number" id="max_buyback_price" name="_max_buyback_price" value="<?php echo esc_attr( $max_price ); ?>" placeholder="168000" />
            <small class="price-badge-label">一覧やトップで表示される上限価格</small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_s">Rank S (新品未開封 ¥)</label>
            <input type="number" id="price_rank_s" name="_price_rank_s" value="<?php echo esc_attr( $price_s ); ?>" placeholder="168000" />
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_a">Rank A (未使用品 ¥)</label>
            <input type="number" id="price_rank_a" name="_price_rank_a" value="<?php echo esc_attr( $price_a ); ?>" placeholder="155000" />
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_b">Rank B (中古美品 ¥)</label>
            <input type="number" id="price_rank_b" name="_price_rank_b" value="<?php echo esc_attr( $price_b ); ?>" placeholder="142000" />
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_c">Rank C (中古良品 ¥)</label>
            <input type="number" id="price_rank_c" name="_price_rank_c" value="<?php echo esc_attr( $price_c ); ?>" placeholder="128000" />
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_d">Rank D (画面割れ・ジャンク ¥)</label>
            <input type="number" id="price_rank_d" name="_price_rank_d" value="<?php echo esc_attr( $price_d ); ?>" placeholder="82000" />
        </div>

        <div class="kaitori-meta-item">
            <label for="installment_deduction">分割支払い中・利用制限△ 減額 (¥)</label>
            <input type="number" id="installment_deduction" name="_installment_deduction" value="<?php echo esc_attr( $installment ); ?>" placeholder="5000" />
            <small style="color:#64748b;">残債あり(△)の場合に差し引く金額</small>
        </div>

        <div class="kaitori-meta-item">
            <label for="model_number">型番 (Model Number)</label>
            <input type="text" id="model_number" name="_model_number" value="<?php echo esc_attr( $model_num ); ?>" placeholder="A3293" />
        </div>

        <div class="kaitori-meta-item">
            <label for="jan_code">JANコード (JAN Code)</label>
            <input type="text" id="jan_code" name="_jan_code" value="<?php echo esc_attr( $jan_code ); ?>" placeholder="4549995734140" />
        </div>

        <div class="kaitori-meta-item kaitori-full-width">
            <label for="storage_options">容量と価格設定 (Storage Capacity : Base Price ¥) 1行に1つ</label>
            <textarea id="storage_options" name="_storage_options" rows="4"><?php echo esc_textarea( $storages ); ?></textarea>
            <small style="color:#64748b;">例: 128GB:152000 (改行) 256GB:168000 (改行) 512GB:184000 (改行) 1TB:202000</small>
        </div>

        <div class="kaitori-meta-item kaitori-full-width">
            <label for="color_options">カラーバリエーション (カンマ区切り)</label>
            <input type="text" id="color_options" name="_color_options" value="<?php echo esc_attr( $colors ); ?>" placeholder="ブラックチタニウム, ホワイトチタニウム, ナチュラルチタニウム, デザートチタニウム" />
        </div>
    </div>
    <?php
}

/**
 * Save Device Meta Box
 */
function kaitoriakiba_save_device_meta( $post_id ) {
    if ( ! isset( $_POST['kaitori_device_meta_nonce'] ) || ! wp_verify_nonce( $_POST['kaitori_device_meta_nonce'], 'kaitori_device_meta_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = array(
        '_max_buyback_price',
        '_price_rank_s',
        '_price_rank_a',
        '_price_rank_b',
        '_price_rank_c',
        '_price_rank_d',
        '_installment_deduction',
        '_model_number',
        '_jan_code',
        '_storage_options',
        '_color_options',
    );

    foreach ( $fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, sanitize_textarea_field( $_POST[$field] ) );
        }
    }
}
add_action( 'save_post_kaitori_device', 'kaitoriakiba_save_device_meta' );

/**
 * Render Buyback Order Meta Box
 */
function kaitoriakiba_order_meta_box_render( $post ) {
    $order_status   = get_post_meta( $post->ID, '_order_status', true ) ?: 'pending';
    $name           = get_post_meta( $post->ID, '_applicant_name', true );
    $kana           = get_post_meta( $post->ID, '_applicant_kana', true );
    $email          = get_post_meta( $post->ID, '_applicant_email', true );
    $phone          = get_post_meta( $post->ID, '_applicant_phone', true );
    $model          = get_post_meta( $post->ID, '_device_model', true );
    $storage        = get_post_meta( $post->ID, '_device_storage', true );
    $color          = get_post_meta( $post->ID, '_device_color', true );
    $carrier        = get_post_meta( $post->ID, '_device_carrier', true );
    $imei           = get_post_meta( $post->ID, '_device_imei', true );
    $serial         = get_post_meta( $post->ID, '_device_serial', true );
    $screen_cond    = get_post_meta( $post->ID, '_screen_condition', true );
    $body_cond      = get_post_meta( $post->ID, '_body_condition', true );
    $battery_health = get_post_meta( $post->ID, '_battery_health', true );
    $powers_on      = get_post_meta( $post->ID, '_powers_on', true );
    $face_touch_id  = get_post_meta( $post->ID, '_face_touch_id', true );
    $camera_works   = get_post_meta( $post->ID, '_camera_works', true );
    $water_history  = get_post_meta( $post->ID, '_water_damage_repair', true );
    $extra_issues   = get_post_meta( $post->ID, '_extra_issues', true );
    $find_my_off    = get_post_meta( $post->ID, '_find_my_off', true );
    $apple_id_out   = get_post_meta( $post->ID, '_apple_id_signout', true );
    $no_mdm_lock    = get_post_meta( $post->ID, '_no_mdm_lock', true );
    $installment_st = get_post_meta( $post->ID, '_installment_status', true );
    $installment_due= get_post_meta( $post->ID, '_installment_due_amount', true );
    $delivery_meth  = get_post_meta( $post->ID, '_delivery_method', true );
    $pickup_dt      = get_post_meta( $post->ID, '_pickup_datetime', true );
    $shipping_kit   = get_post_meta( $post->ID, '_shipping_kit_needed', true );
    $bank_name      = get_post_meta( $post->ID, '_bank_name', true );
    $branch_name    = get_post_meta( $post->ID, '_bank_branch', true );
    $acc_type       = get_post_meta( $post->ID, '_bank_account_type', true );
    $acc_number     = get_post_meta( $post->ID, '_bank_account_number', true );
    $holder_kana    = get_post_meta( $post->ID, '_bank_holder_kana', true );
    $est_price      = get_post_meta( $post->ID, '_estimated_price', true );
    $accessories    = get_post_meta( $post->ID, '_accessories', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label>ステータス (Status)</label></th>
            <td>
                <select name="_order_status" style="padding:6px 12px; font-weight:bold;">
                    <option value="pending" <?php selected($order_status, 'pending'); ?>>受付中 (Pending)</option>
                    <option value="received" <?php selected($order_status, 'received'); ?>>端末受領 (Received)</option>
                    <option value="inspected" <?php selected($order_status, 'inspected'); ?>>査定完了 (Inspected)</option>
                    <option value="paid" <?php selected($order_status, 'paid'); ?>>振込完了 (Paid)</option>
                    <option value="cancelled" <?php selected($order_status, 'cancelled'); ?>>キャンセル (Cancelled)</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>査定金額 (Estimated Price)</label></th>
            <td><strong style="color:#ef4444; font-size:18px;">¥<?php echo number_format((int)$est_price); ?></strong></td>
        </tr>
        <tr>
            <th><label>お客様情報 (Customer Details)</label></th>
            <td>
                <strong>氏名:</strong> <?php echo esc_html($name); ?> (<?php echo esc_html($kana); ?>)<br>
                <strong>電話番号:</strong> <?php echo esc_html($phone); ?><br>
                <strong>メール:</strong> <?php echo esc_html($email); ?>
            </td>
        </tr>
        <tr>
            <th><label>端末情報 (Device Details)</label></th>
            <td>
                <strong>機種:</strong> <?php echo esc_html($model); ?><br>
                <strong>容量・カラー:</strong> <?php echo esc_html($storage); ?> / <?php echo esc_html($color); ?><br>
                <strong>キャリア:</strong> <?php echo esc_html($carrier); ?><br>
                <strong>IMEI (15桁):</strong> <code style="font-size:14px;"><?php echo esc_html($imei); ?></code><br>
                <strong>シリアル番号:</strong> <?php echo esc_html($serial ?: '—'); ?>
            </td>
        </tr>
        <tr>
            <th><label>状態確認 (Condition Checklist)</label></th>
            <td>
                ・画面: <?php echo esc_html($screen_cond); ?><br>
                ・外装/フレーム: <?php echo esc_html($body_cond); ?><br>
                ・バッテリー最大容量: <?php echo esc_html($battery_health); ?>%<br>
                ・通電・起動: <?php echo esc_html($powers_on); ?><br>
                ・生体認証 (Face/Touch ID): <?php echo esc_html($face_touch_id); ?><br>
                ・カメラ動作: <?php echo esc_html($camera_works); ?><br>
                ・水没・修理歴: <?php echo esc_html($water_history); ?><br>
                ・付属品: <?php echo esc_html(is_array($accessories) ? implode(', ', $accessories) : $accessories); ?><br>
                ・その他問題点: <?php echo esc_html($extra_issues ?: 'なし'); ?>
            </td>
        </tr>
        <tr>
            <th><label>分割支払い・残債状況 (Installment)</label></th>
            <td>
                <strong>支払い状況:</strong> <?php echo esc_html($installment_st === 'yes' ? '分割残債あり (△)' : '一括完済 (○)'); ?><br>
                <?php if ($installment_st === 'yes'): ?>
                    <strong>残債金額 (Due Amount):</strong> <span style="color:#dc2626; font-weight:bold;">¥<?php echo number_format((int)$installment_due); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label>セキュリティ確認 (Security Locks)</label></th>
            <td>
                ・「iPhoneを探す」オフ: <?php echo $find_my_off ? '✓ 確認済み' : '未確認'; ?><br>
                ・Apple ID サインアウト: <?php echo $apple_id_out ? '✓ 確認済み' : '未確認'; ?><br>
                ・MDM/会社管理ロックなし: <?php echo $no_mdm_lock ? '✓ 確認済み' : '未確認'; ?>
            </td>
        </tr>
        <tr>
            <th><label>受取方法 (Delivery Method)</label></th>
            <td>
                <strong>方法:</strong> <?php echo esc_html($delivery_meth === 'store' ? '店頭持込 (秋葉原店)' : '郵送買取 (宅配)'); ?><br>
                <strong>梱包キット希望:</strong> <?php echo esc_html($shipping_kit ?: '不要'); ?><br>
                <strong>希望日時:</strong> <?php echo esc_html($pickup_dt ?: '指定なし'); ?>
            </td>
        </tr>
        <tr>
            <th><label>お振込先口座 (Bank Payout Details)</label></th>
            <td>
                <strong>銀行名:</strong> <?php echo esc_html($bank_name); ?><br>
                <strong>支店名:</strong> <?php echo esc_html($branch_name); ?><br>
                <strong>口座種別:</strong> <?php echo esc_html($acc_type); ?><br>
                <strong>口座番号:</strong> <?php echo esc_html($acc_number); ?><br>
                <strong>口座名義 (カタカナ):</strong> <?php echo esc_html($holder_kana); ?>
            </td>
        </tr>
    </table>
    <?php
}

function kaitoriakiba_save_order_meta( $post_id ) {
    if ( isset( $_POST['_order_status'] ) ) {
        update_post_meta( $post_id, '_order_status', sanitize_text_field( $_POST['_order_status'] ) );
    }
}
add_action( 'save_post_buyback_order', 'kaitoriakiba_save_order_meta' );
