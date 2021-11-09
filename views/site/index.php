<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $campain->name;
?>
<style>
@media(max-width:767px) {
}

@media(min-width:768px) {
}   

.campain-wrap .egged-image {
    background: url('<?= Url::to('@web/' . $campain->image) ?>') no-repeat center center; 
    background-size: cover;
}
</style>


<div class="row-fluid flex flex-r flex-wn space-between">
    <div class="egged-image bg-grey">
        
    </div>
    <div class="egged-form flex flex-c bg-green">
        <div class="row-fluid logo text-left bg-white hidden-xs">
            <?= Html::img('@web/' . $campain->logo, ['width' => '187px', 'height' => '106px']) ?>
        </div>
        
        <div class="col-xs-12">
            <?= $contactForm ?>
        </div>
        
        <div class="row-fluid logo text-left bg-white visible-xs">
            <?= Html::img('@web/' . $campain->logo, ['width' => '187px', 'height' => '106px']) ?>
        </div>
    </div>
</div>

