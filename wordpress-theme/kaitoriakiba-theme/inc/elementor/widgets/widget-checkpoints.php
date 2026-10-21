<?php
/**
 * Elementor Key Checkpoints & Process Infographic Widget
 * 
 * Renders the authentic Key Selling Points, Pre-assessment Checkpoints,
 * Secondhand Dealer Act banner, and Buyback Process Flow Infographic.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Checkpoints_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_checkpoints_box';
    }

    public function get_title() {
        return __( 'Key Selling Points & Process (高価買取のコツ・流れ)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-info-box';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Display Sections', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_points',
            array(
                'label'   => __( 'Show Key Selling Points', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'show_notes',
            array(
                'label'   => __( 'Show Checkpoints Before Applying', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'show_flow',
            array(
                'label'   => __( 'Show Buyback Flow Infographic', 'kaitoriakiba' ),
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
        <div class="kaitori-checkpoints-wrapper" style="margin: 20px 0;">
            <?php if ( 'yes' === $settings['show_points'] ) : ?>
                <!-- Key Selling Points -->
                <div class="mainpage-box">
                    <div class="mainpage-title col-center">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_title_1.jpg" alt="Key Checkpoints to Sell Your Phone at the Highest Price" class="img-responsive" /> 
                        <h2><?php _e( 'Key Selling Points', 'kaitoriakiba' ); ?></h2>
                    </div>
                    <ul class="gutter-0" style="list-style:none; padding:0; margin:0;">
                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/gaojia_1.jpg" alt="1. Erase all personal data & restore factory settings" class="img-responsive" /></a> 
                        </li>
                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/gaojia_2.jpg" alt="2. Include original box, cables, and accessories" class="img-responsive" /></a> 
                        </li>
                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/gaojia_3.jpg" alt="3. Clean the device exterior thoroughly" class="img-responsive" /></a> 
                        </li>
                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/gaojia_4.jpg" alt="4. Unlock SIM carrier restrictions (SIM-Free)" class="img-responsive" /></a> 
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_notes'] ) : ?>
                <!-- Checkpoints Before Applying -->
                <div class="mainpage-box">
                    <div class="mainpage-title col-center">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_title_2.jpg" alt="Please check before applying for buyback!" class="img-responsive" /> 
                    </div>
                    <ul class="gutter-10" style="list-style:none; padding:0; margin:0;">
                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_2_1.jpg" alt="1. Check list of non-purchasable items" class="img-responsive" /></a> 
                        </li>
                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_2_2.jpg" alt="2. Check network usage restriction status" class="img-responsive" /></a> 
                        </li>
                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_2_3.jpg" alt="3. Please ensure SIM and memory cards are removed" class="img-responsive" /> 
                        </li>
                        <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_2_4.jpg" alt="4. Charge battery to at least 50% before sending" class="img-responsive" /> 
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>

                <!-- Identity Verification Secondhand Dealer Act Banner -->
                <div class="mainpage-box">
                    <div class="mainpage-title col-center">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_title_3.jpg" alt="Identity verification documents required by Japanese Secondhand Dealer Act" class="img-responsive" /> 
                    </div>
                    <div class="mainpage-content col-center text-center">
                        <a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>">
                            <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_img_3.jpg" alt="Identity verification documents required by Japanese Secondhand Dealer Act" class="img-responsive" style="margin:0 auto; border-radius:8px;" />
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_flow'] ) : ?>
                <!-- Complete Buyback Flow Infographic -->
                <div class="mainpage-box" style="margin:0; padding-bottom:20px;">
                    <div class="mainpage-title col-center">
                        <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/mainpage_title_4.jpg" alt="Buyback Process" class="img-responsive" /> 
                    </div>
                    <div class="liucheng-box gutter-25 img-margin">
                        <!-- In-Store Flow -->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_title_1.jpg" alt="In-Store Buyback" class="img-responsive" /></a> 
                            <div class="liucheng-dian-box">
                                <ul class="col-center" style="list-style:none; padding:0;">
                                    <li>
                                        <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_1.jpg" alt="1. Check buyback price on our website" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_2.jpg" alt="2. Book your visit online (Walk-ins welcome)" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( '/access/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_3.jpg" alt="3. Visit our store in Akihabara, Tokyo" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=store' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_4.jpg" alt="4. Appraisal completed & instant cash payout" class="img-responsive" /></a> 
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Mail-in Flow -->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_title_2.jpg" alt="Mail-in Buyback" class="img-responsive" /></a> 
                            <div class="liucheng-youji-box">
                                <ul class="col-center" style="list-style:none; padding:0;">
                                    <li>
                                        <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_1.jpg" alt="1. Check current rates on website" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_2.jpg" alt="2. Ship device with required documents" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_3.jpg" alt="3. Receive assessment report by email" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_4.jpg" alt="4. Same-day bank transfer payout" class="img-responsive" /></a> 
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div style="text-align:center; margin-top:20px;">
                        <a href="<?php echo esc_url( home_url( '/sell-apply/?type=corporate' ) ); ?>">
                            <button type="button" class="btn btn-faren col-center"><?php _e( 'Corporate Clients Click Here (法人様買取相談)', 'kaitoriakiba' ); ?></button>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
