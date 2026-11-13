<?php
/**
 * Kaitori Akiba AJAX Handlers
 * 
 * Handles Application submissions, eKYC verifications, and live filter queries.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle Buyback Application Submission
 */
function kaitoriakiba_ajax_submit_application() {
    check_ajax_referer( 'kaitori_nonce', 'security' );
    kaitoriakiba_guard_public_form( 'application', 15 );

    // Gate: login + eKYC required (server-side enforcement; cannot be bypassed).
    if ( function_exists( 'kaitoriakiba_user_can_sell' ) ) {
        list( $can_sell, $gate_reason ) = kaitoriakiba_user_can_sell();
        if ( ! $can_sell ) {
            $messages = array(
                'login'    => kaitori_t( 'You must be logged in to apply for a buyback.', '買取申込にはログインが必要です。' ),
                'need_kyc' => kaitori_t( 'Please complete identity verification (eKYC) before applying.', 'お申込みの前に本人確認（eKYC）を完了してください。' ),
                'pending'  => kaitori_t( 'Your identity verification is under review. You can apply once it is approved.', '本人確認の審査中です。承認後にお申込みいただけます。' ),
                'rejected' => kaitori_t( 'There is a problem with your identity verification. Please resubmit.', '本人確認に不備があります。再提出をお願いいたします。' ),
            );
            wp_send_json_error( array(
                'message'  => isset( $messages[ $gate_reason ] ) ? $messages[ $gate_reason ] : $messages['need_kyc'],
                'gate'     => $gate_reason,
                'kyc_url'  => home_url( '/kyc-verify/' ),
                'login_url'=> home_url( '/register/?action=login' ),
            ) );
        }
    }

    // Accept both the native template field names (model/storage/...) and the
    // Elementor widget field names (device_model/device_storage/...).
    $model          = isset( $_POST['model'] ) ? sanitize_text_field( $_POST['model'] ) : ( isset( $_POST['device_model'] ) ? sanitize_text_field( $_POST['device_model'] ) : '' );
    $storage        = isset( $_POST['storage'] ) ? sanitize_text_field( $_POST['storage'] ) : ( isset( $_POST['device_storage'] ) ? sanitize_text_field( $_POST['device_storage'] ) : '' );
    $color          = isset( $_POST['color'] ) ? sanitize_text_field( $_POST['color'] ) : ( isset( $_POST['device_color'] ) ? sanitize_text_field( $_POST['device_color'] ) : '' );
    $carrier        = isset( $_POST['carrier'] ) ? sanitize_text_field( $_POST['carrier'] ) : ( isset( $_POST['device_carrier'] ) ? sanitize_text_field( $_POST['device_carrier'] ) : '' );
    $imei           = isset( $_POST['imei'] ) ? sanitize_text_field( $_POST['imei'] ) : ( isset( $_POST['device_imei'] ) ? sanitize_text_field( $_POST['device_imei'] ) : '' );
    $serial         = isset( $_POST['serial'] ) ? sanitize_text_field( $_POST['serial'] ) : '';
    $screen_cond    = isset( $_POST['screen_condition'] ) ? sanitize_text_field( $_POST['screen_condition'] ) : '';
    $body_cond      = isset( $_POST['body_condition'] ) ? sanitize_text_field( $_POST['body_condition'] ) : '';
    $battery_health = isset( $_POST['battery_health'] ) ? intval( $_POST['battery_health'] ) : 100;
    $powers_on      = isset( $_POST['powers_on'] ) ? sanitize_text_field( $_POST['powers_on'] ) : 'yes';
    $face_touch_id  = isset( $_POST['face_touch_id'] ) ? sanitize_text_field( $_POST['face_touch_id'] ) : 'yes';
    $camera_works   = isset( $_POST['camera_works'] ) ? sanitize_text_field( $_POST['camera_works'] ) : 'yes';
    $water_history  = isset( $_POST['water_damage_repair'] ) ? sanitize_text_field( $_POST['water_damage_repair'] ) : 'none';
    $extra_issues   = isset( $_POST['extra_issues'] ) ? sanitize_textarea_field( $_POST['extra_issues'] ) : '';
    
    // Security & installment
    $find_my_off    = ! empty( $_POST['find_my_off'] ) ? 1 : 0;
    $apple_id_out   = ! empty( $_POST['apple_id_signout'] ) ? 1 : 0;
    $no_mdm_lock    = ! empty( $_POST['no_mdm_lock'] ) ? 1 : 0;
    $installment_st = isset( $_POST['installment_status'] ) ? sanitize_text_field( $_POST['installment_status'] ) : 'no';
    $installment_due= isset( $_POST['installment_due_amount'] ) ? intval( $_POST['installment_due_amount'] ) : 0;

    // Delivery & Bank
    $delivery_meth  = isset( $_POST['delivery_method'] ) ? sanitize_text_field( $_POST['delivery_method'] ) : 'mail';
    $shipping_kit   = isset( $_POST['shipping_kit_needed'] ) ? sanitize_text_field( $_POST['shipping_kit_needed'] ) : ( isset( $_POST['shipping_kit'] ) ? sanitize_text_field( $_POST['shipping_kit'] ) : 'no' );
    $pickup_dt      = isset( $_POST['pickup_datetime'] ) ? sanitize_text_field( $_POST['pickup_datetime'] ) : '';
    $bank_name      = isset( $_POST['bank_name'] ) ? sanitize_text_field( $_POST['bank_name'] ) : '';
    $branch_name    = isset( $_POST['bank_branch'] ) ? sanitize_text_field( $_POST['bank_branch'] ) : '';
    $acc_type       = isset( $_POST['bank_account_type'] ) ? sanitize_text_field( $_POST['bank_account_type'] ) : '普通';
    $acc_number     = isset( $_POST['bank_account_number'] ) ? sanitize_text_field( $_POST['bank_account_number'] ) : '';
    $holder_kana    = isset( $_POST['bank_holder_kana'] ) ? sanitize_text_field( $_POST['bank_holder_kana'] ) : '';

    // SECURITY: never trust the price from the browser. Recompute on the server
    // from the device's admin pricing. The posted value is kept only for reference.
    $client_price   = isset( $_POST['estimated_price'] ) ? intval( $_POST['estimated_price'] ) : 0;
    $est_price      = kaitoriakiba_calculate_quote( array(
        'model'              => $model,
        'storage'            => $storage,
        'screen_condition'   => $screen_cond,
        'body_condition'     => $body_cond,
        'battery_health'     => $battery_health,
        'powers_on'          => $powers_on,
        'camera_works'       => $camera_works,
        'installment_status' => $installment_st,
    ) );

    // Customer
    $name           = isset( $_POST['applicant_name'] ) ? sanitize_text_field( $_POST['applicant_name'] ) : '';
    $kana           = isset( $_POST['applicant_kana'] ) ? sanitize_text_field( $_POST['applicant_kana'] ) : '';
    $email          = isset( $_POST['applicant_email'] ) ? sanitize_email( $_POST['applicant_email'] ) : '';
    $phone          = isset( $_POST['applicant_phone'] ) ? sanitize_text_field( $_POST['applicant_phone'] ) : '';
    $accessories    = isset( $_POST['accessories'] ) && is_array( $_POST['accessories'] ) ? array_map( 'sanitize_text_field', $_POST['accessories'] ) : array();

    // Link & Auto-populate from WordPress User Profile if logged in or matched
    $user_id = is_user_logged_in() ? get_current_user_id() : 0;
    if ( ! $user_id && ! empty( $email ) ) {
        $found_user = get_user_by( 'email', $email );
        if ( $found_user ) {
            $user_id = $found_user->ID;
        }
    }

    if ( $user_id ) {
        $u_prof = get_userdata( $user_id );
        if ( $u_prof ) {
            if ( empty( $name ) || 'Guest Customer' === $name ) {
                $u_full = trim( $u_prof->last_name . ' ' . $u_prof->first_name );
                $name   = $u_full ?: ( $u_prof->display_name ?: $u_prof->user_login );
            }
            if ( empty( $email ) ) {
                $email = $u_prof->user_email;
            }
            if ( empty( $phone ) ) {
                $phone = get_user_meta( $user_id, 'phone_number', true ) ?: ( get_user_meta( $user_id, 'billing_phone', true ) ?: '' );
            }
            if ( empty( $kana ) ) {
                $kana = get_user_meta( $user_id, 'applicant_kana', true ) ?: ( get_user_meta( $user_id, 'last_name_kana', true ) ?: '' );
            }
        }
    }

    // Auto-populate from customer's verified or existing KYC record if still missing
    if ( $user_id && function_exists( 'kaitoriakiba_find_user_kyc_record' ) ) {
        $user_kyc_id = kaitoriakiba_find_user_kyc_record( $user_id );
        if ( $user_kyc_id ) {
            if ( empty( $name ) || 'Guest Customer' === $name ) {
                $name = get_post_meta( $user_kyc_id, '_kyc_name', true );
            }
            if ( empty( $kana ) ) {
                $kana = get_post_meta( $user_kyc_id, '_kyc_kana', true );
            }
            if ( empty( $phone ) ) {
                $phone = get_post_meta( $user_kyc_id, '_kyc_phone', true );
            }
            if ( empty( $email ) ) {
                $email = get_post_meta( $user_kyc_id, '_kyc_email', true );
            }
        }
    }

    if ( empty( $name ) ) {
        $name = 'Guest Customer';
    }

    // Server-side validation for Application Fields
    $invalid_app_fields = array();
    if ( empty( $model ) ) {
        $invalid_app_fields['model'] = kaitori_t( 'Please select a device model.', '機種名を選択してください。' );
    }
    if ( empty( $storage ) ) {
        $invalid_app_fields['storage'] = kaitori_t( 'Please select a storage capacity.', 'ストレージ容量を選択してください。' );
    }
    if ( ! empty( $imei ) ) {
        $clean_imei = preg_replace( '/[\s\-]/', '', $imei );
        if ( strlen( $clean_imei ) !== 15 || ! ctype_digit( $clean_imei ) ) {
            $invalid_app_fields['imei'] = kaitori_t( 'Please enter the IMEI as 15 half-width digits.', 'IMEI番号は15桁の半角数字で入力してください。' );
        }
    }
    if ( ! empty( $email ) && ! is_email( $email ) ) {
        $invalid_app_fields['applicant_email'] = kaitori_t( 'Please enter a valid email address.', '有効なメールアドレスの形式で入力してください。' );
    }
    if ( empty( $_POST['agree_terms'] ) ) {
        $invalid_app_fields['agree_terms'] = kaitori_t( 'Please check the box to agree to the Terms of Service.', '利用規約への同意にチェックを入れてください。' );
    }
    if ( ! empty( $invalid_app_fields ) ) {
        wp_send_json_error( array(
            'message'        => kaitori_t( 'Some entries are invalid. Please check the highlighted fields.', '入力内容に不備があります。該当項目をご確認ください。' ),
            'invalid_fields' => $invalid_app_fields,
        ) );
    }

    // Generate Order Title
    $order_title = 'KB-' . date('Ymd') . '-' . wp_rand(1000, 9999) . ' [' . $model . ' ' . $storage . ']';

    // Insert Post into buyback_order CPT
    $post_data = array(
        'post_title'    => $order_title,
        'post_status'   => 'publish',
        'post_type'     => 'buyback_order',
    );
    $order_id = wp_insert_post( $post_data );

    if ( is_wp_error( $order_id ) || ! $order_id ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Sorry, an error occurred while submitting your application.', '申し訳ありません。申込の送信中にエラーが発生しました。' ) ) );
    }

    // Save Meta Data
    update_post_meta( $order_id, '_order_status', 'pending' );
    update_post_meta( $order_id, '_applicant_name', $name );
    update_post_meta( $order_id, '_applicant_kana', $kana );
    update_post_meta( $order_id, '_applicant_email', $email );
    update_post_meta( $order_id, '_applicant_phone', $phone );
    update_post_meta( $order_id, '_device_model', $model );
    update_post_meta( $order_id, '_device_storage', $storage );
    update_post_meta( $order_id, '_device_color', $color );
    update_post_meta( $order_id, '_device_carrier', $carrier );
    update_post_meta( $order_id, '_device_imei', $imei );
    update_post_meta( $order_id, '_device_serial', $serial );
    update_post_meta( $order_id, '_screen_condition', $screen_cond );
    update_post_meta( $order_id, '_body_condition', $body_cond );
    update_post_meta( $order_id, '_battery_health', $battery_health );
    update_post_meta( $order_id, '_powers_on', $powers_on );
    update_post_meta( $order_id, '_face_touch_id', $face_touch_id );
    update_post_meta( $order_id, '_camera_works', $camera_works );
    update_post_meta( $order_id, '_water_damage_repair', $water_history );
    update_post_meta( $order_id, '_extra_issues', $extra_issues );
    update_post_meta( $order_id, '_accessories', $accessories );
    update_post_meta( $order_id, '_find_my_off', $find_my_off );
    update_post_meta( $order_id, '_apple_id_signout', $apple_id_out );
    update_post_meta( $order_id, '_no_mdm_lock', $no_mdm_lock );
    update_post_meta( $order_id, '_installment_status', $installment_st );
    update_post_meta( $order_id, '_installment_due_amount', $installment_due );
    update_post_meta( $order_id, '_delivery_method', $delivery_meth );
    update_post_meta( $order_id, '_shipping_kit_needed', $shipping_kit );
    update_post_meta( $order_id, '_pickup_datetime', $pickup_dt );
    update_post_meta( $order_id, '_bank_name', $bank_name );
    update_post_meta( $order_id, '_bank_branch', $branch_name );
    update_post_meta( $order_id, '_bank_account_type', $acc_type );
    // Encrypt financial PII at rest (see inc/crypto.php).
    update_post_meta( $order_id, '_bank_account_number', kaitoriakiba_encrypt( $acc_number ) );
    update_post_meta( $order_id, '_bank_holder_kana', kaitoriakiba_encrypt( $holder_kana ) );
    update_post_meta( $order_id, '_estimated_price', $est_price );
    update_post_meta( $order_id, '_estimated_price_client', $client_price ); // reference only

    // Link WordPress User Profile
    $user_id = is_user_logged_in() ? get_current_user_id() : 0;
    if ( ! $user_id && ! empty( $email ) ) {
        $found_user = get_user_by( 'email', $email );
        if ( $found_user ) {
            $user_id = $found_user->ID;
        }
    }
    update_post_meta( $order_id, '_customer_user_id', $user_id );

    // Determine overall KYC status for this customer
    $kyc_lookup = function_exists( 'kaitoriakiba_lookup_kyc_status' )
        ? kaitoriakiba_lookup_kyc_status( $email, $user_id )
        : ( $user_id ? ( get_user_meta( $user_id, '_kaitori_kyc_status', true ) ?: 'none' ) : 'none' );

    // Check if customer already has a submitted or verified eKYC record
    $existing_kyc = get_posts( array(
        'post_type'   => 'kyc_record',
        'meta_key'    => '_kyc_email',
        'meta_value'  => $email,
        'numberposts' => 1,
        'post_status' => 'any',
    ) );
    if ( ! empty( $existing_kyc ) ) {
        $kyc_st = get_post_meta( $existing_kyc[0]->ID, '_kyc_status', true ) ?: 'pending';
        if ( 'verified' === $kyc_lookup ) {
            $kyc_st = 'verified';
        }
        update_post_meta( $order_id, '_order_kyc_status', $kyc_st );
        update_post_meta( $order_id, '_order_kyc_record_id', $existing_kyc[0]->ID );
        update_post_meta( $existing_kyc[0]->ID, '_kyc_order_id', $order_id );
        update_post_meta( $existing_kyc[0]->ID, '_kyc_order_code', $order_title );
    } else {
        if ( 'verified' === $kyc_lookup ) {
            update_post_meta( $order_id, '_order_kyc_status', 'verified' );
        } else {
            update_post_meta( $order_id, '_order_kyc_status', 'unverified' );
        }
    }

    // Send admin notification
    $admin_email = get_option( 'admin_email' );
    $subject = '【Kaitori Akiba】新規買取申込を受付いたしました: ' . $order_title;
    $message = "新規の買取申込が送信されました。\n\n"
             . "申込番号: " . $order_title . "\n"
             . "お客様名: " . $name . " 様 (" . $kana . ")\n"
             . "電話番号: " . $phone . "\n"
             . "メール: " . $email . "\n"
             . "機種名: " . $model . " " . $storage . " (" . $color . ")\n"
             . "IMEI: " . $imei . "\n"
             . "受取方法: " . ($delivery_meth === 'store' ? '店頭買取' : '郵送買取') . "\n"
             . "概算査定金額: ¥" . number_format($est_price) . "\n\n"
             . "管理画面より詳細をご確認ください:\n" . admin_url('post.php?post=' . $order_id . '&action=edit');
    @wp_mail( $admin_email, $subject, $message );

    // Confirmation email to the CUSTOMER (previously missing).
    if ( function_exists( 'kaitoriakiba_send_order_confirmation' ) ) {
        kaitoriakiba_send_order_confirmation( $order_id );
    }

    wp_send_json_success( array(
        'order_id'    => $order_id,
        'order_code'  => $order_title,
        'kyc_status'  => $kyc_lookup,
        'message'     => kaitori_t( 'Your buyback application was submitted successfully. Please check your confirmation email.', '買取申込が正常に送信されました。確認メールをご確認ください。' ),
    ) );
}
add_action( 'wp_ajax_kaitori_submit_application', 'kaitoriakiba_ajax_submit_application' );
add_action( 'wp_ajax_nopriv_kaitori_submit_application', 'kaitoriakiba_ajax_submit_application' );

