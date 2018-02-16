<?php 
namespace app\models;



use Yii;
use yii\base\Model;
use yii\helpers\Url;

use YoutubeDl\YoutubeDl;

class YoutubeDownloader extends Model {

    public $dl;

    /*------------------- Youtube-dl wrapper --------------------*/

    public function init() {

        $this->dl = new YoutubeDl([
            'extract-audio' => true,
            'encoding' => 'utf-8',
            'audio-format' => 'mp3',
            'audio-quality' => 0, // best
            'output' => '%(title)s.%(ext)s',
            'prefer-avconv' => true,
            'ffmpeg-location' => '/usr/local/bin/avconv'
        ]);

    }
    
    public function setDestination($destination) {
        $this->dl->setDownloadPath($destination);
    }

    public function extractMp3($ytLink) {

        $this->dl->download($ytLink);
        
    }
    
    /* --------------------------------------------------------- */


    /*------------------- Utilities --------------------*/

    public function makeTmpDirRoute($url) {
        
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $url, $m);

        $tmrDirRoute = \Yii::getAlias('@app/tmp/dl-folder/') . $m[1];

        return $tmrDirRoute;
    }

    public function generateMp3File($tmrDirRoute) {

        $downloadedFPath = FileHelper::findFiles($tmpDir,['only'=>['*.mp3']]);
        $explodedDownloadedFPath = explode('/', $downloadedFPath[0]);

        $fileName = $explodedDownloadedFPath[count($explodedDownloadedFPath)-1];

        $downloadLink = Url::toRoute(['youtube-downloader/download']);

        return [$fileName, $downloadLink];


    }

    /* --------------------------------------------------------- */

}
    

?>