<?php
/**
 * Kaitori Akiba — Device List Filtering (shared logic)
 *
 * Single source of truth for filtering the /devices/ (kaitori_device) archive by
 * keyword (q) + category (cat) + carrier (s_career) + brand (brand) + series.
 * Used by BOTH:
 *   - the main archive query on page load / direct links (pre_get_posts), and
 *   - the live no-reload AJAX endpoint (kaitori_filter_devices).
 *
 * Keyword search matches the device TITLE + model number + JAN code, requiring
 * every word (so multi-word queries like "Pixel 8 Pro" work). Taxonomy filters
 * combine with AND.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Read the five filter params from $_GET (sanitized).
 *
 * @return array{q:string,cat:string,s_career:string,brand:string,series:string}
 */
function kaitoriakiba_device_filter_params() {
    $get = function( $k ) {
        return isset( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) : '';
    };
    return array(
        'q'        => $get( 'q' ),
        'cat'      => $get( 'cat' ),
        's_career' => $get( 's_career' ),
        'brand'    => $get( 'brand' ),
        'series'   => $get( 'series' ),
    );
}

/** True when at least one filter param is set. */
function kaitoriakiba_device_filter_active( $p ) {
    foreach ( array( 'q', 'cat', 's_career', 'brand', 'series' ) as $k ) {
        if ( isset( $p[ $k ] ) && '' !== trim( (string) $p[ $k ] ) ) {
            return true;
        }
    }
    return false;
}

/**
 * Find device post IDs matching a keyword across title + model number + JAN code.
 * Every whitespace-separated word must appear somewhere in the combined text, so
 * multi-word searches narrow instead of breaking.
 *
 * @param string $keyword
 * @return int[]|null  Matching IDs (possibly empty) when a keyword is given; null when blank.
 */
function kaitoriakiba_search_device_ids( $keyword ) {
    $keyword = trim( (string) $keyword );
    if ( '' === $keyword ) {
        return null;
    }
    $words = preg_split( '/\s+/u', function_exists( 'mb_strtolower' ) ? mb_strtolower( $keyword ) : strtolower( $keyword ) );
    $all   = get_posts( array(
        'post_type'        => 'kaitori_device',
        'post_status'      => 'publish',
        'posts_per_page'   => -1,
        'fields'           => 'ids',
        'suppress_filters' => false,
    ) );
    $match = array();
    foreach ( $all as $id ) {
        $haystack = get_the_title( $id ) . ' '
            . get_post_meta( $id, '_model_number', true ) . ' '
            . get_post_meta( $id, '_jan_code', true );
        $haystack = function_exists( 'mb_strtolower' ) ? mb_strtolower( $haystack ) : strtolower( $haystack );
        $ok = true;
        foreach ( $words as $w ) {
            if ( '' === $w ) {
                continue;
            }
            if ( false === ( function_exists( 'mb_strpos' ) ? mb_strpos( $haystack, $w ) : strpos( $haystack, $w ) ) ) {
                $ok = false;
                break;
            }
        }
        if ( $ok ) {
            $match[] = (int) $id;
        }
    }
    return $match;
}

/**
 * Build WP_Query args for a device filter (shared by AJAX + a fresh query).
 *
 * @param array $p        Filter params (see kaitoriakiba_device_filter_params()).
 * @param int   $paged
 * @param int   $per_page
 * @return array
 */
function kaitoriakiba_device_query_args( $p, $paged = 1, $per_page = 24 ) {
    $args = array(
        'post_type'      => 'kaitori_device',
        'post_status'    => 'publish',
        'posts_per_page' => (int) $per_page,
        'paged'          => max( 1, (int) $paged ),
    );

    // Keyword → explicit ID set across title/model/JAN (multi-word AND).
    $ids = kaitoriakiba_search_device_ids( isset( $p['q'] ) ? $p['q'] : '' );
    if ( is_array( $ids ) ) {
        $args['post__in'] = ! empty( $ids ) ? $ids : array( 0 );
        $args['orderby']  = 'post__in';
    }

    // Taxonomy filters (AND).
    $tax = kaitoriakiba_device_tax_query( $p );
    if ( ! empty( $tax ) ) {
        $tax['relation']   = 'AND';
        $args['tax_query'] = $tax;
    }

    return $args;
}

/**
 * Build the tax_query array (no relation key) from filter params.
 *
 * @param array $p
 * @return array
 */
function kaitoriakiba_device_tax_query( $p ) {
    $map = array(
        'cat'      => 'device_category',
        's_career' => 'carrier',
        'brand'    => 'brand',
        'series'   => 'series',
    );
    $tax = array();
    foreach ( $map as $key => $taxonomy ) {
        $val = isset( $p[ $key ] ) ? trim( (string) $p[ $key ] ) : '';
        if ( '' === $val ) {
            continue;
        }
        $tax[] = array(
            'taxonomy' => $taxonomy,
            'field'    => is_numeric( $val ) ? 'term_id' : 'slug',
            'terms'    => $val,
        );
    }
    return $tax;
}

/**
 * Apply the filter to the MAIN device archive query (page loads & direct links),
 * so URLs like /devices/?cat=iphone&q=Pixel work without JavaScript. The live
 * AJAX endpoint reuses the same helpers, so both paths behave identically.
 *
 * @param WP_Query $query
 */
function kaitoriakiba_filter_device_archive( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( ! $query->is_post_type_archive( 'kaitori_device' ) ) {
        return; // taxonomy term pages already filter themselves.
    }

    $p = kaitoriakiba_device_filter_params();
    if ( ! kaitoriakiba_device_filter_active( $p ) ) {
        return; // no params → default full list.
    }

    // Keyword → ID set (and make sure WP's own text search doesn't also run).
    $ids = kaitoriakiba_search_device_ids( $p['q'] );
    if ( is_array( $ids ) ) {
        $query->set( 'post__in', ! empty( $ids ) ? $ids : array( 0 ) );
        $query->set( 'orderby', 'post__in' );
        $query->set( 's', '' );
    }

    // Taxonomy filters (merge with anything already present, AND).
    $tax = kaitoriakiba_device_tax_query( $p );
    if ( ! empty( $tax ) ) {
        $existing = $query->get( 'tax_query' );
        if ( ! empty( $existing ) && is_array( $existing ) ) {
            $tax = array_merge( $existing, $tax );
        }
        $tax['relation'] = 'AND';
        $query->set( 'tax_query', $tax );
    }

    // `cat` is a reserved core query var (expects a category ID). Our URLs use it
    // for device_category slugs, so clear the core interpretation to avoid an
    // empty core-category constraint hiding everything.
    $query->set( 'cat', '' );
    $query->set( 'category_name', '' );
}
add_action( 'pre_get_posts', 'kaitoriakiba_filter_device_archive' );
