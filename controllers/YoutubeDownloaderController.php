<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;

use app\models\InputField;
use app\models\YoutubeDownloader;
use yii\base\UserException;
use yii\helpers\FileHelper;

class YoutubeDownloaderController extends Controller {

    public $currFileName;
    public $currVidId;

    public function actionDownload() {

        $input = new InputField;
        $model = new YoutubeDownloader;

        if($input->load(\Yii::$app->request->post()) && $input->validate()) {

            $url = $input->url;

            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m);
            $tmpPath = \Yii::getAlias('@app/tmp/dl-folder/') . $m[1] ;

            

            if (FileHelper::createDirectory($tmpPath)) {
                $model->init();
                $model->setDestination($tmpPath);
                $model->download($url);
            } else {
                throw new UserException('oops');

            }

            $downloadedFPath = FileHelper::findFiles($tmpPath);
            $explodedDownloadedFPath = explode('/', $downloadedFPath[0]);

            $fileName = $explodedDownloadedFPath[count($explodedDownloadedFPath)-1];

            $this->currFileName = $fileName;
            $this->currVidId = $m[1];

            return $this->render('success', [
                'fileName' => $fileName,
                'ytVideoId' => $m[1]
            ]);
        }

    }

    public function actionSend() {

        $tmpStorage = \Yii::getAlias('@app/tmp/dl-folder');

        $fileName = \Yii::$app->request->get('fileName');
        $ytVideoId = \Yii::$app->request->get('ytVideoId');

        if(!isset($fileName) || !isset($ytVideoId)) {
            throw new UserException(var_dump($fileName));
        }


        return \Yii::$app->response->sendFile("$tmpStorage/$ytVideoId/$fileName", $fileName);
    }

}


?>