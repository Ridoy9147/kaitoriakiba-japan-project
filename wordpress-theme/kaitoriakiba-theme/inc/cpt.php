<?php
/**
 * Kaitori Akiba Custom Post Types & Taxonomies
 * 
 * Clean English Admin Interface by default.
 * 
 * @package KaitoriAkiba
 * @author dev hridoy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Types and Taxonomies
 */
function kaitoriakiba_register_cpt_taxonomies() {

    // 1. Custom Post Type: Kaitori Devices
    $device_labels = array(
        'name'                  => _x( 'Buyback Devices', 'Post Type General Name', 'kaitoriakiba' ),
        'singular_name'         => _x( 'Buyback Device', 'Post Type Singular Name', 'kaitoriakiba' ),
        'menu_name'             => __( 'Buyback Devices', 'kaitoriakiba' ),
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

    // 2. Custom Post Type: Buyback Orders / Applications
    $order_labels = array(
        'name'                  => _x( 'Buyback Orders', 'Post Type General Name', 'kaitoriakiba' ),
        'singular_name'         => _x( 'Buyback Order', 'Post Type Singular Name', 'kaitoriakiba' ),
        'menu_name'             => __( 'Buyback Orders', 'kaitoriakiba' ),
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

    // 3. Custom Post Type: eKYC Submissions
    $kyc_labels = array(
        'name'                  => _x( 'eKYC Verifications', 'Post Type General Name', 'kaitoriakiba' ),
        'singular_name'         => _x( 'eKYC Verification', 'Post Type Singular Name', 'kaitoriakiba' ),
        'menu_name'             => __( 'eKYC Verifications', 'kaitoriakiba' ),
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

    // 4. Taxonomy: Device Category
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

    // 5. Taxonomy: Carrier
    register_taxonomy( 'carrier', array( 'kaitori_device' ), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'          => _x( 'Carriers', 'taxonomy general name', 'kaitoriakiba' ),
            'singular_name' => _x( 'Carrier', 'taxonomy singular name', 'kaitoriakiba' ),
            'menu_name'     => __( 'Carriers', 'kaitoriakiba' ),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'carrier' ),
    ) );

    // 6. Taxonomy: Brand / Manufacturer
    register_taxonomy( 'brand', array( 'kaitori_device' ), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'          => _x( 'Brands', 'taxonomy general name', 'kaitoriakiba' ),
            'singular_name' => _x( 'Brand', 'taxonomy singular name', 'kaitoriakiba' ),
            'menu_name'     => __( 'Brands', 'kaitoriakiba' ),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'brand' ),
    ) );

    // 7. Taxonomy: Series
    register_taxonomy( 'series', array( 'kaitori_device' ), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'          => _x( 'Series', 'taxonomy general name', 'kaitoriakiba' ),
            'singular_name' => _x( 'Series', 'taxonomy singular name', 'kaitoriakiba' ),
            'menu_name'     => __( 'Series', 'kaitoriakiba' ),
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
            echo esc_html( $model . ' ' . $storage );
            if ( $color ) {
                echo '<br><small style="color:#64748b;">' . esc_html( $color ) . '</small>';
            }
            break;
        case 'estimated_price':
            $price = get_post_meta( $post_id, '_estimated_price', true );
            echo $price ? '<strong style="color:#dc2626;">¥' . number_format( (int)$price ) . '</strong>' : '—';
            break;
        case 'delivery_method':
            $delivery = get_post_meta( $post_id, '_delivery_method', true );
            if ( $delivery === 'store' ) {
                echo '<span class="dashicons dashicons-store" style="color:#f59e0b; vertical-align:middle;"></span> ' . __( 'In-Store Visit', 'kaitoriakiba' );
            } else {
                echo '<span class="dashicons dashicons-car" style="color:#10b981; vertical-align:middle;"></span> ' . __( 'Mail-in Delivery', 'kaitoriakiba' );
            }
            break;
        case 'order_status':
            $status = get_post_meta( $post_id, '_order_status', true ) ?: 'pending';
            $status_labels = array(
                'pending'    => array( 'label' => __( 'Pending Review', 'kaitoriakiba' ), 'bg' => '#fef3c7', 'color' => '#b45309' ),
                'approved'   => array( 'label' => __( 'Approved / Awaiting Device', 'kaitoriakiba' ), 'bg' => '#e0e7ff', 'color' => '#3730a3' ),
                'inspecting' => array( 'label' => __( 'Device Inspecting', 'kaitoriakiba' ), 'bg' => '#fce7f3', 'color' => '#9d174d' ),
                'paid'       => array( 'label' => __( 'Paid / Completed', 'kaitoriakiba' ), 'bg' => '#dcfce7', 'color' => '#15803d' ),
                'rejected'   => array( 'label' => __( 'Cancelled / Returned', 'kaitoriakiba' ), 'bg' => '#fee2e2', 'color' => '#991b1b' ),
            );
            $s_info = isset( $status_labels[$status] ) ? $status_labels[$status] : $status_labels['pending'];
            echo '<span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11.5px; font-weight:700; background:' . esc_attr( $s_info['bg'] ) . '; color:' . esc_attr( $s_info['color'] ) . ';">' . esc_html( $s_info['label'] ) . '</span>';
            break;
    }
}
add_action( 'manage_buyback_order_posts_custom_column', 'kaitoriakiba_order_custom_column', 10, 2 );

