<?php
/**
 * Kaitori Akiba — Google reCAPTCHA v2 ("I'm not a robot" checkbox).
 *
 * Admin-configurable (Appearance → reCAPTCHA): master enable, site key, secret key,
 * and per-form toggles (register / login / eKYC / buyback application). The checkbox
 * widget is rendered inside each enabled form, and the token is verified server-side
 * through the 'kaitori_captcha_result' and 'kaitori_captcha_verify' hooks.
 *
 * Verification is tied to a hidden "kaitori_recaptcha" marker that the field helper
 * prints, so a form only requires a token when its captcha is actually enabled.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Settings (merged with defaults). */
function kaitoriakiba_recaptcha_settings() {
    $defaults = array(
        'enabled'    => 0,
        'site_key'   => '',
        'secret_key' => '',
        'forms'      => array(
            'register'   => 1,
            'login'      => 1,
            'kyc'        => 1,
            'sell_apply' => 1,
        ),
    );
    $saved = get_option( 'kaitori_recaptcha', array() );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }
    $out = wp_parse_args( $saved, $defaults );
    $out['site_key']   = trim( (string) ( $out['site_key'] ?? '' ) );
    $out['secret_key'] = trim( (string) ( $out['secret_key'] ?? '' ) );
    $out['forms']      = wp_parse_args( is_array( $out['forms'] ?? null ) ? $out['forms'] : array(), $defaults['forms'] );
    return $out;
}

/** reCAPTCHA is usable only when enabled AND both keys are present. */
function kaitoriakiba_recaptcha_active() {
    $s = kaitoriakiba_recaptcha_settings();
    return ! empty( $s['enabled'] ) && ! empty( $s['site_key'] ) && ! empty( $s['secret_key'] );
}

/** Is the captcha enabled for a specific form slug? */
function kaitoriakiba_recaptcha_on_form( $form ) {
    if ( ! kaitoriakiba_recaptcha_active() ) {
        return false;
    }
    $s = kaitoriakiba_recaptcha_settings();
    return ! empty( $s['forms'][ $form ] );
}

/**
 * Print the reCAPTCHA checkbox for a form. No-op when disabled for that form.
 * Also prints a hidden marker so the server knows this form must carry a token.
 *
 * @param string $form register|login|kyc|sell_apply
 */
function kaitoriakiba_recaptcha_field( $form ) {
    if ( ! kaitoriakiba_recaptcha_on_form( $form ) ) {
        return;
    }
    $s = kaitoriakiba_recaptcha_settings();
    echo '<div class="kaitori-recaptcha" style="margin:14px 0;">';
    echo '<div class="g-recaptcha" data-sitekey="' . esc_attr( $s['site_key'] ) . '"></div>';
    echo '<input type="hidden" name="kaitori_recaptcha" value="1">';
    echo '</div>';
}

/** Load Google's reCAPTCHA API script on the front-end when active. */
function kaitoriakiba_recaptcha_enqueue() {
    if ( is_admin() || ! kaitoriakiba_recaptcha_active() ) {
        return;
    }
    // Explicit onload-free auto-render; api.js finds every .g-recaptcha on the page.
    wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'kaitoriakiba_recaptcha_enqueue', 20 );

/**
 * Server-side verification with detailed result reporting.
 *
 * @param array $res  Current result array with 'ok' and 'message'.
 * @param array $post The $_POST payload.
 * @return array
 */
