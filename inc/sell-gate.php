<?php
/**
 * Kaitori Akiba — Buyback Form Gate (login + eKYC required)
 *
 * A customer must be logged in AND have completed eKYC before they can fill out /
 * submit the buyback application. The eKYC requirement level is admin-configurable:
 *   - 'off'       : no eKYC required (login still required)
 *   - 'submitted' : eKYC documents uploaded (status pending or verified) — default
 *   - 'verified'  : admin-approved eKYC only
 *
 * Enforced in the UI (template + Elementor widget) and server-side (AJAX handler).
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Current gate mode. */
function kaitoriakiba_sell_gate_mode() {
    $mode = get_option( 'kaitori_sell_gate_mode', 'submitted' );
    return in_array( $mode, array( 'off', 'submitted', 'verified' ), true ) ? $mode : 'submitted';
}

/**
 * Whether the current (or given) user may use the buyback form.
 *
 * @param int|null $user_id
 * @return array{0:bool,1:string} [allowed, reason] reason ∈ login|need_kyc|pending|rejected|''
 */
function kaitoriakiba_user_can_sell( $user_id = null ) {
    // Always let admins/staff through (for previews and testing).
    if ( current_user_can( 'manage_options' ) ) {
        return array( true, '' );
    }

    // Login is always required.
    if ( ! is_user_logged_in() ) {
        return array( false, 'login' );
    }

    $mode = kaitoriakiba_sell_gate_mode();
    if ( 'off' === $mode ) {
        return array( true, '' );
    }

    $user_id = $user_id ?: get_current_user_id();
    $email   = wp_get_current_user()->user_email;
    $status  = function_exists( 'kaitoriakiba_lookup_kyc_status' )
        ? kaitoriakiba_lookup_kyc_status( $email, $user_id )
        : ( get_user_meta( $user_id, '_kaitori_kyc_status', true ) ?: 'none' );

    if ( 'verified' === $mode ) {
        if ( 'verified' === $status ) { return array( true, '' ); }
        if ( 'pending' === $status )  { return array( false, 'pending' ); }
        if ( 'rejected' === $status ) { return array( false, 'rejected' ); }
        return array( false, 'need_kyc' );
    }

    // 'submitted' mode.
    if ( in_array( $status, array( 'pending', 'verified' ), true ) ) { return array( true, '' ); }
    if ( 'rejected' === $status ) { return array( false, 'rejected' ); }
    return array( false, 'need_kyc' );
}

/**
 * Output the gate notice (shown instead of the form). Returns nothing.
 *
 * @param string $reason
 */
function kaitoriakiba_render_sell_gate( $reason ) {
    $login_url = home_url( '/register/?action=login' );
    $reg_url   = home_url( '/register/' );
    $kyc_url   = home_url( '/kyc-verify/' );

    $map = array(
        'login' => array(
            'icon'  => 'fa-sign-in',
            'title' => kaitori_t( 'Login Required', 'ログインが必要です' ),
            'desc'  => kaitori_t( 'A member account and login are required to apply for buyback. If this is your first time, please register and complete identity verification (eKYC).', '買取のお申込みには会員登録・ログインが必要です。はじめての方は会員登録のうえ、本人確認（eKYC）をお済ませください。' ),
            'btns'  => array(
                array( $login_url, kaitori_t( 'Login', 'ログイン' ), true ),
                array( $reg_url,   kaitori_t( 'Create Account', '新規会員登録' ), false ),
            ),
        ),
        'need_kyc' => array(
            'icon'  => 'fa-id-card-o',
            'title' => kaitori_t( 'Identity Verification (eKYC) Required', '本人確認（eKYC）が必要です' ),
            'desc'  => kaitori_t( 'Under the Antique Dealer Act, you must submit identity documents before applying for buyback. Please upload a driver license, My Number card, etc.', '古物営業法に基づき、買取お申込みの前に本人確認書類のご提出が必要です。運転免許証・マイナンバーカード等をアップロードしてください。' ),
            'btns'  => array( array( $kyc_url, kaitori_t( 'Proceed to Identity Verification (eKYC)', '本人確認（eKYC）に進む' ), true ) ),
        ),
        'pending' => array(
            'icon'  => 'fa-clock-o',
            'title' => kaitori_t( 'Identity Verification Under Review', '本人確認を審査中です' ),
            'desc'  => kaitori_t( 'We are reviewing the identity documents you submitted. Once approved, you will be able to apply for buyback. Please wait a little longer.', 'ご提出いただいた本人確認書類を確認しております。承認が完了しましたら買取お申込みが可能になります。今しばらくお待ちください。' ),
            'btns'  => array( array( $kyc_url, kaitori_t( 'Check Submission Status', '提出状況を確認' ), false ) ),
        ),
        'rejected' => array(
            'icon'  => 'fa-exclamation-triangle',
            'title' => kaitori_t( 'There Is a Problem with Your Verification', '本人確認に不備があります' ),
            'desc'  => kaitori_t( 'There was a problem with the documents you submitted. Please re-submit your identity documents.', 'ご提出いただいた書類に不備がありました。お手数ですが、本人確認書類を再度ご提出ください。' ),
            'btns'  => array( array( $kyc_url, kaitori_t( 'Re-submit Identity Verification', '本人確認を再提出' ), true ) ),
        ),
    );
    $g = isset( $map[ $reason ] ) ? $map[ $reason ] : $map['need_kyc'];
    ?>
    <div class="container" style="margin:40px auto;">
        <div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.05);padding:40px 32px;text-align:center;">
            <div style="width:72px;height:72px;border-radius:50%;background:#fff1f2;color:#ef4444;font-size:32px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                <i class="fa <?php echo esc_attr( $g['icon'] ); ?>"></i>
            </div>
            <h1 style="font-size:20px;font-weight:900;color:#0f172a;margin-bottom:10px;"><?php echo esc_html( $g['title'] ); ?></h1>
            <p style="color:#475569;font-size:14px;line-height:1.8;margin-bottom:22px;"><?php echo esc_html( $g['desc'] ); ?></p>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <?php foreach ( $g['btns'] as $b ) : ?>
                    <a href="<?php echo esc_url( $b[0] ); ?>" class="btn <?php echo $b[2] ? 'btn-danger' : 'btn-default'; ?>" style="font-weight:700;border-radius:10px;padding:12px 26px;<?php echo $b[2] ? 'background:#ef4444;border-color:#ef4444;color:#fff;' : ''; ?>"><?php echo esc_html( $b[1] ); ?></a>
                <?php endforeach; ?>
            </div>
            <p style="margin-top:22px;font-size:12.5px;color:#94a3b8;">
                <?php
                printf(
                    /* translators: %s = flow steps */
                    esc_html( kaitori_t( 'Application flow: %s', 'お申込みの流れ: %s' ) ),
                    esc_html( kaitori_t( '① Register / Login → ② Identity Verification (eKYC) → ③ Buyback Application', '① 会員登録・ログイン → ② 本人確認(eKYC) → ③ 買取お申込み' ) )
                );
                ?>
            </p>
        </div>
    </div>
    <?php
}

