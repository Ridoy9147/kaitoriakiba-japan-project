<?php
/**
 * Kaitori Akiba Device Meta Boxes
 * Handles custom pricing ranks, specs, storage options, custom images, and order management.
 * Admin interface is 100% English by default.
 * 
 * @package KaitoriAkiba
 * @author dev hridoy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Meta Boxes for kaitori_device, buyback_order, and kyc_record
 */
function kaitoriakiba_add_meta_boxes() {
    // 1. Device Pricing & Specifications
    add_meta_box(
        'kaitori_device_pricing_meta',
        kaitori_t( 'Buyback Pricing & Technical Specifications', '買取価格・技術仕様' ),
        'kaitoriakiba_device_meta_box_render',
        'kaitori_device',
        'normal',
        'high'
    );

    // 2. Device Primary Image & Custom Gallery
    add_meta_box(
        'kaitori_device_gallery_meta',
        kaitori_t( 'Device Primary Image & Color Variants Gallery', '端末メイン画像・カラーバリエーション ギャラリー' ),
        'kaitoriakiba_device_gallery_meta_box_render',
        'kaitori_device',
        'normal',
        'high'
    );

    // 3. Buyback Order Details
    add_meta_box(
        'kaitori_order_meta',
        kaitori_t( 'Application Details & Customer Information', '申込内容・お客様情報' ),
        'kaitoriakiba_order_meta_box_render',
        'buyback_order',
        'normal',
        'high'
    );

    // 4. eKYC Verification Documents & Status
    add_meta_box(
        'kaitori_kyc_meta',
        kaitori_t( 'Identity Verification (eKYC) Document Review', '本人確認 (eKYC) 書類審査' ),
        'kaitoriakiba_kyc_meta_box_render',
        'kyc_record',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'kaitoriakiba_add_meta_boxes' );

/**
 * Enqueue Media Scripts in Admin for Device Post Type
 */
function kaitoriakiba_admin_enqueue_media( $hook ) {
    global $post;
    if ( ( $hook === 'post-new.php' || $hook === 'post.php' ) && isset( $post->post_type ) && $post->post_type === 'kaitori_device' ) {
        wp_enqueue_media();
    }
}
add_action( 'admin_enqueue_scripts', 'kaitoriakiba_admin_enqueue_media' );

/**
 * Render Device Pricing Meta Box (100% English)
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
    $colors        = get_post_meta( $post->ID, '_color_options', true ) ?: "Black Titanium, White Titanium, Natural Titanium, Desert Titanium";
    ?>
    <style>
        .kaitori-meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        .kaitori-meta-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; }
        .kaitori-meta-item label { display: block; font-weight: 700; margin-bottom: 6px; font-size: 13px; color: #1e293b; }
        .kaitori-meta-item input, .kaitori-meta-item textarea { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 10px; font-size: 13px; }
        .kaitori-full-width { grid-column: span 3; }
        .price-badge-label { color: #dc2626; font-size: 11.5px; font-weight: bold; display: block; margin-top: 4px; }
    </style>

    <div class="kaitori-meta-grid">
        <div class="kaitori-meta-item">
            <label for="max_buyback_price"><?php echo kaitori_t( 'Maximum Buyback Price (¥) *', '最高買取価格 (¥) *' ); ?></label>
            <input type="number" id="max_buyback_price" name="_max_buyback_price" value="<?php echo esc_attr( $max_price ); ?>" placeholder="168000" />
            <small class="price-badge-label"><?php echo kaitori_t( 'The highest ceiling price shown on homepage cards and search', 'トップページのカードや検索に表示される上限価格' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_s"><?php echo kaitori_t( 'Rank S Price (Factory Sealed ¥)', 'ランクS価格（未開封 ¥）' ); ?></label>
            <input type="number" id="price_rank_s" name="_price_rank_s" value="<?php echo esc_attr( $price_s ); ?>" placeholder="168000" />
            <small style="color:#64748b;"><?php echo kaitori_t( 'Brand new, unopened box with security seal', '新品・未開封（セキュリティシール付き）' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_a"><?php echo kaitori_t( 'Rank A Price (Unused / Open Box ¥)', 'ランクA価格（未使用・開封済 ¥）' ); ?></label>
            <input type="number" id="price_rank_a" name="_price_rank_a" value="<?php echo esc_attr( $price_a ); ?>" placeholder="155000" />
            <small style="color:#64748b;"><?php echo kaitori_t( 'Activated for testing, pristine condition', '動作確認済み・極美品' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_b"><?php echo kaitori_t( 'Rank B Price (Used - Excellent ¥)', 'ランクB価格（中古・美品 ¥）' ); ?></label>
            <input type="number" id="price_rank_b" name="_price_rank_b" value="<?php echo esc_attr( $price_b ); ?>" placeholder="142000" />
            <small style="color:#64748b;"><?php echo kaitori_t( 'Used with no noticeable scratches or dents', '目立つ傷や打痕のない中古' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_c"><?php echo kaitori_t( 'Rank C Price (Used - Good / Minor Marks ¥)', 'ランクC価格（中古・小傷あり ¥）' ); ?></label>
            <input type="number" id="price_rank_c" name="_price_rank_c" value="<?php echo esc_attr( $price_c ); ?>" placeholder="128000" />
            <small style="color:#64748b;"><?php echo kaitori_t( 'Visible minor abrasions or small scratches', '目に見える擦れや小傷あり' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_d"><?php echo kaitori_t( 'Rank D Price (Cracked Screen / Heavy Marks ¥)', 'ランクD価格（画面割れ・難あり ¥）' ); ?></label>
            <input type="number" id="price_rank_d" name="_price_rank_d" value="<?php echo esc_attr( $price_d ); ?>" placeholder="82000" />
            <small style="color:#64748b;"><?php echo kaitori_t( 'Cracked glass, heavy wear, or battery below 80%', 'ガラス割れ・使用感大・バッテリー80%未満' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="installment_deduction"><?php echo kaitori_t( 'Installment Balance (△) Deduction (¥)', '分割残債 (△) 減額 (¥)' ); ?></label>
            <input type="number" id="installment_deduction" name="_installment_deduction" value="<?php echo esc_attr( $installment ); ?>" placeholder="5000" />
            <small style="color:#64748b;"><?php echo kaitori_t( 'Amount deducted if device has remaining carrier payments (triangle mark)', 'キャリアの分割残債がある場合（△表示）に減額する金額' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="model_number"><?php echo kaitori_t( 'Model Number (e.g., A3293)', '型番（例: A3293）' ); ?></label>
            <input type="text" id="model_number" name="_model_number" value="<?php echo esc_attr( $model_num ); ?>" placeholder="A3293" />
        </div>

        <div class="kaitori-meta-item">
            <label for="jan_code"><?php echo kaitori_t( 'JAN / EAN Barcode', 'JAN / EAN バーコード' ); ?></label>
            <input type="text" id="jan_code" name="_jan_code" value="<?php echo esc_attr( $jan_code ); ?>" placeholder="4549995734140" />
        </div>

        <div class="kaitori-meta-item kaitori-full-width">
            <label for="storage_options"><?php echo kaitori_t( 'Storage Capacities & Base Buyback Prices (One per line format: Capacity:Price)', '容量・基準買取価格（1行に1件、形式: 容量:価格）' ); ?></label>
            <textarea id="storage_options" name="_storage_options" rows="4"><?php echo esc_textarea( $storages ); ?></textarea>
            <small style="color:#64748b;"><?php echo kaitori_t( 'Format: 128GB:152000 (new line) 256GB:168000 (new line) 512GB:184000 (new line) 1TB:202000', '形式: 128GB:152000（改行）256GB:168000（改行）512GB:184000（改行）1TB:202000' ); ?></small>
        </div>

        <div class="kaitori-meta-item kaitori-full-width">
            <label for="color_options"><?php echo kaitori_t( 'Available Colors / Variants (Comma-separated)', '対応カラー・バリエーション（カンマ区切り）' ); ?></label>
            <input type="text" id="color_options" name="_color_options" value="<?php echo esc_attr( $colors ); ?>" placeholder="Black Titanium, White Titanium, Natural Titanium, Desert Titanium" />
        </div>
    </div>
    <?php
}

/**
 * Render Device Primary Image & Custom Gallery Meta Box
 */
function kaitoriakiba_device_gallery_meta_box_render( $post ) {
    $primary_image  = get_post_meta( $post->ID, '_device_primary_image_url', true );
    $gallery_raw    = get_post_meta( $post->ID, '_device_gallery_images', true );
    $gallery_images = array();

    if ( ! empty( $gallery_raw ) ) {
        if ( is_array( $gallery_raw ) ) {
            $gallery_images = $gallery_raw;
        } else {
            $decoded = json_decode( $gallery_raw, true );
            if ( is_array( $decoded ) ) {
                $gallery_images = $decoded;
            }
        }
    }

    $default_primary = get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png';
    ?>
    <style>
        .device-media-wrap { padding: 10px 0; }
        .device-primary-box { display: flex; align-items: center; gap: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 24px; }
        .device-preview-img { width: 100px; height: 100px; object-fit: contain; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-top: 12px; }
        .gallery-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; text-align: center; position: relative; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .gallery-card img { width: 90px; height: 90px; object-fit: contain; margin-bottom: 8px; background: #f8fafc; border-radius: 6px; padding: 4px; border: 1px solid #f1f5f9; }
        .gallery-card input { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 8px; font-size: 12px; margin-bottom: 8px; }
        .gallery-card .btn-remove-item { background: #fee2e2; color: #ef4444; border: 1px solid #fca5a5; border-radius: 6px; padding: 4px 10px; font-size: 11px; cursor: pointer; font-weight: bold; width: 100%; }
        .gallery-card .btn-remove-item:hover { background: #ef4444; color: #fff; }
    </style>

    <div class="device-media-wrap">
        <!-- 1. Primary Device Image -->
        <h4 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: #0f172a;">
            <i class="dashicons dashicons-format-image" style="vertical-align: middle; color: #ef4444;"></i>
            <?php echo kaitori_t( 'Primary Device Image', '端末メイン画像' ); ?>
        </h4>
        <div class="device-primary-box">
            <img id="primary_image_preview" class="device-preview-img" src="<?php echo esc_url( $primary_image ?: $default_primary ); ?>" alt="Device Preview">
            <div>
                <input type="hidden" id="device_primary_image_url" name="_device_primary_image_url" value="<?php echo esc_attr( $primary_image ); ?>" />
                <button type="button" class="button button-primary" id="btn_upload_primary_image" style="background:#ef4444; border-color:#ef4444; font-weight:700;">
                    <?php echo kaitori_t( 'Upload / Select Primary Image', 'メイン画像をアップロード / 選択' ); ?>
                </button>
                <button type="button" class="button" id="btn_remove_primary_image" style="margin-left: 6px; <?php echo empty( $primary_image ) ? 'display:none;' : ''; ?>">
                    <?php echo kaitori_t( 'Reset to Default Image', 'デフォルト画像に戻す' ); ?>
                </button>
                <p style="margin: 6px 0 0 0; font-size: 12px; color: #64748b;">
                    <?php echo kaitori_t( 'If no custom primary image is uploaded, the theme will automatically show the default showcase device image.', 'メイン画像が未設定の場合、デフォルトの端末ショーケース画像が自動で表示されます。' ); ?>
                </p>
            </div>
        </div>

        <!-- 2. Device Gallery / Color Variants -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div>
                <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: #0f172a;">
                    <i class="dashicons dashicons-images-alt2" style="vertical-align: middle; color: #ef4444;"></i>
                    <?php echo kaitori_t( 'Color Variants & Multi-Angle Gallery', 'カラーバリエーション・多角度ギャラリー' ); ?>
                </h4>
                <small style="color: #64748b; font-size: 12px;">
                    <?php echo kaitori_t( 'Upload photos for individual colors (e.g. Natural Titanium, White, Black). If left empty, default showcase colors are displayed.', 'カラーごとの写真をアップロードしてください（例: ナチュラルチタニウム・ホワイト・ブラック）。未設定の場合はデフォルトのカラーが表示されます。' ); ?>
                </small>
            </div>
            <button type="button" class="button button-secondary" id="btn_add_gallery_item" style="font-weight: 700;">
                <i class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></i> <?php echo kaitori_t( 'Add Gallery Image (+)', 'ギャラリー画像を追加 (+)' ); ?>
            </button>
        </div>

        <div id="gallery_items_container" class="gallery-grid">
            <?php
            if ( ! empty( $gallery_images ) ) :
                foreach ( $gallery_images as $idx => $item ) :
                    $img_url   = is_array( $item ) && isset( $item['url'] ) ? $item['url'] : ( is_string( $item ) ? $item : '' );
                    $img_label = is_array( $item ) && isset( $item['label'] ) ? $item['label'] : '';
                    if ( empty( $img_url ) ) continue;
                    ?>
                    <div class="gallery-card" data-index="<?php echo esc_attr( $idx ); ?>">
                        <img src="<?php echo esc_url( $img_url ); ?>" alt="Gallery Image">
                        <input type="hidden" name="device_gallery_urls[]" value="<?php echo esc_attr( $img_url ); ?>">
                        <input type="text" name="device_gallery_labels[]" value="<?php echo esc_attr( $img_label ); ?>" placeholder="Color name (e.g. Black)" />
                        <button type="button" class="btn-remove-item" onclick="this.closest('.gallery-card').remove();"><?php echo kaitori_t( 'Remove Image', '画像を削除' ); ?></button>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
        <p id="gallery_empty_notice" style="color: #94a3b8; font-style: italic; font-size: 12.5px; margin-top: 10px; <?php echo ! empty( $gallery_images ) ? 'display:none;' : ''; ?>">
            <?php echo kaitori_t( 'No custom gallery images uploaded yet. The default 4 color angles will be displayed.', 'ギャラリー画像はまだ未設定です。デフォルトの4カラー・アングルが表示されます。' ); ?>
        </p>
    </div>

    <!-- Media Uploader Script -->
    <script>
    jQuery(document).ready(function($) {
        var mediaFrame;
        var defaultPrimary = "<?php echo esc_js( $default_primary ); ?>";

        // Primary Image Uploader
        $('#btn_upload_primary_image').on('click', function(e) {
            e.preventDefault();
            if (mediaFrame) {
                mediaFrame.open();
                return;
            }
            mediaFrame = wp.media({
                title: 'Select Device Primary Image',
                button: { text: 'Use this Image' },
                multiple: false
            });
            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                $('#device_primary_image_url').val(attachment.url);
                $('#primary_image_preview').attr('src', attachment.url);
                $('#btn_remove_primary_image').show();
            });
            mediaFrame.open();
        });

        // Reset Primary Image
        $('#btn_remove_primary_image').on('click', function(e) {
            e.preventDefault();
            $('#device_primary_image_url').val('');
            $('#primary_image_preview').attr('src', defaultPrimary);
            $(this).hide();
        });

        // Add Gallery Item Uploader
        var galleryFrame;
        $('#btn_add_gallery_item').on('click', function(e) {
            e.preventDefault();
            galleryFrame = wp.media({
                title: 'Select Gallery Image',
                button: { text: 'Add to Gallery' },
                multiple: true
            });
            galleryFrame.on('select', function() {
                var selection = galleryFrame.state().get('selection');
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    var cardHtml = '<div class="gallery-card">' +
                        '<img src="' + attachment.url + '" alt="Gallery Image">' +
                        '<input type="hidden" name="device_gallery_urls[]" value="' + attachment.url + '">' +
                        '<input type="text" name="device_gallery_labels[]" value="" placeholder="Color / Variant name (e.g. Silver)" />' +
                        '<button type="button" class="btn-remove-item" onclick="this.closest(\'.gallery-card\').remove();">Remove Image</button>' +
                        '</div>';
                    $('#gallery_items_container').append(cardHtml);
                });
                $('#gallery_empty_notice').hide();
            });
            galleryFrame.open();
        });
    });
    </script>
    <?php
}

