<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>
<h2>Ваш mp3 готов. Перейдите по ссылке для скачивания</h2>
<?= Html::a('Скачать', $downloadLink) // 'fileName' => $fileName, 'ytVideoId' => $ytVideoId]) ?>