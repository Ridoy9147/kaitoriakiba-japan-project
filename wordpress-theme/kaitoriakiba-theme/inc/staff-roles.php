<?php
/**
 * Kaitori Akiba — Dedicated Shop Staff Role & Granular Permissions Management
 *
 * Implements a restricted 'shop_staff' WordPress user role designed for shop
 * employees and operators. Provides access to daily buyback operations:
 * - Buyback Orders (view, edit, status update, pricing, walk-in creation)
 * - Buyback Devices & Pricing Matrix (view, edit, prices, categories)
 * - eKYC Identity Verification (view submissions, verify, approve/reject)
 * - Customer Management (view customer history, eKYC status, edit contact)
 *
 * Prevents full website control:
 * - Restricts access to core WordPress settings, plugins, themes, and Elementor.
 * - Granular control checkboxes managed by administrators via Theme Settings:
 *   Appearance > Staff Permissions, Buyback Orders > Staff Permissions, or Users > Staff Permissions.
 *
 * @package KaitoriAkiba
 * @version 2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return default staff role permissions array.
 *
 * @return array
 */
function kaitoriakiba_default_staff_permissions() {
    return array(
        // 1. Buyback Orders (買取注文)
        'orders_view'        => 1, // Browse orders list, full-text IMEI search, customer details
        'orders_edit'        => 1, // Edit order status, IMEI, specs, buyback price, bank details
        'orders_create'      => 1, // Create walk-in / in-store customer buyback applications
        'orders_delete'      => 0, // Delete/trash orders (safe default: OFF to protect audit history)

        // 2. eKYC Identity Verifications (本人確認)
        'ekyc_view'          => 1, // View eKYC submissions & inspect decrypted ID documents
        'ekyc_verify'        => 1, // Approve, reject, update eKYC status and write staff review notes
        'ekyc_delete'        => 0, // Delete eKYC records (safe default: OFF)

        // 3. Customer Management (顧客管理)
        'customers_view'     => 1, // Access dedicated Customers dashboard (history, eKYC, favorites)
        'customers_edit'     => 1, // Edit customer contact details & address
        'users_manage'       => 0, // Access core WordPress Users list (safe default: OFF)

        // 4. Buyback Devices & Pricing (買取端末・査定価格)
        'devices_view'       => 1, // View buyback devices catalog
        'devices_edit'       => 1, // Edit devices & update condition buyback prices (New, Used A/B/C, Junk)
        'devices_create'     => 1, // Add new devices to catalog
        'devices_delete'     => 0, // Delete devices from catalog (safe default: OFF)
        'taxonomies_manage'  => 1, // Manage device categories & carriers

        // 5. Media & Assets (メディア・ファイル)
        'media_upload'       => 1, // Upload images & inspection photos to Media Library

        // 6. Website General Control (ウェブサイト全般・保護)
        'posts_pages_manage' => 0, // Edit WordPress blog posts & site pages (safe default: OFF)
        'elementor_manage'   => 0, // Access Elementor Builder (safe default: OFF)
    );
}

/**
 * Retrieve current staff permissions merged with defaults.
 *
 * @return array
 */
function kaitoriakiba_get_staff_permissions() {
    $defaults = kaitoriakiba_default_staff_permissions();
    $saved    = get_option( 'kaitoriakiba_staff_permissions', array() );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }
    return wp_parse_args( $saved, $defaults );
}

/**
 * Register the 'shop_staff' role in WordPress.
 */
function kaitoriakiba_register_staff_role() {
    $role = get_role( 'shop_staff' );
    if ( ! $role ) {
        add_role(
            'shop_staff',
            kaitori_t( 'Shop Staff', 'ショップスタッフ' ),
            array(
                'read'         => true,
                'upload_files' => true,
            )
        );
    }
}
add_action( 'init', 'kaitoriakiba_register_staff_role', 5 );

/**
 * Dynamically assign capabilities to 'shop_staff' users based on saved permissions.
 */
