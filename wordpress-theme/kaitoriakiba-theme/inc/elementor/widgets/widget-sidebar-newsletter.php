<?php
/**
 * Elementor Sidebar Newsletter, Quick Guides & Bottom Ads Widget
 * 
 * Renders the authentic sidebar elements matching index.html lines 1398-1480:
 * - Menu Navigation Box 2 (.navigation-box2)
 * - All Class banner (.navigation_ad)
 * - 4 Bottom Ad Banners (.nad_2)
 * - Email Newsletter Box (.email)
 * 
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
        return __( 'Sidebar Menu, Ads & Newsletter (サイドバー案内・下部広告・メルマガ)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-envelope';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {

        $theme_uri = get_template_directory_uri();

        // 1. Menu Navigation Box
        $this->start_controls_section(
            'section_menu',
            array(
                'label' => __( '1. Menu Links Box (メニュー・案内)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_menu',
            array(
                'label'   => __( 'Show Menu Box', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'menu_title',
            array(
                'label'   => __( 'Menu Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Menu',
            )
        );

        $repeater_menu = new \Elementor\Repeater();

        $repeater_menu->add_control(
            'link_title',
            array(
                'label'       => __( 'Link Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'First-Time Guide',
                'label_block' => true,
            )
        );

        $repeater_menu->add_control(
            'link_url',
            array(
                'label'   => __( 'Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/start/' ),
            )
        );

        $this->add_control(
            'menu_links_list',
            array(
                'label'       => __( 'Menu Links', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_menu->get_controls(),
                'default'     => array(
                    array( 'link_title' => 'First-Time Guide', 'link_url' => array( 'url' => '/start/' ) ),
                    array( 'link_title' => 'In-Store Buyback Flow', 'link_url' => array( 'url' => '/shop-purchase-flow/' ) ),
                    array( 'link_title' => 'Mail-in Buyback Flow', 'link_url' => array( 'url' => '/delivery-purchase-flow/' ) ),
                    array( 'link_title' => 'Corporate Buyback Flow', 'link_url' => array( 'url' => '/corporation-purchase-flow/' ) ),
                    array( 'link_title' => 'Store Location & Access', 'link_url' => array( 'url' => '/qa/shop-access/' ) ),
                    array( 'link_title' => 'FAQ', 'link_url' => array( 'url' => '/qa/' ) ),
                    array( 'link_title' => 'Contact Us (Individual)', 'link_url' => array( 'url' => '/contact/personal/' ) ),
                    array( 'link_title' => 'Contact Us (Corporate)', 'link_url' => array( 'url' => '/contact/business/' ) ),
                ),
                'title_field' => '{{{ link_title }}}',
                'condition'   => array( 'show_menu' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // 2. All Class Banner & Bottom Ads
        $this->start_controls_section(
            'section_bottom_ads',
            array(
                'label' => __( '2. Category & Bottom Banners (各種バナー)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_all_class',
            array(
                'label'   => __( 'Show All Category Banner', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'all_class_img',
            array(
                'label'   => __( 'All Category Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/navigation/all_class.png' ),
                'condition' => array( 'show_all_class' => 'yes' ),
            )
        );

        $this->add_control(
            'all_class_url',
            array(
                'label'   => __( 'All Category URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/goods-class/' ),
                'condition' => array( 'show_all_class' => 'yes' ),
            )
        );

        $this->add_control(
            'show_bottom_ads',
            array(
                'label'   => __( 'Show 4 Bottom Ad Banners', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $repeater_bottom_ads = new \Elementor\Repeater();

        $repeater_bottom_ads->add_control(
            'ad_img',
            array(
                'label'   => __( 'Ad Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/navigation/ad_bottom_1.jpg' ),
            )
        );

        $repeater_bottom_ads->add_control(
            'ad_url',
            array(
                'label'   => __( 'Ad Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/contact/business/' ),
            )
        );

        $repeater_bottom_ads->add_control(
            'ad_alt',
            array(
                'label'   => __( 'Alt Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Banner',
            )
        );

        $this->add_control(
            'bottom_ads_list',
            array(
                'label'       => __( 'Bottom Ads', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_bottom_ads->get_controls(),
                'default'     => array(
                    array( 'ad_img' => array( 'url' => $theme_uri . '/assets/images/navigation/ad_bottom_1.jpg' ), 'ad_url' => array( 'url' => '/contact/business/' ), 'ad_alt' => 'Contact Us (Corporate)' ),
                    array( 'ad_img' => array( 'url' => $theme_uri . '/assets/images/navigation/ad_bottom_2.jpg' ), 'ad_url' => array( 'url' => '/contact/personal/' ), 'ad_alt' => 'Contact Us (Individual)' ),
                    array( 'ad_img' => array( 'url' => $theme_uri . '/assets/images/navigation/ad_bottom_mqbz.jpg' ), 'ad_url' => array( 'url' => '/home/article/detail/article_id/517' ), 'ad_alt' => 'Mail-in Buyback Instructions' ),
                    array( 'ad_img' => array( 'url' => $theme_uri . '/assets/images/navigation/ad_bottom_4.jpg' ), 'ad_url' => array( 'url' => '/upload/attached/pdf/doisho.pdf' ), 'ad_alt' => 'Download Parental Consent Form' ),
                ),
                'title_field' => '{{{ ad_alt }}}',
                'condition'   => array( 'show_bottom_ads' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // 3. Email Newsletter Box
        $this->start_controls_section(
            'section_newsletter',
            array(
                'label' => __( '3. Email Newsletter (メールマガジン)', 'kaitoriakiba' ),
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
            'nl_header_img',
            array(
                'label'   => __( 'Newsletter Header Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/email/weekly.jpg' ),
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->add_control(
            'nl_info',
            array(
                'label'   => __( 'Info Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Get the latest high buyback rates & featured deals delivered straight to your inbox.',
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->add_control(
            'nl_btn_text',
            array(
                'label'   => __( 'Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Subscribe to Newsletter',
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->add_control(
            'nl_btn_url',
            array(
                'label'   => __( 'Button Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/subcribe' ),
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->add_control(
            'nl_unsub_url',
            array(
                'label'   => __( 'Unsubscribe Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/unsubcribe' ),
                'condition' => array( 'show_newsletter' => 'yes' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        ?>
        <div class="kaitori-sidebar-newsletter-wrap">
            
            <?php if ( 'yes' === $settings['show_menu'] && ! empty( $settings['menu_links_list'] ) ) : 
                $m_title = ! empty( $settings['menu_title'] ) ? $settings['menu_title'] : 'Menu';
            ?>
                <div class="navigation-box2">
                    <div class="navigation-h2"><i class="fa fa-circle"><span><?php echo esc_html( $m_title ); ?></span></i></div>
                    <ul class="navigation-h3">
                        <?php foreach ( $settings['menu_links_list'] as $m_item ) : 
                            $m_url = ! empty( $m_item['link_url']['url'] ) ? $m_item['link_url']['url'] : '#';
                            $m_txt = ! empty( $m_item['link_title'] ) ? $m_item['link_title'] : '';
                        ?>
                            <li>
                                <a href="<?php echo esc_url( home_url( $m_url ) ); ?>">
                                    <i class="fa fa-caret-right"></i><span><?php echo esc_html( $m_txt ); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_all_class'] ) : 
                $ac_img = ! empty( $settings['all_class_img']['url'] ) ? $settings['all_class_img']['url'] : ( $theme_uri . '/assets/images/navigation/all_class.png' );
                $ac_url = ! empty( $settings['all_class_url']['url'] ) ? $settings['all_class_url']['url'] : '/goods-class/';
            ?>
                <div class="navigation_ad mbtm10">
                    <ul class="gutter-0 img-margin" style="list-style:none; padding:0; margin:0;">
                        <li>
                            <a href="<?php echo esc_url( home_url( $ac_url ) ); ?>">
                                <img src="<?php echo esc_url( $ac_img ); ?>" width="100%" class="img-responsive">
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_bottom_ads'] && ! empty( $settings['bottom_ads_list'] ) ) : ?>
                <div class="navigation-ad nad_2">
                    <ul class="gutter-0 img-margin" style="list-style:none; padding:0; margin:0;">
                        <?php foreach ( $settings['bottom_ads_list'] as $b_ad ) : 
                            $b_img = ! empty( $b_ad['ad_img']['url'] ) ? $b_ad['ad_img']['url'] : '';
                            $b_url = ! empty( $b_ad['ad_url']['url'] ) ? $b_ad['ad_url']['url'] : '#';
                            $b_alt = ! empty( $b_ad['ad_alt'] ) ? $b_ad['ad_alt'] : '';
                            if ( ! $b_img ) continue;
                        ?>
                            <li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left" style="margin-bottom:10px; padding:0;">
                                <a href="<?php echo esc_url( home_url( $b_url ) ); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo esc_url( $b_img ); ?>" alt="<?php echo esc_attr( $b_alt ); ?>" class="img-responsive">
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="clearfix"></div>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_newsletter'] ) : 
                $nl_img   = ! empty( $settings['nl_header_img']['url'] ) ? $settings['nl_header_img']['url'] : ( $theme_uri . '/assets/images/email/weekly.jpg' );
                $nl_info  = ! empty( $settings['nl_info'] ) ? $settings['nl_info'] : 'Get the latest high buyback rates & featured deals delivered straight to your inbox.';
                $nl_btn_t = ! empty( $settings['nl_btn_text'] ) ? $settings['nl_btn_text'] : 'Subscribe to Newsletter';
                $nl_btn_u = ! empty( $settings['nl_btn_url']['url'] ) ? $settings['nl_btn_url']['url'] : '/subcribe';
                $nl_uns_u = ! empty( $settings['nl_unsub_url']['url'] ) ? $settings['nl_unsub_url']['url'] : '/unsubcribe';
            ?>
                <div class="email">
                    <div class="wrapper">
                        <div class="header">
                            <img src="<?php echo esc_url( $nl_img ); ?>" width="100%" />
                        </div>
                        <p class="info"><?php echo esc_html( $nl_info ); ?></p>
                        <div class="msg"><?php _e( 'Subscribe easily using just your email address.', 'kaitoriakiba' ); ?></div>
                        <div style="margin-bottom: 10px">
                            <a class="btn-ys block-btn btn-ys-cor1" style="background-color: red;" href="<?php echo esc_url( home_url( $nl_btn_u ) ); ?>"><?php echo esc_html( $nl_btn_t ); ?></a>
                            <div class="after_content">|</div>
                        </div>
                        <div class="link">
                            <a href="<?php echo esc_url( home_url( $nl_uns_u ) ); ?>"><?php _e( 'Click here to unsubscribe from newsletter', 'kaitoriakiba' ); ?></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}
