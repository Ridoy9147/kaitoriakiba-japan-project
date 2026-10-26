<?php
/**
 * Kaitori Akiba — Customer transactional emails.
 *
 * Sends the customer (not just the admin) an order confirmation on submission,
 * and a notification whenever an admin changes the order status. Uses wp_mail so
 * any SMTP/transactional-email plugin (SendGrid, Mailgun, WP Mail SMTP) works
 * transparently. Templates and headers are filterable.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** HTML email headers with a friendly From. */
function kaitoriakiba_email_headers() {
    $from_name  = apply_filters( 'kaitori_email_from_name', get_bloginfo( 'name' ) );
    $from_email = apply_filters( 'kaitori_email_from_address', get_option( 'admin_email' ) );
    return array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
    );
}

/** Wrap body content in a minimal branded HTML shell. */
function kaitoriakiba_email_wrap( $title, $body_html ) {
    $site = esc_html( get_bloginfo( 'name' ) );
    $html  = '<div style="max-width:600px;margin:0 auto;font-family:-apple-system,Segoe UI,Roboto,sans-serif;color:#0f172a;">';
    $html .= '<div style="background:#0f172a;color:#fff;padding:18px 24px;font-size:18px;font-weight:800;">KAITORI AKIBA <span style="color:#ef4444;">買取アキバ</span></div>';
    $html .= '<div style="border:1px solid #e2e8f0;border-top:none;padding:24px;">';
    $html .= '<h2 style="font-size:18px;margin:0 0 16px;">' . esc_html( $title ) . '</h2>';
    $html .= $body_html;
    $html .= '<p style="margin-top:24px;font-size:12px;color:#94a3b8;border-top:1px solid #f1f5f9;padding-top:14px;">© ' . esc_html( date( 'Y' ) ) . ' ' . $site . '. ' . esc_html__( 'このメールは送信専用です。', 'kaitoriakiba' ) . '</p>';
    $html .= '</div></div>';
    return $html;
}

/**
 * Send the buyback order confirmation to the customer.
 *
 * @param int $order_id
 */
function kaitoriakiba_send_order_confirmation( $order_id ) {
    $email = get_post_meta( $order_id, '_applicant_email', true );
    if ( ! is_email( $email ) ) {
        return;
    }
    $name  = get_post_meta( $order_id, '_applicant_name', true );
    $model = get_post_meta( $order_id, '_device_model', true );
    $stor  = get_post_meta( $order_id, '_device_storage', true );
    $price = (int) get_post_meta( $order_id, '_estimated_price', true );
    $code  = get_the_title( $order_id );
    $kyc_url = home_url( '/kyc-verify/?order=' . rawurlencode( $code ) );

    $body  = '<p>' . sprintf( esc_html__( '%s 様', 'kaitoriakiba' ), esc_html( $name ) ) . '</p>';
    $body .= '<p>' . esc_html__( '買取お申込みを受け付けました。内容は以下の通りです。', 'kaitoriakiba' ) . '</p>';
    $body .= '<table style="width:100%;border-collapse:collapse;font-size:14px;margin:12px 0;">';
    $body .= '<tr><td style="padding:6px 0;color:#64748b;">' . esc_html__( '受付番号', 'kaitoriakiba' ) . '</td><td style="padding:6px 0;font-weight:700;">' . esc_html( $code ) . '</td></tr>';
    $body .= '<tr><td style="padding:6px 0;color:#64748b;">' . esc_html__( '機種', 'kaitoriakiba' ) . '</td><td style="padding:6px 0;">' . esc_html( trim( $model . ' ' . $stor ) ) . '</td></tr>';
    $body .= '<tr><td style="padding:6px 0;color:#64748b;">' . esc_html__( '概算査定額', 'kaitoriakiba' ) . '</td><td style="padding:6px 0;font-weight:800;color:#dc2626;">¥' . number_format( $price ) . '</td></tr>';
    $body .= '</table>';
    $body .= '<p>' . esc_html__( '古物営業法に基づき、初回のお取引には本人確認（eKYC）が必要です。下記より書類をご提出ください。', 'kaitoriakiba' ) . '</p>';
    $body .= '<p><a href="' . esc_url( $kyc_url ) . '" style="display:inline-block;background:#ef4444;color:#fff;font-weight:700;padding:12px 24px;border-radius:8px;text-decoration:none;">' . esc_html__( '本人確認 (eKYC) に進む', 'kaitoriakiba' ) . '</a></p>';

    $subject = sprintf( esc_html__( '【買取アキバ】買取お申込みを受け付けました（%s）', 'kaitoriakiba' ), $code );
    $html    = kaitoriakiba_email_wrap( esc_html__( '買取お申込み受付のご案内', 'kaitoriakiba' ), $body );

    wp_mail( $email, $subject, apply_filters( 'kaitori_order_confirmation_html', $html, $order_id ), kaitoriakiba_email_headers() );
}

