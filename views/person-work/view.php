<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '関係者ワーク', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div id="person-work-view" class="person-work-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">

            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            [
                                    'attribute' => 'src',
                                    'value' => function ($model) {
                                        return $model->srcText;
                                    },
                            ],
                            'name',
                            'address',
                    ],
            ]) ?>
            <p>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
            <hr/>
            <div id="person-view">
                <?= $this->render('_person_view', ['model' => $model]) ?>
            </div>
            <?= $this->render('/person/_select_modal.php', [
                    'personIdInput' => 'person-id',
                    'personNameInput' => 'person-name',
            ]);
            ?>
        </div>
    </div>
</div>

<?php
$urlAddLink = Url::to(['person-work/add-link-view', 'id' => $model->id]);
$urlDeleteLink = Url::to(['person-work/delete-link-view', 'id' => $model->id]);
$urlDeletePerson = Url::to(['person-work/delete-person', 'id' => $model->id]);
$urlReorderContact = Url::to(['person-work/reorder-contact', 'id' => $model->id]);
$urlDeleteContact = Url::to(['person-work/delete-contact', 'id' => $model->id]);
$this->registerJs("
$('#person-view').on('click', '#btn-person-select', function(event){
    openPersonSelectModal();
    event.preventDefault();
});
$('#person-view').on('change', '#person-id', function() {
  const personId = $(this).val();
  $.ajax({
    url: '$urlAddLink',
    type: 'POST',
    data: {
      person_id: personId,
      _csrf: yii.getCsrfToken()
    },
    success: function (html) {
      $('#person-view').html(html);
    },
    error: function (xhr) {
      alert('関係者へのリンク更新に失敗しました: ' + xhr.status);
    }
  });
});

$('#person-view').on('click', '#btn-person-unlink', function(event){
  event.preventDefault();
  event.stopPropagation();
  yii.confirm('関係者へのリンクを削除しますか？', function () {
    $.ajax({
      url: '$urlDeleteLink',
      type: 'POST',
      data: { _csrf: yii.getCsrfToken() },
      success: function (html) {
        $('#person-view').html(html);
      },
      error: function (xhr) {
        alert('関係者へのリンク削除に失敗しました: ' + xhr.status);
      }
    });
  });
});

$('#person-view').on('click', '.delete-person', function(event){
  event.preventDefault();
  event.stopPropagation();
  const personId = $(this).data('person-id');
  yii.confirm('この関係者を削除しますか？（連絡先も一緒に削除されます）', function () {
    $.ajax({
      url: '$urlDeletePerson',
      type: 'POST',
      data: {
        person_id: personId,
        _csrf: yii.getCsrfToken()
      },
      success: function (html) {
        $('#person-view').html(html);
      },
      error: function (xhr) {
        alert('関係者の削除に失敗しました: ' + xhr.status);
      }
    });
  });
});

$('#person-view').on('click', '.reorder-contact', function(event){
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
      $('#person-view').html(html);
    },
    error: function (xhr) {
      alert('連絡先の順序変更に失敗しました: ' + xhr.status);
    }
  });
});
$('#person-view').on('click', '.delete-contact', function(event){
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
        $('#person-view').html(html);
      },
      error: function (xhr) {
        alert('連絡先の削除に失敗しました: ' + xhr.status);
      }
    });
  });
});
");

