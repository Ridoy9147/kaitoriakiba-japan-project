<?php
/**
 * Elementor Hero Nav Banners & Swiper Slider Widget
 * 
 * Renders the authentic m-img-nav hero banners and Swiper mainbanner slider from index.html.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Hero_Banner_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_hero_banner';
    }

    public function get_title() {
        return __( 'Hero Banners & Slider (メインバナー・スライダー)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-slides';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Hero Display Settings', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_hero_nav',
            array(
                'label'   => __( 'Show 2-Column Hero Banners', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'show_swiper_slider',
            array(
                'label'   => __( 'Show Swiper Banner Slider', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        ?>
        <div class="maintop" style="margin-top: 15px;">
            <?php if ( 'yes' === $settings['show_hero_nav'] ) : ?>
                <div class="container">
                    <div class="row">
                        <div class="m-img-nav col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/maintop/nav_img_1.jpg" class="img-responsive mbtm15" alt="iPhone Buyback Price List"></a>
                            <a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/maintop/nav_btn_1.png" class="img-responsive" alt="iPhone Buyback Chart"></a>
                        </div>
                        <div class="m-img-nav col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/maintop/nav_img_2.jpg" class="img-responsive mbtm15" alt="Smartphone Buyback List by Brand"></a>
                            <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/maintop/nav_btn_2.png" class="img-responsive" alt="Smartphone Buyback Rates"></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <!-- Promo Banner Posters -->
                <div class="container pding0">
                    <ul style="list-style:none; padding:0; margin:0 0 10px 0;">
                        <li style="margin-bottom:8px;">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>">
                                <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20251229/63a58246c446bf1710aa78f25c4b2619.png" alt="Free Shipping Mail-in Buyback Terms" class="img-responsive col-center poster" style="width:100%; max-width:100%; border-radius:6px;"/>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_swiper_slider'] ) : ?>
                <div class="container pding0">
                    <div class="mainbanner visible-lg visible-md" style="margin-bottom:20px;">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <a href="<?php echo esc_url( home_url( '/devices/?cat=tablets' ) ); ?>">
                                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180508/mainbanner-1.jpg" alt="iPad High Buyback Rate Chart" class="img-responsive" style="width:100%; border-radius:8px;">
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="<?php echo esc_url( home_url( '/devices/?cat=gaming' ) ); ?>">
                                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20180508/mainbanner-3.jpg" alt="Gaming Consoles Enhanced Buyback Rates" class="img-responsive" style="width:100%; border-radius:8px;">
                                    </a>
                                </div>
                                <div class="swiper-slide">
                                    <a href="https://lin.ee/5wvtHzl" target="_blank">
                                        <img src="<?php echo esc_url( $theme_uri ); ?>/upload/ad/20220222/450216 Kaitori Akibaスライドバナー.png" alt="LINE Official Support" class="img-responsive" style="width:100%; border-radius:8px;">
                                    </a>
                                </div>
                            </div>
                            <div class="swiper-pagination-md"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
