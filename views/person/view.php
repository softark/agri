<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Person $model */

$this->title = $model->dispname;
$this->params['breadcrumbs'][] = ['label' => '名簿', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-view">

    <h1><?= Icon::getIconAndLabel('person') . ' : ' . Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">
            <?php
            $attributes = [
                    [
                            'attribute' => 'status',
                            'value' => function ($model) {
                                return $model->statusText;
                            },
                    ],
                    [
                            'attribute' => 'type',
                            'value' => function ($model) {
                                return $model->typeText;
                            },
                    ],
                    'dispname',
                    'yomigana',
            ];
            if ($model->note != '') {
                $attributes[] = 'note';
            }
            if (count($model->ancestors) > 0) {
                $items = [];
                foreach ($model->ancestors as $person) {
                    $items[] = Html::a($person->dispname, ['view', 'id' => $person->id], ['class' => 'btn btn-outline-primary btn-sm']);
                }
                $attributes[] = [
                        'label' => '引継元',
                        'format' => 'raw',
                        'value' => implode(' ', $items),
                ];
            }
            if (count($model->descendants) > 0) {
                $items = [];
                foreach ($model->descendants as $person) {
                    $items[] = Html::a($person->dispname, ['view', 'id' => $person->id], ['class' => 'btn btn-outline-primary btn-sm']);
                }
                $attributes[] = [
                        'label' => '引継先',
                        'format' => 'raw',
                        'value' => implode(' ', $items),
                ];
            }
            if (Yii::$app->user->can('admin')) {
                $attributes = ArrayHelper::merge($attributes, [
                        [
                                'label' => '登録',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->created_at, 'yyyy-MM-dd HH:mm')
                                            . ' / ' . $model->createdBy->longname;
                                }
                        ],
                        [
                                'label' => '更新',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->updated_at, 'yyyy-MM-dd HH:mm')
                                            . ' / ' . $model->updatedBy->longname;
                                }
                        ],
                ]);
            }
            $cmdButtons = [];
            if (Yii::$app->user->can('person.edit')) {
                $cmdButtons[] = Html::a(Icon::getIconAndLabel('update'),
                        ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]],
                        ['class' => 'btn btn-primary btn-sm']);
                if (Yii::$app->user->can('person.delete')) {
                    $cmdButtons[] = Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                    'confirm' => '名簿から <strong>"' . $model->dispname . '"</strong> とその連絡先を削除しますか？',
                                    'method' => 'post',
                            ],
                    ]);
                }
            }
            if (count($cmdButtons) > 0) {
                $attributes[] = [
                        'label' => '操作',
                        'format' => 'raw',
                        'value' => implode(' ', $cmdButtons),
                ];
            }
            ?>
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => $attributes,
            ]) ?>
            <p>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
            <hr/>
            <div id="contact-view">
                <?= $this->render('_contact_view', ['model' => $model]); ?>
            </div>
        </div>
    </div>
</div>

<?php
$urlReorderContact = Url::to(['person/reorder-contact', 'id' => $model->id]);
$urlDeleteContact = Url::to(['person/delete-contact', 'id' => $model->id]);
$this->registerJs("
$('#contact-view').on('click', '.reorder-contact', function(event){
  event.preventDefault();
  event.stopPropagation();
  $.ajax({
    url: '$urlReorderContact',
    type: 'POST',
    data: {
      contact_id: $(this).data('contact-id'),
      direction: $(this).data('direction'),
      _csrf: yii.getCsrfToken()
    },
    success: function (html) {
      $('#contact-view').html(html);
    },
    error: function (xhr) {
      alert('連絡先の順序変更に失敗しました: ' + xhr.status);
    }
  });
});
$('#contact-view').on('click', '.delete-contact', function(event){
  event.preventDefault();
  event.stopPropagation();
  const contactId = $(this).data('contact-id');
  yii.confirm('この連絡先を削除しますか？', function () {
    $.ajax({
      url: '$urlDeleteContact',
      type: 'POST',
      data: {
        contact_id: contactId,
        _csrf: yii.getCsrfToken()
      },
      success: function (html) {
        $('#contact-view').html(html);
      },
      error: function (xhr) {
        alert('連絡先の削除に失敗しました: ' + xhr.status);
      }
    });
  });
});
");
