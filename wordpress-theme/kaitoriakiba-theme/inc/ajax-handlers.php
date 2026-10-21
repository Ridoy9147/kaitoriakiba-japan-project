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

    $model          = isset( $_POST['model'] ) ? sanitize_text_field( $_POST['model'] ) : '';
    $storage        = isset( $_POST['storage'] ) ? sanitize_text_field( $_POST['storage'] ) : '';
    $color          = isset( $_POST['color'] ) ? sanitize_text_field( $_POST['color'] ) : '';
    $carrier        = isset( $_POST['carrier'] ) ? sanitize_text_field( $_POST['carrier'] ) : '';
    $imei           = isset( $_POST['imei'] ) ? sanitize_text_field( $_POST['imei'] ) : '';
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
    $shipping_kit   = isset( $_POST['shipping_kit_needed'] ) ? sanitize_text_field( $_POST['shipping_kit_needed'] ) : 'no';
    $pickup_dt      = isset( $_POST['pickup_datetime'] ) ? sanitize_text_field( $_POST['pickup_datetime'] ) : '';
    $bank_name      = isset( $_POST['bank_name'] ) ? sanitize_text_field( $_POST['bank_name'] ) : '';
    $branch_name    = isset( $_POST['bank_branch'] ) ? sanitize_text_field( $_POST['bank_branch'] ) : '';
    $acc_type       = isset( $_POST['bank_account_type'] ) ? sanitize_text_field( $_POST['bank_account_type'] ) : '普通';
    $acc_number     = isset( $_POST['bank_account_number'] ) ? sanitize_text_field( $_POST['bank_account_number'] ) : '';
    $holder_kana    = isset( $_POST['bank_holder_kana'] ) ? sanitize_text_field( $_POST['bank_holder_kana'] ) : '';
    $est_price      = isset( $_POST['estimated_price'] ) ? intval( $_POST['estimated_price'] ) : 0;

    // Customer
    $name           = isset( $_POST['applicant_name'] ) ? sanitize_text_field( $_POST['applicant_name'] ) : 'Guest Customer';
    $kana           = isset( $_POST['applicant_kana'] ) ? sanitize_text_field( $_POST['applicant_kana'] ) : '';
    $email          = isset( $_POST['applicant_email'] ) ? sanitize_email( $_POST['applicant_email'] ) : '';
    $phone          = isset( $_POST['applicant_phone'] ) ? sanitize_text_field( $_POST['applicant_phone'] ) : '';
    $accessories    = isset( $_POST['accessories'] ) && is_array( $_POST['accessories'] ) ? array_map( 'sanitize_text_field', $_POST['accessories'] ) : array();

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
        wp_send_json_error( array( 'message' => __( '申し訳ありません。申込の送信中にエラーが発生しました。', 'kaitoriakiba' ) ) );
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
    update_post_meta( $order_id, '_bank_account_number', $acc_number );
    update_post_meta( $order_id, '_bank_holder_kana', $holder_kana );
    update_post_meta( $order_id, '_estimated_price', $est_price );

    // Link WordPress User Profile
    $user_id = is_user_logged_in() ? get_current_user_id() : 0;
    if ( ! $user_id && ! empty( $email ) ) {
        $found_user = get_user_by( 'email', $email );
        if ( $found_user ) {
            $user_id = $found_user->ID;
        }
    }
    update_post_meta( $order_id, '_customer_user_id', $user_id );

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
        update_post_meta( $order_id, '_order_kyc_status', $kyc_st );
        update_post_meta( $order_id, '_order_kyc_record_id', $existing_kyc[0]->ID );
        update_post_meta( $existing_kyc[0]->ID, '_kyc_order_id', $order_id );
        update_post_meta( $existing_kyc[0]->ID, '_kyc_order_code', $order_title );
    } else {
        update_post_meta( $order_id, '_order_kyc_status', 'unverified' );
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

    wp_send_json_success( array(
        'order_id'    => $order_id,
        'order_code'  => $order_title,
        'message'     => __( '買取申込が正常に送信されました。確認メールをご確認ください。', 'kaitoriakiba' ),
    ) );
}
add_action( 'wp_ajax_kaitori_submit_application', 'kaitoriakiba_ajax_submit_application' );
add_action( 'wp_ajax_nopriv_kaitori_submit_application', 'kaitoriakiba_ajax_submit_application' );

