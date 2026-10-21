<?php
/**
 * Elementor Key Checkpoints & Process Infographic Widget
 * 
 * Renders the authentic Japanese portal sections matching index.html lines 2196-2315:
 * 1. Key Selling Points (.mainpage-box with .gutter-0)
 * 2. Pre-assessment Checkpoints (.mainpage-box with .gutter-10)
 * 3. Identity Verification Banner (.mainpage-box with mainpage_img_3.jpg)
 * 4. Buyback Process Flow Infographic (.mainpage-box with .liucheng-box & btn-faren)
 * 
 * 100% Fully Editable in Elementor.
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
        return __( 'Key Selling Points & Checkpoints (高価買取のコツ・確認事項)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-info-box';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {

        $theme_uri = get_template_directory_uri();

        // =====================================================================
        // SECTION 1: KEY SELLING POINTS
        // =====================================================================
        $this->start_controls_section(
            'section_selling_points',
            array(
                'label' => __( '1. Key Selling Points (高価買取のコツ)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_points',
            array(
                'label'   => __( 'Show Selling Points Section', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'points_title_image',
            array(
                'label'   => __( 'Title Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_title_1.jpg' ),
                'condition' => array( 'show_points' => 'yes' ),
            )
        );

        $this->add_control(
            'points_subtitle',
            array(
                'label'   => __( 'Heading Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Key Selling Points',
                'condition' => array( 'show_points' => 'yes' ),
            )
        );

        $repeater_points = new \Elementor\Repeater();

        $repeater_points->add_control(
            'item_image',
            array(
                'label'   => __( 'Card Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_1.jpg' ),
            )
        );

        $repeater_points->add_control(
            'item_url',
            array(
                'label'   => __( 'Card Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/sell-high-point#title1' ),
            )
        );

        $repeater_points->add_control(
            'item_alt',
            array(
                'label'   => __( 'Alt Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '1. Erase all personal data & restore factory settings',
            )
        );

        $this->add_control(
            'selling_points_list',
            array(
                'label'       => __( 'Selling Points Cards', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_points->get_controls(),
                'default'     => array(
                    array(
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_1.jpg' ),
                        'item_url'   => array( 'url' => '/sell-high-point#title1' ),
                        'item_alt'   => '1. Erase all personal data & restore factory settings',
                    ),
                    array(
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_2.jpg' ),
                        'item_url'   => array( 'url' => '/sell-high-point#title2' ),
                        'item_alt'   => '2. Include original box, cables, and accessories',
                    ),
                    array(
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_3.jpg' ),
                        'item_url'   => array( 'url' => '/sell-high-point#title3' ),
                        'item_alt'   => '3. Clean the device exterior thoroughly',
                    ),
                    array(
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_4.jpg' ),
                        'item_url'   => array( 'url' => '/sell-high-point#title5' ),
                        'item_alt'   => '4. Unlock SIM carrier restrictions (SIM-Free)',
                    ),
                ),
                'title_field' => '{{{ item_alt }}}',
                'condition'   => array( 'show_points' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 2: PRE-ASSESSMENT CHECKPOINTS
        // =====================================================================
        $this->start_controls_section(
            'section_notes',
            array(
                'label' => __( '2. Checkpoints Before Applying (お申込み前の確認事項)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_notes',
            array(
                'label'   => __( 'Show Checkpoints Section', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'notes_title_image',
            array(
                'label'   => __( 'Title Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_title_2.jpg' ),
                'condition' => array( 'show_notes' => 'yes' ),
            )
        );

        $repeater_notes = new \Elementor\Repeater();

        $repeater_notes->add_control(
            'item_image',
            array(
                'label'   => __( 'Card Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_1.jpg' ),
            )
        );

        $repeater_notes->add_control(
            'item_url',
            array(
                'label'   => __( 'Card Link URL (Optional)', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/purchase-note/' ),
            )
        );

        $repeater_notes->add_control(
            'item_alt',
            array(
                'label'   => __( 'Alt Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '1. Check list of non-purchasable items',
            )
        );

        $this->add_control(
            'checkpoints_list',
            array(
                'label'       => __( 'Checkpoints Cards', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_notes->get_controls(),
                'default'     => array(
                    array(
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_1.jpg' ),
                        'item_url'   => array( 'url' => '/purchase-note/' ),
                        'item_alt'   => '1. Check list of non-purchasable items',
                    ),
                    array(
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_2.jpg' ),
                        'item_url'   => array( 'url' => '/networklock-note/' ),
                        'item_alt'   => '2. Check network usage restriction status',
                    ),
                    array(
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_3.jpg' ),
                        'item_url'   => array( 'url' => '' ),
                        'item_alt'   => '3. Please ensure SIM and memory cards are removed',
                    ),
                    array(
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_4.jpg' ),
                        'item_url'   => array( 'url' => '' ),
                        'item_alt'   => '4. Charge battery to at least 50% before sending',
                    ),
                ),
                'title_field' => '{{{ item_alt }}}',
                'condition'   => array( 'show_notes' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 3: IDENTITY VERIFICATION
        // =====================================================================
        $this->start_controls_section(
            'section_identity',
            array(
                'label' => __( '3. Identity Verification Banner (本人確認書類案内)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_identity',
            array(
                'label'   => __( 'Show Identity Section', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'identity_title_image',
            array(
                'label'   => __( 'Title Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_title_3.jpg' ),
                'condition' => array( 'show_identity' => 'yes' ),
            )
        );

        $this->add_control(
            'identity_banner_image',
            array(
                'label'   => __( 'Content Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_3.jpg' ),
                'condition' => array( 'show_identity' => 'yes' ),
            )
        );

        $this->add_control(
            'identity_link',
            array(
                'label'   => __( 'Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => array( 'url' => '/home/article/detail/article_id/517' ),
                'condition' => array( 'show_identity' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 4: BUYBACK PROCESS FLOW INFOGRAPHIC
        // =====================================================================
        $this->start_controls_section(
            'section_flow',
            array(
                'label' => __( '4. Buyback Process Infographic (買取の流れ)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_flow',
            array(
                'label'   => __( 'Show Process Flow Section', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'flow_title_image',
            array(
                'label'   => __( 'Title Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_title_4.jpg' ),
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->add_control(
            'store_flow_url',
            array(
                'label'   => __( 'In-Store Flow Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/shop-purchase-flow/',
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->add_control(
            'mail_flow_url',
            array(
                'label'   => __( 'Mail-in Flow Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/delivery-purchase-flow/',
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->add_control(
            'corporate_btn_text',
            array(
                'label'   => __( 'Corporate Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Corporate Clients Click Here',
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->add_control(
            'corporate_btn_url',
            array(
                'label'   => __( 'Corporate Button Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/corporation-purchase-flow/',
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        ?>
        <div class="kaitori-checkpoints-wrapper">

            <?php
            // =================================================================
            // 1. KEY SELLING POINTS
            // =================================================================
            if ( 'yes' === $settings['show_points'] ) :
                $p_title_img = ! empty( $settings['points_title_image']['url'] ) ? $settings['points_title_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_title_1.jpg';
                $p_subtitle  = ! empty( $settings['points_subtitle'] ) ? $settings['points_subtitle'] : 'Key Selling Points';
            ?>
                <div class="mainpage-box">
                    <div class="mainpage-title col-center">
                        <img src="<?php echo esc_url( $p_title_img ); ?>" alt="<?php echo esc_attr( $p_subtitle ); ?>" class="img-responsive" /> 
                        <h2><?php echo esc_html( $p_subtitle ); ?></h2>
                    </div>
                    <?php if ( ! empty( $settings['selling_points_list'] ) ) : ?>
                        <ul class="gutter-0">
                            <?php foreach ( $settings['selling_points_list'] as $sp ) : 
                                $sp_img = ! empty( $sp['item_image']['url'] ) ? $sp['item_image']['url'] : '';
                                $sp_url = ! empty( $sp['item_url']['url'] ) ? $sp['item_url']['url'] : '#';
                                $sp_alt = ! empty( $sp['item_alt'] ) ? $sp['item_alt'] : '';
                                if ( ! $sp_img ) continue;
                            ?>
                                <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                    <a href="<?php echo esc_url( home_url( $sp_url ) ); ?>" target="_blank">
                                        <img src="<?php echo esc_url( $sp_img ); ?>" alt="<?php echo esc_attr( $sp_alt ); ?>" class="img-responsive" />
                                    </a> 
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="clear"></div>
                </div>
            <?php endif; ?>

            <?php
            // =================================================================
            // 2. CHECKPOINTS BEFORE APPLYING
            // =================================================================
            if ( 'yes' === $settings['show_notes'] ) :
                $n_title_img = ! empty( $settings['notes_title_image']['url'] ) ? $settings['notes_title_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_title_2.jpg';
            ?>
                <div class="mainpage-box">
                    <div class="mainpage-title col-center">
                        <img src="<?php echo esc_url( $n_title_img ); ?>" alt="Please check before applying for buyback!" class="img-responsive" /> 
                    </div>
                    <?php if ( ! empty( $settings['checkpoints_list'] ) ) : ?>
                        <ul class="gutter-10">
                            <?php foreach ( $settings['checkpoints_list'] as $cp ) : 
                                $cp_img = ! empty( $cp['item_image']['url'] ) ? $cp['item_image']['url'] : '';
                                $cp_url = ! empty( $cp['item_url']['url'] ) ? $cp['item_url']['url'] : '';
                                $cp_alt = ! empty( $cp['item_alt'] ) ? $cp['item_alt'] : '';
                                if ( ! $cp_img ) continue;
                            ?>
                                <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                                    <?php if ( ! empty( $cp_url ) ) : ?>
                                        <a href="<?php echo esc_url( home_url( $cp_url ) ); ?>" target="_blank">
                                            <img src="<?php echo esc_url( $cp_img ); ?>" alt="<?php echo esc_attr( $cp_alt ); ?>" class="img-responsive" />
                                        </a>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url( $cp_img ); ?>" alt="<?php echo esc_attr( $cp_alt ); ?>" class="img-responsive" />
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="clear"></div>
                </div>
            <?php endif; ?>

            <?php
            // =================================================================
            // 3. IDENTITY VERIFICATION BANNER
            // =================================================================
            if ( 'yes' === $settings['show_identity'] ) :
                $id_title_img  = ! empty( $settings['identity_title_image']['url'] ) ? $settings['identity_title_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_title_3.jpg';
                $id_banner_img = ! empty( $settings['identity_banner_image']['url'] ) ? $settings['identity_banner_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_img_3.jpg';
                $id_link       = ! empty( $settings['identity_link']['url'] ) ? $settings['identity_link']['url'] : '/home/article/detail/article_id/517';
            ?>
                <div class="mainpage-box">
                    <div class="mainpage-title col-center">
                        <img src="<?php echo esc_url( $id_title_img ); ?>" alt="Identity verification documents required by Japanese Secondhand Dealer Act" class="img-responsive" /> 
                    </div>
                    <div class="mainpage-content col-center">
                        <a href="<?php echo esc_url( home_url( $id_link ) ); ?>" target="_blank">
                            <img src="<?php echo esc_url( $id_banner_img ); ?>" alt="Identity verification documents required by Japanese Secondhand Dealer Act" class="img-responsive" />
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            // =================================================================
            // 4. BUYBACK PROCESS FLOW INFOGRAPHIC
            // =================================================================
            if ( 'yes' === $settings['show_flow'] ) :
                $flow_title_img = ! empty( $settings['flow_title_image']['url'] ) ? $settings['flow_title_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_title_4.jpg';
                $store_url      = ! empty( $settings['store_flow_url'] ) ? $settings['store_flow_url'] : '/shop-purchase-flow/';
                $mail_url       = ! empty( $settings['mail_flow_url'] ) ? $settings['mail_flow_url'] : '/delivery-purchase-flow/';
                $corp_text      = ! empty( $settings['corporate_btn_text'] ) ? $settings['corporate_btn_text'] : 'Corporate Clients Click Here';
                $corp_url       = ! empty( $settings['corporate_btn_url'] ) ? $settings['corporate_btn_url'] : '/corporation-purchase-flow/';
            ?>
                <div class="mainpage-box" style="margin:0;padding-bottom:20px;">
                    <div class="mainpage-title col-center">
                        <img src="<?php echo esc_url( $flow_title_img ); ?>" alt="Buyback Process" class="img-responsive" /> 
                    </div>
                    <div class="liucheng-box gutter-25 img-margin">
                        <!-- In-Store Flow -->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="<?php echo esc_url( home_url( $store_url ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_title_1.jpg" alt="In-Store Buyback" class="img-responsive" /></a> 
                            <div class="liucheng-dian-box">
                                <ul class="col-center">
                                    <li>
                                        <a href="<?php echo esc_url( home_url( $store_url . '#step1' ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_1.jpg" alt="1. Check buyback price on our website" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( $store_url . '#step2' ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_2.jpg" alt="2. Book your visit online (Walk-ins welcome)" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( $store_url . '#step3' ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_3.jpg" alt="3. Visit our store in Akihabara, Tokyo" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( $store_url . '#step4' ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_4.jpg" alt="4. Appraisal completed & instant cash payout" class="img-responsive" /></a> 
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Mail-in Flow -->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <a href="<?php echo esc_url( home_url( $mail_url ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_title_2.jpg" alt="Mail-in Buyback" class="img-responsive" /></a> 
                            <div class="liucheng-youji-box">
                                <ul class="col-center">
                                    <li>
                                        <a href="<?php echo esc_url( home_url( $mail_url . '#step1' ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_1.jpg" alt="1. Check current rates on website" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( $mail_url . '#step2' ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_2.jpg" alt="2. Ship device with required documents" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( $mail_url . '#step3' ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_3.jpg" alt="3. Receive assessment report by email" class="img-responsive" /></a> 
                                    </li>
                                    <li><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li>
                                        <a href="<?php echo esc_url( home_url( $mail_url . '#step4' ) ); ?>" target="_blank"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_4.jpg" alt="4. Same-day bank transfer payout" class="img-responsive" /></a> 
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <a href="<?php echo esc_url( home_url( $corp_url ) ); ?>" target="_blank">
                        <button type="button" class="btn btn-faren col-center"><?php echo esc_html( $corp_text ); ?></button>
                    </a> 
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}
