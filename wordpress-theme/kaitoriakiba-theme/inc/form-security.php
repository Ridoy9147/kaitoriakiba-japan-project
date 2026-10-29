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
 */
function kaitoriakiba_honeypot_fields() {
    $ts = time();
    // Visually hidden, not display:none (some bots skip display:none), labelled to look real.
    echo '<div aria-hidden="true" style="position:absolute!important;left:-9999px!important;top:auto!important;width:1px;height:1px;overflow:hidden;">';
    echo '<label>Website <input type="text" name="kaitori_hp_url" tabindex="-1" autocomplete="off" value=""></label>';
    echo '</div>';
    echo '<input type="hidden" name="kaitori_form_ts" value="' . esc_attr( $ts ) . '">';
}

/**
 * Validate honeypot + timing from the current POST.
 *
 * @param int $min_seconds Minimum seconds a human needs before submit.
 * @return bool True if it looks human.
 */
function kaitoriakiba_check_honeypot( $min_seconds = 2 ) {
    // Honeypot must be empty.
    if ( ! empty( $_POST['kaitori_hp_url'] ) ) {
        return false;
    }
    // Timing: only reject clearly-instant (bot) submits. A missing OR stale timestamp
    // (cached page, long-open tab, clock skew) is TOLERATED so real users are never
    // blocked — the earlier "> 24h = bot" rule falsely blocked anyone on a cached form.
    $min_seconds = (int) apply_filters( 'kaitori_form_min_seconds', $min_seconds );
    if ( $min_seconds > 0 && ! empty( $_POST['kaitori_form_ts'] ) ) {
        $elapsed = time() - (int) $_POST['kaitori_form_ts'];
        if ( $elapsed >= 0 && $elapsed < $min_seconds ) {
            return false;
        }
    }
    return true;
}

/** Best-effort client IP. */
function kaitoriakiba_client_ip() {
    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    return preg_replace( '/[^0-9a-f:.]/i', '', (string) $ip );
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
 * Pluggable CAPTCHA verification. Returns true by default (no provider configured).
 * Add reCAPTCHA/hCaptcha by returning a bool from this filter.
 *
 * @return bool
 */
function kaitoriakiba_captcha_ok() {
    return (bool) apply_filters( 'kaitori_captcha_verify', true, $_POST );
}

/**
 * Convenience guard used at the top of public AJAX handlers.
 * Sends a JSON error (and dies) if the request fails abuse checks.
 *
 * @param string $action       Rate-limit namespace.
 * @param int    $max_attempts Attempts allowed per window.
 */
function kaitoriakiba_guard_public_form( $action, $max_attempts = 8 ) {
    // Staff/admins are exempt (so repeated in-house testing is never locked out).
    if ( current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( ! kaitoriakiba_check_honeypot() || ! kaitoriakiba_captcha_ok() ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'An invalid submission was detected. Please reload the page and try again.',
            '不正な送信が検出されました。ページを再読み込みしてお試しください。'
        ) ) );
    }
    $max_attempts = (int) apply_filters( 'kaitori_rate_limit_max', $max_attempts, $action );
    if ( ! kaitoriakiba_rate_limit( $action, $max_attempts ) ) {
        wp_send_json_error( array( 'message' => kaitori_t(
            'Too many attempts. Please wait a little while and try again.',
            '試行回数が多すぎます。しばらく時間をおいて再度お試しください。'
        ) ) );
    }
}
