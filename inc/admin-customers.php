<?php
/**
 * Kaitori Akiba — Admin Customer Management
 *
 * A dedicated, filterable admin page that aggregates every customer (registered
 * WP users + guest applicants) with their full buyback history, eKYC status,
 * favorites and (masked) payout details. Staff can view a detail panel, edit
 * contact info, update order/eKYC status inline, and scan device barcodes.
 *
 * Access is restricted to the custom 'manage_kaitori' capability (see cpt.php).
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Register the top-level admin menu. */
function kaitoriakiba_customers_menu() {
    add_menu_page(
        kaitori_t( 'Customers', '顧客管理' ),
        kaitori_t( 'Customers', '顧客管理' ),
        kaitoriakiba_manage_cap(),
        'kaitori-customers',
        'kaitoriakiba_customers_page',
        'dashicons-groups',
        8
    );
}
add_action( 'admin_menu', 'kaitoriakiba_customers_menu' );

/** Enqueue admin assets on our page and on the buyback_order editor. */
function kaitoriakiba_admin_assets( $hook ) {
    global $post;
    $on_customers = ( isset( $_GET['page'] ) && 'kaitori-customers' === $_GET['page'] );
    $on_order     = in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && isset( $post->post_type ) && 'buyback_order' === $post->post_type;

    if ( ! $on_customers && ! $on_order ) {
        return;
    }
    $uri = KAITORIAKIBA_URI;
    $ver = KAITORIAKIBA_VERSION;
    wp_enqueue_style( 'kaitori-admin', $uri . '/assets/css/admin-kaitori.css', array(), $ver );
    wp_enqueue_script( 'kaitori-admin', $uri . '/assets/js/admin-kaitori.js', array( 'jquery' ), $ver, true );
    wp_localize_script( 'kaitori-admin', 'kaitori_admin', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'kaitori_admin_nonce' ),
        'i18n'     => array(
            'no_camera'   => kaitori_t( 'The camera is not available (please use a USB scanner or manual entry).', 'カメラは利用できません（USBスキャナーまたは手入力をご利用ください）。' ),
            'scan_prompt' => kaitori_t( 'When using the camera: point the barcode at the camera…', 'カメラ利用時：バーコードをカメラに向けてください…' ),
            'usb_hint'    => kaitori_t( 'You can scan directly into the field below with a USB barcode scanner (saved automatically after scanning).', 'USBバーコードスキャナーで下の欄に直接スキャンできます（スキャン後は自動保存）。' ),
            'scan_here'   => kaitori_t( 'Scan / enter here (USB supported)', 'ここにスキャン / 手入力 (USB対応)' ),
            'saved'       => kaitori_t( 'Saved.', '保存しました。' ),
            'error'       => kaitori_t( 'An error occurred.', 'エラーが発生しました。' ),
        ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'kaitoriakiba_admin_assets' );

/**
 * Build the aggregated customer list from orders + registered users.
 *
 * @return array<string,array> keyed by lowercased email.
 */
function kaitoriakiba_build_customers() {
    $customers = array();

    // 1. From buyback orders (covers guests too).
    $orders = get_posts( array(
        'post_type'      => 'buyback_order',
        'post_status'    => 'any',
        'posts_per_page' => -1,
    ) );
    foreach ( $orders as $o ) {
        $email = strtolower( trim( (string) get_post_meta( $o->ID, '_applicant_email', true ) ) );
        if ( '' === $email ) {
            $email = 'order-' . $o->ID . '@no-email.local';
        }
        if ( ! isset( $customers[ $email ] ) ) {
            $customers[ $email ] = kaitoriakiba_blank_customer( $email );
        }
        $c =& $customers[ $email ];
        $c['orders'][]   = $o->ID;
        $c['order_count']++;
        $c['total_payout'] += (int) get_post_meta( $o->ID, '_estimated_price', true );
        $c['statuses'][] = get_post_meta( $o->ID, '_order_status', true ) ?: 'pending';
        $ts = get_post_timestamp( $o );
        if ( $ts && $ts > $c['last_ts'] ) {
            $c['last_ts'] = $ts;
        }
        if ( empty( $c['name'] ) )  { $c['name']  = get_post_meta( $o->ID, '_applicant_name', true ); }
        if ( empty( $c['phone'] ) ) { $c['phone'] = get_post_meta( $o->ID, '_applicant_phone', true ); }
        unset( $c );
    }

    // 2. Registered non-admin users (even without orders).
    $users = get_users( array( 'fields' => array( 'ID', 'user_email', 'display_name' ) ) );
    foreach ( $users as $u ) {
        if ( user_can( $u->ID, 'manage_options' ) ) {
            continue; // skip admins
        }
        $email = strtolower( trim( $u->user_email ) );
        if ( '' === $email ) {
            continue;
        }
        if ( ! isset( $customers[ $email ] ) ) {
            $customers[ $email ] = kaitoriakiba_blank_customer( $email );
        }
        $customers[ $email ]['user_id'] = $u->ID;
        if ( empty( $customers[ $email ]['name'] ) ) {
            $customers[ $email ]['name'] = $u->display_name;
        }
        if ( empty( $customers[ $email ]['phone'] ) ) {
            $customers[ $email ]['phone'] = get_user_meta( $u->ID, 'phone_number', true );
        }
    }

    // 3. Resolve eKYC status per customer.
    foreach ( $customers as $email => &$c ) {
        $c['kyc'] = kaitoriakiba_lookup_kyc_status( $email, $c['user_id'] );
    }
    unset( $c );

    return $customers;
}

function kaitoriakiba_blank_customer( $email ) {
    return array(
        'email' => $email, 'name' => '', 'phone' => '', 'user_id' => 0,
        'orders' => array(), 'order_count' => 0, 'total_payout' => 0,
        'statuses' => array(), 'last_ts' => 0, 'kyc' => 'none',
    );
}

/** Find an eKYC status by user id or email. */
function kaitoriakiba_lookup_kyc_status( $email, $user_id = 0 ) {
    if ( $user_id ) {
        $s = get_user_meta( $user_id, '_kaitori_kyc_status', true );
        if ( $s ) { return $s; }
    }
    if ( $email && false === strpos( $email, '@no-email.local' ) ) {
        $k = get_posts( array( 'post_type' => 'kyc_record', 'post_status' => 'any', 'numberposts' => 1, 'meta_key' => '_kyc_email', 'meta_value' => $email ) );
        if ( $k ) {
            return get_post_meta( $k[0]->ID, '_kyc_status', true ) ?: 'pending';
        }
    }
    return 'none';
}

/** eKYC status badge map. */
function kaitoriakiba_kyc_badge( $status ) {
    $map = array(
        'verified' => array( '#dcfce7', '#15803d', kaitori_t( 'Verified', '承認済み' ) ),
        'pending'  => array( '#fef3c7', '#b45309', kaitori_t( 'Pending', '確認中' ) ),
        'rejected' => array( '#fee2e2', '#b91c1c', kaitori_t( 'Rejected', '却下' ) ),
        'none'     => array( '#f1f5f9', '#64748b', kaitori_t( 'None', 'なし' ) ),
    );
    $m = isset( $map[ $status ] ) ? $map[ $status ] : $map['none'];
    return '<span style="background:' . esc_attr( $m[0] ) . ';color:' . esc_attr( $m[1] ) . ';padding:2px 10px;border-radius:10px;font-weight:700;font-size:11.5px;">' . esc_html( $m[2] ) . '</span>';
}

/** Main admin page controller: list or detail. */
function kaitoriakiba_customers_page() {
    if ( ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        wp_die( esc_html( kaitori_t( 'You do not have permission to access this page.', 'このページにアクセスする権限がありません。' ) ) );
    }
    $detail = isset( $_GET['customer'] ) ? sanitize_text_field( wp_unslash( $_GET['customer'] ) ) : '';
    if ( $detail ) {
        kaitoriakiba_render_customer_detail( $detail );
    } else {
        kaitoriakiba_render_customers_list();
    }
}

/** List view with filters + pagination. */
function kaitoriakiba_render_customers_list() {
    $all = kaitoriakiba_build_customers();

    // Filters.
    $s       = isset( $_GET['s'] ) ? strtolower( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) : '';
    $f_kyc   = isset( $_GET['kyc'] ) ? sanitize_text_field( $_GET['kyc'] ) : '';
    $f_ost   = isset( $_GET['ostatus'] ) ? sanitize_text_field( $_GET['ostatus'] ) : '';
    $f_type  = isset( $_GET['ctype'] ) ? sanitize_text_field( $_GET['ctype'] ) : '';
    $f_from  = isset( $_GET['from'] ) ? sanitize_text_field( $_GET['from'] ) : '';
    $f_to    = isset( $_GET['to'] ) ? sanitize_text_field( $_GET['to'] ) : '';
    $from_ts = $f_from ? strtotime( $f_from . ' 00:00:00' ) : 0;
    $to_ts   = $f_to ? strtotime( $f_to . ' 23:59:59' ) : 0;

    $rows = array_filter( $all, function( $c ) use ( $s, $f_kyc, $f_ost, $f_type, $from_ts, $to_ts ) {
        if ( $s && false === strpos( strtolower( $c['name'] . ' ' . $c['email'] . ' ' . $c['phone'] ), $s ) ) { return false; }
        if ( $f_kyc && $c['kyc'] !== $f_kyc ) { return false; }
        if ( $f_ost && ! in_array( $f_ost, $c['statuses'], true ) ) { return false; }
        if ( 'registered' === $f_type && ! $c['user_id'] ) { return false; }
        if ( 'guest' === $f_type && $c['user_id'] ) { return false; }
        if ( $from_ts && $c['last_ts'] < $from_ts ) { return false; }
        if ( $to_ts && $c['last_ts'] > $to_ts ) { return false; }
        return true;
    } );

    usort( $rows, function( $a, $b ) { return $b['last_ts'] <=> $a['last_ts']; } );

    // Pagination.
    $per   = 20;
    $paged = max( 1, isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 );
    $total = count( $rows );
    $pages = max( 1, (int) ceil( $total / $per ) );
    $rows  = array_slice( $rows, ( $paged - 1 ) * $per, $per );

    $statuses = kaitoriakiba_order_statuses();
    ?>
    <div class="wrap kaitori-admin">
        <h1 class="wp-heading-inline"><span class="dashicons dashicons-groups"></span> <?php echo esc_html( kaitori_t( 'Customers', '顧客管理' ) ); ?></h1>
        <p class="kaitori-sub"><?php printf( esc_html( kaitori_t( '%d customers total', '合計 %d 名の顧客' ) ), $total ); ?></p>

        <form method="get" class="kaitori-filters">
            <input type="hidden" name="page" value="kaitori-customers">
            <input type="search" name="s" value="<?php echo esc_attr( isset( $_GET['s'] ) ? wp_unslash( $_GET['s'] ) : '' ); ?>" placeholder="<?php echo esc_attr( kaitori_t( 'Search by name, email, phone', '名前・メール・電話で検索' ) ); ?>">
            <select name="kyc">
                <option value=""><?php echo esc_html( kaitori_t( 'eKYC: All', 'eKYC: すべて' ) ); ?></option>
                <?php foreach ( array( 'verified' => 'Verified', 'pending' => 'Pending', 'rejected' => 'Rejected', 'none' => 'None' ) as $k => $l ) : ?>
                    <option value="<?php echo esc_attr( $k ); ?>" <?php selected( $f_kyc, $k ); ?>><?php echo esc_html( 'eKYC: ' . $l ); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="ostatus">
                <option value=""><?php echo esc_html( kaitori_t( 'Order status: All', '注文状態: すべて' ) ); ?></option>
                <?php foreach ( $statuses as $k => $st ) : ?>
                    <option value="<?php echo esc_attr( $k ); ?>" <?php selected( $f_ost, $k ); ?>><?php echo esc_html( kaitori_t( $st['label'], $st['label_ja'] ) ); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="ctype">
                <option value=""><?php echo esc_html( kaitori_t( 'Member/Guest: All', '会員/ゲスト: すべて' ) ); ?></option>
                <option value="registered" <?php selected( $f_type, 'registered' ); ?>><?php echo esc_html( kaitori_t( 'Members only', '会員のみ' ) ); ?></option>
                <option value="guest" <?php selected( $f_type, 'guest' ); ?>><?php echo esc_html( kaitori_t( 'Guests only', 'ゲストのみ' ) ); ?></option>
            </select>
            <label><?php echo esc_html( kaitori_t( 'Period', '期間' ) ); ?> <input type="date" name="from" value="<?php echo esc_attr( $f_from ); ?>"></label>
            <span>–</span>
            <input type="date" name="to" value="<?php echo esc_attr( $f_to ); ?>">
            <button class="button button-primary"><?php echo esc_html( kaitori_t( 'Filter', 'フィルター' ) ); ?></button>
            <a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=kaitori-customers' ) ); ?>"><?php echo esc_html( kaitori_t( 'Reset', 'リセット' ) ); ?></a>
        </form>

        <table class="widefat striped kaitori-table">
            <thead><tr>
                <th><?php echo esc_html( kaitori_t( 'Customer', '顧客' ) ); ?></th>
                <th><?php echo esc_html( kaitori_t( 'Contact', '連絡先' ) ); ?></th>
                <th><?php echo esc_html( kaitori_t( 'Orders', '注文' ) ); ?></th>
                <th><?php echo esc_html( kaitori_t( 'Total Estimate', '合計査定額' ) ); ?></th>
                <th>eKYC</th>
                <th><?php echo esc_html( kaitori_t( 'Last Activity', '最終活動' ) ); ?></th>
                <th></th>
            </tr></thead>
            <tbody>
            <?php if ( empty( $rows ) ) : ?>
                <tr><td colspan="7" style="padding:24px;text-align:center;color:#64748b;"><?php echo esc_html( kaitori_t( 'No matching customers found.', '該当する顧客が見つかりませんでした。' ) ); ?></td></tr>
            <?php else : foreach ( $rows as $c ) :
                $detail_url = admin_url( 'admin.php?page=kaitori-customers&customer=' . rawurlencode( $c['email'] ) );
            ?>
                <tr>
                    <td>
                        <strong><?php echo esc_html( $c['name'] ?: '—' ); ?></strong>
                        <?php if ( $c['user_id'] ) : ?><span class="kaitori-pill kaitori-pill-member"><?php echo esc_html( kaitori_t( 'Member', '会員' ) ); ?></span><?php else : ?><span class="kaitori-pill"><?php echo esc_html( kaitori_t( 'Guest', 'ゲスト' ) ); ?></span><?php endif; ?>
                    </td>
                    <td><a href="mailto:<?php echo esc_attr( $c['email'] ); ?>"><?php echo esc_html( $c['email'] ); ?></a><br><small><?php echo esc_html( $c['phone'] ?: '' ); ?></small></td>
                    <td><strong><?php echo (int) $c['order_count']; ?></strong></td>
                    <td><strong style="color:#dc2626;">¥<?php echo number_format( (int) $c['total_payout'] ); ?></strong></td>
                    <td><?php echo kaitoriakiba_kyc_badge( $c['kyc'] ); ?></td>
                    <td><?php echo $c['last_ts'] ? esc_html( date_i18n( 'Y.m.d', $c['last_ts'] ) ) : '—'; ?></td>
                    <td><a class="button button-small" href="<?php echo esc_url( $detail_url ); ?>"><?php echo esc_html( kaitori_t( 'Details →', '詳細 →' ) ); ?></a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>

        <?php if ( $pages > 1 ) : ?>
            <div class="tablenav"><div class="tablenav-pages"><?php
                $base = add_query_arg( 'paged', '%#%' );
                echo paginate_links( array( 'base' => $base, 'format' => '', 'current' => $paged, 'total' => $pages ) );
            ?></div></div>
        <?php endif; ?>
    </div>
    <?php
}

/** Detail view for one customer (by email or user id). */
function kaitoriakiba_render_customer_detail( $key ) {
    // Resolve customer.
    $user_id = 0;
    $email   = '';
    if ( is_numeric( $key ) ) {
        $user_id = (int) $key;
        $u       = get_userdata( $user_id );
        $email   = $u ? strtolower( $u->user_email ) : '';
    } else {
        $email = strtolower( $key );
        $u     = get_user_by( 'email', $email );
        $user_id = $u ? $u->ID : 0;
    }

    $orders = get_posts( array(
        'post_type'      => 'buyback_order',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'meta_key'       => '_applicant_email',
        'meta_value'     => $email,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    $name  = $orders ? get_post_meta( $orders[0]->ID, '_applicant_name', true ) : ( $u ? $u->display_name : '' );
    $phone = $orders ? get_post_meta( $orders[0]->ID, '_applicant_phone', true ) : ( $user_id ? get_user_meta( $user_id, 'phone_number', true ) : '' );
    $kyc_status = kaitoriakiba_lookup_kyc_status( $email, $user_id );
    $statuses   = kaitoriakiba_order_statuses();
    $back_url   = admin_url( 'admin.php?page=kaitori-customers' );
    $favorites  = ( $user_id && function_exists( 'kaitoriakiba_get_user_favorites' ) ) ? kaitoriakiba_get_user_favorites( $user_id ) : array();
    ?>
    <div class="wrap kaitori-admin kaitori-detail" data-email="<?php echo esc_attr( $email ); ?>" data-user="<?php echo esc_attr( $user_id ); ?>">
        <a href="<?php echo esc_url( $back_url ); ?>" class="button">&larr; <?php echo esc_html( kaitori_t( 'Back to List', '一覧へ戻る' ) ); ?></a>
        <h1 class="wp-heading-inline" style="margin-top:10px;"><span class="dashicons dashicons-admin-users"></span> <?php echo esc_html( $name ?: $email ); ?></h1>

        <div class="kaitori-cards">
            <!-- Profile card (editable) -->
            <div class="kaitori-card">
                <h2><?php echo esc_html( kaitori_t( 'Customer Profile', '顧客プロフィール' ) ); ?></h2>
                <div class="kaitori-field"><label><?php echo esc_html( kaitori_t( 'Name', '氏名' ) ); ?></label><input type="text" id="cust_name" value="<?php echo esc_attr( $name ); ?>"></div>
                <div class="kaitori-field"><label><?php echo esc_html( kaitori_t( 'Phone', '電話' ) ); ?></label><input type="text" id="cust_phone" value="<?php echo esc_attr( $phone ); ?>"></div>
                <div class="kaitori-field"><label><?php echo esc_html( kaitori_t( 'Email', 'メール' ) ); ?></label><input type="text" value="<?php echo esc_attr( $email ); ?>" readonly></div>
                <div class="kaitori-field"><label>eKYC</label> <?php echo kaitoriakiba_kyc_badge( $kyc_status ); ?>
                    <?php if ( $user_id ) : ?><a class="button button-small" href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $user_id ) ); ?>" target="_blank"><?php echo esc_html( kaitori_t( 'WP Profile', 'WPプロフィール' ) ); ?></a><?php endif; ?>
                </div>
                <button class="button button-primary kaitori-save-customer"><?php echo esc_html( kaitori_t( 'Save Profile', 'プロフィールを保存' ) ); ?></button>
                <span class="kaitori-save-msg"></span>
            </div>

            <!-- Favorites card -->
            <div class="kaitori-card">
                <h2><?php echo esc_html( kaitori_t( 'Favorites', 'お気に入り' ) ); ?></h2>
                <?php if ( $favorites ) : ?>
                    <ul class="kaitori-fav-list">
                    <?php foreach ( $favorites as $fid ) : if ( 'kaitori_device' !== get_post_type( $fid ) ) { continue; } ?>
                        <li><a href="<?php echo esc_url( get_edit_post_link( $fid ) ); ?>"><?php echo esc_html( get_the_title( $fid ) ); ?></a> — ¥<?php echo number_format( (int) get_post_meta( $fid, '_max_buyback_price', true ) ); ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php else : ?><p class="kaitori-muted"><?php echo esc_html( kaitori_t( 'None', 'なし' ) ); ?></p><?php endif; ?>
            </div>
        </div>

        <!-- Orders -->
        <div class="kaitori-card kaitori-card-wide">
            <h2><?php echo esc_html( kaitori_t( 'Buyback Application History', '買取申込履歴' ) ); ?></h2>
            <?php if ( ! $orders ) : ?>
                <p class="kaitori-muted"><?php echo esc_html( kaitori_t( 'No orders.', '注文はありません。' ) ); ?></p>
            <?php else : ?>
            <table class="widefat striped">
                <thead><tr>
                    <th><?php echo esc_html( kaitori_t( 'Order No.', '受付番号' ) ); ?></th>
                    <th><?php echo esc_html( kaitori_t( 'Device', 'デバイス' ) ); ?></th>
                    <th>IMEI</th>
                    <th><?php echo esc_html( kaitori_t( 'Estimate', '査定額' ) ); ?></th>
                    <th><?php echo esc_html( kaitori_t( 'Status', 'ステータス' ) ); ?></th>
                    <th><?php echo esc_html( kaitori_t( 'Action', '操作' ) ); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ( $orders as $o ) :
                    $o_model = get_post_meta( $o->ID, '_device_model', true );
                    $o_stor  = get_post_meta( $o->ID, '_device_storage', true );
                    $o_imei  = get_post_meta( $o->ID, '_device_imei', true );
                    $o_price = (int) get_post_meta( $o->ID, '_estimated_price', true );
                    $o_st    = get_post_meta( $o->ID, '_order_status', true ) ?: 'pending';
                ?>
                    <tr data-order="<?php echo esc_attr( $o->ID ); ?>">
                        <td><a href="<?php echo esc_url( get_edit_post_link( $o->ID ) ); ?>"><?php echo esc_html( $o->post_title ); ?></a></td>
                        <td><?php echo esc_html( trim( $o_model . ' ' . $o_stor ) ); ?></td>
                        <td><span class="kaitori-imei"><?php echo esc_html( $o_imei ?: '—' ); ?></span>
                            <button type="button" class="button button-small kaitori-scan" data-order="<?php echo esc_attr( $o->ID ); ?>" data-field="imei">📷 <?php echo esc_html( kaitori_t( 'Scan', 'スキャン' ) ); ?></button>
                        </td>
                        <td><strong style="color:#dc2626;">¥<?php echo number_format( $o_price ); ?></strong></td>
                        <td>
                            <select class="kaitori-order-status" data-order="<?php echo esc_attr( $o->ID ); ?>">
                                <?php foreach ( $statuses as $k => $st ) : ?>
                                    <option value="<?php echo esc_attr( $k ); ?>" <?php selected( $o_st, $k ); ?>><?php echo esc_html( kaitori_t( $st['label'], $st['label_ja'] ) ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $o->ID ) ); ?>"><?php echo esc_html( kaitori_t( 'Edit →', '編集 →' ) ); ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/* ============================ Admin AJAX ============================ */

/** Shared guard for admin AJAX. */
function kaitoriakiba_admin_ajax_guard() {
    if ( ! check_ajax_referer( 'kaitori_admin_nonce', 'nonce', false ) || ! current_user_can( kaitoriakiba_manage_cap() ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'You do not have permission.', '権限がありません。' ) ) );
    }
}

