define([
    'jquery',
    'jquery/ui',
    'mage/translate'
], function ($, ui, $t) {
    'use strict';

    return function (element, searchUrl) {
        searchUrl = searchUrl || '/catalog/product/search';

        $(element).autocomplete({
            minLength: 2,
            delay: 300,
            source: function (request, response) {
                $.ajax({
                    url: searchUrl,
                    data: { searchKey: request.term, page: 1, limit: 30 },
                    type: 'GET',
                    dataType: 'json',
                    showLoader: true
                }).done(function (data) {
                    var results = [];
                    if (data.options) {
                        $.each(data.options, function (id, option) {
                            results.push({ label: option.label, value: option.path, sku: option.path });
                        });
                    }
                    response(results);
                }).fail(function () { response([]); });
            },
            select: function (event, ui) {
                $(this).val(ui.item.sku);
                return false;
            }
        }).data('ui-autocomplete')._renderItem = function (ul, item) {
            return $('<li>')
                .append('<a><span class="sku">' + item.sku + '</span> - <span class="name">' + item.label + '</span></a>')
                .appendTo(ul);
        };
    };
});
