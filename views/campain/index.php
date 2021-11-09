<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Campains');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="campain-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Campain'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'id',
            [
                'attribute' => 'fbf',
                'format' => 'html',
                'value' => function($data) { return $data->fbf === 0 ? '<span class="label label-danger">' . Yii::t('app', 'No') . '</span>' : '<span class="label label-success">' . Yii::t('app', 'Yes') . '</span>'; },
            ],
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($data) { return Html::a($data->name, '/web/' . $data->id, ['data-toggle' => 'tooltip', 'title' => Yii::t('app', 'Show Campaign')]); },
            ],
            'start_date',
            'end_date',
            //'campain',
            //'image',
            //'logo',
            'sid',
            //'show_licanse',
            //'show_cv',
            //'button_color',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
