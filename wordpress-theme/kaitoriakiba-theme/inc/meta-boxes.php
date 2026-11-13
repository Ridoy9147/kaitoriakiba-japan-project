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
    if ( ( $hook === 'post-new.php' || $hook === 'post.php' ) && isset( $post->post_type ) && in_array( $post->post_type, array( 'kaitori_device', 'kyc_record', 'buyback_order' ), true ) ) {
        wp_enqueue_media();
    }
}
add_action( 'admin_enqueue_scripts', 'kaitoriakiba_admin_enqueue_media' );

/**
 * Enable multipart/form-data on post edit form for KYC documents & orders
 */
function kaitoriakiba_post_edit_form_multipart( $post ) {
    if ( $post && in_array( $post->post_type, array( 'kyc_record', 'buyback_order' ), true ) ) {
        echo ' enctype="multipart/form-data"';
    }
}
add_action( 'post_edit_form_tag', 'kaitoriakiba_post_edit_form_multipart' );

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
    wp_nonce_field( 'kaitori_order_meta_save', 'kaitori_order_meta_nonce' );

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
    $battery_health = get_post_meta( $post->ID, '_battery_health', true ) ?: '95';
    $powers_on      = get_post_meta( $post->ID, '_powers_on', true ) ?: 'yes';
    $face_touch_id  = get_post_meta( $post->ID, '_face_touch_id', true ) ?: 'yes';
    $camera_works   = get_post_meta( $post->ID, '_camera_works', true ) ?: 'yes';
    $water_history  = get_post_meta( $post->ID, '_water_damage_repair', true ) ?: 'none';
    $extra_issues   = get_post_meta( $post->ID, '_extra_issues', true );
    $find_my_off    = get_post_meta( $post->ID, '_find_my_off', true );
    $apple_id_out   = get_post_meta( $post->ID, '_apple_id_signout', true );
    $no_mdm_lock    = get_post_meta( $post->ID, '_no_mdm_lock', true );
    $installment_st = get_post_meta( $post->ID, '_installment_status', true ) ?: 'no';
    $installment_due= get_post_meta( $post->ID, '_installment_due_amount', true );
    $delivery_meth  = get_post_meta( $post->ID, '_delivery_method', true ) ?: 'mail';
    $pickup_dt      = get_post_meta( $post->ID, '_pickup_datetime', true );
    $shipping_kit   = get_post_meta( $post->ID, '_shipping_kit_needed', true ) ?: 'no';
    $bank_name      = get_post_meta( $post->ID, '_bank_name', true );
    $branch_name    = get_post_meta( $post->ID, '_bank_branch', true );
    $acc_type       = get_post_meta( $post->ID, '_bank_account_type', true ) ?: '普通';
    $acc_number     = function_exists( 'kaitoriakiba_decrypt' ) ? kaitoriakiba_decrypt( get_post_meta( $post->ID, '_bank_account_number', true ) ) : get_post_meta( $post->ID, '_bank_account_number', true );
    $holder_kana    = function_exists( 'kaitoriakiba_decrypt' ) ? kaitoriakiba_decrypt( get_post_meta( $post->ID, '_bank_holder_kana', true ) ) : get_post_meta( $post->ID, '_bank_holder_kana', true );
    $est_price      = get_post_meta( $post->ID, '_estimated_price', true );
    $accessories    = get_post_meta( $post->ID, '_accessories', true );
    ?>
    <style>
        .kaitori-admin-card { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; }
        .kaitori-admin-card h4 { margin: 0 0 12px 0; font-size: 14.5px; font-weight: 800; color: #1e293b; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .kaitori-admin-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
        .kaitori-admin-field { display: flex; flex-direction: column; gap: 4px; }
        .kaitori-admin-field label { font-size: 12.5px; font-weight: 700; color: #475569; }
        .kaitori-admin-field input, .kaitori-admin-field select, .kaitori-admin-field textarea { border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; font-size: 13px; }
        .kaitori-admin-field input:focus, .kaitori-admin-field select:focus, .kaitori-admin-field textarea:focus { border-color: #ef4444; outline: none; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2); }
    </style>

    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-left:4px solid #3b82f6; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:13px; color:#1e40af; line-height:1.5;">
        <strong><i class="dashicons dashicons-info" style="vertical-align:middle;"></i> <?php echo kaitori_t( 'Staff Management & Editing Notice:', '店舗スタッフ向け編集のご案内:' ); ?></strong><br>
        <?php echo kaitori_t( 'All customer, device, condition, and payout fields below are directly editable. If a customer entered incorrect information online or brings the device in-store for re-assessment, modify the details below and click the blue "Update" button on the right to save.', 'Web申込時にお客様が誤った情報を入力した場合や、店頭での実機確認後に査定額・状態・IMEI・振込先口座等を変更する場合は、以下の項目を直接修正して画面右上の「更新」ボタンをクリックしてください。' ); ?>
    </div>

    <?php if ( function_exists( 'kaitoriakiba_invoice_url' ) ) : ?>
        <div style="margin:4px 0 20px; padding:12px 16px; background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <strong style="color:#9a3412;"><span class="dashicons dashicons-media-document" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Buyback Invoice', '買取伝票' ) ); ?></strong>
            <a href="<?php echo esc_url( kaitoriakiba_invoice_url( $post->ID, true ) ); ?>" target="_blank" class="button button-primary" style="background:#ef4444; border-color:#ef4444; font-weight:700;">
                <span class="dashicons dashicons-printer" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Print / Save as PDF', '印刷 / PDF保存' ) ); ?>
            </a>
            <a href="<?php echo esc_url( kaitoriakiba_invoice_url( $post->ID, false ) ); ?>" target="_blank" class="button"><?php echo esc_html( kaitori_t( 'Preview (Shop + Customer copy)', 'プレビュー（店舗控え・お客様控え）' ) ); ?></a>
        </div>
    <?php endif; ?>

    <!-- 1. Order Status & Valuation -->
    <div class="kaitori-admin-card" style="background:linear-gradient(135deg, #fef2f2 0%, #fff 100%); border-color:#fecaca;">
        <h4><span class="dashicons dashicons-cart" style="color:#ef4444;"></span> <?php echo kaitori_t( 'Order Status & Final Valuation Amount', '注文ステータス・最終買取確定査定額' ); ?></h4>
        <div class="kaitori-admin-form-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Order Processing Status', '注文処理ステータス' ); ?></label>
                <select name="_order_status" style="padding:8px 14px; font-weight:bold; font-size:14px; border-radius:8px; border:2px solid #cbd5e1;">
                    <?php foreach ( kaitoriakiba_order_statuses() as $st_key => $st ) : ?>
                        <option value="<?php echo esc_attr( $st_key ); ?>" <?php selected( $order_status, $st_key ); ?>><?php echo esc_html( $st['label'] . ' (' . $st['label_ja'] . ')' ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Confirmed / Final Buyback Price (¥)', '買取金額（円）※実機確認後の確定金額' ); ?></label>
                <div style="position:relative;">
                    <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:900; font-size:16px; color:#ef4444;">¥</span>
                    <input type="number" name="_estimated_price" value="<?php echo esc_attr( $est_price ); ?>" style="padding-left:28px; font-size:18px; font-weight:900; color:#dc2626; width:100%;">
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Customer Information (Editable) -->
    <div class="kaitori-admin-card">
        <h4><span class="dashicons dashicons-admin-users" style="color:#3b82f6;"></span> <?php echo kaitori_t( 'Customer Contact Information (Editable)', 'お客様連絡先情報（編集可能）' ); ?></h4>
        <div class="kaitori-admin-form-grid">
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Full Name (Kanji)', '氏名（漢字）' ); ?></label>
                <input type="text" name="_applicant_name" value="<?php echo esc_attr( $name ); ?>" placeholder="山田 太郎">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Furigana (Katakana)', 'フリガナ（カタカナ）' ); ?></label>
                <input type="text" name="_applicant_kana" value="<?php echo esc_attr( $kana ); ?>" placeholder="ヤマダ タロウ">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Phone Number', '電話番号' ); ?></label>
                <input type="text" name="_applicant_phone" value="<?php echo esc_attr( $phone ); ?>" placeholder="09012345678">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Email Address', 'メールアドレス' ); ?></label>
                <input type="email" name="_applicant_email" value="<?php echo esc_attr( $email ); ?>" placeholder="user@example.com">
            </div>
        </div>

        <div style="margin-top:14px; padding-top:10px; border-top:1px dashed #e2e8f0;">
            <?php
            // Raw stored links are what we persist; detect-by-email is only a display fallback.
            $customer_user_id_raw = get_post_meta( $post->ID, '_customer_user_id', true );
            $kyc_id_raw           = get_post_meta( $post->ID, '_order_kyc_record_id', true );

            $customer_user_id = $customer_user_id_raw;
            if ( ! $customer_user_id && $email ) {
                $u = get_user_by( 'email', $email );
                if ( $u ) { $customer_user_id = $u->ID; }
            }
            $kyc_id = $kyc_id_raw;
            if ( ! $kyc_id && $email ) {
                $found_kyc = get_posts( array(
                    'post_type'   => 'kyc_record',
                    'meta_key'    => '_kyc_email',
                    'meta_value'  => $email,
                    'numberposts' => 1,
                    'post_status' => 'any',
                ) );
                if ( ! empty( $found_kyc ) ) { $kyc_id = $found_kyc[0]->ID; }
            }
            ?>
            <input type="hidden" name="_customer_user_id" id="kaitori_assign_user_id" value="<?php echo esc_attr( $customer_user_id_raw ); ?>">
            <input type="hidden" name="_order_kyc_record_id" id="kaitori_assign_kyc_id" value="<?php echo esc_attr( $kyc_id_raw ); ?>">

            <label style="font-weight:700; display:block; margin-bottom:6px;"><span class="dashicons dashicons-search" style="vertical-align:middle;"></span> <?php echo kaitori_t( 'Assign customer account &amp; eKYC (search by email)', 'お客様アカウント・eKYCを紐付け（メールで検索）' ); ?></label>
            <input type="text" id="kaitori_assign_q" class="regular-text" placeholder="<?php echo esc_attr( kaitori_t( 'Search by email…', 'メールアドレスで検索…' ) ); ?>" style="width:280px;">
            <button type="button" class="button" id="kaitori_assign_search"><?php echo kaitori_t( 'Search', '検索' ); ?></button>
            <p style="margin:4px 0 0; color:#64748b; font-size:12px;"><?php echo kaitori_t( 'Assigning a customer auto-links their eKYC (and vice-versa) when you save.', 'お客様を紐付けて保存するとeKYCも自動で連携されます（逆も同様）。' ); ?></p>
            <div id="kaitori_assign_results" style="margin-top:8px; max-width:560px;"></div>

            <div id="kaitori_assign_current" style="margin-top:12px; display:flex; gap:16px; flex-wrap:wrap; font-size:12.5px;">
                <?php if ( $customer_user_id ) :
                    $u_data = get_userdata( $customer_user_id );
                    if ( $u_data ) : ?>
                    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:6px 12px;">
                        <i class="dashicons dashicons-admin-users" style="color:#2563eb; vertical-align:middle;"></i>
                        <strong><?php echo kaitori_t( 'Linked WP Account:', '紐付け会員アカウント:' ); ?></strong>
                        <a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $customer_user_id ) ); ?>" target="_blank" style="font-weight:bold; color:#1d4ed8;">
                            <?php echo esc_html( $u_data->display_name ?: $u_data->user_login ); ?> (#<?php echo esc_html( $customer_user_id ); ?>)
                        </a>
                    </div>
                <?php endif; endif; ?>

                <?php if ( $kyc_id ) :
                    $k_status = get_post_meta( $kyc_id, '_kyc_status', true ) ?: 'pending'; ?>
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:6px 12px;">
                        <i class="dashicons dashicons-id-alt" style="color:#16a34a; vertical-align:middle;"></i>
                        <strong><?php echo kaitori_t( 'eKYC Status:', '本人確認状況:' ); ?></strong>
                        <span style="font-weight:800; color:#15803d;"><?php echo esc_html( ucfirst( $k_status ) ); ?></span>
                        <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $kyc_id . '&action=edit' ) ); ?>" target="_blank" style="margin-left:8px; text-decoration:underline; font-weight:bold;">
                            <?php echo kaitori_t( 'Review KYC Record &rarr;', '本人確認レコードを開く &rarr;' ); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <div style="background:#fefce8; border:1px solid #fef08a; border-radius:6px; padding:6px 12px; color:#854d0e;">
                        <i class="dashicons dashicons-warning" style="vertical-align:middle;"></i>
                        <?php echo kaitori_t( 'eKYC Record: Unverified / Not Uploaded Yet', 'eKYC: 未提出・未承認' ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <script>
            (function(){
                var $ = window.jQuery; if (!$ || typeof ajaxurl === 'undefined') return;
                var nonce = '<?php echo esc_js( wp_create_nonce( 'kaitori_walkin_lookup' ) ); ?>';
                var L = {
                    verified: '<?php echo esc_js( kaitori_t( 'eKYC verified', 'eKYC承認済' ) ); ?>',
                    pending:  '<?php echo esc_js( kaitori_t( 'eKYC pending', 'eKYC審査中' ) ); ?>',
                    rejected: '<?php echo esc_js( kaitori_t( 'eKYC rejected', 'eKYC不備' ) ); ?>',
                    nokyc:    '<?php echo esc_js( kaitori_t( 'no eKYC', 'eKYCなし' ) ); ?>',
                    short:    '<?php echo esc_js( kaitori_t( 'Type at least 2 characters.', '2文字以上入力してください。' ) ); ?>',
                    searching:'<?php echo esc_js( kaitori_t( 'Searching…', '検索中…' ) ); ?>',
                    fail:     '<?php echo esc_js( kaitori_t( 'Search failed.', '検索に失敗しました。' ) ); ?>',
                    none:     '<?php echo esc_js( kaitori_t( 'No matching customer.', '該当するお客様が見つかりません。' ) ); ?>',
                    assign:   '<?php echo esc_js( kaitori_t( 'Assign', '紐付け' ) ); ?>'
                };
                function esc(s){ return $('<div>').text(s==null?'':s).html(); }
                function col(st){ return st==='verified'?'#16a34a':(st==='rejected'?'#dc2626':(st?'#d97706':'#94a3b8')); }
                function run(){
                    var q = $.trim($('#kaitori_assign_q').val()||'');
                    if (q.length<2){ $('#kaitori_assign_results').html('<em style="color:#94a3b8;">'+L.short+'</em>'); return; }
                    $('#kaitori_assign_results').html(L.searching);
                    $.get(ajaxurl,{action:'kaitori_walkin_lookup',security:nonce,q:q},function(res){
                        if(!res||!res.success){ $('#kaitori_assign_results').html('<em style="color:#dc2626;">'+L.fail+'</em>'); return; }
                        var rows=(res.data&&res.data.results)||[];
                        if(!rows.length){ $('#kaitori_assign_results').html('<em style="color:#94a3b8;">'+L.none+'</em>'); return; }
                        var h='<ul style="list-style:none;margin:0;padding:0;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fff;">';
                        rows.forEach(function(r){
                            var kl=r.kyc_status?(L[r.kyc_status]||r.kyc_status):L.nokyc, kc=col(r.kyc_status);
                            h+='<li style="padding:8px 12px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;gap:10px;">'
                             +'<span><strong>'+esc(r.name)+'</strong> <span style="color:'+kc+';font-weight:700;font-size:12px;">● '+esc(kl)+'</span><br><span style="color:#64748b;font-size:12px;">'+esc(r.email)+'</span></span>'
                             +'<button type="button" class="button button-small kaitori-pick" data-uid="'+r.user_id+'" data-kid="'+r.kyc_id+'" data-name="'+esc(r.name)+'" data-email="'+esc(r.email)+'" data-kl="'+esc(kl)+'" data-kc="'+kc+'">'+L.assign+'</button>'
                             +'</li>';
                        });
                        h+='</ul>';
                        $('#kaitori_assign_results').html(h);
                    });
                }
                $(document).on('click','#kaitori_assign_search',run);
                $(document).on('keydown','#kaitori_assign_q',function(e){ if(e.which===13){ e.preventDefault(); run(); }});
                $(document).on('click','.kaitori-pick',function(){
                    var b=$(this);
                    $('#kaitori_assign_user_id').val(b.data('uid'));
                    $('#kaitori_assign_kyc_id').val(b.data('kid'));
                    if(!$('[name="_applicant_name"]').val()) $('[name="_applicant_name"]').val(b.data('name'));
                    if(!$('[name="_applicant_email"]').val()) $('[name="_applicant_email"]').val(b.data('email'));
                    $('#kaitori_assign_current').html('<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:6px 12px;color:#166534;"><strong>✔ '+esc(b.data('name'))+'</strong> ('+esc(b.data('email'))+') — <span style="color:'+b.data('kc')+';font-weight:700;">'+esc(b.data('kl'))+'</span></div>');
                    $('#kaitori_assign_results').html('');
                });
            })();
            </script>
        </div>
    </div>

    <!-- 3. Device Details & Identifiers (Editable) -->
    <div class="kaitori-admin-card">
        <h4><span class="dashicons dashicons-smartphone" style="color:#10b981;"></span> <?php echo kaitori_t( 'Device Details & IMEI / Serial (Editable)', '端末情報・IMEI・シリアル（編集可能）' ); ?></h4>
        <div class="kaitori-admin-form-grid">
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Device Model Name', '機種名（モデル）' ); ?></label>
                <input type="text" name="_device_model" value="<?php echo esc_attr( $model ); ?>" placeholder="iPhone 16 Pro">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Storage Capacity', 'ストレージ容量' ); ?></label>
                <input type="text" name="_device_storage" value="<?php echo esc_attr( $storage ); ?>" placeholder="256GB">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Device Color', '本体カラー' ); ?></label>
                <input type="text" name="_device_color" value="<?php echo esc_attr( $color ); ?>" placeholder="Desert Titanium">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Carrier / SIM Status', 'キャリア・SIM状態' ); ?></label>
                <input type="text" name="_device_carrier" value="<?php echo esc_attr( $carrier ); ?>" placeholder="SIMフリー">
            </div>
            <div class="kaitori-admin-field" style="background:#fef2f2; border:1px solid #fecaca; border-radius:6px; padding:8px;">
                <label style="color:#b91c1c; font-weight:800;"><?php echo kaitori_t( 'IMEI Number (15 Digits) — Searchable', 'IMEI番号（15桁・検索対象）' ); ?></label>
                <div style="display:flex; gap:6px;">
                    <input type="text" id="kaitori_order_imei" name="_device_imei" value="<?php echo esc_attr( $imei ); ?>" maxlength="15" style="font-family:monospace; font-weight:bold; font-size:14px; flex:1;">
                    <button type="button" class="button button-small kaitori-scan" data-order="<?php echo esc_attr( $post->ID ); ?>" data-field="imei">📷 <?php echo kaitori_t( 'Scan', 'スキャン' ); ?></button>
                </div>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Serial Number', 'シリアル番号' ); ?></label>
                <input type="text" name="_device_serial" value="<?php echo esc_attr( $serial ); ?>" placeholder="F2LDXXXX">
            </div>
        </div>
    </div>

    <!-- 4. Condition Checklist (Editable) -->
    <div class="kaitori-admin-card">
        <h4><span class="dashicons dashicons-yes" style="color:#f59e0b;"></span> <?php echo kaitori_t( 'Condition & Inspection Checklist (Editable)', '状態・動作検品チェック項目（編集可能）' ); ?></h4>
        <div class="kaitori-admin-form-grid">
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Screen Condition', '画面の状態' ); ?></label>
                <input type="text" name="_screen_condition" value="<?php echo esc_attr( $screen_cond ); ?>" placeholder="傷なし・美品 (Sランク/Aランク)">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Body & Frame Condition', '外装・フレームの状態' ); ?></label>
                <input type="text" name="_body_condition" value="<?php echo esc_attr( $body_cond ); ?>" placeholder="目立つ傷なし (Flawless)">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Battery Health (%)', 'バッテリー最大容量 (%)' ); ?></label>
                <input type="number" name="_battery_health" value="<?php echo esc_attr( $battery_health ); ?>" min="50" max="100">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Powers On Correctly', '電源・正常起動' ); ?></label>
                <select name="_powers_on">
                    <option value="yes" <?php selected($powers_on, 'yes'); ?>><?php echo kaitori_t( 'Yes - Normal', '正常に起動する' ); ?></option>
                    <option value="no" <?php selected($powers_on, 'no'); ?>><?php echo kaitori_t( 'No - Does not boot', '起動しない' ); ?></option>
                </select>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Biometrics (Face ID / Touch ID)', '生体認証' ); ?></label>
                <select name="_face_touch_id">
                    <option value="yes" <?php selected($face_touch_id, 'yes'); ?>><?php echo kaitori_t( 'Yes - Works', '正常作動' ); ?></option>
                    <option value="no" <?php selected($face_touch_id, 'no'); ?>><?php echo kaitori_t( 'No - Unusable / Faulty', '使用不可・不良' ); ?></option>
                </select>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Camera Operation', 'カメラ動作' ); ?></label>
                <select name="_camera_works">
                    <option value="yes" <?php selected($camera_works, 'yes'); ?>><?php echo kaitori_t( 'Yes - Normal', '正常作動' ); ?></option>
                    <option value="no" <?php selected($camera_works, 'no'); ?>><?php echo kaitori_t( 'No - Spots / Shake / Faulty', '斑点・手ぶれ不良' ); ?></option>
                </select>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Water Damage / Past Repairs', '水没・修理歴' ); ?></label>
                <input type="text" name="_water_damage_repair" value="<?php echo esc_attr( $water_history ); ?>" placeholder="none">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Included Accessories', '付属品' ); ?></label>
                <input type="text" name="_accessories_text" value="<?php echo esc_attr( is_array($accessories) ? implode(', ', $accessories) : $accessories ); ?>" placeholder="箱、ケーブル、説明書">
            </div>
        </div>
        <div class="kaitori-admin-field" style="margin-top:12px;">
            <label><?php echo kaitori_t( 'Other Reported Issues / Staff Inspection Notes', 'その他の申告事項・検品備考' ); ?></label>
            <textarea name="_extra_issues" rows="2" style="width:100%;"><?php echo esc_textarea( $extra_issues ); ?></textarea>
        </div>
    </div>

    <!-- 5. Bank Account & Payout Details (Editable) -->
    <div class="kaitori-admin-card">
        <h4><span class="dashicons dashicons-bank" style="color:#6366f1;"></span> <?php echo kaitori_t( 'Customer Payout Bank Account (Editable & Encrypted at rest)', 'お振込先銀行口座情報（編集可能・暗号化保管）' ); ?></h4>
        <div class="kaitori-admin-form-grid">
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Bank Name', '金融機関名（銀行名）' ); ?></label>
                <input type="text" name="_bank_name" value="<?php echo esc_attr( $bank_name ); ?>" placeholder="三菱UFJ銀行">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Branch Name', '支店名' ); ?></label>
                <input type="text" name="_bank_branch" value="<?php echo esc_attr( $branch_name ); ?>" placeholder="秋葉原支店">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Account Type', '口座種別' ); ?></label>
                <select name="_bank_account_type">
                    <option value="普通" <?php selected($acc_type, '普通'); ?>>普通 (Ordinary)</option>
                    <option value="当座" <?php selected($acc_type, '当座'); ?>>当座 (Current)</option>
                </select>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Account Number (7 digits)', '口座番号（7桁）' ); ?></label>
                <input type="text" name="_bank_account_number" value="<?php echo esc_attr( $acc_number ); ?>" placeholder="1234567">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Account Holder (Katakana)', '口座名義（カタカナ）' ); ?></label>
                <input type="text" name="_bank_holder_kana" value="<?php echo esc_attr( $holder_kana ); ?>" placeholder="ヤマダ タロウ">
            </div>
        </div>
    </div>

    <!-- 6. Delivery & Security Details (Editable) -->
    <div class="kaitori-admin-card">
        <h4><span class="dashicons dashicons-car" style="color:#0ea5e9;"></span> <?php echo kaitori_t( 'Delivery, Installment & Security Verification', '受取方法・分割残債・セキュリティ確認' ); ?></h4>
        <div class="kaitori-admin-form-grid">
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Delivery / Handover Method', '受取・引渡方法' ); ?></label>
                <select name="_delivery_method">
                    <option value="mail" <?php selected($delivery_meth, 'mail'); ?>><?php echo kaitori_t( 'Mail-in Delivery (郵送)', '郵送買取（送料無料）' ); ?></option>
                    <option value="store" <?php selected($delivery_meth, 'store'); ?>><?php echo kaitori_t( 'In-Store Drop-off (店頭)', '店頭持込（秋葉原店）' ); ?></option>
                </select>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Free Packing Kit Needed', '無料梱包キット希望' ); ?></label>
                <select name="_shipping_kit_needed">
                    <option value="no" <?php selected($shipping_kit, 'no'); ?>><?php echo kaitori_t( 'Not Needed (不要)', '不要（自己梱包）' ); ?></option>
                    <option value="yes" <?php selected($shipping_kit, 'yes'); ?>><?php echo kaitori_t( 'Kit Requested (希望する)', '希望する' ); ?></option>
                </select>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Pickup / Store Visit Datetime', '集荷・来店希望日時' ); ?></label>
                <input type="text" name="_pickup_datetime" value="<?php echo esc_attr( $pickup_dt ); ?>" placeholder="例: 2026-11-10 14:00">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Carrier Installment Status', '分割残債状況' ); ?></label>
                <select name="_installment_status">
                    <option value="no" <?php selected($installment_st, 'no'); ?>><?php echo kaitori_t( 'Fully Paid Off (完済 ◯)', '完済（残債なし ◯）' ); ?></option>
                    <option value="yes" <?php selected($installment_st, 'yes'); ?>><?php echo kaitori_t( 'Under Installment (残債あり △)', '分割支払い中（残債あり △）' ); ?></option>
                </select>
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'Remaining Due Balance (¥)', '分割残債金額（円）' ); ?></label>
                <input type="number" name="_installment_due_amount" value="<?php echo esc_attr( $installment_due ); ?>" placeholder="0">
            </div>
            <div class="kaitori-admin-field">
                <label><?php echo kaitori_t( 'iCloud Lock / Find My Disabled', '「探す」ロック解除' ); ?></label>
                <select name="_find_my_off">
                    <option value="1" <?php selected($find_my_off, 1); ?>><?php echo kaitori_t( '✓ Confirmed (解除済み)', '解除済み' ); ?></option>
                    <option value="0" <?php selected($find_my_off, 0); ?>><?php echo kaitori_t( '✗ Not Confirmed (未解除)', '未解除・要確認' ); ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php
}

