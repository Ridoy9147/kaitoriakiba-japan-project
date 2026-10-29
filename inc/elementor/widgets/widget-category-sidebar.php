<?php
/**
 * Elementor Taxonomy Browse Sidebar Widget
 *
 * "Browse by Carrier / Brand / Series / Category" box. Renders a tidy chip grid of
 * terms that looks good WITHOUT the (optional) per-term logo images — if an image
 * exists at /upload/<tax>/<slug>.(jpg|png) it is used, otherwise a styled text chip.
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
            'columns',
            array(
                'label'   => __( 'Columns', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '2',
                'options' => array( '1' => '1', '2' => '2', '3' => '3' ),
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
        $tax = ! empty( $settings['taxonomy_type'] ) ? $settings['taxonomy_type'] : 'carrier';
        $cols = isset( $settings['columns'] ) ? max( 1, min( 3, (int) $settings['columns'] ) ) : 2;

        $default_titles = array(
            'carrier'         => __( 'Browse by Carrier', 'kaitoriakiba' ),
            'brand'           => __( 'Browse by Brand', 'kaitoriakiba' ),
            'series'          => __( 'Browse by Series', 'kaitoriakiba' ),
            'device_category' => __( 'Browse by Category', 'kaitoriakiba' ),
        );
        $icons = array(
            'carrier'         => 'fa-signal',
            'brand'           => 'fa-tags',
            'series'          => 'fa-mobile',
            'device_category' => 'fa-th-large',
        );

        $box_title = ! empty( $settings['custom_title'] ) ? $settings['custom_title'] : ( isset( $default_titles[ $tax ] ) ? $default_titles[ $tax ] : __( 'Browse Devices', 'kaitoriakiba' ) );
        $icon      = isset( $icons[ $tax ] ) ? $icons[ $tax ] : 'fa-list';
        $num_items = ! empty( $settings['number_of_items'] ) ? intval( $settings['number_of_items'] ) : 12;

        $terms = get_terms( array(
            'taxonomy'   => $tax,
            'hide_empty' => false,
            'number'     => $num_items,
        ) );

        // Fallback demo list (English-friendly) if the taxonomy has no terms yet.
        $items = array();
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $img_file = get_template_directory() . '/upload/' . $tax . '/' . $term->slug . '.jpg';
                $img_url  = file_exists( $img_file ) ? get_template_directory_uri() . '/upload/' . $tax . '/' . $term->slug . '.jpg' : '';
                if ( ! $img_url ) {
                    $png_file = get_template_directory() . '/upload/' . $tax . '/' . $term->slug . '.png';
                    $img_url  = file_exists( $png_file ) ? get_template_directory_uri() . '/upload/' . $tax . '/' . $term->slug . '.png' : '';
                }
                $items[] = array( 'name' => $term->name, 'url' => get_term_link( $term ), 'img' => $img_url );
            }
        } else {
            $demo = array( 'docomo' => 'docomo', 'au' => 'au', 'softbank' => 'SoftBank', 'simfree' => 'SIM FREE', 'RakutenMobile' => 'Rakuten Mobile', 'uqmobile' => 'UQ mobile', 'Y!mobile' => 'Y!mobile' );
            foreach ( $demo as $slug => $name ) {
                $items[] = array( 'name' => $name, 'url' => home_url( '/devices/?s_career=' . rawurlencode( $slug ) ), 'img' => '' );
            }
        }
        ?>
        <style>
            .kaitori-taxbox { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,.04); }
            .kaitori-taxbox-head { display:flex; align-items:center; gap:8px; padding:12px 16px !important;
                background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%) !important; color:#fff !important; font-weight:800; font-size:14px; letter-spacing:.3px; }
            .kaitori-taxbox-head i { font-size:14px; }
            .kaitori-taxbox-head a { color:#fff !important; text-decoration:none; }
            .kaitori-taxgrid { display:grid; grid-template-columns:repeat(var(--cols,2),1fr); gap:8px; padding:12px; }
            .kaitori-chip { display:flex; align-items:center; justify-content:space-between; gap:6px;
                padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:10px; background:#f8fafc;
                color:#1e293b; font-weight:700; font-size:13px; text-decoration:none; transition:all .15s ease; min-height:42px; }
            .kaitori-chip:hover { border-color:#ef4444; background:#fff5f5; color:#dc2626; transform:translateY(-1px); box-shadow:0 3px 10px rgba(239,68,68,.12); }
            .kaitori-chip .arw { color:#cbd5e1; font-weight:800; transition:color .15s ease; }
            .kaitori-chip:hover .arw { color:#ef4444; }
            .kaitori-chip-img { padding:6px; }
            .kaitori-chip-img img { display:block; width:100%; height:auto; border-radius:6px; }
        </style>
        <div class="kaitori-taxbox">
            <div class="kaitori-taxbox-head">
                <i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
                <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php echo esc_html( $box_title ); ?></a>
            </div>
            <div class="kaitori-taxgrid" style="--cols:<?php echo (int) $cols; ?>;">
                <?php foreach ( $items as $it ) : ?>
                    <?php if ( ! empty( $it['img'] ) ) : ?>
                        <a class="kaitori-chip kaitori-chip-img" href="<?php echo esc_url( $it['url'] ); ?>" title="<?php echo esc_attr( $it['name'] ); ?>" style="grid-column:1 / -1;">
                            <img src="<?php echo esc_url( $it['img'] ); ?>" alt="<?php echo esc_attr( $it['name'] ); ?>">
                        </a>
                    <?php else : ?>
                        <a class="kaitori-chip" href="<?php echo esc_url( $it['url'] ); ?>" title="<?php echo esc_attr( $it['name'] ); ?>">
                            <span><?php echo esc_html( $it['name'] ); ?></span>
                            <span class="arw">&rsaquo;</span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
