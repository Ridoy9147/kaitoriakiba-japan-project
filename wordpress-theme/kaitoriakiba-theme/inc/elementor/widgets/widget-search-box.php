<?php
/**
 * Elementor Dual Search Box Widget
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Search_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_search_box';
    }

    public function get_title() {
        return __( 'Dual Search Box (検索ボックス)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-search';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        // Section: Keyword Search Content
        $this->start_controls_section(
            'section_keyword_search',
            array(
                'label' => __( 'Keyword Search (キーワード検索)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'kw_title',
            array(
                'label'       => __( 'Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'Search by device model name or model number!', 'kaitoriakiba' ),
                'label_block' => true,
            )
        );

        $this->add_control(
            'kw_subtitle',
            array(
                'label'       => __( 'Subtitle', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'Please enter device name, model number, JAN code, etc.', 'kaitoriakiba' ),
                'label_block' => true,
            )
        );

        $this->add_control(
            'kw_button_text',
            array(
                'label'   => __( 'Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Search', 'kaitoriakiba' ),
            )
        );

        $this->end_controls_section();

        // Section: Step Search Content
        $this->start_controls_section(
            'section_step_search',
            array(
                'label' => __( 'Step-by-Step Search (絞り込み検索)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'step_title',
            array(
                'label'       => __( 'Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'Step-by-Step Category Search', 'kaitoriakiba' ),
                'label_block' => true,
            )
        );

        $this->add_control(
            'step_subtitle',
            array(
                'label'       => __( 'Subtitle', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => __( 'Select one or more categories (Multiple selection supported)', 'kaitoriakiba' ),
                'label_block' => true,
            )
        );

        $this->add_control(
            'step_button_text',
            array(
                'label'   => __( 'Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Search', 'kaitoriakiba' ),
            )
        );

        $this->end_controls_section();

        // Section: Popular Keywords
        $this->start_controls_section(
            'section_popular_keywords',
            array(
                'label' => __( 'Popular Keywords (人気キーワード)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'keyword',
            array(
                'label'       => __( 'Keyword', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'iPhone 16',
                'label_block' => true,
            )
        );

        $this->add_control(
            'keywords_list',
            array(
                'label'       => __( 'Keywords', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => array(
                    array( 'keyword' => 'iPhone 16' ),
                    array( 'keyword' => 'iPhone 16 Pro' ),
                    array( 'keyword' => 'iPhone 15' ),
                    array( 'keyword' => 'Pixel 8' ),
                    array( 'keyword' => 'iPad' ),
                    array( 'keyword' => 'Nintendo switch' ),
                ),
                'title_field' => '{{{ keyword }}}',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $kw_title       = ! empty( $settings['kw_title'] ) ? $settings['kw_title'] : __( 'Search by device model name or model number!', 'kaitoriakiba' );
        $kw_subtitle    = ! empty( $settings['kw_subtitle'] ) ? $settings['kw_subtitle'] : __( 'Please enter device name, model number, JAN code, etc.', 'kaitoriakiba' );
        $kw_btn_text    = ! empty( $settings['kw_button_text'] ) ? $settings['kw_button_text'] : __( 'Search', 'kaitoriakiba' );
        $step_title     = ! empty( $settings['step_title'] ) ? $settings['step_title'] : __( 'Step-by-Step Category Search', 'kaitoriakiba' );
        $step_subtitle  = ! empty( $settings['step_subtitle'] ) ? $settings['step_subtitle'] : __( 'Select one or more categories (Multiple selection supported)', 'kaitoriakiba' );
        $step_btn_text  = ! empty( $settings['step_button_text'] ) ? $settings['step_button_text'] : __( 'Search', 'kaitoriakiba' );

        $keywords_list  = ! empty( $settings['keywords_list'] ) && is_iterable( $settings['keywords_list'] ) ? $settings['keywords_list'] : array(
            array( 'keyword' => 'iPhone 16' ),
            array( 'keyword' => 'iPhone 16 Pro' ),
            array( 'keyword' => 'iPhone 15' ),
            array( 'keyword' => 'Pixel 8' ),
            array( 'keyword' => 'iPad' ),
            array( 'keyword' => 'Nintendo switch' ),
        );

        $categories = get_terms( array( 'taxonomy' => 'device_category', 'hide_empty' => false ) );
        $carriers   = get_terms( array( 'taxonomy' => 'carrier', 'hide_empty' => false ) );
        $brands     = get_terms( array( 'taxonomy' => 'brand', 'hide_empty' => false ) );
        $series_list= get_terms( array( 'taxonomy' => 'series', 'hide_empty' => false ) );
        ?>
        <div class="container search" style="margin-top: 15px; margin-bottom: 25px;">
            <div class="row">
                <!-- 1. Keyword Search -->
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="search_title">
                        <div class="search_title_h2"><?php echo esc_html( $kw_title ); ?></div>
                        <div class="search_title_h3"><?php echo esc_html( $kw_subtitle ); ?></div>
                    </div>
                    <div class="search_box">
                        <form class="m_fullsearch" method="get" action="<?php echo esc_url( home_url( '/devices/' ) ); ?>">
                            <div class="form-group mbtm0 clearfix">
                                <div class="keyword_search">
                                    <select class="form-control fw_cat" name="category">
                                        <option value=""><?php _e( 'All Categories', 'kaitoriakiba' ); ?></option>
                                        <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                                            <?php foreach ( $categories as $cat ) : ?>
                                                <option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <option value="smartphones"><?php _e( 'Smartphones', 'kaitoriakiba' ); ?></option>
                                            <option value="tablets"><?php _e( 'Tablets', 'kaitoriakiba' ); ?></option>
                                            <option value="gaming"><?php _e( 'Gaming Consoles', 'kaitoriakiba' ); ?></option>
                                            <option value="appliances"><?php _e( 'Home Appliances', 'kaitoriakiba' ); ?></option>
                                            <option value="cameras"><?php _e( 'Cameras', 'kaitoriakiba' ); ?></option>
                                            <option value="pc"><?php _e( 'PC & Laptops', 'kaitoriakiba' ); ?></option>
                                        <?php endif; ?>
                                    </select>
                                    <input type="text" class="form-control keyword_search_text q" name="s" placeholder="<?php esc_attr_e( 'Keyword search (2+ characters)', 'kaitoriakiba' ); ?>" required="required" autocomplete="off" />
                                    <button class="btn search_word" type="submit"><?php echo esc_html( $kw_btn_text ); ?> <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                        <?php if ( ! empty( $keywords_list ) ) : ?>
                            <div class="title" style="margin-top: 10px; color: #ffffff;">
                                <?php _e( 'Popular Search Keywords!', 'kaitoriakiba' ); ?>
                                <ul class="hotword" style="margin-top: 6px;">
                                    <?php foreach ( $keywords_list as $kw_item ) : 
                                        $kw_name = isset( $kw_item['keyword'] ) ? $kw_item['keyword'] : '';
                                        if ( empty( $kw_name ) ) continue;
                                    ?>
                                        <li><a href="<?php echo esc_url( home_url( '/devices/?s=' . urlencode( $kw_name ) ) ); ?>"><?php echo esc_html( $kw_name ); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. Step-by-Step Category Search -->
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="search_title">
                        <div class="search_title_h2"><?php echo esc_html( $step_title ); ?></div>
                        <div class="search_title_h3"><?php echo esc_html( $step_subtitle ); ?></div>
                    </div>
                    <div class="search_box">
                        <form class="snssearch" method="get" action="<?php echo esc_url( home_url( '/devices/' ) ); ?>">
                            <div class="form-group">
                                <div class="step_search">
                                    <div class="step_search_filters">
                                        <select class="form-control s_cat" name="category">
                                            <option value=""><?php _e( 'Select Category', 'kaitoriakiba' ); ?></option>
                                            <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                                                <?php foreach ( $categories as $cat ) : ?>
                                                    <option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <option value="iphone">iPhone</option>
                                                <option value="smartphones">Smartphones</option>
                                                <option value="accessories">Accessories</option>
                                                <option value="routers">Mobile Routers</option>
                                            <?php endif; ?>
                                        </select>
                                        <select class="form-control s_career" name="carrier">
                                            <option value=""><?php _e( 'By Carrier', 'kaitoriakiba' ); ?></option>
                                            <?php if ( ! empty( $carriers ) && ! is_wp_error( $carriers ) ) : ?>
                                                <?php foreach ( $carriers as $car ) : ?>
                                                    <option value="<?php echo esc_attr( $car->slug ); ?>"><?php echo esc_html( $car->name ); ?></option>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <option value="docomo">docomo</option>
                                                <option value="au">au</option>
                                                <option value="softbank">SoftBank</option>
                                                <option value="ymobile">Y!mobile</option>
                                                <option value="uqmobile">UQ mobile</option>
                                                <option value="simfree">SIM FREE</option>
                                                <option value="rakuten">RakutenMobile</option>
                                            <?php endif; ?>
                                        </select>
                                        <select class="form-control s_brand" name="brand">
                                            <option value=""><?php _e( 'By Brand', 'kaitoriakiba' ); ?></option>
                                            <?php if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) : ?>
                                                <?php foreach ( $brands as $b ) : ?>
                                                    <option value="<?php echo esc_attr( $b->slug ); ?>"><?php echo esc_html( $b->name ); ?></option>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <option value="apple">Apple</option>
                                                <option value="xiaomi">Xiaomi</option>
                                                <option value="oppo">OPPO</option>
                                                <option value="asus">ASUS</option>
                                                <option value="fujitsu">FUJITSU</option>
                                                <option value="samsung">SAMSUNG</option>
                                                <option value="google">Google</option>
                                                <option value="huawei">HUAWEI</option>
                                                <option value="kyocera">KYOCERA</option>
                                                <option value="motorola">MOTOROLA</option>
                                                <option value="sharp">SHARP</option>
                                                <option value="sony">SONY</option>
                                            <?php endif; ?>
                                        </select>
                                        <select class="form-control s_series" name="series">
                                            <option value=""><?php _e( 'By Series', 'kaitoriakiba' ); ?></option>
                                            <?php if ( ! empty( $series_list ) && ! is_wp_error( $series_list ) ) : ?>
                                                <?php foreach ( $series_list as $ser ) : ?>
                                                    <option value="<?php echo esc_attr( $ser->slug ); ?>"><?php echo esc_html( $ser->name ); ?></option>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <option value="iphone">iPhone</option>
                                                <option value="xperia">Xperia</option>
                                                <option value="galaxy">Galaxy</option>
                                                <option value="arrows">arrows</option>
                                                <option value="aquos">AQUOS</option>
                                                <option value="zenfone">Zenfone</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <button class="btn step_search_btn" type="submit"><?php echo esc_html( $step_btn_text ); ?> <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
