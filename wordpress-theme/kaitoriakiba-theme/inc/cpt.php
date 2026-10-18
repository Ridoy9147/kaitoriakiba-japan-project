<?php
/**
 * Kaitori Akiba Custom Post Types & Taxonomies
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Types and Taxonomies
 */
function kaitoriakiba_register_cpt_taxonomies() {

    // 1. Custom Post Type: Kaitori Devices (買取機種)
    $device_labels = array(
        'name'                  => _x( 'Buyback Devices', 'Post Type General Name', 'kaitoriakiba' ),
        'singular_name'         => _x( 'Buyback Device', 'Post Type Singular Name', 'kaitoriakiba' ),
        'menu_name'             => __( 'Buyback Devices (買取機種)', 'kaitoriakiba' ),
        'name_admin_bar'        => __( 'Device', 'kaitoriakiba' ),
        'archives'              => __( 'Device Archives', 'kaitoriakiba' ),
        'all_items'             => __( 'All Devices', 'kaitoriakiba' ),
        'add_new_item'          => __( 'Add New Device', 'kaitoriakiba' ),
        'add_new'               => __( 'Add New Device', 'kaitoriakiba' ),
        'new_item'              => __( 'New Device', 'kaitoriakiba' ),
        'edit_item'             => __( 'Edit Device', 'kaitoriakiba' ),
        'update_item'           => __( 'Update Device', 'kaitoriakiba' ),
        'view_item'             => __( 'View Device', 'kaitoriakiba' ),
        'search_items'          => __( 'Search Devices', 'kaitoriakiba' ),
    );

    $device_args = array(
        'label'                 => __( 'Buyback Device', 'kaitoriakiba' ),
        'labels'                => $device_labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-smartphone',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'devices',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array( 'slug' => 'device', 'with_front' => false ),
    );
    register_post_type( 'kaitori_device', $device_args );

    // 2. Custom Post Type: Buyback Orders / Applications (買取申込)
    $order_labels = array(
        'name'                  => _x( 'Buyback Orders', 'Post Type General Name', 'kaitoriakiba' ),
        'singular_name'         => _x( 'Buyback Order', 'Post Type Singular Name', 'kaitoriakiba' ),
        'menu_name'             => __( 'Buyback Orders (買取申込)', 'kaitoriakiba' ),
        'all_items'             => __( 'All Applications', 'kaitoriakiba' ),
        'add_new_item'          => __( 'Add New Application', 'kaitoriakiba' ),
        'edit_item'             => __( 'View / Edit Application', 'kaitoriakiba' ),
    );
    $order_args = array(
        'labels'                => $order_labels,
        'supports'              => array( 'title', 'custom-fields' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-clipboard',
        'can_export'            => true,
        'has_archive'           => false,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
    );
    register_post_type( 'buyback_order', $order_args );

    // 3. Custom Post Type: eKYC Submissions (本人確認)
    $kyc_labels = array(
        'name'                  => _x( 'eKYC Verifications', 'Post Type General Name', 'kaitoriakiba' ),
        'singular_name'         => _x( 'eKYC Verification', 'Post Type Singular Name', 'kaitoriakiba' ),
        'menu_name'             => __( 'eKYC Verify (本人確認)', 'kaitoriakiba' ),
        'all_items'             => __( 'All Submissions', 'kaitoriakiba' ),
        'edit_item'             => __( 'View / Verify ID', 'kaitoriakiba' ),
    );
    $kyc_args = array(
        'labels'                => $kyc_labels,
        'supports'              => array( 'title', 'custom-fields' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-id-alt',
        'can_export'            => true,
        'has_archive'           => false,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
    );
    register_post_type( 'kyc_record', $kyc_args );

    // 4. Taxonomy: Device Category (e.g. iPhone, Smartphones, Tablets)
    register_taxonomy( 'device_category', array( 'kaitori_device' ), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'              => _x( 'Device Categories', 'taxonomy general name', 'kaitoriakiba' ),
            'singular_name'     => _x( 'Device Category', 'taxonomy singular name', 'kaitoriakiba' ),
            'search_items'      => __( 'Search Categories', 'kaitoriakiba' ),
            'all_items'         => __( 'All Categories', 'kaitoriakiba' ),
            'parent_item'       => __( 'Parent Category', 'kaitoriakiba' ),
            'parent_item_colon' => __( 'Parent Category:', 'kaitoriakiba' ),
            'edit_item'         => __( 'Edit Category', 'kaitoriakiba' ),
            'update_item'       => __( 'Update Category', 'kaitoriakiba' ),
            'add_new_item'      => __( 'Add New Category', 'kaitoriakiba' ),
            'menu_name'         => __( 'Categories', 'kaitoriakiba' ),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'device-category' ),
    ) );

    // 5. Taxonomy: Carrier (e.g. SIM Free, docomo, au, SoftBank, Rakuten)
    register_taxonomy( 'carrier', array( 'kaitori_device' ), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'          => _x( 'Carriers', 'taxonomy general name', 'kaitoriakiba' ),
            'singular_name' => _x( 'Carrier', 'taxonomy singular name', 'kaitoriakiba' ),
            'menu_name'     => __( 'Carriers (キャリア)', 'kaitoriakiba' ),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'carrier' ),
    ) );

    // 6. Taxonomy: Brand (e.g. Apple, Google, SONY, Xiaomi, OPPO)
    register_taxonomy( 'brand', array( 'kaitori_device' ), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'          => _x( 'Brands', 'taxonomy general name', 'kaitoriakiba' ),
            'singular_name' => _x( 'Brand', 'taxonomy singular name', 'kaitoriakiba' ),
            'menu_name'     => __( 'Brands (メーカー)', 'kaitoriakiba' ),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'brand' ),
    ) );

    // 7. Taxonomy: Series (e.g. iPhone, Xperia, Galaxy, AQUOS)
    register_taxonomy( 'series', array( 'kaitori_device' ), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'          => _x( 'Series', 'taxonomy general name', 'kaitoriakiba' ),
            'singular_name' => _x( 'Series', 'taxonomy singular name', 'kaitoriakiba' ),
            'menu_name'     => __( 'Series (シリーズ)', 'kaitoriakiba' ),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'series' ),
    ) );
}
add_action( 'init', 'kaitoriakiba_register_cpt_taxonomies' );