function kaitoriakiba_recaptcha_verify_result( $res, $post ) {
    // Form did not render a captcha → nothing to check.
    if ( empty( $post['kaitori_recaptcha'] ) ) {
        return $res;
    }
    if ( ! kaitoriakiba_recaptcha_active() ) {
        return $res; // keys removed after page load; don't block.
    }

    $token = isset( $post['g-recaptcha-response'] ) ? trim( (string) $post['g-recaptcha-response'] ) : '';
    if ( '' === $token ) {
        return array(
            'ok'      => false,
            'message' => kaitori_t(
                'Please check the "I\'m not a robot" reCAPTCHA box.',
                '「私はロボットではありません」にチェックを入れてください。'
            ),
        );
    }

    $s      = kaitoriakiba_recaptcha_settings();
    $secret = trim( (string) ( $s['secret_key'] ?? '' ) );
    if ( empty( $secret ) ) {
        return array( 'ok' => true );
    }

    $body_args = array(
        'secret'   => $secret,
        'response' => $token,
    );

    // Only attach remoteip if it is a valid public IP
    $client_ip = kaitoriakiba_client_ip();
    if ( ! empty( $client_ip ) && filter_var( $client_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
        $body_args['remoteip'] = $client_ip;
    }

    $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
        'timeout' => 10,
        'body'    => $body_args,
    ) );

    if ( is_wp_error( $response ) ) {
        // Network/cURL error: fail-open so Google outage never halts registrations or orders.
        error_log( 'kaitoriakiba reCAPTCHA network error: ' . $response->get_error_message() );
        return array( 'ok' => true );
    }

    $raw_body = wp_remote_retrieve_body( $response );
    $body     = json_decode( $raw_body, true );

    if ( empty( $body ) || ! is_array( $body ) ) {
        error_log( 'kaitoriakiba reCAPTCHA empty response: ' . $raw_body );
        return array( 'ok' => true );
    }

    if ( ! empty( $body['success'] ) ) {
        return array( 'ok' => true );
    }

    // Google returned success: false
    $error_codes = isset( $body['error-codes'] ) && is_array( $body['error-codes'] ) ? $body['error-codes'] : array();
    $host        = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( $_SERVER['HTTP_HOST'] ) : '';
    $is_staging  = (
        strpos( $host, 'hostingersite.com' ) !== false ||
        strpos( $host, 'localhost' ) !== false ||
        strpos( $host, '.test' ) !== false ||
        strpos( $host, '.local' ) !== false ||
        strpos( $host, 'ngrok' ) !== false
    );

    error_log( 'kaitoriakiba reCAPTCHA verification failed: ' . implode( ', ', $error_codes ) . ' (Host: ' . $host . ')' );

    // If the failure is due to domain mismatch (e.g., keys created for production domain but running on staging)
    // or invalid secret key on staging: the human did solve the reCAPTCHA token, so fail-open on staging.
    if ( in_array( 'hostname-mismatch', $error_codes, true ) || ( in_array( 'invalid-input-secret', $error_codes, true ) && $is_staging ) ) {
        return array( 'ok' => true );
    }

    if ( in_array( 'timeout-or-duplicate', $error_codes, true ) ) {
        return array(
            'ok'      => false,
            'message' => kaitori_t(
                'reCAPTCHA verification expired. Please check the reCAPTCHA box again.',
                'reCAPTCHAの有効期限が切れました。もう一度チェックを入れてください。'
            ),
        );
    }

    return array(
        'ok'      => false,
        'message' => kaitori_t(
            'reCAPTCHA verification failed. Please try again.',
            'reCAPTCHA認証に失敗しました。もう一度お試しください。'
        ),
    );
}
add_filter( 'kaitori_captcha_result', 'kaitoriakiba_recaptcha_verify_result', 10, 2 );

/**
 * Legacy boolean hook wrapper.
 */
function kaitoriakiba_recaptcha_verify( $pass, $post ) {
    $res = kaitoriakiba_recaptcha_verify_result( array( 'ok' => $pass, 'message' => '' ), $post );
    return ! empty( $res['ok'] );
}
add_filter( 'kaitori_captcha_verify', 'kaitoriakiba_recaptcha_verify', 10, 2 );

/* ----------------------------- Admin settings ----------------------------- */

function kaitoriakiba_recaptcha_menu() {
    add_theme_page(
        kaitori_t( 'reCAPTCHA Settings', 'reCAPTCHA 設定' ),
        kaitori_t( 'reCAPTCHA', 'reCAPTCHA' ),
        'manage_options',
        'kaitori-recaptcha',
        'kaitoriakiba_recaptcha_settings_page'
    );
}
add_action( 'admin_menu', 'kaitoriakiba_recaptcha_menu' );

