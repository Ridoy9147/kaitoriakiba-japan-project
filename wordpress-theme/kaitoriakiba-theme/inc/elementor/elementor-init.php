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
    $elements_manager->add_category(
        'kaitoriakiba-elements',
        array(
            'title' => __( 'Kaitori Akiba Elements (買取アキバ)', 'kaitoriakiba' ),
            'icon'  => 'fa fa-plug',
        )
    );
}
add_action( 'elementor/elements/categories_registered', 'kaitoriakiba_elementor_category' );

/**
 * Register Custom Elementor Widgets
 */
function kaitoriakiba_register_elementor_widgets( $widgets_manager ) {
    // Include widget files
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

    // Register widget instances
    $widgets_manager->register( new \Kaitori_Search_Widget() );
    $widgets_manager->register( new \Kaitori_Device_Grid_Widget() );
    $widgets_manager->register( new \Kaitori_Device_Single_Widget() );
    $widgets_manager->register( new \Kaitori_Taxonomy_Browse_Widget() );
    $widgets_manager->register( new \Kaitori_Process_Flow_Widget() );
    $widgets_manager->register( new \Kaitori_FAQ_Widget() );
    $widgets_manager->register( new \Kaitori_Announcement_Widget() );
    $widgets_manager->register( new \Kaitori_Sell_Apply_Widget() );
    $widgets_manager->register( new \Kaitori_KYC_Verify_Widget() );
    $widgets_manager->register( new \Kaitori_Auth_Form_Widget() );
}
add_action( 'elementor/widgets/register', 'kaitoriakiba_register_elementor_widgets' );
