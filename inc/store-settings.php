<?php
/**
 * Kaitori Akiba — Store & Footer Settings
 *
 * Makes the store information and footer texts (shop name, address, hours,
 * holiday, access, phone, antique-dealer license, footer intro, footer column
 * headings and the footer legal notice) editable from wp-admin, instead of
 * being hard-coded in the theme. Values are stored in one option
 * ("kaitori_store_settings"); an empty field falls back to the theme default,
 * so nothing breaks if the page is never touched.
 *
 * Consumed by:
 *   - kaitoriakiba_store_info()  (functions.php) → Access page + LocalBusiness SEO schema
 *   - footer.php                 (brand intro, address, hours, headings, legal notice)
 *
 * Admin: Appearance → Store & Footer.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get a single store/footer setting, falling back to a default when the admin
 * has left the field blank.
 *
 * @param string $key     Field key.
 * @param string $default Value to use when the admin field is empty.
 * @return string
 */
function kaitoriakiba_store_setting( $key, $default = '' ) {
    $opts = get_option( 'kaitori_store_settings', array() );
    if ( is_array( $opts ) && isset( $opts[ $key ] ) && '' !== trim( (string) $opts[ $key ] ) ) {
        return $opts[ $key ];
    }
    return $default;
}

/**
 * The editable fields: key => [label, type('text'|'textarea'), placeholder(JA default)].
 * The placeholder shows the current theme default so staff can see what blank means.
 *
 * @return array<string,array>
 */
function kaitoriakiba_store_settings_fields() {
    return array(
        // --- Store information (also used by the Access page & SEO schema) ---
        'store_name' => array( 'label' => kaitori_t( 'Store Name', '店舗名' ), 'type' => 'text', 'ph' => 'Kaitori Akiba 秋葉原総本店' ),
        'address'    => array( 'label' => kaitori_t( 'Address', '住所' ), 'type' => 'text', 'ph' => '東京都千代田区外神田1丁目（秋葉原駅 徒歩2分）' ),
        'hours'      => array( 'label' => kaitori_t( 'Business Hours', '営業時間' ), 'type' => 'text', 'ph' => '10:00 〜 19:30' ),
        'holiday'    => array( 'label' => kaitori_t( 'Holidays', '定休日' ), 'type' => 'text', 'ph' => '年中無休' ),
        'access'     => array( 'label' => kaitori_t( 'Access', 'アクセス' ), 'type' => 'text', 'ph' => 'JR秋葉原駅 電気街口より徒歩2分' ),
        'phone'      => array( 'label' => kaitori_t( 'Phone', '電話番号' ), 'type' => 'text', 'ph' => '03-xxxx-xxxx' ),
        'twitter_url'=> array( 'label' => kaitori_t( 'Twitter / X URL', 'Twitter / X のURL' ), 'type' => 'text', 'ph' => 'https://twitter.com/kaitoriakiba' ),
        'line_url'   => array( 'label' => kaitori_t( 'LINE URL', 'LINEのURL' ), 'type' => 'text', 'ph' => 'https://lin.ee/5wvtHzl' ),
        'license'    => array( 'label' => kaitori_t( 'Antique Dealer License No.', '古物商許可番号' ), 'type' => 'text', 'ph' => '東京都公安委員会許可 第301031200000号' ),
        // --- Footer-specific texts ---
        'footer_desc'       => array( 'label' => kaitori_t( 'Footer Intro Text', 'フッター紹介文' ), 'type' => 'textarea', 'ph' => '秋葉原駅徒歩2分。iPhone、Android、iPad、MacBookなどの高価買取専門店。' ),
        'footer_h_services' => array( 'label' => kaitori_t( 'Footer Heading — Services column', 'フッター見出し — サービス列' ), 'type' => 'text', 'ph' => 'サービス案内' ),
        'footer_h_guide'    => array( 'label' => kaitori_t( 'Footer Heading — Guide column', 'フッター見出し — ご利用案内列' ), 'type' => 'text', 'ph' => 'ご利用案内' ),
        'footer_h_legal'    => array( 'label' => kaitori_t( 'Footer Heading — Legal column', 'フッター見出し — 法令表記列' ), 'type' => 'text', 'ph' => '古物営業法に基づく表記' ),
        'footer_legal'      => array( 'label' => kaitori_t( 'Footer Legal Notice (HTML allowed)', 'フッター法令表記（HTML可）' ), 'type' => 'textarea', 'ph' => '東京都公安委員会許可 第301031200000号' ),
    );
}

