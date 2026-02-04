(function ($) {
    'use strict';

    function getMap($modal) {
        var raw = $modal.attr('data-picker-map');
        if (!raw) return null;
        try { return JSON.parse(raw); } catch (e) { return null; }
    }

    // 行クリック：選択状態にして OK を有効化
    $(document).on('click', '.modal [data-picker-row]', function (e) {
        var $row = $(this);
        var $modal = $row.closest('.modal');

        $modal.find('[data-picker-row].is-selected').removeClass('is-selected');
        $row.addClass('is-selected');

        $modal.find('[data-picker-ok]').removeClass('disabled');
        e.preventDefault();
    });

    // OK：選択行の data から map に従って値をセット
    $(document).on('click', '.modal [data-picker-ok]', function (e) {
        e.preventDefault();

        var $modal = $(this).closest('.modal');
        var $row = $modal.find('[data-picker-row].is-selected');
        if ($row.length === 0) return;

        var map = getMap($modal);
        if (!map) return;

        Object.keys(map).forEach(function (key) {
            // key = "person-id" なら data('person-id') で取れる
            var val = $row.data(key);
            var sel = map[key];
            if (!sel) return;

            var $target = $(sel);
            if ($target.length === 0) return;

            // input なら val、その他なら text（必要なら調整）
            if ($target.is('input, textarea, select')) {
                $target.val(val);
            } else {
                $target.text(val);
            }
        });

        $modal.trigger('picker:selected');
        $modal.modal('hide');
        $modal.find('[data-picker-ok]').addClass('disabled');
    });

    // Cancel：閉じるだけ
    $(document).on('click', '.modal [data-picker-cancel]', function (e) {
        e.preventDefault();
        var $modal = $(this).closest('.modal');
        $modal.modal('hide');
        $modal.find('[data-picker-ok]').addClass('disabled');
    });

})(jQuery);