function kaitoriakiba_staff_grant_caps( $allcaps, $caps, $args, $user ) {
    if ( empty( $user ) || ! isset( $user->roles ) || ! is_array( $user->roles ) ) {
        return $allcaps;
    }
    // Only apply to shop_staff users and never restrict an administrator
    if ( ! in_array( 'shop_staff', $user->roles, true ) || in_array( 'administrator', $user->roles, true ) ) {
        return $allcaps;
    }

    $perms = kaitoriakiba_get_staff_permissions();

    // Base capability to access wp-admin
    $allcaps['read'] = true;

    // 1. Buyback Orders
    if ( ! empty( $perms['orders_view'] ) ) {
        $allcaps['read_kaitori_record']          = true;
        $allcaps['read_private_kaitori_records'] = true;
    } else {
        $allcaps['read_kaitori_record']          = false;
        $allcaps['read_private_kaitori_records'] = false;
    }

    if ( ! empty( $perms['orders_edit'] ) ) {
        $allcaps['edit_kaitori_record']            = true;
        $allcaps['edit_kaitori_records']           = true;
        $allcaps['edit_others_kaitori_records']    = true;
        $allcaps['edit_published_kaitori_records'] = true;
        $allcaps['edit_private_kaitori_records']   = true;
    } else {
        $allcaps['edit_kaitori_record']            = false;
        $allcaps['edit_others_kaitori_records']    = false;
        $allcaps['edit_published_kaitori_records'] = false;
        $allcaps['edit_private_kaitori_records']   = false;
    }

    if ( ! empty( $perms['orders_create'] ) ) {
        $allcaps['create_kaitori_records']  = true;
        $allcaps['publish_kaitori_records'] = true;
    } else {
        $allcaps['create_kaitori_records']  = false;
        $allcaps['publish_kaitori_records'] = false;
    }

    if ( ! empty( $perms['orders_delete'] ) ) {
        $allcaps['delete_kaitori_record']            = true;
        $allcaps['delete_kaitori_records']           = true;
        $allcaps['delete_others_kaitori_records']    = true;
        $allcaps['delete_published_kaitori_records'] = true;
        $allcaps['delete_private_kaitori_records']   = true;
    } else {
        $allcaps['delete_kaitori_record']            = false;
        $allcaps['delete_kaitori_records']           = false;
        $allcaps['delete_others_kaitori_records']    = false;
        $allcaps['delete_published_kaitori_records'] = false;
        $allcaps['delete_private_kaitori_records']   = false;
    }

    // 2. eKYC Verifications & Customer Management
    // Both share kaitoriakiba_manage_cap() which returns 'edit_kaitori_records'
    if ( ! empty( $perms['ekyc_view'] ) || ! empty( $perms['ekyc_verify'] ) || ! empty( $perms['customers_view'] ) || ! empty( $perms['orders_edit'] ) ) {
        $allcaps['edit_kaitori_records'] = true;
    }

    if ( ! empty( $perms['ekyc_view'] ) ) {
        $allcaps['read_kaitori_record'] = true;
    }

    // 3. Customers & Users
    if ( ! empty( $perms['users_manage'] ) ) {
        $allcaps['list_users'] = true;
        $allcaps['edit_users'] = true;
    } else {
        $allcaps['list_users'] = false;
        $allcaps['edit_users'] = false;
    }

    // 4. Buyback Devices
    if ( ! empty( $perms['devices_view'] ) || ! empty( $perms['devices_edit'] ) ) {
        $allcaps['edit_posts']           = true;
        $allcaps['edit_published_posts'] = true;
        $allcaps['edit_others_posts']    = true;
        $allcaps['read_private_posts']   = true;
    } else {
        $allcaps['edit_posts']           = false;
        $allcaps['edit_published_posts'] = false;
        $allcaps['edit_others_posts']    = false;
    }

    if ( ! empty( $perms['devices_create'] ) ) {
        $allcaps['publish_posts'] = true;
    } else {
        $allcaps['publish_posts'] = false;
    }

    if ( ! empty( $perms['devices_delete'] ) ) {
        $allcaps['delete_posts']           = true;
        $allcaps['delete_published_posts'] = true;
        $allcaps['delete_others_posts']    = true;
    } else {
        $allcaps['delete_posts']           = false;
        $allcaps['delete_published_posts'] = false;
        $allcaps['delete_others_posts']    = false;
    }

    if ( ! empty( $perms['taxonomies_manage'] ) ) {
        $allcaps['manage_categories'] = true;
    } else {
        $allcaps['manage_categories'] = false;
    }

    // 5. Media Upload
    if ( ! empty( $perms['media_upload'] ) ) {
        $allcaps['upload_files'] = true;
    } else {
        $allcaps['upload_files'] = false;
    }

    // 6. Posts & Pages
    if ( ! empty( $perms['posts_pages_manage'] ) ) {
        $allcaps['edit_pages']           = true;
        $allcaps['edit_published_pages'] = true;
        $allcaps['edit_others_pages']    = true;
        $allcaps['publish_pages']        = true;
    } else {
        $allcaps['edit_pages']           = false;
        $allcaps['edit_published_pages'] = false;
        $allcaps['edit_others_pages']    = false;
        $allcaps['publish_pages']        = false;
    }

    // Strictly deny website core administrative controls to staff
    $allcaps['manage_options']     = false;
    $allcaps['switch_themes']      = false;
    $allcaps['edit_theme_options'] = false;
    $allcaps['activate_plugins']   = false;
    $allcaps['edit_plugins']       = false;
    $allcaps['install_plugins']    = false;
    $allcaps['update_plugins']     = false;
    $allcaps['update_themes']      = false;
    $allcaps['update_core']        = false;

    return $allcaps;
}
add_filter( 'user_has_cap', 'kaitoriakiba_staff_grant_caps', 10, 4 );

/**
 * Filter meta capabilities to prevent deletion or editing of disallowed items.
 */
function kaitoriakiba_staff_map_meta_cap( $caps, $cap, $user_id, $args ) {
    $user = get_userdata( $user_id );
    if ( ! $user || ! in_array( 'shop_staff', (array) $user->roles, true ) || in_array( 'administrator', (array) $user->roles, true ) ) {
        return $caps;
    }

    $perms = kaitoriakiba_get_staff_permissions();

    // Specific post capability checks
    if ( in_array( $cap, array( 'edit_post', 'delete_post', 'read_post' ), true ) && ! empty( $args[0] ) ) {
        $post = get_post( $args[0] );
        if ( $post ) {
            // Block editing standard blog posts and website pages if posts_pages_manage is OFF
            if ( in_array( $post->post_type, array( 'post', 'page' ), true ) && empty( $perms['posts_pages_manage'] ) ) {
                return array( 'do_not_allow' );
            }
            // Block deleting buyback orders if orders_delete is OFF
            if ( 'buyback_order' === $post->post_type && 'delete_post' === $cap && empty( $perms['orders_delete'] ) ) {
                return array( 'do_not_allow' );
            }
            // Block deleting eKYC submissions if ekyc_delete is OFF
            if ( 'kyc_record' === $post->post_type && 'delete_post' === $cap && empty( $perms['ekyc_delete'] ) ) {
                return array( 'do_not_allow' );
            }
            // Block deleting devices if devices_delete is OFF
            if ( 'kaitori_device' === $post->post_type && 'delete_post' === $cap && empty( $perms['devices_delete'] ) ) {
                return array( 'do_not_allow' );
            }
        }
    }

    // Never allow staff to edit or delete administrator accounts
    if ( in_array( $cap, array( 'edit_user', 'delete_user' ), true ) && ! empty( $args[0] ) ) {
        $target_user_id = (int) $args[0];
        if ( user_can( $target_user_id, 'manage_options' ) ) {
            return array( 'do_not_allow' );
        }
    }

    return $caps;
}
add_filter( 'map_meta_cap', 'kaitoriakiba_staff_map_meta_cap', 10, 4 );