/**
 * Custom Admin Columns for Buyback Orders
 */
function kaitoriakiba_order_columns( $columns ) {
    $new_columns = array(
        'cb'              => $columns['cb'],
        'title'           => __( 'Application ID / Title', 'kaitoriakiba' ),
        'applicant_name'  => __( 'Customer Name', 'kaitoriakiba' ),
        'device_model'    => __( 'Device & Storage', 'kaitoriakiba' ),
        'estimated_price' => __( 'Estimated Payout', 'kaitoriakiba' ),
        'delivery_method' => __( 'Delivery Method', 'kaitoriakiba' ),
        'order_status'    => __( 'Status', 'kaitoriakiba' ),
        'date'            => __( 'Date', 'kaitoriakiba' ),
    );
    return $new_columns;
}
add_filter( 'manage_buyback_order_posts_columns', 'kaitoriakiba_order_columns' );

function kaitoriakiba_order_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'applicant_name':
            $name = get_post_meta( $post_id, '_applicant_name', true );
            $phone = get_post_meta( $post_id, '_applicant_phone', true );
            echo '<strong>' . esc_html( $name ?: '—' ) . '</strong><br><small>' . esc_html( $phone ) . '</small>';
            break;
        case 'device_model':
            $model = get_post_meta( $post_id, '_device_model', true );
            $storage = get_post_meta( $post_id, '_device_storage', true );
            $color = get_post_meta( $post_id, '_device_color', true );
            echo esc_html( $model . ' ' . $storage . ' (' . $color . ')' );
            break;
        case 'estimated_price':
            $price = get_post_meta( $post_id, '_estimated_price', true );
            echo '<span style="color:#dc2626; font-weight:bold;">¥' . number_format( (int)$price ) . '</span>';
            break;
        case 'delivery_method':
            $method = get_post_meta( $post_id, '_delivery_method', true );
            echo esc_html( $method === 'store' ? '店頭買取 (In-Store)' : '郵送買取 (Mail-In)' );
            break;
        case 'order_status':
            $status = get_post_meta( $post_id, '_order_status', true ) ?: 'pending';
            $labels = array(
                'pending'   => '<span style="background:#fef3c7; color:#92400e; padding:3px 8px; border-radius:4px; font-weight:bold;">受付中 (Pending)</span>',
                'received'  => '<span style="background:#dbeafe; color:#1e40af; padding:3px 8px; border-radius:4px; font-weight:bold;">端末受領 (Received)</span>',
                'inspected' => '<span style="background:#e0e7ff; color:#3730a3; padding:3px 8px; border-radius:4px; font-weight:bold;">査定完了 (Inspected)</span>',
                'paid'      => '<span style="background:#dcfce7; color:#166534; padding:3px 8px; border-radius:4px; font-weight:bold;">振込完了 (Paid)</span>',
                'cancelled' => '<span style="background:#fee2e2; color:#991b1b; padding:3px 8px; border-radius:4px; font-weight:bold;">キャンセル (Cancelled)</span>',
            );
            echo isset($labels[$status]) ? $labels[$status] : esc_html($status);
            break;
    }
}
add_action( 'manage_buyback_order_posts_custom_column', 'kaitoriakiba_order_custom_column', 10, 2 );

