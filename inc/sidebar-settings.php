<?php
/**
 * Kaitori Akiba — Sidebar Customization
 *
 * Makes the (previously hard-coded) native sidebar editable from wp-admin:
 *   - Promo banners + social (Twitter/LINE) + newsletter box  → Appearance → Sidebar
 *   - Carrier / Brand / Series logo lists                     → driven by taxonomy
 *       terms, with a per-term "Logo image" upload field (and a legacy fallback
 *       to /upload/{folder}/{slug}.jpg|png)
 *   - Quick Links menu                                        → Sidebar Quick Links
 *       menu location (Appearance → Menus), with a hard-coded fallback.
 *
 * Helpers used by sidebar.php:
 *   kaitoriakiba_sidebar_setting( $key, $default )
 *   kaitoriakiba_term_image_url( $term )
 *   kaitoriakiba_render_sidebar_tax_box( $taxonomy, $param, $folder, $title, $fallback )
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ============================ Option helper ============================ */

/**
 * Get one sidebar setting, falling back to a default when blank.
 *
 * @param string $key
 * @param string $default
 * @return string
 */
function kaitoriakiba_sidebar_setting( $key, $default = '' ) {
    $opts = get_option( 'kaitori_sidebar_settings', array() );
    if ( is_array( $opts ) && isset( $opts[ $key ] ) && '' !== trim( (string) $opts[ $key ] ) ) {
        return $opts[ $key ];
    }
    return $default;
}

/** Field definitions: key => [label, type('image'|'url'|'text')]. */
function kaitoriakiba_sidebar_fields() {
    return array(
        'banner1_img'     => array( kaitori_t( 'Top Banner — Image', 'トップバナー — 画像' ), 'image' ),
        'banner1_link'    => array( kaitori_t( 'Top Banner — Link URL', 'トップバナー — リンクURL' ), 'url' ),
        'banner2_img'     => array( kaitori_t( 'Member Banner — Image', '会員バナー — 画像' ), 'image' ),
        'banner2_link'    => array( kaitori_t( 'Member Banner — Link URL', '会員バナー — リンクURL' ), 'url' ),
        'social_img'      => array( kaitori_t( 'Social Header Image', 'SNS見出し画像' ), 'image' ),
        // Twitter/X & LINE URLs are shared site-wide — set them in Appearance → Store & Footer.
        'newsletter_img'  => array( kaitori_t( 'Newsletter — Image', 'メルマガ — 画像' ), 'image' ),
        'newsletter_text' => array( kaitori_t( 'Newsletter — Text', 'メルマガ — 説明文' ), 'text' ),
        'newsletter_link' => array( kaitori_t( 'Newsletter — Button Link', 'メルマガ — ボタンリンク' ), 'url' ),
    );
}

/* ============================ Settings page ============================ */

function kaitoriakiba_sidebar_settings_menu() {
    add_theme_page(
        kaitori_t( 'Sidebar Settings', 'サイドバー設定' ),
        kaitori_t( 'Sidebar', 'サイドバー' ),
        'manage_options',
        'kaitori-sidebar',
        'kaitoriakiba_sidebar_settings_page'
    );
}
add_action( 'admin_menu', 'kaitoriakiba_sidebar_settings_menu' );