/**
 * Clean up admin sidebar menu for Shop Staff role.
 * Hides all technical/administrative menus and unpermitted modules.
 */
function kaitoriakiba_staff_filter_menus() {
    $user = wp_get_current_user();
    if ( ! in_array( 'shop_staff', (array) $user->roles, true ) || in_array( 'administrator', (array) $user->roles, true ) ) {
        return;
    }

    $perms = kaitoriakiba_get_staff_permissions();

    // Core administrative pages to remove
    remove_menu_page( 'options-general.php' ); // Settings
    remove_menu_page( 'plugins.php' );         // Plugins
    remove_menu_page( 'themes.php' );          // Appearance
    remove_menu_page( 'tools.php' );           // Tools
    remove_menu_page( 'elementor' );           // Elementor
    remove_menu_page( 'edit.php?post_type=elementor_library' );

    // Conditionally remove based on settings
    if ( empty( $perms['posts_pages_manage'] ) ) {
        remove_menu_page( 'edit.php' );                // Posts
        remove_menu_page( 'edit.php?post_type=page' ); // Pages
        remove_menu_page( 'edit-comments.php' );       // Comments
    }

    if ( empty( $perms['users_manage'] ) ) {
        remove_menu_page( 'users.php' );
    }

    if ( empty( $perms['media_upload'] ) ) {
        remove_menu_page( 'upload.php' );
    }

    if ( empty( $perms['orders_view'] ) ) {
        remove_menu_page( 'edit.php?post_type=buyback_order' );
    }

    if ( empty( $perms['devices_view'] ) ) {
        remove_menu_page( 'edit.php?post_type=kaitori_device' );
    }

    if ( empty( $perms['ekyc_view'] ) ) {
        remove_menu_page( 'edit.php?post_type=kyc_record' );
    }

    if ( empty( $perms['customers_view'] ) ) {
        remove_menu_page( 'kaitori-customers' );
    }
}
add_action( 'admin_menu', 'kaitoriakiba_staff_filter_menus', 999 );

/**
 * Clean up top Admin Bar for Shop Staff.
 */
function kaitoriakiba_staff_clean_admin_bar( $wp_admin_bar ) {
    $user = wp_get_current_user();
    if ( ! in_array( 'shop_staff', (array) $user->roles, true ) || in_array( 'administrator', (array) $user->roles, true ) ) {
        return;
    }

    $perms = kaitoriakiba_get_staff_permissions();

    $wp_admin_bar->remove_node( 'customize' );
    $wp_admin_bar->remove_node( 'comments' );
    $wp_admin_bar->remove_node( 'themes' );
    $wp_admin_bar->remove_node( 'elementor_inspector' );

    if ( empty( $perms['posts_pages_manage'] ) ) {
        $wp_admin_bar->remove_node( 'new-post' );
        $wp_admin_bar->remove_node( 'new-page' );
    }
}
add_action( 'admin_bar_menu', 'kaitoriakiba_staff_clean_admin_bar', 999 );

/**
 * Enforce page restrictions when directly accessed via URL.
 */
function kaitoriakiba_staff_enforce_admin_restrictions() {
    if ( ! is_admin() || wp_doing_ajax() ) {
        return;
    }
    $user = wp_get_current_user();
    if ( ! in_array( 'shop_staff', (array) $user->roles, true ) || in_array( 'administrator', (array) $user->roles, true ) ) {
        return;
    }

    global $pagenow;
    $blocked = array(
        'options-general.php', 'options-writing.php', 'options-reading.php',
        'options-discussion.php', 'options-media.php', 'options-permalink.php',
        'options-privacy.php', 'plugins.php', 'plugin-install.php', 'plugin-editor.php',
        'themes.php', 'theme-editor.php', 'customize.php', 'tools.php', 'import.php',
        'export.php', 'site-health.php',
    );

    $perms = kaitoriakiba_get_staff_permissions();

    if ( empty( $perms['users_manage'] ) ) {
        $blocked[] = 'users.php';
        $blocked[] = 'user-new.php';
    }

    if ( empty( $perms['posts_pages_manage'] ) ) {
        $blocked[] = 'edit-comments.php';
        if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit.php' ), true ) ) {
            $post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
            if ( ! $post_type && isset( $_GET['post'] ) ) {
                $p = get_post( (int) $_GET['post'] );
                if ( $p ) {
                    $post_type = $p->post_type;
                }
            }
            if ( in_array( $post_type, array( 'post', 'page' ), true ) || ( $pagenow === 'edit.php' && empty( $post_type ) ) ) {
                wp_die(
                    esc_html( kaitori_t( 'Access restricted: Staff are not permitted to manage website blog posts or pages.', 'アクセス制限: スタッフはブログ記事や固定ページを管理する権限がありません。' ) ),
                    esc_html( kaitori_t( 'Permission Denied', '権限エラー' ) ),
                    array( 'back_link' => true )
                );
            }
        }
    }

    if ( in_array( $pagenow, $blocked, true ) ) {
        wp_die(
            esc_html( kaitori_t( 'Access restricted: This administrative section is reserved for site administrators only.', 'アクセス制限: この管理画面はサイト管理者専用です。' ) ),
            esc_html( kaitori_t( 'Permission Denied', '権限エラー' ) ),
            array( 'back_link' => true )
        );
    }
}
add_action( 'admin_init', 'kaitoriakiba_staff_enforce_admin_restrictions' );

/**
 * Redirect Shop Staff to Buyback Orders list on login for a streamlined workflow.
 */
function kaitoriakiba_staff_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'shop_staff', $user->roles, true ) && ! in_array( 'administrator', $user->roles, true ) ) {
            return admin_url( 'edit.php?post_type=buyback_order' );
        }
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'kaitoriakiba_staff_login_redirect', 10, 3 );

