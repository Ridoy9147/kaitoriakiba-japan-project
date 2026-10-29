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
        // Gate: require login + eKYC (skips in the Elementor editor so admins can design).
        if ( function_exists( 'kaitoriakiba_maybe_block_sell_form' ) && kaitoriakiba_maybe_block_sell_form() ) {
            return;
        }

        $settings = $this->get_settings_for_display();

        $def_carrier   = ! empty( $settings['default_carrier'] ) ? $settings['default_carrier'] : 'SIMフリー';
        $form_title    = kaitori_t( 'iPhone & Smartphone Buyback — Online Quote & Application', 'iPhone・スマホ買取 査定・オンライン申込' );
        $form_subtitle = kaitori_t( 'Easy step-by-step input for an instant quote and application. Same-day payment, free shipping!', 'かんたんステップ入力で即時査定＆買取お申込み。最短即日入金・送料無料！' );
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
            <!-- Stepper Progress Header (2 Steps) -->
            <div class="progress-stepper" style="display: flex; justify-content: space-around; max-width: 520px; margin: 0 auto 36px auto; position: relative;">
                <div class="step-item active" id="step-node-1" style="position: relative; z-index: 2; text-align: center; width: 45%;">
                    <div class="step-circle" style="width: 42px; height: 42px; border-radius: 50%; background: #ef4444; color: #ffffff; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #ef4444; font-size: 16px;">1</div>
                    <div class="step-label" style="font-size: 12.5px; font-weight: 700; color: #0f172a; line-height: 1.3;"><?php kaitori_te( 'Device & Condition', '端末情報・状態' ); ?><br><small style="color:#64748b; font-weight:400;"><?php kaitori_te( 'Device Details', '詳細・状態確認' ); ?></small></div>
                </div>
                <div class="step-item" id="step-node-2" style="position: relative; z-index: 2; text-align: center; width: 45%;">
                    <div class="step-circle" style="width: 42px; height: 42px; border-radius: 50%; background: #f1f5f9; color: #64748b; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #cbd5e1; font-size: 16px;">2</div>
                    <div class="step-label" style="font-size: 12.5px; font-weight: 700; color: #64748b; line-height: 1.3;"><?php kaitori_te( 'Your Details & Account', 'お客様情報・口座' ); ?><br><small style="color:#64748b; font-weight:400;"><?php kaitori_te( 'Details & Bank (Optional)', '情報・口座（任意）' ); ?></small></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Success Box (Hidden until AJAX responds) -->
            <div id="application-success-modal" style="display:none; text-align:center; padding: 40px 20px;">
                <div style="width:72px; height:72px; border-radius:50%; background:#dcfce7; color:#16a34a; font-size:36px; display:flex; align-items:center; justify-content:center; margin:0 auto 20px auto;">
                    <i class="fa fa-check"></i>
                </div>
                <h2 style="font-size:24px; font-weight:900; color:#0f172a; margin-bottom:12px;"><?php kaitori_te( 'Your buyback application is complete!', '買取お申込みが完了いたしました！' ); ?></h2>
                <p style="color:#475569; font-size:15px; margin-bottom:20px;">
                    <?php kaitori_te( 'Order No.', '受付番号' ); ?>: <strong id="displayOrderCode" style="color:#ef4444; font-size:18px;"></strong><br>
                    <?php kaitori_te( 'A confirmation has been sent to your registered email address.', 'ご登録のメールアドレスに受付完了のご案内をお送りいたしました。' ); ?>
                </p>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; max-width:540px; margin:0 auto 24px auto; text-align:left;">
                    <h4 style="font-weight:800; margin-bottom:8px; color:#1e293b;"><i class="fa fa-id-card-o" style="color:#ef4444;"></i> <?php kaitori_te( 'Next step: Identity Verification (eKYC)', '次のステップ：本人確認 (eKYC)' ); ?></h4>
                    <p style="font-size:13px; color:#64748b; margin:0;"><?php kaitori_te( 'Under the Antique Dealer Act, first-time use requires submitting an ID. Please photograph and upload your driver license or My Number card online.', '古物営業法により、初回のご利用には身分証明書の提出が必要です。オンラインで運転免許証またはマイナンバーカードの撮影・アップロードを行ってください。' ); ?></p>
                </div>
                <a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>" id="btnGoToKyc" class="btn btn-primary" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 32px; border-radius:10px; font-size:16px;">
                    <?php kaitori_te( 'Proceed to Identity Verification (eKYC)', '本人確認 (eKYC) に進む' ); ?> &rarr;
                </a>
            </div>

            <!-- Form -->
            <form id="sellForm" method="post" enctype="multipart/form-data">
                <?php if ( function_exists( 'kaitoriakiba_honeypot_fields' ) ) { kaitoriakiba_honeypot_fields(); } ?>
                <input type="hidden" name="estimated_price" id="hidden_estimated_price" value="195000">
                <input type="hidden" name="find_my_off" value="1">
                <input type="hidden" name="apple_id_signout" value="1">
                <input type="hidden" name="no_mdm_lock" value="1">
                <input type="hidden" name="installment_status" value="no">
                <input type="hidden" name="delivery_method" value="mail">

                <!-- STEP 1: Device & Condition Details -->
                <div class="form-step active" id="step-1">
                    <h3 style="font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="color:#ef4444;">STEP 1</span> <?php kaitori_te( 'Device & condition details', '買取希望の端末情報・状態確認' ); ?>
                    </h3>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span><?php kaitori_te( 'Model', '機種名・モデル (Model)' ); ?></span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Required', '必須' ); ?></span>
                        </label>
                        <select name="device_model" id="input_model" class="form-control-custom" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <option value="iPhone 16 Pro Max" <?php selected($pre_model, 'iPhone 16 Pro Max'); ?>>iPhone 16 Pro Max (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>235,000)</option>
                            <option value="iPhone 16 Pro" <?php selected($pre_model === '' || $pre_model === 'iPhone 16 Pro', true); ?>>iPhone 16 Pro (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>195,000)</option>
                            <option value="iPhone 16 Plus" <?php selected($pre_model, 'iPhone 16 Plus'); ?>>iPhone 16 Plus (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>145,000)</option>
                            <option value="iPhone 16" <?php selected($pre_model, 'iPhone 16'); ?>>iPhone 16 (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>132,000)</option>
                            <option value="iPhone 15 Pro Max" <?php selected($pre_model, 'iPhone 15 Pro Max'); ?>>iPhone 15 Pro Max (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>175,000)</option>
                            <option value="iPhone 15 Pro" <?php selected($pre_model, 'iPhone 15 Pro'); ?>>iPhone 15 Pro (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>152,000)</option>
                            <option value="iPhone 15" <?php selected($pre_model, 'iPhone 15'); ?>>iPhone 15 (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>105,000)</option>
                            <option value="Google Pixel 9 Pro" <?php selected($pre_model, 'Google Pixel 9 Pro'); ?>>Google Pixel 9 Pro (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>135,000)</option>
                            <option value="Google Pixel 9" <?php selected($pre_model, 'Google Pixel 9'); ?>>Google Pixel 9 (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>98,000)</option>
                            <option value="Galaxy S24 Ultra" <?php selected($pre_model, 'Galaxy S24 Ultra'); ?>>Galaxy S24 Ultra (<?php kaitori_te( 'Max ¥', '最高買取額 ¥' ); ?>155,000)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span><?php kaitori_te( 'Storage', '容量 (Storage)' ); ?></span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Required', '必須' ); ?></span>
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
                                <span><?php kaitori_te( 'Carrier / spec', 'キャリア / 仕様 (Carrier)' ); ?></span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Required', '必須' ); ?></span>
                            </label>
                            <select name="device_carrier" id="input_carrier" class="form-control-custom" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <option value="SIMフリー" <?php selected($pre_carrier, 'SIMフリー'); ?>><?php kaitori_te( 'Apple Store / domestic SIM-free', 'Apple Store / 国内SIMフリー' ); ?></option>
                                <option value="docomo" <?php selected($pre_carrier, 'docomo'); ?>>docomo (ドコモ)</option>
                                <option value="au" <?php selected($pre_carrier, 'au'); ?>>au (KDDI)</option>
                                <option value="SoftBank" <?php selected($pre_carrier, 'SoftBank'); ?>>SoftBank (ソフトバンク)</option>
                                <option value="RakutenMobile" <?php selected($pre_carrier, 'RakutenMobile'); ?>>Rakuten Mobile (楽天モバイル)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span><?php kaitori_te( 'Color', '本体カラー (Color)' ); ?></span>
                                <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Required', '必須' ); ?></span>
                            </label>
                            <input type="text" name="device_color" class="form-control-custom" placeholder="<?php echo esc_attr( kaitori_t( 'e.g. Natural Titanium, Black', '例: ナチュラルチタニウム、ブラック' ) ); ?>" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="col-md-6 form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span><?php kaitori_te( 'IMEI / Serial (15 digits)', 'IMEI / 製造番号 (15桁)' ); ?></span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                            </label>
                            <input type="text" name="device_imei" maxlength="15" class="form-control-custom" placeholder="<?php echo esc_attr( kaitori_t( '35xxxxxxxxxxxxx (Optional)', '35xxxxxxxxxxxxx (任意)' ) ); ?>" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                    </div>

                    <!-- Condition & Functions (Merged into Step 1) -->
                    <div style="margin-top:10px; margin-bottom:24px; border-top:1.5px dashed #e2e8f0; padding-top:20px;">
                        <h4 style="font-size: 15px; font-weight: 800; color: #1e293b; margin-top: 0; margin-bottom: 16px;">
                            <i class="fa fa-sliders" style="color:#ef4444;"></i> <?php kaitori_te( 'Device condition & functions check (Optional)', '外観・動作状態の詳細チェック（任意）' ); ?>
                        </h4>

                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span><?php kaitori_te( 'Screen Condition', '液晶画面の状態 (Screen Condition)' ); ?></span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                            </label>
                            <select name="screen_condition" class="form-control-custom" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <option value="新品未開封"><?php kaitori_te( 'Sealed new (box seal unopened)', '新品未開封 (外箱のシール未開封)' ); ?></option>
                                <option value="未使用品"><?php kaitori_te( 'Unused (power-on check only, no scratches, film on)', '未使用品 (通電確認のみ、傷なし、保護フィルムあり)' ); ?></option>
                                <option value="傷なし美品" selected><?php kaitori_te( 'Mint, no scratches (daily use, no visible scratches/scuffs)', '傷なし美品 (日常使用、目立つ傷やスレなし)' ); ?></option>
                                <option value="微細なスレあり"><?php kaitori_te( 'Fine scuffs (small scratches visible under light)', '微細なスレあり (光に当てるとわかる小傷あり)' ); ?></option>
                                <option value="目立つ傷あり"><?php kaitori_te( 'Visible scratches (catching line scratches, scuffs)', '目立つ傷あり (引っかかりのある線傷、擦れ)' ); ?></option>
                                <option value="画面割れ・ヒビ"><?php kaitori_te( 'Cracked screen (glass damage, display/touch issues)', '画面割れ・ヒビ (液晶ガラス破損、表示乱れ、タッチ不良)' ); ?></option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span><?php kaitori_te( 'Body & Frame condition', '本体外装・フレームの状態 (Body & Frame)' ); ?></span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                            </label>
                            <select name="body_condition" class="form-control-custom" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <option value="新品同様"><?php kaitori_te( 'Like new (no scratches/dents)', '新品同様 (無傷・打痕なし)' ); ?></option>
                                <option value="良好・小傷あり" selected><?php kaitori_te( 'Good (case marks / fine scratches)', '良好・ケース跡や微細な傷あり' ); ?></option>
                                <option value="打痕・角削れあり"><?php kaitori_te( 'Dents / corner wear / paint loss', '打痕・角削れ・塗装剥げあり' ); ?></option>
                                <option value="本体曲がり・変形あり"><?php kaitori_te( 'Bent / deformed body', '本体曲がり・変形あり' ); ?></option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group" style="margin-bottom:20px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                    <span><?php kaitori_te( 'Battery max capacity (%)', 'バッテリー最大容量 (%)' ); ?></span>
                                    <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                                </label>
                                <input type="number" name="battery_health" min="50" max="100" placeholder="92" class="form-control-custom" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <small style="color:#64748b; font-size:12px; display:block; margin-top:4px;"><?php kaitori_te( '* Check in iPhone Settings → Battery → Battery Health (Optional)', '※iPhoneの「設定→バッテリー→バッテリーの状態」で確認可能（任意）' ); ?></small>
                            </div>
                            <div class="col-md-6 form-group" style="margin-bottom:20px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                    <span><?php kaitori_te( 'Camera · Face ID / Touch ID', 'カメラ・Face ID / Touch ID動作' ); ?></span>
                                    <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                                </label>
                                <select name="camera_works" class="form-control-custom" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                    <option value="正常作動" selected><?php kaitori_te( 'Everything works', 'すべて正常に作動する' ); ?></option>
                                    <option value="カメラ不良"><?php kaitori_te( 'Camera has spots / shake issues', 'カメラに斑点・手ぶれ不良あり' ); ?></option>
                                    <option value="生体認証不可"><?php kaitori_te( 'Face ID / Touch ID unusable', 'Face ID / Touch IDが使用不可' ); ?></option>
                                    <option value="両方不良"><?php kaitori_te( 'Both faulty', '両方とも動作不良' ); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="step-nav" style="display:flex; justify-content:flex-end; margin-top:24px;">
                        <button type="button" class="btn btn-primary" onclick="goToStep(2)" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 28px; border-radius:10px; font-size:15px;">
                            <?php kaitori_te( 'Next: Your details & bank account', '次へ進む：お客様情報・振込口座' ); ?> &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Your Details & Payout Bank Account (Optional) -->
                <div class="form-step" id="step-2" style="display:none;">
                    <h3 style="font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="color:#ef4444;">STEP 2</span> <?php kaitori_te( 'Your details & payout bank account', 'お客様情報および買取金お振込口座' ); ?>
                        <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:3px 8px; border-radius:4px; font-weight:600; margin-left:8px; vertical-align:middle;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                    </h3>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13.5px; margin-bottom:4px;">
                                <span><?php kaitori_te( 'Full Name (Kanji)', '氏名 (漢字)' ); ?></span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                            </label>
                            <input type="text" name="applicant_name" placeholder="<?php echo esc_attr( kaitori_t( 'Taro Yamada (Optional)', '山田 太郎 (任意)' ) ); ?>" class="form-control-custom" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="col-md-6 form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13.5px; margin-bottom:4px;">
                                <span><?php kaitori_te( 'Furigana (Katakana)', 'フリガナ (カタカナ)' ); ?></span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                            </label>
                            <input type="text" name="applicant_kana" placeholder="<?php echo esc_attr( kaitori_t( 'ヤマダ タロウ (Optional)', 'ヤマダ タロウ (任意)' ) ); ?>" class="form-control-custom" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13.5px; margin-bottom:4px;">
                                <span><?php kaitori_te( 'Phone Number', '電話番号' ); ?></span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                            </label>
                            <input type="tel" name="applicant_phone" placeholder="09012345678" class="form-control-custom" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="col-md-6 form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13.5px; margin-bottom:4px;">
                                <span><?php kaitori_te( 'Email Address', 'メールアドレス' ); ?></span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;"><?php kaitori_te( 'Optional', '任意' ); ?></span>
                            </label>
                            <input type="email" name="applicant_email" placeholder="example@kaitori-akiba.com" class="form-control-custom" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-top:14px; margin-bottom:20px;">
                        <h4 style="font-size:14px; font-weight:800; color:#1e293b; margin-top:0; margin-bottom:12px;">
                            <i class="fa fa-university" style="color:#ef4444;"></i> <?php kaitori_te( 'Payout bank account (no transfer fees) — Optional', '買取代金のお振込先銀行口座 (振込手数料無料) — 任意' ); ?>
                        </h4>
                        <div class="row">
                            <div class="col-md-6 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;"><?php kaitori_te( 'Bank name', '金融機関名 (銀行名)' ); ?></label>
                                <input type="text" name="bank_name" placeholder="<?php echo esc_attr( kaitori_t( 'MUFG / Japan Post / Rakuten Bank', '三菱UFJ銀行 / ゆうちょ銀行 / 楽天銀行' ) ); ?>" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                            </div>
                            <div class="col-md-6 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;"><?php kaitori_te( 'Branch name', '支店名' ); ?></label>
                                <input type="text" name="bank_branch" placeholder="<?php echo esc_attr( kaitori_t( 'Akihabara Branch', '秋葉原支店' ) ); ?>" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;"><?php kaitori_te( 'Account type', '口座種別' ); ?></label>
                                <select name="bank_account_type" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                                    <option value="普通"><?php kaitori_te( 'Ordinary', '普通' ); ?></option>
                                    <option value="当座"><?php kaitori_te( 'Current', '当座' ); ?></option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;"><?php kaitori_te( 'Account number (7 digits)', '口座番号 (7桁)' ); ?></label>
                                <input type="text" name="bank_account_number" placeholder="1234567" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                            </div>
                            <div class="col-md-4 form-group" style="margin-bottom:12px;">
                                <label style="font-size:12.5px; font-weight:700;"><?php kaitori_te( 'Account holder (Katakana)', '口座名義 (カタカナ)' ); ?></label>
                                <input type="text" name="bank_holder_kana" placeholder="ヤマダ タロウ" class="form-control-custom" style="height:40px; border-radius:8px; border:1px solid #cbd5e1; width:100%; padding:6px 10px;">
                            </div>
                        </div>
                    </div>

                    <!-- Estimated Summary Box -->
                    <div style="background:linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border:2px solid #fed7aa; border-radius:14px; padding:20px; margin:24px 0; text-align:center;">
                        <div style="font-size:13px; font-weight:700; color:#c2410c;"><?php kaitori_te( 'Online provisional quote · estimated max buyback', 'Web事前仮査定・買取上限目安金額' ); ?></div>
                        <div style="font-size:38px; font-weight:900; color:#dc2626; margin:6px 0;">
                            ¥<span id="reviewEstimatedPrice">195,000</span>
                        </div>
                        <p style="font-size:12.5px; color:#78350f; margin:0;">
                            <?php kaitori_te( '* Finalized after inspecting the actual device on arrival. Same-day price guarantee applies.', '※現物到着後の最終検品状態により確定いたします。当日価格保証が適用されます。' ); ?>
                        </p>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:24px;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; font-size:14px;">
                            <input type="checkbox" name="agree_terms" value="1" required checked style="width:18px; height:18px;">
                            <?php kaitori_te( 'I agree to the Terms of Service and privacy policy', '利用規約、個人情報の取扱いに同意します' ); ?>
                        </label>
                    </div>

                    <div class="step-nav" style="display:flex; justify-content:space-between; margin-top:24px;">
                        <button type="button" class="btn btn-default" onclick="goToStep(1)" style="font-weight:700; padding:12px 24px; border-radius:10px;">&larr; <?php kaitori_te( 'Back', '前へ戻る' ); ?></button>
                        <button type="submit" id="btnSubmitSell" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:14px 40px; border-radius:10px; font-size:16px;">
                            <i class="fa fa-paper-plane"></i> <?php kaitori_te( 'Submit Application', '買取を申し込む (Submit Application)' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <script>
            // Live server-side estimate (same engine as the final order) for the Elementor form.
            function kaitoriWidgetEstimate() {
                if (typeof kaitori_ajax === 'undefined') { return; }
                function val(name, def) {
                    var el = document.querySelector('[name="' + name + '"]');
                    if (!el) { return def; }
                    if (el.type === 'radio') {
                        var checked = document.querySelector('[name="' + name + '"]:checked');
                        return checked ? checked.value : def;
                    }
                    return el.value || def;
                }
                var data = {
                    action: 'kaitori_live_estimate',
                    model: val('device_model', ''),
                    storage: val('device_storage', '256GB'),
                    screen_condition: val('screen_condition', ''),
                    body_condition: val('body_condition', ''),
                    battery_health: val('battery_health', 95),
                    camera_works: val('camera_works', '正常作動'),
                    installment_status: val('installment_status', 'no')
                };
                jQuery.get(kaitori_ajax.ajax_url, data, function(res) {
                    if (res && res.success) {
                        var rp = document.getElementById('reviewEstimatedPrice');
                        if (rp) { rp.innerText = Number(res.data.price).toLocaleString(); }
                        var hp = document.getElementById('hidden_estimated_price');
                        if (hp) { hp.value = res.data.price; }
                    }
                });
            }

            var kaitoriCurrentStep = 1;
            function goToStep(stepNum) {
                // Moving FORWARD: validate the current step's fields first. Otherwise a
                // required field left invalid on an earlier (now display:none) step makes
                // the browser silently refuse the final submit ("control is not focusable")
                // — the submit event never fires, so nothing happens on the Review step.
                if (stepNum > kaitoriCurrentStep) {
                    var curStepEl = document.getElementById('step-' + kaitoriCurrentStep);
                    if (curStepEl) {
                        var curFields = curStepEl.querySelectorAll('input, select, textarea');
                        for (var f = 0; f < curFields.length; f++) {
                            if (curFields[f].willValidate && !curFields[f].checkValidity()) {
                                curFields[f].reportValidity();
                                return;
                            }
                        }
                    }
                }
                kaitoriCurrentStep = stepNum;
                for (var i = 1; i <= 2; i++) {
                    var stepEl = document.getElementById('step-' + i);
                    var nodeEl = document.getElementById('step-node-' + i);
                    if (stepEl) stepEl.style.display = (i === stepNum) ? 'block' : 'none';
                    if (nodeEl) {
                        var circle = nodeEl.querySelector('.step-circle');
                        if (circle) {
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
                }
                kaitoriWidgetEstimate();
                window.scrollTo({ top: document.querySelector('.apply-container').offsetTop - 30, behavior: 'smooth' });
            }

            // Recompute when device/condition fields change.
            jQuery(function($){
                $(document).on('change', '[name="device_model"], [name="device_storage"], [name="screen_condition"], [name="body_condition"], [name="battery_health"], [name="camera_works"]', kaitoriWidgetEstimate);
                kaitoriWidgetEstimate();
            });

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
