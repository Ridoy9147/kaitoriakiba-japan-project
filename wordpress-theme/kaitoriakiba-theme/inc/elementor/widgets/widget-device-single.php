<?php
/**
 * Elementor Device Single & Buyback Calculator Widget
 * 
 * @package KaitoriAkiba
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Device_Single_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_device_calculator';
    }

    public function get_title() {
        return __( 'Device Buyback Calculator (端末買取計算機)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-price-table';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Device Configuration', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'device_id',
            array(
                'label'       => __( 'Select Device Post (Optional)', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::NUMBER,
                'description' => __( 'Leave blank to auto-detect from current page or use demo values.', 'kaitoriakiba' ),
            )
        );

        $this->add_control(
            'default_title',
            array(
                'label'   => __( 'Default Model Title', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'iPhone 16 Pro 256GB SIMフリー',
            )
        );

        $this->add_control(
            'default_price',
            array(
                'label'   => __( 'Default Base Price (¥)', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 168000,
            )
        );

        $this->add_control(
            'show_rank_table',
            array(
                'label'     => __( 'Show Condition Ranks S-D Table', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => 'yes',
            )
        );

        $this->add_control(
            'show_installment_notice',
            array(
                'label'     => __( 'Show Installment Notice (利用制限△)', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => 'yes',
            )
        );

        $this->add_control(
            'show_checklist',
            array(
                'label'     => __( 'Show Pre-shipment Checklist', 'kaitoriakiba' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => 'yes',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $post_id = ! empty( $settings['device_id'] ) ? intval( $settings['device_id'] ) : get_the_ID();
        $is_device_post = ( get_post_type( $post_id ) === 'kaitori_device' );

        $title      = $is_device_post ? get_the_title( $post_id ) : $settings['default_title'];
        $base_price = $is_device_post ? ( get_post_meta( $post_id, '_max_buyback_price', true ) ?: $settings['default_price'] ) : $settings['default_price'];
        $jan        = $is_device_post ? ( get_post_meta( $post_id, '_jan_code', true ) ?: '4549995734140' ) : '4549995734140';
        $model_num  = $is_device_post ? ( get_post_meta( $post_id, '_model_number', true ) ?: 'A3293' ) : 'A3293';
        
        $custom_img = $is_device_post ? get_post_meta( $post_id, '_device_primary_image_url', true ) : '';
        $thumb_url  = ! empty( $custom_img ) ? $custom_img : ( ( $is_device_post && has_post_thumbnail( $post_id ) ) ? get_the_post_thumbnail_url( $post_id, 'large' ) : get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png' );

        $gallery_meta = $is_device_post ? get_post_meta( $post_id, '_device_gallery_images', true ) : null;
        $gallery_list = array();
        if ( ! empty( $gallery_meta ) && is_array( $gallery_meta ) ) {
            $gallery_list = $gallery_meta;
        } else {
            $gallery_list = array(
                array( 'url' => get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Black.png', 'label' => 'Black Titanium' ),
                array( 'url' => get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Silver.png', 'label' => 'White Titanium' ),
                array( 'url' => get_template_directory_uri() . '/upload/goods/20260911/iPhone 18 Pro 256GB Glacier.png', 'label' => 'Natural Titanium' ),
            );
        }
        ?>
        <div class="kaitori-single-calc-widget">
            <div class="product-hero-card" style="background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.04); margin-bottom:30px;">
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-12 text-center">
                        <div style="background:#fff; border:1px solid #f1f5f9; border-radius:12px; padding:20px; min-height:340px; display:flex; align-items:center; justify-content:center;">
                            <img id="elMainProductImg" src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" style="max-height:300px; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="display:flex; justify-content:center; gap:10px; margin-top:14px; flex-wrap:wrap;">
                            <?php foreach ( $gallery_list as $g_idx => $g_item ) :
                                $g_url = is_array( $g_item ) ? $g_item['url'] : $g_item;
                                $g_label = is_array( $g_item ) && ! empty( $g_item['label'] ) ? $g_item['label'] : '';
                                if ( empty( $g_url ) ) continue;
                            ?>
                                <div class="gallery-thumb <?php echo $g_idx === 0 ? 'active' : ''; ?>" title="<?php echo esc_attr( $g_label ); ?>" style="width:50px; height:50px; border:<?php echo $g_idx === 0 ? '2px solid #ef4444' : '1px solid #e2e8f0'; ?>; border-radius:8px; padding:3px; cursor:pointer;" onclick="document.getElementById('elMainProductImg').src='<?php echo esc_url( $g_url ); ?>'">
                                    <img src="<?php echo esc_url( $g_url ); ?>" alt="<?php echo esc_attr( $g_label ); ?>" style="width:100%; height:100%; object-fit:contain;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-lg-7 col-md-7 col-sm-12">
                        <div style="border-bottom:1.5px solid #f1f5f9; padding-bottom:14px; margin-bottom:16px;">
                            <span style="display:inline-block; font-size:12px; font-weight:700; color:#ef4444; background:#fef2f2; padding:2px 8px; border-radius:4px; margin-bottom:6px;">Apple / 高価買取中</span>
                            <h2 style="font-size:22px; font-weight:900; color:#0f172a; margin:0 0 6px 0;"><?php echo esc_html( $title ); ?></h2>
                            <p style="font-size:12.5px; color:#64748b; margin:0;">型番: <?php echo esc_html( $model_num ); ?> | JAN: <?php echo esc_html( $jan ); ?></p>
                        </div>

                        <!-- Price display -->
                        <div style="background:linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border:2px solid #fed7aa; border-radius:12px; padding:16px 20px; margin-bottom:20px;">
                            <div style="font-size:12px; font-weight:700; color:#c2410c;">本日の最高買取上限価格 (新品未開封・SIMフリー)</div>
                            <div style="font-size:36px; font-weight:900; color:#dc2626; line-height:1.2;">
                                ¥<span id="elCalcPrice"><?php echo number_format( (int)$base_price ); ?></span>
                                <small style="font-size:12px; color:#78350f; font-weight:700; background:#fed7aa; padding:2px 8px; border-radius:12px; vertical-align:middle; margin-left:8px;">税込・手数料0円</small>
                            </div>
                        </div>

                        <!-- Storage selector -->
                        <div style="margin-bottom:16px;">
                            <label style="font-size:13px; font-weight:800; color:#1e293b; display:block; margin-bottom:6px;">容量を選択:</label>
                            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:8px;">
                                <button type="button" class="btn btn-default" style="font-weight:700;" onclick="elUpdateStorage('128GB', <?php echo intval($base_price * 0.9); ?>)">128GB</button>
                                <button type="button" class="btn btn-danger" style="font-weight:700;" onclick="elUpdateStorage('256GB', <?php echo intval($base_price); ?>)">256GB</button>
                                <button type="button" class="btn btn-default" style="font-weight:700;" onclick="elUpdateStorage('512GB', <?php echo intval($base_price * 1.1); ?>)">512GB</button>
                                <button type="button" class="btn btn-default" style="font-weight:700;" onclick="elUpdateStorage('1TB', <?php echo intval($base_price * 1.2); ?>)">1TB</button>
                            </div>
                        </div>

                        <!-- CTA Apply -->
                        <div style="margin-top:20px;">
                            <a id="elApplyBtn" href="<?php echo esc_url( home_url( '/sell-apply/?model=' . urlencode( $title ) . '&storage=256GB' ) ); ?>" class="btn btn-danger btn-block" style="padding:14px; font-size:16px; font-weight:800; border-radius:8px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                <i class="fa fa-paper-plane"></i> この機種の買取を申し込む (Apply for Buyback)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ( $settings['show_rank_table'] === 'yes' ) : ?>
                <!-- Ranks S-D Table -->
                <div style="background:#ffffff; border-radius:14px; border:1px solid #e2e8f0; padding:24px; margin-bottom:24px;">
                    <h3 style="font-size:17px; font-weight:800; color:#0f172a; margin-top:0; margin-bottom:16px;">
                        <i class="fa fa-sliders" style="color:#ef4444;"></i> コンディション別買取価格一覧表
                    </h3>
                    <table class="table table-bordered table-striped" style="margin-bottom:0;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th>ランク</th>
                                <th>状態の目安</th>
                                <th>買取目安価格</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span style="background:#dbeafe; color:#1e40af; font-weight:800; padding:2px 8px; border-radius:4px;">Rank S</span></td>
                                <td>新品未開封 (Factory Sealed)</td>
                                <td><strong style="color:#dc2626; font-size:16px;" id="elRankS">¥<?php echo number_format( (int)$base_price ); ?></strong></td>
                            </tr>
                            <tr>
                                <td><span style="background:#dcfce7; color:#15803d; font-weight:800; padding:2px 8px; border-radius:4px;">Rank A</span></td>
                                <td>未使用品・通電確認のみ (Opened Box)</td>
                                <td><strong style="color:#dc2626; font-size:16px;" id="elRankA">¥<?php echo number_format( (int)round($base_price * 0.92) ); ?></strong></td>
                            </tr>
                            <tr>
                                <td><span style="background:#fef9c3; color:#854d0e; font-weight:800; padding:2px 8px; border-radius:4px;">Rank B</span></td>
                                <td>中古美品・目立つ傷なし (Used Pristine)</td>
                                <td><strong style="color:#dc2626; font-size:16px;" id="elRankB">¥<?php echo number_format( (int)round($base_price * 0.84) ); ?></strong></td>
                            </tr>
                            <tr>
                                <td><span style="background:#ffedd5; color:#9a3412; font-weight:800; padding:2px 8px; border-radius:4px;">Rank C</span></td>
                                <td>中古良品・小傷あり (Used Minor Scratches)</td>
                                <td><strong style="color:#dc2626; font-size:16px;" id="elRankC">¥<?php echo number_format( (int)round($base_price * 0.76) ); ?></strong></td>
                            </tr>
                            <tr>
                                <td><span style="background:#fee2e2; color:#b91c1c; font-weight:800; padding:2px 8px; border-radius:4px;">Rank D</span></td>
                                <td>画面割れ・故障・難あり (Cracked / Broken)</td>
                                <td><strong style="color:#dc2626; font-size:16px;" id="elRankD">¥<?php echo number_format( (int)round($base_price * 0.48) ); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ( $settings['show_installment_notice'] === 'yes' ) : ?>
                <!-- Installment Info Box -->
                <div style="background:#ffffff; border-radius:14px; border:1px solid #e2e8f0; padding:20px; margin-bottom:24px;">
                    <h4 style="font-size:15px; font-weight:800; color:#0f172a; margin-top:0;">
                        <i class="fa fa-info-circle" style="color:#ef4444;"></i> 分割支払い中（ネットワーク利用制限△）でも高価買取対応！
                    </h4>
                    <p style="font-size:13px; color:#475569; margin-bottom:0;">
                        代金分割支払い中（残債あり）のiPhoneも減額を最小限（-¥3,000〜¥5,000程度）に抑えて即日買取可能です。申込みフォームで残債ありを選択してください。
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <script>
            function elUpdateStorage(storage, price) {
                document.getElementById('elCalcPrice').innerText = price.toLocaleString();
                if(document.getElementById('elRankS')) document.getElementById('elRankS').innerText = '¥' + price.toLocaleString();
                if(document.getElementById('elRankA')) document.getElementById('elRankA').innerText = '¥' + Math.round(price * 0.92).toLocaleString();
                if(document.getElementById('elRankB')) document.getElementById('elRankB').innerText = '¥' + Math.round(price * 0.84).toLocaleString();
                if(document.getElementById('elRankC')) document.getElementById('elRankC').innerText = '¥' + Math.round(price * 0.76).toLocaleString();
                if(document.getElementById('elRankD')) document.getElementById('elRankD').innerText = '¥' + Math.round(price * 0.48).toLocaleString();
                var btn = document.getElementById('elApplyBtn');
                btn.href = "<?php echo esc_url( home_url( '/sell-apply/' ) ); ?>?model=<?php echo urlencode( $title ); ?>&storage=" + encodeURIComponent(storage);
            }
        </script>
        <?php
    }
}