function kaitoriakiba_save_order_meta( $post_id ) {
    // Verify the meta-box nonce first (CSRF protection); bail if absent/invalid.
    if ( ! isset( $_POST['kaitori_order_meta_nonce'] ) || ! wp_verify_nonce( $_POST['kaitori_order_meta_nonce'], 'kaitori_order_meta_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( kaitoriakiba_manage_cap() ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $text_fields = array(
        '_order_status',
        '_applicant_name',
        '_applicant_kana',
        '_applicant_phone',
        '_device_model',
        '_device_storage',
        '_device_color',
        '_device_carrier',
        '_device_serial',
        '_screen_condition',
        '_body_condition',
        '_powers_on',
        '_face_touch_id',
        '_camera_works',
        '_water_damage_repair',
        '_delivery_method',
        '_pickup_datetime',
        '_shipping_kit_needed',
        '_installment_status',
        '_bank_name',
        '_bank_branch',
        '_bank_account_type',
    );

    foreach ( $text_fields as $tf ) {
        if ( isset( $_POST[ $tf ] ) ) {
            update_post_meta( $post_id, $tf, sanitize_text_field( wp_unslash( $_POST[ $tf ] ) ) );
        }
    }

    if ( isset( $_POST['_applicant_email'] ) ) {
        update_post_meta( $post_id, '_applicant_email', sanitize_email( wp_unslash( $_POST['_applicant_email'] ) ) );
    }

    if ( isset( $_POST['_device_imei'] ) ) {
        update_post_meta( $post_id, '_device_imei', preg_replace( '/[^0-9A-Za-z]/', '', wp_unslash( $_POST['_device_imei'] ) ) );
    }

    if ( isset( $_POST['_estimated_price'] ) ) {
        update_post_meta( $post_id, '_estimated_price', intval( $_POST['_estimated_price'] ) );
    }

    if ( isset( $_POST['_battery_health'] ) ) {
        update_post_meta( $post_id, '_battery_health', intval( $_POST['_battery_health'] ) );
    }

    if ( isset( $_POST['_installment_due_amount'] ) ) {
        update_post_meta( $post_id, '_installment_due_amount', intval( $_POST['_installment_due_amount'] ) );
    }

    if ( isset( $_POST['_find_my_off'] ) ) {
        update_post_meta( $post_id, '_find_my_off', intval( $_POST['_find_my_off'] ) );
    }

    if ( isset( $_POST['_extra_issues'] ) ) {
        update_post_meta( $post_id, '_extra_issues', sanitize_textarea_field( wp_unslash( $_POST['_extra_issues'] ) ) );
    }

    if ( isset( $_POST['_accessories_text'] ) ) {
        $acc_raw = sanitize_text_field( wp_unslash( $_POST['_accessories_text'] ) );
        $acc_arr = array_filter( array_map( 'trim', explode( ',', $acc_raw ) ) );
        update_post_meta( $post_id, '_accessories', $acc_arr );
    }

    // Encrypt financial PII at rest
    if ( isset( $_POST['_bank_account_number'] ) ) {
        $clean_num = sanitize_text_field( wp_unslash( $_POST['_bank_account_number'] ) );
        update_post_meta( $post_id, '_bank_account_number', function_exists( 'kaitoriakiba_encrypt' ) ? kaitoriakiba_encrypt( $clean_num ) : $clean_num );
    }
    if ( isset( $_POST['_bank_holder_kana'] ) ) {
        $clean_holder = sanitize_text_field( wp_unslash( $_POST['_bank_holder_kana'] ) );
        update_post_meta( $post_id, '_bank_holder_kana', function_exists( 'kaitoriakiba_encrypt' ) ? kaitoriakiba_encrypt( $clean_holder ) : $clean_holder );
    }

    // --- Customer account / eKYC assignment (two-way: either fills the other) ---
    if ( isset( $_POST['_customer_user_id'] ) || isset( $_POST['_order_kyc_record_id'] ) ) {
        $assign_user = isset( $_POST['_customer_user_id'] ) ? (int) $_POST['_customer_user_id'] : 0;
        $assign_kyc  = isset( $_POST['_order_kyc_record_id'] ) ? (int) $_POST['_order_kyc_record_id'] : 0;

        if ( $assign_user && ! $assign_kyc && function_exists( 'kaitoriakiba_find_user_kyc_record' ) ) {
            $assign_kyc = kaitoriakiba_find_user_kyc_record( $assign_user );
        }
        if ( $assign_kyc && ! $assign_user ) {
            $assign_user = (int) get_post_meta( $assign_kyc, '_kyc_user_id', true );
        }

        update_post_meta( $post_id, '_customer_user_id', $assign_user );

        if ( $assign_kyc && 'kyc_record' === get_post_type( $assign_kyc ) ) {
            update_post_meta( $post_id, '_order_kyc_record_id', $assign_kyc );
            update_post_meta( $post_id, '_order_kyc_status', get_post_meta( $assign_kyc, '_kyc_status', true ) ?: 'pending' );
            if ( ! get_post_meta( $assign_kyc, '_kyc_order_id', true ) ) {
                update_post_meta( $assign_kyc, '_kyc_order_id', $post_id );
            }
        } else {
            update_post_meta( $post_id, '_order_kyc_record_id', $assign_kyc );
        }
    }

    // --- Auto-generate the application ID (post title) for manually-added orders ---
    $cur = get_post( $post_id );
    if ( $cur && ( '' === trim( (string) $cur->post_title ) || 'Auto Draft' === $cur->post_title ) ) {
        $m = get_post_meta( $post_id, '_device_model', true );
        $s = get_post_meta( $post_id, '_device_storage', true );
        $auto_title = 'KB-' . date( 'Ymd' ) . '-' . wp_rand( 1000, 9999 ) . ' [' . trim( $m . ' ' . $s ) . ']';
        remove_action( 'save_post_buyback_order', 'kaitoriakiba_save_order_meta' );
        wp_update_post( array( 'ID' => $post_id, 'post_title' => $auto_title ) );
        add_action( 'save_post_buyback_order', 'kaitoriakiba_save_order_meta' );
    }
}
add_action( 'save_post_buyback_order', 'kaitoriakiba_save_order_meta' );

/**
 * Render eKYC Verification Meta Box (Editable & Reviewable)
 */
function kaitoriakiba_kyc_meta_box_render( $post ) {
    wp_nonce_field( 'kaitori_kyc_meta_save', 'kaitori_kyc_meta_nonce' );

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

    $front_store  = get_post_meta( $post->ID, '_kyc_id_front_file', true ) ?: get_post_meta( $post->ID, '_kyc_id_front_attach', true );
    $back_store   = get_post_meta( $post->ID, '_kyc_id_back_file', true )  ?: get_post_meta( $post->ID, '_kyc_id_back_attach', true );
    $selfie_store = get_post_meta( $post->ID, '_kyc_selfie_file', true )   ?: get_post_meta( $post->ID, '_kyc_selfie_attach', true );
    $admin_notes  = get_post_meta( $post->ID, '_kyc_admin_notes', true );

    $front_url  = function_exists( 'kaitoriakiba_kyc_resolve_src' ) ? kaitoriakiba_kyc_resolve_src( $front_store )  : $front_store;
    $back_url   = function_exists( 'kaitoriakiba_kyc_resolve_src' ) ? kaitoriakiba_kyc_resolve_src( $back_store )   : $back_store;
    $selfie_url = function_exists( 'kaitoriakiba_kyc_resolve_src' ) ? kaitoriakiba_kyc_resolve_src( $selfie_store ) : $selfie_store;

    if ( ! $user_id && ! empty( $email ) ) {
        $u = get_user_by( 'email', $email );
        if ( $u ) {
            $user_id = $u->ID;
            update_post_meta( $post->ID, '_kyc_user_id', $user_id );
        }
    }

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
        .kyc-admin-card { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; }
        .kyc-admin-card h4 { margin: 0 0 12px 0; font-size: 14.5px; font-weight: 800; color: #1e293b; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .kyc-admin-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
        .kyc-admin-field { display: flex; flex-direction: column; gap: 4px; }
        .kyc-admin-field label { font-size: 12.5px; font-weight: 700; color: #475569; }
        .kyc-admin-field input, .kyc-admin-field select, .kyc-admin-field textarea { border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; font-size: 13px; }
        .kyc-admin-field input:focus, .kyc-admin-field select:focus, .kyc-admin-field textarea:focus { border-color: #ef4444; outline: none; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2); }
        .kyc-doc-card { display: inline-block; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 10px; margin-right: 14px; margin-bottom: 10px; text-align: center; }
        .kyc-doc-card img { max-width: 260px; max-height: 180px; object-fit: contain; border-radius: 6px; display: block; margin-bottom: 8px; background: #f1f5f9; }
    </style>

    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-left:4px solid #3b82f6; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:13px; color:#1e40af; line-height:1.5;">
        <strong><i class="dashicons dashicons-info" style="vertical-align:middle;"></i> <?php echo kaitori_t( 'Staff Management & Editing Notice:', '店舗スタッフ向け本人確認編集のご案内:' ); ?></strong><br>
        <?php echo kaitori_t( 'Customer personal details and address below can be directly edited and corrected. If a customer visits the store with their original ID and needs corrections, update the fields below and click "Update".', 'お客様がWebから提出した情報に誤りがある場合や、店頭で原本書類を確認して修正・更新が必要な場合は、以下の各項目（氏名・住所・生年月日等）を直接編集して「更新」ボタンをクリックしてください。' ); ?>
    </div>

    <!-- 1. Verification Decision -->
    <div class="kyc-admin-card" style="background:<?php echo $kyc_status === 'verified' ? '#f0fdf4' : ( $kyc_status === 'rejected' ? '#fef2f2' : '#fffbeb' ); ?>; border-color:<?php echo $kyc_status === 'verified' ? '#bbf7d0' : ( $kyc_status === 'rejected' ? '#fecaca' : '#fde68a' ); ?>;">
        <h4><span class="dashicons dashicons-shield"></span> <?php echo kaitori_t( 'Verification Decision (審査判定)', '本人確認審査ステータス' ); ?></h4>
        <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
            <select name="_kyc_status" style="padding:8px 16px; font-weight:800; font-size:14px; border-radius:8px; border:2px solid <?php echo $kyc_status === 'verified' ? '#16a34a' : ( $kyc_status === 'rejected' ? '#dc2626' : '#f59e0b' ); ?>;">
                <option value="pending" <?php selected($kyc_status, 'pending'); ?>><?php echo kaitori_t( '⏳ Pending Review', '⏳ 確認中・未承認' ); ?></option>
                <option value="verified" <?php selected($kyc_status, 'verified'); ?>><?php echo kaitori_t( '✓ Verified & Approved', '✓ 承認完了・本人確認済' ); ?></option>
                <option value="rejected" <?php selected($kyc_status, 'rejected'); ?>><?php echo kaitori_t( '✗ Rejected (Invalid)', '✗ 不備・再提出要請' ); ?></option>
            </select>
            <span style="font-size: 12.5px; color: #475569;">
                <?php echo kaitori_t( 'When set to "Verified", the customer user account and all connected buyback orders are automatically marked as verified.', '「承認完了」に設定すると、お客様の会員アカウントおよび紐付く買取注文が自動的に本人確認完了ステータスに同期されます。' ); ?>
            </span>
        </div>
    </div>

    <!-- 2. Personal Details (Editable) -->
    <div class="kyc-admin-card">
        <h4><span class="dashicons dashicons-id-alt" style="color:#ef4444;"></span> <?php echo kaitori_t( 'Applicant Identity Details (Editable)', '申請者の本人情報（編集可能）' ); ?></h4>
        <div class="kyc-admin-grid">
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Full Name (Kanji)', '氏名（漢字）' ); ?></label>
                <input type="text" name="_kyc_name" value="<?php echo esc_attr( $name ); ?>" placeholder="山田 太郎">
            </div>
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Furigana (Katakana)', 'フリガナ（カタカナ）' ); ?></label>
                <input type="text" name="_kyc_kana" value="<?php echo esc_attr( $kana ); ?>" placeholder="ヤマダ タロウ">
            </div>
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Email Address', 'メールアドレス' ); ?></label>
                <input type="email" name="_kyc_email" value="<?php echo esc_attr( $email ); ?>" placeholder="user@example.com">
            </div>
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Phone Number', '電話番号' ); ?></label>
                <input type="text" name="_kyc_phone" value="<?php echo esc_attr( $phone ); ?>" placeholder="09012345678">
            </div>
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Date of Birth', '生年月日' ); ?></label>
                <input type="date" name="_kyc_dob" value="<?php echo esc_attr( $dob ); ?>">
            </div>
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Occupation', 'ご職業' ); ?></label>
                <input type="text" name="_kyc_job" value="<?php echo esc_attr( $job ); ?>" placeholder="会社員">
            </div>
        </div>
    </div>

    <!-- 3. Current Residential Address (Editable) -->
    <div class="kyc-admin-card">
        <h4><span class="dashicons dashicons-location" style="color:#f59e0b;"></span> <?php echo kaitori_t( 'Current Residential Address (Editable)', '現住所情報（編集可能）' ); ?></h4>
        <div class="kyc-admin-grid">
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Postal Code', '郵便番号' ); ?></label>
                <input type="text" name="_kyc_postal" value="<?php echo esc_attr( $postal ); ?>" placeholder="101-0021">
            </div>
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Prefecture', '都道府県' ); ?></label>
                <input type="text" name="_kyc_prefecture" value="<?php echo esc_attr( $pref ); ?>" placeholder="東京都">
            </div>
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'City / Ward', '市区町村' ); ?></label>
                <input type="text" name="_kyc_city" value="<?php echo esc_attr( $city ); ?>" placeholder="千代田区">
            </div>
            <div class="kyc-admin-field" style="grid-column: span 2;">
                <label><?php echo kaitori_t( 'Street Address, Building & Room', '番地・建物名・部屋番号' ); ?></label>
                <input type="text" name="_kyc_address" value="<?php echo esc_attr( $address ); ?>" placeholder="外神田1-2-3 アキバビル4F">
            </div>
        </div>
        <?php if ( $pref || $city || $address ) : ?>
            <div style="margin-top:10px;">
                <a href="https://www.google.com/maps/search/<?php echo urlencode( $pref . ' ' . $city . ' ' . $address ); ?>" target="_blank" class="button button-small">
                    <i class="dashicons dashicons-location-alt" style="vertical-align:middle;"></i> <?php echo kaitori_t( 'Verify Address on Google Maps &rarr;', 'Google マップで住所を照合 &rarr;' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 4. Document Type & Order Reference -->
    <div class="kyc-admin-card">
        <h4><span class="dashicons dashicons-media-document" style="color:#8b5cf6;"></span> <?php echo kaitori_t( 'Document Type & Order Association (Editable)', '書類種別・紐付け注文' ); ?></h4>
        <div class="kyc-admin-grid">
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Document Type', '提出書類の種別' ); ?></label>
                <select name="_kyc_doc_type">
                    <option value="運転免許証" <?php selected($doc_type, '運転免許証'); ?>>運転免許証 (Driver License)</option>
                    <option value="マイナンバーカード" <?php selected($doc_type, 'マイナンバーカード'); ?>>マイナンバーカード (My Number Card)</option>
                    <option value="在留カード" <?php selected($doc_type, '在留カード'); ?>>在留カード (Residence Card)</option>
                    <option value="パスポート" <?php selected($doc_type, 'パスポート'); ?>>パスポート (Passport)</option>
                    <option value="その他" <?php selected($doc_type, 'その他'); ?>>その他 (Other ID)</option>
                </select>
            </div>
            <div class="kyc-admin-field">
                <label><?php echo kaitori_t( 'Linked Order Code / Reference', '紐付け買取受付番号' ); ?></label>
                <input type="text" name="_kyc_order_code" value="<?php echo esc_attr( $order_code ); ?>" placeholder="KB-20261107-1234">
            </div>
        </div>
    </div>

    <!-- 5. Document Images / Evidence (With Replace & Upload Feature) -->
    <div class="kyc-admin-card">
        <h4><span class="dashicons dashicons-camera" style="color:#ec4899;"></span> <?php echo kaitori_t( 'Uploaded Document Images & Evidence (Replace / Upload)', '本人確認書類の画像・証跡（差し替え・新規アップロード可能）' ); ?></h4>
        
        <p style="margin:0 0 16px; color:#64748b; font-size:13px; line-height:1.5;">
            <?php echo kaitori_t(
                'Staff can replace or upload clearer identity documents if the customer submitted wrong, blurry, or expired photos. Choose a file from your computer/device or select from the Media Library. After selecting files, click the blue "Update" button to save changes.',
                'お客様が提出した書類に不備（ピンボケ、有効期限切れ、別書類等）がある場合や、店頭で原本を確認した際は、こちらから正しい書類画像を差し替え・新規アップロードできます。ファイル選択後、必ず右上の青い「更新」ボタンをクリックして保存してください。'
            ); ?>
        </p>

        <style>
            .kyc-slot-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
                gap: 18px;
            }
            .kyc-slot-box {
                background: #f8fafc;
                border: 1.5px solid #cbd5e1;
                border-radius: 10px;
                padding: 16px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: relative;
            }
            .kyc-slot-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px;
                border-bottom: 1px solid #e2e8f0;
                padding-bottom: 8px;
            }
            .kyc-slot-title {
                font-size: 13.5px;
                font-weight: 800;
                color: #0f172a;
            }
            .kyc-slot-badge {
                font-size: 11px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 999px;
            }
            .kyc-slot-badge.has-file { background: #dcfce7; color: #15803d; }
            .kyc-slot-badge.no-file  { background: #f1f5f9; color: #64748b; }
            .kyc-preview-frame {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                min-height: 160px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                margin-bottom: 14px;
                padding: 8px;
                text-align: center;
            }
            .kyc-preview-frame img {
                max-width: 100%;
                max-height: 200px;
                object-fit: contain;
                border-radius: 4px;
            }
            .kyc-btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 10px;
            }
        </style>

        <div class="kyc-slot-grid">

            <?php
            $slots = array(
                'front' => array(
                    'title'    => kaitori_t( '1. Front Side Photo (表面の写真)', '1. 表面の写真（免許証・マイナンバー・在留カード等）' ),
                    'file_src' => $front_url,
                    'file_raw' => $front_store,
                    'desc'     => kaitori_t( 'Driver License front, My Number Card front, Residence Card, or Passport photo page.', '運転免許証表面、マイナンバーカード表面、在留カード表面、パスポート等' ),
                ),
                'back' => array(
                    'title'    => kaitori_t( '2. Back Side Photo (裏面の写真)', '2. 裏面の写真（住所変更欄・裏面事項）' ),
                    'file_src' => $back_url,
                    'file_raw' => $back_store,
                    'desc'     => kaitori_t( 'Back of Driver License, Residence Card, or Health Insurance card.', '運転免許証裏面（住所変更記載等）、在留カード裏面等' ),
                ),
                'selfie' => array(
                    'title'    => kaitori_t( '3. Thickness / Selfie Photo (厚み・セルフィー)', '3. 厚み・斜め写真 / セルフィー確認' ),
                    'file_src' => $selfie_url,
                    'file_raw' => $selfie_store,
                    'desc'     => kaitori_t( 'Optional card thickness angle shot or applicant selfie holding ID.', 'カードの厚み斜め撮影または顔写真付き本人撮影画像' ),
                ),
            );

            foreach ( $slots as $slot_key => $slot ) :
                $has_file = ! empty( $slot['file_src'] );
                $is_pdf   = $has_file && ( strtolower( substr( parse_url( $slot['file_src'], PHP_URL_PATH ) ?: '', -4 ) ) === '.pdf' || ( is_string( $slot['file_raw'] ) && strtolower( substr( $slot['file_raw'], -4 ) ) === '.pdf' ) );
            ?>
                <div class="kyc-slot-box">
                    <div>
                        <div class="kyc-slot-header">
                            <span class="kyc-slot-title"><?php echo esc_html( $slot['title'] ); ?></span>
                            <?php if ( $has_file ) : ?>
                                <span class="kyc-slot-badge has-file"><i class="dashicons dashicons-yes-alt" style="font-size:12px; vertical-align:middle;"></i> <?php echo esc_html( kaitori_t( 'Attached', '登録済' ) ); ?></span>
                            <?php else : ?>
                                <span class="kyc-slot-badge no-file"><?php echo esc_html( kaitori_t( 'Missing', '未登録' ) ); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="kyc-preview-frame" id="kyc_current_frame_<?php echo esc_attr( $slot_key ); ?>">
                            <?php if ( $has_file ) : ?>
                                <?php if ( $is_pdf ) : ?>
                                    <div style="padding:20px 10px;">
                                        <span class="dashicons dashicons-pdf" style="font-size:48px; width:48px; height:48px; color:#ef4444; margin-bottom:8px;"></span>
                                        <div style="font-weight:700; color:#1e293b; font-size:13px;"><?php echo kaitori_t( 'PDF Document Attached', 'PDF書類が登録されています' ); ?></div>
                                        <a href="<?php echo esc_url( $slot['file_src'] ); ?>" target="_blank" class="button button-small" style="margin-top:8px;">
                                            <i class="dashicons dashicons-external"></i> <?php echo kaitori_t( 'Open / View PDF &rarr;', 'PDFを開いて確認 &rarr;' ); ?>
                                        </a>
                                    </div>
                                <?php else : ?>
                                    <a href="<?php echo esc_url( $slot['file_src'] ); ?>" target="_blank" title="<?php echo esc_attr( kaitori_t( 'Click to view full image', 'クリックで拡大表示' ) ); ?>">
                                        <img src="<?php echo esc_url( $slot['file_src'] ); ?>" alt="<?php echo esc_attr( $slot_key ); ?>">
                                    </a>
                                    <div style="margin-top:6px;">
                                        <a href="<?php echo esc_url( $slot['file_src'] ); ?>" target="_blank" class="button button-small">
                                            <i class="dashicons dashicons-search"></i> <?php echo kaitori_t( 'Open Full Image', '拡大表示' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else : ?>
                                <div style="color:#94a3b8; padding:30px 10px;">
                                    <span class="dashicons dashicons-format-image" style="font-size:38px; width:38px; height:38px;"></span>
                                    <div style="font-size:12px; margin-top:4px;"><?php echo kaitori_t( 'No document on file', '書類がまだありません' ); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Live preview for newly selected replacement file -->
                        <div id="kyc_preview_<?php echo esc_attr( $slot_key ); ?>"></div>
                    </div>

                    <!-- Upload / Replace Controls -->
                    <div style="margin-top:12px; border-top:1px dashed #cbd5e1; padding-top:12px;">
                        <div style="font-size:12px; font-weight:700; color:#334155; margin-bottom:6px;">
                            <?php echo kaitori_t( 'Replace / Upload Document:', '書類の差し替え・新規アップロード:' ); ?>
                        </div>

                        <!-- Direct File Input (Hidden, triggered via button) -->
                        <input type="file" name="kyc_replace_<?php echo esc_attr( $slot_key ); ?>" id="kyc_file_input_<?php echo esc_attr( $slot_key ); ?>" accept="image/jpeg,image/png,image/webp,application/pdf" class="kyc-file-input" data-slot="<?php echo esc_attr( $slot_key ); ?>" style="display:none;">

                        <!-- Media Library Hidden Inputs -->
                        <input type="hidden" name="kyc_media_<?php echo esc_attr( $slot_key ); ?>_id" id="kyc_media_<?php echo esc_attr( $slot_key ); ?>_id" value="">
                        <input type="hidden" name="kyc_media_<?php echo esc_attr( $slot_key ); ?>_url" id="kyc_media_<?php echo esc_attr( $slot_key ); ?>_url" value="">

                        <div class="kyc-btn-group">
                            <button type="button" class="button button-secondary" onclick="document.getElementById('kyc_file_input_<?php echo esc_attr( $slot_key ); ?>').click();" style="font-weight:600;">
                                <span class="dashicons dashicons-upload" style="vertical-align:middle; font-size:16px;"></span> <?php echo kaitori_t( 'Choose File...', 'ファイル選択...' ); ?>
                            </button>
                            <button type="button" class="button button-secondary kyc-media-picker-btn" data-slot="<?php echo esc_attr( $slot_key ); ?>" style="font-weight:600;">
                                <span class="dashicons dashicons-admin-media" style="vertical-align:middle; font-size:16px;"></span> <?php echo kaitori_t( 'Media Library', 'メディアから選択' ); ?>
                            </button>
                        </div>

                        <?php if ( $has_file ) : ?>
                            <label style="font-size:12px; color:#dc2626; display:inline-flex; align-items:center; gap:5px; margin-top:4px; cursor:pointer;">
                                <input type="checkbox" name="kyc_remove_<?php echo esc_attr( $slot_key ); ?>" value="1">
                                <?php echo kaitori_t( 'Remove this document', 'この書類を削除する' ); ?>
                            </label>
                        <?php endif; ?>

                        <div style="font-size:11px; color:#64748b; margin-top:6px; line-height:1.4;">
                            <?php echo esc_html( $slot['desc'] ); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- Client-side script for instant preview & Media Library -->
    <script>
    jQuery(document).ready(function($){
        // Direct file input change handler with format & size validation
        $('.kyc-file-input').on('change', function(){
            var input = this;
            var slot = $(input).data('slot');
            var container = $('#kyc_preview_' + slot);
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var ext = file.name.split('.').pop().toLowerCase();
                var allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
                var sizeMb = (file.size / (1024 * 1024)).toFixed(1);

                // Check file format
                if (allowedExts.indexOf(ext) === -1) {
                    input.value = '';
                    var errHtml = '<div style="margin-top:10px; padding:10px 12px; background:#fef2f2; border:1.5px solid #fecaca; border-radius:8px; font-size:12px; color:#dc2626; box-shadow:0 2px 6px rgba(0,0,0,0.04);">';
                    errHtml += '<div style="font-weight:800; display:flex; align-items:center; gap:6px;"><span class="dashicons dashicons-warning" style="color:#dc2626;"></span> ' + <?php echo json_encode( kaitori_t( 'Unsupported file format (.', '未対応のファイル形式です（.' ) ); ?> + ext + ')</div>';
                    errHtml += '<div style="margin-top:4px; color:#991b1b;">' + <?php echo json_encode( kaitori_t( 'Allowed formats: JPG, JPEG, PNG, WEBP, PDF (Max 10MB)', '許可されている形式: JPG, JPEG, PNG, WEBP, PDF（最大10MB）' ) ); ?> + '</div>';
                    errHtml += '</div>';
                    container.html(errHtml);
                    alert('⚠️ 許可されていないファイル形式です（.' + ext + '）。\n書類は JPG, PNG, WEBP, PDF 形式を選択してください。（最大10MB）');
                    return false;
                }

                // Check file size
                if (file.size > 10 * 1024 * 1024) {
                    input.value = '';
                    var errHtml = '<div style="margin-top:10px; padding:10px 12px; background:#fef2f2; border:1.5px solid #fecaca; border-radius:8px; font-size:12px; color:#dc2626; box-shadow:0 2px 6px rgba(0,0,0,0.04);">';
                    errHtml += '<div style="font-weight:800; display:flex; align-items:center; gap:6px;"><span class="dashicons dashicons-warning" style="color:#dc2626;"></span> ' + <?php echo json_encode( kaitori_t( 'File size exceeds limit (10MB): ', 'ファイルサイズ上限超過: ' ) ); ?> + sizeMb + ' MB</div>';
                    errHtml += '<div style="margin-top:4px; color:#991b1b;">' + <?php echo json_encode( kaitori_t( 'Please upload a file within 10MB.', '10MB以下のファイルを選択してください。' ) ); ?> + '</div>';
                    errHtml += '</div>';
                    container.html(errHtml);
                    alert('⚠️ ファイルサイズが上限(10MB)を超えています (' + sizeMb + 'MB)。');
                    return false;
                }

                var isPdf = (ext === 'pdf');
                var reader = new FileReader();
                reader.onload = function(e) {
                    var html = '<div style="margin-top:10px; padding:10px 12px; background:#f0fdf4; border:1.5px solid #86efac; border-radius:8px; font-size:12px; color:#166534; box-shadow:0 2px 6px rgba(0,0,0,0.04);">';
                    html += '<div style="font-weight:800; display:flex; align-items:center; gap:6px;"><span class="dashicons dashicons-yes-alt" style="color:#16a34a;"></span> ' + (isPdf ? 'New PDF Selected:' : 'New File Selected:') + '</div>';
                    html += '<div style="font-family:monospace; margin-top:2px;">' + file.name + ' (' + Math.round(file.size / 1024) + ' KB · ' + ext.toUpperCase() + ')</div>';
                    if (!isPdf) {
                        html += '<img src="' + e.target.result + '" style="max-width:180px; max-height:120px; object-fit:contain; border-radius:4px; margin-top:8px; display:block; border:1px solid #bbf7d0; background:#fff;">';
                    }
                    html += '<div style="color:#dc2626; font-weight:700; margin-top:6px; font-size:11.5px;">⚠️ ' + <?php echo json_encode( kaitori_t( 'Click "Update" above to save this replacement.', '右上の「更新」ボタンをクリックすると差し替えが完了します。' ) ); ?> + '</div>';
                    html += '</div>';
                    container.html(html);
                };
                reader.readAsDataURL(file);
            }
        });

        // WP Media Library modal
        $('.kyc-media-picker-btn').on('click', function(e){
            e.preventDefault();
            var btn = $(this);
            var slot = btn.data('slot');
            var frame = wp.media({
                title: <?php echo json_encode( kaitori_t( 'Select or Upload KYC Document', '本人確認書類を選択またはアップロード' ) ); ?>,
                button: { text: <?php echo json_encode( kaitori_t( 'Use This Document', 'この書類を適用する' ) ); ?> },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#kyc_media_' + slot + '_id').val(attachment.id);
                $('#kyc_media_' + slot + '_url').val(attachment.url);
                var isPdf = attachment.subtype === 'pdf' || (attachment.url && attachment.url.toLowerCase().endsWith('.pdf'));
                var html = '<div style="margin-top:10px; padding:10px 12px; background:#f0fdf4; border:1.5px solid #86efac; border-radius:8px; font-size:12px; color:#166534; box-shadow:0 2px 6px rgba(0,0,0,0.04);">';
                html += '<div style="font-weight:800; display:flex; align-items:center; gap:6px;"><span class="dashicons dashicons-yes-alt" style="color:#16a34a;"></span> ' + <?php echo json_encode( kaitori_t( 'Selected from Media Library:', 'メディアライブラリから選択:' ) ); ?> + '</div>';
                html += '<div style="font-family:monospace; margin-top:2px;">' + (attachment.filename || 'Document') + ' (#' + attachment.id + ')</div>';
                if (!isPdf && attachment.url) {
                    html += '<img src="' + attachment.url + '" style="max-width:180px; max-height:120px; object-fit:contain; border-radius:4px; margin-top:8px; display:block; border:1px solid #bbf7d0; background:#fff;">';
                }
                html += '<div style="color:#dc2626; font-weight:700; margin-top:6px; font-size:11.5px;">⚠️ ' + <?php echo json_encode( kaitori_t( 'Click "Update" above to save this replacement.', '右上の「更新」ボタンをクリックすると差し替えが完了します。' ) ); ?> + '</div>';
                html += '</div>';
                $('#kyc_preview_' + slot).html(html);
            });
            frame.open();
        });
    });
    </script>

    <!-- 6. Staff Internal Notes -->
    <div class="kyc-admin-card">
        <h4><span class="dashicons dashicons-edit" style="color:#475569;"></span> <?php echo kaitori_t( 'Staff Internal Review Notes (店舗スタッフ内部メモ)', '審査メモ・確認記録' ); ?></h4>
        <textarea name="_kyc_admin_notes" rows="3" style="width:100%; border:1.5px solid #cbd5e1; border-radius:6px; padding:8px 12px; font-size:13px;" placeholder="<?php echo esc_attr( kaitori_t( 'e.g. Verified in-store on 2026/11/07. Customer presented original driver license. Address and photo match.', '例: 2026/11/07 店頭にて原本確認完了。住所・氏名一致、検品OK。' ) ); ?>"><?php echo esc_textarea( $admin_notes ); ?></textarea>
    </div>
    <?php
}

function kaitoriakiba_save_kyc_meta( $post_id ) {
    // Verify the meta-box nonce first (CSRF protection); bail if absent/invalid.
    if ( ! isset( $_POST['kaitori_kyc_meta_nonce'] ) || ! wp_verify_nonce( $_POST['kaitori_kyc_meta_nonce'], 'kaitori_kyc_meta_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( kaitoriakiba_manage_cap() ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $text_fields = array(
        '_kyc_status',
        '_kyc_name',
        '_kyc_kana',
        '_kyc_phone',
        '_kyc_dob',
        '_kyc_job',
        '_kyc_postal',
        '_kyc_prefecture',
        '_kyc_city',
        '_kyc_address',
        '_kyc_doc_type',
        '_kyc_order_code',
    );

    foreach ( $text_fields as $tf ) {
        if ( isset( $_POST[ $tf ] ) ) {
            update_post_meta( $post_id, $tf, sanitize_text_field( wp_unslash( $_POST[ $tf ] ) ) );
        }
    }

    if ( isset( $_POST['_kyc_email'] ) ) {
        update_post_meta( $post_id, '_kyc_email', sanitize_email( wp_unslash( $_POST['_kyc_email'] ) ) );
    }

    if ( isset( $_POST['_kyc_admin_notes'] ) ) {
        update_post_meta( $post_id, '_kyc_admin_notes', sanitize_textarea_field( wp_unslash( $_POST['_kyc_admin_notes'] ) ) );
    }

    // -------------------------------------------------------------
    // Handle eKYC Document Replacement, Direct Upload & Removal
    // -------------------------------------------------------------
    $doc_slots = array(
        'front'  => array(
            'file_key'    => 'kyc_replace_front',
            'meta_file'   => '_kyc_id_front_file',
            'meta_attach' => '_kyc_id_front_attach',
            'media_id'    => 'kyc_media_front_id',
            'media_url'   => 'kyc_media_front_url',
            'remove'      => 'kyc_remove_front',
            'label'       => 'Front ID (表面)',
        ),
        'back'   => array(
            'file_key'    => 'kyc_replace_back',
            'meta_file'   => '_kyc_id_back_file',
            'meta_attach' => '_kyc_id_back_attach',
            'media_id'    => 'kyc_media_back_id',
            'media_url'   => 'kyc_media_back_url',
            'remove'      => 'kyc_remove_back',
            'label'       => 'Back ID (裏面)',
        ),
        'selfie' => array(
            'file_key'    => 'kyc_replace_selfie',
            'meta_file'   => '_kyc_selfie_file',
            'meta_attach' => '_kyc_selfie_attach',
            'media_id'    => 'kyc_media_selfie_id',
            'media_url'   => 'kyc_media_selfie_url',
            'remove'      => 'kyc_remove_selfie',
            'label'       => 'Selfie / Thickness (厚み・セルフィー)',
        ),
    );

    $audit_actions = array();

    foreach ( $doc_slots as $slot_key => $slot_cfg ) {
        // 1. Check removal request
        if ( ! empty( $_POST[ $slot_cfg['remove'] ] ) ) {
            delete_post_meta( $post_id, $slot_cfg['meta_file'] );
            delete_post_meta( $post_id, $slot_cfg['meta_attach'] );
            $audit_actions[] = $slot_cfg['label'] . ' removed';
            continue;
        }

        // 2. Check direct file upload in $_FILES
        if ( ! empty( $_FILES[ $slot_cfg['file_key'] ] ) && ! empty( $_FILES[ $slot_cfg['file_key'] ]['tmp_name'] ) ) {
            if ( function_exists( 'kaitoriakiba_kyc_store_upload' ) ) {
                $stored = kaitoriakiba_kyc_store_upload( $slot_cfg['file_key'], $post_id );
                if ( ! is_wp_error( $stored ) && ! empty( $stored ) ) {
                    update_post_meta( $post_id, $slot_cfg['meta_file'], $stored );
                    delete_post_meta( $post_id, $slot_cfg['meta_attach'] );
                    $audit_actions[] = $slot_cfg['label'] . ' replaced via direct upload';
                    continue;
                } else if ( is_wp_error( $stored ) ) {
                    set_transient( 'kaitori_kyc_admin_error_' . get_current_user_id(), sprintf( kaitori_t( 'Document Upload Error (%s): %s', '書類アップロードエラー (%s): %s' ), $slot_cfg['label'], $stored->get_error_message() ), 60 );
                }
            }
        }

        // 3. Check Media Library selection
        if ( ! empty( $_POST[ $slot_cfg['media_id'] ] ) ) {
            $att_id = (int) $_POST[ $slot_cfg['media_id'] ];
            if ( $att_id > 0 ) {
                update_post_meta( $post_id, $slot_cfg['meta_attach'], $att_id );
                $att_url = wp_get_attachment_url( $att_id );
                if ( $att_url ) {
                    update_post_meta( $post_id, $slot_cfg['meta_file'], $att_url );
                }
                $audit_actions[] = $slot_cfg['label'] . ' replaced via Media Library (#' . $att_id . ')';
                continue;
            }
        } elseif ( ! empty( $_POST[ $slot_cfg['media_url'] ] ) ) {
            $url = esc_url_raw( wp_unslash( $_POST[ $slot_cfg['media_url'] ] ) );
            if ( $url ) {
                update_post_meta( $post_id, $slot_cfg['meta_file'], $url );
                delete_post_meta( $post_id, $slot_cfg['meta_attach'] );
                $audit_actions[] = $slot_cfg['label'] . ' replaced via Media URL';
                continue;
            }
        }
    }

    // Automatically append an audit note if documents were modified
    if ( ! empty( $audit_actions ) ) {
        $cur_user = wp_get_current_user();
        $staff_title = ( $cur_user && $cur_user->exists() ) ? $cur_user->display_name : 'Staff';
        $audit_line  = sprintf(
            "\n[%s] %s: %s",
            current_time( 'Y/m/d H:i' ),
            $staff_title,
            implode( ', ', $audit_actions )
        );
        $cur_notes = get_post_meta( $post_id, '_kyc_admin_notes', true );
        update_post_meta( $post_id, '_kyc_admin_notes', trim( $cur_notes . $audit_line ) );
    }

    // Synchronize Status with linked Customer Account and Order
    if ( isset( $_POST['_kyc_status'] ) ) {
        $status = sanitize_text_field( $_POST['_kyc_status'] );
        $user_id = get_post_meta( $post_id, '_kyc_user_id', true );
        if ( $user_id ) {
            update_user_meta( $user_id, '_kaitori_kyc_status', $status );
            update_user_meta( $user_id, '_kaitori_kyc_verified', $status === 'verified' ? 'yes' : 'no' );
        }

        $order_id = get_post_meta( $post_id, '_kyc_order_id', true );
        if ( $order_id ) {
            update_post_meta( $order_id, '_order_kyc_status', $status );
        }
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


/**
 * Display KYC admin upload error notices if any
 */
function kaitoriakiba_kyc_admin_error_notice() {
    $err = get_transient( 'kaitori_kyc_admin_error_' . get_current_user_id() );
    if ( $err ) {
        delete_transient( 'kaitori_kyc_admin_error_' . get_current_user_id() );
        ?>
        <div class="notice notice-error is-dismissible" style="border-left-color:#dc2626; padding:12px 16px;">
            <p style="font-weight:700; color:#b91c1c; margin:0;">
                <span class="dashicons dashicons-warning" style="vertical-align:middle; color:#dc2626;"></span> <?php echo esc_html( $err ); ?>
            </p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'kaitoriakiba_kyc_admin_error_notice' );
