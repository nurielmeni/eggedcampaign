<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAssetCampaign;
use yii\helpers\Url;

AppAssetCampaign::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?= Url::to('@web/uploads/theme/egged-logo.png') ?>" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://fonts.googleapis.com/css?family=Assistant:200,300,400,600,700,800&amp;subset=hebrew" rel="stylesheet">
    <?php $this->head() ?>
</head>
<body>
<style>
    .campain-wrap .egged-form .logo.top {
        background: url("<?= Url::to('@web/uploads/theme/shape-up.svg') ?>") no-repeat right center;
    }

    .campain-wrap .egged-form .logo.bottom {
        background: url("<?= Url::to('@web/uploads/theme/shape-down.svg') ?>") no-repeat right center;
    }
</style>

<div class="acess-container">
    <div id="jbbutton" class="balloon" title="Accessibility">
        <span class="balloontext"><?= Yii::t('app', 'Accessibility') ?></span>
        <button class="btn-clear accessibility" aria-expanded="false" aria-label="<?= Yii::t('app', 'Accessibility') ?>">
            <img src="<?= Url::to('@web/js/jbility/img/acessc50.png') ?>">
        </button>
    </div>
    <div id="acess-icons">
        <div id="contrast" class="acess-icon balloon">
            <span class="balloontext"><?= Yii::t('app', 'Contrast') ?></span>
            <button class="btn-clear acess-button" aria-pressed="false" aria-label="<?= Yii::t('app', 'Contrast') ?>">
                <img src="<?= Url::to('@web/js/jbility/img/contraste40.png') ?>"/>
            </button>
        </div>
        <div id="increaseFont" class="acess-icon balloon">
            <span class="balloontext"><?= Yii::t('app', 'Increase Font') ?></span>
            <button class="btn-clear acess-button" aria-pressed="false" aria-label="<?= Yii::t('app', 'Increase Font') ?>">
                <img src="<?= Url::to('@web/js/jbility/img/fontsma40.png') ?>"/>
            </button>
        </div>
        <div id="decreaseFont" class="acess-icon balloon">
            <span class="balloontext"><?= Yii::t('app', 'Decrease Font') ?></span>
            <button class="btn-clear acess-button" aria-pressed="false" aria-label="<?= Yii::t('app', 'Decrease Font') ?>">
                <img src="<?= Url::to('@web/js/jbility/img/fontsme40.png') ?>"/>
            </button>
        </div>
    </div>
    <?= Html::a(Yii::t('app', 'Accessability Statment'), 'http://www.egged.co.il/%D7%9E%D7%90%D7%9E%D7%A8-9720-%D7%A0%D7%92%D7%99%D7%A9%D7%95%D7%AA.aspx', ['style' => 'position: absolute; right: 0; top: -22px; white-space: nowrap;']) ?>
</div>

<?php $this->beginBody() ?>

<div class="campain-wrap">
    <?= $content ?>
</div>

<?php $this->endBody() ?>

<?php
    $script = '
        $("button.btn-clear.accessibility").on("click", function() {
            $(this).attr("aria-expanded", ($(this).attr("aria-expanded") === "false" ? "true" : "false"));
        });
        $("button.btn-clear.acess-button").on("click", function() {
            $(this).attr("aria-pressed", "true");
        });
    ';
    $this->registerJs($script, yii\web\View::POS_READY);
?>
</body>
</html>
<?php $this->endPage() ?>