/**
 * Handle eKYC ID Submission & Connect with Customer Profile + Orders
 */
function kaitoriakiba_ajax_submit_kyc() {
    check_ajax_referer( 'kaitori_nonce', 'security' );
    kaitoriakiba_guard_public_form( 'kyc', 15 );

    // eKYC may only be submitted by a registered, logged-in member (defense in
    // depth; the form is also hidden from guests in the template/widget).
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'Please register or log in before submitting identity verification (eKYC).',
            '本人確認(eKYC)のご提出には、会員登録・ログインが必要です。'
        ) ) );
    }

    // If the user is already verified (and not an admin testing with preview), reject duplicate submission
    $cur_uid        = get_current_user_id();
    $cur_user_email = wp_get_current_user()->user_email;
    $existing_kyc_st = function_exists( 'kaitoriakiba_lookup_kyc_status' )
        ? kaitoriakiba_lookup_kyc_status( $cur_user_email, $cur_uid )
        : ( get_user_meta( $cur_uid, '_kaitori_kyc_status', true ) ?: 'none' );
    if ( 'verified' === $existing_kyc_st && ! ( current_user_can( 'manage_options' ) && ! empty( $_REQUEST['preview_form'] ) ) ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'Your identity verification (eKYC) is already completed. Resubmission is not required.',
            'お客様の本人確認（eKYC）は既に完了・承認済みです。再提出は不要です。'
        ) ) );
    }

    $name       = isset( $_POST['kyc_name'] ) ? sanitize_text_field( $_POST['kyc_name'] ) : ( isset( $_POST['customer_name'] ) ? sanitize_text_field( $_POST['customer_name'] ) : '' );
    $kana       = isset( $_POST['kyc_kana'] ) ? sanitize_text_field( $_POST['kyc_kana'] ) : '';
    $email      = isset( $_POST['kyc_email'] ) ? sanitize_email( $_POST['kyc_email'] ) : ( isset( $_POST['customer_email'] ) ? sanitize_email( $_POST['customer_email'] ) : '' );
    $phone      = isset( $_POST['kyc_phone'] ) ? sanitize_text_field( $_POST['kyc_phone'] ) : '';
    $dob        = isset( $_POST['kyc_dob'] ) ? sanitize_text_field( $_POST['kyc_dob'] ) : '';
    $postal     = isset( $_POST['kyc_postal'] ) ? sanitize_text_field( $_POST['kyc_postal'] ) : '';
    $pref       = isset( $_POST['kyc_prefecture'] ) ? sanitize_text_field( $_POST['kyc_prefecture'] ) : '';
    $city       = isset( $_POST['kyc_city'] ) ? sanitize_text_field( $_POST['kyc_city'] ) : '';
    $address    = isset( $_POST['kyc_address'] ) ? sanitize_text_field( $_POST['kyc_address'] ) : '';
    $job        = isset( $_POST['kyc_job'] ) ? sanitize_text_field( $_POST['kyc_job'] ) : '';
    $doc_type   = isset( $_POST['kyc_doc_type'] ) ? sanitize_text_field( $_POST['kyc_doc_type'] ) : ( isset( $_POST['id_type'] ) ? sanitize_text_field( $_POST['id_type'] ) : 'Driver License (運転免許証)' );
    $order_code = isset( $_POST['kyc_order_code'] ) ? sanitize_text_field( $_POST['kyc_order_code'] ) : '';

    // Field-level validation for eKYC submission
    $invalid_kyc_fields = array();

    if ( empty( $name ) ) {
        $invalid_kyc_fields['kyc_name'] = kaitori_t( 'Please enter your name (in kanji).', '氏名（漢字）を入力してください。' );
    }
    if ( empty( $kana ) ) {
        $invalid_kyc_fields['kyc_kana'] = kaitori_t( 'Please enter your name reading (in katakana).', 'フリガナ（カタカナ）を入力してください。' );
    }
    if ( empty( $email ) || ! is_email( $email ) ) {
        $invalid_kyc_fields['kyc_email'] = kaitori_t( 'Please enter a valid email address.', '有効なメールアドレスを入力してください。' );
    }
    if ( empty( $phone ) ) {
        $invalid_kyc_fields['kyc_phone'] = kaitori_t( 'Please enter your phone number.', '電話番号を入力してください。' );
    }
    if ( empty( $dob ) ) {
        $invalid_kyc_fields['kyc_dob'] = kaitori_t( 'Please select your date of birth.', '生年月日を選択してください。' );
    }
    if ( empty( $postal ) ) {
        $invalid_kyc_fields['kyc_postal'] = kaitori_t( 'Please enter your postal code.', '郵便番号を入力してください。' );
    }
    if ( empty( $pref ) ) {
        $invalid_kyc_fields['kyc_prefecture'] = kaitori_t( 'Please enter your prefecture.', '都道府県を入力してください。' );
    }
    if ( empty( $city ) ) {
        $invalid_kyc_fields['kyc_city'] = kaitori_t( 'Please enter your city, ward, or town.', '市区町村を入力してください。' );
    }
    if ( empty( $address ) ) {
        $invalid_kyc_fields['kyc_address'] = kaitori_t( 'Please enter your street address and building name.', '番地・建物名を入力してください。' );
    }

    $allowed_exts = array( 'jpg', 'jpeg', 'png', 'webp', 'pdf' );

    // Check front ID file
    $front_key = ! empty( $_FILES['kyc_id_front']['name'] ) ? 'kyc_id_front' : ( ! empty( $_FILES['kyc_front']['name'] ) ? 'kyc_front' : '' );
    if ( empty( $front_key ) || empty( $_FILES[ $front_key ]['tmp_name'] ) ) {
        $invalid_kyc_fields['kyc_id_front'] = kaitori_t( 'Please select the front image of your ID document. (Required)', '本人確認書類の表面画像を選択してください。(必須)' );
    } else {
        $front_ext = strtolower( pathinfo( $_FILES[ $front_key ]['name'], PATHINFO_EXTENSION ) );
        if ( ! in_array( $front_ext, $allowed_exts, true ) ) {
            $invalid_kyc_fields['kyc_id_front'] = sprintf(
                kaitori_t( 'The front photo format (.%s) is not supported. Allowed formats: JPG, JPEG, PNG, WEBP, PDF (max 10MB)', '表面写真の形式（.%s）は未対応です。許可形式: JPG, JPEG, PNG, WEBP, PDF（最大10MB）' ),
                esc_html( $front_ext ?: '不明' )
            );
        } elseif ( (int) $_FILES[ $front_key ]['size'] > kaitoriakiba_kyc_max_size() ) {
            $invalid_kyc_fields['kyc_id_front'] = sprintf(
                kaitori_t( 'The front image exceeds the 10MB limit (%sMB). Please choose a file of 10MB or less.', '表面画像のサイズが上限（10MB）を超えています（%sMB）。10MB以下のファイルを選択してください。' ),
                number_format( (int) $_FILES[ $front_key ]['size'] / ( 1024 * 1024 ), 1 )
            );
        }
    }

    // Check back ID file
    $is_my_number = ( false !== mb_strpos( $doc_type, 'マイナンバー' ) || false !== stripos( $doc_type, 'mynumber' ) );
    $back_key     = ! empty( $_FILES['kyc_id_back']['name'] ) ? 'kyc_id_back' : ( ! empty( $_FILES['kyc_back']['name'] ) ? 'kyc_back' : '' );
    if ( ! empty( $back_key ) && ! empty( $_FILES[ $back_key ]['name'] ) ) {
        $back_ext = strtolower( pathinfo( $_FILES[ $back_key ]['name'], PATHINFO_EXTENSION ) );
        if ( ! in_array( $back_ext, $allowed_exts, true ) ) {
            $invalid_kyc_fields['kyc_id_back'] = sprintf(
                kaitori_t( 'The back photo format (.%s) is not supported. Allowed formats: JPG, JPEG, PNG, WEBP, PDF (max 10MB)', '裏面写真の形式（.%s）は未対応です。許可形式: JPG, JPEG, PNG, WEBP, PDF（最大10MB）' ),
                esc_html( $back_ext ?: '不明' )
            );
        } elseif ( (int) $_FILES[ $back_key ]['size'] > kaitoriakiba_kyc_max_size() ) {
            $invalid_kyc_fields['kyc_id_back'] = sprintf(
                kaitori_t( 'The back image exceeds the 10MB limit (%sMB). Please choose a file of 10MB or less.', '裏面画像のサイズが上限（10MB）を超えています（%sMB）。10MB以下のファイルを選択してください。' ),
                number_format( (int) $_FILES[ $back_key ]['size'] / ( 1024 * 1024 ), 1 )
            );
        }
    }

    if ( ! empty( $invalid_kyc_fields ) ) {
        wp_send_json_error( array(
            'message'        => kaitori_t( 'There is a problem with your identity-verification details or documents. Please check the highlighted fields.', '本人確認データの入力内容または書類に不備があります。該当項目をご確認ください。' ),
            'invalid_fields' => $invalid_kyc_fields,
        ) );
    }

    // 1. Connect with WordPress User Profile
    $user_id = is_user_logged_in() ? get_current_user_id() : 0;
    if ( ! $user_id && ! empty( $email ) ) {
        $found_user = get_user_by( 'email', $email );
        if ( $found_user ) {
            $user_id = $found_user->ID;
        }
    }

    // 2. Connect with Buyback Order
    $linked_order_id = 0;
    if ( ! empty( $order_code ) ) {
        $orders = get_posts( array(
            'post_type'   => 'buyback_order',
            'title'       => $order_code,
            'numberposts' => 1,
            'post_status' => 'any',
        ) );
        if ( ! empty( $orders ) ) {
            $linked_order_id = $orders[0]->ID;
        }
    }
    if ( ! $linked_order_id && ! empty( $email ) ) {
        $orders_by_email = get_posts( array(
            'post_type'   => 'buyback_order',
            'meta_key'    => '_applicant_email',
            'meta_value'  => $email,
            'numberposts' => 1,
            'post_status' => 'any',
        ) );
        if ( ! empty( $orders_by_email ) ) {
            $linked_order_id = $orders_by_email[0]->ID;
            if ( empty( $order_code ) ) {
                $order_code = $orders_by_email[0]->post_title;
            }
        }
    }

    $kyc_title = 'KYC-' . date('Ymd') . '-' . wp_rand(1000, 9999) . ' [' . ( $name ?: ( $email ?: 'Applicant' ) ) . ']';
    $kyc_id = wp_insert_post( array(
        'post_title'  => $kyc_title,
        'post_status' => 'publish',
        'post_type'   => 'kyc_record',
    ) );

    if ( ! $kyc_id || is_wp_error( $kyc_id ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Failed to submit your identity-verification data.', '本人確認データの送信に失敗しました。' ) ) );
    }

    update_post_meta( $kyc_id, '_kyc_status', 'pending' );
    update_post_meta( $kyc_id, '_kyc_name', $name );
    update_post_meta( $kyc_id, '_kyc_kana', $kana );
    update_post_meta( $kyc_id, '_kyc_email', $email );
    update_post_meta( $kyc_id, '_kyc_phone', $phone );
    update_post_meta( $kyc_id, '_kyc_dob', $dob );
    update_post_meta( $kyc_id, '_kyc_postal', $postal );
    update_post_meta( $kyc_id, '_kyc_prefecture', $pref );
    update_post_meta( $kyc_id, '_kyc_city', $city );
    update_post_meta( $kyc_id, '_kyc_address', $address );
    update_post_meta( $kyc_id, '_kyc_job', $job );
    update_post_meta( $kyc_id, '_kyc_doc_type', $doc_type );
    update_post_meta( $kyc_id, '_kyc_order_code', $order_code );
    update_post_meta( $kyc_id, '_kyc_order_id', $linked_order_id );
    update_post_meta( $kyc_id, '_kyc_user_id', $user_id );

    // Link customer user profile
    if ( $user_id ) {
        update_user_meta( $user_id, '_kaitori_kyc_status', 'pending' );
        update_user_meta( $user_id, '_kaitori_kyc_record_id', $kyc_id );
    }

    // Link order
    if ( $linked_order_id ) {
        update_post_meta( $linked_order_id, '_order_kyc_status', 'pending' );
        update_post_meta( $linked_order_id, '_order_kyc_record_id', $kyc_id );
    }

    // Handle Uploaded ID Files — stored in a PROTECTED directory, never the public media library.
    // See inc/kyc-security.php (compliance: 古物営業法 / APPI).
    $front_key = ! empty( $_FILES['kyc_id_front']['name'] ) ? 'kyc_id_front' : ( ! empty( $_FILES['kyc_front']['name'] ) ? 'kyc_front' : '' );
    if ( $front_key ) {
        $front_path = kaitoriakiba_kyc_store_upload( $front_key, $kyc_id );
        if ( is_wp_error( $front_path ) ) {
            wp_delete_post( $kyc_id, true );
            wp_send_json_error( array(
                'message'        => $front_path->get_error_message(),
                'invalid_fields' => array( 'kyc_id_front' => $front_path->get_error_message() ),
            ) );
        }
        update_post_meta( $kyc_id, '_kyc_id_front_file', $front_path );
    }
    $back_key = ! empty( $_FILES['kyc_id_back']['name'] ) ? 'kyc_id_back' : ( ! empty( $_FILES['kyc_back']['name'] ) ? 'kyc_back' : '' );
    if ( $back_key ) {
        $back_path = kaitoriakiba_kyc_store_upload( $back_key, $kyc_id );
        if ( is_wp_error( $back_path ) ) {
            wp_delete_post( $kyc_id, true );
            wp_send_json_error( array(
                'message'        => $back_path->get_error_message(),
                'invalid_fields' => array( 'kyc_id_back' => $back_path->get_error_message() ),
            ) );
        }
        update_post_meta( $kyc_id, '_kyc_id_back_file', $back_path );
    }
    if ( ! empty( $_FILES['kyc_selfie']['name'] ) ) {
        $selfie_path = kaitoriakiba_kyc_store_upload( 'kyc_selfie', $kyc_id );
        if ( is_wp_error( $selfie_path ) ) {
            wp_delete_post( $kyc_id, true );
            wp_send_json_error( array(
                'message'        => $selfie_path->get_error_message(),
                'invalid_fields' => array( 'kyc_selfie' => $selfie_path->get_error_message() ),
            ) );
        }
        update_post_meta( $kyc_id, '_kyc_selfie_file', $selfie_path );
    }

    // Confirmation email to the customer that their eKYC documents were received.
    if ( function_exists( 'kaitoriakiba_send_kyc_submitted' ) ) {
        kaitoriakiba_send_kyc_submitted( $kyc_id );
    }

    wp_send_json_success( array(
        'kyc_id'  => $kyc_id,
        'message' => kaitori_t( 'Your identity documents were uploaded successfully. Please wait a little while for the review to complete.', '本人確認書類のアップロードが完了いたしました。審査完了まで今しばらくお待ちください。' ),
    ) );
}
add_action( 'wp_ajax_kaitori_submit_kyc', 'kaitoriakiba_ajax_submit_kyc' );
add_action( 'wp_ajax_nopriv_kaitori_submit_kyc', 'kaitoriakiba_ajax_submit_kyc' );

