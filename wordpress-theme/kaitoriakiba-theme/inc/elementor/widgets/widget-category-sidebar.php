<?php
/**
 * Elementor Taxonomy Browse Sidebar Widget
 * 
 * 100% Fully Editable in Elementor:
 * - Allows full repeater editing of Brand, Carrier, and Series logos, titles, and links.
 * - Supports dynamic mode from WordPress taxonomies as well.
 * - Enforces 100% full-width images (no auto margin margins) as required.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Taxonomy_Browse_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_taxonomy_browse';
    }

    public function get_title() {
        return __( 'Browse by Taxonomy / Brands (キャリア・ブランド別一覧)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {

        $theme_uri = get_template_directory_uri();

        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Navigation Box Settings', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'data_mode',
            array(
                'label'   => __( 'Data Mode', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => array(
                    'custom'  => __( 'Editable Custom Items (Repeater)', 'kaitoriakiba' ),
                    'dynamic' => __( 'Dynamic from WordPress Taxonomies', 'kaitoriakiba' ),
                ),
            )
        );

        $this->add_control(
            'box_title',
            array(
                'label'       => __( 'Box Header Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'ブランドから探す (Browse by Brand)',
                'placeholder' => __( 'e.g. ブランドから探す', 'kaitoriakiba' ),
            )
        );

        // =====================================================================
        // REPEATER FOR CUSTOM ITEMS
        // =====================================================================
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'item_title',
            array(
                'label'       => __( 'Brand / Item Name', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'Brand Name',
                'label_block' => true,
            )
        );

        $repeater->add_control(
            'item_image',
            array(
                'label'   => __( 'Logo / Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => '',
                ),
            )
        );

        $repeater->add_control(
            'item_url',
            array(
                'label'       => __( 'Link URL', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com or /devices/?brand=apple', 'kaitoriakiba' ),
                'default'     => array(
                    'url' => '/devices/',
                ),
            )
        );

        $this->add_control(
            'custom_items_list',
            array(
                'label'       => __( 'Brands / Items List', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => array(
                    array(
                        'item_title' => 'OPPO',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/OPPO.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=oppo' ),
                    ),
                    array(
                        'item_title' => 'ASUS',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/asus.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=asus' ),
                    ),
                    array(
                        'item_title' => 'FUJITSU',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/fujitsu.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=fujitsu' ),
                    ),
                    array(
                        'item_title' => 'SAMSUNG',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/samsung.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=samsung' ),
                    ),
                    array(
                        'item_title' => 'Google',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/google.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=google' ),
                    ),
                    array(
                        'item_title' => 'HUAWEI',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/huawei.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=huawei' ),
                    ),
                    array(
                        'item_title' => 'KYOCERA',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/kyocera.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=kyocera' ),
                    ),
                    array(
                        'item_title' => 'SHARP',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/sharp.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=sharp' ),
                    ),
                    array(
                        'item_title' => 'Apple',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/apple.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=apple' ),
                    ),
                    array(
                        'item_title' => 'Sony',
                        'item_image' => array( 'url' => $theme_uri . '/upload/brand/sony.jpg' ),
                        'item_url'   => array( 'url' => '/devices/?brand=sony' ),
                    ),
                ),
                'title_field' => '{{{ item_title }}}',
                'condition'   => array( 'data_mode' => 'custom' ),
            )
        );

        // =====================================================================
        // DYNAMIC TAXONOMY CONTROLS
        // =====================================================================
        $this->add_control(
            'taxonomy_type',
            array(
                'label'   => __( 'Select Taxonomy', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'brand',
                'options' => array(
                    'brand'           => __( 'Browse by Brand (ブランドから探す)', 'kaitoriakiba' ),
                    'carrier'         => __( 'Browse by Carrier (キャリアから探す)', 'kaitoriakiba' ),
                    'series'          => __( 'Browse by Series (シリーズから探す)', 'kaitoriakiba' ),
                    'device_category' => __( 'Browse by Category (カテゴリから探す)', 'kaitoriakiba' ),
                ),
                'condition' => array( 'data_mode' => 'dynamic' ),
            )
        );

        $this->add_control(
            'number_of_items',
            array(
                'label'     => __( 'Number of Items', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 12,
                'min'       => 1,
                'max'       => 50,
                'condition' => array( 'data_mode' => 'dynamic' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // STYLING SECTION
        // =====================================================================
        $this->start_controls_section(
            'section_style',
            array(
                'label' => __( 'Header Style & Colors', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'header_bg_color',
            array(
                'label'     => __( 'Header Background', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ef4444',
                'selectors' => array(
                    '{{WRAPPER}} .navigation-h2' => 'background: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'card_border_radius',
            array(
                'label'      => __( 'Card Border Radius (px)', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array( 'min' => 0, 'max' => 20 ),
                ),
                'default'    => array( 'unit' => 'px', 'size' => 6 ),
                'selectors'  => array(
                    '{{WRAPPER}} .nav-img-full' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        $title     = ! empty( $settings['box_title'] ) ? $settings['box_title'] : 'ブランドから探す (Browse by Brand)';
        ?>
        <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px; box-shadow:0 4px 15px rgba(0,0,0,0.02);">
            
            <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:11px 16px; color:#ffffff; font-weight:800; font-size:14px; letter-spacing:0.5px;">
                <i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px; color:#fef08a;"></i>
                <span><?php echo esc_html( $title ); ?></span>
            </div>

            <ul style="list-style:none; padding:10px 8px; margin:0;">
                <?php if ( 'custom' === $settings['data_mode'] && ! empty( $settings['custom_items_list'] ) ) : ?>
                    
                    <?php foreach ( $settings['custom_items_list'] as $item ) : 
                        $img_url  = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : '';
                        $link_url = ! empty( $item['item_url']['url'] ) ? $item['item_url']['url'] : home_url( '/devices/' );
                        $is_ext   = ! empty( $item['item_url']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
                    ?>
                        <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                            <a href="<?php echo esc_url( $link_url ); ?>"<?php echo $is_ext; ?> title="<?php echo esc_attr( $item['item_title'] ); ?>" style="display:block; text-decoration:none;">
                                <?php if ( $img_url ) : ?>
                                    <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $item['item_title'] ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; min-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0; object-fit:contain; transition:box-shadow 0.2s, transform 0.2s;">
                                <?php else : ?>
                                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px; font-weight:700; font-size:13px; color:#1e293b; text-align:center;">
                                        <?php echo esc_html( $item['item_title'] ); ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>

                <?php else : 
                    // Dynamic mode from WordPress taxonomy
                    $tax = ! empty( $settings['taxonomy_type'] ) ? $settings['taxonomy_type'] : 'brand';
                    $num = ! empty( $settings['number_of_items'] ) ? intval( $settings['number_of_items'] ) : 12;
                    $tax_folder = ( 'carrier' === $tax ) ? 'career' : $tax;

                    $terms = get_terms( array(
                        'taxonomy'   => $tax,
                        'hide_empty' => false,
                        'number'     => $num,
                    ) );

                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
                        foreach ( $terms as $term ) :
                            $term_link = get_term_link( $term );
                            $img_file  = get_template_directory() . '/upload/' . $tax_folder . '/' . $term->slug . '.jpg';
                            $img_url   = file_exists( $img_file ) ? $theme_uri . '/upload/' . $tax_folder . '/' . $term->slug . '.jpg' : '';
                            if ( ! $img_url ) {
                                $png_file = get_template_directory() . '/upload/' . $tax_folder . '/' . $term->slug . '.png';
                                $img_url  = file_exists( $png_file ) ? $theme_uri . '/upload/' . $tax_folder . '/' . $term->slug . '.png' : '';
                            }
                        ?>
                            <li style="padding:4px 0; margin-bottom:6px; list-style:none; width:100%;">
                                <a href="<?php echo esc_url( $term_link ); ?>" title="<?php echo esc_attr( $term->name ); ?>" style="display:block; text-decoration:none;">
                                    <?php if ( $img_url ) : ?>
                                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" class="img-responsive nav-img-full" style="width:100% !important; max-width:100% !important; min-width:100% !important; margin:0 !important; display:block !important; border-radius:6px; border:1px solid #e2e8f0; object-fit:contain;">
                                    <?php else : ?>
                                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px; font-weight:700; font-size:13px; color:#1e293b; text-align:center;">
                                            <?php echo esc_html( $term->name ); ?>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach;
                    endif;
                endif; ?>
            </ul>
        </div>
        <?php
    }
}
