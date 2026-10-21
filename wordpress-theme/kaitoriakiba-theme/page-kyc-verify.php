<?php
/**
 * Template Name: Kaitori eKYC Verification
 * 
 * Online Identity Verification (eKYC) under the Secondhand Articles Dealer Act.
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
    // Pre-populate values from URL query string or logged-in user
    $pre_order = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : '';
    $pre_name  = isset( $_GET['name'] ) ? sanitize_text_field( $_GET['name'] ) : '';
    $pre_email = isset( $_GET['email'] ) ? sanitize_email( $_GET['email'] ) : '';
    $pre_phone = isset( $_GET['phone'] ) ? sanitize_text_field( $_GET['phone'] ) : '';

    if ( is_user_logged_in() ) {
        $cur_user = wp_get_current_user();
        if ( empty( $pre_name ) ) $pre_name = $cur_user->display_name;
        if ( empty( $pre_email ) ) $pre_email = $cur_user->user_email;
    }
    ?>
    <div class="container" style="margin-top: 36px; margin-bottom: 60px;">
        <div class="kyc-container" style="max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); padding: 36px 32px;">
            
            <div class="kyc-header" style="text-align: center; margin-bottom: 24px;">
                <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">
                    <i class="fa fa-id-card-o" style="color:#ef4444;"></i> <?php _e( '本人確認 (Identity Verification - eKYC)', 'kaitoriakiba' ); ?>
                </h1>
                <p style="color: #64748b; font-size: 13.5px; line-height: 1.5; margin:0;">
                    <?php _e( '古物営業法に基づき、スマートフォン等の買取時には本人確認書類の提出が義務付けられています。オンラインで安全に書類画像をアップロードいただけます。', 'kaitoriakiba' ); ?>
                </p>
            </div>

            <div class="legal-banner" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 10px; padding: 14px 16px; margin-bottom: 24px; font-size: 13px; color: #166534; display: flex; align-items: flex-start; gap: 10px;">
                <i class="fa fa-lock" style="font-size: 18px; margin-top: 2px;"></i>
                <div>
                    <strong>個人情報保護・SSL暗号化通信対応</strong><br>
                    ご提出いただいた本人確認書類および個人情報は、古物営業法に基づく記録保管目的以外には一切利用いたしません。暗号化通信により安全に送信・保管されます。
                </div>
            </div>

            <!-- KYC Success Box -->
            <div id="kyc-success-message" style="display:none; text-align:center; padding:30px 10px;">
                <div style="width:64px; height:64px; border-radius:50%; background:#dcfce7; color:#16a34a; font-size:32px; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <i class="fa fa-check"></i>
                </div>
                <h2 style="font-size:22px; font-weight:800; color:#0f172a; margin-bottom:8px;">本人確認書類を受理いたしました！</h2>
                <p style="color:#475569; font-size:14px; margin-bottom:20px;">
                    スタッフによる身元確認審査を実施いたします。<br>通常30分〜数時間以内に確認完了のお知らせメールをお送りします。
                </p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-default" style="font-weight:700; border-radius:8px; padding:10px 24px;">トップページへ戻る</a>
            </div>

            <form id="kycForm" method="post" enctype="multipart/form-data">
                <!-- Document Type Select Cards -->
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label" style="display:flex; justify-content:space-between; font-weight:700; font-size:14px; margin-bottom:6px;">
                        <span>提出する本人確認書類 (Document Type)</span>
                        <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                    </label>
                    <div class="id-type-grid" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:12px; margin-bottom:14px;">
                        <div class="id-type-card selected" onclick="selectIdType('license', this);" style="border:1.5px solid #ef4444; border-radius:12px; padding:14px; cursor:pointer; background:#fff5f5; display:flex; align-items:center; gap:12px;">
                            <div class="id-icon" style="width:38px; height:38px; border-radius:8px; background:#fee2e2; color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:18px;"><i class="fa fa-id-card"></i></div>
                            <div>
                                <div style="font-weight:700; font-size:14px; color:#1e293b;">運転免許証</div>
                                <div style="font-size:11.5px; color:#64748b;">裏表2面が必要</div>
                            </div>
                        </div>
                        <div class="id-type-card" onclick="selectIdType('mynumber', this);" style="border:1.5px solid #e2e8f0; border-radius:12px; padding:14px; cursor:pointer; background:#fff; display:flex; align-items:center; gap:12px;">
                            <div class="id-icon" style="width:38px; height:38px; border-radius:8px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; font-size:18px;"><i class="fa fa-credit-card"></i></div>
                            <div>
                                <div style="font-weight:700; font-size:14px; color:#1e293b;">マイナンバーカード</div>
                                <div style="font-size:11.5px; color:#64748b;">※表面のみ (裏面不要)</div>
                            </div>
                        </div>
                        <div class="id-type-card" onclick="selectIdType('residence', this);" style="border:1.5px solid #e2e8f0; border-radius:12px; padding:14px; cursor:pointer; background:#fff; display:flex; align-items:center; gap:12px;">
                            <div class="id-icon" style="width:38px; height:38px; border-radius:8px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; font-size:18px;"><i class="fa fa-globe"></i></div>
                            <div>
                                <div style="font-weight:700; font-size:14px; color:#1e293b;">在留カード</div>
                                <div style="font-size:11.5px; color:#64748b;">外国籍の方 / 裏表2面</div>
                            </div>
                        </div>
                        <div class="id-type-card" onclick="selectIdType('passport', this);" style="border:1.5px solid #e2e8f0; border-radius:12px; padding:14px; cursor:pointer; background:#fff; display:flex; align-items:center; gap:12px;">
                            <div class="id-icon" style="width:38px; height:38px; border-radius:8px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; font-size:18px;"><i class="fa fa-book"></i></div>
                            <div>
                                <div style="font-weight:700; font-size:14px; color:#1e293b;">パスポート</div>
                                <div style="font-size:11.5px; color:#64748b;">顔写真面・住所所持人面</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="kyc_doc_type" id="kyc_doc_type" value="運転免許証">
                </div>

                <!-- Personal Information -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:20px;">
                    <h4 style="font-size:14px; font-weight:800; color:#1e293b; margin-bottom:12px;"><i class="fa fa-user" style="color:#ef4444;"></i> 本人情報（書類記載と一致するもの）</h4>
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label" style="font-weight:700; font-size:13px;">氏名 (Full Name) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                            <input type="text" name="kyc_name" value="<?php echo esc_attr($pre_name); ?>" class="form-control-custom" placeholder="山田 太郎" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label" style="font-weight:700; font-size:13px;">フリガナ (Katakana) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                            <input type="text" name="kyc_kana" class="form-control-custom" placeholder="ヤマダ タロウ" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label" style="font-weight:700; font-size:13px;">メールアドレス (Email) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                            <input type="email" name="kyc_email" value="<?php echo esc_attr($pre_email); ?>" class="form-control-custom" placeholder="example@email.com" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            <small style="color:#64748b; font-size:11.5px;">※申込時と同じメールアドレスを入力してください</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label" style="font-weight:700; font-size:13px;">電話番号 (Phone Number) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                            <input type="tel" name="kyc_phone" value="<?php echo esc_attr($pre_phone); ?>" class="form-control-custom" placeholder="09012345678" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label" style="font-weight:700; font-size:13px;">生年月日 (Date of Birth) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                            <input type="date" name="kyc_dob" class="form-control-custom" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label" style="font-weight:700; font-size:13px;">ご職業 (Occupation) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                            <select name="kyc_job" class="form-control-custom" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                                <option value="会社員">会社員 (Employee)</option>
                                <option value="自営業・個人事業主">自営業・個人事業主 (Self-employed)</option>
                                <option value="公務員">公務員 (Civil Servant)</option>
                                <option value="学生">学生 (Student)</option>
                                <option value="パート・アルバイト">パート・アルバイト (Part-time)</option>
                                <option value="その他">その他 (Other)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Linked Order Code -->
                    <div class="form-group" style="margin-top:4px;">
                        <label class="form-label" style="font-weight:700; font-size:13px;">買取受付番号 (Linked Order Reference)</label>
                        <input type="text" name="kyc_order_code" value="<?php echo esc_attr($pre_order); ?>" class="form-control-custom" placeholder="例: KB-20261021-1234 (お申込完了時に発行された受付番号)" style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                        <small style="color:#64748b; font-size:11.5px;">※すでに買取申込がお済みの場合、受付番号を入力すると注文と自動で紐付けされます。</small>
                    </div>

                    <div class="form-group" style="margin-top:8px;">
                        <label class="form-label" style="font-weight:700; font-size:13px;">現住所 (Current Residential Address) <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 6px; border-radius:4px;">必須</span></label>
                        <div class="row" style="margin-bottom:8px;">
                            <div class="col-md-4">
                                <input type="text" name="kyc_postal" class="form-control-custom" placeholder="郵便番号 例: 101-0021" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="kyc_prefecture" class="form-control-custom" placeholder="都道府県 (例: 東京都)" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="kyc_city" class="form-control-custom" placeholder="市区町村 (例: 千代田区)" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                            </div>
                        </div>
                        <input type="text" name="kyc_address" class="form-control-custom" placeholder="番地・建物名・部屋番号 (例: 外神田1-2-3 アキバビル4F)" required style="height:44px; border-radius:8px; border:1.5px solid #cbd5e1; width:100%; padding:8px 12px;">
                    </div>
                </div>

                <!-- ID File Upload Area -->
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label" style="font-weight:700; font-size:14px; margin-bottom:6px;">
                        <span>書類の表面画像 (Front Side)</span>
                        <span class="badge-req" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                    </label>
                    <div class="id-upload-frame" onclick="document.getElementById('kyc_id_front').click();" style="border:2px dashed #cbd5e1; border-radius:12px; padding:24px; text-align:center; background:#f8fafc; cursor:pointer;">
                        <i class="fa fa-camera" style="font-size:28px; color:#ef4444; margin-bottom:8px;"></i>
                        <div style="font-weight:700; color:#1e293b;">表面の写真を選択または撮影</div>
                        <div style="font-size:12px; color:#64748b;">文字・顔写真がはっきりと読み取れるよう全体を撮影してください</div>
                    </div>
                    <input type="file" name="kyc_id_front" id="kyc_id_front" accept="image/*" style="display:none;" required onchange="handlePreview(this, 'preview_front');">
                    <img id="preview_front" style="display:none; max-height:160px; margin:10px auto; border-radius:8px; border:1px solid #cbd5e1;">
                </div>

                <div class="form-group" id="back_upload_group" style="margin-bottom:20px;">
                    <label class="form-label" style="font-weight:700; font-size:14px; margin-bottom:6px;">
                        <span>書類の裏面画像 (Back Side)</span>
                        <span class="badge-req" id="back_req_badge" style="background:#fee2e2; color:#ef4444; font-size:11px; padding:2px 8px; border-radius:4px;">必須</span>
                    </label>
                    <div class="id-upload-frame" onclick="document.getElementById('kyc_id_back').click();" style="border:2px dashed #cbd5e1; border-radius:12px; padding:24px; text-align:center; background:#f8fafc; cursor:pointer;">
                        <i class="fa fa-refresh" style="font-size:28px; color:#64748b; margin-bottom:8px;"></i>
                        <div style="font-weight:700; color:#1e293b;">裏面の写真を選択または撮影</div>
                        <div style="font-size:12px; color:#64748b;">裏面住所記載欄・更新履歴の確認に使用します</div>
                    </div>
                    <input type="file" name="kyc_id_back" id="kyc_id_back" accept="image/*" style="display:none;" onchange="handlePreview(this, 'preview_back');">
                    <img id="preview_back" style="display:none; max-height:160px; margin:10px auto; border-radius:8px; border:1px solid #cbd5e1;">
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label style="display:flex; gap:10px; cursor:pointer;">
                        <input type="checkbox" id="kyc_consent" required style="margin-top:4px;">
                        <span style="font-size:13px; color:#334155;">古物営業法に基づく身元確認のため個人情報を提供することに同意します。</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-submit-kyc" style="width:100%; height:50px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border:none; border-radius:10px; font-weight:800; font-size:16px; color:#fff;">
                    <i class="fa fa-upload"></i> 本人確認書類を提出する (Submit eKYC)
                </button>
            </form>
        </div>
    </div>

    <script>
        function selectIdType(type, card) {
            var cards = document.querySelectorAll('.id-type-card');
            for (var i = 0; i < cards.length; i++) {
                cards[i].classList.remove('selected');
                cards[i].style.borderColor = '#e2e8f0';
                cards[i].style.background = '#ffffff';
                var icon = cards[i].querySelector('.id-icon');
                if (icon) {
                    icon.style.background = '#f1f5f9';
                    icon.style.color = '#475569';
                }
            }
            card.classList.add('selected');
            card.style.borderColor = '#ef4444';
            card.style.background = '#fff5f5';
            var selectedIcon = card.querySelector('.id-icon');
            if (selectedIcon) {
                selectedIcon.style.background = '#fee2e2';
                selectedIcon.style.color = '#ef4444';
            }

            var typeNames = {
                'license': '運転免許証',
                'mynumber': 'マイナンバーカード',
                'residence': '在留カード',
                'passport': 'パスポート'
            };
            document.getElementById('kyc_doc_type').value = typeNames[type] || '運転免許証';

            // MyNumber card back side is confidential and should not be uploaded
            var backGroup = document.getElementById('back_upload_group');
            if (type === 'mynumber') {
                backGroup.style.display = 'none';
                document.getElementById('kyc_id_back').required = false;
            } else {
                backGroup.style.display = 'block';
            }
        }

        function handlePreview(input, imgId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.getElementById(imgId);
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <?php
endif;

get_footer();