/**
 * Live buyback estimate for the sell-apply form. Uses the SAME server-side engine
 * as the final order, so the displayed figure matches what the order will store.
 */
function kaitoriakiba_ajax_live_estimate() {
    $price = kaitoriakiba_calculate_quote( array(
        'model'              => isset( $_REQUEST['model'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['model'] ) ) : '',
        'storage'            => isset( $_REQUEST['storage'] ) ? sanitize_text_field( $_REQUEST['storage'] ) : '256GB',
        'screen_condition'   => isset( $_REQUEST['screen_condition'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['screen_condition'] ) ) : '',
        'body_condition'     => isset( $_REQUEST['body_condition'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['body_condition'] ) ) : '',
        'battery_health'     => isset( $_REQUEST['battery_health'] ) ? intval( $_REQUEST['battery_health'] ) : 100,
        'powers_on'          => isset( $_REQUEST['powers_on'] ) ? sanitize_text_field( $_REQUEST['powers_on'] ) : 'yes',
        'camera_works'       => isset( $_REQUEST['camera_works'] ) ? sanitize_text_field( $_REQUEST['camera_works'] ) : 'yes',
        'installment_status' => isset( $_REQUEST['installment_status'] ) ? sanitize_text_field( $_REQUEST['installment_status'] ) : 'no',
    ) );
    wp_send_json_success( array(
        'price'     => $price,
        'formatted' => '¥' . number_format( $price ),
    ) );
}
add_action( 'wp_ajax_kaitori_live_estimate', 'kaitoriakiba_ajax_live_estimate' );
add_action( 'wp_ajax_nopriv_kaitori_live_estimate', 'kaitoriakiba_ajax_live_estimate' );

