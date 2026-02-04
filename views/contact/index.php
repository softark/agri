<?php

use app\components\Icon;
use app\models\Contact;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\PersonSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '連絡先';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">

    <h1><?= Icon::getIconAndLabel('contact') ?></h1>

    <?php Pjax::begin(['timeout' => 5000]) ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                            'attribute' => 'person.type',
                            'value' => 'person.typetext',
                    ],
                    [
                            'attribute' => 'person.name',
                            'label' => '関係者（氏名／名称）',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a($model->person->dispname, ['/person/view', 'id' => $model->person->id],
                                        ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => 0]);
                            },
                    ],
                    'contact_name',
                    [
                            'attribute' => 'address1',
                            'value' => 'shortaddress'
                    ],
                    'phone1',
                    [
                            'class' => ActionColumn::className(),
                            'urlCreator' => function ($action, Contact $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>
    <hr/>
    <h2><?= Icon::getIconAndLabel('excel') ?> のダウンロード</h2>

    <p>連絡先データを EXCEL シートとしてダウンロードします。</p>
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
        setTimeout(stopLoading, 3000);
    } else {
        alert('ダウンロードするデータがありません。');
    }
});
");
