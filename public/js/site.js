//####################################################
// jQuery Handle
//####################################################
(function ($) {
    $(document).ready(function () {
        // Change lang currency
        $('.change_lang, .change_currency').click(function () {
            $(this).nstUI('loadAjax', {
                url: $(this).attr('_url'),
                field: {load: '', show: ''},
                event_complete: function () {
                    window.location.reload();
                },
            });
            return false;
        });

        // Auto check pages
        $('.auto_check_pages').each(function () {
            auto_check_pages($(this));
        });

        // Date picker
        /*	$('.datepicker').each(function()
         {
         var config = $.extend({}, {
         format: 'dd-mm-yyyy',
         }, $(this).data('datepicker'));

         $(this).datepicker(config);
         });*/

        // Autocomplete
       /* var cache = {}, lastXhr;
        $('.autocomplete').each(function () {
            var url_search = $(this).attr('_url');
            $(this).autocomplete(
                {
                    minLength: 2,
                    source: function (request, response) {
                        var term = request.term;

                        if (term in cache) {
                            response(cache[term]);
                            return;
                        }

                        lastXhr = $.getJSON(url_search, request, function (data, status, xhr) {
                            cache[term] = data;
                            if (xhr === lastXhr) {
                                response(data);
                            }
                        });
                    }
                });
        });*/


       // call_editor($(document));
        nfc.boot();


    });
})(jQuery);


//####################################################
// Main function
//####################################################
/**
 * Load ajax
 */
function load_ajax($this) {
    var field = jQuery($this).attr('_field');
    var url = jQuery($this).attr('_url');
    jQuery($this).nstUI('loadAjax', {
        url: url,
        field: {load: field + '_load', show: field + '_show'},
    });

    return false;
}

/**
 * Gan gia tri cua cac bien vao html
 */
function temp_set_value(html, params) {
    jQuery.each(params, function (param, value) {
        var regex = new RegExp('{' + param + '}', "igm");
        html = html.replace(regex, value);
    });
    return html;
}
/**
 * Copy gia tri giua 2 field
 */
function copy_value(from, to) {
    jQuery(this).nstUI('copyValue', {
        from: from,
        to: to,
    });
}

/**
 * Hien thi lightbox
 */
function lightbox(t) {
    jQuery(t).nstUI('lightbox');
}

/**
 * An pages khi ko co chia trang
 */
function auto_check_pages(t) {
    if (t.find('a')[0] == undefined) {
        t.remove();
    }
}

/**
 * Thay doi captcha
 */
function change_captcha(field) {
    var t = jQuery('#' + field);
    var url = t.attr('_captcha') + '?id=' + Math.random();
    t.attr('src', url);

    return false;
}


/**
 * Hien modal
 */
function modal_show(content) {
    var $modal = $('#modal-system-notify');
    //- gan noi dung
    $modal.find('.modal-body').html(content);
    //- hien thong bao
    $modal.modal('show')

}
function call_editor($main) {
    $main.find('.editor').each(function () {
        var $this = jQuery(this);
        var id = $this.attr('id');

        var config = $this.attr('_config');
        config = (config) ? JSON.parse(config) : {};

        if (typeof(CKEDITOR.instances[id]) != 'undefined')
            CKEDITOR.instances[id].destroy(true);


        CKEDITOR.replace(id, config);
        CKEDITOR.instances[id].on('change', function() { CKEDITOR.instances[id].updateElement() });

        if (CKEDITOR.env.ie && CKEDITOR.env.version == 8) {
            document.getElementById('ie8-warning').className = 'tip alert';
        }
    });
}