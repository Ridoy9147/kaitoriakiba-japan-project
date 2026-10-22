<?php
/**
 * TGM Plugin Activation configuration for Kaitori Akiba Theme
 * 
 * Prompts user to install & activate required plugins (Elementor) upon theme activation.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once KAITORIAKIBA_DIR . '/inc/tgmpa/class-tgm-plugin-activation.php';

/**
 * Register required plugins with TGMPA
 */
function kaitoriakiba_register_required_plugins() {
    $plugins = array(
        array(
            'name'      => __( 'Elementor Website Builder', 'kaitoriakiba' ),
            'slug'      => 'elementor',
            'required'  => true,
            'version'   => '3.0.0',
            'force_activation'   => false,
            'force_deactivation' => false,
        ),
    );

    $config = array(
        'id'           => 'kaitoriakiba-tgmpa',
        'default_path' => '',
        'menu'         => 'tgmpa-install-plugins',
        'parent_slug'  => 'themes.php',
        'capability'   => 'edit_theme_options',
        'has_notices'  => true,
        'dismissable'  => false,
        'dismiss_msg'  => '',
        'is_automatic' => true,
        'message'      => '',
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins - Kaitori Akiba', 'kaitoriakiba' ),
            'menu_title'                      => __( 'Install Plugins', 'kaitoriakiba' ),
            'installing'                      => __( 'Installing Plugin: %s', 'kaitoriakiba' ),
            'updating'                        => __( 'Updating Plugin: %s', 'kaitoriakiba' ),
            'oops'                            => __( 'Something went wrong with the plugin API.', 'kaitoriakiba' ),
            'notice_can_install_required'     => _n_noop(
                'Kaitori Akibaテーマの機能を完全に利用するには、以下のプラグインのインストールが必要です: %1$s。',
                'Kaitori Akibaテーマの機能を完全に利用するには、以下のプラグインのインストールが必要です: %1$s。',
                'kaitoriakiba'
            ),
            'notice_can_activate_required'    => _n_noop(
                '以下の必須プラグインが現在無効化されています。有効化してください: %1$s。',
                '以下の必須プラグインが現在無効化されています。有効化してください: %1$s。',
                'kaitoriakiba'
            ),
            'notice_ask_to_update'            => _n_noop(
                'プラグインの最新バージョンへのアップデートが推奨されます: %1$s。',
                'プラグインの最新バージョンへのアップデートが推奨されます: %1$s。',
                'kaitoriakiba'
            ),
            'install_link'                    => _n_noop(
                'プラグインのインストールを開始 (Begin installing plugin)',
                'プラグインのインストールを開始 (Begin installing plugins)',
                'kaitoriakiba'
            ),
            'activate_link'                   => _n_noop(
                'プラグインを有効化 (Activate plugin)',
                'プラグインを有効化 (Activate plugins)',
                'kaitoriakiba'
            ),
            'return'                          => __( '必須プラグインのインストーラーへ戻る', 'kaitoriakiba' ),
            'plugin_activated'                => __( 'プラグインが正常に有効化されました。', 'kaitoriakiba' ),
            'activated_successfully'          => __( '以下のプラグインが有効化されました:', 'kaitoriakiba' ),
            'complete'                        => __( 'すべての必須プラグインのインストールと有効化が完了しました。%1$s', 'kaitoriakiba' ),
        ),
    );

    tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'kaitoriakiba_register_required_plugins' );