/**
 * Save Device Meta Box (Pricing, Specs, Primary Image & Gallery)
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

    // Save technical & pricing specs
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
        '_device_primary_image_url',
    );

    foreach ( $fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, sanitize_textarea_field( $_POST[$field] ) );
        }
    }

    // Save gallery images
    if ( isset( $_POST['device_gallery_urls'] ) && is_array( $_POST['device_gallery_urls'] ) ) {
        $urls   = array_map( 'esc_url_raw', $_POST['device_gallery_urls'] );
        $labels = isset( $_POST['device_gallery_labels'] ) && is_array( $_POST['device_gallery_labels'] ) 
                    ? array_map( 'sanitize_text_field', $_POST['device_gallery_labels'] ) 
                    : array();

        $gallery_data = array();
        for ( $i = 0; $i < count( $urls ); $i++ ) {
            if ( ! empty( $urls[$i] ) ) {
                $gallery_data[] = array(
                    'url'   => $urls[$i],
                    'label' => isset( $labels[$i] ) ? $labels[$i] : '',
                );
            }
        }
        update_post_meta( $post_id, '_device_gallery_images', $gallery_data );
    } else {
        delete_post_meta( $post_id, '_device_gallery_images' );
    }
}
add_action( 'save_post_kaitori_device', 'kaitoriakiba_save_device_meta' );

/**
 * Render Buyback Order Meta Box (100% English Admin)
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
    $acc_number     = function_exists( 'kaitoriakiba_decrypt' ) ? kaitoriakiba_decrypt( get_post_meta( $post->ID, '_bank_account_number', true ) ) : get_post_meta( $post->ID, '_bank_account_number', true );
    $holder_kana    = function_exists( 'kaitoriakiba_decrypt' ) ? kaitoriakiba_decrypt( get_post_meta( $post->ID, '_bank_holder_kana', true ) ) : get_post_meta( $post->ID, '_bank_holder_kana', true );
    $est_price      = get_post_meta( $post->ID, '_estimated_price', true );
    $accessories    = get_post_meta( $post->ID, '_accessories', true );
    ?>
    <?php if ( function_exists( 'kaitoriakiba_invoice_url' ) ) : ?>
        <div style="margin:4px 0 16px; padding:12px 14px; background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <strong style="color:#9a3412;"><span class="dashicons dashicons-media-document" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Buyback Invoice', '買取伝票' ) ); ?></strong>
            <a href="<?php echo esc_url( kaitoriakiba_invoice_url( $post->ID, true ) ); ?>" target="_blank" class="button button-primary" style="background:#ef4444; border-color:#ef4444; font-weight:700;">
                <span class="dashicons dashicons-printer" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Print / Save as PDF', '印刷 / PDF保存' ) ); ?>
            </a>
            <a href="<?php echo esc_url( kaitoriakiba_invoice_url( $post->ID, false ) ); ?>" target="_blank" class="button"><?php echo esc_html( kaitori_t( 'Preview (Shop + Customer copy)', 'プレビュー（店舗控え・お客様控え）' ) ); ?></a>
        </div>
    <?php endif; ?>
    <table class="form-table">
        <tr>
            <th style="width:200px;"><label><?php echo kaitori_t( 'Order Processing Status', '注文処理ステータス' ); ?></label></th>
            <td>
                <select name="_order_status" style="padding:6px 14px; font-weight:bold; font-size:13px; border-radius:6px; border:1.5px solid #cbd5e1;">
                    <?php foreach ( kaitoriakiba_order_statuses() as $st_key => $st ) : ?>
                        <option value="<?php echo esc_attr( $st_key ); ?>" <?php selected( $order_status, $st_key ); ?>><?php echo esc_html( $st['label'] . ' (' . $st['label_ja'] . ')' ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Estimated Buyback Quote', '概算買取査定額' ); ?></label></th>
            <td><strong style="color:#ef4444; font-size:22px; font-weight:900;">¥<?php echo number_format((int)$est_price); ?></strong></td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Customer Information', 'お客様情報' ); ?></label></th>
            <td>
                <strong><?php echo kaitori_t( 'Full Name:', '氏名:' ); ?></strong> <?php echo esc_html($name); ?> (<?php echo esc_html($kana); ?>)<br>
                <strong><?php echo kaitori_t( 'Phone Number:', '電話番号:' ); ?></strong> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a><br>
                <strong><?php echo kaitori_t( 'Email Address:', 'メールアドレス:' ); ?></strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                <?php
                // Check connected WP User
                $customer_user_id = get_post_meta( $post->ID, '_customer_user_id', true );
                if ( ! $customer_user_id && $email ) {
                    $u = get_user_by( 'email', $email );
                    if ( $u ) {
                        $customer_user_id = $u->ID;
                    }
                }
                if ( $customer_user_id ) :
                    $u_data = get_userdata( $customer_user_id );
                    if ( $u_data ) :
                    ?>
                    <div style="margin-top:6px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:6px 10px; display:inline-block;">
                        <i class="dashicons dashicons-admin-users" style="color:#2563eb; vertical-align:middle;"></i>
                        <strong><?php echo kaitori_t( 'Linked Customer Account:', '紐付け会員アカウント:' ); ?></strong>
                        <a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $customer_user_id ) ); ?>" target="_blank" style="font-weight:bold; color:#1d4ed8; text-decoration:underline;">
                            <?php echo esc_html( $u_data->display_name ?: $u_data->user_login ); ?> (#<?php echo esc_html( $customer_user_id ); ?>)
                        </a>
                    </div>
                    <?php
                    endif;
                endif;
                ?>
            </td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Identity Verification (eKYC) Status', '本人確認 (eKYC) ステータス' ); ?></label></th>
            <td>
                <?php
                // Check linked eKYC
                $kyc_id = get_post_meta( $post->ID, '_order_kyc_record_id', true );
                if ( ! $kyc_id && $email ) {
                    $found_kyc = get_posts( array(
                        'post_type'   => 'kyc_record',
                        'meta_key'    => '_kyc_email',
                        'meta_value'  => $email,
                        'numberposts' => 1,
                        'post_status' => 'any',
                    ) );
                    if ( ! empty( $found_kyc ) ) {
                        $kyc_id = $found_kyc[0]->ID;
                    }
                }

                if ( $kyc_id ) :
                    $k_status = get_post_meta( $kyc_id, '_kyc_status', true ) ?: 'pending';
                    $k_doc    = get_post_meta( $kyc_id, '_kyc_doc_type', true );
                    if ( $k_status === 'verified' ) {
                        $badge_bg = '#dcfce7'; $badge_col = '#15803d'; $badge_txt = kaitori_t( '✓ Verified & Approved', '✓ 承認完了・本人確認済' );
                    } elseif ( $k_status === 'rejected' ) {
                        $badge_bg = '#fee2e2'; $badge_col = '#b91c1c'; $badge_txt = kaitori_t( '✗ Rejected (Invalid ID)', '✗ 却下（書類不備）' );
                    } else {
                        $badge_bg = '#fef3c7'; $badge_col = '#b45309'; $badge_txt = kaitori_t( '⏳ Pending Verification', '⏳ 確認中・未承認' );
                    }
                ?>
                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <span style="background:<?php echo esc_attr( $badge_bg ); ?>; color:<?php echo esc_attr( $badge_col ); ?>; font-weight:800; padding:4px 12px; border-radius:12px; font-size:12.5px;">
                            <?php echo esc_html( $badge_txt ); ?>
                        </span>
                        <span style="color:#64748b; font-size:13px;">(<?php echo esc_html( $k_doc ?: 'ID Document' ); ?>)</span>
                        <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $kyc_id . '&action=edit' ) ); ?>" class="button button-primary" target="_blank" style="font-weight:700;">
                            <i class="dashicons dashicons-id-alt" style="vertical-align:middle;"></i> <?php echo kaitori_t( 'Review / Inspect Customer\'s eKYC Document &rarr;', 'お客様の eKYC 書類を審査 &rarr;' ); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <span style="background:#f1f5f9; color:#64748b; font-weight:700; padding:4px 12px; border-radius:12px; font-size:12.5px;">
                        <?php echo kaitori_t( '⚠ No eKYC Submitted Yet', '⚠ eKYC 未提出' ); ?>
                    </span>
                    <small style="color:#94a3b8; margin-left:8px;"><?php echo kaitori_t( 'Customer has not yet uploaded identification documents for this order.', 'この注文について、お客様はまだ本人確認書類をアップロードしていません。' ); ?></small>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Device Model & Identifiers', '端末モデル・識別情報' ); ?></label></th>
            <td>
                <strong><?php echo kaitori_t( 'Model:', 'モデル:' ); ?></strong> <?php echo esc_html($model); ?><br>
                <strong><?php echo kaitori_t( 'Capacity & Color:', '容量・カラー:' ); ?></strong> <?php echo esc_html($storage); ?> / <?php echo esc_html($color); ?><br>
                <strong><?php echo kaitori_t( 'Carrier:', 'キャリア:' ); ?></strong> <?php echo esc_html($carrier); ?><br>
                <strong><?php echo kaitori_t( 'IMEI Number (15 Digits):', 'IMEI番号（15桁）:' ); ?></strong>
                <input type="text" id="kaitori_order_imei" name="_device_imei" value="<?php echo esc_attr( $imei ); ?>" style="font-family:monospace; font-weight:bold; width:200px; border:1.5px solid #cbd5e1; border-radius:6px; padding:4px 8px;">
                <button type="button" class="button button-small kaitori-scan" data-order="<?php echo esc_attr( $post->ID ); ?>" data-field="imei">📷 <?php echo kaitori_t( 'Scan', 'スキャン' ); ?></button><br>
                <strong><?php echo kaitori_t( 'Serial Number:', 'シリアル番号:' ); ?></strong> <?php echo esc_html($serial ?: '—'); ?>
            </td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Condition Checklist Reported', '申告された状態チェックリスト' ); ?></label></th>
            <td>
                • <strong><?php echo kaitori_t( 'Screen Condition:', '画面の状態:' ); ?></strong> <?php echo esc_html($screen_cond); ?><br>
                • <strong><?php echo kaitori_t( 'Body / Frame Condition:', '本体・フレームの状態:' ); ?></strong> <?php echo esc_html($body_cond); ?><br>
                • <strong><?php echo kaitori_t( 'Battery Maximum Health:', 'バッテリー最大容量:' ); ?></strong> <?php echo esc_html($battery_health); ?>%<br>
                • <strong><?php echo kaitori_t( 'Powers On Correctly:', '正常に起動する:' ); ?></strong> <?php echo esc_html($powers_on); ?><br>
                • <strong><?php echo kaitori_t( 'Biometrics (Face ID / Touch ID):', '生体認証（Face ID / Touch ID）:' ); ?></strong> <?php echo esc_html($face_touch_id); ?><br>
                • <strong><?php echo kaitori_t( 'Camera Operation:', 'カメラ動作:' ); ?></strong> <?php echo esc_html($camera_works); ?><br>
                • <strong><?php echo kaitori_t( 'Water Damage / Past Repairs:', '水没・修理履歴:' ); ?></strong> <?php echo esc_html($water_history); ?><br>
                • <strong><?php echo kaitori_t( 'Included Accessories:', '付属品:' ); ?></strong> <?php echo esc_html(is_array($accessories) ? implode(', ', $accessories) : $accessories); ?><br>
                • <strong><?php echo kaitori_t( 'Other Reported Issues:', 'その他の申告事項:' ); ?></strong> <?php echo esc_html($extra_issues ?: 'None'); ?>
            </td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Carrier Installment Status', 'キャリア分割払いの状況' ); ?></label></th>
            <td>
                <strong><?php echo kaitori_t( 'Status:', '状況:' ); ?></strong> <?php echo esc_html($installment_st === 'yes' ? kaitori_t( 'Under Installment (△)', '分割残債あり (△)' ) : kaitori_t( 'Fully Paid Off (○)', '完済 (○)' ) ); ?><br>
                <?php if ($installment_st === 'yes'): ?>
                    <strong><?php echo kaitori_t( 'Remaining Balance Due:', '残債額:' ); ?></strong> <span style="color:#dc2626; font-weight:bold;">¥<?php echo number_format((int)$installment_due); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Security & iCloud Lock Verification', 'セキュリティ・iCloudロック確認' ); ?></label></th>
            <td>
                • <?php echo kaitori_t( '"Find My" (Activation Lock) Disabled:', '「探す」（アクティベーションロック）解除済み:' ); ?> <?php echo $find_my_off ? '<span style="color:#16a34a; font-weight:bold;">✓ ' . esc_html( kaitori_t( 'Confirmed', '確認済み' ) ) . '</span>' : '<span style="color:#dc2626;">' . esc_html( kaitori_t( 'Not Confirmed', '未確認' ) ) . '</span>'; ?><br>
                • <?php echo kaitori_t( 'Apple ID Signed Out:', 'Apple ID サインアウト済み:' ); ?> <?php echo $apple_id_out ? '<span style="color:#16a34a; font-weight:bold;">✓ ' . esc_html( kaitori_t( 'Confirmed', '確認済み' ) ) . '</span>' : '<span style="color:#dc2626;">' . esc_html( kaitori_t( 'Not Confirmed', '未確認' ) ) . '</span>'; ?><br>
                • <?php echo kaitori_t( 'No MDM / Corporate Profile Lock:', 'MDM・法人プロファイルロックなし:' ); ?> <?php echo $no_mdm_lock ? '<span style="color:#16a34a; font-weight:bold;">✓ ' . esc_html( kaitori_t( 'Confirmed', '確認済み' ) ) . '</span>' : '<span style="color:#dc2626;">' . esc_html( kaitori_t( 'Not Confirmed', '未確認' ) ) . '</span>'; ?>
            </td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Handover & Delivery Method', '受け渡し・配送方法' ); ?></label></th>
            <td>
                <strong><?php echo kaitori_t( 'Method:', '方法:' ); ?></strong> <?php echo esc_html($delivery_meth === 'store' ? kaitori_t( 'In-Store Drop-off (Akihabara Store)', '店頭持ち込み（秋葉原店）' ) : kaitori_t( 'Mail-in Buyback (Free Courier)', '郵送買取（送料無料）' ) ); ?><br>
                <strong><?php echo kaitori_t( 'Free Shipping Kit Needed:', '無料梱包キット希望:' ); ?></strong> <?php echo esc_html($shipping_kit ?: kaitori_t( 'Not Needed (Self Packed)', '不要（自己梱包）' ) ); ?><br>
                <strong><?php echo kaitori_t( 'Preferred Pickup / Visit Date:', '希望集荷・来店日:' ); ?></strong> <?php echo esc_html($pickup_dt ?: kaitori_t( 'Not Specified', '指定なし' ) ); ?>
            </td>
        </tr>
        <tr>
            <th><label><?php echo kaitori_t( 'Customer Bank Payout Details', 'お客様の振込先口座情報' ); ?></label></th>
            <td>
                <strong><?php echo kaitori_t( 'Bank Name:', '銀行名:' ); ?></strong> <?php echo esc_html($bank_name); ?><br>
                <strong><?php echo kaitori_t( 'Branch Name:', '支店名:' ); ?></strong> <?php echo esc_html($branch_name); ?><br>
                <strong><?php echo kaitori_t( 'Account Type:', '口座種別:' ); ?></strong> <?php echo esc_html($acc_type); ?><br>
                <strong><?php echo kaitori_t( 'Account Number:', '口座番号:' ); ?></strong>
                <?php $acc_mask = function_exists( 'kaitoriakiba_mask_account' ) ? kaitoriakiba_mask_account( $acc_number ) : $acc_number; ?>
                <span class="kaitori-acc-masked"><?php echo esc_html( $acc_mask ); ?></span>
                <span class="kaitori-acc-full" style="display:none;"><?php echo esc_html( $acc_number ); ?></span>
                <?php if ( $acc_number ) : ?>
                    <a href="#" class="kaitori-acc-reveal" style="font-size:11px; margin-left:6px;" onclick="var b=this.parentNode;b.querySelector('.kaitori-acc-masked').style.display='none';b.querySelector('.kaitori-acc-full').style.display='inline';this.style.display='none';return false;"><?php echo kaitori_t( 'Reveal', '表示' ); ?></a>
                <?php endif; ?>
                <br>
                <strong><?php echo kaitori_t( 'Account Holder (Katakana):', '口座名義（カナ）:' ); ?></strong> <?php echo esc_html($holder_kana); ?>
            </td>
        </tr>
    </table>
    <?php
}

function kaitoriakiba_save_order_meta( $post_id ) {
    if ( ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        return;
    }
    if ( isset( $_POST['_order_status'] ) ) {
        update_post_meta( $post_id, '_order_status', sanitize_text_field( $_POST['_order_status'] ) );
    }
    if ( isset( $_POST['_device_imei'] ) ) {
        update_post_meta( $post_id, '_device_imei', preg_replace( '/[^0-9A-Za-z]/', '', wp_unslash( $_POST['_device_imei'] ) ) );
    }
}
add_action( 'save_post_buyback_order', 'kaitoriakiba_save_order_meta' );

/**
 * Render eKYC Verification Meta Box (100% English Admin with Full Customer & Order Linking)
 */
