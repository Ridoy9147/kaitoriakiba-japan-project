<?php
/**
 * Elementor Hero Nav Banners & Swiper Slider Widget
 * 
 * 100% Fully Editable in Elementor:
 * - 2-Column Hero Banners with customizable top images, button badges, and links
 * - Full-width Promo Poster with customizable banner image and link
 * - Swiper Slider with repeater for slide images and links
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

        $theme_uri = get_template_directory_uri();

        // =====================================================================
        // SECTION 1: 2-COLUMN HERO NAV BANNERS
        // =====================================================================
        $this->start_controls_section(
            'section_hero_nav',
            array(
                'label' => __( '1. Dual Hero Banners (2分割メインバナー)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_hero_nav',
            array(
                'label'   => __( 'Show Dual Hero Banners', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        // Banner 1 (Left - iPhone)
        $this->add_control(
            'banner1_heading',
            array(
                'label'     => __( 'Left Banner (iPhone)', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array( 'show_hero_nav' => 'yes' ),
            )
        );
        $this->add_control(
            'banner1_top_image',
            array(
                'label'   => __( 'Top Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/maintop/nav_img_1.jpg' ),
                'condition' => array( 'show_hero_nav' => 'yes' ),
            )
        );
        $this->add_control(
            'banner1_btn_image',
            array(
                'label'   => __( 'Button Badge Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/maintop/nav_btn_1.png' ),
                'condition' => array( 'show_hero_nav' => 'yes' ),
            )
        );
        $this->add_control(
            'banner1_url',
            array(
                'label'   => __( 'Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/devices/?cat=iphone' ),
                'condition' => array( 'show_hero_nav' => 'yes' ),
            )
        );

        // Banner 2 (Right - Android / Other)
        $this->add_control(
            'banner2_heading',
            array(
                'label'     => __( 'Right Banner (Smartphones)', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array( 'show_hero_nav' => 'yes' ),
            )
        );
        $this->add_control(
            'banner2_top_image',
            array(
                'label'   => __( 'Top Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/maintop/nav_img_2.jpg' ),
                'condition' => array( 'show_hero_nav' => 'yes' ),
            )
        );
        $this->add_control(
            'banner2_btn_image',
            array(
                'label'   => __( 'Button Badge Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/maintop/nav_btn_2.png' ),
                'condition' => array( 'show_hero_nav' => 'yes' ),
            )
        );
        $this->add_control(
            'banner2_url',
            array(
                'label'   => __( 'Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/devices/' ),
                'condition' => array( 'show_hero_nav' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 2: PROMO BANNER POSTER
        // =====================================================================
        $this->start_controls_section(
            'section_promo_poster',
            array(
                'label' => __( '2. Promo Poster Banner (キャンペーンバナー)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_promo_poster',
            array(
                'label'   => __( 'Show Promo Poster', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'poster_image',
            array(
                'label'   => __( 'Poster Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/upload/ad/20251229/63a58246c446bf1710aa78f25c4b2619.png' ),
                'condition' => array( 'show_promo_poster' => 'yes' ),
            )
        );

        $this->add_control(
            'poster_url',
            array(
                'label'   => __( 'Poster Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/sell-apply/?delivery=mail' ),
                'condition' => array( 'show_promo_poster' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 3: SWIPER SLIDER
        // =====================================================================
        $this->start_controls_section(
            'section_swiper',
            array(
                'label' => __( '3. Swiper Banner Slider (スライダーバナー)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
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

        $repeater_slides = new \Elementor\Repeater();

        $repeater_slides->add_control(
            'slide_title',
            array(
                'label'       => __( 'Slide Title / Label', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'Featured Promotion',
                'label_block' => true,
            )
        );

        $repeater_slides->add_control(
            'slide_image',
            array(
                'label'   => __( 'Slide Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/upload/ad/20260226/iPad.png' ),
            )
        );

        $repeater_slides->add_control(
            'slide_url',
            array(
                'label'   => __( 'Slide Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/devices/' ),
            )
        );

        $this->add_control(
            'swiper_slides_list',
            array(
                'label'       => __( 'Slider Slides', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_slides->get_controls(),
                'default'     => array(
                    array(
                        'slide_title' => 'iPad & Tablet Buyback',
                        'slide_image' => array( 'url' => $theme_uri . '/upload/ad/20260226/iPad.png' ),
                        'slide_url'   => array( 'url' => '/devices/?cat=tablets' ),
                    ),
                    array(
                        'slide_title' => 'Apple Watch Buyback Special',
                        'slide_image' => array( 'url' => $theme_uri . '/upload/ad/20260114/Watch.png' ),
                        'slide_url'   => array( 'url' => '/devices/?cat=watches' ),
                    ),
                    array(
                        'slide_title' => 'Gaming Consoles Buyback Rates',
                        'slide_image' => array( 'url' => $theme_uri . '/upload/ad/20260210/Switch.png' ),
                        'slide_url'   => array( 'url' => '/devices/?cat=games' ),
                    ),
                ),
                'title_field' => '{{{ slide_title }}}',
                'condition'   => array( 'show_swiper_slider' => 'yes' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        ?>
        <div class="maintop" style="margin-top: 15px;">
            <?php if ( 'yes' === $settings['show_hero_nav'] ) : 
                $b1_top = ! empty( $settings['banner1_top_image']['url'] ) ? $settings['banner1_top_image']['url'] : $theme_uri . '/assets/images/maintop/nav_img_1.jpg';
                $b1_btn = ! empty( $settings['banner1_btn_image']['url'] ) ? $settings['banner1_btn_image']['url'] : $theme_uri . '/assets/images/maintop/nav_btn_1.png';
                $b1_url = ! empty( $settings['banner1_url']['url'] ) ? $settings['banner1_url']['url'] : home_url( '/devices/?cat=iphone' );

                $b2_top = ! empty( $settings['banner2_top_image']['url'] ) ? $settings['banner2_top_image']['url'] : $theme_uri . '/assets/images/maintop/nav_img_2.jpg';
                $b2_btn = ! empty( $settings['banner2_btn_image']['url'] ) ? $settings['banner2_btn_image']['url'] : $theme_uri . '/assets/images/maintop/nav_btn_2.png';
                $b2_url = ! empty( $settings['banner2_url']['url'] ) ? $settings['banner2_url']['url'] : home_url( '/devices/' );
            ?>
                <div class="container pding0">
                    <div class="row">
                        <div class="m-img-nav col-lg-6 col-md-6 col-sm-6 col-xs-6" style="padding-right:8px;">
                            <a href="<?php echo esc_url( $b1_url ); ?>"><img src="<?php echo esc_url( $b1_top ); ?>" class="img-responsive mbtm15" alt="iPhone Buyback" style="width:100%; border-radius:6px;"></a>
                            <a href="<?php echo esc_url( $b1_url ); ?>"><img src="<?php echo esc_url( $b1_btn ); ?>" class="img-responsive" alt="iPhone Rates" style="width:100%;"></a>
                        </div>
                        <div class="m-img-nav col-lg-6 col-md-6 col-sm-6 col-xs-6" style="padding-left:8px;">
                            <a href="<?php echo esc_url( $b2_url ); ?>"><img src="<?php echo esc_url( $b2_top ); ?>" class="img-responsive mbtm15" alt="Smartphone Buyback" style="width:100%; border-radius:6px;"></a>
                            <a href="<?php echo esc_url( $b2_url ); ?>"><img src="<?php echo esc_url( $b2_btn ); ?>" class="img-responsive" alt="Smartphone Rates" style="width:100%;"></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Promo Banner Poster -->
            <?php if ( 'yes' === $settings['show_promo_poster'] ) : 
                $poster_img = ! empty( $settings['poster_image']['url'] ) ? $settings['poster_image']['url'] : $theme_uri . '/upload/ad/20251229/63a58246c446bf1710aa78f25c4b2619.png';
                $poster_url = ! empty( $settings['poster_url']['url'] ) ? $settings['poster_url']['url'] : home_url( '/sell-apply/?delivery=mail' );
            ?>
                <div class="container pding0" style="margin-top:10px;">
                    <a href="<?php echo esc_url( $poster_url ); ?>">
                        <img src="<?php echo esc_url( $poster_img ); ?>" alt="Free Shipping Mail-in Buyback" class="img-responsive col-center poster" style="width:100%; max-width:100%; border-radius:6px; display:block;"/>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Swiper Slider -->
            <?php if ( 'yes' === $settings['show_swiper_slider'] && ! empty( $settings['swiper_slides_list'] ) ) : ?>
                <div class="container pding0" style="margin-top:15px; margin-bottom:20px;">
                    <div class="mainbanner visible-lg visible-md">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php foreach ( $settings['swiper_slides_list'] as $slide ) : 
                                    $s_img = ! empty( $slide['slide_image']['url'] ) ? $slide['slide_image']['url'] : '';
                                    $s_url = ! empty( $slide['slide_url']['url'] ) ? $slide['slide_url']['url'] : '#';
                                    if ( ! $s_img ) continue;
                                ?>
                                    <div class="swiper-slide">
                                        <a href="<?php echo esc_url( $s_url ); ?>">
                                            <img src="<?php echo esc_url( $s_img ); ?>" alt="<?php echo esc_attr( $slide['slide_title'] ); ?>" class="img-responsive" style="width:100%; border-radius:6px;">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
