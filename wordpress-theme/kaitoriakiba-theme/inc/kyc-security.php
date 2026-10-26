<?php
/**
 * Kaitori Akiba — eKYC Document Security
 *
 * Stores identity documents (driver license, My Number card, selfie) OUTSIDE the
 * public media library in a protected uploads subfolder, and serves them only to
 * authorized staff through a permission-checked streaming endpoint.
 *
 * This is required for compliance with the Japanese Antique Dealer Act (古物営業法)
 * and the Act on the Protection of Personal Information (APPI / 個人情報保護法):
 * identity documents must never be publicly reachable by a guessable URL.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Allowed MIME types / extensions for eKYC uploads.
 */
function kaitoriakiba_kyc_allowed_types() {
    return array(
        'jpg|jpeg' => 'image/jpeg',
        'png'      => 'image/png',
        'pdf'      => 'application/pdf',
    );
}

/** Max eKYC upload size in bytes (10 MB). */
function kaitoriakiba_kyc_max_size() {
    return (int) apply_filters( 'kaitori_kyc_max_upload_size', 10 * 1024 * 1024 );
}

/**
 * Absolute path + base info of the protected eKYC directory.
 *
 * @return array{ dir:string, url:string }  url is intentionally non-servable.
 */
function kaitoriakiba_kyc_secure_dir() {
    $uploads = wp_upload_dir();
    $base    = trailingslashit( $uploads['basedir'] ) . 'kaitori-kyc-secure';
    return array(
        'dir' => $base,
        'url' => trailingslashit( $uploads['baseurl'] ) . 'kaitori-kyc-secure', // guarded by .htaccess / web.config
    );
}

/**
 * Ensure the protected directory exists and is locked down (Apache + IIS + index guard).
 */
function kaitoriakiba_kyc_ensure_secure_dir() {
    $info = kaitoriakiba_kyc_secure_dir();
    $dir  = $info['dir'];

    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );
    }
    if ( ! is_dir( $dir ) ) {
        return;
    }

    // Apache
    $htaccess = trailingslashit( $dir ) . '.htaccess';
    if ( ! file_exists( $htaccess ) ) {
        @file_put_contents( $htaccess, "Order Allow,Deny\nDeny from all\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n" );
    }
    // IIS (Laragon/Windows may use nginx; kept harmless on Apache)
    $webconfig = trailingslashit( $dir ) . 'web.config';
    if ( ! file_exists( $webconfig ) ) {
        @file_put_contents( $webconfig, "<configuration>\n  <system.webServer>\n    <authorization>\n      <deny users=\"*\" />\n    </authorization>\n  </system.webServer>\n</configuration>\n" );
    }
    // Directory listing / direct hit guard
    $index = trailingslashit( $dir ) . 'index.php';
    if ( ! file_exists( $index ) ) {
        @file_put_contents( $index, "<?php // Silence is golden.\n" );
    }
}
add_action( 'init', 'kaitoriakiba_kyc_ensure_secure_dir' );

/**
 * Validate + move an uploaded eKYC file into the protected directory.
 *
 * @param string $file_key  Key in $_FILES.
 * @param int    $kyc_id    The kyc_record post id (used to namespace the stored file).
 * @return string|WP_Error  Relative path inside the secure dir, or WP_Error on failure.
 */
function kaitoriakiba_kyc_store_upload( $file_key, $kyc_id ) {
    if ( empty( $_FILES[ $file_key ] ) || ! isset( $_FILES[ $file_key ]['tmp_name'] ) ) {
        return new WP_Error( 'no_file', __( 'No file uploaded.', 'kaitoriakiba' ) );
    }

    $file = $_FILES[ $file_key ];

    if ( ! empty( $file['error'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
        return new WP_Error( 'upload_error', __( 'File upload failed.', 'kaitoriakiba' ) );
    }
    if ( (int) $file['size'] > kaitoriakiba_kyc_max_size() ) {
        return new WP_Error( 'too_large', __( 'File is too large (max 10MB).', 'kaitoriakiba' ) );
    }

    // Validate real type (extension + content), not just the browser-sent MIME.
    $check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], kaitoriakiba_kyc_allowed_types() );
    $ext   = $check['ext'] ? $check['ext'] : '';
    $type  = $check['type'] ? $check['type'] : '';

    if ( ! $ext || ! $type || ! in_array( $type, kaitoriakiba_kyc_allowed_types(), true ) ) {
        return new WP_Error( 'bad_type', __( 'Only JPG, PNG, or PDF files are allowed.', 'kaitoriakiba' ) );
    }

    kaitoriakiba_kyc_ensure_secure_dir();
    $info     = kaitoriakiba_kyc_secure_dir();
    $sub      = (int) $kyc_id ? (int) $kyc_id : 'misc';
    $dest_dir = trailingslashit( $info['dir'] ) . $sub;
    if ( ! file_exists( $dest_dir ) ) {
        wp_mkdir_p( $dest_dir );
    }

    // Unpredictable filename to prevent enumeration.
    $filename  = sanitize_key( $file_key ) . '-' . wp_generate_password( 20, false, false ) . '.' . $ext;
    $dest_path = trailingslashit( $dest_dir ) . $filename;

    if ( ! @move_uploaded_file( $file['tmp_name'], $dest_path ) ) {
        return new WP_Error( 'move_failed', __( 'Could not save the uploaded file.', 'kaitoriakiba' ) );
    }
    @chmod( $dest_path, 0640 );

    // Relative path (inside the secure dir) is what we persist.
    return $sub . '/' . $filename;
}