function kaitoriakiba_sidebar_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html( kaitori_t( 'Permission denied.', '権限がありません。' ) ) );
    }

    $fields = kaitoriakiba_sidebar_fields();
    $saved  = false;

    if ( isset( $_POST['kaitori_sidebar_submit'] ) && check_admin_referer( 'kaitori_sidebar_settings' ) ) {
        $clean = array();
        foreach ( $fields as $key => $f ) {
            $raw = isset( $_POST[ 'ss_' . $key ] ) ? wp_unslash( $_POST[ 'ss_' . $key ] ) : '';
            if ( 'image' === $f[1] || 'url' === $f[1] ) {
                $clean[ $key ] = esc_url_raw( trim( $raw ) );
            } else {
                $clean[ $key ] = sanitize_text_field( $raw );
            }
        }
        update_option( 'kaitori_sidebar_settings', $clean );
        $saved = true;
    }

    $opts = get_option( 'kaitori_sidebar_settings', array() );
    if ( ! is_array( $opts ) ) {
        $opts = array();
    }
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-align-left" style="font-size:26px;"></span> <?php echo esc_html( kaitori_t( 'Sidebar Settings', 'サイドバー設定' ) ); ?></h1>
        <?php if ( $saved ) : ?><div class="notice notice-success is-dismissible"><p><strong><?php echo esc_html( kaitori_t( 'Saved.', '保存しました。' ) ); ?></strong></p></div><?php endif; ?>
        <p style="color:#475569;max-width:760px;">
            <?php echo esc_html( kaitori_t( 'Customize the sidebar promo banners, social links and newsletter box. Leave a field blank to use the theme default. The carrier / brand / series logo lists come from your taxonomy terms — set each logo on the term\'s own edit screen.', 'サイドバーのプロモバナー・SNSリンク・メルマガ欄を編集できます。空欄にするとテーマ初期値が使われます。キャリア／ブランド／シリーズのロゴ一覧は各タクソノミーの項目から取得されます（ロゴは各項目の編集画面で設定）。' ) ); ?>
        </p>
        <form method="post" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;max-width:860px;margin-top:14px;">
            <?php wp_nonce_field( 'kaitori_sidebar_settings' ); ?>
            <table class="form-table">
                <?php
                foreach ( $fields as $key => $f ) {
                    $val = isset( $opts[ $key ] ) ? $opts[ $key ] : '';
                    echo '<tr class="kaitori-media-field"><th scope="row"><label for="ss_' . esc_attr( $key ) . '">' . esc_html( $f[0] ) . '</label></th><td>';
                    echo '<input type="text" name="ss_' . esc_attr( $key ) . '" id="ss_' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" class="regular-text" style="width:100%;max-width:480px;">';
                    if ( 'image' === $f[1] ) {
                        echo ' <button class="button kaitori-media-pick" data-target="ss_' . esc_attr( $key ) . '">' . esc_html( kaitori_t( 'Select image', '画像を選択' ) ) . '</button>';
                        echo ' <button class="button kaitori-media-clear" data-target="ss_' . esc_attr( $key ) . '">' . esc_html( kaitori_t( 'Clear', 'クリア' ) ) . '</button>';
                        echo '<br><img src="' . esc_url( $val ) . '" class="kaitori-media-preview" style="max-width:240px;margin-top:8px;border:1px solid #e2e8f0;border-radius:6px;' . ( $val ? '' : 'display:none;' ) . '">';
                    }
                    echo '</td></tr>';
                }
                ?>
            </table>
            <p><button type="submit" name="kaitori_sidebar_submit" value="1" class="button button-primary"><?php echo esc_html( kaitori_t( 'Save Settings', '設定を保存' ) ); ?></button></p>
        </form>
    </div>
    <?php
}

/* ===================== Per-term logo image field ===================== */

/** The taxonomies that get a sidebar logo field. */
function kaitoriakiba_sidebar_logo_taxonomies() {
    return array( 'carrier', 'brand', 'series' );
}

/** URL of a term's uploaded logo (empty when none). */
function kaitoriakiba_term_image_url( $term ) {
    $tid = is_object( $term ) ? (int) $term->term_id : (int) $term;
    if ( ! $tid ) {
        return '';
    }
    $url = get_term_meta( $tid, '_kaitori_term_image', true );
    return $url ? $url : '';
}

/** Add-term screen: logo field. */
function kaitoriakiba_term_image_add_field() {
    ?>
    <div class="form-field kaitori-media-field">
        <label for="kaitori_term_image"><?php echo esc_html( kaitori_t( 'Sidebar Logo Image', 'サイドバー用ロゴ画像' ) ); ?></label>
        <input type="text" name="kaitori_term_image" id="kaitori_term_image" value="">
        <button class="button kaitori-media-pick" data-target="kaitori_term_image"><?php echo esc_html( kaitori_t( 'Select image', '画像を選択' ) ); ?></button>
        <button class="button kaitori-media-clear" data-target="kaitori_term_image"><?php echo esc_html( kaitori_t( 'Clear', 'クリア' ) ); ?></button>
        <p class="description"><?php echo esc_html( kaitori_t( 'Shown in the sidebar list for this item.', 'サイドバーの一覧でこの項目のロゴとして表示されます。' ) ); ?></p>
    </div>
    <?php
}

/** Edit-term screen: logo field. */
function kaitoriakiba_term_image_edit_field( $term ) {
    $val = kaitoriakiba_term_image_url( $term );
    ?>
    <tr class="form-field kaitori-media-field">
        <th scope="row"><label for="kaitori_term_image"><?php echo esc_html( kaitori_t( 'Sidebar Logo Image', 'サイドバー用ロゴ画像' ) ); ?></label></th>
        <td>
            <input type="text" name="kaitori_term_image" id="kaitori_term_image" value="<?php echo esc_attr( $val ); ?>" style="width:60%;">
            <button class="button kaitori-media-pick" data-target="kaitori_term_image"><?php echo esc_html( kaitori_t( 'Select image', '画像を選択' ) ); ?></button>
            <button class="button kaitori-media-clear" data-target="kaitori_term_image"><?php echo esc_html( kaitori_t( 'Clear', 'クリア' ) ); ?></button>
            <br><img src="<?php echo esc_url( $val ); ?>" class="kaitori-media-preview" style="max-width:140px;margin-top:8px;border:1px solid #e2e8f0;border-radius:6px;<?php echo $val ? '' : 'display:none;'; ?>">
            <p class="description"><?php echo esc_html( kaitori_t( 'Shown in the sidebar list for this item.', 'サイドバーの一覧でこの項目のロゴとして表示されます。' ) ); ?></p>
        </td>
    </tr>
    <?php
}

