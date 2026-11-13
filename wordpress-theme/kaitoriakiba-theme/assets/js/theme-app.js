/**
 * Kaitori Akiba Theme Application Scripts
 * Handles AJAX forms for Sell-Apply, eKYC, and live search.
 */

(function($) {
    'use strict';

    // Bilingual helper mirroring PHP kaitori_t(): picks EN or JA by the
    // current site language (passed through the kaitori_ajax localize).
    var KLANG = (typeof kaitori_ajax !== 'undefined' && kaitori_ajax.lang) ? kaitori_ajax.lang : 'ja';
    function kt(en, ja) { return (KLANG === 'en') ? en : ja; }

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

            var title = customTitle || kt('Some entries or documents are invalid. Please check and correct the following items:', '入力内容または書類に不備があります。以下の項目をご確認・修正してください：');
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
                    label: kt('Device Model', '機種名'),
                    message: kt('Please select the device model you want to sell.', '買取希望の機種名を選択してください。'),
                    step: 1
                });
            }

            // Storage
            var storage = $form.find('input[name="storage"]:checked, [name="device_storage"]').val();
            if (!storage || storage.trim() === '') {
                errors.push({
                    name: 'storage',
                    label: kt('Storage', 'ストレージ容量'),
                    message: kt('Please select a storage capacity.', 'ストレージ容量を選択してください。'),
                    step: 1
                });
            }

            // Carrier
            var carrier = $form.find('input[name="carrier"]:checked, [name="device_carrier"]').val();
            if (!carrier || carrier.trim() === '') {
                errors.push({
                    name: 'carrier',
                    label: kt('Carrier', 'キャリア'),
                    message: kt('Please select the carrier / SIM status.', 'キャリア・SIM状態を選択してください。'),
                    step: 1
                });
            }

            // Color
            var $colorEl = $form.find('#selected_color, [name="device_color"]');
            if ($colorEl.length && (!$colorEl.val() || $colorEl.val().trim() === '')) {
                errors.push({
                    name: 'color',
                    label: kt('Color', 'カラー'),
                    message: kt('Please select or enter the device color.', '本体カラーを選択または入力してください。'),
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
                        label: kt('IMEI / Serial', 'IMEI番号'),
                        message: kt('Please enter the IMEI as 15 half-width digits. (e.g. 351234567890123)', 'IMEI番号は15桁の半角数字で入力してください。(例: 351234567890123)'),
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
                        label: kt('Email', 'メールアドレス'),
                        message: kt('Please enter a valid email address. (e.g. taro@example.com)', '有効なメールアドレスの形式で入力してください。(例: taro@example.com)'),
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
                        label: kt('Phone Number', '電話番号'),
                        message: kt('Please enter a 10–11 digit phone number. (e.g. 09012345678)', '10〜11桁の半角数字で電話番号を入力してください。(例: 09012345678)'),
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
                    label: kt('Terms of Service', '利用規約への同意'),
                    message: kt('Please check the box to agree to the Terms of Service and the handling of personal information.', '利用規約および個人情報の取扱いに同意のチェックを入れてください。'),
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
                    label: kt('Full Name', '氏名'),
                    message: kt('Please enter your name as written on your ID document (in kanji).', '身分証明書記載のお名前（漢字）を入力してください。')
                });
            }

            // Furigana
            var $kana = $form.find('input[name="kyc_kana"]');
            if ($kana.length && (!$kana.val() || $kana.val().trim() === '')) {
                errors.push({
                    name: 'kyc_kana',
                    element: $kana[0],
                    label: kt('Katakana', 'フリガナ'),
                    message: kt('Please enter your name in katakana. (e.g. ヤマダ タロウ)', 'カタカナでお名前を入力してください。(例: ヤマダ タロウ)')
                });
            }

            // Email
            var $email = $form.find('input[name="kyc_email"], input[name="customer_email"]');
            if (!$email.val() || $email.val().trim() === '') {
                errors.push({
                    name: $email.attr('name') || 'kyc_email',
                    element: $email[0],
                    label: kt('Email', 'メールアドレス'),
                    message: kt('Please enter an email address where we can reach you.', '連絡可能なメールアドレスを入力してください。')
                });
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($email.val().trim())) {
                errors.push({
                    name: $email.attr('name') || 'kyc_email',
                    element: $email[0],
                    label: kt('Email', 'メールアドレス'),
                    message: kt('Please enter a valid email address. (e.g. user@example.com)', '有効なメールアドレスの形式で入力してください。(例: user@example.com)')
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
                        label: kt('Phone Number', '電話番号'),
                        message: kt('Please enter your phone number. (e.g. 09012345678)', '電話番号を入力してください。(例: 09012345678)')
                    });
                } else if (phoneClean.length < 10 || !/^\d{10,12}$/.test(phoneClean)) {
                    errors.push({
                        name: 'kyc_phone',
                        element: $phone[0],
                        label: kt('Phone Number', '電話番号'),
                        message: kt('Please enter a 10–11 digit phone number.', '10〜11桁の半角数字で電話番号を入力してください。')
                    });
                }
            }

            // Date of birth
            var $dob = $form.find('input[name="kyc_dob"]');
            if ($dob.length && (!$dob.val() || $dob.val().trim() === '')) {
                errors.push({
                    name: 'kyc_dob',
                    element: $dob[0],
                    label: kt('Date of Birth', '生年月日'),
                    message: kt('Please select your date of birth.', '生年月日を選択してください。')
                });
            }

            // Postal Code
            var $postal = $form.find('input[name="kyc_postal"]');
            if ($postal.length && (!$postal.val() || $postal.val().trim() === '')) {
                errors.push({
                    name: 'kyc_postal',
                    element: $postal[0],
                    label: kt('Postal Code', '郵便番号'),
                    message: kt('Please enter your postal code. (e.g. 101-0021)', '郵便番号を入力してください。(例: 101-0021)')
                });
            }

            // Prefecture
            var $pref = $form.find('input[name="kyc_prefecture"]');
            if ($pref.length && (!$pref.val() || $pref.val().trim() === '')) {
                errors.push({
                    name: 'kyc_prefecture',
                    element: $pref[0],
                    label: kt('Prefecture', '都道府県'),
                    message: kt('Please enter your prefecture. (e.g. Tokyo)', '都道府県を入力してください。(例: 東京都)')
                });
            }

            // City
            var $city = $form.find('input[name="kyc_city"]');
            if ($city.length && (!$city.val() || $city.val().trim() === '')) {
                errors.push({
                    name: 'kyc_city',
                    element: $city[0],
                    label: kt('City / Ward', '市区町村'),
                    message: kt('Please enter your city, ward, or town. (e.g. Chiyoda-ku)', '市区町村を入力してください。(例: 千代田区)')
                });
            }

            // Street Address
            var $addr = $form.find('input[name="kyc_address"]');
            if ($addr.length && (!$addr.val() || $addr.val().trim() === '')) {
                errors.push({
                    name: 'kyc_address',
                    element: $addr[0],
                    label: kt('Street Address', '番地・建物名'),
                    message: kt('Please enter your street address, building name, and room number.', '番地・建物名・部屋番号を入力してください。')
                });
            }

            var allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

            // Front ID Image (Required)
            var frontInput = $form.find('input[name="kyc_id_front"], input[name="kyc_front"]')[0];
            if (!frontInput || !frontInput.files || frontInput.files.length === 0) {
                errors.push({
                    name: 'kyc_id_front',
                    element: frontInput,
                    label: kt('Front ID Photo', '書類の表面画像'),
                    message: kt('Please select or take a photo of the front of your ID document. (Required)', '本人確認書類の表面写真を選択または撮影してください。(必須)')
                });
            } else {
                var frontFile = frontInput.files[0];
                var frontExt = frontFile.name ? frontFile.name.split('.').pop().toLowerCase() : '';
                if (allowedExts.indexOf(frontExt) === -1) {
                    errors.push({
                        name: 'kyc_id_front',
                        element: frontInput,
                        label: kt('Front ID Photo', '書類の表面画像'),
                        message: kt('Unsupported file format (.', '未対応のファイル形式です（.') + frontExt + kt(') is not supported. Allowed formats: JPG, JPEG, PNG, WEBP, PDF (max 10MB)', '）。許可形式: JPG, JPEG, PNG, WEBP, PDF（最大10MB）')
                    });
                } else if (frontFile.size > 10 * 1024 * 1024) {
                    errors.push({
                        name: 'kyc_id_front',
                        element: frontInput,
                        label: kt('Front ID Photo', '書類の表面画像'),
                        message: kt('The front image exceeds the 10MB file-size limit.', '表面画像のファイルサイズが上限(10MB)を超えています。')
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
                        label: kt('Back ID Photo', '書類の裏面画像'),
                        message: kt('A back photo is required for a driver\'s license, residence card, or passport.', '運転免許証・在留カード・パスポートは裏面写真の提出が必要です。')
                    });
                } else {
                    var backFile = backInput.files[0];
                    var backExt = backFile.name ? backFile.name.split('.').pop().toLowerCase() : '';
                    if (allowedExts.indexOf(backExt) === -1) {
                        errors.push({
                            name: 'kyc_id_back',
                            element: backInput,
                            label: kt('Back ID Photo', '書類の裏面画像'),
                            message: kt('Unsupported file format (.', '未対応のファイル形式です（.') + backExt + kt(') is not supported. Allowed formats: JPG, JPEG, PNG, WEBP, PDF (max 10MB)', '）。許可形式: JPG, JPEG, PNG, WEBP, PDF（最大10MB）')
                        });
                    } else if (backFile.size > 10 * 1024 * 1024) {
                        errors.push({
                            name: 'kyc_id_back',
                            element: backInput,
                            label: kt('Back ID Photo', '書類の裏面画像'),
                            message: kt('The back image exceeds the 10MB file-size limit.', '裏面画像のファイルサイズが上限(10MB)を超えています。')
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
                    label: kt('Consent', '個人情報提供への同意'),
                    message: kt('Please check the box to consent to providing personal information for identity verification under the Secondhand Dealer Act.', '古物営業法に基づく身元確認のため個人情報の提供に同意のチェックを入れてください。')
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
                showFieldErrors($form, errors, kt('Some fields contain invalid input. Please check the following items:', '入力内容に不備がある項目があります。該当項目をご確認ください：'));
                kaitoriPopup(kt('Some entries are invalid. Please check the fields highlighted in red.', '入力内容に不備があります。赤枠で表示された項目をご確認ください。'), 'error');
                return false;
            }

            // Client-side reCAPTCHA check if active on form
            if ($form.find('.g-recaptcha').length) {
                var rCheckSell = $form.find('textarea[name="g-recaptcha-response"]').val();
                if (!rCheckSell && typeof grecaptcha !== 'undefined') {
                    try { rCheckSell = grecaptcha.getResponse(); } catch(err) {}
                }
                if (!rCheckSell) {
                    kaitoriPopup(
                        (kaitori_ajax.i18n && kaitori_ajax.i18n.recaptcha_required) ||
                        'Please verify that you are not a robot by checking the reCAPTCHA box.',
                        'warning'
                    );
                    return false;
                }
            }

            var $submitBtn = $form.find('button[type="submit"], .btn-submit-app, #btnSubmitSell, #btnFinalSubmit');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.sending) || 'Sending…'));

            var formData = new FormData(this);
            formData.append('action', 'kaitori_submit_application');
            formData.append('security', kaitori_ajax.security);

            // Ensure g-recaptcha-response is explicitly included
            var formTokenSell = $form.find('textarea[name="g-recaptcha-response"]').val();
            if (!formTokenSell && typeof grecaptcha !== 'undefined') {
                try { formTokenSell = grecaptcha.getResponse(); } catch(err) {}
            }
            if (formTokenSell) {
                formData.set('g-recaptcha-response', formTokenSell);
            }

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

                        // Toggle verified notice vs eKYC upload prompt
                        var isVerified = response.data && (response.data.kyc_status === 'verified');
                        if (isVerified) {
                            $('#kyc-needed-box, #btnGoToKyc, .btn-goto-kyc').hide();
                            $('#kyc-verified-success-box').show();
                        } else {
                            $('#kyc-verified-success-box').hide();
                            $('#kyc-needed-box, #btnGoToKyc').show();
                            if (response.data.order_code) {
                                var appName  = $('input[name="applicant_name"]').val() || '';
                                var appEmail = $('input[name="applicant_email"]').val() || '';
                                var appPhone = $('input[name="applicant_phone"]').val() || '';
                                var kycUrl = kaitori_ajax.home_url + 'kyc-verify/?order=' + encodeURIComponent(response.data.order_code) + '&name=' + encodeURIComponent(appName) + '&email=' + encodeURIComponent(appEmail) + '&phone=' + encodeURIComponent(appPhone);
                                $('#btnGoToKyc, .btn-goto-kyc').attr('href', kycUrl);
                            }
                        }

                        kaitoriPopup(response.data.message || (kaitori_ajax.i18n && kaitori_ajax.i18n.app_success) || 'Done', 'success');
                        window.scrollTo({ top: 100, behavior: 'smooth' });
                    } else {
                        if (typeof grecaptcha !== 'undefined') {
                            try { grecaptcha.reset(); } catch (err) {}
                        }
                        // Check if specific field errors returned by server
                        if (response.data && response.data.invalid_fields) {
                            var serverErrors = [];
                            var fieldLabels = {
                                'model': kt('Device Model', '機種名'),
                                'storage': kt('Storage', 'ストレージ容量'),
                                'carrier': kt('Carrier', 'キャリア'),
                                'imei': kt('IMEI / Serial', 'IMEI番号'),
                                'applicant_email': kt('Email', 'メールアドレス'),
                                'agree_terms': kt('Terms of Service', '利用規約への同意')
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
                                gateHtml += '<a href="' + escapeHtml(response.data.kyc_url) + '" class="btn btn-danger" style="background:#ef4444; border-color:#ef4444; font-weight:700; border-radius:8px; padding:10px 22px; color:#fff;">' + kt('Proceed to Identity Verification (eKYC)', '本人確認 (eKYC) へ進む') + ' &rarr;</a>';
                            }
                            if (response.data.login_url) {
                                gateHtml += '<a href="' + escapeHtml(response.data.login_url) + '" class="btn btn-default" style="font-weight:700; border-radius:8px; padding:10px 20px;">' + kt('Log in / Register', 'ログイン・会員登録') + '</a>';
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
                    if (typeof grecaptcha !== 'undefined') {
                        try { grecaptcha.reset(); } catch (err) {}
                    }
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
                showFieldErrors($form, errors, kt('There is a problem with your identity-verification data or attached documents. Please check the following items:', '本人確認データまたは添付書類に不備があります。以下の項目をご確認ください：'));
                kaitoriPopup(kt('Some entries or document images are invalid. Please check the fields highlighted in red.', '入力内容または書類画像に不備があります。赤枠で表示された項目をご確認ください。'), 'error');
                return false;
            }

            // Client-side reCAPTCHA check if active on form
            if ($form.find('.g-recaptcha').length) {
                var rCheckKyc = $form.find('textarea[name="g-recaptcha-response"]').val();
                if (!rCheckKyc && typeof grecaptcha !== 'undefined') {
                    try { rCheckKyc = grecaptcha.getResponse(); } catch(err) {}
                }
                if (!rCheckKyc) {
                    kaitoriPopup(
                        (kaitori_ajax.i18n && kaitori_ajax.i18n.recaptcha_required) ||
                        'Please verify that you are not a robot by checking the reCAPTCHA box.',
                        'warning'
                    );
                    return false;
                }
            }

            var $submitBtn = $form.find('button[type="submit"], .btn-submit-kyc, #btnSubmitKyc');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.uploading) || 'Uploading…'));

            var formData = new FormData(this);
            formData.append('action', 'kaitori_submit_kyc');
            formData.append('security', kaitori_ajax.security);

            // Ensure g-recaptcha-response is explicitly included
            var formTokenKyc = $form.find('textarea[name="g-recaptcha-response"]').val();
            if (!formTokenKyc && typeof grecaptcha !== 'undefined') {
                try { formTokenKyc = grecaptcha.getResponse(); } catch(err) {}
            }
            if (formTokenKyc) {
                formData.set('g-recaptcha-response', formTokenKyc);
            }

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
                                'kyc_name': kt('Full Name', '氏名'),
                                'kyc_kana': kt('Katakana', 'フリガナ'),
                                'kyc_email': kt('Email', 'メールアドレス'),
                                'kyc_phone': kt('Phone Number', '電話番号'),
                                'kyc_dob': kt('Date of Birth', '生年月日'),
                                'kyc_postal': kt('Postal Code', '郵便番号'),
                                'kyc_prefecture': kt('Prefecture', '都道府県'),
                                'kyc_city': kt('City / Ward', '市区町村'),
                                'kyc_address': kt('Street Address', '番地・建物名'),
                                'kyc_id_front': kt('Front ID Photo', '書類の表面画像'),
                                'kyc_id_back': kt('Back ID Photo', '書類の裏面画像'),
                                'kyc_selfie': kt('Selfie', 'セルフィー写真')
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
                        if (typeof grecaptcha !== 'undefined') {
                            try { grecaptcha.reset(); } catch (err) {}
                        }
                        kaitoriPopup(response.data.message || kaitori_ajax.i18n_error, 'error');
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    if (typeof grecaptcha !== 'undefined') {
                        try { grecaptcha.reset(); } catch (err) {}
                    }
                    kaitoriPopup(kaitori_ajax.i18n_error, 'error');
                }
            });
        });

        // 3. AJAX Member Registration Form Submission
        $('#registerForm, #kaitori-register-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);

            // Client-side reCAPTCHA check if active on form
            if ($form.find('.g-recaptcha').length) {
                var rCheck = $form.find('textarea[name="g-recaptcha-response"]').val();
                if (!rCheck && typeof grecaptcha !== 'undefined') {
                    try { rCheck = grecaptcha.getResponse(); } catch(err) {}
                }
                if (!rCheck) {
                    kaitoriPopup(
                        (kaitori_ajax.i18n && kaitori_ajax.i18n.recaptcha_required) ||
                        'Please verify that you are not a robot by checking the reCAPTCHA box.',
                        'warning'
                    );
                    return false;
                }
            }

            var $submitBtn = $form.find('button[type="submit"], .btn-submit-reg');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.registering) || 'Registering…'));

            var formData = new FormData(this);
            formData.append('action', 'kaitori_register_user');
            formData.append('security', kaitori_ajax.security);

            // Ensure g-recaptcha-response is explicitly included
            var formTokenReg = $form.find('textarea[name="g-recaptcha-response"]').val();
            if (!formTokenReg && typeof grecaptcha !== 'undefined') {
                try { formTokenReg = grecaptcha.getResponse(); } catch(err) {}
            }
            if (formTokenReg) {
                formData.set('g-recaptcha-response', formTokenReg);
            }

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
                        if (typeof grecaptcha !== 'undefined') {
                            try { grecaptcha.reset(); } catch (err) {}
                        }
                        kaitoriPopup(response.data.message || kaitori_ajax.i18n_error, 'error');
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    if (typeof grecaptcha !== 'undefined') {
                        try { grecaptcha.reset(); } catch (err) {}
                    }
                    kaitoriPopup(kaitori_ajax.i18n_error, 'error');
                }
            });
        });

        // 3.1 AJAX Member Login Form Submission
        $('#loginForm, #kaitori-login-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);

            // Client-side reCAPTCHA check if active on form
            if ($form.find('.g-recaptcha').length) {
                var rCheckLog = $form.find('textarea[name="g-recaptcha-response"]').val();
                if (!rCheckLog && typeof grecaptcha !== 'undefined') {
                    try { rCheckLog = grecaptcha.getResponse(); } catch(err) {}
                }
                if (!rCheckLog) {
                    kaitoriPopup(
                        (kaitori_ajax.i18n && kaitori_ajax.i18n.recaptcha_required) ||
                        'Please verify that you are not a robot by checking the reCAPTCHA box.',
                        'warning'
                    );
                    return false;
                }
            }

            var $submitBtn = $form.find('button[type="submit"], .btn-submit-login');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.logging_in) || 'Logging in…'));

            var formData = new FormData(this);
            formData.append('action', 'kaitori_login_user');
            formData.append('security', kaitori_ajax.security);

            // Ensure g-recaptcha-response is explicitly included
            var formTokenLog = $form.find('textarea[name="g-recaptcha-response"]').val();
            if (!formTokenLog && typeof grecaptcha !== 'undefined') {
                try { formTokenLog = grecaptcha.getResponse(); } catch(err) {}
            }
            if (formTokenLog) {
                formData.set('g-recaptcha-response', formTokenLog);
            }

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
                        if (typeof grecaptcha !== 'undefined') {
                            try { grecaptcha.reset(); } catch (err) {}
                        }
                        kaitoriPopup(response.data.message || kaitori_ajax.i18n_error, 'error');
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    if (typeof grecaptcha !== 'undefined') {
                        try { grecaptcha.reset(); } catch (err) {}
                    }
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

        // 4. Live Device Filter (AJAX, no page reload) — /devices/ archive.
        //    Collects keyword + category + carrier + brand + series from BOTH the
        //    keyword form and the step form, so they always combine. Runs on
        //    dropdown change, debounced keyword typing, Search-button/Enter, and
        //    pagination clicks. Falls back to normal navigation off this page.
        (function deviceLiveFilter() {
            var $results = $('#live-device-results');
            if (!$results.length) { return; } // only on the device list page

            var $count  = $('#device-result-count');
            var kwTimer = null;

            function readCat() {
                // Two selects share name="cat" (keyword form + step form); use whichever is set.
                var cat = '';
                $('select[name="cat"]').each(function() {
                    if ($(this).val()) { cat = $(this).val(); }
                });
                return cat;
            }

            function collect() {
                return {
                    q:        ($('.keyword_search_text').first().val() || '').trim(),
                    cat:      readCat(),
                    s_career: $('select[name="s_career"]').val() || '',
                    brand:    $('select[name="brand"]').val() || '',
                    series:   $('select[name="series"]').val() || ''
                };
            }

            function run(paged) {
                var data = collect();
                data.action = 'kaitori_filter_devices';
                data.paged  = paged || 1;

                $results.html('<div class="text-center" style="padding:40px;"><i class="fa fa-spinner fa-spin fa-2x"></i> ' + ((kaitori_ajax.i18n && kaitori_ajax.i18n.loading) || 'Loading…') + '</div>');

                $.get(kaitori_ajax.ajax_url, data, function(res) {
                    if (res && res.success && res.data) {
                        $results.html(res.data.html);
                        if ($count.length && typeof res.data.count_label !== 'undefined') {
                            $count.text(res.data.count_label);
                        }
                    } else if (typeof res === 'string') {
                        $results.html(res); // defensive fallback
                    }
                }).fail(function() {
                    $results.html('<div class="alert alert-warning text-center" style="margin:30px 0;"><i class="fa fa-exclamation-triangle"></i> ' + (kaitori_ajax.i18n_error || 'Error') + '</div>');
                });
            }

            // Keep the two category selects in sync so the UI never contradicts itself.
            $(document).on('change', 'select[name="cat"]', function() {
                var v = $(this).val();
                $('select[name="cat"]').not(this).val(v);
            });

            // Dropdown change → filter.
            $(document).on('change', 'select[name="cat"], select[name="s_career"], select[name="brand"], select[name="series"]', function() {
                run(1);
            });

            // Keyword typing → debounced filter (fires on empty, or 2+ chars).
            $(document).on('input', '.keyword_search_text', function() {
                clearTimeout(kwTimer);
                kwTimer = setTimeout(function() {
                    var v = ($('.keyword_search_text').first().val() || '').trim();
                    if (v.length === 0 || v.length >= 2) { run(1); }
                }, 450);
            });

            // Search button / Enter → AJAX instead of a page reload.
            $('.search_box form').on('submit', function(e) {
                e.preventDefault();
                run(1);
            });

            // Pagination (delegated; links carry data-paged).
            $results.on('click', '.pro-ajax-pagination a', function(e) {
                e.preventDefault();
                var paged = parseInt($(this).data('paged'), 10) || 1;
                run(paged);
                if ($results.offset()) {
                    $('html, body').animate({ scrollTop: $results.offset().top - 120 }, 250);
                }
            });
        })();

        // 5. Kaitori Loop Carousel Swiper Initialization
        function initKaitoriLoopCarousel($scope) {
            var $wrapper = $scope.find('.kaitori-loop-carousel-wrapper');
            if (!$wrapper.length) return;

            $wrapper.each(function() {
                var $this = $(this);
                var rawCfg = $this.attr('data-carousel-config');
                if (!rawCfg) return;
                var cfg = JSON.parse(rawCfg);
                var container = $this.find('.kaitori-loop-swiper')[0];
                if (!container) return;

                var swiperOpts = {
                    slidesPerView: cfg.slidesPerView || 1,
                    spaceBetween: cfg.spaceBetween || 0,
                    loop: (cfg.loop === true),
                    speed: cfg.speed || 600,
                    grabCursor: true,
                    watchSlidesProgress: true,
                    observer: true,
                    observeParents: true
                };

                if (cfg.effect === 'fade') {
                    swiperOpts.effect = 'fade';
                    swiperOpts.fadeEffect = { crossFade: true };
                }

                if (cfg.autoplay) {
                    swiperOpts.autoplay = {
                        delay: cfg.autoplayDelay || 4000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: (cfg.pauseOnHover === true)
                    };
                    swiperOpts.autoplayDisableOnInteraction = false;
                }

                if (cfg.showArrows) {
                    var prevEl = $this.find('.kaitori-carousel-prev')[0];
                    var nextEl = $this.find('.kaitori-carousel-next')[0];
                    if (prevEl && nextEl) {
                        swiperOpts.navigation = { prevEl: prevEl, nextEl: nextEl };
                        swiperOpts.prevButton = prevEl;
                        swiperOpts.nextButton = nextEl;
                    }
                }

                if (cfg.showPagination) {
                    var pagEl = $this.find('.kaitori-carousel-pagination')[0];
                    if (pagEl) {
                        swiperOpts.pagination = {
                            el: pagEl,
                            clickable: true,
                            type: cfg.paginationType || 'bullets'
                        };
                        swiperOpts.paginationClickable = true;
                    }
                }

                if (cfg.breakpoints) {
                    swiperOpts.breakpoints = cfg.breakpoints;
                }

                if (container.swiperInstance && typeof container.swiperInstance.destroy === 'function') {
                    try { container.swiperInstance.destroy(true, true); } catch(e) {}
                }

                if (typeof Swiper !== 'undefined') {
                    container.swiperInstance = new Swiper(container, swiperOpts);
                } else if (typeof $.fn.swiper === 'function') {
                    container.swiperInstance = $(container).swiper(swiperOpts);
                }
            });
        }

        // Initialize on ready
        initKaitoriLoopCarousel($(document));

        // Hook Elementor Live Editor
        $(window).on('elementor/frontend/init', function() {
            if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/kaitori_loop_carousel.default', function($scope) {
                    initKaitoriLoopCarousel($scope);
                });
            }
        });
    });

})(jQuery);
