<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = $campain->name;
$session = Yii::$app->session;
?>

<style>   
    .btn.btn-primary {
        background-color: <?= empty($campain->button_color) ? '#dae249' :  $campain->button_color ?>;
    }
    @media(max-width:767px) {
        .campain-wrap .egged-image {
            background: url('<?= Url::to('@web/' . $campain->image) ?>') no-repeat top center; 
            background-size: cover;
            background-color: #00a77a;
            min-height: 30vh;
        }
    }

    @media(min-width:768px) {
        .campain-wrap .egged-image {
            background: url('<?= Url::to('@web/' . $campain->image) ?>') no-repeat top center; 
            background-size: cover;
            background-color: #00a77a;
        }
    }
</style>

<?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>
    
    <div class="reply-wrapper bg-green hv-100 flex flex-c center">
        <div class="container">
            <div class="row-fluid">
                <div role="alert" class="alert alert-success egged-title col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                    <h1><?= Yii::t('app', 'Thank you for your request! we will contact you soon') ?></h1>
                    <h2>אגד מחלקת הגיוס</h2>
                </div>
            </div>

            <div class="row">
                <p class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">  
                    <button class="btn btn-info bg-yellow">
                        <?= Html::a('חזור', Url::to('@web/' . $campain->id)) ?>
                    </button>
                </p>
            </div>
        </div>
    </div>

<?php elseif (Yii::$app->session->hasFlash('contactFormSubmitError')): ?>

    <div class="reply-wrapper bg-green hv-100 flex flex-c center">
        <div class="container">
            <div class="row-fluid">
                <div role="alert" class="alert alert-error egged-title col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                    <h1><?= Yii::t('app', 'We could not send your application. Please try later.') ?></h1>
                    <h2>אגד מחלקת הגיוס</h2>
                </div>
            </div>

            <div class="row">
                <p class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">            
                    <button class="btn btn-info bg-yellow">
                        <?= Html::a('חזור', Url::to('@web/' . $campain->id)) ?>
                    </button>
                </p>
            </div>
        </div>
    </div>

<?php else: ?>


<div class="row-fluid flex flex-r flex-wn space-between">
    <div class="egged-image">
        
    </div>
    <div class="egged-form flex flex-c">
        <div class="row-fluid logo top text-left bg-white hidden-xs">
            <?= Html::img('@web/' . $campain->logo, ['width' => '137px', 'height' => '106px', 'alt' => 'לוגו אגד']) ?>
        </div>
        
        <div class="col-xs-12 bg-green h-100 fields">

        <div id="campain">
            <?= $campain->campain ?>
        </div>
        
        <div class="row">
            <?php $form = ActiveForm::begin(['id' => 'contact-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

                <?= $form->field($model, 'name', ['errorOptions' => ['id' => 'help-name'], 'options' => ['class' => 'col-xs-6 col-sm-12 form-group']])
                    ->textInput([
                        'autofocus' => true, 
                        'placeholder' => $model->getAttributeLabel('name'), 
                        'aria-label' => $model->getAttributeLabel('name'),
                        'aria-describedby' => 'help-name',
                    ])->label(false) ?>
            
                <?= $form->field($model, 'phone', ['errorOptions' => ['id' => 'help-phone'], 'options' => ['class' => 'col-xs-6 col-sm-12 form-group']])
                    ->textInput([
                        'placeholder' => $model->getAttributeLabel('phone'), 
                        'aria-label' => $model->getAttributeLabel('phone'),
                        'aria-describedby' => 'help-phone',
                    ])->label(false) ?>
            
                <?= $form->field($model, 'searchArea', ['errorOptions' => ['id' => 'help-searchArea'], 'options' => ['class' => 'col-xs-6 col-sm-12 form-group']])
                    ->dropDownList($model->searchAreaOptions, [
                        'prompt' => $model->getAttributeLabel('searchArea'), 
                        'aria-label' => $model->getAttributeLabel('searchArea'),
                        'aria-describedby' => 'help-searchArea',
                    ])->label(false) ?>
            
                <?php if ($campain->show_licanse === 1) : ?>
                    <?= $form->field($model, 'licanse', ['errorOptions' => ['id' => 'help-licanse'], 'options' => ['class' => 'col-xs-6 col-sm-12 form-group fg-white', 'style' => 'display: inline-flex;', 'aria-label' => $model->getAttributeLabel('licanse')] ])->inline()->radioList(
                            $model->yesnoOptions, ['style' => 'display:inline; margin: 0 10px;', 'separator' => '  ', 'aria-describedby' => 'help-licanse',]                           
                        );
                    ?>
                <?php endif; ?>
                <?php if ($campain->show_cv === 1) : ?>
                    <?= $form->field($model, 'cvfile', ['errorOptions' => ['id' => 'help-cvfile'], 'options' => ['class' => 'col-xs-12 form-group']])
                        ->fileInput([
                            'class' => 'sr-only',
                            'aria-label' => $model->getAttributeLabel('cvfile'),
                            'aria-describedby' => 'help-cvfile',
                            'accept' => '.pdf,.rtf,.doc,.docx,.xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ])->label('<i class="glyphicon glyphicon-paperclip" style="margin-left: 10px;"></i>'  . $model->getAttributeLabel('cvfile')) ?>
                <?php endif; ?>
                <div class="form-group col-xs-12"  style="min-height: auto;">
                    <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary fg-green text-bold', 'name' => 'contact-button']) ?>
                </div>
                <?= $form->field($model, 'supplierId')->hiddenInput()->label(false) ?>
            <?php ActiveForm::end(); ?>

            <div class="col-xs-12">
                <div class="more-jobs flex flex-r flex-wn space-between">
                    <?= Html::a(Yii::t('app', 'More Jobs'), Yii::$app->params['additionalJobs']) ?>
                    <span class="fg-white"><?= $campain->contact ?></span>
                </div>
            </div>
        </div>


            
        </div>
        
        <div class="row-fluid logo bottom text-left bg-white visible-xs">
            <?= Html::img('@web/' . $campain->logo, ['width' => '117px', 'height' => '58px', 'alt' => 'לוגו אגד']) ?>
        </div>
    </div>
</div>

<?php endif; ?>
<?php
    $this->registerJs('$(document).on("click", "button[type=\'submit\']", function() { setTimeout(function () {$(".has-error:first > input:first").focus(); }, 500); });');
?>