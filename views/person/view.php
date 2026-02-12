<?php

use app\components\Icon;
use app\models\Field;
use app\models\FieldSearch;
use app\models\ForestSearch;
use yii\bootstrap5\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\Person $model */
/** @var ActiveDataProvider $fieldDp */
/** @var ActiveDataProvider $forestDp */

$this->title = $model->dispname;
$this->params['breadcrumbs'][] = ['label' => '関係者', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="person-view">

        <h1><?= Icon::getIconAndLabel('person') . ' : ' . Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-lg-5 col-md-8">
                <?php
                $attributes = [
                        [
                                'attribute' => 'type',
                                'value' => function ($model) {
                                    return $model->typeText;
                                },
                        ],
                        [
                                'attribute' => 'dispname',
                                'label' => '氏名/名称',
                        ],
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
                                        'confirm' => '関係者と <strong>"' . $model->dispname . '"</strong> とその連絡先を削除しますか？',
                                        'method' => 'post',
                                ],
                        ]);
                    }
                    $cmdButtons[] = Html::a(Icon::getIcon('update') . ' 引継の編集',
                            ['update-relation', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]],
                            ['class' => 'btn btn-primary btn-sm']);
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
            <div class="col-lg-7" id="ff-list">
                <h2 class="h4">関係する農地</h2>
                <?php Pjax::begin([
                        'id' => 'field-grid-pjax',
                        'linkSelector' => '#field-grid-pjax a',
                        'timeout' => 5000
                ]); ?>
                <?php
                $ids = FieldSearch::getModelIds($fieldDp);
                $bbox = FieldSearch::getBboxTotal($ids);
                $selectionUrl = Field::getSelectionMapUrl($ids, $bbox);
                $buttonsText = Html::button(Icon::getIcon('map-location'),
                                ['class' => 'btn-map-open btn btn-sm btn-outline-success', 'data-url' => $selectionUrl])
                        . ' ' .
                        Html::a(Icon::getIcon('map-location') . ' i-GIS', $selectionUrl,
                                ['class' => 'btn btn-sm btn-outline-success', 'target' => '_blank']);
                ?>
                <?= GridView::widget([
                        'dataProvider' => $fieldDp,
                    // 'filterModel' => $searchModel,
                        'showFooter' => true,
                        'id' => 'field-grid-view',
                        'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                [
                                        'attribute' => 'p_no',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return Html::a($model->p_str, ['/field/view', 'id' => $model->id],
                                                            ['class' => 'btn btn-outline-primary btn-sm']) . ' ' .
                                                    Html::button(Icon::getIcon('map-location'),
                                                            ['class' => 'btn-map-open btn btn-sm btn-outline-success', 'data-url' => $model->mapurl]);
                                        },
                                        'footer' => $buttonsText,
                                ],
                                [
                                        'attribute' => 'owner',
                                        'value' => function ($model) {
                                            return $model->owner ? $model->owner_name : '';
                                        }
                                ],
                                [
                                        'attribute' => 'cultivator',
                                        'value' => function ($model) {
                                            return $model->cultivator ? $model->cultivator_name : '';
                                        }
                                ],
                                [
                                        'attribute' => 'chusankan',
                                        'value' => function ($model) {
                                            return $model->chusankan ? $model->chusankan_name : '';
                                        }
                                ],
                                [
                                        'attribute' => 'saimokusho',
                                        'value' => function ($model) {
                                            return $model->saimokusho ? $model->saimokusho_name : '';
                                        }
                                ],
                                [
                                        'attribute' => 'usage',
                                        'label' => '利用状況',
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
                                        'footer' => Field::getAreaText(FieldSearch::getFAreaTotal($fieldDp)),
                                        'footerOptions' => ['class' => 'text-end'],
                                ],
//                                [
//                                        'attribute' => 'c_area',
//                                        'value' => function ($model) {
//                                            return Field::getAreaText($model->c_area);
//                                        },
//                                        'contentOptions' => ['class' => 'text-end'],
//                                        'footer' => Field::getAreaText(FieldSearch::getCAreaTotal($fieldDp)),
//                                        'footerOptions' => ['class' => 'text-end'],
//                                ],
//                                'note',
                        ],
                ]); ?>
                <?php Pjax::end(); ?>
                <hr/>
                <h2 class="h4">関係する山林</h2>
                <?php Pjax::begin([
                        'id' => 'forest-grid-pjax',
                        'linkSelector' => '#forest-grid-pjax a',
                        'timeout' => 5000
                ]); ?>
                <?= GridView::widget([
                        'dataProvider' => $forestDp,
                    // 'filterModel' => $searchModel,
                        'id' => 'forest-grid-view',
                        'showFooter' => true,
                        'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                [
                                        'attribute' => 'p_no',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return Html::a($model->p_str, ['/forest/view', 'id' => $model->id],
                                                            ['class' => 'btn btn-outline-primary btn-sm']) . ' ' .
                                                    Html::button(Icon::getIcon('map-location'),
                                                            ['class' => 'btn-map-open btn btn-sm btn-outline-success', 'data-url' => $model->mapurl]);
                                        }
                                ],
                                [
                                        'attribute' => 'owner',
                                        'value' => function ($model) {
                                            return $model->owner ? $model->owner_name : '';
                                        }
                                ],
                                [
                                        'attribute' => 'manager',
                                        'value' => function ($model) {
                                            return $model->manager ? $model->manager_name : '';
                                        }
                                ],
                                [
                                        'attribute' => 'area',
                                        'value' => function ($model) {
                                            return Field::getAreaText($model->area);
                                        },
                                        'contentOptions' => ['class' => 'text-end'],
                                        'footer' => Field::getAreaText(ForestSearch::getAreaTotal($forestDp)),
                                        'footerOptions' => ['class' => 'text-end'],
                                ],
                                'note',
                        ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
        <?= $this->render('/field/_map_modal') ?>
    </div>

<?php
$urlReorderContact = Url::to(['person/reorder-contact', 'id' => $model->id]);
$urlDeleteContact = Url::to(['person/delete-contact', 'id' => $model->id]);
$this->registerJs("
$('#ff-list').on('click', '.btn-map-open', function(e) {
  e.preventDefault();
  const src = $(this).data('url');
  openMapModal(src);
});
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
