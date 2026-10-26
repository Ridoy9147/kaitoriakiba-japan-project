<?php
/**
 * Kaitori Akiba — Server-side Pricing Engine
 *
 * Single source of truth for buyback quotes. Prices are derived from the device's
 * own admin meta (Rank S–D, per-storage base prices, installment deduction) so the
 * quote a customer sees and the quote stored on the order are always computed here
 * — never trusted from the browser.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Find a published device post by its exact title (the model name).
 *
 * @param string $model
 * @return int Device post id, or 0 if not found.
 */
function kaitoriakiba_find_device_by_model( $model ) {
    $model = trim( (string) $model );
    if ( '' === $model ) {
        return 0;
    }
    $found = get_posts( array(
        'post_type'        => 'kaitori_device',
        'post_status'      => 'publish',
        'title'            => $model,
        'posts_per_page'   => 1,
        'fields'           => 'ids',
        'suppress_filters' => false,
    ) );
    return ! empty( $found ) ? (int) $found[0] : 0;
}

/**
 * Parse "_storage_options" meta ("128GB:152000\n256GB:168000") into [capacity => price].
 *
 * @param int $device_id
 * @return array<string,int>
 */
function kaitoriakiba_parse_storage_prices( $device_id ) {
    $raw = get_post_meta( $device_id, '_storage_options', true );
    $out = array();
    if ( empty( $raw ) ) {
        return $out;
    }
    foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
        $line = trim( $line );
        if ( '' === $line || false === strpos( $line, ':' ) ) {
            continue;
        }
        list( $cap, $price ) = array_map( 'trim', explode( ':', $line, 2 ) );
        if ( '' !== $cap ) {
            $out[ strtoupper( $cap ) ] = (int) preg_replace( '/[^\d]/', '', $price );
        }
    }
    return $out;
}

/**
 * Map a submitted condition selection to a rank letter (s, a, b, c, d).
 * The sell-apply form sends Japanese labels that embed the rank.
 *
 * @param string $value
 * @param string $fallback
 * @return string
 */
function kaitoriakiba_condition_to_rank( $value, $fallback = 'b' ) {
    $value = (string) $value;
    if ( preg_match( '/S\s*ランク|factory\s*sealed|未開封/iu', $value ) ) {
        return 's';
    }
    if ( preg_match( '/A\s*ランク|未使用|美品|flawless|目立つ傷なし|目立つ打痕なし/iu', $value ) ) {
        return 'a';
    }
    if ( preg_match( '/B\s*ランク|微細|小傷/iu', $value ) ) {
        return 'b';
    }
    if ( preg_match( '/C\s*ランク|擦れ|スレ|使用感/iu', $value ) ) {
        return 'c';
    }
    if ( preg_match( '/D\s*ランク|割れ|ひび|難あり|ジャンク/iu', $value ) ) {
        return 'd';
    }
    return $fallback;
}

/** Order ranks worst→best for choosing the lower of two. */
function kaitoriakiba_worse_rank( $a, $b ) {
    $order = array( 'd' => 0, 'c' => 1, 'b' => 2, 'a' => 3, 's' => 4 );
    $av = isset( $order[ $a ] ) ? $order[ $a ] : 2;
    $bv = isset( $order[ $b ] ) ? $order[ $b ] : 2;
    return ( $av <= $bv ) ? $a : $b;
}

/**
 * Compute a buyback quote on the server.
 *
 * @param array $args {
 *   @type string $model               Device model (post title).
 *   @type string $storage             e.g. "256GB".
 *   @type string $screen_condition    Submitted label.
 *   @type string $body_condition      Submitted label.
 *   @type int    $battery_health      50–100.
 *   @type string $powers_on           'yes'|'no'.
 *   @type string $camera_works        'yes'|'no'.
 *   @type string $installment_status  'yes'|'no'.
 * }
 * @return int Final quote in yen (>= floor).
 */