/**
 * Live Device Filter AJAX Query
 */
function kaitoriakiba_ajax_filter_devices() {
    // Shared filter logic (same as the main archive query) lives in inc/device-filter.php.
    $p     = kaitoriakiba_device_filter_params();
    $paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
    $args  = kaitoriakiba_device_query_args( $p, $paged, 24 );
    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        echo '<div class="row pro_list_row">';
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/content', 'device-card' );
        }
        echo '</div>';

        // Pagination (links handled by theme-app.js → AJAX, no reload).
        $total = (int) $query->max_num_pages;
        if ( $total > 1 ) {
            echo '<div class="text-center pro-ajax-pagination" style="margin-top:30px;">';
            echo '<ul class="page-numbers" style="list-style:none;padding:0;margin:0;display:inline-flex;gap:6px;flex-wrap:wrap;justify-content:center;">';
            for ( $i = 1; $i <= $total; $i++ ) {
                $is    = ( $i === $paged );
                $style = 'display:inline-block;min-width:38px;text-align:center;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700;border:1.5px solid '
                    . ( $is ? '#ef4444;background:#ef4444;color:#fff;' : '#e2e8f0;background:#fff;color:#334155;' );
                echo '<li><a href="#" data-paged="' . esc_attr( $i ) . '" class="page-number' . ( $is ? ' current' : '' ) . '" style="' . esc_attr( $style ) . '">' . esc_html( $i ) . '</a></li>';
            }
            echo '</ul></div>';
        }
        wp_reset_postdata();
    } else {
        echo '<div class="alert alert-warning text-center" style="padding:40px 20px;border-radius:12px;margin-top:20px;">'
            . '<i class="fa fa-search" style="font-size:28px;color:#f59e0b;display:block;margin-bottom:10px;"></i>'
            . '<strong>' . esc_html( kaitori_t( 'No matching buyback devices were found', '該当する買取機種が見つかりませんでした' ) ) . '</strong><br>'
            . '<span style="color:#64748b;font-size:13px;">' . esc_html( kaitori_t( 'Please try different search conditions.', '条件を変えて検索してください。' ) ) . '</span>'
            . '</div>';
    }
    $html  = ob_get_clean();
    $count = (int) $query->found_posts;

    wp_send_json_success( array(
        'html'        => $html,
        'count'       => $count,
        'count_label' => sprintf( kaitori_t( '%s items', '全 %s 件' ), number_format_i18n( $count ) ),
    ) );
}
add_action( 'wp_ajax_kaitori_filter_devices', 'kaitoriakiba_ajax_filter_devices' );
add_action( 'wp_ajax_nopriv_kaitori_filter_devices', 'kaitoriakiba_ajax_filter_devices' );

