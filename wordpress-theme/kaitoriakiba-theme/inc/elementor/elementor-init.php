<?php
/**
 * Kaitori Akiba Elementor Integration
 * Registers custom Elementor category and widgets.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Elementor Widget Category
 */
function kaitoriakiba_elementor_category( $elements_manager ) {
    if ( method_exists( $elements_manager, 'add_category' ) ) {
        $elements_manager->add_category(
            'kaitoriakiba-elements',
            array(
                'title' => __( 'Kaitori Akiba Elements (買取アキバ)', 'kaitoriakiba' ),
                'icon'  => 'fa fa-plug',
            )
        );
    }
}
add_action( 'elementor/elements/categories_registered', 'kaitoriakiba_elementor_category' );

/**
 * Register Custom Elementor Widgets
 */
function kaitoriakiba_register_elementor_widgets( $widgets_manager = null ) {
    static $already_registered = false;
    if ( $already_registered ) {
        return;
    }
    $already_registered = true;

    // Determine widgets manager instance safely
    if ( ! $widgets_manager ) {
        if ( class_exists( '\Elementor\Plugin' ) && method_exists( '\Elementor\Plugin', 'instance' ) ) {
            $plugin = \Elementor\Plugin::instance();
            if ( isset( $plugin->widgets_manager ) ) {
                $widgets_manager = $plugin->widgets_manager;
            }
        }
    }

    if ( ! $widgets_manager ) {
        return;
    }

    // Include widget files safely
    $widgets_to_load = array(
        'widget-search-box.php'           => 'Kaitori_Search_Widget',
        'widget-process-flow.php'         => 'Kaitori_Process_Flow_Widget',
        'widget-announcement-banner.php'  => 'Kaitori_Announcement_Widget',
        'widget-hero-banner.php'          => 'Kaitori_Hero_Banner_Widget',
        'widget-sidebar-ad.php'           => 'Kaitori_Sidebar_Ad_Widget',
        'widget-category-sidebar.php'     => 'Kaitori_Taxonomy_Browse_Widget',
        'widget-sidebar-newsletter.php'   => 'Kaitori_Sidebar_Newsletter_Widget',
        'widget-news-box.php'             => 'Kaitori_News_Widget',
        'widget-device-grid.php'          => 'Kaitori_Device_Grid_Widget',
        'widget-checkpoints.php'          => 'Kaitori_Checkpoints_Widget',
        'widget-device-single.php'        => 'Kaitori_Device_Single_Widget',
        'widget-sell-apply.php'           => 'Kaitori_Sell_Apply_Widget',
        'widget-kyc-verify.php'           => 'Kaitori_KYC_Verify_Widget',
        'widget-auth-form.php'            => 'Kaitori_Auth_Form_Widget',
        'widget-faq.php'                  => 'Kaitori_FAQ_Widget',
    );

    foreach ( $widgets_to_load as $file => $class_name ) {
        $file_path = __DIR__ . '/widgets/' . $file;
        if ( file_exists( $file_path ) ) {
            require_once $file_path;
            if ( class_exists( $class_name ) ) {
                try {
                    $widget_instance = new $class_name();
                    if ( method_exists( $widgets_manager, 'register' ) ) {
                        $widgets_manager->register( $widget_instance );
                    } elseif ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
                        $widgets_manager->register_widget_type( $widget_instance );
                    }
                } catch ( \Throwable $e ) {
                    // Fail silently to avoid breaking entire page load
                }
            }
        }
    }
}
add_action( 'elementor/widgets/register', 'kaitoriakiba_register_elementor_widgets' );
add_action( 'elementor/widgets/widgets_registered', 'kaitoriakiba_register_elementor_widgets' );
