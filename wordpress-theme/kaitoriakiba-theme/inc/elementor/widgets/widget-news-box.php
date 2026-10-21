<?php
/**
 * Elementor What's News Tabbed Box Widget
 * 
 * Renders the authentic topicInner home section with trumpet icon, category tabs, and news items.
 * Fully customizable via Elementor controls & repeaters.
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_News_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_news_box';
    }

    public function get_title() {
        return __( "What's News Box (新着お知らせ)", 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        // Section: Header & Tabs
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Header & Tabs', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'news_title',
            array(
                'label'   => __( 'Section Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => "What's News (新着情報・お知らせ)",
            )
        );

        $this->add_control(
            'custom_icon',
            array(
                'label'   => __( 'Trumpet Icon Image (Optional)', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => array(
                    'url' => get_template_directory_uri() . '/assets/images/subpage/trumpet_icon.png',
                ),
            )
        );

        $this->add_control(
            'tab1_label',
            array(
                'label'   => __( 'Tab 1 Label (All)', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'All',
            )
        );

        $this->add_control(
            'tab2_label',
            array(
                'label'   => __( 'Tab 2 Label (Topics)', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Topics',
            )
        );

        $this->add_control(
            'tab3_label',
            array(
                'label'   => __( 'Tab 3 Label (Information)', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Information',
            )
        );

        $this->add_control(
            'view_all_text',
            array(
                'label'   => __( 'View All Button Text', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'View All / 一覧を見る',
            )
        );

        $this->add_control(
            'view_all_url',
            array(
                'label'   => __( 'View All Button Link', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/devices/',
            )
        );

        $this->end_controls_section();

        // Section: News Articles Repeater
        $this->start_controls_section(
            'section_news_items',
            array(
                'label' => __( 'News Articles List', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'item_image',
            array(
                'label'   => __( 'Thumbnail Image', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
            )
        );

        $repeater->add_control(
            'item_date',
            array(
                'label'   => __( 'Date', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '2026.09.16',
            )
        );

        $repeater->add_control(
            'item_title',
            array(
                'label'   => __( 'Article Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '【秋葉原店】通常営業時間のご案内 (10:00〜19:30)',
            )
        );

        $repeater->add_control(
            'item_excerpt',
            array(
                'label'   => __( 'Article Excerpt / Message', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'いつも買取アキバをご利用いただきありがとうございます。秋葉原店は年中無休で店頭買取を実施しております。',
            )
        );

        $repeater->add_control(
            'item_url',
            array(
                'label'   => __( 'Link URL', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '/access/',
            )
        );

        $this->add_control(
            'news_list',
            array(
                'label'       => __( 'News Items', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => array(
                    array(
                        'item_date'    => '2026.09.16',
                        'item_title'   => '【秋葉原店】通常営業時間のご案内 (10:00〜19:30)',
                        'item_excerpt' => 'いつも買取アキバをご利用いただきありがとうございます。秋葉原店は年中無休で店頭買取を実施しております。',
                        'item_url'     => '/access/',
                        'item_image'   => array( 'url' => get_template_directory_uri() . '/upload/article/20260916/message_yoroshiku_casual.png' ),
                    ),
                    array(
                        'item_date'    => '2026.07.07',
                        'item_title'   => '【本人確認】オンラインeKYCスピード認証システム導入',
                        'item_excerpt' => '古物営業法に基づく本人確認手続きがスマホひとつで完了。運転免許証・マイナンバーカードのアップロードに対応。',
                        'item_url'     => '/kyc-verify/',
                        'item_image'   => array( 'url' => get_template_directory_uri() . '/upload/article/20260707/スクリーンショット 2026-07-07 172258.png' ),
                    ),
                    array(
                        'item_date'    => '2026.06.09',
                        'item_title'   => '【郵送買取】安全な梱包と無料発送キットのご案内',
                        'item_excerpt' => '輸送中の箱潰れを防ぐため、端末を気泡緩衝材（プチプチ）で丁寧に保護して発送してください。',
                        'item_url'     => '/sell-apply/?delivery=mail',
                        'item_image'   => array( 'url' => get_template_directory_uri() . '/upload/article/20260609/5f270b7f-2361-4448-9981-f16c42ea9875.png' ),
                    ),
                    array(
                        'item_date'    => '2026.03.17',
                        'item_title'   => '【相場更新】iPhone 16 / 15 シリーズ高価買取価格改定のお知らせ',
                        'item_excerpt' => '最新相場に基づきiPhone各シリーズの最高買取上限価格を更新いたしました。分割中(△)も同額買取対応！',
                        'item_url'     => '/devices/?cat=iphone',
                        'item_image'   => array( 'url' => get_template_directory_uri() . '/upload/article/20260317/123456.png' ),
                    ),
                ),
                'title_field' => '{{{ item_title }}}',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        $title     = ! empty( $settings['news_title'] ) ? $settings['news_title'] : "What's News (新着情報・お知らせ)";
        $icon_url  = ! empty( $settings['custom_icon']['url'] ) ? $settings['custom_icon']['url'] : ( $theme_uri . '/assets/images/subpage/trumpet_icon.png' );
        
        $tab1 = ! empty( $settings['tab1_label'] ) ? $settings['tab1_label'] : 'All';
        $tab2 = ! empty( $settings['tab2_label'] ) ? $settings['tab2_label'] : 'Topics';
        $tab3 = ! empty( $settings['tab3_label'] ) ? $settings['tab3_label'] : 'Information';

        $more_txt = ! empty( $settings['view_all_text'] ) ? $settings['view_all_text'] : 'View All / 一覧を見る';
        $more_url = ! empty( $settings['view_all_url'] ) ? home_url( $settings['view_all_url'] ) : home_url( '/devices/' );

        $news_list = ! empty( $settings['news_list'] ) && is_iterable( $settings['news_list'] ) ? $settings['news_list'] : array();
        ?>
        <div class="topicInner home" style="margin-bottom: 25px;">
            <div class="topicHeader">
                <p class="topicTtl">
                    <img src="<?php echo esc_url( $icon_url ); ?>" width="32px" style="margin-right: 16px" alt="News Icon" />
                    <?php echo esc_html( $title ); ?>
                </p>
                <ul class="topicCategory">
                    <li class="tc01 cur"><span class="all" data-id="all"><?php echo esc_html( $tab1 ); ?></span></li>
                    <li class="tc02" data-id="1"><span class="blogTopic"><?php echo esc_html( $tab2 ); ?></span></li>
                    <li class="tc02" data-id="2"><span class="blogTopic"><?php echo esc_html( $tab3 ); ?></span></li>
                </ul>
            </div>
            <div class="topicList">
                <div class="topicWrap">
                    <ul class="clearflex show">
                        <?php if ( ! empty( $news_list ) ) : ?>
                            <?php foreach ( $news_list as $item ) : 
                                $img_src = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : ( $theme_uri . '/upload/article/20260916/message_yoroshiku_casual.png' );
                                $date    = ! empty( $item['item_date'] ) ? $item['item_date'] : '';
                                $ititle  = ! empty( $item['item_title'] ) ? $item['item_title'] : '';
                                $excerpt = ! empty( $item['item_excerpt'] ) ? $item['item_excerpt'] : '';
                                $iurl    = ! empty( $item['item_url'] ) ? home_url( $item['item_url'] ) : home_url();
                            ?>
                                <li class="col-xs-12 col-md-6" style="margin-bottom: 20px;">
                                    <div class="mThumbnail">
                                        <a href="<?php echo esc_url( $iurl ); ?>">
                                            <img src="<?php echo esc_url( $img_src ); ?>" class="img-responsive" alt="<?php echo esc_attr( $ititle ); ?>" style="width: 100%; object-fit: cover; border-radius: 6px;" />
                                        </a>
                                    </div>
                                    <div class="layout_cell">
                                        <?php if ( ! empty( $date ) ) : ?>
                                            <p class="date"><?php echo esc_html( $date ); ?></p>
                                        <?php endif; ?>
                                        <p class="title"><a href="<?php echo esc_url( $iurl ); ?>"><?php echo esc_html( $ititle ); ?></a></p>
                                        <?php if ( ! empty( $excerpt ) ) : ?>
                                            <p class="msg"><a href="<?php echo esc_url( $iurl ); ?>"><?php echo esc_html( $excerpt ); ?></a></p>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="topicFooter">
                <a class="btn-ys more" href="<?php echo esc_url( $more_url ); ?>"><?php echo esc_html( $more_txt ); ?></a>
            </div>
        </div>
        <?php
    }
}
