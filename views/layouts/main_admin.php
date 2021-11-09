<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
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
<?php $this->beginBody() ?>

<?php
    
    $this->registerJs ( "$(function () { 
        $('body').tooltip({
            selector: '[data-toggle=\"tooltip\"]',
                html:true
            });
        }); 
    ");
?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('@web/uploads/theme/egged-logo.png', ['width' => '26px']) . 'אגד קמפיינים',
        'brandOptions' => ['style' => 'display: inline-flex;'],
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-default navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => [
            ['label' => '<i class="glyphicon glyphicon-home"></i>', 'url' => ['/campain/index']],
            Yii::$app->user->isGuest ? (
                ['label' => 'כניסה', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'יציאה (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout dir-ltr']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
