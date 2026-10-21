<?php
/**
 * Elementor Announcement & Quick Guides Widget
 * 
 * 100% Authentic Kaitori Akiba Announcements matching index.html lines 848-952:
 * - 6 Quick Guide buttons (.home_liuxiang)
 * - Urgent Announcement box with expandable/collapsible details (.qingbao)
 * - 100% Fully Editable in Elementor.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Announcement_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_announcement_banner';
    }

    public function get_title() {
        return __( 'Announcements & Quick Guides (お知らせ・案内)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-alert';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {

        // =====================================================================
        // SECTION 1: QUICK GUIDES (6 BUTTONS)
        // =====================================================================
        $this->start_controls_section(
            'section_quick_guides',
            array(
                'label' => __( '1. Quick Guide Buttons (クイック案内)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_quick_links',
            array(
                'label'   => __( 'Show Quick Guide Buttons', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $repeater_guides = new \Elementor\Repeater();

        $repeater_guides->add_control(
            'guide_title',
            array(
                'label'       => __( 'Button Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'How to Register',
                'label_block' => true,
            )
        );

        $repeater_guides->add_control(
            'guide_url',
            array(
                'label'       => __( 'Link URL', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'default'     => array(
                    'url' => '/register/',
                ),
            )
        );

        $this->add_control(
            'quick_guides_list',
            array(
                'label'       => __( 'Quick Guide Items', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater_guides->get_controls(),
                'default'     => array(
                    array( 'guide_title' => 'How to Register', 'guide_url' => array( 'url' => '/register/' ) ),
                    array( 'guide_title' => 'How to Add to Cart', 'guide_url' => array( 'url' => '/devices/' ) ),
                    array( 'guide_title' => 'How to Apply for Buyback', 'guide_url' => array( 'url' => '/sell-apply/' ) ),
                    array( 'guide_title' => 'Price Guarantee Policy', 'guide_url' => array( 'url' => '/delivery-terms/' ) ),
                    array( 'guide_title' => 'How to Update Profile', 'guide_url' => array( 'url' => '/register/#login' ) ),
                    array( 'guide_title' => 'Frequently Asked Questions (FAQ)', 'guide_url' => array( 'url' => '/faq/' ) ),
                ),
                'title_field' => '{{{ guide_title }}}',
                'condition'   => array( 'show_quick_links' => 'yes' ),
            )
        );

        $this->end_controls_section();


        // =====================================================================
        // SECTION 2: URGENT ANNOUNCEMENT BOX
        // =====================================================================
        $this->start_controls_section(
            'section_urgent_notice',
            array(
                'label' => __( '2. Urgent Announcement Box (重要なお知らせ)', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_notice_box',
            array(
                'label'   => __( 'Show Urgent Notice Box', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            )
        );

        $this->add_control(
            'notice_title',
            array(
                'label'   => __( 'Notice Box Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '(Important) Urgent Announcements from Kaitori Akiba',
                'condition' => array( 'show_notice_box' => 'yes' ),
            )
        );

        $this->add_control(
            'notice_html_content',
            array(
                'label'   => __( 'Notice Content (HTML Supported)', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::WYSIWYG,
                'default' => '<p><a href="/news/detail/376" target="_blank"><span style="font-size:16px;color:#003399;"><strong><u>[Important] Notice Regarding Identity Verification &amp; Purchase Proof Requirement</u></strong></span></a></p>
<p><a href="/news/detail/368/" target="_blank"><span style="font-size:16px;color:#003399;"><strong><u>[Important] Deadline for Submitting Proof of Purchase (Receipt)</u></strong></span></a></p>
<p><a href="/news/detail/367/" target="_blank"><span style="font-size:16px;color:#003399;"><strong><u>[Important] Akihabara Main Store Business Hours Update</u></strong></span></a></p>
<p><a href="/news/detail/362" target="_blank"><span style="font-size:16px;color:#003399;"><strong><u>[Important] Revision of Acceptable Identification Documents for In-Store Buyback</u></strong></span></a></p>
<p><a href="/news/detail/361" target="_blank"><span style="font-size:16px;color:#003399;"><strong><u>[Important] Discontinuation of Health Insurance Card as Identification Document</u></strong></span></a></p>
<p><a href="/news/detail/289" target="_blank"><span style="font-size:16px;color:#003399;"><strong><u>[Important] Updated Terms for Free Shipping Mail-in Buyback</u></strong></span></a></p>
<p><a href="/news/detail/306/" target="_blank"><span style="font-size:16px;color:#003399;"><strong><u>Appraisal Policy for Items with Retail Warranty Stamps</u></strong></span></a></p>
<p><span style="background-color:#FFE500;color:#E53333;font-size:16px;"><strong>★ IMPORTANT: Please register an account and submit your buyback reservation from the product page prior to visit/shipment (Advance Reservation Required) ★</strong></span></p>
<p><br /></p>
<p><span style="color:#000000;font-size:14px;">Prices listed on our website represent current market reference quotes. The finalized buyback price will be confirmed in the Buyback Approval Email sent after application.</span></p>
<p><br /></p>
<p><span style="background-color:#FFE500;color:#E53333;font-size:14px;">【Regarding Buybacks】</span></p>
<p><span style="color:#000000;font-size:14px;">Prior reservation via our website is required. Unscheduled walk-ins may not be processed.</span></p>
<p><a href="/shop-purchase-flow/" target="_blank"><span style="color:#003399;font-size:14px;">Click here for In-Store Buyback Guide</span></a><br />
<a href="/delivery-purchase-flow/" target="_blank"><span style="color:#003399;font-size:14px;">Click here for Mail-in Buyback Guide</span></a></p>',
                'condition' => array( 'show_notice_box' => 'yes' ),
            )
        );

        $this->add_control(
            'btn_more_text',
            array(
                'label'   => __( 'Show More Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Show More',
                'condition' => array( 'show_notice_box' => 'yes' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        $title     = ! empty( $settings['notice_title'] ) ? $settings['notice_title'] : '(Important) Urgent Announcements from Kaitori Akiba';
        $btn_text  = ! empty( $settings['btn_more_text'] ) ? $settings['btn_more_text'] : 'Show More';
        ?>
        <div class="kaitori-announcement-wrap">
            
            <?php if ( 'yes' === $settings['show_quick_links'] && ! empty( $settings['quick_guides_list'] ) ) : ?>
                <div class="home_liuxiang">
                    <div class="container">
                        <div class="row home_lx_box">
                            <?php foreach ( $settings['quick_guides_list'] as $guide ) : 
                                $g_url  = ! empty( $guide['guide_url']['url'] ) ? $guide['guide_url']['url'] : '#';
                                $is_ext = ! empty( $guide['guide_url']['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
                                $g_txt  = ! empty( $guide['guide_title'] ) ? $guide['guide_title'] : '';
                            ?>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 home_lx">
                                    <div class="home_lx_td">
                                        <a href="<?php echo esc_url( home_url( $g_url ) ); ?>"<?php echo $is_ext; ?>><?php echo esc_html( $g_txt ); ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_notice_box'] ) : ?>
                <div class="container pding0">
                    <div style="background:#feecec; padding:20px; margin-bottom:10px;">
                        <div style="border-bottom:1px solid #ccc; padding-bottom:10px; font-weight:bold; color:#333;">
                            <?php echo esc_html( $title ); ?>
                        </div>
                        <div style="padding-top:20px;" class="qingbao" id="qb_<?php echo esc_attr( $widget_id ); ?>">
                            <?php echo $settings['notice_html_content']; ?>
                        </div>
                        <div class="dropdownbutton">
                            <button id="moreless_<?php echo esc_attr( $widget_id ); ?>" class="dropdown" onclick="qbToggle_<?php echo esc_attr( $widget_id ); ?>();">
                                <?php echo esc_html( $btn_text ); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    function qbToggle_<?php echo esc_attr( $widget_id ); ?>() {
                        var qb = document.getElementById('qb_<?php echo esc_attr( $widget_id ); ?>');
                        var btn = document.getElementById('moreless_<?php echo esc_attr( $widget_id ); ?>');
                        if (qb && btn) {
                            if (qb.classList.contains('qingbao')) {
                                qb.classList.remove('qingbao');
                                btn.innerText = 'Show Less';
                            } else {
                                qb.classList.add('qingbao');
                                btn.innerText = '<?php echo esc_js( $btn_text ); ?>';
                            }
                        }
                    }
                </script>
            <?php endif; ?>

        </div>
        <?php
    }
}
