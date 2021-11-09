<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Campain */

$this->title = Yii::t('app', 'Create Campain');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Campains'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="campain-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