/**
 * If the buyback form should be blocked for the current user, render the gate and
 * return true. In the Elementor editor/preview it never blocks (so admins can design).
 *
 * @return bool blocked
 */
function kaitoriakiba_maybe_block_sell_form() {
    if ( function_exists( 'kaitoriakiba_is_elementor_active' ) && kaitoriakiba_is_elementor_active() ) {
        return false;
    }
    list( $allowed, $reason ) = kaitoriakiba_user_can_sell();
    if ( $allowed ) {
        return false;
    }
    kaitoriakiba_render_sell_gate( $reason );
    return true;
}

/* ----------------------------- Settings page ----------------------------- */

/** Add the gate setting under Appearance. */
function kaitoriakiba_sell_gate_settings_menu() {
    add_theme_page(
        kaitori_t( 'Buyback Form Gate', '買取フォームのゲート' ),
        kaitori_t( 'Buyback Gate', '買取ゲート' ),
        'manage_options',
        'kaitori-sell-gate',
        'kaitoriakiba_sell_gate_settings_page'
    );
}
add_action( 'admin_menu', 'kaitoriakiba_sell_gate_settings_menu' );

/** Render + save the setting. */
function kaitoriakiba_sell_gate_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html( kaitori_t( 'Permission denied.', '権限がありません。' ) ) );
    }
    $saved = false;
    if ( isset( $_POST['kaitori_sell_gate_mode'] ) && check_admin_referer( 'kaitori_sell_gate' ) ) {
        $mode = sanitize_text_field( $_POST['kaitori_sell_gate_mode'] );
        if ( in_array( $mode, array( 'off', 'submitted', 'verified' ), true ) ) {
            update_option( 'kaitori_sell_gate_mode', $mode );
            $saved = true;
        }
    }
    $mode = kaitoriakiba_sell_gate_mode();
    $opts = array(
        'submitted' => kaitori_t( 'eKYC submitted (upload is enough) — Recommended', 'eKYC提出済み（アップロードすれば可）— 推奨' ),
        'verified'  => kaitori_t( 'eKYC approved only (admin approval required)', 'eKYC承認済みのみ（管理者の承認が必要）' ),
        'off'       => kaitori_t( 'eKYC not required (login only)', 'eKYC不要（ログインのみ必須）' ),
    );
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-lock" style="font-size:26px;"></span> <?php echo esc_html( kaitori_t( 'Buyback Form Gate Settings', '買取フォームのゲート設定' ) ); ?></h1>
        <?php if ( $saved ) : ?><div class="notice notice-success is-dismissible"><p><strong><?php echo esc_html( kaitori_t( 'Saved.', '保存しました。' ) ); ?></strong></p></div><?php endif; ?>
        <form method="post" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;max-width:620px;margin-top:14px;">
            <?php wp_nonce_field( 'kaitori_sell_gate' ); ?>
            <p style="color:#475569;"><?php echo esc_html( kaitori_t( 'Login is always required to use the buyback application form (fixed). In addition, choose the identity verification (eKYC) requirement below.', '買取申込フォームを利用するには、必ずログインが必要です（固定）。加えて、本人確認(eKYC)の必要条件を選択してください。' ) ); ?></p>
            <table class="form-table"><tr><th scope="row"><?php echo esc_html( kaitori_t( 'eKYC Requirement', 'eKYC 必要条件' ) ); ?></th><td>
                <?php foreach ( $opts as $val => $label ) : ?>
                    <label style="display:block;margin-bottom:10px;">
                        <input type="radio" name="kaitori_sell_gate_mode" value="<?php echo esc_attr( $val ); ?>" <?php checked( $mode, $val ); ?>>
                        <strong><?php echo esc_html( $label ); ?></strong>
                    </label>
                <?php endforeach; ?>
            </td></tr></table>
            <button class="button button-primary"><?php echo esc_html( kaitori_t( 'Save Settings', '設定を保存' ) ); ?></button>
        </form>
    </div>
    <?php
}