/**
 * Handle eKYC ID Submission & Connect with Customer Profile + Orders
 */
function kaitoriakiba_ajax_submit_kyc() {
    check_ajax_referer( 'kaitori_nonce', 'security' );

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
        wp_send_json_error( array( 'message' => __( '本人確認データの送信に失敗しました。', 'kaitoriakiba' ) ) );
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

    // Handle Uploaded ID Files
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

    $front_key = ! empty( $_FILES['kyc_id_front'] ) ? 'kyc_id_front' : ( ! empty( $_FILES['kyc_front'] ) ? 'kyc_front' : '' );
    if ( $front_key ) {
        $front_id = media_handle_upload( $front_key, $kyc_id );
        if ( ! is_wp_error( $front_id ) ) {
            update_post_meta( $kyc_id, '_kyc_id_front_attach', $front_id );
        }
    }
    $back_key = ! empty( $_FILES['kyc_id_back'] ) ? 'kyc_id_back' : ( ! empty( $_FILES['kyc_back'] ) ? 'kyc_back' : '' );
    if ( $back_key ) {
        $back_id = media_handle_upload( $back_key, $kyc_id );
        if ( ! is_wp_error( $back_id ) ) {
            update_post_meta( $kyc_id, '_kyc_id_back_attach', $back_id );
        }
    }
    if ( ! empty( $_FILES['kyc_selfie'] ) ) {
        $selfie_id = media_handle_upload( 'kyc_selfie', $kyc_id );
        if ( ! is_wp_error( $selfie_id ) ) {
            update_post_meta( $kyc_id, '_kyc_selfie_attach', $selfie_id );
        }
    }

    wp_send_json_success( array(
        'kyc_id'  => $kyc_id,
        'message' => __( '本人確認書類のアップロードが完了いたしました。審査完了まで今しばらくお待ちください。', 'kaitoriakiba' ),
    ) );
}
add_action( 'wp_ajax_kaitori_submit_kyc', 'kaitoriakiba_ajax_submit_kyc' );
add_action( 'wp_ajax_nopriv_kaitori_submit_kyc', 'kaitoriakiba_ajax_submit_kyc' );

/**
 * Live Device Filter AJAX Query
 */
function kaitoriakiba_ajax_filter_devices() {
    $keyword = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';
    $cat     = isset( $_GET['cat'] ) ? sanitize_text_field( $_GET['cat'] ) : '';
    $carrier = isset( $_GET['s_career'] ) ? sanitize_text_field( $_GET['s_career'] ) : '';
    $brand   = isset( $_GET['brand'] ) ? sanitize_text_field( $_GET['brand'] ) : '';
    $series  = isset( $_GET['series'] ) ? sanitize_text_field( $_GET['series'] ) : '';

    $args = array(
        'post_type'      => 'kaitori_device',
        'posts_per_page' => 24,
        'post_status'    => 'publish',
    );

    if ( ! empty( $keyword ) ) {
        $args['s'] = $keyword;
    }

    $tax_query = array();

    if ( ! empty( $cat ) ) {
        $tax_query[] = array(
            'taxonomy' => 'device_category',
            'field'    => is_numeric( $cat ) ? 'term_id' : 'slug',
            'terms'    => $cat,
        );
    }
    if ( ! empty( $carrier ) ) {
        $tax_query[] = array(
            'taxonomy' => 'carrier',
            'field'    => is_numeric( $carrier ) ? 'term_id' : 'slug',
            'terms'    => $carrier,
        );
    }
    if ( ! empty( $brand ) ) {
        $tax_query[] = array(
            'taxonomy' => 'brand',
            'field'    => is_numeric( $brand ) ? 'term_id' : 'slug',
            'terms'    => $brand,
        );
    }
    if ( ! empty( $series ) ) {
        $tax_query[] = array(
            'taxonomy' => 'series',
            'field'    => is_numeric( $series ) ? 'term_id' : 'slug',
            'terms'    => $series,
        );
    }

    if ( ! empty( $tax_query ) ) {
        $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        echo '<div class="row pro_list_row">';
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/content', 'device-card' );
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<div class="alert alert-warning text-center" style="margin: 30px 0;">'
           . '<i class="fa fa-info-circle"></i> 条件に該当する端末が見つかりませんでした。別のキーワードまたは条件でお試しください。'
           . '</div>';
    }

    wp_die();
}
add_action( 'wp_ajax_kaitori_filter_devices', 'kaitoriakiba_ajax_filter_devices' );
add_action( 'wp_ajax_nopriv_kaitori_filter_devices', 'kaitoriakiba_ajax_filter_devices' );

