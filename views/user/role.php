<?php

use app\models\UserRoleForm;
use app\models\User;
use app\models\Icon;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $model UserRoleForm */

$this->registerMetaTag(['name' => 'description', 'content' => 'このページでユーザの権限を編集することが出来ます。']);
$this->registerMetaTag(['name' => 'description', 'content' => 'This page allows you to edit an user rights of isarigami net.']);
$this->registerMetaTag(['name' => 'keywords', 'content' => '岩座神農会, ユーザ権限編集, Isarigami net, user rights, administration']);

$label = $user->longName;
$this->params['breadcrumbs'][] = ['label' => 'ユーザ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $label, 'url' => ['view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = '権限';
$this->title = $label . ' - 権限設定';
?>
<div class="user-update">
    <div class="h-100 p-3">
        <h1><?= Icon::getIcon('role') . ' ' . Html::encode($this->title) ?></h1>
    </div>
    <div class="body-content">
        <div class="user-form">
            <?php $form = ActiveForm::begin([
                'id' => 'user-admin-form',
                'enableAjaxValidation' => false,
            ]); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'roles')->checkboxList(
                        UserRoleForm::getAvailableRoleList()
                    )->hint('ユーザの権限を選択してください。');
                    ?>
                    <div class="form-group">
                        <?= Html::submitButton(Icon::getIcon('ok') . ' 更新', [
                            'class' => 'btn btn-primary'
                        ]) ?>
                        <?= Html::a(Icon::getIconAndLabel('cancel'), ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']), ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