/**
 * Custom Admin Columns for Devices
 */
function kaitoriakiba_device_columns( $columns ) {
    $new = array();
    $new['cb'] = $columns['cb'];
    $new['device_thumb'] = __( 'Image', 'kaitoriakiba' );
    $new['title'] = $columns['title'];
    $new['max_price'] = __( 'Max Buyback Price', 'kaitoriakiba' );
    $new['ranks'] = __( 'Rank S - D Pricing', 'kaitoriakiba' );
    $new['taxonomy-device_category'] = __( 'Category', 'kaitoriakiba' );
    $new['taxonomy-brand'] = __( 'Brand', 'kaitoriakiba' );
    $new['taxonomy-carrier'] = __( 'Carrier', 'kaitoriakiba' );
    $new['date'] = $columns['date'];
    return $new;
}
add_filter( 'manage_kaitori_device_posts_columns', 'kaitoriakiba_device_columns' );

function kaitoriakiba_device_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'device_thumb':
            $custom_img = get_post_meta( $post_id, '_device_primary_image_url', true );
            if ( ! $custom_img && has_post_thumbnail( $post_id ) ) {
                $custom_img = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
            }
            if ( ! $custom_img ) {
                $custom_img = get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png';
            }
            echo '<img src="' . esc_url( $custom_img ) . '" style="width:50px; height:50px; object-fit:contain; border-radius:4px; border:1px solid #e2e8f0; background:#fff; padding:2px;" />';
            break;
        case 'max_price':
            $price = get_post_meta( $post_id, '_max_buyback_price', true );
            echo $price ? '<strong style="color:#dc2626; font-size:14px;">¥' . number_format( (int)$price ) . '</strong>' : '—';
            break;
        case 'ranks':
            $s = get_post_meta( $post_id, '_price_rank_s', true );
            $d = get_post_meta( $post_id, '_price_rank_d', true );
            if ( $s || $d ) {
                echo '<small>S: ¥' . number_format( (int)$s ) . '<br>D: ¥' . number_format( (int)$d ) . '</small>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action( 'manage_kaitori_device_posts_custom_column', 'kaitoriakiba_device_custom_column', 10, 2 );

/**
 * Custom Admin Columns for eKYC Records
 */
function kaitoriakiba_kyc_columns( $columns ) {
    return array(
        'cb'            => $columns['cb'],
        'title'         => __( 'Record ID / Title', 'kaitoriakiba' ),
        'kyc_name'      => __( 'Applicant Name', 'kaitoriakiba' ),
        'kyc_doc'       => __( 'Document Type', 'kaitoriakiba' ),
        'kyc_status'    => __( 'Verification Status', 'kaitoriakiba' ),
        'kyc_uploads'   => __( 'Uploaded IDs', 'kaitoriakiba' ),
        'date'          => __( 'Submission Date', 'kaitoriakiba' ),
    );
}
add_filter( 'manage_kyc_record_posts_columns', 'kaitoriakiba_kyc_columns' );

function kaitoriakiba_kyc_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'kyc_name':
            $name = get_post_meta( $post_id, '_kyc_name', true );
            $kana = get_post_meta( $post_id, '_kyc_kana', true );
            echo '<strong>' . esc_html( $name ?: '—' ) . '</strong>';
            if ( $kana ) echo '<br><small>' . esc_html( $kana ) . '</small>';
            break;
        case 'kyc_doc':
            echo esc_html( get_post_meta( $post_id, '_kyc_doc_type', true ) ?: '—' );
            break;
        case 'kyc_status':
            $status = get_post_meta( $post_id, '_kyc_status', true ) ?: 'pending';
            if ( $status === 'verified' ) {
                echo '<span style="color:#16a34a; font-weight:700;"><span class="dashicons dashicons-yes-alt"></span> ' . __( 'Verified (Approved)', 'kaitoriakiba' ) . '</span>';
            } elseif ( $status === 'rejected' ) {
                echo '<span style="color:#dc2626; font-weight:700;"><span class="dashicons dashicons-dismiss"></span> ' . __( 'Rejected (Invalid ID)', 'kaitoriakiba' ) . '</span>';
            } else {
                echo '<span style="color:#f59e0b; font-weight:700;"><span class="dashicons dashicons-clock"></span> ' . __( 'Pending Review', 'kaitoriakiba' ) . '</span>';
            }
            break;
        case 'kyc_uploads':
            $front_id = get_post_meta( $post_id, '_kyc_id_front_attach', true );
            $back_id  = get_post_meta( $post_id, '_kyc_id_back_attach', true );
            if ( $front_id ) {
                echo '<a href="' . esc_url( wp_get_attachment_url( $front_id ) ) . '" target="_blank" class="button button-small">' . __( 'View Front', 'kaitoriakiba' ) . '</a> ';
            }
            if ( $back_id ) {
                echo '<a href="' . esc_url( wp_get_attachment_url( $back_id ) ) . '" target="_blank" class="button button-small">' . __( 'View Back', 'kaitoriakiba' ) . '</a>';
            }
            if ( ! $front_id && ! $back_id ) echo '—';
            break;
    }
}
add_action( 'manage_kyc_record_posts_custom_column', 'kaitoriakiba_kyc_custom_column', 10, 2 );
