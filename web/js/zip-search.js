(function ($) {
    'use strict';

    var API_URL = 'https://tools.softark.net/zipdata/api/search';

    function receive(response, data) {
        response($.map(data, function (item) {
            var address = item.pref + item.town + item.block;
            var label = item.zip_code + ' : ' + address;
            if (item.street) label += ' (' + item.street + ')';

            return {
                label: label,
                zip_code: item.zip_code,
                address: address
            };
        }));
    }

    function update($src, ui) {
        var zipSel = $src.data('zip-target');
        var addrSel = $src.data('address-target');

        if (zipSel) $(zipSel).val(ui.item.zip_code);
        if (addrSel) $(addrSel).val(ui.item.address);
    }

    function attachZipAutocomplete($input) {
        if ($input.data('zip-ac-init')) return;
        $input.data('zip-ac-init', 1);

        $input.autocomplete({
            delay: 500,
            minLength: 3,
            source: function (request, response) {
                $.ajax({
                    url: API_URL,
                    dataType: 'jsonp',
                    data: {
                        mode: 0,
                        term: request.term,
                        max_rows: 100,
                        biz_mode: 0,
                        sort: 0
                    },
                    success: function (data) {
                        receive(response, data);
                    }
                });
            },
            select: function (event, ui) {
                update($input, ui);
                return false;
            }
        });
    }

    function attachAddressAutocomplete($input) {
        if ($input.data('addr-ac-init')) return;
        $input.data('addr-ac-init', 1);

        $input.autocomplete({
            delay: 300,
            minLength: 2,
            source: function (request, response) {
                $.ajax({
                    url: API_URL,
                    dataType: 'jsonp',
                    data: {
                        mode: 1,
                        term: request.term,
                        max_rows: 100,
                        biz_mode: 0,
                        sort: 1
                    },
                    success: function (data) {
                        receive(response, data);
                    }
                });
            },
            select: function (event, ui) {
                update($input, ui);
                return false;
            }
        });
    }

    // 初期 & PJAX 後にも対応
    function init(context) {
        $('[data-zip-autocomplete]', context).each(function () {
            attachZipAutocomplete($(this));
        });
        $('[data-address-autocomplete]', context).each(function () {
            attachAddressAutocomplete($(this));
        });
    }

    $(function () {
        init(document);
    });

    // PJAX を使っている場合
    $(document).on('pjax:end', function (e) {
        init(e.target);
    });

})(jQuery);
