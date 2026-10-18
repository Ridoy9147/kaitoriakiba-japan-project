<?php
/**
 * Kaitori Akiba Demo Data & Initial Taxonomy Seeder
 * Ensures the theme is immediately workable and populated upon activation.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Seed initial sample devices and taxonomies on theme switch
 */
function kaitoriakiba_seed_initial_data() {
    // Only seed if no devices exist yet
    $existing = get_posts( array( 'post_type' => 'kaitori_device', 'posts_per_page' => 1 ) );
    if ( ! empty( $existing ) ) {
        return;
    }

    // 1. Seed Taxonomies
    $categories = array(
        'iPhone'          => 'iphone',
        'Smartphones'     => 'smartphones',
        'Tablets'         => 'tablets',
        'Gaming Consoles' => 'gaming',
        'Cameras'         => 'cameras',
        'PC & Laptops'    => 'pc-laptops',
    );
    foreach ( $categories as $name => $slug ) {
        if ( ! term_exists( $slug, 'device_category' ) ) {
            wp_insert_term( $name, 'device_category', array( 'slug' => $slug ) );
        }
    }

    $carriers = array(
        'SIM FREE'      => 'simfree',
        'docomo'        => 'docomo',
        'au'            => 'au',
        'SoftBank'      => 'softbank',
        'Y!mobile'      => 'ymobile',
        'UQ mobile'     => 'uqmobile',
        'RakutenMobile' => 'rakuten',
    );
    foreach ( $carriers as $name => $slug ) {
        if ( ! term_exists( $slug, 'carrier' ) ) {
            wp_insert_term( $name, 'carrier', array( 'slug' => $slug ) );
        }
    }

    $brands = array(
        'Apple'    => 'apple',
        'Google'   => 'google',
        'SONY'     => 'sony',
        'SAMSUNG'  => 'samsung',
        'Xiaomi'   => 'xiaomi',
        'OPPO'     => 'oppo',
        'SHARP'    => 'sharp',
        'ASUS'     => 'asus',
    );
    foreach ( $brands as $name => $slug ) {
        if ( ! term_exists( $slug, 'brand' ) ) {
            wp_insert_term( $name, 'brand', array( 'slug' => $slug ) );
        }
    }

    $series_list = array(
        'iPhone'   => 'iphone',
        'Pixel'    => 'pixel',
        'Xperia'   => 'xperia',
        'Galaxy'   => 'galaxy',
        'AQUOS'    => 'aquos',
    );
    foreach ( $series_list as $name => $slug ) {
        if ( ! term_exists( $slug, 'series' ) ) {
            wp_insert_term( $name, 'series', array( 'slug' => $slug ) );
        }
    }

    // 2. Seed Sample Devices
    $sample_devices = array(
        array(
            'title'        => 'iPhone 16 Pro 256GB Black (SIMフリー)',
            'max_price'    => 168000,
            'price_s'      => 168000,
            'price_a'      => 155000,
            'price_b'      => 142000,
            'price_c'      => 128000,
            'price_d'      => 82000,
            'installment'  => 5000,
            'model'        => 'A3293',
            'jan'          => '4549995734140',
            'cat'          => 'iphone',
            'brand'        => 'apple',
            'series'       => 'iphone',
            'carrier'      => 'simfree',
            'image'        => 'iPhone 18 Pro 256GB Black.png',
        ),
        array(
            'title'        => 'iPhone 16 Pro Max 256GB Silver (SIMフリー)',
            'max_price'    => 188000,
            'price_s'      => 188000,
            'price_a'      => 174000,
            'price_b'      => 159000,
            'price_c'      => 142000,
            'price_d'      => 95000,
            'installment'  => 5000,
            'model'        => 'A3296',
            'jan'          => '4549995735221',
            'cat'          => 'iphone',
            'brand'        => 'apple',
            'series'       => 'iphone',
            'carrier'      => 'simfree',
            'image'        => 'iPhone 18 Pro 256GB Silver.png',
        ),
        array(
            'title'        => 'iPhone 16 128GB Glacier (SIMフリー)',
            'max_price'    => 112000,
            'price_s'      => 112000,
            'price_a'      => 103000,
            'price_b'      => 94000,
            'price_c'      => 83000,
            'price_d'      => 54000,
            'installment'  => 3000,
            'model'        => 'A3287',
            'jan'          => '4549995732104',
            'cat'          => 'iphone',
            'brand'        => 'apple',
            'series'       => 'iphone',
            'carrier'      => 'simfree',
            'image'        => 'iPhone 18 Pro 256GB Glacier.png',
        ),
        array(
            'title'        => 'iPhone 15 Pro 128GB Natural (docomo / SIMフリー)',
            'max_price'    => 138000,
            'price_s'      => 138000,
            'price_a'      => 126000,
            'price_b'      => 115000,
            'price_c'      => 102000,
            'price_d'      => 68000,
            'installment'  => 4000,
            'model'        => 'A3101',
            'jan'          => '4549995427813',
            'cat'          => 'iphone',
            'brand'        => 'apple',
            'series'       => 'iphone',
            'carrier'      => 'docomo',
            'image'        => 'iPhone 18 Pro 256GB Burgundy.png',
        ),
        array(
            'title'        => 'Google Pixel 8 Pro 128GB Obsidian (SIMフリー)',
            'max_price'    => 95000,
            'price_s'      => 95000,
            'price_a'      => 87000,
            'price_b'      => 79000,
            'price_c'      => 69000,
            'price_d'      => 45000,
            'installment'  => 3000,
            'model'        => 'GC3VE',
            'jan'          => '0840262141571',
            'cat'          => 'smartphones',
            'brand'        => 'google',
            'series'       => 'pixel',
            'carrier'      => 'simfree',
            'image'        => 'iPhone 18 Pro 256GB Black.png',
        ),
        array(
            'title'        => 'SONY Xperia 1 VI 256GB Black (SIMフリー)',
            'max_price'    => 125000,
            'price_s'      => 125000,
            'price_a'      => 114000,
            'price_b'      => 103000,
            'price_c'      => 91000,
            'price_d'      => 58000,
            'installment'  => 4000,
            'model'        => 'XQ-EC44',
            'jan'          => '4589771649231',
            'cat'          => 'smartphones',
            'brand'        => 'sony',
            'series'       => 'xperia',
            'carrier'      => 'simfree',
            'image'        => 'iPhone 18 Pro 256GB Silver.png',
        ),
    );

    foreach ( $sample_devices as $dev ) {
        $post_id = wp_insert_post( array(
            'post_title'   => $dev['title'],
            'post_status'  => 'publish',
            'post_type'    => 'kaitori_device',
            'post_content' => '<p>秋葉原のスマホ買取専門店Kaitori Akibaによる「' . esc_html( $dev['title'] ) . '」の高価買取査定ページです。新品未開封、中古美品、画面割れ、ローン分割支払い中（ネットワーク利用制限△）も即日査定＆高額買取実施中！</p>',
        ) );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_max_buyback_price', $dev['max_price'] );
            update_post_meta( $post_id, '_price_rank_s', $dev['price_s'] );
            update_post_meta( $post_id, '_price_rank_a', $dev['price_a'] );
            update_post_meta( $post_id, '_price_rank_b', $dev['price_b'] );
            update_post_meta( $post_id, '_price_rank_c', $dev['price_c'] );
            update_post_meta( $post_id, '_price_rank_d', $dev['price_d'] );
            update_post_meta( $post_id, '_installment_deduction', $dev['installment'] );
            update_post_meta( $post_id, '_model_number', $dev['model'] );
            update_post_meta( $post_id, '_jan_code', $dev['jan'] );

            // Assign Taxonomies
            wp_set_object_terms( $post_id, $dev['cat'], 'device_category' );
            wp_set_object_terms( $post_id, $dev['brand'], 'brand' );
            wp_set_object_terms( $post_id, $dev['series'], 'series' );
            wp_set_object_terms( $post_id, $dev['carrier'], 'carrier' );
        }
    }
}
add_action( 'after_switch_theme', 'kaitoriakiba_seed_initial_data' );

// Also expose an admin action to seed data anytime
function kaitoriakiba_admin_seed_notice() {
    if ( isset( $_GET['kaitori_action'] ) && $_GET['kaitori_action'] === 'seed_demo' && current_user_can('manage_options') ) {
        check_admin_referer( 'kaitori_seed_demo_nonce' );
        delete_option( 'kaitori_demo_seeded' );
        kaitoriakiba_seed_initial_data();
        echo '<div class="notice notice-success is-dismissible"><p>Kaitori Akibaのサンプル機種・カテゴリデータを自動生成しました！</p></div>';
    }
}
add_action( 'admin_notices', 'kaitoriakiba_admin_seed_notice' );