/**
 * Handle Member Registration via AJAX
 */
function kaitoriakiba_ajax_register_user() {
    // Non-fatal nonce check: a stale nonce (e.g. a cached page) returns a clear,
    // readable message instead of a cryptic "-1" that surfaces as a generic error.
    if ( ! check_ajax_referer( 'kaitori_nonce', 'security', false ) ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'Your session expired. Please reload the page and try again.',
            'セッションの有効期限が切れました。ページを再読み込みしてお試しください。'
        ) ) );
    }
    kaitoriakiba_guard_public_form( 'register', 20 );

    $email      = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $password   = isset( $_POST['password'] ) ? $_POST['password'] : '';
    $last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
    $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
    $phone      = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Please enter a valid email address.', '有効なメールアドレスを入力してください。' ) ) );
    }

    if ( email_exists( $email ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'This email address is already registered. Please log in instead.', 'このメールアドレスは既に登録されています。ログインしてください。' ) ) );
    }

    if ( strlen( $password ) < 8 ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Your password must be at least 8 characters.', 'パスワードは8文字以上で設定してください。' ) ) );
    }
    // Require a mix of letters and numbers for basic strength.
    if ( ! preg_match( '/[A-Za-z]/', $password ) || ! preg_match( '/\d/', $password ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Your password must contain both letters and numbers.', 'パスワードは英字と数字を組み合わせてください。' ) ) );
    }

    $username = sanitize_user( current( explode( '@', $email ) ) );
    if ( username_exists( $username ) ) {
        $username .= '_' . wp_rand( 100, 999 );
    }

    $user_id = wp_create_user( $username, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
    }

    wp_update_user( array(
        'ID'         => $user_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'display_name' => trim( $last_name . ' ' . $first_name ),
    ) );

    if ( ! empty( $phone ) ) {
        update_user_meta( $user_id, 'billing_phone', $phone );
        update_user_meta( $user_id, 'phone_number', $phone );
    }

    // Welcome email to the new member (requires an SMTP mailer to actually deliver).
    if ( function_exists( 'kaitoriakiba_send_registration_welcome' ) ) {
        kaitoriakiba_send_registration_welcome( $user_id );
    }

    // Auto sign-in new user
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );

    wp_send_json_success( array(
        'user_id' => $user_id,
        'message' => kaitori_t( 'Registration complete! Redirecting you now.', '会員登録が完了いたしました！マイページへ移動します。' ),
        'redirect_url' => home_url( '/sell-apply/' ),
    ) );
}
add_action( 'wp_ajax_kaitori_register_user', 'kaitoriakiba_ajax_register_user' );
add_action( 'wp_ajax_nopriv_kaitori_register_user', 'kaitoriakiba_ajax_register_user' );

