<?php
/**
 * Kaitori Akiba Demo Data & Initial Auto Setup Seeder
 * 
 * Automatically generates necessary pages (Home, Login, Register, Sell-Apply, KYC-Verify, Phone Single, Devices)
 * with design templates, taxonomies, sample devices, navigation menu, and front-page settings upon theme activation.
 * 
 * @package KaitoriAkiba
 * @author dev hridoy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Auto-generate necessary pages with template bindings
 */
function kaitoriakiba_auto_create_pages() {
    $pages = array(
        'home' => array(
            'title'    => 'ホーム',
            'slug'     => 'home',
            'template' => 'template-elementor-fullwidth.php',
            'content'  => '<!-- Kaitori Akiba Modern Buyback Portal Homepage -->',
            'is_front' => true,
        ),
        'sell-apply' => array(
            'title'    => '買取査定・オンライン申込',
            'slug'     => 'sell-apply',
            'template' => 'page-sell-apply.php',
            'content'  => '<p>iPhone・スマートフォンの高額買取査定およびオンライン申込手続きページです。かんたん6ステップで最短即日入金！</p>',
            'is_front' => false,
        ),
        'kyc-verify' => array(
            'title'    => '本人確認 (eKYC)',
            'slug'     => 'kyc-verify',
            'template' => 'page-kyc-verify.php',
            'content'  => '<p>古物営業法に基づく本人確認書類（運転免許証・マイナンバーカード・在留カード等）の提出・アップロードページです。</p>',
            'is_front' => false,
        ),
        'register' => array(
            'title'    => '新規会員登録',
            'slug'     => 'register',
            'template' => 'page-register.php',
            'content'  => '<p>Kaitori Akibaの無料会員登録ページです。会員ランク限定ボーナスや申込状況の確認が可能です。</p>',
            'is_front' => false,
        ),
        'login' => array(
            'title'    => 'ログイン',
            'slug'     => 'login',
            'template' => 'page-register.php',
            'content'  => '<p>マイページログインページです。</p>',
            'is_front' => false,
        ),
        'phone-single' => array(
            'title'    => 'iPhone 16 Pro 256GB - 端末詳細・買取査定',
            'slug'     => 'phone-single',
            'template' => 'page-phone-single.php',
            'content'  => '<p>iPhone 16 Proのコンディション別買取価格一覧（Rank S-D）、分割支払い中（利用制限△）の減額査定なし買取詳細ページです。</p>',
            'is_front' => false,
        ),
        'devices' => array(
            'title'    => '買取対象機種一覧',
            'slug'     => 'devices',
            'template' => 'template-elementor-fullwidth.php',
            'content'  => '<p>iPhone、Google Pixel、Xperia、Galaxy等の高価買取対象機種一覧です。</p>',
            'is_front' => false,
        ),
    );

    $created_page_ids = array();

    foreach ( $pages as $key => $p_data ) {
        $existing = get_page_by_path( $p_data['slug'] );
        if ( ! $existing ) {
            $page_id = wp_insert_post( array(
                'post_title'     => $p_data['title'],
                'post_name'      => $p_data['slug'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_content'   => $p_data['content'],
                'comment_status' => 'closed',
            ) );

            if ( $page_id && ! is_wp_error( $page_id ) ) {
                update_post_meta( $page_id, '_wp_page_template', $p_data['template'] );
                $created_page_ids[$key] = $page_id;

                // Populate Elementor Data so pages are immediately editable via Elementor
                $el_data = kaitoriakiba_get_elementor_data_for_page( $key );
                if ( ! empty( $el_data ) ) {
                    update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
                    update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
                    update_post_meta( $page_id, '_elementor_version', '3.20.0' );
                    update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $el_data ) ) );
                }

                // Set static front page if requested
                if ( ! empty( $p_data['is_front'] ) ) {
                    update_option( 'show_on_front', 'page' );
                    update_option( 'page_on_front', $page_id );
                }
            }
        } else {
            $created_page_ids[$key] = $existing->ID;
            // Ensure template is assigned
            if ( $p_data['template'] ) {
                update_post_meta( $existing->ID, '_wp_page_template', $p_data['template'] );
            }

            // Also populate Elementor data if not already set
            if ( ! get_post_meta( $existing->ID, '_elementor_edit_mode', true ) ) {
                $el_data = kaitoriakiba_get_elementor_data_for_page( $key );
                if ( ! empty( $el_data ) ) {
                    update_post_meta( $existing->ID, '_elementor_edit_mode', 'builder' );
                    update_post_meta( $existing->ID, '_elementor_template_type', 'wp-page' );
                    update_post_meta( $existing->ID, '_elementor_version', '3.20.0' );
                    update_post_meta( $existing->ID, '_elementor_data', wp_slash( wp_json_encode( $el_data ) ) );
                }
            }

            if ( ! empty( $p_data['is_front'] ) && get_option( 'show_on_front' ) !== 'page' ) {
                update_option( 'show_on_front', 'page' );
                update_option( 'page_on_front', $existing->ID );
            }
        }
    }

    // Set permalink structure to /%postname%/ if currently default/plain
    if ( ! get_option( 'permalink_structure' ) ) {
        update_option( 'permalink_structure', '/%postname%/' );
        flush_rewrite_rules();
    }

    // Create & assign Primary Navigation Menu
    kaitoriakiba_setup_default_menu( $created_page_ids );
}

