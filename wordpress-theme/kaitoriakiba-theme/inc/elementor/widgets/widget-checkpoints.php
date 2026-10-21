<?php
/**
 * Elementor Key Checkpoints & Process Infographic Widget
 * 
 * 100% Fully Editable in Elementor:
 * - Key Selling Points (Custom titles, highlight colors, and repeater cards with images/texts/links)
 * - Pre-assessment Checkpoints (Custom titles, highlight colors, and repeater cards with images/texts/links)
 * - Identity Verification (Secondhand Dealer Act) banner and link
 * - Buyback Process Flow Infographic (In-store, Mail-in, Corporate)
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
        // SECTION 1: KEY SELLING POINTS (高価買取のコツ)
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
            'points_header_type',
            array(
                'label'   => __( 'Header Style', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'text',
                'options' => array(
                    'text'  => __( 'Editable Text Heading (Recommended)', 'kaitoriakiba' ),
                    'image' => __( 'Classic Image Banner', 'kaitoriakiba' ),
                ),
                'condition' => array( 'show_points' => 'yes' ),
            )
        );

        $this->add_control(
            'points_title_prefix',
            array(
                'label'       => __( 'Title Prefix', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'iPhoneやスマートフォンを',
                'condition'   => array( 'show_points' => 'yes', 'points_header_type' => 'text' ),
            )
        );

        $this->add_control(
            'points_title_highlight',
            array(
                'label'       => __( 'Title Highlight Text (Red Accent)', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '高く売る',
                'condition'   => array( 'show_points' => 'yes', 'points_header_type' => 'text' ),
            )
        );

        $this->add_control(
            'points_title_suffix',
            array(
                'label'       => __( 'Title Suffix', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'ために',
                'condition'   => array( 'show_points' => 'yes', 'points_header_type' => 'text' ),
            )
        );

        $this->add_control(
            'points_subtitle',
            array(
                'label'       => __( 'Subtitle', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'Key Selling Points',
                'condition'   => array( 'show_points' => 'yes', 'points_header_type' => 'text' ),
            )
        );

        $this->add_control(
            'points_title_image',
            array(
                'label'   => __( 'Header Image Banner', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/mainpage/mainpage_title_1.jpg',
                ),
                'condition' => array( 'show_points' => 'yes', 'points_header_type' => 'image' ),
            )
        );

        // Selling Points Repeater
        $repeater_points = new \Elementor\Repeater();

        $repeater_points->add_control(
            'item_image',
            array(
                'label'   => __( 'Card Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/mainpage/gaojia_1.jpg',
                ),
            )
        );

        $repeater_points->add_control(
            'item_title',
            array(
                'label'       => __( 'Card Caption / Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'データを完全消去し初期状態にする',
                'label_block' => true,
            )
        );

        $repeater_points->add_control(
            'item_url',
            array(
                'label'       => __( 'Link URL', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com or /sell-apply/', 'kaitoriakiba' ),
                'default'     => array(
                    'url' => '/sell-apply/',
                ),
            )
        );

        $this->add_control(
            'selling_points_list',
            array(
                'label'       => __( 'Selling Point Cards', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_points->get_controls(),
                'default'     => array(
                    array(
                        'item_title' => 'データを完全消去し初期状態にする',
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_1.jpg' ),
                        'item_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                    array(
                        'item_title' => '購入時の付属品をそろえる',
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_2.jpg' ),
                        'item_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                    array(
                        'item_title' => '外観をキレイにしましょう',
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_3.jpg' ),
                        'item_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                    array(
                        'item_title' => 'SIMロックを解除する',
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/gaojia_4.jpg' ),
                        'item_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                ),
                'title_field' => '{{{ item_title }}}',
                'condition'   => array( 'show_points' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 2: CHECKPOINTS BEFORE APPLYING (買取のお申込み前の確認事項)
        // =====================================================================
        $this->start_controls_section(
            'section_checkpoints',
            array(
                'label' => __( '2. Checkpoints Before Applying (事前確認事項)', 'kaitoriakiba' ),
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
            'notes_header_type',
            array(
                'label'   => __( 'Header Style', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'text',
                'options' => array(
                    'text'  => __( 'Editable Text Heading (Recommended)', 'kaitoriakiba' ),
                    'image' => __( 'Classic Image Banner', 'kaitoriakiba' ),
                ),
                'condition' => array( 'show_notes' => 'yes' ),
            )
        );

        $this->add_control(
            'notes_title_prefix',
            array(
                'label'       => __( 'Title Prefix', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '買取のお申込み前に',
                'condition'   => array( 'show_notes' => 'yes', 'notes_header_type' => 'text' ),
            )
        );

        $this->add_control(
            'notes_title_highlight',
            array(
                'label'       => __( 'Title Highlight Text (Red Accent)', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'ご確認ください！',
                'condition'   => array( 'show_notes' => 'yes', 'notes_header_type' => 'text' ),
            )
        );

        $this->add_control(
            'notes_title_image',
            array(
                'label'   => __( 'Header Image Banner', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/mainpage/mainpage_title_2.jpg',
                ),
                'condition' => array( 'show_notes' => 'yes', 'notes_header_type' => 'image' ),
            )
        );

        // Checkpoints Repeater
        $repeater_notes = new \Elementor\Repeater();

        $repeater_notes->add_control(
            'item_image',
            array(
                'label'   => __( 'Circle Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_1.jpg',
                ),
            )
        );

        $repeater_notes->add_control(
            'item_title',
            array(
                'label'       => __( 'Caption / Description', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '買取不可品の事前チェック',
                'label_block' => true,
            )
        );

        $repeater_notes->add_control(
            'item_url',
            array(
                'label'       => __( 'Link URL', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com or /sell-apply/', 'kaitoriakiba' ),
                'default'     => array(
                    'url' => '/sell-apply/',
                ),
            )
        );

        $this->add_control(
            'checkpoints_list',
            array(
                'label'       => __( 'Checkpoint Circle Cards', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_notes->get_controls(),
                'default'     => array(
                    array(
                        'item_title' => '買取不可品の事前確認',
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_1.jpg' ),
                        'item_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                    array(
                        'item_title' => '利用制限(△/×)ステータス確認',
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_2.jpg' ),
                        'item_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                    array(
                        'item_title' => 'SIMカード・SDカードの抜き取り',
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_3.jpg' ),
                        'item_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                    array(
                        'item_title' => 'バッテリー充電・初期化状態確認',
                        'item_image' => array( 'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_2_4.jpg' ),
                        'item_url'   => array( 'url' => '/sell-apply/' ),
                    ),
                ),
                'title_field' => '{{{ item_title }}}',
                'condition'   => array( 'show_notes' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 3: IDENTITY VERIFICATION / SECONDHAND DEALER ACT
        // =====================================================================
        $this->start_controls_section(
            'section_identity',
            array(
                'label' => __( '3. Identity Verification Banner (本人確認・古物営業法)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_identity',
            array(
                'label'   => __( 'Show Identity Verification Box', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'identity_title_image',
            array(
                'label'   => __( 'Title Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/mainpage/mainpage_title_3.jpg',
                ),
                'condition' => array( 'show_identity' => 'yes' ),
            )
        );

        $this->add_control(
            'identity_banner_image',
            array(
                'label'   => __( 'Banner Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/mainpage/mainpage_img_3.jpg',
                ),
                'condition' => array( 'show_identity' => 'yes' ),
            )
        );

        $this->add_control(
            'identity_link',
            array(
                'label'       => __( 'Banner Link', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'default'     => array(
                    'url' => '/kyc-verify/',
                ),
                'condition' => array( 'show_identity' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 4: BUYBACK FLOW INFOGRAPHIC (買取の流れ)
        // =====================================================================
        $this->start_controls_section(
            'section_flow',
            array(
                'label' => __( '4. Buyback Process Flow (買取の流れ)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_flow',
            array(
                'label'   => __( 'Show Buyback Flow Section', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'flow_title_image',
            array(
                'label'   => __( 'Flow Header Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => $theme_uri . '/assets/images/mainpage/mainpage_title_4.jpg',
                ),
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->add_control(
            'store_flow_url',
            array(
                'label'   => __( 'In-Store Buyback Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/sell-apply/?delivery=store',
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->add_control(
            'mail_flow_url',
            array(
                'label'   => __( 'Mail-in Buyback Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/sell-apply/?delivery=mail',
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->add_control(
            'corporate_btn_text',
            array(
                'label'   => __( 'Corporate Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '法人様買取相談はこちら (Corporate Clients Click Here)',
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->add_control(
            'corporate_btn_url',
            array(
                'label'   => __( 'Corporate Button Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/sell-apply/?type=corporate',
                'condition' => array( 'show_flow' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // STYLING SECTION
        // =====================================================================
        $this->start_controls_section(
            'section_style',
            array(
                'label' => __( 'Design & Colors', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'highlight_color',
            array(
                'label'     => __( 'Highlight Accent Color', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ef4444',
                'selectors' => array(
                    '{{WRAPPER}} .kaitori-accent-highlight' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'box_border_radius',
            array(
                'label'      => __( 'Box Border Radius (px)', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array( 'min' => 0, 'max' => 30 ),
                ),
                'default'    => array( 'unit' => 'px', 'size' => 12 ),
                'selectors'  => array(
                    '{{WRAPPER}} .mainpage-box' => 'border-radius: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        ?>
        <div class="kaitori-checkpoints-wrapper" style="margin: 20px 0;">

            <?php
            // =================================================================
            // 1. KEY SELLING POINTS
            // =================================================================
            if ( 'yes' === $settings['show_points'] ) :
                ?>
                <div class="mainpage-box" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 20px; margin-bottom:24px; box-shadow:0 4px 15px rgba(0,0,0,0.02);">
                    
                    <?php if ( 'text' === $settings['points_header_type'] ) : ?>
                        <div class="mainpage-title col-center text-center" style="margin-bottom:20px;">
                            <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0 0 6px 0; letter-spacing:0.5px;">
                                <span style="background:linear-gradient(180deg, transparent 60%, #fef08a 60%); padding:0 4px;"><?php echo esc_html( $settings['points_title_prefix'] ); ?></span> 
                                <span class="kaitori-accent-highlight" style="color:#ef4444; font-size:28px; text-decoration:none; padding:0 4px;"><?php echo esc_html( $settings['points_title_highlight'] ); ?></span> 
                                <span style="background:linear-gradient(180deg, transparent 60%, #fef08a 60%); padding:0 4px;"><?php echo esc_html( $settings['points_title_suffix'] ); ?></span>
                            </h2>
                            <?php if ( ! empty( $settings['points_subtitle'] ) ) : ?>
                                <div style="font-size:14px; font-weight:800; color:#1e3a8a; text-transform:uppercase; letter-spacing:1px; margin-top:4px;">
                                    <?php echo esc_html( $settings['points_subtitle'] ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else : 
                        $title_img = ! empty( $settings['points_title_image']['url'] ) ? $settings['points_title_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_title_1.jpg';
                    ?>
                        <div class="mainpage-title col-center text-center" style="margin-bottom:18px;">
                            <img src="<?php echo esc_url( $title_img ); ?>" alt="Key Checkpoints to Sell Your Phone at the Highest Price" class="img-responsive" style="margin:0 auto; max-width:100%; height:auto;" /> 
                            <?php if ( ! empty( $settings['points_subtitle'] ) ) : ?>
                                <h2 style="font-size:15px; font-weight:800; color:#1e3a8a; margin-top:8px;"><?php echo esc_html( $settings['points_subtitle'] ); ?></h2>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Selling Points Cards Grid -->
                    <?php if ( ! empty( $settings['selling_points_list'] ) ) : ?>
                        <div class="row gutter-10" style="display:flex; flex-wrap:wrap; margin:0 -5px;">
                            <?php foreach ( $settings['selling_points_list'] as $item ) : 
                                $card_img = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : $theme_uri . '/assets/images/mainpage/gaojia_1.jpg';
                                $card_url = ! empty( $item['item_url']['url'] ) ? $item['item_url']['url'] : home_url( '/sell-apply/' );
                                $is_ext   = ! empty( $item['item_url']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
                            ?>
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 text-center" style="padding:5px; margin-bottom:10px;">
                                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; transition:transform 0.2s, box-shadow 0.2s; height:100%; display:flex; flex-direction:column;">
                                        <a href="<?php echo esc_url( $card_url ); ?>"<?php echo $is_ext; ?> style="text-decoration:none; color:inherit; display:block; flex:1;">
                                            <div style="padding:10px; background:#fff; border-bottom:1px solid #f1f5f9;">
                                                <img src="<?php echo esc_url( $card_img ); ?>" alt="<?php echo esc_attr( $item['item_title'] ); ?>" class="img-responsive" style="margin:0 auto; max-height:120px; object-fit:contain;" />
                                            </div>
                                            <?php if ( ! empty( $item['item_title'] ) ) : ?>
                                                <div style="padding:10px 8px; font-size:12px; font-weight:700; color:#1e293b; line-height:1.4;">
                                                    <?php echo esc_html( $item['item_title'] ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="clearfix"></div>
                </div>
            <?php endif; ?>


            <?php
            // =================================================================
            // 2. CHECKPOINTS BEFORE APPLYING
            // =================================================================
            if ( 'yes' === $settings['show_notes'] ) :
                ?>
                <div class="mainpage-box" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 20px; margin-bottom:24px; box-shadow:0 4px 15px rgba(0,0,0,0.02);">
                    
                    <?php if ( 'text' === $settings['notes_header_type'] ) : ?>
                        <div class="mainpage-title col-center text-center" style="margin-bottom:20px;">
                            <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin:0; letter-spacing:0.5px;">
                                <span style="background:linear-gradient(180deg, transparent 60%, #fef08a 60%); padding:0 4px;"><?php echo esc_html( $settings['notes_title_prefix'] ); ?></span> 
                                <span class="kaitori-accent-highlight" style="color:#ef4444; font-size:28px; padding:0 4px;"><?php echo esc_html( $settings['notes_title_highlight'] ); ?></span>
                            </h2>
                        </div>
                    <?php else : 
                        $notes_img = ! empty( $settings['notes_title_image']['url'] ) ? $settings['notes_title_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_title_2.jpg';
                    ?>
                        <div class="mainpage-title col-center text-center" style="margin-bottom:18px;">
                            <img src="<?php echo esc_url( $notes_img ); ?>" alt="Please check before applying for buyback!" class="img-responsive" style="margin:0 auto; max-width:100%; height:auto;" /> 
                        </div>
                    <?php endif; ?>

                    <!-- Checkpoint Circle Cards Grid -->
                    <?php if ( ! empty( $settings['checkpoints_list'] ) ) : ?>
                        <div class="row gutter-10" style="display:flex; flex-wrap:wrap; margin:0 -5px;">
                            <?php foreach ( $settings['checkpoints_list'] as $item ) : 
                                $circle_img = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_img_2_1.jpg';
                                $circle_url = ! empty( $item['item_url']['url'] ) ? $item['item_url']['url'] : home_url( '/sell-apply/' );
                                $is_ext     = ! empty( $item['item_url']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
                            ?>
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 text-center" style="padding:5px; margin-bottom:10px;">
                                    <div style="background:#ffffff; border:1px solid #f1f5f9; border-radius:10px; padding:12px 8px; transition:transform 0.2s; height:100%; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                                        <a href="<?php echo esc_url( $circle_url ); ?>"<?php echo $is_ext; ?> style="text-decoration:none; color:inherit; display:block;">
                                            <div style="width:110px; height:110px; margin:0 auto 10px auto; display:flex; align-items:center; justify-content:center;">
                                                <img src="<?php echo esc_url( $circle_img ); ?>" alt="<?php echo esc_attr( $item['item_title'] ); ?>" class="img-responsive" style="max-width:100%; max-height:100%; object-fit:contain;" />
                                            </div>
                                            <?php if ( ! empty( $item['item_title'] ) ) : ?>
                                                <div style="font-size:12px; font-weight:700; color:#334155; line-height:1.4;">
                                                    <?php echo esc_html( $item['item_title'] ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="clearfix"></div>
                </div>
            <?php endif; ?>


            <?php
            // =================================================================
            // 3. IDENTITY VERIFICATION BANNER
            // =================================================================
            if ( 'yes' === $settings['show_identity'] ) :
                $id_title_img  = ! empty( $settings['identity_title_image']['url'] ) ? $settings['identity_title_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_title_3.jpg';
                $id_banner_img = ! empty( $settings['identity_banner_image']['url'] ) ? $settings['identity_banner_image']['url'] : $theme_uri . '/assets/images/mainpage/mainpage_img_3.jpg';
                $id_link       = ! empty( $settings['identity_link']['url'] ) ? $settings['identity_link']['url'] : home_url( '/kyc-verify/' );
                ?>
                <div class="mainpage-box" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:24px; box-shadow:0 4px 15px rgba(0,0,0,0.02);">
                    <div class="mainpage-title col-center text-center" style="margin-bottom:14px;">
                        <img src="<?php echo esc_url( $id_title_img ); ?>" alt="Identity verification documents required by Japanese Secondhand Dealer Act" class="img-responsive" style="margin:0 auto; max-width:100%; height:auto;" /> 
                    </div>
                    <div class="mainpage-content col-center text-center">
                        <a href="<?php echo esc_url( $id_link ); ?>">
                            <img src="<?php echo esc_url( $id_banner_img ); ?>" alt="Identity verification documents" class="img-responsive" style="margin:0 auto; border-radius:8px; max-width:100%; height:auto;" />
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
                $store_url      = ! empty( $settings['store_flow_url'] ) ? $settings['store_flow_url'] : home_url( '/sell-apply/?delivery=store' );
                $mail_url       = ! empty( $settings['mail_flow_url'] ) ? $settings['mail_flow_url'] : home_url( '/sell-apply/?delivery=mail' );
                $corp_text      = ! empty( $settings['corporate_btn_text'] ) ? $settings['corporate_btn_text'] : '法人様買取相談はこちら (Corporate Clients Click Here)';
                $corp_url       = ! empty( $settings['corporate_btn_url'] ) ? $settings['corporate_btn_url'] : home_url( '/sell-apply/?type=corporate' );
                ?>
                <div class="mainpage-box" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 20px; margin-bottom:20px; box-shadow:0 4px 15px rgba(0,0,0,0.02);">
                    <div class="mainpage-title col-center text-center" style="margin-bottom:20px;">
                        <img src="<?php echo esc_url( $flow_title_img ); ?>" alt="Buyback Process" class="img-responsive" style="margin:0 auto; max-width:100%; height:auto;" /> 
                    </div>
                    <div class="liucheng-box gutter-25 img-margin row">
                        <!-- In-Store Flow -->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:20px;">
                            <a href="<?php echo esc_url( $store_url ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_title_1.jpg" alt="In-Store Buyback" class="img-responsive" style="width:100%; margin-bottom:12px;" /></a> 
                            <div class="liucheng-dian-box">
                                <ul class="col-center" style="list-style:none; padding:0; margin:0;">
                                    <li style="margin-bottom:8px;">
                                        <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_1.jpg" alt="1. Check buyback price on our website" class="img-responsive" style="margin:0 auto;" /></a> 
                                    </li>
                                    <li class="text-center" style="color:#ef4444; margin-bottom:8px;"><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li style="margin-bottom:8px;">
                                        <a href="<?php echo esc_url( $store_url ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_2.jpg" alt="2. Book your visit online" class="img-responsive" style="margin:0 auto;" /></a> 
                                    </li>
                                    <li class="text-center" style="color:#ef4444; margin-bottom:8px;"><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li style="margin-bottom:8px;">
                                        <a href="<?php echo esc_url( home_url( '/access/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_3.jpg" alt="3. Visit our store in Akihabara" class="img-responsive" style="margin:0 auto;" /></a> 
                                    </li>
                                    <li class="text-center" style="color:#ef4444; margin-bottom:8px;"><i class="fa fa-2x fa-sort-desc col-center"></i></li>
                                    <li style="margin-bottom:8px;">
                                        <a href="<?php echo esc_url( $store_url ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_dian_img_4.jpg" alt="4. Instant cash payout" class="img-responsive" style="margin:0 auto;" /></a> 
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Mail-in Flow -->
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:20px;">
                            <a href="<?php echo esc_url( $mail_url ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_title_2.jpg" alt="Mail-in Buyback" class="img-responsive" style="width:100%; margin-bottom:12px;" /></a> 
                            <div class="liucheng-youji-box">
                                <ul class="col-center" style="list-style:none; padding:0; margin:0;">
                                    <li style="margin-bottom:8px;">
                                        <a href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_1.jpg" alt="1. Check current rates on website" class="img-responsive" style="margin:0 auto;" /></a> 
                                    </li>
                                    <li class="text-center" style="color:#10b981; margin-bottom:8px;"><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li style="margin-bottom:8px;">
                                        <a href="<?php echo esc_url( $mail_url ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_2.jpg" alt="2. Ship device with required documents" class="img-responsive" style="margin:0 auto;" /></a> 
                                    </li>
                                    <li class="text-center" style="color:#10b981; margin-bottom:8px;"><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li style="margin-bottom:8px;">
                                        <a href="<?php echo esc_url( $mail_url ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_3.jpg" alt="3. Receive assessment report by email" class="img-responsive" style="margin:0 auto;" /></a> 
                                    </li>
                                    <li class="text-center" style="color:#10b981; margin-bottom:8px;"><i class="fa fa-2x fa-sort-down col-center"></i></li>
                                    <li style="margin-bottom:8px;">
                                        <a href="<?php echo esc_url( $mail_url ); ?>"><img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/mainpage/liucheng_youji_img_4.jpg" alt="4. Same-day bank transfer payout" class="img-responsive" style="margin:0 auto;" /></a> 
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div style="text-align:center; margin-top:20px;">
                        <a href="<?php echo esc_url( $corp_url ); ?>" style="text-decoration:none;">
                            <button type="button" class="btn btn-faren col-center" style="background:#1e3a8a; color:#ffffff; font-weight:800; font-size:15px; padding:12px 28px; border-radius:8px; border:none; box-shadow:0 4px 12px rgba(30,58,138,0.25); cursor:pointer;">
                                <i class="fa fa-building-o" style="margin-right:8px;"></i><?php echo esc_html( $corp_text ); ?>
                            </button>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}
