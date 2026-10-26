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
 * The full set of auto-generated capabilities for the shared 'kaitori_record'
 * capability_type used by buyback_order + kyc_record. WordPress generates these
 * from map_meta_cap=true; we grant them to the right users via the filter below.
 *
 * IMPORTANT: we must NOT remap core meta caps (edit_post/read_post/delete_post) to
 * a core primitive like manage_options — doing so registers that primitive as a
 * meta-cap alias and makes map_meta_cap() return "do_not_allow" for it site-wide.
 *
 * @return string[]
 */
function kaitoriakiba_record_caps_list() {
    $s = 'kaitori_record';
    $p = 'kaitori_records';
    return array(
        "edit_$p", "edit_others_$p", "publish_$p", "read_private_$p",
        "delete_$p", "delete_private_$p", "delete_published_$p", "delete_others_$p",
        "edit_private_$p", "edit_published_$p", "create_$p",
        "edit_$s", "read_$s", "delete_$s",
    );
}

/**
 * The capability required to view the Customers page / manage orders & eKYC.
 * A real auto-generated primitive (never a meta cap), so direct current_user_can()
 * checks work. Filterable for a future dedicated staff role.
 */
function kaitoriakiba_manage_cap() {
    return apply_filters( 'kaitori_manage_cap', 'edit_kaitori_records' );
}

/**
 * Grant the kaitori_record caps at runtime to anyone with manage_options
 * (administrators). Reliable and persistence-free; a staff role can be added later.
 */
function kaitoriakiba_grant_record_caps( $allcaps ) {
    if ( ! empty( $allcaps['manage_options'] ) ) {
        foreach ( kaitoriakiba_record_caps_list() as $c ) {
            $allcaps[ $c ] = true;
        }
    }
    return $allcaps;
}
add_filter( 'user_has_cap', 'kaitoriakiba_grant_record_caps' );

/**
 * Register Custom Post Types and Taxonomies
 */
