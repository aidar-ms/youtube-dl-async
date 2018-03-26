<?php
namespace app\commands;

use app\models\YoutubeDownloader;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class YoutubeDownloaderController extends Controller
{

    public function actionPrepareRoutine() {
        $grmnWorker = new \GearmanWorker();
        $grmnWorker->addServer('gearman', 4730);
        $grmnWorker->addFunction('make_routine', [$this, 'routine']);
        while ($grmnWorker->work());
        
    }

    public function routine($job) {

	    /* Parse input */
	    echo "Got job";
        $userData = json_decode($job->workload(), true);
        $url = $userData['url'];
        $email = $userData['email'];

        /* Prepare temporary directory name */
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m);
        $tmpDir = \Yii::getAlias('@app/tmp/dl-folder/') . $m[1];
        $ytVideoId = $m[1];

        echo "working on $ytVideoId";
        
        /* Extract mp3 file from youtube video */
        $youtubeDownloader = new YoutubeDownloader;
        if(FileHelper::createDirectory($tmpDir)) {
            $youtubeDownloader->init();
            $youtubeDownloader->setDestination($tmpDir);
            $youtubeDownloader->extractMp3($url);
        } else {
            throw new Exception('Tmp directory could not be created');
        }
        
        /* Get file name */
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


    }
}
