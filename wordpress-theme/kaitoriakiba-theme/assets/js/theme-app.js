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

        // 1. AJAX Buyback Application Form Submission
        $('#sellForm, #kaitori-sell-apply-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"], .btn-submit-app');
            var originalText = $submitBtn.html();

            // Validate fields (IMEI is optional, but if entered it must be 15 digits)
            var imei = $form.find('input[name="imei"], input[name="device_imei"]').val();
            if (imei && imei.trim() !== '' && imei.trim().length !== 15) {
                kaitoriPopup((kaitori_ajax.i18n && kaitori_ajax.i18n.imei_error) || 'Invalid IMEI', 'error');
                return false;
            }

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
                        // Update KYC button with order tracking & customer parameters
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
            var $submitBtn = $form.find('button[type="submit"], .btn-submit-kyc');
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
