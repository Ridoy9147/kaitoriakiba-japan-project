<?php
/**
 * Elementor Taxonomy Browse Sidebar Widget
 * Displays Browse by Carrier, Brand, Series with 100% full width images (no margin auto).
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
        return __( 'Browse by Taxonomy (キャリア・ブランド別一覧)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Taxonomy Settings', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'taxonomy_type',
            array(
                'label'   => __( 'Select Taxonomy', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'carrier',
                'options' => array(
                    'carrier'         => __( 'Browse by Carrier (キャリアから探す)', 'kaitoriakiba' ),
                    'brand'           => __( 'Browse by Brand (ブランドから探す)', 'kaitoriakiba' ),
                    'series'          => __( 'Browse by Series (シリーズから探す)', 'kaitoriakiba' ),
                    'device_category' => __( 'Browse by Category (カテゴリから探す)', 'kaitoriakiba' ),
                ),
            )
        );

        $this->add_control(
            'custom_title',
            array(
                'label'       => __( 'Custom Box Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => __( 'Leave empty for default taxonomy title', 'kaitoriakiba' ),
            )
        );

        $this->add_control(
            'number_of_items',
            array(
                'label'   => __( 'Number of Items to Show', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min'     => 1,
                'max'     => 50,
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $tax = $settings['taxonomy_type'];

        $default_titles = array(
            'carrier'         => __( 'Browse by Carrier', 'kaitoriakiba' ),
            'brand'           => __( 'Browse by Brand', 'kaitoriakiba' ),
            'series'          => __( 'Browse by Series', 'kaitoriakiba' ),
            'device_category' => __( 'Browse by Category', 'kaitoriakiba' ),
        );

        $box_title = ! empty( $settings['custom_title'] ) ? $settings['custom_title'] : $default_titles[$tax];

        $terms = get_terms( array(
            'taxonomy'   => $tax,
            'hide_empty' => false,
            'number'     => intval( $settings['number_of_items'] ),
        ) );
        ?>
        <div class="navigation-box" style="background:#ffffff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:20px;">
            <div class="navigation-h2" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding:10px 14px; color:#ffffff; font-weight:800; font-size:14px;">
                <i class="fa fa-circle" style="font-size:8px; vertical-align:middle; margin-right:6px;"></i>
                <span><?php echo esc_html( $box_title ); ?></span>
            </div>
            <ul style="list-style:none; padding:8px; margin:0;">
                <?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
                    <?php foreach ( $terms as $term ) : 
                        $term_link = get_term_link( $term );
                        // Attempt image from theme upload folder
                        $img_file = get_template_directory() . '/upload/' . $tax . '/' . $term->slug . '.jpg';
                        $img_url  = file_exists( $img_file ) ? get_template_directory_uri() . '/upload/' . $tax . '/' . $term->slug . '.jpg' : '';
                        if ( ! $img_url ) {
                            $png_file = get_template_directory() . '/upload/' . $tax . '/' . $term->slug . '.png';
                            $img_url = file_exists( $png_file ) ? get_template_directory_uri() . '/upload/' . $tax . '/' . $term->slug . '.png' : '';
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
                    <?php endforeach; ?>
                <?php else : ?>
                    <!-- Demo Fallbacks with Full Width (no margin auto) -->
                    <li style="padding:4px 0; list-style:none; width:100%;">
                        <a href="<?php echo esc_url( home_url( '/devices/?s_career=docomo' ) ); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/upload/career/docomo.jpg" alt="docomo" class="img-responsive nav-img-full" style="width:100% !important; margin:0 !important; border-radius:6px;">
                        </a>
                    </li>
                    <li style="padding:4px 0; list-style:none; width:100%;">
                        <a href="<?php echo esc_url( home_url( '/devices/?s_career=au' ) ); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/upload/career/au.jpg" alt="au" class="img-responsive nav-img-full" style="width:100% !important; margin:0 !important; border-radius:6px;">
                        </a>
                    </li>
                    <li style="padding:4px 0; list-style:none; width:100%;">
                        <a href="<?php echo esc_url( home_url( '/devices/?s_career=softbank' ) ); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/upload/career/softbank.jpg" alt="SoftBank" class="img-responsive nav-img-full" style="width:100% !important; margin:0 !important; border-radius:6px;">
                        </a>
                    </li>
                    <li style="padding:4px 0; list-style:none; width:100%;">
                        <a href="<?php echo esc_url( home_url( '/devices/?s_career=simfree' ) ); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/upload/career/simfree.jpg" alt="SIM FREE" class="img-responsive nav-img-full" style="width:100% !important; margin:0 !important; border-radius:6px;">
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php
    }
}
