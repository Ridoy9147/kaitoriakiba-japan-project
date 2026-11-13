<?php
/**
 * Kaitori Akiba — Form abuse protection (no external service required).
 *
 * Provides a honeypot + submission-timing check, an IP-based rate limiter, and a
 * pluggable CAPTCHA hook. All three public AJAX endpoints (register, login, buyback
 * application) run these before doing any work.
 *
 * To add Google reCAPTCHA later, hook the 'kaitori_captcha_verify' filter.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the hidden honeypot + timing fields. Call inside each public <form>.
 * Specially formatted with autocomplete="new-password" and display:none so legitimate
 * human browser autofill (Chrome, Edge, Safari, 1Password) never touches it.
 */
function kaitoriakiba_honeypot_fields() {
    $ts = time();
    echo '<div aria-hidden="true" style="opacity:0;position:absolute;top:0;left:0;height:0;width:0;z-index:-1;overflow:hidden;pointer-events:none;">';
    echo '<input type="text" name="kaitori_hp_url" tabindex="-1" autocomplete="new-password" value="" style="display:none!important;" aria-hidden="true">';
    echo '</div>';
    echo '<input type="hidden" name="kaitori_form_ts" value="' . esc_attr( $ts ) . '">';
}

/**
 * Validate honeypot + timing from the current POST.
 *
 * @param int $min_seconds Minimum seconds a human needs before submit.
 * @return bool True if it looks human.
 */
function kaitoriakiba_check_honeypot( $min_seconds = 1 ) {
    // 1. Honeypot check:
    if ( ! empty( $_POST['kaitori_hp_url'] ) ) {
        $hp_val = trim( (string) $_POST['kaitori_hp_url'] );
        // Check if browser autofill erroneously populated it with user info or site URL:
        $site_host  = wp_parse_url( home_url(), PHP_URL_HOST );
        $email      = isset( $_POST['email'] ) ? trim( (string) $_POST['email'] ) : '';
        $first_name = isset( $_POST['first_name'] ) ? trim( (string) $_POST['first_name'] ) : '';
        $last_name  = isset( $_POST['last_name'] ) ? trim( (string) $_POST['last_name'] ) : '';

        $is_autofill_artifact = false;
        if ( ! empty( $site_host ) && stripos( $hp_val, $site_host ) !== false ) {
            $is_autofill_artifact = true;
        } elseif ( ! empty( $email ) && strtolower( $hp_val ) === strtolower( $email ) ) {
            $is_autofill_artifact = true;
        } elseif ( ! empty( $first_name ) && stripos( $hp_val, $first_name ) !== false ) {
            $is_autofill_artifact = true;
        } elseif ( ! empty( $last_name ) && stripos( $hp_val, $last_name ) !== false ) {
            $is_autofill_artifact = true;
        }

        // If not a recognized browser autofill collision, it's a real spam bot.
        if ( ! $is_autofill_artifact ) {
            return false;
        }
    }

    // 2. Timing check: only reject clearly-instant programmatic submits (< 1 second).
    $min_seconds = (int) apply_filters( 'kaitori_form_min_seconds', $min_seconds );
    if ( $min_seconds > 0 && ! empty( $_POST['kaitori_form_ts'] ) ) {
        $elapsed = time() - (int) $_POST['kaitori_form_ts'];
        if ( $elapsed >= 0 && $elapsed < $min_seconds ) {
            return false;
        }
    }
    return true;
}

/**
 * Best-effort client IP detection, respecting Cloudflare and reverse proxies.
 */
function kaitoriakiba_client_ip() {
    $ip = '';
    if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $parts = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
        $ip    = trim( $parts[0] );
    } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $ip = preg_replace( '/[^0-9a-f:.]/i', '', (string) $ip );
    return $ip ?: '0.0.0.0';
}

/**
 * Simple IP-based sliding-window rate limiter backed by transients.
 *
 * @param string $action  Namespace, e.g. 'login'.
 * @param int    $max     Max attempts within the window.
 * @param int    $window  Window length in seconds.
 * @return bool True if the request is ALLOWED, false if the limit is exceeded.
 */
function kaitoriakiba_rate_limit( $action, $max = 5, $window = 600 ) {
    $key   = 'kaitori_rl_' . md5( $action . '|' . kaitoriakiba_client_ip() );
    $count = (int) get_transient( $key );
    if ( $count >= (int) $max ) {
        return false;
    }
    set_transient( $key, $count + 1, (int) $window );
    return true;
}

/**
 * Structured CAPTCHA verification result.
 *
 * @return array with 'ok' (bool) and optional 'message' (string).
 */
function kaitoriakiba_captcha_result() {
    $default = array(
        'ok'      => true,
        'message' => '',
    );
    $result = apply_filters( 'kaitori_captcha_result', $default, $_POST );
    if ( ! is_array( $result ) ) {
        return array(
            'ok'      => (bool) $result,
            'message' => '',
        );
    }
    return $result;
}

/**
 * Pluggable CAPTCHA verification. Returns true by default (no provider configured).
 * Add reCAPTCHA/hCaptcha by returning a bool from this filter.
 *
 * @return bool
 */
function kaitoriakiba_captcha_ok() {
    $res = kaitoriakiba_captcha_result();
    if ( ! empty( $res['ok'] ) ) {
        return (bool) apply_filters( 'kaitori_captcha_verify', true, $_POST );
    }
    return false;
}

/**
 * Convenience guard used at the top of public AJAX handlers.
 * Sends a JSON error (and dies) if the request fails abuse checks.
 *
 * @param string $action       Rate-limit namespace.
 * @param int    $max_attempts Attempts allowed per window.
 */
function kaitoriakiba_guard_public_form( $action, $max_attempts = 15 ) {
    // Staff/admins are exempt (so repeated in-house testing is never locked out).
    if ( current_user_can( 'manage_options' ) ) {
        return;
    }

    // 1. Honeypot check
    if ( ! kaitoriakiba_check_honeypot() ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'An invalid submission was detected. Please reload the page and try again.',
            '不正な送信が検出されました。ページを再読み込みしてお試しください。'
        ) ) );
    }

    // 2. CAPTCHA verification (gives precise message when captcha fails)
    $captcha_res = kaitoriakiba_captcha_result();
    if ( ! empty( $captcha_res ) && empty( $captcha_res['ok'] ) ) {
        $msg = ! empty( $captcha_res['message'] ) ? $captcha_res['message'] : kaitori_t(
            'reCAPTCHA verification failed. Please check the checkbox and try again.',
            'reCAPTCHA認証に失敗しました。チェックボックスをご確認の上、再度お試しください。'
        );
        wp_send_json_error( array( 'message' => $msg ) );
    }

    // 3. Fallback check for legacy boolean filter
    if ( ! kaitoriakiba_captcha_ok() ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'reCAPTCHA verification failed. Please check the checkbox and try again.',
            'reCAPTCHA認証に失敗しました。チェックボックスをご確認の上、再度お試しください。'
        ) ) );
    }

    // 4. Rate limiting
    $max_attempts = (int) apply_filters( 'kaitori_rate_limit_max', $max_attempts, $action );
    if ( ! kaitoriakiba_rate_limit( $action, $max_attempts ) ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'Too many attempts. Please wait a little while and try again.',
            '試行回数が多すぎます。しばらく時間をおいて再度お試しください。'
        ) ) );
    }
}
