<?php

namespace app\commands;

use app\models\YoutubeDownloader;
use app\models\InputField;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\FileHelper;
use yii\helpers\Url;

use Ratchet\WebSocket\WsServer;

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
    public function actionLaunchGearmanWorker() {

        exec('osascript /usr/local/var/www/youtube_dl/launchworker.scpt');

    }

    public function actionPrepareRoutine() {

        $grmnWorker = new \GearmanWorker();
        $grmnWorker->addServer();

        $grmnWorker->addFunction('make_routine', [$this, 'routine']);
        echo "Worker active";


        while ($grmnWorker->work());
        

    }

    public function routine($job) {

        /* Parse input */
        $userData = json_decode($job->workload(), true);

        $url = $userData['url'];
        $email = $userData['email'];

        /* Prepare temporary directory name */
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m);
        $tmpDir = \Yii::getAlias('@app/tmp/dl-folder/') . $m[1];
        $ytVideoId = $m[1];

        /* Extract mp3 file from youtube video */
        $youtubeDownloader = new YoutubeDownloader;

        if(FileHelper::createDirectory($tmpDir)) {
            $youtubeDownloader->init();
            $youtubeDownloader->setDestination($tmpDir);
            $youtubeDownloader->extractMp3($url);
        } else {
            throw new Exception('Tmp directory could not be created');
        }
        
        /* Put file into file system */
        $downloadedFPath = FileHelper::findFiles($tmpDir,['only'=>['*.mp3']]);
        $explodedDownloadedFPath = explode('/', $downloadedFPath[0]);
        
        $fileName = $explodedDownloadedFPath[count($explodedDownloadedFPath)-1];
        
        /* Generate download link */
        $downloadLink = Url::toRoute(['youtube-downloader/download', 'fileName' => $fileName, 'ytVideoId' => $ytVideoId, 'email' => $email]);

        /* Generate email */
        \Yii::$app->mailer->compose('download-link', ['downloadLink' => $downloadLink])
                            ->setFrom('youtubedl@company.com')
                            ->setTo($email)
                            ->setSubject('Youtubedl-mp3')
                            ->send();

        /* Report on worker status */
        echo "\nDone";
        echo "\nShutting down this worker...\n";

        exec("kill -9 ". getmypid());

    }

}