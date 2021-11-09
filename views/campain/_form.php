<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;
use app\widgets\imageInput\ImageInputWidget;
use kartik\date\DatePicker;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Campain */
/* @var $form yii\widgets\ActiveForm */
?>

<?php 
    $this->registerJs('q_quill_1.format("header", 2);',
            View::POS_READY);
?>

<div class="campain-form col-xs-12 col-sm-6">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'fbf')->widget(SwitchInput::class, [
        'pluginOptions'=>[
            'onText'=>Yii::t('app', 'Yes'),
            'offText'=>Yii::t('app', 'No'),
        ],
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'start_date')->widget(DatePicker::class, \Yii::$app->params['datePickerKvOptions']) ?>

    <?= $form->field($model, 'end_date')->widget(DatePicker::class, \Yii::$app->params['datePickerKvOptions']) ?>

    <hr>
    <?= $form->field($model, 'button_color')->widget(\kartik\color\ColorInput::class) ?>

    <?= $form->field($model, 'campain')->widget(\bizley\quill\Quill::className(), [
        'toolbarOptions' => [
            ['bold', 'italic', 'underline'], 
            [['color' => []]],
            [['align' => []]],
            [[ 'size' => ['small', 'medium', 'large', 'huge'] ]],
            [[ 'header' => [1, 2, 3, 4, 5, 6, false] ]],
        ],
        'options' => [
            'style' => 'direction: rtl; background-color: #eeeeee;',
        ],
        'formats' => ['header' => 2],
    ]) ?>
    
    <hr>
    
    <?= $form->field($model, 'show_licanse')->widget(SwitchInput::class, [
        'pluginOptions'=>[
            'onText'=>Yii::t('app', 'Yes'),
            'offText'=>Yii::t('app', 'No'),
        ],
    ]); ?>

    <?= $form->field($model, 'show_cv')->widget(SwitchInput::class, [
        'pluginOptions'=>[
            'onText'=>Yii::t('app', 'Yes'),
            'offText'=>Yii::t('app', 'No'),
        ]
    ]); ?>
    
    <hr>
    <?= $form->field($model, 'image')->widget(ImageInputWidget::class, [
            'htmlOptions' => ['style' => 'cursor: pointer;'],
            'placeHolder' => 'uploads/theme/icons8-picture.svg',
        ]);
    ?>

    <?= $form->field($model, 'logo')->widget(ImageInputWidget::class, [
            'htmlOptions' => ['style' => 'cursor: pointer;'],
            'placeHolder' => 'uploads/theme/icons8-picture.svg',
        ]);
    ?>

    <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>



        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end(); ?>

</div>
