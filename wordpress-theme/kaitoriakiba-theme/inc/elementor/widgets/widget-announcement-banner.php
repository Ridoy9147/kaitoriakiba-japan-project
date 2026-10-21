<?php
/**
 * Elementor Announcement Banner Widget
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Announcement_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_announcement';
    }

    public function get_title() {
        return __( 'Announcement Alert Banner (お知らせバナー)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-alert';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_alert',
            array(
                'label' => __( 'Notice Details', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'alert_title',
            array(
                'label'   => __( 'Notice Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '【重要なお知らせ】iPhone 16 / 15 シリーズ超高価買取キャンペーン実施中！',
            )
        );

        $this->add_control(
            'alert_content',
            array(
                'label'   => __( 'Notice Body', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '現在、秋葉原店持込および郵送買取にてiPhone 16 Pro / 15 Proの買取価格を強化中。店頭なら最短5分で即日現金お渡し可能です。',
            )
        );

        $this->add_control(
            'alert_type',
            array(
                'label'   => __( 'Style Type', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'danger',
                'options' => array(
                    'danger'  => __( 'Red (Urgent / High Priority)', 'kaitoriakiba' ),
                    'warning' => __( 'Orange (Important Notice)', 'kaitoriakiba' ),
                    'info'    => __( 'Blue (General Info)', 'kaitoriakiba' ),
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $border_colors = array(
            'danger'  => '#fca5a5',
            'warning' => '#fde68a',
            'info'    => '#bfdbfe',
        );
        $bg_colors = array(
            'danger'  => '#fef2f2',
            'warning' => '#fffbeb',
            'info'    => '#eff6ff',
        );
        $icon_colors = array(
            'danger'  => '#ef4444',
            'warning' => '#f59e0b',
            'info'    => '#3b82f6',
        );

        $type = ! empty( $settings['alert_type'] ) && isset( $bg_colors[$settings['alert_type']] ) ? $settings['alert_type'] : 'danger';
        $title = ! empty( $settings['alert_title'] ) ? $settings['alert_title'] : ( ! empty( $settings['banner_text'] ) ? $settings['banner_text'] : '【重要なお知らせ】iPhone 16 / 15 シリーズ超高価買取キャンペーン実施中！' );
        $content = ! empty( $settings['alert_content'] ) ? $settings['alert_content'] : '現在、秋葉原店持込および郵送買取にてiPhone 16 Pro / 15 Proの買取価格を強化中。店頭なら最短5分で即日現金お渡し可能です。';
        ?>
        <div class="kaitori-announcement-banner" style="background:<?php echo $bg_colors[$type]; ?>; border:1.5px solid <?php echo $border_colors[$type]; ?>; border-radius:12px; padding:18px 24px; margin:20px 0; display:flex; align-items:flex-start; gap:16px;">
            <div style="font-size:24px; color:<?php echo $icon_colors[$type]; ?>; line-height:1;">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <div>
                <h4 style="font-size:15px; font-weight:800; color:#0f172a; margin:0 0 6px 0;"><?php echo esc_html( $title ); ?></h4>
                <p style="font-size:13px; color:#475569; margin:0; line-height:1.5;"><?php echo esc_html( $content ); ?></p>
            </div>
        </div>
        <?php
    }
}
