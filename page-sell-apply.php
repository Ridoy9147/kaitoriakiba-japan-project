<?php
/**
 * Template Name: Kaitori Sell Application
 * 
 * Multi-step iPhone and smartphone buyback online application form.
 * Supports Elementor page builder.
 *
 * @package KaitoriAkiba
 */

get_header();

// Gate: require login + eKYC before the buyback form (admin-configurable).
if ( function_exists( 'kaitoriakiba_maybe_block_sell_form' ) && kaitoriakiba_maybe_block_sell_form() ) {
    get_footer();
    return;
}

// Check if page is built with Elementor
$is_elementor = kaitoriakiba_is_elementor_page();

if ( $is_elementor ) :
    ?>
    <main id="primary" class="site-main elementor-content-wrap">
        <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
        ?>
    </main>
    <?php
else :
    // Pre-populate values from URL query string if any
    $pre_model    = isset( $_GET['model'] ) ? sanitize_text_field( $_GET['model'] ) : '';
    $pre_storage  = isset( $_GET['storage'] ) ? sanitize_text_field( $_GET['storage'] ) : '256GB';
    $pre_carrier  = isset( $_GET['carrier'] ) ? sanitize_text_field( $_GET['carrier'] ) : 'SIMフリー';
    $pre_delivery = isset( $_GET['delivery'] ) ? sanitize_text_field( $_GET['delivery'] ) : 'mail';
    ?>
    <div class="container" style="margin-top: 30px; margin-bottom: 60px;">
        <div class="apply-container" style="max-width: 860px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); padding: 36px 32px;">
            
            <div class="apply-header" style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 24px; font-weight: 900; color: #0f172a; margin-bottom: 8px;">
                    <i class="fa fa-mobile" style="color:#ef4444;"></i> <?php _e( 'iPhone・スマホ買取 査定・オンライン申込', 'kaitoriakiba' ); ?>
                </h1>
                <p style="color: #64748b; font-size: 13.5px; margin: 0;">
                    <?php _e( 'かんたん2ステップ入力で即時査定＆買取お申込み。最短即日入金・送料無料！', 'kaitoriakiba' ); ?>
                </p>
            </div>

            <!-- Stepper Progress Header (2 Steps) -->
            <div class="progress-stepper" style="display: flex; justify-content: space-around; max-width: 520px; margin: 0 auto 36px auto; position: relative;">
                <div class="step-item active" id="step-node-1" style="position: relative; z-index: 2; text-align: center; width: 45%;">
                    <div class="step-circle" style="width: 42px; height: 42px; border-radius: 50%; background: #ef4444; color: #ffffff; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #ef4444; font-size: 16px;">1</div>
                    <div class="step-label" style="font-size: 12.5px; font-weight: 700; color: #0f172a; line-height: 1.3;">端末情報・状態<br><small style="color:#64748b; font-weight:400;">Device &amp; Condition</small></div>
                </div>
                <div class="step-item" id="step-node-2" style="position: relative; z-index: 2; text-align: center; width: 45%;">
                    <div class="step-circle" style="width: 42px; height: 42px; border-radius: 50%; background: #f1f5f9; color: #64748b; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; border: 2px solid #cbd5e1; font-size: 16px;">2</div>
                    <div class="step-label" style="font-size: 12.5px; font-weight: 700; color: #64748b; line-height: 1.3;">お客様情報・振込口座<br><small style="color:#64748b; font-weight:400;">Details &amp; Bank (Optional)</small></div>
                </div>
            </div>

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
                <?php if ( function_exists( 'kaitoriakiba_honeypot_fields' ) ) { kaitoriakiba_honeypot_fields(); } ?>
                <input type="hidden" name="estimated_price" id="hidden_estimated_price" value="195000">
                <input type="hidden" name="find_my_off" value="1">
                <input type="hidden" name="apple_id_signout" value="1">
                <input type="hidden" name="no_mdm_lock" value="1">
                <input type="hidden" name="installment_status" value="no">
                <input type="hidden" name="delivery_method" value="mail">

                <!-- STEP 1: Device & Condition Details -->
                <div class="step-section active" id="sec-1">
                    <div class="step-title" style="font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px;">
                        <i class="fa fa-mobile" style="color: #ef4444;"></i> ステップ 1: 端末情報・状態確認 (Device &amp; Condition Details)
                    </div>

                    <!-- Model Selection -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>機種名 (Model)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <select name="model" id="phone_model" class="form-control-custom" onchange="calculateEstimate();" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <?php
                            $cpt_devices = get_posts( array( 'post_type' => 'kaitori_device', 'posts_per_page' => 40 ) );
                            if ( ! empty( $cpt_devices ) ) :
                                foreach ( $cpt_devices as $dev ) :
                                    $sel = ( $pre_model === $dev->post_title ) ? 'selected' : '';
                                    echo '<option value="' . esc_attr( $dev->post_title ) . '" ' . $sel . '>' . esc_html( $dev->post_title ) . '</option>';
                                endforeach;
                            else :
                            ?>
                                <option value="iPhone 16 Pro Max">iPhone 16 Pro Max</option>
                                <option value="iPhone 16 Pro" selected>iPhone 16 Pro</option>
                                <option value="iPhone 16 Plus">iPhone 16 Plus</option>
                                <option value="iPhone 16">iPhone 16</option>
                                <option value="iPhone 15 Pro Max">iPhone 15 Pro Max</option>
                                <option value="iPhone 15 Pro">iPhone 15 Pro</option>
                                <option value="iPhone 15">iPhone 15</option>
                                <option value="iPhone 14 Pro Max">iPhone 14 Pro Max</option>
                                <option value="iPhone 14 Pro">iPhone 14 Pro</option>
                                <option value="iPhone 14">iPhone 14</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Storage Capacity -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>ストレージ容量 (Storage Capacity)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <div class="radio-grid" id="storage_grid" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
                            <?php
                            $storages = array( '128GB', '256GB', '512GB', '1TB' );
                            foreach ( $storages as $st ) :
                                $is_active = ( $pre_storage === $st ) ? 'selected' : ( ( $st === '256GB' && ! $pre_storage ) ? 'selected' : '' );
                            ?>
                                <div class="radio-card <?php echo $is_active; ?>" onclick="selectRadioCard(this, 'storage', '<?php echo esc_attr( $st ); ?>');" style="border:1.5px solid <?php echo $is_active ? '#ef4444' : '#e2e8f0'; ?>; border-radius:10px; padding:12px; text-align:center; cursor:pointer; background:<?php echo $is_active ? '#fff5f5' : '#fff'; ?>;">
                                    <input type="radio" name="storage" value="<?php echo esc_attr( $st ); ?>" <?php checked( $is_active, 'selected' ); ?> style="display:none;">
                                    <div class="radio-card-title" style="font-weight:700;"><?php echo esc_html( $st ); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Color Swatches -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>カラー (Color)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <div class="color-swatches" id="color_swatches" style="display:flex; gap:14px; flex-wrap:wrap;">
                            <div class="color-swatch-item selected" onclick="selectColor(this, 'デザートチタニウム / Desert Titanium');" style="cursor:pointer; text-align:center;">
                                <div class="color-circle" style="width:36px; height:36px; border-radius:50%; background:#d4b39b; border:2px solid #ef4444; margin:0 auto 4px auto;"></div>
                                <small style="font-size:11px; font-weight:700;">Desert</small>
                            </div>
                            <div class="color-swatch-item" onclick="selectColor(this, 'ナチュラルチタニウム / Natural Titanium');" style="cursor:pointer; text-align:center;">
                                <div class="color-circle" style="width:36px; height:36px; border-radius:50%; background:#9f9b96; border:2px solid #cbd5e1; margin:0 auto 4px auto;"></div>
                                <small style="font-size:11px; font-weight:700;">Natural</small>
                            </div>
                            <div class="color-swatch-item" onclick="selectColor(this, 'ホワイトチタニウム / White Titanium');" style="cursor:pointer; text-align:center;">
                                <div class="color-circle" style="width:36px; height:36px; border-radius:50%; background:#f2f2f2; border:2px solid #cbd5e1; margin:0 auto 4px auto;"></div>
                                <small style="font-size:11px; font-weight:700;">White</small>
                            </div>
                            <div class="color-swatch-item" onclick="selectColor(this, 'ブラックチタニウム / Black Titanium');" style="cursor:pointer; text-align:center;">
                                <div class="color-circle" style="width:36px; height:36px; border-radius:50%; background:#2b2b2e; border:2px solid #cbd5e1; margin:0 auto 4px auto;"></div>
                                <small style="font-size:11px; font-weight:700;">Black</small>
                            </div>
                        </div>
                        <input type="hidden" name="color" id="selected_color" value="デザートチタニウム / Desert Titanium">
                    </div>

                    <!-- Carrier / SIM (Overseas SIM-free removed) -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>キャリア・SIM状態 (Carrier / SIM Status)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <div class="radio-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:10px;">
                            <div class="radio-card selected" onclick="selectRadioCard(this, 'carrier', 'SIMフリー (SIM-Free)');" style="border:1.5px solid #ef4444; border-radius:10px; padding:12px 10px; text-align:center; cursor:pointer; background:#fff5f5;">
                                <input type="radio" name="carrier" value="SIMフリー (SIM-Free)" checked style="display:none;">
                                <div class="radio-card-title" style="font-weight:700;">SIMフリー</div>
                                <div class="radio-card-desc" style="font-size:11px; color:#64748b;">Apple / 国内解除済</div>
                            </div>
                            <div class="radio-card" onclick="selectRadioCard(this, 'carrier', 'docomo');" style="border:1.5px solid #e2e8f0; border-radius:10px; padding:12px 10px; text-align:center; cursor:pointer; background:#fff;">
                                <input type="radio" name="carrier" value="docomo" style="display:none;">
                                <div class="radio-card-title" style="font-weight:700;">docomo</div>
                            </div>
                            <div class="radio-card" onclick="selectRadioCard(this, 'carrier', 'au');" style="border:1.5px solid #e2e8f0; border-radius:10px; padding:12px 10px; text-align:center; cursor:pointer; background:#fff;">
                                <input type="radio" name="carrier" value="au" style="display:none;">
                                <div class="radio-card-title" style="font-weight:700;">au</div>
                            </div>
                            <div class="radio-card" onclick="selectRadioCard(this, 'carrier', 'SoftBank');" style="border:1.5px solid #e2e8f0; border-radius:10px; padding:12px 10px; text-align:center; cursor:pointer; background:#fff;">
                                <input type="radio" name="carrier" value="SoftBank" style="display:none;">
                                <div class="radio-card-title" style="font-weight:700;">SoftBank</div>
                            </div>
                            <div class="radio-card" onclick="selectRadioCard(this, 'carrier', 'Rakuten');" style="border:1.5px solid #e2e8f0; border-radius:10px; padding:12px 10px; text-align:center; cursor:pointer; background:#fff;">
                                <input type="radio" name="carrier" value="Rakuten" style="display:none;">
                                <div class="radio-card-title" style="font-weight:700;">楽天モバイル</div>
                            </div>
                        </div>
                    </div>

                    <!-- IMEI Number (Optional) -->
                    <div class="form-group" style="margin-bottom:24px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>IMEI番号 (15桁)</span>
                            <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;">任意</span>
                        </label>
                        <input type="text" name="imei" id="imei_num" class="form-control-custom" placeholder="例: 351234567890123 (任意)" maxlength="15" inputmode="numeric" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        <small style="color:#64748b; font-size:12px; display:block; margin-top:4px;">※設定 ＞ 一般 ＞ 情報、または電話画面で「*#06#」を入力して確認できます（任意入力）。</small>
                    </div>

                    <!-- Condition Checklist (Merged into Step 1) -->
                    <div style="border-top:1.5px dashed #cbd5e1; padding-top:24px; margin-top:10px; margin-bottom:24px;">
                        <h4 style="font-size:15px; font-weight:800; color:#1e293b; margin-bottom:16px;">
                            <i class="fa fa-sliders" style="color:#ef4444;"></i> 状態チェック・機能確認 (Condition Details)
                        </h4>

                        <!-- Screen Condition (Optional) -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>画面・液晶の状態 (Screen Condition)</span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;">任意</span>
                            </label>
                            <div class="radio-grid" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px;">
                                <div class="radio-card selected" onclick="selectRadioCard(this, 'screen_condition', '傷なし・美品 (Sランク/Aランク)');" style="border:1.5px solid #ef4444; border-radius:10px; padding:12px; cursor:pointer; background:#fff5f5;">
                                    <input type="radio" name="screen_condition" value="傷なし・美品 (Sランク/Aランク)" checked style="display:none;">
                                    <div class="radio-card-title" style="font-weight:700;">傷なし・極美品</div>
                                    <div class="radio-card-desc" style="font-size:12px; color:#64748b;">目立つ傷なし</div>
                                </div>
                                <div class="radio-card" onclick="selectRadioCard(this, 'screen_condition', '微細な小傷あり (Bランク)');" style="border:1.5px solid #e2e8f0; border-radius:10px; padding:12px; cursor:pointer; background:#fff;">
                                    <input type="radio" name="screen_condition" value="微細な小傷あり (Bランク)" style="display:none;">
                                    <div class="radio-card-title" style="font-weight:700;">微細な小傷あり</div>
                                    <div class="radio-card-desc" style="font-size:12px; color:#64748b;">光に当てると確認できる程度</div>
                                </div>
                            </div>
                        </div>

                        <!-- Body Condition (Optional) -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>外装・フレームの状態 (Body &amp; Frame)</span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;">任意</span>
                            </label>
                            <div class="radio-grid" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px;">
                                <div class="radio-card selected" onclick="selectRadioCard(this, 'body_condition', '目立つ傷なし (Flawless)');" style="border:1.5px solid #ef4444; border-radius:10px; padding:12px; cursor:pointer; background:#fff5f5;">
                                    <input type="radio" name="body_condition" value="目立つ傷なし (Flawless)" checked style="display:none;">
                                    <div class="radio-card-title" style="font-weight:700;">目立つ打痕なし (極上)</div>
                                </div>
                                <div class="radio-card" onclick="selectRadioCard(this, 'body_condition', '角に軽微な擦れ傷あり');" style="border:1.5px solid #e2e8f0; border-radius:10px; padding:12px; cursor:pointer; background:#fff;">
                                    <input type="radio" name="body_condition" value="角に軽微な擦れ傷あり" style="display:none;">
                                    <div class="radio-card-title" style="font-weight:700;">角に軽微な擦れ傷あり</div>
                                </div>
                            </div>
                        </div>

                        <!-- Battery Health (Optional) -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>バッテリー最大容量 (Battery Health %)</span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;">任意</span>
                            </label>
                            <div style="display:flex; align-items:center; gap:16px;">
                                <input type="range" id="battery_range" min="50" max="100" value="95" style="flex:1;" oninput="document.getElementById('battery_val').value = this.value; calculateEstimate();">
                                <div style="display:flex; align-items:center; gap:4px; width:100px;">
                                    <input type="number" name="battery_health" id="battery_val" class="form-control-custom" value="95" min="50" max="100" style="height:38px; text-align:center; border:1.5px solid #cbd5e1; border-radius:8px;" oninput="document.getElementById('battery_range').value = this.value; calculateEstimate();">
                                    <span style="font-weight:700;">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hardware Checks -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                                <span>基本動作確認 (Functional Checks)</span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 8px; border-radius:4px;">任意</span>
                            </label>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                                <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                                    <div style="font-weight:700; font-size:13px; margin-bottom:6px;">電源・ボタン操作</div>
                                    <label><input type="radio" name="powers_on" value="yes" checked> 正常に動作する</label>
                                    <label style="margin-left:14px;"><input type="radio" name="powers_on" value="no"> 不具合あり</label>
                                </div>
                                <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                                    <div style="font-weight:700; font-size:13px; margin-bottom:6px;">Face ID / カメラ</div>
                                    <label><input type="radio" name="camera_works" value="yes" checked> 正常に動作する</label>
                                    <label style="margin-left:14px;"><input type="radio" name="camera_works" value="no"> 不具合あり</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1 Navigation -->
                    <div class="stepper-nav" style="display:flex; justify-content:flex-end; margin-top:28px; padding-top:20px; border-top:1px solid #e2e8f0;">
                        <button type="button" class="btn btn-danger" onclick="goToStep(2);" style="font-weight:800; border-radius:10px; padding:12px 32px; background:#ef4444; border-color:#ef4444; font-size:15px; color:#fff;">
                            次へ進む：お客様情報・振込口座 (Next) <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Your Details & Payout Bank Account (Optional) + Submit -->
                <div class="step-section" id="sec-2" style="display:none;">
                    <div class="step-title" style="font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px;">
                        <i class="fa fa-user" style="color: #ef4444;"></i> ステップ 2: お客様情報・振込口座 (Your Details &amp; Bank Account)
                        <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:3px 8px; border-radius:4px; font-weight:600; margin-left:8px; vertical-align:middle;">任意</span>
                    </div>

                    <!-- Customer Basic Info (Optional) -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:24px;">
                        <h4 style="font-size:14px; font-weight:800; color:#1e293b; margin-top:0; margin-bottom:12px;">
                            <i class="fa fa-id-badge" style="color:#ef4444;"></i> お申込者様情報 (任意)
                        </h4>
                        <div class="row">
                            <div class="col-md-6 form-group" style="margin-bottom:14px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13px; margin-bottom:4px;">
                                    <span>お名前 (Name)</span>
                                    <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;">任意</span>
                                </label>
                                <input type="text" name="applicant_name" class="form-control-custom" placeholder="山田 太郎 (任意)" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                            <div class="col-md-6 form-group" style="margin-bottom:14px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13px; margin-bottom:4px;">
                                    <span>フリガナ (Katakana)</span>
                                    <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;">任意</span>
                                </label>
                                <input type="text" name="applicant_kana" class="form-control-custom" placeholder="ヤマダ タロウ (任意)" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                            <div class="col-md-6 form-group" style="margin-bottom:14px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13px; margin-bottom:4px;">
                                    <span>メールアドレス (Email)</span>
                                    <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;">任意</span>
                                </label>
                                <input type="email" name="applicant_email" class="form-control-custom" placeholder="taro@example.com (任意)" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                            <div class="col-md-6 form-group" style="margin-bottom:14px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13px; margin-bottom:4px;">
                                    <span>電話番号 (Phone)</span>
                                    <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;">任意</span>
                                </label>
                                <input type="tel" name="applicant_phone" class="form-control-custom" placeholder="09012345678 (任意)" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                        </div>
                    </div>

                    <!-- Payout Bank Account (Optional) -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:24px;">
                        <h4 style="font-size:14px; font-weight:800; color:#1e293b; margin-top:0; margin-bottom:12px;">
                            <i class="fa fa-university" style="color:#ef4444;"></i> 振込先口座情報 (振込手数料無料・任意)
                        </h4>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13px; margin-bottom:4px;">
                                <span>銀行名 (Bank Name)</span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;">任意</span>
                            </label>
                            <select name="bank_name" id="bank_name" class="form-control-custom" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <option value="三菱UFJ銀行">三菱UFJ銀行 (MUFG Bank)</option>
                                <option value="三井住友銀行">三井住友銀行 (SMBC)</option>
                                <option value="みずほ銀行">みずほ銀行 (Mizuho Bank)</option>
                                <option value="ゆうちょ銀行">ゆうちょ銀行 (Japan Post Bank)</option>
                                <option value="楽天銀行">楽天銀行 (Rakuten Bank)</option>
                                <option value="PayPay銀行">PayPay銀行 (PayPay Bank)</option>
                                <option value="住信SBIネット銀行">住信SBIネット銀行 (SBI Sumishin)</option>
                                <option value="その他地方銀行・信用金庫">その他地方銀行・信用金庫</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13px; margin-bottom:4px;">
                                <span>支店名 (Branch Name)</span>
                                <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;">任意</span>
                            </label>
                            <input type="text" name="bank_branch" id="bank_branch" class="form-control-custom" placeholder="例: 秋葉原支店" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:4px;">口座種別 (Account Type)</label>
                            <div>
                                <label style="margin-right:24px; cursor:pointer;"><input type="radio" name="bank_account_type" value="普通" checked> 普通預金 (Ordinary)</label>
                                <label style="cursor:pointer;"><input type="radio" name="bank_account_type" value="当座"> 当座預金 (Current)</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group" style="margin-bottom:14px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13px; margin-bottom:4px;">
                                    <span>口座番号 (7桁)</span>
                                    <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;">任意</span>
                                </label>
                                <input type="text" name="bank_account_number" id="acc_number" class="form-control-custom" placeholder="例: 1234567" maxlength="7" inputmode="numeric" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                            <div class="col-md-6 form-group" style="margin-bottom:14px;">
                                <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:13px; margin-bottom:4px;">
                                    <span>口座名義人・カタカナ</span>
                                    <span class="badge-opt" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:2px 6px; border-radius:4px;">任意</span>
                                </label>
                                <input type="text" name="bank_holder_kana" id="acc_holder" class="form-control-custom" placeholder="例: ヤマダ タロウ" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                        </div>
                    </div>

                    <!-- Online Provisional Quote Summary Box -->
                    <div class="price-highlight" style="background: linear-gradient(135deg, #fff7ed 0%, #fef2f2 100%); border: 2px solid #fed7aa; border-radius: 14px; padding: 22px; text-align: center; margin-bottom: 24px;">
                        <div style="font-weight: 700; color: #c2410c; font-size: 14px;">Web事前仮査定・買取予想金額 (Estimated Buyback Price)</div>
                        <div class="price-val" id="summary_price" style="font-size: 36px; font-weight: 900; color: #dc2626; margin: 8px 0;">¥195,000</div>
                        <small style="color: #78350f; font-size: 12.5px;">※実機検品後の状態および付属品の有無により、最終確定価格となります。当日価格保証適用。</small>
                    </div>

                    <!-- Terms Agreement Checkbox -->
                    <div class="form-group" style="margin-bottom:24px;">
                        <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer;">
                            <input type="checkbox" id="chk_final_agree" name="agree_terms" value="1" required checked style="width:18px; height:18px; margin-top:2px;">
                            <span style="font-size:13.5px; color:#334155; line-height:1.5;">利用規約および個人情報保護方針に同意し、買取を申し込みます。（古物営業法に基づく本人確認含む）</span>
                        </label>
                    </div>

                    <!-- Step 2 Navigation (Back & Direct Submit) -->
                    <div class="stepper-nav" style="display:flex; justify-content:space-between; margin-top:28px; padding-top:20px; border-top:1px solid #e2e8f0;">
                        <button type="button" class="btn btn-default" onclick="goToStep(1);" style="font-weight:700; border-radius:10px; padding:12px 24px; border:1.5px solid #cbd5e1;">
                            <i class="fa fa-arrow-left"></i> 前へ戻る (Back)
                        </button>
                        <button type="submit" class="btn btn-primary btn-submit-app" id="btnFinalSubmit" style="background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border:none; border-radius:10px; font-weight:800; font-size:16px; color:#fff; padding:12px 36px;">
                            <i class="fa fa-paper-plane"></i> 買取を申し込む (Submit Buyback Application)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        var currentStep = 1;

        function selectRadioCard(element, groupName, value) {
            var container = element.parentElement;
            var cards = container.querySelectorAll('.radio-card');
            for (var i = 0; i < cards.length; i++) {
                cards[i].classList.remove('selected');
                cards[i].style.borderColor = '#e2e8f0';
                cards[i].style.background = '#ffffff';
            }
            element.classList.add('selected');
            element.style.borderColor = '#ef4444';
            element.style.background = '#fff5f5';

            var radio = element.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                calculateEstimate();
            }
        }

        function selectColor(element, colorName) {
            var items = document.querySelectorAll('.color-swatch-item');
            for (var i = 0; i < items.length; i++) {
                items[i].classList.remove('selected');
                var circ = items[i].querySelector('.color-circle');
                if (circ) circ.style.borderColor = '#cbd5e1';
            }
            element.classList.add('selected');
            var selectedCirc = element.querySelector('.color-circle');
            if (selectedCirc) selectedCirc.style.borderColor = '#ef4444';
            document.getElementById('selected_color').value = colorName;
        }

        var _estimateTimer = null;
        function calculateEstimate() {
            if (_estimateTimer) { clearTimeout(_estimateTimer); }
            _estimateTimer = setTimeout(fetchServerEstimate, 250);
        }

        function fetchServerEstimate() {
            if (typeof kaitori_ajax === 'undefined') return;
            var modelEl = document.getElementById('phone_model');
            var data = {
                action: 'kaitori_live_estimate',
                model: modelEl ? modelEl.value : '',
                storage: (jQuery('input[name="storage"]:checked').val() || '256GB'),
                screen_condition: (jQuery('input[name="screen_condition"]:checked').val() || ''),
                body_condition: (jQuery('input[name="body_condition"]:checked').val() || ''),
                battery_health: (parseInt(document.getElementById('battery_val').value) || 95),
                powers_on: (jQuery('input[name="powers_on"]:checked').val() || 'yes'),
                camera_works: (jQuery('input[name="camera_works"]:checked').val() || 'yes'),
                installment_status: 'no'
            };
            jQuery.get(kaitori_ajax.ajax_url, data, function(res) {
                if (res && res.success) {
                    var priceFormatted = res.data.formatted || ('¥' + Number(res.data.price).toLocaleString());
                    var priceEl = document.getElementById('summary_price');
                    if (priceEl) priceEl.innerText = priceFormatted;
                    var hiddenPrice = document.getElementById('hidden_estimated_price');
                    if (hiddenPrice) hiddenPrice.value = res.data.price;
                }
            });
        }

        function navigateStep(direction) {
            goToStep(currentStep + direction);
        }

        function goToStep(step) {
            if (step < 1 || step > 2) return;

            // Hide old step
            document.getElementById('sec-' + currentStep).style.display = 'none';
            var currNode = document.getElementById('step-node-' + currentStep);
            if (currNode) {
                var currCirc = currNode.querySelector('.step-circle');
                if (step > currentStep) {
                    currCirc.style.background = '#10b981';
                    currCirc.style.color = '#ffffff';
                    currCirc.style.borderColor = '#10b981';
                } else {
                    currCirc.style.background = '#f1f5f9';
                    currCirc.style.color = '#64748b';
                    currCirc.style.borderColor = '#cbd5e1';
                }
            }

            // Show new step
            currentStep = step;
            document.getElementById('sec-' + currentStep).style.display = 'block';
            var nextNode = document.getElementById('step-node-' + currentStep);
            if (nextNode) {
                var nextCirc = nextNode.querySelector('.step-circle');
                nextCirc.style.background = '#ef4444';
                nextCirc.style.color = '#ffffff';
                nextCirc.style.borderColor = '#ef4444';
            }

            calculateEstimate();
            window.scrollTo({ top: 150, behavior: 'smooth' });
        }

        // Initial server-side estimate on load
        jQuery(function(){ fetchServerEstimate(); });
    </script>
    <?php
endif;

get_footer();
