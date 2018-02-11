<?php
require __DIR__ . '/vendor/autoload.php';

use YoutubeDl\YoutubeDl;

$dl = new YoutubeDl([
    'extract-audio' => true,
    'audio-format' => 'mp3',
    'audio-quality' => 0, // best
    'output' => '%(title)s.%(ext)s',
]);
$dl->setDownloadPath('/dl-folder');

$video = $dl->download('https://www.youtube.com/watch?v=oDAw7vW7H0c');
?>