function kaitoriakiba_register_cpt_taxonomies() {

    // 1. Custom Post Type: Kaitori Devices
    $device_labels = array(
        'name'                  => kaitori_t( 'Buyback Devices', '買取端末' ),
        'singular_name'         => kaitori_t( 'Buyback Device', '買取端末' ),
        'menu_name'             => kaitori_t( 'Buyback Devices', '買取端末' ),
        'name_admin_bar'        => kaitori_t( 'Device', '端末' ),
        'archives'              => kaitori_t( 'Device Archives', '端末アーカイブ' ),
        'all_items'             => kaitori_t( 'All Devices', 'すべての端末' ),
        'add_new_item'          => kaitori_t( 'Add New Device', '新規端末を追加' ),
        'add_new'               => kaitori_t( 'Add New Device', '新規端末を追加' ),
        'new_item'              => kaitori_t( 'New Device', '新規端末' ),
        'edit_item'             => kaitori_t( 'Edit Device', '端末を編集' ),
        'update_item'           => kaitori_t( 'Update Device', '端末を更新' ),
        'view_item'             => kaitori_t( 'View Device', '端末を表示' ),
        'search_items'          => kaitori_t( 'Search Devices', '端末を検索' ),
    );

    $device_args = array(
        'label'                 => kaitori_t( 'Buyback Device', '買取端末' ),
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
        'name'                  => kaitori_t( 'Buyback Orders', '買取注文' ),
        'singular_name'         => kaitori_t( 'Buyback Order', '買取注文' ),
        'menu_name'             => kaitori_t( 'Buyback Orders', '買取注文' ),
        'all_items'             => kaitori_t( 'All Applications', 'すべての申込' ),
        'add_new_item'          => kaitori_t( 'Add New Application', '新規申込を追加' ),
        'edit_item'             => kaitori_t( 'View / Edit Application', '申込を表示 / 編集' ),
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
        // Restrict to admins (and any future role granted the kaitori_record caps).
        // Dedicated capability_type so core generates proper primitives.
        'capability_type'       => array( 'kaitori_record', 'kaitori_records' ),
        'map_meta_cap'          => true,
    );
    register_post_type( 'buyback_order', $order_args );

    // 3. Custom Post Type: eKYC Submissions
    $kyc_labels = array(
        'name'                  => kaitori_t( 'eKYC Verifications', '本人確認 (eKYC)' ),
        'singular_name'         => kaitori_t( 'eKYC Verification', '本人確認 (eKYC)' ),
        'menu_name'             => kaitori_t( 'eKYC Verifications', '本人確認 (eKYC)' ),
        'all_items'             => kaitori_t( 'All Submissions', 'すべての提出' ),
        'edit_item'             => kaitori_t( 'View / Verify ID', 'ID を表示 / 審査' ),
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
        // Highly sensitive (identity documents): same restriction as orders.
        'capability_type'       => array( 'kaitori_record', 'kaitori_records' ),
        'map_meta_cap'          => true,
    );
    register_post_type( 'kyc_record', $kyc_args );

    // 4. Taxonomy: Device Category
    register_taxonomy( 'device_category', array( 'kaitori_device' ), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'              => kaitori_t( 'Device Categories', '端末カテゴリ' ),
            'singular_name'     => kaitori_t( 'Device Category', '端末カテゴリ' ),
            'search_items'      => kaitori_t( 'Search Categories', 'カテゴリを検索' ),
            'all_items'         => kaitori_t( 'All Categories', 'すべてのカテゴリ' ),
            'parent_item'       => kaitori_t( 'Parent Category', '親カテゴリ' ),
            'parent_item_colon' => kaitori_t( 'Parent Category:', '親カテゴリ:' ),
            'edit_item'         => kaitori_t( 'Edit Category', 'カテゴリを編集' ),
            'update_item'       => kaitori_t( 'Update Category', 'カテゴリを更新' ),
            'add_new_item'      => kaitori_t( 'Add New Category', '新規カテゴリを追加' ),
            'menu_name'         => kaitori_t( 'Categories', 'カテゴリ' ),
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
            'name'          => kaitori_t( 'Carriers', 'キャリア' ),
            'singular_name' => kaitori_t( 'Carrier', 'キャリア' ),
            'menu_name'     => kaitori_t( 'Carriers', 'キャリア' ),
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
            'name'          => kaitori_t( 'Brands', 'ブランド' ),
            'singular_name' => kaitori_t( 'Brand', 'ブランド' ),
            'menu_name'     => kaitori_t( 'Brands', 'ブランド' ),
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
            'name'          => kaitori_t( 'Series', 'シリーズ' ),
            'singular_name' => kaitori_t( 'Series', 'シリーズ' ),
            'menu_name'     => kaitori_t( 'Series', 'シリーズ' ),
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
 * Canonical buyback order statuses — single source of truth used by the admin
 * list column, the order meta-box dropdown, and customer status emails.
 *
 * @return array<string,array{label:string,label_ja:string,bg:string,color:string}>
 */
function kaitoriakiba_order_statuses() {
    return apply_filters( 'kaitori_order_statuses', array(
        'pending'    => array( 'label' => 'Pending Review',        'label_ja' => '審査待ち',       'bg' => '#fef3c7', 'color' => '#b45309' ),
        'received'   => array( 'label' => 'Device Received',       'label_ja' => '端末到着・入荷', 'bg' => '#e0e7ff', 'color' => '#3730a3' ),
        'inspecting' => array( 'label' => 'Inspecting',            'label_ja' => '検品中',         'bg' => '#fce7f3', 'color' => '#9d174d' ),
        'inspected'  => array( 'label' => 'Inspection Completed',  'label_ja' => '検品完了',       'bg' => '#cffafe', 'color' => '#0e7490' ),
        'paid'       => array( 'label' => 'Paid / Completed',      'label_ja' => '入金完了',       'bg' => '#dcfce7', 'color' => '#15803d' ),
        'cancelled'  => array( 'label' => 'Cancelled / Returned',  'label_ja' => 'キャンセル・返送', 'bg' => '#fee2e2', 'color' => '#991b1b' ),
    ) );
}

/**
 * Get a single status row with a safe fallback to "pending".
 */
function kaitoriakiba_get_order_status( $status ) {
    $all = kaitoriakiba_order_statuses();
    return isset( $all[ $status ] ) ? $all[ $status ] : $all['pending'];
}

/**
 * Custom Admin Columns for Buyback Orders
 */
function kaitoriakiba_order_columns( $columns ) {
    $new_columns = array(
        'cb'              => $columns['cb'],
        'title'           => kaitori_t( 'Application ID / Title', '申込ID / タイトル' ),
        'applicant_name'  => kaitori_t( 'Customer Name', 'お客様名' ),
        'device_model'    => kaitori_t( 'Device & Storage', '端末・容量' ),
        'estimated_price' => kaitori_t( 'Estimated Payout', '概算査定額' ),
        'delivery_method' => kaitori_t( 'Delivery Method', '受取方法' ),
        'order_status'    => kaitori_t( 'Status', 'ステータス' ),
        'date'            => kaitori_t( 'Date', '日付' ),
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
                echo '<span class="dashicons dashicons-store" style="color:#f59e0b; vertical-align:middle;"></span> ' . esc_html( kaitori_t( 'In-Store Visit', '店頭来店' ) );
            } else {
                echo '<span class="dashicons dashicons-car" style="color:#10b981; vertical-align:middle;"></span> ' . esc_html( kaitori_t( 'Mail-in Delivery', '郵送' ) );
            }
            break;
        case 'order_status':
            $status = get_post_meta( $post_id, '_order_status', true ) ?: 'pending';
            $s_info = kaitoriakiba_get_order_status( $status );
            echo '<span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11.5px; font-weight:700; background:' . esc_attr( $s_info['bg'] ) . '; color:' . esc_attr( $s_info['color'] ) . ';">' . esc_html( kaitori_t( $s_info['label'], $s_info['label_ja'] ) ) . '</span>';
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
    $new['device_thumb'] = kaitori_t( 'Image', '画像' );
    $new['title'] = $columns['title'];
    $new['max_price'] = kaitori_t( 'Max Buyback Price', '最高買取価格' );
    $new['ranks'] = kaitori_t( 'Rank S - D Pricing', 'ランク S〜D 価格' );
    $new['taxonomy-device_category'] = kaitori_t( 'Category', 'カテゴリ' );
    $new['taxonomy-brand'] = kaitori_t( 'Brand', 'ブランド' );
    $new['taxonomy-carrier'] = kaitori_t( 'Carrier', 'キャリア' );
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
        'title'         => kaitori_t( 'Record ID', 'レコードID' ),
        'kyc_name'      => kaitori_t( 'Applicant Name', '申請者名' ),
        'kyc_contact'   => kaitori_t( 'Email & Phone', 'メール・電話' ),
        'kyc_user'      => kaitori_t( 'Customer Account', '会員アカウント' ),
        'kyc_order'     => kaitori_t( 'Linked Order', '紐付け注文' ),
        'kyc_doc'       => kaitori_t( 'Document Type', '書類種別' ),
        'kyc_uploads'   => kaitori_t( 'ID Photos', 'ID写真' ),
        'kyc_status'    => kaitori_t( 'Verification Status', '審査ステータス' ),
        'date'          => kaitori_t( 'Submission Date', '提出日' ),
    );
}
add_filter( 'manage_kyc_record_posts_columns', 'kaitoriakiba_kyc_columns' );

function kaitoriakiba_kyc_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'kyc_name':
            $name = get_post_meta( $post_id, '_kyc_name', true );
            $kana = get_post_meta( $post_id, '_kyc_kana', true );
            echo '<strong>' . esc_html( $name ?: '—' ) . '</strong>';
            if ( $kana ) echo '<br><small style="color:#64748b;">' . esc_html( $kana ) . '</small>';
            break;

        case 'kyc_contact':
            $email = get_post_meta( $post_id, '_kyc_email', true );
            $phone = get_post_meta( $post_id, '_kyc_phone', true );
            if ( $email ) echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a><br>';
            if ( $phone ) echo '<small><a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a></small>';
            if ( ! $email && ! $phone ) echo '—';
            break;

        case 'kyc_user':
            $user_id = get_post_meta( $post_id, '_kyc_user_id', true );
            if ( ! $user_id ) {
                $email = get_post_meta( $post_id, '_kyc_email', true );
                if ( $email ) {
                    $u = get_user_by( 'email', $email );
                    if ( $u ) $user_id = $u->ID;
                }
            }
            if ( $user_id ) {
                $u_data = get_userdata( $user_id );
                if ( $u_data ) {
                    echo '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $user_id ) ) . '" style="font-weight:bold; color:#2563eb;">';
                    echo '<span class="dashicons dashicons-admin-users" style="font-size:16px; vertical-align:middle;"></span> ';
                    echo esc_html( $u_data->display_name ?: $u_data->user_login );
                    echo '</a>';
                } else {
                    echo 'User #' . esc_html($user_id);
                }
            } else {
                echo '<span style="color:#94a3b8;">' . esc_html( kaitori_t( 'Guest', 'ゲスト' ) ) . '</span>';
            }
            break;

        case 'kyc_order':
            $order_code = get_post_meta( $post_id, '_kyc_order_code', true );
            $order_id   = get_post_meta( $post_id, '_kyc_order_id', true );
            if ( ! $order_id && $order_code ) {
                $orders = get_posts( array(
                    'post_type'   => 'buyback_order',
                    'title'       => $order_code,
                    'numberposts' => 1,
                    'post_status' => 'any',
                ) );
                if ( ! empty( $orders ) ) $order_id = $orders[0]->ID;
            }
            if ( $order_id ) {
                echo '<a href="' . esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ) . '" style="font-weight:bold; color:#dc2626;">';
                echo '<span class="dashicons dashicons-clipboard" style="font-size:16px; vertical-align:middle;"></span> ';
                echo esc_html( $order_code ?: 'Order #' . $order_id );
                echo '</a>';
            } elseif ( $order_code ) {
                echo '<code>' . esc_html( $order_code ) . '</code>';
            } else {
                echo '<span style="color:#94a3b8;">—</span>';
            }
            break;

        case 'kyc_doc':
            echo esc_html( get_post_meta( $post_id, '_kyc_doc_type', true ) ?: '—' );
            break;

        case 'kyc_uploads':
            $front_store = get_post_meta( $post_id, '_kyc_id_front_file', true ) ?: get_post_meta( $post_id, '_kyc_id_front_attach', true );
            $back_store  = get_post_meta( $post_id, '_kyc_id_back_file', true )  ?: get_post_meta( $post_id, '_kyc_id_back_attach', true );
            $front_url = function_exists( 'kaitoriakiba_kyc_resolve_src' ) ? kaitoriakiba_kyc_resolve_src( $front_store ) : $front_store;
            $back_url  = function_exists( 'kaitoriakiba_kyc_resolve_src' ) ? kaitoriakiba_kyc_resolve_src( $back_store )  : $back_store;
            
            if ( $front_url ) {
                echo '<a href="' . esc_url($front_url) . '" target="_blank" title="Front Photo">';
                echo '<img src="' . esc_url($front_url) . '" style="width:36px; height:26px; object-fit:cover; border-radius:3px; border:1px solid #cbd5e1; margin-right:4px; vertical-align:middle;" />';
                echo '</a>';
            }
            if ( $back_url ) {
                echo '<a href="' . esc_url($back_url) . '" target="_blank" title="Back Photo">';
                echo '<img src="' . esc_url($back_url) . '" style="width:36px; height:26px; object-fit:cover; border-radius:3px; border:1px solid #cbd5e1; vertical-align:middle;" />';
                echo '</a>';
            }
            if ( ! $front_url && ! $back_url ) echo '<span style="color:#94a3b8;">No Image</span>';
            break;

        case 'kyc_status':
            $status = get_post_meta( $post_id, '_kyc_status', true ) ?: 'pending';
            if ( $status === 'verified' ) {
                echo '<span style="color:#16a34a; font-weight:800; background:#dcfce7; padding:3px 8px; border-radius:10px;"><span class="dashicons dashicons-yes-alt" style="font-size:14px; vertical-align:middle;"></span> ' . esc_html( kaitori_t( 'Verified', '承認済み' ) ) . '</span>';
            } elseif ( $status === 'rejected' ) {
                echo '<span style="color:#dc2626; font-weight:800; background:#fee2e2; padding:3px 8px; border-radius:10px;"><span class="dashicons dashicons-dismiss" style="font-size:14px; vertical-align:middle;"></span> ' . esc_html( kaitori_t( 'Rejected', '不備・却下' ) ) . '</span>';
            } else {
                echo '<span style="color:#b45309; font-weight:800; background:#fef3c7; padding:3px 8px; border-radius:10px;"><span class="dashicons dashicons-clock" style="font-size:14px; vertical-align:middle;"></span> ' . esc_html( kaitori_t( 'Pending', '確認中' ) ) . '</span>';
            }
            break;
    }
}
add_action( 'manage_kyc_record_posts_custom_column', 'kaitoriakiba_kyc_custom_column', 10, 2 );

