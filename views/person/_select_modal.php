<?php

/* @var $this yii\web\View */
/* @var $modalId string */
/* @var $pickerMap array */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\components\Icon;
use app\models\Person;
use app\models\PersonSearch;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\helpers\Json;

$this->registerCss("
.person-row.is-selected td {
  background: #0d6efd; /* Bootstrap3なら info 系 */
  color: white;
");

app\assets\SelectModalAsset::register($this);

$searchModel = new PersonSearch(['_form_name' => 'psel']);
$dataProvider = $searchModel->search([], 10, 'person:select');

Modal::begin([
        'title' => '関係者を選択',
        'toggleButton' => false,
        'id' => $modalId,
        'options' => [
                'data-picker-map' => Json::encode($pickerMap),
        ],
        'size' => Modal::SIZE_EXTRA_LARGE,
]);
?>
    <div class="form-group float-end">
        <?= Html::button(Icon::getIconAndLabel('ok', '選択'), ['class' => 'disabled btn btn-primary', 'data-picker-ok' => 1]) ?>
        <?= Html::button(Icon::getIconAndLabel('cancel'), ['class' => 'btn btn-outline-secondary', 'data-picker-cancel' => 1]) ?>
    </div>

<?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['/person/select'],
        'options' => [
                'data-search-form' => 1,
        ],
        'fieldConfig' => [
                'inputOptions' => ['class' => 'allow_submit form-control']
        ],
]); ?>
    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'type')->dropDownList(Person::getTypes(), ['prompt' => '']) ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'search_name') ?>
        </div>
        <div class="col-md-3 col-sm-4 col-6">
            <?= $form->field($searchModel, 'search_address') ?>
        </div>
        <div class="form-group col-md-2 col-sm-2">
            <?= Html::submitButton(Icon::getBtnText('search'), ['class' => 'btn btn-primary btn-sm d-block']) ?>
            <?= Html::button(Icon::getBtnText('clear'), ['class' => 'btn btn-outline-secondary btn-sm d-block', 'data-clear' => 1]) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<?php echo $this->render('_select', ['dataProvider' => $dataProvider]); ?>

<?php
Modal::end();
