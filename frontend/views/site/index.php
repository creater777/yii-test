<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="body-content">
        <p>
            <?= Html::a('Генерировать', ['generate'], ['class' => 'btn btn-success']) ?>
        </p>
        <div class="row">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'dateCreateFormatted',
                    'fall_at',
                    'stateName',
                    'integrity',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{fall} {eat}',
                        'urlCreator' => function ($action, $model, $key, $index, $thiz){
                            return Url::toRoute("site/$action?id={$model->id}");
                        },
                        'buttons' => [
                            'fall' => function ($url, $model, $key) {
                                return Html::a("Уронить", $url);
                            },
                            'eat' => function ($url, $model, $key) {
                                return Html::a("Съесть", $url);
                            },
                        ],
                    ],
                ],
                'rowOptions' => function($model, $key, $index, $grid){
                    return ['style' => 'background-color:'.$model->color];
                }
            ]); ?>
        </div>

    </div>
</div>