/**
 * Notify the customer when an admin changes the order status.
 * Hooked on meta update so it fires only on a real status change (not on create).
 *
 * @param int    $meta_id
 * @param int    $post_id
 * @param string $meta_key
 * @param mixed  $meta_value
 */
function kaitoriakiba_order_status_changed_email( $meta_id, $post_id, $meta_key, $meta_value ) {
    if ( '_order_status' !== $meta_key ) {
        return;
    }
    if ( 'buyback_order' !== get_post_type( $post_id ) ) {
        return;
    }
    $email = get_post_meta( $post_id, '_applicant_email', true );
    if ( ! is_email( $email ) ) {
        return;
    }

    $status = (string) $meta_value;
    $info   = function_exists( 'kaitoriakiba_get_order_status' ) ? kaitoriakiba_get_order_status( $status ) : array( 'label' => $status, 'label_ja' => '' );
    $name   = get_post_meta( $post_id, '_applicant_name', true );
    $code   = get_the_title( $post_id );

    // Optional per-status message to the customer.
    $messages = apply_filters( 'kaitori_status_messages', array(
        'received'   => esc_html__( '端末が当店に到着いたしました。これより検品を開始いたします。', 'kaitoriakiba' ),
        'inspecting' => esc_html__( '検品を行っております。結果が確定次第ご連絡いたします。', 'kaitoriakiba' ),
        'inspected'  => esc_html__( '検品が完了し、最終査定額が確定いたしました。詳細をご確認ください。', 'kaitoriakiba' ),
        'paid'       => esc_html__( 'お振込みが完了いたしました。ご利用誠にありがとうございました。', 'kaitoriakiba' ),
        'cancelled'  => esc_html__( '本件のお取引はキャンセル／返送となりました。', 'kaitoriakiba' ),
    ) );
    $extra = isset( $messages[ $status ] ) ? $messages[ $status ] : '';

    $body  = '<p>' . sprintf( esc_html__( '%s 様', 'kaitoriakiba' ), esc_html( $name ) ) . '</p>';
    $body .= '<p>' . sprintf( esc_html__( 'お申込み（%s）のステータスが更新されました。', 'kaitoriakiba' ), '<strong>' . esc_html( $code ) . '</strong>' ) . '</p>';
    $body .= '<p style="font-size:16px;"><strong>' . esc_html__( '現在のステータス:', 'kaitoriakiba' ) . '</strong> <span style="color:#dc2626;font-weight:800;">' . esc_html( $info['label_ja'] ?: $info['label'] ) . '</span></p>';
    if ( $extra ) {
        $body .= '<p>' . $extra . '</p>';
    }
    $body .= '<p><a href="' . esc_url( home_url( '/mypage/' ) ) . '" style="display:inline-block;background:#0f172a;color:#fff;font-weight:700;padding:10px 20px;border-radius:8px;text-decoration:none;">' . esc_html__( 'マイページで確認', 'kaitoriakiba' ) . '</a></p>';

    $subject = sprintf( esc_html__( '【買取アキバ】お申込みステータス更新（%s）', 'kaitoriakiba' ), $code );
    $html    = kaitoriakiba_email_wrap( esc_html__( 'ステータス更新のお知らせ', 'kaitoriakiba' ), $body );

    wp_mail( $email, $subject, apply_filters( 'kaitori_status_email_html', $html, $post_id, $status ), kaitoriakiba_email_headers() );
}
add_action( 'updated_post_meta', 'kaitoriakiba_order_status_changed_email', 10, 4 );
