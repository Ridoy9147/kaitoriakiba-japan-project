<?php
/**
 * Elementor Announcement & Quick Guides Widget
 * 
 * Renders the authentic home_liuxiang quick guide links and qingbao announcement box.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Announcement_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_announcement_banner';
    }

    public function get_title() {
        return __( 'Announcements & Quick Guides (お知らせ・案内)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-alert';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Announcement Content', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'announcement_title',
            array(
                'label'   => __( 'Urgent Notice Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '【重要】買取アキバからのお知らせ (Urgent Announcements)',
            )
        );

        $this->add_control(
            'show_quick_links',
            array(
                'label'   => __( 'Show Quick Guide Links', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $title    = ! empty( $settings['announcement_title'] ) ? $settings['announcement_title'] : '【重要】買取アキバからのお知らせ (Urgent Announcements)';
        $show_ql  = ( 'yes' === $settings['show_quick_links'] );
        ?>
        <div class="maintop" style="margin-top: 15px;">
            <?php if ( $show_ql ) : ?>
                <div class="home_liuxiang">
                    <div class="container">
                        <div class="row home_lx_box">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                                <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php _e( 'How to Register / 新規登録', 'kaitoriakiba' ); ?></a></div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                                <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><?php _e( 'How to Apply / 買取申込方法', 'kaitoriakiba' ); ?></a></div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                                <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><?php _e( 'Identity Verification / 本人確認(eKYC)', 'kaitoriakiba' ); ?></a></div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                                <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php _e( 'Price Guarantee / 査定価格保証', 'kaitoriakiba' ); ?></a></div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                                <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/register/#login' ) ); ?>"><?php _e( 'Member Profile / 会員情報変更', 'kaitoriakiba' ); ?></a></div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                                <div class="home_lx_td"><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php _e( 'FAQ / よくあるご質問', 'kaitoriakiba' ); ?></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="container pding0">
                <div style="background:#feecec; padding:20px; margin-bottom:10px; border-radius:8px;">
                    <div style="border-bottom:1px solid #fca5a5; padding-bottom:10px; font-weight:800; color:#b91c1c; font-size:15px;">
                        <i class="fa fa-bullhorn" style="margin-right:6px;"></i> <?php echo esc_html( $title ); ?>
                    </div>
                    <div style="padding-top:15px;" class="qingbao" id="el_qb">
                        <p><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>"><span style="font-size:15px; color:#003399;"><strong><u>【重要】古物営業法に基づく本人確認書類（eKYC）および購入証明書の提出について</u></strong></span></a></p>
                        <p><a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><span style="font-size:15px; color:#003399;"><strong><u>【郵送買取】送料無料キャンペーン実施中！到着当日にスピード本査定＆最短即日入金</u></strong></span></a></p>
                        <p><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>"><span style="font-size:15px; color:#003399;"><strong><u>【店舗情報】秋葉原店は年中無休で営業中 (営業時間: 10:00 〜 19:30)</u></strong></span></a></p>
                        <p><span style="background-color:#FFE500; color:#E53333; font-size:14px; padding:3px 6px;"><strong>★ 重要: ご来店・ご郵送の前にWEBページより事前査定申込をお願いいたします (事前予約制) ★</strong></span></p>
                        <p style="margin-top:10px; font-size:13px; color:#334155;">■ 分割残債あり（利用制限△）の端末も減額査定なしでお買取り可能です。</p>
                        <p style="font-size:13px; color:#334155;">■ 法人様の大量端末お買取り・データ消去証明書の発行にも対応しております。</p>
                    </div>
                    <div class="dropdownbutton" style="text-align:center; margin-top:10px;">
                        <button id="el_moreless" class="dropdown" onclick="elQbOpen();" style="cursor:pointer;"><?php _e( 'Show More / 詳細を見る', 'kaitoriakiba' ); ?></button>
                    </div>
                </div>
            </div>

            <script>
                function elQbOpen() {
                    var qb = document.getElementById('el_qb');
                    var btn = document.getElementById('el_moreless');
                    if (qb && btn) {
                        if (qb.classList.contains('qingbao')) {
                            qb.classList.remove('qingbao');
                            btn.innerText = 'Show Less / 閉じる';
                        } else {
                            qb.classList.add('qingbao');
                            btn.innerText = 'Show More / 詳細を見る';
                        }
                    }
                }
            </script>
        </div>
        <?php
    }
}
