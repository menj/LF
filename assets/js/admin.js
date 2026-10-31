/**
 * Admin JavaScript for Meta Boxes
 * Langgam Fikir WordPress Theme
 */

(function($) {
    'use strict';
    
    /**
     * Initialize About Page Logo Upload
     */
    function initLogoUploader() {
        var mediaUploader;
        
        // Upload logo button click handler
        $('.upload-logo-button').on('click', function(e) {
            e.preventDefault();
            
            // If the uploader object already exists, reopen the dialog
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // Create the media uploader
            mediaUploader = wp.media({
                title: 'Choose Logo',
                button: {
                    text: 'Use this logo'
                },
                multiple: false
            });
            
            // When an image is selected, run the callback
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#about_logo_id').val(attachment.id);
                var $preview = $('.about-logo-preview');
                if ($preview.find('img').length) {
                    $preview.find('img').attr('src', attachment.url);
                } else {
                    $preview.append('<img src="' + attachment.url + '" alt="Logo Preview" class="lf-logo-preview-img">');
                }
                $preview.show();
                $('.remove-logo-button').show();
            });
            
            // Open the uploader dialog
            mediaUploader.open();
        });
        
        // Remove logo button click handler
        $('.remove-logo-button').on('click', function(e) {
            e.preventDefault();
            $('#about_logo_id').val('');
            $('.about-logo-preview').hide();
            $(this).hide();
        });
    }
    
    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        initLogoUploader();
    });
    
})(jQuery);

/*--------------------------------------------------------------
# FAQ Meta Box (Phase C — v2.5.0)
# Handles add/remove/reindex of FAQ rows.
--------------------------------------------------------------*/
jQuery(document).ready(function ($) {
    var $list = $('#langgam-fikir-faq-list');

    if (!$list.length) {
        return;
    }

    var $template = $('#langgam-fikir-faq-template');

    // Reindex all rows so name="langgam_fikir_faq[N][...]" stays sequential after adds/removes.
    function reindex() {
        $list.find('.langgam-fikir-faq-row').each(function (i) {
            var $row = $(this);
            $row.attr('data-faq-index', i);
            $row.find('.langgam-fikir-faq-row-title').text(
                $row.find('.langgam-fikir-faq-row-title').text().replace(/\d+|#/, i + 1)
            );
            $row.find('input, textarea').each(function () {
                var $field = $(this);
                var name = $field.attr('name');
                var id = $field.attr('id');
                if (name) {
                    $field.attr('name', name.replace(/\[\d+\]|\[__INDEX__\]/, '[' + i + ']'));
                }
                if (id) {
                    $field.attr('id', id.replace(/_(\d+|__INDEX__)_/, '_' + i + '_'));
                }
            });
            $row.find('label').each(function () {
                var $label = $(this);
                var forAttr = $label.attr('for');
                if (forAttr) {
                    $label.attr('for', forAttr.replace(/_(\d+|__INDEX__)_/, '_' + i + '_'));
                }
            });
        });
    }

    // Add row
    $('#langgam-fikir-faq-add').on('click', function (e) {
        e.preventDefault();
        var html = $template.html();
        var index = $list.find('.langgam-fikir-faq-row').length;
        html = html.replace(/__INDEX__/g, index);
        $list.append(html);
        reindex();
    });

    // Remove row (event delegation)
    $list.on('click', '.langgam-fikir-faq-remove', function (e) {
        e.preventDefault();
        var $rows = $list.find('.langgam-fikir-faq-row');
        if ($rows.length <= 1) {
            // Don't remove the last row — clear it instead so users always see the empty starter.
            $(this).closest('.langgam-fikir-faq-row').find('input, textarea').val('');
            return;
        }
        $(this).closest('.langgam-fikir-faq-row').remove();
        reindex();
    });

    // Drag-to-reorder using jQuery UI sortable if it's available
    if ($.fn.sortable) {
        $list.sortable({
            handle: '.langgam-fikir-faq-row-header',
            axis: 'y',
            update: reindex
        });
    }
});

/*--------------------------------------------------------------
# Reviews Meta Box (Phase D — v2.6.0)
# Handles add/remove/reindex of review rows.
--------------------------------------------------------------*/
jQuery(document).ready(function ($) {
    var $list = $('#langgam-fikir-reviews-list');

    if (!$list.length) {
        return;
    }

    var $template = $('#langgam-fikir-review-template');

    function reindex() {
        $list.find('.langgam-fikir-review-row').each(function (i) {
            var $row = $(this);
            $row.attr('data-review-index', i);
            $row.find('.langgam-fikir-review-row-title').text(
                $row.find('.langgam-fikir-review-row-title').text().replace(/\d+|#/, i + 1)
            );
            $row.find('input, textarea, select').each(function () {
                var $field = $(this);
                var name = $field.attr('name');
                var id = $field.attr('id');
                if (name) {
                    $field.attr('name', name.replace(/\[\d+\]|\[__INDEX__\]/, '[' + i + ']'));
                }
                if (id) {
                    $field.attr('id', id.replace(/_(\d+|__INDEX__)_/, '_' + i + '_'));
                }
            });
            $row.find('label').each(function () {
                var $label = $(this);
                var forAttr = $label.attr('for');
                if (forAttr) {
                    $label.attr('for', forAttr.replace(/_(\d+|__INDEX__)_/, '_' + i + '_'));
                }
            });
        });
    }

    $('#langgam-fikir-reviews-add').on('click', function (e) {
        e.preventDefault();
        var html = $template.html();
        var index = $list.find('.langgam-fikir-review-row').length;
        html = html.replace(/__INDEX__/g, index);
        $list.append(html);
        reindex();
    });

    $list.on('click', '.langgam-fikir-review-remove', function (e) {
        e.preventDefault();
        var $rows = $list.find('.langgam-fikir-review-row');
        if ($rows.length <= 1) {
            $(this).closest('.langgam-fikir-review-row').find('input, textarea, select').val('');
            return;
        }
        $(this).closest('.langgam-fikir-review-row').remove();
        reindex();
    });

    if ($.fn.sortable) {
        $list.sortable({
            handle: '.langgam-fikir-review-row-header',
            axis: 'y',
            update: reindex
        });
    }
});