/**
 * Handle Member Registration via AJAX
 */
function kaitoriakiba_ajax_register_user() {
    check_ajax_referer( 'kaitori_nonce', 'security' );

    $email      = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $password   = isset( $_POST['password'] ) ? $_POST['password'] : '';
    $last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
    $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
    $phone      = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => __( '有効なメールアドレスを入力してください。', 'kaitoriakiba' ) ) );
    }

    if ( email_exists( $email ) ) {
        wp_send_json_error( array( 'message' => __( 'このメールアドレスは既に登録されています。ログインしてください。', 'kaitoriakiba' ) ) );
    }

    if ( strlen( $password ) < 6 ) {
        wp_send_json_error( array( 'message' => __( 'パスワードは6文字以上で設定してください。', 'kaitoriakiba' ) ) );
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

    // Auto sign-in new user
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );

    wp_send_json_success( array(
        'user_id' => $user_id,
        'message' => __( '会員登録が完了いたしました！マイページへ移動します。', 'kaitoriakiba' ),
        'redirect_url' => home_url( '/sell-apply/' ),
    ) );
}
add_action( 'wp_ajax_kaitori_register_user', 'kaitoriakiba_ajax_register_user' );
add_action( 'wp_ajax_nopriv_kaitori_register_user', 'kaitoriakiba_ajax_register_user' );

/**
 * Handle Member Login via AJAX
 */
function kaitoriakiba_ajax_login_user() {
    check_ajax_referer( 'kaitori_nonce', 'security' );

    $user_login = isset( $_POST['log'] ) ? sanitize_text_field( $_POST['log'] ) : '';
    $user_pass  = isset( $_POST['pwd'] ) ? $_POST['pwd'] : '';
    $remember   = ! empty( $_POST['rememberme'] );

    if ( empty( $user_login ) || empty( $user_pass ) ) {
        wp_send_json_error( array( 'message' => __( 'メールアドレス（またはユーザー名）とパスワードを入力してください。', 'kaitoriakiba' ) ) );
    }

    $creds = array(
        'user_login'    => $user_login,
        'user_password' => $user_pass,
        'remember'      => $remember,
    );

    $user = wp_signon( $creds, is_ssl() );

    if ( is_wp_error( $user ) ) {
        wp_send_json_error( array( 'message' => __( 'ログイン情報が正しくありません。パスワードまたはメールアドレスをご確認ください。', 'kaitoriakiba' ) ) );
    }

    wp_send_json_success( array(
        'message'      => __( 'ログインに成功しました！マイページへ移動します。', 'kaitoriakiba' ),
        'redirect_url' => home_url( '/sell-apply/' ),
    ) );
}
add_action( 'wp_ajax_kaitori_login_user', 'kaitoriakiba_ajax_login_user' );
add_action( 'wp_ajax_nopriv_kaitori_login_user', 'kaitoriakiba_ajax_login_user' );


