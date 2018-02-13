<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;

use app\models\Records;
use app\models\InputField;
use app\models\YoutubeDownloader;
use yii\base\UserException;
use yii\helpers\FileHelper;

use toriphes\console\Runner;

class YoutubeDownloaderController extends Controller {

    /* ----------- UTILITIES ------------*/
    protected function makeRecord($ytVideoId, $fileName) {
        $records = new Records();

        $records->yt_video_id = $ytVideoId;
        $records->file_name = $fileName;

       if($records->save()) {
            return 1;
       }

       return 0;    
    }

    protected function sendEmail($addressee, $downloadLink) {

        \Yii::$app->mailer->compose('download',['link' => $downloadLink])
                            ->setFrom('')
                            ->setTo($addressee)
                            ->setSubject('Ваш mp3 файл готов')
                            ->send();

    }

    protected function makeTmpDir($url) {

        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m);

        return \Yii::getAlias('@app/tmp/dl-folder/') . $m[1];
    }

    /*------------ ACTIONS ----------*/

    public function actionValidate() {

        

        if(\Yii::$app->request->isAjax) {

            $input = new InputField;

            // Request is Ajax

            if($input->load(\Yii::$app->request->post()) && $input->validate()) {

                $email = $input->email;
                $url = $input->url;

                $command = sprintf('youtube-downloader/launch %s %s', $email, $url);
                $output = '';
                $runner = new Runner();
                
                if($runner->run($command,$output)) {
                    return $this->asJson(['success' => true, 'output' => $output]);
                    
                } else {
                    throw new UserException(var_dump($runner));
                }

                /* DATA WAS NOT SAVED */

                $validationResults = [];
                foreach($input->getErrors() as $attribute => $errors) {
                    $validationResults[yii\helpers\Html::getInputId($model, $attribute)] = $errors;
                }

                return $this->asJson(['validation' => $validationResults]);
            }

            /* DATA IS NOT JSON */
            return $this->render('error');
        }

    }

    public function actionDownload() {

        $tmpStorage = \Yii::getAlias('@app/tmp/dl-folder');

        $fileName = \Yii::$app->request->get('fileName');
        $ytVideoId = \Yii::$app->request->get('ytVideoId');
        //$fileName = 'Ha GAY!!!.mp3';
        //$ytVideoId = 'YaG5SAw1n0c';

        if(!isset($fileName) || !isset($ytVideoId)) {
            throw new UserException(var_dump($fileName));
        }

        return \Yii::$app->response->sendFile("$tmpStorage/$ytVideoId/$fileName", $fileName);
    }

}


?>