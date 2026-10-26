<?php
/**
 * Kaitori Akiba — Lightweight field encryption for sensitive data at rest.
 *
 * Used to encrypt customer bank payout details (account number, account holder)
 * before they are written to the database. The key is derived from this site's
 * WordPress secret keys (wp-config.php), so encrypted values are only readable
 * on this installation.
 *
 * NOTE: This protects against casual DB/postmeta exposure. It is not a substitute
 * for a dedicated secrets vault, but it removes plaintext financial data from the DB.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Prefix that marks a value as encrypted by this module. */
if ( ! defined( 'KAITORI_ENC_PREFIX' ) ) {
    define( 'KAITORI_ENC_PREFIX', 'kcenc:' );
}

/**
 * Derive a stable 32-byte key from the site's secret keys.
 *
 * @return string Raw 32-byte key.
 */
function kaitoriakiba_crypto_key() {
    $material = '';
    foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'NONCE_SALT', 'AUTH_SALT' ) as $const ) {
        if ( defined( $const ) ) {
            $material .= constant( $const );
        }
    }
    if ( '' === $material ) {
        // Last resort: still site-specific, but weaker. Prefer defining WP salts.
        $material = wp_salt( 'secure_auth' ) . DB_NAME;
    }
    return hash( 'sha256', 'kaitori-akiba|' . $material, true );
}

/** Whether encryption is available on this server. */
function kaitoriakiba_crypto_available() {
    return function_exists( 'openssl_encrypt' ) && function_exists( 'openssl_decrypt' );
}

/**
 * Encrypt a string. Returns the original string if empty or if OpenSSL is missing.
 *
 * @param string $plaintext
 * @return string
 */
function kaitoriakiba_encrypt( $plaintext ) {
    $plaintext = (string) $plaintext;
    if ( '' === $plaintext || ! kaitoriakiba_crypto_available() ) {
        return $plaintext;
    }
    $iv     = random_bytes( 16 );
    $cipher = openssl_encrypt( $plaintext, 'aes-256-cbc', kaitoriakiba_crypto_key(), OPENSSL_RAW_DATA, $iv );
    if ( false === $cipher ) {
        return $plaintext;
    }
    return KAITORI_ENC_PREFIX . base64_encode( $iv . $cipher );
}

/**
 * Decrypt a value produced by kaitoriakiba_encrypt(). Legacy plaintext (no prefix)
 * is returned unchanged for backward compatibility.
 *
 * @param string $stored
 * @return string
 */
function kaitoriakiba_decrypt( $stored ) {
    $stored = (string) $stored;
    if ( '' === $stored || 0 !== strpos( $stored, KAITORI_ENC_PREFIX ) ) {
        return $stored; // not encrypted (legacy or empty)
    }
    if ( ! kaitoriakiba_crypto_available() ) {
        return '';
    }
    $raw = base64_decode( substr( $stored, strlen( KAITORI_ENC_PREFIX ) ), true );
    if ( false === $raw || strlen( $raw ) <= 16 ) {
        return '';
    }
    $iv     = substr( $raw, 0, 16 );
    $cipher = substr( $raw, 16 );
    $plain  = openssl_decrypt( $cipher, 'aes-256-cbc', kaitoriakiba_crypto_key(), OPENSSL_RAW_DATA, $iv );
    return ( false === $plain ) ? '' : $plain;
}

/**
 * Mask a bank account number, keeping only the last few digits.
 *
 * @param string $number
 * @param int    $visible
 * @return string
 */
function kaitoriakiba_mask_account( $number, $visible = 2 ) {
    $number = (string) $number;
    $len    = strlen( $number );
    if ( $len <= $visible ) {
        return str_repeat( '•', $len );
    }
    return str_repeat( '•', $len - $visible ) . substr( $number, -$visible );
}
