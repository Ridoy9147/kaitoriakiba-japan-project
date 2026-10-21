<?php
/**
 * Elementor Service Process Flow Widget
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Process_Flow_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_process_flow';
    }

    public function get_title() {
        return __( 'Buyback Flow Steps (買取の流れ)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-navigation-horizontal';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Process Cards', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'card1_title',
            array(
                'label'   => __( 'Card 1 Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '店頭買取 (In-Store Buyback)',
            )
        );
        $this->add_control(
            'card1_desc',
            array(
                'label'   => __( 'Card 1 Description', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '秋葉原店へ直接端末をお持ち込み。最短5分で動作確認・査定完了、その場で現金手渡し！',
            )
        );

        $this->add_control(
            'card2_title',
            array(
                'label'   => __( 'Card 2 Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '郵送買取 (Mail-In Buyback)',
            )
        );
        $this->add_control(
            'card2_desc',
            array(
                'label'   => __( 'Card 2 Description', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '送料無料の宅配買取キットをお届け。端末到着当日に即日査定＆ご指定口座へ即日お振込み！',
            )
        );

        $this->add_control(
            'card3_title',
            array(
                'label'   => __( 'Card 3 Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '法人買取 (Corporate Buyback)',
            )
        );
        $this->add_control(
            'card3_desc',
            array(
                'label'   => __( 'Card 3 Description', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '社用スマホ・タブレットの大量台数一括買取。データ完全消去証明書の発行や請求書払い対応。',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $card1_title = ! empty( $settings['card1_title'] ) ? $settings['card1_title'] : '店頭買取 (In-Store Buyback)';
        $card1_desc  = ! empty( $settings['card1_desc'] ) ? $settings['card1_desc'] : '秋葉原店へ直接端末をお持ち込み。最短5分で動作確認・査定完了、その場で現金手渡し！';
        $card2_title = ! empty( $settings['card2_title'] ) ? $settings['card2_title'] : '郵送買取 (Mail-In Buyback)';
        $card2_desc  = ! empty( $settings['card2_desc'] ) ? $settings['card2_desc'] : '送料無料の宅配キット完備。到着当日に即日査定＆指定口座へ即日振込！';
        $card3_title = ! empty( $settings['card3_title'] ) ? $settings['card3_title'] : '法人買取 (Corporate Buyback)';
        $card3_desc  = ! empty( $settings['card3_desc'] ) ? $settings['card3_desc'] : '社用スマホ・タブレットの大量台数一括買取。データ完全消去証明書の発行や請求書払い対応。';
        ?>
        <div class="kaitori-process-flow-widget" style="margin: 30px 0;">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12" style="margin-bottom: 20px;">
                    <div style="background:#ffffff; border:1px solid #fed7aa; border-radius:14px; padding:24px; height:100%; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                            <div style="width:42px; height:42px; border-radius:10px; background:#fff7ed; color:#ea580c; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                <i class="fa fa-building"></i>
                            </div>
                            <h3 style="font-size:16px; font-weight:800; color:#0f172a; margin:0;"><?php echo esc_html( $card1_title ); ?></h3>
                        </div>
                        <p style="font-size:13px; color:#475569; line-height:1.6; margin:0;"><?php echo esc_html( $card1_desc ); ?></p>
                        <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>" class="btn btn-default" style="margin-top:16px; width:100%; font-weight:700; border-radius:6px; border-color:#cbd5e1;">店頭買取を申込む &rarr;</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-12" style="margin-bottom: 20px;">
                    <div style="background:#ffffff; border:1px solid #bbf7d0; border-radius:14px; padding:24px; height:100%; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                            <div style="width:42px; height:42px; border-radius:10px; background:#f0fdf4; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                <i class="fa fa-truck"></i>
                            </div>
                            <h3 style="font-size:16px; font-weight:800; color:#0f172a; margin:0;"><?php echo esc_html( $card2_title ); ?></h3>
                        </div>
                        <p style="font-size:13px; color:#475569; line-height:1.6; margin:0;"><?php echo esc_html( $card2_desc ); ?></p>
                        <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>" class="btn btn-success" style="margin-top:16px; width:100%; font-weight:700; border-radius:6px; background:#16a34a; border-color:#16a34a;">郵送買取を申込む &rarr;</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-12" style="margin-bottom: 20px;">
                    <div style="background:#ffffff; border:1px solid #bfdbfe; border-radius:14px; padding:24px; height:100%; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                            <div style="width:42px; height:42px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                <i class="fa fa-briefcase"></i>
                            </div>
                            <h3 style="font-size:16px; font-weight:800; color:#0f172a; margin:0;"><?php echo esc_html( $card3_title ); ?></h3>
                        </div>
                        <p style="font-size:13px; color:#475569; line-height:1.6; margin:0;"><?php echo esc_html( $card3_desc ); ?></p>
                        <a href="<?php echo esc_url( home_url( '/sell-apply/?type=corporate' ) ); ?>" class="btn btn-primary" style="margin-top:16px; width:100%; font-weight:700; border-radius:6px; background:#2563eb; border-color:#2563eb;">法人見積・相談 &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
