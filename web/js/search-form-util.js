(function ($) {
    'use strict';

    // クリア
    $(document).on('click', 'form[data-search-form] [data-clear]', function (event) {
        event.preventDefault();
        var $form = $(this).closest('form');

        // select, input をクリア（submitボタン等は除外したいなら selector を絞る）
        $form.find('select').val('');
        $form.find('input:not([type=hidden]):not([type=submit]):not([type=button])').val('');

        $form.submit();
    });

    // 変更で即 submit（select）
    $(document).on('change', 'form[data-search-form] select', function (event) {
        event.preventDefault();
        $(this).closest('form').submit();
    });

    // 変更で即 submit（input）
    $(document).on('change', 'form[data-search-form] input', function (event) {
        event.preventDefault();
        $(this).closest('form').submit();
    });

})(jQuery);