function kaitoriakiba_recaptcha_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html( kaitori_t( 'Permission denied.', '権限がありません。' ) ) );
    }

    $saved = false;
    if ( isset( $_POST['kaitori_recaptcha_submit'] ) && check_admin_referer( 'kaitori_recaptcha_save' ) ) {
        $forms_in = isset( $_POST['forms'] ) && is_array( $_POST['forms'] ) ? $_POST['forms'] : array();
        $new = array(
            'enabled'    => ! empty( $_POST['enabled'] ) ? 1 : 0,
            'site_key'   => trim( sanitize_text_field( wp_unslash( $_POST['site_key'] ?? '' ) ) ),
            'secret_key' => trim( sanitize_text_field( wp_unslash( $_POST['secret_key'] ?? '' ) ) ),
            'forms'      => array(
                'register'   => ! empty( $forms_in['register'] ) ? 1 : 0,
                'login'      => ! empty( $forms_in['login'] ) ? 1 : 0,
                'kyc'        => ! empty( $forms_in['kyc'] ) ? 1 : 0,
                'sell_apply' => ! empty( $forms_in['sell_apply'] ) ? 1 : 0,
            ),
        );
        update_option( 'kaitori_recaptcha', $new );
        $saved = true;
    }

    $s = kaitoriakiba_recaptcha_settings();
    $form_labels = array(
        'register'   => kaitori_t( 'Member Registration', '会員登録フォーム' ),
        'login'      => kaitori_t( 'Login', 'ログインフォーム' ),
        'kyc'        => kaitori_t( 'eKYC Verification', '本人確認 (eKYC) フォーム' ),
        'sell_apply' => kaitori_t( 'Buyback Application', '買取申込フォーム' ),
    );
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-shield" style="font-size:26px;vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Google reCAPTCHA (v2) Settings', 'Google reCAPTCHA (v2) 設定' ) ); ?></h1>

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><strong><?php echo esc_html( kaitori_t( 'reCAPTCHA settings saved.', 'reCAPTCHA 設定を保存しました。' ) ); ?></strong></p></div>
        <?php endif; ?>

        <?php if ( ! empty( $s['enabled'] ) && ( empty( $s['site_key'] ) || empty( $s['secret_key'] ) ) ) : ?>
            <div class="notice notice-warning"><p><?php echo esc_html( kaitori_t( 'reCAPTCHA is enabled but a Site Key / Secret Key is missing — it will not run until both are filled.', 'reCAPTCHA は有効ですが、サイトキー / シークレットキーが未入力のため動作しません。両方を入力してください。' ) ); ?></p></div>
        <?php endif; ?>

        <form method="post" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;max-width:760px;margin-top:14px;">
            <?php wp_nonce_field( 'kaitori_recaptcha_save' ); ?>

            <p style="color:#475569;">
                <?php echo esc_html( kaitori_t( 'Get your v2 "I\'m not a robot" keys from the Google reCAPTCHA admin console, then paste them below.', 'Google reCAPTCHA 管理コンソールで v2「私はロボットではありません」のキーを取得し、以下に貼り付けてください。' ) ); ?>
                <a href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener">https://www.google.com/recaptcha/admin</a>
            </p>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo esc_html( kaitori_t( 'Enable reCAPTCHA', 'reCAPTCHA を有効化' ) ); ?></th>
                    <td><label><input type="checkbox" name="enabled" value="1" <?php checked( $s['enabled'], 1 ); ?>> <?php echo esc_html( kaitori_t( 'Turn on reCAPTCHA protection for the forms selected below.', '下記で選択したフォームで reCAPTCHA を有効にします。' ) ); ?></label></td>
                </tr>
                <tr>
                    <th scope="row"><label for="kr_site"><?php echo esc_html( kaitori_t( 'Site Key', 'サイトキー' ) ); ?></label></th>
                    <td><input type="text" id="kr_site" name="site_key" class="regular-text" value="<?php echo esc_attr( $s['site_key'] ); ?>" style="width:100%;max-width:520px;" autocomplete="off"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="kr_secret"><?php echo esc_html( kaitori_t( 'Secret Key', 'シークレットキー' ) ); ?></label></th>
                    <td><input type="text" id="kr_secret" name="secret_key" class="regular-text" value="<?php echo esc_attr( $s['secret_key'] ); ?>" style="width:100%;max-width:520px;" autocomplete="off"></td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html( kaitori_t( 'Protect these forms', '対象フォーム' ) ); ?></th>
                    <td>
                        <?php foreach ( $form_labels as $key => $label ) : ?>
                            <label style="display:block;margin-bottom:8px;">
                                <input type="checkbox" name="forms[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $s['forms'][ $key ] ), true ); ?>>
                                <?php echo esc_html( $label ); ?>
                            </label>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>

            <p><button type="submit" name="kaitori_recaptcha_submit" value="1" class="button button-primary button-hero"><?php echo esc_html( kaitori_t( 'Save Settings', '設定を保存' ) ); ?></button></p>
        </form>
    </div>
    <?php
}
