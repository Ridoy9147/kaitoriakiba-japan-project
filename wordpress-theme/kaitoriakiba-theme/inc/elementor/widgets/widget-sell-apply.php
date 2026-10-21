<?php
/**
 * Elementor Widget: Buyback Application Multi-Step Form
 * 
 * Allows visual customization of the 6-step online buyback application form
 * directly in Elementor page builder.
 * 
 * @package KaitoriAkiba
 * @author dev hridoy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kaitori_Sell_Apply_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kaitori_sell_apply';
    }

    public function get_title() {
        return __( 'Buyback Application Form (買取申込フォーム)', 'kaitoriakiba' );
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    public function get_categories() {
        return array( 'kaitoriakiba-elements' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Form Content & Headings', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'form_title',
            array(
                'label'       => __( 'Header Title', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => 'iPhone・スマホ買取 査定・オンライン申込',
                'label_block' => true,
            )
        );

        $this->add_control(
            'form_subtitle',
            array(
                'label'       => __( 'Subtitle', 'kaitoriakiba' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'     => 'かんたんステップ入力で即時査定＆買取お申込み。最短即日入金・送料無料！',
                'rows'        => 2,
            )
        );

        $this->add_control(
            'show_stepper',
            array(
                'label'        => __( 'Display Stepper Indicator', 'kaitoriakiba' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            )
        );

        $this->add_control(
            'default_carrier',
            array(
                'label'   => __( 'Default Selected Carrier', 'kaitoriakiba' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'SIMフリー'    => 'SIM FREE (SIMフリー)',
                    'docomo'        => 'docomo (ドコモ)',
                    'au'            => 'au (KDDI)',
                    'SoftBank'      => 'SoftBank (ソフトバンク)',
                    'RakutenMobile' => 'Rakuten (楽天モバイル)',
                ),
                'default' => 'SIMフリー',
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            array(
                'label' => __( 'Form Container Styling', 'kaitoriakiba' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'max_width',
            array(
                'label'      => __( 'Form Max Width (px)', 'kaitoriakiba' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 600,
                        'max' => 1200,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 880,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .apply-container' => 'max-width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $def_carrier   = ! empty( $settings['default_carrier'] ) ? $settings['default_carrier'] : 'SIMフリー';
        $form_title    = ! empty( $settings['form_title'] ) ? $settings['form_title'] : __( 'iPhone・スマホ買取 査定・オンライン申込', 'kaitoriakiba' );
        $form_subtitle = ! empty( $settings['form_subtitle'] ) ? $settings['form_subtitle'] : __( 'かんたんステップ入力で即時査定＆買取お申込み。最短即日入金・送料無料！', 'kaitoriakiba' );
        $show_stepper  = ! empty( $settings['show_stepper'] ) ? $settings['show_stepper'] : 'yes';

        $pre_model    = isset( $_GET['model'] ) ? sanitize_text_field( $_GET['model'] ) : '';
        $pre_storage  = isset( $_GET['storage'] ) ? sanitize_text_field( $_GET['storage'] ) : '256GB';
        $pre_carrier  = isset( $_GET['carrier'] ) ? sanitize_text_field( $_GET['carrier'] ) : $def_carrier;
        $pre_delivery = isset( $_GET['delivery'] ) ? sanitize_text_field( $_GET['delivery'] ) : 'mail';
        ?>
        <div class="apply-container" style="margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); padding: 36px 32px;">
            
            <div class="apply-header" style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 24px; font-weight: 900; color: #0f172a; margin-bottom: 8px;">
                    <i class="fa fa-mobile" style="color:#ef4444;"></i> <?php echo esc_html( $form_title ); ?>
                </h1>
                <p style="color: #64748b; font-size: 13.5px; margin: 0;">
                    <?php echo esc_html( $form_subtitle ); ?>
                </p>
            </div>

            <?php if ( $show_stepper === 'yes' ) : ?>
            <!-- Stepper Progress Header -->
            <div class="progress-stepper" style="display: flex; justify-content: space-between; position: relative; margin-bottom: 36px;">
                <div class="step-item active" id="step-node-1" style="position: relative; z-index: 2; text-align: center; width: 16.66%;">
                    <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: #ef4444; color: #ffffff; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #ef4444; font-size: 15px;">1</div>
                    <div class="step-label" style="font-size: 11.5px; font-weight: 700; color: #0f172a; line-height: 1.3;">端末情報<br><small>Details</small></div>
                </div>
                <div class="step-item" id="step-node-2" style="position: relative; z-index: 2; text-align: center; width: 16.66%;">
                    <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; color: #64748b; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #cbd5e1; font-size: 15px;">2</div>
                    <div class="step-label" style="font-size: 11.5px; font-weight: 700; color: #64748b; line-height: 1.3;">状態確認<br><small>Condition</small></div>
                </div>
                <div class="step-item" id="step-node-3" style="position: relative; z-index: 2; text-align: center; width: 16.66%;">
                    <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; color: #64748b; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #cbd5e1; font-size: 15px;">3</div>
                    <div class="step-label" style="font-size: 11.5px; font-weight: 700; color: #64748b; line-height: 1.3;">ロック確認<br><small>Security</small></div>
                </div>
                <div class="step-item" id="step-node-4" style="position: relative; z-index: 2; text-align: center; width: 16.66%;">
                    <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; color: #64748b; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #cbd5e1; font-size: 15px;">4</div>
                    <div class="step-label" style="font-size: 11.5px; font-weight: 700; color: #64748b; line-height: 1.3;">引渡方法<br><small>Delivery</small></div>
                </div>
                <div class="step-item" id="step-node-5" style="position: relative; z-index: 2; text-align: center; width: 16.66%;">
                    <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; color: #64748b; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #cbd5e1; font-size: 15px;">5</div>
                    <div class="step-label" style="font-size: 11.5px; font-weight: 700; color: #64748b; line-height: 1.3;">振込口座<br><small>Payment</small></div>
                </div>
                <div class="step-item" id="step-node-6" style="position: relative; z-index: 2; text-align: center; width: 16.66%;">
                    <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; color: #64748b; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #cbd5e1; font-size: 15px;">6</div>
                    <div class="step-label" style="font-size: 11.5px; font-weight: 700; color: #64748b; line-height: 1.3;">最終確認<br><small>Review</small></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Success Box (Hidden until AJAX responds) -->
            <div id="application-success-modal" style="display:none; text-align:center; padding: 40px 20px;">
                <div style="width:72px; height:72px; border-radius:50%; background:#dcfce7; color:#16a34a; font-size:36px; display:flex; align-items:center; justify-content:center; margin:0 auto 20px auto;">
                    <i class="fa fa-check"></i>
                </div>
                <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin-bottom:12px;">買取お申込みが完了いたしました！</h2>
                <p style="color:#475569; font-size:15px; margin-bottom:20px;">
                    受付番号: <strong id="displayOrderCode" style="color:#ef4444; font-size:18px;"></strong><br>
                    ご登録のメールアドレスに受付完了のご案内をお送りいたしました。
                </p>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; max-width:540px; margin:0 auto 24px auto; text-align:left;">
                    <h4 style="font-weight:800; margin-bottom:8px; color:#1e293b;"><i class="fa fa-id-card-o" style="color:#ef4444;"></i> 次のステップ：本人確認 (eKYC)</h4>
                    <p style="font-size:13px; color:#64748b; margin:0;">古物営業法により、初回のご利用には身分証明書の提出が必要です。オンラインで運転免許証またはマイナンバーカードの撮影・アップロードを行ってください。</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>" id="btnGoToKyc" class="btn btn-primary" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 32px; border-radius:10px; font-size:16px;">
                    本人確認 (eKYC) に進む &rarr;
                </a>
            </div>

            <!-- Form -->
            <form id="sellForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="estimated_price" id="hidden_estimated_price" value="195000">

                <!-- STEP 1: Device Details -->
                <div class="form-step active" id="step-1">
                    <h3 style="font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="color:#ef4444;">STEP 1</span> 買取希望の端末情報
                    </h3>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>機種名・モデル (Model)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <select name="device_model" id="input_model" class="form-control-custom" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <option value="iPhone 16 Pro Max" <?php selected($pre_model, 'iPhone 16 Pro Max'); ?>>iPhone 16 Pro Max (最高買取額 ¥235,000)</option>
                            <option value="iPhone 16 Pro" <?php selected($pre_model === '' || $pre_model === 'iPhone 16 Pro', true); ?>>iPhone 16 Pro (最高買取額 ¥195,000)</option>
                            <option value="iPhone 16 Plus" <?php selected($pre_model, 'iPhone 16 Plus'); ?>>iPhone 16 Plus (最高買取額 ¥145,000)</option>
                            <option value="iPhone 16" <?php selected($pre_model, 'iPhone 16'); ?>>iPhone 16 (最高買取額 ¥132,000)</option>
                            <option value="iPhone 15 Pro Max" <?php selected($pre_model, 'iPhone 15 Pro Max'); ?>>iPhone 15 Pro Max (最高買取額 ¥175,000)</option>
                            <option value="iPhone 15 Pro" <?php selected($pre_model, 'iPhone 15 Pro'); ?>>iPhone 15 Pro (最高買取額 ¥152,000)</option>
                            <option value="iPhone 15" <?php selected($pre_model, 'iPhone 15'); ?>>iPhone 15 (最高買取額 ¥105,000)</option>
                            <option value="Google Pixel 9 Pro" <?php selected($pre_model, 'Google Pixel 9 Pro'); ?>>Google Pixel 9 Pro (最高買取額 ¥135,000)</option>
                            <option value="Google Pixel 9" <?php selected($pre_model, 'Google Pixel 9'); ?>>Google Pixel 9 (最高買取額 ¥98,000)</option>
                            <option value="Galaxy S24 Ultra" <?php selected($pre_model, 'Galaxy S24 Ultra'); ?>>Galaxy S24 Ultra (最高買取額 ¥155,000)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>容量 (Storage)</span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                            </label>
                            <select name="device_storage" id="input_storage" class="form-control-custom" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <option value="128GB" <?php selected($pre_storage, '128GB'); ?>>128GB</option>
                                <option value="256GB" <?php selected($pre_storage, '256GB'); ?>>256GB</option>
                                <option value="512GB" <?php selected($pre_storage, '512GB'); ?>>512GB</option>
                                <option value="1TB" <?php selected($pre_storage, '1TB'); ?>>1TB</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>キャリア / 仕様 (Carrier)</span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                            </label>
                            <select name="device_carrier" id="input_carrier" class="form-control-custom" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <option value="SIMフリー" <?php selected($pre_carrier, 'SIMフリー'); ?>>Apple Store / 国内SIMフリー</option>
                                <option value="docomo" <?php selected($pre_carrier, 'docomo'); ?>>docomo (ドコモ)</option>
                                <option value="au" <?php selected($pre_carrier, 'au'); ?>>au (KDDI)</option>
                                <option value="SoftBank" <?php selected($pre_carrier, 'SoftBank'); ?>>SoftBank (ソフトバンク)</option>
                                <option value="RakutenMobile" <?php selected($pre_carrier, 'RakutenMobile'); ?>>Rakuten Mobile (楽天モバイル)</option>
                                <option value="海外版SIMフリー" <?php selected($pre_carrier, '海外版SIMフリー'); ?>>海外版SIMフリー</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>本体カラー (Color)</span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                            </label>
                            <input type="text" name="device_color" class="form-control-custom" placeholder="例: ナチュラルチタニウム、ブラック" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>IMEI / 製造番号 (15桁)</span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                            </label>
                            <input type="text" name="device_imei" maxlength="15" pattern="[0-9]{15}" class="form-control-custom" placeholder="35xxxxxxxxxxxxx (設定→一般→情報で確認)" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                    </div>

                    <div class="step-nav" style="display:flex; justify-content:flex-end; margin-top:24px;">
                        <button type="button" class="btn btn-primary" onclick="goToStep(2)" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 28px; border-radius:10px; font-size:15px;">
                            次へ進む：端末の状態確認 &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Condition -->
                <div class="form-step" id="step-2" style="display:none;">
                    <h3 style="font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="color:#ef4444;">STEP 2</span> 外観・動作状態の詳細チェック
                    </h3>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>液晶画面の状態 (Screen Condition)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <select name="screen_condition" class="form-control-custom" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <option value="新品未開封">新品未開封 (外箱のシール未開封)</option>
                            <option value="未使用品">未使用品 (通電確認のみ、傷なし、保護フィルムあり)</option>
                            <option value="傷なし美品" selected>傷なし美品 (日常使用、目立つ傷やスレなし)</option>
                            <option value="微細なスレあり">微細なスレあり (光に当てるとわかる小傷あり)</option>
                            <option value="目立つ傷あり">目立つ傷あり (引っかかりのある線傷、擦れ)</option>
                            <option value="画面割れ・ヒビ">画面割れ・ヒビ (液晶ガラス破損、表示乱れ、タッチ不良)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>本体外装・フレームの状態 (Body & Frame)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <select name="body_condition" class="form-control-custom" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <option value="新品同様">新品同様 (無傷・打痕なし)</option>
                            <option value="良好・小傷あり" selected>良好・ケース跡や微細な傷あり</option>
                            <option value="打痕・角削れあり">打痕・角削れ・塗装剥げあり</option>
                            <option value="本体曲がり・変形あり">本体曲がり・変形あり</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="font-weight:700; font-size:14px; margin-bottom:6px; display:block;">
                                バッテリー最大容量 (%)
                            </label>
                            <input type="number" name="battery_health" min="50" max="100" value="92" class="form-control-custom" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <small style="color:#64748b;">※iPhoneの「設定→バッテリー→バッテリーの状態」で確認可能</small>
                        </div>
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="font-weight:700; font-size:14px; margin-bottom:6px; display:block;">
                                カメラ・Face ID / Touch ID動作
                            </label>
                            <select name="camera_works" class="form-control-custom" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <option value="正常作動" selected>すべて正常に作動する</option>
                                <option value="カメラ不良">カメラに斑点・手ぶれ不良あり</option>
                                <option value="生体認証不可">Face ID / Touch IDが使用不可</option>
                                <option value="両方不良">両方とも動作不良</option>
                            </select>
                        </div>
                    </div>

                    <div class="step-nav" style="display:flex; justify-content:space-between; margin-top:24px;">
                        <button type="button" class="btn btn-default" onclick="goToStep(1)" style="font-weight:700; padding:12px 24px; border-radius:10px;">&larr; 前へ戻る</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(3)" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 28px; border-radius:10px; font-size:15px;">
                            次へ進む：セキュリティ確認 &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Security & Installment -->
                <div class="form-step" id="step-3" style="display:none;">
                    <h3 style="font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="color:#ef4444;">STEP 3</span> 残債状況およびセキュリティ解除の確認
                    </h3>

                    <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; padding:18px; margin-bottom:24px;">
                        <h4 style="font-size:15px; font-weight:800; color:#9a3412; margin-top:0; margin-bottom:8px;">
                            <i class="fa fa-credit-card"></i> 分割支払い中（ネットワーク利用制限△）の確認
                        </h4>
                        <p style="font-size:13px; color:#78350f; margin-bottom:12px;">
                            端末代金を分割払い中の端末でも高価買取いたします！完済予定または残債ありをお知らせください。
                        </p>
                        <label style="display:inline-flex; align-items:center; gap:8px; font-weight:700; margin-right:24px; cursor:pointer;">
                            <input type="radio" name="installment_status" value="no" checked onchange="toggleInstallmentDue(false);"> 一括完済済み (○判定)
                        </label>
                        <label style="display:inline-flex; align-items:center; gap:8px; font-weight:700; color:#c2410c; cursor:pointer;">
                            <input type="radio" name="installment_status" value="yes" onchange="toggleInstallmentDue(true);"> 代金分割支払い中 (△判定・残債あり)
                        </label>

                        <div id="installmentDueWrap" style="display:none; margin-top:12px;">
                            <label style="font-size:13px; font-weight:700;">おおよその残債金額 (円):</label>
                            <input type="number" name="installment_due_amount" placeholder="50000" style="border:1.5px solid #f97316; border-radius:8px; padding:6px 12px; width:200px;">
                        </div>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:24px;">
                        <h4 style="font-size:15px; font-weight:800; color:#1e293b; margin-top:0; margin-bottom:12px;">
                            <i class="fa fa-lock" style="color:#ef4444;"></i> ご発送・お持込前の必須セキュリティ解除
                        </h4>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; font-size:13.5px;">
                                <input type="checkbox" name="find_my_off" value="1" required style="width:18px; height:18px;">
                                「iPhoneを探す (アクティベーションロック)」をオフに解除しました (必須)
                            </label>
                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; font-size:13.5px;">
                                <input type="checkbox" name="apple_id_signout" value="1" required style="width:18px; height:18px;">
                                Apple ID / Google アカウントからサインアウトし、端末を初期化しました (必須)
                            </label>
                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; font-size:13.5px;">
                                <input type="checkbox" name="no_mdm_lock" value="1" required style="width:18px; height:18px;">
                                会社等の管理プロファイル (MDMロック) は入っていません (必須)
                            </label>
                        </div>
                    </div>

                    <div class="step-nav" style="display:flex; justify-content:space-between; margin-top:24px;">
                        <button type="button" class="btn btn-default" onclick="goToStep(2)" style="font-weight:700; padding:12px 24px; border-radius:10px;">&larr; 前へ戻る</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(4)" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 28px; border-radius:10px; font-size:15px;">
                            次へ進む：受取方法 &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 4: Delivery -->
                <div class="form-step" id="step-4" style="display:none;">
                    <h3 style="font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="color:#ef4444;">STEP 4</span> 買取方法（郵送宅配または店頭持込）
                    </h3>

                    <div class="delivery-options" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:16px; margin-bottom:24px;">
                        <label class="delivery-card selected" id="card_mail" style="border:2px solid #ef4444; border-radius:12px; padding:18px; cursor:pointer; background:#fff5f5; display:block;">
                            <input type="radio" name="delivery_method" value="mail" <?php checked($pre_delivery, 'mail'); ?> onchange="selectDelivery('mail');" style="margin-right:8px;">
                            <strong style="font-size:15px; color:#0f172a;"><i class="fa fa-truck" style="color:#ef4444;"></i> 郵送買取 (全国送料無料)</strong>
                            <p style="font-size:12.5px; color:#64748b; margin:8px 0 0 0;">自宅にいながらラクラク発送。無料梱包キットのお届けも承ります。端末到着日に即査定・即日振込！</p>
                        </label>

                        <label class="delivery-card" id="card_store" style="border:2px solid #e2e8f0; border-radius:12px; padding:18px; cursor:pointer; background:#fff; display:block;">
                            <input type="radio" name="delivery_method" value="store" <?php checked($pre_delivery, 'store'); ?> onchange="selectDelivery('store');" style="margin-right:8px;">
                            <strong style="font-size:15px; color:#0f172a;"><i class="fa fa-building" style="color:#ef4444;"></i> 秋葉原 店頭買取 (即現金化)</strong>
                            <p style="font-size:12.5px; color:#64748b; margin:8px 0 0 0;">秋葉原駅電気街口徒歩2分の実店舗にお持込。最短5分で現物査定＆その場で現金手渡し！</p>
                        </label>
                    </div>

                    <div id="mail_extra_fields" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:20px;">
                        <label style="font-weight:700; font-size:14px; margin-bottom:8px; display:block;">無料梱包キットの手配:</label>
                        <div style="display:flex; gap:20px;">
                            <label style="cursor:pointer;"><input type="radio" name="shipping_kit" value="キット希望" checked> 専用梱包資材・送り状を自宅へ届けてほしい (無料)</label>
                            <label style="cursor:pointer;"><input type="radio" name="shipping_kit" value="自分で梱包"> 自分で箱を用意して着払いで今すぐ発送する</label>
                        </div>
                    </div>

                    <div class="step-nav" style="display:flex; justify-content:space-between; margin-top:24px;">
                        <button type="button" class="btn btn-default" onclick="goToStep(3)" style="font-weight:700; padding:12px 24px; border-radius:10px;">&larr; 前へ戻る</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(5)" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 28px; border-radius:10px; font-size:15px;">
                            次へ進む：お振込先口座 &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 5: Payment & Customer Info -->
                <div class="form-step" id="step-5" style="display:none;">
                    <h3 style="font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="color:#ef4444;">STEP 5</span> お客様情報および買取金お振込口座
                    </h3>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="font-weight:700; font-size:13.5px; margin-bottom:4px; display:block;">氏名 (漢字) *</label>
                            <input type="text" name="applicant_name" placeholder="山田 太郎" class="form-control-custom" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="col-md-6 form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="font-weight:700; font-size:13.5px; margin-bottom:4px; display:block;">フリガナ (カタカナ) *</label>
                            <input type="text" name="applicant_kana" placeholder="ヤマダ タロウ" class="form-control-custom" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="font-weight:700; font-size:13.5px; margin-bottom:4px; display:block;">電話番号 *</label>
                            <input type="tel" name="applicant_phone" placeholder="09012345678" class="form-control-custom" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="col-md-6 form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="font-weight:700; font-size:13.5px; margin-bottom:4px; display:block;">メールアドレス *</label>
                            <input type="email" name="applicant_email" placeholder="example@kaitori-akiba.com" class="form-control-custom" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-top:14px; margin-bottom:20px;">
                        <h4 style="font-size:14px; font-weight:800; color:#1e293b; margin-top:0; margin-bottom:12px;">
                            <i class="fa fa-university" style="color:#ef4444;"></i> 買取代金のお振込先銀行口座 (振込手数料無料)
                        </h4>
                        <div class="row">
                            <div class="col-md-6 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;">金融機関名 (銀行名)</label>
                                <input type="text" name="bank_name" placeholder="三菱UFJ銀行 / ゆうちょ銀行 / 楽天銀行" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                            </div>
                            <div class="col-md-6 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;">支店名</label>
                                <input type="text" name="bank_branch" placeholder="秋葉原支店" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;">口座種別</label>
                                <select name="bank_account_type" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                                    <option value="普通">普通</option>
                                    <option value="当座">当座</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;">口座番号 (7桁)</label>
                                <input type="text" name="bank_account_number" placeholder="1234567" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                            </div>
                            <div class="col-md-4 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;">口座名義 (カタカナ)</label>
                                <input type="text" name="bank_holder_kana" placeholder="ヤマダ タロウ" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                            </div>
                        </div>
                    </div>

                    <div class="step-nav" style="display:flex; justify-content:space-between; margin-top:24px;">
                        <button type="button" class="btn btn-default" onclick="goToStep(4)" style="font-weight:700; padding:12px 24px; border-radius:10px;">&larr; 前へ戻る</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(6)" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 28px; border-radius:10px; font-size:15px;">
                            次へ進む：最終確認 &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 6: Review & Submit -->
                <div class="form-step" id="step-6" style="display:none;">
                    <h3 style="font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="color:#ef4444;">STEP 6</span> お申込み内容の確認と同意
                    </h3>

                    <!-- Estimated Summary Box -->
                    <div style="background:linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border:2px solid #fed7aa; border-radius:14px; padding:20px; margin-bottom:24px; text-align:center;">
                        <div style="font-size:13px; font-weight:700; color:#c2410c;">Web事前仮査定・買取上限目安金額</div>
                        <div style="font-size:38px; font-weight:900; color:#dc2626; margin:6px 0;">
                            ¥<span id="reviewEstimatedPrice">195,000</span>
                        </div>
                        <p style="font-size:12.5px; color:#78350f; margin:0;">
                            ※現物到着後の最終検品状態により確定いたします。当日価格保証が適用されます。
                        </p>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:24px;">
                        <h4 style="font-size:14px; font-weight:800; color:#1e293b; margin-top:0; margin-bottom:10px;">利用規約およびプライバシーポリシーへの同意</h4>
                        <div style="max-height:100px; overflow-y:auto; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px; font-size:12px; color:#64748b; line-height:1.6; margin-bottom:12px;">
                            Kaitori Akiba（以下「当社」）の買取サービスをご利用いただくにあたり、古物営業法に基づく本人確認手続きおよび利用規約に同意いただく必要がございます。盗難品、不正契約端末の買取は固くお断りいたします。
                        </div>
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; font-size:14px;">
                            <input type="checkbox" name="agree_terms" value="1" required style="width:18px; height:18px;">
                            利用規約、個人情報の取扱いに同意します (必須)
                        </label>
                    </div>

                    <div class="step-nav" style="display:flex; justify-content:space-between; margin-top:24px;">
                        <button type="button" class="btn btn-default" onclick="goToStep(5)" style="font-weight:700; padding:12px 24px; border-radius:10px;">&larr; 前へ戻る</button>
                        <button type="submit" id="btnSubmitSell" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:14px 40px; border-radius:10px; font-size:16px;">
                            <i class="fa fa-paper-plane"></i> 買取を申し込む (Submit Application)
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <script>
            function goToStep(stepNum) {
                for (var i = 1; i <= 6; i++) {
                    var stepEl = document.getElementById('step-' + i);
                    var nodeEl = document.getElementById('step-node-' + i);
                    if (stepEl) stepEl.style.display = (i === stepNum) ? 'block' : 'none';
                    if (nodeEl) {
                        var circle = nodeEl.querySelector('.step-circle');
                        if (i <= stepNum) {
                            circle.style.background = '#ef4444';
                            circle.style.borderColor = '#ef4444';
                            circle.style.color = '#fff';
                        } else {
                            circle.style.background = '#f1f5f9';
                            circle.style.borderColor = '#cbd5e1';
                            circle.style.color = '#64748b';
                        }
                    }
                }
                window.scrollTo({ top: document.querySelector('.apply-container').offsetTop - 30, behavior: 'smooth' });
            }

            function toggleInstallmentDue(show) {
                var el = document.getElementById('installmentDueWrap');
                if (el) el.style.display = show ? 'block' : 'none';
            }

            function selectDelivery(type) {
                var cardMail = document.getElementById('card_mail');
                var cardStore = document.getElementById('card_store');
                var mailExtra = document.getElementById('mail_extra_fields');
                if (type === 'mail') {
                    if (cardMail) { cardMail.style.borderColor = '#ef4444'; cardMail.style.background = '#fff5f5'; }
                    if (cardStore) { cardStore.style.borderColor = '#e2e8f0'; cardStore.style.background = '#fff'; }
                    if (mailExtra) mailExtra.style.display = 'block';
                } else {
                    if (cardMail) { cardMail.style.borderColor = '#e2e8f0'; cardMail.style.background = '#fff'; }
                    if (cardStore) { cardStore.style.borderColor = '#ef4444'; cardStore.style.background = '#fff5f5'; }
                    if (mailExtra) mailExtra.style.display = 'none';
                }
            }
        </script>
        <?php
    }
}