/**
 * Filter editable roles so staff cannot assign administrator roles.
 */
function kaitoriakiba_staff_filter_editable_roles( $roles ) {
    $user = wp_get_current_user();
    if ( in_array( 'shop_staff', (array) $user->roles, true ) && ! in_array( 'administrator', (array) $user->roles, true ) ) {
        unset( $roles['administrator'] );
        unset( $roles['editor'] );
        unset( $roles['author'] );
    }
    return $roles;
}
add_filter( 'editable_roles', 'kaitoriakiba_staff_filter_editable_roles' );

/**
 * Register Admin Settings Pages for Staff Role & Permissions.
 * Placed under:
 * 1. Appearance > Staff Permissions (Theme Settings)
 * 2. Buyback Orders > Staff Permissions
 * 3. Users > Staff Permissions
 */
function kaitoriakiba_staff_admin_menu() {
    // 1. Under Appearance (Theme Settings)
    add_theme_page(
        kaitori_t( 'Shop Staff Permissions & Controls', 'ショップスタッフ権限設定' ),
        kaitori_t( 'Staff Permissions', 'スタッフ権限設定' ),
        'manage_options',
        'kaitori-staff-permissions',
        'kaitoriakiba_staff_permissions_page'
    );

    // 2. Under Buyback Orders
    add_submenu_page(
        'edit.php?post_type=buyback_order',
        kaitori_t( 'Shop Staff Permissions', 'スタッフ権限設定' ),
        kaitori_t( 'Staff Permissions', 'スタッフ権限設定' ),
        'manage_options',
        'kaitori-staff-permissions',
        'kaitoriakiba_staff_permissions_page'
    );

    // 3. Under Users menu
    add_submenu_page(
        'users.php',
        kaitori_t( 'Shop Staff Permissions', 'スタッフ権限設定' ),
        kaitori_t( 'Staff Permissions', 'スタッフ権限設定' ),
        'manage_options',
        'kaitori-staff-permissions',
        'kaitoriakiba_staff_permissions_page'
    );
}
add_action( 'admin_menu', 'kaitoriakiba_staff_admin_menu' );

/**
 * Render Staff Permissions Admin Page.
 */
