<?php
/**
 * Elementor What's News Tabbed Box Widget
 * 
 * Renders the authentic topicInner home section with trumpet icon, category tabs, and news items.
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
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'News Settings', 'kaitoriakiba' ),
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

        $this->end_controls_section();
    }

    protected function render() {
        $settings  = $this->get_settings_for_display();
        $theme_uri = get_template_directory_uri();
        $title     = ! empty( $settings['news_title'] ) ? $settings['news_title'] : "What's News (新着情報・お知らせ)";
        ?>
        <div class="topicInner home" style="margin-bottom: 25px;">
            <div class="topicHeader">
                <p class="topicTtl">
                    <img src="<?php echo esc_url( $theme_uri ); ?>/assets/images/subpage/trumpet_icon.png" width="32px" style="margin-right: 16px" alt="News Icon" />
                    <?php echo esc_html( $title ); ?>
                </p>
                <ul class="topicCategory">
                    <li class="tc01 cur"><span class="all" data-id="all"><?php _e( 'All', 'kaitoriakiba' ); ?></span></li>
                    <li class="tc02" data-id="1"><span class="blogTopic"><?php _e( 'Topics', 'kaitoriakiba' ); ?></span></li>
                    <li class="tc02" data-id="2"><span class="blogTopic"><?php _e( 'Information', 'kaitoriakiba' ); ?></span></li>
                </ul>
            </div>
            <div class="topicList">
                <div class="topicWrap">
                    <ul class="clearflex show">
                        <li class="col-xs-12 col-md-6">
                            <div class="mThumbnail">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>">
                                    <img src="<?php echo esc_url( $theme_uri ); ?>/upload/article/20260916/message_yoroshiku_casual.png" class="img-responsive" alt="News" />
                                </a>
                            </div>
                            <div class="layout_cell">
                                <p class="date">2026.09.16</p>
                                <p class="title"><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>">【秋葉原店】通常営業時間のご案内 (10:00〜19:30)</a></p>
                                <p class="msg"><a href="<?php echo esc_url( home_url( '/access/' ) ); ?>">いつも買取アキバをご利用いただきありがとうございます。秋葉原店は年中無休で店頭買取を実施しております。</a></p>
                            </div>
                        </li>
                        <li class="col-xs-12 col-md-6">
                            <div class="mThumbnail">
                                <a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>">
                                    <img src="<?php echo esc_url( $theme_uri ); ?>/upload/article/20260707/スクリーンショット 2026-07-07 172258.png" class="img-responsive" alt="eKYC" />
                                </a>
                            </div>
                            <div class="layout_cell">
                                <p class="date">2026.07.07</p>
                                <p class="title"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>">【本人確認】オンラインeKYCスピード認証システム導入</a></p>
                                <p class="msg"><a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>">古物営業法に基づく本人確認手続きがスマホひとつで完了。運転免許証・マイナンバーカードのアップロードに対応。</a></p>
                            </div>
                        </li>
                        <li class="col-xs-12 col-md-6">
                            <div class="mThumbnail">
                                <a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>">
                                    <img src="<?php echo esc_url( $theme_uri ); ?>/upload/article/20260609/5f270b7f-2361-4448-9981-f16c42ea9875.png" class="img-responsive" alt="Mail In" />
                                </a>
                            </div>
                            <div class="layout_cell">
                                <p class="date">2026.06.09</p>
                                <p class="title"><a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>">【郵送買取】安全な梱包と無料発送キットのご案内</a></p>
                                <p class="msg"><a href="<?php echo esc_url( home_url( '/sell-apply/?delivery=mail' ) ); ?>">輸送中の箱潰れを防ぐため、端末を気泡緩衝材（プチプチ）で丁寧に保護して発送してください。</a></p>
                            </div>
                        </li>
                        <li class="col-xs-12 col-md-6">
                            <div class="mThumbnail">
                                <a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>">
                                    <img src="<?php echo esc_url( $theme_uri ); ?>/upload/article/20260317/123456.png" class="img-responsive" alt="Price Guarantee" />
                                </a>
                            </div>
                            <div class="layout_cell">
                                <p class="date">2026.03.17</p>
                                <p class="title"><a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>">【相場更新】iPhone 16 / 15 シリーズ高価買取価格改定のお知らせ</a></p>
                                <p class="msg"><a href="<?php echo esc_url( home_url( '/devices/?cat=iphone' ) ); ?>">最新相場に基づきiPhone各シリーズの最高買取上限価格を更新いたしました。分割中(△)も同額買取対応！</a></p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="topicFooter">
                <a class="btn-ys more" href="<?php echo esc_url( home_url( '/devices/' ) ); ?>"><?php _e( 'View All / 一覧を見る', 'kaitoriakiba' ); ?></a>
            </div>
        </div>
        <?php
    }
}
