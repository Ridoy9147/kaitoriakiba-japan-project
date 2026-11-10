<?php
/**
 * Elementor Custom Loop Carousel Widget
 *
 * Allows creating versatile, infinite loop carousels with unlimited slides.
 * Each slide supports custom background images, overlays, links, and can render
 * ANY Elementor Saved Template (with any modules/widgets) or custom headings/buttons.
 *
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Loop_Carousel_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_loop_carousel';
    }

    public function get_title() {
        return __( 'Loop Carousel & Slider (ループカルーセル)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-slider-album';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    public function get_keywords() {
        return array( 'carousel', 'slider', 'loop', 'swiper', 'banner', 'template', 'slides' );
    }

    /**
     * Retrieve list of saved Elementor templates for dropdown.
     */
    private function get_elementor_templates() {
        $options = array( '' => __( '— Select an Elementor Saved Template —', 'kaitoriakiba' ) );
        
        $templates = get_posts( array(
            'post_type'   => 'elementor_library',
            'numberposts' => -1,
            'post_status' => 'publish',
            'orderby'     => 'title',
            'order'       => 'ASC',
        ) );

        if ( ! empty( $templates ) && ! is_wp_error( $templates ) ) {
            foreach ( $templates as $t ) {
                $options[ (string) $t->ID ] = $t->post_title . ' (ID: ' . $t->ID . ')';
            }
        }

        return $options;
    }

    protected function register_controls() {

        // =========================================================================
        // 1. SECTION: SLIDES (REPEATER)
        // =========================================================================
        $this->start_controls_section(
            'section_slides',
            array(
                'label' => __( 'Slides (スライド一覧)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'template_hint',
            array(
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw'  => __( '<div style="background:#eff6ff; border:1px solid #bfdbfe; border-left:4px solid #3b82f6; border-radius:6px; padding:10px 12px; font-size:12px; color:#1e40af; line-height:1.5;"><strong>💡 Pro Tip:</strong> You can place <strong>ANY Elementor widget or module</strong> inside a slide! Create a section/container in <strong>Templates → Saved Templates</strong>, then select it in the slide below.</div>', 'kaitoriakiba' ),
            )
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'slide_title',
            array(
                'label'       => __( 'Slide Title / Admin Label', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'Slide Item', 'kaitoriakiba' ),
                'label_block' => true,
            )
        );

        $repeater->add_control(
            'content_type',
            array(
                'label'   => __( 'Slide Content Type', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => array(
                    'template' => __( 'Elementor Saved Template (Any Module / Widget)', 'kaitoriakiba' ),
                    'custom'   => __( 'Custom Content (Title, Description & Button)', 'kaitoriakiba' ),
                ),
            )
        );

        // --- Template Option ---
        $repeater->add_control(
            'template_id',
            array(
                'label'       => __( 'Select Saved Template', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'default'     => '',
                'options'     => $this->get_elementor_templates(),
                'condition'   => array(
                    'content_type' => 'template',
                ),
                'description' => __( 'Choose any section or container template designed in Elementor.', 'kaitoriakiba' ),
            )
        );

        // --- Custom Content Options ---
        $repeater->add_control(
            'custom_subtitle',
            array(
                'label'       => __( 'Subtitle / Eyebrow', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'Special Promotion', 'kaitoriakiba' ),
                'condition'   => array(
                    'content_type' => 'custom',
                ),
                'label_block' => true,
            )
        );

        $repeater->add_control(
            'custom_title',
            array(
                'label'       => __( 'Heading / Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'Smartphone High Buyback Rate', 'kaitoriakiba' ),
                'condition'   => array(
                    'content_type' => 'custom',
                ),
                'label_block' => true,
            )
        );

        $repeater->add_control(
            'custom_description',
            array(
                'label'       => __( 'Description Text', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'rows'        => 3,
                'default'     => __( 'Get the highest buyback price in Akihabara with fast same-day payment and free shipping.', 'kaitoriakiba' ),
                'condition'   => array(
                    'content_type' => 'custom',
                ),
            )
        );

        $repeater->add_control(
            'custom_btn_text',
            array(
                'label'       => __( 'Button Text', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'View Buyback Prices →', 'kaitoriakiba' ),
                'condition'   => array(
                    'content_type' => 'custom',
                ),
            )
        );

        $repeater->add_control(
            'custom_btn_link',
            array(
                'label'       => __( 'Button URL', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://example.com/apply', 'kaitoriakiba' ),
                'default'     => array(
                    'url' => '#',
                ),
                'condition'   => array(
                    'content_type' => 'custom',
                ),
            )
        );

        // --- Slide Link Option ---
        $repeater->add_control(
            'slide_link_type',
            array(
                'label'   => __( 'Slide Click Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => array(
                    'none'   => __( 'None', 'kaitoriakiba' ),
                    'entire' => __( 'Whole Slide is Clickable', 'kaitoriakiba' ),
                ),
                'separator' => 'before',
            )
        );

        $repeater->add_control(
            'slide_url',
            array(
                'label'       => __( 'Slide Link URL', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://example.com', 'kaitoriakiba' ),
                'condition'   => array(
                    'slide_link_type' => 'entire',
                ),
            )
        );

        // --- Background Settings for each slide ---
        $repeater->add_control(
            'bg_heading',
            array(
                'label'     => __( 'Background Image & Overlay (背景画像・オーバーレイ)', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $repeater->add_control(
            'bg_image',
            array(
                'label'   => __( 'Slide Background Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ),
            )
        );

        $repeater->add_control(
            'bg_position',
            array(
                'label'   => __( 'Background Position', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'center center',
                'options' => array(
                    'top left'      => __( 'Top Left', 'kaitoriakiba' ),
                    'top center'    => __( 'Top Center', 'kaitoriakiba' ),
                    'top right'     => __( 'Top Right', 'kaitoriakiba' ),
                    'center left'   => __( 'Center Left', 'kaitoriakiba' ),
                    'center center' => __( 'Center Center', 'kaitoriakiba' ),
                    'center right'  => __( 'Center Right', 'kaitoriakiba' ),
                    'bottom left'   => __( 'Bottom Left', 'kaitoriakiba' ),
                    'bottom center' => __( 'Bottom Center', 'kaitoriakiba' ),
                    'bottom right'  => __( 'Bottom Right', 'kaitoriakiba' ),
                ),
            )
        );

        $repeater->add_control(
            'bg_size',
            array(
                'label'   => __( 'Background Size', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => array(
                    'cover'   => __( 'Cover (Full Width/Height)', 'kaitoriakiba' ),
                    'contain' => __( 'Contain (Fit inside)', 'kaitoriakiba' ),
                    'auto'    => __( 'Auto', 'kaitoriakiba' ),
                ),
            )
        );

        $repeater->add_control(
            'bg_repeat',
            array(
                'label'   => __( 'Background Repeat', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'no-repeat',
                'options' => array(
                    'no-repeat' => __( 'No-repeat', 'kaitoriakiba' ),
                    'repeat'    => __( 'Repeat', 'kaitoriakiba' ),
                ),
            )
        );

        $repeater->add_control(
            'bg_color',
            array(
                'label'     => __( 'Background Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#0f172a',
            )
        );

        $repeater->add_control(
            'overlay_enable',
            array(
                'label'        => __( 'Enable Color Overlay', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            )
        );

        $repeater->add_control(
            'overlay_color',
            array(
                'label'     => __( 'Overlay Color & Opacity', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(15, 23, 42, 0.45)',
                'condition' => array(
                    'overlay_enable' => 'yes',
                ),
            )
        );

        $this->add_control(
            'slides',
            array(
                'label'       => __( 'Slide Items', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ slide_title }}}',
                'default'     => array(
                    array(
                        'slide_title'        => __( 'Slide 1 — High Rate Smartphone Buyback', 'kaitoriakiba' ),
                        'content_type'       => 'custom',
                        'custom_subtitle'    => __( 'Akihabara Store Best Pricing', 'kaitoriakiba' ),
                        'custom_title'       => __( 'iPhone 16 / 15 Pro Max Enhanced Buyback Rates', 'kaitoriakiba' ),
                        'custom_description' => __( 'Up to ¥240,000 for brand-new sealed devices. Fast assessment & instant payout.', 'kaitoriakiba' ),
                        'custom_btn_text'    => __( 'Check Device Prices →', 'kaitoriakiba' ),
                        'bg_color'           => '#0f172a',
                        'overlay_enable'     => 'yes',
                        'overlay_color'      => 'rgba(15, 23, 42, 0.55)',
                    ),
                    array(
                        'slide_title'        => __( 'Slide 2 — Fast Same-Day Online eKYC', 'kaitoriakiba' ),
                        'content_type'       => 'custom',
                        'custom_subtitle'    => __( '100% Online Verification', 'kaitoriakiba' ),
                        'custom_title'       => __( 'Fast Second-hand Verification with Japanese eKYC', 'kaitoriakiba' ),
                        'custom_description' => __( 'Upload your driver license or My Number Card in 2 minutes. Free shipping kit delivered to your door.', 'kaitoriakiba' ),
                        'custom_btn_text'    => __( 'Start Online Application →', 'kaitoriakiba' ),
                        'bg_color'           => '#1e1b4b',
                        'overlay_enable'     => 'yes',
                        'overlay_color'      => 'rgba(30, 27, 75, 0.55)',
                    ),
                ),
            )
        );

        $this->end_controls_section();


        // =========================================================================
        // 2. SECTION: CAROUSEL SETTINGS (カルーセル設定)
        // =========================================================================
        $this->start_controls_section(
            'section_carousel_settings',
            array(
                'label' => __( 'Carousel & Loop Settings (カルーセル・ループ設定)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_responsive_control(
            'slides_to_show',
            array(
                'label'          => __( 'Slides to Show (表示スライド数)', 'kaitoriakiba' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '1',
                'tablet_default' => '1',
                'mobile_default' => '1',
                'options'        => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ),
            )
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            array(
                'label'          => __( 'Slides to Scroll (スクロール数)', 'kaitoriakiba' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '1',
                'tablet_default' => '1',
                'mobile_default' => '1',
                'options'        => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ),
            )
        );

        $this->add_responsive_control(
            'space_between',
            array(
                'label'      => __( 'Space Between Slides (px)', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min'  => 0,
                        'max'  => 60,
                        'step' => 1,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 20,
                ),
            )
        );

        $this->add_control(
            'infinite_loop',
            array(
                'label'        => __( 'Infinite Loop (無限ループ)', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
                'description'  => __( 'Seamless continuous carousel loop. Never stops at the last slide.', 'kaitoriakiba' ),
            )
        );

        $this->add_control(
            'autoplay',
            array(
                'label'        => __( 'Autoplay (自動再生)', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            )
        );

        $this->add_control(
            'autoplay_delay',
            array(
                'label'     => __( 'Autoplay Delay (ms)', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 4000,
                'min'       => 1000,
                'max'       => 20000,
                'step'      => 500,
                'condition' => array(
                    'autoplay' => 'yes',
                ),
            )
        );

        $this->add_control(
            'pause_on_hover',
            array(
                'label'        => __( 'Pause on Hover (ホバー時一時停止)', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
                'condition'    => array(
                    'autoplay' => 'yes',
                ),
            )
        );

        $this->add_control(
            'transition_speed',
            array(
                'label'   => __( 'Animation Speed (ms)', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 600,
                'min'     => 200,
                'max'     => 3000,
                'step'    => 50,
            )
        );

        $this->add_control(
            'transition_effect',
            array(
                'label'   => __( 'Transition Effect', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => array(
                    'slide' => __( 'Slide (スライド)', 'kaitoriakiba' ),
                    'fade'  => __( 'Fade (フェード - 1 slide view only)', 'kaitoriakiba' ),
                ),
            )
        );

        $this->add_responsive_control(
            'min_height',
            array(
                'label'      => __( 'Slide Height (スライドの高さ)', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'vh' ),
                'range'      => array(
                    'px' => array(
                        'min'  => 180,
                        'max'  => 900,
                        'step' => 10,
                    ),
                    'vh' => array(
                        'min' => 20,
                        'max' => 100,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 420,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kaitori-carousel-slide-inner' => 'min-height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'content_valign',
            array(
                'label'     => __( 'Vertical Alignment', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::CHOOSE,
                'options'   => array(
                    'flex-start' => array(
                        'title' => __( 'Top', 'kaitoriakiba' ),
                        'icon'  => 'eicon-v-align-top',
                    ),
                    'center'     => array(
                        'title' => __( 'Middle', 'kaitoriakiba' ),
                        'icon'  => 'eicon-v-align-middle',
                    ),
                    'flex-end'   => array(
                        'title' => __( 'Bottom', 'kaitoriakiba' ),
                        'icon'  => 'eicon-v-align-bottom',
                    ),
                ),
                'default'   => 'center',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-slide-inner' => 'align-items: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'content_halign',
            array(
                'label'     => __( 'Horizontal Alignment', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::CHOOSE,
                'options'   => array(
                    'left'   => array(
                        'title' => __( 'Left', 'kaitoriakiba' ),
                        'icon'  => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => __( 'Center', 'kaitoriakiba' ),
                        'icon'  => 'eicon-text-align-center',
                    ),
                    'right'  => array(
                        'title' => __( 'Right', 'kaitoriakiba' ),
                        'icon'  => 'eicon-text-align-right',
                    ),
                ),
                'default'   => 'center',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-custom-content' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .kaitori-carousel-slide-inner'    => 'justify-content: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();


        // =========================================================================
        // 3. SECTION: NAVIGATION & PAGINATION CONTROLS
        // =========================================================================
        $this->start_controls_section(
            'section_nav_pagination',
            array(
                'label' => __( 'Navigation & Pagination (矢印・ドット)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_arrows',
            array(
                'label'        => __( 'Show Navigation Arrows', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            )
        );

        $this->add_control(
            'arrows_position',
            array(
                'label'     => __( 'Arrows Position', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'inside',
                'options'   => array(
                    'inside'  => __( 'Inside Slide', 'kaitoriakiba' ),
                    'outside' => __( 'Outside Slide', 'kaitoriakiba' ),
                ),
                'condition' => array(
                    'show_arrows' => 'yes',
                ),
            )
        );

        $this->add_control(
            'show_pagination',
            array(
                'label'        => __( 'Show Pagination (ドット表示)', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            )
        );

        $this->add_control(
            'pagination_type',
            array(
                'label'     => __( 'Pagination Type', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'bullets',
                'options'   => array(
                    'bullets'     => __( 'Bullets / Dots', 'kaitoriakiba' ),
                    'fraction'    => __( 'Fraction (1 / 5)', 'kaitoriakiba' ),
                    'progressbar' => __( 'Progress Bar', 'kaitoriakiba' ),
                ),
                'condition' => array(
                    'show_pagination' => 'yes',
                ),
            )
        );

        $this->end_controls_section();


        // =========================================================================
        // 4. STYLE: SLIDE CARDS (スライドカードのスタイル)
        // =========================================================================
        $this->start_controls_section(
            'section_style_slide',
            array(
                'label' => __( 'Slide Card Style (スライドカード)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'slide_border_radius',
            array(
                'label'      => __( 'Border Radius (角丸)', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'default'    => array(
                    'top'      => 12,
                    'right'    => 12,
                    'bottom'   => 12,
                    'left'     => 12,
                    'unit'     => 'px',
                    'isLinked' => true,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kaitori-carousel-slide-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .kaitori-carousel-slide'       => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'slide_box_shadow',
                'selector' => '{{WRAPPER}} .kaitori-carousel-slide-inner',
            )
        );

        $this->add_responsive_control(
            'slide_padding',
            array(
                'label'      => __( 'Content Padding (内側余白)', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'default'    => array(
                    'top'      => 40,
                    'right'    => 40,
                    'bottom'   => 40,
                    'left'     => 40,
                    'unit'     => 'px',
                    'isLinked' => true,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kaitori-carousel-slide-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();


        // =========================================================================
        // 5. STYLE: CUSTOM CONTENT (カスタムコンテンツ)
        // =========================================================================
        $this->start_controls_section(
            'section_style_custom_content',
            array(
                'label' => __( 'Custom Content Typography & Colors', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'subtitle_color',
            array(
                'label'     => __( 'Subtitle Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#38bdf8',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-subtitle' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'subtitle_typography',
                'selector' => '{{WRAPPER}} .kaitori-carousel-subtitle',
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => __( 'Title Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'separator' => 'before',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .kaitori-carousel-title',
            )
        );

        $this->add_control(
            'desc_color',
            array(
                'label'     => __( 'Description Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#e2e8f0',
                'separator' => 'before',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-desc' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'desc_typography',
                'selector' => '{{WRAPPER}} .kaitori-carousel-desc',
            )
        );

        // Button Style
        $this->add_control(
            'button_style_heading',
            array(
                'label'     => __( 'Button Style', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            array(
                'label' => __( 'Normal', 'kaitoriakiba' ),
            )
        );

        $this->add_control(
            'btn_text_color',
            array(
                'label'     => __( 'Text Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-btn' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'btn_bg_color',
            array(
                'label'     => __( 'Background Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ef4444',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            array(
                'label' => __( 'Hover', 'kaitoriakiba' ),
            )
        );

        $this->add_control(
            'btn_hover_text_color',
            array(
                'label'     => __( 'Text Color (Hover)', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-btn:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'btn_hover_bg_color',
            array(
                'label'     => __( 'Background Color (Hover)', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#dc2626',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-btn:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'btn_border_radius',
            array(
                'label'      => __( 'Button Border Radius', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'default'    => array(
                    'top'      => 8,
                    'right'    => 8,
                    'bottom'   => 8,
                    'left'     => 8,
                    'unit'     => 'px',
                    'isLinked' => true,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .kaitori-carousel-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();


        // =========================================================================
        // 6. STYLE: ARROWS & PAGINATION
        // =========================================================================
        $this->start_controls_section(
            'section_style_arrows_dots',
            array(
                'label' => __( 'Arrows & Pagination Style (矢印・ドットのスタイル)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'arrow_heading',
            array(
                'label' => __( 'Navigation Arrows (矢印)', 'kaitoriakiba' ),
                'type'  => \Elementor\Controls_Manager::HEADING,
            )
        );

        $this->add_control(
            'arrow_color',
            array(
                'label'     => __( 'Arrow Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-arrow' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'arrow_bg_color',
            array(
                'label'     => __( 'Arrow Background Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(15, 23, 42, 0.65)',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-arrow' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'arrow_hover_bg',
            array(
                'label'     => __( 'Arrow Hover Background', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ef4444',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-carousel-arrow:hover' => 'background-color: {{VALUE}}; color: #ffffff;',
                ),
            )
        );

        $this->add_control(
            'dots_heading',
            array(
                'label'     => __( 'Pagination Dots (ドット)', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_control(
            'dot_color',
            array(
                'label'     => __( 'Inactive Dot Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(255, 255, 255, 0.45)',
                'selectors' => array(
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'dot_active_color',
            array(
                'label'     => __( 'Active Dot Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ef4444',
                'selectors' => array(
                    '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .swiper-pagination-progressbar-fill' => 'background: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( empty( $settings['slides'] ) || ! is_array( $settings['slides'] ) ) {
            return;
        }

        $widget_id = 'kaitori-carousel-' . $this->get_id();

        // Responsive slides to show
        $slides_desktop = ! empty( $settings['slides_to_show'] ) ? (int) $settings['slides_to_show'] : 1;
        $slides_tablet  = ! empty( $settings['slides_to_show_tablet'] ) ? (int) $settings['slides_to_show_tablet'] : 1;
        $slides_mobile  = ! empty( $settings['slides_to_show_mobile'] ) ? (int) $settings['slides_to_show_mobile'] : 1;

        // Space between
        $space_desktop  = isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 20;
        $space_tablet   = isset( $settings['space_between_tablet']['size'] ) ? (int) $settings['space_between_tablet']['size'] : $space_desktop;
        $space_mobile   = isset( $settings['space_between_mobile']['size'] ) ? (int) $settings['space_between_mobile']['size'] : 10;

        // Swiper JSON configuration
        $carousel_config = array(
            'slidesPerView'  => $slides_desktop,
            'spaceBetween'   => $space_desktop,
            'loop'           => ( 'yes' === $settings['infinite_loop'] ),
            'autoplay'       => ( 'yes' === $settings['autoplay'] ),
            'autoplayDelay'  => ! empty( $settings['autoplay_delay'] ) ? (int) $settings['autoplay_delay'] : 4000,
            'pauseOnHover'   => ( 'yes' === $settings['pause_on_hover'] ),
            'speed'          => ! empty( $settings['transition_speed'] ) ? (int) $settings['transition_speed'] : 600,
            'effect'         => ! empty( $settings['transition_effect'] ) ? $settings['transition_effect'] : 'slide',
            'showArrows'     => ( 'yes' === $settings['show_arrows'] ),
            'arrowsPosition' => ! empty( $settings['arrows_position'] ) ? $settings['arrows_position'] : 'inside',
            'showPagination' => ( 'yes' === $settings['show_pagination'] ),
            'paginationType' => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
            'breakpoints'    => array(
                // Mobile (< 768px)
                0 => array(
                    'slidesPerView' => $slides_mobile,
                    'spaceBetween'  => $space_mobile,
                ),
                // Tablet (768px - 1024px)
                768 => array(
                    'slidesPerView' => $slides_tablet,
                    'spaceBetween'  => $space_tablet,
                ),
                // Desktop (>= 1025px)
                1025 => array(
                    'slidesPerView' => $slides_desktop,
                    'spaceBetween'  => $space_desktop,
                ),
            ),
        );
        ?>
        <div class="kaitori-loop-carousel-wrapper <?php echo esc_attr( 'arrows-pos-' . $carousel_config['arrowsPosition'] ); ?>" id="<?php echo esc_attr( $widget_id ); ?>" data-carousel-config="<?php echo esc_attr( wp_json_encode( $carousel_config ) ); ?>">

            <style>
                #<?php echo esc_attr( $widget_id ); ?> {
                    position: relative;
                    width: 100%;
                    overflow: hidden;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-loop-swiper {
                    width: 100%;
                    position: relative;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-slide {
                    box-sizing: border-box;
                    overflow: hidden;
                    position: relative;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-slide-inner {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    background-position: center center;
                    background-size: cover;
                    background-repeat: no-repeat;
                    box-sizing: border-box;
                    overflow: hidden;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-slide-overlay {
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    z-index: 1;
                    pointer-events: none;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-slide-content-wrap {
                    position: relative;
                    z-index: 2;
                    width: 100%;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-subtitle {
                    font-size: 13px;
                    font-weight: 700;
                    letter-spacing: 1px;
                    text-transform: uppercase;
                    margin-bottom: 8px;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-title {
                    font-size: 26px;
                    font-weight: 800;
                    line-height: 1.3;
                    margin: 0 0 12px 0;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-desc {
                    font-size: 14px;
                    line-height: 1.6;
                    margin: 0 0 20px 0;
                    max-width: 650px;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-btn {
                    display: inline-block;
                    font-size: 14px;
                    font-weight: 700;
                    padding: 10px 24px;
                    text-decoration: none !important;
                    transition: all 0.2s ease;
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-arrow {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    z-index: 10;
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.25s ease;
                    user-select: none;
                    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
                }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-prev { left: 16px; }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-next { right: 16px; }
                #<?php echo esc_attr( $widget_id ); ?>.arrows-pos-outside .kaitori-carousel-prev { left: 2px; }
                #<?php echo esc_attr( $widget_id ); ?>.arrows-pos-outside .kaitori-carousel-next { right: 2px; }
                #<?php echo esc_attr( $widget_id ); ?> .kaitori-carousel-pagination {
                    position: absolute;
                    bottom: 14px;
                    left: 0; right: 0;
                    z-index: 10;
                    text-align: center;
                }
                #<?php echo esc_attr( $widget_id ); ?> .swiper-pagination-bullet {
                    width: 10px;
                    height: 10px;
                    margin: 0 5px;
                    opacity: 1;
                    transition: all 0.25s ease;
                    border-radius: 999px;
                }
                #<?php echo esc_attr( $widget_id ); ?> .swiper-pagination-bullet-active {
                    width: 26px;
                }
            </style>

            <div class="swiper-container swiper kaitori-loop-swiper">
                <div class="swiper-wrapper">
                    <?php
                    foreach ( $settings['slides'] as $index => $slide ) :
                        $content_type = ! empty( $slide['content_type'] ) ? $slide['content_type'] : 'custom';
                        $bg_img       = ! empty( $slide['bg_image']['url'] ) ? $slide['bg_image']['url'] : '';
                        $bg_pos       = ! empty( $slide['bg_position'] ) ? $slide['bg_position'] : 'center center';
                        $bg_sz        = ! empty( $slide['bg_size'] ) ? $slide['bg_size'] : 'cover';
                        $bg_rep       = ! empty( $slide['bg_repeat'] ) ? $slide['bg_repeat'] : 'no-repeat';
                        $bg_col       = ! empty( $slide['bg_color'] ) ? $slide['bg_color'] : '#0f172a';
                        
                        $slide_styles = "background-color: {$bg_col};";
                        if ( $bg_img ) {
                            $slide_styles .= " background-image: url('" . esc_url( $bg_img ) . "');";
                            $slide_styles .= " background-position: {$bg_pos};";
                            $slide_styles .= " background-size: {$bg_sz};";
                            $slide_styles .= " background-repeat: {$bg_rep};";
                        }

                        $has_overlay    = ( 'yes' === $slide['overlay_enable'] );
                        $overlay_color  = ! empty( $slide['overlay_color'] ) ? $slide['overlay_color'] : 'rgba(0,0,0,0.4)';
                        $link_whole     = ( 'entire' === $slide['slide_link_type'] && ! empty( $slide['slide_url']['url'] ) );
                        $link_url       = $link_whole ? esc_url( $slide['slide_url']['url'] ) : '';
                        $link_target    = ( $link_whole && ! empty( $slide['slide_url']['is_external'] ) ) ? ' target="_blank"' : '';
                        $link_nofollow  = ( $link_whole && ! empty( $slide['slide_url']['nofollow'] ) ) ? ' rel="nofollow"' : '';
                    ?>
                        <div class="swiper-slide kaitori-carousel-slide elementor-repeater-item-<?php echo esc_attr( $slide['_id'] ); ?>">
                            
                            <?php if ( $link_whole ) : ?>
                                <a href="<?php echo $link_url; ?>"<?php echo $link_target . $link_nofollow; ?> style="text-decoration:none; color:inherit; display:block; width:100%; height:100%;">
                            <?php endif; ?>

                            <div class="kaitori-carousel-slide-inner" style="<?php echo esc_attr( $slide_styles ); ?>">
                                
                                <?php if ( $has_overlay ) : ?>
                                    <div class="kaitori-slide-overlay" style="background-color: <?php echo esc_attr( $overlay_color ); ?>;"></div>
                                <?php endif; ?>

                                <div class="kaitori-slide-content-wrap">
                                    <?php if ( 'template' === $content_type && ! empty( $slide['template_id'] ) ) : ?>
                                        <!-- Render Elementor Saved Template -->
                                        <div class="kaitori-carousel-template-content">
                                            <?php
                                            if ( class_exists( '\Elementor\Plugin' ) ) {
                                                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( (int) $slide['template_id'] );
                                            }
                                            ?>
                                        </div>
                                    <?php else : ?>
                                        <!-- Render Custom Content -->
                                        <div class="kaitori-carousel-custom-content">
                                            <?php if ( ! empty( $slide['custom_subtitle'] ) ) : ?>
                                                <div class="kaitori-carousel-subtitle"><?php echo esc_html( $slide['custom_subtitle'] ); ?></div>
                                            <?php endif; ?>

                                            <?php if ( ! empty( $slide['custom_title'] ) ) : ?>
                                                <h3 class="kaitori-carousel-title"><?php echo esc_html( $slide['custom_title'] ); ?></h3>
                                            <?php endif; ?>

                                            <?php if ( ! empty( $slide['custom_description'] ) ) : ?>
                                                <div class="kaitori-carousel-desc"><?php echo wp_kses_post( $slide['custom_description'] ); ?></div>
                                            <?php endif; ?>

                                            <?php if ( ! empty( $slide['custom_btn_text'] ) && ! empty( $slide['custom_btn_link']['url'] ) ) : 
                                                $btn_target   = ! empty( $slide['custom_btn_link']['is_external'] ) ? ' target="_blank"' : '';
                                                $btn_nofollow = ! empty( $slide['custom_btn_link']['nofollow'] ) ? ' rel="nofollow"' : '';
                                            ?>
                                                <div>
                                                    <a href="<?php echo esc_url( $slide['custom_btn_link']['url'] ); ?>" class="kaitori-carousel-btn"<?php echo $btn_target . $btn_nofollow; ?>>
                                                        <?php echo esc_html( $slide['custom_btn_text'] ); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <?php if ( $link_whole ) : ?>
                                </a>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Arrows -->
                <?php if ( 'yes' === $settings['show_arrows'] ) : ?>
                    <div class="kaitori-carousel-arrow kaitori-carousel-prev" aria-label="Previous Slide">
                        <i class="fa fa-chevron-left" style="font-size:16px;"></i>
                    </div>
                    <div class="kaitori-carousel-arrow kaitori-carousel-next" aria-label="Next Slide">
                        <i class="fa fa-chevron-right" style="font-size:16px;"></i>
                    </div>
                <?php endif; ?>

                <!-- Pagination Dots -->
                <?php if ( 'yes' === $settings['show_pagination'] ) : ?>
                    <div class="kaitori-carousel-pagination swiper-pagination"></div>
                <?php endif; ?>

            </div>

        </div>

        <!-- Inline Swiper initialization script for instantaneous rendering -->
        <script>
        (function() {
            function initSwiperWidget() {
                var wrapper = document.getElementById('<?php echo esc_js( $widget_id ); ?>');
                if (!wrapper) return;
                var rawConfig = wrapper.getAttribute('data-carousel-config');
                if (!rawConfig) return;
                var cfg = JSON.parse(rawConfig);
                var container = wrapper.querySelector('.kaitori-loop-swiper');
                if (!container) return;

                var swiperOpts = {
                    slidesPerView: cfg.slidesPerView || 1,
                    spaceBetween: cfg.spaceBetween || 0,
                    loop: (cfg.loop === true),
                    speed: cfg.speed || 600,
                    grabCursor: true,
                    watchSlidesProgress: true,
                    observer: true,
                    observeParents: true
                };

                if (cfg.effect === 'fade') {
                    swiperOpts.effect = 'fade';
                    swiperOpts.fadeEffect = { crossFade: true };
                }

                if (cfg.autoplay) {
                    swiperOpts.autoplay = {
                        delay: cfg.autoplayDelay || 4000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: (cfg.pauseOnHover === true)
                    };
                    swiperOpts.autoplayDisableOnInteraction = false;
                }

                if (cfg.showArrows) {
                    var prevEl = wrapper.querySelector('.kaitori-carousel-prev');
                    var nextEl = wrapper.querySelector('.kaitori-carousel-next');
                    if (prevEl && nextEl) {
                        swiperOpts.navigation = { prevEl: prevEl, nextEl: nextEl };
                        swiperOpts.prevButton = prevEl;
                        swiperOpts.nextButton = nextEl;
                    }
                }

                if (cfg.showPagination) {
                    var pagEl = wrapper.querySelector('.kaitori-carousel-pagination');
                    if (pagEl) {
                        swiperOpts.pagination = {
                            el: pagEl,
                            clickable: true,
                            type: cfg.paginationType || 'bullets'
                        };
                        swiperOpts.paginationClickable = true;
                    }
                }

                if (cfg.breakpoints) {
                    swiperOpts.breakpoints = cfg.breakpoints;
                }

                // If existing Swiper instance exists, destroy first
                if (container.swiperInstance && typeof container.swiperInstance.destroy === 'function') {
                    container.swiperInstance.destroy(true, true);
                }

                if (typeof Swiper !== 'undefined') {
                    container.swiperInstance = new Swiper(container, swiperOpts);
                } else if (typeof jQuery !== 'undefined' && typeof jQuery.fn.swiper === 'function') {
                    container.swiperInstance = jQuery(container).swiper(swiperOpts);
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSwiperWidget);
            } else {
                initSwiperWidget();
            }

            // Hook for Elementor Frontend Live Preview
            if (typeof jQuery !== 'undefined') {
                jQuery(window).on('elementor/frontend/init', function() {
                    if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
                        elementorFrontend.hooks.addAction('frontend/element_ready/kaitori_loop_carousel.default', function($scope) {
                            initSwiperWidget();
                        });
                    }
                });
            }
        })();
        </script>
        <?php
    }
}
