<?php 
    use yii\helpers\Html;
?>

<h1> Файл <?= $fileName ?> успешно загружен. </h1>

<h4>Нажмите на ссылку ниже, чтобы скачать</h4>

<?= Html::a('Скачать', ['youtube-downloader/send', 'ytVideoId' => $ytVideoId, 'fileName' => $fileName]) ?>