/** Save the term logo. */
function kaitoriakiba_term_image_save( $term_id ) {
    if ( ! current_user_can( 'manage_categories' ) ) {
        return;
    }
    if ( isset( $_POST['kaitori_term_image'] ) ) {
        update_term_meta( $term_id, '_kaitori_term_image', esc_url_raw( wp_unslash( $_POST['kaitori_term_image'] ) ) );
    }
}

/** Wire the logo field onto each sidebar taxonomy. */
function kaitoriakiba_register_term_image_fields() {
    foreach ( kaitoriakiba_sidebar_logo_taxonomies() as $tx ) {
        add_action( "{$tx}_add_form_fields", 'kaitoriakiba_term_image_add_field' );
        add_action( "{$tx}_edit_form_fields", 'kaitoriakiba_term_image_edit_field' );
        add_action( "created_{$tx}", 'kaitoriakiba_term_image_save' );
        add_action( "edited_{$tx}", 'kaitoriakiba_term_image_save' );
    }
}
add_action( 'admin_init', 'kaitoriakiba_register_term_image_fields' );

/* ===================== Admin assets (media picker) ===================== */

function kaitoriakiba_sidebar_admin_assets( $hook ) {
    $load   = ( 'appearance_page_kaitori-sidebar' === $hook );
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( $screen && ! empty( $screen->taxonomy ) && in_array( $screen->taxonomy, kaitoriakiba_sidebar_logo_taxonomies(), true ) ) {
        $load = true;
    }
    if ( ! $load ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script( 'kaitori-media-picker', KAITORIAKIBA_URI . '/assets/js/admin-media-picker.js', array( 'jquery' ), KAITORIAKIBA_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'kaitoriakiba_sidebar_admin_assets' );

/* ===================== Sidebar tax-box renderer ===================== */

/**
 * Render a carrier/brand/series logo box for the sidebar from taxonomy terms.
 * Uses the per-term uploaded logo, else a legacy /upload/{folder}/{slug} image,
 * else a text chip. Falls back to a provided list if the taxonomy has no terms.
 *
 * @param string $taxonomy
 * @param string $param     URL query key on /devices/ (e.g. 's_career').
 * @param string $folder    Legacy image folder under /upload/.
 * @param string $title     Already-translated heading.
 * @param array  $fallback  slug => relative image path (used when no terms exist).
 */
function kaitoriakiba_render_sidebar_tax_box( $taxonomy, $param, $folder, $title, $fallback = array() ) {
    $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
    $items = array();

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $t ) {
            $img = kaitoriakiba_term_image_url( $t );
            if ( ! $img ) {
                foreach ( array( 'jpg', 'png' ) as $ext ) {
                    $file = KAITORIAKIBA_DIR . '/upload/' . $folder . '/' . $t->slug . '.' . $ext;
                    if ( file_exists( $file ) ) {
                        $img = KAITORIAKIBA_URI . '/upload/' . $folder . '/' . $t->slug . '.' . $ext;
                        break;
                    }
                }
            }
            $items[] = array( 'name' => $t->name, 'slug' => $t->slug, 'img' => $img );
        }
    } else {
        foreach ( $fallback as $slug => $path ) {
            $file = KAITORIAKIBA_DIR . '/' . $path;
            $items[] = array(
                'name' => $slug,
                'slug' => $slug,
                'img'  => file_exists( $file ) ? KAITORIAKIBA_URI . '/' . $path : '',
            );
        }
    }

    if ( empty( $items ) ) {
        return;
    }
    ?>
    <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
        <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>" style="color:#ffffff; text-decoration:none;"><i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i><span><?php echo esc_html( $title ); ?></span></a>
        </div>
        <ul style="list-style:none; padding:8px; margin:0;">
            <?php foreach ( $items as $it ) : ?>
                <?php $url = esc_url( home_url( '/devices/?' . $param . '=' . urlencode( $it['slug'] ) ) ); ?>
                <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                    <?php if ( $it['img'] ) : ?>
                        <a href="<?php echo $url; ?>" style="display:block;">
                            <img src="<?php echo esc_url( $it['img'] ); ?>" alt="<?php echo esc_attr( $it['name'] ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0;">
                        </a>
                    <?php else : ?>
                        <a href="<?php echo $url; ?>" style="display:block; padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; background:#f8fafc; color:#1e293b; font-weight:700; font-size:13px; text-decoration:none; text-align:center;">
                            <?php echo esc_html( $it['name'] ); ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <?php
}
