<?php

namespace app\models;

use xstreamka\mobiledetect\Device;
use Yii;
use yii\base\Component;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\widgets\ListView;

class GridAndListUtil extends Component
{
    static public function setupGrid($itemName = null)
    {
        if ($itemName === null) {
            $itemName = '項目';
        }
        Yii::$container->set(SerialColumn::class, [
            'contentOptions' => ['class' => 'text-end'],
        ]);

        Yii::$container->set(GridView::class, [
            'layout' => "
<div class='d-flex flex-row'>
<div class='pe-2'>{pager}</div>
<div class='pt-2'>{summary}</div>
</div>
<div class='row d-flex'><div>{items}</div></div>
<div class='no-print'><div>{pager}</div></div>
",
            'pager' => ['class' => LinkPager::class],
            // 'summary' => '<div class="summary">{begin} - {end} / TOTAL {totalCount}</div>',
            'summary' => '<div class="summary">P. {page} / {pageCount}</div>',
            'options' => ['class' => 'text-nowrap'],
        ]);

        Yii::$container->set(ActionColumn::class, [
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a(Icon::getBtnText('view'), $url, ['class' => 'btn btn-primary btn-sm', 'title' => '閲覧', 'data' => ['pjax' => '0']]);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a(Icon::getBtnText('update'), $url, ['class' => 'btn btn-primary btn-sm', 'title' => '編集', 'data' => ['pjax' => '0']]);
                },
                'info-update' => function ($url, $model, $key) {
                    return Html::a(Icon::getBtnText('info'), $url, ['class' => 'btn btn-primary btn-sm', 'title' => '情報編集', 'data' => ['pjax' => '0']]);
                },
                'clone' => function ($url, $model, $key) {
                    return Html::a(Icon::getBtnText('clone'), $url, ['class' => 'btn btn-success btn-sm', 'title' => '複製', 'data' => ['pjax' => '0']]);
                },
                'create' => function ($url, $model, $key) {
                    return Html::a(Icon::getBtnText('clone'), $url, ['class' => 'btn btn-success btn-sm', 'title' => '複製', 'data' => ['pjax' => '0']]);
                },
                'delete' => function ($url, $model, $key) use ($itemName) {
                    return Html::a(Icon::getBtnText('delete'), $url, ['class' => 'btn btn-danger btn-sm', 'title' => '削除', 'data' => [
                        'pjax' => '0',
                        'confirm' => 'この<strong>' . $itemName . '</strong>を削除しても本当に構いませんか?',
                        'method' => 'post'
                    ]]);
                },
            ],
        ]);
        self::setupLinkPager();
    }

    static public function setupListView($pagerButtonCount = 10, $sorter = true, $lowerPager = true)
    {
        $layout = "
<div class='d-flex flex-row'>
";
        if ($sorter) {
            $layout .= "
<div class='pe-2'>{sorter}</div>
";
        }
        $layout .= "
<div class='pe-2'>{pager}</div>
<div class='ps-1 pt-2'>{summary}</div>
</div>
<div class='row d-flex ms-0'>{items}</div>
";
        if ($lowerPager) {
            $layout .= "
<div class='no-print'><div>{pager}</div></div>
";
        }
        Yii::$container->set(ListView::class, [
            'layout' => $layout,
            'pager' => ['class' => LinkPager::class],
            // 'summary' => '<div class="summary">{begin} - {end} / TOTAL {totalCount}</div>',
            'summary' => '<div class="summary">P. {page} / {pageCount}</div>',
        ]);
        self::setupLinkPager($pagerButtonCount);
        self::setupSorterStyle();
    }

    static public function setupLinkPager($buttonCount = 10)
    {
        Yii::$container->set(LinkPager::class, [
            'firstPageLabel' => Icon::getIcon('fast-backward'),
            'prevPageLabel' => Icon::getIcon('prev'),
            'nextPageLabel' => Icon::getIcon('next'),
            'lastPageLabel' => Icon::getIcon('fast-forward'),
            'maxButtonCount' => Device::$isPhone ? 0 : $buttonCount,
        ]);
    }

    static public function setupSorterStyle()
    {
        Yii::$app->view->registerCss("
.sorter {
    display: inline-block;
    padding-left: 0;
    margin: 0;
    border-radius: 4px;
}
.sorter > li, .summary {
    display: inline;
    text-wrap: nowrap;
}
.sorter > li > a,
.sorter > li > span {
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.42857143;
    color: #337ab7;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd;
}
.sorter > li:first-child > a,
.sorter > li:first-child > span {
    margin-left: 0;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}
.sorter > li:last-child > a,
.sorter > li:last-child > span {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
.sorter > li > a:hover,
.sorter > li > span:hover,
.sorter > li > a:focus,
.sorter > li > span:focus {
    z-index: 2;
    color: #23527c;
    background-color: #eee;
    border-color: #ddd;
}
.sorter > .active > a,
.sorter > .active > span,
.sorter > .active > a:hover,
.sorter > .active > span:hover,
.sorter > .active > a:focus,
.sorter > .active > span:focus {
    z-index: 3;
    color: #fff;
    cursor: default;
    background-color: #337ab7;
    border-color: #337ab7;
}
");
    }

}