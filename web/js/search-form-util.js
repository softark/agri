(function ($) {
    'use strict';

    // クリア
    $(document).on('click', 'form[data-search-form] [data-clear]', function (event) {
        var $form = $(this).closest('form');

        // select, input をクリア（submitボタン等は除外したいなら selector を絞る）
        $form.find('select').val('');
        $form.find('input:not([type=hidden]):not([type=submit]):not([type=button])').val('');

        $form.submit();
        event.preventDefault();
    });

    // 変更で即 submit（select）
    $(document).on('change', 'form[data-search-form] select', function (event) {
        $(this).closest('form').submit();
        event.preventDefault();
    });

    // 変更で即 submit（input）
    $(document).on('change', 'form[data-search-form] input', function (event) {
        $(this).closest('form').submit();
        event.preventDefault();
    });

})(jQuery);
