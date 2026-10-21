<?php
/**
 * Elementor Sidebar Top Ads & SNS Widget
 * 
 * Renders the authentic top sidebar elements matching index.html:
 * - First-time user guide banner (ad_top_1.jpg)
 * - Online Consultation / Twitter & LINE box
 * - Free consultation box (nav_consult_01.jpg & nav_consult_03.jpg)
 * - Member Rank / Benefits banner (member_rank02.png)
 * - 4 Series side banners (left_iphone.jpg, left_xperia.jpg, left_aquos.jpg, left_galaxy.jpg)
 * 
 * 100% Fully Editable in Elementor.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Sidebar_Ad_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_sidebar_ad';
    }

    public function get_title() {
        return __( 'Sidebar Top Ads & SNS (サイドバー上部広告・SNS)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-banner';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {

        $theme_uri = get_template_directory_uri();

        // 1. Top Banner (First-Time Guide)
        $this->start_controls_section(
            'section_top_ad',
            array(
                'label' => __( '1. Top Guide Banner (初めての方へ)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_top_banner',
            array(
                'label'   => __( 'Show Top Banner', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'top_banner_img',
            array(
                'label'   => __( 'Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/navigation/ad_top_1.jpg',
                ),
                'condition' => array( 'show_top_banner' => 'yes' ),
            )
        );

        $this->add_control(
            'top_banner_url',
            array(
                'label'   => __( 'Banner Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array(
                    'url' => '/sell-apply/',
                ),
                'condition' => array( 'show_top_banner' => 'yes' ),
            )
        );

        $this->add_control(
            'top_banner_alt',
            array(
                'label'   => __( 'Image Alt Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'First-Time User Guide (初めての方へ)',
                'condition' => array( 'show_top_banner' => 'yes' ),
            )
        );

        $this->end_controls_section();

        // 2. SNS & Online Consultation (Twitter / LINE)
        $this->start_controls_section(
            'section_sns',
            array(
                'label' => __( '2. Social / Consultation Box (SNS・お問い合わせ)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_sns_box',
            array(
                'label'   => __( 'Show Social Box', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'sns_top_img',
            array(
                'label'   => __( 'Box Header Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/navigation/zxlx_top.jpg',
                ),
                'condition' => array( 'show_sns_box' => 'yes' ),
            )
        );

        $this->add_control(
            'twitter_img',
            array(
                'label'   => __( 'Twitter Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/navigation/zxlx_twitter.jpg',
                ),
                'condition' => array( 'show_sns_box' => 'yes' ),
            )
        );

        $this->add_control(
            'twitter_url',
            array(
                'label'   => __( 'Twitter URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array(
                    'url' => 'https://twitter.com/kaitoriakiba',
                ),
                'condition' => array( 'show_sns_box' => 'yes' ),
            )
        );

        $this->add_control(
            'line_img',
            array(
                'label'   => __( 'LINE Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/navigation/zxlx_line.jpg',
                ),
                'condition' => array( 'show_sns_box' => 'yes' ),
            )
        );

        $this->add_control(
            'line_url',
            array(
                'label'   => __( 'LINE URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array(
                    'url' => 'https://lin.ee/5wvtHzl',
                ),
                'condition' => array( 'show_sns_box' => 'yes' ),
            )
        );

        $this->end_controls_section();

        // 3. Free Service / Consult Box (無料査定・ご相談)
        $this->start_controls_section(
            'section_consult',
            array(
                'label' => __( '3. Consultation Box (無料査定・お問い合わせ)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_consult_box',
            array(
                'label'   => __( 'Show Consult Box', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'consult_header_img',
            array(
                'label'   => __( 'Header Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/navigation/nav_consult_01.jpg' ),
                'condition' => array( 'show_consult_box' => 'yes' ),
            )
        );

        $this->add_control(
            'consult_btn_img',
            array(
                'label'   => __( 'Button Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/navigation/nav_consult_03.jpg' ),
                'condition' => array( 'show_consult_box' => 'yes' ),
            )
        );

        $this->add_control(
            'consult_url',
            array(
                'label'   => __( 'Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/sell-apply/' ),
                'condition' => array( 'show_consult_box' => 'yes' ),
            )
        );

        $this->end_controls_section();

        // 4. Member Rank Banner (会員ランク特典)
        $this->start_controls_section(
            'section_member_rank',
            array(
                'label' => __( '4. Member Benefits Banner (会員特典バナー)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_member_banner',
            array(
                'label'   => __( 'Show Member Banner', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'member_banner_img',
            array(
                'label'   => __( 'Member Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/upload/ad/20210621/member_rank02.png',
                ),
                'condition' => array( 'show_member_banner' => 'yes' ),
            )
        );

        $this->add_control(
            'member_banner_url',
            array(
                'label'   => __( 'Member Banner URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array(
                    'url' => '/register/',
                ),
                'condition' => array( 'show_member_banner' => 'yes' ),
            )
        );

        $this->add_control(
            'member_banner_alt',
            array(
                'label'   => __( 'Member Banner Alt', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Member Benefits & Rank System (会員ランク特典)',
                'condition' => array( 'show_member_banner' => 'yes' ),
            )
        );

        $this->end_controls_section();

        // 5. Featured Series Side Banners (iPhone, Xperia, AQUOS, Galaxy)
        $this->start_controls_section(
            'section_series_banners',
            array(
                'label' => __( '5. Featured Series Banners (シリーズバナー)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_series_banners',
            array(
                'label'   => __( 'Show Series Banners', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $repeater_series = new \Elementor\Repeater();

        $repeater_series->add_control(
            'banner_image',
            array(
                'label'   => __( 'Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/upload/ad/20180508/left_iphone.jpg' ),
            )
        );

        $repeater_series->add_control(
            'banner_url',
            array(
                'label'   => __( 'Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/devices/?series=iphone' ),
            )
        );

        $repeater_series->add_control(
            'banner_alt',
            array(
                'label'   => __( 'Alt Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'iPhone Buyback Rates',
            )
        );

        $this->add_control(
            'series_banners_list',
            array(
                'label'       => __( 'Series Banners', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_series->get_controls(),
                'default'     => array(
                    array(
                        'banner_image' => array( 'url' => $theme_uri . '/upload/ad/20180508/left_iphone.jpg' ),
                        'banner_url'   => array( 'url' => '/devices/?series=iphone' ),
                        'banner_alt'   => 'iPhone Buyback Rates',
                    ),
                    array(
                        'banner_image' => array( 'url' => $theme_uri . '/upload/ad/20180508/left_xperia.jpg' ),
                        'banner_url'   => array( 'url' => '/devices/?series=xperia' ),
                        'banner_alt'   => 'Xperia Buyback Price Chart',
                    ),
                    array(
                        'banner_image' => array( 'url' => $theme_uri . '/upload/ad/20180508/left_aquos.jpg' ),
                        'banner_url'   => array( 'url' => '/devices/?series=aquos' ),
                        'banner_alt'   => 'AQUOS Buyback Rates',
                    ),
                    array(
                        'banner_image' => array( 'url' => $theme_uri . '/upload/ad/20180508/left_galaxy.jpg' ),
                        'banner_url'   => array( 'url' => '/devices/?series=galaxy' ),
                        'banner_alt'   => 'Galaxy Buyback Rates',
                    ),
                ),
                'title_field' => '{{{ banner_alt }}}',
                'condition'   => array( 'show_series_banners' => 'yes' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        ?>
        <div class="navigation-ad kaitori-sidebar-ads-wrap" style="margin-bottom: 20px;">
            <ul class="gutter-0 img-margin" style="list-style:none; padding:0; margin:0;">
                
                <?php if ( 'yes' === $settings['show_top_banner'] ) : 
                    $ad1_img = ! empty( $settings['top_banner_img']['url'] ) ? $settings['top_banner_img']['url'] : ( $theme_uri . '/assets/images/navigation/ad_top_1.jpg' );
                    $ad1_url = ! empty( $settings['top_banner_url']['url'] ) ? $settings['top_banner_url']['url'] : '/sell-apply/';
                    $ad1_alt = ! empty( $settings['top_banner_alt'] ) ? $settings['top_banner_alt'] : 'First-Time Guide';
                ?>
                    <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 img-left" style="margin-bottom:10px; padding:0;">
                        <a href="<?php echo esc_url( $ad1_url ); ?>">
                            <img src="<?php echo esc_url( $ad1_img ); ?>" alt="<?php echo esc_attr( $ad1_alt ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:8px;">
                        </a>
                    </li>
                    <div class="clear" style="clear:both;"></div>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_sns_box'] ) : 
                    $sns_top = ! empty( $settings['sns_top_img']['url'] ) ? $settings['sns_top_img']['url'] : ( $theme_uri . '/assets/images/navigation/zxlx_top.jpg' );
                    $tw_img  = ! empty( $settings['twitter_img']['url'] ) ? $settings['twitter_img']['url'] : ( $theme_uri . '/assets/images/navigation/zxlx_twitter.jpg' );
                    $tw_url  = ! empty( $settings['twitter_url']['url'] ) ? $settings['twitter_url']['url'] : 'https://twitter.com/kaitoriakiba';
                    $ln_img  = ! empty( $settings['line_img']['url'] ) ? $settings['line_img']['url'] : ( $theme_uri . '/assets/images/navigation/zxlx_line.jpg' );
                    $ln_url  = ! empty( $settings['line_url']['url'] ) ? $settings['line_url']['url'] : 'https://lin.ee/5wvtHzl';
                ?>
                    <li class="zxlx_nav_box" style="margin:0 0 10px 0; width:100%; list-style:none;">
                        <div><img src="<?php echo esc_url( $sns_top ); ?>" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" /></div>
                        <div><a href="<?php echo esc_url( $tw_url ); ?>" target="_blank"><img src="<?php echo esc_url( $tw_img ); ?>" alt="Twitter" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" border="0"></a></div>
                        <div><a href="<?php echo esc_url( $ln_url ); ?>" target="_blank"><img src="<?php echo esc_url( $ln_img ); ?>" alt="Add on LINE" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;" border="0"></a></div>
                    </li>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_consult_box'] ) : 
                    $c_hdr = ! empty( $settings['consult_header_img']['url'] ) ? $settings['consult_header_img']['url'] : ( $theme_uri . '/assets/images/navigation/nav_consult_01.jpg' );
                    $c_btn = ! empty( $settings['consult_btn_img']['url'] ) ? $settings['consult_btn_img']['url'] : ( $theme_uri . '/assets/images/navigation/nav_consult_03.jpg' );
                    $c_url = ! empty( $settings['consult_url']['url'] ) ? $settings['consult_url']['url'] : '/sell-apply/';
                ?>
                    <li class="free_service" style="list-style:none; margin-bottom:10px;">
                        <div class="consult">
                            <div class="consult_header">
                                <img src="<?php echo esc_url( $c_hdr ); ?>" width="100%" class="img-responsive" /> 
                            </div>
                            <div class="content">
                                <a href="<?php echo esc_url( $c_url ); ?>">
                                    <img src="<?php echo esc_url( $c_btn ); ?>" width="100%" class="img-responsive" />
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_member_banner'] ) : 
                    $mem_img = ! empty( $settings['member_banner_img']['url'] ) ? $settings['member_banner_img']['url'] : ( $theme_uri . '/upload/ad/20210621/member_rank02.png' );
                    $mem_url = ! empty( $settings['member_banner_url']['url'] ) ? $settings['member_banner_url']['url'] : '/register/';
                    $mem_alt = ! empty( $settings['member_banner_alt'] ) ? $settings['member_banner_alt'] : 'Member Benefits';
                ?>
                    <li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left" style="margin-bottom:10px; padding:0;">
                        <a href="<?php echo esc_url( $mem_url ); ?>">
                            <img src="<?php echo esc_url( $mem_img ); ?>" alt="<?php echo esc_attr( $mem_alt ); ?>" class="img-responsive col-center" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important; border-radius:8px;">
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_series_banners'] && ! empty( $settings['series_banners_list'] ) ) : ?>
                    <?php foreach ( $settings['series_banners_list'] as $sb ) : 
                        $sb_img = ! empty( $sb['banner_image']['url'] ) ? $sb['banner_image']['url'] : '';
                        $sb_url = ! empty( $sb['banner_url']['url'] ) ? $sb['banner_url']['url'] : '#';
                        $sb_alt = ! empty( $sb['banner_alt'] ) ? $sb['banner_alt'] : '';
                        if ( ! $sb_img ) continue;
                    ?>
                        <li class="col-lg-12 col-md-12 col-sm-12 col-xs-6 img-left" style="margin-bottom:10px; padding:0;">
                            <a href="<?php echo esc_url( $sb_url ); ?>">
                                <img src="<?php echo esc_url( $sb_img ); ?>" alt="<?php echo esc_attr( $sb_alt ); ?>" class="img-responsive col-center" style="width:100% !important; max-width:100% !important; margin:0 !important; display:block !important;">
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>

            </ul>
            <div class="clearfix" style="clear:both;"></div>
        </div>
        <?php
    }
}