function kaitoriakiba_kyc_meta_box_render( $post ) {
    $kyc_status   = get_post_meta( $post->ID, '_kyc_status', true ) ?: 'pending';
    $name         = get_post_meta( $post->ID, '_kyc_name', true );
    $kana         = get_post_meta( $post->ID, '_kyc_kana', true );
    $email        = get_post_meta( $post->ID, '_kyc_email', true );
    $phone        = get_post_meta( $post->ID, '_kyc_phone', true );
    $dob          = get_post_meta( $post->ID, '_kyc_dob', true );
    $job          = get_post_meta( $post->ID, '_kyc_job', true );
    $postal       = get_post_meta( $post->ID, '_kyc_postal', true );
    $pref         = get_post_meta( $post->ID, '_kyc_prefecture', true );
    $city         = get_post_meta( $post->ID, '_kyc_city', true );
    $address      = get_post_meta( $post->ID, '_kyc_address', true );
    $doc_type     = get_post_meta( $post->ID, '_kyc_doc_type', true ) ?: 'Driver License (運転免許証)';
    $order_code   = get_post_meta( $post->ID, '_kyc_order_code', true );
    $order_id     = get_post_meta( $post->ID, '_kyc_order_id', true );
    $user_id      = get_post_meta( $post->ID, '_kyc_user_id', true );
    // Prefer new secure files; fall back to legacy attachment ids/urls.
    $front_store  = get_post_meta( $post->ID, '_kyc_id_front_file', true ) ?: get_post_meta( $post->ID, '_kyc_id_front_attach', true );
    $back_store   = get_post_meta( $post->ID, '_kyc_id_back_file', true )  ?: get_post_meta( $post->ID, '_kyc_id_back_attach', true );
    $selfie_store = get_post_meta( $post->ID, '_kyc_selfie_file', true )   ?: get_post_meta( $post->ID, '_kyc_selfie_attach', true );
    $admin_notes  = get_post_meta( $post->ID, '_kyc_admin_notes', true );

    // Resolve through the permission-checked endpoint (new) or legacy resolver.
    $front_url  = function_exists( 'kaitoriakiba_kyc_resolve_src' ) ? kaitoriakiba_kyc_resolve_src( $front_store )  : $front_store;
    $back_url   = function_exists( 'kaitoriakiba_kyc_resolve_src' ) ? kaitoriakiba_kyc_resolve_src( $back_store )   : $back_store;
    $selfie_url = function_exists( 'kaitoriakiba_kyc_resolve_src' ) ? kaitoriakiba_kyc_resolve_src( $selfie_store ) : $selfie_store;

    // Auto-detect matching user if not explicitly linked
    if ( ! $user_id && ! empty( $email ) ) {
        $u = get_user_by( 'email', $email );
        if ( $u ) {
            $user_id = $u->ID;
            update_post_meta( $post->ID, '_kyc_user_id', $user_id );
        }
    }

    // Auto-detect matching order if not explicitly linked
    if ( ! $order_id && ! empty( $email ) ) {
        $orders = get_posts( array(
            'post_type'   => 'buyback_order',
            'meta_key'    => '_applicant_email',
            'meta_value'  => $email,
            'numberposts' => 1,
            'post_status' => 'any',
        ) );
        if ( ! empty( $orders ) ) {
            $order_id   = $orders[0]->ID;
            $order_code = $orders[0]->post_title;
            update_post_meta( $post->ID, '_kyc_order_id', $order_id );
            update_post_meta( $post->ID, '_kyc_order_code', $order_code );
        }
    }
    ?>
    <style>
        .kyc-meta-table th { width: 220px; font-size: 13.5px; color: #1e293b; padding: 14px 10px; vertical-align: top; }
        .kyc-meta-table td { font-size: 13.5px; padding: 14px 10px; vertical-align: top; }
        .kyc-box-panel { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 6px; }
        .kyc-doc-card { display: inline-block; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 10px; margin-right: 14px; margin-bottom: 10px; text-align: center; }
        .kyc-doc-card img { max-width: 260px; max-height: 180px; object-fit: contain; border-radius: 6px; display: block; margin-bottom: 8px; background: #f1f5f9; }
    </style>

    <table class="form-table kyc-meta-table">
        <!-- 1. Status Selection -->
        <tr style="background:#f8fafc;">
            <th><label><?php echo kaitori_t( 'Verification Status', '審査判定' ); ?></label></th>
            <td>
                <select name="_kyc_status" style="padding:8px 16px; font-weight:800; font-size:14px; border-radius:8px; border:2px solid <?php echo $kyc_status === 'verified' ? '#16a34a' : ( $kyc_status === 'rejected' ? '#dc2626' : '#f59e0b' ); ?>;">
                    <option value="pending" <?php selected($kyc_status, 'pending'); ?>><?php echo kaitori_t( '⏳ Pending Review', '⏳ 確認中・未承認' ); ?></option>
                    <option value="verified" <?php selected($kyc_status, 'verified'); ?>><?php echo kaitori_t( '✓ Verified & Approved', '✓ 承認完了・本人確認済' ); ?></option>
                    <option value="rejected" <?php selected($kyc_status, 'rejected'); ?>><?php echo kaitori_t( '✗ Rejected', '✗ 不備・再提出要請' ); ?></option>
                </select>
                <span style="margin-left: 10px; font-size: 12px; color: #64748b;">
                    <?php echo kaitori_t( 'When "Verified", customer and linked orders are automatically marked as verified.', '「承認」にすると、お客様および紐付く注文が自動的に本人確認済みになります。' ); ?>
                </span>
            </td>
        </tr>

        <!-- 2. Customer User Account Link -->
        <tr>
            <th><label><?php echo kaitori_t( 'Connected Customer Account', '紐付け会員アカウント' ); ?></label></th>
            <td>
                <?php if ( $user_id ) : 
                    $u_data = get_userdata( $user_id );
                ?>
                    <div class="kyc-box-panel" style="background:#eff6ff; border-color:#bfdbfe;">
                        <i class="dashicons dashicons-admin-users" style="color:#2563eb; font-size:20px; vertical-align:middle;"></i>
                        <strong><?php echo kaitori_t( 'Registered WordPress User:', '登録済み WordPress ユーザー:' ); ?></strong>
                        <span style="font-weight:700; color:#1e40af; margin-left:4px;">
                            <?php echo esc_html( $u_data->display_name ?: $u_data->user_login ); ?> (#<?php echo esc_html($user_id); ?>)
                        </span>
                        <div style="margin-top:6px; font-size:12.5px; color:#475569;">
                            Email: <code><?php echo esc_html( $u_data->user_email ); ?></code> | Registered: <?php echo esc_html( $u_data->user_registered ); ?>
                        </div>
                        <div style="margin-top:8px;">
                            <a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $user_id ) ); ?>" target="_blank" class="button button-secondary" style="font-weight:700;">
                                <i class="dashicons dashicons-external" style="vertical-align:middle;"></i> <?php echo kaitori_t( 'Open Customer User Profile &rarr;', 'お客様のユーザープロフィールを開く &rarr;' ); ?>
                            </a>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="kyc-box-panel" style="color:#64748b;">
                        <em><?php echo kaitori_t( 'Guest Customer (No WordPress account created yet with this email).', 'ゲストのお客様（このメールで作成された WordPress アカウントはまだありません）。' ); ?></em>
                    </div>
                <?php endif; ?>
            </td>
        </tr>

        <!-- 3. Connected Buyback Order(s) -->
        <tr>
            <th><label><?php echo kaitori_t( 'Connected Buyback Order', '紐付け買取申込' ); ?></label></th>
            <td>
                <?php if ( $order_id ) : 
                    $order_post = get_post( $order_id );
                    $order_model = get_post_meta( $order_id, '_device_model', true );
                    $order_quote = get_post_meta( $order_id, '_estimated_price', true );
                    $order_st    = get_post_meta( $order_id, '_order_status', true ) ?: 'pending';
                ?>
                    <div class="kyc-box-panel" style="background:#fef2f2; border-color:#fecaca;">
                        <i class="dashicons dashicons-clipboard" style="color:#ef4444; font-size:20px; vertical-align:middle;"></i>
                        <strong><?php echo kaitori_t( 'Application Order ID:', '申込注文ID:' ); ?></strong>
                        <span style="font-weight:800; color:#dc2626; margin-left:4px; font-size:14px;">
                            <?php echo esc_html( $order_code ?: ( $order_post ? $order_post->post_title : 'KB-Order' ) ); ?>
                        </span>
                        <div style="margin-top:4px; font-size:12.5px; color:#475569;">
                            Device: <strong><?php echo esc_html( $order_model ?: '—' ); ?></strong> | Quote: <strong style="color:#dc2626;">¥<?php echo number_format((int)$order_quote); ?></strong> | Status: <strong><?php echo esc_html( ucfirst($order_st) ); ?></strong>
                        </div>
                        <div style="margin-top:8px;">
                            <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ); ?>" target="_blank" class="button button-primary" style="background:#ef4444; border-color:#ef4444; font-weight:700;">
                                <i class="dashicons dashicons-visibility" style="vertical-align:middle;"></i> <?php echo kaitori_t( 'View Full Buyback Order Details &rarr;', '買取注文の詳細を表示 &rarr;' ); ?>
                            </a>
                        </div>
                    </div>
                <?php elseif ( ! empty( $order_code ) ) : ?>
                    <div class="kyc-box-panel">
                        <strong>Order Reference:</strong> <code><?php echo esc_html( $order_code ); ?></code>
                    </div>
                <?php else : ?>
                    <div class="kyc-box-panel" style="color:#64748b;">
                        <em><?php echo kaitori_t( 'Direct / In-store eKYC submission (no order code attached).', '直接・店頭での eKYC 提出（注文コードの紐付けなし）。' ); ?></em>
                    </div>
                <?php endif; ?>
            </td>
        </tr>

        <!-- 4. Applicant Identity Details -->
        <tr>
            <th><label><?php echo kaitori_t( 'Applicant Personal Details', '申請者の個人情報' ); ?></label></th>
            <td>
                <div style="line-height:1.8;">
                    <strong><?php echo kaitori_t( 'Full Name:', '氏名:' ); ?></strong> <span style="font-size:15px; font-weight:800; color:#0f172a;"><?php echo esc_html( $name ?: '—' ); ?></span>
                    <?php if ( $kana ) : ?>
                        <span style="color:#64748b; font-size:13px;">(<?php echo esc_html( $kana ); ?>)</span>
                    <?php endif; ?><br>
                    <strong><?php echo kaitori_t( 'Email Address:', 'メールアドレス:' ); ?></strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email ?: '—'); ?></a><br>
                    <strong><?php echo kaitori_t( 'Phone Number:', '電話番号:' ); ?></strong> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone ?: '—'); ?></a><br>
                    <strong><?php echo kaitori_t( 'Date of Birth:', '生年月日:' ); ?></strong> <?php echo esc_html($dob ?: '—'); ?><br>
                    <strong><?php echo kaitori_t( 'Occupation:', 'ご職業:' ); ?></strong> <?php echo esc_html($job ?: '—'); ?>
                </div>
            </td>
        </tr>

        <!-- 5. Current Residential Address -->
        <tr>
            <th><label><?php echo kaitori_t( 'Current Residential Address', '現住所' ); ?></label></th>
            <td>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; line-height:1.7;">
                    <strong><?php echo kaitori_t( 'Postal Code:', '郵便番号:' ); ?></strong> <?php echo esc_html($postal ? '〒' . $postal : '—'); ?><br>
                    <strong><?php echo kaitori_t( 'Address:', '住所:' ); ?></strong>
                    <span style="font-weight:700; color:#1e293b;">
                        <?php echo esc_html( $pref . ' ' . $city . ' ' . $address ); ?>
                    </span>
                    <div style="margin-top:6px;">
                        <a href="https://www.google.com/maps/search/<?php echo urlencode( $pref . ' ' . $city . ' ' . $address ); ?>" target="_blank" class="button button-small">
                            <i class="dashicons dashicons-location-alt" style="vertical-align:middle;"></i> <?php echo kaitori_t( 'Verify Address on Google Maps', 'Google マップで住所を確認' ); ?>
                        </a>
                    </div>
                </div>
            </td>
        </tr>

        <!-- 6. Uploaded ID Photos -->
        <tr>
            <th><label><?php echo kaitori_t( 'Document Photos & Evidence', '書類写真・証跡' ); ?></label></th>
            <td>
                <div style="margin-bottom:10px;">
                    <strong><?php echo kaitori_t( 'Document Type Submitted:', '提出された書類種別:' ); ?></strong>
                    <span style="display:inline-block; font-weight:800; color:#ef4444; background:#fef2f2; padding:3px 10px; border-radius:6px; font-size:13px;">
                        <i class="dashicons dashicons-id" style="vertical-align:middle;"></i> <?php echo esc_html( $doc_type ); ?>
                    </span>
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:16px;">
                    <?php if ( $front_url ) : ?>
                        <div class="kyc-doc-card">
                            <div style="font-weight:700; font-size:12px; margin-bottom:6px; color:#1e293b;"><?php echo kaitori_t( 'Front Side Photo', '表面の写真' ); ?></div>
                            <a href="<?php echo esc_url($front_url); ?>" target="_blank" title="Click to view full size">
                                <img src="<?php echo esc_url($front_url); ?>" alt="Front ID">
                            </a>
                            <a href="<?php echo esc_url($front_url); ?>" target="_blank" class="button button-small" style="width:100%;">
                                <i class="dashicons dashicons-search"></i> <?php echo kaitori_t( 'Open Full Image', '画像を拡大表示' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ( $back_url ) : ?>
                        <div class="kyc-doc-card">
                            <div style="font-weight:700; font-size:12px; margin-bottom:6px; color:#1e293b;"><?php echo kaitori_t( 'Back Side Photo', '裏面の写真' ); ?></div>
                            <a href="<?php echo esc_url($back_url); ?>" target="_blank" title="Click to view full size">
                                <img src="<?php echo esc_url($back_url); ?>" alt="Back ID">
                            </a>
                            <a href="<?php echo esc_url($back_url); ?>" target="_blank" class="button button-small" style="width:100%;">
                                <i class="dashicons dashicons-search"></i> <?php echo kaitori_t( 'Open Full Image', '画像を拡大表示' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ( $selfie_url ) : ?>
                        <div class="kyc-doc-card">
                            <div style="font-weight:700; font-size:12px; margin-bottom:6px; color:#1e293b;"><?php echo kaitori_t( 'Selfie / Face Photo', '本人顔写真' ); ?></div>
                            <a href="<?php echo esc_url($selfie_url); ?>" target="_blank" title="Click to view full size">
                                <img src="<?php echo esc_url($selfie_url); ?>" alt="Selfie">
                            </a>
                            <a href="<?php echo esc_url($selfie_url); ?>" target="_blank" class="button button-small" style="width:100%;">
                                <i class="dashicons dashicons-search"></i> <?php echo kaitori_t( 'Open Full Image', '画像を拡大表示' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! $front_url && ! $back_url && ! $selfie_url ) : ?>
                        <div style="color:#dc2626; font-style:italic;">
                            <?php echo kaitori_t( 'No photo attachments found for this submission.', 'この提出に写真の添付が見つかりませんでした。' ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <!-- 7. Reviewer Notes -->
        <tr>
            <th><label><?php echo kaitori_t( 'Reviewer Internal Notes', '審査者 社内メモ' ); ?></label></th>
            <td>
                <textarea name="_kyc_admin_notes" rows="4" style="width:100%; border:1.5px solid #cbd5e1; border-radius:6px; padding:8px;" placeholder="<?php echo esc_attr( kaitori_t( 'Enter verification remarks, reason for rejection, or compliance notes...', '審査コメント・却下理由・コンプライアンスメモを入力…' ) ); ?>"><?php echo esc_textarea($admin_notes); ?></textarea>
                <small style="color:#64748b;"><?php echo kaitori_t( 'Only visible to administrators and staff.', '管理者・スタッフのみ閲覧できます。' ); ?></small>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save eKYC Meta and Synchronize with Connected User & Order
 */
function kaitoriakiba_save_kyc_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['_kyc_status'] ) ) {
        $status = sanitize_text_field( $_POST['_kyc_status'] );
        update_post_meta( $post_id, '_kyc_status', $status );

        // Synchronize with User Account
        $user_id = get_post_meta( $post_id, '_kyc_user_id', true );
        if ( $user_id ) {
            update_user_meta( $user_id, '_kaitori_kyc_status', $status );
            update_user_meta( $user_id, '_kaitori_kyc_verified', $status === 'verified' ? 'yes' : 'no' );
        }

        // Synchronize with Buyback Order
        $order_id = get_post_meta( $post_id, '_kyc_order_id', true );
        if ( $order_id ) {
            update_post_meta( $order_id, '_order_kyc_status', $status );
        }
    }

    if ( isset( $_POST['_kyc_admin_notes'] ) ) {
        update_post_meta( $post_id, '_kyc_admin_notes', sanitize_textarea_field( $_POST['_kyc_admin_notes'] ) );
    }
}
add_action( 'save_post_kyc_record', 'kaitoriakiba_save_kyc_meta' );

