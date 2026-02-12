<?php

use app\components\Icon;
use app\models\Field;
use app\models\FieldSearch;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\FieldSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '農地';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="field-index" id="field-index">

        <h1><?= Icon::getIconAndLabel('field') ?></h1>

        <?php Pjax::begin(['timeout' => 5000]) ?>
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

        <?php
        $ids = FieldSearch::getModelIds($dataProvider);
        $bbox = FieldSearch::getBboxTotal($ids);
        $selectionUrl = Field::getSelectionMapUrl($ids, $bbox);
        $buttonsText = Html::button(Icon::getIconAndLabel('map-location'),
                        ['class' => 'btn-map-open btn btn-sm btn-outline-success', 'data-url' => $selectionUrl])
                . ' ' .
                Html::a(Icon::getIcon('map-location') . ' i-GIS', $selectionUrl,
                        ['class' => 'btn btn-sm btn-outline-success', 'target' => '_blank']);
        ?>

        <?= GridView::widget([
                'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
                'showFooter' => true,
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                                'attribute' => 'aza_id',
                                'value' => 'aza_name',
                        ],
                        [
                                'attribute' => 'p_no',
                                'value' => 'p_str',
                        ],
                        [
                                'attribute' => 'owner',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->owner ? Html::a($model->owner_name, ['/person/view', 'id' => $model->owner_id],
                                            ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0]) : '&nbsp;';
                                }
                        ],
                        [
                                'attribute' => 'cultivator',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->cultivator ? Html::a($model->cultivator_name, ['/person/view', 'id' => $model->cultivator_id],
                                            ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0]) : '&nbsp;';
                                }
                        ],
                        [
                                'attribute' => 'chusankan',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->chusankan ? Html::a($model->chusankan_name, ['/person/view', 'id' => $model->chusankan_id],
                                            ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0]) : '&nbsp;';
                                }
                        ],
                        [
                                'attribute' => 'saimokusho',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->saimokusho ? Html::a($model->saimokusho_name, ['/person/view', 'id' => $model->saimokusho_id],
                                            ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0]) : '&nbsp;';
                                }
                        ],
                        [
                                'attribute' => 'usage',
                                'value' => function ($model) {
                                    return $model->usage_name;
                                }
                        ],
                        [
                                'attribute' => 'f_area',
                                'value' => function ($model) {
                                    return Field::getAreaText($model->f_area);
                                },
                                'contentOptions' => ['class' => 'text-end'],
                                'footer' => Field::getAreaText(FieldSearch::getFAreaTotal($dataProvider)),
                                'footerOptions' => ['class' => 'text-end'],
                        ],
//                        [
//                                'attribute' => 'c_area',
//                                'value' => function ($model) {
//                                    return Field::getAreaText($model->c_area);
//                                },
//                                'contentOptions' => ['class' => 'text-end'],
//                                'footer' => Field::getAreaText(FieldSearch::getCAreaTotal($dataProvider)),
//                                'footerOptions' => ['class' => 'text-end'],
//                        ],
//                        'note',
                        [
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $buttons = [
                                            Html::a(Icon::getIconAndLabel('view'), ['view', 'id' => $model->id],
                                                    ['class' => 'btn btn-sm btn-primary', 'data-pjax' => 0]),
                                            Html::button(Icon::getIconAndLabel('map-location'),
                                                    ['class' => 'btn-map-open btn btn-sm btn-outline-success', 'data-url' => $model->mapurl]),
                                            Html::a(Icon::getIcon('map-location') . ' i-GIS', $model->mapurl,
                                                    ['class' => 'btn btn-sm btn-outline-success', 'target' => '_blank']),
                                    ];
                                    if (Yii::$app->user->can('forest.edit')) {
                                        $buttons[] = Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id],
                                                ['class' => 'btn btn-sm btn-primary', 'data-pjax' => 0]);
                                    }
                                    return implode(' ', $buttons);
                                },
                                'footer' => $buttonsText,
                        ],
                ],
        ]); ?>
        <?php Pjax::end(); ?>
        <?= $this->render('/field/_map_modal') ?>
        <hr/>
        <h2><?= Icon::getIconAndLabel('excel') ?> のダウンロード</h2>

        <p>農地データを EXCEL シートとしてダウンロードします。</p>
        <p>現在の検索条件に従って、画面に表示されていないものも含めて、全てのデータが出力されます。</p>
        <p>
            <?= Html::button(Icon::getIconAndLabel('excel'), ['class' => 'btn btn-primary', 'id' => 'btn-list-export']) ?>
            <span id="export-loading" style="display:none; margin-left:10px;">
                Excel ファイルを作成中 ... データ数が多いと多少時間がかかります
            </span>
        </p>
        <div class="excel-export">
            <?php $form = ActiveForm::begin(['action' => ['export'],
                    'method' => 'post',
                    'id' => 'export-form',
                    'options' => ['target' => 'dl_iframe'],   // ★ここ重要
            ]); ?>
            <?php ActiveForm::end(); ?>
        </div>
        <iframe name="dl_iframe" id="dl_iframe" style="display:none;"></iframe>

    </div>
<?php

$this->registerJs("
$('#field-index').on('click', '.btn-map-open', function(e) {
  e.preventDefault();
  const src = $(this).data('url');
  openMapModal(src);
});
const btn = $('#btn-list-export');
const msg = $('#export-loading');
const form = $('#export-form');
function startLoading(){
    btn.prop('disabled', true);
    msg.show();
    document.body.style.cursor = 'progress';
}
function stopLoading(){
    btn.prop('disabled', false);
    msg.hide();
    document.body.style.cursor = '';
}
btn.on('click', function(){
    if ($('div.summary div.summary')[0]) {
        startLoading();
        form.trigger('submit');
        setTimeout(stopLoading, 5000);
    } else {
        alert('ダウンロードするデータがありません。');
    }
});
");
