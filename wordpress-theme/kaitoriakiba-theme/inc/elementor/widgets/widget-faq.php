<?php
/**
 * Elementor FAQ Accordion Widget
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_FAQ_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_faq';
    }

    public function get_title() {
        return __( 'Buyback FAQ (よくある質問)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-help-o';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_faq',
            array(
                'label' => __( 'FAQ Items', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'question',
            array(
                'label'       => __( 'Question', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '箱や付属品がなくても買取できますか？',
                'label_block' => true,
            )
        );
        $repeater->add_control(
            'answer',
            array(
                'label'       => __( 'Answer', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'     => 'はい、本体のみでも高価買取しております！付属品の欠品による大幅な減額はございません。',
            )
        );

        $this->add_control(
            'faq_list',
            array(
                'label'       => __( 'Questions List', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => array(
                    array(
                        'question' => '箱や付属品がなくても買取できますか？',
                        'answer'   => 'はい、本体のみでも高価買取しております！付属品の欠品による大幅な減額はございません。',
                    ),
                    array(
                        'question' => '分割支払い中（残債あり）のiPhoneも売れますか？',
                        'answer'   => 'はい、当店ではネットワーク利用制限△（分割中）の端末も僅かな減額幅で即日買取可能です。',
                    ),
                    array(
                        'question' => '画面が割れていても買い取ってもらえますか？',
                        'answer'   => 'はい、ガラス割れ・背面割れ・液晶不良などのジャンク状態でも適正価格でお買取いたします。',
                    ),
                    array(
                        'question' => '振込手数料や送料はかかりますか？',
                        'answer'   => '送料・振込手数料・査定料すべて当店負担（0円）です。お客様の費用負担は一切ございません。',
                    ),
                ),
                'title_field' => '{{{ question }}}',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="kaitori-faq-widget" style="background:#ffffff; border-radius:14px; border:1px solid #e2e8f0; padding:24px; margin:20px 0;">
            <h3 style="font-size:18px; font-weight:800; color:#0f172a; margin-top:0; margin-bottom:18px; border-bottom:2px solid #f1f5f9; padding-bottom:10px;">
                <i class="fa fa-question-circle" style="color:#ef4444;"></i> <?php _e( 'よくあるご質問 (FAQ)', 'kaitoriakiba' ); ?>
            </h3>
            <div class="faq-accordion-list">
                <?php foreach ( $settings['faq_list'] as $index => $item ) : ?>
                    <div class="faq-item" style="border-bottom:1px solid #f1f5f9; padding:14px 0;">
                        <div class="faq-question" style="font-size:14.5px; font-weight:800; color:#0f172a; cursor:pointer;" onclick="this.nextElementSibling.classList.toggle('hidden');">
                            <span style="color:#ef4444; margin-right:6px;">Q.</span> <?php echo esc_html( $item['question'] ); ?>
                            <i class="fa fa-angle-down pull-right" style="color:#94a3b8;"></i>
                        </div>
                        <div class="faq-answer" style="font-size:13px; color:#475569; line-height:1.6; margin-top:8px; padding-left:20px;">
                            <span style="color:#10b981; font-weight:bold; margin-right:6px;">A.</span> <?php echo esc_html( $item['answer'] ); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
