/**
 * Kaitori Akiba — Admin media picker
 *
 * Powers the "Select image" / "Clear" buttons on the Sidebar settings page and
 * on the carrier/brand/series term screens. Each button targets a text input by
 * id (data-target) and writes the chosen attachment URL into it.
 */
(function ($) {
    'use strict';

    $(document).on('click', '.kaitori-media-pick', function (e) {
        e.preventDefault();
        var $btn   = $(this);
        var target = $btn.data('target');
        var $input = $('#' + target);
        if (!$input.length || typeof wp === 'undefined' || !wp.media) { return; }

        var frame = wp.media({
            title: $btn.data('title') || 'Select image',
            button: { text: 'Use this image' },
            library: { type: 'image' },
            multiple: false
        });

        frame.on('select', function () {
            var att = frame.state().get('selection').first().toJSON();
            $input.val(att.url).trigger('change');
            var $prev = $btn.closest('.kaitori-media-field').find('.kaitori-media-preview');
            if ($prev.length) { $prev.attr('src', att.url).show(); }
        });

        frame.open();
    });

    $(document).on('click', '.kaitori-media-clear', function (e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('#' + target).val('').trigger('change');
        $(this).closest('.kaitori-media-field').find('.kaitori-media-preview').hide();
    });
})(jQuery);