/**
 * Canonical social-profile URL (Twitter/X or LINE), shared by the header,
 * footer, homepage and sidebar so every social link is set in one place
 * (Appearance → Store & Footer).
 *
 * @param string $net     'twitter' | 'line'
 * @param string $default
 * @return string
 */
function kaitoriakiba_social_url( $net, $default = '' ) {
    $key = ( 'line' === $net ) ? 'line_url' : 'twitter_url';
    return kaitoriakiba_store_setting( $key, $default );
}

/** Register the page under Appearance. */
function kaitoriakiba_store_settings_menu() {
    add_theme_page(
        kaitori_t( 'Store & Footer Settings', '店舗・フッター設定' ),
        kaitori_t( 'Store & Footer', '店舗・フッター' ),
        'manage_options',
        'kaitori-store-info',
        'kaitoriakiba_store_settings_page'
    );
}
add_action( 'admin_menu', 'kaitoriakiba_store_settings_menu' );

/** Render + save the settings. */
function kaitoriakiba_store_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html( kaitori_t( 'Permission denied.', '権限がありません。' ) ) );
    }

    $fields = kaitoriakiba_store_settings_fields();
    $saved  = false;

    if ( isset( $_POST['kaitori_store_settings_submit'] ) && check_admin_referer( 'kaitori_store_settings' ) ) {
        $clean = array();
        foreach ( $fields as $key => $f ) {
            $raw = isset( $_POST[ 'kss_' . $key ] ) ? wp_unslash( $_POST[ 'kss_' . $key ] ) : '';
            // Legal notice / intro allow basic HTML (e.g. <br>); everything else plain text.
            $clean[ $key ] = ( 'footer_legal' === $key || 'footer_desc' === $key )
                ? wp_kses_post( $raw )
                : sanitize_text_field( $raw );
        }
        update_option( 'kaitori_store_settings', $clean );
        $saved = true;
    }

    $opts = get_option( 'kaitori_store_settings', array() );
    if ( ! is_array( $opts ) ) {
        $opts = array();
    }
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-store" style="font-size:26px;"></span> <?php echo esc_html( kaitori_t( 'Store &amp; Footer Settings', '店舗・フッター設定' ) ); ?></h1>
        <?php if ( $saved ) : ?><div class="notice notice-success is-dismissible"><p><strong><?php echo esc_html( kaitori_t( 'Saved.', '保存しました。' ) ); ?></strong></p></div><?php endif; ?>
        <p style="color:#475569;max-width:760px;">
            <?php echo esc_html( kaitori_t( 'Edit the store information and footer texts here. Leave a field blank to use the theme default (shown as placeholder). These values appear in the footer, the Access page, and the site\'s LocalBusiness SEO data.', '店舗情報とフッターの文言をここで編集できます。空欄にするとテーマの初期値（プレースホルダー表示）が使われます。これらの値はフッター・アクセスページ・SEO（LocalBusiness）情報に反映されます。' ) ); ?>
        </p>
        <form method="post" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;max-width:820px;margin-top:14px;">
            <?php wp_nonce_field( 'kaitori_store_settings' ); ?>
            <table class="form-table">
                <?php foreach ( $fields as $key => $f ) : ?>
                    <?php $val = isset( $opts[ $key ] ) ? $opts[ $key ] : ''; ?>
                    <tr>
                        <th scope="row"><label for="kss_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $f['label'] ); ?></label></th>
                        <td>
                            <?php if ( 'textarea' === $f['type'] ) : ?>
                                <textarea name="kss_<?php echo esc_attr( $key ); ?>" id="kss_<?php echo esc_attr( $key ); ?>" rows="3" class="large-text" placeholder="<?php echo esc_attr( $f['ph'] ); ?>"><?php echo esc_textarea( $val ); ?></textarea>
                            <?php else : ?>
                                <input type="text" name="kss_<?php echo esc_attr( $key ); ?>" id="kss_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text" style="width:100%;max-width:560px;" placeholder="<?php echo esc_attr( $f['ph'] ); ?>">
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p><button type="submit" name="kaitori_store_settings_submit" value="1" class="button button-primary"><?php echo esc_html( kaitori_t( 'Save Settings', '設定を保存' ) ); ?></button></p>
        </form>
    </div>
    <?php
}
