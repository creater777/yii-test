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
        <div class="row">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'dateCreateFormatted',
                    'dateFallFormatted',
                    'stateName',
                    'integrity'
                ],
                'rowOptions' => function($model, $key, $index, $grid){
                    return ['style' => 'background-color:'.$model->color];
                }
            ]); ?>
        </div>

    </div>
</div>