/** Update a single order's status (triggers the status-change email via meta hook). */
function kaitoriakiba_ajax_admin_update_order_status() {
    kaitoriakiba_admin_ajax_guard();
    $order_id = isset( $_POST['order_id'] ) ? (int) $_POST['order_id'] : 0;
    $status   = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
    $valid    = array_keys( kaitoriakiba_order_statuses() );
    if ( ! $order_id || 'buyback_order' !== get_post_type( $order_id ) || ! in_array( $status, $valid, true ) ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Invalid data.', '無効なデータです。' ) ) );
    }
    update_post_meta( $order_id, '_order_status', $status );
    wp_send_json_success( array( 'message' => kaitori_t( 'Status updated.', 'ステータスを更新しました。' ) ) );
}
add_action( 'wp_ajax_kaitori_admin_update_order_status', 'kaitoriakiba_ajax_admin_update_order_status' );

/** Update customer contact (WP user + all matching orders). */
function kaitoriakiba_ajax_admin_update_customer() {
    kaitoriakiba_admin_ajax_guard();
    $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $user_id = isset( $_POST['user_id'] ) ? (int) $_POST['user_id'] : 0;
    $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';

    if ( $user_id ) {
        wp_update_user( array( 'ID' => $user_id, 'display_name' => $name ) );
        update_user_meta( $user_id, 'phone_number', $phone );
        update_user_meta( $user_id, 'billing_phone', $phone );
    }
    if ( $email ) {
        $orders = get_posts( array( 'post_type' => 'buyback_order', 'post_status' => 'any', 'posts_per_page' => -1, 'meta_key' => '_applicant_email', 'meta_value' => $email ) );
        foreach ( $orders as $o ) {
            update_post_meta( $o->ID, '_applicant_name', $name );
            update_post_meta( $o->ID, '_applicant_phone', $phone );
        }
    }
    wp_send_json_success( array( 'message' => kaitori_t( 'Customer information updated.', '顧客情報を更新しました。' ) ) );
}
add_action( 'wp_ajax_kaitori_admin_update_customer', 'kaitoriakiba_ajax_admin_update_customer' );

/** Save a scanned barcode number to an order's IMEI or serial field. */
function kaitoriakiba_ajax_admin_save_scan() {
    kaitoriakiba_admin_ajax_guard();
    $order_id = isset( $_POST['order_id'] ) ? (int) $_POST['order_id'] : 0;
    $field    = isset( $_POST['field'] ) && 'serial' === $_POST['field'] ? 'serial' : 'imei';
    $code     = isset( $_POST['code'] ) ? preg_replace( '/[^0-9A-Za-z]/', '', wp_unslash( $_POST['code'] ) ) : '';
    if ( ! $order_id || 'buyback_order' !== get_post_type( $order_id ) || '' === $code ) {
        wp_send_json_error( array( 'message' => kaitori_t( 'Invalid scan data.', '無効なスキャンデータです。' ) ) );
    }
    update_post_meta( $order_id, '_device_' . $field, $code );
    wp_send_json_success( array( 'message' => kaitori_t( 'Scan number saved.', 'スキャン番号を保存しました。' ), 'code' => $code, 'field' => $field ) );
}
add_action( 'wp_ajax_kaitori_admin_save_scan', 'kaitoriakiba_ajax_admin_save_scan' );
