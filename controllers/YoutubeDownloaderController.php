<?php
namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;


use app\models\Records;
use app\models\InputField;
use app\models\YoutubeDownloader;
use app\models\YtdlGearmanWorker;
use app\models\YtdlGearmanClient;
use yii\base\UserException;
use yii\helpers\FileHelper;

use toriphes\console\Runner;


class YoutubeDownloaderController extends Controller {

    /* ----------- UTILITIES ------------*/
    

    protected function sendEmail($addressee, $downloadLink) {

        \Yii::$app->mailer->compose('download',['link' => $downloadLink])
                            ->setFrom('')
                            ->setTo($addressee)
                            ->setSubject('Ваш mp3 файл готов')
                            ->send();

    }

    /*------------ ACTIONS ----------*/

    public function actionValidate() {

        

        if(\Yii::$app->request->isAjax) {

            // Request is Ajax


            $records = new Records;
            $input = new InputField;

            if($input->load(\Yii::$app->request->post()) && $input->validate()) {

                $url = $input->url;
                $email = $input->email;
                
                $runner = new Runner();
                $runner->run('youtube-downloader/launch-gearman-worker');
                $grmnClient = new YtdlGearmanClient($url, $email);
                

                return $this->asJson(['success' => true]);
                    
            } else {

                $validationResults = [];
                foreach($input->getErrors() as $attribute => $errors) {
                    $validationResults[yii\helpers\Html::getInputId($model, $attribute)] = $errors;
                }

                return $this->asJson(['validation' => $validationResults]);
            }

            /* REQUEST IS NOT AJAX */
            throw new UserException('Произошла ошибка при обработке запроса');
        }

    }

    public function actionDownload() {
        
        $records = new Records;

        $tmpStorage = \Yii::getAlias('@app/tmp/dl-folder');

        $email = \Yii::$app->request->get('email');
        $fileName = \Yii::$app->request->get('fileName');
        $ytVideoId = \Yii::$app->request->get('ytVideoId');


        if(!isset($fileName) || !isset($ytVideoId)) {
            throw new UserException(var_dump($fileName));
        }

        $records->makeRecord($email, $fileName, $ytVideoId);

        return \Yii::$app->response->sendFile("$tmpStorage/$ytVideoId/$fileName", $fileName);
    }



}


?>