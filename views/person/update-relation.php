<?php

use app\models\Icon;
use app\models\PersonRelation;
use app\models\PersonWorkSearch;
use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Person $model */

$this->title = '関係者 : ' . $model->dispname . ' - 引継編集';
$this->params['breadcrumbs'][] = ['label' => '関係者', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dispname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '引継編集';

?>
<div class="update-relation">

    <h1><?= Icon::getIcon('contact') . ' 関係者 : ' . $model->dispname . ' - ' . Icon::getIcon('update') . ' 引継編集' ?></h1>

    <div class="row">
        <div class="col-lg-5 col-md-8">
            <h2 class="h4">引継元</h2>
            <?php
            $attrs = [];
            if (count($model->ancRelIds) > 0) {
                $ancs = [];
                foreach ($model->ancestors as $a) {
                    $ancs[] = $a->dispname;
                }
                $attrs = [[
                        'label' => '引継元',
                        'value' => implode(' , ', $ancs),
                ]];
                $n = 1;
                foreach ($model->ancRelIds as $arId) {
                    $ar = PersonRelation::findOne($arId);
                    $attrs[] = [
                            'label' => '# ' . $n,
                            'value' => $ar->fromPerson->dispname . ' > ' . $ar->toPerson->dispname . ' ... ' .
                                    Html::a(Icon::getIconAndLabel('delete'), ['delete-relation', 'id' => $model->id, 'rel_id' => $ar->id], [
                                            'class' => 'btn btn-sm btn-danger',
                                            'data' => [
                                                    'confirm' => '# ' . $n . ' を削除しても構いませんか？<br />' .
                                                            'これによって、 [' . $ar->fromPerson->dispname . '] が [' . $model->dispname . '] の引継元から消えます。<br />（[' .
                                                            $ar->fromPerson->dispname . '] に引継元がある場合は、それも [' . $model->dispname . '] の引継元から消えます）',
                                                    'method' => 'post',
                                            ],
                                    ])
                    ];
                    $n++;
                }
            } else {
                $attrs = [[
                        'label' => '引継元',
                        'value' => '（なし）',
                ]];
            }
            $attrs[] = [
                    'label' => '追加',
                    'value' => Html::a(Icon::getIcon('plus') . ' 引継元を追加', ['add-relation', 'id' => $model->id, 'mode' => 'A'],
                            ['class' => 'btn btn-sm btn-success'])
            ];
            ?>
            <table class="table table-bordered table-striped">
                <?php foreach ($attrs as $attr) : ?>
                    <tr>
                        <th><?= $attr['label'] ?></th>
                        <td><?= $attr['value'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <h2 class="h4">引継先</h2>
            <?php
            $attrs = [];
            if (count($model->descRelIds) > 0) {
                $descs = [];
                foreach ($model->descendants as $d) {
                    $descs[] = $d->dispname;
                }
                $attrs = [[
                        'label' => '引継先',
                        'value' => implode(' , ', $descs),
                ]];
                $n = 1;
                foreach ($model->descRelIds as $drId) {
                    $dr = PersonRelation::findOne($drId);
                    $attrs[] = [
                            'label' => '# ' . $n,
                            'value' => $dr->fromPerson->dispname . ' > ' . $dr->toPerson->dispname . ' ... ' .
                                    Html::a(Icon::getIconAndLabel('delete'), ['delete-relation', 'id' => $model->id, 'rel_id' => $dr->id], [
                                            'class' => 'btn btn-sm btn-danger',
                                            'data' => [
                                                    'confirm' => '# ' . $n . ' を削除しても構いませんか？<br />' .
                                                            'これによって、 [' . $dr->toPerson->dispname . '] が [' . $model->dispname . '] の引継先から消えます。<br />（[' .
                                                            $dr->toPerson->dispname . '] に引継先がある場合は、それも [' . $model->dispname . '] の引継先から消えます）',
                                                    'method' => 'post',
                                            ],
                                    ])
                    ];
                    $n++;
                }
            } else {
                $attrs = [[
                        'label' => '引継先',
                        'value' => '（なし）',
                ]];
            }
            $attrs[] = [
                    'label' => '追加',
                    'value' => Html::a(Icon::getIcon('plus') . ' 引継先を追加', ['add-relation', 'id' => $model->id, 'mode' => 'D'],
                            ['class' => 'btn btn-sm btn-success'])
            ];
            ?>
            <table class="table table-bordered table-striped">
                <?php foreach ($attrs as $attr) : ?>
                    <tr>
                        <th><?= $attr['label'] ?></th>
                        <td><?= $attr['value'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p>
                <?= Html::a(Icon::getIcon('go-back') . ' 引継編集を終了', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
    </div>

</div>
