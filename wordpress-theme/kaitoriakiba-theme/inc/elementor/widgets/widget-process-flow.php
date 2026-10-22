<?php
/**
 * Elementor Service Process Flow Widget
 * 
 * Renders the authentic Kaitori Akiba 3-button flow bar (guid) with s1.png, s2.png, s3.png
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Process_Flow_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_process_flow';
    }

    public function get_title() {
        return __( 'Process Flow Buttons (買取の流れバナー)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-navigation-horizontal';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Buttons Settings', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'btn1_text',
            array(
                'label'   => __( 'Button 1 Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'In-Store Buyback / 店頭買取',
            )
        );
        $this->add_control(
            'btn1_url',
            array(
                'label'   => __( 'Button 1 Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/sell-apply/?delivery=store',
            )
        );

        $this->add_control(
            'btn2_text',
            array(
                'label'   => __( 'Button 2 Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Mail-in Buyback / 郵送買取',
            )
        );
        $this->add_control(
            'btn2_url',
            array(
                'label'   => __( 'Button 2 Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/sell-apply/?delivery=mail',
            )
        );

        $this->add_control(
            'btn3_text',
            array(
                'label'   => __( 'Button 3 Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Corporate Buyback / 法人買取',
            )
        );
        $this->add_control(
            'btn3_url',
            array(
                'label'   => __( 'Button 3 Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/sell-apply/?type=corporate',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();

        $b1_txt = ! empty( $settings['btn1_text'] ) ? $settings['btn1_text'] : __( 'In-Store Buyback', 'kaitoriakiba' );
        $b1_url = ! empty( $settings['btn1_url'] ) ? esc_url( home_url( $settings['btn1_url'] ) ) : esc_url( home_url( '/sell-apply/?delivery=store' ) );
        $b2_txt = ! empty( $settings['btn2_text'] ) ? $settings['btn2_text'] : __( 'Mail-in Buyback', 'kaitoriakiba' );
        $b2_url = ! empty( $settings['btn2_url'] ) ? esc_url( home_url( $settings['btn2_url'] ) ) : esc_url( home_url( '/sell-apply/?delivery=mail' ) );
        $b3_txt = ! empty( $settings['btn3_text'] ) ? $settings['btn3_text'] : __( 'Corporate Buyback', 'kaitoriakiba' );
        $b3_url = ! empty( $settings['btn3_url'] ) ? esc_url( home_url( $settings['btn3_url'] ) ) : esc_url( home_url( '/sell-apply/?type=corporate' ) );
        ?>
        <div class="guid">
            <div class="container">
                <div class="row home_index">
                    <div class="col-lg-1 col-md-0 col-sm-0 col-xs-0 col-preg"></div>
                    <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12 center col-preg-main">
                        <div class="col-lg-1 col-xs-0"></div>
                        <div class="col-lg-10 col-xs-12 col-preg-main">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 type_1">
                                <a href="<?php echo esc_url( $b1_url ); ?>" title="<?php echo esc_attr( $b1_txt ); ?>">
                                    <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/topmenu/s1.png" class="img-responsive hidden-xs" alt="In-Store Buyback Flow" />
                                    <span class="btn btn-orange"><?php echo esc_html( $b1_txt ); ?></span>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 type_2">
                                <a href="<?php echo esc_url( $b2_url ); ?>" title="<?php echo esc_attr( $b2_txt ); ?>">
                                    <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/topmenu/s2.png" class="img-responsive hidden-xs" alt="Mail-in Buyback Flow" />
                                    <span class="btn btn-green"><?php echo esc_html( $b2_txt ); ?></span>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 type_3">
                                <a href="<?php echo esc_url( $b3_url ); ?>" title="<?php echo esc_attr( $b3_txt ); ?>">
                                    <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/topmenu/s3.png" class="img-responsive hidden-xs" alt="Corporate Buyback Flow" />
                                    <span class="btn btn-blue"><?php echo esc_html( $b3_txt ); ?></span>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-1 col-xs-0"></div>
                    </div>
                    <div class="col-lg-1 col-md-0 col-sm-0 col-xs-0 col-preg"></div>
                </div>
            </div>
        </div>
        <?php
    }
}
