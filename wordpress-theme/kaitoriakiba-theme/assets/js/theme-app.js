/**
 * Kaitori Akiba Theme Application Scripts
 * Handles AJAX forms for Sell-Apply, eKYC, and live search.
 */

(function($) {
    'use strict';

    /**
     * Styled modal popup that replaces the browser's native alert() for all
     * form responses. type = 'success' (green ✓) or 'error' (red !).
     * Returns the overlay element. Plain text only (XSS-safe via textContent).
     */
    function kaitoriPopup(message, type) {
        type = (type === 'success') ? 'success' : 'error';

        var existing = document.getElementById('kaitori-popup-overlay');
        if (existing && existing.parentNode) { existing.parentNode.removeChild(existing); }

        var okText = (window.kaitori_ajax && kaitori_ajax.i18n && kaitori_ajax.i18n.popup_ok) || 'OK';
        var icon   = (type === 'success') ? '✓' : '!';

        var overlay = document.createElement('div');
        overlay.id = 'kaitori-popup-overlay';
        overlay.className = 'kaitori-popup-overlay';

        var card = document.createElement('div');
        card.className = 'kaitori-popup-card kaitori-popup-' + type;
        card.setAttribute('role', 'alertdialog');
        card.setAttribute('aria-modal', 'true');
        card.innerHTML =
            '<div class="kaitori-popup-icon" aria-hidden="true"></div>' +
            '<div class="kaitori-popup-msg"></div>' +
            '<button type="button" class="kaitori-popup-ok"></button>';
        card.querySelector('.kaitori-popup-icon').textContent = icon;
        card.querySelector('.kaitori-popup-msg').textContent = message || '';
        card.querySelector('.kaitori-popup-ok').textContent = okText;
        overlay.appendChild(card);
        document.body.appendChild(overlay);

        function close() {
            document.removeEventListener('keydown', onKey);
            overlay.classList.remove('kaitori-popup-shown');
            overlay.classList.add('kaitori-popup-closing');
            setTimeout(function() { if (overlay.parentNode) { overlay.parentNode.removeChild(overlay); } }, 200);
        }
        function onKey(e) { if (e.key === 'Escape' || e.keyCode === 27) { close(); } }

        overlay.addEventListener('click', function(e) { if (e.target === overlay) { close(); } });
        card.querySelector('.kaitori-popup-ok').addEventListener('click', close);
        document.addEventListener('keydown', onKey);

        requestAnimationFrame(function() {
            overlay.classList.add('kaitori-popup-shown');
            var btn = card.querySelector('.kaitori-popup-ok');
            if (btn) { btn.focus(); }
        });

        return { close: close, overlay: overlay };
    }
    // Expose for inline/other scripts that may want it.
    window.kaitoriPopup = kaitoriPopup;

    $(document).ready(function() {

        // Helper: Escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Helper: Clear all errors from form
        function clearFormErrors($form) {
            $form.find('.kaitori-form-error-alert').remove();
            $form.find('.kaitori-field-error-text').remove();
            $form.find('.kaitori-gate-warning-banner').remove();
            $form.find('.kaitori-field-error').removeClass('kaitori-field-error');
            $form.find('.id-upload-frame, .upload-dropzone, .upload-area').removeClass('kaitori-field-error');
        }

        // Helper: Show field-level errors and top alert banner
        function showFieldErrors($form, errors, customTitle) {
            clearFormErrors($form);

            if (!errors || errors.length === 0) return;

            var title = customTitle || '入力内容または書類に不備があります。以下の項目をご確認・修正してください：';
            var alertHtml = '<div class="kaitori-form-error-alert" role="alert">' +
                '<div class="alert-title"><i class="fa fa-exclamation-triangle" style="color:#ef4444; font-size:18px;"></i> ' + escapeHtml(title) + '</div>' +
                '<ul class="alert-list">';

            var firstErrorItem = null;

            errors.forEach(function(item, idx) {
                alertHtml += '<li><a href="javascript:void(0)" class="alert-field-link" data-err-index="' + idx + '"><strong>' +
                    escapeHtml(item.label) + '</strong>: ' + escapeHtml(item.message) + '</a></li>';

                var $target = null;
                if (item.element) {
                    $target = $(item.element);
                } else if (item.name) {
                    $target = $form.find('[name="' + item.name + '"]');
                    if (!$target.length) {
                        $target = $form.find('#' + item.name);
                    }
                }

                if ($target && $target.length) {
                    var $visual = $target;

                    // If file input, highlight upload frame
                    if ($target.is(':file') || $target.attr('type') === 'file') {
                        var $frame = $target.closest('.form-group, .col-md-6').find('.id-upload-frame, .upload-dropzone, .upload-area');
                        if ($frame.length) {
                            $visual = $frame;
                        }
                    } else if ($target.is(':checkbox') || $target.attr('type') === 'checkbox') {
                        var $lbl = $target.closest('label');
                        if ($lbl.length) {
                            $visual = $lbl;
                        }
                    }

                    $visual.addClass('kaitori-field-error');

                    // Inline error text
                    var $group = $target.closest('.form-group, .col-md-6, .col-md-4, .step-section, .form-step');
                    if ($group.length && !$group.find('.kaitori-field-error-text[data-for="' + item.name + '"]').length) {
                        var $errEl = $('<div class="kaitori-field-error-text" data-for="' + item.name + '"><i class="fa fa-times-circle"></i> ' + escapeHtml(item.message) + '</div>');
                        $visual.after($errEl);
                    }

                    // Remove error dynamically when user changes the field
                    $target.one('input change keyup', function() {
                        $visual.removeClass('kaitori-field-error');
                        $group.find('.kaitori-field-error-text[data-for="' + item.name + '"]').remove();
                    });

                    if (!firstErrorItem) {
                        firstErrorItem = { $el: $visual, step: item.step };
                    }
                }
            });

            alertHtml += '</ul></div>';
            $form.prepend(alertHtml);

            // Click listener for alert items
            $form.find('.alert-field-link').on('click', function(e) {
                e.preventDefault();
                var idx = parseInt($(this).data('err-index'), 10);
                var err = errors[idx];
                if (err) {
                    if (err.step && typeof window.goToStep === 'function') {
                        window.goToStep(err.step);
                    }
                    var $el = err.element ? $(err.element) : $form.find('[name="' + err.name + '"], #' + err.name);
                    if ($el.length) {
                        var topPos = $el.offset().top - 80;
                        $('html, body').animate({ scrollTop: topPos }, 350);
                        if ($el.is(':visible') && $el.is(':input') && !$el.is(':file')) {
                            $el.focus();
                        }
                    }
                }
            });

            // Automatically jump to the step containing the first error if multi-step
            if (firstErrorItem && firstErrorItem.step && typeof window.goToStep === 'function') {
                window.goToStep(firstErrorItem.step);
            }

            // Scroll to the top error alert
            var $topAlert = $form.find('.kaitori-form-error-alert');
            if ($topAlert.length) {
                $('html, body').animate({ scrollTop: $topAlert.offset().top - 70 }, 350);
            }
        }

        // Validate Sell-Apply Form Client-Side
        function validateSellFormClient($form) {
            var errors = [];

            // Model
            var model = $form.find('[name="model"], [name="device_model"]').val();
            if (!model || model.trim() === '') {
                errors.push({
                    name: 'model',
                    label: '機種名 (Device Model)',
                    message: '買取希望の機種名を選択してください。',
                    step: 1
                });
            }

            // Storage
            var storage = $form.find('input[name="storage"]:checked, [name="device_storage"]').val();
            if (!storage || storage.trim() === '') {
                errors.push({
                    name: 'storage',
                    label: 'ストレージ容量 (Storage)',
                    message: 'ストレージ容量を選択してください。',
                    step: 1
                });
            }

            // Carrier
            var carrier = $form.find('input[name="carrier"]:checked, [name="device_carrier"]').val();
            if (!carrier || carrier.trim() === '') {
                errors.push({
                    name: 'carrier',
                    label: 'キャリア (Carrier)',
                    message: 'キャリア・SIM状態を選択してください。',
                    step: 1
                });
            }

            // Color
            var $colorEl = $form.find('#selected_color, [name="device_color"]');
            if ($colorEl.length && (!$colorEl.val() || $colorEl.val().trim() === '')) {
                errors.push({
                    name: 'color',
                    label: 'カラー (Color)',
                    message: '本体カラーを選択または入力してください。',
                    step: 1
                });
            }

            // IMEI (Optional, but if filled must be 15 digits)
            var $imei = $form.find('input[name="imei"], input[name="device_imei"]');
            if ($imei.length && $imei.val() && $imei.val().trim() !== '') {
                var cleanImei = $imei.val().trim().replace(/[-s]/g, '');
                if (cleanImei.length !== 15 || !/^\d{15}$/.test(cleanImei)) {
                    errors.push({
                        name: $imei.attr('name') || 'imei',
                        element: $imei[0],
                        label: 'IMEI番号 (IMEI / Serial)',
                        message: 'IMEI番号は15桁の半角数字で入力してください。(例: 351234567890123)',
                        step: 1
                    });
                }
            }

            // Email (Optional, but if filled must be valid email)
            var $email = $form.find('input[name="applicant_email"]');
            if ($email.length && $email.val() && $email.val().trim() !== '') {
                var emailVal = $email.val().trim();
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                    errors.push({
                        name: 'applicant_email',
                        element: $email[0],
                        label: 'メールアドレス (Email)',
                        message: '有効なメールアドレスの形式で入力してください。(例: taro@example.com)',
                        step: 2
                    });
                }
            }

            // Phone (Optional, but if filled must be valid)
            var $phone = $form.find('input[name="applicant_phone"]');
            if ($phone.length && $phone.val() && $phone.val().trim() !== '') {
                var cleanPhone = $phone.val().trim().replace(/[-\s]/g, '');
                if (cleanPhone.length < 10 || !/^\d{10,12}$/.test(cleanPhone)) {
                    errors.push({
                        name: 'applicant_phone',
                        element: $phone[0],
                        label: '電話番号 (Phone Number)',
                        message: '10〜11桁の半角数字で電話番号を入力してください。(例: 09012345678)',
                        step: 2
                    });
                }
            }

            // Terms Agreement
            var $terms = $form.find('input[name="agree_terms"]');
            if ($terms.length && !$terms.is(':checked')) {
                errors.push({
                    name: 'agree_terms',
                    element: $terms[0],
                    label: '利用規約への同意 (Terms of Service)',
                    message: '利用規約および個人情報の取扱いに同意のチェックを入れてください。',
                    step: 2
                });
            }

            return errors;
        }

        // Validate eKYC Form Client-Side
        function validateKycFormClient($form) {
            var errors = [];

            // Name
            var $name = $form.find('input[name="kyc_name"], input[name="customer_name"]');
            if (!$name.val() || $name.val().trim() === '') {
                errors.push({
                    name: $name.attr('name') || 'kyc_name',
                    element: $name[0],
                    label: '氏名 (Full Name)',
                    message: '身分証明書記載のお名前（漢字）を入力してください。'
                });
            }

            // Furigana
            var $kana = $form.find('input[name="kyc_kana"]');
            if ($kana.length && (!$kana.val() || $kana.val().trim() === '')) {
                errors.push({
                    name: 'kyc_kana',
                    element: $kana[0],
                    label: 'フリガナ (Katakana)',
                    message: 'カタカナでお名前を入力してください。(例: ヤマダ タロウ)'
                });
            }

            // Email
            var $email = $form.find('input[name="kyc_email"], input[name="customer_email"]');
            if (!$email.val() || $email.val().trim() === '') {
                errors.push({
                    name: $email.attr('name') || 'kyc_email',
                    element: $email[0],
                    label: 'メールアドレス (Email)',
                    message: '連絡可能なメールアドレスを入力してください。'
                });
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($email.val().trim())) {
                errors.push({
                    name: $email.attr('name') || 'kyc_email',
                    element: $email[0],
                    label: 'メールアドレス (Email)',
                    message: '有効なメールアドレスの形式で入力してください。(例: user@example.com)'
                });
            }

            // Phone
            var $phone = $form.find('input[name="kyc_phone"]');
            if ($phone.length) {
                var phoneClean = $phone.val() ? $phone.val().trim().replace(/[-\s]/g, '') : '';
                if (!phoneClean) {
                    errors.push({
                        name: 'kyc_phone',
                        element: $phone[0],
                        label: '電話番号 (Phone Number)',
                        message: '電話番号を入力してください。(例: 09012345678)'
                    });
                } else if (phoneClean.length < 10 || !/^\d{10,12}$/.test(phoneClean)) {
                    errors.push({
                        name: 'kyc_phone',
                        element: $phone[0],
                        label: '電話番号 (Phone Number)',
                        message: '10〜11桁の半角数字で電話番号を入力してください。'
                    });
                }
            }

            // Date of birth
            var $dob = $form.find('input[name="kyc_dob"]');
            if ($dob.length && (!$dob.val() || $dob.val().trim() === '')) {
                errors.push({
                    name: 'kyc_dob',
                    element: $dob[0],
                    label: '生年月日 (Date of Birth)',
                    message: '生年月日を選択してください。'
                });
            }

            // Postal Code
            var $postal = $form.find('input[name="kyc_postal"]');
            if ($postal.length && (!$postal.val() || $postal.val().trim() === '')) {
                errors.push({
                    name: 'kyc_postal',
                    element: $postal[0],
                    label: '郵便番号 (Postal Code)',
                    message: '郵便番号を入力してください。(例: 101-0021)'
                });
            }

            // Prefecture
            var $pref = $form.find('input[name="kyc_prefecture"]');
            if ($pref.length && (!$pref.val() || $pref.val().trim() === '')) {
                errors.push({
                    name: 'kyc_prefecture',
                    element: $pref[0],
                    label: '都道府県 (Prefecture)',
                    message: '都道府県を入力してください。(例: 東京都)'
                });
            }

            // City
            var $city = $form.find('input[name="kyc_city"]');
            if ($city.length && (!$city.val() || $city.val().trim() === '')) {
                errors.push({
                    name: 'kyc_city',
                    element: $city[0],
                    label: '市区町村 (City / Ward)',
                    message: '市区町村を入力してください。(例: 千代田区)'
                });
            }

            // Street Address
            var $addr = $form.find('input[name="kyc_address"]');
            if ($addr.length && (!$addr.val() || $addr.val().trim() === '')) {
                errors.push({
                    name: 'kyc_address',
                    element: $addr[0],
                    label: '番地・建物名 (Street Address)',
                    message: '番地・建物名・部屋番号を入力してください。'
                });
            }

            var allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

            // Front ID Image (Required)
            var frontInput = $form.find('input[name="kyc_id_front"], input[name="kyc_front"]')[0];
            if (!frontInput || !frontInput.files || frontInput.files.length === 0) {
                errors.push({
                    name: 'kyc_id_front',
                    element: frontInput,
                    label: '書類の表面画像 (Front ID Photo)',
                    message: '本人確認書類の表面写真を選択または撮影してください。(必須)'
                });
            } else {
                var frontFile = frontInput.files[0];
                var frontExt = frontFile.name ? frontFile.name.split('.').pop().toLowerCase() : '';
                if (allowedExts.indexOf(frontExt) === -1) {
                    errors.push({
                        name: 'kyc_id_front',
                        element: frontInput,
                        label: '書類の表面画像 (Front ID Photo)',
                        message: '未対応のファイル形式です（.' + frontExt + '）。許可形式: JPG, JPEG, PNG, WEBP, PDF（最大10MB）'
                    });
                } else if (frontFile.size > 10 * 1024 * 1024) {
                    errors.push({
                        name: 'kyc_id_front',
                        element: frontInput,
                        label: '書類の表面画像 (Front ID Photo)',
                        message: '表面画像のファイルサイズが上限(10MB)を超えています。'
                    });
                }
            }

            // Back ID Image (Required unless My Number Card)
            var docType = $form.find('#kyc_doc_type').val() || '';
            var isMyNumber = (docType.indexOf('マイナンバー') !== -1 || docType.indexOf('mynumber') !== -1);
            var backWrapVisible = $form.find('#back_upload_group, #backSideUploadWrap').is(':visible');

            if (!isMyNumber && backWrapVisible) {
                var backInput = $form.find('input[name="kyc_id_back"], input[name="kyc_back"]')[0];
                if (!backInput || !backInput.files || backInput.files.length === 0) {
                    errors.push({
                        name: 'kyc_id_back',
                        element: backInput,
                        label: '書類の裏面画像 (Back ID Photo)',
                        message: '運転免許証・在留カード・パスポートは裏面写真の提出が必要です。'
                    });
                } else {
                    var backFile = backInput.files[0];
                    var backExt = backFile.name ? backFile.name.split('.').pop().toLowerCase() : '';
                    if (allowedExts.indexOf(backExt) === -1) {
                        errors.push({
                            name: 'kyc_id_back',
                            element: backInput,
                            label: '書類の裏面画像 (Back ID Photo)',
                            message: '未対応のファイル形式です（.' + backExt + '）。許可形式: JPG, JPEG, PNG, WEBP, PDF（最大10MB）'
                        });
                    } else if (backFile.size > 10 * 1024 * 1024) {
                        errors.push({
                            name: 'kyc_id_back',
                            element: backInput,
                            label: '書類の裏面画像 (Back ID Photo)',
                            message: '裏面画像のファイルサイズが上限(10MB)を超えています。'
                        });
                    }
                }
            }

            // Consent Checkbox
            var $consent = $form.find('#kyc_consent, input[name="kyc_consent"]');
            if ($consent.length && !$consent.is(':checked')) {
                errors.push({
                    name: 'kyc_consent',
                    element: $consent[0],
                    label: '個人情報提供への同意 (Consent)',
                    message: '古物営業法に基づく身元確認のため個人情報の提供に同意のチェックを入れてください。'
                });
            }

            return errors;
        }

        // 1. AJAX Buyback Application Form Submission
        $('#sellForm, #kaitori-sell-apply-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            clearFormErrors($form);

            // Run client-side validation
            var errors = validateSellFormClient($form);
            if (errors.length > 0) {
                showFieldErrors($form, errors, '入力内容に不備がある項目があります。該当項目をご確認ください：');
                kaitoriPopup('入力内容に不備があります。赤枠で表示された項目をご確認ください。', 'error');
                return false;
            }

            var $submitBtn = $form.find('button[type="submit"], .btn-submit-app, #btnSubmitSell, #btnFinalSubmit');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.sending) || 'Sending…'));

            var formData = new FormData(this);
            formData.append('action', 'kaitori_submit_application');
            formData.append('security', kaitori_ajax.security);

            $.ajax({
                url: kaitori_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $submitBtn.prop('disabled', false).html(originalText);
                    if (response.success) {
                        $('#sellForm').hide();
                        $('#application-success-modal, #app-success-box').fadeIn();
                        if (document.getElementById('displayOrderCode')) {
                            document.getElementById('displayOrderCode').innerText = response.data.order_code;
                        }
                        if (response.data.order_code) {
                            var appName  = $('input[name="applicant_name"]').val() || '';
                            var appEmail = $('input[name="applicant_email"]').val() || '';
                            var appPhone = $('input[name="applicant_phone"]').val() || '';
                            var kycUrl = kaitori_ajax.home_url + 'kyc-verify/?order=' + encodeURIComponent(response.data.order_code) + '&name=' + encodeURIComponent(appName) + '&email=' + encodeURIComponent(appEmail) + '&phone=' + encodeURIComponent(appPhone);
                            $('#btnGoToKyc, .btn-goto-kyc').attr('href', kycUrl);
                        }
                        kaitoriPopup(response.data.message || (kaitori_ajax.i18n && kaitori_ajax.i18n.app_success) || 'Done', 'success');
                        window.scrollTo({ top: 100, behavior: 'smooth' });
                    } else {
                        // Check if specific field errors returned by server
                        if (response.data && response.data.invalid_fields) {
                            var serverErrors = [];
                            var fieldLabels = {
                                'model': '機種名 (Device Model)',
                                'storage': 'ストレージ容量 (Storage)',
                                'carrier': 'キャリア (Carrier)',
                                'imei': 'IMEI番号 (IMEI / Serial)',
                                'applicant_email': 'メールアドレス (Email)',
                                'agree_terms': '利用規約への同意'
                            };
                            $.each(response.data.invalid_fields, function(fName, msg) {
                                serverErrors.push({
                                    name: fName,
                                    label: fieldLabels[fName] || fName,
                                    message: msg
                                });
                            });
                            showFieldErrors($form, serverErrors, response.data.message);
                        } else if (response.data && response.data.gate) {
                            // Gate error (eKYC or Login required)
                            var gateHtml = '<div class="kaitori-gate-warning-banner">' +
                                '<div style="font-weight:800; color:#b45309; font-size:16px; margin-bottom:8px;">' +
                                '<i class="fa fa-lock" style="color:#d97706; font-size:20px;"></i> ' + escapeHtml(response.data.message) + '</div>' +
                                '<div style="margin-top:14px; display:flex; gap:12px; flex-wrap:wrap;">';
                            if (response.data.kyc_url) {
                                gateHtml += '<a href="' + escapeHtml(response.data.kyc_url) + '" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; border-radius:8px; padding:10px 22px; color:#fff;">本人確認 (eKYC) へ進む &rarr;</a>';
                            }
                            if (response.data.login_url) {
                                gateHtml += '<a href="' + escapeHtml(response.data.login_url) + '" class="btn btn-default" style="font-weight:700; border-radius:8px; padding:10px 20px;">ログイン・会員登録</a>';
                            }
                            gateHtml += '</div></div>';
                            $form.prepend(gateHtml);
                            $('html, body').animate({ scrollTop: $form.offset().top - 80 }, 350);
                        }
                        kaitoriPopup(response.data.message || kaitori_ajax.i18n_error, 'error');
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    kaitoriPopup(kaitori_ajax.i18n_error, 'error');
                }
            });
        });

        // 2. AJAX eKYC Verification Form Submission
        $('#kycForm, #kaitori-kyc-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            clearFormErrors($form);

            // Run client-side validation
            var errors = validateKycFormClient($form);
            if (errors.length > 0) {
                showFieldErrors($form, errors, '本人確認データまたは添付書類に不備があります。以下の項目をご確認ください：');
                kaitoriPopup('入力内容または書類画像に不備があります。赤枠で表示された項目をご確認ください。', 'error');
                return false;
            }

            var $submitBtn = $form.find('button[type="submit"], .btn-submit-kyc, #btnSubmitKyc');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.uploading) || 'Uploading…'));

            var formData = new FormData(this);
            formData.append('action', 'kaitori_submit_kyc');
            formData.append('security', kaitori_ajax.security);

            $.ajax({
                url: kaitori_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $submitBtn.prop('disabled', false).html(originalText);
                    if (response.success) {
                        $form.hide();
                        $('#kyc-success-message').fadeIn();
                        kaitoriPopup(response.data.message || (kaitori_ajax.i18n && kaitori_ajax.i18n.kyc_success) || 'Done', 'success');
                        window.scrollTo({ top: 100, behavior: 'smooth' });
                    } else {
                        if (response.data && response.data.invalid_fields) {
                            var serverErrors = [];
                            var fieldLabels = {
                                'kyc_name': '氏名 (Full Name)',
                                'kyc_kana': 'フリガナ (Katakana)',
                                'kyc_email': 'メールアドレス (Email)',
                                'kyc_phone': '電話番号 (Phone Number)',
                                'kyc_dob': '生年月日 (Date of Birth)',
                                'kyc_postal': '郵便番号 (Postal Code)',
                                'kyc_prefecture': '都道府県 (Prefecture)',
                                'kyc_city': '市区町村 (City / Ward)',
                                'kyc_address': '番地・建物名 (Street Address)',
                                'kyc_id_front': '書類の表面画像 (Front ID Photo)',
                                'kyc_id_back': '書類の裏面画像 (Back ID Photo)',
                                'kyc_selfie': 'セルフィー写真 (Selfie)'
                            };
                            $.each(response.data.invalid_fields, function(fName, msg) {
                                serverErrors.push({
                                    name: fName,
                                    label: fieldLabels[fName] || fName,
                                    message: msg
                                });
                            });
                            showFieldErrors($form, serverErrors, response.data.message);
                        }
                        kaitoriPopup(response.data.message || kaitori_ajax.i18n_error, 'error');
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    kaitoriPopup(kaitori_ajax.i18n_error, 'error');
                }
            });
        });

        // 3. AJAX Member Registration Form Submission
        $('#registerForm, #kaitori-register-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"], .btn-submit-reg');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.registering) || 'Registering…'));

            var formData = new FormData(this);
            formData.append('action', 'kaitori_register_user');
            formData.append('security', kaitori_ajax.security);

            $.ajax({
                url: kaitori_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $submitBtn.prop('disabled', false).html(originalText);
                    if (response.success) {
                        kaitoriPopup(response.data.message || (kaitori_ajax.i18n && kaitori_ajax.i18n.reg_success) || 'Done', 'success');
                        if (response.data.redirect_url) {
                            // Give the success popup a moment before redirecting.
                            setTimeout(function() { window.location.href = response.data.redirect_url; }, 1400);
                        }
                    } else {
                        kaitoriPopup(response.data.message || kaitori_ajax.i18n_error, 'error');
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    kaitoriPopup(kaitori_ajax.i18n_error, 'error');
                }
            });
        });

        // 3.1 AJAX Member Login Form Submission
        $('#loginForm, #kaitori-login-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"], .btn-submit-login');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.logging_in) || 'Logging in…'));

            var formData = new FormData(this);
            formData.append('action', 'kaitori_login_user');
            formData.append('security', kaitori_ajax.security);

            $.ajax({
                url: kaitori_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $submitBtn.prop('disabled', false).html(originalText);
                    if (response.success) {
                        kaitoriPopup(response.data.message || (kaitori_ajax.i18n && kaitori_ajax.i18n.login_success) || 'Done', 'success');
                        // Give the success popup a moment before redirecting / reloading.
                        setTimeout(function() {
                            if (response.data.redirect_url) {
                                window.location.href = response.data.redirect_url;
                            } else {
                                window.location.reload();
                            }
                        }, 1400);
                    } else {
                        kaitoriPopup(response.data.message || kaitori_ajax.i18n_error, 'error');
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    kaitoriPopup(kaitori_ajax.i18n_error, 'error');
                }
            });
        });

        // 3.4 Accessibility: make click-driven widgets keyboard operable.
        (function a11yWidgets() {
            var selectors = '.radio-card, .color-swatch-item, .collect';
            $(selectors).each(function() {
                var $w = $(this);
                if ($w.attr('tabindex') === undefined) { $w.attr('tabindex', '0'); }
                if (!$w.attr('role')) { $w.attr('role', 'button'); }
            });
            // Enter / Space triggers the element's existing click handler.
            $(document).on('keydown', selectors, function(e) {
                if (e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32) {
                    e.preventDefault();
                    this.click();
                }
            });
        })();

        // 3.5 Favorites (★) toggle — persists for members, localStorage for guests
        function getLocalFavs() {
            try { return JSON.parse(localStorage.getItem('kaitori_favs') || '[]'); } catch (e) { return []; }
        }
        function setLocalFavs(arr) {
            try { localStorage.setItem('kaitori_favs', JSON.stringify(arr)); } catch (e) {}
        }

        // Mark initial favorite state on all cards.
        (function markFavorites() {
            var favs = [];
            if (kaitori_ajax.logged_in && kaitori_ajax.favorites) {
                favs = kaitori_ajax.favorites.map(String);
            } else {
                favs = getLocalFavs().map(String);
            }
            $('.collect[data-value]').each(function() {
                var id = String($(this).data('value'));
                $(this).attr('data-isok', favs.indexOf(id) !== -1 ? 'true' : 'false');
            });
        })();

        $(document).on('click', '.collect', function() {
            var $el = $(this);
            var id = $el.data('value');
            if (!id) { return; }

            $.post(kaitori_ajax.ajax_url, {
                action: 'kaitori_toggle_favorite',
                security: kaitori_ajax.security,
                device_id: id
            }, function(res) {
                if (!res || !res.success) {
                    kaitoriPopup((res && res.data && res.data.message) || kaitori_ajax.i18n_error, 'error');
                    return;
                }
                if (res.data.guest) {
                    // Guest: toggle in localStorage, keep the UI responsive, gently prompt once.
                    var favs = getLocalFavs().map(String);
                    var sid = String(id);
                    var idx = favs.indexOf(sid);
                    if (idx === -1) {
                        favs.push(sid);
                        $el.attr('data-isok', 'true');
                    } else {
                        favs.splice(idx, 1);
                        $el.attr('data-isok', 'false');
                    }
                    setLocalFavs(favs);
                } else {
                    $el.attr('data-isok', res.data.active ? 'true' : 'false');
                }
            }).fail(function() {
                kaitoriPopup(kaitori_ajax.i18n_error, 'error');
            });
        });

        // 4. Step Search / Filter Auto-Submit or AJAX
        $('.step_search select, .keyword_search select').on('change', function() {
            var $form = $(this).closest('form');
            // If inside live filter container
            if ($('#live-device-results').length > 0) {
                var queryData = $form.serialize();
                queryData += '&action=kaitori_filter_devices';

                $('#live-device-results').html('<div class="text-center" style="padding:40px;"><i class="fa fa-spinner fa-spin fa-2x"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.loading) || 'Loading…') + '</div>');

                $.get(kaitori_ajax.ajax_url, queryData, function(html) {
                    $('#live-device-results').html(html);
                });
            }
        });

    });

})(jQuery);
