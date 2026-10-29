<?php
/**
 * Kaitori Akiba — Bilingual Language Switcher (EN ⇄ JP)
 *
 * Lightweight custom switcher (not WP .mo). The whole UI shows one language at a time;
 * visitors/admins toggle it. Front-end remembers the choice in a cookie (default ja),
 * the admin remembers it per user (default en).
 *
 * Usage in templates:  echo kaitori_t( 'English text', '日本語テキスト' );
 *                       kaitori_te( 'English', '日本語' );  // echo shortcut
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Current UI language: 'en' or 'ja'. */
function kaitoriakiba_lang() {
    if ( is_admin() ) {
        $uid  = get_current_user_id();
        $lang = $uid ? get_user_meta( $uid, '_kaitori_admin_lang', true ) : '';
        $lang = $lang ? $lang : 'en'; // admin default English
    } else {
        $lang = isset( $_COOKIE['kaitori_lang'] ) ? sanitize_key( $_COOKIE['kaitori_lang'] ) : 'ja'; // front default Japanese
    }
    if ( ! in_array( $lang, array( 'en', 'ja' ), true ) ) {
        $lang = is_admin() ? 'en' : 'ja';
    }
    return apply_filters( 'kaitori_lang', $lang );
}

/** Pick a string by current language. */
function kaitori_t( $en, $ja ) {
    return ( 'en' === kaitoriakiba_lang() ) ? $en : $ja;
}

/** Echo shortcut. */
function kaitori_te( $en, $ja ) {
    echo kaitori_t( $en, $ja ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/** Front-end: apply ?lang=en|ja via cookie, then redirect to a clean URL. */
function kaitoriakiba_handle_front_lang() {
    if ( isset( $_GET['lang'] ) ) {
        $lang = sanitize_key( $_GET['lang'] );
        if ( in_array( $lang, array( 'en', 'ja' ), true ) ) {
            setcookie( 'kaitori_lang', $lang, time() + YEAR_IN_SECONDS, defined( 'COOKIEPATH' ) ? COOKIEPATH : '/', defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '' );
            $_COOKIE['kaitori_lang'] = $lang;
        }
        wp_safe_redirect( remove_query_arg( 'lang' ) );
        exit;
    }
}
add_action( 'template_redirect', 'kaitoriakiba_handle_front_lang' );

/** Admin: apply ?admin_lang=en|ja via user meta, then redirect. */
function kaitoriakiba_handle_admin_lang() {
    if ( isset( $_GET['admin_lang'] ) && is_user_logged_in() ) {
        $lang = sanitize_key( $_GET['admin_lang'] );
        if ( in_array( $lang, array( 'en', 'ja' ), true ) ) {
            update_user_meta( get_current_user_id(), '_kaitori_admin_lang', $lang );
        }
        wp_safe_redirect( remove_query_arg( 'admin_lang' ) );
        exit;
    }
}
add_action( 'admin_init', 'kaitoriakiba_handle_admin_lang' );

/** Front-end switcher markup (placed in the header utility bar). */
function kaitoriakiba_lang_switcher() {
    $cur = kaitoriakiba_lang();
    $en  = esc_url( add_query_arg( 'lang', 'en' ) );
    $ja  = esc_url( add_query_arg( 'lang', 'ja' ) );
    echo '<li class="kaitori-lang-switch" style="white-space:nowrap; display:inline-flex; align-items:center; gap:4px;"><i class="fa fa-globe"></i> ';
    echo '<a href="' . $en . '" style="font-weight:' . ( 'en' === $cur ? '800;text-decoration:underline;' : '400;' ) . 'white-space:nowrap;">EN</a>';
    echo ' <span style="opacity:.5;">|</span> ';
    echo '<a href="' . $ja . '" style="font-weight:' . ( 'ja' === $cur ? '800;text-decoration:underline;' : '400;' ) . 'white-space:nowrap;">日本語</a>';
    echo '</li>';
}

/** Admin toolbar switcher. */
function kaitoriakiba_admin_lang_toolbar( $wp_admin_bar ) {
    if ( ! is_admin_bar_showing() ) {
        return;
    }
    $cur = kaitoriakiba_lang();
    $wp_admin_bar->add_node( array(
        'id'    => 'kaitori-lang',
        'title' => '🌐 ' . ( 'en' === $cur ? 'EN' : '日本語' ),
        'href'  => esc_url( add_query_arg( 'admin_lang', 'en' === $cur ? 'ja' : 'en' ) ),
        'meta'  => array( 'title' => 'Switch Kaitori admin language' ),
    ) );
    $wp_admin_bar->add_node( array( 'parent' => 'kaitori-lang', 'id' => 'kaitori-lang-en', 'title' => 'English', 'href' => esc_url( add_query_arg( 'admin_lang', 'en' ) ) ) );
    $wp_admin_bar->add_node( array( 'parent' => 'kaitori-lang', 'id' => 'kaitori-lang-ja', 'title' => '日本語', 'href' => esc_url( add_query_arg( 'admin_lang', 'ja' ) ) ) );
}
add_action( 'admin_bar_menu', 'kaitoriakiba_admin_lang_toolbar', 100 );
