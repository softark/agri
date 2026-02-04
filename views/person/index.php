<?php

use app\components\Icon;
use app\models\Person;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\PersonSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '関係者';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-index">

    <h1><?= Icon::getIconAndLabel('person') ?></h1>

    <p>
        <?php if (Yii::$app->user->can('person.edit')): ?>
            <?= Html::a(Icon::getIconAndLabel('person') . ' を新規登録', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?php Pjax::begin(['timeout' => 5000]) ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
//                    [
//                            'attribute' => 'status',
//                            'value' => 'statusText',
//                    ],
                    [
                            'attribute' => 'num_fields',
                            'contentOptions' => ['class' => 'text-end'],
                            'value' => function ($model) {
                                $count = $model->num_fields;
                                return ($count > 0) ? $count : '';
                            }
                    ],
                    [
                            'attribute' => 'num_forests',
                            'contentOptions' => ['class' => 'text-end'],
                            'value' => function ($model) {
                                $count = $model->num_forests;
                                return ($count > 0) ? $count : '';
                            }
                    ],

                    [
                            'attribute' => 'type',
                            'value' => 'typeText',
                    ],
                    [
                            'attribute' => 'name',
                            'label' => '氏名/名称',
                            'value' => 'dispname'
                    ],
                    [
                            'attribute' => 'c_name',
                            'label' => '連絡先',
                            'value' => function ($model) {
                                return $model->contact ? $model->contact->contact_name : '';
                            }
                    ],
                    [
                            'attribute' => 'c_address',
                            'label' => '住所',
                            'value' => function ($model) {
                                return $model->contact ? $model->contact->fulladdress : '';
                            }
                    ],
                    [
                            'attribute' => 'c_phone',
                            'label' => '電話（メイン）',
                            'value' => function ($model) {
                                return $model->contact ? $model->contact->phone1 : '';
                            }
                    ],
                    [
                            'class' => ActionColumn::class,
                            'template' => (Yii::$app->user->can('person.delete')) ?
                                    '{view} {update} {delete}' :
                                    ((Yii::$app->user->can('person.edit')) ?
                                            '{view} {update}' : '{view}'),
                            'urlCreator' => function ($action, Person $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>

    <hr/>
    <h2><?= Icon::getIconAndLabel('excel') ?> のダウンロード</h2>

    <p>関係者のデータを EXCEL シートとしてダウンロードします。</p>
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