/**
 * Helper: Generate initial Elementor section & widget schema for each auto-created page
 */
function kaitoriakiba_get_elementor_data_for_page( $key ) {
    $sections = array();

    switch ( $key ) {
        case 'home':
            if ( function_exists( 'kaitoriakiba_get_full_elementor_homepage_schema' ) ) {
                $sections = kaitoriakiba_get_full_elementor_homepage_schema();
            }
            break;

        case 'sell-apply':
            $sections = array(
                array(
                    'id'       => 'sec_sell_apply',
                    'elType'   => 'section',
                    'settings' => array(),
                    'elements' => array(
                        array(
                            'id'       => 'col_sell_apply',
                            'elType'   => 'column',
                            'settings' => array( '_column_size' => 100 ),
                            'elements' => array(
                                array(
                                    'id'         => 'wid_sell_apply',
                                    'elType'     => 'widget',
                                    'widgetType' => 'kaitori_sell_apply',
                                    'settings'   => array(),
                                ),
                            ),
                        ),
                    ),
                ),
            );
            break;

        case 'kyc-verify':
            $sections = array(
                array(
                    'id'       => 'sec_kyc',
                    'elType'   => 'section',
                    'settings' => array(),
                    'elements' => array(
                        array(
                            'id'       => 'col_kyc',
                            'elType'   => 'column',
                            'settings' => array( '_column_size' => 100 ),
                            'elements' => array(
                                array(
                                    'id'         => 'wid_kyc',
                                    'elType'     => 'widget',
                                    'widgetType' => 'kaitori_kyc_verify',
                                    'settings'   => array(),
                                ),
                            ),
                        ),
                    ),
                ),
            );
            break;

        case 'register':
            $sections = array(
                array(
                    'id'       => 'sec_auth_reg',
                    'elType'   => 'section',
                    'settings' => array(),
                    'elements' => array(
                        array(
                            'id'       => 'col_auth_reg',
                            'elType'   => 'column',
                            'settings' => array( '_column_size' => 100 ),
                            'elements' => array(
                                array(
                                    'id'         => 'wid_auth_reg',
                                    'elType'     => 'widget',
                                    'widgetType' => 'kaitori_auth_form',
                                    'settings'   => array(
                                        'default_tab' => 'register',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            );
            break;

        case 'login':
            $sections = array(
                array(
                    'id'       => 'sec_auth_login',
                    'elType'   => 'section',
                    'settings' => array(),
                    'elements' => array(
                        array(
                            'id'       => 'col_auth_login',
                            'elType'   => 'column',
                            'settings' => array( '_column_size' => 100 ),
                            'elements' => array(
                                array(
                                    'id'         => 'wid_auth_login',
                                    'elType'     => 'widget',
                                    'widgetType' => 'kaitori_auth_form',
                                    'settings'   => array(
                                        'default_tab' => 'login',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            );
            break;

        case 'phone-single':
            $sections = array(
                array(
                    'id'       => 'sec_phone_single',
                    'elType'   => 'section',
                    'settings' => array(),
                    'elements' => array(
                        array(
                            'id'       => 'col_phone_single',
                            'elType'   => 'column',
                            'settings' => array( '_column_size' => 100 ),
                            'elements' => array(
                                array(
                                    'id'         => 'wid_phone_single',
                                    'elType'     => 'widget',
                                    'widgetType' => 'kaitori_device_calculator',
                                    'settings'   => array(
                                        'default_title' => 'iPhone 16 Pro 256GB SIMフリー',
                                        'default_price' => 168000,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            );
            break;

        case 'devices':
            $sections = array(
                array(
                    'id'       => 'sec_dev_search',
                    'elType'   => 'section',
                    'settings' => array(),
                    'elements' => array(
                        array(
                            'id'       => 'col_dev_search',
                            'elType'   => 'column',
                            'settings' => array( '_column_size' => 100 ),
                            'elements' => array(
                                array(
                                    'id'         => 'wid_dev_search',
                                    'elType'     => 'widget',
                                    'widgetType' => 'kaitori_search_box',
                                    'settings'   => array(),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'id'       => 'sec_dev_grid',
                    'elType'   => 'section',
                    'settings' => array(),
                    'elements' => array(
                        array(
                            'id'       => 'col_dev_grid',
                            'elType'   => 'column',
                            'settings' => array( '_column_size' => 100 ),
                            'elements' => array(
                                array(
                                    'id'         => 'wid_dev_grid',
                                    'elType'     => 'widget',
                                    'widgetType' => 'kaitori_device_grid',
                                    'settings'   => array(
                                        'section_title'  => '全買取対象機種一覧',
                                        'posts_per_page' => 16,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            );
            break;
    }

    return $sections;
}

/**
 * 2. Setup default header menu with generated pages
 */
function kaitoriakiba_setup_default_menu( $page_ids = array() ) {
    $menu_name = 'Kaitori Akiba Main Menu';
    $menu_exists = wp_get_nav_menu_object( $menu_name );

    if ( ! $menu_exists ) {
        $menu_id = wp_create_nav_menu( $menu_name );
        if ( is_wp_error( $menu_id ) ) {
            return;
        }

        // Add Home
        if ( ! empty( $page_ids['home'] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => __( 'ホーム (Home)', 'kaitoriakiba' ),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_ids['home'],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ) );
        }

        // Add Devices
        if ( ! empty( $page_ids['devices'] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => __( '買取機種一覧 (Devices)', 'kaitoriakiba' ),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_ids['devices'],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ) );
        }

        // Add Phone Single Showcase
        if ( ! empty( $page_ids['phone-single'] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => __( 'iPhone 16 Pro 査定', 'kaitoriakiba' ),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_ids['phone-single'],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ) );
        }

        // Add Sell Apply
        if ( ! empty( $page_ids['sell-apply'] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => __( '買取申込 (Sell Apply)', 'kaitoriakiba' ),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_ids['sell-apply'],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ) );
        }

        // Add KYC Verify
        if ( ! empty( $page_ids['kyc-verify'] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => __( '本人確認 (eKYC)', 'kaitoriakiba' ),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_ids['kyc-verify'],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ) );
        }

        // Add Register
        if ( ! empty( $page_ids['register'] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => __( '会員登録 (Register)', 'kaitoriakiba' ),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_ids['register'],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ) );
        }

        // Assign to 'primary' theme location
        $locations = get_theme_mod( 'nav_menu_locations' );
        if ( ! is_array( $locations ) ) {
            $locations = array();
        }
        $locations['primary'] = $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );
    }
}

/**
 * 3. Seed initial sample devices and taxonomies
 */
function kaitoriakiba_seed_initial_data() {
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

    // 2. Seed Sample Devices if none exist
    $existing = get_posts( array( 'post_type' => 'kaitori_device', 'posts_per_page' => 1 ) );
    if ( empty( $existing ) ) {
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
}

/**
 * 4. Master setup trigger on theme switch & admin init check
 */
function kaitoriakiba_run_initial_setup() {
    kaitoriakiba_seed_initial_data();
    kaitoriakiba_auto_create_pages();
    update_option( 'kaitoriakiba_setup_completed', 'yes' );
}
add_action( 'after_switch_theme', 'kaitoriakiba_run_initial_setup' );

/**
 * Ensure auto-setup runs if theme files were copied or not triggered via switch
 */
function kaitoriakiba_check_initial_setup() {
    if ( is_admin() && current_user_can( 'manage_options' ) ) {
        if ( get_option( 'kaitoriakiba_setup_completed' ) !== 'yes' ) {
            kaitoriakiba_run_initial_setup();
        }
    }
}
add_action( 'admin_init', 'kaitoriakiba_check_initial_setup' );

/**
 * 5. Admin manual reset/re-seed action
 */
function kaitoriakiba_admin_seed_notice() {
    if ( isset( $_GET['kaitori_action'] ) && $_GET['kaitori_action'] === 'seed_demo' && current_user_can( 'manage_options' ) ) {
        check_admin_referer( 'kaitori_seed_demo_nonce' );
        kaitoriakiba_run_initial_setup();
        echo '<div class="notice notice-success is-dismissible"><p><strong>Kaitori Akiba</strong>: 固定ページ（ホーム・申込・本人確認・会員登録・詳細）、メニュー、サンプル機種データを正常に自動生成しました！</p></div>';
    }
}
add_action( 'admin_notices', 'kaitoriakiba_admin_seed_notice' );
