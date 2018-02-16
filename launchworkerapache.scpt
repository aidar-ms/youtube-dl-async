tell application "Terminal"
	do script "cd /usr/local/var/www/youtube_dl && php yii youtube-downloader/prepare-routine; exit"
    activate
end tell
