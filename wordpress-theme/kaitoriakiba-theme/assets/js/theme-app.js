/**
 * Kaitori Akiba Theme Application Scripts
 * Handles AJAX forms for Sell-Apply, eKYC, and live search.
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        // 1. AJAX Buyback Application Form Submission
        $('#sellForm, #kaitori-sell-apply-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"], .btn-submit-app');
            var originalText = $submitBtn.html();

            // Validate mandatory fields
            var imei = $form.find('input[name="imei"]').val();
            if (imei && imei.length !== 15) {
                alert('IMEI番号は15桁の数字で入力してください。');
                return false;
            }

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> 送信中...');

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
                        alert(response.data.message || '買取申込が完了いたしました！');
                        window.scrollTo({ top: 100, behavior: 'smooth' });
                    } else {
                        alert(response.data.message || kaitori_ajax.i18n_error);
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    alert(kaitori_ajax.i18n_error);
                }
            });
        });

        // 2. AJAX eKYC Verification Form Submission
        $('#kycForm, #kaitori-kyc-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"], .btn-submit-kyc');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> 書類アップロード中...');

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
                        alert(response.data.message || '本人確認書類の提出が完了いたしました。');
                        window.scrollTo({ top: 100, behavior: 'smooth' });
                    } else {
                        alert(response.data.message || kaitori_ajax.i18n_error);
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    alert(kaitori_ajax.i18n_error);
                }
            });
        });

        // 3. AJAX Member Registration Form Submission
        $('#registerForm, #kaitori-register-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"], .btn-submit-reg');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> 登録中...');

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
                        alert(response.data.message || '会員登録が完了いたしました！');
                        if (response.data.redirect_url) {
                            window.location.href = response.data.redirect_url;
                        }
                    } else {
                        alert(response.data.message || kaitori_ajax.i18n_error);
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    alert(kaitori_ajax.i18n_error);
                }
            });
        });

        // 3.1 AJAX Member Login Form Submission
        $('#loginForm, #kaitori-login-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"], .btn-submit-login');
            var originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ログイン中...');

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
                        alert(response.data.message || 'ログインしました。');
                        if (response.data.redirect_url) {
                            window.location.href = response.data.redirect_url;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert(response.data.message || kaitori_ajax.i18n_error);
                    }
                },
                error: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                    alert(kaitori_ajax.i18n_error);
                }
            });
        });

        // 4. Step Search / Filter Auto-Submit or AJAX
        $('.step_search select, .keyword_search select').on('change', function() {
            var $form = $(this).closest('form');
            // If inside live filter container
            if ($('#live-device-results').length > 0) {
                var queryData = $form.serialize();
                queryData += '&action=kaitori_filter_devices';

                $('#live-device-results').html('<div class="text-center" style="padding:40px;"><i class="fa fa-spinner fa-spin fa-2x"></i> 読み込み中...</div>');

                $.get(kaitori_ajax.ajax_url, queryData, function(html) {
                    $('#live-device-results').html(html);
                });
            }
        });

    });

})(jQuery);