function kaitoriakiba_calculate_quote( $args ) {
    $args = wp_parse_args( $args, array(
        'model'              => '',
        'storage'            => '256GB',
        'screen_condition'   => '',
        'body_condition'     => '',
        'battery_health'     => 100,
        'powers_on'          => 'yes',
        'camera_works'       => 'yes',
        'installment_status' => 'no',
    ) );

    $device_id = kaitoriakiba_find_device_by_model( $args['model'] );

    // 1. Base price for the chosen storage.
    $base = 0;
    if ( $device_id ) {
        $storage_prices = kaitoriakiba_parse_storage_prices( $device_id );
        $cap            = strtoupper( trim( (string) $args['storage'] ) );
        if ( isset( $storage_prices[ $cap ] ) && $storage_prices[ $cap ] > 0 ) {
            $base = $storage_prices[ $cap ];
        } else {
            $base = (int) get_post_meta( $device_id, '_max_buyback_price', true );
        }
    }
    // Fallback estimate from the model name when the device is not in the catalog.
    if ( $base <= 0 ) {
        $base = kaitoriakiba_fallback_base_price( $args['model'] );
    }

    // 2. Rank from condition (use the worse of screen/body).
    $rank = kaitoriakiba_worse_rank(
        kaitoriakiba_condition_to_rank( $args['screen_condition'] ),
        kaitoriakiba_condition_to_rank( $args['body_condition'] )
    );

    // 3. Apply rank: prefer the admin's explicit Rank price, else a percentage of base.
    $price = 0;
    if ( $device_id ) {
        $rank_price = (int) get_post_meta( $device_id, '_price_rank_' . $rank, true );
        if ( $rank_price > 0 ) {
            $price = $rank_price;
        }
    }
    if ( $price <= 0 ) {
        $pct   = kaitoriakiba_rank_percentages();
        $price = (int) round( $base * ( isset( $pct[ $rank ] ) ? $pct[ $rank ] : 0.85 ) );
    }

    // 4. Battery penalty.
    $battery = (int) $args['battery_health'];
    if ( $battery > 0 && $battery < 80 ) {
        $price -= (int) apply_filters( 'kaitori_price_battery_penalty', 8000, $battery );
    }

    // 5. Functional faults.
    if ( 'no' === $args['powers_on'] ) {
        $price = (int) round( $price * (float) apply_filters( 'kaitori_price_dead_multiplier', 0.35 ) );
    }
    if ( 'no' === $args['camera_works'] ) {
        $price -= (int) apply_filters( 'kaitori_price_camera_penalty', 6000 );
    }

    // 6. Installment (△). Deduction is admin-controlled per device; default 0,
    //    matching the site's advertised "分割残債あり(△)も減額なし" policy. Admins who
    //    want a deduction set "_installment_deduction" on the device.
    if ( 'yes' === $args['installment_status'] && $device_id ) {
        $deduct = (int) get_post_meta( $device_id, '_installment_deduction', true );
        $price -= max( 0, $deduct );
    }

    $floor = (int) apply_filters( 'kaitori_price_floor', 1000 );
    $price = max( $floor, (int) $price );

    return (int) apply_filters( 'kaitori_calculate_quote', $price, $args, $device_id );
}

/** Rank → percentage of base when no explicit rank price is set. */
function kaitoriakiba_rank_percentages() {
    return apply_filters( 'kaitori_rank_percentages', array(
        's' => 1.00,
        'a' => 0.92,
        'b' => 0.85,
        'c' => 0.76,
        'd' => 0.50,
    ) );
}

/**
 * Rough base price from a model name when the device isn't in the catalog.
 * Mirrors the former hard-coded figures so quotes degrade gracefully.
 */
function kaitoriakiba_fallback_base_price( $model ) {
    $model = (string) $model;
    $table = apply_filters( 'kaitori_fallback_base_prices', array(
        '16 Pro Max' => 215000,
        '16 Pro'     => 195000,
        '16 Plus'    => 140000,
        '16'         => 135000,
        '15 Pro Max' => 165000,
        '15 Pro'     => 145000,
        '15'         => 115000,
        '14 Pro Max' => 130000,
        '14 Pro'     => 110000,
        '14'         => 90000,
    ) );
    foreach ( $table as $needle => $price ) {
        if ( false !== strpos( $model, $needle ) ) {
            return (int) $price;
        }
    }
    return (int) apply_filters( 'kaitori_fallback_default_price', 80000 );
}