/**
 * Enable Full-Text Search in eKYC Records (Search by Name, Furigana, Email, Phone, Order Code)
 */
function kaitoriakiba_kyc_search_query( $query ) {
    global $pagenow, $wpdb;
    if ( is_admin() && $pagenow === 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'kyc_record' && ! empty( $_GET['s'] ) ) {
        $search = sanitize_text_field( $_GET['s'] );
        // Custom search across meta fields
        add_filter( 'posts_search', function( $search_sql, $wp_query ) use ( $search, $wpdb ) {
            global $wpdb;
            if ( ! is_admin() || ! $wp_query->is_main_query() ) {
                return $search_sql;
            }
            $term = '%' . $wpdb->esc_like( $search ) . '%';
            $meta_sql = $wpdb->prepare( "
                OR {$wpdb->posts}.ID IN (
                    SELECT post_id FROM {$wpdb->postmeta} 
                    WHERE meta_key IN ('_kyc_name', '_kyc_kana', '_kyc_email', '_kyc_phone', '_kyc_address', '_kyc_order_code')
                    AND meta_value LIKE %s
                )
            ", $term );

            return preg_replace( '/\)\s*$/', $meta_sql . ')', $search_sql );
        }, 10, 2 );
    }
}
add_action( 'pre_get_posts', 'kaitoriakiba_kyc_search_query' );
