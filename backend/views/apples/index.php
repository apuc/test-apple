<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form_model backend\models\GenerateForm */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Яблоки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apples-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin([
                'id' => 'generate-form',
            ]); ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($form_model, 'count')
                        ->textInput(['placeholder' => $form_model->getAttributeLabel('count')])
                        ->label(false);
                    ?>
                </div>
                <div class="col-sm-3">
                    <?= Html::submitButton('Создать', ['class' => 'btn btn-primary', 'name' => 'generate']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    ['attribute' => 'color_id', 'value' => function ($model) {
                        return $model->colorTitle." ({$model->color})";
                    }, 'format' => 'raw'],
                    ['attribute' => 'size', 'value' => function ($model) { return ($model->size * 100).'%'; }],
                    ['attribute' => 'status', 'value' => function ($model) { return $model->statusText; }],
                    ['attribute' => 'created_at', 'format' => ['date', 'php:d.m.Y H:i:s']],
                    ['attribute' => 'fallen_at', 'format' => ['date', 'php:d.m.Y H:i:s']],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{fall} {eat}',
                        'buttons' => [
                            'fall' => function ($url, $model, $key) {
                                return Html::a(
                                    Html::tag('span', '', ['title' => 'Уронить', 'class' => 'glyphicon glyphicon-cloud-download']),
                                    ['/apples/fall', 'id' => $model->id]
                                );
                            },
                            'eat' => function ($url, $model, $key) {
                                return Html::a(
                                    Html::tag('span', '', ['title' => 'Откусить', 'class' => 'glyphicon glyphicon-cutlery']),
                                    ['/apples/eat', 'id' => $model->id]
                                );
                            },
                        ],
                        'visibleButtons' => [
                            'fall' => function ($model, $key, $index) {
                                return $model->status == $model::STATUS_ON_TREE;
                            },
                            'eat' => function ($model, $key, $index) {
                                return $model->status == $model::STATUS_FALLEN;
                            },
                        ],
                    ],
                ]
            ]); ?>
        </div>
    </div>
</div>