/**
 * Custom Admin Columns for Kaitori Devices
 */
function kaitoriakiba_device_columns( $columns ) {
    $new_columns = array(
        'cb'             => $columns['cb'],
        'device_thumb'   => __( 'Photo', 'kaitoriakiba' ),
        'title'          => __( 'Device Name', 'kaitoriakiba' ),
        'max_price'      => __( 'Max Buyback Price', 'kaitoriakiba' ),
        'device_cats'    => __( 'Category', 'kaitoriakiba' ),
        'device_brand'   => __( 'Brand', 'kaitoriakiba' ),
        'device_series'  => __( 'Series', 'kaitoriakiba' ),
        'model_jan'      => __( 'Model / JAN', 'kaitoriakiba' ),
        'date'           => __( 'Date', 'kaitoriakiba' ),
    );
    return $new_columns;
}
add_filter( 'manage_kaitori_device_posts_columns', 'kaitoriakiba_device_columns' );

function kaitoriakiba_device_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'device_thumb':
            if ( has_post_thumbnail( $post_id ) ) {
                echo get_the_post_thumbnail( $post_id, array( 50, 50 ), array( 'style' => 'border-radius:6px; object-fit:contain;' ) );
            } else {
                echo '<span style="color:#94a3b8;">No photo</span>';
            }
            break;
        case 'max_price':
            $price = get_post_meta( $post_id, '_max_buyback_price', true );
            echo '<strong style="color:#ef4444; font-size:14px;">¥' . number_format( (int)$price ) . '</strong>';
            break;
        case 'device_cats':
            $terms = get_the_term_list( $post_id, 'device_category', '', ', ' );
            echo $terms ? $terms : '—';
            break;
        case 'device_brand':
            $terms = get_the_term_list( $post_id, 'brand', '', ', ' );
            echo $terms ? $terms : '—';
            break;
        case 'device_series':
            $terms = get_the_term_list( $post_id, 'series', '', ', ' );
            echo $terms ? $terms : '—';
            break;
        case 'model_jan':
            $model = get_post_meta( $post_id, '_model_number', true );
            $jan   = get_post_meta( $post_id, '_jan_code', true );
            echo '<small>' . esc_html( $model ?: '' ) . '<br>' . esc_html( $jan ?: '' ) . '</small>';
            break;
    }
}
add_action( 'manage_kaitori_device_posts_custom_column', 'kaitoriakiba_device_custom_column', 10, 2 );
