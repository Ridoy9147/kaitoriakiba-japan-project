<?php
/**
 * Elementor Taxonomy / Category Browse Widget
 * 
 * Supports 3 authentic Japanese portal layout modes matching index.html:
 * 1. Carrier Browse (Full-width carrier image banners)
 * 2. Brand Browse (3-column logo grid: .quick_side col-lg-4)
 * 3. Series Browse (Clean text links list: .navigation-h3)
 * 
 * 100% Fully Editable in Elementor.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Category_Sidebar_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_taxonomy_browse';
    }

    public function get_title() {
        return __( 'Browse by Category / Brands (条件から探す)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-folder';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {

        $theme_uri = get_template_directory_uri();

        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Box Settings', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'box_title',
            array(
                'label'   => __( 'Section Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'ブランドから探す (Browse by Brand)',
            )
        );

        $this->add_control(
            'box_link',
            array(
                'label'   => __( 'Title Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/brand/' ),
            )
        );

        $this->add_control(
            'display_mode',
            array(
                'label'   => __( 'Display Layout Mode', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'brand_grid',
                'options' => array(
                    'brand_grid'   => __( '3-Column Brand Logo Grid (クイックロゴ)', 'kaitoriakiba' ),
                    'carrier_full' => __( 'Full-Width Carrier Banners (キャリアバナー)', 'kaitoriakiba' ),
                    'series_list'  => __( 'Text Link List (シリーズ一覧リスト)', 'kaitoriakiba' ),
                ),
            )
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'item_title',
            array(
                'label'       => __( 'Item Name', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'Apple',
                'label_block' => true,
            )
        );

        $repeater->add_control(
            'item_image',
            array(
                'label'   => __( 'Item Image / Logo', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/upload/brand/apple.jpg',
                ),
            )
        );

        $repeater->add_control(
            'item_url',
            array(
                'label'   => __( 'Item Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array(
                    'url' => '/devices/?brand=apple',
                ),
            )
        );

        $this->add_control(
            'show_more_button',
            array(
                'label'        => __( 'Show More Button (もっと見る)', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            )
        );

        $this->add_control(
            'custom_items_list',
            array(
                'label'       => __( 'Items List', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => array(
                    array( 'item_title' => 'Apple', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/apple.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=apple' ) ),
                    array( 'item_title' => 'Xiaomi', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/20200603/3.png' ), 'item_url' => array( 'url' => '/devices/?brand=xiaomi' ) ),
                    array( 'item_title' => 'OPPO', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/OPPO.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=oppo' ) ),
                    array( 'item_title' => 'ASUS', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/asus.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=asus' ) ),
                    array( 'item_title' => 'FUJITSU', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/fujitsu.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=fujitsu' ) ),
                    array( 'item_title' => 'SAMSUNG', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/samsung.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=samsung' ) ),
                    array( 'item_title' => 'Google', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/google.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=google' ) ),
                    array( 'item_title' => 'HUAWEI', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/huawei.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=huawei' ) ),
                    array( 'item_title' => 'KYOCERA', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/kyocera.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=kyocera' ) ),
                    array( 'item_title' => 'MOTOROLA', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/motorola.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=motorola' ) ),
                    array( 'item_title' => 'SHARP', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/sharp.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=sharp' ) ),
                    array( 'item_title' => 'SONY', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/sony.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=sony' ) ),
                    array( 'item_title' => 'ZTE', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/zte.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=zte' ) ),
                    array( 'item_title' => 'FREETEL', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/freetel.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=freetel' ) ),
                    array( 'item_title' => 'Other', 'item_image' => array( 'url' => $theme_uri . '/upload/brand/other.jpg' ), 'item_url' => array( 'url' => '/devices/?brand=other' ) ),
                ),
                'title_field' => '{{{ item_title }}}',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        $title     = ! empty( $settings['box_title'] ) ? $settings['box_title'] : 'ブランドから探す (Browse by Brand)';
        $box_url   = ! empty( $settings['box_link']['url'] ) ? $settings['box_link']['url'] : '/brand/';
        $mode      = ! empty( $settings['display_mode'] ) ? $settings['display_mode'] : 'brand_grid';
        $items     = ! empty( $settings['custom_items_list'] ) && is_iterable( $settings['custom_items_list'] ) ? $settings['custom_items_list'] : array();
        ?>
        <div class="navigation-box">
            <div class="navigation-h2">
                <a href="<?php echo esc_url( home_url( $box_url ) ); ?>">
                    <i class="fa fa-circle"><span><?php echo esc_html( $title ); ?></span></i>
                </a>
            </div>

            <?php if ( 'brand_grid' === $mode ) : ?>
                <!-- 3-Column Logo Grid (.quick_side) -->
                <ul class="quick_side">
                    <?php foreach ( $items as $item ) : 
                        $img_url  = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : '';
                        $link_url = ! empty( $item['item_url']['url'] ) ? $item['item_url']['url'] : home_url( '/devices/' );
                        $ititle   = ! empty( $item['item_title'] ) ? $item['item_title'] : '';
                    ?>
                        <li class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mbtm10">
                            <a href="<?php echo esc_url( home_url( $link_url ) ); ?>" title="<?php echo esc_attr( $ititle ); ?>">
                                <?php if ( $img_url ) : ?>
                                    <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $ititle ); ?>" class="img-responsive mcenter" />
                                <?php else : ?>
                                    <span><?php echo esc_html( $ititle ); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ( ! empty( $settings['show_more_button'] ) && 'yes' === $settings['show_more_button'] ) : ?>
                    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 btn-more">
                        <a href="<?php echo esc_url( home_url( $box_url ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri . '/assets/images/navigation/more_open.jpg' ); ?>" alt="Open" class="img-responsive">
                        </a>
                    </div>
                <?php endif; ?>
                <div class="clearfix"></div>

            <?php elseif ( 'series_list' === $mode ) : ?>
                <!-- Text Link List (.navigation-h3) -->
                <ul class="navigation-h3">
                    <?php foreach ( $items as $item ) : 
                        $link_url = ! empty( $item['item_url']['url'] ) ? $item['item_url']['url'] : home_url( '/devices/' );
                        $ititle   = ! empty( $item['item_title'] ) ? $item['item_title'] : '';
                    ?>
                        <li class="col-lg-12 col-md-12 col-sm-12 col-xs-6">
                            <a href="<?php echo esc_url( home_url( $link_url ) ); ?>" title="<?php echo esc_attr( $ititle ); ?>"><?php echo esc_html( $ititle ); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="clearfix"></div>

            <?php else : ?>
                <!-- Full-Width Carrier Banners -->
                <ul>
                    <?php foreach ( $items as $item ) : 
                        $img_url  = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : '';
                        $link_url = ! empty( $item['item_url']['url'] ) ? $item['item_url']['url'] : home_url( '/devices/' );
                        $ititle   = ! empty( $item['item_title'] ) ? $item['item_title'] : '';
                    ?>
                        <li class="col-lg-12 col-sm-12 col-md-12 col-xs-6">
                            <a href="<?php echo esc_url( home_url( $link_url ) ); ?>" title="<?php echo esc_attr( $ititle ); ?>">
                                <?php if ( $img_url ) : ?>
                                    <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $ititle ); ?>" class="img-responsive">
                                <?php else : ?>
                                    <span><?php echo esc_html( $ititle ); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="clearfix"></div>
            <?php endif; ?>
        </div>
        <?php
    }
}