/**
 * Add Kaitori Akiba eKYC & Order Info Section in WordPress User Profile (user-edit.php)
 */
function kaitoriakiba_user_profile_kyc_section( $user ) {
    $kyc_status    = get_user_meta( $user->ID, '_kaitori_kyc_status', true ) ?: 'unverified';
    $kyc_record_id = get_user_meta( $user->ID, '_kaitori_kyc_record_id', true );

    // If not directly saved in user meta, look up by user email
    if ( ! $kyc_record_id && $user->user_email ) {
        $found = get_posts( array(
            'post_type'   => 'kyc_record',
            'meta_key'    => '_kyc_email',
            'meta_value'  => $user->user_email,
            'numberposts' => 1,
            'post_status' => 'any',
        ) );
        if ( ! empty( $found ) ) {
            $kyc_record_id = $found[0]->ID;
            $kyc_status    = get_post_meta( $kyc_record_id, '_kyc_status', true ) ?: 'pending';
        }
    }

    // Look up user's buyback orders
    $user_orders = get_posts( array(
        'post_type'   => 'buyback_order',
        'meta_key'    => '_applicant_email',
        'meta_value'  => $user->user_email,
        'numberposts' => 10,
        'post_status' => 'any',
    ) );
    ?>
    <hr style="margin: 30px 0;">
    <h2><i class="dashicons dashicons-shield" style="color:#ef4444; vertical-align:middle;"></i> <?php echo kaitori_t( 'Kaitori Akiba Buyback Customer Profile & eKYC Verification', 'Kaitori Akiba 買取顧客プロフィール・eKYC 本人確認' ); ?></h2>

    <table class="form-table">
        <tr>
            <th><label><?php echo kaitori_t( 'eKYC Identity Verification Status', 'eKYC 本人確認ステータス' ); ?></label></th>
            <td>
                <?php if ( $kyc_status === 'verified' ) : ?>
                    <span style="background:#dcfce7; color:#15803d; font-weight:800; padding:6px 14px; border-radius:12px; font-size:13px;">
                        ✓ <?php echo kaitori_t( 'Verified (Identity Document Approved)', '承認済み（本人確認書類 承認済）' ); ?>
                    </span>
                <?php elseif ( $kyc_status === 'pending' ) : ?>
                    <span style="background:#fef3c7; color:#b45309; font-weight:800; padding:6px 14px; border-radius:12px; font-size:13px;">
                        ⏳ <?php echo kaitori_t( 'Pending Review (Documents Uploaded)', '確認中（書類アップロード済）' ); ?>
                    </span>
                <?php elseif ( $kyc_status === 'rejected' ) : ?>
                    <span style="background:#fee2e2; color:#b91c1c; font-weight:800; padding:6px 14px; border-radius:12px; font-size:13px;">
                        ✗ <?php echo kaitori_t( 'Rejected (Re-submission Required)', '却下（再提出が必要）' ); ?>
                    </span>
                <?php else : ?>
                    <span style="background:#f1f5f9; color:#64748b; font-weight:700; padding:6px 14px; border-radius:12px; font-size:13px;">
                        <?php echo kaitori_t( 'Not Submitted Yet', '未提出' ); ?>
                    </span>
                <?php endif; ?>

                <?php if ( $kyc_record_id ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $kyc_record_id . '&action=edit' ) ); ?>" class="button button-primary" target="_blank" style="margin-left:14px; font-weight:700;">
                        <i class="dashicons dashicons-id-alt" style="vertical-align:middle;"></i> <?php echo kaitori_t( 'View / Inspect Customer\'s eKYC Document &rarr;', 'お客様の eKYC 書類を表示・審査 &rarr;' ); ?>
                    </a>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <th><label><?php echo kaitori_t( 'Customer\'s Buyback Orders', 'お客様の買取申込履歴' ); ?></label></th>
            <td>
                <?php if ( ! empty( $user_orders ) ) : ?>
                    <ul style="margin:0; padding-left:18px;">
                        <?php foreach ( $user_orders as $o ) :
                            $o_model = get_post_meta( $o->ID, '_device_model', true );
                            $o_price = get_post_meta( $o->ID, '_estimated_price', true );
                            $o_st    = get_post_meta( $o->ID, '_order_status', true ) ?: 'pending';
                        ?>
                            <li style="margin-bottom:8px;">
                                <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $o->ID . '&action=edit' ) ); ?>" target="_blank" style="font-weight:700; font-size:13.5px;">
                                    <?php echo esc_html( $o->post_title ); ?>
                                </a>
                                — <?php echo esc_html( $o_model ); ?> (¥<?php echo number_format((int)$o_price); ?>) [Status: <strong><?php echo esc_html( ucfirst($o_st) ); ?></strong>]
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <span style="color:#64748b;"><?php echo kaitori_t( 'No buyback orders found for this customer email.', 'このお客様のメールに該当する買取申込は見つかりませんでした。' ); ?></span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'kaitoriakiba_user_profile_kyc_section' );
add_action( 'edit_user_profile', 'kaitoriakiba_user_profile_kyc_section' );

