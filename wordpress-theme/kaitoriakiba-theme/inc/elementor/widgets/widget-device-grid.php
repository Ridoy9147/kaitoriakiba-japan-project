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

        $args = array(
            'post_type'      => 'kaitori_device',
            'posts_per_page' => intval( $settings['posts_per_page'] ),
            'post_status'    => 'publish',
            'orderby'        => $settings['orderby'],
            'order'          => $settings['order'],
        );

        if ( $settings['orderby'] === 'meta_value_num' ) {
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
        if ( $settings['columns'] === '2' ) {
            $col_class = 'col-lg-6 col-md-6 col-sm-6 col-xs-12';
        } elseif ( $settings['columns'] === '3' ) {
            $col_class = 'col-lg-4 col-md-4 col-sm-6 col-xs-12';
        } elseif ( $settings['columns'] === '6' ) {
            $col_class = 'col-lg-2 col-md-3 col-sm-4 col-xs-6';
        }
        ?>
        <div class="kaitori-device-grid-wrapper sub-pro-box">
            <div class="row">
                <?php if ( $query->have_posts() ) : ?>
                    <?php while ( $query->have_posts() ) : $query->the_post(); 
                        $post_id   = get_the_ID();
                        $price     = get_post_meta( $post_id, '_max_buyback_price', true ) ?: '168000';
                        $jan       = get_post_meta( $post_id, '_jan_code', true ) ?: '4549995734140';
                        $brands    = get_the_terms( $post_id, 'brand' );
                        $brand_name= ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0]->name : 'Apple';
                        $permalink = get_permalink();
                        $thumb_url = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'medium' ) : get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png';
                    ?>
                        <div class="<?php echo esc_attr( $col_class ); ?>">
                            <div class="pro-border gutter-10 row pro_list" style="margin-bottom: 20px;">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-5" style="text-align: center; padding: 15px;">
                                    <a href="<?php echo esc_url( $permalink ); ?>">
                                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="img-responsive" style="max-height: 180px; margin: 0 auto; object-fit: contain;">
                                    </a>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-7">
                                    <ul style="list-style: none; padding: 0;">
                                        <div class="sub-pro">
                                            <li class="sub-pro-career">
                                                <span style="font-size: 11.5px; font-weight: 700; color: #ef4444;"><?php echo esc_html( $brand_name ); ?></span>
                                            </li>
                                            <li class="sub-pro-name">
                                                <a title="<?php the_title_attribute(); ?>" href="<?php echo esc_url( $permalink ); ?>" style="font-weight: 800; color: #0f172a; text-decoration: none;"><?php the_title(); ?></a>
                                            </li>
                                            <li class="sub-pro-name" style="font-size: 11px; color: #ec1600;">
                                                <span>分割中・利用制限△も高価買取中</span>
                                            </li>
                                            <li class="sub-pro-name" style="font-size: 11px; color: #64748b;">JAN：<?php echo esc_html( $jan ); ?></li>
                                        </div>
                                        <li class="sub-pro-jia" style="font-size: 13px; font-weight: 700; margin-top: 6px;">
                                            最高買取: <span style="font-size: 18px; font-weight: 900; color: #dc2626;">¥<?php echo number_format( (int)$price ); ?></span>
                                        </li>
                                        <li style="margin-top: 10px;">
                                            <a href="<?php echo esc_url( $permalink ); ?>">
                                                <button type="button" class="btn btn-pro" style="width: 100%; font-weight: 700; border-radius: 6px;"><?php echo esc_html( $settings['button_text'] ); ?></button>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <!-- Demo Fallback if no posts created yet -->
                    <div class="col-xs-12">
                        <div class="alert alert-info text-center" style="margin: 20px 0;">
                            <?php _e( 'No devices published yet. Add new devices from admin (Buyback Devices).', 'kaitoriakiba' ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
