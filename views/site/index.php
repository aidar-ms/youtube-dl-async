<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\InputField;
?>
<div class="site-index">

    <?php
        $formModel = new InputField;
        $form = ActiveForm::begin([
                'action' => ['youtube-downloader/validate'],
                'id' => 'input-field',
                'method' => 'post',
                //'enableAjaxValidation' => true,
                'options' => ['class' => 'form-horizontal']
                ]);
    ?>

    <?= $form->field($formModel, 'email')->textInput() ?>
    <?= $form->field($formModel, 'url')->textInput() ?>

    <?= Html::submitButton('Download', ['class'=> 'btn btn-primary']); ?>

    <?php ActiveForm::end() ?>

</div>
