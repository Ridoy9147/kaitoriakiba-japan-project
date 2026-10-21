<?php
/**
 * Elementor Sidebar Newsletter & Quick Guides Widget
 * 
 * Renders the authentic navigation-box2 (Information & Guides) and email (Newsletter) sections.
 * 100% Fully Editable in Elementor.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Sidebar_Newsletter_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_sidebar_newsletter';
    }

    public function get_title() {
        return __( 'Sidebar Newsletter & Guides (サイドバー メルマガ・案内)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-envelope';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {

        $theme_uri = get_template_directory_uri();

        // 1. Information & Guides Box
        $this->start_controls_section(
            'section_guides',
            array(
                'label' => __( '1. Information & Guides Box (ご利用案内)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_guides',
            array(
                'label'   => __( 'Show Guides Box', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'guides_title',
            array(
                'label'   => __( 'Box Header Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Information & Guides (ご利用案内)',
                'condition' => array( 'show_guides' => 'yes' ),
            )
        );

        $repeater_guides = new \Elementor\Repeater();

        $repeater_guides->add_control(
            'link_title',
            array(
                'label'       => __( 'Link Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '買取申込 (Online Application)',
                'label_block' => true,
            )
        );

        $repeater_guides->add_control(
            'link_url',
            array(
                'label'   => __( 'URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array(
                    'url' => '/sell-apply/',
                ),
            )
        );

        $this->add_control(
            'guides_links_list',
            array(
                'label'       => __( 'Guide Links', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_guides->get_controls(),
                'default'     => array(
                    array( 'link_title' => '買取申込 (Online Application)', 'link_url' => array( 'url' => '/sell-apply/' ) ),
                    array( 'link_title' => '本人確認 (eKYC Verification)', 'link_url' => array( 'url' => '/kyc-verify/' ) ),
                    array( 'link_title' => '新規会員登録 (Register)', 'link_url' => array( 'url' => '/register/' ) ),
                    array( 'link_title' => '買取価格一覧 (Price List)', 'link_url' => array( 'url' => '/devices/' ) ),
                ),
                'condition'   => array( 'show_guides' => 'yes' ),
            )
        );

        $this->end_controls_section();

        // 2. Weekly Newsletter Box
        $this->start_controls_section(
            'section_newsletter',
            array(
                'label' => __( '2. Newsletter Box (メールマガジン)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_newsletter',
            array(
                'label'   => __( 'Show Newsletter Box', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'newsletter_img',
            array(
                'label'   => __( 'Newsletter Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/email/weekly.jpg',
                ),
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->add_control(
            'newsletter_desc',
            array(
                'label'   => __( 'Description Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '最新の高価買取相場や会員限定キャンペーン情報をメールでお届けします。',
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->add_control(
            'newsletter_btn_text',
            array(
                'label'   => __( 'Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'メールマガジン登録',
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->add_control(
            'newsletter_btn_url',
            array(
                'label'   => __( 'Button Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array(
                    'url' => '/register/',
                ),
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        ?>
        <div class="kaitori-sidebar-newsletter-wrap">
            
            <?php if ( 'yes' === $settings['show_guides'] ) : ?>
                <div class="navigation-box2" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
                    <div class="navigation-h2" style="background:#0f172a; padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
                        <i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i>
                        <span><?php echo esc_html( $settings['guides_title'] ); ?></span>
                    </div>
                    <?php if ( ! empty( $settings['guides_links_list'] ) && is_array( $settings['guides_links_list'] ) ) : ?>
                        <ul class="navigation-h3" style="list-style:none; padding:10px 14px; margin:0;">
                            <?php foreach ( $settings['guides_links_list'] as $g_item ) : 
                                $g_url = ! empty( $g_item['link_url']['url'] ) ? $g_item['link_url']['url'] : '#';
                            ?>
                                <li style="padding:8px 0; border-bottom:1px solid #f1f5f9; list-style:none;">
                                    <a href="<?php echo esc_url( $g_url ); ?>" style="text-decoration:none; color:#1e293b; font-weight:600; font-size:13px; display:block;">
                                        <i class="fa fa-caret-right" style="color:#ef4444; margin-right:6px;"></i>
                                        <span><?php echo esc_html( $g_item['link_title'] ); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_newsletter'] ) : 
                $nl_img = ! empty( $settings['newsletter_img']['url'] ) ? $settings['newsletter_img']['url'] : ( $theme_uri . '/assets/images/email/weekly.jpg' );
                $nl_url = ! empty( $settings['newsletter_btn_url']['url'] ) ? $settings['newsletter_btn_url']['url'] : '/register/';
            ?>
                <div class="email" style="margin-bottom: 20px;">
                    <div class="wrapper" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; padding:12px;">
                        <?php if ( ! empty( $nl_img ) ) : ?>
                            <div class="header">
                                <img src="<?php echo esc_url( $nl_img ); ?>" width="100%" alt="Weekly Newsletter" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:6px;" />
                            </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $settings['newsletter_desc'] ) ) : ?>
                            <p class="info" style="font-size:12px; margin:10px 0 10px 0; color:#475569; line-height:1.5;">
                                <?php echo nl2br( esc_html( $settings['newsletter_desc'] ) ); ?>
                            </p>
                        <?php endif; ?>
                        <div>
                            <a class="btn-ys block-btn btn-ys-cor1" style="background-color:#ef4444; color:#fff; display:block; text-align:center; padding:10px; border-radius:6px; font-weight:700; text-decoration:none;" href="<?php echo esc_url( $nl_url ); ?>">
                                <?php echo esc_html( $settings['newsletter_btn_text'] ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}
