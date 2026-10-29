<?php
/**
 * Elementor Device Buyback Grid Widget
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Device_Grid_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_device_grid';
    }

    public function get_title() {
        return __( 'Device Buyback Grid (買取機種グリッド)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-products-grid';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        // Query Section
        $this->start_controls_section(
            'section_query',
            array(
                'label' => __( 'Query & Filter', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'posts_per_page',
            array(
                'label'   => __( 'Number of Devices', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 8,
                'min'     => 1,
                'max'     => 48,
            )
        );

        $this->add_control(
            'columns',
            array(
                'label'   => __( 'Columns', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '4',
                'options' => array(
                    '2' => __( '2 Columns', 'kaitoriakiba' ),
                    '3' => __( '3 Columns', 'kaitoriakiba' ),
                    '4' => __( '4 Columns (Recommended)', 'kaitoriakiba' ),
                    '6' => __( '6 Columns', 'kaitoriakiba' ),
                ),
            )
        );

        $this->add_control(
            'orderby',
            array(
                'label'   => __( 'Order By', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => array(
                    'date'       => __( 'Date Added', 'kaitoriakiba' ),
                    'meta_value_num' => __( 'Price (Highest)', 'kaitoriakiba' ),
                    'title'      => __( 'Device Title', 'kaitoriakiba' ),
                    'rand'       => __( 'Random', 'kaitoriakiba' ),
                ),
            )
        );

        $this->add_control(
            'order',
            array(
                'label'   => __( 'Order', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => array(
                    'DESC' => __( 'Descending (DESC)', 'kaitoriakiba' ),
                    'ASC'  => __( 'Ascending (ASC)', 'kaitoriakiba' ),
                ),
            )
        );

        $this->add_control(
            'filter_brand',
            array(
                'label'       => __( 'Filter by Brand Slug (Optional)', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'apple',
            )
        );

        $this->add_control(
            'filter_category',
            array(
                'label'       => __( 'Filter by Category Slug (Optional)', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'iphone',
            )
        );

        $this->end_controls_section();

        // Section Header Controls
        $this->start_controls_section(
            'section_header_opts',
            array(
                'label' => __( 'Section Header', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_section_header',
            array(
                'label'        => __( 'Show Section Header', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Show', 'kaitoriakiba' ),
                'label_off'    => __( 'Hide', 'kaitoriakiba' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'section_badge',
            array(
                'label'       => __( 'Badge Text', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '高価買取強化中',
                'condition'   => array( 'show_section_header' => 'yes' ),
            )
        );

        $this->add_control(
            'section_title',
            array(
                'label'       => __( 'Section Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '買取強化商品一覧',
                'condition'   => array( 'show_section_header' => 'yes' ),
            )
        );

        $this->add_control(
            'section_subtitle',
            array(
                'label'       => __( 'Section Subtitle', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'iPhone・最新スマホ・タブレットなど高額買取対象モデル',
                'condition'   => array( 'show_section_header' => 'yes' ),
            )
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'section_display_opts',
            array(
                'label' => __( 'Display Options', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'button_text',
            array(
                'label'   => __( 'Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'Apply for Buyback', 'kaitoriakiba' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $posts_per_page = ! empty( $settings['posts_per_page'] ) ? intval( $settings['posts_per_page'] ) : 8;
        $orderby        = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
        $order          = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';
        $columns        = ! empty( $settings['columns'] ) ? $settings['columns'] : '4';
        $btn_text       = ! empty( $settings['button_text'] ) ? $settings['button_text'] : __( 'Apply for Buyback', 'kaitoriakiba' );
        $show_header    = isset( $settings['show_section_header'] ) ? $settings['show_section_header'] : 'yes';
        $section_badge  = ! empty( $settings['section_badge'] ) ? $settings['section_badge'] : '高価買取強化中';
        $section_title  = ! empty( $settings['section_title'] ) ? $settings['section_title'] : '買取強化商品一覧';
        $section_sub    = ! empty( $settings['section_subtitle'] ) ? $settings['section_subtitle'] : 'iPhone・最新スマホ・タブレットなど高額買取対象モデル';

        $args = array(
            'post_type'      => 'kaitori_device',
            'posts_per_page' => $posts_per_page,
            'post_status'    => 'publish',
            'orderby'        => $orderby,
            'order'          => $order,
        );

        if ( $orderby === 'meta_value_num' ) {
            $args['meta_key'] = '_max_buyback_price';
        }

        $tax_query = array();
        if ( ! empty( $settings['filter_brand'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'brand',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $settings['filter_brand'] ),
            );
        }
        if ( ! empty( $settings['filter_category'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'device_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $settings['filter_category'] ),
            );
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        $query = new \WP_Query( $args );

        // Determine Bootstrap column class
        $col_class = 'col-lg-3 col-md-3 col-sm-4 col-xs-12';
        if ( $columns === '2' ) {
            $col_class = 'col-lg-6 col-md-6 col-sm-6 col-xs-12';
        } elseif ( $columns === '3' ) {
            $col_class = 'col-lg-4 col-md-4 col-sm-6 col-xs-12';
        } elseif ( $columns === '6' ) {
            $col_class = 'col-lg-2 col-md-3 col-sm-4 col-xs-6';
        }
        $theme_uri = get_template_directory_uri();
        ?>
        <div class="mainpage-box xs-pad0 kaitori-device-grid-wrapper">
            <div class="sub-box" id="layout_y">
                <?php if ( 'yes' === $show_header ) : ?>
                    <div class="kaitori-section-header" style="text-align: center; margin-bottom: 25px; padding: 15px 0;">
                        <div class="section-title-wrap" style="display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap; margin-bottom: 8px;">
                            <?php if ( ! empty( $section_badge ) ) : ?>
                                <div class="section-badge" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; font-size: 12px; font-weight: 800; padding: 4px 14px; border-radius: 20px; letter-spacing: 0.5px; margin: 0; line-height: 1.4; vertical-align: middle; box-sizing: border-box;">
                                    <span class="badge-dot" style="width: 7px; height: 7px; background-color: #ef4444; border-radius: 50%; display: inline-block;"></span>
                                    <span class="badge-text" style="display: inline-block; line-height: 1;"><?php echo esc_html( $section_badge ); ?></span>
                                </div>
                            <?php endif; ?>
                            <h2 class="section-title" style="display: inline-flex; align-items: center; font-size: 24px; font-weight: 900; color: #0f172a; margin: 0; padding: 0; line-height: 1.2; letter-spacing: -0.5px; vertical-align: middle;">
                                <span class="title-highlight" style="background: linear-gradient(135deg, #0f172a 0%, #dc2626 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block; line-height: 1.2;"><?php echo esc_html( $section_title ); ?></span>
                            </h2>
                        </div>
                        <?php if ( ! empty( $section_sub ) ) : ?>
                            <p class="section-subtitle" style="font-size: 13px; color: #64748b; margin: 0 auto; font-weight: 500; max-width: 600px; line-height: 1.5;"><?php echo esc_html( $section_sub ); ?></p>
                        <?php endif; ?>
                        <div class="section-title-line" style="width: 48px; height: 3px; background: linear-gradient(90deg, #ef4444, #f59e0b); border-radius: 2px; margin: 12px auto 0 auto;"></div>
                    </div>
                <?php endif; ?>
                <div class="sub-pro-box">
                    <div class="row">
                        <?php if ( $query->have_posts() ) : ?>
                            <?php while ( $query->have_posts() ) : $query->the_post(); 
                                $post_id   = get_the_ID();
                                $price     = get_post_meta( $post_id, '_max_buyback_price', true ) ?: '195000';
                                $jan       = get_post_meta( $post_id, '_jan_code', true ) ?: '4549995734140';
                                $brands    = get_the_terms( $post_id, 'brand' );
                                $brand_name= ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0]->name : 'Apple';
                                $permalink = get_permalink();
                                $custom_img= get_post_meta( $post_id, '_device_primary_image_url', true );
                                $thumb_url = ! empty( $custom_img ) ? $custom_img : ( has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'medium' ) : $theme_uri . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png' );
                            ?>
                                <div class="<?php echo esc_attr( $col_class ); ?>">
                                    <div class="pro-border gutter-10 row pro_list" style="padding: 14px 12px 8px 12px;">
                                        <div data-isok="false" data-value="<?php echo esc_attr( $post_id ); ?>" class="collect"></div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-5 text-center pro-img-box" style="padding: 6px 0; margin-bottom: 8px;">
                                            <a href="<?php echo esc_url( $permalink ); ?>">
                                                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="img-responsive" style="max-height: 140px; margin: 0 auto; object-fit: contain;">
                                            </a>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-7 pro-info-box" style="margin-bottom: 0; padding-bottom: 0;">
                                            <ul style="list-style: none; padding: 0; margin: 0;">
                                                <div class="sub-pro" style="margin-bottom: 4px;">
                                                    <li class="sub-pro-career">
                                                        <a href="<?php echo esc_url( home_url( '/devices/?brand=' . urlencode( $brand_name ) ) ); ?>"><?php echo esc_html( $brand_name ); ?></a>
                                                    </li>
                                                    <li class="sub-pro-name sub-pro-device-title">
                                                        <a title="<?php the_title_attribute(); ?>" href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
                                                    </li>
                                                    <li class="sub-pro-notice-item" style="margin: 2px 0 1px 0; line-height: 1.2;">
                                                        <a style="color:#ec1600; font-size: 11px; font-weight: 600; display: block; height: auto; max-height: 18px; line-height: 1.2; text-decoration: none;" title="分割残債あり(△)も減額なし対応" href="<?php echo esc_url( $permalink ); ?>">分割残債あり(△)も減額なし対応</a>
                                                    </li>
                                                    <li class="sub-pro-jan-item" style="font-size: 11px; color: #64748b; margin: 2px 0 0 0; line-height: 1.2; padding: 0;">JAN：<?php echo esc_html( $jan ); ?></li>
                                                </div>
                                                <li class="sub-pro-jia" style="margin: auto 0 6px 0; padding-top: 6px;">
                                                    <?php _e( 'Buyback Price:', 'kaitoriakiba' ); ?> <span>¥<?php echo number_format( (int)$price ); ?></span>
                                                </li>
                                                <li style="margin: 0; padding: 0;">
                                                    <a href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( get_the_title() ) ) ); ?>" style="display: block; margin: 0; padding: 0;">
                                                        <button type="button" class="btn btn-pro" style="margin: 0; padding: 8px 12px;"><?php echo esc_html( $btn_text ); ?></button>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        <?php else : ?>
                            <!-- Fallback sample models from index.html -->
                            <?php
                            $sample_devices = array(
                                array( 'title' => 'iPhone 16 Pro 256GB Black', 'price' => 195000, 'jan' => '4549995734140', 'img' => 'iPhone 18 Pro 256GB Black.png' ),
                                array( 'title' => 'iPhone 16 Pro 256GB Silver', 'price' => 195000, 'jan' => '4549995734157', 'img' => 'iPhone 18 Pro 256GB Silver.png' ),
                                array( 'title' => 'iPhone 16 Pro 256GB Natural', 'price' => 195000, 'jan' => '4549995734171', 'img' => 'iPhone 18 Pro 256GB Glacier.png' ),
                                array( 'title' => 'iPhone 16 Pro 256GB Desert', 'price' => 208000, 'jan' => '4549995734164', 'img' => 'iPhone 18 Pro 256GB Burgundy.png' ),
                            );
                            foreach ( $sample_devices as $dev ) :
                                ?>
                                <div class="<?php echo esc_attr( $col_class ); ?>">
                                    <div class="pro-border gutter-10 row pro_list" style="padding: 14px 12px 8px 12px;">
                                        <div data-isok="false" class="collect"></div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-5 text-center pro-img-box" style="padding: 6px 0; margin-bottom: 8px;">
                                            <a href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $dev['title'] ) ) ); ?>">
                                                <img src="<?php echo esc_url( $theme_uri . '/upload/goods/20260911/' . $dev['img'] ); ?>" alt="<?php echo esc_attr( $dev['title'] ); ?>" class="img-responsive" style="max-height: 140px; margin: 0 auto; object-fit: contain;">
                                            </a>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-7 pro-info-box" style="margin-bottom: 0; padding-bottom: 0;">
                                            <ul style="list-style: none; padding: 0; margin: 0;">
                                                <div class="sub-pro" style="margin-bottom: 4px;">
                                                    <li class="sub-pro-career">
                                                        <a href="<?php echo esc_url( home_url( '/devices/?brand=apple' ) ); ?>">Apple</a>
                                                    </li>
                                                    <li class="sub-pro-name sub-pro-device-title">
                                                        <a title="<?php echo esc_attr( $dev['title'] ); ?>" href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $dev['title'] ) ) ); ?>"><?php echo esc_html( $dev['title'] ); ?></a>
                                                    </li>
                                                    <li class="sub-pro-notice-item" style="margin: 2px 0 1px 0; line-height: 1.2;">
                                                        <a style="color:#ec1600; font-size: 11px; font-weight: 600; display: block; height: auto; max-height: 18px; line-height: 1.2; text-decoration: none;" title="分割残債あり(△)も減額なし対応" href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $dev['title'] ) ) ); ?>">分割残債あり(△)も減額なし対応</a>
                                                    </li>
                                                    <li class="sub-pro-jan-item" style="font-size: 11px; color: #64748b; margin: 2px 0 0 0; line-height: 1.2; padding: 0;">JAN：<?php echo esc_html( $dev['jan'] ); ?></li>
                                                </div>
                                                <li class="sub-pro-jia" style="margin: auto 0 6px 0; padding-top: 6px;">
                                                    <?php _e( 'Buyback Price:', 'kaitoriakiba' ); ?> <span>¥<?php echo number_format( $dev['price'] ); ?></span>
                                                </li>
                                                <li style="margin: 0; padding: 0;">
                                                    <a href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $dev['title'] ) ) ); ?>" style="display: block; margin: 0; padding: 0;">
                                                        <button type="button" class="btn btn-pro" style="margin: 0; padding: 8px 12px;"><?php echo esc_html( $btn_text ); ?></button>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endforeach;
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="sub-pro-footer" style="text-align: center; margin-top: 15px;">
                    <a class="btn-ys more" href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php _e( 'View All / 全機種一覧', 'kaitoriakiba' ); ?></a>
                </div>
            </div>
        </div>
        <?php
    }
}