/**
 * Handle Member Login via AJAX
 */
function kaitoriakiba_ajax_login_user() {
    // Non-fatal nonce check (clear message instead of a cryptic "-1").
    if ( ! check_ajax_referer( 'kaitori_nonce', 'security', false ) ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'Your session expired. Please reload the page and try again.',
            'セッションの有効期限が切れました。ページを再読み込みしてお試しください。'
        ) ) );
    }
    kaitoriakiba_guard_public_form( 'login', 15 );

    $user_login = isset( $_POST['log'] ) ? sanitize_text_field( $_POST['log'] ) : '';
    $user_pass  = isset( $_POST['pwd'] ) ? $_POST['pwd'] : '';
    $remember   = ! empty( $_POST['rememberme'] );

    if ( empty( $user_login ) || empty( $user_pass ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Please enter your email address (or username) and password.', 'メールアドレス（またはユーザー名）とパスワードを入力してください。' ) ) );
    }

    $creds = array(
        'user_login'    => $user_login,
        'user_password' => $user_pass,
        'remember'      => $remember,
    );

    $user = wp_signon( $creds, is_ssl() );

    if ( is_wp_error( $user ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'The login details are incorrect. Please check your email address and password.', 'ログイン情報が正しくありません。パスワードまたはメールアドレスをご確認ください。' ) ) );
    }

    wp_send_json_success( array(
        'message'      => kaitori_t( 'Logged in successfully! Redirecting you now.', 'ログインに成功しました！マイページへ移動します。' ),
        'redirect_url' => home_url( '/sell-apply/' ),
    ) );
}
add_action( 'wp_ajax_kaitori_login_user', 'kaitoriakiba_ajax_login_user' );
add_action( 'wp_ajax_nopriv_kaitori_login_user', 'kaitoriakiba_ajax_login_user' );

