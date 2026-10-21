<?php
/**
 * Elementor Announcement & Quick Guides Widget
 * 
 * 100% Editable in Elementor:
 * - Repeater for the 6 Quick Guide buttons (title, icon, link)
 * - Urgent Announcement banner (title, accordion content, link, badge, toggle button text)
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

        // =====================================================================
        // SECTION 1: QUICK GUIDES (6 BUTTONS)
        // =====================================================================
        $this->start_controls_section(
            'section_quick_guides',
            array(
                'label' => __( '1. Quick Guide Buttons (クイック案内)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_quick_links',
            array(
                'label'   => __( 'Show Quick Guide Buttons', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $repeater_guides = new \Elementor\Repeater();

        $repeater_guides->add_control(
            'guide_title',
            array(
                'label'       => __( 'Button Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'How to Register / 新規登録',
                'label_block' => true,
            )
        );

        $repeater_guides->add_control(
            'guide_url',
            array(
                'label'       => __( 'Link URL', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com or /register/', 'kaitoriakiba' ),
                'default'     => array(
                    'url' => '/register/',
                ),
            )
        );

        $this->add_control(
            'quick_guides_list',
            array(
                'label'       => __( 'Quick Guide Items', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_guides->get_controls(),
                'default'     => array(
                    array(
                        'guide_title' => 'How to Register / 新規登録',
                        'guide_url'   => array( 'url' => '/register/' ),
                    ),
                    array(
                        'guide_title' => 'How to Apply / 買取申込方法',
                        'guide_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                    array(
                        'guide_title' => 'Identity Verification / 本人確認(eKYC)',
                        'guide_url'   => array( 'url' => '/kyc-verify/' ),
                    ),
                    array(
                        'guide_title' => 'Price Guarantee / 査定価格保証',
                        'guide_url'   => array( 'url' => '/devices/' ),
                    ),
                    array(
                        'guide_title' => 'Member Profile / 会員情報変更',
                        'guide_url'   => array( 'url' => '/register/#login' ),
                    ),
                    array(
                        'guide_title' => 'FAQ / よくあるご質問',
                        'guide_url'   => array( 'url' => '/faq/' ),
                    ),
                ),
                'title_field' => '{{{ guide_title }}}',
                'condition'   => array( 'show_quick_links' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 2: URGENT ANNOUNCEMENT ACCORDION
        // =====================================================================
        $this->start_controls_section(
            'section_announcement',
            array(
                'label' => __( '2. Urgent Announcement Box (重要なお知らせ)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_notice_box',
            array(
                'label'   => __( 'Show Notice Box', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'announcement_title',
            array(
                'label'       => __( 'Notice Header Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '【重要】買取アキバからのお知らせ (Urgent Announcements)',
                'label_block' => true,
                'condition'   => array( 'show_notice_box' => 'yes' ),
            )
        );

        $this->add_control(
            'announcement_badge',
            array(
                'label'       => __( 'Highlight Badge Text', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '★ 重要: ご来店・ご郵送の前にWEBページより事前査定申込をお願いいたします (事前予約制) ★',
                'label_block' => true,
                'condition'   => array( 'show_notice_box' => 'yes' ),
            )
        );

        $repeater_notices = new \Elementor\Repeater();

        $repeater_notices->add_control(
            'notice_text',
            array(
                'label'       => __( 'Notice Bullet Text', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '【郵送買取】送料無料キャンペーン実施中！到着当日にスピード本査定＆最短即日入金',
                'label_block' => true,
            )
        );

        $repeater_notices->add_control(
            'notice_url',
            array(
                'label'       => __( 'Optional Link', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com or /sell-apply/', 'kaitoriakiba' ),
                'default'     => array(
                    'url' => '',
                ),
            )
        );

        $this->add_control(
            'notice_items_list',
            array(
                'label'       => __( 'Notice Bullet Points', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_notices->get_controls(),
                'default'     => array(
                    array(
                        'notice_text' => '【重要】古物営業法に基づく本人確認書類（eKYC）および購入証明書の提出について',
                        'notice_url'  => array( 'url' => '/kyc-verify/' ),
                    ),
                    array(
                        'notice_text' => '【郵送買取】送料無料キャンペーン実施中！到着当日にスピード本査定＆最短即日入金',
                        'notice_url'  => array( 'url' => '/sell-apply/?delivery=mail' ),
                    ),
                    array(
                        'notice_text' => '【店舗情報】秋葉原店は年中無休で営業中 (営業時間: 10:00 〜 19:30)',
                        'notice_url'  => array( 'url' => '/access/' ),
                    ),
                    array(
                        'notice_text' => '■ 分割残債あり（利用制限△）の端末も減額査定なしでお買取り可能です。',
                        'notice_url'  => array( 'url' => '' ),
                    ),
                    array(
                        'notice_text' => '■ 法人様の大量端末お買取り・データ消去証明書の発行にも対応しております。',
                        'notice_url'  => array( 'url' => '' ),
                    ),
                ),
                'title_field' => '{{{ notice_text }}}',
                'condition'   => array( 'show_notice_box' => 'yes' ),
            )
        );

        $this->add_control(
            'btn_more_text',
            array(
                'label'     => __( 'Show More Button Text', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Show More / 詳細を見る',
                'condition' => array( 'show_notice_box' => 'yes' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $title    = ! empty( $settings['announcement_title'] ) ? $settings['announcement_title'] : '【重要】買取アキバからのお知らせ (Urgent Announcements)';
        $widget_id = $this->get_id();
        ?>
        <div class="maintop" style="margin-top: 15px;">
            <?php if ( 'yes' === $settings['show_quick_links'] && ! empty( $settings['quick_guides_list'] ) ) : ?>
                <div class="home_liuxiang">
                    <div class="container">
                        <div class="row home_lx_box">
                            <?php foreach ( $settings['quick_guides_list'] as $guide ) : 
                                $g_url = ! empty( $guide['guide_url']['url'] ) ? $guide['guide_url']['url'] : '#';
                                $is_ext = ! empty( $guide['guide_url']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
                            ?>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                                    <div class="home_lx_td">
                                        <a href="<?php echo esc_url( $g_url ); ?>"<?php echo $is_ext; ?>><?php echo esc_html( $guide['guide_title'] ); ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_notice_box'] ) : ?>
                <div class="container pding0">
                    <div style="background:#feecec; padding:20px; margin-bottom:10px; border-radius:8px; border:1px solid #fecaca;">
                        <div style="border-bottom:1px solid #fca5a5; padding-bottom:10px; font-weight:800; color:#b91c1c; font-size:15px;">
                            <i class="fa fa-bullhorn" style="margin-right:6px;"></i> <?php echo esc_html( $title ); ?>
                        </div>
                        <div style="padding-top:15px;" class="qingbao" id="el_qb_<?php echo esc_attr( $widget_id ); ?>">
                            <?php if ( ! empty( $settings['announcement_badge'] ) ) : ?>
                                <p><span style="background-color:#FFE500; color:#E53333; font-size:14px; padding:4px 8px; font-weight:800; border-radius:4px;"><?php echo esc_html( $settings['announcement_badge'] ); ?></span></p>
                            <?php endif; ?>

                            <?php if ( ! empty( $settings['notice_items_list'] ) ) : 
                                foreach ( $settings['notice_items_list'] as $notice ) :
                                    $n_url = ! empty( $notice['notice_url']['url'] ) ? $notice['notice_url']['url'] : '';
                            ?>
                                <p style="margin: 8px 0; font-size:14px; color:#334155;">
                                    <?php if ( ! empty( $n_url ) ) : ?>
                                        <a href="<?php echo esc_url( $n_url ); ?>" style="color:#003399; font-weight:700; text-decoration:underline;">
                                            <?php echo esc_html( $notice['notice_text'] ); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo esc_html( $notice['notice_text'] ); ?>
                                    <?php endif; ?>
                                </p>
                            <?php endforeach; endif; ?>
                        </div>
                        <div class="dropdownbutton" style="text-align:center; margin-top:12px;">
                            <button type="button" id="el_btn_<?php echo esc_attr( $widget_id ); ?>" class="dropdown" onclick="elQbToggle_<?php echo esc_attr( $widget_id ); ?>();" style="cursor:pointer; background:#ffffff; border:1px solid #fca5a5; padding:6px 18px; border-radius:20px; font-size:13px; font-weight:700; color:#b91c1c;">
                                <?php echo esc_html( ! empty( $settings['btn_more_text'] ) ? $settings['btn_more_text'] : 'Show More / 詳細を見る' ); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    function elQbToggle_<?php echo esc_attr( $widget_id ); ?>() {
                        var qb = document.getElementById('el_qb_<?php echo esc_attr( $widget_id ); ?>');
                        var btn = document.getElementById('el_btn_<?php echo esc_attr( $widget_id ); ?>');
                        if (qb && btn) {
                            if (qb.classList.contains('qingbao')) {
                                qb.classList.remove('qingbao');
                                btn.innerText = 'Show Less / 閉じる';
                            } else {
                                qb.classList.add('qingbao');
                                btn.innerText = '<?php echo esc_js( ! empty( $settings['btn_more_text'] ) ? $settings['btn_more_text'] : 'Show More / 詳細を見る' ); ?>';
                            }
                        }
                    }
                </script>
            <?php endif; ?>
        </div>
        <?php
    }
}
