<?php

namespace app\commands;

use app\models\YoutubeDownloader;
use app\models\InputField;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\FileHelper;
use yii\helpers\Url;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class YoutubeDownloaderController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    protected function makeTmpDir($url) {
        
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m);

        return [$m[1], \Yii::getAlias('@app/tmp/dl-folder/') . $m[1]];
    }

    
    protected function downloadFile($url, $tmpDir) {

        $model = new YoutubeDownloader;

        if (FileHelper::createDirectory($tmpDir)) {
            $model->init();
            $model->setDestination($tmpDir);
            $model->download($url);
        } else {
            throw new UserException('oops');

        }

        $downloadedFPath = FileHelper::findFiles($tmpDir,['only'=>['*.mp3']]);
        $explodedDownloadedFPath = explode('/', $downloadedFPath[0]);

        $fileName = $explodedDownloadedFPath[count($explodedDownloadedFPath)-1];

        $downloadLink = '?r=youtube-downloader%2Fdownload';

        return [$fileName, $downloadLink];

    }


    protected function sendEmail($addressee, $downloadLink, $fileName, $ytVideoId) //$downloadLink = Url::toRoute([])
    {
        \Yii::$app->mailer->compose('download-link', ['downloadLink' => $downloadLink, 'fileName' => $fileName, 'ytVideoId' => $ytVideoId])
                            ->setFrom('youtubedl@company.com')
                            ->setTo($addressee)
                            ->setSubject('Youtubedl-mp3')
                            ->send();

        return;
    }

    public function actionLaunch($addressee, $url) {

        echo "Creating tmp directory...";
        $ytVideoId = $this->makeTmpDir($url)[0];
        $tmpDir = $this->makeTmpDir($url)[1];

        echo "Extracting mp3 from provided youtube link...";
        $fileName = $this->downloadFile($url, $tmpDir)[0];
        $downloadLink = $this->downloadFile($url, $tmpDir)[1];

        echo "Sending email...";
        $this->sendEmail($addressee, $downloadLink, $fileName, $ytVideoId);

        return ExitCode::OK;

    }


}