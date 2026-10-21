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
        __( 'Buyback Pricing & Technical Specifications', 'kaitoriakiba' ),
        'kaitoriakiba_device_meta_box_render',
        'kaitori_device',
        'normal',
        'high'
    );

    // 2. Device Primary Image & Custom Gallery
    add_meta_box(
        'kaitori_device_gallery_meta',
        __( 'Device Primary Image & Color Variants Gallery', 'kaitoriakiba' ),
        'kaitoriakiba_device_gallery_meta_box_render',
        'kaitori_device',
        'normal',
        'high'
    );

    // 3. Buyback Order Details
    add_meta_box(
        'kaitori_order_meta',
        __( 'Application Details & Customer Information', 'kaitoriakiba' ),
        'kaitoriakiba_order_meta_box_render',
        'buyback_order',
        'normal',
        'high'
    );

    // 4. eKYC Verification Documents & Status
    add_meta_box(
        'kaitori_kyc_meta',
        __( 'Identity Verification (eKYC) Document Review', 'kaitoriakiba' ),
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
            <label for="max_buyback_price"><?php _e( 'Maximum Buyback Price (¥) *', 'kaitoriakiba' ); ?></label>
            <input type="number" id="max_buyback_price" name="_max_buyback_price" value="<?php echo esc_attr( $max_price ); ?>" placeholder="168000" />
            <small class="price-badge-label"><?php _e( 'The highest ceiling price shown on homepage cards and search', 'kaitoriakiba' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_s"><?php _e( 'Rank S Price (Factory Sealed ¥)', 'kaitoriakiba' ); ?></label>
            <input type="number" id="price_rank_s" name="_price_rank_s" value="<?php echo esc_attr( $price_s ); ?>" placeholder="168000" />
            <small style="color:#64748b;"><?php _e( 'Brand new, unopened box with security seal', 'kaitoriakiba' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_a"><?php _e( 'Rank A Price (Unused / Open Box ¥)', 'kaitoriakiba' ); ?></label>
            <input type="number" id="price_rank_a" name="_price_rank_a" value="<?php echo esc_attr( $price_a ); ?>" placeholder="155000" />
            <small style="color:#64748b;"><?php _e( 'Activated for testing, pristine condition', 'kaitoriakiba' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_b"><?php _e( 'Rank B Price (Used - Excellent ¥)', 'kaitoriakiba' ); ?></label>
            <input type="number" id="price_rank_b" name="_price_rank_b" value="<?php echo esc_attr( $price_b ); ?>" placeholder="142000" />
            <small style="color:#64748b;"><?php _e( 'Used with no noticeable scratches or dents', 'kaitoriakiba' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_c"><?php _e( 'Rank C Price (Used - Good / Minor Marks ¥)', 'kaitoriakiba' ); ?></label>
            <input type="number" id="price_rank_c" name="_price_rank_c" value="<?php echo esc_attr( $price_c ); ?>" placeholder="128000" />
            <small style="color:#64748b;"><?php _e( 'Visible minor abrasions or small scratches', 'kaitoriakiba' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="price_rank_d"><?php _e( 'Rank D Price (Cracked Screen / Heavy Marks ¥)', 'kaitoriakiba' ); ?></label>
            <input type="number" id="price_rank_d" name="_price_rank_d" value="<?php echo esc_attr( $price_d ); ?>" placeholder="82000" />
            <small style="color:#64748b;"><?php _e( 'Cracked glass, heavy wear, or battery below 80%', 'kaitoriakiba' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="installment_deduction"><?php _e( 'Installment Balance (△) Deduction (¥)', 'kaitoriakiba' ); ?></label>
            <input type="number" id="installment_deduction" name="_installment_deduction" value="<?php echo esc_attr( $installment ); ?>" placeholder="5000" />
            <small style="color:#64748b;"><?php _e( 'Amount deducted if device has remaining carrier payments (triangle mark)', 'kaitoriakiba' ); ?></small>
        </div>

        <div class="kaitori-meta-item">
            <label for="model_number"><?php _e( 'Model Number (e.g., A3293)', 'kaitoriakiba' ); ?></label>
            <input type="text" id="model_number" name="_model_number" value="<?php echo esc_attr( $model_num ); ?>" placeholder="A3293" />
        </div>

        <div class="kaitori-meta-item">
            <label for="jan_code"><?php _e( 'JAN / EAN Barcode', 'kaitoriakiba' ); ?></label>
            <input type="text" id="jan_code" name="_jan_code" value="<?php echo esc_attr( $jan_code ); ?>" placeholder="4549995734140" />
        </div>

        <div class="kaitori-meta-item kaitori-full-width">
            <label for="storage_options"><?php _e( 'Storage Capacities & Base Buyback Prices (One per line format: Capacity:Price)', 'kaitoriakiba' ); ?></label>
            <textarea id="storage_options" name="_storage_options" rows="4"><?php echo esc_textarea( $storages ); ?></textarea>
            <small style="color:#64748b;"><?php _e( 'Format: 128GB:152000 (new line) 256GB:168000 (new line) 512GB:184000 (new line) 1TB:202000', 'kaitoriakiba' ); ?></small>
        </div>

        <div class="kaitori-meta-item kaitori-full-width">
            <label for="color_options"><?php _e( 'Available Colors / Variants (Comma-separated)', 'kaitoriakiba' ); ?></label>
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
            <?php _e( 'Primary Device Image', 'kaitoriakiba' ); ?>
        </h4>
        <div class="device-primary-box">
            <img id="primary_image_preview" class="device-preview-img" src="<?php echo esc_url( $primary_image ?: $default_primary ); ?>" alt="Device Preview">
            <div>
                <input type="hidden" id="device_primary_image_url" name="_device_primary_image_url" value="<?php echo esc_attr( $primary_image ); ?>" />
                <button type="button" class="button button-primary" id="btn_upload_primary_image" style="background:#ef4444; border-color:#ef4444; font-weight:700;">
                    <?php _e( 'Upload / Select Primary Image', 'kaitoriakiba' ); ?>
                </button>
                <button type="button" class="button" id="btn_remove_primary_image" style="margin-left: 6px; <?php echo empty( $primary_image ) ? 'display:none;' : ''; ?>">
                    <?php _e( 'Reset to Default Image', 'kaitoriakiba' ); ?>
                </button>
                <p style="margin: 6px 0 0 0; font-size: 12px; color: #64748b;">
                    <?php _e( 'If no custom primary image is uploaded, the theme will automatically show the default showcase device image.', 'kaitoriakiba' ); ?>
                </p>
            </div>
        </div>

        <!-- 2. Device Gallery / Color Variants -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div>
                <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: #0f172a;">
                    <i class="dashicons dashicons-images-alt2" style="vertical-align: middle; color: #ef4444;"></i>
                    <?php _e( 'Color Variants & Multi-Angle Gallery', 'kaitoriakiba' ); ?>
                </h4>
                <small style="color: #64748b; font-size: 12px;">
                    <?php _e( 'Upload photos for individual colors (e.g. Natural Titanium, White, Black). If left empty, default showcase colors are displayed.', 'kaitoriakiba' ); ?>
                </small>
            </div>
            <button type="button" class="button button-secondary" id="btn_add_gallery_item" style="font-weight: 700;">
                <i class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></i> <?php _e( 'Add Gallery Image (+)', 'kaitoriakiba' ); ?>
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
                        <button type="button" class="btn-remove-item" onclick="this.closest('.gallery-card').remove();"><?php _e( 'Remove Image', 'kaitoriakiba' ); ?></button>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
        <p id="gallery_empty_notice" style="color: #94a3b8; font-style: italic; font-size: 12.5px; margin-top: 10px; <?php echo ! empty( $gallery_images ) ? 'display:none;' : ''; ?>">
            <?php _e( 'No custom gallery images uploaded yet. The default 4 color angles will be displayed.', 'kaitoriakiba' ); ?>
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
    $acc_number     = get_post_meta( $post->ID, '_bank_account_number', true );
    $holder_kana    = get_post_meta( $post->ID, '_bank_holder_kana', true );
    $est_price      = get_post_meta( $post->ID, '_estimated_price', true );
    $accessories    = get_post_meta( $post->ID, '_accessories', true );
    ?>
    <table class="form-table">
        <tr>
            <th style="width:200px;"><label><?php _e( 'Order Processing Status', 'kaitoriakiba' ); ?></label></th>
            <td>
                <select name="_order_status" style="padding:6px 14px; font-weight:bold; font-size:13px; border-radius:6px; border:1.5px solid #cbd5e1;">
                    <option value="pending" <?php selected($order_status, 'pending'); ?>><?php _e( 'Pending Review', 'kaitoriakiba' ); ?></option>
                    <option value="received" <?php selected($order_status, 'received'); ?>><?php _e( 'Device Received in Store / Warehouse', 'kaitoriakiba' ); ?></option>
                    <option value="inspected" <?php selected($order_status, 'inspected'); ?>><?php _e( 'Inspection Completed (Final Quote Ready)', 'kaitoriakiba' ); ?></option>
                    <option value="paid" <?php selected($order_status, 'paid'); ?>><?php _e( 'Payout Completed (Bank Transfer Done)', 'kaitoriakiba' ); ?></option>
                    <option value="cancelled" <?php selected($order_status, 'cancelled'); ?>><?php _e( 'Cancelled / Returned', 'kaitoriakiba' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Estimated Buyback Quote', 'kaitoriakiba' ); ?></label></th>
            <td><strong style="color:#ef4444; font-size:22px; font-weight:900;">¥<?php echo number_format((int)$est_price); ?></strong></td>
        </tr>
        <tr>
            <th><label><?php _e( 'Customer Information', 'kaitoriakiba' ); ?></label></th>
            <td>
                <strong><?php _e( 'Full Name:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($name); ?> (<?php echo esc_html($kana); ?>)<br>
                <strong><?php _e( 'Phone Number:', 'kaitoriakiba' ); ?></strong> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a><br>
                <strong><?php _e( 'Email Address:', 'kaitoriakiba' ); ?></strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Device Model & Identifiers', 'kaitoriakiba' ); ?></label></th>
            <td>
                <strong><?php _e( 'Model:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($model); ?><br>
                <strong><?php _e( 'Capacity & Color:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($storage); ?> / <?php echo esc_html($color); ?><br>
                <strong><?php _e( 'Carrier:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($carrier); ?><br>
                <strong><?php _e( 'IMEI Number (15 Digits):', 'kaitoriakiba' ); ?></strong> <code style="font-size:14px; background:#f1f5f9; padding:2px 6px; border-radius:4px; font-weight:bold;"><?php echo esc_html($imei); ?></code><br>
                <strong><?php _e( 'Serial Number:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($serial ?: '—'); ?>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Condition Checklist Reported', 'kaitoriakiba' ); ?></label></th>
            <td>
                • <strong><?php _e( 'Screen Condition:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($screen_cond); ?><br>
                • <strong><?php _e( 'Body / Frame Condition:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($body_cond); ?><br>
                • <strong><?php _e( 'Battery Maximum Health:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($battery_health); ?>%<br>
                • <strong><?php _e( 'Powers On Correctly:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($powers_on); ?><br>
                • <strong><?php _e( 'Biometrics (Face ID / Touch ID):', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($face_touch_id); ?><br>
                • <strong><?php _e( 'Camera Operation:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($camera_works); ?><br>
                • <strong><?php _e( 'Water Damage / Past Repairs:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($water_history); ?><br>
                • <strong><?php _e( 'Included Accessories:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html(is_array($accessories) ? implode(', ', $accessories) : $accessories); ?><br>
                • <strong><?php _e( 'Other Reported Issues:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($extra_issues ?: 'None'); ?>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Carrier Installment Status', 'kaitoriakiba' ); ?></label></th>
            <td>
                <strong><?php _e( 'Status:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($installment_st === 'yes' ? __( 'Under Installment (△)', 'kaitoriakiba' ) : __( 'Fully Paid Off (○)', 'kaitoriakiba' ) ); ?><br>
                <?php if ($installment_st === 'yes'): ?>
                    <strong><?php _e( 'Remaining Balance Due:', 'kaitoriakiba' ); ?></strong> <span style="color:#dc2626; font-weight:bold;">¥<?php echo number_format((int)$installment_due); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Security & iCloud Lock Verification', 'kaitoriakiba' ); ?></label></th>
            <td>
                • <?php _e( '"Find My" (Activation Lock) Disabled:', 'kaitoriakiba' ); ?> <?php echo $find_my_off ? '<span style="color:#16a34a; font-weight:bold;">✓ Confirmed</span>' : '<span style="color:#dc2626;">Not Confirmed</span>'; ?><br>
                • <?php _e( 'Apple ID Signed Out:', 'kaitoriakiba' ); ?> <?php echo $apple_id_out ? '<span style="color:#16a34a; font-weight:bold;">✓ Confirmed</span>' : '<span style="color:#dc2626;">Not Confirmed</span>'; ?><br>
                • <?php _e( 'No MDM / Corporate Profile Lock:', 'kaitoriakiba' ); ?> <?php echo $no_mdm_lock ? '<span style="color:#16a34a; font-weight:bold;">✓ Confirmed</span>' : '<span style="color:#dc2626;">Not Confirmed</span>'; ?>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Handover & Delivery Method', 'kaitoriakiba' ); ?></label></th>
            <td>
                <strong><?php _e( 'Method:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($delivery_meth === 'store' ? __( 'In-Store Drop-off (Akihabara Store)', 'kaitoriakiba' ) : __( 'Mail-in Buyback (Free Courier)', 'kaitoriakiba' ) ); ?><br>
                <strong><?php _e( 'Free Shipping Kit Needed:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($shipping_kit ?: __( 'Not Needed (Self Packed)', 'kaitoriakiba' ) ); ?><br>
                <strong><?php _e( 'Preferred Pickup / Visit Date:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($pickup_dt ?: __( 'Not Specified', 'kaitoriakiba' ) ); ?>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Customer Bank Payout Details', 'kaitoriakiba' ); ?></label></th>
            <td>
                <strong><?php _e( 'Bank Name:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($bank_name); ?><br>
                <strong><?php _e( 'Branch Name:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($branch_name); ?><br>
                <strong><?php _e( 'Account Type:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($acc_type); ?><br>
                <strong><?php _e( 'Account Number:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($acc_number); ?><br>
                <strong><?php _e( 'Account Holder (Katakana):', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($holder_kana); ?>
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

/**
 * Render eKYC Verification Meta Box (100% English Admin)
 */
function kaitoriakiba_kyc_meta_box_render( $post ) {
    $kyc_status   = get_post_meta( $post->ID, '_kyc_status', true ) ?: 'pending';
    $doc_type     = get_post_meta( $post->ID, '_id_document_type', true );
    $front_url    = get_post_meta( $post->ID, '_front_photo_url', true );
    $back_url     = get_post_meta( $post->ID, '_back_photo_url', true );
    $cust_name    = get_post_meta( $post->ID, '_customer_name', true );
    $cust_email   = get_post_meta( $post->ID, '_customer_email', true );
    $cust_phone   = get_post_meta( $post->ID, '_customer_phone', true );
    $admin_notes  = get_post_meta( $post->ID, '_kyc_admin_notes', true );
    ?>
    <table class="form-table">
        <tr>
            <th style="width:200px;"><label><?php _e( 'Verification Status', 'kaitoriakiba' ); ?></label></th>
            <td>
                <select name="_kyc_status" style="padding:6px 14px; font-weight:bold; font-size:13px; border-radius:6px; border:1.5px solid #cbd5e1;">
                    <option value="pending" <?php selected($kyc_status, 'pending'); ?>><?php _e( 'Pending Verification', 'kaitoriakiba' ); ?></option>
                    <option value="verified" <?php selected($kyc_status, 'verified'); ?>><?php _e( 'Verified & Approved', 'kaitoriakiba' ); ?></option>
                    <option value="rejected" <?php selected($kyc_status, 'rejected'); ?>><?php _e( 'Rejected (Blurry / Expired)', 'kaitoriakiba' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Applicant Details', 'kaitoriakiba' ); ?></label></th>
            <td>
                <strong><?php _e( 'Name:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($cust_name); ?><br>
                <strong><?php _e( 'Email:', 'kaitoriakiba' ); ?></strong> <a href="mailto:<?php echo esc_attr($cust_email); ?>"><?php echo esc_html($cust_email); ?></a><br>
                <strong><?php _e( 'Phone:', 'kaitoriakiba' ); ?></strong> <?php echo esc_html($cust_phone); ?>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'ID Document Type', 'kaitoriakiba' ); ?></label></th>
            <td><strong style="font-size:14px; color:#1e293b;"><?php echo esc_html($doc_type ?: 'Driver License'); ?></strong></td>
        </tr>
        <tr>
            <th><label><?php _e( 'Uploaded ID Photos', 'kaitoriakiba' ); ?></label></th>
            <td>
                <div style="display:flex; gap:20px; margin-top:6px;">
                    <?php if ( $front_url ) : ?>
                        <div>
                            <div style="font-weight:700; margin-bottom:4px; font-size:12px;"><?php _e( 'Front Side Photo:', 'kaitoriakiba' ); ?></div>
                            <a href="<?php echo esc_url($front_url); ?>" target="_blank">
                                <img src="<?php echo esc_url($front_url); ?>" style="max-width:240px; max-height:160px; border-radius:8px; border:1.5px solid #cbd5e1; object-fit:cover;">
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ( $back_url ) : ?>
                        <div>
                            <div style="font-weight:700; margin-bottom:4px; font-size:12px;"><?php _e( 'Back Side Photo:', 'kaitoriakiba' ); ?></div>
                            <a href="<?php echo esc_url($back_url); ?>" target="_blank">
                                <img src="<?php echo esc_url($back_url); ?>" style="max-width:240px; max-height:160px; border-radius:8px; border:1.5px solid #cbd5e1; object-fit:cover;">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr>
            <th><label><?php _e( 'Reviewer Internal Notes', 'kaitoriakiba' ); ?></label></th>
            <td>
                <textarea name="_kyc_admin_notes" rows="3" style="width:100%; border:1.5px solid #cbd5e1; border-radius:6px; padding:8px;" placeholder="<?php esc_attr_e( 'Enter verification remarks or rejection reason...', 'kaitoriakiba' ); ?>"><?php echo esc_textarea($admin_notes); ?></textarea>
            </td>
        </tr>
    </table>
    <?php
}

function kaitoriakiba_save_kyc_meta( $post_id ) {
    if ( isset( $_POST['_kyc_status'] ) ) {
        update_post_meta( $post_id, '_kyc_status', sanitize_text_field( $_POST['_kyc_status'] ) );
    }
    if ( isset( $_POST['_kyc_admin_notes'] ) ) {
        update_post_meta( $post_id, '_kyc_admin_notes', sanitize_textarea_field( $_POST['_kyc_admin_notes'] ) );
    }
}
add_action( 'save_post_kyc_record', 'kaitoriakiba_save_kyc_meta' );
