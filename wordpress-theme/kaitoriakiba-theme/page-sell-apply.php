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

// Check if page is built with Elementor
$is_elementor = false;
if ( did_action( 'elementor/loaded' ) ) {
    $document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
    if ( $document && $document->is_built_with_elementor() ) {
        $is_elementor = true;
    }
}

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
                    <?php _e( 'かんたんステップ入力で即時査定＆買取お申込み。最短即日入金・送料無料！', 'kaitoriakiba' ); ?>
                </p>
            </div>

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
                <a href="<?php echo esc_url( home_url( '/kyc-verify/' ) ); ?>" class="btn btn-primary" style="background:#ef4444; border-color:#ef4444; font-weight:800; padding:12px 32px; border-radius:10px; font-size:16px;">
                    本人確認 (eKYC) に進む &rarr;
                </a>
            </div>

            <!-- Form -->
            <form id="sellForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="estimated_price" id="hidden_estimated_price" value="195000">

                <!-- STEP 1: Phone Details -->
                <div class="step-section active" id="sec-1">
                    <div class="step-title" style="font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px;">
                        <i class="fa fa-mobile" style="color: #ef4444;"></i> ステップ 1: iPhone端末情報の選択 (Phone Details)
                    </div>
                    
                    <!-- Customer Basic Info -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:24px;">
                        <h4 style="font-size:14px; font-weight:800; color:#1e293b; margin-bottom:12px;"><i class="fa fa-user" style="color:#ef4444;"></i> お申込者様情報</h4>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="form-label" style="font-weight:700; font-size:13px;">お名前 (Name) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                                <input type="text" name="applicant_name" class="form-control-custom" placeholder="山田 太郎" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label" style="font-weight:700; font-size:13px;">フリガナ (Katakana) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                                <input type="text" name="applicant_kana" class="form-control-custom" placeholder="ヤマダ タロウ" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label" style="font-weight:700; font-size:13px;">メールアドレス (Email) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                                <input type="email" name="applicant_email" class="form-control-custom" placeholder="taro@example.com" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label" style="font-weight:700; font-size:13px;">電話番号 (Phone) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                                <input type="tel" name="applicant_phone" class="form-control-custom" placeholder="09012345678" required>
                            </div>
                        </div>
                    </div>

                    <!-- Model Selection -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>iPhone機種名 (Model)</span>
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

                    <!-- Carrier / SIM -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>キャリア・SIMロック状態 (Carrier / SIM Status)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <div class="radio-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:10px;">
                            <div class="radio-card selected" onclick="selectRadioCard(this, 'carrier', 'SIMフリー (SIM-Free)');" style="border:1.5px solid #ef4444; border-radius:10px; padding:12px 10px; text-align:center; cursor:pointer; background:#fff5f5;">
                                <input type="radio" name="carrier" value="SIMフリー (SIM-Free)" checked style="display:none;">
                                <div class="radio-card-title" style="font-weight:700;">SIMフリー</div>
                                <div class="radio-card-desc" style="font-size:11px; color:#64748b;">Apple / 解除済</div>
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

                    <!-- IMEI Number -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>IMEI番号 (15桁)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <input type="text" name="imei" id="imei_num" class="form-control-custom" placeholder="例: 351234567890123" maxlength="15" inputmode="numeric" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        <small style="color:#64748b; font-size:12px; display:block; margin-top:4px;">※設定 ＞ 一般 ＞ 情報、または電話画面で「*#06#」を入力して確認できます。</small>
                    </div>
                </div>

                <!-- STEP 2: Condition Checklist -->
                <div class="step-section" id="sec-2" style="display:none;">
                    <div class="step-title" style="font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px;">
                        <i class="fa fa-check-square-o" style="color: #ef4444;"></i> ステップ 2: 状態チェック (Condition Checklist)
                    </div>
                    
                    <!-- Screen Condition -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>画面・液晶の状態 (Screen Condition)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
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

                    <!-- Body Condition -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>外装・フレームの状態 (Body &amp; Frame)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
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

                    <!-- Battery Health -->
                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>バッテリー最大容量 (Battery Health %)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
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
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
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

                <!-- STEP 3: Lock & Security -->
                <div class="step-section" id="sec-3" style="display:none;">
                    <div class="step-title" style="font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px;">
                        <i class="fa fa-shield" style="color: #ef4444;"></i> ステップ 3: ロック解除・セキュリティ確認 (Security Check)
                    </div>

                    <div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px; padding:16px; margin-bottom:20px;">
                        <div style="color:#9f1239; font-weight:800; font-size:14px; margin-bottom:4px;">
                            <i class="fa fa-exclamation-triangle"></i> 重要：初期化およびアカウント連携の解除が必須です
                        </div>
                        <div style="color:#be123c; font-size:12.5px;">
                            アクティベーションロックがかかった端末は買取不可となります。必ず事前にサインアウトと初期化を行ってください。
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label style="display:block; margin-bottom:10px; cursor:pointer;">
                            <input type="checkbox" name="find_my_off" id="chk_findmy" value="1" required>
                            <strong>「iPhoneを探す」をオフにしました</strong> (Find My iPhone is disabled) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span>
                        </label>
                        <label style="display:block; margin-bottom:10px; cursor:pointer;">
                            <input type="checkbox" name="apple_id_signout" id="chk_appleid" value="1" required>
                            <strong>Apple ID / iCloudからサインアウトしました</strong> (Signed out of Apple ID) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span>
                        </label>
                        <label style="display:block; margin-bottom:10px; cursor:pointer;">
                            <input type="checkbox" name="no_mdm_lock" id="chk_mdm" value="1" required>
                            <strong>MDM・会社等の遠隔管理ロックはありません</strong> (No MDM / Corporate Lock) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span>
                        </label>
                    </div>

                    <!-- Payment Type & Installment Due -->
                    <div class="form-group" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>端末代金の支払方法 (Payment Method at Purchase)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <div class="radio-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px;">
                            <div class="radio-card selected" id="card_pay_full" onclick="selectPaymentType('full', this);" style="border:1.5px solid #ef4444; border-radius:10px; padding:12px; cursor:pointer; background:#fff5f5;">
                                <div class="radio-card-title" style="font-weight:700;"><i class="fa fa-check-circle" style="color:#10b981;"></i> 一括払い (Full Payment)</div>
                                <div class="radio-card-desc" style="font-size:12px; color:#64748b;">全額一括で購入（残債なし）</div>
                            </div>
                            <div class="radio-card" id="card_pay_installment" onclick="selectPaymentType('installment', this);" style="border:1.5px solid #e2e8f0; border-radius:10px; padding:12px; cursor:pointer; background:#fff;">
                                <div class="radio-card-title" style="font-weight:700;"><i class="fa fa-credit-card" style="color:#3b82f6;"></i> 分割払い (Installment Plan)</div>
                                <div class="radio-card-desc" style="font-size:12px; color:#64748b;">ローン・キャリア分割払い</div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_type" id="payment_type" value="一括払い (Full Payment)">

                        <div id="installment_detail_box" style="display:none; border-top:1px dashed #cbd5e1; padding-top:16px; margin-top:10px;">
                            <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:6px;">
                                <span>分割払いの残債（未払い残高）はありますか？</span>
                            </label>
                            <div class="radio-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px;">
                                <div class="radio-card selected" id="card_debt_none" onclick="selectInstallmentDebtStatus('none', this);" style="border:1.5px solid #ef4444; border-radius:10px; padding:12px; cursor:pointer; background:#fff5f5;">
                                    <div class="radio-card-title" style="font-weight:700;">残債なし・完済済</div>
                                </div>
                                <div class="radio-card" id="card_debt_exist" onclick="selectInstallmentDebtStatus('exist', this);" style="border:1.5px solid #e2e8f0; border-radius:10px; padding:12px; cursor:pointer; background:#fff;">
                                    <div class="radio-card-title" style="font-weight:700; color:#b91c1c;">残債あり (利用制限△)</div>
                                </div>
                            </div>
                            <input type="hidden" name="installment_status" id="installment_debt_status" value="no">

                            <div id="debt_amount_box" style="display:none; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px; margin-top:10px;">
                                <label class="form-label" style="font-weight:700; font-size:13px; color:#991b1b;">残債の残額 (Remaining Due Amount)</label>
                                <input type="number" name="installment_due_amount" id="debt_amount_val" class="form-control-custom" placeholder="例: 35000" min="0" step="1000" style="height:42px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="display:block; cursor:pointer; background:#f8fafc; border:1px solid #e2e8f0; padding:14px; border-radius:8px;">
                            <input type="checkbox" id="chk_legal_owner" required>
                            <strong>宣誓：私は本端末の正当な所有者であり、不正取得・盗難品ではありません。</strong>
                        </label>
                    </div>
                </div>

                <!-- STEP 4: Delivery Method -->
                <div class="step-section" id="sec-4" style="display:none;">
                    <div class="step-title" style="font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px;">
                        <i class="fa fa-truck" style="color: #ef4444;"></i> ステップ 4: 買取・引渡方法の選択 (Delivery Method)
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                            <span>売却方法の選択 (Method)</span>
                            <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                        </label>
                        <div class="radio-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div class="radio-card <?php echo ($pre_delivery !== 'store') ? 'selected' : ''; ?>" onclick="selectRadioCard(this, 'delivery_method', 'mail'); toggleDeliveryUI('mail');" style="border:1.5px solid <?php echo ($pre_delivery !== 'store') ? '#ef4444' : '#e2e8f0'; ?>; border-radius:10px; padding:16px; cursor:pointer; background:<?php echo ($pre_delivery !== 'store') ? '#fff5f5' : '#fff'; ?>;">
                                <input type="radio" name="delivery_method" value="mail" <?php checked( $pre_delivery !== 'store' ); ?> style="display:none;">
                                <div class="radio-card-title" style="font-weight:800; font-size:15px;"><i class="fa fa-cube" style="font-size:22px; color:#10b981; display:block; margin-bottom:6px;"></i> 宅配買取 (郵送)</div>
                                <div class="radio-card-desc" style="font-size:12px; color:#64748b;">送料無料・自宅から発送</div>
                            </div>
                            <div class="radio-card <?php echo ($pre_delivery === 'store') ? 'selected' : ''; ?>" onclick="selectRadioCard(this, 'delivery_method', 'store'); toggleDeliveryUI('store');" style="border:1.5px solid <?php echo ($pre_delivery === 'store') ? '#ef4444' : '#e2e8f0'; ?>; border-radius:10px; padding:16px; cursor:pointer; background:<?php echo ($pre_delivery === 'store') ? '#fff5f5' : '#fff'; ?>;">
                                <input type="radio" name="delivery_method" value="store" <?php checked( $pre_delivery === 'store' ); ?> style="display:none;">
                                <div class="radio-card-title" style="font-weight:800; font-size:15px;"><i class="fa fa-building-o" style="font-size:22px; color:#f59e0b; display:block; margin-bottom:6px;"></i> 店頭買取 (秋葉原店)</div>
                                <div class="radio-card-desc" style="font-size:12px; color:#64748b;">即日スピード現金払い</div>
                            </div>
                        </div>
                    </div>

                    <div id="mail_delivery_options" style="display:<?php echo ($pre_delivery === 'store') ? 'none' : 'block'; ?>;">
                        <div class="form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:6px;">無料梱包キットの送付 (Shipping Kit)</label>
                            <label style="margin-right:20px;"><input type="radio" name="shipping_kit_needed" value="yes" checked> 希望する (無料キットをお届け)</label>
                            <label><input type="radio" name="shipping_kit_needed" value="no"> 希望しない (自宅の箱で即発送)</label>
                        </div>
                    </div>

                    <div id="store_delivery_options" style="display:<?php echo ($pre_delivery === 'store') ? 'block' : 'none'; ?>;">
                        <div class="form-group" style="margin-bottom:16px;">
                            <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:6px;">来店予定店舗</label>
                            <p style="margin:0; font-size:14px; font-weight:700; color:#0f172a;"><i class="fa fa-map-marker" style="color:#ef4444;"></i> Kaitori Akiba 秋葉原総本店 (秋葉原駅徒歩3分)</p>
                        </div>
                    </div>
                </div>

                <!-- STEP 5: Payment Details -->
                <div class="step-section" id="sec-5" style="display:none;">
                    <div class="step-title" style="font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px;">
                        <i class="fa fa-credit-card" style="color: #ef4444;"></i> ステップ 5: 振込先口座情報 (Payment Bank Details)
                    </div>

                    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px; margin-bottom:20px;">
                        <i class="fa fa-info-circle" style="color:#2563eb;"></i> 
                        <strong style="color:#1e40af;">名義人に関するご注意：</strong> 
                        <span style="color:#1d4ed8; font-size:12.5px;">お振込先口座名義は、本人確認書類（ID）のお名前と完全に一致している必要があります。</span>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:6px;">銀行名 (Bank Name) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                        <select name="bank_name" id="bank_name" class="form-control-custom" style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
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

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:6px;">支店名 (Branch Name) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                        <input type="text" name="bank_branch" id="bank_branch" class="form-control-custom" placeholder="例: 秋葉原支店" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:6px;">口座種別 (Account Type) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                        <label style="margin-right:24px;"><input type="radio" name="bank_account_type" value="普通" checked> 普通預金 (Ordinary)</label>
                        <label><input type="radio" name="bank_account_type" value="当座"> 当座預金 (Current)</label>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:6px;">口座番号 7桁 (Account Number) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                        <input type="text" name="bank_account_number" id="acc_number" class="form-control-custom" placeholder="例: 1234567" maxlength="7" inputmode="numeric" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="font-weight:700; font-size:13px; margin-bottom:6px;">口座名義人・カタカナ (Account Holder Name) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                        <input type="text" name="bank_holder_kana" id="acc_holder" class="form-control-custom" placeholder="例: ヤマダ タロウ (全角カタカナ)" required style="height:46px; border-radius:10px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                    </div>
                </div>

                <!-- STEP 6: Review & Submit -->
                <div class="step-section" id="sec-6" style="display:none;">
                    <div class="step-title" style="font-size: 18px; font-weight: 800; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 24px;">
                        <i class="fa fa-file-text-o" style="color: #ef4444;"></i> ステップ 6: 最終確認 (Summary &amp; Submit)
                    </div>

                    <div class="price-highlight" style="background: #fff1f2; border: 1.5px solid #fecdd3; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px;">
                        <div style="font-weight: 700; color: #9f1239; font-size: 14px;">オンライン事前査定予想金額 (Estimated Buyback Price)</div>
                        <div class="price-val" id="summary_price" style="font-size: 32px; font-weight: 900; color: #dc2626; margin: 6px 0;">¥195,000</div>
                        <small style="color: #881337;">※実機検品後の状態および付属品の有無により、最終価格が確定します。</small>
                    </div>

                    <div class="summary-card" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                        <div style="font-weight: 800; font-size: 16px; margin-bottom: 12px; color: #0f172a; display: flex; justify-content: space-between;">
                            <span>申込み内容の概要</span>
                            <a href="javascript:goToStep(1);" style="color: #ef4444; font-size: 13px; font-weight: 700;"><i class="fa fa-pencil"></i> 修正する</a>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f1f5f9;">
                            <span style="color:#64748b;">機種・容量:</span>
                            <strong id="sum_model">iPhone 16 Pro (256GB)</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f1f5f9;">
                            <span style="color:#64748b;">カラー:</span>
                            <span id="sum_color">Desert Titanium</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f1f5f9;">
                            <span style="color:#64748b;">IMEI番号:</span>
                            <span id="sum_imei">351234567890123</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f1f5f9;">
                            <span style="color:#64748b;">バッテリー容量:</span>
                            <span id="sum_battery">95%</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0;">
                            <span style="color:#64748b;">振込先:</span>
                            <span id="sum_bank">三菱UFJ銀行</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label style="display:flex; gap:10px; cursor:pointer;">
                            <input type="checkbox" id="chk_final_agree" required style="margin-top:4px;">
                            <span style="font-size:13.5px; color:#334155;">古物営業法に基づく取引記録の10年間保存および売買規約に同意し、買取を申し込みます。</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-submit-app" id="btnFinalSubmit" style="width:100%; height:50px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border:none; border-radius:10px; font-weight:800; font-size:16px; color:#fff;">
                        <i class="fa fa-paper-plane"></i> 買取を申し込む (Submit Buyback Application)
                    </button>
                </div>

                <!-- Navigation Controls -->
                <div class="stepper-nav" id="stepper_nav_bar" style="display: flex; justify-content: space-between; margin-top: 32px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-default" id="btnPrev" onclick="navigateStep(-1);" style="display:none; font-weight:700; border-radius:10px; padding:10px 24px;">
                        <i class="fa fa-arrow-left"></i> 前へ戻る (Back)
                    </button>
                    <button type="button" class="btn btn-danger" id="btnNext" onclick="navigateStep(1);" style="margin-left:auto; font-weight:800; border-radius:10px; padding:10px 28px; background:#ef4444; border-color:#ef4444;">
                        次へ進む (Next) <i class="fa fa-arrow-right"></i>
                    </button>
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

        function selectPaymentType(type, card) {
            var cards = document.querySelectorAll('#sec-3 .radio-card');
            for (var i = 0; i < 2; i++) {
                cards[i].classList.remove('selected');
                cards[i].style.borderColor = '#e2e8f0';
                cards[i].style.background = '#ffffff';
            }
            card.classList.add('selected');
            card.style.borderColor = '#ef4444';
            card.style.background = '#fff5f5';

            var installmentBox = document.getElementById('installment_detail_box');
            if (type === 'installment') {
                document.getElementById('payment_type').value = '分割払い (Installment Plan)';
                installmentBox.style.display = 'block';
            } else {
                document.getElementById('payment_type').value = '一括払い (Full Payment)';
                installmentBox.style.display = 'none';
                document.getElementById('installment_debt_status').value = 'no';
            }
            calculateEstimate();
        }

        function selectInstallmentDebtStatus(status, card) {
            var debtBox = document.getElementById('debt_amount_box');
            if (status === 'exist') {
                document.getElementById('installment_debt_status').value = 'yes';
                debtBox.style.display = 'block';
            } else {
                document.getElementById('installment_debt_status').value = 'no';
                debtBox.style.display = 'none';
            }
            calculateEstimate();
        }

        function toggleDeliveryUI(type) {
            if (type === 'mail') {
                document.getElementById('mail_delivery_options').style.display = 'block';
                document.getElementById('store_delivery_options').style.display = 'none';
            } else {
                document.getElementById('mail_delivery_options').style.display = 'none';
                document.getElementById('store_delivery_options').style.display = 'block';
            }
        }

        function calculateEstimate() {
            var modelEl = document.getElementById('phone_model');
            var model = modelEl ? modelEl.value : '';
            var basePrice = 140000;
            if (model.indexOf('16 Pro Max') !== -1) basePrice = 215000;
            else if (model.indexOf('16 Pro') !== -1) basePrice = 195000;
            else if (model.indexOf('16') !== -1) basePrice = 135000;
            else if (model.indexOf('15 Pro Max') !== -1) basePrice = 165000;
            else if (model.indexOf('15 Pro') !== -1) basePrice = 145000;
            else if (model.indexOf('15') !== -1) basePrice = 115000;

            var battery = parseInt(document.getElementById('battery_val').value) || 95;
            if (battery < 80) basePrice -= 8000;

            var debt = document.getElementById('installment_debt_status').value;
            if (debt === 'yes') {
                basePrice = Math.max(20000, basePrice - 10000);
            }

            document.getElementById('summary_price').innerText = '¥' + basePrice.toLocaleString();
            document.getElementById('hidden_estimated_price').value = basePrice;
            return basePrice;
        }

        function navigateStep(direction) {
            if (direction === 1 && !validateCurrentStep(currentStep)) {
                return;
            }
            goToStep(currentStep + direction);
        }

        function goToStep(step) {
            if (step < 1 || step > 6) return;

            document.getElementById('sec-' + currentStep).style.display = 'none';
            var currNode = document.getElementById('step-node-' + currentStep);
            var currCirc = currNode.querySelector('.step-circle');
            currCirc.style.background = '#f1f5f9';
            currCirc.style.color = '#64748b';
            currCirc.style.borderColor = '#cbd5e1';

            if (step > currentStep) {
                currCirc.style.background = '#10b981';
                currCirc.style.color = '#ffffff';
                currCirc.style.borderColor = '#10b981';
            }

            currentStep = step;
            document.getElementById('sec-' + currentStep).style.display = 'block';
            var nextNode = document.getElementById('step-node-' + currentStep);
            var nextCirc = nextNode.querySelector('.step-circle');
            nextCirc.style.background = '#ef4444';
            nextCirc.style.color = '#ffffff';
            nextCirc.style.borderColor = '#ef4444';

            document.getElementById('btnPrev').style.display = (currentStep === 1) ? 'none' : 'inline-block';
            document.getElementById('btnNext').style.display = (currentStep === 6) ? 'none' : 'inline-block';

            if (currentStep === 6) {
                populateSummary();
            }

            window.scrollTo({ top: 150, behavior: 'smooth' });
        }

        function validateCurrentStep(step) {
            if (step === 1) {
                var imei = document.getElementById('imei_num').value.trim();
                if (!imei || imei.length !== 15 || !/^\d+$/.test(imei)) {
                    alert('15桁の正しいIMEI番号を入力してください。(Please enter a valid 15-digit IMEI number)');
                    return false;
                }
            } else if (step === 3) {
                if (!document.getElementById('chk_findmy').checked ||
                    !document.getElementById('chk_appleid').checked ||
                    !document.getElementById('chk_mdm').checked ||
                    !document.getElementById('chk_legal_owner').checked) {
                    alert('セキュリティおよび所有権に関する必須確認事項にすべてチェックを入れてください。');
                    return false;
                }
            } else if (step === 5) {
                var branch = document.getElementById('bank_branch').value.trim();
                var num = document.getElementById('acc_number').value.trim();
                var holder = document.getElementById('acc_holder').value.trim();
                if (!branch || !num || !holder) {
                    alert('振込先口座情報をすべて正しく入力してください。');
                    return false;
                }
            }
            return true;
        }

        function populateSummary() {
            var modelEl = document.getElementById('phone_model');
            var model = modelEl ? modelEl.value : '';
            var storage = $('input[name="storage"]:checked').val() || '256GB';
            document.getElementById('sum_model').innerText = model + ' (' + storage + ')';
            document.getElementById('sum_color').innerText = document.getElementById('selected_color').value;
            document.getElementById('sum_imei').innerText = document.getElementById('imei_num').value;
            document.getElementById('sum_battery').innerText = document.getElementById('battery_val').value + '%';
            document.getElementById('sum_bank').innerText = document.getElementById('bank_name').value + ' ' + document.getElementById('bank_branch').value;
        }
    </script>
    <?php
endif;

get_footer();
