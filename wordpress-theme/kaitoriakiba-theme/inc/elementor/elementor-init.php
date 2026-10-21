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
    // Determine widgets manager instance
    if ( ! $widgets_manager ) {
        if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->widgets_manager ) ) {
            $widgets_manager = \Elementor\Plugin::$instance->widgets_manager;
        } else {
            return;
        }
    }

    // Include widget files safely
    require_once __DIR__ . '/widgets/widget-search-box.php';
    require_once __DIR__ . '/widgets/widget-device-grid.php';
    require_once __DIR__ . '/widgets/widget-device-single.php';
    require_once __DIR__ . '/widgets/widget-category-sidebar.php';
    require_once __DIR__ . '/widgets/widget-process-flow.php';
    require_once __DIR__ . '/widgets/widget-faq.php';
    require_once __DIR__ . '/widgets/widget-announcement-banner.php';
    require_once __DIR__ . '/widgets/widget-sell-apply.php';
    require_once __DIR__ . '/widgets/widget-kyc-verify.php';
    require_once __DIR__ . '/widgets/widget-auth-form.php';

    $widget_classes = array(
        'Kaitori_Search_Widget',
        'Kaitori_Device_Grid_Widget',
        'Kaitori_Device_Single_Widget',
        'Kaitori_Taxonomy_Browse_Widget',
        'Kaitori_Process_Flow_Widget',
        'Kaitori_FAQ_Widget',
        'Kaitori_Announcement_Widget',
        'Kaitori_Sell_Apply_Widget',
        'Kaitori_KYC_Verify_Widget',
        'Kaitori_Auth_Form_Widget',
    );

    foreach ( $widget_classes as $class_name ) {
        if ( class_exists( $class_name ) ) {
            $widget_instance = new $class_name();
            if ( method_exists( $widgets_manager, 'register' ) ) {
                $widgets_manager->register( $widget_instance );
            } elseif ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
                $widgets_manager->register_widget_type( $widget_instance );
            }
        }
    }
}
add_action( 'elementor/widgets/register', 'kaitoriakiba_register_elementor_widgets' );
add_action( 'elementor/widgets/widgets_registered', 'kaitoriakiba_register_elementor_widgets' );