function kaitoriakiba_staff_permissions_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html( kaitori_t( 'You do not have sufficient permissions to access this page.', 'このページにアクセスする権限がありません。' ) ) );
    }

    $notice = '';

    // Handle Save / Reset Actions
    if ( isset( $_POST['kaitori_staff_action'] ) && check_admin_referer( 'kaitori_staff_perms_action', 'kaitori_staff_nonce' ) ) {
        if ( $_POST['kaitori_staff_action'] === 'save' ) {
            $submitted = isset( $_POST['perms'] ) && is_array( $_POST['perms'] ) ? $_POST['perms'] : array();
            $new_perms = array();
            foreach ( kaitoriakiba_default_staff_permissions() as $key => $default_val ) {
                $new_perms[ $key ] = ! empty( $submitted[ $key ] ) ? 1 : 0;
            }
            update_option( 'kaitoriakiba_staff_permissions', $new_perms );
            $notice = 'saved';
        } elseif ( $_POST['kaitori_staff_action'] === 'reset' ) {
            update_option( 'kaitoriakiba_staff_permissions', kaitoriakiba_default_staff_permissions() );
            $notice = 'reset';
        }
    }

    $perms = kaitoriakiba_get_staff_permissions();

    // Query active staff users
    $staff_query = new WP_User_Query( array(
        'role'    => 'shop_staff',
        'orderby' => 'registered',
        'order'   => 'ASC',
    ) );
    $staff_users = $staff_query->get_results();
    $staff_count = count( $staff_users );

    ?>
    <div class="wrap kaitori-staff-wrap" style="max-width:1160px; margin:20px auto 40px;">

        <style>
            .kaitori-staff-wrap * { box-sizing: border-box; }
            .kstaff-hero {
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                color: #fff;
                padding: 28px 32px;
                border-radius: 14px;
                box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
                margin-bottom: 24px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 18px;
            }
            .kstaff-hero h1 {
                color: #fff;
                font-size: 24px;
                font-weight: 700;
                margin: 0 0 8px 0;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .kstaff-hero p {
                color: #cbd5e1;
                margin: 0;
                font-size: 14px;
                line-height: 1.6;
                max-width: 680px;
            }
            .kstaff-badge-box {
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 12px;
                padding: 12px 20px;
                text-align: center;
                min-width: 170px;
            }
            .kstaff-badge-num {
                font-size: 28px;
                font-weight: 800;
                color: #38bdf8;
                line-height: 1;
            }
            .kstaff-badge-label {
                font-size: 12px;
                color: #e2e8f0;
                margin-top: 4px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .kstaff-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
                gap: 20px;
                margin-bottom: 26px;
            }
            .kstaff-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.03);
                overflow: hidden;
                display: flex;
                flex-direction: column;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }
            .kstaff-card:hover {
                box-shadow: 0 6px 16px rgba(0,0,0,0.06);
            }
            .kstaff-card-header {
                padding: 16px 20px;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .kstaff-card-icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 20px;
            }
            .kstaff-card-title {
                font-size: 16px;
                font-weight: 700;
                color: #0f172a;
                margin: 0;
            }
            .kstaff-card-sub {
                font-size: 12px;
                color: #64748b;
                margin: 2px 0 0;
            }
            .kstaff-card-body {
                padding: 18px 20px;
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 14px;
            }
            .kstaff-perm-row {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding-bottom: 12px;
                border-bottom: 1px dashed #f1f5f9;
            }
            .kstaff-perm-row:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }
            .kstaff-perm-row input[type="checkbox"] {
                width: 20px;
                height: 20px;
                margin-top: 2px;
                accent-color: #2563eb;
                cursor: pointer;
            }
            .kstaff-perm-label {
                cursor: pointer;
                flex: 1;
            }
            .kstaff-perm-name {
                font-size: 14px;
                font-weight: 600;
                color: #1e293b;
                display: block;
            }
            .kstaff-perm-desc {
                font-size: 12px;
                color: #64748b;
                line-height: 1.45;
                margin-top: 2px;
                display: block;
            }
            .kstaff-tag-danger {
                display: inline-block;
                background: #fee2e2;
                color: #991b1b;
                font-size: 10.5px;
                font-weight: 700;
                padding: 2px 6px;
                border-radius: 4px;
                margin-left: 6px;
                vertical-align: middle;
            }
            .kstaff-tag-rec {
                display: inline-block;
                background: #dcfce7;
                color: #166534;
                font-size: 10.5px;
                font-weight: 700;
                padding: 2px 6px;
                border-radius: 4px;
                margin-left: 6px;
                vertical-align: middle;
            }
            .kstaff-actions-bar {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 18px 24px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 14px;
                margin-bottom: 30px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            }
            .kstaff-users-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 24px;
                margin-bottom: 24px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            }
        </style>

        <?php if ( $notice === 'saved' ) : ?>
            <div class="notice notice-success is-dismissible" style="border-radius:8px; border-left-color:#10b981; margin:16px 0 20px;">
                <p><strong><span class="dashicons dashicons-yes-alt" style="color:#10b981; vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Staff permissions successfully saved!', 'ショップスタッフの権限設定を保存しました。' ) ); ?></strong></p>
            </div>
        <?php elseif ( $notice === 'reset' ) : ?>
            <div class="notice notice-info is-dismissible" style="border-radius:8px; border-left-color:#0284c7; margin:16px 0 20px;">
                <p><strong><span class="dashicons dashicons-image-rotate" style="color:#0284c7; vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Staff permissions have been reset to recommended shop defaults.', 'ショップスタッフの権限設定を推奨初期値にリセットしました。' ) ); ?></strong></p>
            </div>
        <?php endif; ?>

        <!-- Hero Header -->
        <div class="kstaff-hero">
            <div>
                <h1>
                    <span class="dashicons dashicons-id" style="font-size:28px;"></span>
                    <?php echo esc_html( kaitori_t( 'Shop Staff Role & Permissions', 'ショップスタッフ権限管理' ) ); ?>
                </h1>
                <p>
                    <?php echo esc_html( kaitori_t(
                        'Control exactly what shop staff members can view, edit, or modify in the WordPress Admin. Staff only have access to daily shop operations (Buyback Orders, Devices, eKYC, and Customers) and are strictly prevented from altering website theme settings, plugins, or system configuration.',
                        'ショップスタッフ（店舗担当者）が管理画面で操作できる権限を細かく設定できます。スタッフには日々の買取業務（注文管理・端末査定・eKYC本人確認・顧客管理）のみを許可し、テーマ設定やプラグインなどのサイト全般の管理は非公開となります。'
                    ) ); ?>
                </p>
            </div>
            <div class="kstaff-badge-box">
                <div class="kstaff-badge-num"><?php echo esc_html( $staff_count ); ?></div>
                <div class="kstaff-badge-label"><?php echo esc_html( kaitori_t( 'Active Staff Users', '登録スタッフ数' ) ); ?></div>
                <div style="margin-top:10px;">
                    <a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" class="button button-small" style="font-weight:600; border-radius:6px; color:#0f172a;">
                        <span class="dashicons dashicons-plus-alt2" style="font-size:14px; vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Add New Staff', 'スタッフ新規追加' ) ); ?>
                    </a>
                </div>
            </div>
        </div>

        <form method="post" action="">
            <?php wp_nonce_field( 'kaitori_staff_perms_action', 'kaitori_staff_nonce' ); ?>
            <input type="hidden" name="kaitori_staff_action" id="kaitori_staff_action" value="save">

            <!-- Permissions Cards Grid -->
            <div class="kstaff-grid">

                <!-- 1. Buyback Orders -->
                <div class="kstaff-card">
                    <div class="kstaff-card-header">
                        <div class="kstaff-card-icon" style="background:#2563eb;">
                            <span class="dashicons dashicons-clipboard"></span>
                        </div>
                        <div>
                            <div class="kstaff-card-title"><?php echo esc_html( kaitori_t( 'Buyback Orders', '買取注文' ) ); ?></div>
                            <div class="kstaff-card-sub"><?php echo esc_html( kaitori_t( 'Order processing & IMEI lookups', '申込対応・IMEI検索・査定更新' ) ); ?></div>
                        </div>
                    </div>
                    <div class="kstaff-card-body">
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[orders_view]" id="perm_orders_view" value="1" <?php checked( $perms['orders_view'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_orders_view">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'View & Search Orders', '注文一覧の閲覧・IMEI検索' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Browse orders list, search by 15-digit IMEI, order ID, or customer name.', '申込一覧の閲覧、15桁のIMEI番号・注文ID・顧客名による検索ができます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[orders_edit]" id="perm_orders_edit" value="1" <?php checked( $perms['orders_edit'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_orders_edit">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Edit Orders & Final Price', '注文の編集・最終査定額の変更' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Update order status, correct IMEI, adjust final payout amount, and edit bank info.', 'ステータス更新、IMEI修正、店頭査定後の最終買取額の確定、振込口座の変更が可能です。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[orders_create]" id="perm_orders_create" value="1" <?php checked( $perms['orders_create'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_orders_create">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Create In-Store / Walk-in Orders', '店頭申込の新規作成' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Add new in-store walk-in buyback applications directly from admin dashboard.', '店舗に来店されたお客様の買取申込を管理画面から即時新規登録できます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[orders_delete]" id="perm_orders_delete" value="1" <?php checked( $perms['orders_delete'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_orders_delete">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Delete / Trash Orders', '買取注文の削除' ) ); ?> <span class="kstaff-tag-danger"><?php echo esc_html( kaitori_t( 'Caution', '注意' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Allow staff to delete buyback orders. Recommended OFF to preserve transaction records.', 'スタッフによる注文データの削除を許可します（古物台帳保護のため通常OFF推奨）。' ) ); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 2. eKYC Identity Verifications -->
                <div class="kstaff-card">
                    <div class="kstaff-card-header">
                        <div class="kstaff-card-icon" style="background:#0284c7;">
                            <span class="dashicons dashicons-id-alt"></span>
                        </div>
                        <div>
                            <div class="kstaff-card-title"><?php echo esc_html( kaitori_t( 'eKYC Identity Verifications', '本人確認 (eKYC)' ) ); ?></div>
                            <div class="kstaff-card-sub"><?php echo esc_html( kaitori_t( 'Customer identity proof & approval', '身分証明書の確認・審査承認' ) ); ?></div>
                        </div>
                    </div>
                    <div class="kstaff-card-body">
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[ekyc_view]" id="perm_ekyc_view" value="1" <?php checked( $perms['ekyc_view'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_ekyc_view">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'View eKYC Submissions & ID Images', '提出書類の閲覧・身分証画像確認' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Inspect applicant details and securely decrypt driver licenses, passports, or My Number cards.', '申請者の情報および暗号化された身分証明書画像（免許証・マイナンバー等）を確認できます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[ekyc_verify]" id="perm_ekyc_verify" value="1" <?php checked( $perms['ekyc_verify'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_ekyc_verify">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Approve / Reject & Edit KYC', '本人確認の承認・却下・情報修正' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Change verification status (Approved, Rejected) and write internal staff notes or correct typos.', '審査結果の判定、スタッフ確認メモの追記、誤入力された氏名や住所の修正が可能です。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[ekyc_delete]" id="perm_ekyc_delete" value="1" <?php checked( $perms['ekyc_delete'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_ekyc_delete">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Delete eKYC Submissions', '本人確認記録の削除' ) ); ?> <span class="kstaff-tag-danger"><?php echo esc_html( kaitori_t( 'Caution', '注意' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Allow staff to delete KYC submissions. Recommended OFF to comply with Secondhand Articles Dealer Act.', '本人確認データの削除を許可します（古物営業法の保存義務遵守のためOFF推奨）。' ) ); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 3. Customers Management -->
                <div class="kstaff-card">
                    <div class="kstaff-card-header">
                        <div class="kstaff-card-icon" style="background:#0d9488;">
                            <span class="dashicons dashicons-groups"></span>
                        </div>
                        <div>
                            <div class="kstaff-card-title"><?php echo esc_html( kaitori_t( 'Customers Management', '顧客管理' ) ); ?></div>
                            <div class="kstaff-card-sub"><?php echo esc_html( kaitori_t( 'Customer database & buyback history', '顧客台帳・取引履歴・連絡先' ) ); ?></div>
                        </div>
                    </div>
                    <div class="kstaff-card-body">
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[customers_view]" id="perm_customers_view" value="1" <?php checked( $perms['customers_view'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_customers_view">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Access Customers Dashboard', '顧客台帳の閲覧' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'View aggregated customer profiles, lifetime orders, eKYC status, and favorites.', '全顧客の一覧、過去の買取履歴、本人確認ステータス、お気に入り端末を閲覧できます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[customers_edit]" id="perm_customers_edit" value="1" <?php checked( $perms['customers_edit'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_customers_edit">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Edit Customer Contact & Address', '顧客の連絡先・住所の編集' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Modify customer phone number, kana, and address when updating customer files in-store.', '店頭でのヒアリングに基づき、電話番号や住所等の顧客登録情報を更新できます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[users_manage]" id="perm_users_manage" value="1" <?php checked( $perms['users_manage'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_users_manage">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Manage Core WordPress Users', 'WordPress一般ユーザー管理' ) ); ?></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Allow staff to browse core Users list (users.php). Staff cannot edit or delete admin accounts.', 'WordPress標準のユーザー一覧（users.php）へのアクセスを許可します（管理者権限は保護されます）。' ) ); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 4. Buyback Devices & Pricing -->
                <div class="kstaff-card">
                    <div class="kstaff-card-header">
                        <div class="kstaff-card-icon" style="background:#7c3aed;">
                            <span class="dashicons dashicons-smartphone"></span>
                        </div>
                        <div>
                            <div class="kstaff-card-title"><?php echo esc_html( kaitori_t( 'Buyback Devices & Pricing', '買取端末・価格設定' ) ); ?></div>
                            <div class="kstaff-card-sub"><?php echo esc_html( kaitori_t( 'Device specifications & condition pricing', '端末スペック・状態別ランク査定額' ) ); ?></div>
                        </div>
                    </div>
                    <div class="kstaff-card-body">
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[devices_view]" id="perm_devices_view" value="1" <?php checked( $perms['devices_view'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_devices_view">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'View Devices Catalog', '買取端末一覧の閲覧' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Browse all devices, market prices, and condition ranks.', '取扱端末の一覧や設定されている買取上限価格を閲覧できます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[devices_edit]" id="perm_devices_edit" value="1" <?php checked( $perms['devices_edit'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_devices_edit">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Edit Devices & Pricing Matrix', '買取価格・ランク価格の編集' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Update daily market prices, rank condition pricing (New, Used A/B/C, Junk), and storage tiers.', '日々の市場相場に応じたランク別価格（新品・中古A/B/C・ジャンク）を更新できます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[devices_create]" id="perm_devices_create" value="1" <?php checked( $perms['devices_create'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_devices_create">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Add New Devices', '新規端末の追加登録' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Publish new smartphone and tablet models to the public catalog.', '新しいスマートフォンやタブレットのモデルを新機種としてカタログに登録できます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[taxonomies_manage]" id="perm_taxonomies_manage" value="1" <?php checked( $perms['taxonomies_manage'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_taxonomies_manage">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Manage Categories & Carriers', 'カテゴリ・キャリアの管理' ) ); ?></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Organize device categories (iPhone, iPad, Android) and carriers (SIM Free, docomo, au, etc.).', '端末カテゴリ（iPhone, iPad, Android等）や対応キャリアの整理・追加ができます。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[devices_delete]" id="perm_devices_delete" value="1" <?php checked( $perms['devices_delete'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_devices_delete">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Delete Devices', '端末データの削除' ) ); ?> <span class="kstaff-tag-danger"><?php echo esc_html( kaitori_t( 'Caution', '注意' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Allow staff to delete devices permanently from catalog.', '端末データの削除を許可します（誤削除防止のため通常OFF推奨）。' ) ); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 5. Media & Assets -->
                <div class="kstaff-card">
                    <div class="kstaff-card-header">
                        <div class="kstaff-card-icon" style="background:#ea580c;">
                            <span class="dashicons dashicons-format-image"></span>
                        </div>
                        <div>
                            <div class="kstaff-card-title"><?php echo esc_html( kaitori_t( 'Media & Assets', 'メディア・画像' ) ); ?></div>
                            <div class="kstaff-card-sub"><?php echo esc_html( kaitori_t( 'Photo uploads & inspection files', '画像アップロード・端末写真' ) ); ?></div>
                        </div>
                    </div>
                    <div class="kstaff-card-body">
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[media_upload]" id="perm_media_upload" value="1" <?php checked( $perms['media_upload'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_media_upload">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Upload Media Files', 'メディアライブラリへのアップロード' ) ); ?> <span class="kstaff-tag-rec"><?php echo esc_html( kaitori_t( 'Recommended', '推奨' ) ); ?></span></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Allow staff to upload device thumbnails, inspection damage photos, or receipts.', '端末のサムネイル画像、査定時の状態写真、納品書等をアップロードできます。' ) ); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 6. Website General Control -->
                <div class="kstaff-card">
                    <div class="kstaff-card-header">
                        <div class="kstaff-card-icon" style="background:#475569;">
                            <span class="dashicons dashicons-admin-settings"></span>
                        </div>
                        <div>
                            <div class="kstaff-card-title"><?php echo esc_html( kaitori_t( 'Website General Control', 'サイト全般・保護' ) ); ?></div>
                            <div class="kstaff-card-sub"><?php echo esc_html( kaitori_t( 'CMS pages, posts, and Elementor', 'ブログ記事・固定ページ・Elementor' ) ); ?></div>
                        </div>
                    </div>
                    <div class="kstaff-card-body">
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[posts_pages_manage]" id="perm_posts_pages_manage" value="1" <?php checked( $perms['posts_pages_manage'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_posts_pages_manage">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Manage Blog Posts & Site Pages', 'ブログ記事・固定ページの編集' ) ); ?></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Allow staff to edit regular WordPress blog posts, announcements, and static landing pages.', 'スタッフにお知らせ記事や固定ページの作成・編集を許可します（通常はOFF推奨）。' ) ); ?></span>
                            </label>
                        </div>
                        <div class="kstaff-perm-row">
                            <input type="checkbox" name="perms[elementor_manage]" id="perm_elementor_manage" value="1" <?php checked( $perms['elementor_manage'], 1 ); ?>>
                            <label class="kstaff-perm-label" for="perm_elementor_manage">
                                <span class="kstaff-perm-name"><?php echo esc_html( kaitori_t( 'Allow Elementor Builder Access', 'Elementorビルダーの利用' ) ); ?></span>
                                <span class="kstaff-perm-desc"><?php echo esc_html( kaitori_t( 'Allow staff to edit templates in Elementor. Normally restricted to site designers.', 'スタッフによるElementor編集を許可します（デザイン崩れ防止のため通常OFF推奨）。' ) ); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sticky Actions Bar -->
            <div class="kstaff-actions-bar">
                <div style="display:flex; align-items:center; gap:12px;">
                    <button type="submit" class="button button-primary button-hero" style="font-size:15px; font-weight:700; border-radius:8px; padding:4px 28px;">
                        <span class="dashicons dashicons-saved" style="vertical-align:middle; font-size:18px;"></span> <?php echo esc_html( kaitori_t( 'Save Staff Permissions', '権限設定を保存する' ) ); ?>
                    </button>
                    <button type="button" class="button button-secondary" onclick="if(confirm('<?php echo esc_js( kaitori_t( 'Reset all permissions to recommended shop staff defaults?', 'スタッフ権限を推奨初期値にリセットしますか？' ) ); ?>')) { document.getElementById('kaitori_staff_action').value='reset'; this.form.submit(); }" style="border-radius:8px; font-weight:600; padding:4px 18px;">
                        <span class="dashicons dashicons-image-rotate" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Reset to Recommended Defaults', '推奨初期値に戻す' ) ); ?>
                    </button>
                </div>
                <div style="color:#64748b; font-size:13px;">
                    <span class="dashicons dashicons-shield-alt" style="color:#10b981; vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Security hardened: Theme and plugin settings remain administrator-only.', 'セキュリティ保護: テーマ設定・プラグイン管理は管理者のみ利用可能です。' ) ); ?>
                </div>
            </div>
        </form>

        <!-- Registered Staff Members Table -->
        <div class="kstaff-users-card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; border-bottom:1px solid #e2e8f0; padding-bottom:14px; flex-wrap:wrap; gap:12px;">
                <div>
                    <h2 style="font-size:18px; margin:0; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px;">
                        <span class="dashicons dashicons-admin-users" style="color:#2563eb;"></span>
                        <?php echo esc_html( kaitori_t( 'Registered Shop Staff Accounts', '登録済みスタッフアカウント一覧' ) ); ?>
                        <span style="background:#e0e7ff; color:#3730a3; font-size:12px; font-weight:700; padding:2px 8px; border-radius:999px; margin-left:6px;"><?php echo esc_html( $staff_count ); ?></span>
                    </h2>
                    <p style="margin:4px 0 0; color:#64748b; font-size:13px;">
                        <?php echo esc_html( kaitori_t( 'All WordPress user accounts currently assigned the "Shop Staff" role.', '現在「ショップスタッフ (shop_staff)」ロールが割り当てられているユーザーです。' ) ); ?>
                    </p>
                </div>
                <a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" class="button button-primary" style="font-weight:700; border-radius:6px;">
                    <span class="dashicons dashicons-plus-alt" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Add New Staff Member', '新規スタッフを追加' ) ); ?>
                </a>
            </div>

            <?php if ( ! empty( $staff_users ) ) : ?>
                <table class="wp-list-table widefat fixed striped" style="border:none; box-shadow:none;">
                    <thead>
                        <tr>
                            <th style="width:240px;"><?php echo esc_html( kaitori_t( 'Staff Name', 'スタッフ氏名' ) ); ?></th>
                            <th><?php echo esc_html( kaitori_t( 'Username', 'ユーザー名' ) ); ?></th>
                            <th><?php echo esc_html( kaitori_t( 'Email', 'メールアドレス' ) ); ?></th>
                            <th style="width:160px;"><?php echo esc_html( kaitori_t( 'Registered Date', '登録日時' ) ); ?></th>
                            <th style="width:120px; text-align:right;"><?php echo esc_html( kaitori_t( 'Actions', '操作' ) ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $staff_users as $staff ) : ?>
                            <tr>
                                <td style="vertical-align:middle;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <?php echo get_avatar( $staff->ID, 34, '', '', array( 'style' => 'border-radius:50%;' ) ); ?>
                                        <div>
                                            <strong><?php echo esc_html( $staff->display_name ?: $staff->user_login ); ?></strong>
                                            <?php if ( $staff->first_name || $staff->last_name ) : ?>
                                                <div style="font-size:12px; color:#64748b;"><?php echo esc_html( trim( $staff->last_name . ' ' . $staff->first_name ) ); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td style="vertical-align:middle;"><code><?php echo esc_html( $staff->user_login ); ?></code></td>
                                <td style="vertical-align:middle;"><a href="mailto:<?php echo esc_attr( $staff->user_email ); ?>"><?php echo esc_html( $staff->user_email ); ?></a></td>
                                <td style="vertical-align:middle; color:#64748b;"><?php echo esc_html( date_i18n( 'Y/m/d H:i', strtotime( $staff->user_registered ) ) ); ?></td>
                                <td style="vertical-align:middle; text-align:right;">
                                    <a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $staff->ID ) ); ?>" class="button button-small" style="font-weight:600;">
                                        <?php echo esc_html( kaitori_t( 'Edit', '編集' ) ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div style="padding:32px 20px; text-align:center; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:10px;">
                    <span class="dashicons dashicons-businessman" style="font-size:42px; color:#94a3b8; height:42px; margin-bottom:12px;"></span>
                    <h3 style="font-size:16px; margin:0 0 6px; color:#334155;"><?php echo esc_html( kaitori_t( 'No Shop Staff Users Yet', 'スタッフアカウントがまだ登録されていません' ) ); ?></h3>
                    <p style="color:#64748b; font-size:13.5px; max-width:480px; margin:0 auto 16px;">
                        <?php echo esc_html( kaitori_t(
                            'To create your first staff member, click the button below, enter their details, and select "Shop Staff" in the Role dropdown.',
                            '最初のスタッフを登録するには下のボタンをクリックし、ロール（権限グループ）で「ショップスタッフ (Shop Staff)」を選択してください。'
                        ) ); ?>
                    </p>
                    <a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" class="button button-primary" style="font-weight:700;">
                        <span class="dashicons dashicons-plus-alt" style="vertical-align:middle;"></span> <?php echo esc_html( kaitori_t( 'Create First Staff Account', '最初のスタッフを新規作成' ) ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Setup Instructions Guide -->
        <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:12px; padding:20px 24px; color:#334155;">
            <h4 style="margin:0 0 8px; font-size:14px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                <span class="dashicons dashicons-info" style="color:#2563eb;"></span>
                <?php echo esc_html( kaitori_t( 'How to assign the Shop Staff role to a user:', 'スタッフアカウントの作成・割り当て方法:' ) ); ?>
            </h4>
            <ol style="margin:0; padding-left:20px; font-size:13px; line-height:1.7; color:#475569;">
                <li><?php echo kaitori_t( 'Go to <strong>Users &rarr; Add New</strong> in the WordPress admin menu.', 'WordPress管理画面の <strong>ユーザー &rarr; 新規追加</strong> を開きます。' ); ?></li>
                <li><?php echo kaitori_t( 'Fill in the staff member\'s Username, Email, and Name.', 'スタッフのユーザー名、メールアドレス、氏名を入力します。' ); ?></li>
                <li><?php echo kaitori_t( 'In the <strong>Role</strong> dropdown, choose <strong>Shop Staff (ショップスタッフ)</strong> and click <strong>Add New User</strong>.', '<strong>権限グループ (Role)</strong> のプルダウンから <strong>「ショップスタッフ (Shop Staff)」</strong> を選択して「新規ユーザーを追加」をクリックします。' ); ?></li>
                <li><?php echo kaitori_t( 'When the staff member logs in, they will automatically see their clean shop dashboard without any risky settings or theme controls.', 'スタッフがログインすると、不要な設定画面のない安全な店舗専用管理画面が表示されます。' ); ?></li>
            </ol>
        </div>

    </div>
    <?php
}
