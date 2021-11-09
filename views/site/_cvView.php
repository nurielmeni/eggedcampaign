<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'אגד - הגשת מועמדות';
?>

<div dir="rtl">
    <h3>קובץ קורות חיים אוטומטי - אגד משרות</h3>
    <br>
    <h4>פרטי מועמד/חבר</h4>
    <?php foreach ($model->attributes as $name => $value) : ?>
        <?php if ($name === 'cvfile' || $name === 'supplierId') continue; ?>
        <p><span style="font-weight: bold;"><?= $model->getAttributeLabel($name) ?>: </span> <?= $value ?></p>
    <?php endforeach; ?>
    <p><span style="font-weight: bold;">קוד משרה: </span> <?= $model->jobCode ?></p>
</div>