/**
 * Get a user's favorite device ids.
 *
 * @param int|null $user_id
 * @return int[]
 */
function kaitoriakiba_get_user_favorites( $user_id = null ) {
    $user_id = $user_id ?: get_current_user_id();
    if ( ! $user_id ) {
        return array();
    }
    $favs = get_user_meta( $user_id, '_kaitori_favorites', true );
    if ( ! is_array( $favs ) ) {
        return array();
    }
    return array_values( array_unique( array_filter( array_map( 'intval', $favs ) ) ) );
}

/**
 * Toggle a device in the current user's favorites (★).
 * Logged-in users persist to user meta; guests get a flag so the client can
 * keep the state in localStorage and prompt to register.
 */
function kaitoriakiba_ajax_toggle_favorite() {
    check_ajax_referer( 'kaitori_nonce', 'security' );

    $device_id = isset( $_POST['device_id'] ) ? intval( $_POST['device_id'] ) : 0;
    if ( ! $device_id || 'kaitori_device' !== get_post_type( $device_id ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'The specified device could not be found.', '対象の端末が見つかりませんでした。' ) ) );
    }

    if ( ! is_user_logged_in() ) {
        wp_send_json_success( array(
            'guest'      => true,
            'device_id'  => $device_id,
            'login_url'  => home_url( '/register/' ),
            'message'    => kaitori_t( 'You must register and log in to save favorites.', 'お気に入りの保存には会員登録・ログインが必要です。' ),
        ) );
    }

    $uid  = get_current_user_id();
    $favs = kaitoriakiba_get_user_favorites( $uid );

    if ( in_array( $device_id, $favs, true ) ) {
        $favs   = array_values( array_diff( $favs, array( $device_id ) ) );
        $active = false;
    } else {
        $favs[] = $device_id;
        $active = true;
    }
    update_user_meta( $uid, '_kaitori_favorites', $favs );

    wp_send_json_success( array(
        'active' => $active,
        'count'  => count( $favs ),
    ) );
}
add_action( 'wp_ajax_kaitori_toggle_favorite', 'kaitoriakiba_ajax_toggle_favorite' );
add_action( 'wp_ajax_nopriv_kaitori_toggle_favorite', 'kaitoriakiba_ajax_toggle_favorite' );


