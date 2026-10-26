/**
 * Kaitori Akiba — Admin Customer Management + Barcode Scanner
 */
(function ($) {
    'use strict';

    function post(action, data, done) {
        data = data || {};
        data.action = action;
        data.nonce = kaitori_admin.nonce;
        $.post(kaitori_admin.ajax_url, data, done).fail(function () {
            alert(kaitori_admin.i18n.error);
        });
    }

    $(function () {

        // --- Inline order status update ---
        $(document).on('change', '.kaitori-order-status', function () {
            var $sel = $(this);
            var orderId = $sel.data('order');
            $sel.prop('disabled', true);
            post('kaitori_admin_update_order_status', { order_id: orderId, status: $sel.val() }, function (res) {
                $sel.prop('disabled', false);
                $sel.css('outline', res && res.success ? '2px solid #16a34a' : '2px solid #dc2626');
                setTimeout(function () { $sel.css('outline', ''); }, 1200);
            });
        });

        // --- Save customer profile ---
        $(document).on('click', '.kaitori-save-customer', function () {
            var $wrap = $('.kaitori-detail');
            var $msg = $('.kaitori-save-msg');
            post('kaitori_admin_update_customer', {
                email: $wrap.data('email'),
                user_id: $wrap.data('user'),
                name: $('#cust_name').val(),
                phone: $('#cust_phone').val()
            }, function (res) {
                if (res && res.success) {
                    $msg.text(kaitori_admin.i18n.saved).attr('class', 'kaitori-save-msg ok');
                } else {
                    $msg.text((res && res.data && res.data.message) || kaitori_admin.i18n.error).attr('class', 'kaitori-save-msg err');
                }
                setTimeout(function () { $msg.text(''); }, 2500);
            });
        });

        // USB scanners append an Enter keystroke; on the order editor, stop that from
        // submitting/publishing the post. The scanned value stays in the field.
        $(document).on('keydown', '#kaitori_order_imei', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) { e.preventDefault(); $(this).blur(); }
        });

        // --- Barcode scanner ---
        var activeStream = null, scanLoop = null;

        function stopScan() {
            if (scanLoop) { clearInterval(scanLoop); scanLoop = null; }
            if (activeStream) { activeStream.getTracks().forEach(function (t) { t.stop(); }); activeStream = null; }
            $('.kaitori-scan-modal').remove();
        }

        function saveScan(orderId, field, code, $row) {
            post('kaitori_admin_save_scan', { order_id: orderId, field: field, code: code }, function (res) {
                if (res && res.success) {
                    if ($row && $row.length) { $row.find('.kaitori-imei').text(res.data.code); }
                    // Order-edit screen: reflect into the editable IMEI input too.
                    if (res.data.field === 'imei') { $('#kaitori_order_imei').val(res.data.code); }
                    alert(kaitori_admin.i18n.saved + ' (' + res.data.code + ')');
                } else {
                    alert((res && res.data && res.data.message) || kaitori_admin.i18n.error);
                }
            });
        }

        $(document).on('click', '.kaitori-scan', function () {
            var $btn = $(this);
            var orderId = $btn.data('order');
            var field = $btn.data('field') || 'imei';
            var $row = $btn.closest('tr');

            var modalHtml =
                '<div class="kaitori-scan-modal"><div class="kaitori-scan-box">' +
                '<h3>🔖 ' + (field === 'serial' ? 'Serial' : 'IMEI / JAN') + ' Scan</h3>' +
                '<div class="kaitori-scan-hint">' + kaitori_admin.i18n.usb_hint + '</div>' +
                '<input type="text" class="kaitori-scan-manual" autocomplete="off" placeholder="' + kaitori_admin.i18n.scan_here + '">' +
                '<video autoplay playsinline muted></video>' +
                '<div class="kaitori-scan-status">' + kaitori_admin.i18n.scan_prompt + '</div>' +
                '<div class="kaitori-scan-actions">' +
                '<button class="button button-primary kaitori-scan-save">保存</button>' +
                '<button class="button kaitori-scan-cancel">閉じる</button>' +
                '</div></div></div>';
            $('body').append(modalHtml);

            var $modal = $('.kaitori-scan-modal');
            var video = $modal.find('video')[0];
            var $status = $modal.find('.kaitori-scan-status');
            var $manual = $modal.find('.kaitori-scan-manual');

            function commitManual() {
                var code = ($manual.val() || '').trim();
                if (!code) { return; }
                saveScan(orderId, field, code, $row);
                stopScan();
            }

            // USB HID (keyboard-wedge) scanners type the code + Enter into the focused
            // field. Auto-focus the input so a scan lands here, and Enter saves it.
            $manual.trigger('focus');
            $manual.on('keydown', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    commitManual();
                }
            });

            $modal.on('click', '.kaitori-scan-cancel', stopScan);
            $modal.on('click', '.kaitori-scan-save', commitManual);

            // Native BarcodeDetector + camera, with graceful manual fallback.
            if (!('BarcodeDetector' in window) || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                $status.text(kaitori_admin.i18n.no_camera);
                $(video).hide();
                return;
            }

            var detector;
            try {
                detector = new window.BarcodeDetector();
            } catch (e) {
                $status.text(kaitori_admin.i18n.no_camera);
                $(video).hide();
                return;
            }

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function (stream) {
                    activeStream = stream;
                    video.srcObject = stream;
                    scanLoop = setInterval(function () {
                        detector.detect(video).then(function (codes) {
                            if (codes && codes.length) {
                                var val = (codes[0].rawValue || '').replace(/[^0-9A-Za-z]/g, '');
                                if (val) {
                                    $status.text('✓ ' + val);
                                    $manual.val(val);
                                    saveScan(orderId, field, val, $row);
                                    stopScan();
                                }
                            }
                        }).catch(function () {});
                    }, 400);
                })
                .catch(function () {
                    $status.text(kaitori_admin.i18n.no_camera);
                    $(video).hide();
                });
        });

    });
})(jQuery);