/**
 * Build a permission-checked viewing URL for a stored eKYC file.
 *
 * @param string $rel_path  Relative path returned by kaitoriakiba_kyc_store_upload().
 * @return string           admin-ajax URL (only works for logged-in staff).
 */
function kaitoriakiba_kyc_file_src( $rel_path ) {
    if ( empty( $rel_path ) ) {
        return '';
    }
    return add_query_arg(
        array(
            'action' => 'kaitori_view_kyc_file',
            'f'      => rawurlencode( $rel_path ),
            '_wpnonce' => wp_create_nonce( 'kaitori_view_kyc' ),
        ),
        admin_url( 'admin-ajax.php' )
    );
}

/**
 * Resolve a legacy value (attachment id OR url) or a new secure relative path
 * into a viewable src. Keeps backward compatibility with older records.
 *
 * @param mixed $stored  Secure relative path, numeric attachment id, or url.
 * @return string
 */
function kaitoriakiba_kyc_resolve_src( $stored ) {
    if ( empty( $stored ) ) {
        return '';
    }
    // New secure files contain a slash and no scheme.
    if ( is_string( $stored ) && false !== strpos( $stored, '/' ) && ! preg_match( '#^https?://#i', $stored ) ) {
        return kaitoriakiba_kyc_file_src( $stored );
    }
    // Legacy numeric attachment id.
    if ( is_numeric( $stored ) ) {
        return wp_get_attachment_url( (int) $stored );
    }
    // Legacy absolute url.
    return esc_url_raw( $stored );
}

/**
 * Secure streaming endpoint for eKYC documents. Staff-only.
 */
function kaitoriakiba_kyc_view_file() {
    if ( ! is_user_logged_in() ) {
        status_header( 403 );
        exit;
    }
    // Only users who can review KYC records (admins / designated staff).
    if ( ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        status_header( 403 );
        exit;
    }
    check_admin_referer( 'kaitori_view_kyc' );

    $rel = isset( $_GET['f'] ) ? rawurldecode( wp_unslash( $_GET['f'] ) ) : '';
    // Reject path traversal and absolute paths.
    if ( empty( $rel ) || false !== strpos( $rel, '..' ) || preg_match( '#^([a-zA-Z]:)?[\\\\/]#', $rel ) ) {
        status_header( 400 );
        exit;
    }

    $info = kaitoriakiba_kyc_secure_dir();
    $path = trailingslashit( $info['dir'] ) . ltrim( $rel, '/\\' );
    $real = realpath( $path );
    $base = realpath( $info['dir'] );

    if ( ! $real || ! $base || 0 !== strpos( $real, $base ) || ! is_file( $real ) ) {
        status_header( 404 );
        exit;
    }

    $ft   = wp_check_filetype( $real, kaitoriakiba_kyc_allowed_types() );
    $mime = $ft['type'] ? $ft['type'] : 'application/octet-stream';

    nocache_headers();
    header( 'Content-Type: ' . $mime );
    header( 'Content-Length: ' . filesize( $real ) );
    header( 'Content-Disposition: inline; filename="' . basename( $real ) . '"' );
    header( 'X-Content-Type-Options: nosniff' );
    readfile( $real );
    exit;
}
add_action( 'wp_ajax_kaitori_view_kyc_file', 'kaitoriakiba_kyc_view_file' );
// No nopriv handler — guests are never allowed.
