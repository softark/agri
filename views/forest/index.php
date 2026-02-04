<?php

use app\components\Icon;
use app\models\Forest;
use app\models\ForestSearch;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\ForestSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '山林';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="forest-index" id="forest-index">

        <h1><?= Icon::getIconAndLabel('tree') ?></h1>

        <?php Pjax::begin(['timeout' => 5000]) ?>
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
                'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
                'showFooter' => true,
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                                'attribute' => 'aza_id',
                                'value' => function ($model) {
                                    return $model->aza_name;
                                }
                        ],
                        [
                                'attribute' => 'p_no',
                                'value' => 'p_str',
                        ],
                        [
                                'attribute' => 'type_id',
                                'value' => function ($model) {
                                    return $model->type_name;
                                }
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
                                'attribute' => 'manager',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->manager ? Html::a($model->manager_name, ['/person/view', 'id' => $model->manager_id],
                                            ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0]) : '&nbsp;';
                                }
                        ],
                        [
                                'attribute' => 'area',
                                'value' => function ($model) {
                                    return Forest::getAreaTextLong($model->area);
                                },
                                'contentOptions' => ['class' => 'text-end'],
                                'footer' => Forest::getAreaTextLong(ForestSearch::getAreaTotal($dataProvider)),
                                'footerOptions' => ['class' => 'text-end'],
                        ],
                        'note',
                        [
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $buttons = [
                                            Html::a(Icon::getIconAndLabel('view'), ['view', 'id' => $model->id],
                                                    ['class' => 'btn btn-sm btn-primary', 'data-pjax' => 0]),
                                            Html::button(Icon::getIconAndLabel('map-location'),
                                                    ['class' => 'btn-map-open btn btn-sm btn-outline-success', 'data-url' => $model->mapurl]),
                                            Html::a(Icon::getIcon('map-location') . ' i-GIS で見る', $model->mapurl,
                                                    ['class' => 'btn btn-sm btn-outline-success', 'target' => '_blank']),
                                    ];
                                    if (Yii::$app->user->can('forest.edit')) {
                                        $buttons[] = Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id],
                                                ['class' => 'btn btn-sm btn-primary', 'data-pjax' => 0]);
                                    }
                                    return implode(' ', $buttons);
                                }
                        ],
                ],
        ]); ?>

        <?php Pjax::end(); ?>
        <?= $this->render('/field/_map_modal') ?>

        <hr/>
        <h2><?= Icon::getIconAndLabel('excel') ?> のダウンロード</h2>

        <p>山林データを EXCEL シートとしてダウンロードします。</p>
        <p>現在の検索条件に従って、画面に表示されていないものも含めて、全てのデータが出力されます。</p>
        <p>
            <?= Html::button(Icon::getIconAndLabel('excel'), ['class' => 'btn btn-primary', 'id' => 'btn-list-export']) ?>
            <span id="export-loading" style="display:none; margin-left:10px;">
                Excel ファイルを作成中 ... データ数が多いと多少時間がかかります
            </span>
        </p>
        <div class="excel-export">
            <?php $form = ActiveForm::begin([
                    'action' => ['export'],
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
$('#forest-index').on('click', '.btn-map-open', function(e) {
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
        setTimeout(stopLoading, 3000);
    } else {
        alert('ダウンロードするデータがありません。');
    }
});
");
